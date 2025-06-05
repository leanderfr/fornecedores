
<template>

  <div>  <!-- exigencia do Vue, tudo agrupado em 1 elemento -->

    <div class='appBody'>  

      <div class='headerBar'>

        <div id='headerLogo' ></div>

        <div class='headerText' >
            <div>Avaliação PHP / Laravel</div>
        </div>

        <!-- seletor de backend  -->
        <div class="headerRight">    

          <div :class="! backendChanged ? 'backendClicked' : 'backendUnclicked' "   id='backendPHP'  @click="backendChanged = false"  >         
            <img src="./assets/images/php.svg" alt='' />
          </div>

          <label for="chkBackendSelector" class="switch_backend"  >
            <input id="chkBackendSelector" type="checkbox"  v-model="backendChanged"    />
            <span class="slider_backend round"></span>
          </label>

          <div :class="backendChanged ? 'backendClicked' : 'backendUnclicked' "   id='backendLaravel'  @click="backendChanged = true"  >         
            <img src="./assets/images/laravel.svg" alt='' />
          </div>

        </div>

      </div>

      <!-- menu superior -->
      <div class='menuSuperior' id='menuSuperior' style='scrollbar-width: thin;' @wheel='toWheelMenu'>
        <MenuSuperior 
          :selectedItem='selectedMenu' 
          :backendUrl='backendUrl'    
          @setNewselectedMenu='setNewselectedMenu'            /> 
      </div>


      <!-- mainContainer=> conteudo dinamico, depende do que o usuario quer ver no momento (datatable ou eventuais outros componentes) -->
      <div id='mainContainer'  >


          <!-- mostra datatable se usuario pediu para ver alguma tabela  -->
          <div v-if='toDisplayDatatable'>

            <Datatable  
                :key='toRefreshDatatable'
                :currentViewedDatatable = currentViewedDatatable
                :setDatatableToDisplay='setDatatableToDisplay' 
                :expressions='expressions' 
                :currentBackend="backendChanged ? 'usa' : 'brazil'" 
                :backendUrl='backendUrl'    
                @showLoading="isLoading=true" 
                @hideLoading="isLoading=false"     
                @setDatatableToDisplay='setDatatableToDisplay'  />
          </div>


      </div>

      <!-- canto inferior   -->
      <div class='bottomToolbar'  >     
      </div>

    </div>



    <!-- animacao 'carregando...' exibida qdo frontend aguardando resposta do backend  -->
    <div v-show="isLoading" class='backdropTransparent'  >
      <div id='divLoading' >&nbsp;</div>
    </div>

    <!-- mensagem rolante (horizontal) para avisar erro/sucesso em operacoes -->
    <div id="messagesSlidingDiv" >
      &nbsp;
    </div>

    <audio id="alertBeep" >
      <source src="./assets/sounds/error_beep.mp3" type="audio/mpeg">    
    </audio>

    <!-- icone cachorrinho , canto inferior direito  -->
    <div class='_doggy'  id='divDoggy'></div>
    <div class='_doggy_1' id='divDoggy_1'></div>
    <div class='_doggy_2' id='divDoggy_2'></div>
    <div class='_doggy_3' id='divDoggy_3'></div>

    <div class='absolute bottom-0  flex flex-row left-4 text-[20px] font-bold gap-7 h-[50px]'>
      <div class='flex flex-row items-center gap-3' >
        Frontend:
        <img src="./assets/images/vue.svg" alt='' />
      </div>

    </div>



  </div>


</template>


<!-- composition API , way cleaner then options API -->
<script setup>
  import { ref, onMounted, watch, onBeforeMount  } from 'vue';
  import MenuSuperior from './MenuSuperior.vue';
  import Datatable from './components/Datatable.vue';

  // alguns efeitos jquery 
  import 'jquery-ui-bundle';
  import 'jquery-ui-bundle/jquery-ui.min.css';

  import { toWheelMenu, prepareLoadingAnimation, slidingMessage , preparePuppyIcon, loadScripts } from './assets/js/utils.js'

  const neededJsLoaded = ref(false)   

  const toRefreshDatatable = ref(0)  

  const backendChanged = ref(true)
  const isLoading = ref(true)
  const error = ref(null)

  // backend depende da escolha do usuario e se esta rodando local ou na AWS
  //const backendUrl = ref('http://ec2-54-233-183-5.sa-east-1.compute.amazonaws.com:8073')  
  const backendUrl = ref('http://localhost')  

  // opcao do menu atual
  const selectedMenu = ref(0)

  // qual tabela deve ser exibida no momento
  const currentViewedDatatable = ref('')

  // se é hora de exibir texto sobre a aplicação
  const toDisplayAbout = ref(true)

  // se é hora de exibir uma tabela
  const toDisplayDatatable = ref(false)





  //***************************************************************************
  // usuario escolheu opcao no menu (top)
  //***************************************************************************
  const setNewselectedMenu = (itemMenu) => {
    selectedMenu.value = itemMenu
  }

  //***************************************************************************
  // usuario escolheu alguma tabela pra ser exibida
  //***************************************************************************
  const setDatatableToDisplay = (datatable) => {
    toDisplayAbout.value = false;
    toDisplayDatatable.value = true;

    toRefreshDatatable.value++  // força re-render da tabela escolhida

    currentViewedDatatable.value = datatable
  }

    //***************************************************************************
  //***************************************************************************
  onMounted( () => {
      // handler do teclado, facilita vida do usuario, exemplo: F2= grava registro, Enter= avança campo, etc
      window.onkeydown = (event) => {
        onKeyDown(event)
      };


      // nao consigo preparar a aplicacao pra ser responsiva agora, so tenho 24h pra entregar o teste
      // se usuario redimensionar, fecha qq modal e redesenha tela
      window.onresize = () => {
        setTimeout(() => {
          window.location.reload();  
        }, 500);
      }
 
      preparePuppyIcon()
      prepareLoadingAnimation()

      isLoading.value = true

  })

  //***************************************************************************
  // se usuario alterar backend atual, força atualizacao da datatable atual
  // isso se alguma datatable estiver sendo exibida, claro
  //*************************************************************************** 
  watch([backendChanged], () => { 
      isLoading.value = true;

      Promise.all( [fetchExpressions()] ).then(() => {
        isLoading.value = false
        // componentes afetados
        toRefreshDatatable.value++
        toRefreshSchedule.value++
      })        
    },
    { immediate: false }
  )




/************************************************************************************************************************************************************
facilita vida do usuario com atalhos teclado
F2= grava registro, Esc= fecha modal, etc
************************************************************************************************************************************************************/

const onKeyDown = (e) =>  {

  // usuario pressionou Enter ou setas acima/abaixo, avança/retrocede campo do form atual
  if ( (e.which == 13 || e.which == 38 || e.which == 40)  && $('.text_formFieldValue').is(':focus') )   { 
        // tive que inventar a propriedade 'sequence', porque o VITE reclama muito em relacao a tabIndex

        let tab =  $(':focus').attr("sequence");
        if (e.which==13 || e.which == 40)  tab++;
        else if (e.which==38)  tab--;

        e.preventDefault()

        // coloca foco no campo anterior/proximo
        $("[sequence='"+tab+"']").focus();          
  }

  // usuario pressionou Enter com a caixa de pesquisa (datatable) focada, dispara pesquisa 
  if (e.which == 13 && $('#txtTableSearchText').is(':focus') )   { 
    if (  $.trim($('#txtTableSearchText').val()).length<3 ) 
      slidingMessage('Mínimo 3 caracteres', 2000)        
    else 
      $('#triggerSearchBox').click()
  } 
    
  
  // usuario pressionou F2 com algum form edicao aberto, dispara gravacao
  // pressionou Esc, fecha qq modal
  if (e.which == 27 || e.which == 113)   { 
        let editionForm = typeof $('#fornecedorForm').attr("id")!='undefined' || 
                          typeof $('#usuarioForm').attr("id")!='undefined' 

        // dispara botao 'fechar'
        if (editionForm)  {
          if (e.which == 27)   $('#btnCLOSE').trigger('click')

          // 'F2= salvar'
          if (e.which == 113)   $('#btnSALVAR').trigger('click')   // f2 was pressed
        }
  }
}


</script>


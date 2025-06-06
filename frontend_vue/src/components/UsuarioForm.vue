

<template>

<!-- form container  -->
<div  class="flex flex-col w-[95%] max-w-[1300px] overflow-hidden pt-8 "  id='usuarioForm'>

  <div  class="flex flex-col w-full bg-white relative rounded-lg"  >

    <!-- titulo e botoes topo superior direito  -->
    <div id='divWINDOW_TOP'>
      
      <div id='divWINDOW_TITLE'>

        <div v-if="formHttpMethodApply=='POST'">
          Novo Usuário
        </div>
        <div v-if="formHttpMethodApply=='PATCH'">
          Editar Usuário
        </div>

      </div>

      <div class='flex flex-row '>
          <div id='divWINDOW_DRAG' class='mr-8'   >
            &nbsp;
          </div>

          <div class='divWINDOW_BUTTON mr-2'  aria-hidden="true" @click='userNeedsHelp' >
            &nbsp;&nbsp;[ ? ]&nbsp;&nbsp;
          </div>

          <div class='divWINDOW_BUTTON mr-6'  @click="emit('closeForm')"  aria-hidden="true" > 
            &nbsp;&nbsp;[ X ]&nbsp;&nbsp;
          </div>
      </div>
    
    </div>

    <!-- campos do form -->
    <div class="flex flex-col w-full h-auto  px-4 my-6" >

      <div class="flex flex-row w-full gap-[10px] border-b-2 pb-4">

        <div class="flex flex-row w-full mt-5 ml-3">

          <div class="flex flex-col text-right mr-4">  
            <div class='h-[55px] pt-1 mb-5'>Nome:</div>
          </div>

          <div class="flex flex-col  grow max-w-[60%]">  

            <div class='h-[55px] mb-5'>
              <div>
                <input type="text" autocomplete="off" sequence="1"   id="txtNome" maxlength='150' minlength='5' class='text_formFieldValue w-full ml-3'  
                  msgErro='Preencha o nome' >  
              </div>
            </div>

          </div>

        </div>

      </div>


    </div>

    <!-- botoes salvar/sair -->
    <div class="flex flex-row w-full justify-between px-6 border-t-[1px] border-t-gray-300 py-2">
      <button  id="btnCLOSE" class="btnCANCELAR" @click="emit('closeForm')" >Esc= cancelar</button>

      <button v-if='! exibirBtnExcluir' id="btnSALVAR" class="btnSALVAR" @click="salvarUsuario()" aria-hidden="true">F2= salvar</button>
      <button v-if='exibirBtnExcluir==true' id="btnDELETE" class="btnDELETE" @click="excluirUsuario()" aria-hidden="true">Excluir</button>
    </div>

  </div> 

</div> 

</template>


<script setup>
import { onMounted, ref  } from 'vue';
import { makeWindowDraggable, slidingMessage, cnpjOK, getNumbersFromString  } from '../assets/js/utils.js'
const emit = defineEmits( ['showLoading', 'hideLoading', 'closeForm','refreshDatatable', 'setCurrentId'] );

const props = defineProps( ['backendUrl', 'formHttpMethodApply', 'currentId', 'exibirBtnExcluir'] )

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************

onMounted( () => {
  getUsuarioFormPopulatedAndReady()
})

//************************************************************************************************************************************************************
// usuario clicou no icon '[ ? ]', canto superior direito do form
//************************************************************************************************************************************************************
const userNeedsHelp = () => {
  slidingMessage('Mensagem de auxílio para usuário...', 3000)
}


/************************************************************************************************************************************************************
fetch do registro atual
************************************************************************************************************************************************************/
async function getUsuarioFormPopulatedAndReady() { 

  // if o form nao foi chamado para insercao de registro, faz fetch do reg
  if ( props.formHttpMethodApply != 'POST')   {

    emit('showLoading')

    try {
        let _route_ = `${props.backendUrl}/usuario/${props.currentId}`

        await fetch(_route_, {method: 'GET'})

        .then( (response) => {

          if (!response.ok) {
            return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
          }
          return response.json();
        })

        .then( (registro) => {
          emit('hideLoading')

          $('#txtNome').val( registro.nome )

          preparaFormUsuario() 
        })


    } 
    catch(err) {
      emit('hideLoading')
      throw new Error(`Usuário Erro Fatal= ${err.message}`);
    }

  }

  // formulario foi chamado para add registro
  if ( props.formHttpMethodApply === 'POST')   {
    preparaFormUsuario()
  }

}


/************************************************************************************************************************************************************
coloca foco no 1o campo e prepara mascaras
************************************************************************************************************************************************************/
const preparaFormUsuario = () => { 

  setTimeout(() => {
    $('#txtNome').focus()    

  }, 100);

  // faz o form ser arrastavel
  makeWindowDraggable('divWINDOW_TOP', 'usuarioForm')
}



/********************************************************************************************************************************************************
 valida dados no front end primeiramente
********************************************************************************************************************************************************/
async function salvarUsuario()  {

  let cmpFocar = ''
  let erroExibir = ''

  var formData = new FormData();  // body do request

  // percorre campos para crítica dos dados digitados
  $("input[type='text']").each(function() {
    
    let vlr = $.trim( $(this).val() );
    let minimo = $(this).attr('minlength')   // minlength, propriedade inventada para facilitar
    let maximo = $(this).attr('maxlength')

    // msgErro, propriedade inventada para auxiliar
    let erroMsg = $(this).attr('msgErro') + ' - Mín.: '+minimo+ ' - Máx.: '+maximo

    let cmpJSON = $(this).attr('id')   
    cmpJSON = cmpJSON.replace('txt','').toLowerCase();

    if ( vlr.length < parseInt(minimo, 10) || vlr.length > parseInt(maximo, 10) )   {
      erroExibir = erroMsg 
      cmpFocar = $(this).attr('id')

      return false
    }
    // vai montando body do request
    formData.append(cmpJSON, vlr)    
  });

  if (erroExibir!='') {
    slidingMessage(erroExibir, 3000)
    $(`#${cmpFocar}`).focus()
    return false;
  }
  let route = '', acao = ''
  if (props.formHttpMethodApply=='POST')  {
    route += 'usuario'        
    acao = 'Registro adicionado'
  }
  if (props.formHttpMethodApply=='PATCH')  {
    route += `usuario/${props.currentId}`   
    acao = 'Alteração feita'
  }

  // formHttpMethodApply= POST, PATCH ou DELETE
  setTimeout(() => {
    emit('showLoading')    
  }, 10);
  
  // insere ou edita reg
  await fetch(`${props.backendUrl}/${route}`, {method: props.formHttpMethodApply, body: formData})

  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {throw new Error(`HTTP error! <strong>${response.status}` + text + '</strong>')})
    }
    return response.text()
  })
  .then((msg) => {
    slidingMessage(acao, 1500)        
    emit('hideLoading')
    setTimeout(() => {

      // qdo reg inserido com sucesso, retorna string => __successo__|id reg criado
      if ( props.formHttpMethodApply=='POST' ) {
        let currentId = msg.split('|')[1]  // id recem gravado 
        emit('setCurrentId', currentId)    // devolve para Datatable o ID do reg recem criado
      }

      emit('closeForm')  
      emit('refreshDatatable')  

    }, 1700);
    
  })
  .catch((error) => {
    emit('hideLoading')
    slidingMessage(error, 3000)        
  })  
}


/********************************************************************************************************************************************************
exclui registro
********************************************************************************************************************************************************/
async function excluirUsuario()  {

  setTimeout(() => {
    emit('showLoading')    
  }, 10);
  
  // insere ou edita reg
  await fetch(`${props.backendUrl}/usuario/${props.currentId}`, {method: 'DELETE'})

  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {throw new Error(`HTTP error! <strong>${response.status}` + text + '</strong>')})
    }
    return response.text()
  })
  .then((msg) => {
    slidingMessage('Registro excluído', 1500)        
    emit('hideLoading')

    setTimeout(() => {
      emit('closeForm')  
      emit('refreshDatatable')  

    }, 1700);
    
  })
  .catch((error) => {
    emit('hideLoading')
    slidingMessage(error, 3000)        
  })  
}

</script>

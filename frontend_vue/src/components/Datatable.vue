
<template>
 
    <div>    <!-- exigencia do Vue, elementos agrupados em 1 elemento -->


    <div class='flex flex-col h-full ' id='datatableContainer'>

      <!-- titulo da tabela, sobra espaço canto direito caso precise adicionar opcoes  -->
      <div class="flex flex-row h-[60px] w-full justify-between border-b-2 " >

          <div class="flex flex-row pt-1 w-full justify-between">
              <div class='pl-4 pt-2 text-2xl'> {{ title }} </div>
          </div> 

      </div>


      <!-- botoes de acao da tabela (exibir somente regs ativos, inativos, novo reg, etc)  -->
      <div class='datatableToolbar' >
        <div class='flex flex-row'>

            <!--  search box --> 
            <div class="flex flex-col" >  
              <input type="text" class='txtTABLE_SEARCHBOX'  id='txtTableSearchText'  autocomplete="off" 
                  @focus='showTipSearchbox=true' 
                  @blur='showTipSearchbox=false' 
                  @mouseenter="focusSearchBox" />

              <div class="flex flex-row pt-1 text-xs"  >  
                  <div v-if="showTipSearchbox">
                    <span class="text-blue-900 font-bold">Enter</span>
                    <span class="text-black">= pesquisar</span>
                  </div>
                  <div v-else>&nbsp;</div> 
              </div>

              <!-- botao escondido que é acionado quando usuario pressiona Enter na search box -->
              <button id='triggerSearchBox' v-show="false" @click='fetchData()'></button>
            </div>

            <!-- reset do filtro --> 
            <div id='btnResetTextTableFilter'  class='putPrettierTooltip' title="Resetar o filtro"
                :class="filterApplied ? 'btnTABLE_CANCEL_FILTER_ACTIVE' : 'btnTABLE_CANCEL_FILTER_INACTIVE'"
                @click="forceHideToolTip();clearFilter()"  aria-hidden="true">
            </div> 
          
        </div>

        <div className=' flex flex-col'>
            <!-- botoes de acao -->
            <div class=' flex flex-row items-start  h-full gap-5 pt-3 '>

              <!-- exibe/esconde registros ativos -->
              <div v-if="currentStatus=='active'" class='btnTABLE_ONLY_ACTIVE_RECORDS_ON putPrettierTooltip' 
                      title='Somente ativos'
                      @click="forceHideToolTip();currentStatus='all'" 
                      aria-hidden="true"></div>   

              <div v-else class='btnTABLE_ONLY_ACTIVE_RECORDS_OFF putPrettierTooltip' 
                    title='Somente ativos' 
                    @click="forceHideToolTip();currentStatus='active'" 
                    aria-hidden="true"></div>   

              <!-- exibe/esconde registros inativos -->
              <div v-if="currentStatus=='inactive'" class='btnTABLE_ONLY_INACTIVE_RECORDS_ON putPrettierTooltip' 
                    title='Somente inativos'
                    @click="forceHideToolTip();currentStatus=''" 
                    aria-hidden="true"></div>   

              <div v-else class='btnTABLE_ONLY_INACTIVE_RECORDS_OFF putPrettierTooltip' 
                  title='Somente inativos'
                  @click="forceHideToolTip();currentStatus='inactive'" 
                  aria-hidden="true"></div>   

              <!-- novo registro -->
              <div  class='btnTABLE_NOVO_REGISTRO putPrettierTooltip' title='Adicionar registro' @click="editForm();" aria-hidden="true"></div>   
            </div>

            <!-- legenda -->
            <div :style="{paddingRight: '10px', fontSize: '12px', display:'flex', flexDirection: 'row', justifyContent: 'flex-end'}"  >
                <div :style="{paddingTop: '10px'}">Legenda: <span :style="{backgroundColor: 'red'}">&nbsp;&nbsp;&nbsp;</span>= inativos</div>
            </div>

         </div>

      </div>

      <!-- exibe titulos colunas -->
      <div class="datatableHeader" > 
            <div v-for='column in columns' :key="column" :style="{width: column.width, paddingLeft: '5px'}">{{ column.title }}</div> 
      </div>          

      <!-- lista registros  -->
      <template v-if='records'>
        <div id='rowsContainer'>
          <div  class="DatatableRows" v-for='record in records' :key="record"  @click="rowClicked('tr_'+record.id)" :id="'tr_'+record.id"> 

              <!-- se o status= ativo, imprime em preto, se nao, vermelho -->
              <div :class="record.active ? 'DatatableRow' : 'DatatableRowInactiveRecord'"   >         

                <!-- imprime cada coluna -->
                <template v-for='(column, index) in columns'   >         

                  <div v-if='index < (columns.length-1)'  
                      :style="{width: column.width, paddingLeft: '15px'}" 
                      :key="'1tr-'+index" 
                     :class="record.active==0 ? 'text-red-500 font-bold' : 'text-black'"  
                     class='datatableColumn'  >
                    {{ record[column.fieldname] }}
                  </div>

                  <!-- se a ultima coluna foi impressa e reg ativo, adiciona 3 icones de acao (editar, excluir, desativar ) -->
                  <div v-if='index === columns.length-1 && record.active==1' class='actionColumn' :style="{width: column.width}" :key="'tr-'+index" >
                      <div class='actionIcon' @click='editForm(record.id)' ><img alt=''  src='../assets/images/edit.svg' /></div>
                      <div class='actionIcon'  @click='deleteRecord'><img alt=''   src='../assets/images/delete.svg' /></div>
                      <div class='actionIcon' @click='changeStatus(record.id)'><img alt=''  src='../assets/images/active.svg' /></div>
                  </div>   

                  <!-- se a ultima coluna foi impressa e reg inativo, adiciona icones (re)ativar -->
                  <div v-if='index === columns.length-1 && record.active==0' class='actionColumn' :style="{width: column.width}" :key="'tr-'+index"  >
                      <div class='actionIconNull'>&nbsp;</div>
                      <div class='actionIconNull'>&nbsp;</div>
                      <div class='actionIcon' @click='changeStatus(record.id)'><img alt=''   src='../assets/images/inactive.svg' /></div>
                  </div> 

              </template>
            </div>
          </div>
      </div>
      </template>

  </div>


  <!-- usuario pediu para editar/inserir fornecedor -->  
  <div v-if="exibirFormFornecedor" id='backDrop' class='w-full h-full  absolute flex items-center justify-center left-0 top-0 z-10 bg-[rgba(0,0,0,0.5)]' @click.self='exibirFormFornecedor=false' aria-hidden="true"  >  
    <FornecedorForm 
        :backendUrl='props.backendUrl' 
        :currentId='currentId'
        :formHttpMethodApply = 'formHttpMethodApply'  
        @closeForm="exibirFormFornecedor=false"  
        @showLoading="emit('showLoading')" 
        @hideLoading="emit('hideLoading')"  
        @refreshDatatable = "fetchData();"   />
  </div>


</div>

</template>


<script setup>
import { slidingMessage, forceHideToolTip , divStillVisible, scrollUntilElementVisible, improveTooltipLook } from '../assets/js/utils.js'
import { onMounted, ref, watch  } from 'vue';
import FornecedorForm from './FornecedorForm.vue';

const emit = defineEmits( ['showLoading', 'hideLoading' ] );
const props = defineProps( ['currentViewedDatatable', 'backendUrl' ] )

// registros que serao listados
const records = ref(null)  

// formulario de fornecedor deve ser mostrado?
const exibirFormFornecedor = ref(false)

// colunas que serao exibidias dependendo da tabela sendo vista 
let columns = []

let title = ''

// usuario pediu ver tabela fornecedores
if (props.currentViewedDatatable === 'fornecedores')   {
  columns.push({ fieldname: "id", width: "5%", title: 'Id', id: 'col1', boolean: false },
              { fieldname: "razao_social", width: "calc(75% - 150px)", title: 'Razão Social', id: 'col2', boolean: false},
              { fieldname: "cnpj", width: "20%", title: 'CNPJ', id: 'col3', boolean: false} )
  title = 'Fornecedores'
}

// usuario pediu ver tabela fornecedores
if (props.currentViewedDatatable === 'usuarios')   {
  columns.push({ fieldname: "id", width: "5%", title: 'Id', id: 'col1', boolean: false },
              { fieldname: "nome", width: "calc(25% - 150px)", title: 'Nome', id: 'col2', boolean: false},
              { fieldname: "is_admin", width: "35%", title: 'Administrador', id: 'col3', boolean: true} )
  title = 'Usuários'
}


// ultima coluna, acoes (editar, excluir, etc)
columns.push( {name: 'actions', width: '150px', title: '', id: 3} )


// id do registro que sera editado/visualizado
const currentId = ref(null)

// metodo que sera chamado no formulario do registro (get, patch)
const formHttpMethodApply = ref(null)

// tipo de registro que deve ser buscado (ativo, inativo, todos)
const currentStatus = ref('all')  

// mostrar a frase 'Enter= pesquisar' quando foco esta na search box
const showTipSearchbox = ref(false)

// tem filtro aplicado no exato momento?
const filterApplied = ref(false)  



//*****************************************************************************
//*****************************************************************************
const clearFilter = () => {
  $('#txtTableSearchText').val('');
  filterApplied.value=false
}
//*****************************************************************************
// linha (div) da datatable foi clicada, coloca em destaque
//*****************************************************************************
const rowClicked = (id) =>   {
  let tr = $(`#${id}`)

  $('.DatatableRow_selected').removeClass('DatatableRow_selected')
  tr.addClass('DatatableRow_selected')

}

//*****************************************************************************
// se usuario passar mouse sobre a search box, coloca foco nela
//*****************************************************************************
const focusSearchBox = (e) => {
  if (! $(e.target).is(':focus') ) $(e.target).focus()
}

//*****************************************************************************
//*****************************************************************************
onMounted( () => {    
  improveTooltipLook()

  fetchData()
})


//***************************************************************************
// se usuario aplicou filtro, recarrega tabela
//*************************************************************************** 
watch([currentStatus, filterApplied], () => { 
  fetchData()
  },
  { immediate: false }
)


//***************************************************************************
//*************************************************************************** 
async function fetchData() {
  emit('showLoading')

  // zera altura da tabela para que jquery mais abaixo aumente o maximo possivel
  // perdi quase 6 horas tentando fazer o tailwind aumentar sozinho, deve haver um jeito
  // mas nao tenho mais tempo, outra hora resolvo
  $('#rowsContainer').height('0')

  // se a search box foi preenchida, leva em consideracao na busca
  // qual campo sera pesquisado, quem define é o backend
  // se search box especificada, ignora filtro ativo/inativo

  let route = `${props.backendUrl}/${props.currentViewedDatatable}/${currentStatus.value}` 

  // concatena eventual search box
  let stringSearch = $.trim( $('#txtTableSearchText').val() )
  route += (stringSearch!='' ? `/${stringSearch}` : '')

  filterApplied.value = stringSearch!='' ? true : false ;

  await fetch(route) 
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
    }
    return response.json()
  })
  .then((data) => {
    emit('hideLoading')
    records.value = data;        

    // aumenta a div que contem a datatable, desisto de tentar fazer isso com tailwind/flex, nao tenho mais tempo
    setTimeout(() => {
        if( divStillVisible('#rowsContainer') ) {
          while ( divStillVisible('#rowsContainer') ) { $('#rowsContainer').height( $('#rowsContainer').height()+5 );     }
        }        

        // se ha um registro recem inserido/alterado , rola a datatable ate exibid lo
        let lastRowUpdated = 'tr_'+currentId.value
        // highlight the last updated row
        setTimeout(() => {
          scrollUntilElementVisible(lastRowUpdated)
          $(`#${lastRowUpdated}`).addClass('DatatableRow_selected')  
        }, 100);
        

    }, 300);



  })
  .catch((error) => {
    emit('hideLoading')
    slidingMessage('Error= '+error, 3000)        
  })  
}

//***************************************************************************
// usuario clicou em determinado registro para editar ou pediu para add um reg
//*************************************************************************** 
const editForm = (id='') => {
  currentId.value = id;

  if (id=='') formHttpMethodApply.value = 'POST'  // add record
  else formHttpMethodApply.value = 'PATCH'  // update record

  if (props.currentViewedDatatable === 'fornecedores')   {  
    exibirFormFornecedor.value = true
  }

}

//***************************************************************************
// usuario pediu para alterar status de um registro
//*************************************************************************** 
async function changeStatus (id) {

  let route = `${props.backendUrl}/${props.currentViewedDatatable}/status/${id}`

  emit('showLoading')

  await fetch(route, {method: 'PATCH'})

  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
    }
    return response.text()
  })
  .then((data) => {
    slidingMessage('Status alterado', 3000)         
    fetchData()
  })
  .catch((error) => {
    emit('hideLoading')
    slidingMessage('Error= '+error, 3000)        
  })  
}


//***************************************************************************
// usuario pediu para excluir registro
//*************************************************************************** 
const deleteRecord = (id) => {

  slidingMessage('deletar', 3000)        
  slidingMessage('deletar', 3000)        


}

</script>
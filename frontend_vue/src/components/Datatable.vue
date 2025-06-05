
<template>

    <div>    <!-- exigencia do Vue, tudo agrupado em 1 elemento -->


    <div class='flex flex-col h-full ' id='datatableContainer'>

      <!-- titulo da tabela, sobra espaço canto direito caso precise adicionar opcoes  -->
      <div class="flex flex-row h-[60px] w-full justify-between border-b-2 " >

          <div class="flex flex-row pt-1 w-full justify-between">
              <div class='pl-4 pt-2 text-2xl'> {{ title }} </div>
          </div> 

      </div>


      <!-- botoes de acao da tabela (somente regs ativos, inativos, novo reg, etc)  -->
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
                    <span class="text-black">= {{ expressions.search_verb }}</span>
                  </div>
                  <div v-else>&nbsp;</div> 
              </div>

              <!-- botao escondido que é acionado quando usuario pressiona Enter na search box -->
              <button id='triggerSearchBox' v-show="false" @click='fetchData()'></button>
            </div>

            <!-- reset do filtro --> 
            <div id='btnResetTextTableFilter'  class='putPrettierTooltip' :title="expressions.reset_filter"
                :class="filterApplied ? 'btnTABLE_CANCEL_FILTER_ACTIVE' : 'btnTABLE_CANCEL_FILTER_INACTIVE'"
                @click="forceHideToolTip();clearFilter()"  aria-hidden="true">
            </div> 
          
        </div>

        <div className=' flex flex-col'>
            <!-- botoes de acao -->
            <div class=' flex flex-row items-start  h-full gap-5 pt-3 '>

              <!-- exibe/esconde registros ativos -->
              <div v-if="currentStatus=='active'" class='btnTABLE_ONLY_ACTIVE_RECORDS_ON putPrettierTooltip' 
                      :title="expressions.only_active" 
                      @click="forceHideToolTip();currentStatus=''" 
                      aria-hidden="true"></div>   

              <div v-else class='btnTABLE_ONLY_ACTIVE_RECORDS_OFF putPrettierTooltip' 
                    :title="expressions.only_active" 
                    @click="forceHideToolTip();currentStatus='active'" 
                    aria-hidden="true"></div>   

              <!-- exibe/esconde registros inativos -->
              <div v-if="currentStatus=='inactive'" class='btnTABLE_ONLY_INACTIVE_RECORDS_ON putPrettierTooltip' 
                    :title="expressions.only_inactive" 
                    @click="forceHideToolTip();currentStatus=''" 
                    aria-hidden="true"></div>   

              <div v-else class='btnTABLE_ONLY_INACTIVE_RECORDS_OFF putPrettierTooltip' 
                  :title="expressions.only_inactive" 
                      @click="forceHideToolTip();currentStatus='inactive'" 
                  aria-hidden="true"></div>   

              <!-- novo registro -->
              <div  class='btnTABLE_NOVO_REGISTRO putPrettierTooltip' :title="expressions.add_record" @click="editForm();" aria-hidden="true"></div>   
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
                    {{ record[column.fieldname] }} >
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
    <ExpressionForm 
        :expressions='expressions' 
        :backendUrl='props.backendUrl' 
        :currentId='currentId'
        :formHttpMethodApply = 'formHttpMethodApply'  
        @closeExpressionForm="exibirFormFornecedor=false"  
        @showLoading="emit('showLoading')" 
        @hideLoading="emit('hideLoading')"  juca
        @refreshDatatable = "fetchData();emit('toRefreshExpressions');"   />
  </div>



</div>

</template>


<script setup>
import { slidingMessage, forceHideToolTip , divStillVisible, scrollUntilElementVisible, improveTooltipLook } from '../assets/js/utils.js'
import { onMounted, ref, watch  } from 'vue';
import CarForm from './CarForm.vue';
import ExpressionForm from './ExpressionForm.vue';

const emit = defineEmits( ['showLoading', 'hideLoading','setDatatableToDisplay','toDisplayAbout', 'toRefreshtopMenu'] );
const props = defineProps( ['currentViewedDatatable', 'currentBackend', 'backendUrl', 'imagesUrl', 'expressions' ] )

// registros que serao listados
const records = ref(null)  

// formulario de fornecedor deve ser mostrado?
const exibirFormFornecedor = ref(false)

// colunas que serao exibidias dependendo da tabela sendo vista 
let columns = []

let title = ''

// se usuario pediu ver tabela fornecedores
if (props.currentViewedDatatable === 'fornecedores')   {
  columns.push({ fieldname: "id", width: "5%", title: 'Id', id: 'col1', boolean: false },
              { fieldname: "nome", width: "calc(75% - 150px)", title: 'Nome', id: 'col2', boolean: false},
              { fieldname: "cpnj", width: "20%", title: 'CNPJ', id: 'col3', boolean: false} )
  title = 'Fornecedores'
}

if (props.currentViewedDatatable === 'usuarios')   {
  columns.push({ fieldname: "id", width: "5%", title: 'Id', id: 'col1', boolean: false },
              { fieldname: "nome", width: "calc(25% - 150px)", title: 'Nome', id: 'col2', boolean: false},
              { fieldname: "is_admin", width: "35%", title: 'Administrador', id: 'col3', boolean: true} )
  title = 'Usuários'
}


// ultima coluna, acoes (editar, excluir, etc)
columns.push( {name: 'actions', width: '150px', title: '', id: 3} )


// id of the current car being edited or viewed
const currentId = ref(null)

// method being used with the booking form
const formHttpMethodApply = ref(null)

// which type of status should be viewed at the moment, active or inactive
const currentStatus = ref('all')  

// if must show the 'Press Enter to search' message underneath the search box
const showTipSearchbox = ref(false)

// controls if the searchbox filter was applied
const filterApplied = ref(false)  



//*****************************************************************************
//*****************************************************************************
const clearFilter = () => {
  $('#txtTableSearchText').val('');
  filterApplied.value=false
}
//*****************************************************************************
//*****************************************************************************
const rowClicked = (id) =>   {
  let tr = $(`#${id}`)

  $('.DatatableRow_selected').removeClass('DatatableRow_selected')
  tr.addClass('DatatableRow_selected')

}

//*****************************************************************************
// if users hovers mouse over search box, put the focus in it 
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
// if user changes current status, refresh table base on the last choice
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

  $('#rowsContainer').height('0')

  // in the case of expressions being fetched, once the json parameter is sent,   
  // no matter the country parameter, backend will return expressions from USA/Brazil

  // if user fullfilled searchbox, consider it
  // what field will be searched in the backend depends on the table, the backend decides
  // if a text is specified to search for, the status will be ignored in the backend

  let stringSearch = $.trim( $('#txtTableSearchText').val() )
  let route
  if ( props.currentViewedDatatable === 'expressions')   
    route = `${props.backendUrl}/expressions/json/__no_matter__/${currentStatus.value}` 

  else 
    route = `${props.backendUrl}/${props.currentViewedDatatable}/${currentStatus.value}`  

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

    // strecth the div containing the records to the maximum
    setTimeout(() => {
        if( divStillVisible('#rowsContainer') ) {
          while ( divStillVisible('#rowsContainer') ) { $('#rowsContainer').height( $('#rowsContainer').height()+5 );     }
        }        
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
// user click in a given record to edit or ask to add new record (id='')
//*************************************************************************** 
const editForm = (id='') => {
  currentId.value = id;

  if (id=='') formHttpMethodApply.value = 'POST'  // add record
  else formHttpMethodApply.value = 'PATCH'  // update record

  if (props.currentViewedDatatable === 'cars')   { 
    showCarForm.value = true
  }

  if (props.currentViewedDatatable === 'expressions')   {  
    exibirFormFornecedor.value = true
  }

}

//***************************************************************************
// user click in a given record to change its status (active/inactive)
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
    slidingMessage(props.expressions.status_changed, 3000)         
    fetchData()
    // ask App.vue to refresh cars list, once one record's been activate/deactivated
    emit('toRefreshtopMenu') 
  })
  .catch((error) => {
    emit('hideLoading')
    slidingMessage('Error= '+error, 3000)        
  })  
}


//***************************************************************************
// user click in a given record to delete
//*************************************************************************** 
const deleteRecord = (id) => {

  slidingMessage(props.expressions.unavailable_option, 3000)        

}

</script>
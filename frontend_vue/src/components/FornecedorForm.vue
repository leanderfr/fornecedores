
<template>

<!-- form container  -->
<div  class="flex flex-col w-[95%] max-w-[1300px] overflow-hidden pt-8 "  id='fornecedorForm'>

  <div  class="flex flex-col w-full bg-white relative rounded-lg"  >

    <!-- titulo e botoes topo superior direito  -->
    <div id='divWINDOW_TOP'>
      
      <div id='divWINDOW_TITLE'>

        <div v-if="formHttpMethodApply=='POST'">
          Novo Fornecedor
        </div>
        <div v-if="formHttpMethodApply=='PATCH'">
          Editar Fornecedor
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

        <div class="flex flex-row w-full ">

          <div class="flex flex-col basis-[150px] text-right mr-4">  
            <div class='h-[55px] pt-1 mb-5'>CNPJ:</div>
            <div class='h-[45px] pt-1'>Nome:</div>
            <div class='h-[45px] pt-1'>Nome Fantasia:</div>          
          </div>

          <div class="flex flex-col  grow max-w-[60%]">  

            <div class='h-[55px] mb-5'>
              <input type="text" autocomplete="off" sequence="1"   id="txtCNPJ" maxlength='200' minlength='3' class='text_formFieldValue w-full'  >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="2"   id="txtRazao" maxlength='30' minlength='3' class='text_formFieldValue '  >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="3"   id="txtFantasia" maxlength='200' minlength='3' class='text_formFieldValue w-[300px]'  >  
            </div>

          </div>

        </div>











      </div>


    </div>

    <!-- botoes salvar/sair -->
    <div class="flex flex-row w-full justify-between px-6 border-t-[1px] border-t-gray-300 py-2">
      <button  id="btnCLOSE" class="btnCANCELAR" @click="emit('closeForm')" >Esc= cancelar</button>

      <button  id="btnSALVAR" class="btnSALVAR" @click="saveFornecedor()" aria-hidden="true">F2= salvar</button>
    </div>

  </div> 

</div> 

</template>


<script setup>
import { onMounted, ref  } from 'vue';
import { makeWindowDraggable, slidingMessage   } from '../assets/js/utils.js'
const emit = defineEmits( ['showLoading', 'hideLoading', 'closeForm','refreshDatatable'] );

const props = defineProps( ['backendUrl', 'formHttpMethodApply', 'currentId'] )

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************

onMounted( () => {
  getFornecedorFormPopulatedAndReady()
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
async function getFornecedorFormPopulatedAndReady() { 

  // if o form nao foi chamado para insercao de registro, faz fetch do reg
  if ( props.formHttpMethodApply != 'POST')   {

    emit('showLoading')

    try {
        let _route_ = `${props.backendUrl}/fornecedor/${props.currentId}`

        await fetch(_route_, {method: 'GET'})

        .then( (response) => {

          if (!response.ok) {
            return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
          }
          return response.json();
        })

        .then( (registro) => {
          emit('hideLoading')
          $('#txtRazao').val( registro.nome )
          $('#txtCNPJ').val( registro.cnpj )
          $('#txtFantasia').val( registro.fantasia )

          preparaFormFornecedor() 
        })


    } 
    catch(err) {
      emit('hideLoading')
      throw new Error(`Fornecedor Erro Fatal= ${err.message}`);
    }

  }

  // formulario foi chamado para add registro
  if ( props.formHttpMethodApply === 'POST')   {
    preparaFormFornecedor()
  }

}


/************************************************************************************************************************************************************
put focus first field and prepare masks
************************************************************************************************************************************************************/
const preparaFormFornecedor = () => { 

  setTimeout(() => {
    $('#txtCNPJ').focus()    
  }, 100);

  // faz o form ser arrastavel
  makeWindowDraggable('divWINDOW_TOP', 'fornecedorForm')

  $('#txtCNPJ').mask('00.000.000/0000-00', {reverse: true});
}





/********************************************************************************************************************************************************
 valida dados no front end primeiramente
********************************************************************************************************************************************************/
async function saveFornecedor()  {

  let error = ''
  let toFocus = ''

  if ( $('#txtRazao').val().trim().length < parseInt($('#txtRazao').attr('minlength'), 10)  )  {
      error = 'Preencha a razão social do fornecedor - Mín '+$('#txtRazao').attr('minlength')
      toFocus = 'txtRazao'
  }
//  if ( $('#txtCNPJ').val().trim().length < parseInt($('#txtCNPJ').attr('minlength'), 10) )  
//      error = 'CNPJ inválido'
  if ( $('#txtFantasia').val().trim().length < parseInt($('#txtFantasia').attr('minlength'), 10) )   {
      error = 'Preencha o nome fantasia - Mín '+$('#txtFantasia').attr('minlength')
      toFocus = 'txtFantasia'
  }


  // show any error detected
  if (error!='') {
    slidingMessage(error, 3000)
    $(`#${toFocus}`).focus()    
    return;
  }

  var formData = new FormData(); 
  formData.append('razao_social', $('#txtRazao').val())
  formData.append('cnpj', $('#txtCNPJ').val())
  formData.append('nome_fantasia', $('#txtFantasia').val())

  let route = '', acao = ''
  if (props.formHttpMethodApply=='POST')  {
    route += 'fornecedor'        
    acao = 'Registro adicionado'
  }
  if (props.formHttpMethodApply=='PATCH')  {
    route += `fornecedor/${props.currentId}`   
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
      return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
    }
    return response.text()
  })
  .then((msg) => {
    slidingMessage(acao, 1500)        
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

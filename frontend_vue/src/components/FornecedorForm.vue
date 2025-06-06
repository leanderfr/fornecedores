

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
            <div class='h-[45px] pt-1'>Rua, Av.:</div>          
            <div class='h-[45px] pt-1'>Número:</div>          
            <div class='h-[45px] pt-1'>Bairro:</div>          
            <div class='h-[45px] pt-1'>CEP:</div>          
            <div class='h-[45px] pt-1'>Cidade:</div>          
            <div class='h-[45px] pt-1'>UF:</div>          
            <div class='h-[45px] pt-1'>País:</div>          
          </div>

          <div class="flex flex-col  grow max-w-[60%]">  

            <div class='h-[55px] mb-5'>
              <div>
                <input type="text" autocomplete="off" sequence="1"   id="txtCNPJ" maxlength='18' minlength='18' @blur='verificaCNPJ' class='text_formFieldValue w-full'  
                  msgErro='Preencha o CNPJ' >  
              </div>
              <div id='cnpjERROR'></div>
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="2"   id="txtRazao_social" maxlength='150' minlength='5' class='text_formFieldValue' 
                msgErro='Preencha a razão social' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="3"   id="txtLogradouro" maxlength='150' minlength='5' class='text_formFieldValue' 
                msgErro='Preencha o logradouro' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="4"   id="txtNumero" maxlength='20' minlength='2' class='text_formFieldValue' 
                msgErro='Preencha o número' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="5"   id="txtBairro" maxlength='150' minlength='5' class='text_formFieldValue'  
                msgErro='Preencha o bairro' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="6"   id="txtCEP" maxlength='9' minlength='9' class='text_formFieldValue'  
                  msgErro='Preencha o CEP' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="7"   id="txtCidade" maxlength='150' minlength='5' class='text_formFieldValue'  
                msgErro='Preencha a cidade' >    
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="8"   id="txtUF" maxlength='2' minlength='2' class='text_formFieldValue'  
                msgErro='Preencha o Estado' >  
            </div>
            <div class='h-[45px]'>
              <input type="text" autocomplete="off" sequence="9"   id="txtPais" maxlength='150' minlength='5' class='text_formFieldValue'  
                msgErro='Preencha o país' >  
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
import { makeWindowDraggable, slidingMessage, cnpjOK, getNumbersFromString  } from '../assets/js/utils.js'
const emit = defineEmits( ['showLoading', 'hideLoading', 'closeForm','refreshDatatable', 'setCurrentId'] );

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

          let cep = registro.cep.padEnd(9, " ");
        
          $('#txtRazao_social').val( registro.razao_social )
          $('#txtCNPJ').val( registro.cnpj )
          $('#txtLogradouro').val( registro.logradouro )
          $('#txtNumero').val( registro.numero )  
          $('#txtBairro').val( registro.bairro )  
          $('#txtCEP').val( cep.substring(0, 6) + '-' + cep.substring(5, 9 )  )
          $('#txtCidade').val( registro.cidade )  
          $('#txtUF').val( registro.uf )  
          $('#txtPais').val( registro.pais )  

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
coloca foco no 1o campo e prepara mascaras
************************************************************************************************************************************************************/
const preparaFormFornecedor = () => { 

  setTimeout(() => {
    // se esta inserindo reg, cai no campo cnpj para auxiliar usuario a procurar dados da empresa via API
    if ( props.formHttpMethodApply == 'POST')   
      $('#txtCNPJ').focus()    

    // se esta editando reg, cai no razao social pois CNPJ nao precisa ser pesquisado novamente
    else 
      $('#txtRazao_social').focus()    

  }, 100);

  // faz o form ser arrastavel
  makeWindowDraggable('divWINDOW_TOP', 'fornecedorForm')

  $('#txtCNPJ').mask('00.000.000/0000-00', {reverse: true});
  $('#txtCEP').mask('00000-000', {reverse: true});
}



/********************************************************************************************************************************************************
 valida dados no front end primeiramente
********************************************************************************************************************************************************/
async function saveFornecedor()  {

  let cmpFocar = ''
  let erroExibir = ''

  var formData = new FormData();  // body do request

  // percorre campos para critica dos dados digitados
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

/************************************************************************************************************************************************************
auxilia no preenchimento dos campos baseado no CNPJ digitado
************************************************************************************************************************************************************/
async function cnpjAuxiliarPreenchimento()  { 

  emit('showLoading')

  let cnpj = getNumbersFromString(  $.trim($('#txtCNPJ').val())  )

  try {
      let _route_ = `https://open.cnpja.com/office/${cnpj}`

      await fetch(_route_, {method: 'GET'})

      .then( (response) => {
        if (!response.ok) {
          return response.text().then(text => {throw new Error(`HTTP error! ${response.status}` + text)})
        }
        return response.json();
      })

      .then( (registro) => {
        emit('hideLoading')
        $('#txtRazao_social').val( registro.company.name )
        $('#txtLogradouro').val( registro.address.street )
        $('#txtLogradouro').val( registro.address.street )
        $('#txtNumero').val( registro.address.number )  
        $('#txtBairro').val( registro.address.district )  
        $('#txtCidade').val( registro.address.city )  
        $('#txtCEP').val( registro.address.zip.substring(0, 5) + '-' + registro.address.zip.substring(5, 8 )  )
        $('#txtUF').val( registro.address.state )  
        $('#txtPais').val( registro.address.country.name )  
      })

  } 
  catch(err) {
    emit('hideLoading')
    slidingMessage(`Erro API CNPJ= &nbsp;&nbsp;&nbsp;<strong>${err.message}</strong>`, 3000);  
  }

}


/********************************************************************************************************************************************************
 valida dados no front end primeiramente 
********************************************************************************************************************************************************/
const verificaCNPJ = () => {

$('#cnpjERROR').html('')

let cnpj = getNumbersFromString(  $.trim($('#txtCNPJ').val())  )

if (cnpj=='') return

if (! cnpjOK(cnpj) ) {
  $('#cnpjERROR').html('CNPJ inválido')
  return
}

cnpjAuxiliarPreenchimento()

}

//01122833000279

</script>

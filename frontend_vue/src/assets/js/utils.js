
import 'spin.js/spin.css';
import {Spinner} from 'spin.js';

/************************************************************************************************************************************************************
prepara animacao 'carregando...'
************************************************************************************************************************************************************/

const prepareLoadingAnimation = () => {
    var opts = {
      lines: 12 // linhas desenhar
    , length: 30 // largura cada linha
    , width: 18 // espessura linha
    , radius: 42 // raius do circulo
    , scale: 0.3 // tamanho do spinner
    , corners: 3 // contorno do canto
    , color: 'gray' 
    , opacity: 0.3 
    , rotate: 0 
    , direction: 1 // 1: sentido horario, -1: ñ horario
    , speed: 1 
    , trail: 60 // birlho
    , fps: 20 // Frames por segundo
    , zIndex: 2e9 // acima de qq div
    , className: 'spinner' 
    , top: '50%' // posicao em relacao ao parent
    , left: '50%' // posicao em relacao ao parent
    , shadow: true // sombra?
    , hwaccel: true // aceleracao de hardwar5e
    , position: 'absolute' 
    ,animation: 'spinner-line-fade-quick'
    }

    // para exibir/ocultar esta div, usar as funcoes: showLoadingGif()/hideLoadingGif()
    var divLoading = document.getElementById('divLoading');
    new Spinner(opts).spin(divLoading);
}

/************************************************************************************************************************************************************
string esta formato json?
************************************************************************************************************************************************************/
const  isStringJSON = (string) => {
    try {
        JSON.parse(string);
    } catch (e) {
        return false;
    }
    return true;

}



/************************************************************************************************************************************************************
mensagem rolante (horitontal) de alerta/erro/sucesso
************************************************************************************************************************************************************/
const slidingMessage = (html, time) => {

  let slidingDIV = $('#messagesSlidingDiv')

  slidingDIV.html('&nbsp;&nbsp;&nbsp;&nbsp;' + html);
  slidingDIV.show("slide", { direction: "left" }, 200);

  // navegador nao vai tocar beep a nao ser que usuario tenha clicado em algo, questao de segurança
  if (navigator.userActivation.hasBeenActive)     $('#alertBeep')[0].play()

  setTimeout(function () { slidingDIV.hide("slide", { direction: "right" }, 200); }, time);
}

/************************************************************************************************************************************************************
contador, util para loops 'v-for' do Vue
************************************************************************************************************************************************************/
const counter = (start, end) => {
    const result = [];
    for (let i = start; i <= end; i++) {
      result.push(i);
    }
    return result;
}

//********************************************************************************************************************************
// prepara mouseenter do icone cachorrinho (canto inferior direito)
//*******************************************************************************************************************************

const preparePuppyIcon = () => {
  $('#divDoggy').mouseover(function (e) {
    $('#divDoggy_1').show(); $('#divDoggy_2').show(); $('#divDoggy_3').show();
    $('#divDoggy_1').animate({ bottom: '55px', right: '105px', zIndex: 3000 }, 200, function () {
    });

    $('#divDoggy_2').animate({ bottom: '75px', right: '125px', zIndex: 3000 }, 200);
    $('#divDoggy_3').animate({ bottom: '90px', right: '105px' }, 200, function () {
      $(this).css('z-index', 2101);
    });
  });

  // usuario tirou  mouse do icone cachorro 
  $('#divDoggy').mouseout(function (e) {
    $('#divDoggy_1').hide(); $('#divDoggy_2').hide(); $('#divDoggy_3').hide();
    $('#divDoggy_1').css('right', '80px'); $('#divDoggy_1').css('bottom', '1px');
    $('#divDoggy_2').css('right', '80px'); $('#divDoggy_2').css('bottom', '1px');
    $('#divDoggy_3').css('right', '80px'); $('#divDoggy_3').css('bottom', '-150px');
  });
}


/***********************************************************************************************************************
torna uma div arrastavel pelo mouse
***********************************************************************************************************************/
const makeWindowDraggable = (title_id, window_id) => {
  $(`#${window_id}`).draggable({ handle: `#${title_id}`, containment: '#backDrop' });
}

/***********************************************************************************************************************
a tooltip jquery nao some quando clica se sobre o elemento, necessario fazer a tooltip sumir via codigo
***********************************************************************************************************************/
const forceHideToolTip = () => {
  $('[role="tooltip"]').remove();

  // confirma que as tooltips estao ok
  setTimeout(() => {
    improveTooltipLook()  
  }, 100);


}

/*******************************************************************************/
// verifica se um elemento ainda esta visivel na viewport
// util quando reexibindo uma div 'row' de uma datatable que foi recem alterada
/*******************************************************************************/
const divStillVisible = (divId) => {
    // distancia topo da pagina
    var windowScrollTopView = $(window).scrollTop();
    
    // topo pagina + altura video
    var windowBottomView = windowScrollTopView + $(window).height();
 
    let $div = $(divId)
    
    // distancia elemento do topo
    var elemTop = $div.offset().top;
            
    let elemBottom = elemTop + $div.height() + 80; 
      return ( elemBottom <= windowBottomView) && (elemTop >= windowScrollTopView ) ;
}



/***********************************************************************************************************************************************************/
// rola uma div ate reexibir determinado elemento dentro dela
// util quando reexibindo uma div 'row' de uma datatable que foi recem alterada
/***********************************************************************************************************************************************************/
export const scrollUntilElementVisible = (element_id) => {

  if (typeof $("#" + element_id).attr("id") == "undefined") return; // verifica existe

  let container_div = $("#" + element_id).scrollParent(); // acha o parent

  // elemento obrigatoriamente tem que ter ID
  let element = document.getElementById(element_id);

  element.scrollIntoView();

  let posY = container_div.scrollTop();
  container_div.scrollTop(posY ); // rola um pouco pra cima porque scrollIntoView exagera quando desce
}



//************************************************************************************************************
// carrega arquivos JS um apos o outro, pra evitar chamar uma funcao nao carregada ainda
//************************************************************************************************************
  function loadScripts(scripts) {
      var promises = [];
      scripts.forEach(function(script) {
          promises.push($.getScript(script));
      });
      return $.when.apply($, promises);
  }

//******************************************************************************
// melhora o visual da tooltip (title) 
// elementos que tem a classe indicativa (sem css valido) 'putPrettierTooltip'
//******************************************************************************
function improveTooltipLook() {

  setTimeout( () => {
    if (typeof $('.putPrettierTooltip').tooltip !== "undefined") {
      $('.putPrettierTooltip').tooltip({ 
        tooltipClass: 'prettierTitle_black',  
        show: false,  
        hide: false,  
        position: { my: "left top", at: "left top-40", collision: "flipfit" }
      })
    }

  }, 500)    
}

/************************************************************************************************************************************************************
rola o menu (top) que é horizontal, mesmo usuario acionando o mouse wheel vertical
************************************************************************************************************************************************************/
const toWheelMenu = e => {

  if (e.type == "wheel") {
    var getDelta = e.deltaY;
    let divId = 'carsBrowserContainer'

    if (getDelta>0) 
      $(`#${divId}`).scrollLeft( $(`#${divId}`).scrollLeft() + 100 )
    else 
      $(`#${divId}`).scrollLeft( $(`#${divId}`).scrollLeft() - 100 )
  };
}

/************************************************************************************************************************************************************
************************************************************************************************************************************************************/

function cnpjOK(cnpj) {
    
    var b = [ 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2 ]
    var c = String(cnpj).replace(/[^\d]/g, '')
    
    if(c.length !== 14)
        return false

    if(/0{14}/.test(c))
        return false

    for (var i = 0, n = 0; i < 12; n += c[i] * b[++i]);
    if(c[12] != (((n %= 11) < 2) ? 0 : 11 - n))
        return false

    for (var i = 0, n = 0; i <= 12; n += c[i] * b[i++]);
    if(c[13] != (((n %= 11) < 2) ? 0 : 11 - n))
        return false

    return true

}

//********************************************************************************************************************************
//*******************************************************************************************************************************
const getNumbersFromString = (str) => {
  const numbers = str.match(/\d+/g);
  return numbers ? numbers.join('') : '';
}

//********************************************************************************************************************************
//*******************************************************************************************************************************
export { prepareLoadingAnimation, isStringJSON, slidingMessage, counter, makeWindowDraggable, divStillVisible, 
      preparePuppyIcon, forceHideToolTip, loadScripts, toWheelMenu, improveTooltipLook, cnpjOK, getNumbersFromString };


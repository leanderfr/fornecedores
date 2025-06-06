<?php
declare(strict_types=1);

header("Content-Type: text/html; charset=utf-8"); 

// headers que permite frontend em determinada URL chamar backend em outra URL (CORS)
//  CORS = Cross-Origin Resource Sharing
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');       

$method = $_SERVER['REQUEST_METHOD'];

// OPTIONS= browser mandou so um sinal para teste
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
} 
header("HTTP/1.0 200 OK"); 


require 'setup.php';
require 'functions.php';
require "Router.php";
require "handlers/Fornecedores.php";  
require "handlers/Usuarios.php";  


//********************************************************************
// analise rota recebida e decide o que fazer
//********************************************************************
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// prepara handlers (controllers)
$handlerFornecedores = new Fornecedores;
$handlerUsuarios = new Usuarios;

$router = new Router;

$getRequest = $_SERVER['REQUEST_METHOD']==='GET';
$postRequest = $_SERVER['REQUEST_METHOD']==='POST';
$patchRequest = $_SERVER['REQUEST_METHOD']==='PATCH';
$deleteRequest = $_SERVER['REQUEST_METHOD']==='DELETE';

//*********************************************************************************************************************************************************
// lê registros (qdo plural) ou registro (singular)   (GET)
if ($getRequest) {

  $router->Get("/fornecedores/{status}/{searchbox}", function($status, $searchbox) use($handlerFornecedores) {  
    $handlerFornecedores->getFornecedores($status, $searchbox);
  });

  // nenhum  searchbox recebido
  $router->Get("/fornecedores/{status}", function($status) use($handlerFornecedores) {  
    $handlerFornecedores->getFornecedores($status, '');
  });

  $router->Get("/fornecedor/{id}", function($id) use($handlerFornecedores)  {  
    $handlerFornecedores->getFornecedorById($id);
  });


  $router->Get("/usuarios/{status}/{searchbox}", function($status, $searchbox) use($handlerUsuarios) {  
    $handlerUsuarios->getUsuarios($status, $searchbox);
  });

  // nenhum  searchbox recebido
  $router->Get("/usuarios/{status}", function($status) use($handlerUsuarios) {  
    $handlerUsuarios->getUsuarios($status, '');
  });

  $router->Get("/usuario/{id}", function($id) use($handlerUsuarios)  {  
    $handlerUsuarios->getUsuarioById($id);
  });


 

} 

//*********************************************************************************************************************************************************
// update registro (PATCH)
if ($patchRequest) {
    $router->Patch("/fornecedor/{id}", function($id) use($handlerFornecedores)  {  
      $handlerFornecedores->postOuPatchFornecedor($id);
    });

    $router->Patch("/fornecedor/status/{id}", function($id) use($handlerFornecedores)  {  
      $handlerFornecedores->ChangeStatus($id);
    });

    $router->Patch("/usuario/{id}", function($id) use($handlerUsuarios)  {  
      $handlerUsuarios->postOuPatchUsuario($id);
    });

    $router->Patch("/usuario/status/{id}", function($id) use($handlerUsuarios)  {  
      $handlerUsuarios->ChangeStatus($id);
    });




}


//*********************************************************************************************************************************************************
// adiciona registro (POST)
if ($postRequest)  {

    $router->Post("/fornecedor", function() use($handlerFornecedores)  {  
      $handlerFornecedores->postOuPatchFornecedor();
    });

    $router->Post("/usuario", function() use($handlerUsuarios)  {  
      $handlerUsuarios->postOuPatchUsuario();
    });


}

//*********************************************************************************************************************************************************
// excluir registro (DELETE)
if ($deleteRequest) {

    $router->Delete("/fornecedor/{id}", function($id) use($handlerFornecedores)  {  
      $handlerFornecedores->deleteFornecedor($id);
    }); 

    $router->Delete("/usuario/{id}", function($id) use($handlerUsuarios)  {  
      $handlerUsuarios->deleteUsuario($id);
    }); 

}



$router->dispatch($path);

//********************************************************************
//********************************************************************


?>
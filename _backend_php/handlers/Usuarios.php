<?php

class Usuarios
{

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getUsuarios(string $status, string $searchbox): void   {

    $sql =  "select id, nome, ifnull(active, false) as active ".
            "from usuarios  ".
            "where deleted_at is null ";

    // prioridade é filtrar baseado na searchbox enviada
    if ($searchbox!='')  {
      $sql .= "and trim(nome) like('%$searchbox%') ";
    } 

    // searchbox vazia, filtra pelo status 
    else {
        if ($status=='active') $sql .= 'and ifnull(active, false)=true';
        else if ($status=='inactive') $sql .= 'and ifnull(active, false)=false';
    }

    $sql .= ' order by nome';

    executeFetchQueryAndReturnJsonResult( $sql, false, false );
  }

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getUsuarioById($id): void   {
    $sql =  "select nome, ifnull(active, false) as active ".
            "from usuarios  ".
            "where id=$id ";

    executeFetchQueryAndReturnJsonResult( $sql, true);
  }





  //***************************************************************************************************************************************
  //***************************************************************************************************************************************
  public function changeStatus($id): void   {
    global $dbConnection;

    if (! is_numeric($id)) {
      internalError( 'Not numeric' );
    }

    $crudSql = "update usuarios set active = if(active, false, true) where id = $id ";
    $dbConnection -> autocommit(true);    // grava sem necessidade de commit manual

    $result = executeCrudQueryAndReturnResult($crudSql, true);    

    http_response_code(200);   // 200= it was ok
    die( '__successo__' );
  }



  //***************************************************************************************************************************************
  //***************************************************************************************************************************************
  public function postOuPatchUsuario($usuario_id=''): void   {
    global $dbConnection;

    // se ID veio na rota (usuario_id) é PATCH
    // se nao veio é POST
  	if ($usuario_id!='' && ! is_numeric($usuario_id))   routeError();

    // verify request
    $fields = [ ['string', 'nome', 5, 150] 
              ];

    // se é POST, obtem dados usando o conhecido $_POST 
    if ($usuario_id=='')    {
      $_FIELDS = $_POST;
    }

    // se nao é POST, é PATCH, e nesse caso, obrigatoriamente o PHP 8.4 tem que estar instalado
    // é o unico que lê o body do request com a funcao 'request_parse_body()'
    // versoes anteriores nao leem!   ja perdi muito tempo no passado tentando fazer ler com PHP < 8.4
    else {
      [$_FIELDS] = request_parse_body();   // desmenbra FIELDS
    }


    // faz analise abaixo caso alguem esteja usando POSTMAN ou INSONMIA ou ouutro soft para inserir dados de forma 'clandestina'
    // o frontend ja fez a verificacao previa, mas eu sempre implanto verificacao em ambos os lados
    $dataError = '';
    for ($i=0; $i < count($fields); $i++)  {

      $fieldType = $fields[$i][0];
      $fieldName = $fields[$i][1];
      $minSize = $fields[$i][2];
      $maxSize = $fields[$i][3];

      $fieldValue = $_FIELDS[$fieldName];

      // é numerico
      if ($fields[$i][0] == 'int') {
        if (! is_numeric($fieldValue)) {
          $dataError = 'Não numérico';
          break;
        }
      }

      // larguras min / max 
      if ($fieldType=='string') {
          if ( strlen($fieldValue) < $minSize || strlen($fieldValue) > $maxSize )  {
            $dataError = ' - <strong>' .strtoupper($fieldName) . ' - Tamanho string errado </strong>';
            break;
          }
      }
    }

    if ($dataError!='') internalError( $dataError );

    $nome =   addslashes($_FIELDS['nome']);

    // se ID nao informado , é POST
    if ($usuario_id=='')    {
      $crudSql = "insert into usuarios(nome, created_at, updated_at, active) ". 
                "select '$nome', now(), now(), true "; 
      $dbOperation = 'insert';
    }

    // se ID informado, é PATCH
    else { 
      $crudSql = "update usuarios set nome='$nome', ".
                 " updated_at=now() ". 
                "where id = $usuario_id ";
      $dbOperation = 'update';
    } 
    $dbConnection -> autocommit(true);    // registra sem precisar commit manual

    // executa query e retorna ID (2o parametro)
    $result = executeCrudQueryAndReturnResult($crudSql, true);    

    // se foi um POST, obtem o ID recem criado (__successo__|record id)
    if ($usuario_id=='') {
      $usuario_id = explode("|", $result)[1];
    }

    http_response_code(200);   // 200= tudo ok
    if ($dbOperation == 'update')   die( '__successo__' );
    else die( $result );    // __successo__|id registro
  }



  //***************************************************************************************************************************************
  //***************************************************************************************************************************************
  public function deleteUsuario($id=''): void   {
    global $dbConnection;

    if (! is_numeric($id))   routeError();

    $crudSql = "update usuarios set deleted_at=now() where id = $id ";
    $dbConnection -> autocommit(true);    


    $result = executeCrudQueryAndReturnResult($crudSql);    

    http_response_code(200);   // 200= it was ok
    die( '__successo__' );
  }


}


?>

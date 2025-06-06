<?php

class Fornecedores
{

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getFornecedores(string $status, string $searchbox): void   {

    $sql =  "select id, razao_social, nome_fantasia, cnpj, ifnull(active, false) as active ".
            "from fornecedores  ".
            "where deleted_at is null ";

    // prioridade é filtrar baseado na searchbox enviada
    if ($searchbox!='')  {
      $sql .= "and trim(nome) like('%$searchbox%') or trim(razao_social) like('%$searchbox%') ";
    } 

    // searchbox vazia, filtra pelo status 
    else {
        if ($status=='active') $sql .= 'and ifnull(active, false)=true';
        else if ($status=='inactive') $sql .= 'and ifnull(active, false)=false';
    }

    //$sql .= ' order by razao_social';

    executeFetchQueryAndReturnJsonResult( $sql, false, false );
  }

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getFornecedorById($id): void   {
    $sql =  "select nome_fantasia, cpnj, razao_social  ".
            "from fornecedores  ".
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

    $crudSql = "update fornecedores set active = if(active, false, true) where id = $id ";
    $dbConnection -> autocommit(true);    // grava sem necessidade de commit manual

    $result = executeCrudQueryAndReturnResult($crudSql, true);    

    http_response_code(200);   // 200= it was ok
    die( '__successo__' );
  }



  //***************************************************************************************************************************************
  //***************************************************************************************************************************************
  public function postOuPatchFornecedor($fornecedor_id=''): void   {
    global $dbConnection;

    // se ID veio na rota (fornecedor_id) é PATCH
    // se nao veio é POST
  	if ($fornecedor_id!='' && ! is_numeric($fornecedor_id))   routeError();

    // verify request
    $fields = [ ['string', 'razao_social', 5, 30]  ,
                ['string', 'cnpj', 3, 200],
                ['string', 'nome_fantasia', 3, 200] 
              ];

    // se é POST, obtem dados usando o conhecido $_POST 
    if ($fornecedor_id=='')    {
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
            $dataError = $fieldName . ' - Tamanho string errado';
            break;
          }
      }
    }

    if ($dataError!='') internalError( $dataError );

    $razao_social =   addslashes($_FIELDS['razao_social']);
    $cnpj =   addslashes($_FIELDS['cnpj']);
    $nome_fantasia =   addslashes($_FIELDS['nome_fantasia']);

    // se ID nao informado , é POST
    if ($fornecedor_id=='')    {
      $crudSql = "insert into fornecedores(razao_social, cpnj, nome_fantasia, created_at, updated_at, active) ". 
                "select '$razao_social', '$cnpj', '$nome_fantasia', now(), now(), true "; 
      $dbOperation = 'insert';
    }

    // se ID informado, é PATCH
    else { 
      $crudSql = "update fornecedores set razao_social='$razao_social', cpnj='$cnpj', nome_fantasia='$nome_fantasia', updated_at=now() ". 
                "where id = $fornecedor_id ";
      $dbOperation = 'update';
    } 
    $dbConnection -> autocommit(true);    // registra sem precisar commit manual

    // executa query e retorna ID (2o parametro)
    $result = executeCrudQueryAndReturnResult($crudSql, true);    

    // se foi um POST, obtem o ID recem criado (__successo__|record id)
    if ($fornecedor_id=='') {
      $fornecedor_id = explode("|", $result)[1];
    }

    http_response_code(200);   // 200= tudo ok
    if ($dbOperation == 'update')   die( '__successo__' );
    else die( $result );    // __successo__|id registro

  }


}
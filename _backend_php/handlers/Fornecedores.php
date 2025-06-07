<?php

class Fornecedores
{

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getFornecedores(string $status, string $searchbox): void   {

    $sql =  "select id, razao_social, cnpj, logradouro, numero, bairro, cep, cidade, uf, pais, ifnull(active, false) as active ".
            "from fornecedores  ".
            "where deleted_at is null ";

    // prioridade é filtrar baseado na searchbox enviada
    if ($searchbox!='')  {
      $sql .= "and trim(razao_social) like('%$searchbox%')  ";
    } 

    // searchbox vazia, filtra pelo status 
    else {
        if ($status=='active') $sql .= 'and ifnull(active, false)=true';
        else if ($status=='inactive') $sql .= 'and ifnull(active, false)=false';
    }

    $sql .= ' order by razao_social';

    executeFetchQueryAndReturnJsonResult( $sql, false, false );
  }

  //***************************************************************************************************************************************
  //***************************************************************************************************************************************

  public function getFornecedorById($id): void   {
    $sql =  "select razao_social, cnpj, logradouro, numero, bairro, cep, cidade, uf, pais, ifnull(active, false) as active ".
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
    $fields = [ ['string', 'razao_social', 5, 150]  ,
                ['string', 'cnpj', 18, 18],
                ['string', 'logradouro', 5, 150],
                ['string', 'numero', 2, 20],
                ['string', 'bairro', 5, 150],
                ['string', 'cep', 9, 9],
                ['string', 'cidade', 5, 150],
                ['string', 'uf', 2, 2],
                ['string', 'cep', 5, 150],
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

    // verifica se ha fornecedor cadastrado com CNPJ enviado
    $cnpjTeste = $_FIELDS['cnpj'];
    $sql =  "select razao_social  ".
            "from fornecedores  ".
            "where trim(cnpj) = trim('$cnpjTeste') and deleted_at is null ";

    if ($fornecedor_id!='')    {    
      $sql .= " and id <> $fornecedor_id";
    }

    $result = mysqli_query($dbConnection, $sql) or internalError('[1] Database error / Erro na base de dados');    
    if ( mysqli_num_rows($result) > 0 ) {
      http_response_code(500);   
      die('Fornecedor já cadastrado');
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

    $razao_social =   addslashes($_FIELDS['razao_social']);
    $cnpj =   addslashes($_FIELDS['cnpj']);
    $logradouro =   addslashes($_FIELDS['logradouro']);
    $numero =   addslashes($_FIELDS['numero']);
    $cep =   addslashes($_FIELDS['cep']);
    $bairro =   addslashes($_FIELDS['bairro']);
    $cidade =   addslashes($_FIELDS['cidade']);
    $uf =   addslashes($_FIELDS['uf']);
    $pais =   addslashes($_FIELDS['pais']);


    // se ID nao informado , é POST
    if ($fornecedor_id=='')    {
      $crudSql = "insert into fornecedores(razao_social, cnpj, logradouro, numero, bairro, cep, cidade, uf, pais, created_at, updated_at, active) ". 
                "select '$razao_social', '$cnpj', '$logradouro', '$numero', '$bairro', '$cep', '$cidade', '$uf', '$pais', now(), now(), true "; 
      $dbOperation = 'insert';
    }

    // se ID informado, é PATCH
    else { 
      $crudSql = "update fornecedores set razao_social='$razao_social', cnpj='$cnpj', logradouro='$logradouro', ".
                 "       numero='$numero', cep='$cep', bairro='$bairro', cidade='$cidade', uf='$uf', pais='$pais', ".
                 " updated_at=now() ". 
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



  //***************************************************************************************************************************************
  //***************************************************************************************************************************************
  public function deleteFornecedor($id=''): void   {
    global $dbConnection;

    if (! is_numeric($id))   routeError();

    $crudSql = "update fornecedores set deleted_at=now() where id = $id ";
    $dbConnection -> autocommit(true);    

    $result = executeCrudQueryAndReturnResult($crudSql);    

    http_response_code(200);   // 200= it was ok
    die( '__successo__' );
  }


}


?>

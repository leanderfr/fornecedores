
<?php

// database 
$server = 'autogestor.ckv6s00ampaj.us-east-1.rds.amazonaws.com';
$login = 'admin';
$password = "Sucesso123#";  
$database = 'revendamais';

$LOCAL_TMP_FOLDER= '/tmp';

// mostra erros / warnings qdo local
if ( strpos($_SERVER['HTTP_HOST'], '__localhost')!==false || strpos($_SERVER['HTTP_HOST'], '__127.0.0.1')!==false ) {

  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);

}
// nao mostra em producao
else {    
  error_reporting(0); 
  ini_set('display_errors', 1);
}

  
// compartilha conexao por toda aplicacao backend
$dbConnection = mysqli_connect($server, $login, $password);
if (mysqli_connect_errno())    {
    echo "Error connection database: " . mysqli_connect_error();
}

mysqli_select_db($dbConnection, $database) or die(mysqli_error($dbConnection));

mysqli_query($dbConnection, "SET NAMES 'utf8'");
mysqli_query($dbConnection,'SET character_set_connection=utf8');
mysqli_query($dbConnection,'SET character_set_client=utf8');
mysqli_query($dbConnection,'SET character_set_results=utf8');


// rodar laravel
// sudo apt-get install php-xml

?>
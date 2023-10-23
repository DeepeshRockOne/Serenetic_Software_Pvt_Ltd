<?php 

if(isset($layout)){
  $layout = $layout;
  
}
else{
  $layout = 'main.layout.php';	
}
include_once $layout;
$pdo->closeConnection();
$pdo=null;
?>
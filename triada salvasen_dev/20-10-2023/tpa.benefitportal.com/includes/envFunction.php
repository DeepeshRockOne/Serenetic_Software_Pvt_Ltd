<?php

function setEnvVarriables(){
  include dirname(__DIR__).'/libs/phpdotenv-master/vendor/autoload.php';

  try {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    //$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
  } catch (Exception $e) {
    throw new Exception('can not load the config file! ' . $e->getMessage() . "\n");
  }
}

?>
<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/function.class.php';
$function_list = new functionsList();
/*
Script to review current default Credit Card expiration dates, and notify members 2 months and 1 month prior to expiration month. Run script on the 5th of every month. 
*/

//Before 1 Month
$expMonth1 = date("m",strtotime("+1 Month"));
$expYear1 = date("y",strtotime("+1 Month"));
$function_list->getExpireCreditCardProfiles($expMonth1,$expYear1);

//Before 2 Month
$expMonth2 = date("m",strtotime("+2 Month"));
$expYear2 = date("y",strtotime("+2 Month"));
$function_list->getExpireCreditCardProfiles($expMonth2,$expYear2);


echo 'Completed';
?>
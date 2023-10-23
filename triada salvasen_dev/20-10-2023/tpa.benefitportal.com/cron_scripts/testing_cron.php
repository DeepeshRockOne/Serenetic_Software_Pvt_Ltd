<?php 
include_once dirname(__DIR__) .'/includes/connect.php';
$trigger_param = array("Commissions script called....");
trigger_mail(1, $trigger_param, "karan@cyberxllc.com", '', 3);
?>
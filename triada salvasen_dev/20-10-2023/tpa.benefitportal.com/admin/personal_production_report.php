<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$exStylesheets = array('thirdparty/select2/css/select2.css');
$exJs = array('thirdparty/select2/js/select2.full.min.js');

$agent_id = getname('customer',$_GET['agent_id'],'id','md5(id)');

$agent_name = $pdo->selectOne("SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,cs.agent_coded_id from customer c LEFT JOIN customer_settings cs on(cs.customer_id=c.id) where c.is_deleted='N' and c.id=:id",array(":id"=>$agent_id));
$template = 'personal_production_report.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

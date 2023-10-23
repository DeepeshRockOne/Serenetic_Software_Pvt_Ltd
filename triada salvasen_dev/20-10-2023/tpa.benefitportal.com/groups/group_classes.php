<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboardphp';
$breadcrumbes[1]['title'] = 'Classes';

group_has_access(3);

$group_id = $_SESSION['groups']['id'];
$sqlClass="SELECT gc.class_name,gc.created_at,md5(gc.id) as id,count(cs.id) as total_assigned 
			FROM group_classes gc 
			LEFT JOIN customer_settings cs ON (gc.id=cs.class_id)
			WHERE gc.is_deleted='N' AND gc.group_id = :group_id group by gc.id";
$resClass=$pdo->select($sqlClass,array(":group_id"=>$group_id));



$template = 'group_classes.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

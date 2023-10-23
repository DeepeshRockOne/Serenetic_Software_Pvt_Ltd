<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Classes';
$breadcrumbes[1]['link'] = 'group_classes.php';
$breadcrumbes[2]['title'] = '+ Class';

group_has_access(3);

$group_id = $_SESSION['groups']['id'];
$group_classes_id = !empty($_GET['class']) ? $_GET['class'] : 0;
$respaydate = 0 ;
$resData = array();
$resdate = array();
if(!empty($group_classes_id)){
	$sqlData = "SELECT * FROM group_classes where md5(id)=:id";
	$resData = $pdo->selectOne($sqlData,array(":id"=>$group_classes_id));

	if(!empty($resData)){
		$class_name = $resData['class_name'];
		$existing_member_eligible_coverage = $resData['existing_member_eligible_coverage'];
		$new_member_eligible_coverage = $resData['new_member_eligible_coverage'];
		$renewed_member_eligible_coverage = $resData['renewed_member_eligible_coverage'];
		$pay_period = $resData['pay_period'];

		$sqlPayData = "SELECT paydate FROM group_classes_paydates WHERE class_id =:id AND is_deleted ='N'";
	    $resPayData = $pdo->select($sqlPayData,array(":id"=>$resData['id']));
// Creating new date format from that timestamp
	foreach($resPayData as $value){
		$new_date = date("m-d-Y",  strtotime($value['paydate']));
		$paydate = str_replace('-', '/', $new_date);
		$resdate[] =  $paydate;
	}
	$respaydate = implode(',', $resdate);
	$description['ac_message'] = array(
	        'ac_red_1' => array(
	            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	        	'title'=>$_SESSION['groups']['rep_id'],
	        ),
	        'ac_message_1' => ' read Class ',
	        'ac_red_2' => array(
	            'title' => $class_name,
	        ),
	    );
	    $desc = json_encode($description);
	   
	    activity_feed(3, $_SESSION['groups']['id'], 'Group', $resData['id'], 'group_classes', 'Group Read Class', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
	}
}


$sqlClass="SELECT gc.class_name,gc.created_at,md5(gc.id) as id
			FROM group_classes gc 
			LEFT JOIN customer_settings cs ON (gc.id=cs.group_classes_id)
			WHERE gc.is_deleted='N' AND gc.group_id=:group_id AND cs.id is null limit 0,10";
$resClass=$pdo->select($sqlClass,array(":group_id"=>$group_id));

$weekName = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$exStylesheets = array(
'thirdparty/multiple-select-master/multiple-select.css' . $cache,
'thirdparty/bootstrap-datepicker-master/css/datepicker.css' , 
);

$exJs = array(
'thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache,
'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js' , 
'thirdparty/Birthdate/moment.min.js',
);

$template = 'group_add_class.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

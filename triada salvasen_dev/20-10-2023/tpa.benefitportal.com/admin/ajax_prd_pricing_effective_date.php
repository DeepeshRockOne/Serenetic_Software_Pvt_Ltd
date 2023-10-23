<?php
include_once 'layout/start.inc.php';
$data = array();
$date = $_POST['date'];
$todayDate= date('m/d/Y');
$data['date']='';
if(!empty($date)){
	$termDate=date('m/d/Y',strtotime($date));
	$next_date = date('m/d/Y',strtotime($termDate.'+1 day'));
	$data['date']=$next_date;

	if(strtotime($termDate) >= strtotime($todayDate)){
		$data['data_type']='Future Date';
	}else{
		$data['data_type']='Past Date';
	}
}
echo json_encode($data);
exit;
?>
<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = !empty($_POST['list_bill']) ? $_POST['list_bill'] : '';
$notes = !empty($_POST['notes']) ? $_POST['notes'] : '';


$sqlListBill = "SELECT lb.id,lb.notes FROM list_bills lb WHERE md5(id)=:id";
$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$id));

$updateParams=array(
	'notes'=> $notes,
);
$updateWhere=array(
	'clause'=>'md5(id)=:id',
	'params'=>array(
		":id"=>$id,
	)
);
$pdo->update("list_bills",$updateParams,$updateWhere);


$ac_description["ac_description_link"] = array(
    'From '=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'Note','data-desc'=>checkIsset($resListBill['notes']),'data-encode'=>'no'),
    'To '=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'Note','data-desc'=>$notes,'data-encode'=>'no'),
);
activity_feed(3, $_SESSION['groups']['id'],'Group',$_SESSION['groups']['id'] , 'Group', 'List Bill Note Updated', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], json_encode($ac_description));


$result = array();
$result['msg']='Note added to list bill';

header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>

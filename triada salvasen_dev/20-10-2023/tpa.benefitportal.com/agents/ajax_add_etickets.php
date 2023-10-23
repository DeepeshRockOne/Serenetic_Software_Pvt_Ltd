<?php
include_once 'layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';

$validate = new Validation();
$functionList =new functionsList();
$response = array();

$userType = 'Agent';
$userId = checkIsset($_POST['userId']);
$categoryId = checkIsset($_POST['category']);
$subject = checkIsset($_POST['subject']);
// $assigne_admins = checkIsset($_POST['assigne_admins']);
$description = checkIsset($_POST['description']);

$REAL_IP_ADDRESS = get_real_ipaddress();
$docFile = isset($_FILES['docFile']) ? $_FILES['docFile']  : '' ;
if(empty($userType)){
    $validate->setError("userType","Please select any option.");
}
if(empty($userId)){
    $validate->setError("userId","Please select any option.");
}
if(empty($categoryId)){
    $validate->setError("category","Please select any option.");
}
// if(empty($assigne_admins)){
//     $validate->setError("assigne_admins","Please select any option.");
// }
if(!empty($docFile) && $docFile['size'] > 10485760){
    $validate->setError("docFile","Please select file less then 10MB.");
}

$validate->string(array('required' => true, 'field' => 'subject', 'value' => $subject), array('required' => 'Subject is required'));
$validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));

if($validate->isValid()){

    $file = !empty($docFile) && !empty($docFile['name']) ? $docFile : array();
    $sessionArr['agents'] = $_SESSION['agents'];
    $real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
    $functionList->createNewTicket($sessionArr,$categoryId,$subject,0,$description,$userId,$userType,$real_ip_address,$file);
    setNotifySuccess("eTicket Submitted Successfully",true);
    $response['status'] = 'success';

}else{
    $errors = $validate->getErrors();
	$response['status'] = 'fail';
	$response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>
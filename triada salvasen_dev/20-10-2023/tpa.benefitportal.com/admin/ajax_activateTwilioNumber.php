<?php
include_once 'layout/start.inc.php';

$response = array();

$id = checkIsset($_POST['id']);
$response['status'] = 'fail';
$response['msg'] = 'Number Not Found';
if(!empty($id)){
    $sqlNumber = "SELECT * FROM twilio_numbers where md5(id)=:id";
    $resNumber = $pdo->selectOne($sqlNumber,array(":id"=>$id));

    if(!empty($resNumber)){
        $updParams=array("is_active"=>'N');
        $updWhere = array(
            'clause'=>'is_deleted=:id',
            'params'=>array(":id"=>'N'),
        );
        $pdo->update("twilio_numbers",$updParams,$updWhere);

        $updParams=array("is_active"=>'Y');
        $updWhere = array(
            'clause'=>'id=:id',
            'params'=>array(":id"=>$resNumber['id']),
        );
        $pdo->update("twilio_numbers",$updParams,$updWhere);

        $setting_value = str_replace($callingCode, $callingCodeReplace, $resNumber['TwilioNumber']);
        $updParams=array("setting_value"=>$setting_value);
        $updWhere = array(
            'clause'=>'setting_key=:id',
            'params'=>array(":id"=>'sms_twilio_number'),
        );
        $pdo->update("app_settings",$updParams,$updWhere);

        $description = array();
        $description['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title' => $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => " Activated ",
            'ac_red_2'=>array(
              'title'=> format_telephone(str_replace($callingCode, $callingCodeReplace, $resNumber['TwilioNumber'])),
            ),
        );
        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'Admin','Text Message Number Activated',($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);

        $update_where = array(
                'clause' => 'id=:id',
                'params' => array(':id' => 1),
            );
        $update_params=array("version"=>"msqlfunc_version+0.01");
        $pdo->update("cache_management",$update_params,$update_where);
        unlink($CACHE_PATH_DIR.$CACHE_FILE_NAME);

        $response['msg'] = 'Number Activated Successfully';
        $response['status'] = 'success';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>
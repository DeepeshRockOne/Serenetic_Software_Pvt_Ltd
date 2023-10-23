<?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();
$adminId=$_SESSION['admin']['id'];
$ListBillGlobalDay = !empty($_POST['global_day_listbill']) ? $_POST['global_day_listbill'] : '';
// $isAutoPaymentSet = !empty($_POST['set_auto_payment']) ? $_POST['set_auto_payment'] : '';
$autoPaymentDays = !empty($_POST['auto_payment_days']) ? $_POST['auto_payment_days'] : '';

if(empty($ListBillGlobalDay)){
  $validate->setError("global_day_listbill","Please Select Day");
}
// if(empty($isAutoPaymentSet)){
//     $validate->setError("set_auto_payment","Please Select Option");
// }
if(empty($autoPaymentDays)){
  $validate->setError("auto_payment_days","Please Select Auto Payment Day");
}

if(!empty($ListBillGlobalDay) && !empty($autoPaymentDays)){
    if($ListBillGlobalDay <= $autoPaymentDays){
        $validate->setError('auto_payment_days',"Auto Payment prior days must be less than List Bill generation prior days");
    }
}

if ($validate->isValid()) {
    $sqlPayOptions = "SELECT id,days_prior_pay_period,auto_payment_days FROM list_bill_options where rule_type='Global' and is_deleted='N'";
    $resPayOptions = $pdo->selectOne($sqlPayOptions);

    if(!empty($resPayOptions) && ($resPayOptions['days_prior_pay_period']!=$ListBillGlobalDay ||$resPayOptions['auto_payment_days']!=$autoPaymentDays)){

        $update_param = array(
            "billing_setting" => 'days_prior_pay_period',
            "days_prior_pay_period"=>$ListBillGlobalDay,
            // "auto_set_payment_received"=>'N',
            "auto_payment_days"=>NUll,
        );
        // $update_param['auto_set_payment_received']= (!empty($isAutoPaymentSet)) ? $isAutoPaymentSet : $update_param['auto_set_payment_received'];
        $update_param['auto_payment_days']= (!empty($autoPaymentDays)) ? $autoPaymentDays : $update_param['auto_payment_days'];
         
        $upd_where = array(
          'clause' => 'id = :id',
          'params' => array(
              ':id' => $resPayOptions['id'],
          ),
          );
       $updated_param = $pdo->update('list_bill_options', $update_param, $upd_where,true);
    
        $description['&nbsp;&nbsp;&nbsp;Global List Bill Day'] = "List Bill Day: From ".$resPayOptions['days_prior_pay_period'].' to '.$update_param['days_prior_pay_period'];

        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'],'Admin','0','Global List Bill Options','Admin Update Global List Bill Options',"","",$desc);
        
        $response['msg']='List Bill Options Global Updated Successfully';
    
    }else{

        $response['msg']='No Change in List Bill Global Options';
    }
    
    $response['status'] = 'success';
}
else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>
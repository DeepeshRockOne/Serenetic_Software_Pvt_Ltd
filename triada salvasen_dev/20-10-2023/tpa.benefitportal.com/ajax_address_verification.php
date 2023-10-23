<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$function_list = new functionsList();
$response = array();
$type = !empty($_POST['type'])?$_POST['type']:"";
$basic_primary_zip = !empty($_POST['basic_primary_zip'])?$_POST['basic_primary_zip']:"";

$primary_address1 = !empty($_POST['primary_address1'])?$_POST['primary_address1']:"";
$primary_address2 = !empty($_POST['primary_address2'])?$_POST['primary_address2']:"#";
$primary_zip = !empty($_POST['primary_zip'])?$_POST['primary_zip']:"";
$primary_city = !empty($_POST['primary_city'])?$_POST['primary_city']:"";
$primary_state = !empty($_POST['primary_state'])?$_POST['primary_state']:"";

$response['type']=$type;
$response['address']='';
$response['status']='fail';
$response['error']='Address Not Found.';

if($type=="Zip"){
  $zipResponse = $function_list->uspsCityVerification($basic_primary_zip);  
  if(!empty($zipResponse)){
      if($zipResponse['status']=='success'){
        $response['city'] = $zipResponse['city'];
        $response['state'] = $zipResponse['state'];
        $response['long_state'] = $getStateNameByShortName[$zipResponse['state']];
        $response['zip'] = $zipResponse['zip'];
        $response['status']='success';
        $response['error'] = "";
      }else{
        $response['city']='';
        $response['state']='';
        $response['long_state']='';
        $response['zip']='';
        $response['status']='fail';
        $response['error']=isset($zipResponse['error_message']) ? $zipResponse['error_message'] : 'Address Not Found.';
      }
  }
}else{
  if(!empty($primary_address1) && !empty($primary_city) && !empty($primary_state) && !empty($primary_zip)){

    $address_response = $function_list->uspsAddressVerification($primary_address1,$primary_address2,$primary_city,$primary_state,$primary_zip);

    if(!empty($address_response)){
      if($address_response['status']=='success'){
        $response['address'] = $address_response['address'];
        $response['address2'] = $address_response['address2'];
        $response['city'] = $address_response['city'];
        $response['state'] = $address_response['state'];
        $response['long_state'] = $getStateNameByShortName[$address_response['state']];
        $response['zip'] = $address_response['zip'];
        $response['enteredAddress']= $primary_address1 .' '.$primary_address2 .'</br>'.$primary_city.', '.$allStateShortName[$primary_state] . ' '.$primary_zip;
        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
        $response['status']='success';
        $response['error'] = "";
      }else{
        $response['address']='';
        $response['address2']='';
        $response['city']='';
        $response['state']='';
        $response['long_state']='';
        $response['zip']='';
        $response['enteredAddress']='';
        $response['suggestedAddress']='';
        $response['status']='fail';
        $response['error']=isset($address_response['error_message']) ? $address_response['error_message'] : 'Address Not Found.';
      }
    }
  }
}



echo json_encode($response);
dbConnectionClose();
exit;
?>
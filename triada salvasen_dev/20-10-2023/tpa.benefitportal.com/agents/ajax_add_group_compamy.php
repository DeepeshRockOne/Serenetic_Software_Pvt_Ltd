<?php
include_once __DIR__ . '/includes/connect.php';

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;

if($is_address_ajaxed){

  $response = array("status"=>'success');
  $address = $_POST['address'];
  $address_2 = checkIsset($_POST['address_2']);
  $city = $_POST['city'];
  $state = checkIsset($_POST['state']);
  $zipcode = $_POST['zip'];
  $old_address = $_POST['old_address'];
  $old_zip = $_POST['old_zip'];

  $validate = new Validation();

  $validate->digit(array('required' => true, 'field' => 'zip', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

  $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
  if($validate->isValid()){
      include_once '../includes/function.class.php';
      $function_list = new functionsList();
      $zipAddress = $function_list->uspsCityVerification($zipcode);

      if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
          
          if($zipAddress['status'] =='success'){
              $response['city'] = $zipAddress['city'];
              $response['state'] = $allStateResByName[$getStateNameByShortName[$zipAddress['state']]]['id'];
              $response['zip_response_status']='success';

              $tmpAdd1=$address;
              $tmpAdd2=!empty($address_2) ? $address_2 : '#';
              $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);
              
              if(!empty($address_response)){
                  if($address_response['status']=='success'){
                      $response['address'] = $address_response['address'];
                      $response['address2'] = $address_response['address2'];
                      $response['city'] = $address_response['city'];
                      $response['state'] = $allStateResByName[$getStateNameByShortName[$address_response['state']]]['id'];
                      $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zipcode;
                      $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                      $response['zip_response_status']='';
                      $response['address_response_status']='success';
                  }
              }
          }else if($zipAddress['status'] =='fail'){
              $response['status'] = 'fail';
              $response['errors'] = array("zip"=>$zipAddress['error_message']);
          }
          
      }
  }else{
      $errors = $validate->getErrors();
      $response['status'] = 'fail';
      $response['errors'] = $errors;
  }

  header('Content-type: application/json');
  echo json_encode($response);
  exit();
}

$validate = new Validation();


$company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : 0;
$group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : 0;

$is_valid_address = $_POST['is_valid_address'];
$name = $_POST['name'];
$address = $_POST['address'];
$address_2 = $_POST['address_2'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];

$ein = phoneReplaceMain($_POST['ein']);
$location = $_POST['location'];
$contact = $_POST['contact'];
$phone = phoneReplaceMain($_POST['phone']);
$email = checkIsset($_POST['email']);
$title = $_POST['title'];
$found_state_id = 0;

if($group_id != '' && $company_id != ''){
	 $selCompany = "SELECT * FROM group_company WHERE is_deleted='N' AND group_id=:group_id AND id=:company_id";
   $resCompany = $pdo->selectOne($selCompany,array(":group_id"=>$group_id,":company_id"=>$company_id));
}



$validate->string(array('required' => true, 'field' => 'name', 'value' => $name), array('required' => 'Name is required'));
$validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
    $validate->setError('address_2','Special character not allowed');
}
$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
$validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip), array('required' => 'Zip code is required'));
$validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'title is required'));
$validate->string(array('required' => true, 'field' => 'ein', 'value' => $ein), array('required' => 'EIN/FEIN is required'));
$validate->string(array('required' => true, 'field' => 'location', 'value' => $location), array('required' => 'Location is required'));
$validate->string(array('required' => true, 'field' => 'contact', 'value' => $contact), array('required' => 'Contact is required'));
$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 6, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

if(!$validate->getError('zip')){
	$zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zip));

	if(empty($zipRes)){
		$validate->setError('zip', 'Zip code is not valid');
	}else{
		$stateRes=$pdo->selectOne("SELECT id FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

		if(empty($stateRes)){
			$validate->setError('zip', 'Zip code is not valid');
		}else{
			$found_state_id = $stateRes['id'];
		}
	}
}

if (!$validate->getError('zip')){
    include_once '../includes/function.class.php';
    $function_list = new functionsList();
    $zipAddress = $function_list->uspsCityVerification($zip);
    if($zipAddress['status'] !='success'){
      $validate->setError("zip",$zipAddress['error_message']);
    }
}

if(!$validate->getError('state')){
	if($found_state_id != $state){
		$validate->setError('state', 'Zip code is not valid for this state');
	}
}

if ($validate->isValid()) {

	$upd_params = array(
		'group_id' => $group_id,
		'name' => $name,
		'address' => $address,
		'address_2' => $address_2,
		'city' => $city,
		'state' => $state,
		'zip' => $zip,
		'title' => $title,
		'ein' => $ein,
		'location' => $location,
		'contact' => $contact,
		'phone' => $phone,
		'email' => $email,
	);
	if(!empty($resCompany)){
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($resCompany['id'])
            )
        );
        $group_update_activity = $pdo->update("group_company",$upd_params,$update_where,true);
        $response['msg'] = "Company updated Successfully";

        /*--- Activity Feed -----*/
        $group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) AS name ,rep_id FROM customer WHERE id=:id",array(":id"=>$group_id));

        $description = array();
        $flg = "true";
        
        $description['ac_message'] = array(
	        'ac_red_1'=>array(
	            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	            'title' => $_SESSION['agents']['rep_id'],
	        ),
	        'ac_message_1' =>'  updated company details in Group '.$group_name['name'].' (',
	        'ac_red_2'=>array(
	            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
	            'title'=> $group_name['rep_id'],
	        ),
	        'ac_message_2' =>')<br/> Company : '.$name.'<br/>',
        );

        foreach($group_update_activity as $key2 => $val){
            if(array_key_exists($key2,$upd_params)){
                if(in_array($val,array('Y','N'))){
                    $val = $val == 'Y' ? "selected" : "unselected";
                }
                if($key2 == "phone") {
                    $val = format_telephone($val);
                    $upd_params[$key2] = format_telephone($upd_params[$key2]);
                }
                $key2_tmp = ucwords(str_replace('_',' ',$key2));
                $description['key_value']['desc_arr'][$key2_tmp] = ' Updated From '.$val." To ".$upd_params[$key2].".<br>";
                $flg = "false";
            } else {
                $description['description2'][] = ucwords(str_replace('_',' ',$val));
                $flg = "false";
            }
        }
        if($flg == "true"){
	        $description['description_novalue'] = 'No updates in group company details.';
	    }
        $desc=json_encode($description);
    	activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_name['id'],'Group','Agent Updated Group Company Details',"","",$desc);
        /*---/Activity Feed -----*/
	} else {
		$ins_company_id = $pdo->insert('group_company', $upd_params);
		//OP29-843 task updates, when add Any company Assign default Billing Profile to that copmany
		$billing_inserted = insertGroupCompanyBillingProfile($group_id,$ins_company_id);

		$response['msg'] = "Company added Successfully";

		/*--- Activity Feed -----*/
        $group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) AS name ,rep_id FROM customer WHERE id=:id",array(":id"=>$group_id));

        $description = array();
        $description['ac_message'] = array(
	        'ac_red_1'=>array(
	            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	            'title' => $_SESSION['agents']['rep_id'],
	        ),
	        'ac_message_1' =>'  added company in Group '.$group_name['name'].' (',
	        'ac_red_2'=>array(
	            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
	            'title'=> $group_name['rep_id'],
	        ),
	        'ac_message_2' =>')<br/> Company : '.$name.'<br/>',
        );
		if($billing_inserted){
			$description['desc'] = 'Added Default Billing Profile for '.$name;
		}
		
        $desc=json_encode($description);
    	activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_name['id'],'Group','Agent Added Company In Group',"","",$desc);
        /*---/Activity Feed -----*/
	}
	$response['status'] = 'success';
} else {
	$response['status'] = 'fail';
}
if (count($validate->getErrors()) > 0) {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>
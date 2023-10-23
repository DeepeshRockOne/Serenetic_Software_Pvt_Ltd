<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$response = array();
$agent_id = isset($_POST['agent_id'])?$_POST['agent_id']:'';
$agent_id_org = isset($_POST['agent_id_org'])?$_POST['agent_id_org']:'';

$is_address_ajaxed = isset($_POST['is_address_ajaxed'])?$_POST['is_address_ajaxed']:'';
$is_agency_address_ajaxed = checkIsset($_POST['is_agency_address_ajaxed']) ;

if($is_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zipcode = $_POST['zipcode'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = "";
        if(!empty($is_agency_address_ajaxed)){
            $response['agencyApi'] = 'success';
        }

        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zipcode);

        if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zipcode;
                        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                        $response['zip_response_status']='';
                        $response['address_response_status']='success';
                    }
                }
            }else if($zipAddress['status'] =='fail'){
                $response['status'] = 'fail';
                $response['errors'] = array("zipcode"=>$zipAddress['error_message']);
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

if($is_agency_address_ajaxed){

    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address2']);
    $city = $_POST['business_city'];
    $state = checkIsset($_POST['business_state']);
    $zipcode = $_POST['business_zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_business_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'business_zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = 'done';
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zipcode);

        if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zipcode;
                        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                        $response['zip_response_status']='';
                        $response['address_response_status']='success';
                    }
                }
            }else if($zipAddress['status'] =='fail'){
                $response['status'] = 'fail';
                $response['errors'] = array("business_zipcode"=>$zipAddress['error_message']);
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

/*---- Licence Operation -----*/
$ajax_delete = !empty($_POST['ajax_delete']) ? $_POST['ajax_delete'] : '' ;
$pending_license = !empty($_POST['pending_license']) ? $_POST['pending_license'] : '' ;
if ($ajax_delete) {
    $result = array();
    $lid = !empty($_POST['lid']) ? $_POST['lid'] : '' ;
    if(!empty($lid)){
        $license_sql = "SELECT * FROM agent_license WHERE md5(agent_id)=:agent_id AND id=:id AND is_deleted='N'";
        $license_row = $pdo->selectOne($license_sql,array(":agent_id"=>$agent_id,":id"=>$lid));
        if (!empty($license_row)) {
            $upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $license_row['id'],
                ),
            );
            if($pending_license == 1) { //Deleted from Pending Approvals
                if(!empty($license_row['license_num'])) {
                    $upd_data = array(
                        'new_request' => 'N',
                        'is_rejected' => 'N',
                        'new_license_status' => '',
                        'new_selling_licensed_state' => '',
                        'new_license_num' => '',
                        'new_license_active_date'=> NULL,
                        'new_license_not_expire' => '',
                        'new_license_exp_date' => NULL,
                        'new_license_type' => '',
                        'new_license_auth' => '',
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $pdo->update('agent_license',$upd_data,$upd_where);   

                    $desc = array();
                    $desc['ac_message'] = array(
                        'ac_red_1'=>array(
                            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                            'title'=> $_SESSION['agents']['rep_id'],
                        ),
                        'ac_message_1' =>' Updated Profile <br/>',
                        'ac_message_2' =>' Cancelled License Update for State : '.$license_row['selling_licensed_state'].'.'
                    );
                    $desc=json_encode($desc);
                    activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent Cancelled License Update',"","",$desc);
                } else {
                    $pdo->update('agent_license', array("is_deleted" => 'Y', 'updated_at' => 'msqlfunc_NOW()', 'license_removal_date'=>'msqlfunc_NOW()','license_status'=>'Inactive'), $upd_where);       

                    $desc = array();
                    $desc['ac_message'] = array(
                        'ac_red_1'=>array(
                            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                            'title'=> $_SESSION['agents']['rep_id'],
                        ),
                        'ac_message_1' =>' Updated Profile <br/>',
                        'ac_message_2' =>' License Deleted for State : '.$license_row['selling_licensed_state'].'.'
                    );
                    $desc=json_encode($desc);
                    activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent License Deleted',"","",$desc);                    
                }
            } else {
                $pdo->update('agent_license', array("is_deleted" => 'Y', 'updated_at' => 'msqlfunc_NOW()', 'license_removal_date'=>'msqlfunc_NOW()','license_status'=>'Inactive'), $upd_where);   

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1'=>array(
                        'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                        'title'=> $_SESSION['agents']['rep_id'],
                    ),
                    'ac_message_1' =>' Updated Profile <br/>',
                    'ac_message_2' =>' License Deleted for State : '.$license_row['selling_licensed_state'].'.'
                );
                $desc=json_encode($desc);
                activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent License Deleted',"","",$desc);
            }

            $rejection_license_row = $pdo->selectOne("SELECT id FROM agent_license WHERE is_rejected='Y' AND md5(agent_id)=:agent_id AND is_deleted='N'",array(":agent_id"=>$agent_id));

            if(empty($rejection_license_row)) {
                $updateParams = array(
                    'license_reject_status'=>'N',
                    'license_reject_text' => '',
                );
                $update_where = array(
                    'clause' => 'md5(customer_id) = :id',
                    'params' => array(
                        ':id' => $agent_id,
                    ),
                );
                $pdo->update('customer_settings',$updateParams,$update_where);
            }
        }
    }
    $result['status'] = "success";
    header('Content-type: application/json');
    echo json_encode($result); 
    exit;
}

$is_ajax_license = !empty($_POST['is_ajax_license']) ? $_POST['is_ajax_license'] : '' ;
if ($is_ajax_license) {
    $result = array();
    $license_expiry = $_POST["license_expiry"];
    $license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
    $license_number = $_POST['license_number'];
    $license_active = $_POST["license_active_date"];
    $license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
    $license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : '';
    $license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : '';
    $lid = !empty($_POST["lid"]) ? $_POST["lid"] : '';
    $hdn_license = $_POST["hdn_license"];
    $edit = !empty($_POST['edit']) ? $_POST['edit'] : '';

    $hdn_license = array_flip($hdn_license);
    foreach($hdn_license as $key => $value){
        $license_staten[$key] = $license_state;
        $license_numbern[$key] = $license_number;
        $license_activen[$key] = $license_active;
        $license_typen[$key] = $license_type;
        $license_authn[$key] = $license_auth;
        $license_expiryn[$key] = $license_expiry;
        $license_not_expn[$key] = $license_not_exp;
        if(!empty($lid)) {
            $hdn_license[$key] = $lid;    
        }
        $editn[$key] = $edit;
    }
    $ajax = 1;
    check_agent_license_validation($_SESSION['agents']['id'],$validate,$hdn_license,$license_staten,$license_numbern,$license_activen,$license_typen,$license_authn,$license_expiryn,$license_not_expn,$editn,$ajax);
    if($validate->isValid()){
        $doc_id = add_update_license($hdn_license,$license_staten,$license_numbern,$license_activen,$license_typen,$license_authn,$license_expiryn,$license_not_expn,$ajax);
        $result['status'] = "success";
        $result['doc_id'] = $doc_id;        
        /*--- Activity Feed -----*/
        if(!empty($doc_id)) {
            $flg = "true";
            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=>$_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' =>'  Updated Profile <br>',
            );
            foreach($doc_id as $key => $value){
                if(!empty($value) && is_array($value)){
                    foreach($value as $key2 => $val){
                        if(array_key_exists($key2,$new_update_details)){
                                $key2_org = $key2;
                                if(in_array($val,array('Y','N'))){
                                    $val = $val == 'Y' ? "selected" : "unselected";
                                }                            
                                if(in_array($key2,array("e_o_expiration"))) {
                                    $new_update_details[$key2] = date('m/d/Y',strtotime($new_update_details[$key2]));
                                    $val = date('m/d/Y',strtotime($val));
                                }

                                $desc['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$new_update_details[$key2_org].".<br>";
                                $flg = "false";
                        }else{
                            $desc['description2'][] = ucwords(str_replace('_',' ',$val));
                            $flg = "false";
                        }
                    }    
                }else{
                    if(is_array($value) && !empty($value)){
                        $desc['description'.$key][] = implode('',$value);
                        $flg = "false";
                    }else if(!empty($value)){
                        $desc['description'.$key][] = str_replace('_',' ',$value);
                        $flg = "false";
                    }
                }
                
            }
            if($flg == "true"){
                $desc['description_novalue'] = 'No updates in agent profile page.';
            }
            $desc = json_encode($desc);
            activity_feed(3,$_SESSION['agents']['id'],'Agent',$agent_id_org,'customer','Agent Profile Updated',"","",$desc);
        }
        /*---/Activity Feed ----*/

        $rejection_license_row = $pdo->selectOne("SELECT id FROM agent_license WHERE is_rejected='Y' AND md5(agent_id)=:agent_id AND is_deleted='N'",array(":agent_id"=>$agent_id));
        if(empty($rejection_license_row)) {
            $updateParams = array(
                'license_reject_status'=>'N',
                'license_reject_text' => '',
            );
            $update_where = array(
                'clause' => 'md5(customer_id) = :id',
                'params' => array(
                    ':id' => $agent_id,
                ),
            );
            $pdo->update('customer_settings',$updateParams,$update_where);
        }
    } else {
        $errors = $validate->getErrors();
        $result['errors'] = $errors;
        $result['status'] = "fail";
    }
    header('Content-type: application/json');
    echo json_encode($result); 
    exit;
}

function add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_exp,$ajax=''){
    $agent_doc_id ='';
    global $pdo;

    $license_key_arr = array(
        'selling_licensed_state' => 'Selling License state',
        'license_num' => 'License Number',
        'license_active_date' => 'License Active Date',
        'license_type' => 'License Type',
        'license_not_expire' => 'License Not Expire',
        'license_exp_date' => 'License Expire Date',
        'license_auth' => 'License Auth',
    );
    //insert and update license
    $agent_licence_activity = array();
    $i=0;

    $isSendNotification = false;
    $updatedState = array();
    $insertedState = array();
    $updatedRemoveState = array();
    $confirmedState = array();

    foreach ($hdn_license as $hkey => $h_id) {
        $i++;        
        /*pre_print($h_id,false);
        pre_print($hkey,false);
        pre_print($license_not_exp,false);
        pre_print($license_expiry,false);
        pre_print($license_state,false);*/
        //check if license id is empty/zero then we need to insert else we need to update
        if (empty($h_id)) {
            $h_id = 0;
        }

        $selADoc = "SELECT * FROM agent_license WHERE md5(agent_id)=:agent_id AND id=:id AND is_deleted='N'";
        $whrADoc = array(":agent_id" => $GLOBALS['agent_id'], ":id" => $h_id);
        $license_upd_row = $pdo->selectOne($selADoc, $whrADoc);
        if (!empty($license_upd_row['id'])) {
            $t_selling_licensed_state = $license_state[$hkey];
            $t_license_num = $license_number[$hkey];
            $t_license_active_date = date('Y-m-d', strtotime($license_active[$hkey]));
            $t_license_type = $license_type[$hkey];
            $t_license_not_expire = $license_not_exp[$hkey];
            if ($license_expiry[$hkey] != "" && $t_license_not_expire == 'N') {
                $t_license_exp_date = date('Y-m-d', strtotime($license_expiry[$hkey]));
            } else {
                $t_license_exp_date = date('Y-m-d', strtotime(date('12/31/2099')));
            }
            $t_license_auth = $license_auth[$hkey];

            //Check License Updated or Not
            if( $t_selling_licensed_state != $license_upd_row["selling_licensed_state"] ||
                $t_license_num != $license_upd_row["license_num"] ||
                $t_license_active_date != $license_upd_row["license_active_date"] ||
                $t_license_type != $license_upd_row["license_type"] ||
                $t_license_not_expire != $license_upd_row["license_not_expire"] ||
                $t_license_exp_date != $license_upd_row["license_exp_date"] ||
                $t_license_auth != $license_upd_row["license_auth"] ||
                isset($_POST['pending_license'])
            ) {
                $is_license_updated = true;
            } else {
                $is_license_updated = false;
            }

            if($is_license_updated == true) {
                $license_upd_data = array(
                    'new_request' => 'Y',
                    'new_license_status' => 'Pending Approval',
                    'new_selling_licensed_state' => $t_selling_licensed_state,
                    'new_license_num' => $t_license_num,
                    'new_license_active_date' => $t_license_active_date,
                    'new_license_type' => $t_license_type,
                    'new_license_not_expire' => $t_license_not_expire,
                    'new_license_exp_date' => $t_license_exp_date,
                    'new_license_auth' => $t_license_auth,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                $license_upd_where = array(
                    'clause' => 'id=:id',
                    'params' => array(
                        ':id' => $license_upd_row['id'],
                    ),
                );
                $license_upd_data = array_filter($license_upd_data, "strlen"); //removes null and blank array
                $updated_license_data = $pdo->update('agent_license', $license_upd_data, $license_upd_where,true);
                $updated_license_data = $license_upd_row;

                addAdminNotification(0, 3, "{HOST}/agent_detail_v1.php?id=".md5($_SESSION['agents']['id']),0,'N',$_SESSION['agents']['id']);
                alert_admin_license_add_update(md5($_SESSION['agents']['id']));

                $j = $license_upd_row['id'];

                if(!empty($updated_license_data)){
                    foreach($updated_license_data as $key => $license){
                        if(!isset($license_upd_data['new_'.$key]) || $license_upd_data['new_'.$key] == $license || $key == "license_status") {
                            continue;
                        }                        
                        if(in_array($key,array('license_exp_date','license_active_date'))){
                            $license = getCustomDate($license);
                            $license_upd_data['new_'.$key] = getCustomDate($license_upd_data['new_'.$key]);
                        }
                        if(in_array($license,array('Y','N'))){
                            $license = $license == 'Y' ? "Selected" : "Unselected";
                            $license_upd_data['new_'.$key] = $license_upd_data['new_'.$key] == 'Y' ? "Selected" : "Unselected";
                        }

                        if($license_upd_data['new_'.$key] == 'Business'){
                            $license_upd_data['new_'.$key] ='Agency';
                        }else if($license_upd_data['new_'.$key] == 'Personal'){
                            $license_upd_data['new_'.$key] ='Agent';
                        }

                        if($license == 'Business'){
                            $license ='Agency';
                        } else if($license == 'Personal'){
                            $license ='Agent';
                        }

                        if(array_key_exists('new_'.$key,$license_upd_data)){
                            if($license_upd_row['id'] == $j){
                                if(empty($license_upd_row["license_num"])) {
                                    $agent_licence_activity[] = 'resend license approval request for State : '.$license_state[$hkey].'<br>';
                                } else {
                                    if(empty($license_upd_row["new_license_num"])) {
                                        $agent_licence_activity[] = 'send license approval request for State : '.$license_state[$hkey].'<br>';
                                    } else {
                                        $agent_licence_activity[] = 'resend license approval request for State : '.$license_state[$hkey].'<br>';
                                    }
                                    
                                }
                                $j++;
                            }

                            $license = ucwords(str_replace('_',' ',$license));
                            $license_upd_data['new_'.$key] = ucwords(str_replace('_',' ',$license_upd_data['new_'.$key]));

                            $agent_licence_activity[] = '&nbsp;&nbsp;'.$license_key_arr[$key] .' Updated : From '.$license.' To '.$license_upd_data['new_'.$key]."<br>";
                        }
                    }
                }
            }
        } else {
            $ag = "SELECT id FROM customer WHERE md5(id)=:agent_id AND is_deleted='N'";
            $whrADoc = array(":agent_id" => $GLOBALS['agent_id']);
            $ag_res = $pdo->selectOne($ag, $whrADoc);
            $insparams = array(
                'new_request' => 'Y',
                'new_license_status' => 'Pending Approval',
                'agent_id' => $ag_res['id'],
                'new_selling_licensed_state' => $license_state[$hkey],
                'new_license_num' => $license_number[$hkey],
                'new_license_active_date'=>date('Y-m-d', strtotime($license_active[$hkey])),
                'new_license_not_expire' => $license_not_exp[$hkey],
                'new_license_type' => isset($license_type[$hkey]) ? $license_type[$hkey] : ''  ,
                'new_license_auth' => isset($license_auth[$hkey]) ?  $license_auth[$hkey] : '',
                'created_at' => 'msqlfunc_NOW()',
                'updated_at' => 'msqlfunc_NOW()',
                'license_added_date'=>'msqlfunc_NOW()',
            );
            if ($license_expiry[$hkey] != "") {
                $insparams['new_license_exp_date'] = $license_not_exp[$hkey]=='Y' ? date('Y-m-d', strtotime(date('12/31/2099'))) : date('Y-m-d', strtotime($license_expiry[$hkey]));
            }
            $insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
            $agent_doc_id = $pdo->insert('agent_license', $insparams);
            $agent_licence_activity[] = 'send license approval request for State : '.$license_state[$hkey].'.<br>';

            addAdminNotification(0, 3, "{HOST}/agent_detail_v1.php?id=".md5($_SESSION['agents']['id']),0,'N',$_SESSION['agents']['id']);
            alert_admin_license_add_update(md5($_SESSION['agents']['id']));
        }
    }
    // return $agent_doc_id;
    return $agent_licence_activity;
}
/*----/Licence Operation -----*/


$section = isset($_POST['section'])?$_POST['section']:'';
$cust_table_data = array();
$cs_table_data = array();
$agent_update_activity = array();

/*-- account_tab --*/
$account_type = isset($_POST["account_type"])?$_POST["account_type"]:'';

$business_name = isset($_POST['business_name'])?$_POST['business_name']:'';
$business_address = isset($_POST['business_address'])?$_POST['business_address']:'';
$business_address2 = isset($_POST['business_address2'])?$_POST['business_address2']:'';
$business_city = isset($_POST['business_city'])?$_POST['business_city']:'';
$business_state = isset($_POST['business_state'])?$_POST['business_state']:'';
$business_zipcode = isset($_POST['business_zipcode'])?$_POST['business_zipcode']:'';
$business_taxid = isset($_POST['business_taxid'])?$_POST['business_taxid']:'';

$is_address_verified = $_POST['is_address_verified'];

$fname = isset($_POST['fname'])?$_POST['fname']:'';
$lname = isset($_POST['lname'])?$_POST['lname']:'';
$email = checkIsset($_POST['email']);
$cell_phone = isset($_POST['cell_phone']) ? phoneReplaceMain($_POST['cell_phone']) : '';
$dob = $_POST['dob'];
$address = isset($_POST['address'])?$_POST['address']:'';
$address_2 = isset($_POST['address_2'])?$_POST['address_2']:'';
$city = isset($_POST['city'])?$_POST['city']:'';
$state = isset($_POST['state'])?$_POST['state']:'';
$zipcode = isset($_POST['zipcode'])?str_replace("_",'',$_POST['zipcode']):'';
$ssn = isset($_POST['ssn'])?phoneReplaceMain($_POST['ssn']):'';
$is_ssn_edit = isset($_POST['is_ssn_edit'])?$_POST['is_ssn_edit']:'';

$password = !empty($_POST['password']) ? $_POST['password'] : '';
$c_password = !empty($_POST['c_password']) ? $_POST['c_password'] : '';
$is_2fa = !empty($_POST['is_2fa']) ? $_POST['is_2fa'] : 'N';
$is_ip_restriction = !empty($_POST['is_ip_restriction']) ? $_POST['is_ip_restriction'] : 'N';
$allowed_ip_res = !empty($_POST['allowed_ip_res']) ? $_POST['allowed_ip_res'] : array();
if($is_ip_restriction == "N") {
    $allowed_ip_res = array();
}
$send_via = checkIsset($_POST['send_via']);
$via_mobile = checkIsset($_POST['via_mobile'])!='' ? phoneReplaceMain($_POST['via_mobile']) : '';
$via_email = checkIsset($_POST['via_email']);

/*-- attribute_tab --*/
$npn_no = isset($_POST['npn_number'])?$_POST['npn_number']:'';
$w9_form_business = isset($_FILES["w9_form_business"])?$_FILES["w9_form_business"]:'';
$e_o_coverage = isset($_POST['e_o_coverage'])?$_POST['e_o_coverage']:'';
$e_o_by_parent = isset($_POST['e_o_by_parent']) ? $_POST['e_o_by_parent']:'N';
if ($e_o_coverage == "Y") {
    $e_o_amount = isset($_POST['e_o_amount'])?str_replace(array("$", ","), array("", ""),$_POST['e_o_amount']):'';
    $e_o_expiration = isset($_POST['e_o_expiration'])?$_POST['e_o_expiration']:'';
    $e_o_document = isset($_FILES['e_o_document'])?$_FILES['e_o_document']:'';
}

/*-- personal_brand_tab --*/
$display_in_member = !empty($_POST['display_in_member']) ? 'Y' : 'N';
$public_name = isset($_POST["public_name"])?$_POST["public_name"]:'';
$public_email = checkIsset($_POST["public_email"]);
$public_phone = isset($_POST["public_phone"])?phoneReplaceMain($_POST["public_phone"]):'';
$is_branding = !empty($_POST['is_branding']) ? 'Y' : 'N';

/*-- direct_deposit_tab --*/
$d_account_type = isset($_POST['d_account_type'])?$_POST['d_account_type']:'';
$d_bank_name = isset($_POST['d_bank_name'])?$_POST['d_bank_name']:'';
$d_routing_number = isset($_POST['d_routing_number'])?$_POST['d_routing_number']:'';
$d_account_number = isset($_POST['d_account_number']) ? $_POST['d_account_number'] : '';
$d_c_account_number = isset($_POST['d_c_account_number']) ? $_POST['d_c_account_number'] : '';

if($section == "account_tab") {
    $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account type is required'));

    if ($account_type == "Business") {              
        $validate->string(array('required' => true, 'field' => 'business_name', 'value' => $business_name), array('required' => 'Agency Legal Name is required.'));
        $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $business_address), array('required' => 'Address required.'));
        if(!empty($business_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$business_address2)) {
            $validate->setError('business_address2','Special character not allowed');
        }
        $validate->string(array('required' => true, 'field' => 'business_city', 'value' => $business_city), array('required' => 'City required.'));
        $validate->string(array('required' => true, 'field' => 'business_state', 'value' => $business_state), array('required' => 'State required.'));
        $validate->string(array('required' => true, 'field' => 'business_zipcode', 'value' => $business_zipcode ,'min'=>5), array('required' => 'Zip Code required.'));
        if (!$validate->getError('business_zipcode')){
            include_once '../includes/function.class.php';
            $function_list = new functionsList();
            $zipAddress = $function_list->uspsCityVerification($business_zipcode);
            if($zipAddress['status'] !='success'){
                $validate->setError("business_zipcode",$zipAddress['error_message']);
            }
        }
    }

    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'Firstname is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Lastname is required'));
    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

    if(!$validate->getError('email')) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("email", "Valid Email is required");
        } else {
            /*$check_email = "SELECT email FROM customer WHERE type='Agent' AND email=:email AND md5(id)!=:id AND is_deleted='N'";
            $where_email = array(':email' => $email, ":id" => $agent_id);
            $email_exist = $pdo->selectOne($check_email, $where_email);
            if ($email_exist) {
                $validate->setError("email", "This email is already associated with another agent account.");
            }*/
        }
    }

    $validate->digit(array('required' => true, 'field' => 'cell_phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
        $validate->setError('address_2','Special character not allowed');
    }
    $validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
    $validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5 ), array('required' => 'Zip Code is required'));

    if ($is_ssn_edit == "Y") {
        $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
    }

    $validate->string(array('required' => true, 'field' => 'dob', 'value' => $dob), array('required' => 'Date of Birth is required'));
    if (!$validate->getError('dob') && !empty($dob)) {
        list($mm, $dd, $yyyy) = explode('/', $dob);
        if (!checkdate($mm, $dd, $yyyy)) {
            $validate->setError('dob', 'Valid Date of Birth is required');
        }
        if (!$validate->getError('dob')) {
            $age_y = dateDifference($dob, '%y');
            if ($age_y < 18) {
                $validate->setError('dob', 'You must be 18 years of age');
            } else if ($age_y > 90) {
                $validate->setError('dob', 'You must be younger then 90 years of age');
            }
        }
    }

    if(!empty($password)){
        $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
        $validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));

        //for strong password
        if (!$validate->getError('password')) {
            if (strlen($password) < 8 || strlen($password) > 20) {
                $validate->setError('password', 'Password must be 8-20 characters');
            } else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
                || !preg_match('`[0-9]`', $password)) {
                // at least one digit
                $validate->setError('password', 'Valid Password is required');
            } else if (!ctype_alnum($password)) {
                $validate->setError('password', 'Special character not allowed');
            } else if (preg_match('`[?/$\*+]`', $password)) {
                $validate->setError('password', 'Password not valid');
            } else if (preg_match('`[,"]`', $password)) {
                $validate->setError('password', 'Password not valid');
            } else if (preg_match("[']", $password)) {
                $validate->setError('password', 'Password not valid');
            }
        }

        if (!$validate->getError('c_password') && !$validate->getError('password')) {
            if ($password != $c_password) {
                $validate->setError('c_password', 'Both Password must be same');
            }
        }
    }

    if($is_2fa == 'Y'){
        if($send_via == ''){
          $validate->setError('send_via', 'Please select any method.');
        }else{
          if($send_via == 'sms'){
            $validate->phoneDigit(array('required' => true, 'field' => 'via_mobile', 'value' => $via_mobile), array('required' => 'Phone number is required', 'invalid' => 'Enter valid phone number'));
          }else{
            $validate->email(array('required' => true, 'field' => 'via_email', 'value' => $via_email), array('required' => 'Email Address is required.', 'invalid' => 'Please enter valid Email Address'));
          }
        }
    }

    if($is_ip_restriction == "Y") {
        foreach ($allowed_ip_res as $key => $allowed_ip) {
            $validate->string(array('required' => true, 'field' => 'ip_address_'.$key, 'value' => $allowed_ip), array('required' => 'IP Address is required'));
            if (!empty($allowed_ip) && !filter_var($allowed_ip, FILTER_VALIDATE_IP)) {
                $validate->setError('ip_address_'.$key, 'IP Address not valid');
            }
        }
    }
}

if($section == "attribute_tab" && 0) {
    $validate->digit(array('required' => true, 'field' => 'npn_number', 'value' => $npn_no), array('required' => 'NPN number is required', 'invalid' => 'Valid NPN number is required'));

    if (empty($_POST["w9_pdf"]) && !empty($w9_form_business)) {
        if (!isset($w9_form_business) || $w9_form_business['error'] == UPLOAD_ERR_NO_FILE) {
            $validate->setError('w9_form_business', "Please add w9 file");
        } else {
            if ($w9_form_business["type"] != "application/pdf") {
                $validate->setError('w9_form_business', "Please add valid w9 pdf file");
            }
        }
    }

    $validate->string(array('required' => true, 'field' => 'e_o_coverage', 'value' => $e_o_coverage), array('required' => 'Select any option'));
    if ($e_o_coverage == 'Y' && $e_o_by_parent=="N") {
            $validate->string(array('required' => true, 'field' => 'e_o_expiration', 'value' => $e_o_expiration), array('required' => 'Expiration Date is required'));
            if ($e_o_expiration != "") {
                if (validateDate($e_o_expiration,'m/d/Y')) {
                    if (!isFutureDateMain($e_o_expiration,'m/d/Y')) {
                        $validate->setError("e_o_expiration", "Please Add Future Expiration Date is required");
                    }
                } else {
                    $validate->setError("e_o_expiration", "Valid Expiration Date is required");
                }
            }
    }

    if ($e_o_coverage == "Y" && $e_o_by_parent == 'N') {
        if (empty($_POST["chk_e_o_document"]) && !empty($e_o_document)) {
            if (checkIsset($e_o_document['error']) == UPLOAD_ERR_NO_FILE) {
                $validate->setError('e_o_document', "Please add E&O document");
            } else {
                if (!empty($e_o_document["name"]) && !in_array($e_o_document["type"], array("application/pdf", "application/doc"))) {
                    $validate->setError('e_o_document', "Please add valid E&O document");
                }
            }
        }
    }
}

if($section == "personal_brand_tab") {
    $validate->string(array('required' => true, 'field' => 'public_name', 'value' => $public_name), array('required' => 'Name is required'));

    $validate->email(array('required' => true, 'field' => 'public_email', 'value' => $public_email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));

    $validate->digit(array('required' => true, 'field' => 'public_phone', 'value' => $public_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
}

if($section == "direct_deposit_tab") {
    $validate->string(array('required' => true, 'field' => 'd_account_type', 'value' => $d_account_type), array('required' => 'Account type is required'));
    $validate->string(array('required' => true, 'field' => 'd_bank_name', 'value' => $d_bank_name), array('required' => 'Bank Name is required'));

    $validate->digit(array('required' => true, 'field' => 'd_routing_number', 'value' => $d_routing_number), array('required' => 'Routing number is required', 'invalid' => 'Only digits allow'));

    $validate->digit(array('required' => true, 'field' => 'd_account_number', 'value' => $d_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

    $validate->digit(array('required' => true, 'field' => 'd_c_account_number', 'value' => $d_c_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Account number"));

    if (!$validate->getError("d_routing_number")) {
        if (checkRoutingNumber($d_routing_number) == false) {
            $validate->setError("d_routing_number", "Enter valid routing number");
        }
    }
    if (!$validate->getError('d_account_number')) {
        if ($d_c_account_number != '') {
            if ($d_account_number != $d_c_account_number) {
                $validate->setError('d_c_account_number', 'Account number not matched');
            }
        }
    }
}

if (!$validate->getError('zipcode')){
    include_once '../includes/function.class.php';
    $function_list = new functionsList();
    $zipAddress = $function_list->uspsCityVerification($zipcode);
    if($zipAddress['status'] !='success'){
        $validate->setError("zipcode",$zipAddress['error_message']);
    }
}

if($validate->isValid()) {
    //writing number end
    $new_update_details = array(
        'account_type' => checkIsset($account_type)=='Business' ? 'Agency' : 'Agent',
        'fname' => checkIsset($fname),
        'lname' => checkIsset($lname),
        'address' => checkIsset($address),
        'address_2' => checkIsset($address_2),
        'city' => checkIsset($city),
        'state' => checkIsset($state),
        'zip' => checkIsset($zipcode),
        'public_name' => checkIsset($public_name),
        'public_email' => checkIsset($public_email),
        'public_phone' => checkIsset($public_phone),
        'email' => checkIsset($email),
        'cell_phone' => checkIsset($cell_phone),
        'last_four_ssn' =>substr($ssn, -4),
        'company_name' => checkIsset($business_name),
        'company_address' => checkIsset($business_address),
        'company_address_2' => checkIsset($business_address2),
        'company_city' => checkIsset($business_city),
        'company_state' => checkIsset($business_state),
        'company_zip' => checkIsset($business_zipcode),
        'tax_id' => checkIsset($business_taxid),
        'npn' => checkIsset($npn_no),
        'display_in_member' =>  isset($_POST['display_in_member']) ? 'Selected' : 'Unselected' ,
        'is_branding' =>  isset($_POST['is_branding']) ? 'Selected' : 'Unselected' ,
        'e_o_coverage' => checkIsset($e_o_coverage),
        'e_o_amount' => checkIsset($e_o_amount),
        'e_o_expiration' => checkIsset($e_o_expiration),
        'birth_date' => $dob,
        'is_2fa' => isset($_POST['is_2fa']) ? 'Selected' : 'Unselected',
        'is_ip_restriction' => isset($_POST['is_ip_restriction']) ? 'Selected' : 'Unselected',
        'allowed_ip' => implode(',',array_values($allowed_ip_res)),
        'send_otp_via' => checkIsset($send_via),
        'via_sms' => checkIsset($via_mobile),
        'via_email' => checkIsset($via_email),
    );

    if($section == "account_tab") {
        $cs_table_data['account_type'] = $account_type;
        if ($account_type == "Business") {
            $cs_table_data = array_merge($cs_table_data,array(
                'company_name' => $business_name,
                'company_address' => $business_address,
                'company_address_2' => $business_address2,
                'company_city' => $business_city,
                'company_state' => $business_state,
                'company_zip' => $business_zipcode,
                'tax_id' => $business_taxid,
            ));
        }
        
        $cust_table_data = array_merge($cust_table_data,array(
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'cell_phone' => $cell_phone,
            'address' => $address,
            'address_2' => $address_2,
            'city' => $city,
            'state' => $state,
            'zip' => $zipcode,
            'birth_date' => date('Y-m-d',strtotime($dob)),
        ));

        if ($is_ssn_edit == "Y" && $ssn != "") {
            $cust_table_data['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
            $cust_table_data['last_four_ssn'] = substr($ssn, -4);
        }

        if ($password != "") {
            $cust_table_data['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
        }
        $cs_table_data['is_address_verified'] = $is_address_verified;
        $cs_table_data['is_2fa'] = $is_2fa;
        if($send_via !=''){
            $cs_table_data['send_otp_via'] = $send_via;
            if($send_via == 'sms'){
              $cs_table_data['via_sms'] = $via_mobile;
            }else{
              $cs_table_data['via_email'] = $via_email;
            }
        }
        $cs_table_data['is_ip_restriction'] = $is_ip_restriction;
        if($is_ip_restriction == "Y") {
            $cs_table_data['allowed_ip'] = implode(',',array_values($allowed_ip_res));
        } else {
            $cs_table_data['allowed_ip'] = "";
        }
    }

    if($section == "attribute_tab") {
        $cs_table_data['npn'] = $npn_no;

        $w9_doc = $w9_form_business;
        if (!empty($w9_doc["name"])) {
            $agent_res = $pdo->selectOne("SELECT w9_pdf from customer_settings where md5(customer_id)=:id",array(":id"=>$agent_id));
            $w9_pdf_extension_tmp = explode(".", $w9_doc['name']);
            $w9_pdf_extension = end($w9_pdf_extension_tmp);
            $w9_pdf_tmp_name = $w9_doc['tmp_name'];
            $new_w9_pdf_name = 'w9_doc_' . round(microtime(true)) . '.' . $w9_pdf_extension;
            $new_update_details['w9_pdf'] = $new_w9_pdf_name;

            //Remove Existing File            
            if (!empty($agent_res["w9_pdf"])) {
                if (file_exists($AGENT_DOC_DIR . $agent_res["w9_pdf"])) {
                    unlink($AGENT_DOC_DIR . $agent_res["w9_pdf"]);
                }
            }

            move_uploaded_file($w9_pdf_tmp_name, $AGENT_DOC_DIR . $new_w9_pdf_name);
            $response["w9_pdf_link"] = $AGENT_DOC_WEB . $new_w9_pdf_name;
            
            $upd_where = array(
                'clause' => 'md5(customer_id) = :id',
                'params' => array(
                    ':id' => $agent_id,
                ),
            );
            $pdo->update('customer_settings', array('w9_pdf' => $new_w9_pdf_name),$upd_where);

            $agent_update_activity['customer_setting_doc'] = array("w9 document updated.");
        }

        /*--- e_o_document ---*/
        if(!empty($e_o_document)){
            $tmp_v1 = explode(".", $e_o_document['name']);
            $extension = end($tmp_v1);
            $doc_tmp_name = $e_o_document['tmp_name'];
            $e_o_coverage_filename = 'agent_doc_' . round(microtime(true)) . '.' . $extension;
            $selADoc = "SELECT e_o_document FROM agent_document WHERE md5(agent_id)=:agent_id";
            $whrADoc = array(":agent_id" => $agent_id);
            $resADoc = $pdo->selectOne($selADoc, $whrADoc);
            if ($resADoc) {
                $updateparams = array(
                    'e_o_coverage' => $e_o_coverage,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                if ($e_o_coverage == 'Y' &&!empty($e_o_document['name'])) {
                    $updateparams['e_o_document'] = $e_o_coverage_filename;

                    $existingErrorDocument = $resADoc["e_o_document"];
                    if ($existingErrorDocument != "") {
                        if (file_exists($AGENT_DOC_DIR . $existingErrorDocument)) {
                            unlink($AGENT_DOC_DIR . $existingErrorDocument);
                        }
                    }
                    move_uploaded_file($doc_tmp_name, $AGENT_DOC_DIR . $e_o_coverage_filename);
                    $response["e_o_document_link"] = $AGENT_DOC_WEB . $e_o_coverage_filename;
                }
                $upd_where = array(
                    'clause' => 'md5(agent_id) = :id',
                    'params' => array(
                        ':id' => $agent_id,
                    ),
                );
                $new_update_details['e_o_document'] = $updateparams['e_o_document'];
                $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
                $pdo->update('agent_document', $updateparams, $upd_where);
                $agent_update_activity['agent_document_file'] = array('E&O Document Updated.');
            } 
        }

        $selADoc = "SELECT id FROM agent_document WHERE md5(agent_id)=:agent_id";
        $whrADoc = array(":agent_id" => $agent_id);
        $resADoc = $pdo->selectOne($selADoc, $whrADoc);
        if (!empty($resADoc) && count($resADoc) > 0) {
            $updateparams = array(
                'e_o_coverage' => $e_o_coverage,
                'e_o_amount' => $e_o_amount,
                'by_parent'=>$e_o_by_parent,
                'updated_at' => 'msqlfunc_NOW()',
            );
            if ($e_o_expiration != "") {
                $updateparams['e_o_expiration'] = date('Y-m-d', strtotime($e_o_expiration));
            }
            $upd_where = array(
                'clause' => 'md5(agent_id) = :id',
                'params' => array(
                    ':id' => $agent_id,
                ),
            );
            $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
            $agent_update_activity['agent_document'] = $pdo->update('agent_document', $updateparams, $upd_where,true);
        }
        /*---/e_o_document ---*/
    }

    if($section == "personal_brand_tab") {
        $cs_table_data['display_in_member'] = $display_in_member;
        //$cs_table_data['is_branding'] = $is_branding; //Removed By Troy

        $cust_table_data['public_name'] = $public_name;
        $cust_table_data['public_email'] = $public_email;
        $cust_table_data['public_phone'] = $public_phone;
    }

    if($section == "direct_deposit_tab") {
        $dd_sql = "SELECT id FROM direct_deposit_account WHERE customer_id=:customer_id ORDER BY id DESC";
        $dd_where = array(":customer_id" => $agent_id_org);
        $dd_row = $pdo->selectOne($dd_sql, $dd_where);
        if(!empty($dd_row)) {
            $dd_upd_data = array(
                'bank_name' => $d_bank_name,
                'account_type' => $d_account_type,
                'routing_number' => $d_routing_number,
                'account_number' => $d_account_number,
                'updated_at' => 'msqlfunc_NOW()',
            );
            $dd_upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $dd_row['id'],
                ),
            );
            $pdo->update('direct_deposit_account',$dd_upd_data,$dd_upd_where,true);
            $agent_update_activity['direct_deposit_account'] = "Direct deposite account updated<br/>Bank Name : ".$d_bank_name." <br/> Account Type : ".ucfirst($d_account_type)." <br/> ABA Routing : ".$d_routing_number." <br/> Account Number : *". substr($d_account_number, -4);
        } else {
            $dd_data = array(
                'customer_id' => $agent_id_org,
                'bank_name' => $d_bank_name,
                'account_type' => $d_account_type,
                'routing_number' => $d_routing_number,
                'account_number' => $d_account_number,
                'effective_date' => date('Y-m-d'),
                'status' => 'Active',
                'created_at' => 'msqlfunc_NOW()',
                'updated_at' => 'msqlfunc_NOW()',
            );
            $pdo->insert('direct_deposit_account', $dd_data); 
            $desc = array();
            $desc['description']['new_account'] = 'New direct deposite account Added!';
            $desc = json_encode($desc);
            activity_feed(3,$agent_id_org, 'Agent', $agent_id_org, 'Agent', 'Direct Deposite Account',"","",$desc);
        }

        $short_d_account_number = ($d_account_number!='') ? "(*". substr($d_account_number, -4).")":'';

        $response['direct_deposit_detail'] = '<li class="text-success"><i class="fa fa-check-circle"></i></li><li>'.$d_bank_name.'</li><li class="text-capitalize">'.$d_account_type.'</li><li>ABA Routing ('.$d_routing_number.')</li><li>Account Number '.$short_d_account_number.'</li><li><b class="text-blue">Active</b></li>';
    }

    if(!empty($cust_table_data)) {
        $cust_table_data['updated_at'] = 'msqlfunc_NOW()';

        $cust_upd_where = array(
            'clause' => 'md5(id)=:id',
            'params' => array(
                ':id' => $agent_id,
            ),
        );
        $agent_update_activity['customer'] = $pdo->update('customer',$cust_table_data,$cust_upd_where,true);
    }

    if(!empty($cs_table_data)) {
        $cs_upd_where = array(
            'clause' => 'md5(customer_id) = :id',
            'params' => array(
                ':id' => $agent_id,
            ),
        );
        $agent_update_activity['customer_settings'] = $pdo->update('customer_settings',$cs_table_data,$cs_upd_where,true);
    }

    $response['status'] = "success";

    /*---- Activity Feed ----*/
    if(!empty($agent_update_activity)){
        $flg = "true";
        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title'=>$_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>'  Updated Profile <br>',
        );
        foreach($agent_update_activity as $key => $value){
            if(!empty($value) && is_array($value)){
                foreach($value as $key2 => $val){
                    if(array_key_exists($key2,$new_update_details)){
                            $key2_org = $key2;
                            if(in_array($val,array('Y','N'))){
                                $new_update_details[$key2_org] = $val == 'Y' ? "unselected" : "selected";
                                $val = $val == 'Y' ? "selected" : "unselected";
                            }
                            if($key2=='account_type'){
                                $val = $val =='Business' ? 'Agency' : 'Agent';
                            }
                            if(in_array($key2,array('is_2fa'))){
                                $key2 = "Two-Factor Authentication (2FA)";
                            }
                            if(in_array($key2,array('is_ip_restriction'))){
                                $key2 = "IP Address Restriction";
                            }

                            if($key2=='display_in_member'){
                                $key2 = "Display personal branding in the member portal";
                            }
                            
                            if($key2 == 'is_branding'){
                                $key2 = "Allow personal branding of agent portal";
                            }
                            
                            if(in_array($key2,array("e_o_expiration"))) {
                                $new_update_details[$key2] = date('m/d/Y',strtotime($new_update_details[$key2]));
                                $val = date('m/d/Y',strtotime($val));
                            }
                            if($key2=='birth_date'){
                                $val = date('m/d/Y',strtotime($val));
                            }
                            $tmp_key2 = ucfirst(str_replace('_',' ',$key2));

                            $desc['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$new_update_details[$key2_org].".<br>";
                            $flg = "false";
                    }else{
                        $desc['description2'][] = ucwords(str_replace('_',' ',$val));
                        $flg = "false";
                    }
                }    
            }else{
                if(is_array($value) && !empty($value)){
                    $desc['description'.$key][] = implode('',$value);
                    $flg = "false";
                }else if(!empty($value)){
                    $desc['description'.$key][] = $value;
                    $flg = "false";
                }
            }
            
        }
        if($section == "account_tab" && $password !=''){
            $desc['description_password'] = 'Password updated.';
            $flg = "false";
        }

        if($flg == "true"){
            $desc['description_novalue'] = 'No updates in agent profile page.';
        }
        $desc = json_encode($desc);
        activity_feed(3,$_SESSION['agents']['id'],'Agent',$agent_id_org,'customer','Agent Profile Updated',"","",$desc);
    }
    /*----/Activity Feed ----*/
}
if(count($validate->getErrors()) > 0){
    $response['status'] = "errors";   
    $response['errors'] = $validate->getErrors();   
}
echo json_encode($response);
dbConnectionClose();
exit();
<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new Validation();
$response = array();
$agent_id = $_POST['agent_id'];
$license_ids = $_POST["pending_hdn_license"];
$license_state = $_POST["pending_license_state"];
$license_number = $_POST["pending_license_number"];
$license_active_date = $_POST["pending_license_active_date"];
$license_expiry = $_POST["pending_license_expiry"];
$license_not_expire = isset($_POST["pending_license_not_expire"])?$_POST["pending_license_not_expire"]:array();
$license_type = $_POST["pending_license_type"];
$licsense_authority = $_POST["pending_licsense_authority"];

foreach($license_number as $lekey => $v) {
    if(!empty($license_not_expire[$lekey]) && $license_not_expire[$lekey] == 'Y'){

    } else {
        if(!isset($license_expiry[$lekey]) || empty($license_expiry[$lekey])){
            $validate->setError("license_expiry_" . $lekey, "License expiry required");
        } else {
            if (validateDate($license_expiry[$lekey],'m/d/Y')) {
                if (!isFutureDateMain($license_expiry[$lekey],'m/d/Y')) {
                    $validate->setError("license_expiry_" . $lekey, "Please Add Future License Date is required");
                }
            }
        }
    }

    if(!isset($license_active_date[$lekey])){
        $validate->setError("license_active_date_" . $lekey, "License Active date required");
    } else {
        $validate->string(array('required' => true, 'field' => 'license_active_date_' . $lekey, 'value' => $license_active_date[$lekey]), array('required' => 'License Active date required'));
    }

    if(!isset($license_number[$lekey])){
        $validate->setError("license_number_" . $lekey, "Valid license Number is required");
    } else {
        $validate->string(array('required' => true, 'field' => 'license_number_' . $lekey, 'value' => $license_number[$lekey]), array('required' => 'Valid license Number is required'));
    }
    
    if(!isset($license_type[$lekey])){
        $validate->setError("license_type_" . $lekey, "License Type required");
    } else {
        $validate->string(array('required' => true, 'field' => 'license_type_' . $lekey, 'value' => $license_type[$lekey]), array('required' => 'License Type required'));
    }
    
    $l_auth = !empty($licsense_authority[$lekey]) ? $licsense_authority[$lekey] : "";
    if(!isset($l_auth) || empty($l_auth)){
        $validate->setError("licsense_authority_" . $lekey, "License Of Authority required");
    } else {
        $validate->string(array('required' => true, 'field' => 'licsense_authority_' . $lekey, 'value' => $l_auth), array('required' => 'License Of Authority required'));
    }

    $l_state = !empty($license_state[$lekey]) ? $license_state[$lekey] : "";
    if(!isset($l_state) || empty($l_state)){
        $validate->setError("license_state_" . $lekey, "Select License State");
    } else {
        $validate->string(array('required' => true, 'field' => 'license_state_' . $lekey, 'value' => $l_state), array('required' => 'License state is required'));
    }
}

if ($validate->isValid()) {
    $agent_row = $pdo->selectOne('SELECT * FROM customer WHERE md5(id)=:id',array(":id" => $agent_id));
    $selling_licensed_states = array();

    foreach($license_number as $hkey => $v){
        $license_id = $license_ids[$hkey];
        $license_sql = "SELECT * FROM agent_license WHERE id=:id";
        $license_row = $pdo->selectOne($license_sql, array(":id" => $license_id));
        if ($license_row) {
            $selling_licensed_states[] = $license_row['new_selling_licensed_state'];

            $t_selling_licensed_state = $license_state[$hkey];
            $t_license_num = $license_number[$hkey];
            $t_license_active_date = date('Y-m-d', strtotime($license_active_date[$hkey]));
            $t_license_type = $license_type[$hkey];
            $t_license_not_expire = isset($license_not_expire[$hkey])?$license_not_expire[$hkey]:'N';
            if ($license_expiry[$hkey] != "" && $t_license_not_expire == 'N') {
                $t_license_exp_date = date('Y-m-d', strtotime($license_expiry[$hkey]));
            } else {
                $t_license_exp_date = date('Y-m-d', strtotime(date('12/31/2099')));
            }
            $t_license_auth = $licsense_authority[$hkey];

            $license_upd_data = array(
                'is_rejected'=>"N",
                'new_request'=>'Y',
                'extended_attempt'=>0,
                'extended_date'=>'0000-00-00',
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
                    ':id' => $license_id
                ),
            );
            $pdo->update('agent_license',$license_upd_data,$license_upd_where);

            /*--- Activity Feed Data ---*/
            $flg = "true";
            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_row['id']),
                    'title'=> $agent_row['rep_id'],
                ),
                'ac_message_1' =>' updated license for State : '.$license_row['new_selling_licensed_state'].' <br/>'
            );

            $license_key_arr = array(
                'selling_licensed_state' => 'Selling License state',
                'license_num' => 'License Number',
                'license_active_date' => 'License Active Date',
                'license_type' => 'License Type',
                'license_not_expire' => 'License Not Expire',
                'license_exp_date' => 'License Expire Date',
                'license_auth' => 'License Auth',
            );

            $license_af_data = array(
                'selling_licensed_state' => $license_row['new_selling_licensed_state'],
                'license_num' => $license_row['new_license_num'],
                'license_active_date' => $license_row['new_license_active_date'],
                'license_type' => $license_row['new_license_type'],
                'license_not_expire' => $license_row['new_license_not_expire'],
                'license_exp_date' => $license_row['new_license_exp_date'],
                'license_auth' => $license_row['new_license_auth'],
            );

            foreach($license_af_data as $key => $license) {
                if(!isset($license_upd_data['new_'.$key]) || $license_upd_data['new_'.$key] == $license) {
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
                    $license ='Agent';
                }else if($license_upd_data['new_'.$key] == 'Personal'){
                    $license_upd_data['new_'.$key] ='Agent';
                    $license ='Agency';
                }

                $license = ucwords(str_replace('_',' ',$license));
                $license_upd_data['new_'.$key] = ucwords(str_replace('_',' ',$license_upd_data['new_'.$key]));

                $value = '&nbsp;&nbsp;'.$license_key_arr[$key] .' Updated : From '.$license.' To '.$license_upd_data['new_'.$key]."<br>";

                $desc['description'.$key][] = str_replace('_',' ',$value);
                $flg = "false";
            }

            if($flg == "false") {
                $desc = json_encode($desc);
                activity_feed(3,$agent_row['id'],'Agent',$agent_row['id'],'Agent','Agent Profile Updated',"","",$desc);
            }
            /*---/Activity Feed Data ---*/
        }
    }
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
    setNotifySuccess("License Updated Successfully");

    if(!empty($selling_licensed_states)) {
        $selling_licensed_states_str = implode(', ',$selling_licensed_states);
        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_row['id']),
                'title'=> $agent_row['rep_id'],
            ),
            'ac_message_2' =>' resend license approval request for State : '.$selling_licensed_states_str
        );
        $desc=json_encode($desc);
        activity_feed(3,$agent_row['id'],'Agent',$agent_row['id'],'Agent','Agent Resend License Approval Request ',"","",$desc);

        addAdminNotification(0, 3, "{HOST}/agent_detail_v1.php?id=".$agent_id,0,'N',$_SESSION['agents']['id']);

        alert_admin_license_add_update($agent_id);
    }

    $response['status'] = "success";
} else {
    $response['status'] = "fail";
    $response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>
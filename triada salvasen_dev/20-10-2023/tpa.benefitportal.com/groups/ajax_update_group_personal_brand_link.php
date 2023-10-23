<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$group_id = $_POST['group_id'];
$group_id_ = $_POST['group_id_'];


$group_update_activity = array();

$display_in_member = !empty($_POST['display_in_member']) ? 'Y' : 'N';
$is_branding = !empty($_POST['is_branding']) ? $_POST['is_branding'] : 'N';
$username = !empty($_POST['username']) ? $_POST['username'] : '';
$admin_name = !empty($_POST['admin_name']) ? $_POST['admin_name'] : '';
$admin_email = checkIsset($_POST['admin_email']);
$admin_phone = !empty($_POST['admin_phone']) ? $_POST['admin_phone'] : '';
$admin_phone = phoneReplaceMain($admin_phone);


$validate->string(array('required' => true, 'field' => 'admin_name', 'value' => $admin_name), array('required' => 'Name is required'));
$validate->email(array('required' => true, 'field' => 'admin_email', 'value' => $admin_email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));
$validate->digit(array('required' => true, 'field' => 'admin_phone', 'value' => $admin_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

// $validate->regex(array('required' => true, 'pattern' => '/^[A-Za-z0-9]+$/', 'field' => 'username', 'value' => $username, 'min' => 4, 'max' => 20), array('required' => 'Username is required', 'invalid' => 'Valid Username is required'));

// if (!$validate->getError('username')) {
//     if (!isValidUserName($username, $group_id_)) {
//         $validate->setError("username", "Username already exist");
//     }
// }


$new_update_details =array(
    'public_name' => $admin_name,
    'public_email' => $admin_email,
    'public_phone' => $admin_phone,
    'user_name' => $username,
    'display_in_member'=>$display_in_member,
    'is_branding'=>$is_branding,
);

if($validate->isValid()){

    $upd_params = array(
        'public_name' => $admin_name,
        'public_email' => $admin_email,
        'public_phone' => $admin_phone,
        'user_name' => $username,
    );

    $c_upd_where = array(
        'clause' => 'md5(id)=:id',
        'params' => array(
            ':id' => $group_id,
        ),
    );

    $upd_cs_param = array(
        'display_in_member'=>$display_in_member,
        'is_branding'=>$is_branding,
    );

    $cs_upd_where = array(
        'clause' => 'md5(customer_id) = :id',
        'params' => array(
            ':id' => $group_id,
        ),
    );

    $group_update_activity['customer'] = $pdo->update('customer',$upd_params,$c_upd_where,true);
    $group_update_activity['customer_settings'] = $pdo->update('customer_settings',$upd_cs_param,$cs_upd_where,true);
    
    $response['status'] = "success";   
}
$description = array();
if(!empty($group_update_activity)){

    group_profile_activity($group_update_activity);
}
function group_profile_activity($group_update_activity){
    global $pdo,$group_id,$ADMIN_HOST,$new_update_details,$password;
    $flg = "true";
    $group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$group_id));
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
            'title'=> $group_name['rep_id'],
        ),
        'ac_message_1' =>' Updated Profile',
        );
    foreach($group_update_activity as $key => $value){
        if(!empty($value) && is_array($value)){
            foreach($value as $key2 => $val){
                if(array_key_exists($key2,$new_update_details)){
                        if(in_array($val,array('Y','N'))){
                            $val = $val == 'Y' ? "selected" : "unselected";
                        }
                        $description['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
                        $flg = "false";
                }else{
                    $description['description2'][] = ucwords(str_replace('_',' ',$val));
                    $flg = "false";
                }
            }    
        }else{
            if(is_array($value) && !empty($value)){
                $description['description'.$key][] = implode('',$value);
                $flg = "false";
            }else if(!empty($value)){
                $description['description'.$key][] = $value;
                $flg = "false";
            }
        }
        
    }
    if($flg == "true"){
        $description['description_novalue'] = 'No updates in group profile page.';
    }
    
    $desc=json_encode($description);
    activity_feed(3,$group_name['id'], 'Group' , $group_name['id'], 'Group', 'Group Profile Updated',$group_name['name'],"",$desc);
}
if(count($validate->getErrors()) > 0){
    $response['status'] = "errors";   
    $response['errors'] = $validate->getErrors();   
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();

?>
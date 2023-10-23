<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new Validation();
$agent_id = $_SESSION['agents']['id'];
$extra = [];

$REAL_IP_ADDRESS = get_real_ipaddress();
if (!empty($_REQUEST['operation']) && $_REQUEST['operation'] == 'edit_note') {
    $note_id = $_POST['note_id'];
    $description = $_POST['note_text'];
    $note_file = checkIsset($_FILES['note_file'],'arr');
    $delete_file = $_POST['delete_file_value'];
    $old_file_name = $_POST['note_file_name_text'];
    $note_sql = "SELECT * FROM note WHERE  id = :id";
    $note_row = $pdo->selectOne($note_sql, array(':id' => $note_id));
    $user_type = $note_row['user_type'];

    $c_user_id = '';
    if($note_row['user_type'] == 'Admin'){
        $company_id = 3;
        $c_user_id = $note_row['admin_id'];

    } else if($note_row['user_type'] == 'Lead'){
        $company_id = 3;
        $c_user_id = $note_row['lead_id'];
    } else {
        $customer_sql = "SELECT display_id,rep_id,type,company_id,fname,lname FROM customer WHERE  id = :id and is_deleted='N'";
        $customer_row = $pdo->selectOne($customer_sql, array(':id' => $note_row['customer_id']));
        $company_id = $customer_row['company_id'];

        if($note_row['user_type'] == 'Agent') {
            $c_user_id = $note_row['agent_id'];
        } else {
            $c_user_id = $note_row['customer_id'];    
        }
        
    }


    $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));
    $res = array();
    if (checkIsset($note_file['name']) != '') {
        $upload_path = $SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
        $file_name_upload = time() . $note_file['name'];
        $image_status = remote_move_uploaded_file($upload_path, $note_file, $file_name_upload, 3, "");
    }


    if ($validate->isValid()) {


        $name = array();
        if($user_type == 'Admin'){
            $name = $pdo->selectOne("SELECT id,fname,lname,display_id as rep_id from admin where id=:id",array(":id"=>$c_user_id));
        } else if($user_type == 'Lead'){

            $customer_sql = "SELECT *,lead_id as rep_id FROM leads WHERE  id=:id and is_deleted='N'";
            $name = $pdo->selectOne($customer_sql, array(':id' => $c_user_id));  
        } else {
            $customer_sql = "SELECT display_id,rep_id,type,company_id,fname,lname,id FROM customer WHERE  id = :id and is_deleted='N'";
            $name = $pdo->selectOne($customer_sql, array(':id' => $c_user_id));
        }

        $url = '';        
        if($user_type == 'Lead') {
            $url = "lead_details.php";

        } elseif($user_type == 'Admin') {
            $url = "admin_profile.php";
        
        } elseif($user_type == 'Customer') {
            $url = "members_details.php";
        
        } elseif($user_type == 'Agent') {
            $url = "agent_detail_v1.php";

        } elseif($user_type == 'Group') {
            $url = "groups_details.php";
        }

        $ac_description['ac_message'] = array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
              'title'=>$_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>'  Updated note on '.$name['fname'].$name['lname'].' (',
            'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/'.$url.'?id='.md5($name['id']),
              'title'=> $name['rep_id'],
            ),
            'ac_message_2' =>') : ',
        );

        //$ac_description['description'] = $_SESSION['agents']['rep_id'].' updated note on '. $name['fname'].$name['lname'].' ('.$name['rep_id'].')' ;

        $ac_description['description1'] = "Description : From ".$note_row['description'].' To '.$description;
        $update_params = array(
            'description' => addslashes($description),
        );

        if ($delete_file == 'Y') {
            if (checkIsset($file_name_upload) != '') {
                $update_params['file_name'] = $file_name_upload;
                $unlink_path = $SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
                $path = $unlink_path;
                $res_curl = remote_unlink_uploaded_file($path, $note_row['file_name'], 3);
            } else {
                $update_params['file_name'] = null;
                $unlink_path = $SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
                $path = $unlink_path;
                $res_curl = remote_unlink_uploaded_file($path, $note_row['file_name'], 3);
            }
            $ac_description['delete_file'] ='File deleted.';

        } else {
            if (checkIsset($file_name_upload) != '') {
                $update_params['file_name'] = $file_name_upload;
                $unlink_path = $SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
                $path = $unlink_path;
                $res_curl = remote_unlink_uploaded_file($path, $note_row['file_name'], 3);
                $ac_description['delete_file'] ='File updated.';
            } else {
                $update_params['file_name'] = $old_file_name;
            }
            
        }

        $edit_note_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($note_id)
            )
        );

        $pdo->update("note", $update_params, $edit_note_where);

        $res['status'] = 'success';
        $res['user_type'] = $user_type;
        // setNotifySuccess('Note updated successfully.');

        activity_feed(3,$_SESSION['agents']['id'],'Agent',$c_user_id,$user_type,'Note Updated',$_SESSION['agents']['fname'], $_SESSION['agents']['lname'], json_encode($ac_description));
    } else {
        $res['status'] = 'fail';
        $res['errors'] = $validate->getErrors();
    }

    if (!empty($res['errors'])) {
        $res['msg'] = 'Description is required';
    }

    header('Content-Type: application/json');
    echo json_encode($res);
    exit;
}

if (!empty($_REQUEST['operation']) && $_REQUEST['operation'] == 'add_note') {
    $company_id = 3;
    $user_id = $_REQUEST['customer_id'];
    $user_type = $_POST['user_type'];
    $user_row = '';
    if($user_type == 'Lead'){
        $selLead = "SELECT *,lead_id as rep_id FROM leads WHERE md5(id)=:id";
        $whrlead = array(":id"=> $user_id);
        $user_row = $pdo->selectOne($selLead,$whrlead);
    } else {
        $company_id = 3;
        $customer_sql = "SELECT display_id,rep_id,type,company_id,fname,lname,id FROM customer WHERE md5(id)=:id and is_deleted='N'";
        $user_row = $pdo->selectOne($customer_sql, array(':id' => $user_id));
        $user_type = $user_row['type'];
    }

    $res = array();
    $description = $_POST['note_text'];
    $note_file = checkIsset($_FILES['note_file'],'arr');
    $quick_note_id = checkIsset($_POST['quick_note_id']);
    $set_quick_note = isset($_POST['set_quick_note']) ? 'Y' : 'N';
    $reply_id = isset($_POST['reply_id']) ? $_POST['reply_id'] : '';
    $from_page = isset($_POST['from_page']) ? $_POST['from_page'] : '';

    $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));

    if ($validate->isValid() && $agent_id != '') {
        $admin_id = 0;
        $file_name = "";
        
        if (count($note_file) > 0) {
            $upload_path = $SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
            $file_name = time() . $note_file['name'];
            $image_status = remote_move_uploaded_file($upload_path, $note_file, $file_name, 3, "");
        }
        $add_note_params = array(
            'agent_id' => makeSafe($agent_id),
            'description' => addslashes($description),
            'user_type' => $user_type,
            'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? makeSafe($REAL_IP_ADDRESS['original_ip_address']):makesafe($REAL_IP_ADDRESS['ip_address']),
            'created_at' => 'msqlfunc_NOW()'
        );

        if($user_type == "Agent" || $user_type == "Group") {
            $add_note_params['customer_id'] = $user_row['id'];
        }

        if($user_type == "Customer" || $user_type == "Admin") {
            $add_note_params['customer_id'] = $user_row['id'];
        }

        if($user_type == "Lead") {
            $add_note_params['lead_id'] = $user_row['id'];
        }

        if ($file_name != "") {
            $add_note_params['file_name'] = $file_name;
        }

        if($set_quick_note == 'Y'){
            $add_note_params['is_quick_note'] = $set_quick_note;
        }

        if($reply_id != ''){
            $add_note_params['note_type'] = 'Reply';
            $add_note_params['reply_id'] = $reply_id;
        }

        $note_id = $pdo->insert('note', $add_note_params);
        
        /* Code for activity feed*/
        if($reply_id == ''){
            $extra['user_fname'] = $_SESSION['agents']['fname'];
            $extra['user_lname'] = $_SESSION['agents']['lname'];
            $extra['user_display_id'] = $_SESSION['agents']['rep_id'];
            $extra['user_photo'] = $_SESSION['agents']['rep_id'];
            $extra['user_type'] = 'Agent';
            $extra['user_id'] = $agent_id;
            $extra['en_file_name'] = checkIsset($file_name);

            $url = '';        
            if($user_type == 'Lead') {
                $url = "lead_details.php";

            } elseif($user_type == 'Admin') {
                $url = "admin_profile.php";
            
            } elseif($user_type == 'Customer') {
                $url = "members_details.php";
            
            } elseif($user_type == 'Agent') {
                $url = "agent_detail_v1.php";

            } elseif($user_type == 'Group') {
                $url = "groups_details.php";
            }

            $extra['en_id'] = $user_row['id'];
            $extra['en_fname'] = $user_row['fname'];
            $extra['en_lname'] = $user_row['lname'];
            $extra['en_display_id'] = $user_row['rep_id'];
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                  'title'=>$_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' =>'  created note on '.$user_row['fname'].$user_row['lname'].' (',
                'ac_red_2'=>array(
                  'href'=> $ADMIN_HOST.'/'.$url.'?id='.md5($user_row['id']),
                  'title'=> $user_row['rep_id'],
                ),
                'ac_message_2' =>') : ',
            );
            $ac_description['note_description'] = addslashes($description);

            activity_feed(3,$user_row['id'],$user_type,$note_id, 'note', 'Note Created', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], json_encode($ac_description),'',json_encode($extra));

            activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'customer', 'Note Created', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], json_encode($ac_description));
        }
        /* End Code for activity feed*/

        $res['status'] = 'success';
        $res['from_page'] = $from_page;
        $res['user_type'] = $user_type;
        if($reply_id == ''){
        //    setNotifySuccess('Note saved successfully.');
        } else {
           setNotifySuccess('Reply of Note saved successfully.');
        }

    } else {
        $res['status'] = 'fail';
        $res['errors'] = $validate->getErrors();
    }

    if (!empty($res['errors'])) {
        $res['msg'] = 'Description is required';
    }

    header('Content-Type: application/json');
    echo json_encode($res);
    dbConnectionClose();
    exit;
}

function remote_unlink_uploaded_file($path, $tmp_image, $company_id)
{

    global $SITE_SETTINGS;
    $site_url = $SITE_SETTINGS[$company_id]['HOST'];

    $curl_handle = curl_init($site_url . "/remote_scripts/delete.php");
    curl_setopt($curl_handle, CURLOPT_POST, 1);
    $args['path'] = $path;
    $args['file'] = $tmp_image;
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $args);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);

    //execute the API Call
    $returned_data = curl_exec($curl_handle);
    if (curl_errno($curl_handle)) {
        echo $msg = curl_error($curl_handle);
        exit;
    }
    curl_close($curl_handle);
    return json_decode($returned_data, true);
}
?>
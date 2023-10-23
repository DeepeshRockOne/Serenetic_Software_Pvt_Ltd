<?php

include_once __DIR__ . '/includes/connect.php';
//include_once 'includes/SimpleImage.php';
$validate = new Validation();
$id = $_GET['id'];
$res = array("url" => "");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profile = $_POST['profile_picture'];
    if (!empty($profile)) {
        try {
            if (strpos($profile, 'image/jpeg') === false  && strpos($profile, 'image/jpg') === false && strpos($profile, 'image/png') === false && strpos($profile, 'image/gif') === false) {
                $validate->setError('image', 'Only .jpg, .png, .gif file format allow');
            }
        } catch (Exception $e) {
            $validate->setError('image', 'Please select valid image for Profile');
        }        
    }

    if ($validate->isValid()) {
        if (!empty($profile)) {
            $image_name = "B" . mt_rand() . time() . '.png';
            $base_64_data = str_replace(
                array('image/jpg','image/jpeg', 'image/png', 'image/gif', 'data:', 'base64,'), array('','', '', '', '', ''), $profile
            );            
            //$res = remote_file_upload_all($PROFILE
            //_DIR, $base_64_data, $image_name, $old_image = '');
            $id = getname('customer', $id, 'id', 'md5(id)');
            $oldFile = getname('customer_settings', $id, 'brand_icon', 'customer_id');

            $res = saveImage($base_64_data, $AGENTS_BRAND_ICON, $image_name, $oldFile);
            //upload file using curl
            if ($res == "fail") {
                $validate->setError('image', 'Error on image upload please try again');
            } else {
                $params = array();
                $params['brand_icon'] = $image_name;
                $where = array(
                    'clause' => 'customer_id=:id',
                    'params' => array(':id' => makesafe($id)),
                );

                $pdo->update('customer_settings', $params, $where);

                $o_data = $pdo->selectOne('SELECT fname,lname,rep_id,type from customer where id = :id',array(":id"=>$id));

                $res['status'] = 'success';
                $res['message'] = 'Brand logo added successfully';

                $res['url'] = $AGENTS_BRAND_ICON . $image_name;
                setNotifySuccess('Profile picture added successfully');
                
                $detail_page = '';
                if($o_data['type'] == "Agent") {
                    $detail_page = 'agent_detail_v1.php';
                } else if($o_data['type'] == "Group") {
                    $detail_page = 'groups_details.php';
                }

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1'=>array(
                        'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=> $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Updated Brand Logo For Agent '.$o_data['fname'].' '.$o_data['lname'].' ',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/'.$detail_page.'?id='.md5($id),
                        'title'=> $o_data['rep_id'],
                    ),
                );
                $desc=json_encode($desc);
                activity_feed(3,$_SESSION['admin']['id'],'Admin',$id,$o_data['type'],$o_data['type'].' Profile Updated',"","",$desc);
            }
        }
    }
}
if (!empty($res['error']) && count($res['error']) > 0) {
    $res['error'] = $validate->getError('image');
    $res['status'] = 'fail';
}
header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
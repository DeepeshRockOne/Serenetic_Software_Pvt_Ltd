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
            $oldFile = $o_fname = getname('customer_settings', $id, 'brand_icon', 'customer_id');

            $res = saveImage($base_64_data, $GROUPS_BRAND_ICON_DIR, $image_name, $oldFile);
            //upload file using curl
            if ($res == "fail") {
                $validate->setError('image', 'Error on image upload please try again');
            } else {
                $params = array();
                $params['brand_icon'] = $image_name;
                // $params['updated_at'] = date('Y-m-d H:i:s', time());
                $where = array(
                    'clause' => 'customer_id=:id',
                    'params' => array(':id' => makesafe($id)),
                );

                $pdo->update('customer_settings', $params, $where);

                
                $o_data = $pdo->selectOne('SELECT fname,lname,email,rep_id from customer where id = :id',array(":id"=>$id));

                $res['status'] = 'success';
                $res['message'] = 'Brand logo added successfully';

                $res['url'] = $GROUPS_BRAND_ICON_DIR . $image_name;

                
                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Updated Profile <br/>',
                    'ac_message_2' =>' Brand Logo Updated'
                );
                $desc=json_encode($desc);
                activity_feed(3,$id,'Group',$id,'customer','Group Profile Updated',"","",$desc);
                
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
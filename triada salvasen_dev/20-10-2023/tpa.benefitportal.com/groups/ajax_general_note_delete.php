<?php
include_once __DIR__ . '/includes/connect.php';

$note_id = $_POST['note_id'];
$activity_feed_id = $_POST['activity_feed_id'];
$usertype = checkIsset($_POST['usertype']);
$user_id = checkIsset($_POST['user_id']);

function remote_unlink_uploaded_file($path, $tmp_image,$company_id) {
            
  global $SITE_SETTINGS;
  $site_url = $SITE_SETTINGS[$company_id]['HOST'];

  $curl_handle = curl_init($site_url . "/remote_scripts/delete.php");
  curl_setopt($curl_handle, CURLOPT_POST, 1);
  $args['path'] = $path;
  //$args['file'] = new CurlFile($tmp_image);
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

$sql = "SELECT * FROM note WHERE id = :note_id AND is_deleted = 'N'";
$params = array(
  ':note_id' => makeSafe($note_id)
  );
$selSql = $pdo->selectOne($sql,$params);


$activity_sql = "SELECT company_id FROM activity_feed WHERE id = :activity_feed_id";
$activity_params = array(
  ':activity_feed_id' => makeSafe($activity_feed_id)
  );
$activity_selSql = $pdo->selectOne($activity_sql, $activity_params);

if ((count($selSql) > 0) && (count($activity_selSql) > 0)) {

  $company_id = $activity_selSql['company_id'];

  $upd_params = array(
    'is_deleted' => 'Y'
  ); 

  if($selSql['file_name'] != ""){
    $unlink_path=$SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
    $path = $unlink_path;
    $res_curl = remote_unlink_uploaded_file($path,$selSql['file_name'],$company_id);
  }

  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($note_id)
    )
  );
  $pdo->update("note", $upd_params, $update_where);

  $reply_note = $pdo->select("SELECT * FROM note WHERE reply_id = :id", array(':id' => $note_id));

  if(count($reply_note) > 0){

    foreach ($reply_note as $value) {

      $replay_upd_params = array(
        'is_deleted' => 'Y'
      ); 

      if($value['file_name'] != ""){
        $unlink_path=$SITE_SETTINGS[$company_id]['NOTE_FILES']['upload'];
        $path = $unlink_path;
        $res_curl = remote_unlink_uploaded_file($path,$value['file_name'],$company_id);
      }

      $replay_update_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => makeSafe($value['id'])
        )
      );

      $pdo->update("note", $replay_upd_params, $replay_update_where);
    }
  }

  // $activity_upd_params = array(
  //   'is_deleted' => 'Y'
  // ); 

  // $activity_update_where = array(
  //   'clause' => 'id = :id AND entity_type = :entity_type',
  //   'params' => array(
  //     ':id' => makeSafe($activity_feed_id),
  //     ':entity_type' => 'note'
  //   )
  // );

  // $pdo->update("activity_feed", $activity_upd_params, $activity_update_where);


  $res['status'] = 'success';
  $res['msg'] = 'Note deleted successfully.';

  if(!empty($user_id) && !empty($usertype)){
      $table ='';
      $rep_id = ',rep_id ';
      $url = '';
      // if($usertype == 'Admin'){
      //   $table ='admin';
      //   $rep_id =' ,display_id as rep_id';
      //   $url="admin_profile.php?id=".$user_id;
      // } else
       if($usertype == 'Customer'){
        $table =' customer ';
        $url="members_details.php?id=".$user_id;

      } else if($usertype == 'Lead'){
        $rep_id =' ,lead_id as rep_id';
        $table =' leads ';
        $url="lead_details.php?id=".$user_id;
      }
      $username = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,id ".$rep_id." from ".$table." where md5(id)=:id ",array(":id"=>$user_id));
      $description = array();
      $description['ac_message'] = array(
          'ac_red_1' => array(
              'href' => $ADMIN_HOST.'/groups_details.php?id=' . md5($_SESSION['groups']['id']),
              'title' => $_SESSION['groups']['rep_id'],
          ),
          'ac_message_1' =>'  Deleted Note on '.$username['name'].' (',
          'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/'.$url,
                'title'=> $username['rep_id'],
          ),
          'ac_message_2' => ')',
      );    
      $desc=json_encode($description);
      activity_feed(3,$_SESSION['groups']['id'],'Group', $username['id'],  $usertype, 'Note Deleted.',$_SESSION['groups']['fname'],"",$desc);
  }
} else {
  $res['status'] = 'fail';
  $res['msg'] = "Could not delete note";
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
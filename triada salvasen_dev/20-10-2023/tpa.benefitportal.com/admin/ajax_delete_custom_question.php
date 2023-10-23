<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$question = $_POST["question"];
$res = array();

$query = "SELECT id,display_label FROM prd_enrollment_questions WHERE md5(id) = :id";
$srow = $pdo->selectOne($query,array(":id"=>$question));

if (!empty($srow)) {
  $update_params = array(
      'is_deleted' => 'Y',
  );
  $update_where = array(
      'clause' => 'md5(id) = :delete_id',
      'params' => array(
          ':delete_id' => makeSafe($question)
      )
  );
  $pdo->update("prd_enrollment_questions", $update_params, $update_where);

  $update_where = array(
      'clause' => 'md5(prd_question_id) = :delete_id',
      'params' => array(
          ':delete_id' => makeSafe($question)
      )
  );
  $pdo->update("prd_enrollment_answers", $update_params, $update_where);
       
  $res['status'] = 'success';

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' Deleted Custom Question ',
    'ac_red_2'=>array(
        'title'=>$srow['display_label'],
    ),
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'prd_enrollment_questions','Deleted Custom Question', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
} else {
  $res['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

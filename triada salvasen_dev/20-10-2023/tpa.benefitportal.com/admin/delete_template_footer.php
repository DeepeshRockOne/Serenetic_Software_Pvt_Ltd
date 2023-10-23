<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$footer_id = $_GET['footer_id'];
$validate = new Validation();

if (isset($_POST['is_ajax'])) {
  $footer_id = $_POST['footer_id'];
  $res = array();
  $t_query = "SELECT ttm.id,tt.title,tt.id as template_id FROM trigger_footer ttm JOIN trigger_template tt ON(tt.trg_footer_id=ttm.id) WHERE ttm.id=:footer_id";
  $t_where = array(':footer_id' => $footer_id);
  $t_res = $pdo->select($t_query, $t_where);
  if (count($t_res) == 0) {
    $u_params = array(
        'is_deleted' => 'Y',
    );
    $u_where = array(
        'clause' => 'id=:footer_id',
        'params' => array(':footer_id' => $footer_id)
    );
    $pdo->update('trigger_footer', $u_params, $u_where);
    $res['status'] = 'success';
    setNotifySuccess('Trigger footer deleted successfully.');
  } else {
    $res['status'] = 'fail';
  }

  header('Content-Type:appliaction/json');
  echo json_encode($res);
  exit;
}

if ($footer_id != '') {
//
  $i_query = "SELECT id FROM trigger_footer WHERE id=:footer_id";
  $i_where = array(':footer_id' => $footer_id);
  $i_res = $pdo->selectOne($i_query, $i_where);

  if ($i_res) {
    $t_query = "SELECT ttm.id,tt.title,tt.id as template_id FROM trigger_footer ttm JOIN trigger_template tt ON(tt.trg_footer_id=ttm.id) WHERE ttm.id=:footer_id";
    $t_where = array(':footer_id' => $footer_id);
    $t_res = $pdo->select($t_query, $t_where);

    if (count($t_res) == 0) {
      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:footer_id',
          'params' => array(':footer_id' => $o_footer_id)
      );
      $pdo->update('trigger_footer', $u_params, $u_where);
      setNotifySuccess('Trigger footer deleted successfully.');
      redirect('trigger_footer.php', 1);
    }
  }
  $img_query = "SELECT id,title FROM trigger_footer WHERE is_deleted='N' AND id!=:footer_id";
  $img_w = array(':footer_id' => $footer_id);
  $img_res = $pdo->select($img_query,$img_w);
}
if (isset($_POST['change'])) {
  $o_footer_id = $_POST['footer_id'];
  $footer_replace = $_POST['footer_replace'];
  $validate->string(array('required' => true, 'value' => $footer_replace, 'field' => 'footer_replace'), array('required' => 'Please select footer'));

  if ($validate->isValid()) {
    $query = "SELECT id FROM trigger_footer WHERE id=:id";
    $where = array(':id' => $footer_replace);
    $u_res = $pdo->selectOne($query, $where);
    if ($u_res) {
      $u_params = array(
          'trg_footer_id' => $u_res['id']
      );
      $u_where = array(
          'clause' => 'trg_footer_id=:footer_id',
          'params' => array(':footer_id' => $o_footer_id)
      );
      $pdo->update('trigger_template', $u_params, $u_where);

      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:footer_id',
          'params' => array(':footer_id' => $o_footer_id)
      );
      $pdo->update('trigger_footer', $u_params, $u_where);
      setNotifySuccess('Trigger footer deleted successfully.');
      redirect('trigger_footer.php', 1);
    }
  }
}
$errors = $validate->getErrors();
$template = "delete_template_footer.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
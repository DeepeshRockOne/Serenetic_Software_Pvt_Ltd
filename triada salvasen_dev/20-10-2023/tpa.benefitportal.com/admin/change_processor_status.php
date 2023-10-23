<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$processor_id = $_GET['id'];
$status = !empty($_GET['status']) ? $_GET['status'] : '';
$is_status = $_GET['is_status'];
$res = array();

$query = "SELECT * FROM payment_master WHERE md5(id) = :id AND is_deleted='N'";
$srow = $pdo->selectOne($query, array(":id" => $processor_id));

if ($srow) {
  $function_list = new functionsList();
    if($is_status == 'Y'){
        $update_params = array(
            'status' => makeSafe($status),
            'updated_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($srow['id'])
            )
        );

        $pdo->update("payment_master", $update_params, $update_where);

        if($status=='Closed'){
          $update_where_assigned = array(
            'clause' => 'payment_master_id = :id',
            'params' => array(
                ':id' => makeSafe($srow['id'])
            )
          );
          $pdo->update("payment_master_assigned_product", array('is_deleted' => 'Y','updated_at' => 'msqlfunc_NOW()'), $update_where_assigned);
        }

        // Database detials start
        $databaseFields = $srow;
        if(!empty($databaseFields['live_details'])){
            $databaseFields_live_details_arr = json_decode($databaseFields['live_details'], true);
            if(!empty($databaseFields_live_details_arr)){
              foreach ($databaseFields_live_details_arr as $key => $value) {
                $databaseFields["live_".$key."_details"] = $value;
              }
              unset($databaseFields['live_details']);
            }
        }
        if(!empty($databaseFields['sandbox_details'])){
            $databaseFields_sandbox_details_arr = json_decode($databaseFields['sandbox_details'], true);
            if(!empty($databaseFields_sandbox_details_arr)){
              foreach ($databaseFields_sandbox_details_arr as $key => $value) {
                $databaseFields["sandbox_".$key."_details"] = $value;
              }
              unset($databaseFields['sandbox_details']);
            }
        }
        // Database detials end
        $function_list->get_updated_payment_field($srow['id'], 'N', $update_params, $databaseFields);

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' updated Merchant Processor ',
          'ac_red_2'=>array(
              'href'=>$ADMIN_HOST.'/add_merchant_processor.php?type='.$srow['type'].'&id='.md5($srow['id']),
              'title'=>' ('.$srow['name'].')',
          ),
        );
  
        $description['description'] = 'Updated Status from '.$srow['status'].' to '.$update_params['status'];
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'Admin','Merchant Processor Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    } else {
        $update_params = array(
            'is_deleted' => 'Y',
            'updated_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($srow['id'])
            )
        );
        $update_where_assigned = array(
          'clause' => 'payment_master_id = :id',
          'params' => array(
              ':id' => makeSafe($srow['id'])
          )
      );
        $pdo->update("payment_master_assigned_product", $update_params, $update_where_assigned);
        $pdo->update("payment_master_assigned_agent", $update_params, $update_where_assigned);
        $pdo->update("payment_master", $update_params, $update_where);
        $databaseFields = $srow;
        if(!empty($databaseFields['live_details'])){
            $databaseFields_live_details_arr = json_decode($databaseFields['live_details'], true);
            if(!empty($databaseFields_live_details_arr)){
              foreach ($databaseFields_live_details_arr as $key => $value) {
                $databaseFields["live_".$key."_details"] = $value;
              }
              unset($databaseFields['live_details']);
            }
        }
        if(!empty($databaseFields['sandbox_details'])){
            $databaseFields_sandbox_details_arr = json_decode($databaseFields['sandbox_details'], true);
            if(!empty($databaseFields_sandbox_details_arr)){
              foreach ($databaseFields_sandbox_details_arr as $key => $value) {
                $databaseFields["sandbox_".$key."_details"] = $value;
              }
              unset($databaseFields['sandbox_details']);
            }
        }

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' deleted Merchant Processor '.$srow['name'],
          'ac_red_2'=>array(
              'title'=>' ('.$srow['name'].')',
          ),
        );
  
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'Admin','Delete Merchant Processor', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        // Database detials end
        $function_list->get_updated_payment_field($srow['id'], 'N', $update_params, $databaseFields);
    }
    
    $res['status'] = 'success';
    $res['msg'] = 'Status Changed Successfully';
} else {
    setNotifyError('Something went wrong');
    $res['status'] = 'error';
    $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>


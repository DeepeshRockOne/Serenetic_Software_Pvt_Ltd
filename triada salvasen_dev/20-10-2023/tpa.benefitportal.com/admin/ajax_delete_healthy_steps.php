<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$product_ids = checkIsset($_POST['product_ids'],'arr');
$health_ids = checkIsset($_POST['health_ids'],'arr');
$agent_ids = checkIsset($_POST['agent_ids'],'arr');
$product_id  = checkIsset($_POST['product_id']);

if(!empty($product_ids)){

    $query = "SELECT name,product_code FROM prd_main WHERE id = :id and is_deleted='N'";
    $srow = $pdo->selectOne($query, array(":id"=>$product_id));

    foreach($product_ids as $key => $id){
            $update_params = array("is_deleted"=>'Y');

            $pdo->update("prd_main",$update_params,array("clause"=>"id=:id","params"=>array(":id"=>$product_ids[$id])));
            $pdo->update("prd_matrix",$update_params,array("clause"=>"product_id=:id","params"=>array(":id"=>$product_ids[$id])));
        
            $where = array(
                "clause"=>"fee_id=:id and prd_fee_id=:prd_fee_id and is_deleted='N'",
                "params"=>array(":id"=>$product_ids[$id],":prd_fee_id"=>$health_ids[$id])
            );
        
            $pdo->update("prd_assign_fees",$update_params,$where);
            $pdo->update("prd_member_portal_information",$update_params,array("clause"=>"product_id=:id","params"=>array(":id"=>$product_ids[$id])));

            $pdo->update("agent_product_rule",$update_params,array("clause"=>"product_id=:id","params"=>array(":id"=>$product_ids[$id])));

            $upd_where_state = array(
                "clause" => 'healthy_steps_fee_id=:healthy_steps_fee_id',
                "params" => array(":healthy_steps_fee_id"=>$product_ids[$id])
            );
            $pdo->update("healthy_steps_states",$update_params,$upd_where_state);

            $count_variations = $pdo->selectOne("SELECT COUNT(DISTINCT(p.id)) AS total_fees
            FROM agent_product_rule apr
            JOIN prd_main p ON(p.id=apr.product_id AND p.type='Fees'
            AND p.product_type='Healthy Step' AND p.record_type='Variation' AND p.is_deleted='N') WHERE apr.agent_id=:agent_id and apr.is_deleted='N'",array(":agent_id"=>checkIsset($agent_ids[$id])));

            if(empty($count_variations) || $count_variations['total_fees'] == 0){
                $pdo->update("prd_fees",$update_params,array("clause"=>"id=:id","params"=>array(":id"=>$health_ids[$id]))); 
            }
    }
    $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' deleted Healthy Steps '.$srow['name'],
        'ac_red_2'=>array(
            // 'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
            'title'=>' ('.$srow['product_code'].')',
        ),
      );
  
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'prd_fees','Deleted Healthy Step', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    setNotifySuccess("Healthy Step Deleted Successfully!",true);
    echo json_encode(array("status"=>"success"));
    dbConnectionClose();
    exit;
}else{
    setNotifyError("Record not found!",true);
    echo json_encode(array("status"=>"success"));
    exit;
}
?>
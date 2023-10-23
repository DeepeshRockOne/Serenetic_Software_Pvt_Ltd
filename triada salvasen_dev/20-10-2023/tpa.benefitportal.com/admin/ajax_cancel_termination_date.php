<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$response = array();
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));

if(!empty($ws_row)){
    $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
    $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));

    $customer_id = $ws_row['customer_id'];
    $product_id = $ws_row['product_id'];
    $plan_id = $ws_row['plan_id'];

	$upd_term_date_data = array('termination_date' => NULL,'termination_reason' => NULL,"term_date_set" => NULL,'status' => 'Active');
    $upd_term_date_where = array(
        "clause" => "id=:id",
        "params" => array(":id" => $ws_row['id'])
    );

    $pdo->update("website_subscriptions", $upd_term_date_data, $upd_term_date_where);

    $upd_customer_data = array('status' => 'Active');
    $upd_customer_where = array(
        "clause" => "id=:id",
        "params" => array(":id" => $ws_row['customer_id'])
    );

    $pdo->update("customer", $upd_customer_data, $upd_customer_where);
    

    $upd_cd_term_date_data = array('process_status' => 'Active');
    $upd_cd_term_date_where = array(
        "clause" => "website_id=:id",
        "params" => array(":id" => $ws_row['id'])
    );

    $pdo->update("customer_enrollment", $upd_cd_term_date_data, $upd_cd_term_date_where);
     /* term dependent start */
    $term_cd_data = array(
        "terminationDate" => NULL,
        "updated_at" => "msqlfunc_NOW()"
    );
    $term_cd_where = array(
        'clause' => "product_id=:product_id and customer_id=:customer_id",
        'params' => array(':customer_id' => $customer_id, ":product_id" => $ws_row['product_id'])
    );
    $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);
    /* term dependent end */
    
    $af_message = ' removed termination date';
    $af_desc = array();
    $af_desc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=> $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => $af_message.' on ',
        'ac_red_2'=>array(
            'href'=> 'members_details.php?id='.md5($customer_row['id']),
            'title'=>$customer_row['rep_id'],
        ),
        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']),
    );
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

    // setNotifySuccess("Termination date removed successfully");
    $response['status'] = "success";
    echo json_encode($response);
    dbConnectionClose();
    exit();
}
?>
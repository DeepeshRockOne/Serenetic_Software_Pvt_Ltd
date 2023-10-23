<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$member_id = isset($_GET['id']) ? $_GET['id'] : "";
$member_status = "";
$member_rep_id = "";
if(!empty($member_id)){
    $member_res = $pdo->selectOne("SELECT status,rep_id FROM customer WHERE md5(id) = :id",array(":id" => $member_id));
    if($member_res && !empty($member_res['status'])){
        $member_status = $member_res['status'];
        $member_rep_id = $member_res['rep_id'];
    }
}

if(isset($_FILES['proof_of_coverage'])){
	$coverage_file = $_FILES['proof_of_coverage'];
	$file_name = $coverage_file['name'];
	$file_tmp_name = $coverage_file['tmp_name'];
	$fileSize = $coverage_file['size'];
	$fileType = $coverage_file['type'];

	$m_id = $_POST['member_id'];
	$ce_id = $_POST['ce_id'];
	$error = "";
	$response = array();

	if(!$error){
		$file_name = time() . $file_name;
	    $add_file = $OLD_COVERAGE_DIR . $file_name;
	    move_uploaded_file($file_tmp_name, $add_file);
	    $updParams = array('old_coverage_file' => $file_name);
	    $updWhere = array(
	        "clause" => "id=:id",
	        "params" => array(":id" => $ce_id)
	    );
	    $pdo->update("customer_enrollment", $updParams, $updWhere);
    	
	    $response['status'] = 'success';
	    setNotifySuccess('Request Proceed Successfully');
    } else {
    	$response['status'] = 'fail';
    	$response['error'] = $error;
    }
    echo json_encode($response);
    exit();
}

$is_ajax_prd = isset($_GET['is_ajax_prd']) ? $_GET['is_ajax_prd'] : '';
$incr = "";
$sch_params = array();

if($member_id){
	$incr .= " AND md5(c.id) = :id";
	$sch_params[':id'] = $member_id;
}


if ($is_ajax_prd) {
    $sponsorArr = $pdo->selectOne("SELECT s.type as sponsor_type,s.id as sponsor_id from customer c JOIN customer s ON(s.id=c.sponsor_id) AND md5(c.id)=:customer_id",array(":customer_id"=>$member_id));
    $sponsor_billing_method = "individual";
    if($sponsorArr['sponsor_type'] == "Group") {
        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsorArr['sponsor_id']));
        if(!empty($resBillingType)){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }
    
	$sel_sql = "SELECT IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as agent_name, s.rep_id as agent_id,IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.product_code,w.eligibility_date,w.termination_date,w.end_coverage_period,w.website_id,w.next_purchase_date,ppt.title as benefit_tier,w.price,DATE(w.created_at) as added_date,ce.fulfillment_date,p.id as p_id,pm.id as matrix_id,w.status,ce.tier_change_date,ce.process_status,c.id as customer_id,w.id as ws_id,
            ores.future_payment as is_active_in_future,c.sponsor_id
            FROM customer c
            JOIN website_subscriptions w ON (w.customer_id=c.id)
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            JOIN customer s ON (s.id=c.sponsor_id)
            JOIN prd_main p ON (p.id=w.product_id)
            JOIN prd_matrix pm ON (pm.product_id=w.product_id AND w.plan_id = pm.id)
            LEFT JOIN prd_plan_type ppt ON (ppt.id = w.prd_plan_type_id)
            LEFT JOIN (
                SELECT o.future_payment,o.customer_id as custId, od.product_id as ordPrdId 
                FROM orders o
                JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                WHERE o.status NOT IN('Pending Validation')
                ORDER BY o.id ASC
            ) ores ON(ores.custId=w.customer_id AND ores.ordPrdId=w.product_id)
            WHERE c.type='Customer' $incr GROUP BY w.id ORDER BY p.type DESC,p.name";
    $fetch_rows = $pdo->select($sel_sql,$sch_params);

    /*---------- Product Sorting Process -------------*/
    $product_names = $pending_products = $active_pending_products = $active_products = $terminated_products = array();
    foreach ($fetch_rows as $key => $rows) {
        if (in_array($rows['status'], array('Inactive'))) {
            $terminated_products[] = $rows;
        } elseif (!empty($rows['termination_date']) && strtotime($rows['termination_date']) > 0 && strtotime($rows['termination_date']) <= strtotime(date('Y-m-d'))) {
            $terminated_products[] = $rows;
        } elseif (!empty($rows['eligibility_date']) && strtotime($rows['eligibility_date']) > 0 && strtotime($rows['eligibility_date']) > strtotime(date('Y-m-d'))) {
            $pending_products[] = $rows;
        } elseif ($rows['is_active_in_future'] == 'Y') {
            $pending_products[] = $rows;
        } elseif (!empty($rows['termination_date']) && strtotime($rows['termination_date']) > 0 && strtotime($rows['termination_date']) > strtotime(date('Y-m-d'))) {
            $active_pending_products[] = $rows;
        } elseif ($rows['process_status'] == 'Pending' && !empty($rows['tier_change_date']) && strtotime($rows['tier_change_date']) > strtotime(date('Y-m-d'))) {
            $active_pending_products[] = $rows;
        } else {
            $active_products[] = $rows;
        }

        $product_names[] = $rows['name'];
    }

    $fetch_rows = array_merge($pending_products, $active_pending_products, $active_products, $terminated_products);
    array_multisort($product_names, SORT_ASC, $product_names);
    /*---------- Product Sorting Process -------------*/

	include_once 'tmpl/member_products_tab.inc.php';
	exit;
}
$template = 'member_products_tab.inc.php';
include_once 'layout/end.inc.php';
?>
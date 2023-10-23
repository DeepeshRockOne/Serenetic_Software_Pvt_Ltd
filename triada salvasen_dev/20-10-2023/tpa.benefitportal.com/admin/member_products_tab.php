<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

$function_list = new functionsList();
$validate = new Validation();
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$member_id = isset($_GET['id']) ? $_GET['id'] : "";
$member_status = "";
if(!empty($member_id)){
    $member_res = $pdo->selectOne("SELECT status FROM customer WHERE md5(id) = :id",array(":id" => $member_id));
    if($member_res && !empty($member_res['status'])){
        $member_status = $member_res['status'];
    }
}

$member_document = checkIsset($_FILES['member_document'],'arr');

if(isset($_FILES['proof_of_coverage']) || $member_document){
	$coverage_file = $_FILES['proof_of_coverage'];
	$file_name = $coverage_file['name'];
	$file_tmp_name = $coverage_file['tmp_name'];
	$fileSize = $coverage_file['size'];
	$fileType = $coverage_file['type'];

	$m_id = $_POST['member_id'];
	$ce_id = $_POST['ce_id'];
	$error = "";
	$response = array();

    $location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
    $memberRes = $pdo->selectOne("SELECT id,rep_id FROM customer WHERE md5(id) = :id",array(":id" => $m_id));
    $rep_id = $memberRes['rep_id'];
    $customerId = $memberRes['id'];
    $policy_id = $_POST['website_id'];

    if(!$error && !empty($file_name)){
		$file_name = time() . $file_name;
	    $add_file = $OLD_COVERAGE_DIR . $file_name;
	    move_uploaded_file($file_tmp_name, $add_file);
	    $updParams = array('old_coverage_file' => $file_name);
	    $updWhere = array(
	        "clause" => "id=:id",
	        "params" => array(":id" => $ce_id)
	    );
	    $pdo->update("customer_enrollment", $updParams, $updWhere);

    	$af_message = ' Updated Proof of Prior Coverage';
	    $af_desc = array();
    	if($location == "admin") {
	        $af_desc['ac_message'] =array(
	            'ac_red_1'=>array(
	                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
	                'title'=> $_SESSION['admin']['display_id'],
	            ),
	            'ac_message_1' => $af_message.' on ',
	            'ac_red_2'=>array(
	                'href'=> 'members_details.php?id='.md5($customerId),
	                'title'=>$rep_id,
	            ),
	            'ac_message_2' =>' <br/> Plan : '.display_policy($policy_id),
	        );
	        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customerId, 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
	    } elseif($location == "agent") {
	        
	        $af_desc['ac_message'] =array(
	            'ac_red_1'=>array(
	                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	                'title' => $_SESSION['agents']['rep_id'],
	            ),
	            'ac_message_1' => $af_message.' on ',
	            'ac_red_2'=>array(
	                'href'=> 'members_details.php?id='.md5($customerId),
	                'title'=>$rep_id,
	            ),
	            'ac_message_2' =>' <br/> Plan : '.display_policy($policy_id),
	        );
	        activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customerId, 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
	    }
	    $response['status'] = 'success';
	    setNotifySuccess('Request Proceed Successfully');
    } else {
    	$response['status'] = 'fail';
    	$response['error'] = $error;
    }
    
    if(!empty($member_document)){
        $count = $_POST['count'];
        $file_nameArr = $member_document['name'];
        $error = "";
        $response = array();
        foreach($file_nameArr as $f_key=>$f_val){
            $old_document = checkIsset($_POST['old_member_document'][$f_key]);
            if(empty($old_document)){
                $import_ext ="";
                foreach($count as $key=>$value){
                    if(empty($f_val) && ($value > 0 || $f_key != 0)){
                        $validate->setError('member_document_'.$f_key,"Please select file");
                    }
                }
            }
                if(!empty($f_val)){
                    $file_name = date('Ymdhis') . $member_document['name'][$f_key];
                    $original_file_name = $member_document['name'][$f_key];
                    $file_tmp_name = $member_document['tmp_name'][$f_key];
                    $fileSize = $member_document['size'][$f_key];
                    $fileType = $member_document['type'][$f_key];
                    $file_loc = $MEMBER_DOCUMENT_PATH. $file_name;
                    
                    $import_ext = strtolower(pathinfo($file_loc, PATHINFO_EXTENSION));
                    if (!$validate->getError('member_document')) {
                        $allowed_extensions = array('pdf');
                        $allowed_extensions_display = '*.pdf';
                        $allowed_file_size = '10485760';
                        $size_in_mb = "10";
                        $mime_type = $fileType;
                        if (!in_array($import_ext, $allowed_extensions)) {
                            $validate->setError('member_document_'.$f_key, "Only " . $allowed_extensions_display . " file format allowed");
                        } else if ($fileSize > $allowed_file_size) {
                            $validate->setError('member_document_'.$f_key, "Maximum " . $size_in_mb . " MB file size allowed");
                        }
                    }     
                }
        }	
                        
        if($validate->isValid()){
            $file_nameArr = $member_document['name'];
            foreach($file_nameArr as $fnkey=>$fnval){
                if(!empty($fnval)){
                    $file_name =  date('Ymdhis').$member_document['name'][$fnkey];
                    $original_file_name = $member_document['name'][$fnkey];
                    $file_tmp_name = $member_document['tmp_name'][$fnkey];
                    $file_loc = $MEMBER_DOCUMENT_PATH. $file_name;
                
                    $s3Client = new S3Client([
                        'version' => 'latest',
                        'region'  => $S3_REGION,
                        'credentials'=>array(
                            'key'=> $S3_KEY,
                            'secret'=> $S3_SECRET
                        )
                    ]);
                    
                    $result = $s3Client->putObject([
                        'Bucket' => $S3_BUCKET_NAME,
                        'Key'    => $file_loc,
                        'SourceFile' => $file_tmp_name,
                        'ACL' => 'public-read'
                    ]);
                    
                    if($fnkey > 0){ 
                        $updParams = array(
                            'member_document' => $file_name,
                            'original_file_name' => $original_file_name
                        );
                        $updWhere = array(
                            "clause" => "id=:id",
                            "params" => array(":id" =>$fnkey)
                        );
                        $pdo->update("customer_document", $updParams, $updWhere);  
                               
                        $description['ac_message'] =array( 'ac_red_1'=>array(
                            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']), 
                            'title'=>$_SESSION['admin']['display_id'], 
                        ), 
                        'ac_message_1' =>'Updated Member Document on (', 
                        'ac_red_2'=>array( 
                            'href'=> $ADMIN_HOST.'/members_details.php?id='.$m_id, 
                            'title'=> $rep_id,
                        ), 
                        'ac_message_2' => ')', );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customerId, 'customer','Admin Updated Member Document', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                    }else {
                        $insparam = array(
                            'website_id'=> $policy_id,
                            'member_document' => $file_name,
                            'original_file_name' => $original_file_name
                        );
                        $pdo->insert("customer_document", $insparam); 
                                  
                        $description['ac_message'] =array( 'ac_red_1'=>array(
                            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']), 
                            'title'=>$_SESSION['admin']['display_id'], 
                        ), 
                        'ac_message_1' =>'Added Member Document on (', 
                        'ac_red_2'=>array( 
                            'href'=>  $ADMIN_HOST.'/members_details.php?id='.$m_id, 
                            'title'=> $rep_id,
                        ), 
                        'ac_message_2' => ')', );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customerId, 'customer','Admin Added Member Document', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                    }
                }
            }
        }
    } if($validate -> getErrors()) {
            $error = $validate -> getErrors();
            $response['status'] = 'fail';
            $response['error'] = $error;
        }
        
        header('Content-Type: application/json'); 
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
            ores.future_payment as is_active_in_future,c.sponsor_id,w.application_type,e.file_name
            FROM customer c
            JOIN website_subscriptions w ON (w.customer_id=c.id)
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            JOIN customer s ON (s.id=c.sponsor_id)
            JOIN prd_main p ON (p.id=w.product_id)
            JOIN prd_matrix pm ON (pm.product_id=w.product_id AND w.plan_id = pm.id)
            LEFT JOIN prd_plan_type ppt ON (ppt.id = w.prd_plan_type_id)
            LEFT JOIN (
                SELECT o.id as ordId,o.future_payment,o.customer_id as custId, od.product_id as ordPrdId 
                FROM orders o
                JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                WHERE o.status NOT IN('Pending Validation')
                ORDER BY o.id ASC
            ) ores ON(ores.custId=w.customer_id AND ores.ordPrdId=w.product_id)
            LEFT JOIN enroll_application e ON(e.order_id=ores.ordId)
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
$exJs = array('thirdparty\jquery_custom.js');
$template = 'member_products_tab.inc.php';
include_once 'layout/end.inc.php';
?>
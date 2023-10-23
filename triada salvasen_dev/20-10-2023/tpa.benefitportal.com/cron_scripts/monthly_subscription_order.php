<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/notification_function.php';
require_once dirname(__DIR__) . '/includes/upload_paths.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();

$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

$today = date('Y-m-d');
$requestCustomerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$failed_order_id = isset($_GET['failed_order_id']) ? $_GET['failed_order_id'] : '';

$incr = "";
$schParams=array();
$totalPolicy = 0;
$approvedPolicy = 0;
$declinePolicy = 0;
$terminatedPolicy = 0;
$productSkip = 0;
$customerSkip = 0;
$billingSkip = 0;
$decline_log_id = "";

if(!empty($requestCustomerId)){
    $incr.=" AND c.id = :requestCustomerId";
    $schParams[":requestCustomerId"]=$requestCustomerId;
}

/*---------- System script status code start -----------*/

/*---------- System script status code start -----------*/
if(empty($requestCustomerId)){
    $cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "renewal_order");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);

    if(!empty($cronRow)){
        $cronWhere = array(
                          "clause" => "script_code=:script_code", 
                          "params" => array(
                              ":script_code" => 'renewal_order'
                          )
                      );
        $pdo->update('system_scripts',array("is_running" => "Y","status"=>"Running","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
    }
}
/*---------- System script status code ends -----------*/

// echo "Date : ".$today."<br/>";
$SelSql = "SELECT c.id,w.plan_id,w.id as wid,w.renew_count,c.sponsor_id,w.last_order_id,o.subscription_ids,o.id as orderId,c.sponsor_id,o.grand_total,o.is_renewal,o.original_order_date,c.upline_sponsors,c.level,w.issued_state
  FROM website_subscriptions w
  JOIN customer c ON (c.id=w.customer_id)
  JOIN transactions t ON(t.customer_id=w.customer_id AND t.transaction_type='New Order')
  JOIN orders o ON(o.id=t.order_id)
  WHERE c.status IN ('Active') AND c.type='Customer' AND
  (
    (DATE(w.next_purchase_date)='$today' AND w.total_attempts=0) OR
    (DATE(w.next_attempt_at) = '$today' AND w.total_attempts>0)
  )
  AND w.status in('Active') AND w.is_onetime='N' $incr GROUP BY c.id";
$AutoRows = $pdo->select($SelSql,$schParams);
//pre_print($AutoRows,false);
if (count($AutoRows) > 0) {
    $sendEmailSummary = array();
    foreach ($AutoRows as $autorow) {
       
        // if is_stop flag updated to Y then renewal script execution will be stopped
        if(empty($requestCustomerId)){
            $renewalSql = "SELECT id FROM system_scripts WHERE script_code='renewal_order' AND is_stop='Y'";
            $renewalRow = $pdo->selectOne($renewalSql);
            if(!empty($renewalRow["id"])){
                echo "Script stopped";
                exit;
            }
        }

        $allow_process = true;
        $decline_txt = $decline_type = "";
        $grandTotal = $subTotal = 0;
        $healthy_step_fee_products = $service_fee_products = 0;
        $lastFailOrderId = 0;
        $isAttemptOrder = false;
        $order_id = 0;
        $mbrPolicyCount = 0;

        $plan_ids_arr = array();
        $productWiseInformation = array();
        $renewalCountsArr = array();

        $selSql = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type,c.zip as zip_code
            FROM website_subscriptions w
            JOIN customer c on (c.id=w.customer_id)
            JOIN prd_main pm ON (pm.id=w.product_id)
            JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            WHERE c.status IN('Active') AND c.type='Customer' AND
            (
              (DATE(w.next_purchase_date)='$today' AND w.total_attempts=0) OR
              (w.next_attempt_at='$today' AND w.total_attempts>0)
            )
            AND w.status in('Active') AND c.id=:customer_id
            AND pm.type!='Fees'
            GROUP BY w.id";

        $selParams = array(":customer_id" =>$autorow['id']);
        $ProfileRows = $pdo->select($selSql, $selParams);
        //pre_print($ProfileRows);
        if ($ProfileRows) {
            foreach ($ProfileRows as $key => $row){
                $totalPolicy++;
                // last Declined Order code start
                    $NextAttemptDate = date('Y-m-d',strtotime($row['next_attempt_at']));
                    if($NextAttemptDate == $today && $row['total_attempts'] > 0){
                        $isAttemptOrder = true;
                    }

                    if($row['fail_order_id'] > 0){
                        $lastFailOrderId =  $row['fail_order_id'];  
                    }

                    if(!empty($failed_order_id) && $failed_order_id > 0){
                        $lastFailOrderId = $failed_order_id;
                    }
                // last Declined Order code ends

                $member_payment_type = $row['member_payment_type'];

                //Check if benifit tire change Or renewal fail on next attempt
                if(empty($row['last_order_id'])){
                    $startCoveragePeriod = date('Y-m-d',strtotime($row['start_coverage_period']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                } else {
                    $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                    $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));
                    $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                    $selectOrder = $pdo->selectOne("SELECT od.start_coverage_period,od.end_coverage_period FROM order_details od JOIN orders o on(o.id = od.order_id) WHERE o.id = :order_id AND o.status in('Void','Refund','Cancelled','Chargeback') AND od.website_id = :website_id",array(":order_id" => $row['last_order_id'],':website_id' => $row['id']));

                    if($selectOrder){
                        if(strtotime($selectOrder['start_coverage_period']) > strtotime($today)){
                            $startCoveragePeriod = $selectOrder['start_coverage_period'];
                            $endCoveragePeriod = $selectOrder['end_coverage_period'];
                        }
                    }

                }
                
                // echo "<pre>Start Coverage : ".$startCoveragePeriod." ";
                // echo "End Coverage : ".$endCoveragePeriod."</pre>";

                //checking for plan change in current or next month
                if ($row['process_status'] == 'Pending' && !empty($row['new_plan_id']) && !empty($row['tier_change_date'])) {
                    
                    $tire_change_date = $row['tier_change_date'];

                    if (strtotime($startCoveragePeriod) <= strtotime($tire_change_date) && strtotime($tire_change_date) <= strtotime($endCoveragePeriod)) {

                        $new_ws_sql = "SELECT ce.id as ce_id,ws.* 
                                        FROM customer_enrollment ce 
                                        JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                                        WHERE
                                        ce.parent_coverage_id=:parent_coverage_id AND 
                                        ws.status='Pending' AND 
                                        ce.process_status='Pending'";
                        $new_ws_row = $pdo->selectOne($new_ws_sql, array(":parent_coverage_id" => $row['ce_id']));
                        if(!empty($new_ws_row)) {
                            $ProfileRows[$key]['id'] = $new_ws_row['id'];
                            $ProfileRows[$key]['website_id'] = $new_ws_row['website_id'];
                            $ProfileRows[$key]['product_id'] = $new_ws_row['product_id'];
                            $ProfileRows[$key]['prd_plan_type_id'] = $new_ws_row['prd_plan_type_id'];
                            $ProfileRows[$key]['plan_id'] = $new_ws_row['plan_id'];
                            $ProfileRows[$key]['prd_matrix_id'] = $new_ws_row['plan_id'];
                            $ProfileRows[$key]['subs_price'] = $new_ws_row['price'];
                            $ProfileRows[$key]['price'] = $new_ws_row['price'];
                            $ProfileRows[$key]['old_ws_id'] = $row['id'];

                            $update_new_ws_data = array(
                                "next_purchase_date" => $row['next_purchase_date'],
                                "updated_at" => "msqlfunc_NOW()",
                            );
                            $update_new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $new_ws_row['id']));
                            $pdo->update("website_subscriptions", $update_new_ws_data, $update_new_ws_where);
                        }
                    }
                }

                // Check Termination Date set for subscription
                if (!empty($row['termination_date']) && strtotime($row['termination_date']) > 0) {

                    //checking for Termination Date
                    /*------ Check New Plan Created Or Not ---------*/
                    $new_ws_sql = "SELECT ce.id as ce_id 
                                        FROM customer_enrollment ce 
                                        JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                                        WHERE
                                        ws.plan_id=:plan_id AND
                                        ce.parent_coverage_id=:parent_coverage_id AND 
                                        ws.status='Pending' AND 
                                        ce.process_status='Pending'";
                    $new_ws_row = $pdo->selectOne($new_ws_sql, array(":plan_id" => $row['new_plan_id'],":parent_coverage_id" => $row['ce_id']));
                    if (empty($new_ws_row['ce_id'])) { // Not Created New Plan
                        $term_date = $row['termination_date'];                        
                        if (strtotime($term_date) < strtotime($startCoveragePeriod)) {
                            unset($ProfileRows[$key]);
                            $terminatedPolicy++;
                            continue;
                        }
                    }
                    /*------ Check New Plan Created Or Not ---------*/
                }

                $pricing_change = get_renewals_new_price($row['id']);
                if($pricing_change['pricing_changed'] == 'Y'){
                    $new_ws_data = $pricing_change['new_ws_row'];

                    $ProfileRows[$key]['id'] = $new_ws_data['id'];
                    $ProfileRows[$key]['product_id'] = $new_ws_data['product_id'];
                    $ProfileRows[$key]['plan_id'] = $new_ws_data['plan_id'];
                    $ProfileRows[$key]['prd_matrix_id'] = $new_ws_data['plan_id'];
                    $ProfileRows[$key]['subs_price'] = $new_ws_data['price'];
                    $ProfileRows[$key]['price'] = $new_ws_data['price'];
                }

                $index = $ProfileRows[$key]["product_id"] . "-" . $ProfileRows[$key]["prd_matrix_id"];
              
                if (!isset($productWiseInformation[$index])) {
                    $productWiseInformation[$index] = array();
                    $productWiseInformation[$index]['qty'] = 1;
                }else{
                    $productWiseInformation[$index]['qty'] = $productWiseInformation[$index]['qty'] + 1;
                }

                $renewalCountsArr[$ProfileRows[$key]["product_id"]] = ($ProfileRows[$key]["renew_count"] + 1);

                $prdPrice = $ProfileRows[$key]['price'];
                $subsPrice = $ProfileRows[$key]['subs_price'];
                $subTotal += $prdPrice;
                                
                $productWiseInformation[$index]["subTotal"] = $prdPrice;
                $productWiseInformation[$index]["grandTotal"] = $subsPrice;
               
                $site_load = 'USA';
                $price_tag = "$";

                $plan_ids_arr[$ProfileRows[$key]['product_id']] = $ProfileRows[$key]['plan_id'];

                $mbrPolicyCount++;
            }
        }

        /*pre_print($ProfileRows,false);
        pre_print($productWiseInformation);*/

        if (count($productWiseInformation) == 0) {
            $productSkip = $productSkip + $mbrPolicyCount;
            continue;
            // skipping customer record when subscriptions are not available to process
        }

        //selecting customer details
        $custSql = "SELECT c.fname,c.lname,c.email,c.sponsor_id,c.id,c.rep_id,c.type,c.cell_phone,sp.id as sponsor_id,sp.type as sponsor_type,sp.email as sponsor_email,IFNULL(sp.payment_master_id,0)as payment_master_id,IFNULL(sp.ach_master_id,0)as ach_master_id
                  FROM customer c
                  LEFT JOIN customer sp ON sp.id=c.sponsor_id
                  where c.id=:id AND c.type='Customer' AND c.status IN('Active')";
        $custParams = array(":id" => $autorow['id']);
        $customer_rows = $pdo->selectOne($custSql, $custParams);
        if (!$customer_rows) {
            $customerSkip = $customerSkip + $mbrPolicyCount;
            continue;
            exit;
        }
 
        // selecting service fee code start
            $serviceFeePrice = 0;
            $serviceFee = $MemberEnrollment->getRenewalServiceFee($plan_ids_arr,$autorow['id'],$autorow["sponsor_id"],$subTotal,'Members',"N","Y",$renewalCountsArr);
       
            if(!empty($serviceFee)){
                $serviceFeeRow = $serviceFee[0];
                $websiteServiceRow = array();
                
                if(!empty($serviceFeeRow)){
                    $serviceFeePrice = $serviceFee["total"];

                    $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.customer_id=:customer_id AND w.product_id=:product_id AND w.plan_id=:plan_id";
                    $subParams = array(":customer_id" => $autorow['id'],":product_id"=>$serviceFeeRow["product_id"],":plan_id" => $serviceFeeRow["matrix_id"]);
                    $resSub = $pdo->selectOne($selSub,$subParams);
                 
                    if(!empty($resSub)){
                        $websiteServiceRow = $resSub;

                        if(!empty($websiteServiceRow['fee_applied_for_product'] !=  $serviceFeeRow["fee_product_id"])){
                            $selAppliedPrd = "SELECT id,eligibility_date,start_coverage_period,end_coverage_period FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id";
                            $resAppliedPrd = $pdo->selectOne($selAppliedPrd,array(":customer_id" => $autorow["id"],":product_id" => $serviceFeeRow["fee_product_id"]));

                            if(!empty($resAppliedPrd)){                              
                                $serviceFeeUpdData = array();
                                $serviceFeeUpdData["fee_applied_for_product"] = $serviceFeeRow["fee_product_id"];
                                $serviceFeeUpdData["eligibility_date"] = $resAppliedPrd['eligibility_date'];
                                $serviceFeeUpdData["start_coverage_period"] = $resAppliedPrd['start_coverage_period'];
                                $serviceFeeUpdData["end_coverage_period"] = $resAppliedPrd['end_coverage_period'];
                                $serviceFeeUpdData["termination_date"] = NULL;
                                $serviceFeeUpdData["term_date_set"] = NULL;
                                $serviceFeeUpdData["total_attempts"] = 0;                                
                                $serviceFeeUpdWhere = array("clause" => "id=:id", "params" => array(":id" => $websiteServiceRow['id']));
                                $pdo->update("website_subscriptions", $serviceFeeUpdData, $serviceFeeUpdWhere);

                                $websiteServiceRow = array_merge($websiteServiceRow,$serviceFeeUpdData);
                            }
                        }
                    }else{
                        $insServiceFee = array(
                            "website_id" => $function_list->get_website_id(),
                            "customer_id" => $autorow['id'],
                            "product_id" => $serviceFeeRow["product_id"],
                            "plan_id" => $serviceFeeRow["matrix_id"],
                            "prd_plan_type_id" => $serviceFeeRow["plan_id"],
                            "product_type" => $serviceFeeRow["type"],
                            "fee_applied_for_product" => $serviceFeeRow["fee_product_id"],
                            "qty" => 1,
                            'total_attempts' => 0,
                            'termination_date'=>NULL,
                            'term_date_set' => NULL,
                            "price" => $serviceFeeRow["price"],
                            "product_code" => $serviceFeeRow["product_code"],
                            "issued_state" => $autorow["issued_state"],
                        );

                        $selAppliedPrd = "SELECT id,end_coverage_period FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id";
                        $resAppliedPrd = $pdo->selectOne($selAppliedPrd,array(":customer_id" => $autorow["id"],":product_id" => $serviceFeeRow["fee_product_id"]));

                        if(!empty($resAppliedPrd)){
                            $eligibility_date = date("Y-m-d",strtotime("+1 days",strtotime($resAppliedPrd["end_coverage_period"])));
                            $product_dates=$enrollDate->getCoveragePeriod($eligibility_date);
                            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                            $insServiceFee["eligibility_date"] = $eligibility_date;
                            $insServiceFee["start_coverage_period"] = $startCoveragePeriod;
                            $insServiceFee["end_coverage_period"] = $endCoveragePeriod;
                        }
                        $websiteId = $pdo->insert("website_subscriptions",$insServiceFee);

                        $enrollParams = array(
                                        "website_id" => $websiteId,
                                        "sponsor_id" => $autorow["sponsor_id"],
                                        "level" => $autorow["level"],
                                        "upline_sponsors" => $autorow["upline_sponsors"],
                                        "process_status" => "Active",
                                        );
                        $custEnrollId = $pdo->insert("customer_enrollment",$enrollParams);

                        $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.id=:id";
                        $subParams = array(":id" => $websiteId);
                        $resSub = $pdo->selectOne($selSub,$subParams);

                        if(!empty($resSub)){
                            $websiteServiceRow = $resSub;
                        }
                    }

                    if(!empty($websiteServiceRow)){
                        $websiteServiceRow["prd_matrix_id"] = $websiteServiceRow["plan_id"];
                        $index = $websiteServiceRow["product_id"] . "-" . $websiteServiceRow["prd_matrix_id"];

                        if (!isset($productWiseInformation[$index])) {
                            $productWiseInformation[$index] = array();
                            $productWiseInformation[$index]['qty'] = 1;
                        }else{
                            $productWiseInformation[$index]['qty'] = $productWiseInformation[$index]['qty'] + 1;
                        }

                        $productWiseInformation[$index]["subTotal"] = $websiteServiceRow["price"];
                        $productWiseInformation[$index]["grandTotal"] = $websiteServiceRow["price"];

                        $ProfileRows[] = $websiteServiceRow;
                    }
                }
            }
        // selecting service fee code ends

        // selecting membership fee code start
            $membershipFeePrice = 0;
            $membershipFee = $MemberEnrollment->getRenewalMembershipFee($plan_ids_arr,$autorow['id'],$row["zip_code"],"N","Y",$renewalCountsArr);

            if(!empty($membershipFee)){
                $membershipFeePrice = $membershipFee['total'];
                unset($membershipFee['total']);
            }
            if(!empty($membershipFee)){
                foreach ($membershipFee as $key => $membershipFeeRow) {
                   
                    $websiteMembershipRow = array();
                    
                    $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.customer_id=:customer_id AND w.product_id=:product_id AND w.plan_id=:plan_id";
                    $subParams = array(":customer_id" => $autorow['id'],":product_id"=>$membershipFeeRow["product_id"],":plan_id" => $membershipFeeRow["matrix_id"]);
                    $resSub = $pdo->selectOne($selSub,$subParams);
                      
                    if(!empty($resSub)){
                        $websiteMembershipRow = $resSub;
                    }else{
                        $insMembershipFee = array(
                            "website_id" => $function_list->get_website_id(),
                            "customer_id" => $autorow['id'],
                            "product_id" => $membershipFeeRow["product_id"],
                            "plan_id" => $membershipFeeRow["matrix_id"],
                            "prd_plan_type_id" => $membershipFeeRow["plan_id"],
                            "product_type" => $membershipFeeRow["type"],
                            "fee_applied_for_product" => $membershipFeeRow["fee_product_id"],
                            "qty" => 1,
                            'total_attempts' => 0,
                            'termination_date'=>NULL,
                            'term_date_set' => NULL,
                            "price" => $membershipFeeRow["price"],
                            "product_code" => $membershipFeeRow["product_code"],
                            "issued_state" => $autorow["issued_state"],
                        );

                        $selAppliedPrd = "SELECT id,end_coverage_period FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id";
                        $resAppliedPrd = $pdo->selectOne($selAppliedPrd,array(":customer_id" => $autorow["id"],":product_id" => $membershipFeeRow["fee_product_id"]));

                        if(!empty($resAppliedPrd)){
                            $eligibility_date = date("Y-m-d",strtotime("+1 days",strtotime($resAppliedPrd["end_coverage_period"])));
                            $product_dates=$enrollDate->getCoveragePeriod($eligibility_date);
                            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                            $insMembershipFee["eligibility_date"] = $eligibility_date;
                            $insMembershipFee["start_coverage_period"] = $startCoveragePeriod;
                            $insMembershipFee["end_coverage_period"] = $endCoveragePeriod;
                        }
                        $websiteId = $pdo->insert("website_subscriptions",$insMembershipFee);

                        $enrollParams = array(
                                        "website_id" => $websiteId,
                                        "sponsor_id" => $autorow["sponsor_id"],
                                        "level" => $autorow["level"],
                                        "upline_sponsors" => $autorow["upline_sponsors"],
                                        "process_status" => "Active",
                                        );
                        $custEnrollId = $pdo->insert("customer_enrollment",$enrollParams);

                        $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.id=:id";
                        $subParams = array(":id" => $websiteId);
                        $resSub = $pdo->selectOne($selSub,$subParams);

                        if(!empty($resSub)){
                            $websiteMembershipRow = $resSub;
                        }
                    }

                    if(!empty($websiteMembershipRow)){
                        $websiteMembershipRow["prd_matrix_id"] = $websiteMembershipRow["plan_id"];
                        $index = $websiteMembershipRow["product_id"] . "-" . $websiteMembershipRow["prd_matrix_id"];

                        if (!isset($productWiseInformation[$index])) {
                            $productWiseInformation[$index] = array();
                            $productWiseInformation[$index]['qty'] = 1;
                        }else{
                            $productWiseInformation[$index]['qty'] = $productWiseInformation[$index]['qty'] + 1;
                        }

                        $productWiseInformation[$index]["subTotal"] = $websiteMembershipRow["price"];
                        $productWiseInformation[$index]["grandTotal"] = $websiteMembershipRow["price"];

                        $ProfileRows[] = $websiteMembershipRow;
                    }
                }
            }
        // selecting membership fee code ends
          
        // selecting vendor/carrier/product fee code start
            $linkedFeeTotal = 0;
            
            $linkedFee = $MemberEnrollment->getRenewalLinkedFee($plan_ids_arr,$autorow['id'],$autorow["sponsor_id"],"N","Y",$renewalCountsArr);
            $linkedFeeTotal = $linkedFee['total'];
            unset($linkedFee['total']);

            $sponser_type = getname("customer",$autorow["sponsor_id"],"type","id");

            if(!empty($linkedFee)){
              foreach ($linkedFee as $key => $feeRow) {
                $websiteFeeRow = array();
                $fee_ord_by = '';
                if($sponser_type == 'Group' && $feeRow['product_type'] == 'AdminFee'){
                    $fee_ord_by = "ORDER BY w.id DESC";
                }
                
                if(!empty($feeRow)){
                    $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.customer_id=:customer_id AND w.product_id=:product_id AND w.plan_id=:plan_id $fee_ord_by";
                    $subParams = array(":customer_id" => $autorow['id'],":product_id"=>$feeRow["product_id"],":plan_id" => $feeRow["matrix_id"]);
                    $resSub = $pdo->selectOne($selSub,$subParams);
                    if(!empty($resSub)){
                        $websiteFeeRow = $resSub;
                    }else{
                        $insFeeRow = array(
                            "website_id" => $function_list->get_website_id(),
                            "customer_id" => $autorow['id'],
                            "product_id" => $feeRow["product_id"],
                            "plan_id" => $feeRow["matrix_id"],
                            "prd_plan_type_id" => $feeRow["plan_id"],
                            "product_type" => $feeRow["type"],
                            "fee_applied_for_product" => $feeRow["fee_product_id"],
                            "qty" => 1,
                            'total_attempts' => 0,
                            'termination_date'=>NULL,
                            'term_date_set' => NULL,
                            "price" => $feeRow["price"],
                            "product_code" => $feeRow["product_code"],
                            "issued_state" => $autorow["issued_state"],
                        );

                        $selAppliedPrd = "SELECT id,issued_state,end_coverage_period FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id";
                        $resAppliedPrd = $pdo->selectOne($selAppliedPrd,array(":customer_id" => $autorow["id"],":product_id" => $feeRow["fee_product_id"]));

                        if(!empty($resAppliedPrd)){
                            $eligibility_date = date("Y-m-d",strtotime("+1 days",strtotime($resAppliedPrd["end_coverage_period"])));
                            $product_dates=$enrollDate->getCoveragePeriod($eligibility_date);
                            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                            $insFeeRow["issued_state"] = $resAppliedPrd["issued_state"];
                            $insFeeRow["eligibility_date"] = $eligibility_date;
                            $insFeeRow["start_coverage_period"] = $startCoveragePeriod;
                            $insFeeRow["end_coverage_period"] = $endCoveragePeriod;
                        }
                        $websiteId = $pdo->insert("website_subscriptions",$insFeeRow);

                        $enrollParams = array(
                                        "website_id" => $websiteId,
                                        "sponsor_id" => $autorow["sponsor_id"],
                                        "level" => $autorow["level"],
                                        "upline_sponsors" => $autorow["upline_sponsors"],
                                        "process_status" => "Active",
                                        );
                        $custEnrollId = $pdo->insert("customer_enrollment",$enrollParams);

                        $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                        FROM website_subscriptions w
                        JOIN customer c on (c.id=w.customer_id)
                        JOIN prd_main pm ON (pm.id=w.product_id)
                        JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                        JOIN customer_enrollment ce ON (ce.website_id=w.id)
                        WHERE w.id=:id";
                        $subParams = array(":id" => $websiteId);
                        $resSub = $pdo->selectOne($selSub,$subParams);

                        if(!empty($resSub)){
                            $websiteFeeRow = $resSub;
                        }
                    }

                    if(!empty($websiteFeeRow)){
                        $websiteFeeRow["prd_matrix_id"] = $websiteFeeRow["plan_id"];
                        $index = $websiteFeeRow["product_id"] . "-" . $websiteFeeRow["prd_matrix_id"];

                        if (!isset($productWiseInformation[$index])) {
                            $productWiseInformation[$index] = array();
                            $productWiseInformation[$index]['qty'] = 1;
                        }else{
                            $productWiseInformation[$index]['qty'] = $productWiseInformation[$index]['qty'] + 1;
                        }

                        $productWiseInformation[$index]["subTotal"] += $websiteFeeRow["price"];
                        $productWiseInformation[$index]["grandTotal"] += $websiteFeeRow["price"];
                        $ProfileRows[] = $websiteFeeRow;
                    }
                }
              }
            }
        // selecting vendor/carrier/product fee code ends
       
        
        //check if Renewal Declined order exists
        $existOrder = array();
        if($isAttemptOrder && $lastFailOrderId > 0){
            $sel_order = "SELECT id,subscription_ids,display_id FROM orders WHERE id=:o_id AND status='Payment Declined' AND is_renewal='Y'";
            $order_params = array(":o_id" => $lastFailOrderId);
            $existOrder = $pdo->selectOne($sel_order,$order_params);
            if($existOrder['id']){
                $existing_sub_ids = explode(',', $existOrder['subscription_ids']);
                $cur_sub_ids = array_column($ProfileRows,'id');
                if(!array_merge(array_diff($existing_sub_ids,$cur_sub_ids),array_diff($cur_sub_ids,$existing_sub_ids))){
                    $order_id = $existOrder['id'];
                }
            } else {
                $order_id = 0;
            }
        }

        //selecting billing profile
        $billSql = "SELECT *, 
                    AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                    AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                    AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                    FROM customer_billing_profile WHERE is_default = 'Y' AND customer_id=:cust_id AND is_deleted='N'";
        $params = array(":cust_id" => $autorow['id']);
        $billRow = $pdo->selectOne($billSql, $params);
        //pre_print($billRow,false);

        if (count($billRow) == 0) {
            $allow_process = false;
            $decline_type = 'System';
            $decline_txt = "Billing profile missing";
        }

        //allowed only CC & ACH payment here
        $payment_mode = $billRow["payment_mode"];
        if (!in_array($payment_mode, array("CC","ACH"))) {
            $billingSkip = $billingSkip + $mbrPolicyCount;
            continue;
            exit;
        }


        //calculating Fee and Subtotal
        $product_total = $subTotal;
        
        $subTotal = $product_total + $linkedFeeTotal + $membershipFeePrice;
        $grand_total = $subTotal + $serviceFeePrice;

        $grandTotal = number_format($grand_total, 2, ".", "");

        //pre_print("order_id ".$order_id,false);
        //pre_print("Service Price: ".$serviceFeePrice,false);

        //echo "<hr><br>sub:" . $subTotal;
        //echo "<br>Grand:" . $grandTotal;
        
        //create array to take charge from api
        if($order_id > 0){
            $order_display_id = $existOrder['display_id'];
        }else{
            $order_display_id = $function_list->get_order_id();
        }
       
        $payment_master_id = $function_list->get_agent_merchant_detail($plan_ids_arr, $customer_rows['sponsor_id'], $payment_mode,array('is_renewal'=>'Y','customer_id'=>$autorow['id']));
        $payment_processor= getname('payment_master',$payment_master_id,'processor_id');
        
        $payment_approved = false;
        $txn_id = 0;
        $payment_processor = $payment_processor;
        $cc_params = array();
        $cc_params['order_id'] = $order_display_id;
        $cc_params['amount'] = $grandTotal;

        if($payment_mode == "ACH"){
            $cc_params['ach_account_type'] = $billRow['ach_account_type'];
            $cc_params['ach_routing_number'] = $billRow['ach_routing_number'];
            $cc_params['ach_account_number'] = $billRow['ach_account_number'];
            $cc_params['name_on_account'] = $billRow['fname'].' '.$billRow['lname'];
            $cc_params['bankname'] = $billRow['bankname'];
        } else {
            $cc_params['ccnumber'] = $billRow['cc_no'];
            $cc_params['card_type'] = $billRow['card_type'];
            $cc_params['ccexp'] = str_pad($billRow['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billRow['expiry_year'], -2);
        }
        $cc_params['description'] = "Customer Subscription Payment";
        $cc_params['firstname'] = $billRow['fname'];
        $cc_params['lastname'] = $billRow['lname'];
        $cc_params['address1'] = $billRow['address'];
        $cc_params['city'] = $billRow['city'];
        $cc_params['state'] = $billRow['state'];
        $cc_params['zip'] = $billRow['zip'];
        $cc_params['country'] = 'USA';
        $cc_params['phone'] = $billRow['phone'];
        $cc_params['email'] = $customer_rows['email'];
        $cc_params['processor'] = $payment_processor;

        // pre_print($plan_ids_arr,false);
        // pre_print($subTotal,false);
        // pre_print($grandTotal,false);
        // pre_print($productWiseInformation);
        // pre_print($autorow,false);
        // pre_print($cc_params);

        if($grandTotal == 0) {
            $payment_res = array('status'=>'Success','transaction_id'=>0,'message'=>"Bypass payment API due to order have zero amount.");
        } else {
            if($payment_mode == "ACH"){
                $api = new CyberxPaymentAPI();
                $payment_res = $api->processPaymentACH($cc_params,$payment_master_id);
            } else {
                if($cc_params['ccnumber'] == "4111111111111114") {
                    $payment_res = array('status'=>'Success','transaction_id'=>0);

                    /*$payment_res = '{"status":"Fail","transaction_id":"40049416880","message":"This transaction has been declined.","API_Type":"Auhtorize Global","API_Mode":"sandbox","API_response":{"status":"Fail","error_code":"2","error_message":"This transaction has been declined.","txn_id":"40049416880"}}';
                    $payment_res = json_decode($payment_res,true);*/
                } else {
                    $api = new CyberxPaymentAPI();
                    $payment_res = $api->processPayment($cc_params,$payment_master_id);
                }
            }
        }

        /*pre_print($payment_res);*/

        if ($payment_res['status'] == 'Success') {
            $payment_approved = true;
            $txn_id = $payment_res['transaction_id'];
        } else {
            $decline_txt = $payment_res['message'];
            $decline_type = 'Payment Processor';
            $allow_process = false;
            $payment_approved = false;
            $cc_params['order_type'] = 'Subscription';
            $cc_params['browser'] = $BROWSER;
            $cc_params['os'] = $OS;
            $cc_params['req_url'] = $REQ_URL;
            $cc_params['err_text'] = $decline_txt;
            $payment_error = $decline_txt;
            $decline_log_id = $function_list->credit_card_decline_log($autorow['id'], $cc_params, $payment_res);
        }
        
        //if payment done
        $member_setting = $memberSetting->get_status_by_payment($payment_approved);
        if ($allow_process) {
            // checking if payment processed
            if ($payment_approved) {
                // inserting in order table
                $insOrderSql = array(
                    'display_id' => $order_display_id,
                    'customer_id' => $autorow['id'],
                    'transaction_id' => makeSafe($txn_id),
                    'product_total' => $subTotal,
                    'sub_total' => $subTotal,
                    'grand_total' => $grandTotal,
                    'status' => ($payment_mode == 'ACH') ? 'Pending Settlement' : 'Payment Approved',
                    'type' => ',Renewals,',
                    'payment_type' => $payment_mode,
                    'is_renewal' => 'Y',
                    'payment_processor' => makeSafe($payment_processor),
                    'payment_processor_res' => json_encode($payment_res),
                    'site_load' => makeSafe($site_load),
                    'payment_master_id' => $payment_master_id,
                    'browser' => 'System',
                    'os' => 'System',
                    'req_url' => 'cron_scripts/monthly_subscription_order.php',
                    'original_order_date' => 'msqlfunc_NOW()',
                    'subscription_ids' => implode(',', array_column($ProfileRows, 'id')),
                );

                if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                    $insOrderSql['review_require'] = 'Y';
                }

                $billing_id= 0;             
                if ($order_id > 0) {
                    $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                    $billing_id = getname('order_billing_info',$order_id,'id','order_id');
                    $pdo->update("orders", $insOrderSql, $order_where);
                } else {
                    //if order is new then create new order
                    $order_id = $pdo->insert("orders", $insOrderSql);
                }

                //payment received now update Database
                $billSql = array(
                    'order_id' => $order_id,
                    'customer_billing_id' => $billRow['id'],
                    'customer_id' => $autorow['id'],
                    'fname' => makeSafe($billRow['fname']),
                    'lname' => makeSafe($billRow['lname']),
                    'email' => makeSafe($customer_rows['email']),
                    'country_id' => makeSafe($billRow['country_id']),
                    'country' => makeSafe($billRow['country']),
                    'state' => makeSafe($billRow['state']),
                    'city' => makeSafe($billRow['city']),
                    'zip' => makeSafe($billRow['zip']),
                    'phone' => makeSafe($billRow['phone']),
                    'address' => makeSafe($billRow['address']),
                    'created_at' => 'msqlfunc_NOW()',
                    'updated_at' => 'msqlfunc_NOW()',
                    'payment_mode' => $payment_mode,
                    'last_cc_ach_no' => makeSafe($billRow['last_cc_ach_no']),
                );
                
                if($payment_mode == "ACH"){
                    $billSql = array_merge($billSql,array(
                        'ach_account_type' => $billRow['ach_account_type'],
                        'bankname' => $billRow['bankname'],
                        'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                    ));
                } else{
                    $billSql = array_merge($billSql,array(
                        'card_no' => makeSafe(substr($billRow['cc_no'], -4)),
                        'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billRow['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'card_type' => makeSafe($billRow['card_type']),
                        'expiry_month' => makeSafe($billRow['expiry_month']),
                        'expiry_year' => makeSafe($billRow['expiry_year']),
                    ));
                }

                // if attempt declined order then updates same order
                if ($billing_id > 0) {
                    unset($billSql['created_at']);
                    $pdo->update("order_billing_info", $billSql, array("clause" => "id=:id", "params" => array(":id" => $billing_id)));
                } else {
                    $billSql['order_id'] = $order_id;
                    $pdo->insert("order_billing_info", $billSql);
                }
               
                // generate dpg agreement on order is approved
                $function_list->checkOrderDpgAgreement($order_id);
                
                // $triggerId = getRandomRenewalTriggerId();132
                $triggerId = 0;
                $productDetail = "";
                if ($ProfileRows) {
                    foreach ($ProfileRows as $row) {

                        $prdIndex = $row['product_id'] . "-" . $row["prd_matrix_id"];
                        $prdSql = "SELECT name,type,product_code,member_payment_type from prd_main where id=:id";
                        $productRow = $pdo->selectOne($prdSql, array(":id" => $row['product_id']));
                        $write_steps .= " 8";

                        ////////////////////////////////////////////////
                            $member_payment_type = $productRow['member_payment_type'];

                            $endCoveragePeriod = $startCoveragePeriod = '';

                            if(empty($row['last_order_id'])){
                                $startCoveragePeriod = date('Y-m-d',strtotime($row['start_coverage_period']));
                                $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                            } else {
                                $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                                $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));

                                $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

                                $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                                $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                                $selectOrder = $pdo->selectOne("SELECT od.start_coverage_period,od.end_coverage_period FROM order_details od JOIN orders o on(o.id = od.order_id) WHERE o.id = :order_id AND o.status in('Void','Refund','Cancelled','Chargeback') AND od.website_id = :website_id",array(":order_id" => $row['last_order_id'],':website_id' => $row['id']));

                                if($selectOrder){
                                    if(strtotime($selectOrder['start_coverage_period']) > strtotime($today)){
                                        $startCoveragePeriod = $selectOrder['start_coverage_period'];
                                        $endCoveragePeriod = $selectOrder['end_coverage_period'];
                                    }
                                }
                            }
                        ///////////////////////////////////////////////

                        //inserting order details
                        $insOrderDetailSql = array(
                            'order_id' => $order_id,
                            'website_id' => $row['id'],
                            'product_id' => $row['product_id'],
                            'fee_applied_for_product' =>  $row['fee_applied_for_product'],
                            'plan_id' => $row['plan_id'],
                            'prd_plan_type_id' => $row['prd_plan_type_id'],
                            'product_type' => $productRow['type'],
                            'product_name' => $productRow['name'],
                            'product_code' => $productRow['product_code'],
                            'unit_price' => $productWiseInformation[$prdIndex]["subTotal"],
                            'start_coverage_period' => $startCoveragePeriod,
                            'end_coverage_period' => $endCoveragePeriod,
                            'qty' => $productWiseInformation[$prdIndex]["qty"],
                            'renew_count' => $row['renew_count']+2,
                        );
                        // echo "Order Detail<pre>";
                        //pre_print($insOrderDetailSql, false);
                        
                        $checkOdSql = "SELECT id FROM order_details WHERE order_id=:order_id AND website_id=:website_id AND is_deleted='N'";
                        $checkOdParams = array(":order_id" => $order_id,":website_id"=>$row['id']);
                        $checkOdRow = $pdo->selectOne($checkOdSql,$checkOdParams);
                        if (!$checkOdRow) {
                            $detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);
                        } else {
                            $detail_insert_id = $checkOdRow["id"];
                            $pdo->update("order_details", $insOrderDetailSql, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));
                        }
                        $productDetail .= $productRow['name'] . " ";
                       
                        // inserting in website_subsciption history
                        $insHistorySql = array(
                            'customer_id' => $autorow['id'],
                            'website_id' => $row['id'],
                            'product_id' => $row['product_id'],
                            'fee_applied_for_product' => $row['fee_applied_for_product'],
                            'prd_plan_type_id' => $row['prd_plan_type_id'],
                            'plan_id' => $row['plan_id'],
                            'order_id' => $order_id,
                            'status' => 'Success',
                            'message' => 'Renewed Successfully',
                            'authorize_id' => makeSafe($txn_id),
                            'created_at' => 'msqlfunc_NOW()',
                            'processed_at' => 'msqlfunc_NOW()',
                        );
                        // echo "history<pre>";
                        //pre_print($insHistorySql, false);
                       
                        $history_id = $pdo->insert("website_subscriptions_history", $insHistorySql);

                        $write_steps .= " 11";

                        //updating autoship product
                        $updateArr = array(
                            'last_order_id' => $order_id,
                            'fail_order_id' => 0,
                            'total_attempts' => 0,
                            'next_attempt_at' => NULL,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $member_setting['policy_status'],
                            'payment_type' => $payment_mode,
                            'renew_count' => 'msqlfunc_renew_count + 1',
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $updateArr['start_coverage_period'] = $startCoveragePeriod;
                        $updateArr['end_coverage_period'] = $endCoveragePeriod;

                        $updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['id']));
                        $pdo->update("website_subscriptions", $updateArr, $updateWhere);

                        if(isset($row['old_ws_id'])) {
                            $oldupdateArr = array();
                            $oldupdateArr['total_attempts'] = 1;
                            $oldupdateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['old_ws_id']));
                            $pdo->update("website_subscriptions", $oldupdateArr, $oldupdateWhere);
                        }

                        $upd_cust = array(
                            'status' => $member_setting['member_status'],
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $upd_cust_where = array("clause" => 'id=:id', 'params' => array(":id" => $row['cust_id']));
                        $pdo->update("customer", $upd_cust, $upd_cust_where);

                        $ac_descriptions['ac_message'] =array(
                            'ac_red_1'=>array(
                              'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                              'title'=>$customer_rows['rep_id'],
                            ),
                            'ac_message_1' =>'  Successful Renewal For Policy '.$row['website_id'],
                        );
                        activity_feed(3, $customer_rows['sponsor_id'], $customer_rows['sponsor_type'], $customer_rows['id'], 'customer', 'Successful Renewal', $productRow['name'], "", json_encode($ac_descriptions));
                        
                        //resolve e-tickets
                        //check any ticket for this customer is generated then resoved and create activity feed
                        $checkTicketExists = $pdo->select("SELECT * FROM s_ticket WHERE user_id=:user_id AND subject =:subject AND website_id=:website_id",
                            array('user_id' => $customer_rows['id'],
                                'subject' => "Failed Renewal", ":website_id" => $row['id'])
                        );

                        if (count($checkTicketExists) > 0) {
                            foreach ($checkTicketExists as $chkTicket) {
                                $update_params = array(
                                    'status' => "Completed",
                                );
                                $update_where = array(
                                    'clause' => 'id = :id',
                                    'params' => array(
                                        ':id' => $chkTicket["id"],
                                    ),
                                );
                                $pdo->update("s_ticket", $update_params, $update_where);

                                $ac1_descriptions['ac_message'] =array(
                                    'ac_red_1'=>array(
                                    //   'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                                      'title'=>$chkTicket['tracking_id'],
                                    ),
                                    'ac_message_1' =>'  E-Ticket Resolved For Policy '.$row['website_id'],
                                );

                                activity_feed(3, $customer_rows['id'], $customer_rows['type'], $chkTicket["id"], 's_ticket', 'E-Ticket Resolved', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac1_descriptions));
                            }
                        }
                        //resolve e-tickets ends

                        //sending notifications to chris and mike //For Special People
                        $trigger_param = array(
                            'order_status' => ($payment_mode=="ACH" ? 'Pending Settlement': 'Payment Approved'),
                            'name' => ($customer_rows['fname'] . ' ' . $customer_rows['lname'] . ' (' . $customer_rows['rep_id'] . ')'),
                            'order_id' => makeSafe("#" . $order_display_id),
                            'Transaction_ID' => makeSafe($txn_id),
                            'amount_charged' => ($price_tag . number_format($productWiseInformation[$prdIndex]["grandTotal"], 2, ".", ",")),
                            'payment_type' => $payment_mode,
                            'product_name' => $productRow['name'],
                            'decline_type' => '-',
                            'reason' => 'Success',
                            'Attempt' => makeSafe($row['total_attempts'] + 1),
                        );
                        $sendEmailSummary[] = $trigger_param;
                        if($productRow['type'] != 'Fees'){
                            $approvedPolicy++;
                        }
                    }
                }
                
                $txn_id = $payment_res['transaction_id'];
                $other_params=array("transaction_id"=>$txn_id,"req_url" => "cron_scripts/monthly_subscription_order.php",'transaction_response'=>$payment_res);

                //************************ insert transaction code start ***********************
                    if($payment_mode == "ACH"){
                        $transactionInsId = $function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);
                    } else {
                        $transactionInsId = $function_list->transaction_insert($order_id,'Credit','Renewal Order','Renewal Transaction','',$other_params);
                    }

                    /*-- ACtivity Feed --*/
                    $ac_descriptions = array();
                    $ac_descriptions['ac_message'] =array(
                        'ac_red_1'=>array(
                          'href'=> 'members_details.php?id='.md5($customer_rows['id']),
                          'title'=> $customer_rows['rep_id'],
                        ),
                        'ac_message_1' =>'  Successful Payment OrderID ',
                        'ac_red_2'=>array(
                            'title'=> $order_display_id,
                        ),
                    );
                    activity_feed(3,$customer_rows['sponsor_id'],$customer_rows['sponsor_type'],$customer_rows['id'], 'customer','Successful Payment',"","",json_encode($ac_descriptions));
                //************************ insert transaction code end ***********************

                //********* Payable Insert Code Start ********************
                    if($payment_mode != "ACH"){
                        $payable_params=array(
                            'payable_type'=>'Vendor',
                            'type'=>'Vendor',
                            'transaction_tbl_id' => $transactionInsId['id'],
                        );
                        $payable=$function_list->payable_insert($order_id,0,0,0,$payable_params);
                    }       
                //********* Payable Insert Code End   ********************

                // echo "Order Id : ".$order_id."<br>";
                //update next purchase date code start     
                $enrollDate->updateNextBillingDateByOrder($order_id);
                //update next purchase date code end
                
                // echo "<br>Subscription Processed";
               
                //sending email to customer to notify monthly supply purchase
                $triggerId = 42;
                $email_params = array();
                $email_params['product_name'] = $productDetail;
                $email_params['OrderID'] = "#" . $order_display_id;
                $email_params['TransactionDate'] = date('m/d/Y');
                $email_params['Grand_Total'] = ($price_tag . number_format($grandTotal, 2, ".", ","));
                $email_params['fname'] = $customer_rows['fname'];
                $email_params['lname'] = $customer_rows['lname'];
                $email_params['MemberID'] = $customer_rows['rep_id'];
                $email_params['BillingProfileLast4'] = '*' . $billRow['last_cc_ach_no'];
                $email_params['USER_IDENTITY'] = array('rep_id' => $customer_rows['id'], 'cust_type' => $customer_rows['type'], 'location' => 'cron_scripts/monthly_subscription_order.php');
                trigger_mail($triggerId, $email_params, $customer_rows['email'], "");
                // echo "<br>Email Sent";
            }
            // allow process over
        }
        //If payment failed then Inserting Fail Status to history
        if (!$allow_process && $decline_type != "") {
            // inserting in order table
            $insOrderSql = array(
                'display_id' => $order_display_id,
                'customer_id' => $autorow['id'],
                'transaction_id' => makeSafe($txn_id),
                'product_total' => $subTotal,
                'sub_total' => $subTotal,
                'grand_total' => $grandTotal,
                'status' => 'Payment Declined',
                'type' => ',Renewals,',
                'payment_type' => $payment_mode,
                'is_renewal' => 'Y',
                'payment_processor' => makeSafe($payment_processor),
                'payment_processor_res' => json_encode($payment_res),
                'site_load' => makeSafe($site_load),
                'payment_master_id' => $payment_master_id,
                'browser' => 'System',
                'os' => 'System',
                'req_url' => 'cron_scripts/monthly_subscription_order.php',
                'original_order_date' => 'msqlfunc_NOW()',
                'subscription_ids' => implode(',', array_column($ProfileRows, 'id')),
            );
            $billing_id= 0;
            if ($order_id > 0) {
                $billing_id = getname('order_billing_info',$order_id,'id','order_id');
                $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                $pdo->update("orders", $insOrderSql, $order_where);
            } else {
                //if order is new then create new order
                $order_id = $pdo->insert("orders", $insOrderSql);
            }
            // inserting in order table

            //payment fail now update Database
            if (count($billRow) > 0) {
                $billSql = array(
                    'customer_id' => $autorow['id'],
                    'order_id' => $order_id,
                    'fname' => makeSafe($billRow['fname']),
                    'lname' => makeSafe($billRow['lname']),
                    'email' => makeSafe($customer_rows['email']),
                    'country_id' => makeSafe($billRow['country_id']),
                    'country' => makeSafe($billRow['country']),
                    'state' => makeSafe($billRow['state']),
                    'city' => makeSafe($billRow['city']),
                    'zip' => makeSafe($billRow['zip']),
                    'phone' => makeSafe($billRow['phone']),
                    'address' => makeSafe($billRow['address']),
                    'customer_billing_id' => $billRow['id'],
                    'payment_mode' => $payment_mode,
                    'last_cc_ach_no' => makeSafe($billRow['last_cc_ach_no']),
                );
                if($payment_mode == "ACH"){
                    $billSql = array_merge($billSql,array(
                        'ach_account_type' => $billRow['ach_account_type'],
                        'bankname' => $billRow['bankname'],
                        'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billRow['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                    ));
                } else{
                    $billSql = array_merge($billSql,array(
                        'card_no' => makeSafe(substr($billRow['cc_no'], -4)),
                        'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billRow['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'card_type' => makeSafe($billRow['card_type']),
                        'expiry_month' => makeSafe($billRow['expiry_month']),
                        'expiry_year' => makeSafe($billRow['expiry_year']),
                    ));
                }

                // if attempt declined order then updates same order
                if ($billing_id > 0) {
                    unset($billSql['created_at']);
                    $pdo->update("order_billing_info", $billSql, array("clause" => "id=:id", "params" => array(":id" => $billing_id)));
                } else {
                    $pdo->insert("order_billing_info", $billSql);
                }
            }
            $payment_failed_triggers = array();
            if ($ProfileRows) {
                foreach ($ProfileRows as $row) {
                    $prdIndex = $row['product_id'] . "-" . $row["prd_matrix_id"];
                    $fail_trigger_id = 0;
                    $admin_ticket = 'N';
                    $prdSql = "SELECT name,type,product_code,member_payment_type from prd_main where id=:id";
                    $productRow = $pdo->selectOne($prdSql, array(":id" => $row['product_id']));
                    // inserting in autoship history
                    $insHistorySql = array(
                        'customer_id' => $autorow['id'],
                        'website_id' => $row['id'],
                        'product_id' => $row['product_id'],
                        'fee_applied_for_product' => $row['fee_applied_for_product'],
                        'prd_plan_type_id' => $row['prd_plan_type_id'],
                        'plan_id' => $row['plan_id'],
                        'order_id' => $order_id,
                        'status' => 'Fail',
                        'message' => $decline_txt,
                        'attempt' => ($row['total_attempts'] + 1),
                        'created_at' => 'msqlfunc_NOW()',
                        'processed_at' => 'msqlfunc_NOW()',
                    );
                    // echo "history fail<pre>";
                    //pre_print($insHistorySql,false);
                    $history_id = $pdo->insert("website_subscriptions_history", $insHistorySql);

                    //updating autoship product
                    $updateArr = array(
                        'fail_order_id' => $order_id,
                        'total_attempts' => 'msqlfunc_total_attempts + 1',
                        'updated_at' => 'msqlfunc_NOW()',
                    );

                    $attemptSql = "SELECT * FROM prd_subscription_attempt
                                   WHERE attempt=:attempt AND is_deleted='N'";
                    $attemptParams = array(":attempt" => ($row['total_attempts'] + 1));

                    $attemptRow = $pdo->selectOne($attemptSql, $attemptParams);
                    
                    $extra = array('attempt' => $row['total_attempts'] + 1);
                    $member_setting = $memberSetting->get_status_by_payment($payment_approved,"","","",$extra);

                    if ($attemptRow) {
                        $atmpt = $attemptRow['attempt'];
                        $fail_trigger_id = $attemptRow['fail_trigger_id'];
                        $admin_ticket = $attemptRow['admin_ticket'];
                        $updateArr['next_attempt_at'] = date('Y-m-d', strtotime("+" . $attemptRow['attempt_frequency'] . " Days"));
                        $updateArr['status'] = $member_setting['policy_status'];

                        /*$customrt_updateArr['status'] = $member_setting['member_status'];
                        $customer_updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['cust_id']));
                        $pdo->update("customer", $customrt_updateArr, $customer_updateWhere);*/
                    } else {
                        $termination_date=$enrollDate->getTerminationDate($row['id']);

                        $extra_params = array();
                        $extra_params['location'] = "monthly_subscription_order";
                        $extra_params['cancel_post_payment_order'] = true;
                        $termination_reason = "Failed Billing";
                        $policySetting->setTerminationDate($row['id'],$termination_date,$termination_reason,$extra_params);

                        $ac_descriptions['ac_message'] =array(
                            'ac_red_1'=>array(
                              'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                              'title'=>$customer_rows['rep_id'],
                            ),
                            'ac_message_1' =>' ' . $member_setting['member_status'],
                        );


                        activity_feed(3, $row['cust_id'], $row['cust_type'], $history_id, 'website_subscriptions_history', $member_setting['member_status'], $productRow['name'], "",json_encode($ac_descriptions));
                        
                        $email_params = array();
                        $email_params['fname'] = $customer_rows['fname'];
                        $email_params['Date'] = "";
                        $email_params['USER_IDENTITY'] = array(
                            'rep_id' => $customer_rows['rep_id'], 
                            'cust_type' => $customer_rows['type'], 
                            'location' => 'cron_scripts/monthly_subscription_order.php'
                        );
                        $agent_detail = get_sponsor_detail_for_mail($customer_rows['id'],$customer_rows['sponsor_id']);
                        if(!empty($agent_detail)){
                            $email_params['agent_name'] = $agent_detail['agent_name'];
                            $email_params['agent_email'] = $agent_detail['agent_email'];
                            $email_params['agent_phone'] = $agent_detail['agent_phone'];
                            $email_params['rep_id'] = $agent_detail['rep_id'];
                            $email_params['is_public_info'] = $agent_detail['is_public_info'];
                        } else {
                            $email_params['is_public_info'] = 'display:none';
                        }

                        $smart_tags = get_user_smart_tags($customer_rows['id'],'member');
                
                        if($smart_tags){
                            $email_params = array_merge($email_params,$smart_tags);
                        }
                        if(empty($payment_failed_triggers) || !in_array(40,$payment_failed_triggers)){
                            //send Cancellation of services mail Trigger Id = 40
                            trigger_mail(40, $email_params, $customer_rows['email'], "");

                            array_push($payment_failed_triggers, 40);
                        }

                        

                        //Generating e-tickets
                        $tkt_customer_id = $customer_rows['id'];
                        $customer_email = $customer_rows['email'];
                        $tkt_user_type = $customer_rows['type'];

                        $message1 = "<h4>Failed Renewal</h4><br>
                                     <p>Name of Member : " . $customer_rows['fname'] . ' ' . $customer_rows['lname'] . "</p></br>
                                     <p>Member ID: " . $customer_rows['rep_id'] . "</p></br>
                                     <p>Product Name : " . $productRow['name'] . "</p></br>
                                     <p>Email : " . $customer_rows['email'] . "</p></br>
                                     <p>Phone : " . format_telephone($customer_rows['cell_phone']) . "</p></br>
                                     <p>Failed Billing Reason : " . $decline_txt . "</p></br>
                                     ";
                        $sessionArr = array('System'=>'System');
                        $function_list->createNewTicket($sessionArr,4,"Failed Renewal",0,$message1,$tkt_customer_id,$tkt_user_type,'',array(),'notes',$row['id']);
                        

                        $ac_descriptions_ti['ac_message'] =array(
                            'ac_red_1'=>array(
                              'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                              'title'=>$customer_rows['rep_id'],
                            ),
                            'ac_message_1' =>' E-Ticket Opened For Policy '.$row['website_id']
                        );


                        activity_feed(3, $tkt_customer_id, $tkt_user_type, $tkt_customer_id, $tkt_user_type, 'E-Ticket Opened', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac_descriptions_ti));

                    }

                    $updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['id']));

                    // echo "Update next billing<pre>";
                    // print_r($updateArr);
                    // print_r($updateWhere);
                    // echo "</pre>";
                        
                    $pdo->update("website_subscriptions", $updateArr, $updateWhere);

                    if(isset($row['old_ws_id'])) {
                        $oldupdateArr = array();
                        $oldupdateArr['total_attempts'] = 1;
                        $oldupdateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['old_ws_id']));
                        $pdo->update("website_subscriptions", $oldupdateArr, $oldupdateWhere);
                    }
                    //custom

                    $prdSql = "SELECT name,type,product_code,member_payment_type from prd_main where id=:id";
                    $productRow = $pdo->selectOne($prdSql, array(":id" => $row['product_id']));
                    $write_steps .= " 8";

                    //inserting order details

                    $insOrderDetailSql = array(
                        'order_id' => $order_id,
                        'website_id' => $row['id'],
                        'product_id' => $row['product_id'],
                        'fee_applied_for_product' =>  $row['fee_applied_for_product'],
                        'plan_id' => $row['plan_id'],
                        'prd_plan_type_id' => $row['prd_plan_type_id'],
                        'product_type' => $productRow['type'],
                        'product_name' => $productRow['name'],
                        'product_code' => $productRow['product_code'],
                        'unit_price' => $productWiseInformation[$prdIndex]["subTotal"],
                        'qty' => $productWiseInformation[$prdIndex]["qty"],
                        'renew_count' => $row['renew_count']+2,
                    );
                    
                    $member_payment_type = $productRow['member_payment_type'];
                    if(empty($row['last_order_id'])){
                        $startCoveragePeriod = date('Y-m-d',strtotime($row['start_coverage_period']));
                        $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                    }else{
                        $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                        $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));

                        $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

                        $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                        $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                        $selectOrder = $pdo->selectOne("SELECT od.start_coverage_period,od.end_coverage_period FROM order_details od JOIN orders o on(o.id = od.order_id) WHERE o.id = :order_id AND o.status in('Void','Refund','Cancelled','Chargeback') AND od.website_id = :website_id",array(":order_id" => $row['last_order_id'],':website_id' => $row['id']));

                        if($selectOrder){
                            if(strtotime($selectOrder['start_coverage_period']) > strtotime($today)){
                                $startCoveragePeriod = $selectOrder['start_coverage_period'];
                                $endCoveragePeriod = $selectOrder['end_coverage_period'];
                            }
                        }
                    }

                    $insOrderDetailSql['start_coverage_period'] = $startCoveragePeriod;
                    $insOrderDetailSql['end_coverage_period'] = $endCoveragePeriod;
                    // echo "Order Detail<pre>";
                    //pre_print($insOrderDetailSql, false);

                    $checkOdSql = "SELECT id FROM order_details WHERE order_id=:order_id AND website_id=:website_id AND is_deleted='N'";
                    $checkOdParams = array(":order_id" => $order_id, ":website_id" => $row['id']);
                    $checkOdRow = $pdo->selectOne($checkOdSql, $checkOdParams);

                    if (!$checkOdRow) {
                        $detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);
                    } else {
                        $detail_insert_id = $checkOdRow["id"];
                        $pdo->update("order_details", $insOrderDetailSql, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));
                    }
                    //custom ends

                    // sending email to notify that monthly supply was failed to process
                    $email_params['fname'] = $customer_rows['fname'];
                    $email_params['lname'] = $customer_rows['lname'];
                    $email_params['subscription_type'] = "Smart Saver";
                    $email_params['USER_IDENTITY'] = array(
                        'rep_id' => $customer_rows['rep_id'], 
                        'cust_type' => $customer_rows['type'], 
                        'location' => 'cron_scripts/monthly_supply_orders.php'
                    );
                    $email_params['reason'] = $decline_txt;
                    $email_params['login_link'] = '<a href="' . $CUSTOMER_HOST . '">Your Account</a>';
                    $email_params['link'] = '<a href="' . $CUSTOMER_HOST . '">Login</a>';
                    $email_params['billing_short_url'] = get_short_url(array(
                        'dest_url' => $HOST . '/order_billing/' . md5($order_id),
                        'type' => 'Redirect',
                        'customer_id' => $customer_rows['id'],
                    ));
                    $agent_detail = get_sponsor_detail_for_mail($customer_rows['id'],$customer_rows['sponsor_id']);
                   
                    if(!empty($agent_detail)){
                        $email_params['agent_name'] = $agent_detail['agent_name'];
                        $email_params['agent_email'] = $agent_detail['agent_email'];
                        $email_params['agent_phone'] = $agent_detail['agent_phone'];
                        $email_params['agent_id'] = $agent_detail['rep_id'];
                        $email_params['is_public_info'] = $agent_detail['is_public_info'];
                    } else {
                        $email_params['is_public_info'] = 'display:none';
                    }
                    
                    if ($fail_trigger_id > 0) {
                        if(empty($payment_failed_triggers) || !in_array($fail_trigger_id,$payment_failed_triggers)){

                            trigger_mail($fail_trigger_id, $email_params, $customer_rows['email'], "");

                            $phone = $customer_rows['cell_phone'];
                            if ($phone != "") {
                                $calling_code = "+1";
                                $tophone = $calling_code . $phone;
                                if (isset($_SERVER['HTTP_HOST']) && $SITE_ENV=='Local') {
                                    $tophone = "+917405445244";
                                }
                                //trigger_sms($fail_trigger_id, $tophone, $email_params, "");
                                $commun_data = array(
                                    'type' => "SMS",
                                    'to_phone' => $tophone,
                                    'sms_params' => $email_params,
                                    'req_url' => "cron_scripts/monthly_subscription_order.php",
                                );
                                $function_list->addCommunicationRequest($customer_rows['id'],"Member",$fail_trigger_id,$commun_data);
                            }

                            array_push($payment_failed_triggers, $fail_trigger_id);
                        }
                        
                        
                        $ac_descriptions_tri['ac_message'] =array(
                            'ac_red_1'=>array(
                              'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                              'title'=>$customer_rows['rep_id'],
                            ),
                            'ac_message_1' =>' Failed Renewal For Policy '.$row['website_id']
                        );

                        activity_feed(3, $row['cust_id'], $row['cust_type'], $history_id, 'website_subscriptions_history', 'Failed Renewal', $productRow['name'], "", json_encode($ac_descriptions_tri));

                        

                        if($admin_ticket == 'Y'){
                            //Generating e-tickets
                            $tkt_customer_id = $customer_rows['id'];
                            $customer_email = $customer_rows['email'];
                            $tkt_user_type = $customer_rows['type'];

                            $message1 = "<h4>Failed Renewal</h4><br>
                                         <p>Name of Member : " . $customer_rows['fname'] . ' ' . $customer_rows['lname'] . "</p></br>
                                         <p>Member ID: " . $customer_rows['rep_id'] . "</p></br>
                                         <p>Product Name : " . $productRow['name'] . "</p></br>
                                         <p>Email : " . $customer_rows['email'] . "</p></br>
                                         <p>Phone : " . format_telephone($customer_rows['cell_phone']) . "</p></br>
                                         <p>Failed Billing Reason : " . $decline_txt . "</p></br>
                                         ";
                            $sessionArr = array('System'=>'System');
                            $function_list->createNewTicket($sessionArr,4,"Failed Renewal",0,$message1,$tkt_customer_id,$tkt_user_type,'',array(),'notes',$row['id']);

                            $ac_descriptions_ti['ac_message'] =array(
                                'ac_red_1'=>array(
                                  'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_rows['id']),
                                  'title'=>$customer_rows['rep_id'],
                                ),
                                'ac_message_1' =>' E-Ticket Opened For Policy '.$row['website_id']
                            );

                            activity_feed(3, $tkt_customer_id, $tkt_user_type, $tkt_customer_id, $tkt_user_type, 'E-Ticket Opened', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac_descriptions_ti));
                        }
                    }

                    //sending notifications to chris and mike //For Special People
                    $productDetail = $productRow['name'] . " ";

                    $trigger_param = array(
                        'order_status' => 'Failed',
                        'name' => ($customer_rows['fname'] . ' ' . $customer_rows['lname'] . ' (' . $customer_rows['rep_id'] . ')'),
                        'order_id' => makeSafe("#" . $order_display_id),
                        'Transaction_ID' => '-',
                        'amount_charged' => ($price_tag . number_format($productWiseInformation[$prdIndex]["grandTotal"], 2, ".", ",")),
                        'payment_type' => $payment_mode,
                        'product_name' => makeSafe($productDetail),
                        'decline_type' => makeSafe($decline_type),
                        'reason' => makeSafe($decline_txt),
                        'Attempt' => makeSafe($row['total_attempts'] + 1),
                    );
                    $sendEmailSummary[] = $trigger_param;
                    if($productRow["type"] != 'Fees'){
                        $declinePolicy++;
                    }
                }
            }

            //************************ insert transaction code start ***********************
            $txn_id = $payment_res['transaction_id'];
            $other_params=array("transaction_id"=>$txn_id,"req_url" => "cron_scripts/monthly_subscription_order.php","reason"=>checkIsset($payment_error),"transaction_response" => $payment_res,'cc_decline_log_id'=>checkIsset($decline_log_id));
            $transactionInsId = $function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined','',$other_params);
            //************************ insert transaction code end ***********************

            $ac_descriptions = array();
            $ac_descriptions['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=> 'members_details.php?id='.md5($customer_rows['id']),
                  'title'=> $customer_rows['rep_id'],
                ),
                'ac_message_1' =>'  Failed Payment on Order '.$order_display_id.' <br/>',
                'ac_message_2' =>' due to '. checkIsset($payment_error),  
            );
            activity_feed(3,$customer_rows['sponsor_id'], $customer_rows['sponsor_type'], $customer_rows['id'], 'customer',"Payment Failed",'','',json_encode($ac_descriptions));
        }
    }
    if(empty($requestCustomerId) && $SITE_ENV=='Live'){
        if (!empty($sendEmailSummary)) {
            $DEFAULT_ORDER_EMAIL = array("karan.shukla@serenetic.in","dharmesh@cyberxllc.com");
            trigger_mail_to_email($sendEmailSummary, $DEFAULT_ORDER_EMAIL, $SITE_NAME ." : Renewals Order");
        }
        $renewalStatsArr = array();
        if(!empty($totalPolicy)){
            $renewalStatsArr[] = array(
                'Total Policy' => $totalPolicy,
                'Successfull Policy ' => $approvedPolicy,
                'Decilne Policy' => $declinePolicy,
                'Terminated Policy' => $terminatedPolicy,
                'Product Skip' => $productSkip,
                'Customer Skip' => $customerSkip,
                'Billing Skip' => $billingSkip,
            );
            $defaultEmailIds = array("karan.shukla@serenetic.in","dharmesh@cyberxllc.com","shivalik204@gmail.com","cpearson@cyberxllc.com");
            trigger_mail_to_email($renewalStatsArr, $defaultEmailIds, $SITE_NAME ." : Renewals Order Script");
        }
    }
}

/*--------- System script status code start ----------*/
if(!empty($cronRow) && empty($requestCustomerId)){
    $cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "renewal_order");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);
    if(date("H",strtotime($cronRow['last_processed'])) > 16) {
        $next_processed = date("Y-m-d 05:00:00",strtotime("+1 day", strtotime($cronRow['last_processed'])));
    } else {
        $next_processed = date("Y-m-d 17:00:00",strtotime($cronRow['last_processed']));
    }
    $cronUpdParams = array("is_running"=>"N","status"=>"Active","next_processed"=>$next_processed);
    $cronWhere = array(
        "clause" => "script_code=:script_code", 
        "params" => array(
            ":script_code" => 'renewal_order'
        )
    );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
}
/*---------- System script status code ends -----------*/

// echo "<br>Process Complete";
?>
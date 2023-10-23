<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();


$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

$today = date('Y-m-d');
$requestCustomerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';

$incr = "";
$cronRow = $schParams=array();

if(!empty($requestCustomerId)){
    $incr.=" AND c.id = :requestCustomerId";
    $schParams[":requestCustomerId"]=$requestCustomerId;
}

    /*---------- System script status code start -----------*/
    if(empty($requestCustomerId)){
        $cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
        $cronWhere = array(":script_code" => "tpa_renewal_subscription");
        $cronRow = $pdo->selectOne($cronSql,$cronWhere);
        if(!empty($cronRow)){
            $cronWhere = array(
                            "clause" => "script_code=:script_code", 
                            "params" => array(
                                ":script_code" => 'tpa_renewal_subscription'
                            )
                        );
            $pdo->update('system_scripts',array("is_running" => "Y","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
        }
    }
    /*---------- System script status code ends -----------*/

echo "Date : ".$today."<br/>";
$SelSql = "SELECT c.id,c.rep_id,w.plan_id,w.id as wid,w.renew_count,c.sponsor_id,w.last_order_id,c.sponsor_id,c.upline_sponsors,c.level,w.issued_state
  FROM website_subscriptions w
  JOIN customer c ON (c.id=w.customer_id)
  JOIN customer s ON (s.id=c.sponsor_id AND s.type='Group')
  WHERE c.status IN ('Active') AND c.type='Customer' AND
  (
    (DATE(w.next_purchase_date)='$today' AND w.total_attempts=0) OR
    (DATE(w.next_attempt_at) = '$today' AND w.total_attempts>0)
  )
  AND w.status in('Active') AND w.payment_type='TPA' AND w.is_onetime='N' $incr GROUP BY c.id";
$AutoRows = $pdo->select($SelSql,$schParams);
// pre_print($AutoRow);
if (count($AutoRows) > 0) {
    $sendEmailSummary = array();
    foreach ($AutoRows as $autorow) {
        $allow_process = true;
        $grandTotal = $subTotal = 0;
        $lastFailOrderId = 0;
        $isAttemptOrder = false;
        $order_id = 0;

        $plan_ids_arr = array();
        $productWiseInformation = array();
        $renewalCountsArr = array();

        $selSql = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type,c.zip as zip_code
            FROM website_subscriptions w
            JOIN customer c on (c.id=w.customer_id)
            JOIN customer s ON (s.id=c.sponsor_id AND s.type='Group')
            JOIN prd_main pm ON (pm.id=w.product_id)
            JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            WHERE c.status IN('Active') AND c.type='Customer' AND
            (
              (DATE(w.next_purchase_date)='$today' AND w.total_attempts=0) OR
              (w.next_attempt_at='$today' AND w.total_attempts>0)
            )
            AND w.status in('Active') AND c.id=:customer_id AND w.payment_type='TPA' 
            AND pm.type!='Fees'
            GROUP BY w.id";
        $selParams = array(":customer_id" =>$autorow['id']);
        $ProfileRows = $pdo->select($selSql, $selParams);
        if ($ProfileRows) {
            foreach ($ProfileRows as $key => $row){

                $member_payment_type = $row['member_payment_type'];

                //Check if benifit tire change Or renewal fail on next attempt
                    $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                    $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));
                    $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);
                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
               
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
            }
        }

        if (count($productWiseInformation) == 0) {
            continue;
            // skipping customer record when subscriptions are not available to process
        }

        //selecting customer details
        $custSql = "SELECT c.fname,c.lname,c.email,c.sponsor_id,c.id,c.rep_id,c.type,c.cell_phone,sp.id as sponsor_id,sp.type as sponsor_type,sp.email as sponsor_email,IFNULL(sp.payment_master_id,0)as payment_master_id,IFNULL(sp.ach_master_id,0)as ach_master_id
                  FROM customer c
                  LEFT JOIN customer sp ON (sp.id=c.sponsor_id)
                  where c.id=:id AND c.type='Customer' AND sp.type='Group' AND c.status IN('Active')";
        $custParams = array(":id" => $autorow['id']);
        $customer_rows = $pdo->selectOne($custSql, $custParams);

        if (!$customer_rows) {
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
                        WHERE w.customer_id=:customer_id AND w.payment_type='TPA' AND w.product_id=:product_id AND w.plan_id=:plan_id";
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
                            "payment_type" => "TPA",
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
                        WHERE w.id=:id  AND w.payment_type='TPA'";
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
                        WHERE w.customer_id=:customer_id AND w.product_id=:product_id AND w.plan_id=:plan_id  AND w.payment_type='TPA'";
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
                            "payment_type" => "TPA",
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
                        WHERE w.id=:id  AND w.payment_type='TPA'";
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

            if(!empty($linkedFee)){
                foreach ($linkedFee as $key => $feeRow) {
                    $websiteFeeRow = array();
                    
                    if(!empty($feeRow)){
                        $selSub = "SELECT w.*,c.id as cust_id,c.type as cust_type,w.price as subs_price,p.id as prd_matrix_id,pm.type as prd_type,ce.id as ce_id,ce.process_status,ce.new_plan_id,ce.tier_change_date,w.termination_date,pm.parent_product_id,pm.company_id as prd_company_id,pm.member_payment_type,p.price_calculated_on,p.price_calculated_type,p.commission_amount,p.non_commission_amount,pm.product_type as product_type
                            FROM website_subscriptions w
                            JOIN customer c on (c.id=w.customer_id)
                            JOIN prd_main pm ON (pm.id=w.product_id)
                            JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
                            JOIN customer_enrollment ce ON (ce.website_id=w.id)
                            WHERE w.customer_id=:customer_id AND w.product_id=:product_id AND w.plan_id=:plan_id AND w.payment_type='TPA'";
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
                                "payment_type" => "TPA",
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
                            WHERE w.id=:id AND w.payment_type='TPA'";
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

        //calculating Fee and Subtotal
        $product_total = $subTotal;
        
        $subTotal = $product_total + $linkedFeeTotal + $membershipFeePrice;
        $grand_total = $subTotal + $serviceFeePrice;

        $grandTotal = number_format($grand_total, 2, ".", "");
        $payment_approved = true;
        $member_setting = $memberSetting->get_status_by_payment($payment_approved);
        if ($allow_process) {
            if ($payment_approved) {
                $triggerId = 0;
                $productDetail = "";
                if ($ProfileRows) {
                    foreach ($ProfileRows as $row) {

                        $prdIndex = $row['product_id'] . "-" . $row["prd_matrix_id"];
                        $prdSql = "SELECT name,type,product_code,member_payment_type from prd_main where id=:id";
                        $productRow = $pdo->selectOne($prdSql, array(":id" => $row['product_id']));

                        ////////////////////////////////////////////////
                            $member_payment_type = $productRow['member_payment_type'];

                            $endCoveragePeriod = $startCoveragePeriod = '';

                                $endCoveragePeriod = date('Y-m-d',strtotime($row['end_coverage_period']));
                                $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));

                                $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

                                $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                                $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                        ///////////////////////////////////////////////

                        //inserting order details
                        $productDetail .= $productRow['name'] . " ";
                       
                        // inserting in website_subsciption history
                        $insHistorySql = array(
                            'customer_id' => $autorow['id'],
                            'website_id' => $row['id'],
                            'product_id' => $row['product_id'],
                            'fee_applied_for_product' => $row['fee_applied_for_product'],
                            'prd_plan_type_id' => $row['prd_plan_type_id'],
                            'plan_id' => $row['plan_id'],
                            'order_id' => 0,
                            'status' => 'Success',
                            'message' => 'Renewed Successfully',
                            'authorize_id' => '',
                            'created_at' => 'msqlfunc_NOW()',
                            'processed_at' => 'msqlfunc_NOW()',
                        );
                        // echo "history<pre>";
                        //pre_print($insHistorySql, false);
                       
                        $history_id = $pdo->insert("website_subscriptions_history", $insHistorySql);

                        //updating autoship product
                        $updateArr = array(
                            'last_order_id' => $order_id,
                            'fail_order_id' => 0,
                            'total_attempts' => 0,
                            'next_attempt_at' => NULL,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $member_setting['policy_status'],
                            'payment_type' => 'TPA',
                            'renew_count' => 'msqlfunc_renew_count + 1',
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $updateArr['start_coverage_period'] = $startCoveragePeriod;
                        $updateArr['end_coverage_period'] = $endCoveragePeriod;

                        $next_purchase_date = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);
                        $updateArr['next_purchase_date'] = $next_purchase_date;

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
                            'ac_message_1' =>'  Successful Renewal For Plan '.$row['website_id'],
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
                                    'ac_message_1' =>'  E-Ticket Resolved For Plan '.$row['website_id'],
                                );

                                activity_feed(3, $customer_rows['id'], $customer_rows['type'], $chkTicket["id"], 's_ticket', 'E-Ticket Resolved', $customer_rows['fname'], $customer_rows['lname'], json_encode($ac1_descriptions));
                            }
                        }
                        //resolve e-tickets ends

                        //sending notifications to chris and mike //For Special People
                        $trigger_param = array(
                            'name' => ($customer_rows['fname'] . ' ' . $customer_rows['lname'] . ' (' . $customer_rows['rep_id'] . ')'),
                            'amount_charged' => ($price_tag . number_format($productWiseInformation[$prdIndex]["grandTotal"], 2, ".", ",")),
                            'payment_type' => $payment_mode,
                            'product_name' => $productRow['name'],
                            'decline_type' => '-',
                            'reason' => 'Success',
                            'Attempt' => makeSafe($row['total_attempts'] + 1),
                        );
                        $sendEmailSummary[] = $trigger_param;
                    }
                }

            }
        }

    }
    if (count($sendEmailSummary)) {
        $DEFAULT_ORDER_EMAIL = array("karan@cyberxllc.com","dharmesh@cyberxllc.com");
        trigger_mail_to_email($sendEmailSummary, $DEFAULT_ORDER_EMAIL, "Salvasen : TPA Renewals Subscriptions");
    }
}

/*--------- System script status code start ----------*/
if(!empty($cronRow) && empty($requestCustomerId)){
    $cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "tpa_renewal_subscription");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);
    if(date("H",strtotime($cronRow['last_processed'])) > 16) {
        $next_processed = date("Y-m-d 05:00:00",strtotime("+1 day", strtotime($cronRow['last_processed'])));
    } else {
        $next_processed = date("Y-m-d 17:00:00",strtotime($cronRow['last_processed']));
    }
    $cronUpdParams = array("is_running"=>"N","next_processed"=>$next_processed);
    $cronWhere = array(
        "clause" => "script_code=:script_code", 
        "params" => array(
            ":script_code" => 'tpa_renewal_subscription'
        )
    );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
}
/*---------- System script status code ends -----------*/

dbConnectionClose();
?>
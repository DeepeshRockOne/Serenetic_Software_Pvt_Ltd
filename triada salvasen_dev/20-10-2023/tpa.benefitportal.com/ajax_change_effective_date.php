<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/list_bill.class.php';
$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();
$functionsList = new functionsList();
$ListBill = new ListBill();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : "";
$effective_date = isset($_POST['effective_date']) ? $_POST['effective_date'] : "";
$location = isset($_POST['location']) ? $_POST['location'] : "admin";
$next_billing_date = "";
$new_dependent_effective_date = "";
$end_coverage_periods = array();
$is_group_member = false;
$sponsor_billing_method = "individual";

if(!empty($ws_id) && !empty($effective_date)){
    $ws_row = $pdo->selectOne("SELECT w.*,p.name,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count,p.member_payment_type,p.product_type as p_type FROM website_subscriptions w JOIN prd_main p on(p.id = w.product_id) where w.id = :id",array(":id" => $ws_id));

    $sponsor_info = $pdo->selectOne("SELECT s.id,s.rep_id,s.type FROM customer s JOIN customer c on(s.id = c.sponsor_id) WHERE c.id = :id",array(":id" => $ws_row['customer_id']));
    
    if($sponsor_info && $sponsor_info['type'] == 'Group'){
        $is_group_member = true;
        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsor_info['id']));
        if(!empty($resBillingType)){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }

    if($ws_row){
        if($sponsor_billing_method == 'individual'){
            $count = 1;
            $endCoveragePeriod = '';
            $orders = $pdo->select("SELECT od.* FROM order_details od JOIN orders o on(od.order_id = o.id) WHERE od.website_id = :website_id AND od.is_deleted='N'",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }

            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            

            if($orders){
                foreach ($orders as $k => $v) {
                    foreach ($coverage_periods as $coverage) {
                        array_push($end_coverage_periods, $coverage['end_coverage_period']);

                        if($v['renew_count'] == $coverage['renew_count']){
                            $startCoveragePeriod = $coverage['start_coverage_period'];
                            $endCoveragePeriod = $coverage['end_coverage_period'];

                            $od_update_params = array(
                                'start_coverage_period' => $coverage['start_coverage_period'],
                                'end_coverage_period' => $coverage['end_coverage_period']
                            );
                            
                            $od_whr = array(
                                "clause" => "id=:id",
                                "params" => array(
                                         ":id" => $v['id']
                                )
                            );
                            $pdo->update('order_details', $od_update_params, $od_whr);
                                // break;
                        }   
                    }
                }
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);

            $update_params = array(
                'eligibility_date' => $effective_date,
                'updated_at' => 'msqlfunc_NOW()',
            );

            if(!empty($startCoveragePeriod)){
                $update_params['start_coverage_period'] = $startCoveragePeriod;
            }
            if(!empty($endCoveragePeriod)){
                $update_params['end_coverage_period'] = $endCoveragePeriod;
            }
            if(!empty($next_billing_date)){
                $update_params['next_purchase_date'] = $next_billing_date;
                if(strtotime($next_billing_date) < strtotime(date('Y-m-d'))){
                    $update_params['coverage_missed'] = 'Y';
                }   
            }
            $update_params['eligibility_date_changed'] = 'Y';

            if($ws_row['p_type'] == "Healthy Step") {
                if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                    $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');
                    $product_dates = $enrollDate->getCoveragePeriod($effective_date,$member_payment_type);
                    $tmp_fee_renewal_count = $ws_row['fee_renewal_count'];
                    $tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
                    $tmp_termination_date = $product_dates['endCoveragePeriod'];
                    while ($tmp_fee_renewal_count > 0) {
                        $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
                        $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                        $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                        $tmp_fee_renewal_count--;
                    }
                    $update_params['termination_date'] = $tmp_termination_date;
                    $update_params['term_date_set'] = date('Y-m-d');
                    $update_params['termination_reason'] = 'Policy Change';

                    if(strtotime($tmp_termination_date) <= strtotime(date("Y-m-d"))) {
                        $update_params['status'] = 'Inactive';
                    }
                }
            }

            $ws_whr = array(
                "clause" => "id=:id",
                "params" => array(
                         ":id" => $ws_row['id']
                )
            );
            $pdo->update('website_subscriptions', $update_params, $ws_whr);

            $ws_history = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                'order_id' => $ws_row['last_order_id'],
                'status' => $ws_row['status'],
                'message' => "Effective date changed",
                'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
                'note' => "",
                'processed_at' => 'msqlfunc_NOW()'
            );
            $pdo->insert("website_subscriptions_history", $ws_history);

            $dependets = $pdo->select("SELECT * FROM customer_dependent WHERE website_id = :website_id AND is_deleted = 'N'",array(":website_id" => $ws_row['id']));

            if($dependets){
                foreach ($dependets as $value) {
                    $update_dp_params = array(
                        'eligibility_date' => $effective_date,
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $whr = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $value['id']
                        )
                    );
                    $pdo->update('customer_dependent', $update_dp_params, $whr);
                }
            }

            if(strtotime(date('Y-m-d',strtotime($next_billing_date))) == strtotime(date('Y-m-d'))) {
                $test = $functionsList->generateRenewalOrder($ws_row['customer_id']);
                if(!empty($test)){
                    
                }
            }
        }else if($sponsor_billing_method == 'list_bill'){
            $count = 1;
            $orders = $pdo->select("SELECT lbd.*,l.status,l.customer_id FROM list_bill_details lbd JOIN list_bills l WHERE lbd.ws_id = :website_id group by lbd.start_coverage_date",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }

            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            foreach ($coverage_periods as $coverage) {
                $startCoveragePeriod = $coverage['start_coverage_period'];
                $endCoveragePeriod = $coverage['end_coverage_period'];
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);

            $update_params = array(
                'eligibility_date' => $effective_date,
                'updated_at' => 'msqlfunc_NOW()',
            );

            if($startCoveragePeriod){
                $update_params['start_coverage_period'] = $startCoveragePeriod;
            }
            if($endCoveragePeriod){
                $update_params['end_coverage_period'] = $endCoveragePeriod;
            }
            if($next_billing_date){
                $update_params['next_purchase_date'] = $next_billing_date;
                if(strtotime($next_billing_date) < strtotime(date('Y-m-d'))){
                    $update_params['coverage_missed'] = 'Y';
                }   
            }
            $update_params['eligibility_date_changed'] = 'Y';

            if($ws_row['product_type'] == "Healthy Step") {
                if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                    $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');
                    $product_dates = $enrollDate->getCoveragePeriod($effective_date,$member_payment_type);
                    $tmp_fee_renewal_count = $ws_row['fee_renewal_count'];
                    $tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
                    $tmp_termination_date = $product_dates['endCoveragePeriod'];
                    while ($tmp_fee_renewal_count > 0) {
                        $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
                        $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                        $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                        $tmp_fee_renewal_count--;
                    }
                    $update_params['termination_date'] = $tmp_termination_date;
                    $update_params['term_date_set'] = date('Y-m-d');
                    $update_params['termination_reason'] = 'Policy Change';
                }
            }

            $ws_whr = array(
                "clause" => "id=:id",
                "params" => array(
                         ":id" => $ws_row['id']
                )
            );
            $pdo->update('website_subscriptions', $update_params, $ws_whr);

            $ws_history = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                'order_id' => $ws_row['last_order_id'],
                'status' => $ws_row['status'],
                'message' => "Effective date changed",
                'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
                'note' => "",
                'processed_at' => 'msqlfunc_NOW()'
            );
            $pdo->insert("website_subscriptions_history", $ws_history);

            $dependets = $pdo->select("SELECT * FROM customer_dependent WHERE website_id = :website_id AND is_deleted = 'N'",array(":website_id" => $ws_row['id']));

            if($dependets){
                foreach ($dependets as $value) {
                    $update_dp_params = array(
                        'eligibility_date' => $effective_date,
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $whr = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $value['id']
                        )
                    );
                    $pdo->update('customer_dependent', $update_dp_params, $whr);
                }
            }

        }else if($sponsor_billing_method == 'TPA'){

            $count = 1;
            $orders = $pdo->select("SELECT god.* FROM group_order_details god WHERE god.website_id = :website_id AND god.is_deleted='N'",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }

            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            if($orders){
                foreach ($orders as $k => $v) {
                    foreach ($coverage_periods as $coverage) {
                        array_push($end_coverage_periods, $coverage['end_coverage_period']);
                        
                            $startCoveragePeriod = $coverage['start_coverage_period'];
                            $endCoveragePeriod = $coverage['end_coverage_period'];

                        if($v['renew_count'] == $coverage['renew_count']){    
                            $od_update_params = array(
                                'start_coverage_period' => $coverage['start_coverage_period'],
                                'end_coverage_period' => $coverage['end_coverage_period']
                            );
                            
                            $od_whr = array(
                                "clause" => "id=:id",
                                "params" => array(
                                         ":id" => $v['id']
                                )
                            );
                            $pdo->update('group_order_details', $od_update_params, $od_whr);
                        }
                    }
                }
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);

            $update_params = array(
                'eligibility_date' => $effective_date,
                'updated_at' => 'msqlfunc_NOW()',
            );

            if($startCoveragePeriod){
                $update_params['start_coverage_period'] = $startCoveragePeriod;
            }
            if($endCoveragePeriod){
                $update_params['end_coverage_period'] = $endCoveragePeriod;
            }
            if($next_billing_date){
                $update_params['next_purchase_date'] = $next_billing_date;
                if(strtotime($next_billing_date) < strtotime(date('Y-m-d'))){
                    $update_params['coverage_missed'] = 'Y';
                }   
            }
            $update_params['eligibility_date_changed'] = 'Y';

            if($ws_row['product_type'] == "Healthy Step") {
                if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                    $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');
                    $product_dates = $enrollDate->getCoveragePeriod($effective_date,$member_payment_type);
                    $tmp_fee_renewal_count = $ws_row['fee_renewal_count'];
                    $tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
                    $tmp_termination_date = $product_dates['endCoveragePeriod'];
                    while ($tmp_fee_renewal_count > 0) {
                        $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
                        $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                        $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                        $tmp_fee_renewal_count--;
                    }
                    $update_params['termination_date'] = $tmp_termination_date;
                    $update_params['term_date_set'] = date('Y-m-d');
                    $update_params['termination_reason'] = 'Policy Change';
                }
            }

            $ws_whr = array(
                "clause" => "id=:id",
                "params" => array(
                         ":id" => $ws_row['id']
                )
            );
            $pdo->update('website_subscriptions', $update_params, $ws_whr);

            $ws_history = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                'order_id' => $ws_row['last_order_id'],
                'status' => $ws_row['status'],
                'message' => "Effective date changed",
                'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
                'note' => "",
                'processed_at' => 'msqlfunc_NOW()'
            );
            $pdo->insert("website_subscriptions_history", $ws_history);

            $dependets = $pdo->select("SELECT * FROM customer_dependent WHERE website_id = :website_id AND is_deleted = 'N'",array(":website_id" => $ws_row['id']));

            if($dependets){
                foreach ($dependets as $value) {
                    $update_dp_params = array(
                        'eligibility_date' => $effective_date,
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $whr = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $value['id']
                        )
                    );
                    $pdo->update('customer_dependent', $update_dp_params, $whr);
                }
            }

        }

        $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
        $old_eligibility_date = $ws_row['eligibility_date'];
        
        $af_message = 'changed effective date';
        $activity_id = 0;
        if($location == "admin") {
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
                'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($effective_date),
            );
            $activity_id = activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
            
        } elseif($location == "agent") {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($effective_date),
            );
            $activity_id = activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif($location == "group") {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($effective_date),
            );
            $activity_id = activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
        }

        /*------- Update Policy Document ------*/
        if(!empty($ws_row['agreement_id'])) {
            $tmp_extra = array(
                'website_id' => $ws_row['id'],
                'activity_id' => $activity_id,
                'action' => "effective_date_updated"
            );
            $functionsList->update_member_terms($customer_row['id'],$ws_row['id'],$ws_row['agreement_id'],$tmp_extra);
        }
        /*-------/Update Policy Document ------*/

        // next billing date activity feed code start
        if(!empty($next_billing_date)){
            $af_message = 'changed next billing date';
            $old_next_billing_date = $ws_row['next_purchase_date'];
            if($location == "admin") {
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
                    'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Next billing date changed from : '.displayDate($old_next_billing_date).' to : '.displayDate($next_billing_date),
                );
                activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
                
            } elseif($location == "agent") {
                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                        'title'=> $_SESSION['agents']['rep_id'],
                    ),
                    'ac_message_1' => $af_message.' on ',
                    'ac_red_2'=>array(
                        'href'=> 'members_details.php?id='.md5($customer_row['id']),
                        'title'=>$customer_row['rep_id'],
                    ),
                    'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Next billing date changed from : '.displayDate($old_next_billing_date).' to : '.displayDate($next_billing_date),
                );
                activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
            } elseif($location == "group") {
                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                        'title'=> $_SESSION['groups']['rep_id'],
                    ),
                    'ac_message_1' => $af_message.' on ',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                        'title'=>$customer_row['rep_id'],
                    ),
                    'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Next billing date changed from : '.displayDate($old_next_billing_date).' to : '.displayDate($next_billing_date),
                );
                activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
            }
        }
         // next billing date activity feed code ends
        $response['status'] = 'success';
        $response['message'] = 'Effective date updated successfully'; 
    }else{
        $response['status'] = 'fail';
        $response['message'] = 'Subscription Not Found';
    }
}else{
    $response['status'] = 'fail';
    $response['message'] = 'Subscription Not Found';
}

echo json_encode($response);
dbConnectionClose();
exit();
?>
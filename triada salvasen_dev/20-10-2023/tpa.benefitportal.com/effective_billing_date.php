<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/list_bill.class.php';
$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();
$ListBill = new ListBill();
$error = "";

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";


$wsSql = "SELECT ws.*,p.product_type,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count,p.main_product_type
        FROM website_subscriptions ws 
        JOIN prd_main p ON (p.id=ws.product_id)
        WHERE md5(ws.id)=:id";
$ws_row = $pdo->selectOne($wsSql,array(':id' => $ws_id));

$cust_sql = "SELECT id,rep_id,sponsor_id FROM customer WHERE id=:id";
$cust_row = $pdo->selectOne($cust_sql, array(":id" => $ws_row['customer_id']));

$customer_id = $ws_row['customer_id'];
$product_id = $ws_row['product_id'];

$response = array();

if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){

    $eligibility_date = isset($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : "";

    if(empty($eligibility_date)){
        $error = "Please select effective date";
        $response['error'] = $error;
        $response['status'] = 'fail';
    }else{

        if(strtotime($ws_row['eligibility_date']) != strtotime($eligibility_date)) {

            $is_list_bill_enroll = "N";
            $is_group_member = is_group_member($ws_row['customer_id']);

            if ($is_group_member == true) {
                $group_cb_row = $pdo->selectOne("SELECT billing_type FROM customer_group_settings WHERE customer_id=:customer_id", array(":customer_id" => $cust_row['sponsor_id']));
                $is_list_bill_enroll = (!empty($group_cb_row['billing_type']) && $group_cb_row['billing_type'] == "list_bill") ? "Y" : "N";
            }

            $lowest_next_billing_date = get_customer_billing_date($customer_id);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            $product_dates=$enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);

            $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
            $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

            $next_purchase_date = $enrollDate->getNextBillingDateByCoverageStart($customer_id,$startCoveragePeriod);

            $update_params = array(
                'eligibility_date' => $eligibility_date,
                'next_purchase_date' => $next_purchase_date,
                'start_coverage_period' => $startCoveragePeriod,
                'end_coverage_period' => $endCoveragePeriod,
                'updated_at' => 'msqlfunc_NOW()',
            );

            /*------ Set Termination Date for Healthy Step ------*/
            if($ws_row['product_type'] == "Healthy Step") {
                if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                    $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');
                    $product_dates = $enrollDate->getCoveragePeriod($eligibility_date,$member_payment_type);
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
            /*------/Set Termination Date for Healthy Step ------*/
            $ws_whr = array(
                "clause" => "id=:id",
                "params" => array(
                    ":id" => $ws_row['id']
                )
            );
            $pdo->update('website_subscriptions', $update_params, $ws_whr);

            // Charged/Refund Adjustment for ListBill Member Code Start
                if ($is_list_bill_enroll == "Y") {
                    $ListBill->getSubscriptionRefundChargeCoverage($ws_row["id"]);

                    /*if(strtotime($eligibility_date) < strtotime($ws_row["eligibility_date"])){
                        $ListBill->listBillMemberAdjustment("reinstate",$ws_row["id"],$cust_row["id"],$cust_row["sponsor_id"]);
                    }else if(strtotime($eligibility_date) > strtotime($ws_row["eligibility_date"])){
                         $ListBill->listBillMemberAdjustment("refund",$ws_row["id"],$cust_row["id"],$cust_row["sponsor_id"],$eligibility_date,true);
                    }*/
                }
            // Charged/Refund Adjustment for ListBill Member Code Start
            
            /*----- Update Order Detail Coverage Date ------------*/
            $order_detail_row = $pdo->selectOne("SELECT id FROM order_details WHERE order_id=:order_id AND plan_id=:plan_id AND is_deleted='N'",array(":order_id"=>$ws_row['last_order_id'],":plan_id"=>$ws_row['plan_id']));
            if(!empty($order_detail_row)) {
                $od_update_params = array(
                    'start_coverage_period' => $startCoveragePeriod,
                    'end_coverage_period' => $endCoveragePeriod,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                $od_whr = array(
                    "clause" => "id=:id",
                    "params" => array(
                        ":id" => $order_detail_row['id']
                    )
                );
                $pdo->update('order_details', $od_update_params, $od_whr);
            }

            $dependets = $pdo->select("SELECT * FROM customer_dependent WHERE customer_id = :customer_id AND product_id = :product_id AND product_plan_id = :plan_id AND is_deleted = 'N'",array(":customer_id" => $ws_row['customer_id'],":product_id" => $ws_row['product_id'],":plan_id" => $ws_row['plan_id']));

            if($dependets){
                foreach ($dependets as $value) {
                    if(strtotime($eligibility_date) > strtotime($value['eligibility_date'])){
                        $update_params = array(
                            'eligibility_date' => $eligibility_date,
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $whr = array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $value['id']
                            )
                        );
                        $pdo->update('customer_dependent', $update_params, $whr);
                    }
                }
            }



            /*----- Update Order Detail Coverage Date ------------*/
            $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
            $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
            $old_eligibility_date = $ws_row['eligibility_date'];
            
            $af_message = 'changed effective date';
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
                    'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($eligibility_date),
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
                    'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($eligibility_date),
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
                    'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($eligibility_date),
                );
                activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
            }
        }

        $response['status'] = 'success';
        $response['effective_date'] = date('m/d/Y',strtotime($eligibility_date));
        $response['product_id'] =$product_id;
        $response['message'] ='The eligibility date has been updated.';

    }
    echo json_encode($response);
    exit();
}
$extra = array();
$coverage_period_data = $MemberEnrollment->get_coverage_period(array($product_id),$cust_row['sponsor_id'],$extra);
$coverage_period_data = (isset($coverage_period_data[$product_id])?$coverage_period_data[$product_id]:array());
$earliest_effective_date = '';
if($ws_row['main_product_type'] == "Core Product") {
    $earliest_effective_date = $MemberEnrollment->get_core_prd_earliest_effective_date($ws_row['id']);
}

$disabledDates = !empty($coverage_period_data['datesDisabled']) ? $coverage_period_data['datesDisabled'] : array();
$disableDays = array();
if(!empty($disabledDates)){
    foreach ($disabledDates as $value) {
        $day = date("d",strtotime($value));
        if(!in_array($day, $disableDays)){
            array_push($disableDays, $day);
        }
    }
    if(!empty($disableDays) && $coverage_period_data['is_allow_31'] == 'N'){
        array_push($disableDays, "31");
    }
}

$template = 'effective_billing_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
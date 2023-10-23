<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();

$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "";
$effective_date = isset($_GET['effective_date']) ? $_GET['effective_date'] : "";
$location = isset($_GET['location']) ? $_GET['location'] : "admin";

$new_coverage_periods = array();
$next_billing_date = "";
$new_effective_date = "";
$new_dependent_effective_date = "";
$end_coverage_periods = array();
$is_group_member = false;
$sponsor_billing_method = "individual";

if(!empty($ws_id) && !empty($effective_date)){
    $ws_row = $pdo->selectOne("SELECT w.*,p.name,p.product_type as prd_type,p.main_product_type FROM website_subscriptions w JOIN prd_main p on(p.id = w.product_id) where md5(w.id) = :id",array(":id" => $ws_id));

    if($ws_row['main_product_type'] == "Core Product") {
        $earliest_effective_date = $MemberEnrollment->get_core_prd_earliest_effective_date($ws_row['id']);
        if(!empty($earliest_effective_date) && strtotime(date('Y-m-d',strtotime($effective_date))) < strtotime($earliest_effective_date)) {
            setNotifyError("Core Product Already Active On Selected Coverage.");
            echo "<script>window.parent.location.reload();</script>";
            exit();
        }
    }
    $sponsor_info = $pdo->selectOne("SELECT s.id,s.rep_id,s.type FROM customer s JOIN customer c on(s.id = c.sponsor_id) WHERE c.id = :id",array(":id" => $ws_row['customer_id']));
    
    if($sponsor_info && $sponsor_info['type'] == 'Group'){
        $is_group_member = true;
        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsor_info['id']));
        if(!empty($resBillingType)){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }

    $effective_date = date('Y-m-d',strtotime($effective_date));
    if($ws_row){
        if($sponsor_billing_method == 'individual'){
            $count = 1;
            $endCoveragePeriod = '';
            $orders = $pdo->select("SELECT od.* FROM order_details od JOIN orders o on(od.order_id = o.id) WHERE od.website_id = :website_id AND od.is_deleted='N' group by o.id",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }
            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            if($orders){
                foreach ($orders as $k => $v) {
                    foreach ($coverage_periods as $date => $coverage) {
                        if(!isset($new_coverage_periods[$date])){
                            $new_coverage_periods[$date] = $coverage;
                        }
                        array_push($end_coverage_periods, $coverage['end_coverage_period']);
                        if($v['renew_count'] == $coverage['renew_count']){
                            $startCoveragePeriod = $coverage['start_coverage_period'];
                            $endCoveragePeriod = $coverage['end_coverage_period'];
                            break;
                        }   
                    }
                }
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);

            if($ws_row['prd_type'] == "Healthy Step"){
                $next_billing_date = "";
            }

            $dependents = $pdo->select("SELECT cd.*,CONCAT(cdp.fname,' ',cdp.lname) as dependent_name 
                      FROM customer_dependent cd
                      JOIN customer_dependent_profile cdp on(cdp.id = cd.cd_profile_id)
                      WHERE cd.website_id=:website_id AND cd.is_deleted = 'N' GROUP BY cdp.id", array(":website_id" => $ws_row['id']));

        }else if($sponsor_billing_method == 'list_bill'){
            $count = 1;
            $orders = $pdo->select("SELECT lbd.* FROM list_bill_details lbd WHERE lbd.ws_id = :website_id group by lbd.start_coverage_date",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }
            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            if($orders){
                foreach ($orders as $k => $v) {
                    foreach ($coverage_periods as $date => $coverage) {
                        if(!isset($new_coverage_periods[$date])){
                            $new_coverage_periods[$date] = $coverage;
                        }
                        array_push($end_coverage_periods, $coverage['end_coverage_period']);
                        if($count == $coverage['renew_count']){
                            $startCoveragePeriod = $coverage['start_coverage_period'];
                            $endCoveragePeriod = $coverage['end_coverage_period'];
                            break;
                        }   
                    }
                }
            }else{
                foreach ($coverage_periods as $date => $coverage) {
                    if(!isset($new_coverage_periods[$date])){
                        $new_coverage_periods[$date] = $coverage;
                    }
                    array_push($end_coverage_periods, $coverage['end_coverage_period']);
                    if($count == $coverage['renew_count']){
                        $startCoveragePeriod = $coverage['start_coverage_period'];
                        $endCoveragePeriod = $coverage['end_coverage_period'];
                        break;
                    }   
                }
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverageList($end_coverage_periods,$startCoveragePeriod,$ws_row['customer_id']);

            if($ws_row['prd_type'] == "Healthy Step"){
                $next_billing_date = "";
            }

            $dependents = $pdo->select("SELECT cd.*,CONCAT(cdp.fname,' ',cdp.lname) as dependent_name 
                      FROM customer_dependent cd
                      JOIN customer_dependent_profile cdp on(cdp.id = cd.cd_profile_id)
                      WHERE cd.website_id=:website_id AND cd.is_deleted = 'N' GROUP BY cdp.id", array(":website_id" => $ws_row['id']));
        }else if($sponsor_billing_method == 'TPA'){
            $count = 1;
            $orders = $pdo->select("SELECT od.* FROM group_order_details od JOIN group_orders o on(od.order_id = o.id) WHERE od.website_id = :website_id AND od.is_deleted='N' group by o.id",array(':website_id' => $ws_row['id']));
            if($orders){
                $count = count($orders);
            }
            $coverage_periods = coverage_periods_for_effective_date_change($ws_row['id'],$effective_date,$count);
            $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

            if($orders){
                foreach ($orders as $k => $v) {
                    foreach ($coverage_periods as $date => $coverage) {
                        if(!isset($new_coverage_periods[$date])){
                            $new_coverage_periods[$date] = $coverage;
                        }
                        array_push($end_coverage_periods, $coverage['end_coverage_period']);
                        if($count == $coverage['renew_count']){
                            $startCoveragePeriod = $coverage['start_coverage_period'];
                            $endCoveragePeriod = $coverage['end_coverage_period'];
                            break;
                        }   
                    }
                }
            }else{
                foreach ($coverage_periods as $date => $coverage) {
                    if(!isset($new_coverage_periods[$date])){
                        $new_coverage_periods[$date] = $coverage;
                    }
                    array_push($end_coverage_periods, $coverage['end_coverage_period']);
                    if($count == $coverage['renew_count']){
                        $startCoveragePeriod = $coverage['start_coverage_period'];
                        $endCoveragePeriod = $coverage['end_coverage_period'];
                        break;
                    }   
                }
            }

            $next_billing_date = $enrollDate->getNextBillingDateFromCoverageList($end_coverage_periods,$startCoveragePeriod,$ws_row['customer_id']);

            if($ws_row['prd_type'] == "Healthy Step"){
                $next_billing_date = "";
            }

            $dependents = $pdo->select("SELECT cd.*,CONCAT(cdp.fname,' ',cdp.lname) as dependent_name 
                      FROM customer_dependent cd
                      JOIN customer_dependent_profile cdp on(cdp.id = cd.cd_profile_id)
                      WHERE cd.website_id=:website_id AND cd.is_deleted = 'N' GROUP BY cdp.id", array(":website_id" => $ws_row['id']));
        }

    }
}

$template = 'coverage_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
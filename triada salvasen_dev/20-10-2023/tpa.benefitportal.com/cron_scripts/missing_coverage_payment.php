<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
$function_list = new functionsList();
$enrollDate = new enrollmentDate();

// Script will run 1 day before renewal and it will check if any coverage payment missed for the product then it will insert in table and create E-ticket for it.

$tomorrow = date('Y-m-d',strtotime("+1 days"));
// pre_print($tomorrow);

$selCustomer = "SELECT c.id
  FROM website_subscriptions w
  JOIN customer c ON (c.id=w.customer_id)
  JOIN orders o ON(o.customer_id=w.customer_id AND o.is_renewal='N' AND o.status='Payment Approved')
  WHERE c.status IN ('Active') AND c.type='Customer' AND
  (
    (DATE(w.next_purchase_date)='$tomorrow' AND w.total_attempts=0) OR
    (DATE(w.next_attempt_at) = '$tomorrow' AND w.total_attempts>0)
  )
  AND w.status in('Active') AND w.is_onetime='N' GROUP BY c.id";
$resCustomer = $pdo->select($selCustomer);
// pre_print($resCustomer);

if(!empty($resCustomer)){
    foreach($resCustomer as $custRow){

        $selPolicy = "SELECT w.id,w.last_order_id,w.start_coverage_period,w.end_coverage_period,
                        w.termination_date,pm.member_payment_type,ce.new_plan_id,ce.id as ce_id,w.website_id
            FROM website_subscriptions w
            JOIN customer c on (c.id=w.customer_id)
            JOIN prd_main pm ON (pm.id=w.product_id)
            JOIN prd_matrix p ON (p.product_id = w.product_id AND FIND_IN_SET(p.id,w.plan_id))
            JOIN customer_enrollment ce ON (ce.website_id=w.id)
            WHERE c.status IN('Active') AND c.type='Customer' AND
            (
              (DATE(w.next_purchase_date)='$tomorrow' AND w.total_attempts=0) OR
              (w.next_attempt_at='$tomorrow' AND w.total_attempts>0)
            )
            AND w.status in('Active') AND c.id=:customer_id
            AND pm.type!='Fees'
            GROUP BY w.id";

        $paramsPolicy = array(":customer_id" =>$custRow['id']);
        $resPolicy = $pdo->select($selPolicy, $paramsPolicy);
       

        if(!empty($resPolicy)){
            foreach ($resPolicy as $key => $policyRow){

                $member_payment_type = $policyRow['member_payment_type'];

                //Check if benefit tire change Or renewal fail on next attempt
                if(empty($policyRow['last_order_id'])){
                    $startCoveragePeriod = date('Y-m-d',strtotime($policyRow['start_coverage_period']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($policyRow['end_coverage_period']));
                } else {
                    $endCoveragePeriod = date('Y-m-d',strtotime($policyRow['end_coverage_period']));
                    $startDate=date('Y-m-d',strtotime('+1 day',strtotime($endCoveragePeriod)));
                    $product_dates=$enrollDate->getCoveragePeriod($startDate,$member_payment_type);

                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                }
           

                // Check Termination Date set for subscription
                if(!empty($policyRow['termination_date']) && strtotime($policyRow['termination_date']) > 0){
                
                    /*------ Check New Plan Created Or Not ---------*/
                    $new_ws_sql = "SELECT ce.id as ce_id 
                                        FROM customer_enrollment ce 
                                        JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                                        WHERE
                                        ws.plan_id=:plan_id AND
                                        ce.parent_coverage_id=:parent_coverage_id AND 
                                        ws.status='Pending' AND 
                                        ce.process_status='Pending'";
                    $new_ws_row = $pdo->selectOne($new_ws_sql, array(":plan_id" => $policyRow['new_plan_id'],":parent_coverage_id" => $policyRow['ce_id']));
                    if (empty($new_ws_row['ce_id'])) { // Not Created New Plan
                        $term_date = $policyRow['termination_date'];     
                        if (strtotime($term_date) < strtotime($startCoveragePeriod)) {
                            unset($policyRow[$key]);
                            continue;
                        }
                    }
                    /*------ Check New Plan Created Or Not ---------*/
                }
                                            

                $function_list->getPaymentFailedCoverages(md5($custRow["id"]),md5($policyRow["id"]));
            }
        }
      
    }
}

// Check if the subscription coverage period payment approved then update flag in table.

$selCoverage = "SELECT fc.id,fc.website_id,fc.customer_id,od.start_coverage_period
            FROM payment_failed_coverages fc
            JOIN order_details od ON(fc.order_id=od.order_id AND od.id=fc.order_detail_id AND od.is_deleted='N')
            WHERE fc.is_deleted = 'N' AND fc.is_paid='N'
            GROUP BY fc.id";
$resCoverage = $pdo->select($selCoverage);

if(!empty($resCoverage)){
    foreach ($resCoverage as $row) {
        $ws_payment_status = subscriotion_has_approved_payment_this_coverage($row['website_id'],$row['start_coverage_period']);

        if($ws_payment_status['success'] == true && $ws_payment_status['is_post_date_order'] == false) {
            $updParams = array("is_paid" => "Y");
            $updWhere = array("clause" => "id=:id", "params" => array(":id" => $row['id']));
            $pdo->update("payment_failed_coverages",$updParams,$updWhere);

        //check any ticket for this customer is generated then resoved and create activity feed
            $ticketSql = "SELECT s.id,s.tracking_id,ws.website_id,c.id as mbrId,c.type as mbrType,
                            c.fname as mbrFname,c.lname as mbrLname
                            FROM s_ticket s
                            LEFT JOIN website_subscriptions ws ON(s.website_id=ws.id)
                            LEFT JOIN customer c ON(c.id=s.user_id)
                            WHERE s.user_id=:user_id AND s.subject =:subject 
                            AND s.website_id=:website_id AND s.status!='Completed'";
            $ticketParams = array(
                                'user_id' => $row['customer_id'],
                                'subject' => "Missing Coverage Payment",
                                ":website_id" => $row['website_id']
                            );
            $ticketRes = $pdo->select($ticketSql,$ticketParams);
            // pre_print($ticketRes);
            if(!empty($ticketRes)){
                foreach($ticketRes as $ticketRow){
                    $updTicket = array(
                        'status' => "Completed",
                    );
                    $ticketWhere = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => $ticketRow["id"],
                        ),
                    );
                    $pdo->update("s_ticket", $updTicket, $ticketWhere);

                    $ac1_descriptions['ac_message'] =array(
                        'ac_red_1'=>array(
                        'title'=>$ticketRow['tracking_id'],
                        ),
                        'ac_message_1' =>'  E-Ticket Resolved For Plan '.$row['website_id'],
                    );
                    activity_feed(3, $ticketRow['mbrId'], $ticketRow['mbrType'], $ticketRow["id"], 's_ticket', 'E-Ticket Resolved', $ticketRow['mbrFname'], $ticketRow['mbrLname'], json_encode($ac1_descriptions));
                }
            }
        //resolve e-tickets code ends

        }
    }
}


// echo "<br>Process Complete";
?>
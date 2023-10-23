<?php
include_once dirname(__DIR__) . "/includes/functions.php";

class enrollmentDate {

    //Get or Update Member Billing Day
    public function getMemberBillingDay($customer_id,$new_billing_day = 0,$is_update = false){
        global $pdo;
        $today = date("Y-m-d");
        $billing_day = 0;
        $ws_sql = "SELECT MIN(w.next_purchase_date) as next_purchase_date
                  FROM website_subscriptions w
                  JOIN prd_main pm ON (pm.id=w.product_id)
                  WHERE 
                  (
                    (DATE(w.next_purchase_date) >= '$today' AND w.total_attempts=0) OR
                    (DATE(w.next_attempt_at) >= '$today' AND w.total_attempts>0)
                  )
                  AND w.status IN('Active') AND w.termination_date IS NULL AND w.is_onetime='N' AND pm.type!='Fees' AND w.customer_id=:customer_id";
        $ws_row = $pdo->selectOne($ws_sql,array(":customer_id" => $customer_id));
        if(!empty($ws_row['next_purchase_date'])) {
            $billing_day = date("d",strtotime($ws_row['next_purchase_date']));
        } else {
            $sql="SELECT MIN(od.end_coverage_period) as end_coverage_period,o.customer_id 
                  FROM order_details od 
                  JOIN orders o ON(o.id = od.order_id) 
                  JOIN transactions t ON(t.order_id=od.order_id)
                  WHERE 
                  o.is_renewal='N' AND 
                  t.transaction_status IN('Payment Approved','Pending Settlement') AND 
                  t.customer_id=:customer_id AND 
                  od.is_deleted='N'
                  ORDER BY t.id ASC LIMIT 1";
            $res=$pdo->selectOne($sql,array(":customer_id"=>$customer_id));
            if(!empty($res['end_coverage_period'])) {
                $next_billing_date = date('Y-m-d',strtotime($res['end_coverage_period'] .'-1 day'));
                $billing_day = date("d",strtotime($next_billing_date));
            }
        }

        if($new_billing_day > 0 && (empty($billing_day) || $is_update == true)) {
            $billing_day = $new_billing_day;
        }

        $params = array('billing_day'=>$billing_day);
        $where = array(
          'clause' => 'customer_id=:customer_id ', 
          'params' => array(':customer_id' => $customer_id)
        );
        $pdo->update("customer_settings", $params, $where);

        return $billing_day;
    }

    //Get NBD From Coverage Start and Member Billing Day
    public function getNextBillingDateByCoverageStart($customer_id,$startCoveragePeriod,$billing_day = 0,$payment_type_subscription='Monthly'){
        global $pdo;
        $startCoveragePeriod = date('Y-m-d',strtotime($startCoveragePeriod));
        if($billing_day == 0) {
            $billing_day = $this->getMemberBillingDay($customer_id);
        }

        if($billing_day >= date('d',strtotime($startCoveragePeriod))) {
            if($billing_day > date('t',strtotime($startCoveragePeriod))) {
                $next_billing_date = date('Y-m-t',strtotime($startCoveragePeriod));
            } else {
                $next_billing_date = date('Y-m-'.$billing_day,strtotime($startCoveragePeriod));
            }
        } else {
            $date = new DateTime($startCoveragePeriod);
            $startCoveragePeriod = addMonth($date->modify('-1 day'),'1');
            if($billing_day > date('t',strtotime($startCoveragePeriod))) {
                $next_billing_date = date('Y-m-t',strtotime($startCoveragePeriod));
            } else {
                $next_billing_date = date('Y-m-'.$billing_day,strtotime($startCoveragePeriod));
            }
        }
        $next_billing_date = date('Y-m-d',strtotime($next_billing_date));
        if($payment_type_subscription == 'Annually'){
          $temp_date = new DateTime($next_billing_date);
          $next_billing_date = addMonth($temp_date,'11');
        }
        return $next_billing_date;
    }

  //****************** Get Next Billing Date From Order ********************
    public function getNextBillingDate($order_id,$startCoveragePeriod=''){
      global $pdo;
      $next_billing_date = '';
      
      $sql="SELECT MIN(od.end_coverage_period) as end_coverage_period,o.customer_id from order_details od JOIN orders o ON(o.id = od.order_id) where od.order_id=:order_id AND od.is_deleted='N'";

      $res=$pdo->selectOne($sql,array(":order_id"=>$order_id));
      $customer_id = 0;
      if(!empty($res['end_coverage_period'])){
          $next_billing_date = date('Y-m-d',strtotime($res['end_coverage_period'] .'-1 day'));
          $customer_id = $res['customer_id'];
      }

      if(!empty($customer_id)) {
          $billing_day = date("d",strtotime($next_billing_date));
          $billing_day = $this->getMemberBillingDay($customer_id,$billing_day);

          $next_billing_date = $this->getNextBillingDateByCoverageStart($customer_id,$startCoveragePeriod,$billing_day);
      }

      if(!empty($startCoveragePeriod)){
        if(strtotime($next_billing_date) < strtotime($startCoveragePeriod)){
          $lowest_coverage_date = $res['end_coverage_period'];
          while(strtotime($next_billing_date) < strtotime($startCoveragePeriod)){
            $next_coverage_start = date('Y-m-d',strtotime('+1 day',strtotime($lowest_coverage_date)));
            $next_coverage_data = $this->getCoveragePeriod($next_coverage_start);
            $next_billing_date = date('Y-m-d',strtotime($next_coverage_data['endCoveragePeriod'] .'-1 day'));
            $lowest_coverage_date = $next_coverage_data['endCoveragePeriod'];
          }
        }
      }
      return $next_billing_date;
    }
  //****************** Get Next Billing Date From Order ********************

  //****************** Update Next Billing Date By Order ********************
    public function updateNextBillingDateByOrder($order_id){
      global $pdo;
      $ord_row = $pdo->selectOne("SELECT customer_id,subscription_ids FROM orders WHERE id=:id",array(":id" => $order_id));
      $customer_id = $ord_row['customer_id'];
      $today = date('Y-m-d');
      $billing_day = $this->getMemberBillingDay($customer_id);
      $is_update_billing_day = true;

      if(!empty($billing_day)) {
          //Check All Policy have same billing date or not, If same then find update billing day Else Use Old Billing Day
          $ws_ids = $ord_row['subscription_ids'];
          if(!empty($ws_ids)) {
              $ws_sql = "SELECT w.id
              FROM website_subscriptions w
              JOIN prd_main pm ON (pm.id=w.product_id)
              WHERE 
              (
                (DATE(w.next_purchase_date) >= '$today' AND w.total_attempts=0) OR
                (DATE(w.next_attempt_at) >= '$today' AND w.total_attempts>0)
              )
              AND w.status IN('Active') AND w.termination_date IS NULL AND w.is_onetime='N' AND pm.type!='Fees' AND w.customer_id=:customer_id AND w.id NOT IN($ws_ids)";
              $ws_row = $pdo->selectOne($ws_sql,array(":customer_id" => $customer_id));
              if(!empty($ws_row['id'])) {
                  $is_update_billing_day = false;
              }
          }
      }

      //Set New Billing Day Based On Lowest End Coverage - 4 Day
      $sql="SELECT MIN(od.start_coverage_period) as start_coverage_period,MIN(od.end_coverage_period) as end_coverage_period,o.customer_id,o.created_at 
            FROM order_details od 
            JOIN prd_main pm ON (pm.id=od.product_id)
            JOIN orders o ON(o.id = od.order_id) 
            WHERE pm.type!='Fees' AND od.order_id=:order_id AND od.is_deleted='N'";
      $res=$pdo->selectOne($sql,array(":order_id"=>$order_id));
      $next_billing_date = date('Y-m-d',strtotime($res['end_coverage_period'] .'-1 day'));
      if(!empty($billing_day)) {
            $next_billing_date2 = $this->getNextBillingDateByCoverageStart($customer_id,$res['start_coverage_period'],$billing_day);
            $date1=date_create(date('Y-m-d',strtotime($res['created_at'])));
            $date2=date_create($next_billing_date2);
            $days_diff = date_diff($date1,$date2)->format("%r%a"); //number of days
            if($is_update_billing_day == true || $days_diff < 23) {
                $billing_day = date("d",strtotime($next_billing_date));
            }
      } else {
          $billing_day = date("d",strtotime($next_billing_date));
      }
      
      if($is_update_billing_day == true) {          
          $billing_day = $this->getMemberBillingDay($customer_id,$billing_day,true);
      }

      $sql ="SELECT w.id as id,w.next_purchase_date,w.start_coverage_period as SuccessfullStartCoverage,w.end_coverage_period as SuccessfullEndCoverage,od.start_coverage_period,od.end_coverage_period,
        w.next_purchase_date_changed,w.next_purchase_date_retain_rule,w.manual_next_purchase_date,IF(p.payment_type='Recurring',p.payment_type_subscription,'One Time') as member_payment_type 
            FROM order_details od
            JOIN website_subscriptions w ON (w.id = od.website_id)
            JOIN prd_main p on(p.id = w.product_id)
            WHERE od.order_id=:order_id AND od.is_deleted='N'";
      $res = $pdo->select($sql,array(":order_id"=>$order_id));
      if(!empty($res)){
          foreach ($res as $value) {
              $next_purchase_date = $this->getNextBillingDateByCoverageStart($customer_id,$value['start_coverage_period'],$billing_day,$value['member_payment_type']);
            
            // OP29-769 Next Billing Day Updates Start
              if($value["next_purchase_date_changed"] == "Y" && $value["next_purchase_date_retain_rule"] == "allRenewal"){
                $next_purchase_date = $this->getManualNextBillingDate($value['id'],$value['start_coverage_period'],$next_purchase_date);
              }
            // OP29-769 Next Billing Day Updates Ends
               
              $upd_ws_data = array();
              if(strtotime($next_purchase_date) >= strtotime($value['next_purchase_date'])) {
                  $upd_ws_data['next_purchase_date'] = date('Y-m-d',strtotime($next_purchase_date));
              }
              if((strtotime($value['start_coverage_period']) >= strtotime($value['SuccessfullStartCoverage'])) && 
                (strtotime($value['end_coverage_period']) >= strtotime($value['SuccessfullEndCoverage']))) {
                  $upd_ws_data['start_coverage_period'] = $value['start_coverage_period'];
                  $upd_ws_data['end_coverage_period'] = $value['end_coverage_period'];
              }

              $upd_ws_where = array("clause" => " id = :subscription_id ", 'params' => array(':subscription_id' => $value['id']));
              if(!empty($upd_ws_data)) {
                  $pdo->update("website_subscriptions", $upd_ws_data, $upd_ws_where);
              }
          }
      }
      return true;
    }
  //****************** Update Next Billing Date By Order ********************

  //****************** Get Next Billing Date From List of coverage ********************
    public function getNextBillingDateFromCoverageList($coverageArray=array(),$startCoveragePeriod='',$customer_id='',$payment_type_subscription='Monthly'){
      global $pdo;
      $next_billing_date = '';

      if(!empty($coverageArray)){
          $lowest_coverage_date = $this->getLowestCoverageDate($coverageArray);
          $next_billing_date = date('Y-m-d',strtotime($lowest_coverage_date .'-1 day'));
      }

      if(!empty($customer_id)) {
          $billing_day = date("d",strtotime($next_billing_date));
          
          $today = date('Y-m-d');
          $ws_sql = "SELECT w.id
          FROM website_subscriptions w
          JOIN prd_main pm ON (pm.id=w.product_id)
          WHERE 
          (
            (DATE(w.next_purchase_date) >= '$today' AND w.total_attempts=0) OR
            (DATE(w.next_attempt_at) >= '$today' AND w.total_attempts>0)
          )
          AND w.status IN('Active') AND w.termination_date IS NULL AND w.is_onetime='N' AND pm.type!='Fees' AND w.customer_id=:customer_id";
          $ws_row = $pdo->selectOne($ws_sql,array(":customer_id" => $customer_id));
          if(empty($ws_row)) {
              $billing_day = $this->getMemberBillingDay($customer_id,$billing_day,true);
          } else {
              $billing_day = $this->getMemberBillingDay($customer_id,$billing_day);
          }
          $next_billing_date = $this->getNextBillingDateByCoverageStart($customer_id,$startCoveragePeriod,$billing_day,$payment_type_subscription);
      }

      if(!empty($startCoveragePeriod)){
        if(strtotime($next_billing_date) < strtotime($startCoveragePeriod)){
          while(strtotime($next_billing_date) < strtotime($startCoveragePeriod)){
            $next_coverage_start = date('Y-m-d',strtotime('+1 day',strtotime($lowest_coverage_date)));
            $next_coverage_data = $this->getCoveragePeriod($next_coverage_start);
            $next_billing_date = date('Y-m-d',strtotime($next_coverage_data['endCoveragePeriod'] .'-1 day'));
            $lowest_coverage_date = $next_coverage_data['endCoveragePeriod'];
          }
        }
      }
      return $next_billing_date;
    }
  //****************** Get Next Billing Date From List of coverage ********************


  //****************** Get Coverage Period ********************
    public function getCoveragePeriod($start_coverage_date='',$coverage_duration='Monthly',$WS_ID=''){
      global $pdo;
      $response = array();
      if (empty($start_coverage_date)) {
        $start_coverage_date = date("Y-m-d");
      }
      $eligibility_date = $start_coverage_date;
      if(!empty($WS_ID)){
        $sql="SELECT eligibility_date,start_coverage_period,end_coverage_period FROM website_subscriptions WHERE id=:id";
        $res=$pdo->selectOne($sql,array(":id"=>$WS_ID));
        if(!empty($res)){
          $today_date=date("Y-m-d");
          $eligibility_date = date("Y-m-d",strtotime($res['eligibility_date'])); 
          $old_coverage_date = date("Y-m-d",strtotime($res['start_coverage_period']));

          if(strtotime($eligibility_date) >= strtotime($old_coverage_date)){
            $start_coverage_date = $eligibility_date;
          }else{
            $tmp_date = new DateTime($old_coverage_date);   
            $start_coverage_date = addMonth($tmp_date,'1');
          }
        }
      }

      $date = new DateTime($start_coverage_date);
      $startCoveragePeriod = $date->format('Y-m-d');

      if(strtotime($startCoveragePeriod) == strtotime($date->format('Y-m-01'))) {
        if($coverage_duration=='Monthly'){
          $endCoveragePeriod =$date->format('Y-m-t');
        }elseif($coverage_duration=='90 Days'){
          $endCoveragePeriod = addMonth($date->modify('-1 day'),'3');
        }elseif($coverage_duration=='Annually'){
          $endCoveragePeriod = addMonth($date,'12');
        }else if ($coverage_duration=='Semi-Monthly'){
          $endCoveragePeriod = date('Y-m-d', strtotime("+14 day", strtotime($startCoveragePeriod))); 
        }else if ($coverage_duration=='Weekly'){
          $endCoveragePeriod = date('Y-m-d', strtotime("+6 day", strtotime($startCoveragePeriod))); 
        }else if ($coverage_duration=='Bi-Weekly'){
          $endCoveragePeriod = date('Y-m-d', strtotime("+13 day", strtotime($startCoveragePeriod))); 
        }else{
          $endCoveragePeriod =$date->format('Y-m-t');
        }
        
      } else {
        if($coverage_duration=='Monthly'){
          $endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
        }elseif($coverage_duration=='90 Days'){
          $endCoveragePeriod = addMonth($date->modify('-1 day'),'3');
        }elseif($coverage_duration=='Annually'){
          $endCoveragePeriod = addMonth($date->modify('-1 day'),'12');
        }else if ($coverage_duration=='Semi-Monthly'){
          if(date('d',strtotime($startCoveragePeriod)) == '16'){
            $endCoveragePeriod = date('Y-m-t', strtotime($startCoveragePeriod)); 
          }else{
            $endCoveragePeriod = date('Y-m-d', strtotime("+14 day", strtotime($startCoveragePeriod))); 
          }
        }else if ($coverage_duration=='Weekly'){
          $endCoveragePeriod = date('Y-m-d', strtotime("+6 day", strtotime($startCoveragePeriod))); 
        }else if ($coverage_duration=='Bi-Weekly'){
          $endCoveragePeriod = date('Y-m-d', strtotime("+13 day", strtotime($startCoveragePeriod))); 
        }else{
          $endCoveragePeriod = addMonth($date->modify('-1 day'),'1');
        }        
      }
      $response["startCoveragePeriod"] = $startCoveragePeriod;
      $response["endCoveragePeriod"] = $endCoveragePeriod;
      $response["eligibility_date"] = $eligibility_date;
      return $response;
    }
  //****************** Get Coverage Period ********************

  //****************** Check Last Successfull Coverage Periods with current orders Date ********************
    public function checkLastSuccessfullCoverage($order_id){
      global $pdo;
      $response=array();

      $sql="SELECT ws.id as ws_id,ws.start_coverage_period as SuccessfullStartCoverage,ws.end_coverage_period as SuccessfullEndCoverage,od.start_coverage_period,od.end_coverage_period,od.product_id as od_product,ws.product_id as subscription_product
        FROM orders o
        JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
        JOIN website_subscriptions ws ON (FIND_IN_SET(ws.id,o.subscription_ids) AND od.product_id=ws.product_id AND od.plan_id = ws.plan_id)
        WHERE o.id=:id";
      $res=$pdo->select($sql,array(":id"=>$order_id));

      if(!empty($res)){
        foreach ($res as $key => $value) {
          $id= $value['ws_id'];
          $SuccessfullStartCoverage = $value['SuccessfullStartCoverage'];
          $SuccessfullEndCoverage = $value['SuccessfullEndCoverage'];
          
          $start_coverage_period = $value['start_coverage_period'];
          $end_coverage_period = $value['end_coverage_period'];

          if((strtotime($start_coverage_period) >= strtotime($SuccessfullStartCoverage)) && 
            (strtotime($end_coverage_period) >= strtotime($SuccessfullEndCoverage))) {
            $response[$id]['start_coverage_period'] = $start_coverage_period;
            $response[$id]['end_coverage_period'] = $end_coverage_period;
          }
        }
      }

      return $response;
    }
  //****************** Check Last Successfull Coverage Periods with current orders Date ********************
  
  //****************** get Lowest Coverage Date ********************
    function getLowestCoverageDate($coverage_dates){
      global $pdo;
      $lowest_coverage_date = min(array_map(function($item) { return $item; }, array_values($coverage_dates)));

      return $lowest_coverage_date;
    }
  //****************** get Lowest Coverage Date ********************

  //****************** get Lowest Coverage Date ********************
    function checkOrderChargeOptions($order_id,$is_regenerate=false){
      global $pdo;
      $today=date('Y-m-d');
      $next_billing_date = $this->getNextBillingDate($order_id);
      $checkDate= date('Y-m-d',strtotime($next_billing_date .'-1 day'));

      $is_allowed=false;

      if(strtotime($today) < strtotime($checkDate)){
        $is_allowed=true;
      }

      if($is_allowed == true && $is_regenerate == false){
        $sql = "SELECT w.termination_date
          FROM orders o
          JOIN website_subscriptions w ON(FIND_IN_SET(w.id,o.subscription_ids))
          WHERE o.id=:id AND w.termination_date IS NOT NULL";
        $whr = array(":id" => $order_id);
        $res = $pdo->select($sql,$whr);

        if(!empty($res)){
          $is_allowed = false;
        }
      }
      return $is_allowed;

    }
  //****************** get Lowest Coverage Date ********************

  //****************** Get Next Billing Date From End Coverage ********************
    public function getNextBillingDateFromCoverage($endCoveragePeriod){
      global $pdo;

      $next_billing_date = date('Y-m-d',strtotime($endCoveragePeriod .'-1 day'));
      
      return $next_billing_date;
    }
  //****************** Get Next Billing Date From End Coverage ********************


  //****************** Get Termination Date ********************
    public function getTerminationDate($ws_id){
      global $pdo;
      $wsSql="SELECT eligibility_date,product_id from website_subscriptions where id=:ws_id";
      $wsRes=$pdo->selectOne($wsSql,array(":ws_id"=>$ws_id)); 

      $termination_date=date('Y-m-d',strtotime($wsRes['eligibility_date']));

      $sql="SELECT od.start_coverage_period,od.end_coverage_period FROM orders o 
          JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
          WHERE FIND_IN_SET(:ws_id,o.subscription_ids) AND od.product_id=:product_id GROUP BY od.start_coverage_period";
      $res=$pdo->select($sql,array(":ws_id"=>$ws_id,":product_id" =>$wsRes['product_id']));

      $total_success=0;

      if(!empty($res)){
        foreach ($res as $key => $value) {
          $start_coverage_period = $value['start_coverage_period'];

          $response=subscription_is_paid_for_coverage_period($ws_id,$start_coverage_period);

          if(!empty($response) && $response['is_paid']==true && $response['is_post_date_order']==false){
            $total_success++;
          }
        }
      }

      if(!empty($total_success)){
        $limit = $total_success - 1;

        $sql="SELECT od.start_coverage_period,od.end_coverage_period FROM orders o 
          JOIN order_details od ON (o.id=od.order_id AND od.is_deleted='N')
          WHERE FIND_IN_SET(:ws_id,o.subscription_ids) AND od.product_id=:product_id GROUP BY od.start_coverage_period  LIMIT $limit,1";
        $res=$pdo->selectOne($sql,array(":ws_id"=>$ws_id,":product_id" =>$wsRes['product_id']));

        

        if(!empty($res)){
          if(!empty($res['end_coverage_period'])){
            $termination_date=date('Y-m-d',strtotime($res['end_coverage_period']));
          }       
        }
      }
      return $termination_date;
    }
  //****************** Get Termination Date ********************

  //****************** Get Manually Next Billing Date OP29-769 ********************
    public function getManualNextBillingDate($ws_id,$start_coverage_period,$next_billing_date){
      global $pdo;
      $member_payment_type = '';
      $wsSql = "SELECT w.id,w.customer_id,w.next_purchase_date_changed,w.
              manual_next_purchase_date,w.next_purchase_date_retain_rule,IF(p.payment_type='Recurring',p.payment_type_subscription,'One Time') as member_payment_type
              FROM website_subscriptions w
              JOIN prd_main p on(p.id = w.product_id)
              WHERE w.id=:id";
      $wsParams = array(":id" => $ws_id);
      $wsRes = $pdo->selectOne($wsSql,$wsParams);
     
      if(!empty($wsRes)){
        if($wsRes["next_purchase_date_changed"] == "Y" && $wsRes["next_purchase_date_retain_rule"] == "allRenewal"){
          $billing_day = date("d",strtotime($wsRes["manual_next_purchase_date"]));

          if($billing_day > date('d',strtotime($start_coverage_period))) {
            if($billing_day > date('t',strtotime($start_coverage_period))) {
                $next_billing_date = date('Y-m-t',strtotime($start_coverage_period));
            } else {
                $next_billing_date = date('Y-m-'.$billing_day,strtotime($start_coverage_period));
            }
          } else {
              $date = new DateTime($start_coverage_period);
              $start_coverage_period = addMonth($date->modify('-1 day'),'1');
              if($billing_day > date('t',strtotime($start_coverage_period))) {
                  $next_billing_date = date('Y-m-t',strtotime($start_coverage_period));
              } else {
                  $next_billing_date = date('Y-m-'.$billing_day,strtotime($start_coverage_period));
              }
          }
          $next_billing_date = date('Y-m-d',strtotime($next_billing_date));
          $member_payment_type = $wsRes['$member_payment_type'];
        }
      }
      if($member_payment_type == 'Annually'){
        $temp_date = new DateTime($next_billing_date);
        $next_billing_date = addMonth($temp_date,'11');
      }
      return $next_billing_date;
    }
  //****************** Get Manually Next Billing Date ********************
}
?>
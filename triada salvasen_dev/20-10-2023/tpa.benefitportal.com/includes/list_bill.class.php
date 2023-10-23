<?php

/*
 * Class for product settings
 */

class ListBill
{

  public function get_next_list_bill_generate_date($groupId, $wsId = 0, $displayDate = true, $extra = array())
  {
    global $pdo;
    $response = array();
    require_once dirname(__DIR__) . '/includes/function.class.php';
    $function_list = new functionsList();
    
    $today = (!empty($extra['type']) && $extra['type'] == "manual" ) ? date('Y-m-d', strtotime($extra['today'])) : date('Y-m-d');
    $fromDate = (!empty($extra['fromDate']) ? date('Y-m-d', strtotime($extra['fromDate'])) : $today);
    $effectiveDate = '';
    $listBillPayDate = '';
    $listBillDate = '';

    if (!empty($wsId)) {
      $wsSql = "SELECT eligibility_date FROM website_subscriptions where id=:subscription_id";
      $wsParam = array(
        ':subscription_id' => $wsId,
      );
      $wsResult = $pdo->selectOne($wsSql, $wsParam);
      $effectiveDate = $wsResult['eligibility_date'];
    }
   
    $variationRuleSql = "SELECT billing_setting,days_prior_pay_period,auto_set_payment_received FROM  list_bill_options WHERE group_id=:groupId AND rule_type='Variation' AND is_deleted='N'";

    $variationRuleParam = array(
      ':groupId' => $groupId,
    );
    $variationRuleRes = $pdo->selectOne($variationRuleSql, $variationRuleParam);
    if (!empty($variationRuleRes)) {
      
      $priorDays = $variationRuleRes['days_prior_pay_period'];
      $incr = '';

      $listBillDayParam = array(
        ':groupId' => $groupId,
      );

      if (!empty($wsId)) {
        $classSql = "SELECT cs.class_id FROM website_subscriptions ws 
                        JOIN customer_settings cs ON (cs.customer_id=ws.customer_id) where ws.id=:subscription_id";
        $classParam = array(
          ':subscription_id' => $wsId,
        );
        $classRes = $pdo->selectOne($classSql, $classParam);
        
        if (!empty($classRes)) {
          $incr .= ' AND class_id=:class_id';
          $listBillDayParam[':class_id'] = $classRes['class_id'];
        }
      }
     
      if (!empty($effectiveDate) && strtotime($effectiveDate) > strtotime($fromDate)) {
        $incr .= " AND paydate >='" . $effectiveDate . "'";
      } else if ( !empty($effectiveDate) && strtotime($effectiveDate) <= strtotime($fromDate) ) {
        $incr .= " AND paydate >'" . $fromDate . "'";
      } else {
        $incr .= " AND paydate >='" . $fromDate . "'";
      }

      $listBillDaySql="SELECT paydate as list_bill_date 
                      FROM group_classes_paydates 
                      WHERE group_id=:groupId AND is_deleted='N' 
                      $incr ORDER BY paydate";

      $nextListBillDate = $pdo->selectOne($listBillDaySql, $listBillDayParam);
      if(!empty($nextListBillDate['list_bill_date'])){
        $listBillPayDate = $nextListBillDate['list_bill_date'];
        $listBillDate = $function_list->getWorkingPriorDay($listBillPayDate,$priorDays);
      }

    } else {

      $globalRuleSql = "SELECT billing_setting,days_prior_pay_period,auto_set_payment_received FROM  list_bill_options WHERE rule_type='global' AND is_deleted='N'";

      $globalRuleRes = $pdo->selectOne($globalRuleSql);
      
      $priorDays = $globalRuleRes['days_prior_pay_period'];
      $incr = '';

      $listBillDayParam = array(
        ':groupId' => $groupId,
      );

      if (!empty($wsId)) {
        $classSql = "SELECT cs.class_id FROM website_subscriptions ws 
                        JOIN customer_settings cs ON (cs.customer_id=ws.customer_id) where ws.id=:subscription_id";
        $classParam = array(
          ':subscription_id' => $wsId,
        );
        $classRes = $pdo->selectOne($classSql, $classParam);
      
        if (!empty($classRes)) {
          $incr .= ' AND class_id=:class_id';
          $listBillDayParam[':class_id'] = $classRes['class_id'];
        }
      }
      
      if (!empty($effectiveDate) && strtotime($effectiveDate) > strtotime($fromDate)) {
        $incr .= " AND paydate >='" . $effectiveDate . "'";
      } else if ( !empty($effectiveDate) && strtotime($effectiveDate) <= strtotime($fromDate) ) {
        $incr .= " AND paydate >'" . $fromDate . "'";
      } else {
        $incr .= " AND paydate >='" . $fromDate . "'";
      }
      
      $listBillDaySql="SELECT paydate as list_bill_date 
                      FROM group_classes_paydates 
                      WHERE group_id=:groupId AND is_deleted='N'
                      $incr ORDER BY paydate";
      $nextListBillDate = $pdo->selectOne($listBillDaySql, $listBillDayParam);
      if(!empty($nextListBillDate['list_bill_date'])){
        $listBillPayDate = $nextListBillDate['list_bill_date'];
        $listBillDate = $function_list->getWorkingPriorDay($listBillPayDate,$priorDays);
      }
    }

    if(!empty($listBillDate) && strtotime($listBillDate) < strtotime($today)){
      $extra["fromDate"] = date('Y-m-d',strtotime('+1 day',strtotime($listBillPayDate)));
      $extra["loop"] = !empty($extra["loop"]) ? $extra["loop"] + 1 : 1;
      // Max 10 times loop will run
      if(!empty($extra["loop"]) && $extra["loop"] <= 10){
        return $this->get_next_list_bill_generate_date($groupId,$wsId,$displayDate,$extra);
      }
    }
    if ($displayDate == true) {
      $response["listBillPayDate"] = displayDate($listBillPayDate);
      $response["listBillDate"] = displayDate($listBillDate);
    } else {
      $response["listBillPayDate"] = $listBillPayDate;
      $response["listBillDate"] = $listBillDate;
    }
    return $response;
  }

  public function get_list_bill_global_day()
  {
    global $pdo;
    $globalBillDaySql = "SELECT days_prior_pay_period FROM list_bill_options WHERE rule_type='Global' AND is_deleted='N'";
    $globalListBill = $pdo->selectOne($globalBillDaySql);
    $globalBillGenerateDay = $globalListBill['days_prior_pay_period'];
    return $globalBillGenerateDay;
  }

  public function adjust_holidays($nextBillGenerateDate)
  {
    $publicHolidaysArr = array();
    $publicHolidaysArr = fetch_public_holidays($nextBillGenerateDate);

    if (in_array($nextBillGenerateDate, $publicHolidaysArr)) {
      $nextBillGenerateDate = date('Y-m-d', strtotime('-1 day', strtotime($nextBillGenerateDate)));
    }

    $day = date('D', strtotime($nextBillGenerateDate));
    if ($day == 'Sat') {
      $nextBillGenerateDate = date('Y-m-d', strtotime('-1 day', strtotime($nextBillGenerateDate)));
    } else if ($day == 'Sun') {
      $nextBillGenerateDate = date('Y-m-d', strtotime('-2 day', strtotime($nextBillGenerateDate)));
    }

    return $nextBillGenerateDate;
  }

  public function get_plan_pay_period_price($productPrice, $payPrd = 'Monthly')
  {
    $amount = $productPrice;
    if ($payPrd == 'Semi-Monthly') {
      $amount = round(($productPrice * 12) / 24, 2);
    } else if ($payPrd == 'Weekly') {
      $amount = round(($productPrice * 12) / 52, 2);
    } else if ($payPrd == 'Bi-Weekly') {
      $amount = round(($productPrice * 12) / 26, 2);
    }
    return $amount;
  }

  public function get_list_bill_coverage_date($baseDate, $payPrd = 'Monthly', $group_id)
  {
    global $pdo;
    //**Base date is date on which we decide list bill start date */

    $coverageArr = array();
    if ($payPrd == 'Monthly') {
      $date = new DateTime($baseDate);
      $coverageArr['list_bill_coverage_start_date'] = date('Y-m-01', strtotime($baseDate)); // month first day
      $coverageArr['list_bill_coverage_end_date'] = date('Y-m-t', strtotime($baseDate)); // month last day
    } else if ($payPrd == 'Semi-Monthly') {
      $selectedPayDay = date('d', strtotime($baseDate));
      if ($selectedPayDay < 16) {
        $coverageArr['list_bill_coverage_start_date'] =  date('Y-m-01', strtotime($baseDate));
        $coverageArr['list_bill_coverage_end_date'] =  date('Y-m-15', strtotime($baseDate));
      } else {
        $coverageArr['list_bill_coverage_start_date'] = date('Y-m-16', strtotime($baseDate));
        $coverageArr['list_bill_coverage_end_date'] = date('Y-m-t', strtotime($baseDate));
      }
    } else if ($payPrd == 'Weekly') {
      $coverageArr['list_bill_coverage_start_date'] = date('Y-m-d', strtotime("monday this week", strtotime($baseDate)));
      $coverageArr['list_bill_coverage_end_date'] = date('Y-m-d', strtotime("+6 Days", strtotime($coverageArr['list_bill_coverage_start_date'])));
    } else if ($payPrd == 'Bi-Weekly') {
      $firstDateRes = $pdo->select("SELECT paydate FROM group_classes_paydates WHERE group_id=:group_id AND is_deleted='N' LIMIT 2",array(":group_id" => $group_id));
      if(!empty($firstDateRes) && in_array($baseDate,array_column($firstDateRes, 'paydate'))){
        if($baseDate == $firstDateRes[0]['paydate']){
          $coverageArr['list_bill_coverage_start_date'] = date('Y-m-01', strtotime($baseDate));
        }else if($baseDate == $firstDateRes[1]['paydate']){
          $coverageArr['list_bill_coverage_start_date'] = date('Y-m-15', strtotime($baseDate));
        }
      }else{
        $coverageArr['list_bill_coverage_start_date'] = date('Y-m-d',strtotime("+1 day",strtotime($baseDate)));
      }
      $coverageArr['list_bill_coverage_end_date'] = date('Y-m-d', strtotime("+13 Days", strtotime($coverageArr['list_bill_coverage_start_date'])));
    }

    return $coverageArr;
  }

  public function get_pay_period_type($ws_id){
    global $pdo;
    $pay_period='Monthly';
    $ws_sql = "SELECT ws.id as id,gc.pay_period
    FROM website_subscriptions ws
    JOIN customer c ON(c.id = ws.customer_id)
    JOIN customer_settings cs ON (c.id=cs.customer_id)
    JOIN group_classes gc ON (gc.id=cs.class_id AND gc.group_id=c.sponsor_id AND gc.is_deleted='N')
    WHERE ws.id=:id";
    $ws_row = $pdo->selectOne($ws_sql, array(":id" => $ws_id));
    if (!empty($ws_row['id'])) {
      $pay_period = $ws_row['pay_period'];
    }

    return $pay_period;
  }

  public function getListBillId(){
    global $pdo;
    
    $list_bill_id = rand(1000000,9999999);
    $list_bill_id = "LIST-" . $list_bill_id;
    
    $sql = "SELECT list_bill_no FROM list_bills WHERE list_bill_no='" . $list_bill_id . "'";
    $res = $pdo->selectOne($sql);

    if (!empty($res)) {
      return $this->getListBillId();
    } else {
      return $list_bill_id;
    }
  }

  public function getListBillPaymentChargePrd(){
      global $pdo;

      $sql = "SELECT id as product_id,product_code,name as product_name,product_type FROM prd_main WHERE is_deleted='N' AND product_type='ListBillServiceFee' ORDER BY id DESC";
      $res = $pdo->selectOne($sql);
      if (!empty($res)) {
          return $res;
      } else {
          $prd_data = array(
              'record_type' => 'Primary',
              'product_code' => 'LB44484',
              'name' => 'Service Fee',
              'type' => 'Fees',
              'fee_type' => 'Display Only',
              'product_type' => 'ListBillServiceFee',
          );
          $pdo->insert('prd_main',$prd_data);
          return $this->getListBillPaymentChargePrd();
      }
  }

  public function updateSubscriptionData($list_bill_id){
    global $pdo;
    require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    $enrollDate = new enrollmentDate();

    $list_bill_res = $pdo->selectOne("SELECT * FROM list_bills WHERE id = :id",array(":id" => $list_bill_id));
    
    if(!empty($list_bill_res)){
      $list_bill_details_res = $pdo->select("SELECT * FROM list_bill_details WHERE list_bill_id = :list_bill_id", array(":list_bill_id" => $list_bill_id));
      
      if(!empty($list_bill_details_res)){
        $order_id = 0;
        $order_details_res = $pdo->selectOne("SELECT order_id FROM order_details WHERE list_bill_id = :list_bill_id AND is_list_bill = 'Y' AND is_deleted='N'", array(":list_bill_id" => $list_bill_id));
        if(!empty($order_details_res)){
          $order_id = $order_details_res['order_id'];
        }
        foreach ($list_bill_details_res as $key => $value) {
          $website_subscription_update_data = array(
            'last_order_id' => $order_id,
            'total_attempts' => 0,
            'next_attempt_at' => NULL,
            'last_purchase_date' => 'msqlfunc_NOW()',
            'status' => 'Active',
            'next_purchase_date' => date("Y-m-d",strtotime("+1 month",strtotime(date('Y-m-01')))),
            'renew_count' => 'msqlfunc_renew_count + 1',
          );

          if(strtotime($list_bill_res['time_period_start_date']) >= strtotime($value['start_coverage_date'])){
            if(date('d',strtotime($list_bill_res['time_period_start_date'])) == "01") {
              $website_subscription_update_data['start_coverage_period'] = date("Y-m-01",strtotime("+1 month",strtotime(date('Y-m-01',strtotime($list_bill_res['time_period_start_date'])))));
              $website_subscription_update_data['end_coverage_period'] = date("Y-m-t",strtotime("+1 month",strtotime(date('Y-m-01',strtotime($list_bill_res['time_period_start_date'])))));
            } else {
                $start_coverage_period = date('Y-m-d',strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));

                $member_payment_type = getname('prd_main',$value['product_id'],'payment_type_subscription','id');

                $product_dates = $enrollDate->getCoveragePeriod($start_coverage_period,$member_payment_type);

                $website_subscription_update_data['start_coverage_period'] = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                $website_subscription_update_data['end_coverage_period'] = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
            }
          }
          $website_subscription_update_where = array("clause" => 'id=:id', 'params' => array(":id" => $value['ws_id']));
          $pdo->update("website_subscriptions", $website_subscription_update_data, $website_subscription_update_where);
        }
      }
    }

    return $list_bill_id;
  }

  public function updateWebsiteSubscription($groupId, $wsId, $extra)
  {
    global $pdo;
    require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    $enrollDate = new enrollmentDate();
    if(!empty($extra["listBillPayDate"])){
      $extra["fromDate"] = $extra["listBillPayDate"];
    }
    $listBillGenerateRes = $this->get_next_list_bill_generate_date($groupId, $wsId, false,$extra);
    $nextBillDateAfterToday = $listBillGenerateRes["listBillDate"];

    $today = (!empty($extra) && $extra['type'] == 'manual') ? $extra['today'] : date('Y-m-d');

    $website_subscription_update_data=array();
    $website_subscription_update_data['next_purchase_date'] = date('Y-m-d', strtotime($nextBillDateAfterToday));
    $website_subscription_update_data['last_purchase_date'] = 'msqlfunc_NOW()';

    if (strtotime(date('Y-m', strtotime($nextBillDateAfterToday))) > strtotime(date('Y-m', strtotime($today)))) {

      $webSubscptionSql = "SELECT id,start_coverage_period,end_coverage_period,product_id FROM  website_subscriptions WHERE id = :id";
      $webSubscptionParam = array(
        ':id' => $wsId,
      );
      $webSubcptionData = $pdo->selectOne($webSubscptionSql, $webSubscptionParam);
      if (!empty($webSubcptionData)) {
        $member_payment_type = getname('prd_main', $webSubcptionData['product_id'], 'payment_type_subscription', 'id');

        if ($member_payment_type == 'Annually') {
          //update webiste subscription coverage if last bill of annually coverage generated

          $current_coverage_end_date = $webSubcptionData['end_coverage_period'];
          if (strtotime(date('Y-m', strtotime($nextBillDateAfterToday))) > strtotime(date('Y-m', strtotime($current_coverage_end_date)))) {
            $date = date('d', strtotime($webSubcptionData['start_coverage_period']));
            $month = date('m', strtotime($webSubcptionData['start_coverage_period']));
            $Year = date('Y', strtotime($nextBillDateAfterToday));
            $start_coverage_period = date($date . '-' . $month . '-' . $Year);

            $product_dates = $enrollDate->getCoveragePeriod($start_coverage_period, $member_payment_type);
            $website_subscription_update_data['start_coverage_period'] = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
            $website_subscription_update_data['end_coverage_period'] = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
        
          }
        } else {
          //update webiste subscription coverage if last bill is generated of month

          $current_coverage_start_date = $webSubcptionData['start_coverage_period'];

          if (strtotime(date('Y-m', strtotime($nextBillDateAfterToday))) > strtotime(date('Y-m', strtotime($current_coverage_start_date)))) {
            $date = date('d', strtotime($webSubcptionData['start_coverage_period']));
            $month = date('m', strtotime($nextBillDateAfterToday));
            $Year = date('Y', strtotime($nextBillDateAfterToday));
            $start_coverage_period = date($date . '-' . $month . '-' . $Year);
            $product_dates = $enrollDate->getCoveragePeriod($start_coverage_period, $member_payment_type);

            $website_subscription_update_data['start_coverage_period'] = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
            $website_subscription_update_data['end_coverage_period'] = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
    
    
          }
        }
      }
    }

    $website_subscription_update_where = array("clause" => 'id=:id', 'params' => array(":id" => $wsId));
    $pdo->update("website_subscriptions", $website_subscription_update_data, $website_subscription_update_where);


  }

  public function manageListBillCategoryDetails($params){

    global $pdo;
    $detailSql = "SELECT * FROM list_bill_category_details WHERE customer_id = :customer_id AND list_bill_id = :list_bill_id AND group_id = :group_id AND start_coverage_date = :start_coverage_date AND transaction_type = :transaction_type AND group_company_id = :group_company_id AND FIND_IN_SET(:list_bill_detail_id,list_bill_details_ids)";

    $detailWhr=array(
      ":customer_id" => $params['customer_id'],
      ":list_bill_id" => $params['list_bill_id'],
      ":group_id" => $params['group_id'],
      ":start_coverage_date" => $params['start_coverage_date'],
      ":group_company_id" => $params['group_company_id'],
      ":transaction_type" => $params['transaction_type'],
      ":list_bill_detail_id" => $params['details_id']
    );
    $list_bill_category_details_res = $pdo->selectOne($detailSql,$detailWhr);

    if(!empty($list_bill_category_details_res)){
      $category_total = $list_bill_category_details_res['category_total'] + $params['category_total'];
      $employee_total = $list_bill_category_details_res['employee_total'] + $params['employee_total'];
      $employer_total = $list_bill_category_details_res['employer_total'] + $params['employer_total'];
      $list_bill_details_ids_str = implode(",", array($list_bill_category_details_res['list_bill_details_ids'],$params['details_id']));
      $website_ids_str = implode(",", array($list_bill_category_details_res['website_ids'],$params['ws_id']));
      $update_param = array(
        'list_bill_details_ids' => $list_bill_details_ids_str,
        'website_ids' => $website_ids_str,
        'category_total' => $category_total,
        'employee_total' => $employee_total,
        'employer_total' => $employer_total,
      );
      $update_where = array(
        "clause" => "id=:id",
        "params" => array(
          ":id" => $list_bill_category_details_res['id'],
        ),
      );
      $pdo->update("list_bill_category_details", $update_param, $update_where);
      
    } else {
      $insert_param = array(
        'customer_id' => $params['customer_id'],
        'group_id' => $params['group_id'],
        'group_company_id' => $params['group_company_id'],
        'list_bill_id' => $params['list_bill_id'],
        'category_id' => $params['category_id'],
        'list_bill_details_ids' => $params['details_id'],
        'website_ids' => $params['ws_id'],
        'category_total' => $params['category_total'],
        'employee_total' => $params['employee_total'],
        'employer_total' => $params['employer_total'],
        'transaction_type' => $params['transaction_type'],
        'start_coverage_date' => $params['start_coverage_date'],
        'end_coverage_date' => $params['end_coverage_date'],
        'is_cobra_coverage' => $params['is_cobra_coverage'],
      );
      $pdo->insert("list_bill_category_details",$insert_param);
    }
  }

  public function listBillCategoryDetailsInsert($list_bill_id){

    global $pdo;
    $listBillCategoryDetailsInsert = array();
    $list_bill_response = $pdo->select("SELECT * FROM list_bills WHERE id = :id",array(":id" => $list_bill_id));

    if(!empty($list_bill_response)){
      foreach ($list_bill_response as $key => $value) {
        $list_bill_detail_sql = "SELECT lbd.*,w.group_price,w.member_price,w.price,p.category_id,c.group_company_id 
            FROM list_bill_details as lbd 
            JOIN website_subscriptions as w ON (w.id=lbd.ws_id)
            JOIN customer as c ON (c.id = w.customer_id)
            JOIN prd_main as p ON (p.id=w.product_id)
            WHERE list_bill_id = :list_bill_id";

        $list_bill_detail_res = $pdo->select($list_bill_detail_sql,array(":list_bill_id" => $value['id']));
        if(!empty($list_bill_detail_res)){
          foreach ($list_bill_detail_res as $index => $element) {
            $params = array(
              'customer_id' => $element['customer_id'],
              'group_id' => $value['customer_id'],
              'group_company_id' => $element['group_company_id'],
              'list_bill_id' => $value['id'],
              'category_id' => $element['category_id'],
              'category_total' => $element['price'],
              'employee_total' => $element['member_price'],
              'employer_total' => $element['group_price'],
              'transaction_type' => 'charged',
              'start_coverage_date' => $element['start_coverage_date'],
              'end_coverage_date' => $element['end_coverage_date'],
              'is_cobra_coverage' => $element['is_cobra_coverage'],
              'details_id' => $element['id'],
              'ws_id' => $element['ws_id'],
            );
            $categoryDetailID = $this->manageListBillCategoryDetails($params);
            array_push($listBillCategoryDetailsInsert, $categoryDetailID);
          }
        }
        
        $group_member_refund_charge_res = $pdo->select("SELECT gmrc.transaction_type,gmrc.transaction_amount,w.id as ws_id,w.group_price,w.member_price,w.price,p.category_id,lbd.id as details_id,gmrc.old_ws_id,w.customer_id,c.group_company_id,lbd.start_coverage_date,lbd.end_coverage_date,lbd.is_cobra_coverage
          FROM group_member_refund_charge gmrc 
          JOIN website_subscriptions as w ON(w.id = gmrc.ws_id) 
          JOIN customer as c ON (c.id = w.customer_id)
          JOIN prd_main p ON(p.id = w.product_id) 
          JOIN list_bill_details as lbd ON (FIND_IN_SET(lbd.list_bill_id,gmrc.payment_received_from) AND FIND_IN_SET(lbd.id,gmrc.payment_received_details_id) AND lbd.customer_id = w.customer_id AND (lbd.ws_id = gmrc.ws_id OR lbd.ws_id = gmrc.old_ws_id)) 
          WHERE is_applied_to_list_bill = 'Y' AND gmrc.list_bill_id = :list_bill_id",array(":list_bill_id" => $value['id']));
        if(count($group_member_refund_charge_res) > 0){
          foreach ($group_member_refund_charge_res as $index => $element) {
            if($element['transaction_type'] == 'charged'){
              if($element['old_ws_id'] > 0){
                $old_website_subcription_sql = "SELECT p.category_id,w.price,w.member_price,w.group_price,w.customer_id,c.group_company_id,w.id as ws_id,w.is_cobra_coverage
                  FROM website_subscriptions as w 
                  JOIN customer c ON(c.id = w.customer_id) 
                  JOIN prd_main p ON(p.id = w.product_id) WHERE w.id = :old_ws_id";
                $old_website_subcription_res = $pdo->selectOne($old_website_subcription_sql, array(":old_ws_id" => $element['old_ws_id']));
                if(!empty($old_website_subcription_res)){
                  $params = array(
                    'customer_id' => $old_website_subcription_res['customer_id'],
                    'group_id' => $value['customer_id'],
                    'group_company_id' => $old_website_subcription_res['group_company_id'],
                    'list_bill_id' => $value['id'],
                    'category_id' => $old_website_subcription_res['category_id'],
                    'category_total' => $old_website_subcription_res['price'],
                    'employee_total' => $old_website_subcription_res['member_price'],
                    'employer_total' => $old_website_subcription_res['group_price'],
                    'transaction_type' => 'refund',
                    'start_coverage_date' => $element['start_coverage_date'],
                    'end_coverage_date' => $element['end_coverage_date'],
                    'details_id' => $element['details_id'],
                    'is_cobra_coverage' => $old_website_subcription_res['is_cobra_coverage'],
                    'ws_id' => $old_website_subcription_res['ws_id'],
                  );
                  $categoryDetailID = $this->manageListBillCategoryDetails($params);
                  array_push($listBillCategoryDetailsInsert, $categoryDetailID);
                }
              } else {
                $params = array(
                  'customer_id' => $element['customer_id'],
                  'group_id' => $value['customer_id'],
                  'group_company_id' => $element['group_company_id'],
                  'list_bill_id' => $value['id'],
                  'category_id' => $element['category_id'],
                  'category_total' => $element['price'],
                  'employee_total' => $element['member_price'],
                  'employer_total' => $element['group_price'],
                  'transaction_type' => 'charged',
                  'start_coverage_date' => $element['start_coverage_date'],
                  'end_coverage_date' => $element['end_coverage_date'],
                  'is_cobra_coverage' => $element['is_cobra_coverage'],
                  'details_id' => $element['details_id'],
                  'ws_id' => $element['ws_id'],
                );
                $categoryDetailID = $this->manageListBillCategoryDetails($params);
                array_push($listBillCategoryDetailsInsert, $categoryDetailID);
              }
            } else {
              if($element['old_ws_id'] > 0){
                $old_website_subcription_sql = "SELECT p.category_id,w.price,w.member_price,w.group_price,w.customer_id,c.group_company_id,w.id as ws_id,w.is_cobra_coverage
                  FROM website_subscriptions as w 
                  JOIN customer c ON(c.id = w.customer_id) 
                  JOIN prd_main p ON(p.id = w.product_id) WHERE w.id = :old_ws_id";
                $old_website_subcription_res = $pdo->selectOne($old_website_subcription_sql, array(":old_ws_id" => $element['old_ws_id']));
                if(!empty($old_website_subcription_res)){
                  $params = array(
                    'customer_id' => $old_website_subcription_res['customer_id'],
                    'group_id' => $value['customer_id'],
                    'group_company_id' => $old_website_subcription_res['group_company_id'],
                    'list_bill_id' => $value['id'],
                    'category_id' => $old_website_subcription_res['category_id'],
                    'category_total' => $old_website_subcription_res['price'],
                    'employee_total' => $old_website_subcription_res['member_price'],
                    'employer_total' => $old_website_subcription_res['group_price'],
                    'transaction_type' => 'refund',
                    'start_coverage_date' => $element['start_coverage_date'],
                    'end_coverage_date' => $element['end_coverage_date'],
                    'details_id' => $element['details_id'],
                    'is_cobra_coverage' => $old_website_subcription_res['is_cobra_coverage'],
                    'ws_id' => $old_website_subcription_res['ws_id'],
                  );
                  $categoryDetailID = $this->manageListBillCategoryDetails($params);
                  array_push($listBillCategoryDetailsInsert, $categoryDetailID);
                }
              } else {
                $params = array(
                  'customer_id' => $element['customer_id'],
                  'group_id' => $value['customer_id'],
                  'group_company_id' => $element['group_company_id'],
                  'list_bill_id' => $value['id'],
                  'category_id' => $element['category_id'],
                  'category_total' => $element['price'],
                  'employee_total' => $element['member_price'],
                  'employer_total' => $element['group_price'],
                  'transaction_type' => 'refund',
                  'start_coverage_date' => $element['start_coverage_date'],
                  'end_coverage_date' => $element['end_coverage_date'],
                  'is_cobra_coverage' => $element['is_cobra_coverage'],
                  'details_id' => $element['details_id'],
                  'ws_id' => $element['ws_id'],
                );
                $categoryDetailID = $this->manageListBillCategoryDetails($params); 
                array_push($listBillCategoryDetailsInsert, $categoryDetailID);
              }
            }
          }
        }
      }
    }
    return $listBillCategoryDetailsInsert;
  }

  public function pay_list_bill($list_bill_id,$billing_profile,$location,$other_params = array()) {
      global $pdo,$CREDIT_CARD_ENC_KEY,$SITE_ENV,$ADMIN_HOST,$GROUP_HOST,$LIST_BILL_PAYMENT_FILES_DIR;
      require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
      require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
      require_once dirname(__DIR__) . '/includes/function.class.php';
      $function_list = new functionsList();
      $enrollDate = new enrollmentDate();
      $response = array();

      $REAL_IP_ADDRESS = get_real_ipaddress();
      if ($location == "auto_payment_list_bill_cron") {
        $BROWSER = 'auto_list_bill_payment.php';
        $OS = 'auto_list_bill_payment.php';
        $REQ_URL = 'auto_list_bill_payment.php';
      }else {
        $BROWSER = getBrowser();
        $OS = getOS($_SERVER['HTTP_USER_AGENT']);
        $REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      }

      if(isset($_REQUEST['debug'])) {
          pre_print($list_bill_id,false);
      }
      $list_bill_sql = "SELECT lb.id,lb.status,lb.customer_id,lb.company_id,lb.class_id,lb.time_period_start_date,lb.time_period_end_date,lb.due_amount,lb.total_attempts,lb.list_bill_no,lb.admin_fee,lb.past_due_amount
                        FROM list_bills lb 
                        WHERE lb.status IN('open','paid') AND lb.id=:list_bill_id";
      $list_bill_where = array(':list_bill_id' => $list_bill_id);
      $list_bill_row = $pdo->selectOne($list_bill_sql, $list_bill_where);

      if(empty($list_bill_row)) {
          return array('status' => 'fail','error_code' => 'general_error','message' => "List Bill Not Found");  

      } elseif($list_bill_row['status'] == "paid") {
          return array('status' => 'fail','error_code' => 'general_error','message' => "List Bill Already Paid");  
      }

      $list_bill_admin_fee = 0;
      $customer_id = $list_bill_row['customer_id'];
      $company_id = $list_bill_row['company_id'];
      $class_id= $list_bill_row['class_id'];

      /*---------- Group Detail -----------*/
        $group_sql = "SELECT id,rep_id,fname,lname,email,cell_phone,sponsor_id,address,city,state,zip FROM customer WHERE id =:id";
        $group_row = $pdo->selectOne($group_sql, array(":id" => $customer_id));
        $sponsor_id = $group_row['sponsor_id'];
      /*---------- Group Detail -----------*/

      /*---------- Billing Date -----------*/
        if($billing_profile == "record_check_payment") {
            $payment_mode = 'Check';
        } else {
            $cb_sql = "SELECT *,
                  AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                  AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                  AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as card_no_full
                  FROM customer_billing_profile WHERE id=:id";
            $cb_row = $pdo->selectOne($cb_sql,array(':id'=>$billing_profile));
            if(empty($cb_row)) {
                return array('status' => 'fail','error_code' => 'general_error','message' => "Billing Profile Not Found");  
            }
            $payment_mode = $cb_row['payment_mode'];
        }
        
        $bill_address = !empty($cb_row['address'])?$cb_row['address']:$group_row['address'];
        $bill_city = !empty($cb_row['city'])?$cb_row['city']:$group_row['city'];
        $bill_state = !empty($cb_row['state'])?$cb_row['state']:$group_row['state'];
        $bill_zip = !empty($cb_row['zip'])?$cb_row['zip']:$group_row['zip'];
        $bill_country = 231;

        if($payment_mode=='CC'){
            $name_on_card = $cb_row['fname'];
            $card_number = $cb_row['card_no_full'];
            $card_type = $cb_row['card_type'];
            $expiry_month = $cb_row['expiry_month'];
            $expiry_year = $cb_row['expiry_year'];
            $cvv_no = $cb_row['cvv_no'];

        } else if($payment_mode=='ACH'){
            $ach_bill_fname = $cb_row['fname'];
            $ach_bill_lname = $cb_row['lname'];
            $bankname = $cb_row['bankname'];
            $ach_account_type = $cb_row['ach_account_type'];
            $routing_number = $cb_row['ach_routing_number'];
            $account_number = $cb_row['ach_account_number'];

        }  else if($payment_mode=='Check'){
            $ach_bill_fname = $group_row['fname'];
            $ach_bill_lname = $group_row['lname'];

        }
      /*----------/Billing Date -----------*/

      /*-------- Get Order Count ---------*/
        $sch_params = array();
        $incr = '';

        $incr .= " AND customer_id=:customer_id";
        $sch_params[':customer_id'] = $customer_id;

        if($company_id > 0) {
            $incr .= " AND company_id=:company_id";
            $sch_params[':company_id'] = $company_id;
        }

        if(!empty($class_id)){
          $incr .= " AND class_id=:class_id";
          $sch_params[':class_id'] = $class_id;
        }

        $ord_count_sql = "SELECT COUNT(id) as total_order FROM list_bills WHERE is_deleted='N' AND status IN('paid') $incr";
        $ord_count_row = $pdo->selectOne($ord_count_sql,$sch_params);
        if(!empty($ord_count_row['total_order'])) {
            $order_count = $ord_count_row['total_order'] + 1;
        } else {
            $order_count = 1;
        }
      /*--------/Get Order Count ---------*/

      /************* Fetch List Bill Detail Rows **************/
        $list_bill_admin_fee=0;
        $lbd_res=array();

        /************** Past Forwarded Bill Details Rows Code start *************/
        if($list_bill_row['past_due_amount']>0){

          $membersDetailsSql="SELECT DISTINCT lbd.customer_id from list_bill_details lbd 
                              JOIN list_bills lb ON (lbd.list_bill_id = lb.id) Where lb.id=:id";
          $membersDetailsParam=array(
            ':id'=>$list_bill_id
          );
          $details_res = $pdo->select($membersDetailsSql,$membersDetailsParam);

          if(!empty($details_res)) {
                $membersIdStr=implode(',',array_column($details_res,'customer_id'));                          
                $ListBillDetailsSql="SELECT lbd.id as list_bill_detail_id,lbd.ws_id as website_id,lbd.list_bill_id,ws.product_id,ws.fee_applied_for_product,ws.plan_id,ws.prd_plan_type_id,ws.product_type,pm.name as product_name,ws.product_code,lbd.rate as unit_price,lbd.quantity as qty,ws.member_price,ws.group_price,ws.contribution_type,ws.contribution_value,lbd.start_coverage_date,lbd.end_coverage_date ,ws.eligibility_date,ws.renew_count,lbd.transaction_type,pm.payment_type_subscription as member_payment_type,ws.customer_id
                FROM list_bill_details lbd
                JOIN list_bills lb ON( lbd.list_bill_id = lb.id  AND status NOT IN ('Regenerate') )
                JOIN website_subscriptions ws ON (ws.id=lbd.ws_id)
                JOIN prd_main pm ON (pm.id=ws.product_id)
                LEFT JOIN 
                (
                  SELECT MAX(tlb.id) AS customer_last_bill_paid_id,tlbd.customer_id FROM list_bill_details tlbd
                  JOIN list_bills tlb ON( tlbd.list_bill_id = tlb.id AND tlb.status IN ('paid') AND tlb.is_deleted='N')
                  WHERE  tlbd.is_reverse='N' AND tlbd.refund_charge_id IS NULL AND tlbd.customer_id IN ( $membersIdStr )
                  GROUP BY tlbd.customer_id
                ) AS pbd ON (pbd.customer_id=lbd.customer_id)
                WHERE lbd.is_reverse='N' AND  lbd.customer_id IN ( $membersIdStr ) AND 
                ( lbd.list_bill_id>IFNULL(pbd.customer_last_bill_paid_id,0)  
                  OR  
                  FIND_IN_SET(lbd.id,  
                    IFNULL(
                        (
                        SELECT GROUP_CONCAT(list_bill_details_id) FROM list_bill_amendment 
                        WHERE  list_bill_id = IFNULL(pbd.customer_last_bill_paid_id,0) AND amount>0 and is_deleted='N'
                        )
                    ,0)
                  )
                ) AND lbd.list_bill_id != :current_list_bill_id ORDER BY lbd.id ASC";
                $listBillDetailParam=array(
                  ':current_list_bill_id'=>$list_bill_id
                );
                $details_res = $pdo->select($ListBillDetailsSql,$listBillDetailParam);
                $lbd_res = (!empty($details_res))?array_merge($lbd_res,$details_res):$lbd_res;
          }

        }
        /************** Past Forwarded Bill Details Rows Code start **************/

        /************** Current Bill Details Rows Code start *************/
        $billDetailsSql="SELECT lbd.id as list_bill_detail_id,lbd.ws_id as website_id,lbd.list_bill_id,ws.product_id,ws.fee_applied_for_product,ws.plan_id,ws.prd_plan_type_id,ws.product_type,pm.name as product_name,ws.product_code,lbd.rate as unit_price,lbd.quantity as qty,ws.member_price,ws.group_price,ws.contribution_type,ws.contribution_value,lbd.start_coverage_date,lbd.end_coverage_date,ws.eligibility_date,ws.renew_count,lbd.transaction_type,pm.payment_type_subscription as member_payment_type,ws.customer_id
        FROM list_bill_details lbd
        JOIN website_subscriptions ws ON (ws.id=lbd.ws_id)
        JOIN prd_main pm ON (pm.id=ws.product_id)
        WHERE lbd.is_reverse='N' AND  lbd.list_bill_id=:list_bill_id AND NOT FIND_IN_SET (lbd.id,
          IFNULL( (
          SELECT GROUP_CONCAT(list_bill_details_id) from list_bill_amendment where list_bill_id=:list_bill_id and is_deleted='N' and amount>0
          ) , 0 )
        )
        ORDER BY lbd.id ASC";
        $billDetailParam=array(
          ':list_bill_id'=>$list_bill_id,
        );
        $detailsRes = $pdo->select($billDetailsSql,$billDetailParam);
        $lbd_res = (!empty($detailsRes))?array_merge($lbd_res,$detailsRes):$lbd_res;
        /************** Current Bill Details Rows Code End *************/
      
      /************* Fetch List Bill Detail Rows **************/
      
      $plan_id_arr = array();
      foreach ($lbd_res as $key => $lbd_row) {
        $plan_id_arr = array_merge($plan_id_arr,explode(',',$lbd_row['plan_id']));
      }
      
      /*---- Get Merchant Processor ----*/
        $due_amount = $list_bill_row['due_amount'];
        if($payment_mode == 'Check'){
          $payment_master_id = 0;   
        } else {
            $plan_id_arr = array_unique($plan_id_arr);
            $payment_master_id = $function_list->get_agent_merchant_detail($plan_id_arr,$sponsor_id,$payment_mode,array('is_renewal' => 'L','customer_id' => $customer_id));
            if(isset($_REQUEST['debug'])) {
                pre_print('payment_master_id',false);
                pre_print($payment_master_id,false);
                pre_print('plan_id_arr',false);
                pre_print($plan_id_arr,false);
            }
        }
      /*----/Get Merchant Processor ----*/

      /*---- Get Declined Order If Exists ----*/
        $order_sql = "SELECT id,display_id FROM orders WHERE list_bill_id=:list_bill_id AND status IN('Payment Declined')";
        $order_row = $pdo->selectOne($order_sql, array(":list_bill_id" => $list_bill_id));
        if(!empty($order_row)) {
            $order_id = $order_row['id'];
            $order_display_id = $order_row['display_id'];
        } else {
            $order_id =  0;
            $order_display_id = $function_list->get_order_id();
        }
      /*----/Get Declined Order If Exists ----*/

      /*---- Take Payment ----*/
        $payment_processor = "";
        $decline_log_id="";
        $paymentApproved = false;
        if ($payment_mode == "Check") {
            $paymentApproved = true;
            $txn_id = (isset($other_params['check_number'])?$other_params['check_number']:0);
            $payment_res = array("status" => "Success","transaction_id" => $txn_id,"message" => "Record Payment (Check)");
        } else {
            if(!empty($payment_master_id)){
                $payment_processor= getname('payment_master',$payment_master_id,'processor_id');
            }

            $api = new CyberxPaymentAPI();
            $cc_params = array();
            $cc_params['customer_id'] = $group_row['rep_id'];
            $cc_params['order_id'] = $order_display_id;
            $cc_params['amount'] = $due_amount;
            $cc_params['description'] = "List Bill Order";
            $cc_params['firstname'] = ($payment_mode == 'CC' ? $name_on_card : $ach_bill_fname);
            $cc_params['lastname'] = ($payment_mode == 'CC' ? '' : $ach_bill_lname);
            $cc_params['address1'] = $bill_address;
            $cc_params['city'] = $bill_city;
            $cc_params['state'] = $bill_state;
            $cc_params['zip'] = $bill_zip;
            $cc_params['country'] = $bill_country;
            $cc_params['phone'] = $group_row['cell_phone'];
            $cc_params['email'] = $group_row['email'];
            $cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
            $cc_params['processor'] = $payment_processor;

            if ($payment_mode == "ACH") {
                $cc_params['ach_account_type'] = $ach_account_type;
                $cc_params['ach_routing_number'] = $routing_number;
                $cc_params['ach_account_number'] = $account_number;
                $cc_params['name_on_account'] = $ach_bill_fname . ' ' . $ach_bill_lname;
                $cc_params['bankname'] = $bankname;

                $payment_res = $api->processPaymentACH($cc_params, $payment_master_id);

                if ($payment_res['status'] == 'Success') {
                    $paymentApproved = true;
                    $txn_id = $payment_res['transaction_id'];
                } else {
                    $paymentApproved = false;
                    $txn_id = $payment_res['transaction_id'];
                    $payment_error = $payment_res['message'];
                    $cc_params['order_type'] = 'List Bill Payment';
                    $cc_params['browser'] = $BROWSER;
                    $cc_params['os'] = $OS;
                    $cc_params['req_url'] = $REQ_URL;
                    $cc_params['err_text'] = $payment_error;
                    $decline_log_id = $function_list->credit_card_decline_log($group_row['id'],$cc_params,$payment_res);
                }
            } elseif ($payment_mode == "CC") {
                $cc_params['ccnumber'] = $card_number;
                $cc_params['card_type'] = $card_type;
                $cc_params['ccexp'] = str_pad($expiry_month, 2, "0", STR_PAD_LEFT) . substr($expiry_year, -2);

                if ($cc_params['ccnumber'] == '4111111111111114') {
                    $paymentApproved = true;
                    $txn_id = 0;
                    $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                } else {
                    $payment_res = $api->processPayment($cc_params, $payment_master_id);

                    if ($payment_res['status'] == 'Success') {
                        $paymentApproved = true;
                        $txn_id = $payment_res['transaction_id'];
                    } else {
                        $paymentApproved = false;
                        $txn_id = $payment_res['transaction_id'];
                        $payment_error = $payment_res['message'];
                        $cc_params['order_type'] = 'List Bill Payment';
                        $cc_params['browser'] = $BROWSER;
                        $cc_params['os'] = $OS;
                        $cc_params['req_url'] = $REQ_URL;
                        $cc_params['err_text'] = $payment_error;
                        $decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                    }
                }
            }
        }
      /*---- Take Payment ----*/

      /*---- Order ----*/
        $order_data = array(
            'is_list_bill_order'=>"Y",
            'is_renewal'=>"L",
            'payment_type'=>$payment_mode,
            'payment_master_id' => $payment_master_id,
            'payment_processor' => $payment_processor,
            'type' => "List Bill",
            'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
            'browser' => $BROWSER,
            'os' => $OS,
            'req_url' => $REQ_URL,
            'product_total' => ($due_amount - $list_bill_admin_fee),
            'sub_total' => ($due_amount - $list_bill_admin_fee),
            'grand_total' => $due_amount,
            'order_count' => $order_count,
            'post_date' => date('Y-m-d'),
            'future_payment'=>'N',
            'transaction_id'=>$txn_id,
            'payment_processor_res'=> (isset($payment_res)?json_encode($payment_res):""),
        );
        $order_data['status'] = ($payment_mode == "ACH"?'Pending Settlement':'Payment Approved');
        if ($paymentApproved == false) {
            $order_data['status'] = 'Payment Declined';
        }
        if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
            $order_data['review_require'] = 'Y';
        }

        if ($order_id > 0) {
            $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
            if(!(isset($_REQUEST['debug']))) {
                $pdo->update("orders", $order_data, $order_where);    
            } else {
                pre_print($order_data,false);
            }
        } else {
            $order_data['display_id'] = $order_display_id; 
            $order_data['customer_id'] = $customer_id; 
            $order_data['list_bill_id'] =  $list_bill_row['id']; 
            $order_data['original_order_date'] =  'msqlfunc_NOW()';
            if($billing_profile == "record_check_payment") {
                $order_data['original_order_date'] =  date("Y-m-d",strtotime($other_params['payment_date']));
            }
            if(!empty($company_id)) {
                $order_data['group_company_id'] = $company_id;
            }
            if(!(isset($_REQUEST['debug']))) {
                $order_id = $pdo->insert("orders",$order_data);
            } else {
                pre_print($order_data,false);
            }
        }
      /*----/Order ----*/

      /*---- Order Billing ----*/
        $billing_data = array(
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'fname' => ($payment_mode == 'CC' ? $name_on_card : $ach_bill_fname),
            'lname' => ($payment_mode == 'CC' ? '' : $ach_bill_lname),
            'email' => $group_row['email'],
            'country_id' => 231,
            'country' => 'United States',
            'state' => $bill_state,
            'city' => $bill_city,
            'zip' => $bill_zip,
            'address' => $bill_address,
            'created_at' => 'msqlfunc_NOW()',
            'payment_mode' => $payment_mode,
            'customer_billing_id' => $billing_profile,
            'listbill_enroll' => 'Y',
        );

        if ($payment_mode == "CC") {
            $billing_data = array_merge($billing_data,array(
                'cvv_no' => makeSafe($cvv_no),
                'card_no' => makeSafe(substr($card_number, -4)),
                'last_cc_ach_no' => makeSafe(substr($card_number, -4)),
                'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                'card_type' => makeSafe($card_type),
                'expiry_month' => makeSafe($expiry_month),
                'expiry_year' => makeSafe($expiry_year)
            ));
        } elseif($payment_mode == "ACH") {
            $billing_data = array_merge($billing_data,array(
                'ach_account_type' => $ach_account_type,
                'bankname' => $bankname,
                'last_cc_ach_no' => makeSafe(substr($account_number, -4)),
                'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
            ));
        }

        $sql_obi = "SELECT ob.id FROM order_billing_info ob WHERE ob.order_id=:order_id";
        $obi_row = $pdo->selectOne($sql_obi,array(":order_id" => $order_id));
        
        if(!empty($obi_row)) {
            $obi_where = array("clause" => "id=:id", "params" => array(":id" => $obi_row['id']));
            if(!(isset($_REQUEST['debug']))) {
                $pdo->update("order_billing_info",$billing_data,$obi_where);    
            } else {
                pre_print($billing_data,false);
            }
            
        } else {
            if(!(isset($_REQUEST['debug']))) {
                $pdo->insert("order_billing_info",$billing_data);    
            } else {
                pre_print($billing_data,false);
            }
        }
      /*----/Order Billing ----*/

      /*---- Order Detail ----*/
        $pdo->delete("DELETE FROM order_details WHERE order_id=:order_id",array(":order_id"=>$order_id));

        $total_unit_price = 0;
        $new_business_total = 0;
        $renewal_total = 0;
        $new_business_members = array();
        $renewal_members = array();

        if(!empty($lbd_res)){

            array_multisort($lbd_res,SORT_ASC,array_column($lbd_res,'list_bill_detail_id'));
            
            $ws_data = array();
            foreach ($lbd_res as $list_bill_detail_id => $detail) {

                if(strtotime($detail['start_coverage_date']) == strtotime($detail['eligibility_date'])) {
                    $coverage_period_count = 1;
                } else {
                    $coverage_period_count = 0;
                    $tmp_eligibility_date = $detail['eligibility_date']; 
                    while (strtotime(date('Y-m',strtotime($tmp_eligibility_date))) <=  strtotime(date('Y-m',strtotime($detail['start_coverage_date'])))) {
                        $coverage_period_count++;
                        $product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date,$detail['member_payment_type']);
                        $tmp_eligibility_date = date('Y-m-d',strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                    }
                }

                $family_member = 0;
                if($detail['prd_plan_type_id'] > 1) {
                    $count_dep = $pdo->selectOne("SELECT COUNT(id) as family_member FROM customer_dependent WHERE website_id=:website_id GROUP BY website_id",array(":website_id" => $detail['website_id']));
                    if(!empty($count_dep['family_member'])) {
                        $family_member = $count_dep['family_member'];
                    }
                }

                if(empty($detail['qty'])) {
                  $detail['qty'] = 1;
                }

                $od_data = array(
                    'list_bill_id'=> $list_bill_id,
                    'is_list_bill'=> 'Y',
                    'list_bill_detail_id'=> $detail['list_bill_detail_id'],
                    'website_id'=> $detail['website_id'],
                    'order_id' => $order_id,
                    'product_id' => $detail['product_id'],
                    'fee_applied_for_product' => $detail['fee_applied_for_product'],
                    'plan_id' => $detail['plan_id'],
                    'prd_plan_type_id' => $detail['prd_plan_type_id'],
                    'product_type' => $detail['product_type'],
                    'product_name' => $detail['product_name'],
                    'product_code' => $detail['product_code'],
                    'family_member' => $family_member,
                    'unit_price' => $detail['unit_price'],
                    'qty' => $detail['qty'],
                    'member_price' => $detail['member_price'],
                    'group_price' => $detail['group_price'],
                    'contribution_type' => $detail['contribution_type'],
                    'contribution_value' => $detail['contribution_value'],
                    'renew_count' => $coverage_period_count,
                    'is_renewal' => ($coverage_period_count > 1?"Y":"N"),
                    'start_coverage_period' => $detail['start_coverage_date'],
                    'end_coverage_period' => $detail['end_coverage_date'],
                    'lbd_transaction_type' => $detail['transaction_type'],
                    'updated_at' => 'msqlfunc_NOW()',
                );

                if(!(isset($_REQUEST['debug']))) {
                    $pdo->insert("order_details", $od_data);    
                } else {
                    pre_print($od_data,false);
                }

                if(!(isset($ws_data[$detail['website_id']]))) {
                    $ws_data[$detail['website_id']] = array(
                        'website_id' => $detail['website_id'],
                        'eligibility_date' => $detail['eligibility_date'],
                        'start_coverage_date' => $detail['start_coverage_date'],
                        'end_coverage_date' => $detail['end_coverage_date'],
                        'renew_count' => ($coverage_period_count - 1),
                    );
                } else {
                    if(strtotime($detail['start_coverage_date']) > strtotime($ws_data[$detail['website_id']]['start_coverage_date'])) {
                        $ws_data[$detail['website_id']] = array(
                            'website_id' => $detail['website_id'],
                            'eligibility_date' => $detail['eligibility_date'],
                            'start_coverage_date' => $detail['start_coverage_date'],
                            'end_coverage_date' => $detail['end_coverage_date'],
                            'renew_count' => ($coverage_period_count - 1),
                        );
                    }
                }

                $tmp_price = $detail['unit_price'] * $detail['qty'];
                $total_unit_price += ($detail['transaction_type'] != "refund"? $tmp_price : ($total_unit_price - $tmp_price));

                if($coverage_period_count > 1) {
                    $renewal_total += ($detail['transaction_type'] != "refund"? $tmp_price : ($renewal_total - $tmp_price));
                    $renewal_members[] = $detail['customer_id'];

                } else {                    
                    $new_business_total += ($detail['transaction_type'] != "refund"? $tmp_price : ($new_business_total - $tmp_price));
                    $new_business_members[] = $detail['customer_id'];
                }
            }

            $renewal_members = array_unique($renewal_members);
            $new_business_members = array_unique($new_business_members);
            
            /*--- List Bill Admin Fee Will Add in Renewal Total - Confirmed with @Troy ---*/
            $renewal_total += $list_bill_admin_fee;
            $ChargePrd = $this->getListBillPaymentChargePrd();
            $od_data = array(
                'list_bill_id'=> $list_bill_id,
                'is_list_bill'=> 'Y',
                'list_bill_detail_id'=> 0,
                'website_id'=> 0,
                'order_id' => $order_id,
                'product_id' => $ChargePrd['product_id'],
                'product_type' => $ChargePrd['product_type'],
                'product_name' => $ChargePrd['product_name'],
                'product_code' => $ChargePrd['product_code'],
                'unit_price' => $list_bill_admin_fee,
                'qty' => 1,
                'renew_count' => 2,
                'is_renewal' => "Y",
                'start_coverage_period' => $list_bill_row['time_period_start_date'],
                'end_coverage_period' => $list_bill_row['time_period_end_date'],
                'lbd_transaction_type' => 'charged',
                'updated_at' => 'msqlfunc_NOW()',
            );
            if(!(isset($_REQUEST['debug']))) {
                $pdo->insert("order_details", $od_data);    
            } else {
                pre_print($od_data,false);
            }
            /*--- List Bill Admin Fee Will Add in Renewal Total - Confirmed with @Troy ---*/
        }
      /*----/Order Detail ----*/

      /*---- Website Subscriptions ----*/
        if(!empty($ws_data)) {
            foreach ($ws_data as $ws_id => $ws_row) {
                if($paymentApproved == true) {
                    $ws_upd_data = array(
                        'renew_count' => $ws_row['renew_count'],
                        'last_order_id' => $order_id,
                        'fail_order_id' => 0,
                        'last_purchase_date' => 'msqlfunc_NOW()',
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                } else {
                    $ws_upd_data = array(
                        'fail_order_id' => $order_id,
                    );                       
                }
                
                if(!(isset($_REQUEST['debug']))) {
                    $pdo->update("website_subscriptions",$ws_upd_data,array("clause" => "id=:id", "params" => array(":id" => $ws_id)));
                } else {
                    pre_print($ws_upd_data,false);
                }
                
            }

            $ws_ids = array_keys($ws_data);

            if(!(isset($_REQUEST['debug']))) {
                $ord_upd_data = array(
                    'subscription_ids' => implode(',',$ws_ids),
                    'new_business_total' => $new_business_total,
                    'renewal_total' => $renewal_total,
                    'new_business_members' => count($new_business_members),
                    'renewal_members' => count($renewal_members),
                );
                $pdo->update("orders",$ord_upd_data,array("clause"=>"id=:id","params"=>array(":id"=>$order_id)));
            } else {
                pre_print($ws_ids,false);
            }
        }
      /*----/Website Subscriptions ----*/

      if(isset($_REQUEST['debug'])) {
          pre_print($new_business_total,false);
          pre_print($renewal_total,false);
          pre_print($new_business_members,false);
          pre_print($renewal_members,false);
          pre_print($total_unit_price);
      }

      /*---- Transaction ----*/
        $trans_params = array(
            "transaction_id" => $txn_id,
            'transaction_response' => $payment_res,
            'new_business_total' => $new_business_total,
            'renewal_total' => $renewal_total,
            'new_business_members' => count($new_business_members),
            'renewal_members' => count($renewal_members),
        );
        if ($paymentApproved ){
            if($payment_mode != "ACH"){
                $transactionInsId = $function_list->transaction_insert($order_id,'Credit','List Bill Order','Transaction Approved',0,$trans_params);              
            } else {
                $transactionInsId = $function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$trans_params);
            }
        } else {
            $trans_params["reason"] = checkIsset($payment_error);
            $trans_params["cc_decline_log_id"] = checkIsset($decline_log_id);
            $transactionInsId = $function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$trans_params);
        }
      /*----/Transaction ----*/

      /*---- Update List BIll No As Per Order Display ID ----*/
        $list_bill_no = "LIST-".$order_display_id;
        if($list_bill_no != $list_bill_row['list_bill_no']) {
            $sql = "SELECT list_bill_no FROM list_bills WHERE list_bill_no='" . $list_bill_no . "'";
            $res = $pdo->selectOne($sql);
            if (empty($res)) {
                /*-------- Update List Bill No -------*/
                    $lb_upd_data = array(
                        'list_bill_no' => $list_bill_no,
                        'updated_at' => 'msqlfunc_NOW()'
                    );
                    $lb_update_where = array(
                        'clause' => 'id=:id',
                        'params' => array(
                            ':id' => $list_bill_id
                        )
                    );
                    $pdo->update("list_bills", $lb_upd_data, $lb_update_where);

                    $ac_description = array();
                    $ac_description['ac_message'] = array(
                        'ac_red_1' => array(
                            'href' => 'view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                            'title' => $list_bill_row['list_bill_no'],
                        ),
                        'ac_message_1' => ' List Bill # updated from '.$list_bill_row['list_bill_no'].' to '.$list_bill_no,
                    );
                    activity_feed(3,$customer_id,'Group',$customer_id,'Group','List Bill Updated','','',json_encode($ac_description));
                /*--------/Update List Bill No -------*/
            } else {
              $list_bill_no = $list_bill_row['list_bill_no'];
            }
        }
      /*----/Update List BIll No As Per Order Display ID ----*/

      $payment_method_text = '';
      if($billing_data['payment_mode'] == "CC") {
          $payment_method_text = $billing_data['card_type']." *".$billing_data['last_cc_ach_no'];

      } else if($billing_data['payment_mode'] == "ACH") {
          $payment_method_text = "ACH *".$billing_data['last_cc_ach_no'];

      } else if($billing_data['payment_mode'] == "Check") {
          $payment_method_text = "Check";
      }

      if ($paymentApproved) {
        /*-------- Update List Bill -------*/
            $lb_upd_data = array(
                'received_amount' => $due_amount,
                'due_amount' => 0,
                'status' => 'paid',
                'order_id' => $order_id,
                'payment_received_date' => 'msqlfunc_NOW()',
                'next_attempt_at' => NULL,
                'total_attempts' => 0,
                'updated_at' => 'msqlfunc_NOW()'
            );
            $lb_update_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $list_bill_id
                )
            );
            $pdo->update("list_bills", $lb_upd_data, $lb_update_where);
        /*--------/Update List Bill -------*/

        /*------- Add Admin Fee for CC/Check -------*/
        if($billing_data['payment_mode'] != "ACH") {
            $pay_options = $pdo->selectOne("SELECT cc_additional_charge,cc_charge,cc_charge_type,check_additional_charge,check_charge FROM group_pay_options WHERE is_deleted='N' AND group_id=:group_id",array(":group_id"=>$customer_id));
            if(empty($pay_options)) {
                $pay_options = $pdo->selectOne("SELECT cc_additional_charge,cc_charge,cc_charge_type,check_additional_charge,check_charge FROM group_pay_options WHERE is_deleted='N' AND rule_type='Global'");
            }
            if(!empty($pay_options)) {
                $charge_amount = 0;

                if($billing_data['payment_mode'] == "CC" && $pay_options['cc_additional_charge'] == "Y" && !empty($pay_options['cc_charge'])) {
                    if($pay_options['cc_charge_type'] == "Fixed") {
                        $charge_amount = $pay_options['cc_charge'];

                    } else if($pay_options['cc_charge_type'] == "Percentage") {
                        $charge_amount = (($due_amount * $pay_options['cc_charge']) / 100);
                    }

                } elseif($billing_data['payment_mode'] == "Check" && $pay_options['check_additional_charge'] == "Y" && !empty($pay_options['check_charge'])) {

                    $charge_amount = $pay_options['check_charge'];
                }

                if($charge_amount > 0) {
                    /*---- Add Entry In Fee Table ----*/
                    $admin_fee_data = array(
                        'group_id' => $customer_id,
                        'list_bill_id' => $list_bill_id,
                        'class_id' => $class_id,
                        'amount' => $charge_amount,
                    );
                    if(!empty($company_id)) {
                        $admin_fee_data['group_company_id'] = $company_id;
                    }
                    $pdo->insert("list_bill_admin_fee", $admin_fee_data);
                    /*----/Add Entry In Fee Table ----*/
                }
            }
        }
        /*-------/Add Admin Fee for CC/Check -------*/

        /*------- Upload Record Payment Files -------*/
        if($billing_profile == "record_check_payment") {
            if(isset($_FILES['file']['name']) && count($_FILES['file']['name']) > 0){
                $stored_file_name = array();
                foreach ($_FILES['file']['name'] as $key => $file_size) {
                    if(!empty($_FILES['file']['name'][$key])){
                        $_FILES['temp_file'] = array(
                            'name' => $_FILES['file']['name'][$key],
                            'type' => $_FILES['file']['type'][$key],
                            'tmp_name' => $_FILES['file']['tmp_name'][$key],
                            'error' => $_FILES['file']['error'][$key],
                            'size' => $_FILES['file']['size'][$key],
                        );
                        $name = basename($_FILES['temp_file']['name']);
                        $stored_file_name[$key] = time() . $name;
                        move_uploaded_file($_FILES['temp_file']['tmp_name'], $LIST_BILL_PAYMENT_FILES_DIR . $stored_file_name[$key]);
                        $_FILES['temp_file'] = array();
                    }
                }
                if (!empty($stored_file_name)) {
                    $ord_data = array();
                    $ord_data['payment_files'] = json_encode($stored_file_name);
                    $ord_where = array(
                        'clause' => 'id=:id',
                        'params' => array(
                            ':id' => $order_id
                        )
                    );
                    $pdo->update("orders",$ord_data,$ord_where);
                }
            }
        }
        /*-------/Upload Record Payment Files -------*/

        /*---- Generate Payable ----*/
          if($payment_mode != "ACH"){
              $payable_params = array(
                  'payable_type'=>'Vendor',
                  'type'=>'Vendor',
                  'transaction_tbl_id' => $transactionInsId['id'],
              );
              $function_list->payable_insert($order_id,0,0,0,$payable_params);
          }
        /*---- /Generate Payable ----*/

        /*-------- Activty Feed -------*/
          if($location == "auto_payment_list_bill_cron") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> $GROUP_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_1' => " Successful List Bill Payment, Payment Method: ",
                'ac_red_2'=>array(
                    'title'=> 'Auto Draft - '.$payment_method_text,
                ),
            );
            activity_feed(3,$customer_id,'Group',$customer_id,'Group','Successful List Bill Payment','','',json_encode($ac_description));

          } elseif($location == "group") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$GROUP_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                  'title'=>$_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => " paid List Bill Payment ",
                'ac_red_2'=>array(
                    'href'=> $GROUP_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_2' => ' Payment Method: ',
                'ac_red_3'=>array(
                    'title'=> $payment_method_text,
                ),
            );
            activity_feed(3,$_SESSION['groups']['id'],'Group',$_SESSION['groups']['id'],'Group','Successful List Bill Payment','','',json_encode($ac_description));

          } elseif($location == "admin") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => " paid List Bill Payment ",
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_2' => ' Payment Method: ',
                'ac_red_3'=>array(
                    'title'=> $payment_method_text,
                ),
            );
            activity_feed(3,$_SESSION['admin']['id'],'Admin',$customer_id,'Group','Successful List Bill Payment','','',json_encode($ac_description));
          }
        /*--------/Activty Feed -------*/

        /*-------- Send Successful Payment Email To Group -------*/
          if($billing_profile == "record_check_payment") {
              if(isset($other_params['sent_receipt']) && $other_params['sent_receipt'] == "Y") {
                $trigger_id=83;
                if($SITE_ENV == 'Local'){
                    $email_arr = array('shailesh@cyberxllc.com');
                } else {
                    $email_arr = array($group_row['email']);
                    $email_arr[] = 'pramit@siddhisai.net';
                }
                $trigger_param = array();
                $trigger_param['GroupContactName'] = $group_row['fname'].' '.$group_row['lname'];
                $trigger_param['list_bill_id'] = $list_bill_no; 
                $trigger_param['amount'] = displayAmount($due_amount,2,'USA');
                trigger_mail($trigger_id, $trigger_param, $email_arr);
              }
          } else {
              $trigger_id=72;
              if($SITE_ENV == 'Local'){
                  $email_arr = array('shailesh@cyberxllc.com');
              } else {
                  $email_arr = array($group_row['email']);
                  $email_arr[] = 'pramit@siddhisai.net';
              }
              $trigger_param = array();
              $trigger_param['GroupContactName'] = $group_row['fname'].' '.$group_row['lname'];
              $trigger_param['list_bill_id'] = $list_bill_no; 
              $trigger_param['amount'] = displayAmount($due_amount,2,'USA');
              trigger_mail($trigger_id, $trigger_param, $email_arr);
          } 
        /*--------/Send Successful Payment Email To Group -------*/

        /*-------- Pay HRM Payments start -------*/
          $previous_list_bill_id = array();
          if($list_bill_row['past_due_amount']>0){
            $listBillPreSql = "SELECT GROUP_CONCAT(DISTINCT lb.id) AS previous_list_bill FROM list_bills lb 
              JOIN hrm_payment hrm ON (hrm.list_bill_id=lb.id AND hrm.status='Pending')
              WHERE lb.status = 'Cancelled' AND lb.is_deleted = 'N' 
              AND lb.customer_id = :group_id AND lb.class_id = :class_id";
            $listBillPreWhere = array(':group_id' => $customer_id,':class_id'=>$class_id);
            $listBillPreRow = $pdo->selectOne($listBillPreSql, $listBillPreWhere);
            if(!empty($listBillPreRow)){
              $previous_list_bill_id = !empty($listBillPreRow['previous_list_bill']) ? explode(",",$listBillPreRow['previous_list_bill']) : '';
            }
          }

          $listBillArray = (!empty($previous_list_bill_id))?array_unique(array_merge(array($list_bill_id),$previous_list_bill_id)):array($list_bill_id);
          foreach ($listBillArray as $key => $list_bill_id_row) {
            $hrm_sch_params = ['group_id' => $customer_id,':list_bill_id'=>$list_bill_id_row];
            $selHRMPayment = "SELECT hrmp.id, hrmp.amount AS amount,hrmp.pay_date,  hrmp.pay_period, hrmp.status, hrmpb.id AS creditRowId, a.rep_id AS groupDispId, a.id AS groupId, CONCAT(a.fname,' ',a.lname) AS groupName,hrmp.hrm_payment_duration
            FROM hrm_payment hrmp
            JOIN hrm_payment_credit_balance hrmpb ON(hrmp.group_id = hrmpb.group_id AND hrmpb.is_deleted='N')
            JOIN customer a ON(hrmp.group_id=a.id AND a.type='Group')
            JOIN customer c ON(hrmp.payer_id = c.id AND c.type='Customer')
            WHERE hrmp.is_deleted='N' AND hrmp.status='Pending' AND hrmp.group_id=:group_id AND hrmp.list_bill_id=:list_bill_id
            GROUP BY hrmp.id,hrmp.pay_period,hrmp.hrm_payment_duration";

            $resHRMPayment = $pdo->select($selHRMPayment, $hrm_sch_params);
            $orderRaw = $pdo->selectOne("SELECT od.is_renewal FROM orders o JOIN order_details od ON(od.is_deleted='N' AND od.order_id=o.id) WHERE o.id=:order_id",array(":order_id"=>$order_id));

            if (!empty($resHRMPayment)) {
              // HRM foreach start
              foreach($resHRMPayment as $hrmPayment){
                $paidToDebit = 0;
                $walletHistoryId = 0;
                $pay_date = $hrmPayment['pay_date'];
                $creditRowId = !empty($hrmPayment['creditRowId']) ? $hrmPayment['creditRowId'] : 0;
                $paidToGroup = $hrmPayment['amount'];
                $is_renewal = !empty($orderRaw['is_renewal']) ? $orderRaw['is_renewal'] : 'N';
                $pay_period = $hrmPayment['pay_period'];
                $hrm_payment_duration = $hrmPayment['hrm_payment_duration'];

                $selHRMPaymentSql = "SELECT hrmp.id as hrmId,hrmp.pay_period,hrmp.group_id,c.email,CONCAT(c.fname,' ',c.lname) AS group_name,c.type AS group_type,hrmp.payer_id AS payerId,hrmp.website_id,hrmp.list_bill_id,hrmp.list_bill_detail_id
                FROM hrm_payment hrmp
                JOIN customer c ON(c.id = hrmp.group_id)
                WHERE hrmp.hrm_payment_duration=:hrm_payment_duration AND hrmp.pay_period = :pay_period 
                AND hrmp.status IN('Pending') AND hrmp.credit_balance_id=0 
                AND hrmp.group_id=:groupId AND hrmp.list_bill_id=:list_bill_id AND hrmp.is_deleted = 'N'";
                $wparam = array(
                    ":pay_period" => $pay_period,
                    ":hrm_payment_duration" => $hrm_payment_duration,
                    ":groupId" => $customer_id,
                    ':list_bill_id'=>$list_bill_id_row
                );
                $resHRMPaymeentRow = $pdo->select($selHRMPaymentSql, $wparam);
                //Update HRM Payments Code Ends
                  if (!empty($resHRMPaymeentRow)) {
                    foreach ($resHRMPaymeentRow as $key => $row) {
                      if($list_bill_row['past_due_amount']>0 && $list_bill_id != $row['list_bill_id']){
                        $oderDetailSel = "SELECT od.id AS order_detail_id
                          FROM orders o
                          JOIN order_details od ON (o.id = od.order_id) 
                          WHERE o.customer_id=:group_id AND o.id=:order_id AND o.list_bill_id=:list_bill_id AND od.list_bill_detail_id=:list_bill_detail_id";
                        $oderDetailParams = array(":order_id"=>$order_id,":group_id"=>$customer_id,":list_bill_id"=>$list_bill_id,":list_bill_detail_id"=>$row['list_bill_detail_id']);
                        $oderDetailRes = $pdo->selectOne($oderDetailSel,$oderDetailParams);
                        $orderDetailId = !empty($oderDetailRes['order_detail_id']) ? $oderDetailRes['order_detail_id']:'';
                      }else{
                        $oderDetailSel = "SELECT od.id AS order_detail_id
                          FROM orders o
                          JOIN order_details od ON (o.id = od.order_id) 
                          WHERE o.customer_id=:group_id AND o.id=:order_id AND o.list_bill_id=:list_bill_id AND od.list_bill_detail_id=:list_bill_detail_id";
                        $oderDetailParams = array(":order_id"=>$order_id,":group_id"=>$customer_id,":list_bill_id"=>$list_bill_id_row,":list_bill_detail_id"=>$row['list_bill_detail_id']);
                        $oderDetailRes = $pdo->selectOne($oderDetailSel,$oderDetailParams);
                        $orderDetailId = !empty($oderDetailRes['order_detail_id']) ? $oderDetailRes['order_detail_id']:'';
                      }
                      $updParams = array(
                          'sub_type' => $is_renewal == "Y" ? 'Renewals' : 'New',
                          'status' => "Completed",
                          'transaction_id' => $transactionInsId['id'],
                          'credit_balance_id' => $creditRowId,
                          'updated_at' => 'msqlfunc_NOW()',
                          'order_id' => $order_id,
                          'order_detail_id' => $orderDetailId,
                          'paid_at' => 'msqlfunc_NOW()'
                      );
                      $updWhere = array(
                          'clause' => "id=:id",
                          'params' => array(
                              ':id' => makeSafe($row['hrmId']),
                          )
                      );
                      $pdo->update("hrm_payment", $updParams, $updWhere);
                    }
                  }
                // Update HRM Payments Code Ends

                // Update HRM  Payment Credit balance pay period Code Start
                    $updParams = array(
                      "status" => "Paid",
                      "admin_id" => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
                      "wallet_history_id" => $walletHistoryId,
                      "paid_to_debit" => $paidToDebit,
                      "paid_to_group" => $paidToGroup,
                      "paid_date" => "msqlfunc_NOW()",
                  );
                  $updWhere = array(
                      "clause" => "id=:id",
                      "params" => array(":id" => $creditRowId)
                  );
                  $pdo->update("hrm_payment_credit_balance", $updParams, $updWhere);
                // Update HRM  Payment Credit balance pay period Code Ends

                // Activity Feed Code Start
                if($location == "admin") {
                  $description['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => ' approved HRM Payments in ' . getCustomDate($hrmPayment['pay_period']) . ' for ',
                    'ac_red_2' => array(
                        'href' => $ADMIN_HOST . '/groups_details.php.php?id=' . md5($hrmPayment['groupId']),
                        'title' => $hrmPayment['groupDispId'],
                    ),
                  );
                  activity_feed(3, $_SESSION['admin']['id'], 'Admin', $hrmPayment['groupId'], 'Group', "Approved HRM Payments", $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
                }
                // Activity Feed Code Ends

                //Generate NACHA File Start
                  $memberIdsRes = $pdo->select("SELECT customer_id from list_bill_details WHERE list_bill_id=:id",array(":id"=>$list_bill_id_row));
                  if(!empty($memberIdsRes)){
                    $memberIds = implode(',',array_unique(array_column($memberIdsRes,'customer_id')));
                    $function_list->generateNachaFile($customer_id,$pay_date,$hrm_payment_duration,$memberIds);
                  }
                //Generate NACHA File End
              }
              // HRM foreach end
            }
          }
        /*-------- Pay HRM Payments end -------*/

        return array('status' => 'success','message' => "You have successfully recorded payment");
      } else {
        /*-------- Update List Bill -------*/
            $lb_upd_data = array(
                'order_id' => $order_id,
                'updated_at' => 'msqlfunc_NOW()'
            );

            $lb_update_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $list_bill_id
                )
            );
            $pdo->update("list_bills", $lb_upd_data, $lb_update_where);
        /*--------/Update List Bill -------*/

        /*-------- Activty Feed -------*/
          if($location=="auto_payment_list_bill_cron") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> $GROUP_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_1' => " Failed List Bill Payment, Payment Method: ",
                'ac_red_2'=>array(
                    'title'=> 'Auto Draft - '.$payment_method_text,
                ),
            );
            activity_feed(3,$customer_id,'Group',$customer_id,'Group','Failed List Bill Payment','','',json_encode($ac_description));

          } elseif($location == "group") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$GROUP_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                  'title'=>$_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => " Failed List Bill Payment ",
                'ac_red_2'=>array(
                    'href'=> $GROUP_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_2' => ' Payment Method: ',
                'ac_red_3'=>array(
                    'title'=> $payment_method_text,
                ),
            );
            activity_feed(3,$_SESSION['groups']['id'],'Group',$_SESSION['groups']['id'],'Group','Failed List Bill Payment','','',json_encode($ac_description));
          } elseif($location == "admin") {
            $ac_description = array();
            $ac_description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => " Failed List Bill Payment ",
                'ac_red_2'=>array(
                    'href'=> $GROUP_HOST.'/view_listbill_statement.php?list_bill='.md5($list_bill_row['id']),
                    'title'=> $list_bill_no,
                ),
                'ac_message_2' => ' Payment Method: ',
                'ac_red_3'=>array(
                    'title'=> $payment_method_text,
                ),
            );
            activity_feed(3,$_SESSION['admin']['id'],'Admin',$customer_id,'Group','Failed List Bill Payment','','',json_encode($ac_description));
          }
        /*--------/Activty Feed -------*/

        /*-------- Send Payment Fail Email To Group -------*/
          if($location=="auto_payment_list_bill_cron") {
            $trigger_id = 73;
            if($SITE_ENV == 'Local'){
                $email_arr = array('shailesh@cyberxllc.com');
            } else {
                $email_arr = array($group_row['email']);
                $email_arr[] = 'pramit@siddhisai.net';
            }
            $trigger_param = array();
            $trigger_param['GroupContactName'] = $group_row['fname'].' '.$group_row['lname'];
            $trigger_param['list_bill_id'] = $list_bill_no; 
            $trigger_param['amount'] = displayAmount($due_amount,2,'USA');
            trigger_mail($trigger_id, $trigger_param, $email_arr);
          }
        /*--------/Send Payment Fail Email To Group -------*/

        return array('status' => 'fail','error_code' => 'payment_fail', 'message' => $payment_res['message']);
      }
  }

  public function generateListBill($is_regenerate = false, $regenerate_group_id = 0, $regenerate_id = 0, $regenerate_company_id = '', $extra = array())
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    global $pdo, $SITE_ENV, $ADMIN_HOST;
    require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    require_once dirname(__DIR__) . '/includes/function.class.php';
    $function_list = new functionsList();
    $enrollDate = new enrollmentDate();
    $listBillDate = $today = (!empty($extra) && $extra['type'] == "manual") ? date('Y-m-d', strtotime($extra['today'])) : date('Y-m-d');
    /*------------ Fetch subscribed_members --------------*/
    $incr = "";
    $sch_params = array();
    if (!empty($regenerate_group_id)) {
      $incr .= " AND c.id=:group_id";
      $sch_params[':group_id'] = $regenerate_group_id;
    }
    
    //OP29-843 updates generate list bill for suspended groups also
    $sponsor_sql = "SELECT c.fname,c.lname,c.email,c.id,c.type,cgs.invoice_broken_locations,cgs.is_auto_draft_set,
                    cgs.auto_draft_date,lbo.billing_setting,lbo.days_prior_pay_period as prior_days
                    FROM customer c 
                    LEFT JOIN customer_group_settings cgs ON (cgs.customer_id=c.id)
                    LEFT JOIN list_bill_options lbo on (lbo.group_id=c.id and lbo.is_deleted='N')
                    WHERE c.type IN ('Group') AND c.status IN('Active','Suspended') AND c.is_deleted = 'N' $incr";
    $sponsor_result = $pdo->select($sponsor_sql, $sch_params);

    $list_bill_id_arr = array();
    if (!empty($sponsor_result)) {
      foreach ($sponsor_result as $sponsor_row) {
        $compan_group_id_arr = array();
        $company_res = array();
        $group_id = $sponsor_row['id'];
        $tmpIncr = " AND group_id = :customer_id";
        $tmpSchParams[":customer_id"] = $group_id;
        $class_res[0] = '';

        $listBillGenerateRes = $this->get_next_list_bill_generate_date($group_id, 0, false, $extra);
        $listBillDate = $listBillGenerateRes["listBillDate"];
        $listBillPayDate = $listBillGenerateRes["listBillPayDate"];
        // pre_print($today);
        if (!$is_regenerate && ( (!empty($extra) && $extra['type']=='manual') || empty($regenerate_group_id))) {
          /** Check list bill date is today or not code start */
            if (strtotime($listBillDate) != strtotime($today)) {
              continue;
            }
          /** Check list bill date is today or not code end */
        }else if($is_regenerate){
          /** Update List Bill Status code start */
          $pre_list_bill_update = array();
          $pre_list_bill_update['status'] = 'Regenerate';
          $pre_list_bill_update_where = array('clause' => "id=:id AND status ='open'", 'params' => array(':id' => $regenerate_id));
          $pdo->update('list_bills', $pre_list_bill_update, $pre_list_bill_update_where);
          /** Update List Bill Status code end */
        }

        $display_company = true;
        if (empty($regenerate_company_id)) {
          array_push($compan_group_id_arr, 0);
          $company_res[0] = array(
            'is_auto_draft_set' => $sponsor_row['is_auto_draft_set'],
            'auto_draft_date' => $sponsor_row['auto_draft_date'],
          );
        }

        /** Check is group have companies code start */
          if (!empty($sponsor_row['invoice_broken_locations']) && ($sponsor_row['invoice_broken_locations'] == 'Y')) {
            if ($regenerate_company_id != '') {
              $tmpIncr .= " AND id=:id";
              $tmpSchParams[':id'] = $regenerate_company_id;
            }
            $group_company_res = $pdo->select("SELECT id,is_auto_draft_set,auto_draft_date FROM group_company WHERE  is_deleted='N' $tmpIncr", $tmpSchParams);

            if (!empty($group_company_res)) {
              foreach ($group_company_res as $gp_key => $gp_value) {
                if (!in_array($gp_value['id'], $compan_group_id_arr)) {
                  array_push($compan_group_id_arr, $gp_value['id']);
                }
                $company_res[$gp_value['id']] = array(
                  'is_auto_draft_set' => $gp_value['is_auto_draft_set'],
                  'auto_draft_date' => $gp_value['auto_draft_date'],
                );
              }
            }
          } else {
            $display_company = false;
          }
        /** Check is group have companies code end */

        if (!empty($compan_group_id_arr)) {
          foreach ($company_res as $company_id => $company_row) {

            /** Check bill subDivided on Class code start */
              if (empty($regenerate_id)) {
                $classSql= "SELECT class_id,paydate AS class_paydate
                            FROM group_classes_paydates 
                            WHERE group_id=:groupId AND paydate=:listBillPayDate AND is_deleted='N' ORDER BY paydate";

                $classParam = array(
                  ':groupId' => $group_id,
                  ':listBillPayDate' => $listBillPayDate,
                );
                $classData = $pdo->select($classSql, $classParam);

                if (!empty($classData)) {
                  $class_res = array_column($classData,'class_paydate','class_id');
                }
              } else {
                $regenerateListSql = "SELECT class_id FROM 
                            list_bills 
                            WHERE id=:listBillId  AND is_deleted='N' ";
                $regenerateListParam = array(
                  ':listBillId' => $regenerate_id,
                );
                $regenerateRes = $pdo->select($regenerateListSql, $regenerateListParam);
                $class_res = array_flip(array_column($regenerateRes,'class_id'));
              }
   
            /** Check bill subDivided on Class code start */
            foreach ($class_res as $class_id=>$class_paydate) {

              /*************** Set payPrd code start ***********/
                $same_dates_res = array(0);
                $cFlag=false;
                $payPeriod='Monthly';
        
                $payPrdSql = "SELECT pay_period FROM 
                        group_classes 
                        WHERE group_id=:groupId  AND id=:classId and is_deleted='N'  ";
                $payPrdParam = array(
                  ':groupId' => $group_id,
                  ':classId' => $class_id,
                );
                $payPrdRes = $pdo->selectOne($payPrdSql, $payPrdParam);
                $payPeriod = (!empty($payPrdRes))?$payPrdRes['pay_period']:$payPeriod;

                if (empty($regenerate_id)) {                      
                  /** No of Same Dates Selected Code Start */
                  if($payPeriod=='Semi-Monthly' ){
                    $PayPrdDateSql = "SELECT count(id) as count FROM 
                    group_classes_paydates 
                    WHERE group_id=:groupId  AND class_id=:classId and payDate=:date and is_deleted='N'";
                    $PayPrdDateParam = array(
                      ':groupId' => $group_id,
                      ':classId' => $class_id,
                      ':date' => $class_paydate,
                    );
                    $payPrdDatesCount = $pdo->selectOne($PayPrdDateSql, $PayPrdDateParam);
                    /** (Count) No of list bill is generated for particular class on same date  */
                    if(!empty($payPrdDatesCount) && $payPrdDatesCount['count']>1){
                      $same_dates_res[1] = 0;
                    }
                  }
                  /** No of Same Dates Selected Code End */  
                }
              /*************** Set  payPrd code end ***********/
           
              foreach($same_dates_res as $sameDates){

                $sch_params = array();
                $incr = " AND customer_id = :customer_id";
                $sch_params[':customer_id'] = $group_id;
  
                if ($display_company) {
                  $incr .= " AND company_id = :company_id";
                  $sch_params[':company_id'] = $company_id;
                }
  
                $incr .= " AND class_id = :class_id";
                $sch_params[':class_id'] = $class_id;
  
                if ($is_regenerate) {
                  $incr .= " AND id != :regenerate_id AND status ='open'";
                  $sch_params[':regenerate_id'] = $regenerate_id;
                }

                 //*************** Check List Bill Already Generated Code Start ***************
                 $isHRMpayment = false;
                 $list_bill_sql = "SELECT id,list_bill_date,time_period_start_date,time_period_end_date,status FROM list_bills WHERE is_deleted = 'N'  $incr ORDER BY id DESC";
                 $list_bill_res = $pdo->selectOne($list_bill_sql, $sch_params);
                 if (!empty($list_bill_res) && !empty($list_bill_res['list_bill_date'])) {
                   if (strtotime($list_bill_res['list_bill_date']) >= strtotime($today)) {
                    if($list_bill_res['status'] == 'paid'){
                      $isHRMpayment = true;
                      $cFlag=false;
                    }else{
                      $cFlag=true;
                       continue;
                    }
                   }
                 }
                //*************** Check List Bill Already Generated Code End ***************
    
                //*************** Set List Bill Coverage  Code Start ***************
                  if (empty($regenerate_id)) {
                    $exactPayPrdDate=( !empty($class_paydate) ) ? $class_paydate : $today;

                    // if ($payPeriod == 'Semi-Monthly' && !empty($list_bill_res) ) {
                    //   $exactPayPrdDate = $list_bill_res['time_period_start_date'];
                    // }

                    if ($payPeriod == 'Bi-Weekly' && !empty($list_bill_res) ) {
                      $exactPayPrdDate = $list_bill_res['time_period_end_date'];
                    }

                    $coverageDatesArr = $this->get_list_bill_coverage_date($exactPayPrdDate, $payPeriod, $group_id);
                    //list bill coverage_start_date
                    $time_period_start_date = $coverageDatesArr['list_bill_coverage_start_date'];
                    //list bill coverage_start_date
                    $time_period_end_date = $coverageDatesArr['list_bill_coverage_end_date'];
                    $due_dates = date('Y-m-d', strtotime("-1 day", strtotime($time_period_start_date)));
                  } else {
                    $tmpSqlList = "SELECT due_date,time_period_start_date,time_period_end_date,list_bill_date FROM list_bills WHERE id=:id";
                    $tmpResList = $pdo->selectOne($tmpSqlList, array(":id" => $regenerate_id));

                    if (!empty($tmpResList)) {
                      $time_period_start_date = $tmpResList['time_period_start_date'];
                      $time_period_end_date = $tmpResList['time_period_end_date'];
                      $due_dates = $tmpResList['due_date'];
                      $listBillDate=$tmpResList['list_bill_date'];
                    }
                  }
                //*************** Set List Bill Coverage  Code End ***************

                //*************** List Bill Set Refund/Charge (Missing Bill) Code Start Start ***************
                      $sch_params = array();
                      $incr = " AND c.sponsor_id =:sponsor_id";
                      $sch_params[':sponsor_id'] = $group_id;
                      if ($display_company) {
                        $incr .= " AND c.group_company_id = :company_id";
                        $sch_params[':company_id'] = $company_id;
                      }
            
                      $incr .= " AND cs.class_id= :class_id";
                      $sch_params[':class_id'] = $class_id;

                      $list_bill_ws_sql = "SELECT ws.id
                            FROM website_subscriptions ws
                           JOIN customer_enrollment ce ON(ce.website_id=ws.id)
                            JOIN customer c ON(c.id = ws.customer_id)
                            JOIN customer_settings cs ON (c.id=cs.customer_id)
                            JOIN prd_main pm ON (pm.id=ws.product_id AND (pm.type='Normal' OR (pm.type='Fees' AND pm.status='Active')))
                            WHERE ws.payment_type='list_bill' AND c.is_deleted = 'N' AND c.status IN('Active','Inactive')  $incr 
                            GROUP BY ws.id ";
                      $list_bill_ws_result = $pdo->select($list_bill_ws_sql, $sch_params);
                      if (!empty($list_bill_ws_result)) {
                        foreach ($list_bill_ws_result as $list_bill_ws_row) {
                          $this->getSubscriptionRefundChargeCoverage($list_bill_ws_row['id'], $time_period_start_date);
                        }
                      }
                //*************** List Bill Set Refund/Charge (Missing Bill) Code Start Start ***************

                $sch_params = array();
                $incr = " AND c.sponsor_id =:sponsor_id";
                $sch_params[':sponsor_id'] = $group_id;

                if ($display_company) {
                  $incr .= " AND c.group_company_id = :company_id";
                  $sch_params[':company_id'] = $company_id;
                }

                $incr .= " AND cs.class_id = :class_id";
                $sch_params[':class_id'] = $class_id;
                // If fee status updated to Inactive then it should not apply to further coverages
                $subscribed_members_sql = "SELECT ws.*,c.sponsor_id,
                    IF(ws.qty = 0, 1, ws.qty) as quantity,ce.id as ce_id,ce.new_plan_id,ce.tier_change_date,ce.process_status,pm.payment_type_subscription as member_payment_type
                    FROM website_subscriptions ws
                    JOIN customer_enrollment ce ON(ce.website_id=ws.id)
                    JOIN customer c ON(c.id = ws.customer_id)
                    JOIN prd_main pm ON (pm.id=ws.product_id AND (pm.type='Normal' OR (pm.type='Fees' AND pm.status='Active')))
                    JOIN customer_settings cs ON (c.id=cs.customer_id)
                    WHERE (ws.payment_type='list_bill' AND (ws.status = 'Active' OR ws.status IN ('Pending','Pending Payment') OR ws.is_listbill_refund_charge = 'Y')) AND c.is_deleted = 'N' $incr  GROUP BY ws.id";

                $subscribed_members = $pdo->select($subscribed_members_sql, $sch_params);
                if (!empty($subscribed_members)) {

                  //*************** List Bill Details Code Start ***************
                    $listBillDetailsArr=array();
                    $items_total = 0;
                    foreach ($subscribed_members as $key => $ws_row) {

                    /******************* Product Next Payment coverage period code start  ***********/             
                        $detail_time_period_start_date=$ws_row['eligibility_date'];
                        $oldlbSql = "SELECT MIN(lbd.start_coverage_date) AS initial_coverage_start, MAX(lbd.end_coverage_date) AS last_coverage_end
                        FROM list_bills lb
                        JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
                        WHERE 
                        lb.status IN('open','paid','Cancelled') AND 
                        lb.is_deleted = 'N' AND 
                        lbd.amount > 0 AND 
                        lbd.ws_id = :ws_id
                        ";
                        $oldlbWhere = array(":ws_id" => $ws_row['id']);
                        $oldlbRes = $pdo->selectOne($oldlbSql, $oldlbWhere);
                        //List bill detail period start date
                        $detail_time_period_start_date=(!empty($oldlbRes) && $oldlbRes['initial_coverage_start']==$ws_row['eligibility_date'])?date('Y-m-d',strtotime("+1 day",strtotime($oldlbRes['last_coverage_end']))):$detail_time_period_start_date;

                        while ((strtotime($detail_time_period_start_date) < strtotime($time_period_start_date))) {
                          $date = new DateTime($detail_time_period_start_date);
                          if ($payPeriod == 'Monthly') {
                            $detail_time_period_start_date = addMonth($date, '1');
                          } else if ($payPeriod == 'Semi-Monthly') {
                              if (date('d', strtotime($detail_time_period_start_date)) == "16") {
                                $detail_time_period_start_date = date('Y-m-01', strtotime('+ 1 month', strtotime($detail_time_period_start_date)));
                                pre_print("here ".$detail_time_period_start_date);
                              } else {
                                $detail_time_period_start_date = date('Y-m-d', strtotime("+ 15 Days", strtotime($detail_time_period_start_date)));
                              }
                          } else if ($payPeriod == 'Weekly') {
                            $detail_time_period_start_date = date('Y-m-d', strtotime("+ 7 Days", strtotime($detail_time_period_start_date)));
                          } else if ($payPeriod == 'Bi-Weekly') {
                            $detail_time_period_start_date = date('Y-m-d', strtotime("+ 14 Days", strtotime($detail_time_period_start_date)));
                          }
                        }

                        /********* Skip,If Product Next Coverage for future list bill ************/
                         if(!($detail_time_period_start_date >= $time_period_start_date && $detail_time_period_start_date <= $time_period_end_date)){
                          unset($ws_row[$key]);
                          continue;
                        }

                       
                        /********* Skip,If Product Next Coverage for future list bill ************/

                        $product_dates = $enrollDate->getCoveragePeriod($detail_time_period_start_date, $payPeriod);
                        $detail_time_period_start_date = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
                        $detail_time_period_end_date = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));

                 
                    /****************** Product Next Payment coverage period code End  ***********/ 

                    if (!empty($ws_row['termination_date'])) {
                      //*************** Check New Plan AND Terminated Plan Start ***************
                      if ($ws_row['process_status'] == 'Pending' && !empty($ws_row['new_plan_id']) && !empty($ws_row['tier_change_date'])) {

                        $tire_change_date = $ws_row['tier_change_date'];
                        if (strtotime($time_period_start_date) <= strtotime($tire_change_date) && strtotime($tire_change_date) <= strtotime($time_period_end_date)) {
                          $new_ws_sql = "SELECT ce.id as ce_id,ws.id,ws.product_id,ws.plan_id,ws.price,ws.prd_plan_type_id 
                                                            FROM customer_enrollment ce 
                                                            JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                                                            WHERE
                                                            ce.parent_coverage_id=:parent_coverage_id AND 
                                                            ws.status='Pending' AND 
                                                            ce.process_status='Pending'";
                          $new_ws_row = $pdo->selectOne($new_ws_sql, array(":parent_coverage_id" => $ws_row['ce_id']));
                          if (!empty($new_ws_row)) {
                            $ws_row['id'] = $new_ws_row['id'];
                            $ws_row['product_id'] = $new_ws_row['product_id'];
                            $ws_row['plan_id'] = $new_ws_row['plan_id'];
                            $ws_row['prd_plan_type_id'] = $new_ws_row['prd_plan_type_id'];
                            $ws_row['price'] = $new_ws_row['price'];
                          }
                        }
                      } else {
                        if ($ws_row["is_listbill_refund_charge"] == "Y") {
                          $wsParams = array(
                            'is_listbill_refund_charge' => 'N',
                          );
                          $wsWhere = array("clause" => 'id=:id', 'params' => array(':id' => $ws_row["id"]));
                          $pdo->update("website_subscriptions", $wsParams, $wsWhere);
                        } 
                        if (strtotime($ws_row['termination_date'])<= strtotime($detail_time_period_start_date)) {
                          unset($ws_row[$key]);
                          continue;
                        }
                      }
                      //*************** Check New Plan AND Terminated Plan End   ***************
                    }

                      $quantity = $ws_row['qty'];
                      $price = $this->get_plan_pay_period_price($ws_row['price'], $payPeriod);

                      $listBillDetailsArr[] = array(
                        'ws_id' => $ws_row['id'],
                        'customer_id' => $ws_row['customer_id'],
                        'product_id' => $ws_row['product_id'],
                        'prd_matrix_id' => $ws_row['plan_id'],
                        'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                        'quantity' => $quantity,
                        'rate' => $price,
                        'amount' => $quantity * $price,
                        'eligibility_date' => date('Y-m-d', strtotime($ws_row['eligibility_date'])),
                        'start_coverage_date' => $detail_time_period_start_date,
                        'end_coverage_date' => $detail_time_period_end_date,
                        'is_cobra_coverage' => $ws_row['is_cobra_coverage'],
                        'transaction_type' => 'charged',
                      );

                      $items_total += $quantity * $price;

                      //* Update Website Subscription Coverage */
                      if (!$is_regenerate) {
                        $extra["listBillPayDate"] = $listBillPayDate;
                        $this->updateWebsiteSubscription($group_id, $ws_row['id'], $extra);
                      }
                      //* Update Website Subscription Coverage */

                    }
                  //*************** List Bill Details Code End   ***************
                  
                  $membersIdStr=implode(',',array_unique(array_column($subscribed_members,'customer_id')));

                  if(empty($membersIdStr)){
                    continue;
                  }
                  //*************** Cancel Last Bills if not paid Code Start ******************/
                    if(!$is_regenerate){
                      $openListBillSql = "SELECT DISTINCT lb.id
                      FROM list_bill_details lbd
                      JOIN list_bills lb ON( lbd.list_bill_id = lb.id AND lb.status IN ('Open') )
                      WHERE lbd.transaction_type='charged' AND lbd.is_reverse='N' AND lbd.customer_id IN (".$membersIdStr.")
                      ";
                      $openListBills = $pdo->select($openListBillSql);
                      if(!empty($openListBills)){
                        foreach($openListBills as $billId){
                          $tmpCancelParams = array(
                            'status' => 'Cancelled',
                          );
                          $tmpCancelParamsWHERE = array(
                            'clause' => 'id=:id',
                            'params' => array(':id'=>$billId['id']),
                          );
                          $pdo->update('list_bills', $tmpCancelParams, $tmpCancelParamsWHERE);  
                        }
                      }
                    }
                  //*************** Cancel Last Bills if not paid Code End ********************/

                  //*************** List Bill Past Amount Calculation Code Start ***************
                    $credits_applied = 0;
                    $past_due_amount = 0;
                    $received_amount = 0;
                    $due_amount = 0;

                    if(!$is_regenerate){

                        $previous_list_bill_sql = "SELECT lb.id,lb.received_amount,lb.grand_total,lb.amendment,lb.due_amount
                                                   FROM list_bills lb WHERE lb.status IN ('Cancelled','paid') AND lb.is_deleted = 'N' 
                                                   AND lb.customer_id = :customer_id AND lb.class_id =  :class_id ORDER BY lb.id DESC";
                        $previous_list_bill_where = array(':customer_id' => $group_id,':class_id' => $class_id);
                        $previous_list_bill_row = $pdo->selectOne($previous_list_bill_sql, $previous_list_bill_where);

                        if(!empty($previous_list_bill_row)){
                          $received_amount = $previous_list_bill_row['received_amount'];
                          $due_amount = $previous_list_bill_row['due_amount'];
                          if ($previous_list_bill_row['received_amount'] > $previous_list_bill_row['grand_total']) {
                            $credits_applied = $previous_list_bill_row['received_amount'] - $previous_list_bill_row['grand_total'];
                            $past_due_amount = $previous_list_bill_row['due_amount'];
                          } elseif ($previous_list_bill_row['received_amount'] < $previous_list_bill_row['grand_total']) {
                            $credits_applied = 0;
                            $past_due_amount = $previous_list_bill_row['grand_total'] - $previous_list_bill_row['received_amount'];
                          } else {
                            $past_due_amount = 0;
                            $credits_applied = 0;
                          }

                          if(!empty($previous_list_bill_row['amendment'])){
                              $past_due_amount = $past_due_amount + $previous_list_bill_row['amendment'];
                          }
                        }
                        
                    }else{                      
                      $pre_list_bill_sql = "SELECT past_due_amount,credits_applied,previous_list_bill
                                            FROM list_bills
                                             WHERE is_deleted = 'N'  AND id = :regenerate_id AND status ='Regenerate' ORDER BY id DESC";
                      $sch_params=array(
                        ':regenerate_id'=>$regenerate_id,
                      );
                      $pre_list_bill_res = $pdo->selectOne($pre_list_bill_sql, $sch_params);

                      if(!empty($pre_list_bill_res)){
                        $past_due_amount = $pre_list_bill_res['past_due_amount'];
                        $credits_applied = $pre_list_bill_res['credits_applied'];
                        $lastListBillsIds= $pre_list_bill_res['previous_list_bill'];
                      }
  
                    }
                  //*************** List Bill Past Due Amount Calculation Code Start ***************

                  //*************** List Bill Refund/Charge Adjustment Code Start ***************
                    if ($is_regenerate) {
                      $tmpRefundChargeParams = array(
                        'is_applied_to_list_bill' => 'N',
                        'list_bill_id' => 0,
                      );
                      $tmpRefundChargeParamsWhere = array(
                        'clause' => 'list_bill_id=:id AND is_deleted="N" and transaction_type="refund" ',
                        'params' => array(':id' => $regenerate_id)
                      );
                      $pdo->update('group_member_refund_charge', $tmpRefundChargeParams, $tmpRefundChargeParamsWhere);
                    }
                    $sch_params = array();
                    $incr = " AND gc.group_id = :group_id";
                    $sch_params[':group_id'] = $group_id;
                    if ($display_company) {
                      $incr .= " AND c.group_company_id = :company_id";
                      $sch_params[':company_id'] = $company_id;
                    }
              
                    $incr .= " AND cs.class_id= :class_id";
                    $sch_params[':class_id'] = $class_id;

                    $gmrc_sql = "SELECT gc.id,gc.transaction_type,gc.transaction_amount,gc.old_ws_id, gc.ws_id,gc.start_coverage_date,gc.end_coverage_date,gc.payment_received_details_id,ws.id as ws_id,ws.customer_id,ws.product_id,ws.plan_id,ws.prd_plan_type_id,ws.eligibility_date,ws.is_cobra_coverage,cs.class_id
                                          FROM group_member_refund_charge as gc 
                                          JOIN customer as c ON (gc.customer_id = c.id)
                                          JOIN customer_settings cs ON (c.id=cs.customer_id)
                                          JOIN website_subscriptions ws ON(ws.id = gc.ws_id)
                                          WHERE gc.is_applied_to_list_bill = 'N' AND gc.is_deleted = 'N' $incr ORDER BY gc.id ASC";
                    $gmrc_rows = $pdo->select($gmrc_sql, $sch_params);

                    $refund_charge_adjustment = 0.0;
                    $refundChargeArr = array();
                    if (!empty($gmrc_rows)) {
                      foreach ($gmrc_rows as $key => $gmrc_row) {
                        $refundChargeArr[] = $gmrc_row['id'];
                        if ($gmrc_row['transaction_type'] == "refund") {
                          $refund_charge_adjustment = $refund_charge_adjustment - $gmrc_row['transaction_amount'];
                        } else {
                          $refund_charge_adjustment = $refund_charge_adjustment + $gmrc_row['transaction_amount'];
                        }

                        $listBillDetailsArr[] = array(
                          'ws_id' => $gmrc_row['ws_id'],
                          'customer_id' => $gmrc_row['customer_id'],
                          'product_id' => $gmrc_row['product_id'],
                          'prd_matrix_id' => $gmrc_row['plan_id'],
                          'prd_plan_type_id' => $gmrc_row['prd_plan_type_id'],
                          'quantity' => '1',
                          'rate' => $gmrc_row['transaction_amount'],
                          'amount' =>  $gmrc_row['transaction_amount'],
                          'eligibility_date' => date('Y-m-d', strtotime($gmrc_row['eligibility_date'])),
                          'transaction_type' => $gmrc_row['transaction_type'],
                          'refund_charge_id' => $gmrc_row['id'],
                          'start_coverage_date' => $gmrc_row['start_coverage_date'],
                          'end_coverage_date' => $gmrc_row['end_coverage_date'],
                          'is_cobra_coverage' => $gmrc_row['is_cobra_coverage'],
                        );

                        if (!empty($gmrc_row['payment_received_details_id'])) {
                          $updateParams = array(
                            'is_reverse' => 'Y',
                          );
                          $updateWhere = array(
                            "clause" => 'id=:id',
                            'params' => array(':id' => $gmrc_row['payment_received_details_id'])
                          );
                          $pdo->update("list_bill_details", $updateParams, $updateWhere);
                        }
                      }
                    }
                  //*************** List Bill Refund/Charge Adjustment Code End   ***************

                  //*************** List Bill Admin Fee Code Start ***************
                    if ($is_regenerate) {
                      $tmpParams = array(
                        'is_applied' => 'N',
                        'applied_list_bill_id' => 0,
                      );
                      $tmpParamsWHERE = array(
                        'clause' => 'applied_list_bill_id=:id AND is_deleted="N"',
                        'params' => array(':id' => $regenerate_id)
                      );
                      $pdo->update('list_bill_admin_fee', $tmpParams, $tmpParamsWHERE);
                    }
                    $sch_params = array();
                    $incr = " AND af.group_id = :group_id AND af.class_id=:class_id";
                    $sch_params[':group_id'] = $group_id;
                    $sch_params[':class_id'] = $class_id;
                    
                    if ($display_company) {
                      $incr .= " AND af.group_company_id = :company_id";
                      $sch_params[':company_id'] = $company_id;
                    }
                    $adminFeeSql = "SELECT af.id,af.amount
                                          FROM list_bill_admin_fee as af 
                                          WHERE af.is_applied = 'N' AND af.is_deleted = 'N' $incr ORDER BY af.id ASC";
                    $adminFeeRes = $pdo->select($adminFeeSql, $sch_params);

                    $admin_fee = 0;
                    $adminFeeArr = array();
                    if (!empty($adminFeeRes)) {
                      foreach ($adminFeeRes as $key => $feeRow) {
                        $adminFeeArr[] = $feeRow['id'];
                        $admin_fee = $admin_fee + $feeRow['amount'];
                      }
                    }
                  //*************** List Bill Admin Fee Code End   ***************

                 

                  //*************** List Bill Generate Code Start ***************
                  if(!empty($listBillDetailsArr))
                  {

                    $list_bill_display_id = $this->getListBillId();
                    $list_bill_data = array(
                      'list_bill_no' => $list_bill_display_id,
                      'customer_id' => $group_id,
                      'company_id' => $company_id,
                      'status' => 'open',
                      'list_bill_date' => $listBillDate,
                      'list_bill_pay_date' => $listBillPayDate,
                      'payment_terms' => 'net_15',
                      'due_date' => $due_dates,
                      'time_period_start_date' => $time_period_start_date,
                      'time_period_end_date' => $time_period_end_date,
                      'shipping_charge' => 0,
                      'adjustment' => 0,
                      'received_amount' => 0,
                      'credits_applied' => $credits_applied,
                      'past_due_amount' => $past_due_amount,
                      'amendment' => 0,
                      'is_deleted' => 'N',
                    );

                    /************************  Fetch Last Bill Id Code Start ******************** */
                      if(!$is_regenerate){
                        $lastListBillSql = "SELECT max(lb.id) AS id
                        FROM list_bill_details lbd
                        JOIN list_bills lb ON( lbd.list_bill_id = lb.id AND lb.status NOT IN ('Regenerate') )
                        WHERE lbd.transaction_type='charged' AND lbd.is_reverse='N' AND lbd.customer_id IN (".$membersIdStr.")
                        GROUP BY lbd.customer_id
                        ";
                        $lastListBillsRes = $pdo->select($lastListBillSql);
                        if(!empty($lastListBillsRes)){
                          $lastListBillsIds=implode(',',array_unique(array_column($lastListBillsRes,'id')));
                        }
                      }

                      if (!empty($lastListBillsIds)) {
                        $list_bill_data['previous_list_bill'] = $lastListBillsIds;
                      }  
                    /************************  Fetch Last Bill Id Code End ******************** */

                    if ($company_row['is_auto_draft_set'] == "Y") {
                      $list_bill_data['next_purchase_date'] = $company_row['auto_draft_date'];
                    }
               
                    $list_bill_data['class_id'] = $class_id;
                  
                    
                    $list_bill_id = $pdo->insert('list_bills', $list_bill_data);
                    array_push($list_bill_id_arr, $list_bill_id);

                    //*************** List Bill Details Insert Code Start ******************/-
                      foreach($listBillDetailsArr as $details_row){

                        /** Check if coverage already exist or not */
                        $lbSql = "SELECT lbd.id as lbd_id
                                FROM list_bill_details lbd
                                WHERE 
                                lbd.list_bill_id = :list_bill_id AND 
                                lbd.ws_id = :ws_id AND 
                                lbd.start_coverage_date = :start_coverage_date AND
                                lbd.transaction_type = :transaction_type
                                ";
                        $lbWhere = array(
                              ":list_bill_id" => $list_bill_id, 
                              ":ws_id" => $details_row['ws_id'], 
                              ":start_coverage_date" => $details_row['start_coverage_date'],
                              ":transaction_type" => $details_row['transaction_type'],
                            );
                        $lbRow = $pdo->selectOne($lbSql,$lbWhere);
                        
                        if(empty($lbRow)){
                          $details_row['list_bill_id'] = $list_bill_id;
                          $pdo->insert('list_bill_details',$details_row);  

                          //if coverage is past refund/charge, then update is applied to list bill
                          if(!empty($details_row['refund_charge_id'])){
                            $gmrc_update_data = array(
                              'is_applied_to_list_bill' => 'Y',
                              'list_bill_id'=>$list_bill_id,
                            );
                            $gmrc_update_where = array('clause' => 'id =:id', 'params' => array(':id' => $details_row['refund_charge_id']));
                            $pdo->update('group_member_refund_charge', $gmrc_update_data, $gmrc_update_where);
                          }

                          $update_ws_data = array(
                            'last_list_bill_id' => $list_bill_id,
                          );
                          $update_ws_where = array("clause" => 'id=:id', 'params' => array(':id' => $details_row['ws_id']));
                          $pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);

                        }else if(!empty($details_row['refund_charge_id'])){
                          //if coverage is past refund/charge and already exist in list bill, then remove coverag

                          $gmrc_update_data = array(
                            'is_deleted' => 'Y',
                          );
                          $gmrc_update_where = array('clause' => 'id =:id', 'params' => array(':id' => $details_row['refund_charge_id']));
                          $pdo->update('group_member_refund_charge', $gmrc_update_data, $gmrc_update_where);
                          if ($details_row['transaction_type'] == "refund") {
                            $refund_charge_adjustment = $refund_charge_adjustment + $details_row['amount'];
                          } else {
                            $refund_charge_adjustment = $refund_charge_adjustment - $details_row['amount'];
                          }
                        }

                      }
                    //*************** List Bill Details Insert Code End *******************/

                    //*************** List Bill Update Total Code Start ***************
                      if($received_amount > $due_amount){
                        $grand_total = $items_total + $past_due_amount + $refund_charge_adjustment + $admin_fee;
                      }else{
                        $grand_total = ($items_total + $past_due_amount) - $credits_applied + $refund_charge_adjustment + $admin_fee;
                      }
                      $list_bill_update_data = array(
                        'items_total' => $items_total,
                        'refund_charge_adjustment' => $refund_charge_adjustment,
                        'grand_total' => $grand_total,
                        'due_amount' => $grand_total,
                        'admin_fee' => $admin_fee
                      );
                  
                      $list_bill_update_where = array('clause' => 'id=:id', 'params' => array(':id' => $list_bill_id));
                      $pdo->update('list_bills', $list_bill_update_data, $list_bill_update_where);
                    //*************** List Bill Update Total Code End   ***************

                    //***************Generate HRM payment start code ******************
                      if($isHRMpayment == true || $is_regenerate==true){
                        $coveragePaydateSql = "SELECT lb.id,lb.status,gcp.paydate AS coveragePaydate,h.pay_date
                            FROM list_bills lb 
                            JOIN list_bill_details lbd ON (lbd.list_bill_id = lb.id)
                            JOIN group_classes gc ON(gc.id=lb.class_id AND gc.is_deleted='N')
                            JOIN group_classes_paydates gcp ON(gcp.class_id = gc.id AND gcp.is_deleted='N')
                            JOIN hrm_payment h ON (h.group_id = lb.customer_id AND h.is_deleted='N')
                            WHERE lb.is_deleted='N' AND lb.status='open' AND lb.id=:list_bill_id AND lb.customer_id = :group_id AND gc.id=:class_id AND gcp.paydate >= :startCoverageDate AND gcp.paydate <= :endCoverageDate GROUP BY h.pay_date";
                        $coveragePaydateParams = array(
                          ':list_bill_id' => $list_bill_id,
                          ':group_id' =>$group_id,
                          ':startCoverageDate'=>$detail_time_period_start_date,
                          ':endCoverageDate'=>$detail_time_period_end_date,
                          ':class_id' =>$class_id,
                        );
                        $coveragePaydateRes = $pdo->select($coveragePaydateSql,$coveragePaydateParams);
                        if(!empty($coveragePaydateRes)){
                          foreach ($coveragePaydateRes as $key => $coveragePaydateRow) {
                            $function_list->add_hrm_payments($coveragePaydateRow['id'], $coveragePaydateRow['status'], array($coveragePaydateRow['pay_date']));
                            if($is_regenerate){
                              $hrmSel = "SELECT id,list_bill_id,list_bill_detail_id,payer_id,pay_date FROM hrm_payment WHERE list_bill_id=:regenerate_id AND group_id= :group_id";
                              $hrmParams=array(
                                ':regenerate_id'=>$regenerate_id,
                                ':group_id'=>$regenerate_group_id
                              );
                              $hrmRes = $pdo->select($hrmSel, $hrmParams);
                              if(!empty($hrmRes)){
                                foreach ($hrmRes as $key => $hrmValue) {
                                  $hrmlistbillSel = "SELECT id FROM list_bill_details WHERE list_bill_id=:list_bill_id AND customer_id=:customer_id";
                                  $hrmlistbillParams = array(':list_bill_id'=>$list_bill_id,':customer_id'=>$hrmValue['payer_id']);
                                  $hrmlistbillRes = $pdo->selectOne($hrmlistbillSel,$hrmlistbillParams);
                                  $listbillDetailId = !empty($hrmlistbillRes['id']) ? $hrmlistbillRes['id'] : '';
                                  $hrmListbillParams = array(
                                    'list_bill_id' => $list_bill_id,
                                    'list_bill_detail_id' => $listbillDetailId,
                                  );
                              
                                  $hrmListbillWhere = array('clause' => 'id=:id', 'params' => array(':id' => $hrmValue['id']));
                                  $pdo->update('hrm_payment', $hrmListbillParams, $hrmListbillWhere);
                                }
                              }
                            }
                          }
                        }
                      }  
                    //***************Generate HRM payment end code *****************  

                    //*************** List Bill Category Insert Code Start ***************
                      $listBillCategoryDetailsInsertID = $this->listBillCategoryDetailsInsert($list_bill_id);
                    //*************** List Bill Category Insert Code End   ***************

                    //*************** Email Send To Group And Agent Code Start ***************
                      $group_detail_sql = "SELECT c.id as c_id, c.email as c_email, c.fname as c_name,c.type as c_type,s.id as s_id, s.email as s_email, s.fname as s_name,s.type as s_type
                                      FROM customer as c 
                                      JOIN customer as s ON(c.sponsor_id=s.id)
                                      WHERE c.id=:id AND c.status IN('active','Suspended')";
                      $group_detail_res = $pdo->selectOne($group_detail_sql, array(":id" => $group_id));

                      $trigger_id = 71;
                      $group_fname = $group_detail_res['c_name'];
                      $sponsor_fname = $group_detail_res['s_name'];
                      if ($SITE_ENV == 'Local') {
                        $group_email = 'karan@cyberxllc.com';
                        $sponsor_email = array('karan@cyberxllc.com');
                      } else {
                        $group_email = $group_detail_res['c_email'];
                        $sponsor_email = array($group_detail_res['s_email']);
                        $sponsor_email[] = 'pramit@siddhisai.net';
                      }
                      $cus_trigger_param = array();
                      $cus_trigger_param['fname'] = $group_fname;
                      $cus_trigger_param['list_bill_id'] = $list_bill_display_id;
                      $cus_trigger_param['amount'] = displayAmount($grand_total, 2, 'USA');

                      $spon_trigger_param = array();
                      $spon_trigger_param['fname'] = $sponsor_fname;
                      $spon_trigger_param['list_bill_id'] = $list_bill_display_id;
                      $spon_trigger_param['amount'] = displayAmount($grand_total, 2, 'USA');

                      //Send Email to group user
                      trigger_mail($trigger_id, $cus_trigger_param, $group_email, array(), 3);

                      //Send Email to group parent agent
                      trigger_mail($trigger_id, $spon_trigger_param, $sponsor_email, array(), 3);

                      $ac_description['ac_message'] = array(
                        'ac_red_1' => array(
                          'title' => 'System',
                        ),
                        'ac_message_1' => "Generated List Bill",
                      );

                      $ac_description['key_value']['desc_arr']['List Bill'] = $list_bill_display_id;
                      $ac_description['key_value']['desc_arr']['Amount'] = displayAmount($grand_total, 2);
                      $ac_description['key_value']['desc_arr']['Due Date'] = date('m/d/Y', strtotime($due_dates));
                      $ac_description['key_value']['desc_arr']['Recipient'] = $group_email;

                      $ac_description['ac_message']['ac_red_2'] = array(
                        'href' => $ADMIN_HOST . '/view_listbill_statement.php?list_bill=' . md5($list_bill_id),
                        'title' => $list_bill_display_id
                      );
                      activity_feed(3, $group_detail_res['c_id'], $group_detail_res['c_type'], $group_detail_res['c_id'], 'Group', 'List Bill Generated', '', '', json_encode($ac_description));
                    //*************** Email Send To Group And Agent Code End   ***************

                  }

                   //*************** List Bill Admin Fee Update Code Start ***************
                   if (!empty($adminFeeArr) && !empty($list_bill_id)) {
                    foreach ($adminFeeArr as $key => $fee_id) {
                      $updateParams = array(
                        'is_applied' => 'Y',
                        'applied_list_bill_id' => $list_bill_id,
                      );
                      $updateWhere = array('clause' => 'id =:id', 'params' => array(':id' => $fee_id));
                      $pdo->update('list_bill_admin_fee', $updateParams, $updateWhere);
                    }
                  }
                //*************** List Bill Admin Fee Update Code End ***************
                  //*************** List Bill Generate Code End   ***************

                }
              }

              if($cFlag){
                continue;
              }

            }
          }
        }
      }
    }
    return $list_bill_id_arr;
  }

  public function getSubscriptionRefundChargeCoverage($ws_id, $listBillNextCoverageDate)
  {
  
    global $pdo;
    require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    require_once dirname(__DIR__) . '/includes/function.class.php';
    $function_list = new functionsList();
    $enrollDate = new enrollmentDate();
  
    $coverage_data = array();
    $REAL_IP_ADDRESS = get_real_ipaddress();
    $ws_sql = "SELECT ws.id,ws.customer_id,c.sponsor_id,ws.eligibility_date,ws.termination_date,ws.product_id,ws.price
                FROM website_subscriptions ws
                JOIN customer c ON(c.id = ws.customer_id)
                WHERE ws.id=:id";
    $ws_row = $pdo->selectOne($ws_sql, array(":id" => $ws_id));
  
    if (!empty($ws_row['id'])) {
      $pay_period = $this->get_pay_period_type($ws_row['id']); //fetch whether pay period is monthly/weekly/semi-monthly/bi-weekly
      if (!empty($ws_row['termination_date']) && strtotime($ws_row['termination_date']) > 0) {
        if (strtotime($ws_row['termination_date']) < strtotime($listBillNextCoverageDate)) {
          $listBillNextCoverageDate = $ws_row['termination_date'];
        }
      }
  
      /*----- Check Listbill generated when effective date changes--------*/
        $is_effective_date_changed=false;
        $OldlbSql = "SELECT lb.id,lbd.ws_id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
        FROM list_bills lb
        JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
        WHERE 
        lb.status IN('open','paid','Cancelled') AND 
        lb.is_deleted = 'N' AND 
        lbd.amount > 0 AND 
        lbd.ws_id = :ws_id ORDER BY lbd.start_coverage_date
        ";
        $oldlbWhere = array(":ws_id" => $ws_id);
        $oldlbRes = $pdo->select($OldlbSql, $oldlbWhere);                
        if(!empty($oldlbRes)){

            $firstCoverageStartDate=$oldlbRes[0]['start_coverage_date'];
            if($firstCoverageStartDate!=$ws_row['eligibility_date']){
              $is_effective_date_changed=true;
            }

            if($is_effective_date_changed){
                /******* Fetch New Coverage starting from new eligbility date  *****************/
                $tmpStartCoverageDate=$ws_row['eligibility_date'];
                $newCoverageArr=array();
                while (strtotime($tmpStartCoverageDate) < strtotime($listBillNextCoverageDate)) {
                  $product_dates = $enrollDate->getCoveragePeriod($tmpStartCoverageDate, $pay_period);
                  $startCoveragePeriod = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
                  $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
                  $tmpStartCoverageDate = date("Y-m-d", strtotime("+1 day", strtotime($endCoveragePeriod)));
                  $newCoverageArr[]=$startCoveragePeriod;
                }
                /******* Fetch New Coverage starting from new eligbility date  *****************/
        
                /******* Refund Old Coverages if not same as new coverages *****************/
                foreach ($oldlbRes as $row) {
                    if(!in_array($row['start_coverage_date'],$newCoverageArr)){
                      $coverage_data[] = array(
                        'transaction_type' => 'refund',
                        'transaction_amount' => $row['amount'],
                        'payment_received_from' => $row['id'],
                        'payment_received_details_id' => $row['detail_id'],
                        'start_coverage_date' => $row['start_coverage_date'],
                        'end_coverage_date' => $row['end_coverage_date'],
                        'description' => 'Refund coverage',
                      );
                  }
                }
                /******* Refund Old Coverages if not same as new coverages *****************/
            }

        }
      /*-----Check Listbill generated when effective date changes --------*/
  
      /*----- Check Missing Listbill --------*/
        if (strtotime($ws_row['eligibility_date']) != strtotime($ws_row['termination_date'])) {
  
          $lbSql = "SELECT lbd.end_coverage_date FROM list_bill_details lbd
                   JOIN list_bills lb ON( lbd.list_bill_id = lb.id AND lb.status IN ('open','paid','Cancelled') )
                   WHERE lbd.ws_id=:ws_id AND lbd.transaction_type='charged' AND lbd.is_reverse='N'  ORDER BY lbd.start_coverage_date DESC";  
          $lbWhere = array(":ws_id" => $ws_id);
          $prevMemberBillDetail = $pdo->selectOne($lbSql, $lbWhere);  
  
          $tmp_eligibility_date = (empty($prevMemberBillDetail) || $is_effective_date_changed)?$ws_row['eligibility_date']:date('Y-m-d',strtotime('+1 day',strtotime($prevMemberBillDetail['end_coverage_date']))); 
  
          while (strtotime($tmp_eligibility_date) < strtotime($listBillNextCoverageDate)) {
            $product_dates = $enrollDate->getCoveragePeriod($tmp_eligibility_date, $pay_period);
            $startCoveragePeriod = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
            $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));
    
            /*********check whether coverage already charged or not **************/
            $alreadylbChargeSql = "SELECT lbd.id FROM list_bill_details lbd
            JOIN list_bills lb ON( lbd.list_bill_id = lb.id AND lb.status IN ('open','paid','Cancelled') )
            WHERE lbd.ws_id=:ws_id AND lbd.transaction_type='charged' AND lbd.is_reverse='N' AND lbd.start_coverage_date= :start_coverage_date AND lbd.end_coverage_date= :end_coverage_date";  
            $alreadylbWhere = array(":ws_id" => $ws_id,":start_coverage_date"=>$startCoveragePeriod,":end_coverage_date"=>$endCoveragePeriod);
            $alreadyChargeRes = $pdo->selectOne($alreadylbChargeSql, $alreadylbWhere);  
  
            if(empty($alreadyChargeRes)){
                $price = $this->get_plan_pay_period_price($ws_row['price'], $pay_period);
                $coverage_data[] = array(
                  'transaction_type' => 'charged',
                  'transaction_amount' => $price,
                  'start_coverage_date' => $startCoveragePeriod,
                  'end_coverage_date' => $endCoveragePeriod,
                  'description' => 'Charge missing coverage',
              );              
            }
            /*********check whether coverage already charged or not **************/
            
            $tmp_eligibility_date = date("Y-m-d", strtotime("+1 day", strtotime($endCoveragePeriod)));
          }
        }
      /*-----/Check Missing Listbill --------*/

      /*----- Check Listbill generated after termination date --------*/
        if (strtotime($ws_row['eligibility_date']) == strtotime($ws_row['termination_date'])) {
          $lbSql = "SELECT lb.id,lbd.ws_id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
                      FROM list_bills lb
                      JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
                      WHERE 
                      lb.status IN('open','paid','Cancelled') AND 
                      lb.is_deleted = 'N' AND 
                      lbd.amount > 0 AND 
                      lbd.ws_id = :ws_id
                      ";
          $lbWhere = array(":ws_id" => $ws_id);
          $lbRes = $pdo->select($lbSql, $lbWhere);
          if (!empty($lbRes)) {
            foreach ($lbRes as $key => $row) {
              $coverage_data[] = array(
                'transaction_type' => 'refund',
                'transaction_amount' => $row['amount'],
                'payment_received_from' => $row['id'],
                'payment_received_details_id' => $row['detail_id'],
                'start_coverage_date' => $row['start_coverage_date'],
                'end_coverage_date' => $row['end_coverage_date'],
                'description' => 'Refund coverage',
              );
            }
          }
        } else {
          if (!empty($ws_row['termination_date']) && strtotime($ws_row['termination_date']) > 0) {
            $lbSql = "SELECT lb.id,lbd.ws_id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
                          FROM list_bills lb
                          JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
                          WHERE 
                          lb.status IN('open','paid','Cancelled') AND 
                          lb.is_deleted = 'N' AND 
                          lbd.amount > 0 AND 
                          lbd.ws_id = :ws_id AND 
                          lbd.start_coverage_date > :from_date
                          ";
            $lbWhere = array(":ws_id" => $ws_id, ":from_date" => $ws_row['termination_date']);
            $lbRes = $pdo->select($lbSql, $lbWhere);
            if (!empty($lbRes)) {
              foreach ($lbRes as $key => $row) {
                $coverage_data[] = array(
                  'transaction_type' => 'refund',
                  'transaction_amount' => $row['amount'],
                  'payment_received_from' => $row['id'],
                  'payment_received_details_id' => $row['detail_id'],
                  'start_coverage_date' => $row['start_coverage_date'],
                  'end_coverage_date' => $row['end_coverage_date'],
                  'description' => 'Refund coverage',
                );
              }
            }
          }
        }
      /*-----/Check Listbill generated after termination date --------*/
    }

    // pre_print($coverage_data);
    if (!empty($coverage_data)) {
      foreach ($coverage_data as $coverage_row) {
        $rc_data = array(
          'customer_id' => $ws_row['customer_id'],
          'ws_id' => $ws_row['id'],
          'group_id' => $ws_row['sponsor_id'],
          'transaction_type' => $coverage_row['transaction_type'],
          'transaction_amount' => $coverage_row['transaction_amount'],
          'start_coverage_date' => $coverage_row['start_coverage_date'],
          'end_coverage_date' => $coverage_row['end_coverage_date'],
          'description' => $coverage_row['description'],
          'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
          'req_url' => 'system',
        );
        if ($coverage_row['transaction_type'] == "refund") {
          $rc_data['payment_received_from'] = $coverage_row['payment_received_from'];
          $rc_data['payment_received_details_id'] = $coverage_row['payment_received_details_id'];
        }
        $pdo->insert('group_member_refund_charge', $rc_data);
      }
      $wsParams = array(
        'is_listbill_refund_charge' => 'Y',
      );
      $wsWhere = array("clause" => 'id=:id', 'params' => array(':id' => $ws_id));
      $pdo->update("website_subscriptions", $wsParams, $wsWhere);
    }
    return $coverage_data;
  }

  public function listBillMemberAdjustment($type,$ws_id,$customer_id = 0, $sponsor_id = 0,$termination_date='',$is_effective_date = false) {
    global $pdo;

    $REAL_IP_ADDRESS = get_real_ipaddress();
    return $this->getSubscriptionRefundChargeCoverage($ws_id);

    if ($customer_id == 0) {
      $customer_id = getname('website_subscriptions', $ws_id, 'customer_id', 'id');
    }

    if($sponsor_id == 0) {
      $sponsor_id = getname('customer', $customer_id, 'sponsor_id', 'id');
    }
    $REQ_URL = ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

    if($type == "refund" && !empty($termination_date)){
      $incr = '';
      $desc = '';
    
      if($is_effective_date == true){
        $incr .= ' AND lbd.start_coverage_date < :from_date';
        $desc .= 'Effective date updated';
      }else{
        $incr .= ' AND lbd.start_coverage_date >= :from_date';
        $desc .= 'Terminate subscription';
      }

      $refundChargeSql = "SELECT id 
                          FROM group_member_refund_charge 
                          WHERE ws_id = :ws_id AND group_id = :group_id 
                          AND is_deleted = 'N' AND customer_id = :customer_id 
                          AND is_deleted='N' AND is_applied_to_list_bill='N'";
      $refundChargeParams = array(":ws_id" => $ws_id,
                          ":group_id" => $sponsor_id,
                          ":customer_id" => $customer_id);
      $refundChargeRes = $pdo->select($refundChargeSql,$refundChargeParams);
      
      if(!empty($refundChargeRes)){
        $updateData = array(
            'is_deleted' => 'Y',
        );
        $updateWhere = array("clause" => 'ws_id=:ws_id AND is_deleted="N" AND is_applied_to_list_bill="N"', 
          'params' => array(':ws_id' => $ws_id));
        $pdo->update("group_member_refund_charge", $updateData, $updateWhere);
      }

      // Check List Bill Already Generated For this subscription
      $lbSql = "SELECT lb.id,lbd.ws_id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
          FROM list_bills lb
          JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
          WHERE lb.is_deleted = 'N' AND lbd.ws_id = :ws_id AND lb.status IN('open','paid') $incr";
      $lbWhere = array(":ws_id" => $ws_id, ":from_date" => date('Y-m-d', strtotime($termination_date)));
      $lbRes = $pdo->select($lbSql,$lbWhere);
     
      if(!empty($lbRes)){
        foreach ($lbRes as $key => $row) {
          if($row['amount'] > 0){
            $refund_charge_data = array(
              'customer_id' => $customer_id,
              'ws_id' => $ws_id,
              'group_id' => $sponsor_id,
              'transaction_type' => 'refund',
              'transaction_amount' => $row['amount'],
              'payment_received_from' => $row['id'],
              'payment_received_details_id' => $row['detail_id'],
              'start_coverage_date' => $row['start_coverage_date'],
              'end_coverage_date' => $row['end_coverage_date'],
              'description' => $desc,
              'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
              'req_url' => $REQ_URL,
            );
            $pdo->insert('group_member_refund_charge', $refund_charge_data);
          }
        }

        $wsParams = array(
          'is_listbill_refund_charge' => 'Y',
        );
        $wsWhere = array("clause" => 'id=:id', 'params' => array(':id' => $ws_id));
        $pdo->update("website_subscriptions", $wsParams, $wsWhere);
      }
    }else if($type == "charged" && !empty($termination_date)){
      $incr = '';
      $desc = '';

      $incr .= ' AND lbd.start_coverage_date <= :termination_date';
      $desc .= 'Terminate date updated';

      $refundChargeSql = "SELECT id 
                          FROM group_member_refund_charge 
                          WHERE ws_id = :ws_id AND group_id = :group_id 
                          AND is_deleted = 'N' AND customer_id = :customer_id 
                          AND is_deleted='N' AND is_applied_to_list_bill='N'";
      $refundChargeParams = array(":ws_id" => $ws_id,
                          ":group_id" => $sponsor_id,
                          ":customer_id" => $customer_id);
      $refundChargeRes = $pdo->select($refundChargeSql,$refundChargeParams);
          
      if(!empty($refundChargeRes)){
        $updateData = array(
            'is_deleted' => 'Y',
        );
        $updateWhere = array("clause" => 'ws_id=:ws_id AND is_deleted="N" AND is_applied_to_list_bill="N"', 
          'params' => array(':id' => $row['id']));
        $pdo->update("group_member_refund_charge", $updateData, $updateWhere);
      }

      // Check List Bill Already Generated For this subscription
      $lbSql = "SELECT lb.id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
          FROM list_bills lb
          JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' AND lbd.transaction_type='charged')
          WHERE lb.is_deleted = 'N' AND lbd.ws_id = :ws_id AND lb.status IN('open','paid') $incr 
          GROUP BY lbd.start_coverage_date ORDER BY lb.id DESC";
      $lbWhere = array(":ws_id" => $ws_id, ":termination_date" => date('Y-m-d', strtotime($termination_date)));
      $lbRes = $pdo->select($lbSql,$lbWhere);
    
      if(!empty($lbRes)){
        foreach ($lbRes as $row) {
          if($row['amount'] > 0){
            $refund_charge_data = array(
              'customer_id' => $customer_id,
              'ws_id' => $ws_id,
              'group_id' => $sponsor_id,
              'transaction_type' => 'charged',
              'transaction_amount' => $row['amount'],
              'payment_received_from' => $row['id'],
              'payment_received_details_id' => $row['detail_id'],
              'start_coverage_date' => $row['start_coverage_date'],
              'end_coverage_date' => $row['end_coverage_date'],
              'description' => $desc,
              'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
              'req_url' => $REQ_URL,
            );
            $pdo->insert('group_member_refund_charge', $refund_charge_data);
          }
        }

        $wsParams = array(
          'is_listbill_refund_charge' => 'Y',
        );
        $wsWhere = array("clause" => 'id=:id', 'params' => array(':id' => $ws_id));
        $pdo->update("website_subscriptions", $wsParams, $wsWhere);
      }
    }else if($type == "reinstate"){
      $incr = '';
      $desc = '';
      
      $today = date("Y-m-d");

      $wsSql = "SELECT ws.id,IF(ws.qty = 0, 1, ws.qty) as quantity,ws.price,ws.eligibility_date
                  FROM website_subscriptions ws
                  WHERE ws.id=:id";
      $wsRes = $pdo->selectOne($wsSql, array(":id" => $ws_id));

      if(!empty($wsRes)){
        $tmp_eligibility_date = date('Y-m-01',strtotime($wsRes["eligibility_date"]));
     
        $refundChargeSql = "SELECT id 
                            FROM group_member_refund_charge 
                            WHERE ws_id = :ws_id AND group_id = :group_id 
                            AND customer_id = :customer_id
                            AND is_deleted='N' AND is_applied_to_list_bill='N'";
        $refundChargeParams = array(":ws_id" => $ws_id,
                            ":group_id" => $sponsor_id,
                            ":customer_id" => $customer_id);
        $refundChargeRes = $pdo->selectOne($refundChargeSql,$refundChargeParams);
      
        if(!empty($refundChargeRes)){
          $updateData = array(
              'is_deleted' => 'Y',
          );
          $updateWhere = array("clause" => 'ws_id=:ws_id AND is_deleted="N" AND is_applied_to_list_bill="N"', 
            'params' => array(':ws_id' => $ws_id));
          $pdo->update("group_member_refund_charge", $updateData, $updateWhere);
        }

        $is_last_coverage_period = false;
        while ($is_last_coverage_period == false) {
          $startCoveragePeriod = date('Y-m-d',strtotime($tmp_eligibility_date));
          $endCoveragePeriod = date('Y-m-t',strtotime($startCoveragePeriod));
         
          $tmp_eligibility_date = date("Y-m-d",strtotime("+1 day",strtotime($endCoveragePeriod)));
         
          if(strtotime($today) <= strtotime($startCoveragePeriod)) {
            $is_last_coverage_period = true; 
            continue;           
          }

          $incr = '';
          $desc = '';

          $incr .= ' AND lbd.start_coverage_date = :start_coverage_date AND lbd.end_coverage_date = :end_coverage_date';
          $desc .= 'Reinstate subscription';

          $ws_payment_status = subscriotion_has_approved_payment_this_coverage($wsRes['id'],$startCoveragePeriod);
        
          if(empty($ws_payment_status['success']) || $ws_payment_status['success'] == false){
            // Check List Bill Already Generated For this subscription
            $lbSql = "SELECT lb.id,lbd.amount,lbd.id as detail_id,lbd.start_coverage_date,lbd.end_coverage_date
                FROM list_bills lb
                JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id AND lbd.is_reverse='N' 
                AND lbd.transaction_type='charged')
                WHERE lb.is_deleted = 'N' AND lbd.ws_id = :ws_id AND lb.status IN('open','paid') $incr";
            $lbWhere = array(":ws_id" => $ws_id,
                            ":start_coverage_date" => $startCoveragePeriod,
                            ":end_coverage_date" => $endCoveragePeriod,
                            );
            $lbRes = $pdo->selectOne($lbSql,$lbWhere);


            if(empty($lbRes)){
              $refund_charge_data = array(
                'customer_id' => $customer_id,
                'ws_id' => $ws_id,
                'group_id' => $sponsor_id,
                'transaction_type' => 'charged',
                'transaction_amount' => ($wsRes['quantity'] * $wsRes['price']),
                'start_coverage_date' => $startCoveragePeriod,
                'end_coverage_date' => $endCoveragePeriod,
                'description' => $desc,
                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                'req_url' => $REQ_URL,
              );
              $pdo->insert('group_member_refund_charge', $refund_charge_data);

              $wsParams = array(
                'is_listbill_refund_charge' => 'Y',
              );
              $wsWhere = array("clause" => 'id=:id', 'params' => array(':id' => $ws_id));
              $pdo->update("website_subscriptions", $wsParams, $wsWhere);
            }
          }
        }
      }
    }
  }
}
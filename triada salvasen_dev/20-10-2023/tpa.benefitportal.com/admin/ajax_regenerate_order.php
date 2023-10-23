<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';

$response = array();
$validate = new Validation();
$enrollDate = new enrollmentDate();

  $today = date('Y-m-d');
  $customer_id = $_POST['customer_id'];
  $order_id = $_POST['order_id'];
  $order_display_id = $_POST['order_display_id'];

  $selected_product = checkIsset($_POST['selected_product']);
  $product_type = $_POST['product_type'];
  $product_plan_id = $_POST['product_plan_id'];
  $start_coverage_date = $_POST['start_coverage_date'];
  $fee_applied_for_product = $_POST['fee_applied_for_product'];
  $ord_regenerate_product = $_POST['ord_regenerate_product'];

  $post_payment_date = $_POST['post_payment_date'];
  $payment_method = $_POST['payment_method'];

  if($order_id != '' && $customer_id != ''){
    if(empty($selected_product)){
      $validate->setError("selected_product","Please select product for regenerate order");
    }else{

      $old_start_coverage_date_arr = $old_end_coverage_date_arr  = $old_effective_date_arr = array();
      /*---------- Coverage Date Validation Start ---------*/ 
        foreach ($selected_product as $product_id) {

          if($product_type[$product_id] == 'Fees'){ 
            $start_coverage_date[$product_id] = $start_coverage_date[$fee_applied_for_product[$product_id]];
            $ord_regenerate_product[$product_id] = $ord_regenerate_product[$fee_applied_for_product[$product_id]];
          }
          $dateObj = get_product_effective_detail($product_id, $today);

          if($product_type[$product_id] == 'Fees') {
            continue;
          }
          if(empty($ord_regenerate_product[$product_id]) || strtotime($ord_regenerate_product[$product_id]) < 0) {
            $validate->setError('ord_regenerate_product_'.$product_id,'Please select plan date');
          } else {
            if(strtotime($ord_regenerate_product[$product_id]) < strtotime($dateObj->default_effective_from)) {
              $validate->setError('ord_regenerate_product_'.$product_id, 'Plan date must be greater than or equal to ' . date('m/d/Y', strtotime($dateObj->default_effective_from)));
            } else {
              if($dateObj->calender_type == "monthly" && date('d',strtotime($ord_regenerate_product[$product_id])) != "01") {
                $validate->setError('ord_regenerate_product_'.$product_id, 'Please select only first day of month for this product.');
              }
            }
          }
        }
      /*---------- Coverage Date Validation Ends ---------*/      
      /*---------- lowest_coverage_date code starts ---------*/
        $lowest_coverage_date = '';
        if(!empty($ord_regenerate_product)){
          foreach($ord_regenerate_product as $key => $value){
            if(empty($value)){
              unset($ord_regenerate_product[$key]);
            }
          }
          if(!empty($ord_regenerate_product)){
            $lowest_coverage_date = min(array_map(function($item) { return $item; }, array_values($ord_regenerate_product)));
          }
        }
      /*---------- lowest_coverage_date code ends ---------*/

      /*---------- Payment Date Validation Start ---------*/
        $validate->string(array('required' => true, 'field' => 'post_payment_date', 'value' => $post_payment_date), array('required' => 'Please Select Payment Date'));

        if (empty($validate->getError('post_payment_date'))) {
          if (strtotime($post_payment_date) >= strtotime($lowest_coverage_date)) {
            $validate->setError('post_payment_date', 'Payment date must be less than ' . date('m/d/Y', strtotime($lowest_coverage_date)));
          }
          if (strtotime($post_payment_date) <= strtotime("-1 days",strtotime(date('Y-m-d')))) {
            $validate->setError('post_payment_date', 'Payment date must future date');
          }
        }
      /*---------- Payment Date Validation Ends ---------*/

      $validate->string(array('required' => true, 'field' => 'payment_method', 'value' => $payment_method), array('required' => 'Please Select Payment Method'));
    }  

    if ($validate->isValid()) {
      $order_sql = "SELECT o.id,o.is_renewal,o.customer_id
                              FROM orders o 
                              JOIN customer c ON(o.customer_id = c.id) 
                              JOIN order_billing_info ob ON(o.id = ob.order_id) 
                              WHERE md5(o.id)=:display_id";
      $order_where = array(":display_id" => $order_id);
      $order_row = $pdo->selectOne($order_sql, $order_where);

        $coverage_dates = array();
        if(!empty($product_plan_id)){
          foreach($product_plan_id as $key => $value){
            if(in_array($key,array_keys($ord_regenerate_product))){
              $coverage_dates[$value] = $ord_regenerate_product[$key];
            }            
          }
        }
        if (count($order_row) > 0 && !empty($coverage_dates)) {
          include_once dirname(__DIR__) . '/includes/function.class.php';
          $functionLst = new functionsList();
          $functionLst->create_reorder($order_row['id'], $order_row['customer_id'], $payment_method, $post_payment_date, $order_row['is_renewal'], $coverage_dates,$order_display_id);
        }
        
      $res['status'] = "success";
    }else {
        $res['status'] = "fail";
        $res['errors'] = $validate->getErrors();
    }
  }else {
      $res['status'] = "not_found";
  }

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit();
?>
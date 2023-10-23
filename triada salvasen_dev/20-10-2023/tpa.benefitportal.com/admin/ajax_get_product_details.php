<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();
$response['status'] = 'fail';
$product_value = !empty($_POST['product_value']) ? $_POST['product_value'] : array();
// $variation_pro_val = !empty($_POST['variation_pro_val']) ? $_POST['variation_pro_val'] : array();
$display = isset($_POST['display']) ? $_POST['display'] : '' ;
$payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : '' ;
if(!empty($product_value)){

    $incr = '';
    $incr .= ' AND p.id IN ('.implode(',', $product_value).')';
    $field_incr = $table_incr = '';
    $sch_param = array();
    if(!empty($payment_id) && !empty($display))
      {
        $table_incr.=" LEFT JOIN payment_master_assigned_product pp ON( pp.product_id = p.id and pp.is_deleted='N' AND md5(pp.payment_master_id)=:id ) "  ;
        $field_incr = ' ,pp.created_at ';
        $sch_param[':id'] = $payment_id;
      }

    $product_res = $pdo->select("SELECT p.id,p.product_code,p.name $field_incr FROM prd_main p $table_incr  WHERE p.is_deleted = 'N' " .$incr. " ",$sch_param);
    if(!empty($product_res) && count($product_res) > 0){
        $table_date = '<div class="table-responsive m-b-25">
              <table class="'.$table_class.'">
                <tbody>
                  <thead>
                    <tr>
                      <th >Product ID</th>
                      <th >Product Name</th>
                      <th style="width: 70px" id="product_action">Action</th>
                    </tr>
                  </thead>
                  <tbody>';
        if(!empty($display) && $display=='display'){
          $table_date = '<div class="table-responsive">
              <table class="'.$table_class.'">
                <tbody>
                  <thead>
                    <tr>
                      <th>Added Date</th>
                      <th>Product ID</th>
                      <th>Product Name</th>
                    </tr>
                  </thead>
                  <tbody>';
          foreach ($product_res as $key => $value) {
            $table_date .= '<tr>
                      <td >'.getCustomDate($value['created_at']).'</td>
                      <td >'.$value['product_code'].'</td>
                      <td >'.$value['name'] .'</td>
                    </tr>';
          }
        }else{
          foreach ($product_res as $key => $value) {
            $is_seleted = '';
            // if(!empty($variation_pro_val) && array_key_exists($value['id'], $variation_pro_val)){
            //     $is_seleted = 'checked';
            // }
            $table_date .= '<tr>
                      <td >'.$value['product_code'].'</td>
                      <td >'.$value['name'] .'</td>
                      <td class="icons"><a href="javascript:void(0);" data-toggle="tooltip" title="Delete" data-placement="top" class="product_selected" data-id="'.$value['id'].'"><i class="fa fa-trash"></i></a></td>
                    </tr>';
        }
        }
        $table_date .= '</tbody>
                </tbody>
              </table>
            </div>';
        $response['status'] = 'success';
        $response['data_html'] = $table_date;
    }
}

echo json_encode($response);
dbConnectionClose();
exit;
?>

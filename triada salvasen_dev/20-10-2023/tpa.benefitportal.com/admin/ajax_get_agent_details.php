<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();
$response['status'] = 'fail';
$agent_value = !empty($_POST['agent_value']) ? $_POST['agent_value'] : array();
$agent_downline_val = !empty($_POST['agent_downline_val']) ? $_POST['agent_downline_val'] : array();
$agent_loa_val = !empty($_POST['agent_loa_val']) ? $_POST['agent_loa_val'] : array();
$display = isset($_POST['display']) ? $_POST['display'] : '' ;
$payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : '' ;
if(!empty($agent_value)){
    $incr = '';
    $incr .= ' AND c.id IN ('.implode(',', $agent_value).')';
    $field_incr = $table_incr = '';
    $sch_param = array();
    if(!empty($payment_id) && !empty($display))
      {
        $table_incr.=" LEFT JOIN payment_master_assigned_agent pa ON( pa.agent_id = c.id and pa.is_deleted='N' AND md5(pa.payment_master_id)=:id AND pa.status!='Deleted') "  ;
        $field_incr = ' ,pa.created_at ';
        $sch_param[':id'] = $payment_id;
      }
    $customer_res = $pdo->select("SELECT c.id,c.rep_id,c.fname,c.lname $field_incr FROM customer c  $table_incr  WHERE c.is_deleted = 'N' " .$incr. " ",$sch_param);
    if(!empty($customer_res) && count($customer_res) > 0){
        $table_date = '<div class="table-responsive m-b-15">
              <table class="'.$table_class.'">
                <tbody>
                  <thead>
                    <tr>
                      <th>Agent ID</th>
                      <th>Name</th>
                      <th class="text-center">Include Downline?</th>
                      <th class="text-center">Include LOA?</th>
                      <th width="70px">Action</th>
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
                      <th>Agent ID</th>
                      <th>Name</th>
                    </tr>
                  </thead>
                  <tbody>';
          foreach ($customer_res as $key => $value) {
            $table_date .= '<tr>
                      <td >'.getCustomDate($value['created_at']).'</td>
                      <td >'.$value['rep_id'].'</td>
                      <td >'.$value['fname'].' '.$value['lname'] .'</td>
                    </tr>';
          }
        }else{
          foreach ($customer_res as $key => $value) {
            $is_seleted_loa = $is_seleted = '';
            if(!empty($agent_downline_val) && array_key_exists($value['id'], $agent_downline_val)){
                $is_seleted = 'checked';
            }
            if(!empty($agent_loa_val) && array_key_exists($value['id'], $agent_loa_val)){
              $is_seleted_loa = 'checked';
          }
            $table_date .= '<tr>
                      <td >'.$value['rep_id'].'</td>
                      <td >'.$value['fname'].' '.$value['lname'] .'</td>
                      <td class="text-center"><input name="agents_downline['.$value['id'].']" class="agents_downline" type="checkbox" data-id="'.$value['id'].'" '.$is_seleted.'></td>
                      <td class="text-center"><input name="agents_loa['.$value['id'].']" class="agents_loa" type="checkbox" data-id="'.$value['id'].'" '.$is_seleted_loa.'></td>
                      <td class="icons"><a href="javascript:void(0);" data-toggle="tooltip" title="Delete" data-placement="top" class="agent_selected" data-id="'.$value['id'].'"><i class="fa fa-trash"></i></a></td>
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

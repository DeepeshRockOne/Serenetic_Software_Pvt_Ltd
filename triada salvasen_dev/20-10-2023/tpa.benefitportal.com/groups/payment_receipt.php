<?php
include_once __DIR__ . '/includes/connect.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'payment_receipt.php';
$breadcrumbes[1]['title'] = 'Billing';

$order_id = $_GET['order_id'];

$order_found = false;
if(!empty($order_id)){
	$order_sql = "SELECT o.*,ob.payment_mode,ob.card_type,ob.last_cc_ach_no 
					FROM orders o 
					LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
					WHERE md5(o.id)=:id";
	$order_row = $pdo->selectOne($order_sql,array(":id"=>$order_id));
	if($order_row){
		$order_found = true;
	}
}

if(!$order_found){
	setNotifyError('No Receipt Found');
	redirect('group_billing.php');
}

$list_bill_id = $order_row['list_bill_id'];
$sqlListBill = "SELECT lb.*,c.business_name as group_name,c.address,c.city,c.state,c.zip
					FROM list_bills lb 
					JOIN customer c ON(c.id = lb.customer_id)
					WHERE lb.id=:id";
$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

$group_id = $resListBill['customer_id'];
$group_name = $resListBill['group_name'];
$group_company_id = $resListBill['company_id'];
$past_due_amount = $resListBill['past_due_amount'];
$credits_applied = $resListBill['credits_applied'];
$list_bill_adjustment = $resListBill['adjustment'];

$address = $resListBill['address'];
$city = $resListBill['city'];
$state = $resListBill['state'];
$zip = $resListBill['zip'];

$invoice_no = $resListBill['list_bill_no'];
$invoice_date = !empty($resListBill['list_bill_date']) ? date('F d, Y',strtotime($resListBill['list_bill_date'])) : '-';

$due_date = !empty($resListBill['due_date']) ? date('F d, Y',strtotime($resListBill['due_date'])) : '-';

$invoice_total = displayAmount(($resListBill['grand_total'] + $resListBill['amendment']),2);
$amendment = displayAmount($resListBill['amendment'],2);
$received_amount = displayAmount($resListBill['received_amount'],2);
$forward_amount = displayAmount($resListBill['amendment'],2);

$group_company_name = '';
$exStylesheets = array('css/mpdf_common_style.css');
if(!empty($group_company_id)) {
	$group_cmp_row = $pdo->selectOne("SELECT id,name,location FROM group_company WHERE id=:id",array(":id"=>$group_company_id));
	if(!empty($group_cmp_row)) {
		$group_company_name = $group_cmp_row['name'];
	}
}

if(!empty($group_cmp_res)){
    foreach ($group_cmp_res as $key => $value) {
      $group_company_res[$key+1] = $value;        
    }
}
if(isset($_REQUEST['export'])) {
	ob_start();
	include_once 'tmpl/payment_receipt.inc.php';
	$pdf_html_code = ob_get_clean();

	require_once '../libs/mpdf/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf();
	$stylesheet = file_get_contents('../css/mpdf_common_style.css');
	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($pdf_html_code,2);
	$mpdf->use_kwt = true;
	$mpdf->shrink_tables_to_fit = 1;
	$mpdf->Output("List Bill-".$invoice_no."_PaymentReceipt.pdf",'D');
	exit;
} else {
	$template = '../tmpl/payment_receipt.inc.php';
	$layout = 'main.layout.php';
	include_once 'layout/end.inc.php';
}
	
?>

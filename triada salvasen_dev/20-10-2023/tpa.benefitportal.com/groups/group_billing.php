<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'group_billing.php';
$breadcrumbes[1]['title'] = 'Billing';

$group_id = $_SESSION['groups']['id'];
$sqlGroup = "SELECT c.business_name,cgs.invoice_broken_locations 
            FROM customer c 
            JOIN customer_group_settings cgs ON (cgs.customer_id=c.id)
			WHERE c.id=:group_id";
$resGroup = $pdo->selectOne($sqlGroup,array(":group_id"=>$group_id));
$group_name = '';

$checkPaymentButtonDisplay = checkPaymentButtonDisplay($group_id);
if(!empty($resGroup)){
	$group_name = $resGroup['business_name'];
}
//********************** Code Start **********************
//********************** Code End   **********************

//********************** List Bill Invoice Code Start **********************
	//lb.id,lb.status,lb.list_bill_date,lb.list_bill_no,md5(lb.id) as secured,gc.name as group_company_name,lb.grand_total,lb.received_amount,lb.due_amount
	$invoiceSql = "SELECT lb.id,lb.status,lb.list_bill_date,lb.list_bill_no,md5(lb.id) as secured,gc.name as group_company_name,lb.grand_total,lb.received_amount,lb.due_amount,lb.amendment,lb.created_at,gcl.class_name
                    FROM list_bills lb 
                    LEFT JOIN group_company as gc ON (gc.group_id = lb.customer_id AND lb.company_id = gc.id)
                    LEFT JOIN group_classes as gcl ON (gcl.id = lb.class_id AND gcl.is_deleted='N')
                    WHERE lb.is_deleted ='N' AND lb.status IN('open','paid','Past Due', 'Cancelled','Regenerate') AND lb.customer_id = :group_id ORDER BY  lb.created_at DESC";
	$invoiceRes = $pdo->select($invoiceSql,array(":group_id"=>$group_id));
//********************** List Bill Invoice Code End   **********************

/*------- List Bill Order ------*/
$lb_order_sql = "SELECT o.list_bill_id,o.transaction_id,o.grand_total,o.id,lb.list_bill_no,ob.payment_mode,ob.card_type,ob.last_cc_ach_no,o.created_at
                FROM orders o 
                JOIN list_bills lb ON(lb.id = o.list_bill_id)
                LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
                WHERE o.status IN('Pending Settlement','Payment Approved') AND lb.is_deleted = 'N' AND o.customer_id=:customer_id ORDER BY o.created_at DESC";
$lb_order_res = $pdo->select($lb_order_sql,array(":customer_id"=>$group_id));
/*-------/List Bill Order ------*/

$open_lb_sql = "SELECT lb.id FROM list_bills lb WHERE lb.is_deleted ='N' AND lb.customer_id=:customer_id AND lb.status = 'open' ";
$open_lb_row = $pdo->selectOne($open_lb_sql,array(":customer_id"=>$group_id));
$has_open_list_bill = false;
if(!empty($open_lb_row['id'])) {
    $has_open_list_bill = true;
}
//$resGroup['invoice_broken_locations'] = "N";
$invoice_broken_locations = "N";
$group_company_res = array();
$selected_company_id = 0;
if(!empty($resGroup['invoice_broken_locations']) && $resGroup['invoice_broken_locations'] == 'Y'){
    $invoice_broken_locations = "Y";
    $gc_res = $pdo->select("SELECT id,name FROM group_company WHERE group_id=:customer_id AND is_deleted='N' ORDER BY name",array(':customer_id'=>$group_id));
    if(!empty($gc_res)) {
        $group_company_res[] = array(
            'id' => 0,
            'name' => $group_name,
        );
        foreach ($gc_res as $key => $gc_row) {
            $group_company_res[] = $gc_row;
        }
        if(isset($group_company_res[0]['id'])) {
            $selected_company_id = $group_company_res[0]['id'];
        }
    }
    
}
$template = 'group_billing.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

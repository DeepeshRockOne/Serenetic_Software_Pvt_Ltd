<?php
include_once __DIR__ . '/includes/connect.php'; 
$group_id = $_SESSION['groups']['id'];
$group_setting_sql = "SELECT cgs.*,c.business_name,cb.payment_mode,cb.card_type,cb.last_cc_ach_no 
						FROM customer_group_settings cgs 
						JOIN customer c ON(c.id = cgs.customer_id) 
						LEFT JOIN customer_billing_profile cb ON(cb.id=cgs.billing_id)
						WHERE cgs.customer_id=:group_id";
$group_setting_row = $pdo->selectOne($group_setting_sql,array(":group_id"=>$group_id));
if(!empty($group_setting_row)) {
	if($group_setting_row['invoice_broken_locations'] == "Y") {
		$gc_sql = "SELECT gc.*,cb.payment_mode,cb.card_type,cb.last_cc_ach_no,gc.id as company_id  
					FROM group_company gc 
					LEFT JOIN customer_billing_profile cb ON(cb.id=gc.billing_id)
					WHERE gc.is_deleted='N' AND gc.group_id=:group_id ORDER BY gc.name";
		$gc_res = $pdo->select($gc_sql,array(":group_id"=>$group_id));
	}
}
$template = 'group_manage_payment_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>

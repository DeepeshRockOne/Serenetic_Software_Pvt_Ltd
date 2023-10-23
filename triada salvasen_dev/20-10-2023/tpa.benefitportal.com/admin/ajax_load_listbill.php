<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
 
$group_id= !empty($_POST['group_id']) ? $_POST['group_id'] : '';

$sqlListBills="SELECT lb.id,lb.list_bill_no,c.business_name as group_name,gc.name as company_name
				FROM list_bills lb 
				JOIN customer c ON (c.id=lb.customer_id AND c.type='Group')
				LEFT JOIN group_company gc ON (gc.id = lb.company_id)
				where lb.status='open' AND lb.is_deleted='N' and lb.customer_id = :group_id";
$resListBills=$pdo->select($sqlListBills,array(":group_id"=>$group_id));


ob_start();
?>
	<option data-hidden="true"></option>
	<?php if(!empty($resListBills)){
		foreach($resListBills AS $k=>$v){ ?>
		   <?php $title = (!empty($v['company_name'])) ? $v['company_name'] : $v['group_name'] ?>
		  <option value="<?= $v['id'] ?>" ><?= $v['list_bill_no'] ?> - <?= $title ?></option>
		<?php }
	}
$html = ob_get_clean();
  
$result = array();	
$result['html'] = $html;
$result['status'] = "success"; 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>
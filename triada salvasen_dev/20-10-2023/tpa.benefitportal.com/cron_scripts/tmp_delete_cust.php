<?php
session_start();
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
if(!isset($_SESSION['site_access'])){
	$_SESSION["HTTP_REFERER"]=(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	redirect("site_access.php");
}
$msg="";

if(isset($_POST['id'])){
	$id=$_POST['id'];
	$selSql="SELECT * FROM customer WHERE id=:id OR rep_id=:id";
	$row=$pdo->selectOne($selSql,array(":id"=>$id));

	if($row){
		
		// Update commission credit & debit balance code start

			// Update Commission Credit Balance start
				$selCreditComm = "SELECT SUM(cs.amount) as creditBal,cs.commission_duration,cs.pay_period,cs.customer_id
							FROM commission cs
							WHERE cs.payer_id=:id 
							GROUP BY cs.customer_id,cs.pay_period,cs.commission_duration";
				$resCreditComm = $pdo->select($selCreditComm,array(":id"=>$row['id']));
			
				if(!empty($resCreditComm)){
					foreach($resCreditComm as $comm){
						if(!empty($comm['creditBal'])){
							$creditParam = array(
								"credit" => "msqlfunc_credit - ".$comm['creditBal'],
								"updated_at" => "msqlfunc_NOW()"
							);
							$creditWhere = array(
								"clause" => " agent_id=:agent_id AND commission_duration=:duration AND pay_period=:pay_period AND status='Open'",
								"params" => array(
									":agent_id"=>$comm['customer_id'],
									":duration"=>$comm['commission_duration'],
									":pay_period"=>$comm['pay_period'],
								)
							);
							$pdo->update('commission_credit_balance',$creditParam,$creditWhere);
						}
					}
				}
			// Update Commission Credit Balance ends

			// Update Commission Debit Balance start
				$selDebitComm = "SELECT SUM(h.amount) as debitBal,h.debit_id as debitId
							FROM commission cs
							JOIN commission_debit_balance_history h ON(h.commission_id=cs.id AND h.is_deleted='N')
							WHERE cs.payer_id=:id 
							GROUP BY cs.customer_id,cs.pay_period,cs.commission_duration,h.debit_id";
				$resDebitComm = $pdo->select($selDebitComm,array(":id"=>$row['id']));
				if(!empty($resDebitComm)){
					foreach($resDebitComm as $comm){
						if(!empty($comm['debitBal']) && !empty($comm['debitId'])){
							$debitParam = array(
								"balance" => "msqlfunc_balance - ".$comm['debitBal'],
								"updated_at" => "msqlfunc_NOW()"
							);
							$debitWhere = array(
								"clause" => "id=:id",
								"params" => array(
									":id"=>$comm['debitId']
								)
							);
							$pdo->update('commission_debit_balance',$debitParam,$debitWhere);
						}
					}
				}
			// Update Commission Debit Balance end

			// Update commission debit balance history start
			    $commID = $pdo->selectOne("SELECT GROUP_CONCAT(id) AS commIds FROM commission WHERE payer_id=:id",array(":id"=>$row['id']));
				
				if(!empty($commID['commIds'])){
				  	$delHistory = "DELETE FROM commission_debit_balance_history WHERE is_deleted='N' AND commission_id IN (".$commID['commIds'].")";
				  	$pdo->delete($delHistory);
				}
			// Update commission debit balance history ends

		// Update commission credit & debit balance code ends


      $delSql="DELETE FROM customer WHERE id=:id; 
      	DELETE FROM activity_feed WHERE user_id=:id AND user_type='Customer';
      	DELETE FROM activity_feed WHERE entity_id=:id AND user_type='Customer';
      	DELETE FROM leads WHERE customer_id=:id;
	  	DELETE FROM order_details WHERE order_id IN (select id FROM orders WHERE customer_id=:id); 
	  	DELETE FROM return_orders WHERE order_id IN (select id FROM orders WHERE customer_id=:id); 
	  	DELETE FROM orders WHERE customer_id=:id;
	  	DELETE FROM customer_enrollment WHERE website_id IN (select id FROM website_subscriptions WHERE customer_id=:id);
	  	DELETE FROM website_subscriptions WHERE customer_id=:id;
	  	DELETE FROM website_subscriptions_history WHERE customer_id=:id;
	  	DELETE FROM transactions WHERE customer_id=:id;
	  	DELETE FROM commission WHERE payer_id=:id;
	  	DELETE FROM payable_details WHERE payable_id IN(select id FROM payable WHERE customer_id=:id);
	  	DELETE FROM payable WHERE customer_id=:id;
        ";
	  $pdo->delete($delSql,array(":id"=>$row['id']));
	  $msg= "Customer Deleted";
	}else{
	  $msg= "Customer not Found";
	}
}
?>
<form method="post">
	<input type="text" required="" name="id" placeholder="Enter Id or Rep Id" /> <?=$msg?>
	<br>
	<br>
	<button type="submit">Delete</button>
</form>
<?php 
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$last_date=date('Y-m-d 00:00:00', strtotime('-30 days'));

$sel_sql="SELECT t.id,t.order_id,t.bank_name,AES_DECRYPT(obi.card_no_full,'".$CREDIT_CARD_ENC_KEY."') as card_number
			FROM transactions t
			JOIN orders o ON(t.order_id = o.id AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation'))
			JOIN customer c ON (c.id = o.customer_id AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N')
			JOIN customer s ON (c.sponsor_id = s.id)
			LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)
			WHERE t.transaction_status='Payment Declined' AND t.is_deleted='N' AND t.bank_name = '' AND t.created_at >= :transaction_create_date
			GROUP BY t.id
			ORDER BY t.created_at DESC";

$result = $pdo->select($sel_sql,array(':transaction_create_date'=>$last_date));

if(!empty($result)){
	$insParams = array();
	foreach ($result as $key => $value) {
		if(!empty($value['card_number']) && empty($value['bank_name'])){
			$card_len = strlen($value['card_number']);
			$card_first_digits = "";
			if($card_len >= 6){
				$card_first_digits = substr($value['card_number'],0,6);
			}

			$binSql = "SELECT issuer FROM binlist_data WHERE bin=:bin";
			$binRes = $pdo->selectOne($binSql,array(':bin'=>$card_first_digits));

			if(!empty($binRes['issuer'])){

				$updateParams = array(
					'bank_name' => $binRes['issuer']
				);
				$updateWhere = array(
					'clause' => 'id=:id',
					'params' => array(
						':id' => $value['id']
					),
				);

				$pdo->update('transactions',$updateParams,$updateWhere);
			}
		}			
	}
}
echo "Completed";
dbConnectionClose();
?>
<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);

$all_state = $pdo->select("SELECT * from states_c where country_id = 231 and is_deleted = 'N'");

$all_products = $pdo->select("SELECT id FROM prd_main WHERE type = 'Normal' AND is_deleted = 'N'");

if($all_products){
	foreach ($all_products as $product) {

		$available_states = $pdo->select("SELECT product_id,state_name from prd_available_state where is_deleted='N' AND product_id = :product_id",array(':product_id' => $product['id']));

		$no_sale_states = $pdo->select("SELECT product_id,state_name from prd_no_sale_states where is_deleted='N' AND product_id = :product_id",array(':product_id' => $product['id']));

		$available_state_array = array();
		$no_sale_state_array = array();

		if(!empty($available_states)){
			foreach ($available_states as $v) {
				$available_state_array[$v['product_id']][$v['state_name']] = $v['state_name'];
			}
		}

		if(!empty($no_sale_states)){
			foreach ($no_sale_states as $vl) {
				$no_sale_state_array[$vl['product_id']][$vl['state_name']] = $vl['state_name'];
			}
		}

		if($all_state){
			foreach ($all_state as $state) {

				$state_name = $state['name'];
				$available_state = isset($available_state_array[$product['id']][$state_name]) ? $available_state_array[$product['id']][$state_name] : "";

				if(empty($available_state)){

					$no_sale_state = isset($no_sale_state_array[$product['id']][$state_name]) ? $no_sale_state_array[$product['id']][$state_name] : "";

					if(empty($no_sale_state)){
						$insert_params = array(
							'product_id' => $product['id'],
							'state_id' => $state['id'],
							'state_name' => $state_name,
							'is_deleted'=>'N',
							'effective_date' => date('Y-m-d'),
						);

						$inset_id = $pdo->insert('prd_no_sale_states',$insert_params);
					}
				}

			}
		}

	}
}
echo "complete";
dbConnectionClose();
?>
<?php
include_once dirname(__DIR__) . '/includes/connect.php';

//Update Merchant Processor Display ID Start
	$paymentMasterRes = $pdo->select("SELECT id FROM payment_master where processor_code IS NULL OR processor_code=''");

	if(!empty($paymentMasterRes)){
		
		include_once dirname(__DIR__) . '/includes/function.class.php';
		$function_list = new functionsList();

		foreach($paymentMasterRes as $processor){

			$upd_params = array(
				'processor_code' => $function_list->generateMerchantProcessorDisplayID(),
			);
			$upd_where = array(
				"clause"=>"id=:id",
				"params" => array(
					":id"=>$processor['id']
				)
			);
			$pdo->update("payment_master",$upd_params,$upd_where);
		}
	}else{
		echo "No Record Updates.";
		exit;
	}
    echo "Completed";
    dbConnectionClose();
    exit;
//Update Merchant Processor Display ID End
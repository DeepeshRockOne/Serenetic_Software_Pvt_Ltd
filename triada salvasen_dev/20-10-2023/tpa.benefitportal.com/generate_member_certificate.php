<?php
include_once __DIR__ . '/includes/connect.php';
ini_set('memory_limit', '-1');
$sub_res_id = $_REQUEST['sub_resource_id'];
$customer_id = $_REQUEST['user_id'];
$ws_id = $_REQUEST['ws_id'];
$fee_price = array();
if(empty($sub_res_id) || empty($customer_id)){
	exit("Resource not found.");
}

$sub_res = $pdo->selectOne("SELECT * FROM sub_resources WHERE md5(id) = :id AND coll_type = 'html' AND is_deleted = 'N'",array(":id" => $sub_res_id));

if($sub_res){
	if(!empty($sub_res['member_description'])){

		$customer_row = $pdo->selectOne("SELECT id,fname,lname,email,rep_id,status FROM customer WHERE md5(id) = :customer_id",array(":customer_id" => $customer_id));

		$ws_row = $pdo->selectOne("SELECT id,customer_id,product_id,prd_plan_type_id,price as retail_price,plan_id FROM website_subscriptions WHERE md5(id) = :ws_id",array(":ws_id" => $ws_id));

		$pdf_html_code = $sub_res['member_description'];

		preg_match_all('#\[\[VendorFee_(.*?)\]\]#', $pdf_html_code, $match);
		$feeAndPolictTag = $match[1];
		if(!empty($feeAndPolictTag)){
			$planId = $ws_row['plan_id'];
			$prdPlanTypeId = $ws_row['prd_plan_type_id'];
		    foreach($feeAndPolictTag as $tag){
				$fee_price['VendorFee_'.$tag] = 0;
				$pricing_model = getname('prd_main',$tag,'pricing_model','product_code');
				if($pricing_model == 'FixedPrice'){
					$selFeePrice = "SELECT fee.product_type,product_mat.id AS prd_matrix_id,product_mat.price AS retail_price,product_mat.commission_amount,product_mat.non_commission_amount,fee_mat.price AS fee_price,fee_mat.price_calculated_on,fee_mat.price_calculated_type,fee_mat.plan_type
					FROM prd_matrix product_mat
					JOIN prd_assign_fees paf ON(paf.is_deleted='N' AND paf.product_id=product_mat.product_id)
					JOIN prd_main fee ON(fee.is_deleted='N' AND paf.fee_id=fee.id)
					JOIN prd_matrix fee_mat ON(fee_mat.is_deleted='N' AND fee_mat.product_id=fee.id)
					WHERE product_mat.is_deleted='N' AND fee.product_type='Vendor' AND product_mat.id IN($planId) AND fee.product_code=:product_code AND (IF(fee_mat.plan_type=0,1,fee_mat.plan_type=:prd_plan_type_id))";
					$feePrice = $pdo->select($selFeePrice,array(":product_code"=>$tag,":prd_plan_type_id"=>$ws_row['prd_plan_type_id']));
				}else{
					$selFeePrice = "SELECT fee.product_type,product_mat.id as prd_matrix_id,product_mat.price as retail_price,product_mat.commission_amount,product_mat.non_commission_amount,fee_mat.price as fee_price,fee_mat.price_calculated_on,fee_mat.price_calculated_type
						FROM prd_matrix product_mat
						JOIN prd_fee_pricing_model model ON(model.prd_matrix_id=product_mat.id AND model.is_deleted='N')
						JOIN prd_main fee ON(fee.is_deleted='N' AND model.fee_product_id=fee.id)
						JOIN prd_matrix fee_mat ON(fee_mat.is_deleted='N' AND fee_mat.product_id=fee.id AND fee_mat.id=model.prd_matrix_fee_id)
						WHERE product_mat.is_deleted='N' AND product_mat.id IN($planId) AND fee.product_type='Vendor' AND fee.product_code=:product_code";
					$feePrice = $pdo->select($selFeePrice,array(":product_code"=>$tag));
				}
    	    	$feeAmount = 0;
    	    	if(!empty($feePrice)){
    	    		foreach($feePrice as $feeValue){
    	    			if($feeValue['price_calculated_on'] == "Percentage"){
				        	if($feeValue['price_calculated_type']=="Retail"){
				        		$calclulatedPrice = ($feeValue['retail_price'] * $feeValue['fee_price'])/100;
				        	  }else if($feeValue['price_calculated_type']=="Commissionable"){
				        		$calclulatedPrice = ($feeValue['commission_amount'] * $feeValue['fee_price'])/100;
				        	  }else if($feeValue['price_calculated_type']=="NonCommissionable"){
				        		$calclulatedPrice = ($feeValue['non_commission_amount'] * $feeValue['fee_price'])/100;
				        	  }
				        	  $feeValue['fee_price'] = $calclulatedPrice;
	                    }
				        $fee_price['VendorFee_'.$tag] += $feeValue['fee_price'];
    	    		}
		        }else{
			    	$fee_price['VendorFee_'.$tag] += 0;
			    }
			    $fee_price['VendorFee_'.$tag] = displayAmount($fee_price['VendorFee_'.$tag]);
		    }
	    }

		if(!empty($fee_price)){
			foreach ($fee_price as $key => $value) {
				$pdf_html_code = str_replace("[[" . $key . "]]", $value, $pdf_html_code);
			  }
		}

		$smart_tags = get_user_smart_tags($ws_row['customer_id'],'member',$ws_row['product_id'],$ws_row['id']);
		if($smart_tags){
			foreach ($smart_tags as $key => $value) {
			  $pdf_html_code = str_replace("[[" . $key . "]]", $value, $pdf_html_code);
			}
		}

		require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
		require_once "libs/mpdf/src/Mpdf.php";
		$mpdf = new \Mpdf\Mpdf();
		$stylesheet = file_get_contents('css/mpdf_common_style.css');
	    $mpdf->WriteHTML($stylesheet,1);
	    $mpdf->WriteHTML($pdf_html_code,2);
		$mpdf->use_kwt = true;
		$mpdf->shrink_tables_to_fit = 1;  
		/*header('Content-type:application/pdf');
		header('Content-disposition: attachment;filename="User_ID_Card_' . date('Ymd') . '.pdf"');*/
		echo $mpdf->Output($customer_row['rep_id']."_PRODUCT_CERTI_" . date('Ymd') . ".pdf","D");

	}
}
?>

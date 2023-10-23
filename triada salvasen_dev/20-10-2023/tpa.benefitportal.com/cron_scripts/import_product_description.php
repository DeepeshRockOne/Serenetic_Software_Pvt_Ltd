<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

//Insert prd_member_portal_information Start
	$sql="SELECT p.product_code,pmpi.* FROM prd_member_portal_information pmpi 
	JOIN prd_main p ON (p.id = pmpi.product_id)
	WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND pmpi.is_deleted='N'";
	$res=$OtherPdo->select($sql);
	// pre_print($res);
	if(!empty($res)){
		foreach ($res as $key => $value) {
			$sqlPrd="SELECT id FROM prd_main where product_code=:code";
			$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
			// pre_print($resPrd,false);
			if(!empty($resPrd)){
				$sqldesc = "SELECT id FROM prd_member_portal_information where product_id=:product_id AND name=:name AND is_deleted='N'";
				$resdesc = $pdo->selectOne($sqldesc,array(":product_id"=>$resPrd['id'],":name"=>$value['name']));
				if(empty($resdesc['id'])){
					$ins_params = array(
						"product_id"=>$resPrd['id'],
						"name"=>$value['name'],
						"description"=>$value['description'],
						"is_member_portal" => $value['is_member_portal'],
					);
					$pdo->insert("prd_member_portal_information", $ins_params);
				}
				
			}
			
		}
	}
//Insert prd_member_portal_information End

//Insert prd_descriptions start
	$sqlDesc="SELECT p.product_code,pd.* FROM prd_descriptions pd 
	JOIN prd_main p ON (p.id = pd.product_id)
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
	$resDesc=$OtherPdo->select($sqlDesc);
	if(!empty($resDesc)){
		foreach ($resDesc as $key => $value) {
			$sqlPrd="SELECT id FROM prd_main where product_code=:code";
			$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
			if(!empty($resPrd)){
				$product_id = $resPrd['id'];

				$sql = "SELECT id FROM prd_descriptions where product_id=:product_id";
				$res = $pdo->selectOne($sql,array(":product_id"=>$product_id));

				if(empty($res) && empty($res['id'])){
					$ins_params = array(
						"product_id"=>$product_id,
						"agent_portal"=>$value['agent_portal'],
						"agent_info"=>!empty($value['agent_info']) ? $value['agent_info'] : '',
						"limitations_exclusions"=>$value['limitations_exclusions'],
						"enrollment_desc" => !empty($value['prd_descriptions']) ? $value['prd_descriptions'] :  '',
					);
					$pdo->insert("prd_descriptions",$ins_params);
				}
			}
		}
	}
//Insert prd_descriptions End
echo "import_product_description->Completed";
dbConnectionClose();
exit;
?>

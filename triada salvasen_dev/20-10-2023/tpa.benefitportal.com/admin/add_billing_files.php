<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/files.class.php';
$FilesClass = new FilesClass();
// $carriers = $pdo->select("SELECT id,name FROM `prd_fees` where setting_type = 'Carrier' AND is_deleted = 'N'");

// $productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
//             FROM prd_main p 
//             LEFT JOIN prd_category c ON (c.id = p.category_id)
//             where p.type!='Fees' AND p.record_type = 'Primary' AND p.is_deleted='N' AND p.status='Active'  GROUP BY p.id ORDER BY name ASC";
// $productRes = $pdo->selectGroup($productSql,array(),'title');

$file_id = isset($_GET['id']) ? $_GET['id'] : "";
$file_name = "";
$products = array();
$file_type = "";
$carrier = "";
$period_type = "";

if($file_id){
	$file_data = $pdo->selectOne("SELECT * FROM billing_files where id = :id",array(':id' => $file_id));

	if($file_data){
		$file_name = $file_data['file_name'];
		$file_type = $file_data['file_type'];
		$carrier = $file_data['carrier_id'];
		$period_type = $file_data['period_type'];
		$products = $FilesClass->getBillingFilePrd($file_id);
		
		if($file_type && $carrier){
			$carriers = $pdo->select("SELECT id,name from prd_fees where setting_type = :setting_type and is_deleted = 'N'",array(":setting_type" => $file_type));
		}
		
		// if($carrier){
		// 	$productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
		//             FROM prd_main p 
		//             LEFT JOIN prd_category c ON (c.id = p.category_id)
		//             where p.prd_fee_id = :prd_fee_id AND p.type!='Fees' AND p.record_type = 'Primary' AND p.is_deleted='N' AND p.status='Active'  GROUP BY p.id ORDER BY name ASC";
		// 	$productRes = $pdo->selectGroup($productSql,array(':prd_fee_id' => $carrier),'title');
		// }

		$incr = "";
		$sch_params = array();

		if($file_type == 'Carrier'){
		  $sch_params[":carrier_id"] = makeSafe($carrier);
		  $incr .= " AND p.carrier_id = :carrier_id";
		}

		if($file_type == 'Membership'){
		  $sch_params[":membership_id"] = makeSafe($carrier);
		  $incr .= " AND FIND_IN_SET(:membership_id,p.membership_ids)";
		}
		if($file_type == 'Vendor'){
			$sch_params[":vendor_id"] = makeSafe($carrier);
		    $incr .= " AND pf.id = :vendor_id";	
		}

		if($file_type == 'Product'){
			$sch_params[":vendor_id"] = makeSafe($carrier);
		    $incr .= " AND p.prd_fee_id = :vendor_id";	
		}

		$productSql="SELECT p.id,p.name,p.product_code,p.type,IF(p.product_type='Healthy Step','Healthy Step', c.title) AS title 
	            FROM prd_fees pf
	          	JOIN prd_assign_fees paf ON(pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')   
	          	JOIN prd_main p ON(p.id = paf.product_id)
	          	LEFT JOIN prd_category c ON (c.id = p.category_id)
	            where p.is_deleted='N' AND p.status IN('Active','Suspended') $incr GROUP BY p.id ORDER BY name ASC";
		$productRes = $pdo->selectGroup($productSql,$sch_params,'title');
	}
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "add_billing_files.inc.php";
include_once 'layout/iframe.layout.php';
?>
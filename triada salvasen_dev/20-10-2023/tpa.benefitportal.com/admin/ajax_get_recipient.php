<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_type = isset($_GET['file_type']) ? $_GET['file_type'] : "";
$carrier = isset($_GET['carrier']) ? $_GET['carrier'] : "";
$name = isset($_GET['name']) ? $_GET['name'] : "";

if($file_type){
	
	$content = "<option data-hidden='true'></option>";

	$data = $pdo->select("SELECT id,name from prd_fees where setting_type = :setting_type and is_deleted = 'N'",array(":setting_type" => $file_type));
	if($data){
		foreach ($data as $k => $v) {
			$content .= "<option value='".$v['id']."'>".$v['name']."</option>";
		}
	}

	$response = array('status' => "success",'content' => $content);
	echo json_encode($response);
	exit();
}

if($carrier && $name){
	$incr = "";
	$sch_params = array();

	if($name == 'Carrier'){
	  $sch_params[":carrier_id"] = makeSafe($carrier);
	  $incr .= " AND p.carrier_id = :carrier_id";
	}

	if($name == 'Membership'){
	  $sch_params[":membership_id"] = makeSafe($carrier);
	  $incr .= " AND FIND_IN_SET(:membership_id,p.membership_ids)";
	}
	if($name == 'Vendor'){
		$sch_params[":vendor_id"] = makeSafe($carrier);
	    $incr .= " AND pf.id = :vendor_id";	
	}

	if($name == 'Product'){
		$sch_params[":vendor_id"] = makeSafe($carrier);
	    $incr .= " AND p.prd_fee_id = :vendor_id";	
	}
	
	$content = "";

	// $data = $pdo->select("SELECT id,name from prd_main where prd_fee_id = :prd_fee_id and is_deleted = 'N'",array(":setting_type" => $carrier));

	$productSql="SELECT p.id,p.name,p.product_code,p.type,IF(p.product_type='Healthy Step','Healthy Step', c.title) AS title 
            FROM prd_fees pf
          	JOIN prd_assign_fees paf ON(pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')   
          	JOIN prd_main p ON(p.id = paf.product_id)
          	LEFT JOIN prd_category c ON (c.id = p.category_id)
            where p.is_deleted='N' AND p.status IN('Active','Suspended') AND p.parent_product_id=0 $incr GROUP BY p.id ORDER BY p.name ASC";
	$data = $pdo->selectGroup($productSql,$sch_params,'title');
	if($data){
		// foreach ($data as $k => $v) {
			// if(!empty($data)){

                foreach ($data as $key=> $category) { 
                  if(!empty($category)){ 
		              $content .= "<optgroup label= '".$key."'>";
		                foreach ($category as $pkey => $row) {
		                  $content .= "<option value='".$row['id']."'>";
		                    $content .= $row['name'] .' ('.$row['product_code'].')';    
		                  $content .= "</option>";
		                }
		              $content .= "</optgroup>";
		          }
              } 
            // }
		// }
	}

	$response = array('status' => "success",'content' => $content);
	echo json_encode($response);
	dbConnectionClose();
	exit();
}

?>
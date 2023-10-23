<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
 
$validate = new Validation();

$matrix_id=isset($_GET['id']) ? $_GET['id'] : 0;
$product_id=$_GET['product_id'];
$productActiveEffectiveDate=date('m/d/Y');
$sqlProduct="SELECT * FROM prd_main WHERE id=:product_id";
$params=array(":product_id"=>$product_id);
$resProduct=$pdo->selectOne($sqlProduct,$params);
if(!$resProduct){
  setNotifyError("Product not Found");
  redirect("manage_product.php",true);
}
$todayDate=date('m/d/Y');
$coverage_options_list=0;
$sqlMatrix="SELECT * FROM prd_matrix where id=:id";
$resMatrix=$pdo->selectOne($sqlMatrix,array(":id"=>$matrix_id));
$matrix_group_count = 0;

$sqlMatrixGroup="SELECT matrix_group 
				FROM prd_matrix 
				WHERE product_id=:product_id AND is_deleted='N' 
				order by id DESC";
$resMatrixGroup=$pdo->selectOne($sqlMatrixGroup,array(":product_id"=>$product_id));
if($resMatrixGroup){
	$matrix_group_count = $resMatrixGroup['matrix_group'];
}
if($resMatrix){
	$plan_type = $resMatrix['plan_type'];
	$plan_type = $resMatrix['plan_type'];
  
	$age_from = $resMatrix['age_from'];
	$age_to = $resMatrix['age_to'];
	$state = $resMatrix['state'];
	$zip = $resMatrix['zip'];
	$gender = $resMatrix['gender'];

	$smoking_status = $resMatrix['smoking_status'];
	$tobacco_status = $resMatrix['tobacco_status'];
	$height_feet = $resMatrix['height_feet'];
	$height_inch = $resMatrix['height_inch'];
	$weight = $resMatrix['weight'];

	$no_of_children = $resMatrix['no_of_children'];
	$has_spouse = $resMatrix['has_spouse'];
	$spouse_age_from = $resMatrix['spouse_age_from'];
	$spouse_age_to = $resMatrix['spouse_age_to'];
	$spouse_gender = $resMatrix['spouse_gender'];

	$spouse_smoking_status = $resMatrix['spouse_smoking_status'];
	$spouse_tobacco_status = $resMatrix['spouse_tobacco_status'];
	$spouse_height_feet = $resMatrix['spouse_height_feet'];
	$spouse_height_inch = $resMatrix['spouse_height_inch'];
	$spouse_weight = $resMatrix['spouse_weight'];

	$price = $resMatrix['price'];
	$non_commissionable = $resMatrix['non_commission_amount'];
	$commissionable = $resMatrix['commission_amount'];
}

if($resProduct){
	if($resProduct['price_control']!=""){
	  $PrCtrl= json_decode($resProduct['price_control'],true);
	}
	$coverage_options_list = $resProduct['coverage_options'];
	$coverage_options = !empty($coverage_options_list) ? explode(",", $coverage_options_list) : array();
	if(empty($coverage_options_list)){
		$coverage_options_list=0;
	}
	$productActiveEffectiveDate = date('m/d/Y',strtotime($resProduct['create_date']));
}
//******************** CSV Matrix Code Start   ***********************************
	$field=array(
		array(
			'display_field'=>'Age (Date of Birth) From',
			'input_field'=>'age_from',
			'price_control_field'=>'Age',
		),
		array(
			'display_field'=>'Age (Date of Birth) To',
			'input_field'=>'age_to',
			'price_control_field'=>'Age',
		),
		array(
			'display_field'=>'State',
			'input_field'=>'state',
			'price_control_field'=>'State',
		),
		array(
			'display_field'=>'Zip Code',
			'input_field'=>'zip',
			'price_control_field'=>'Zip Code',
		),
		array(
			'display_field'=>'Legal Sex/Gender',
			'input_field'=>'gender',
			'price_control_field'=>'Gender',
		),
		array(
			'display_field'=>'Smoke',
			'input_field'=>'smoking_status',
			'price_control_field'=>'Smoke',
		),
		array(
			'display_field'=>'Tobacco Use',
			'input_field'=>'tobacco_status',
			'price_control_field'=>'Tobacco Use',
		),
		array(
			'display_field'=>'Height Feet',
			'input_field'=>'height_feet',
			'price_control_field'=>'Height',
		),
		array(
			'display_field'=>'Height Inch',
			'input_field'=>'height_inch',
			'price_control_field'=>'Height',
		),
		array(
			'display_field'=>'Weight',
			'input_field'=>'weight',
			'price_control_field'=>'Weight',
		),
		array(
			'display_field'=>'Number Of Children',
			'input_field'=>'no_of_children',
			'price_control_field'=>'Number Of Children',
		),
		array(
			'display_field'=>'Has Spouse',
			'input_field'=>'has_spouse',
			'price_control_field'=>'Has Spouse',
		),
		array(
			'display_field'=>'Spouse Age From',
			'input_field'=>'spouse_age_from',
			'price_control_field'=>'Spouse Age',
		),
		array(
			'display_field'=>'Spouse Age To',
			'input_field'=>'spouse_age_to',
			'price_control_field'=>'Spouse Age',
		),
		array(
			'display_field'=>'Spouse Gender',
			'input_field'=>'spouse_gender',
			'price_control_field'=>'Spouse Gender',
		),
		array(
			'display_field'=>'Spouse Smoke',
			'input_field'=>'spouse_smoking_status',
			'price_control_field'=>'Spouse Smoke',
		),
		array(
			'display_field'=>'Spouse Tobacco Use',
			'input_field'=>'spouse_tobacco_status',
			'price_control_field'=>'Spouse Tobacco Use',
		),
		array(
			'display_field'=>'Spouse Height Feet',
			'input_field'=>'spouse_height_feet',
			'price_control_field'=>'Spouse Height',
		),
		array(
			'display_field'=>'Spouse Height Inch',
			'input_field'=>'spouse_height_inch',
			'price_control_field'=>'Spouse Height',
		),
		array(
			'display_field'=>'Spouse Weight',
			'input_field'=>'spouse_weight',
			'price_control_field'=>'Spouse Weight',
		),
	);

//******************** CSV Matrix Code End     ***********************************

$planTypeSql="SELECT id,title from prd_plan_type where id in ($coverage_options_list)";
$planTypeRows=$pdo->select($planTypeSql);

$matrixSql="SELECT m.*,t.title as p_type 
		  FROM prd_matrix m 
		  JOIN prd_plan_type t ON t.id=m.plan_type
		  WHERE m.is_deleted='N' AND m.product_id=:product_id 
		  order by m.pricing_termination_date,t.order_by ASC";
$matrixParams=array(":product_id"=>$product_id);
$matrixRows=$pdo->select($matrixSql,$matrixParams);


$sql = "SELECT * FROM states_c where country_id=231 and is_deleted='N'";
$statesRows = $pdo->select($sql);
if(!$statesRows){
  $statesRows=array();
}

$exJs = array(
	'thirdparty/price_format/jquery.price_format.2.0.js',
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
);
$errors = $validate->getErrors();
$template = "pricing_matrix.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
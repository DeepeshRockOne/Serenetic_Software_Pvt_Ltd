<?php
include_once 'layout/start.inc.php';
$data = array();
$product_id= $_POST['product_id'];
$plan_type_field = $_POST['plan_type'];

$age_from_field = $_POST['age_from'];
$age_to_field = $_POST['age_to'];
$state_field = $_POST['state'];
$zip_field = $_POST['zip'];
$gender_field = $_POST['gender'];

$smoking_status_field = $_POST['smoking_status'];
$tobacco_status_field = $_POST['tobacco_status'];
$height_feet_field = $_POST['height_feet'];
$height_inch_field = $_POST['height_inch'];
$weight_field = $_POST['weight'];

$no_of_children_field = $_POST['no_of_children'];
$has_spouse_field = $_POST['has_spouse'];
$spouse_age_from_field = $_POST['spouse_age_from'];
$spouse_age_to_field = $_POST['spouse_age_to'];
$spouse_gender_field = $_POST['spouse_gender'];

$spouse_smoking_status_field = $_POST['spouse_smoking_status'];
$spouse_tobacco_status_field = $_POST['spouse_tobacco_status'];
$spouse_height_feet_field = $_POST['spouse_height_feet'];
$spouse_height_inch_field = $_POST['spouse_height_inch'];
$spouse_weight_field = $_POST['spouse_weight'];

$price_field = $_POST['price'];
$non_commissionable_field = $_POST['non_commissionable'];


function csvToArraywithFields($filename) {
  $csv = array_map('str_getcsv', file($filename));
  $headers = $csv[0];
  unset($csv[0]);
  $rowsWithKeys = [];

  foreach ($csv as $row) {

    $newRow = [];
    foreach ($headers as $k => $key) {
      if (trim($key) != "") {
        $newRow[$key] = $row[$k];
      }
    }
    $rowsWithKeys[] = $newRow;
  }

  return $rowsWithKeys;
}


$sqlProduct="SELECT * FROM prd_main WHERE id=:product_id";
$params=array(":product_id"=>$product_id);
$resProduct=$pdo->selectOne($sqlProduct,$params);
if($resProduct){
    if($resProduct['price_control']!=""){
      $PrCtrl= json_decode($resProduct['price_control'],true);
    }
}

$csv_file = $PRICE_MATRIX_CSV_DIR . $_SESSION['stored_file_name'];
$field_row = csvToArraywithFields($csv_file);

ob_start();
?>
<div class="row">
  <div class="col-sm-12">
    <div class="table-responsive m-t-25">
      <div class="tbl-header">
        <table class="color-table info-table table table-striped mn table-fixed">
          <thead>
            <tr>
              <th>Plan Tier</th>
              <?php 
              	if(in_array("Age", $PrCtrl)){?> 
              		<th>Age From</th> 
              		<th>Age To</th> 
                <?php }
                if(in_array("State", $PrCtrl)){ ?>
                    <th>State</th> 
                <?php }
                if(in_array("Zip Code", $PrCtrl)){ ?>
                    <th>Zip Code</th> 
                <?php }
                if(in_array("Gender", $PrCtrl)){ ?>
                    <th>Legal Sex/Gender</th> 
                <?php }
                if(in_array("Smoke", $PrCtrl)){ ?>
                    <th>Smoking</th> 
                <?php }
                if(in_array("Tobacco Use", $PrCtrl)){ ?>
                     <th>Tobacco Use</th> 
                <?php }
                if(in_array("Height", $PrCtrl)){ ?>
                    <th>Height Feet</th> 
                    <th>Height Inch</th> 
                <?php }
                if(in_array("Weight", $PrCtrl)){ ?>
                    <th>Weight</th> 
                <?php }
                if(in_array("Number Of Children", $PrCtrl)){ ?>
                    <th>Number Of Children</th> 
                <?php }
                if(in_array("Has Spouse", $PrCtrl)){ ?>
                    <th>Has Spouse</th> 
                <?php }
                if(in_array("Spouse Age", $PrCtrl)){ ?>
                    <th>Spouse Age From</th> 
              		<th>Spouse Age To</th> 
                <?php }
                if(in_array("Spouse Gender", $PrCtrl)){ ?>
                  	<th>Spouse Gender</th> 
                <?php }
                if(in_array("Spouse Smoke", $PrCtrl)){ ?>
                    <th>Spouse Smoking</th>
                <?php }
                if(in_array("Spouse Tobacco Use", $PrCtrl)){ ?>
                    <th>Spouse Tobacco Use</th>
                <?php }
                if(in_array("Spouse Height", $PrCtrl)){ ?>
                    <th>Spouse Height Feet</th> 
                    <th>Spouse Height Inch</th> 
                <?php }
                if(in_array("Spouse Weight", $PrCtrl)){ ?>
                   <th>Spouse Weight</th> 
                <?php } ?>
              <th>Price</th>
              <th>Non Commissionable<br />
                Price</th>
              <th>Commissionable<br />
                Price</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="tbl-body">
        <table class="color-table info-table table table-striped table-fixed">
          <tbody class="tbody">
            <tr>
               <?php if(!empty($field_row)){ ?>
				<?php foreach ($field_row as $key=> $value) { 
					$plan_type = $value[$plan_type_field];
					$age_from = $value[$age_from_field];
			        $age_to = $value[$age_to_field];
			        $state = $value[$state_field];
			        $zip = $value[$zip_field];
			        $gender = $value[$gender_field];

			        $smoking_status = $value[$smoking_status_field];
			        $tobacco_status = $value[$tobacco_status_field];
			        $height_feet = $value[$height_feet_field];
			        $height_inch = $value[$height_inch_field];
			        $weight = $value[$weight_field];

			        $no_of_children = $value[$no_of_children_field];
			        $has_spouse = $value[$has_spouse_field];
			        $spouse_age_from = $value[$spouse_age_from_field];
			        $spouse_age_to = $value[$spouse_age_to_field];
			        $spouse_gender = $value[$spouse_gender_field];

			        $spouse_smoking_status = $value[$spouse_smoking_status_field];
			        $spouse_tobacco_status = $value[$spouse_tobacco_status_field];
			        $spouse_height_feet = $value[$spouse_height_feet_field];
			        $spouse_height_inch = $value[$spouse_height_inch_field];
			        $spouse_weight = $value[$spouse_weight_field];

			        $price = $value[$price_field];
			        $non_commissionable = $value[$non_commissionable_field];
			        //$commissionable = $value[$commissionable_field];
			        $commissionable = $price - $non_commissionable;

			        ?> <td> <?= $plan_type ?> </td> <?php
					if(in_array("Age", $PrCtrl)){
                        ?> <td> <?= $age_from; ?> </td> <?php
                        ?> <td> <?= $age_to; ?> </td> <?php
                    }
                    if(in_array("State", $PrCtrl)){
                        ?> <td> <?= $state; ?> </td> <?php
                    }
                    if(in_array("Zip Code", $PrCtrl)){
                        ?> <td> <?= $zip; ?> </td> <?php
                    }
                    if(in_array("Gender", $PrCtrl)){
                        ?> <td> <?= $gender; ?> </td> <?php
                    }
                    if(in_array("Smoke", $PrCtrl)){
                        ?> <td> <?= $smoking_status; ?> </td> <?php
                    }
                    if(in_array("Tobacco Use", $PrCtrl)){
                        ?> <td> <?= $tobacco_status; ?> </td> <?php
                    }
                    if(in_array("Height", $PrCtrl)){
                        ?> <td> <?= $height_feet; ?> </td> <?php
                        ?> <td> <?= $height_inch; ?> </td> <?php
                    }
                    if(in_array("Weight", $PrCtrl)){
                        ?> <td> <?= $weight; ?> </td> <?php
                    }
                    if(in_array("Number Of Children", $PrCtrl)){
                        ?> <td> <?= $no_of_children; ?> </td> <?php
                    }if(in_array("Has Spouse", $PrCtrl)){
                        ?> <td> <?= $has_spouse; ?> </td> <?php
                    }if(in_array("Spouse Age", $PrCtrl)){
                        ?> <td> <?= $spouse_age_from; ?> </td> <?php
                        ?> <td> <?= $spouse_age_to; ?> </td> <?php
                    }
                    if(in_array("Spouse Gender", $PrCtrl)){
                        ?> <td> <?= $spouse_gender; ?> </td> <?php
                    }
                    if(in_array("Spouse Smoke", $PrCtrl)){
                        ?> <td> <?= $spouse_smoking_status; ?> </td> <?php
                    }
                    if(in_array("Spouse Tobacco Use", $PrCtrl)){
                        ?> <td> <?= $spouse_tobacco_status; ?> </td> <?php
                    }
                    if(in_array("Spouse Height", $PrCtrl)){
                        ?> <td> <?= $spouse_height_feet; ?> </td> <?php
                        ?> <td> <?= $spouse_height_inch; ?> </td> <?php
                    }
                    if(in_array("Spouse Weight", $PrCtrl)){
                        ?> <td> <?= $spouse_weight; ?> </td> <?php
                    }
                    ?> <td> <?= $price ?> </td> <?php
                    ?> <td> <?= $non_commissionable ?> </td> <?php
                    ?> <td> <?= $commissionable ?> </td> <?php  
				} ?>
			  <?php }else{ ?>
			  	<td colspan="15" class="text-center"> No Records </td>
			  <?php } ?>              
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php 
$html = ob_get_clean();
$data["html"] = $html;
echo json_encode($data);
dbConnectionClose();
exit;
?>
<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$cat_sql="SELECT p.product_code,pc.*
	FROM prd_main p 
	JOIN prd_category pc ON(pc.id=p.category_id AND pc.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."') GROUP BY pc.id";
$cat_res=$OtherPdo->select($cat_sql);
//******* Category insert code start *******
  if(!empty($cat_res)){
    foreach ($cat_res as $key => $categoryRow) {
      
      $sqlCategory="SELECT id FROM prd_category WHERE is_deleted='N' AND title = :title";
      $resCategory=$pdo->select($sqlCategory,array(":title"=>$categoryRow['title']));

      if(empty($resCategory)){
        $ins_params = array(
          'title' => $categoryRow['title'],
          'short_description' => $categoryRow['short_description'],
          'status' => 'Active',
          'admin_id' => 1,
          'create_date' => 'msqlfunc_NOW()'
        );
        $category_id = $pdo->insert("prd_category", $ins_params);
      }
    }
  }
//******* Category insert code end   *******
echo "Category->Completed<br>";
//******* Company insert code start *******
  $com_sql="SELECT p.product_code,pc.*
    FROM prd_main p 
    JOIN prd_company pc ON(pc.id=p.company_id AND pc.is_deleted='N')
    WHERE p.product_code IN ('".implode("','",$productIDArray)."') GROUP BY pc.id";
  $company_array=$OtherPdo->select($com_sql);
  if(!empty($company_array)){
    foreach ($company_array as $key => $companyRow) {
      
      $sqlCompany="SELECT id FROM prd_company WHERE is_deleted='N' AND company_name = :company_name";
      $resCompany=$pdo->select($sqlCompany,array(":company_name"=>$companyRow['company_name']));

      if(empty($resCompany)){
        $ins_params = array(
          'company_name' => $companyRow['company_name'],
          'site_url' => '',
          'short_name' => str_replace(" ","_", $companyRow['short_name'])
        );
        $company_id = $pdo->insert("prd_company", $ins_params);
      }
    }
  }
//******* Company insert code end   *******
echo "Company->Completed<br>";
//******* Carrier insert code start *******

  $com_sql="SELECT p.product_code,pf.*
    FROM prd_main p 
    JOIN prd_fees pf ON(pf.id=p.carrier_id AND pf.is_deleted='N')
    WHERE pf.setting_type='Carrier' AND p.product_code IN ('".implode("','",$productIDArray)."')";
  $carrier_array=$OtherPdo->select($com_sql);

  if(!empty($carrier_array)){
    foreach ($carrier_array as $key => $carrierRow) {
      $selectCarrier = "SELECT id FROM prd_fees WHERE setting_type='Carrier' AND name=:name AND is_deleted='N' ";
      $resultCarrier = $pdo->selectOne($selectCarrier, array(":name"=>$carrierRow['name']));

      if (empty($resultCarrier)) {
        $display_id=get_carrier_id();

        $insert_params = array(
          'name' => $carrierRow['name'],
          'display_id' => $display_id,
          'setting_type' => 'Carrier',
          'contact_fname' => '',
          'contact_lname' => '',
          'phone' => '',
          'email' => '',
          'status' => 'Active',
          'use_appointments' => 'N',
        );
        $carrier_id = $pdo->insert("prd_fees", $insert_params);
      }
    }
  }
//******* Carrier insert code end   *******
echo "Carrier->Completed<br>";
echo "before_product_import->Completed";
dbConnectionClose();
exit;
?>

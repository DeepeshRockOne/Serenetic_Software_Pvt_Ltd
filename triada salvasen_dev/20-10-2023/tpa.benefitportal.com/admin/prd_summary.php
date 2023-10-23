<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(15);
require_once "../libs/dompdf/dompdf_config.inc.php";
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Product List';
$breadcrumbes[1]['link'] = 'manage_product.php';
$breadcrumbes[2]['title'] = 'Add Product';
$page_title = "Add New Product";
$generatePdf= isset($_GET['generatePdf']) ? $_GET['generatePdf'] : 0;
$product_id=$_GET['id'];
$sqlProduct="SELECT * FROM prd_main where id=:id";
$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product_id));

$memberDetailsArray = array(
	'fname'=>'First Name',
	'lname'=>'Last Name',
	'email'=>'Email',
	'phone'=>'Phone',
	'SSN'=>'SSN',
	'address'=>'Address',
	'city'=>'City',
	'state'=>'State',
	'zipcode'=>'ZipCode',
	'dob'=>'Date of Birth (Age)',
	'gender'=>'Legal Sex/Gender',
	'Salary'=>'Salary',
	'employmentStatus'=>'Employment Status',
	'tobaccoUse'=>'Tobacco Use',
	'Smoke'=>'Smoke',
	'height'=>'Height',
	'weight'=>'Weight',
	'BenefitLevel'=>'Benefit Level',
);
$spouseDetailsArray = array(
	'fname'=>'First Name',
	'lname'=>'Last Name',
	'email'=>'Email',
	'phone'=>'Phone',
	'SSN'=>'SSN',
	'address'=>'Address',
	'city'=>'City',
	'state'=>'State',
	'zipcode'=>'ZipCode',
	'relation'=>'Relation',
	'dob'=>'Date of Birth (Age)',
	'gender'=>'Legal Sex/Gender',
	'Salary'=>'Salary',
	'employmentStatus'=>'Employment Status',
	'tobaccoUse'=>'Tobacco Use',
	'Smoke'=>'Smoke',
	'height'=>'Height',
	'weight'=>'Weight',
	'BenefitLevel'=>'Benefit Level',
);
$dependantDetailsArray = array(
	'fname'=>'First Name',
	'lname'=>'Last Name',
	'email'=>'Email',
	'phone'=>'Phone',
	'SSN'=>'SSN',
	'address'=>'Address',
	'city'=>'City',
	'state'=>'State',
	'zipcode'=>'ZipCode',
	'relation'=>'Relation',
	'dob'=>'Date of Birth (Age)',
	'gender'=>'Legal Sex/Gender',
	'Salary'=>'Salary',
	'employmentStatus'=>'Employment Status',
	'tobaccoUse'=>'Tobacco Use',
	'Smoke'=>'Smoke',
	'height'=>'Height',
	'weight'=>'Weight',
	'BenefitLevel'=>'Benefit Level',
);

$primaryPricingArray = array(
	'Age'=>'Age (Date of Birth)',
	'State'=>'State',
	'Zip Code'=>'Zip Code',
	'Gender'=>'Legal Sex/Gender',
	'Smoke'=>'Smoke',
	'Tobacco Use'=>'Tobacco Use',
	'Height'=>'Height',
	'Weight'=>'Weight',
	'Number Of Children'=>'Number Of Children',
	'Has Spouse'=>'Has Spouse',
);

$spouseMatrixArray = array(
	'Spouse Age'=>'Spouse Age',
	'Spouse Gender'=>'Spouse Gender',
	'Spouse Smoke'=>'Spouse Smoke',
	'Spouse Tobacco Use'=>'Spouse Tobacco Use',
	'Spouse Height'=>'Spouse Height',
	'Spouse Weight'=>'Spouse Weight',
);

$matrixPricingArray = array_merge($primaryPricingArray,$spouseMatrixArray);



if($resProduct){
	
	//********** step1 varible intialization code start **********************
		$company_id = $resProduct['company_id'];

		$companySql="SELECT company_name FROM prd_company where id=:id";
		$companyRes=$pdo->selectOne($companySql,array(":id"=>$company_id));
		$company_name='';
		if($companyRes){
			$company_name=$companyRes['company_name'];
		}

		$product_name = $resProduct['name'];
		$product_code = $resProduct['product_code'];
		$category_id  = $resProduct['category_id'];

		$sqlProductCode="SELECT code_no,plan_code_value 
						FROM prd_plan_code 
						WHERE product_id=:id AND is_deleted='N'";
		$resProductCode=$pdo->select($sqlProductCode,array(":id"=>$product_id));

		$categorySql="SELECT title FROM prd_category where id=:id";
		$categoryRes=$pdo->selectOne($categorySql,array(":id"=>$category_id));
		$category_name='';
		if($categoryRes){
			$category_name=$categoryRes['title'];
		}

		if($resProduct['is_association_require']=='Y' && $resProduct['association_ids']!=""){
			$associaltonIDs=$resProduct['association_ids'];
			$associationSql="SELECT p.*, pm.price 
							FROM prd_main p 
							JOIN prd_matrix pm on p.id=pm.product_id 
							WHERE p.id IN($associaltonIDs) AND pm.is_Deleted='N'";
			$associationRes=$pdo->select($associationSql);
		}
		

		$carrier_id = $resProduct['carrier_id'];
		$carrierSql="SELECT title FROM prd_carrier where id=:id";
		$carrierRes=$pdo->selectOne($carrierSql,array(":id"=>$carrier_id));
		$carrier_name='';
		if($carrierRes){
			$carrier_name=$carrierRes['title'];
		}

		$enrollment_type = isset($resProduct['enrollment_type'])?$resProduct['enrollment_type']:"";
		$product_type = $resProduct['product_type'];
		
		$outside_sales_page_desc = $resProduct['long_desc'];

		$sqlDepartment="SELECT * FROM prd_department_description where product_id = :product_id AND is_deleted='N'";
		$resDepartment=$pdo->select($sqlDepartment,array(":product_id"=>$product_id));

		$sectionCount=1;
		$member_portal ='';
		$pdf_member_portal ='';
		if(!empty($resDepartment)){
			foreach ($resDepartment as $key => $value) {
				$pdf_member_portal .='<h5 style="margin:0; padding-top:15px; font-size:12px; font-weight:600; color:#000;">Section '.$sectionCount.' - Title:</h5>';
				$pdf_member_portal .= $value['name'];
				$pdf_member_portal .='<h5 style="margin:0; padding-top:15px; font-size:12px; font-weight:600; color:#000;">Section '.$sectionCount.' - Description:</h5>';
				$pdf_member_portal	 .= $value['description'];
				
				$member_portal .='<h5 class="h5_title p-b-5 p-r-20 m-t-20">Section '.$sectionCount.' - Title:</h5>';
				$member_portal .= $value['name'];
				$member_portal .='<h5 class="h5_title p-b-5 p-r-20 m-t-20">Section '.$sectionCount.' - Description:</h5>';
				$member_portal .= $value['description'];
				$sectionCount++;
			}
		}
		$agent_portal = $resProduct['agent_portal'];
		$limitations_exclusions = $resProduct['limitations_exclusions'];
	//********** step1 varible intialization code end   **********************

	//********** step2 varible intialization code start **********************
		$direct_policy = isset($resProduct['direct_policy'])?$resProduct['direct_policy']:"";
		$directPolicyText = '';

		if($direct_policy=="Next Day"){
			$directPolicyText='Next Day';
		}else if($direct_policy=="First Of Month"){
			$directPolicyText='First of the Following Month';
		}else if($direct_policy=="Select Day Of Month"){
			$directPolicyText='Select Day of Month';
		}
		$effective_day = $resProduct['effective_day'];
		$sold_day = $resProduct['sold_day'];

		$group_policy = isset($resProduct['group_policy'])?$resProduct['group_policy']:"";
		$group_effective_day = $resProduct['group_effective_day'];
		$group_sold_day = $resProduct['group_sold_day'];

		$is_association_require = $resProduct['is_association_require'];
		$association_ids_list = $resProduct['association_ids'];
		$association_ids = !empty($association_ids_list) ? explode(",", $association_ids_list) : array();

		$available_state_list = isset($resProduct['carrier_state'])?$resProduct['carrier_state']:array();
		$available_state = !empty($available_state_list) ? explode(",", $available_state_list) : array();
	
		$is_zipcode_restricted = $resProduct['is_zipcode_restricted'];
		$restricted_zipcodes_json = $resProduct['restricted_zipcodes'];
		$restricted_zipcodes = json_decode($restricted_zipcodes_json,true);
		$restricted_zipcodes_text='';

		if(isset($restricted_zipcodes) && count($restricted_zipcodes) > 0){
			foreach ($restricted_zipcodes as $key => $restricted) {
				$state_name=getname('states_c',$key,'short_name','name');
                $state_name = $state_name.', '.$key;
                $restricted_zipcodes_text .=$state_name.'</br>';
                $restricted_zipcodes_text .='<span class="h5_title">ZipCodes: </span>'.$restricted.'</br></br>';
			}
		}

		$coverage_options_list = $resProduct['coverage_options'];
		$coverage_options = !empty($coverage_options_list) ? explode(",", $coverage_options_list) : array();
		$coverage_text='';
		if(count($coverage_options) > 0){
			foreach ($coverage_options as $key => $value) {
				$coverageName=getname('prd_plan_type',$value,'title','id');
				if($coverage_text == "Member"){
                	$coverageName='Member Only';
              	}
				$coverage_text.=$coverageName.'</br>';
			}
		}

		$family_plan_rule = $resProduct['family_plan_rule'];

		$is_primary_age_restrictions = $resProduct['is_primary_age_restrictions'];
		$primary_age_restrictions_from = $resProduct['primary_age_restrictions_from'];
		$primary_age_restrictions_to = $resProduct['primary_age_restrictions_to'];
		$primary_text = ($is_primary_age_restrictions=='Y') ? 'From '.$primary_age_restrictions_from.' to '.$primary_age_restrictions_to: 'No';


		$is_spouse_age_restrictions = $resProduct['is_spouse_age_restrictions'];
		$spouse_age_restrictions_from = $resProduct['spouse_age_restrictions_from'];
		$spouse_age_restrictions_to = $resProduct['spouse_age_restrictions_to'];
		$spouse_text = ($is_spouse_age_restrictions=='Y') ? 'From '.$spouse_age_restrictions_from.' to '.$spouse_age_restrictions_to: 'No';

		$is_children_age_restrictions = $resProduct['is_children_age_restrictions'];
		$children_age_restrictions_from = $resProduct['children_age_restrictions_from'];
		$children_age_restrictions_to = $resProduct['children_age_restrictions_to'];
		$children_text = ($is_children_age_restrictions=='Y') ? 'From '.$children_age_restrictions_from.' to '.$children_age_restrictions_to: 'No';

		$child_products_list = $resProduct['child_product'];
		$child_products = !empty($child_products_list) ? explode(",", $child_products_list) : array();
		$childProduct_text = '';
		if(!empty($child_products)){
			foreach ($child_products as $key => $value) {
				if($value!='Future_Product'){
					$childProduct_text.='<p style="margin:0px; padding:0 0 5px 0">'.getname('sub_products',$value,'product_name','id').'</p>';
				}else{
					$childProduct_text.='<p style="margin:0px; padding:0 0 5px 0">Future Product </p>';
				}
				
			}
		}
		//$combination_product_type = $resProduct['combination_product_type'];
		$combination_product_type = 'Excludes';
		$combination_products_list = $resProduct['restricted_products'];
		$combination_product = !empty($combination_products_list) ? explode(",", $combination_products_list) : array();
		$exluded_product_text = '';

		$required_product_type = 'Required';
		$required_product_list = $resProduct['auto_assign_no_delete_product'];
		$required_product = !empty($required_product_list) ? explode(",", $required_product_list) : array();
		$required_product_text = '';

		$auto_assisgn_product_list = $resProduct['auto_assign_product'];
		$auto_assisgn_product = !empty($auto_assisgn_product_list) ? explode(",", $auto_assisgn_product_list) : array();
		$auto_assisgn_product_text = '';

		$packaged_product_list = $resProduct['packaged_product'];
		$packaged_product = !empty($packaged_product_list) ? explode(",", $packaged_product_list) : array();
		$packaged_product_text = '';

		if(!empty($combination_product)){
			foreach ($combination_product as $key => $value) {
				$exluded_product_text.=getname('prd_main',$value,'name','id').', ';
			}
		}
		if(!empty($required_product)){
			foreach ($required_product as $key => $value) {
				$required_product_text.=getname('prd_main',$value,'name','id').', ';
			}
		}
		if(!empty($auto_assisgn_product)){
			foreach ($auto_assisgn_product as $key => $value) {
				$auto_assisgn_product_text.=getname('prd_main',$value,'name','id').', ';
			}
		}
		if(!empty($packaged_product)){
			foreach ($packaged_product as $key => $value) {
				$packaged_product_text.=getname('prd_main',$value,'name','id').', ';
			}
		}

		$termination_rule = $resProduct['termination_rule'];
		
		$reinstate_option = $resProduct['reinstate_option'];
		$reinstate_within_days = isset($resProduct['reinstate_within_days'])?$resProduct['reinstate_within_days']:0;
		$reinstate_option_text='Not available';
		if($reinstate_option=='Y'){
			$reinstate_option_text='Within '.$reinstate_within_days.' days of last payment.';
		}
		
		$reenroll_options = $resProduct['reenroll_options'];
		$reenroll_within_days = isset($resProduct['reenroll_within_days'])?$resProduct['reenroll_within_days']:0;
		$reenroll_option_text='Not available';
		if($reenroll_options=='Y'){
			$reenroll_option_text='Within '.$reenroll_within_days.' days of termination.';
		}
	//********** step2 varible intialization code end   **********************
	
	//********** step3 varible intialization code start **********************
		$member_details_asked_list = $resProduct['member_details_asked'];
		$member_details_asked = !empty($member_details_asked_list) ? explode(",", $member_details_asked_list) : array();

		$member_details_required_list = $resProduct['member_details_required'];
		$member_details_required = !empty($member_details_required_list) ? explode(",", $member_details_required_list) : array();

		$spouse_details_asked_list = $resProduct['spouse_details_asked'];
		$spouse_details_asked = !empty($spouse_details_asked_list) ? explode(",", $spouse_details_asked_list) : array();

		$spouse_details_required_list = $resProduct['spouse_details_required'];
		$spouse_details_required = !empty($spouse_details_required_list) ? explode(",", $spouse_details_required_list) : array();

		$dependent_details_asked_list = $resProduct['dependent_details_asked'];
		$dependent_details_asked = !empty($dependent_details_asked_list) ? explode(",", $dependent_details_asked_list) : array();

		$dependent_details_required_list = $resProduct['dependent_details_required'];
		$dependent_details_required = !empty($dependent_details_required_list) ? explode(",", $dependent_details_required_list) : array();

		$enrollment_verification_list = $resProduct['enrollment_verification'];
		$enrollment_verification = !empty($enrollment_verification_list) ? explode(",", $enrollment_verification_list) : array();
		$enrollment_verification_options =array();

		foreach ($enrollment_verification as $key => $value) {
			if($value=='upload_document'){
				array_push($enrollment_verification_options,"Upload Document");
			}
			if($value=='eSign'){
				array_push($enrollment_verification_options,"eSign");
			}
			if($value=='voice_verification'){
				array_push($enrollment_verification_options,"Voice Verification");
			}
			if($value=='allVerification'){
				$enrollment_verification_options =array();
				array_push($enrollment_verification_options,"Upload Document");
				array_push($enrollment_verification_options,"eSign");
				array_push($enrollment_verification_options,"Voice Verification");
			}
		}
		$enrollment_verification_options_list = !empty($enrollment_verification_options) ? implode(", ", $enrollment_verification_options) : '';

		$is_eSignTermsCondition = $resProduct['is_eSignTermsCondition'];
		$eSignTermsCondition_doc = $resProduct['eSignTermsCondition_doc'];
		$eSignTermsCondition_desc = $resProduct['eSignTermsCondition_desc'];

		$eSIGN_TERMS_CONDITION_DIR = $SITE_SETTINGS[3]['ESIGN_TERMS_CONDITION']['upload'];
		$eSIGN_TERMS_CONDITION_WEB = $SITE_SETTINGS[3]['ESIGN_TERMS_CONDITION']['download'];
		
		$eSignTermsCondition_doc_old = '';
		if(!empty($eSignTermsCondition_doc) && remote_file_exists($eSIGN_TERMS_CONDITION_DIR . $eSignTermsCondition_doc)){
			$eSignTermsCondition_doc_old = $eSignTermsCondition_doc;
		}

		$is_agent_requirement = $resProduct['is_agent_requirement'];
		$agent_requirement_list = $resProduct['agent_requirement'];
		$agent_requirement = !empty($agent_requirement_list) ? explode(",", $agent_requirement_list) : array();

		$license_rule = $resProduct['license_rule'];
		$is_writing_number = $resProduct['is_writing_number'];
	//********** step3 varible intialization code end   **********************
	
	//********** step4 varible intialization code start **********************
		$member_payment = $resProduct['member_payment'];
		$member_payment_type = $resProduct['member_payment_type'];

		$is_enrollment_fee = $resProduct['is_enrollment_fee'];
		$enrollment_fee_ids_list = $resProduct['enrollment_fee_ids'];
		$enrollment_fee_ids = !empty($enrollment_fee_ids_list) ? explode(",", $enrollment_fee_ids_list) : array();

		$is_admin_fee = $resProduct['is_admin_fee'];
		$admin_fee_ids_list = $resProduct['admin_fee_ids'];
		$admin_fee_ids = !empty($admin_fee_ids_list) ? explode(",", $admin_fee_ids_list) : array();

		$is_service_fee = $resProduct['is_service_fee'];
		$service_fee_ids_list = $resProduct['service_fee_ids'];
		$service_fee_ids = !empty($service_fee_ids_list) ? explode(",", $service_fee_ids_list) : array();

		$is_product_commissionable = $resProduct['is_product_commissionable'];
		$is_commissionable_by_tier = $resProduct['is_commissionable_by_tier'];
		 
	//********** step4 varible intialization code end   **********************

	
}else{
	setNotifyError("No Product Found");
	redirect("manage_product.php");
}

if(isset($_GET['profile_id'])){
	$levelParam = array(":profile_id"=>$_GET['profile_id']);
	$agent_coded_profile = $_GET['profile_id'];
}else{
	$levelParam = array(":profile_id"=>1);
	$agent_coded_profile = 1;
}
$agentCodedLevelSql="SELECT * FROM agent_coded_level WHERE is_active='Y' AND profile_id=:profile_id ORDER BY id DESC";
$agentCodedLevelRes=$pdo->select($agentCodedLevelSql,$levelParam);

$summary='';
//********************** Summary Html Code Start *********************** 
$summary .= "<style type='text/css'>
	strong {font-weight: bold; color:#686868;}
	h1, h2, h3, h4, h5, h6 { color:#333;}
	body { font-family:Arial, Helvetica, sans-serif; font-size:12px; }
	 </style>";
	
$summary .= '</head><body style="font-family: Arial, Helvetica, sans-serif !important; font-weight: normal; font-size:12px; color:#686868;">';
$summary.='<table cellpadding="0" cellspacing="0" border="0" >
      	<tbody>
		
<tr >
          	<td ><img src="https://portal.agentra.com/admin/images/logo.png" height="53px"></td>
            <td style="padding-left:25px;" valign="top"><h2 style="margin:0px; padding:0px; font-size:18px;">'.$company_name.'</h2>
			<p style="margin:0px; padding:0px; font-size:18px;">'.$product_name.'</p>
			</td>
          </tr>
		  <tr><div style="background:#979797; margin-top:27px;  width:100%; height:0.50px;"></div></tr>
		         </tbody>
      </table>';
$summary.='<h3 style="margin:0px; font-size:20px; line-height:20px; padding-bottom:10px; padding-top:27px;">Product Information</h3>';
$summary.='<table cellpadding="0" cellspacing="0" border="0" >
      	<tbody>
					<tr>
						<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;" valign="top">Product Company:</h4></td>
						<td valign="top">'.$company_name.'</td>
					</tr>
        	<tr>
          	<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;" valign="top">Product Name:</h4></td>
            <td valign="top">'.$product_name.'</td>
          </tr>
          <tr>
          	<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Product ID:</h4></td>
            <td valign="top">'.$product_code .'</td>
          </tr>
          <tr>
          	<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Category:</h4></td>
            <td valign="top">'.$category_name.'</td>
          </tr>
          <tr>
          	<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Carrier:</h4></td>
            <td valign="top">'.$carrier_name .'</td>
          </tr>
          <tr>
          	<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Product Type:</h4></td>
            <td valign="top">'. $product_type .'</td>
					</tr>';
		if(isset($resProductCode) && !empty($resProductCode)){
			$i=0;
			foreach ($resProductCode as $key => $value) {
				$summary.='<tr>';
				if($value['code_no']=='GC'){
					$summary.='<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Group Code:</h4></td>';
				}else{
					$summary.='<td><h4 style="margin: 0px; padding-bottom:7px; padding-right:20px;"  valign="top">Plan Code '.$i.':</h4></td>';
				}
				$summary.='<td valign="top">'. $value['plan_code_value'] .'</td>
				</tr>';
				$i++;
			}
		}
		$summary.='</tbody>
      </table>';

 
    
$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px;">Member Portal</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$pdf_member_portal.'</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Inside Agent Portal</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$agent_portal.'</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Limitations & Exclusions</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$limitations_exclusions.'</p>';

$summary.='<h3 style="padding-top:30px; padding-bottom:10px; font-size:20px; line-height:20px; margin:0px;">Product Rules</h3>';
$summary.='<h5 style="padding-top:10px; font-size:12px; line-height:12px; margin:0px;">Effective Date</h5>';
$summary.='<p >
        <strong  class="fw-700">Direct Plan</strong> <br />
        '.$directPolicyText;
        if($direct_policy=="Select Day Of Month"){
          $summary.='<strong  class="fw-700">Day of Month:</strong>'.$effective_day.'<br />';
        }
        if($direct_policy=="Select Day Of Month" || $direct_policy=="First Of Month"){ 
          $summary.='<strong  class="fw-700">Can be sold until day of month:</strong> '.$sold_day;
        } 
$summary.='</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px;"> Association Requirement</h5>';
$summary.='<p>';
		if($is_association_require=='Y'){
			$summary.='Yes <br />';
		} else{
			$summary.='No <br />';
		}
        if($is_association_require=='Y') { 
			if(isset($associationRes) && !empty($associationRes)){
				foreach ($associationRes as $akey => $avalue) {
					$is_association_fee_included_string=$avalue['is_association_fee_included']=='Y'?'Included in Price':"Not Included in Price";
					$summary.='<p></p><strong class="fw-700">Association:</strong> '.$avalue['name'].' <br />
					<strong class="fw-700">Association Fee Type:</strong> '.$is_association_fee_included_string.' <br />
					<strong class="fw-700">Association Fee Amount:</strong> $'.$avalue['price'].' ';
				}
			}
        } 
$summary.='</p>';
$summary.='<p style="margin:0px; padding:0px;"><div style="clear:both"></div></p>';
$summary.='<h5 style="padding-top:20px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;" >Available States</h5>';
$summary.='<p style="margin:0px; padding:0px;"><div style="clear:both"></div></p>';
$summary.='<div style="width:100%; clear:both">
        <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">';
          $stateRowCount=0;
          if(!empty($available_state)){ 
            foreach ($available_state as $key => $state) { 
              if($stateRowCount!=0 && $stateRowCount%13 == 0){
                $summary.='</ul>';
                $summary.=' <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">';
              } 
              $summary.='<li>';
                  $state_name=getname('states_c',$state,'short_name','name');
                  $summary.= $state_name.', '.$state .'</br>';
              $summary.='</li>';
              $stateRowCount++;
            }
          }
		$summary.='</ul>';
$summary.='</div>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;" >Specific Zipcodes Available</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$restricted_zipcodes_text.'</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;"> Plan Options</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$coverage_text .'</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Age Restrictions</h5>';
$summary.='<p style="margin:0px; padding:0px;">
        <strong >Primary:</strong>'.$primary_text .' <br /> 
        <strong>Spouse:</strong> '.$spouse_text .'   <br />      
        <strong >Children:</strong> '.$children_text .'
      </p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Child Products</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$childProduct_text .'</p>';
      
$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;"> Additional Product Combination Rules</h5>';
$summary.='<p style="margin:0px; padding:0px;" class="fs16"><strong class="fw-700">May Not Have:</strong> '.$exluded_product_text .'</p>
	  <p style="margin:0px; padding:0px;" class="fs16"><strong class="fw-700">Must Have:</strong> '.$required_product_text .'</p>
	  <p style="margin:0px; padding:0px;" class="fs16"><strong class="fw-700">Auto Assign:</strong> '.$auto_assisgn_product_text .'</p>
	  <p style="margin:0px; padding:0px;" class="fs16"><strong class="fw-700">Packaged Product:</strong> '.$packaged_product_text .'</p>';
      
$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Termination</h5>';
$summary.='<p style="margin:0px; padding:0px;"><strong class="fw-700">Date Rules:</strong> '.$termination_rule.' <br />
        <strong class="fw-700">Reinstate Options:</strong> '. $reinstate_option_text .' <br />
        <strong class="fw-700">Reenroll Options:</strong> '.$reenroll_option_text .'
      </p>';
$summary.='<p ><div style="clear:both"></div></p>';
$summary.='<h3 style="padding-top:30px; padding-bottom:10px; font-size:20px; line-height:20px; margin:0px;">Application</h3>';
$summary.='<p style="margin:0px; padding:0px;"><div style="clear:both"></div></p>';
$summary.='<div style="width:100%; clear:both">
         <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Member Details Asked</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($member_details_asked)){
              foreach ($member_details_asked as $key => $value) { 
              	  if(!empty($memberDetailsArray[$value])){
                    $summary.=$memberDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }
                  
              } 
            } 
$summary.='</li>           
        </ul>
    <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Member Details Required</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($member_details_required)){
              foreach ($member_details_required as $key => $value) { 
                  if(!empty($memberDetailsArray[$value])){
                    $summary.=$memberDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }
              } 
						} 
	$summary.='</li>           
        </ul>
        <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Dependant Details Asked</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($spouse_details_asked)){
              foreach ($spouse_details_asked as $key => $value) { 
                  if(!empty($spouseDetailsArray[$value])){
                    $summary.=$spouseDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }

              } 
						}
	$summary.='</li>           
        </ul>
    <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Member Details Required</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($spouse_details_required)){
              foreach ($spouse_details_required as $key => $value) { 
                  if(!empty($spouseDetailsArray[$value])){
                    $summary.=$spouseDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }
              } 
						}  
$summary.='<br/></li>           
        </ul>
        <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Dependant Details Asked</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($dependent_details_asked)){
              foreach ($dependent_details_asked as $key => $value) { 
                  if(!empty($dependantDetailsArray[$value])){
                    $summary.=$dependantDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }

              } 
            } 
$summary.='</li>                
        </ul>
        <ul style="width:25%;  display:inline-block; list-style-type:none; padding:0px; margin:0px;">
          <h5 style="font-size:13px; margin:0px 0px 7px 0px; padding:0px;">Dependant Details Required</h5>
          <li style="padding-bottom:5px;">';
            if(!empty($dependent_details_required)){
              foreach ($dependent_details_required as $key => $value) { 
                  if(!empty($dependantDetailsArray[$value])){
                    $summary.=$dependantDetailsArray[$value].'<br />'; 
                  }
                  if(!empty($matrixPricingArray[$value])){
                    $summary.=$matrixPricingArray[$value].'<br />';
                  }
              } 
            }
$summary.='</li>                
        </ul>
      </div>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Verification</h5>';
$summary.='<p style="margin:0px; padding:0px;"><strong class="fw-700">Options:</strong>'.$enrollment_verification_options_list.'<br />';
        
$summary.='<strong>E-Sign Special Terms & Conditions:</strong>';
		if(in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y'){
			$summary.='Required';
		}else{
			$summary.='Not Required';
		}
        
        if (in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y' && $eSignTermsCondition_doc_old != ''){
            $summary.='<a href="'.$ESIGN_TERMS_CONDITION_WEB.$eSignTermsCondition_doc_old.'" target="_blank">
                   
            </a>';
        }
$summary.='</p>';
      if (in_array("eSign",$enrollment_verification_options) && $is_eSignTermsCondition=='Y' && $eSignTermsCondition_desc != ''){
      $summary.='<p style="margin:0px; padding:0px;">'.$eSignTermsCondition_desc.'</p>';
      }

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Agent Requirements</h5>';
$summary.='<p style="margin:0px; padding:0px;">
        <strong>Licenses:</strong>  <br />';
        if($is_agent_requirement=='Y'){
			$summary.='Required';
		}else{
			$summary.='Not Required';
		} 
$summary.='<br />';
        if($is_agent_requirement=='Y' && count($agent_requirement) > 0) {
          foreach ($agent_requirement as $key => $value) {
            if($value=="GeneralLines_Both"){
                $summary.='General Lines/Both <br />';
            }else{
               $summary.=$value.'<br />';
            } 
          }
        }
$summary.='<br />
        <strong >License Rules:</strong><br /> 
        '.$license_rule.'
      </p>';

$summary.='<h3 style="padding-top:30px; padding-bottom:10px; font-size:20px; line-height:20px; margin:0px;">Pricing</h3>';
$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;"> Member Payment Subscription Type</h5>';
$summary.='<p style="margin:0px; padding:0px;">'.$member_payment .' Payment <br />';
        if($member_payment=='Recurring'){
        	$summary.=$member_payment_type;
        }else{
        	$summary.='';
        }
$summary.='</p>';

$summary.='<h5 style="padding-top:30px; font-size:14px; line-height:14px; margin:0px; padding-bottom:10px;">Benefit Teir Pricing</h5>';
      	if(!empty($coverage_options)) {
         foreach ($coverage_options as $key => $value) {
            $coverage_name = getname('prd_plan_type',$value,'title','id');

            if($coverage_name == "Member"){
              $coverage_name='Member Only';
            }

            $prdMatrixSql="SELECT id,price,non_commission_amount,commission_amount 
            				FROM prd_matrix 
            				WHERE product_id=:product_id AND plan_type=:plan_type AND is_deleted='N'";
            $prdMatrixWhr=array(":product_id"=>$product_id,":plan_type"=>$value);
						$prdMatrixRes=$pdo->select($prdMatrixSql,$prdMatrixWhr);
						if(isset($prdMatrixRes) && !empty($prdMatrixRes)){
							foreach ($prdMatrixRes as $key => $value) {
          
$summary.='<p style="margin:0px; padding:0px;"><strong >'.$coverage_name .'</strong> <br /> 
            Price: $'.$value['price'] .' <br />
            Non Commissionable: $'.$value['non_commission_amount'] .' <br />
            Commissionable: $'.$value['commission_amount'] .' <br />
					</p><p></p>';
					 }
					}
         }
      	}
 

			$i = 1;
      if($is_product_commissionable == 'Y'){
$summary.='<div style=" list-style-type:none; padding:0px; margin:0px;">';
          if(!empty($commission_price_array)){
            if(!empty($agentCodedLevelRes)){
              foreach ($agentCodedLevelRes as $key => $codedLevel) {
                if($is_commissionable_by_tier == 'N') {
                  if(!empty($commission_price_array)){
                    
                      $summary.='<div style="width:33.333%; display:inline-block;	 ">';
                      $summary.='<h5 style="margin:13px 0px; font-size:12px;">'.$codedLevel['level_heading'] .' : ';

                      if($commission_price_array[$codedLevel['level']]['amount_type'] == "Percentage"){
                      	$summary.='%';
                      }else{
                      	$summary.='$';
                      } 
                      $summary.=$commission_price_array[$codedLevel['level']]['amount'];
                      $summary.='</h5>';
                      $summary.='</div>';
					  if($i%3 == 0){
                      	$summary.= '<div style="clear:both"></div>';
                      }
                  }
                }else{
                  if(!empty($commission_price_array)){
                    $summary.='<div style="  width:33.333%; display:inline-block;">';
                      $summary.='<h5 style="margin:13px 0px; font-size:12px;">'.$codedLevel['level_heading'].'</h5>';
                      $summary.='<table border="0" cellpadding="0" cellspacing="0" class="fs16">'; 
                        $summary.='<tbody>';
                      		foreach ($commission_price_array as $planId => $commission) {
                            
								$plan_name='';
								if($planId==1){
									$plan_name = 'Member Only';
								}else if($planId==5){
									$plan_name = 'Member + One';
								}else if($planId==2){
									$plan_name = 'Member & Child(ren)';
								}else if($planId==3){
									$plan_name = 'Member & Spouse';
								}else if($planId==4){
									$plan_name = 'Family';
								}
	                            
	                            $summary.='<tr>';
	                               $summary.='<td><strong class="fw-700">'.$plan_name .':</strong></td>';
	                               $summary.='<td>';
	                                if($commission_price_array[$planId][$codedLevel['level']]['amount_type'] == "Percentage"){
	                                	$summary.='%';
	                                }else{
	                                	$summary.='$';
	                                }
	                                $summary.=$commission_price_array[$planId][$codedLevel['level']]['amount'];
	                                $summary.='</td>';
	                            $summary.='</tr>';
                          	}
                        $summary.='</tbody>';
                      $summary.='</table>';
                    $summary.='</div>';
					if($i%3 == 0){
                      	$summary.= '<div style="clear:both"></div>';
                    }
                  }
                }
								$i++;
              }
							
            }
          }
$summary.='</div>';
      }


if($generatePdf==1) {
      $dompdf = new DOMPDF();
      $dompdf->load_html($summary);
      $dompdf->render();
      $dompdf->stream("Product_Summary" . date('YmdHis') . ".pdf");
      redirect('prd_summary.php?id='.$product_id);
      exit;
}    

//********************** Summary Html Code end   *********************** 
 
$template = "prd_summary.inc.php";
include_once 'layout/end.inc.php';
?>
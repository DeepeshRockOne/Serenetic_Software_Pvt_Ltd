<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$is_ajaxed = checkIsset($_POST['is_ajaxed']);
if($is_ajaxed){
    include_once __DIR__ . '/../includes/member_enrollment.class.php';
    $customer_id = $_POST['id'];
    $MemberEnrollment = new MemberEnrollment();
    $primary_member_field = $MemberEnrollment->get_primary_member_field_all();
    $spouse_field = $MemberEnrollment->get_spouse_field_all();
    $child_field = $MemberEnrollment->get_child_field_all();
    $principal_beneficiary_field = $MemberEnrollment->get_principal_beneficiary_field_all();
    $contingent_beneficiary_field = $MemberEnrollment->get_contingent_beneficiary_field_all();
    
    $product_ids_arr = array();
    $product_ids = '';
    $ws_sql = "SELECT GROUP_CONCAT(DISTINCT ws.product_id) as product_ids
                FROM website_subscriptions as ws
                WHERE ws.status NOT IN('Inactive') AND ws.product_type = 'Normal' AND md5(ws.customer_id)=:customer_id";
    $ws_where = array(":customer_id" => $customer_id);
    $ws_res = $pdo->selectOne($ws_sql,$ws_where);
    if(!empty($ws_res)) {
        if(!empty($ws_res['product_ids'])) {
            $product_ids = $ws_res['product_ids'];
            $product_ids_arr = explode(',',$ws_res['product_ids']);
        }    
    }
    if(is_array($product_ids_arr) && !empty($product_ids_arr)) {
        $prd_primary_member_field = $MemberEnrollment->get_primary_member_field_all($product_ids_arr);
        $prd_spouse_field = $MemberEnrollment->get_spouse_field_all($product_ids_arr);
        $prd_child_field = $MemberEnrollment->get_child_field_all($product_ids_arr);    
    }
    
    
    $is_update = checkIsset($_POST['is_update']);
    if ($is_update) {
        $validate = new Validation();
        $response = array();
    
        if(!empty($primary_member_field)){
            foreach($primary_member_field as $key => $row) {
                ${'primary_'.$row['label']} = isset($_POST['primary_'.$row['label']])?$_POST['primary_'.$row['label']]:"";
            }
        }
    
        $spouse_ids = isset($_POST['spouse_ids'])?$_POST['spouse_ids']:array();
        $spouse_data = array();
        if(!empty($spouse_ids)) {
            foreach ($spouse_ids as $spouseKey => $spouse_id) {
                if(!empty($spouse_field)){
                    foreach($spouse_field as $field_key => $row) {
                        $control_value = isset($_POST['spouse_'.$row['label']][$spouseKey])?$_POST['spouse_'.$row['label']][$spouseKey]:"";
                        $spouse_data[$spouseKey][$row['label']] = $control_value;
                    }
                }
            }
        }
    
        $child_ids = isset($_POST['child_ids'])?$_POST['child_ids']:array();
        $child_data = array();
        if(!empty($child_ids)) {
            foreach ($child_ids as $childKey => $child_id) {
                if(!empty($child_field)){
                    foreach($child_field as $field_key => $row) {
                        $control_value = isset($_POST['child_'.$row['label']][$childKey])?$_POST['child_'.$row['label']][$childKey]:"";
                        $child_data[$childKey][$row['label']] = $control_value;
                    }
                }
            }
        }
    
        $principal_beneficiary_field = $MemberEnrollment->get_principal_beneficiary_field_all();
        $contingent_beneficiary_field = $MemberEnrollment->get_contingent_beneficiary_field_all();
    
        $cust_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,c.email,c.cell_phone,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,c.address,c.address_2,c.city,c.state,c.zip,c.birth_date,c.gender,cs.salary,cs.employmentStatus,cs.tobacco_use,cs.smoke_use,cs.height_feet,cs.height_inch,cs.weight,cs.benefit_level,cs.hours_per_week,cs.pay_frequency,cs.us_citizen,cs.no_of_children,cs.has_spouse,cs.hire_date
              FROM customer c 
              LEFT JOIN customer_settings cs ON (c.id = cs.customer_id)
              WHERE md5(c.id)=:customer_id";
        $cust_row = $pdo->selectOne($cust_sql, array(":customer_id" => $customer_id));

        $primary_product_arr = isset($_POST['primary_product']) ? $_POST['primary_product'] : array();
        $primary_benefit_amount = isset($_POST['primary_benefit_amount']) ? $_POST['primary_benefit_amount'] : array();
        $primary_in_patient_benefit = isset($_POST['primary_in_patient_benefit']) ? $_POST['primary_in_patient_benefit'] : array();
        $primary_out_patient_benefit = isset($_POST['primary_out_patient_benefit']) ? $_POST['primary_out_patient_benefit'] : array();
        $primary_monthly_income = isset($_POST['primary_monthly_income']) ? $_POST['primary_monthly_income'] : array();
        $primary_benefit_percentage = isset($_POST['primary_benefit_percentage']) ? $_POST['primary_benefit_percentage'] : array();

        $spouse_product_arr = isset($_POST['spouse_product']) ? $_POST['spouse_product'] : array();
        $spouse_benefit_amount_arr = isset($_POST['spouse_benefit_amount']) ? $_POST['spouse_benefit_amount'] : array();
        $spouse_in_patient_benefit_arr = isset($_POST['spouse_in_patient_benefit']) ? $_POST['spouse_in_patient_benefit'] : array();
        $spouse_out_patient_benefit_arr = isset($_POST['spouse_out_patient_benefit']) ? $_POST['spouse_out_patient_benefit'] : array();
        $spouse_monthly_income_arr = isset($_POST['spouse_monthly_income']) ? $_POST['spouse_monthly_income'] : array();
        $spouse_benefit_percentage_arr = isset($_POST['spouse_benefit_percentage']) ? $_POST['spouse_benefit_percentage'] : array();

        $child_product_arr = isset($_POST['child_product']) ? $_POST['child_product'] : array();
        $child_benefit_amount_arr = isset($_POST['child_benefit_amount']) ? $_POST['child_benefit_amount'] : array();
        $child_in_patient_benefit_arr = isset($_POST['child_in_patient_benefit']) ? $_POST['child_in_patient_benefit'] : array();
        $child_out_patient_benefit_arr = isset($_POST['child_out_patient_benefit']) ? $_POST['child_out_patient_benefit'] : array();
        $child_monthly_income_arr = isset($_POST['child_monthly_income']) ? $_POST['child_monthly_income'] : array();
        $child_benefit_percentage_arr = isset($_POST['child_benefit_percentage']) ? $_POST['child_benefit_percentage'] : array();
    
        /*--- Primary ----*/
        if(!empty($prd_primary_member_field)){
            $primarybenefitArrUpdate = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');
            foreach($prd_primary_member_field as $key => $row) {
                $prd_question_id= $row['id'];
                $is_required = $row['required'];
                $control_name = 'primary_'.$row['label'];
    
                $label = $row['display_label'];
                $type = $row['questionType'];
                $control_class = $row['control_class'];
                $questionType = $row['questionType'];
    
                if (in_array($row['label'], array('fname', 'lname', 'SSN', 'phone', 'address1', 'address2', 'city', 'state', 'zip', 'email', 'birthdate', 'gender'))) {
                    continue;
                }
    
                if(in_array($control_name,$primarybenefitArrUpdate)){
                    continue;
                }
    
                $control_value = isset($_POST[$control_name])?$_POST[$control_name]:"";
                ${$control_name} = $control_value;
    
                if($questionType=='Custom'){
                    $custom_control_name = str_replace($prd_question_id,"", $control_name);
                    $custom_control_value = isset($_POST[$custom_control_name][$prd_question_id])?$_POST[$custom_control_name][$prd_question_id]:"";
                    $tmpControlName = $custom_control_name;
                    $tmpControlValue = $custom_control_value;
                    ${$tmpControlName} = $custom_control_value;
                }else{
                    $tmpControlName = $control_name;
                    $tmpControlValue = $control_value;
                    ${$tmpControlName} = $control_value;
                }
                if($is_required=='Y') {
                    if(is_array(${$tmpControlName})){
                        if(empty($tmpControlValue)){
                            $validate->setError($control_name,$label.' is required');
                        }
                    }else{
                        if($row['label'] == "phone"){
                            $tmpControlValue = phoneReplaceMain($tmpControlValue);
                            $validate->digit(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue, 'min' => 10, 'max' => 10), array('required' => 'Phone is required', 'invalid' => 'Valid Phone is required'));
                        } else {
                            $validate->string(array('required' => true, 'field' => $control_name, 'value' => $tmpControlValue), array('required' => $label.' is required'));    
                        }                    
                    }
                }
    
                if($control_name == "primary_address1" && !empty($control_value) && $_POST['is_valid_address'] !='Y'){
                    $validate->setError("primary_address1","Valid Address is required");
                }
    
                if($control_class == "dob" && !empty($control_value)){
                    if (!$validate->getError($control_name)) {
                        list($mm, $dd, $yyyy) = explode('/', $control_value);
    
                        if (!checkdate($mm, $dd, $yyyy)) {
                            $validate->setError($control_name, 'Valid Date is required');
                        }
                    }
                }
    
                if($questionType=='Custom'){
                    $productNames = "";
                    if(!empty($product_ids)){
                        $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                        $resProduct = $pdo->selectOne($sqlProduct);
    
                        if(!empty($resProduct) && !empty($resProduct['productNames'])){
                            $productNames = $resProduct['productNames'];
                        }
                    }
                    $custom_control_name = str_replace($prd_question_id,"", $control_name);
                    $custom_control_value = isset($_POST[$custom_control_name][$prd_question_id])?$_POST[$custom_control_name][$prd_question_id]:"";
    
                    if(!empty($custom_control_value)){
                        if(is_array($custom_control_value)){
                            $tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
                        }else{
                            $tmpIncr = " AND answer = '".$custom_control_value."'";
                        }
    
                        $sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                        $resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
    
                        if(!empty($resAnswer)){
                            $validate->setError($control_name,"Answer is not eligible For <b>".$productNames."</b>");
                        }
                    }
    
                }
            }
        }
    
        /*--- Spouse ---*/
        if(!empty($spouse_ids)) {
            $spousebenefitArrUpdate = array('spouse_benefit_amount','spouse_in_patient_benefit','spouse_out_patient_benefit','spouse_monthly_income','spouse_benefit_percentage');
            foreach ($spouse_ids as $spouseKey => $spouse_id) {
    
                if(!empty($prd_spouse_field)){
                    foreach($prd_spouse_field as $field_key => $row) {
                        $prd_question_id = $row['id'];
                        $is_required = $row['required'];
                        $control_name = 'spouse_'.$row['label'];
                        $label = $row['display_label'];
                        $control_value = isset($_POST[$control_name][$spouseKey])?$_POST[$control_name][$spouseKey]:"";
                        ${$control_name} = $control_value;
                        $control_class = $row['control_class'];
                        $questionType = $row['questionType'];
    
                        if(in_array($control_name ,$spousebenefitArrUpdate)){
                            continue;
                        }
                        if (in_array($row['label'], array('fname', 'lname', 'SSN', 'address1', 'address2', 'city', 'state', 'zip', 'birthdate', 'gender'))) {
                            continue;
                        }
    
    
                        if($questionType=='Custom'){
                            $custom_control_name = str_replace($prd_question_id,"", $control_name);
                            $custom_control_value = isset($_POST[$custom_control_name][$spouseKey][$prd_question_id])?$_POST[$custom_control_name][$spouseKey][$prd_question_id]:"";
                            $tmpControlName = $custom_control_name;
                            $tmpControlValue = $custom_control_value;
                            ${$tmpControlName} = $custom_control_value;
                        }else{
                            $tmpControlName = $control_name;
                            $tmpControlValue = $control_value;
                            ${$tmpControlName} = $control_value;
                        }
    
                        if($is_required=='Y') {
                            if(is_array(${$tmpControlName})){
                                if(empty($tmpControlValue)){
                                    $validate->setError($control_name."_".$spouseKey,$label.' is required');
                                }
                            }else{
                                if($row['label'] == "phone"){
                                    $tmpControlValue = phoneReplaceMain($tmpControlValue);
                                    $validate->digit(array('required' => true, 'field' => $control_name."_".$spouseKey, 'value' => $tmpControlValue, 'min' => 10, 'max' => 10), array('required' => 'Phone is required', 'invalid' => 'Valid Phone is required'));
                                } else {
                                    $validate->string(array('required' => true, 'field' => $control_name."_".$spouseKey, 'value' => $tmpControlValue), array('required' => $label.' is required'));    
                                }
                            }
                        }
    
                        if($control_class == "dob" && !empty($control_value)){
                            if (!$validate->getError($control_name."_".$spouseKey)) {
                                list($mm, $dd, $yyyy) = explode('/', $control_value);
    
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError($control_name."_".$spouseKey, 'Valid Date is required');
                                }
                            }
                        }
    
                        if($control_name == "spouse_gender" && !empty($control_value)){
                            $tmpDependent[$spouseKey]['dependent_relation']=getRelation('spouse', $control_value);
                        }
                        if($control_name == 'spouse_birthdate' && !empty($control_value)){
                            if(strtotime($control_value) >= strtotime($today_date)){
                                $validate->setError($control_name."_".$spouseKey,"Please Enter Valid Birthdate");
                            }
                        }
                        if($control_name == 'spouse_email' && !empty($control_value)){
                            if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
                                $validate->setError($control_name."_".$spouseKey, "Valid Email is required");
                            }
                        }
                        if($row['label'] == "phone"){
                            $tmpControlValue = phoneReplaceMain($tmpControlValue);
                            if(!empty($tmpControlValue)) {
                                $validate->digit(array('required' => true, 'field' => $control_name."_".$spouseKey, 'value' => $tmpControlValue, 'min' => 10, 'max' => 10), array('required' => 'Phone is required', 'invalid' => 'Valid Phone is required'));
                            }
                        }
    
                        $tmpDependent[$spouseKey][$control_name]=$control_value;
    
                        if($questionType=='Custom'){
                            $productNames = "";
                            if(!empty($product_ids)){
                                $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                                $resProduct = $pdo->selectOne($sqlProduct);
    
                                if(!empty($resProduct) && !empty($resProduct['productNames'])){
                                    $productNames = $resProduct['productNames'];
                                }
                            }
                            $custom_control_name = str_replace($prd_question_id,"", $control_name);
                            $custom_control_value = isset($_POST[$custom_control_name][$spouseKey][$prd_question_id])?$_POST[$custom_control_name][$spouseKey][$prd_question_id]:"";
                            if(!empty($custom_control_value)){
                                if(is_array($custom_control_value)){
                                    $tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
                                }else{
                                    $tmpIncr = " AND answer = '".$custom_control_value."'";
                                }
    
                                $sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                                $resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
    
                                if(!empty($resAnswer)){
                                    $validate->setError($control_name."_".$spouseKey,"Answer is not eligible For <b>".$productNames."</b>");
                                }
                            }
    
    
                        }
    
                        if($control_name == "spouse_birthdate" && !empty($control_value) && $resProducts['is_spouse_age_restrictions']=='Y'){
                            $ageFrom=$resProducts['spouse_age_restrictions_from'];
                            $ageTo=$resProducts['spouse_age_restrictions_to'];
    
    
                            $dependentAge=calculateAge(date('Y-m-d',strtotime($control_value)));
    
                            if ($dependentAge < $ageFrom) {
                                $validate->setError('dependent_general', 'Spouse must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
                            } else if ($dependentAge > $ageTo) {
                                $validate->setError('dependent_general', 'Spouse must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
                            }
    
                        }
                    }
                }
            }
        }
    
        /*--- Child ---*/
        if(!empty($child_ids)) {
            $childbenefitArrUpdate = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage');
            foreach ($child_ids as $childKey => $child_id) {
                if(!empty($prd_child_field)){
                    foreach($prd_child_field as $field_key => $row) {
                        $prd_question_id = $row['id'];
                        $is_required = $row['required'];
                        $control_name = 'child_'.$row['label'];
                        $label = $row['display_label'];
                        $control_value = isset($_POST[$control_name][$childKey])?$_POST[$control_name][$childKey]:"";
                        ${$control_name} = $control_value;
                        $control_class = $row['control_class'];
                        $questionType = $row['questionType'];
    
                        if(in_array($control_name,$childbenefitArrUpdate)){
                            continue;
                        }
                        if (in_array($row['label'], array('fname', 'lname', 'SSN', 'address1', 'address2', 'city', 'state', 'zip', 'birthdate', 'gender'))) {
                            continue;
                        }
    
                        if($questionType=='Custom'){
                            $custom_control_name = str_replace($prd_question_id,"", $control_name);
                            $custom_control_value = isset($_POST[$custom_control_name][$childKey][$prd_question_id])?$_POST[$custom_control_name][$childKey][$prd_question_id]:"";
                            $tmpControlName = $custom_control_name;
                            $tmpControlValue = $custom_control_value;
                            ${$tmpControlName} = $custom_control_value;
                        }else{
                            $tmpControlName = $control_name;
                            $tmpControlValue = $control_value;
                            ${$tmpControlName} = $control_value;
                        }
    
                        if($is_required=='Y') {
                            if(is_array(${$tmpControlName})){
                                if(empty($custom_control_value)){
                                    $validate->setError($control_name."_".$childKey,$label.' is required');
                                }
                            }else{
                                if($row['label'] == "phone"){
                                    $tmpControlValue = phoneReplaceMain($tmpControlValue);
                                    $validate->digit(array('required' => true, 'field' => $control_name."_".$childKey, 'value' => $tmpControlValue, 'min' => 10, 'max' => 10), array('required' => 'Phone is required', 'invalid' => 'Valid Phone is required'));
                                } else {
                                    $validate->string(array('required' => true, 'field' => $control_name."_".$childKey, 'value' => $tmpControlValue), array('required' => $label.' is required'));    
                                }
                            }
                        }
    
                        if($control_class == "dob" && !empty($control_value)){
                            if (!$validate->getError($control_name."_".$childKey)) {
                                list($mm, $dd, $yyyy) = explode('/', $control_value);
    
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError($control_name."_".$childKey, 'Valid Date is required');
                                }
                            }
                        }
    
                        if($control_name == "child_gender" && !empty($control_value)){
                            $tmpDependent[$childKey]['dependent_relation']=getRelation('child', $control_value);
                        }
                        if($control_name == 'child_email' && !empty($control_value)){
                            if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
                                $validate->setError($control_name.'_'.$childKey, "Valid Email is required");
                            }
                        }
                        if($row['label'] == "phone"){
                            $tmpControlValue = phoneReplaceMain($tmpControlValue);
                            if(!empty($tmpControlValue)) {
                                $validate->digit(array('required' => true, 'field' => $control_name."_".$childKey, 'value' => $tmpControlValue, 'min' => 10, 'max' => 10), array('required' => 'Phone is required', 'invalid' => 'Valid Phone is required'));
                            }
                        }
                        if($control_name == 'child_birthdate' && !empty($control_value)){
                            if(strtotime($control_value) >= strtotime($today_date)){
                                $validate->setError($control_name.'_'.$childKey,"Please Enter Valid Birthdate");
                            }
                        }
    
                        $tmpDependent[$childKey][$control_name]=$control_value;
    
                        if($questionType=='Custom'){
                            $productNames = "";
                            if(!empty($product_ids)){
                                $sqlProduct = "SELECT GROUP_CONCAT(name) as productNames FROM prd_main where id in ($product_ids)";
                                $resProduct = $pdo->selectOne($sqlProduct);
    
                                if(!empty($resProduct) && !empty($resProduct['productNames'])){
                                    $productNames = $resProduct['productNames'];
                                }
                            }
    
                            $custom_control_name = str_replace($prd_question_id,"", $control_name);
                            $custom_control_value = isset($_POST[$custom_control_name][$childKey][$prd_question_id])?$_POST[$custom_control_name][$childKey][$prd_question_id]:"";
                            if(!empty($custom_control_value)){
                                if(is_array($custom_control_value)){
                                    $tmpIncr = " AND answer in ('".implode("','", $custom_control_value)."')";
                                }else{
                                    $tmpIncr = " AND answer = '".$custom_control_value."'";
                                }
    
                                $sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N' AND answer_eligible = 'N' $tmpIncr";
                                $resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
    
                                if(!empty($resAnswer)){
                                    $validate->setError($control_name."_".$childKey,"Answer is not eligible For For <b>".$productNames."</b>");
                                }
                            }
    
                        }
    
                        if($control_name == "child_birthdate" && !empty($control_value) && $resProducts['is_children_age_restrictions']=='Y'){
                            $ageFrom=$resProducts['children_age_restrictions_from'];
                            $ageTo=$resProducts['children_age_restrictions_to'];
    
    
                            $dependentAge=calculateAge(date('Y-m-d',strtotime($control_value)));
                            if ($dependentAge < $ageFrom) {
                                $validate->setError('dependent_general', 'Child must be '.$ageFrom.' years of age for product <b>'.$product_name.'</b>');
                            } else if ($dependentAge > $ageTo) {
                                $validate->setError('dependent_general', 'Child must be younger then '.$ageTo.' years of age for product <b>'.$product_name.'</b>');
                            }
    
                        }
                    }
                }
            }
        }
    
        /*--- Principal Beneficiary ---*/
        $principal_beneficiary_percentage = 0;
        $tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();
        if(!empty($tmpPrincipal)){
            foreach ($tmpPrincipal as $principalKey => $childArr) {
                if(!empty($principal_beneficiary_field)){
                    foreach($principal_beneficiary_field as $field_key => $row) {
                        $is_required = $row['required'];
                        $control_name = 'principal_'.$row['label'];
                        $label = $row['display_label'];
                        $control_value = isset($_POST[$control_name][$principalKey])?$_POST[$control_name][$principalKey]:"";
                        ${$control_name} = $control_value;
                        $control_class = $row['control_class'];
                        if($control_name == "principal_queBeneficiaryAllow3"){
                            continue;
                        }
                        if($is_required=='Y'){
                            if(is_array(${$control_name})){
                                if(empty($control_value)){
                                    $validate->setError($control_name."_".$principalKey,$label.' is required');
                                }
                            }else{
                                $validate->string(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => $control_value), array('required' => $label.' is required'));
                            }
                        }
    
                        if($control_class == "dob" && !empty($control_value)){
                            if (!$validate->getError($control_name."_".$principalKey)) {
                                list($mm, $dd, $yyyy) = explode('/', $control_value);
    
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError($control_name."_".$principalKey, 'Valid Date is required');
                                }
                            }
                        }
                        if($control_name == "principal_queBeneficiaryPercentage" && $control_value != ''){
                            $principal_beneficiary_percentage = $principal_beneficiary_percentage + $control_value;
                        }
                    }
                }
            }
            if($principal_beneficiary_percentage != 100){
                $validate->setError('principal_beneficiary_general', 'Sum of all Principal Beneficiary percentages must equal 100%');
            }
        }
    
        /*--- Contingent Beneficiary ---*/
        $contingent_beneficiary_percentage = 0;
        $tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();
        if(!empty($tmpContingent)){
            foreach ($tmpContingent as $contingentKey => $childArr) {
                if(!empty($contingent_beneficiary_field)){
                    foreach($contingent_beneficiary_field as $field_key => $row) {
    
                        $is_required = $row['required'];
                        $control_name = 'contingent_'.$row['label'];
                        $label = $row['display_label'];
                        $control_value = isset($_POST[$control_name][$contingentKey])?$_POST[$control_name][$contingentKey]:"";
                        ${$control_name} = $control_value;
                        $control_class = $row['control_class'];
                        if($control_name == "contingent_queBeneficiaryAllow3"){
                            continue;
                        }
                        if($is_required=="Y"){
                            if(is_array(${$control_name})){
                                if(empty($control_value)){
                                    $validate->setError($control_name."_".$contingentKey,$label.' is required');
                                }
                            }else{
                                $validate->string(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => $control_value), array('required' => $label.' is required'));
                            }
                        }
                        if($control_class == "dob" && !empty($control_value)){
                            if (!$validate->getError($control_name."_".$contingentKey)) {
                                list($mm, $dd, $yyyy) = explode('/', $control_value);
    
                                if (!checkdate($mm, $dd, $yyyy)) {
                                    $validate->setError($control_name."_".$contingentKey, 'Valid Date is required');
                                }
                            }
                        }
                        if($control_name == "contingent_queBeneficiaryPercentage" && $control_value != ''){
    
                            $contingent_beneficiary_percentage = $contingent_beneficiary_percentage + $control_value;
                        }
                    }
                }
            }
            if($contingent_beneficiary_percentage != 100) {
                $validate->setError("contingent_beneficiary_general", 'Sum of all Contingent Beneficiary percentages must equal 100%');
            }
        }
    
        if ($validate->isValid()) {
            $af_new_data = array();
            $af_old_data = array();
    
            /*--- Primary Data ----*/
                $customerSettingParams=array();
                if (!empty($primary_height)) {
                    $primary_height_array = explode(".", $primary_height);
                    $customerSettingParams['height_feet']=$primary_height_array[0];
                    $customerSettingParams['height_inch']=$primary_height_array[1];
                } else {
                    $customerSettingParams['height_feet']='';
                    $customerSettingParams['height_inch']='';
                }
    
                if (!empty($primary_weight)) {
                    $customerSettingParams['weight']=$primary_weight;
                } else {
                    $customerSettingParams['weight']='';
                }
                if (!empty($primary_smoking_status)) {
                    $customerSettingParams['smoke_use']=$primary_smoking_status;
                } else {
                    $customerSettingParams['smoke_use']='';
                }
                if (!empty($primary_tobacco_status)) {
                    $customerSettingParams['tobacco_use']=$primary_tobacco_status;
                } else {
                    $customerSettingParams['tobacco_use']='';
                }
                if (!empty($primary_benefit_level)) {
                    $customerSettingParams['benefit_level']=$primary_benefit_level;
                } else {
                    $customerSettingParams['benefit_level']='';
                }
                if (!empty($primary_employment_status)) {
                    $customerSettingParams['employmentStatus']=$primary_employment_status;
                } else {
                    $customerSettingParams['employmentStatus']='';
                }
                if (!empty($primary_salary)) {
                    $customerSettingParams['salary']=$primary_salary;
                } else {
                    $customerSettingParams['salary']=NULL;
                }
                if (!empty($primary_date_of_hire)) {
                    $customerSettingParams['hire_date']=date('Y-m-d',strtotime($primary_date_of_hire));
                } else {
                    $customerSettingParams['hire_date']=NULL;
                }
                if (!empty($primary_hours_per_week)) {
                    $customerSettingParams['hours_per_week']=$primary_hours_per_week;
                } else {
                    $customerSettingParams['hours_per_week']=NULL;
                }
                if (!empty($primary_pay_frequency)) {
                    $customerSettingParams['pay_frequency']=$primary_pay_frequency;
                } else {
                    $customerSettingParams['pay_frequency']='';
                }
                if (!empty($primary_us_citizen)) {
                    $customerSettingParams['us_citizen']=$primary_us_citizen;
                } else {
                    $customerSettingParams['us_citizen']='';
                }
                if (!empty($primary_no_of_children)) {
                    $customerSettingParams['no_of_children']=$primary_no_of_children;
                } else {
                    $customerSettingParams['no_of_children']='';
                }
                if (!empty($primary_has_spouse)) {
                    $customerSettingParams['has_spouse']=$primary_has_spouse;
                } else {
                    $customerSettingParams['has_spouse']='';
                }
                $upd_where = array(
                    'clause' => 'md5(customer_id)=:customer_id',
                    'params' => array(
                        ':customer_id' => $customer_id,
                    ),
                );
                if(!empty($customerSettingParams)) {
                    $af_old_data['Primary']['customer_settings'] = $pdo->update('customer_settings',$customerSettingParams,$upd_where,true);
                    $af_new_data['Primary']['customer_settings'] = $customerSettingParams;    
                }            
    
                $primary_queCustom = !empty($_POST['primary_queCustom']) ? $_POST['primary_queCustom'] : array();
                if(!empty($primary_queCustom)){
                    foreach ($primary_queCustom as $key => $value) {
                        $sqlQue= "SELECT * FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND customer_id =:customer_id AND enrollee_type='Primary'";
                        $resQue=$pdo->selectOne($sqlQue,array(":customer_id"=>$cust_row['id'],":question_id"=>$key));
    
                        if(is_array($value)){
                            $answer = implode(",", $value);
                        }else{
                            $answer = $value;
                        }
                        $queInsParams = array(
                            "enrollee_type"=>'primary',
                            "customer_id"=>$cust_row['id'],
                            "question_id"=>$key,
                            "answer"=>$answer,
                        );
                        if(!empty($resQue)){
                            $queInswhere = array(
                                "clause" => "id=:id",
                                "params" => array(
                                    ":id" => $resQue['id'],
                                ),
                            );
                            $pdo->update("customer_custom_questions", $queInsParams, $queInswhere,true);
    
                            if($resQue['answer'] != $answer) {
                                $question = getname('prd_enrollment_questions',$key,'display_label','id');
    
                                $af_old_data['Primary']['customer_custom_questions_'.$resQue['id']] = array($question => $resQue['answer']);
                                $af_new_data['Primary']['customer_custom_questions_'.$resQue['id']] = array($question => $answer);
                            }
                        } else {
                            $tmp_id = $pdo->insert("customer_custom_questions", $queInsParams);
    
                            $question = getname('prd_enrollment_questions',$key,'display_label','id');
                            $af_old_data['Primary']['customer_custom_questions_'.$tmp_id] = array($question => '');
                            $af_new_data['Primary']['customer_custom_questions_'.$tmp_id] = array($question => $answer);
                        }
                    }
                }
    
                if(!empty($primary_product_arr)) {

                    foreach($primary_product_arr as $product_id => $val){
                        $benefitAmountParams = array(
                            'customer_id' => $cust_row['id'],
                            'product_id' =>$product_id,
                            'type'=>'Primary',
                            'amount'=>$primary_benefit_amount[$product_id],
                            'in_patient_benefit'=>$primary_in_patient_benefit[$product_id],
                            'out_patient_benefit'=>$primary_out_patient_benefit[$product_id],
                            'monthly_income'=>$primary_monthly_income[$product_id],
                            // 'benefit_percentage'=>$primary_benefit_percentage[$product_id],
                        );
    
                        $sqlAmount="SELECT *,amount as benefit_amount FROM customer_benefit_amount where is_deleted='N' AND customer_id=:customer_id AND product_id=:product_id AND type='Primary'";
                        $resAmount=$pdo->selectOne($sqlAmount,array(":customer_id"=>$cust_row['id'],":product_id"=>$product_id));
                        $prd_name = getname('prd_main',$product_id,'name','id');
                        if(!empty($resAmount)){
                            $benefitAmountWhere = array("clause" => "id=:id", "params" => array(":id" => $resAmount['id']));
                            $updatedPrimaryField = $pdo->update("customer_benefit_amount", $benefitAmountParams,$benefitAmountWhere,true);
                                
                            if(!empty($updatedPrimaryField)){
                                foreach($updatedPrimaryField as $key => $value){
                                        $af_old_data['Primary']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                        $af_new_data['Primary']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $benefitAmountParams[$key]);
                                }
                            }
                        } else {
                            $tmp_id = $pdo->insert("customer_benefit_amount", $benefitAmountParams);
                            unset($benefitAmountParams['id']);
                            unset($benefitAmountParams['customer_id']);
                            unset($benefitAmountParams['customer_dependent_profile_id']);
                            unset($benefitAmountParams['product_id']);
                            unset($benefitAmountParams['type']);
                            if(!empty($benefitAmountParams)){
                                foreach($benefitAmountParams as $key => $value){
                                        $af_old_data['Primary']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => '');
                                        $af_new_data['Primary']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                }
                            }
                        }
                    }
                }
            /*---/Primary Data ----*/
    
            /*--- Spouse Data ----*/
                if(!empty($spouse_ids)) {
                    foreach ($spouse_ids as $spouseKey => $spouse_id) {
                        $dep_row = $pdo->selectOne("SELECT * FROM customer_dependent_profile WHERE id=:id",array(":id" => $spouse_id));
                        $relation = 'spouse';
                        $dep_data = array();
    
                        if (!empty($spouse_data[$spouseKey]["phone"])) {
                            $dep_data['phone']=phoneReplaceMain($spouse_data[$spouseKey]["phone"]);
                        } else {
                            $dep_data['phone']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["email"])) {
                            $dep_data['email']=$spouse_data[$spouseKey]["email"];
                        } else {
                            $dep_data['email']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["salary"])) {
                            $dep_data['salary']=$spouse_data[$spouseKey]["salary"];
                        } else {
                            $dep_data['salary']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["employment_status"])) {
                            $dep_data['employmentStatus']=$spouse_data[$spouseKey]["employment_status"];
                        } else {
                            $dep_data['employmentStatus']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["tobacco_status"])) {
                            $dep_data['tobacco_use']=$spouse_data[$spouseKey]["tobacco_status"];
                        } else {
                            $dep_data['tobacco_use']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["smoking_status"])) {
                            $dep_data['smoke_use']=$spouse_data[$spouseKey]["smoking_status"];
                        } else {
                            $dep_data['smoke_use']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["height"])) {
                            $dependent_height_array = explode(".", $spouse_data[$spouseKey]["height"]);
                            $dep_data['height_feet']=$dependent_height_array[0];
                            $dep_data['height_inches']=$dependent_height_array[1];
                        } else {
                            $dep_data['height_feet']='';
                            $dep_data['height_inches']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["weight"])) {
                            $dep_data['weight']=$spouse_data[$spouseKey]["weight"];
                        } else {
                            $dep_data['weight']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["benefit_level"])) {
                            $dep_data['benefit_level']=$spouse_data[$spouseKey]["benefit_level"];
                        } else {
                            $dep_data['benefit_level']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["hours_per_week"])) {
                            $dep_data['hours_per_week']=$spouse_data[$spouseKey]["hours_per_week"];
                        } else {
                            $dep_data['hours_per_week']=NULL;
                        }
    
                        if (!empty($spouse_data[$spouseKey]["pay_frequency"])) {
                            $dep_data['pay_frequency']=$spouse_data[$spouseKey]["pay_frequency"];
                        } else {
                            $dep_data['pay_frequency']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["us_citizen"])) {
                            $dep_data['us_citizen']=$spouse_data[$spouseKey]["us_citizen"];
                        } else {
                            $dep_data['us_citizen']='';
                        }
    
                        if (!empty($spouse_data[$spouseKey]["date_of_hire"])) {
                            $dep_data['hire_date']=date('Y-m-d', strtotime($spouse_data[$spouseKey]["date_of_hire"]));
                        } else {
                            $dep_data['hire_date']=NULL;
                        }
    
                        if(!empty($dep_data)) {
                            $dep_where = array(
                                'clause' => 'id=:id',
                                'params' => array(
                                    ':id' => $spouse_id,
                                ),
                            );
                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_dependent_profile_'.$spouse_id] = $pdo->update('customer_dependent_profile', $dep_data, $dep_where,true);
                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_dependent_profile_'.$spouse_id] = $dep_data;
                        }
    
                        $queCustom = !empty($_POST[$relation.'_queCustom'][$spouseKey]) ? $_POST[$relation.'_queCustom'][$spouseKey] : array();
                        if(!empty($queCustom)){
                            foreach ($queCustom as $key => $value) {
                                $sqlQue= "SELECT * FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND dependent_profile_id=:dependent_profile_id";
                                $resQue=$pdo->selectOne($sqlQue,array(":question_id"=>$key,":dependent_profile_id"=>$spouse_id));
    
                                if(is_array($value)){
                                    $answer = implode(",", $value);
                                } else {
                                    $answer = $value;
                                }
                                $queInsParams = array(
                                    "enrollee_type"=>'spouse',
                                    "customer_id"=>$cust_row['id'],
                                    "question_id"=>$key,
                                    "dependent_profile_id"=>$spouse_id,
                                    "answer"=>$answer,
                                );
                                if(!empty($resQue)){
                                    $queInswhere = array(
                                        "clause" => "id=:id",
                                        "params" => array(
                                            ":id" => $resQue['id'],
                                        ),
                                    );
                                    $pdo->update("customer_custom_questions", $queInsParams, $queInswhere);
    
                                    if($resQue['answer'] != $answer) {
                                
                                        $question = getname('prd_enrollment_questions',$key,'display_label','id');
    
                                        $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$resQue['id']] = array($question => $resQue['answer']);
                                        $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$resQue['id']] = array($question => $answer);
                                    }
                                } else {
                                    $tmp_id = $pdo->insert("customer_custom_questions", $queInsParams);
    
                                    $question = getname('prd_enrollment_questions',$key,'display_label','id');
                                    $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$tmp_id] = array($question => '');
                                    $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$tmp_id] = array($question => $answer);
                                }
                            }
                        }
    
                        if(!empty($spouse_product_arr[$spouseKey])) {
                            foreach ($spouse_product_arr[$spouseKey] as $product_id => $value) {
                                $benefitAmountParams = array(
                                    'customer_id' => $cust_row['id'],
                                    'customer_dependent_profile_id' => $spouse_id,
                                    'product_id' =>$product_id,
                                    'type'=>'Spouse',
                                    'amount'=>$spouse_benefit_amount_arr[$spouseKey][$product_id],
                                    'in_patient_benefit'=>$spouse_in_patient_benefit_arr[$spouseKey][$product_id],
                                    'out_patient_benefit'=>$spouse_out_patient_benefit_arr[$spouseKey][$product_id],
                                    'monthly_income'=>$spouse_monthly_income_arr[$spouseKey][$product_id],
                                    // 'benefit_percentage'=>$spouse_benefit_percentage_arr[$spouseKey][$product_id],
                                );
    
                                $sqlAmount="SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_dependent_profile_id=:customer_dependent_profile_id AND product_id=:product_id";
                                $resAmount = $pdo->selectOne($sqlAmount,array(":customer_dependent_profile_id"=>$spouse_id,":product_id"=>$product_id));
                                $prd_name = getname('prd_main',$product_id,'name','id');
    
                                if(!empty($resAmount)){
                                    $benefitAmountWhere = array("clause" => "id=:id", "params" => array(":id" => $resAmount['id']));
                                    $updatedSpouseField = $pdo->update("customer_benefit_amount", $benefitAmountParams,$benefitAmountWhere,true);
                                    
                                    if(!empty($updatedSpouseField)){
                                        foreach($updatedSpouseField as $key => $value){
                                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $benefitAmountParams[$key]);
                                        }
                                    }
                                } else {
                                    $tmp_id = $pdo->insert("customer_benefit_amount", $benefitAmountParams);
    
                                    unset($benefitAmountParams['id']);
                                    unset($benefitAmountParams['customer_id']);
                                    unset($benefitAmountParams['customer_dependent_profile_id']);
                                    unset($benefitAmountParams['type']);
                                    unset($benefitAmountParams['product_id']);
                                    if(!empty($benefitAmountParams)){
                                        foreach($benefitAmountParams as $key => $value){
                                            if($key=='amount'){
                                                $key = "benefit_amount";
                                            }
                                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => '');
                                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            /*---/Spouse Data ----*/
    
            /*--- Child Data ----*/
                if(!empty($child_ids)) {
                    foreach ($child_ids as $childKey => $child_id) {
                        $dep_row = $pdo->selectOne("SELECT * FROM customer_dependent_profile WHERE id=:id",array(":id" => $child_id));
    
                        $relation = 'child';
                        $dep_data = array();
    
                        if (!empty($child_data[$childKey]["phone"])) {
                            $dep_data['phone']=phoneReplaceMain($child_data[$childKey]["phone"]);
                        } else {
                            $dep_data['phone']='';
                        }
    
                        if (!empty($child_data[$childKey]["email"])) {
                            $dep_data['email']=$child_data[$childKey]["email"];
                        } else {
                            $dep_data['email']='';
                        }
    
                        if (!empty($child_data[$childKey]["salary"])) {
                            $dep_data['salary']=$child_data[$childKey]["salary"];
                        } else {
                            $dep_data['salary']='';
                        }
    
                        if (!empty($child_data[$childKey]["employment_status"])) {
                            $dep_data['employmentStatus']=$child_data[$childKey]["employment_status"];
                        } else {
                            $dep_data['employmentStatus']='';
                        }
    
                        if (!empty($child_data[$childKey]["tobacco_status"])) {
                            $dep_data['tobacco_use']=$child_data[$childKey]["tobacco_status"];
                        } else {
                            $dep_data['tobacco_use']='';
                        }
    
                        if (!empty($child_data[$childKey]["smoking_status"])) {
                            $dep_data['smoke_use']=$child_data[$childKey]["smoking_status"];
                        } else {
                            $dep_data['smoke_use']='';
                        }
    
                        if (!empty($child_data[$childKey]["height"])) {
                            $dependent_height_array = explode(".", $child_data[$childKey]["height"]);
                            $dep_data['height_feet']=$dependent_height_array[0];
                            $dep_data['height_inches']=$dependent_height_array[1];
                        } else {
                            $dep_data['height_feet']='';
                            $dep_data['height_inches']='';
                        }
    
                        if (!empty($child_data[$childKey]["weight"])) {
                            $dep_data['weight']=$child_data[$childKey]["weight"];
                        } else {
                            $dep_data['weight']='';
                        }
    
                        if (!empty($child_data[$childKey]["benefit_level"])) {
                            $dep_data['benefit_level']=$child_data[$childKey]["benefit_level"];
                        } else {
                            $dep_data['benefit_level']='';
                        }
    
                        if (!empty($child_data[$childKey]["hours_per_week"])) {
                            $dep_data['hours_per_week']=$child_data[$childKey]["hours_per_week"];
                        } else {
                            $dep_data['hours_per_week']=NULL;
                        }
    
                        if (!empty($child_data[$childKey]["pay_frequency"])) {
                            $dep_data['pay_frequency']=$child_data[$childKey]["pay_frequency"];
                        } else {
                            $dep_data['pay_frequency']='';
                        }
    
                        if (!empty($child_data[$childKey]["us_citizen"])) {
                            $dep_data['us_citizen']=$child_data[$childKey]["us_citizen"];
                        } else {
                            $dep_data['us_citizen']='';
                        }
    
                        if (!empty($child_data[$childKey]["date_of_hire"])) {
                            $dep_data['hire_date']=date('Y-m-d', strtotime($child_data[$childKey]["date_of_hire"]));
                        } else {
                            $dep_data['hire_date']=NULL;
                        }
    
                        if(!empty($dep_data)) {
    
                            $dep_where = array(
                                'clause' => 'id=:id',
                                'params' => array(
                                    ':id' => $child_id,
                                ),
                            );
                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_dependent_profile_'.$child_id] = $pdo->update('customer_dependent_profile', $dep_data, $dep_where,true);
                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_dependent_profile_'.$child_id] = $dep_data;
                        }
    
                        $queCustom = !empty($_POST[$relation.'_queCustom'][$childKey]) ? $_POST[$relation.'_queCustom'][$childKey] : array();
                        if(!empty($queCustom)){
                            foreach ($queCustom as $key => $value) {
                                $sqlQue= "SELECT * FROM customer_custom_questions WHERE is_deleted='N' AND question_id=:question_id AND dependent_profile_id=:dependent_profile_id";
                                $resQue=$pdo->selectOne($sqlQue,array(":question_id"=>$key,":dependent_profile_id"=>$child_id));
    
                                if(is_array($value)){
                                    $answer = implode(",", $value);
                                } else {
                                    $answer = $value;
                                }
                                $queInsParams = array(
                                    "enrollee_type"=>'child',
                                    "customer_id"=>$cust_row['id'],
                                    "question_id"=>$key,
                                    "dependent_profile_id"=>$child_id,
                                    "answer"=>$answer,
                                );
    
                                if(!empty($resQue)){
                                    $queInswhere = array(
                                        "clause" => "id=:id",
                                        "params" => array(
                                            ":id" => $resQue['id'],
                                        ),
                                    );
                                    $pdo->update("customer_custom_questions", $queInsParams, $queInswhere);
    
                                    if($resQue['answer'] != $answer) {
                                        $question = getname('prd_enrollment_questions',$key,'display_label','id');
                                        $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$resQue['id']] = array($question => $resQue['answer']);
                                        $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$resQue['id']] = array($question => $answer);
                                    }
                                } else {
                                    $tmp_id = $pdo->insert("customer_custom_questions", $queInsParams);
    
                                    $question = getname('prd_enrollment_questions',$key,'display_label','id');
                                    $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$tmp_id] = array($question => '');
                                    $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_custom_questions_'.$tmp_id] = array($question => $answer);
                                }
                            }
                        }
    
                        if(!empty($child_product_arr[$childKey])) {
                            foreach ($child_product_arr[$childKey] as $product_id => $value) {
                                $benefitAmountParams = array(
                                    'customer_id' => $cust_row['id'],
                                    'customer_dependent_profile_id' => $child_id,
                                    'product_id' =>$product_id,
                                    'type'=>'Child',
                                    'amount'=>$child_benefit_amount_arr[$childKey][$product_id],
                                    'in_patient_benefit'=>$child_in_patient_benefit_arr[$childKey][$product_id],
                                    'out_patient_benefit'=>$child_out_patient_benefit_arr[$childKey][$product_id],
                                    'monthly_income'=>$child_monthly_income_arr[$childKey][$product_id],
                                    // 'benefit_percentage'=>$child_benefit_percentage_arr[$childKey][$product_id],
                                    
                                );
    
                                $sqlAmount="SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_dependent_profile_id=:customer_dependent_profile_id AND product_id=:product_id";
                                $resAmount = $pdo->selectOne($sqlAmount,array(":customer_dependent_profile_id"=>$child_id,":product_id"=>$product_id));
                                $prd_name = getname('prd_main',$product_id,'name','id');
    
                                if(!empty($resAmount)){
                                    $benefitAmountWhere = array("clause" => "id=:id", "params" => array(":id" => $resAmount['id']));
                                    $updatedChildField = $pdo->update("customer_benefit_amount", $benefitAmountParams,$benefitAmountWhere,true);
                                    
                                    if(!empty($updatedChildField)){
                                        foreach($updatedChildField as $key => $value){
                                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$resAmount['id']] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $benefitAmountParams[$key]);
                                        }
                                    }
                                    
                                } else {
                                    $tmp_id = $pdo->insert("customer_benefit_amount", $benefitAmountParams);
                                    unset($benefitAmountParams['id']);
                                    unset($benefitAmountParams['type']);
                                    unset($benefitAmountParams['customer_id']);
                                    unset($benefitAmountParams['customer_dependent_profile_id']);
                                    unset($benefitAmountParams['product_id']);
                                    unset($benefitAmountParams['type']);
                                    if(!empty($benefitAmountParams)){
                                        foreach($benefitAmountParams as $key => $value){
                                            if($key=='amount'){
                                                $key = "benefit_amount";
                                            }
                                            $af_old_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => '');
                                            $af_new_data[$dep_row['fname'].' '.$dep_row['lname'].' ('.$dep_row['display_id'].')']['customer_'.$key.'_'.$tmp_id] = array($prd_name.' '.ucwords(str_replace('_',' ',$key)) => $value);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            /*---/child Data ----*/
    
            //********* Beneficiery Insert Code Start ********************
                $tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();
                if(!empty($tmpPrincipal)){
                    $saved_principal_ids = array();
                    foreach ($tmpPrincipal as $key => $value) {
                        $principal_beneficiary_id = !empty($_POST['principal_beneficiary_id'][$key]) ? $_POST['principal_beneficiary_id'][$key] : 0;
                        if(!empty($principal_beneficiary_id)) {
                            $sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
                            $resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$principal_beneficiary_id));
                        } else {
                            $resBeneficiery = array();
                        }
    
                        $name = !empty($_POST['principal_queBeneficiaryFullName'][$key]) ? $_POST['principal_queBeneficiaryFullName'][$key] : '';
                        $address = !empty($_POST['principal_queBeneficiaryAddress'][$key]) ? $_POST['principal_queBeneficiaryAddress'][$key] : '';
                        $cell_phone = !empty(phoneReplaceMain($_POST['principal_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($_POST['principal_queBeneficiaryPhone'][$key]) : '';
                        $email = !empty($_POST['principal_queBeneficiaryEmail'][$key]) ? $_POST['principal_queBeneficiaryEmail'][$key] : '';
                        $ssn = !empty($_POST['principal_queBeneficiarySSN'][$key]) ? $_POST['principal_queBeneficiarySSN'][$key] : '';
                        $relationship = !empty($_POST['principal_queBeneficiaryRelationship'][$key]) ? $_POST['principal_queBeneficiaryRelationship'][$key] : '';
                        $percentage = !empty($_POST['principal_queBeneficiaryPercentage'][$key]) ? $_POST['principal_queBeneficiaryPercentage'][$key] : '';
                        $insParams=array(
                            'beneficiary_type'=>'Principal',
                            'customer_id'=>$cust_row['id'],
                            'name'=>$name,
                            'address'=>$address,
                            'cell_phone'=>$cell_phone,
                            'email'=>$email,
                            'relationship'=>$relationship,
                            'percentage'=>$percentage,
                        );
                        if(!empty($ssn)){
                            $insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                            $insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
                        }
                        if(!empty($resBeneficiery)){
                            $updWhr = array(
                                'clause' => 'id = :id',
                                'params' => array(
                                    ':id' => $resBeneficiery['id'],
                                ),
                            );
                            $af_old_data['Principal Beneficiary']['customer_beneficiary_'.$resBeneficiery['id']] = $pdo->update("customer_beneficiary",$insParams,$updWhr,true);
                            $af_new_data['Principal Beneficiary']['customer_beneficiary_'.$resBeneficiery['id']] = $insParams;
    
                            $saved_principal_ids[] = $resBeneficiery['id'];
                        }else{
                            $tmp_id = $pdo->insert("customer_beneficiary",$insParams);
                            $saved_principal_ids[] = $tmp_id;
    
                            $af_old_data['Principal Beneficiary']['customer_beneficiary_'.$tmp_id] = "Principal Beneficiary Added";
                            $af_new_data['Principal Beneficiary']['customer_beneficiary_'.$tmp_id] = $insParams;
                        }
                    }
                    if(count($saved_principal_ids) > 0) {
                        $updWhr = array(
                            'clause' => 'customer_id=:customer_id AND id NOT IN('.implode(',',$saved_principal_ids).') AND beneficiary_type="Principal"',
                            'params' => array(
                                ':customer_id' => $cust_row['id'],
                            ),
                        );
                        $pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
                    }
                }
    
                $tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();
                if(!empty($tmpContingent)){
                    $saved_principal_ids = array();
                    foreach ($tmpContingent as $key => $value) {
                        $contingent_beneficiary_id = !empty($_POST['contingent_beneficiary_id'][$key]) ? $_POST['contingent_beneficiary_id'][$key] : 0;
                        if(!empty($contingent_beneficiary_id)) {
                            $sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
                            $resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$contingent_beneficiary_id));
                        } else {
                            $resBeneficiery = array();
                        }
    
                        $name = !empty($_POST['contingent_queBeneficiaryFullName'][$key]) ? $_POST['contingent_queBeneficiaryFullName'][$key] : '';
                        $address = !empty($_POST['contingent_queBeneficiaryAddress'][$key]) ? $_POST['contingent_queBeneficiaryAddress'][$key] : '';
                        $cell_phone = !empty(phoneReplaceMain($_POST['contingent_queBeneficiaryPhone'][$key])) ? phoneReplaceMain($_POST['contingent_queBeneficiaryPhone'][$key]) : '';
                        $email = !empty($_POST['contingent_queBeneficiaryEmail'][$key]) ? $_POST['contingent_queBeneficiaryEmail'][$key] : '';
                        $ssn = !empty($_POST['contingent_queBeneficiarySSN'][$key]) ? $_POST['contingent_queBeneficiarySSN'][$key] : '';
                        $relationship = !empty($_POST['contingent_queBeneficiaryRelationship'][$key]) ? $_POST['contingent_queBeneficiaryRelationship'][$key] : '';
                        $percentage = !empty($_POST['contingent_queBeneficiaryPercentage'][$key]) ? $_POST['contingent_queBeneficiaryPercentage'][$key] : '';
                        $insParams=array(
                            'beneficiary_type'=>'Contingent',
                            'customer_id'=>$cust_row['id'],
                            'name'=>$name,
                            'address'=>$address,
                            'cell_phone'=>$cell_phone,
                            'email'=>$email,
                            'relationship'=>$relationship,
                            'percentage'=>$percentage,
                        );
                        if(!empty($ssn)){
                            $insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
                            $insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
                        }
    
                        if(!empty($resBeneficiery)){
                            $updWhr = array(
                                'clause' => 'id = :id',
                                'params' => array(
                                    ':id' => $resBeneficiery['id'],
                                ),
                            );
                            $af_old_data['Contingent Beneficiary']['customer_beneficiary_'.$resBeneficiery['id']] = $pdo->update("customer_beneficiary",$insParams,$updWhr,true);
                            $af_new_data['Contingent Beneficiary']['customer_beneficiary_'.$resBeneficiery['id']] = $insParams;
    
                            $saved_principal_ids[] = $resBeneficiery['id'];
                        }else{
                            $tmp_id = $pdo->insert("customer_beneficiary",$insParams);
                            $saved_principal_ids[] = $tmp_id;
                            
                            $af_old_data['Contingent Beneficiary']['customer_beneficiary_'.$tmp_id] = "Contingent Beneficiary Added";
                            $af_new_data['Contingent Beneficiary']['customer_beneficiary_'.$tmp_id] = $insParams;
                        }
                    }
                    if(count($saved_principal_ids)) {
                        $updWhr = array(
                            'clause' => 'customer_id=:customer_id AND id NOT IN('.implode(',',$saved_principal_ids).') AND beneficiary_type="Contingent"',
                            'params' => array(
                                ':customer_id' => $cust_row['id'],
                            ),
                        );
                        $pdo->update('customer_beneficiary',array('is_deleted' => 'Y'),$updWhr);
                    }
                }
            //********* Beneficiery Insert Code End   ********************
    
            /*----- Activity Feed ------*/
            $flg = "true";
            $ac_desc = array();
            $ac_desc['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>'admin_profile.php?id='.md5($_SESSION['groups']['id']),
                  'title'=>$_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' =>'  updated health details '.$cust_row['fname'].' '.$cust_row['lname'].'(',
                'ac_red_2'=>array(
                  'href'=> 'members_details.php?id='.md5($cust_row['id']),
                  'title'=> $cust_row['rep_id'],
                ),
                'ac_message_2'=>') <br/> ',
            );
    
            foreach($af_old_data as $tmp_key => $tmp_value){
                $show_section_label = true;
                foreach($tmp_value as $key => $value){
                    if(!empty($value) && is_array($value)){
                        foreach($value as $key2 => $val){
                            if(!empty($af_new_data[$tmp_key][$key]) && array_key_exists($key2,$af_new_data[$tmp_key][$key])){
                                if(empty($val) && empty($af_new_data[$tmp_key][$key][$key2])) {
                                    continue;
                                }
    
                                $tmp_key2 = str_replace('_',' ',$key2);
                                $tmp_key2 = ucwords($tmp_key2);
    
                                if(in_array($val,array('Y','N'))){
                                    $val = $val == 'Y' ? "Yes" : "No";
                                }
    
                                if(in_array($af_new_data[$tmp_key][$key][$key2],array('Y','N'))){
                                    $af_new_data[$tmp_key][$key][$key2] = $af_new_data[$tmp_key][$key][$key2] == 'Y' ? "Yes" : "No";
                                }
    
                                if(empty($val)) {
                                    $tmp_label = " set ";
                                    if(in_array($af_new_data[$tmp_key][$key][$key2],array('Y','N'))){
                                        $tmp_label = " selected ";
                                    }
                                    if(in_array($af_new_data[$tmp_key][$key][$key2],array('Y','N'))){
                                        
                                    }
                                    if($show_section_label == true) {
                                        $ac_desc['key_value']['desc_arr'][] = '<strong>'.$tmp_key.' Information : </strong>';
                                        $show_section_label = false;
                                    }
                                    $ac_desc['key_value']['desc_arr'][] = $tmp_key2.' : '.$tmp_label.$af_new_data[$tmp_key][$key][$key2].".<br>";
                                
                                } elseif(empty($af_new_data[$tmp_key][$key][$key2])) {
                                    if($show_section_label == true) {
                                        $ac_desc['key_value']['desc_arr'][] = '<strong>'.$tmp_key.' Information : </strong>';
                                        $show_section_label = false;
                                    }
                                    $ac_desc['key_value']['desc_arr'][] = $tmp_key2.' : '.' removed <br>';
                                    
                                } else {
                                    if($show_section_label == true) {
                                        $ac_desc['key_value']['desc_arr'][] = '<strong>'.$tmp_key.' Information : </strong>';
                                        $show_section_label = false;
                                    }
                                    $tmp_label = " updated from ";
                                    if(in_array($val,array('Y','N'))){
                                        $tmp_label = " selected from ";
                                    }
                                    $ac_desc['key_value']['desc_arr'][] = $tmp_key2.' : '.$tmp_label.$val." to ".$af_new_data[$tmp_key][$key][$key2].".<br>";    
                                }                            
                                $flg = "false";
                            } else {
                                $ac_desc['description2'][] = ucwords(str_replace('_',' ',$val));
                                $flg = "false";
                            }
                        }    
                    } elseif($value == "Principal Beneficiary Added" || $value == "Contingent Beneficiary Added") {
    
                        $ac_desc['description'.$key][] = "<strong>".$value." : </strong>";
    
                        foreach ($af_new_data[$tmp_key][$key] as $key3 => $value3) {
                            if(in_array($key3,array('name','address','cell_phone','email','relationship','percentage','last_four_ssn'))) {
                                $tmp_key3 =str_replace('_',' ',$key3);
                                $tmp_key3 =ucfirst($tmp_key3);
                                if($key3 == "last_four_ssn") {
                                    $tmp_key3 = "SSN";
                                    $value3 = "*".$value3;
                                }
                                $ac_desc['description'.$key][$key3] = $tmp_key3." : ".$value3;
                                $flg = "false";    
                            }                        
                        }
                    } else {
                        if(is_array($value) && !empty($value)){
                            $ac_desc['description'.$key][] = implode('',$value);
                            $flg = "false";
                        } else if(!empty($value)) {
                            $ac_desc['description'.$key][] = $value;
                            $flg = "false";
                        }
                    }
                }
            }
    
            if($flg == "true"){
                $ac_desc['description_novalue'] = 'No updates in health details.';
            }
            activity_feed(3,$_SESSION['groups']['id'], 'Group' , $cust_row['id'], 'customer', 'Group Updated Member Health Details',$_SESSION['groups']['fname'].$_SESSION['groups']['lname'],"",json_encode($ac_desc));
            /*-----/Activity Feed ------*/
            $response['status'] = 'success';
        } else {
            $errors = $validate->getErrors();
            $response['status'] = 'fail';
            $response['errors'] = $errors;
        }
        echo json_encode($response);
        exit();
    }
    
    /*--- Primary Data ----*/
    $cust_sql = "SELECT c.fname,c.lname,c.email,c.cell_phone,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,c.address,c.address_2,c.city,c.state,c.zip,c.birth_date,c.gender,cs.salary,cs.employmentStatus,cs.tobacco_use,cs.smoke_use,cs.height_feet,cs.height_inch,cs.weight,cs.benefit_level,cs.hours_per_week,cs.pay_frequency,cs.us_citizen,cs.no_of_children,cs.has_spouse,cs.hire_date
          FROM customer c 
          LEFT JOIN customer_settings cs ON (c.id = cs.customer_id)
          WHERE md5(c.id)=:customer_id";
    $cust_row = $pdo->selectOne($cust_sql, array(":customer_id" => $customer_id));
    $primary_fname_value = $cust_row['fname'];
    $primary_lname_value = $cust_row['lname'];
    $primary_email_value = $cust_row['email'];
    $primary_phone_value = $cust_row['cell_phone'];
    $primary_SSN_value = $cust_row['ssn'];
    $primary_address_value = $cust_row['address'];
    $primary_address2_value = $cust_row['address_2'];
    $primary_city_value = $cust_row['city'];
    $primary_state_value = $cust_row['state'];
    $primary_zip_value = $cust_row['zip'];
    $primary_birthdate_value = date('m/d/Y', strtotime($cust_row['birth_date']));
    $primary_gender_value = $cust_row['gender'];
    $primary_salary_value = $cust_row['salary'];
    $primary_employment_status_value = $cust_row['employmentStatus'];
    $primary_tobacco_status_value = $cust_row['tobacco_use'];
    $primary_smoking_status_value = $cust_row['smoke_use'];
    $primary_height_feet_value = $cust_row['height_feet'];
    $primary_height_inch_value = $cust_row['height_inch'];
    $primary_height_value = $primary_height_feet_value . '.' . $primary_height_inch_value;
    $primary_height_value = '';
    if (!empty($primary_height_feet_value)) {
        $primary_height_value = $primary_height_feet_value;
    
        if (!empty($primary_height_inch_value)) {
            $primary_height_value .= '.' . $primary_height_inch_value;
        } else {
            $primary_height_value .= '.0';
        }
    }
    $primary_weight_value = !empty($cust_row['weight']) ? $cust_row['weight'] : '';
    $primary_benefit_level_value = $cust_row['benefit_level'];
    $primary_hours_per_week_value = $cust_row['hours_per_week'];
    $primary_pay_frequency_value = $cust_row['pay_frequency'];
    $primary_us_citizen_value = $cust_row['us_citizen'];
    $primary_no_of_children_value = $cust_row['no_of_children'];
    $primary_has_spouse_value = $cust_row['has_spouse'];
    $primary_date_of_hire_value = !empty($cust_row['hire_date']) ? date('m/d/Y', strtotime($cust_row['hire_date'])) : '';
    
    $primary_benefit_amount = array();
    $ba_res = $pdo->select("SELECT * FROM customer_benefit_amount WHERE type='Primary' AND md5(customer_id)=:customer_id AND is_deleted='N'",array(":customer_id"=>$customer_id));
    foreach ($ba_res as $key => $ba_row) {
        $primary_benefit_amount['benefit_amount'][$ba_row['product_id']] = $ba_row['amount'];
        $primary_benefit_amount['in_patient_benefit'][$ba_row['product_id']] = $ba_row['in_patient_benefit'];
        $primary_benefit_amount['out_patient_benefit'][$ba_row['product_id']] = $ba_row['out_patient_benefit'];
        $primary_benefit_amount['monthly_income'][$ba_row['product_id']] = $ba_row['monthly_income'];
        $primary_benefit_amount['benefit_percentage'][$ba_row['product_id']] = $ba_row['benefit_percentage'];
    }
    
    $custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                      FROM customer_custom_questions ccq 
                      JOIN prd_enrollment_questions q ON(q.id = ccq.question_id AND q.is_deleted='N')
                      WHERE md5(ccq.customer_id)=:customer_id AND ccq.enrollee_type='primary' AND ccq.is_deleted='N'";
    $custom_que_res = $pdo->select($custom_que_sql, array(":customer_id" => $customer_id));
    if (!empty($custom_que_res)) {
        foreach ($custom_que_res as $custom_que_row) {
            ${"primary_" . $custom_que_row['label'] . "_value"} = $custom_que_row['answer'];
        }
    }
    /*---/Primary Data ----*/
    
    /*--- Spouse Dependent ----*/
    $spouse_sql = "SELECT cp.*,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,'Spouse' as type  
                  FROM  customer_dependent_profile cp 
                  WHERE cp.is_deleted='N' AND
                  LOWER(cp.relation) IN('husband','wife') AND 
                  md5(cp.customer_id)=:customer_id
                  ORDER BY cp.fname,cp.lname";
    $spouse_res = $pdo->select($spouse_sql, array(":customer_id" => $customer_id));
    if(!empty($spouse_res)) {
        foreach ($spouse_res as $key => $spouse_row) {
            $ba_res = $pdo->select("SELECT * FROM customer_benefit_amount WHERE customer_dependent_profile_id=:customer_dependent_profile_id AND is_deleted='N'",array(":customer_dependent_profile_id"=>$spouse_row['id']));
            foreach ($ba_res as $ba_row) {
                $spouse_res[$key]['benefit_amount'][$ba_row['product_id']] = $ba_row['amount'];
                $spouse_res[$key]['in_patient_benefit'][$ba_row['product_id']] = $ba_row['in_patient_benefit'];
                $spouse_res[$key]['out_patient_benefit'][$ba_row['product_id']] = $ba_row['out_patient_benefit'];
                $spouse_res[$key]['monthly_income'][$ba_row['product_id']] = $ba_row['monthly_income'];
                // $spouse_res[$key]['benefit_percentage'][$ba_row['product_id']] = $ba_row['benefit_percentage'];
            }
    
            $custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                          FROM customer_custom_questions ccq 
                          JOIN prd_enrollment_questions q ON(q.id = ccq.question_id AND q.is_deleted='N')
                          LEFT JOIN customer_dependent cd ON(cd.id = ccq.dependent_id)
                          WHERE ccq.is_deleted='N' AND (cd.cd_profile_id=:dependent_profile_id OR ccq.dependent_profile_id=:dependent_profile_id)
                          GROUP BY q.label";
            $custom_que_res = $pdo->select($custom_que_sql,array(":dependent_profile_id"=>$spouse_row["id"]));
            if(!empty($custom_que_res)) {
                foreach ($custom_que_res as $custom_que_row) {
                    $spouse_res[$key][$custom_que_row['label']] = $custom_que_row['answer'];                
                }
            }
        }
    }
    /*---/Spouse Dependent ----*/
    
    /*--- Child Dependent ----*/
    $child_sql = "SELECT cp.*,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,'Child' as type 
                  FROM  customer_dependent_profile cp 
                  WHERE cp.is_deleted='N' AND
                  LOWER(cp.relation) IN('son','daughter') AND 
                  md5(cp.customer_id)=:customer_id
                  ORDER BY cp.fname,cp.lname";
    $child_res = $pdo->select($child_sql, array(":customer_id" => $customer_id));
    if(!empty($child_res)) {
        foreach ($child_res as $key => $child_row) {
            $benefit_amount = array();
            $ba_res = $pdo->select("SELECT * FROM customer_benefit_amount WHERE customer_dependent_profile_id=:customer_dependent_profile_id AND is_deleted='N'",array(":customer_dependent_profile_id"=>$child_row['id']));
            foreach ($ba_res as $ba_row) {
                $child_res[$key]['benefit_amount'][$ba_row['product_id']] = $ba_row['amount'];
                $child_res[$key]['in_patient_benefit'][$ba_row['product_id']] = $ba_row['in_patient_benefit'];
                $child_res[$key]['out_patient_benefit'][$ba_row['product_id']] = $ba_row['out_patient_benefit'];
                $child_res[$key]['monthly_income'][$ba_row['product_id']] = $ba_row['monthly_income'];
                // $child_res[$key]['benefit_percentage'][$ba_row['product_id']] = $ba_row['benefit_percentage'];
            }
    
            $custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                          FROM customer_custom_questions ccq 
                          JOIN prd_enrollment_questions q ON(q.id = ccq.question_id AND q.is_deleted='N')
                          LEFT JOIN customer_dependent cd ON(cd.id = ccq.dependent_id)
                          WHERE ccq.is_deleted='N' AND (cd.cd_profile_id=:dependent_profile_id OR ccq.dependent_profile_id=:dependent_profile_id)
                          GROUP BY q.label";
            $custom_que_res = $pdo->select($custom_que_sql,array(":dependent_profile_id"=>$child_row["id"]));
            if(!empty($custom_que_res)) {
                foreach ($custom_que_res as $custom_que_row) {
                    $child_res[$key][$custom_que_row['label']] = $custom_que_row['answer'];                
                }
            }
        }
    }
    /*---/Child Dependent ----*/
    
    $dep_res = array();
    if (!empty($spouse_res)) {
        $dep_res = $spouse_res;
    }
    if (!empty($child_res)) {
        $dep_res = array_merge($dep_res, $child_res);
    }
    
    /*---- Beneficiary Information ---*/
    $principal_ben_res = array();
    $contingent_ben_res = array();
    $beneficiery_sql = "SELECT *,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn FROM customer_beneficiary WHERE is_deleted='N' AND md5(customer_id)=:customer_id";
    $beneficiery_res = $pdo->select($beneficiery_sql, array(":customer_id" => $customer_id));
    if (!empty($beneficiery_res)) {
        foreach ($beneficiery_res as $key => $beneficiery_row) {
            if ($beneficiery_row['beneficiary_type'] == "Principal") {
                $principal_ben_res[] = $beneficiery_row;
            } else {
                $contingent_ben_res[] = $beneficiery_row;
            }
        }
    }
    /*----/Beneficiary Information ---*/
    
    /*--- Policy Benefit Amount ----*/
    $prd_benefit_amount = array();
    if(!empty($product_ids_arr)) {
    $benefitArr = array('benefit_amount','in_patient_benefit','out_patient_benefit','monthly_income'/*,'benefit_percentage'*/);
        foreach ($product_ids_arr as $key => $product_id) {
            $assignedQuestionValue = $MemberEnrollment->assignedQuestionValue($product_id);
            if(!empty($assignedQuestionValue)) {
                foreach ($assignedQuestionValue as $enrollee_type => $value) {
                    if(!empty($value)){
                        foreach($value as $vkey => $vval){
                            if(in_array($vkey,$benefitArr)) {
                                foreach ($vval as $key1 => $value2) {
                                    if(empty($prd_benefit_amount[$enrollee_type][$product_id][$vkey]) || !in_array($value2,$prd_benefit_amount[$enrollee_type][$product_id][$vkey])) {
                                        $prd_benefit_amount[$enrollee_type][$product_id][$vkey][] = $value2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //pre_print($prd_benefit_amount);
    }
    /*---/Policy Benefit Amount ----*/
include_once 'tmpl/member_health_tab.inc.php';
exit;
}
include_once 'tmpl/member_health_tab.inc.php';
?>

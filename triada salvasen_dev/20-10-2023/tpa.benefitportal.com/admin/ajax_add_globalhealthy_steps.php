<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$name = checkIsset($_POST['step_name']); 
$display_id = checkIsset($_POST['display_id']);
$products = checkIsset($_POST['products']);
$effective_date = !empty($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : '';
$termination_date = !empty($_POST['termination_date']) ? date('Y-m-d',strtotime($_POST['termination_date'])) : NULL;
$fee_price = checkIsset($_POST['step_fee']);
$is_fee_commissionable = isset($_POST['is_commissionable']) ? $_POST['is_commissionable'] : "";
$non_commissionable_amount = isset($_POST['non_commission_price']) ? $_POST['non_commission_price'] : "";
$commissionable_amount = isset($_POST['commission_price']) ? $_POST['commission_price'] : "";
$is_member_benefits = isset($_POST['is_member_benefits']) ? $_POST['is_member_benefits'] : "";
$is_member_portal = isset($_POST['is_member_portal']) ? "Y" : "N";
$description = isset($_POST['description']) ? $_POST['description'] : "";
$benifit_period = isset($_POST['benifit_period']) ? $_POST['benifit_period'] : "";
$month = isset($_POST['select_month']) ? $_POST['select_month'] : "";
$product_id = checkIsset($_POST['product_id']);
$fees_id = checkIsset($_POST['health_id']);

$is_clone = checkIsset($_POST['is_clone']) == 'Y' ? 'Y' : 'N';
$states = isset($_POST['states']) ? $_POST['states'] : array();

$validate->string(array('required' => true, 'field' => 'name', 'value' => $name), array('required' => 'Name is required'));

if(!empty($products)){
    $variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $products)."') OR parent_product_id in('".implode("','", $products)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
    if($variations){
        $products = explode(',', $variations['product_ids']);
    }
} 

// if (!$validate->getError('name')) {
//   $incr="";
//   $sch_params=array();
//   $sch_params[':name']=$name;
//   if(!empty($product_id) && $is_clone == 'N'){
//     $incr.=" AND id != :id";
//     $sch_params[':id'] = $product_id;
//     }
//   $selectCarrier = "SELECT id FROM prd_main WHERE product_type='Healthy Step' AND record_type='Primary' AND name=:name $incr  AND is_deleted='N' ";
//   $resultCarrier = $pdo->selectOne($selectCarrier, $sch_params);
//   if (!empty($resultCarrier['id'])) {
//     $validate->setError("name", "This Name is already associated with another Health step.");
//   }
// }
$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Healthy Step ID is required'));

if(empty($states)){
    $validate->setError("states","Please Select State");
}

if (!$validate->getError('display_id')) {
  $incr="";
  $sch_params=array();
  $sch_params[':display_id']=$display_id;
  $incr='';
if(!empty($product_id) && $is_clone == 'N'){
    $incr.=" AND id != :id";
    $sch_params[':id'] = $product_id;
}
  $selectCarrier = "SELECT id FROM prd_main WHERE product_type='Healthy Step' AND record_type='Primary' AND product_code=:display_id $incr AND is_deleted='N' ";
  $resultCarrier1 = $pdo->selectOne($selectCarrier, $sch_params);
    if (!empty($resultCarrier1['id'])) {
    $validate->setError("display_id", "This Healthy step ID is already associated with another Healthy step id.");
    }
}

if(empty($products)){
    $validate->setError("products","Select Product");
}
if(!empty($products)){
    $validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));
    if (!empty($termination_date) && !empty($effective_date)) {
        if(strtotime(date($termination_date)) < strtotime(date($effective_date))){ 
            $validate->setError("termination_date", "Termination date must be greater then or equal to effective date.");
        }
    }
    $validate->string(array('required' => true, 'field' => 'is_commissionable', 'value' => $is_fee_commissionable), array('required' => 'Commissionable Fee is required'));
    $validate->string(array('required' => true, 'field' => 'is_member_benefits', 'value' => $is_member_benefits), array('required' => 'Member Benefits is required'));
    if($is_member_benefits =='Y'){
        $validate->string(array('required' => true, 'field' => 'benifit_period', 'value' => $benifit_period), array('required' => 'Benefits Period is required'));
        // $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));
    }
}

if($benifit_period == 'Renewals'){
    $validate->string(array('required' => true, 'field' => 'select_month', 'value' => $month), array('required' => 'Please select month.'));
}

if ($validate->isValid()) {

  $insert_params = array(
    'name' => $name,
    'setting_type' => 'Healthy Step',
    'status' => 'Active',
  );


  $insert_params_key =  implode(",", array_keys($insert_params));
  $feesSql = "SELECT id,$insert_params_key FROM prd_fees WHERE id=:c_id AND is_deleted='N'";
  $feesRow = $pdo->selectOne($feesSql, array(":c_id" => $fees_id));

  $health_id = '';
  if(!empty($feesRow) && $is_clone=='N'){
    $health_id = $feesRow['id'];
    $update_where = array(
      'clause' => 'id = :id',
      'params' => array(':id' => $health_id)
    );
    $update_status = $pdo->update('prd_fees', $insert_params, $update_where);
  }else{     
        include_once __DIR__ . '/../includes/function.class.php';
        $functionsList = new functionsList();
        $display_id_fees=$functionsList->generateHealthyStepDisplayIDFees();

      $insert_params['display_id'] = $display_id_fees;

    $health_id = $pdo->insert("prd_fees", $insert_params);
  }

    unset($insert_params['setting_type']);
    unset($insert_params['display_id']);
  
    $insert_params['product_code'] = $display_id;
    $insert_params['is_fee_on_commissionable'] = $is_fee_commissionable;
    $insert_params['is_member_benefits'] = $is_member_benefits;

    $insert_params['payment_type'] = 'Single';

    if($is_member_benefits == 'Y'){
        $insert_params['payment_type'] = 'Recurring';
        $insert_params['fee_renewal_type'] = $benifit_period;
        $insert_params['is_fee_on_renewal'] = 'Y';
        if($benifit_period == 'Renewals'){
            $insert_params['fee_renewal_count'] = $month;
        }
    }


    $insert_params_key =  implode(",", array_keys($insert_params));
    $productSql = "SELECT id,$insert_params_key FROM prd_main WHERE id=:product_id AND is_deleted='N'";
    $productRow = $pdo->selectOne($productSql, array(":product_id" => $product_id));
    
    if(!empty($productRow) && $is_clone=='N'){
        $product_id = $productRow['id'];
        $update_where = array(
          'clause' => 'id = :id',
          'params' => array(':id' => $product_id)
        );
        $pdo->update('prd_main', $insert_params, $update_where);
        //for variation healthy step update start.
            $product_ids = $pdo->selectOne("SELECT GROUP_CONCAT(id) as variation_ids from prd_main where parent_product_id=:product_id and product_type='Healthy Step' and record_type='Variation' and is_deleted='N'",array(":product_id"=>$product_id));
            if(!empty($product_ids['variation_ids'])){
                $update_variation =  array(
                    'clause' => "id IN(".$product_ids['variation_ids'].")",
                    'params' => array()
                );

                unset($insert_params['product_code']);
                $rec = $pdo->update('prd_main', $insert_params, $update_variation,true);
                $variation_products = explode(',',$product_ids['variation_ids']);
                foreach($variation_products as $var_id){
                    $prd_variation_fee = $pdo->selectOne("SELECT prd_fee_id from prd_assign_fees where fee_id=:product_id and is_deleted='N'",array(":product_id"=>$var_id));
                    $variation_fee_id = $prd_variation_fee['prd_fee_id'];

                    $prd_assign_fees_variation = array();
                    $prd_assign_variation = $pdo->select("SELECT id,product_id from prd_assign_fees where fee_id=:product_id and is_deleted='N'",array(":product_id"=>$var_id));
                    if(!empty($prd_assign_variation)){
                        foreach($prd_assign_variation as $fees){
                            $prd_assign_fees_variation[$fees['id']] = $fees['product_id'];
                        }
                    }
    
                    $delete_product_var = array_diff($prd_assign_fees_variation,$products);
                    $insert_product_var = array_diff($products,$prd_assign_fees_variation);
                    if(!empty($delete_product_var)){
                        $upd_param_var = array(
                            'is_deleted'=>'Y',
                            'updated_at' => 'msqlfunc_NOW()'
                        );
                    
                        foreach($delete_product_var as $ky => $tp){
                            $upd_where_var =array(
                                "clause"=>" id = :id and product_id = :prd_id",
                                "params" => array(":id"=>$ky,":prd_id"=>$tp)
                            );
                            $pdo->update("prd_assign_fees",$upd_param_var,$upd_where_var);
                        }
                    }
    
                    if(!empty($insert_product_var)){
                        $ins_param_fees_var = array(
                            'fee_id' =>$var_id,
                            'prd_fee_id' =>$variation_fee_id,
                        );
                        foreach ($insert_product_var as $key => $value) {
                            $ins_param_fees_var["product_id"] = $value;
                            $pdo->insert("prd_assign_fees",$ins_param_fees_var);
                        }
                    }

                    $prd_matrix_upd_param_var = array(
                        'price' => $fee_price,
                        'commission_amount' => $commissionable_amount,
                        'non_commission_amount' => $non_commissionable_amount,
                        'pricing_effective_date' => date('Y-m-d',strtotime($effective_date)) ,
                        'pricing_termination_date' => !empty($termination_date) ? date('Y-m-d',strtotime($termination_date)) : NULL,
                    );
            
                    $prd_matrix_where_var = array(
                        "clause" => "product_id=:id",
                        'params' => array(":id"=>$var_id)
                    );
                    $pdo->update('prd_matrix',$prd_matrix_upd_param_var,$prd_matrix_where_var);
            
                    if($is_member_benefits == 'Y'){
                        $chkDesc = $pdo->selectOne("SELECT id from prd_member_portal_information where product_id=:product_id and is_deleted='N'",array(":product_id"=>$var_id));
                        if(!empty($chkDesc['id'])){
                            $upd_desc_var = array(
                                'description' => $description,
                                'is_member_portal' => $is_member_portal,
                            );
                            $pdo->update('prd_member_portal_information',$upd_desc_var,$prd_matrix_where_var);    
                        }else{
                            $ins_desc = array(
                                'product_id' =>  $var_id,
                                'description' => $description,
                                'is_member_portal' => $is_member_portal,
                            );
                            $pdo->insert('prd_member_portal_information',$ins_desc);
                        }
                    }
                }
            }
        //for variation healthy step update end.
        
        $prd_assign_fees = array();
        $prd_assign = $pdo->select("SELECT id,product_id from prd_assign_fees where fee_id=:product_id and prd_fee_id=:fee_id and is_deleted='N'",array(":product_id"=>$product_id,":fee_id"=>$health_id));
        if(!empty($prd_assign)){
            foreach($prd_assign as $fees){
                $prd_assign_fees[$fees['id']] = $fees['product_id'];
            }
        }

        $delete_product = array_filter(array_diff($prd_assign_fees,$products));
        $insert_product = array_filter(array_diff($products,$prd_assign_fees));
        if(!empty($delete_product)){
            $upd_param = array(
                'is_deleted'=>'Y',
                'updated_at' => 'msqlfunc_NOW()'
            );
        
            foreach($delete_product as $ky => $tp){
                $upd_where =array(
                    "clause"=>" id = :id and product_id = :prd_id",
                    "params" => array(":id"=>$ky,":prd_id"=>$tp)
                );
                $pdo->update("prd_assign_fees",$upd_param,$upd_where);
            }
        }

        if(!empty($insert_product)){
            $ins_param_fees = array(
                'fee_id' =>$product_id,
                'prd_fee_id' =>$health_id,
            );
            foreach ($insert_product as $key => $value) {
                $ins_param_fees["product_id"] = $value;
                $pdo->insert("prd_assign_fees",$ins_param_fees);
            }
        }
        
        //************* Activity Code Start *************
        $oldVaArray = $productRow;
        $NewVaArray = $insert_params;
        unset($oldVaArray['id']);

        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

        $prd_matrix_upd_param = array(
            'price' => $fee_price,
            'commission_amount' => $commissionable_amount,
            'non_commission_amount' => $non_commissionable_amount,
            'pricing_effective_date' => date('Y-m-d',strtotime($effective_date)) ,
            'pricing_termination_date' => !empty($termination_date) ? date('Y-m-d',strtotime($termination_date)) : NULL,
        );

        $prd_matrix_where = array(
            "clause" => "product_id=:id",
            'params' => array(":id"=>$product_id)
        );
        $checkDiffmatrix = $pdo->update('prd_matrix',$prd_matrix_upd_param,$prd_matrix_where,true);
        $checkDiffDesc = array();

        if($is_member_benefits == 'Y'){
            $chk_desc = $pdo->selectOne("SELECT id from prd_member_portal_information where product_id=:product_id and is_deleted='N'",array(":product_id"=>$product_id));
            if(!empty($chk_desc['id'])){
                $upd_desc = array(
                    'description' => $description,
                    'is_member_portal' => $is_member_portal,
                );
                $checkDiffDesc = $pdo->update('prd_member_portal_information',$upd_desc,$prd_matrix_where);    
            }else{
                $ins_desc = array(
                    'product_id' =>  $product_id,
                    'description' => $description,
                    'is_member_portal' => $is_member_portal,
                );
                $checkDiffDescins = $pdo->insert('prd_member_portal_information',$ins_desc);
            }
            
        }
        if(!empty($checkDiff) || !empty($checkDiffmatrix) || !empty($checkDiffDescins) || !empty($checkDiffDesc) || !empty($delete_product) || !empty($insert_product)){
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id']
                ),
                'ac_message_1' =>' Updated Healthy Steps ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/add_globalhealthy_steps.php?product_id='.md5($product_id).'&health_id='.md5($health_id),
                    'title'=>$display_id,
                ),
            ); 
            
            if(!empty($checkDiff)){
                foreach ($checkDiff as $key1 => $value1) {
                    if(in_array($value1,array('Y','N')) ){
                        if($oldVaArray[$key1] == 'Y'){
                            $oldVaArray[$key1] = 'selected';
                        }else if($oldVaArray[$key1] == 'N'){
                            $oldVaArray[$key1] = 'unselected';
                        }
                        if($NewVaArray[$key1] == 'Y'){
                            $NewVaArray[$key1] = 'selected';
                        }else if($NewVaArray[$key1] == 'N'){
                            $NewVaArray[$key1] = 'unselected';
                        }
                    }
                    $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
                } 
            }

            if(!empty($delete_product)){
                $prds = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as products from prd_main where id IN(".implode(',',$delete_product).")");
                $activityFeedDesc['description_arr_deleted'] = 'Deleted Products : '.$prds['products'];
            }

            if(!empty($insert_product)){
                $prds = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as products from prd_main where id IN(".implode(',',$insert_product).")");
                $activityFeedDesc['description_arr_insertd'] = 'Inserted Products : '.$prds['products'];
            }

            if(!empty($checkDiffmatrix)){
                foreach ($checkDiffmatrix as $key1 => $value1) {
                    $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$value1.' To '.$prd_matrix_upd_param[$key1];
                } 
            }

            if(!empty($checkDiffDesc)){
                foreach ($checkDiffDesc as $key1 => $value1) {
                    if(in_array($value1,array('Y','N')) ){
                        if($value1 == 'Y'){
                            $value1 = 'selected';
                        }else if($value1 == 'N'){
                            $value1 = 'unselected';
                        }
                        if($upd_desc[$key1] == 'Y'){
                            $upd_desc[$key1] = 'selected';
                        }else if($upd_desc[$key1] == 'N'){
                            $upd_desc[$key1] = 'unselected';
                        }
                    }
                    $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$value1.' To '.$upd_desc[$key1];
                } 
            }
            
            if(!empty($checkDiffDescins)){
                $activityFeedDesc['key_value']['desc_arr']['description'] = 'Description updated!';
            }
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $health_id, 'Healthy Step','Admin Updated Healthy Steps', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
        }
        //************* Activity Code End   *************

        // $response['message']="Healthy Steps Updated successfully";
        setNotifySuccess("Healthy Steps Updated successfully",true);
    
        $response['message']= 'Fee Updated successfully';
    } else {
        $insert_params['status']='Active';
        $insert_params['type'] = 'Fees';
        $insert_params['fee_type'] = 'Charged';
        $insert_params['product_type'] = 'Healthy Step';
        $insert_params['term_back_to_effective'] = 'Y';
        $product_id = $pdo->insert("prd_main", $insert_params);
        // array_push($carrier_fee_id, $product_id);

        $ins_param_fees = array(
            'fee_id' =>$product_id,
            'prd_fee_id' =>$health_id,
        );

        foreach($products as $prd){
            $ins_param_fees["product_id"] = $prd;
            $pdo->insert("prd_assign_fees",$ins_param_fees);
        }

        $prd_matrix_ins_param = array(
            'product_id' => $product_id,
            'price' => $fee_price,
            'commission_amount' => $commissionable_amount,
            'non_commission_amount' => $non_commissionable_amount,
            'pricing_effective_date' => date('Y-m-d',strtotime($effective_date)) ,
            'pricing_termination_date' => !empty($termination_date) ? date('Y-m-d',strtotime($termination_date)) : NULL,
        );

        $pdo->insert('prd_matrix',$prd_matrix_ins_param);

        if($is_member_benefits == 'Y'){
            $ins_desc = array(
                'product_id' =>  $product_id,
                'description' => $description,
                'is_member_portal' => $is_member_portal,
            );
            $pdo->insert('prd_member_portal_information',$ins_desc);    
        }

        //************* Activity Code Start *************
        $acdescription['ac_message'] =array(
            'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' Healthy Steps ',
            'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/add_globalhealthy_steps.php?product_id='.md5($product_id).'&health_id='.md5($health_id),
            'title'=>$display_id,
            ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $health_id, 'Healthy Step','Admin Created Healthy Steps', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($acdescription));
        //************* Activity Code End *************
    
        if($is_clone=='N'){
            setNotifySuccess("Healthy Steps created successfully",true);
        }else{
            setNotifySuccess("Healthy Steps Clone created successfully",true);
        }
    }


    $sqlStates="SELECT state FROM healthy_steps_states where prd_fee_id=:fee_id AND is_deleted='N'";
    $resStates=$pdo->select($sqlStates,array(":fee_id"=>$health_id));

    $availStateArray = array();
                    
    if(!empty($resStates)){
        foreach ($resStates as $key => $value) {
            array_push($availStateArray, $value['state']);
        }
    }
    
    $stateResult=array_diff($availStateArray,$states);
    if(!empty($stateResult)){
        foreach ($stateResult as $key => $value) {
            $updStateParams=array(
                'is_deleted'=>'Y'
            );
            $updStateWhere=array(
                'clause'=>'is_deleted="N" AND prd_fee_id = :prd_fee_id AND state=:state',
                'params'=>array(
                    ":prd_fee_id"=>$health_id,
                    ":state"=>$value
                )
            );
            $pdo->update("healthy_steps_states",$updStateParams,$updStateWhere);
        }
    }

    $stateResult=array_diff($states,$availStateArray);
    if(!empty($stateResult)){
        foreach ($stateResult as $key => $value) {
            $insStateParams = array(
                "prd_fee_id" => $health_id,
                "healthy_steps_fee_id" => $product_id,
                "state" => $value,
            );
            $prd_available_state = $pdo->insert('healthy_steps_states',$insStateParams);
        }
    }



  $response['redirect_url']=$ADMIN_HOST.'/healthy_steps.php';    
  $response['status']="success";
  
} else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
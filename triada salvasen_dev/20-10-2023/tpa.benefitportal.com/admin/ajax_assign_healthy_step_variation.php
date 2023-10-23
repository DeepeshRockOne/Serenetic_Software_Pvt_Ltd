<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$response = array();

$healthy_steps = isset($_POST['healthy_steps']) ? $_POST['healthy_steps'] : '' ;
$agent_id = checkIsset($_POST['receiving_agent']) !='' ? $_POST['receiving_agent'] : $_POST['agent_id'] ;
$status_main =  isset($_POST['status_main']) ? $_POST['status_main'] : '' ;
$products = checkIsset($_POST['selected_products'],'arr');
$effective_date = checkIsset($_POST['effective_date'],'arr');
$termination_date = checkIsset($_POST['termination_date'],'arr');
$fee_price = checkIsset($_POST['fee_price'],'arr');
$is_fee_commissionable = checkIsset($_POST['is_fee_commissionable'],'arr');
$non_commissionable_amount = checkIsset($_POST['non_commissionable_amount'],'arr');
$commissionable_amount = checkIsset($_POST['commissionable_amount'],'arr');
$is_member_benefits = checkIsset($_POST['is_member_benefits'],'arr');
$is_member_portal = checkIsset($_POST['is_member_portal'],'arr');
$description = checkIsset($_POST['description'],'arr');
$benifit_period = checkIsset($_POST['benifit_period'],'arr');
$month = checkIsset($_POST['month'],'arr');
$fees_id = checkIsset($_POST['fee_id']);
$display_id = checkIsset($_POST['display_id']);
$parent_product_id = checkIsset($_POST['parent_product_id'],'arr');
$fee_id = checkIsset($_POST['fee_id']);
$is_clone = checkIsset($_POST['is_clone']) == 'Y' ? 'Y' : 'N';
$product_status = checkIsset($_POST['product_status'],'arr');
$validate = new Validation();
if($is_clone=='Y'){
    $agent_id = checkIsset($_POST['receiving_agent']) !='' ? $_POST['receiving_agent'] : $_POST['cagent_id'] ;
}
if(empty($healthy_steps)){
    $validate->setError('healthy_steps',"Please select any healthy Step.");
}

if(empty($status_main)){
    $validate->setError('status_main',"Please select any Status.");
}

if(empty($agent_id)){
    $validate->setError('agent_id',"Please select any Agent.");
}

$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Healthy Step ID is required'));

if (!$validate->getError('display_id')) {
  $incr="";
  $sch_params=array();
  $sch_params[':display_id']=$display_id;
  $incr='';
if(!empty($fee_id) && $is_clone == 'N'){
    $incr.=" AND id != :id";
    $sch_params[':id'] = $fee_id;
}
  $selectCarrier = "SELECT id FROM prd_fees WHERE setting_type='Healthy Step Variation' AND display_id=:display_id $incr AND is_deleted='N' ";
  $resultCarrier1 = $pdo->selectOne($selectCarrier, $sch_params);
    if (!empty($resultCarrier1['id'])) {
    $validate->setError("display_id", "This Healthy step ID is already associated with another Healthy step id.");
    }
}

if($validate->isValid()){
    $agents_list = array();
    $agents_list  = $pdo->selectOne("SELECT GROUP_CONCAT(c.id) as ids from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where (c.id=:id  or (c.sponsor_id=:id and cs.agent_coded_id=1)) and c.type='Agent' and c.is_deleted='N' ",array(":id"=>$agent_id));
    include_once __DIR__ . '/../includes/function.class.php';
    $functionsList = new functionsList();
    $inserted = $updated = $deleted = false;

    $variation_prd_all_flip = $activity_desc = $variation_prd_all = $variation_prd = $deleted_fees_ids = $inserted_fee_ids = $old_data  = $new_data = array();
    $main_fee_id = '';

    $feesSql = "SELECT id,display_id FROM prd_fees WHERE id=:c_id AND is_deleted='N'";
    $feesRow = $pdo->selectOne($feesSql, array(":c_id" => $fees_id));    
    $health_id = '';
    $insert_params['display_id'] = $display_id;
    $insert_params['status'] = $status_main;
    if(!empty($feesRow['id']) && $is_clone=='N'){
        $health_id = $feesRow['id'];
        $update_where = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $health_id)
        );
        $old_data['prd_fees'] = $pdo->update('prd_fees', $insert_params, $update_where,true);
        $new_data['prd_fees'] = $insert_params;
        if(!empty($old_data['prd_fees'])){
            foreach($old_data['prd_fees'] as $key => $value){
                if(!$key='display_id'){
                    $activity_desc[]['udpated'] = $key.' updated from '.$value.' to '.$insert_params[$key];
                }else{
                    $activity_desc[]['udpated'] = 'Healthy Step Variation Id'.' updated from '.$value.' to '.$insert_params[$key];
                }
                
            }
        }
        $updated = true;
    }else{     
        $feesName=$functionsList->generateHealthyStepFeesName(true);
        $insert_params['name'] = $feesName;
        $insert_params['setting_type'] = 'Healthy Step Variation';
        $health_id = $pdo->insert("prd_fees", $insert_params);
    }
    $main_fee_id = $insert_params['display_id'];
    if(!empty($feesRow['id'])){
        $sel_product = "SELECT GROUP_CONCAT(p.id) as id,GROUP_CONCAT(p.parent_product_id) as parent_id from prd_main p  JOIN agent_product_rule apr ON(apr.product_id=p.id and apr.is_deleted='N') where apr.agent_id=:agent_id and p.type='Fees' and product_type='Healthy Step' and p.is_deleted='N'";
        $variation_prd = $pdo->selectOne($sel_product,array(":agent_id"=>$agent_id));
        $variation_prd_all = explode(',',$variation_prd['id']);
        $variation_prd_parent = explode(',',$variation_prd['parent_id']);
        $variation_prd_all_flip = array_flip($variation_prd_all);
    }
    
    foreach($healthy_steps as $step){
        
        if(!empty($variation_prd_all) && in_array($step,$variation_prd_parent) && $is_clone == 'N' && !empty($agents_list)){

            $sel_product = "SELECT p.id,parent.name,parent.product_code from prd_main p JOIN prd_main parent ON(parent.id=p.parent_product_id and parent.is_deleted='N') JOIN agent_product_rule apr ON(apr.product_id=p.id ) where apr.agent_id=:agent_id and p.type='Fees' and p.parent_product_id=:parent_product_id and p.product_type='Healthy Step' and p.is_deleted='N' and apr.is_deleted='N'";
            $variation_prds = $pdo->selectOne($sel_product,array(":agent_id"=>$agent_id,":parent_product_id"=>$step));
            $variation_id = $variation_prds['id'];

            if(in_array($variation_id,$variation_prd_all)){
                unset($variation_prd_all_flip[$variation_id]);
            }
            $update_param = array(
                "status" => $product_status[$step],
                "updated_at" => 'msqlfunc_NOW()',
            );
            $where = array(
                "clause" => ' product_id=:prd_id and agent_id IN('.$agents_list['ids'].') and is_deleted="N" ',
                "params" => array(":prd_id"=>$variation_id)
            );
            $old_data['apr'] = $pdo->update('agent_product_rule',$update_param,$where,true);

            $insert_param_apr = array(
                'status' => $product_status[$step],
                'admin_id' => checkIsset($_SESSION['admin']['id']),
                'product_id' => $variation_id,
                'parent_product_id' => $step,
            );
            $agents = explode(',',$agents_list['ids']);
            foreach($agents as $id){
                $insert_param_apr['agent_id'] = $id;

                $existingRes = $pdo->selectOne("SELECT id FROM agent_product_rule WHERE agent_id = :agent_id AND product_id = :product_id AND is_deleted='N'",array(":agent_id" => $id,":product_id" => $variation_id));
                if(empty($existingRes)){
                    $pdo->insert('agent_product_rule',$insert_param_apr);
                }

            }
            
            if(!empty($old_data['apr'])){
                foreach($old_data['apr'] as $key => $value){
                    if($value=='Pending Approval'){
                         $value='Inactive';
                    }else{
                        $value='Active';
                    }
                    if($update_param[$key] =='Contracted'){
                        $update_param[$key] = 'Active';
                    }else{
                        $update_param[$key] = 'Inactive';
                    }
                    $activity_desc[]['udpated'] = $variation_prds['name'].'('.$variation_prds['product_code'].') '.$key .' updated from '.$value.' to '.$update_param[$key];
                }
            }
        }else{
            $insert_param_prd_main = array(
                'name' => getname('prd_main',$step,'name',"id"),//$functionsList->generateHealthyStepFeesName(),
                'parent_product_id' =>$step,
                'product_code' => $functionsList->generateHealthyStepVariationDisplayID(),
                'record_type' => 'Variation',
                'type' => 'Fees',
                'fee_type' => 'Charged',
                'product_type' => 'Healthy Step',
                'fee_renewal_type' => $benifit_period[$step],
                'is_member_benefits' => $is_member_benefits[$step],
                'payment_type'  =>  'Single',
                'is_fee_on_commissionable' => $is_fee_commissionable[$step],
                'status' => 'Active',
            );
            
            if($is_member_benefits[$step] == 'Y'){
                $insert_param_prd_main['payment_type'] = 'Recurring';
                $insert_param_prd_main['is_fee_on_renewal'] = 'Y';                
                if($benifit_period[$step] == 'Renewals'){
                    $insert_param_prd_main['fee_renewal_count'] = $month[$step];
                }
            }

            $ins_product_id = $pdo->insert('prd_main',$insert_param_prd_main);
            $inserted_fee_ids[] = $ins_product_id;
            $insert_param_prd_matrix = array(
                'product_id' => $ins_product_id,
                'price' => $fee_price[$step],
                'commission_amount' => $commissionable_amount[$step],
                'non_commission_amount' => $non_commissionable_amount[$step],
                'pricing_effective_date' => date('Y-m-d',strtotime($effective_date[$step])) ,
                'pricing_termination_date' => !empty($termination_date[$step]) ? date('Y-m-d',strtotime($termination_date[$step])) : NULL,
            );
            $pdo->insert('prd_matrix',$insert_param_prd_matrix);

            $insert_param_prd_assign_fee = array(
                'fee_id' => $ins_product_id,
                'prd_fee_id' => $health_id,
            );
            $selected_products = explode(',',$products[$step]);
            foreach($selected_products as $prds){
                $insert_param_prd_assign_fee['product_id'] = $prds;
                $pdo->insert("prd_assign_fees",$insert_param_prd_assign_fee);
            }

            if($is_member_benefits[$step] == 'Y'){
                $insert_param_prd_member_info = array(
                    'product_id' =>  $ins_product_id,
                    'description' => $description[$step],
                    'is_member_portal' => $is_member_portal[$step],
                );
                $pdo->insert('prd_member_portal_information',$insert_param_prd_member_info);    
            }

            $sqlStates = "SELECT state FROM healthy_steps_states WHERE healthy_steps_fee_id=:id AND is_deleted='N'";
            $resStates = $pdo->select($sqlStates,array(":id"=>$step));

            if(!empty($resStates)){
                foreach ($resStates as $stateKey => $stateRow) {
                    $insStateParams = array(
                        "prd_fee_id" => $health_id,
                        "healthy_steps_fee_id" => $ins_product_id,
                        "state" => $stateRow['state'],
                    );
                    $prd_available_state = $pdo->insert('healthy_steps_states',$insStateParams);
                }
            }

            $insert_param_apr = array(
                'status' => $product_status[$step],
                'admin_id' => checkIsset($_SESSION['admin']['id']),
                'product_id' => $ins_product_id,
                'parent_product_id' => $step,
            );
            $agents = explode(',',$agents_list['ids']);
            foreach($agents as $id){
                $insert_param_apr['agent_id'] = $id;
                $pdo->insert('agent_product_rule',$insert_param_apr);
            }
            $inserted = true;
        }
    }
$variation_prd_all_flip = array_flip($variation_prd_all_flip);
if(!empty($variation_prd_all_flip)){
    foreach($variation_prd_all_flip as $variation_id){
        //Select assigned agents for variation Healthy step
        $selAsignedAgent = "SELECT CONCAT(c.fname,' ',c.lname,'(',c.rep_id,') - ',cs.agent_coded_level) AS agentList
        FROM customer c 
        JOIN customer_settings cs ON(cs.customer_id=c.id AND cs.agent_coded_id!=1)
        JOIN agent_product_rule apr ON(apr.agent_id=c.id AND apr.is_deleted='N')
        WHERE c.type='Agent' AND c.is_deleted='N' AND apr.product_id=:variationId AND apr.agent_id!=:agentId  GROUP BY c.id ORDER BY c.created_at ASC";
        $resAssignedAgent = $pdo->select($selAsignedAgent,array(":variationId" =>$variation_id,":agentId"=>$agent_id));
        if(count($resAssignedAgent) > 0){
            $update_param = array("is_deleted"=>'Y','updated_at'=>'msqlfunc_NOW()');
            $upd_where = array(
                "clause" => 'product_id=:prd_id AND agent_id=:agent_id',
                "params" => array(":prd_id"=>$variation_id,":agent_id"=>$agent_id)
            );
            $pdo->update("agent_product_rule",$update_param,$upd_where);
            $deleted= true;
        }else{
            $update_param = array("is_deleted"=>'Y','update_date'=>'msqlfunc_NOW()');
            $where = array(
                "clause" => 'id=:prd_id',
                "params" => array(":prd_id"=>$variation_id)
            );
            $pdo->update("prd_main",$update_param,$where);
        
            $upd_where = array(
                "clause" => 'product_id=:prd_id',
                "params" => array(":prd_id"=>$variation_id)
            );
        
            $pdo->update("prd_matrix",$update_param,$upd_where);
        
            unset($update_param['update_date']);
            $update_param['updated_at'] = 'msqlfunc_NOW()';
        
            $pdo->update("prd_member_portal_information",$update_param,$upd_where);
            $pdo->update("agent_product_rule",$update_param,$upd_where);
        
            $upd_where_state = array(
                "clause" => 'healthy_steps_fee_id=:healthy_steps_fee_id',
                "params" => array(":healthy_steps_fee_id"=>$variation_id)
            );
            $pdo->update("healthy_steps_states",$update_param,$upd_where_state);
    
            $upd_where_fees = array(
                "clause" => 'fee_id=:prd_id',
                "params" => array(":prd_id"=>$variation_id)
            );
        
            $data = $pdo->update("prd_assign_fees",$update_param,$upd_where_fees,true);
            if(!empty($data)){
                $deleted= true;
            }
        }
    }   
}

if($inserted && !empty($inserted_fee_ids)){
    $name = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(parent.name,' (',parent.product_code,') ')) AS product_code FROM prd_main p LEFT JOIN prd_main parent ON(parent.id=p.parent_product_id ) WHERE   p.id IN(".implode(',',$inserted_fee_ids).")");
    $activity_desc[]['instered'] = 'Inserted Healthy Step Fees : '.$name['product_code'];
}
if($deleted && !empty($variation_prd_all_flip)){
    $name1 = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(parent.name,' (',parent.product_code,') ')) AS product_code FROM prd_main p LEFT JOIN prd_main parent ON(parent.id=p.parent_product_id ) WHERE   p.id IN(".implode(',',$variation_prd_all_flip).")");
    $activity_desc[]['deleted'] = 'Deleted Healthy Step Fees : '.$name1['product_code'];
}

if(!empty($activity_desc) ){
    $agent_name = $pdo->selectOne("SELECT fname,lname,rep_id from customer c where id=:agent_id and is_deleted='N' and type='Agent'",array(":agent_id"=>$agent_id));
    $message = $action ='';
    if($updated){
        $response['message']="Variation Healthy step Updated successfully!";
        $action ='Admin Updated Variation Healthy Steps';
        $message = "Updated Healthy Agent ".$agent_name['fname'].' '.$agent_name['lname']."(".$agent_name['rep_id'].") "; 
    }else{
        $response['message']="Variation Healthy step Inserted successfully!";
        $action ='Admin Created Variation Healthy Steps';
        $message = "Inserted Healthy Agent ".$agent_name['fname'].' '.$agent_name['lname']."(".$agent_name['rep_id'].") "; 
    }

    $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id']
        ),
        'ac_message_1' =>$message,
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/variation_healthy_steps.php?fee_id='.md5($health_id),
            'title'=>$main_fee_id,
        ),
    );

    $desc ='';
    foreach($activity_desc as $key => $activity){
        
        if(!empty($activity)){
            foreach($activity as $key1 => $activity1){
                $desc .= $activity1."<br>";
            }
        }
    }
    $activityFeedDesc['description'] = $desc;

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $health_id, 'Variation Healthy Step',$action, $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));

}
    $response['status']="success";
    $response['redirect_url']=$ADMIN_HOST.'/healthy_steps.php';
}

if(count($validate->getErrors()) > 0){
    $errors = $validate->getErrors();
    $response['status'] = "fail";
    $response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
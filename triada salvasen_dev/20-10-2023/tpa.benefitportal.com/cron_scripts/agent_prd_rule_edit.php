<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$validate = new Validation();


$cm_product = checkIsset($_POST['cm_product']);
$product_id = checkIsset($_POST['product_id']);
$loa  = checkIsset($_POST['loa']);
$downline  = checkIsset($_POST['downline']);

$agent_id = checkIsset($_POST['agent_id']);
$available_state = checkIsset($_POST['available_state'],'arr');
$excludes = checkIsset($_POST['excludes'],'arr');
$auto = checkIsset($_POST['auto'],'arr');
$required = checkIsset($_POST['required'],'arr');
$packaged = checkIsset($_POST['packaged'],'arr');

$ADMIN_ID = checkIsset($_POST['admin_id']);
$ADMIN_DISPLAY_ID = checkIsset($_POST['admin_display_id']);
$ADMIN_NAME = checkIsset($_POST['admin_name']);

$cronWhere = array(
    "clause" => "script_code=:script_code", 
    "params" => array(
        ":script_code" => 'agent_product_rule'
    )
);
$pdo->update('system_scripts',array("is_running" => "Y","status"=>"Running","last_processed"=>"msqlfunc_NOW()"),$cronWhere);

if(!empty($excludes)){
    $exclude_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $excludes)."') OR parent_product_id in('".implode("','", $excludes)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
    if($exclude_variations){
        $excludes = explode(',', $exclude_variations['product_ids']);
    }
}

if(!empty($required)){
    $required_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $required)."') OR parent_product_id in('".implode("','", $required)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
    if($required_variations){
        $required = explode(',', $required_variations['product_ids']);
    }
}


if(!empty($auto)){
    $sql = "SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $auto)."') OR parent_product_id in('".implode("','", $auto)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'";
    $autoassign_variations = $pdo->selectOne($sql);
    if($autoassign_variations){
        $auto = explode(',', $autoassign_variations['product_ids']);
    }
}

if(!empty($packaged)){
    $pakaged_variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $packaged)."') OR parent_product_id in('".implode("','", $packaged)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
    if($pakaged_variations){
        $packaged = explode(',', $pakaged_variations['product_ids']);
    }
}

$agents = [];
$agent_details = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where md5(id)=:id",array(":id"=>$agent_id));
$id = $agent_details['id'];
$agent_name_rep = $agent_details['name'].' ('.$agent_details['rep_id'].')';
$product_details =  $pdo->selectOne("SELECT name,product_code from prd_main where is_deleted='N' and status='Active' and md5(id)=:prd_id",array(":prd_id"=>$product_id));
$prd_name_code = $product_details['name'].'('.$product_details['product_code'] .')';
$pr_id = getname('prd_main',$product_id,'id','md5(id)');

$downline_agents = $pdo->selectOne("SELECT group_concat(c.id) as ids FROM customer c WHERE type='Agent' AND upline_sponsors LIKE '%,$id,%' AND is_deleted='N'");
$loa_agents = $pdo->selectOne("SELECT group_concat(c.id) as ids FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE type='Agent' AND agent_coded_level=:type AND sponsor_id=:sponsor_id AND is_deleted='N'",array(':type' => "LOA",':sponsor_id' => $id));
 

    $commission_activity = $product_settings = '';
    $state_update_activity = $combination_activity = array();
    if(!empty($loa['cm']) && !empty($downline['cm'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $commission_activity .= updateCommissionRule($agent_ids,$cm_product,$pr_id);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'commission','Y','Y');

    }else if(!empty($loa['cm']) && empty($downline['cm'])){
        $agent_ids = !empty($loa_agents['ids']) ? explode(",",$loa_agents['ids']) : array();
        array_push($agent_ids,$id);
        $commission_activity .= updateCommissionRule($agent_ids,$cm_product,$pr_id);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'commission','Y','N');

    }else if(!empty($downline['cm'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $commission_activity.=updateCommissionRule($agent_ids,$cm_product,$pr_id);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'commission','N','Y');

    }else{
        $agent_ids = array($id);
        $commission_activity.=updateCommissionRule($agent_ids,$cm_product,$pr_id);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'commission');
    }

    if(!empty($loa['state']) && !empty($downline['state'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $state_update_activity[] = insertUpdateState($agent_ids,$pr_id,$available_state);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'state','Y','Y');

    }else if(!empty($loa['state']) && empty($downline['state'])){
        $agent_ids = !empty($loa_agents['ids']) ? explode(",",$loa_agents['ids']) : array();
        array_push($agent_ids,$id);
        $state_update_activity[] = insertUpdateState($agent_ids,$pr_id,$available_state);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'state','Y','N');

    }else if(!empty($downline['state'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $state_update_activity[] = insertUpdateState($agent_ids,$pr_id,$available_state);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'state','N','Y');

    }else{
        $agent_ids = array($id);
        $state_update_activity[] = insertUpdateState($agent_ids,$pr_id,$available_state);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'state');
    }
        
    if(!empty($loa['add']) && !empty($downline['add'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $combination_activity = insertUpdateCombination($agent_ids,$auto,$excludes,$required,$packaged);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'combination','Y','Y');

    }else if(!empty($loa['add']) && empty($downline['add'])){
        $agent_ids = !empty($loa_agents['ids']) ? explode(",",$loa_agents['ids']) : array();
        array_push($agent_ids,$id);
        $combination_activity = insertUpdateCombination($agent_ids,$auto,$excludes,$required,$packaged);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'combination','Y','N');

    }else if(!empty($downline['add'])){
        $agent_ids = !empty($downline_agents['ids']) ? explode(",",$downline_agents['ids']) : array();
        array_push($agent_ids,$id);
        $combination_activity = insertUpdateCombination($agent_ids,$auto,$excludes,$required,$packaged);
        $product_settings .= insertupdateproductSetting($id,$pr_id,'combination','N','Y');

    } else {
        $agent_ids = array($id);
        $combination_activity = insertUpdateCombination($agent_ids,$auto,$excludes,$required,$packaged); 
        $product_settings .= insertupdateproductSetting($id,$pr_id,'combination');  
    }

    $deleted_state = $inserted_state = $state_activity = '';
    if(!empty($state_update_activity[0])){
        $state_activity .= "<br>State Updates : <br>";
        foreach($state_update_activity as $state){
            $state_activity .= checkIsset($state['agents'])."<br>";
            $inserted_state = ' &nbsp;'.checkIsset($state['inserted_state']);
            $deleted_state = ' &nbsp;'.checkIsset($state['deleted_state']);
        }
    }

    $combi_acitivity = '';
    if(!empty($combination_activity['selected']) || !empty($combination_activity['deleted'])){
        $combi_acitivity .= '<br>Combination Products : <br>'.$combination_activity['agents'].'<br>';
        unset($combination_activity['agents']);
        foreach($combination_activity as $key => $types){
            foreach($types as $type => $value){
                $products =  $pdo->selectOne(" SELECT GROUP_CONCAT(concat(name,' (',product_code,')')) as prd_name from prd_main where id IN($value) and is_deleted='N' ");
                $combi_acitivity.= " &nbsp;".$type ." ". $key ." : ". $products['prd_name']."<br>";
            }
        }
    }

    $cm_activity =$prd_activity ='';
    if($commission_activity!=''){
        $cm_activity .= "<br>Commission Rule updates : <br>";
    }
    if($product_settings !=''){
        $prd_activity.='<br>LOA or Downline updates : <br>';
    }
    $description =array();
    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($ADMIN_ID),
            'title'=>$ADMIN_DISPLAY_ID,
        ),
        'ac_message_1' =>'  Updated product rule In Agent : '.$agent_details['name'].' (',
        'ac_red_2'=>array(
            'href'=> 'agent_detail_v1.php?id='.md5($id),
            'title'=> $agent_details['rep_id'],
        ),
        'ac_message_2' =>')',
        );
    $description['product'] = 'For product : '.$prd_name_code."<br>";
    $description ['description'] = $cm_activity.$commission_activity.$prd_activity.$product_settings.$state_activity.$deleted_state.$inserted_state.$combi_acitivity;
    $desc=json_encode($description);
    activity_feed(3,$ADMIN_ID,'Admin', $id, 'Agent', 'Product Rule Updated.',$ADMIN_NAME,"",$desc);

function insertUpdateState($agent_ids,$pr_id,$available_state){

    global $pdo;
    $state_activity = array();
    if(!empty($agent_ids)){
        $agent_de = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT('<br>',fname,' ',lname,' (',rep_id,')')) as  names from customer where is_deleted='N' and id IN(".implode(',',$agent_ids).")");
        $state_activity['agents'] = "This update Effected following Agents : ".$agent_de['names'];
        foreach($agent_ids as $key => $agent_id){
            $db_state = array();    
            $dbstate = $pdo->select("SELECT id,state from agent_assign_state where agent_id=:id and product_id=$pr_id and is_deleted='N'",array(":id"=>$agent_id));
            $insert_state = $update_state = array();
            if(!empty($dbstate) && count($dbstate) > 0){
                foreach($dbstate as $prd){
                    $db_state[$prd['id']] = $prd['state'] ;
                }
                $update_state = array_diff($db_state,$available_state);
                if(count($update_state) > 0 && !empty($update_state)){
                    $upd_param = array('is_deleted' => 'Y','updated_at'=>'msqlfunc_NOW()');
                    foreach($update_state as $sid => $state){
                        $upd_where = array(
                            "clause"=>" id = :id ",
                            "params"=>array(":id"=>$sid)
                        );
                        $pdo->update('agent_assign_state',$upd_param,$upd_where);
                    }
                    $state_activity['deleted_state'] = "Selected States : ".implode(',',$update_state)."<br>";
                }
    
                $insert_state = array_diff($available_state,$db_state);
                if(count($insert_state) > 0 && !empty($insert_state)){
                    foreach ($insert_state as $key => $value) {
                        $ins_param_st = array(
                            "agent_id" => $agent_id,
                            "product_id" => $pr_id,
                            "state" => $value,
                            "created_at" => "msqlfunc_NOW()",
                            "updated_at" => "msqlfunc_NOW()"
                        );
                        $pdo->insert("agent_assign_state",$ins_param_st);
                    }
                    $state_activity['inserted_state'] = "Unselected States : ".implode(',',$insert_state)."<br>";
                }
    
            }else{
                if(!empty($available_state)){
                    foreach ($available_state as $key => $value) {
                        $ins_param_st = array(
                            "agent_id" => $agent_id,
                            "product_id" => $pr_id,
                            "state" => $value,
                            "created_at" => "msqlfunc_NOW()",
                            "updated_at" => "msqlfunc_NOW()"
                        );
                        $pdo->insert("agent_assign_state",$ins_param_st);
                    }
                    $state_activity['inserted_state'] = "Unselected States : ".implode(',',$available_state)."<br>";
                }
            }
        }
    }
    return $state_activity;
}

function updateCommissionRule($agents_id,$cm_product,$pr_id){
    global $pdo,$ADMIN_ID;
    $commission_activity = '';
    if(!empty($agents_id)){

        $new_rule_code = $pdo->selectOne("SELECT rule_code from commission_rule where id=:id and is_deleted='N'",array(":id"=>$cm_product));

        foreach($agents_id as $value){
            $rule_id = $pdo->selectOne("SELECT commission_rule_id as rule_id,cr.rule_code,apr.id, GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) AS names from agent_commission_rule apr JOIN customer c ON(c.id=apr.agent_id and c.type='Agent' and c.is_deleted='N') LEFT JOIN commission_rule cr on(cr.id=apr.commission_rule_id and cr.is_deleted='N') where apr.agent_id=:agent_id and apr.product_id=:prd_id and apr.is_deleted='N'",array(":agent_id"=>$value,':prd_id'=>$pr_id));

            if(empty($rule_id['rule_id'])){
                $agents_dts = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) AS names  FROM customer c WHERE c.id=:id and c.is_deleted='N' and c.type='Agent'",array(":id"=>$value));
                $ins_param = array(
                    'agent_id' => $value,
                    'product_id' =>$pr_id,
                    'commission_rule_id' =>$cm_product,
                    'admin_id' => $ADMIN_ID,
                    "created_at" => "msqlfunc_NOW()",
                    "updated_at" => "msqlfunc_NOW()"
                );
                $pdo->insert('agent_commission_rule',$ins_param);

                $commission_activity .= "New Rule added in Agent :".$agents_dts['names']." , Rule Code : ".$new_rule_code['rule_code']."<br>";
            }else if(!empty($rule_id['rule_id']) && $rule_id['rule_id'] != $cm_product){
                $up_param = array(
                    "is_deleted" => 'Y',
                    "updated_at" => 'msqlfunc_NOW()' ,
                );
                $where = array(
                    "clause" => "id=:id ",
                    "params" => array(":id"=>$rule_id['id']),
                );
                $pdo->update('agent_commission_rule',$up_param,$where);

                $commission_activity .= "Rule updated in Agent : ".$rule_id['names']." , Rule Code : ".$rule_id['rule_code'].' To '.$new_rule_code['rule_code']."<br>";
                $ins_param = array(
                    'agent_id' => $value,
                    'product_id' =>$pr_id,
                    'commission_rule_id' =>$cm_product,
                    'admin_id' => $ADMIN_ID,
                    "created_at" => "msqlfunc_NOW()",
                    "updated_at" => "msqlfunc_NOW()"
                );
                $pdo->insert('agent_commission_rule',$ins_param);
            }            
        }
        return $commission_activity;
    } else {
        return $commission_activity;
    }
    
}

function insertUpdateCombination($agent_ids,$auto,$excludes,$required,$packaged){

    global $pdo,$pr_id;
    $combination_activity = array('selected'=>array(),'deleted'=>array(),'agents'=>'');
    if(!empty($agent_ids)){
        $agent_de = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT('<br>',fname,' ',lname,' (',rep_id,')')) as  names from customer where id IN(".implode(',',$agent_ids).")");
        $combination_activity['agents'] = "This update Effected following Agents : ".$agent_de['names'];
        foreach($agent_ids as $agent_id){
            $dbcombi = $pdo->select("SELECT id,combination_product_id,comb_type as type from agent_assign_combination where agent_id=:id and product_id=:product_id and is_deleted='N'",array(":id"=>$agent_id,":product_id"=>$pr_id));
            $combi_ex=$combi_at=$combi_re=$combi_pk = [];

            if(!empty($dbcombi) && count($dbcombi) > 0){
                foreach($dbcombi as $cmb){
                    if($cmb['type'] == 'Auto Assign'){
                        $combi_at[$cmb['id']] = $cmb['combination_product_id'];
                    }else if($cmb['type'] == 'Excludes'){
                        $combi_ex[$cmb['id']] = $cmb['combination_product_id'];
                    }else if($cmb['type'] == 'Required'){
                        $combi_re[$cmb['id']] = $cmb['combination_product_id'];
                    }else if($cmb['type'] == 'Packaged'){
                        $combi_pk[$cmb['id']] = $cmb['combination_product_id'];
                    }
                }
                $update_auto = array_diff($combi_at,$auto);
                $update_ex = array_diff($combi_ex,$excludes);
                $update_re = array_diff($combi_re,$required);
                $update_pk = array_diff($combi_pk,$packaged);
        
                $ins_auto = array_diff($auto,$combi_at);
                $ins_ex = array_diff($excludes,$combi_ex);
                $ins_re = array_diff($required,$combi_re);
                $ins_pk = array_diff($packaged,$combi_pk);
        
                if(count($update_auto)>0){
                    updateCombination($update_auto);
                    $combination_activity['deleted']['Auto Assign'] = implode(',',$update_auto);
                }else if(count($ins_auto) > 0){
                    insertCombination($agent_id,$auto,'Auto Assign');
                    $combination_activity['selected']['Auto Assign'] = implode(',',$ins_auto);
                }
                
                if(count($update_ex)>0){
                    updateCombination($update_ex);
                    $combination_activity['deleted']['Excludes'] = implode(',',$update_ex);
                }else if(count($ins_ex) > 0){
                    insertCombination($agent_id,$excludes,'Excludes');
                    $combination_activity['selected']['Excludes'] = implode(',',$ins_ex);
                }
        
                if(count($update_re)>0){
                    updateCombination($update_re);
                    $combination_activity['deleted']['Required'] = implode(',',$update_re);
                }else if(count($ins_re)>0){
                    insertCombination($agent_id,$required,'Required');
                    $combination_activity['selected']['Required'] = implode(',',$ins_re);
                }
        
                if(count($update_pk)>0){
                    updateCombination($update_pk);
                    $combination_activity['deleted']['Packaged'] = implode(',',$update_pk);
                }else if(count($ins_pk) > 0){
                    insertCombination($agent_id,$packaged,'Packaged');
                    $combination_activity['selected']['Packaged'] = implode(',',$ins_pk);
                }
        
            }else{
        
                if(!empty($excludes)){
                    insertCombination($agent_id,$excludes,'Excludes');
                    $combination_activity['selected']['Excludes'] = implode(',',$excludes);
                }
                if(!empty($auto)){
                    insertCombination($agent_id,$auto,'Auto Assign');
                    $combination_activity['selected']['Auto Assign'] = implode(',',$auto);
                }
                if(!empty($required)){
                    insertCombination($agent_id,$required,'Required');
                    $combination_activity['selected']['Required'] = implode(',',$required);
                }
                if(!empty($packaged)){
                    insertCombination($agent_id,$packaged,'Packaged');
                    $combination_activity['selected']['Packaged'] = implode(',',$packaged);
                }
            }

        }
    }
    return $combination_activity;
}

function insertCombination($agent_id,$combination,$type){
    global $pdo,$pr_id;
    $ins_param_ex = array(
        "agent_id" => $agent_id,
        "product_id" => $pr_id,
        "created_at" => "msqlfunc_NOW()",
        "updated_at" => "msqlfunc_NOW()"
    );
    foreach ($combination as $key => $value) {
        $ins_param_ex["comb_type"] = $type;
        $ins_param_ex["combination_product_id"] = $value;
        $pdo->insert("agent_assign_combination",$ins_param_ex);
    }
}

function updateCombination($type){
    global $pdo;
    $upd_param = array(
        'is_deleted'=>'Y',
        'updated_at' => 'msqlfunc_NOW()'
    );

    foreach($type as $ky => $tp){
        $upd_where =array(
            "clause"=>" id = :id and combination_product_id = :prd_id",
            "params" => array(":id"=>$ky,":prd_id"=>$tp)
        );
        $pdo->update("agent_assign_combination",$upd_param,$upd_where);

    }
}

function insertupdateproductSetting($agent_id,$product_id,$type='',$loa='N',$downline='N'){
    if ($type != ''){
        global $pdo,$agent_name_rep,$prd_name_code;

        $product_settings = '';
        $res_setting = $pdo->selectOne("SELECT id from agent_product_settings where agent_id = :id and product_id=:prd_id",array(":id"=>$agent_id,":prd_id"=>$product_id));
  
        $agent_name = $agent_name_rep;
        $prd_name = $prd_name_code;

        if(!empty($res_setting['id'])){
            
            if($type == 'commission'){
                $upd_param = array("commission_downline"=>$downline,"commission_loa"=>$loa);
            }else if($type == 'state'){
                $upd_param = array("state_downline"=>$downline,"state_loa"=>$loa);
            }else if($type == 'combination'){
                $upd_param = array("combination_downline"=>$downline,"combination_loa"=>$loa);
            }

            $upd_param["updated_at"] = 'msqlfunc_NOW()';
            $upd_where = array("clause"=>"id =:id ","params"=>array(":id"=>$res_setting['id']));

            $upated_array = $pdo->update("agent_product_settings",$upd_param,$upd_where,true);
            unset($upd_param['updated_at']);

            if(!empty($upated_array)){
                foreach($upated_array as $key => $val){
                    $selected_new = $val == 'Y' ? ' Unselected ' : 'Selected';
                    $selected_old = $val == 'Y' ? ' Selected ' : 'Unselected';
                    $product_settings.= " &nbsp;".ucfirst($key) .' is '.$selected_old.' To '.$selected_new.' For Agent '.$agent_name.' And product '.$prd_name.'.<br>';
                }
            }else{
                $product_settings.='No updates in downline or LOA for '.$type.'.<br>';
            }

        }else{

            $ins_param = array(
                "agent_id" => $agent_id,
                "product_id" => $product_id,
                "created_at" => 'msqlfunc_NOW()',
                "updated_at" => 'msqlfunc_NOW()'
            );

            if($type == 'commission'){
                $ins_param['commission_loa'] = $loa;
                $ins_param['commission_downline'] = $downline;
            }else if($type == 'state'){
                $ins_param['state_loa'] = $loa;
                $ins_param['state_downline'] = $downline;
            }else if($type == 'combination'){
                $ins_param['combination_loa'] = $loa;
                $ins_param['combination_downline'] = $downline;
            }

            $pdo->insert("agent_product_settings",$ins_param);

            if(!empty($ins_param)){

                unset($ins_param['agent_id']);
                unset($ins_param['product_id']);
                unset($ins_param['created_at']);
                unset($ins_param['updated_at']);

                foreach($ins_param as $key => $val){
                    if($val == 'Y'){
                        $selected = $val == 'Y' ? ' Selected ' : '';
                        $product_settings.= ucfirst($key) .' is '.$selected.' For Agent '.$agent_name.' And product '.$prd_name.'.<br>';
                    }
                }
            }else{
                $product_settings.=' No updates in downline or LOA '.$type.'.<br>';
            }
        }

        return $product_settings;
    }else{
        return false;
    }
}

$cronWhere = array(
    "clause" => "script_code=:script_code", 
    "params" => array(
        ":script_code" => 'agent_product_rule'
    )
);
$pdo->update('system_scripts',array("is_running" => "N","status"=>"Active"),$cronWhere);

echo "Completed";
dbConnectionClose();
exit;
?>
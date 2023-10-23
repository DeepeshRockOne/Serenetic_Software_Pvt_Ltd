<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$rule_id  = checkIsset($_GET['rule_id']);
$agent_id  = checkIsset($_GET['agent_id']);
$product_id  = checkIsset($_GET['product_id']);

$available_state = array();
$availableCheckAll = array();
$state_arr = [];
$dbstate = $dbcombination = $agent_ed_rule = $agent_product_setting = [];
    $agent_product_setting = $pdo->selectOne("SELECT * from agent_product_settings where md5(agent_id)=:agent_id and md5(product_id)=:prd_id",array(":agent_id"=>$agent_id,":prd_id"=>$product_id));
    
    $agent_ed_rule = $pdo->selectOne("SELECT id,commission_rule_id from agent_commission_rule where md5(agent_id)=:agent_id and md5(product_id)=:prd_id and is_deleted='N'",array(":agent_id"=>$agent_id,':prd_id'=>$product_id));

    $resstate = $pdo->selectOne("SELECT group_concat(state) as available_state from agent_assign_state where md5(agent_id)=:agent_id and md5(product_id)=:product_id and is_deleted='N'",array(":agent_id"=>$agent_id,":product_id"=>$product_id));

    if(!empty($resstate['available_state'])){
        $dbstate = !empty($resstate['available_state']) ? explode(",", $resstate['available_state']) : array(); 
    }

    $dbcombination = $pdo->select("SELECT comb_type as combination_type,combination_product_id from agent_assign_combination where md5(agent_id)=:agent_id and md5(product_id)=:product_id and is_deleted='N'",array(":agent_id"=>$agent_id,":product_id"=>$product_id));

    $RuleRows = $pdo->select(" SELECT pc.title,c.id,c.product_id,c.product_type,c.rule_code,c.parent_rule_id,p.name as name,p.product_code FROM commission_rule c 
    LEFT JOIN prd_main p ON(p.id=c.product_id and p.is_deleted='N' and p.status='Active')
    LEFT JOIN prd_category pc ON(pc.id=p.category_id and pc.is_deleted='N' and pc.status='Active')
    WHERE  c.is_deleted='N' and md5(c.product_id)=:product_id ",array(":product_id"=>$product_id));

    $company_arr=[];
    if (!empty($RuleRows)){
        foreach($RuleRows as $key => $row) {
            if($row["parent_rule_id"] > 0 ){
                $company_arr[$row['title']][] = $row;
            }else{
                $company_arr[$row['title']][] = $row;             
            }

            if (empty($company_arr[$row['title']])) {
                unset($company_arr[$row['title']]);
            }

        }
    }
    ksort($company_arr);
    $drop_down_html = '';
    $option_display = '';
    foreach ($company_arr as $key => $company){
        if($company){
            $drop_down_html .= '<optgroup label="'.$key.'">';
            
                if(empty($agent_ed_rule))
                    $drop_down_html .= '<option value="" selected hidden disabled></option>';

                foreach ($company as $pkey => $row) {
                    $option_display =  (!empty($row["rule_code"]) ? $row["rule_code"] ." (".$row['name']." - ".$row['product_code'].")" : '');
                    if(!empty($agent_ed_rule) && $row['id'] == $agent_ed_rule['commission_rule_id']){
                        $drop_down_html .= '<option value="'.$row["id"].'" selected >'.$option_display.'</option>';
                    }else{
                        $drop_down_html .= '<option value="'.$row["id"].'">'.$option_display.'</option>';
                    }
                }
            $drop_down_html .= '</optgroup>';
        }
    }

    $products = $pdo->selectOne('SELECT id,name from prd_main where md5(id) = :id and is_deleted="N"',array(":id"=>$product_id));
    $sqlAvailState="SELECT group_concat(pas.state_name) as available_state 
                FROM prd_available_state pas
                WHERE md5(pas.product_id) = :product_id AND pas.is_deleted='N'";
    $resAvailState = $pdo->selectOne($sqlAvailState,array(":product_id"=>$product_id));
    if(!empty($resAvailState['available_state'])){
        $available_state = !empty($resAvailState['available_state']) ? explode(",", $resAvailState['available_state']) : array(); 
    }

    $productSql = "SELECT p.id,p.name,p.product_code,pc.title as category_name,p.pricing_model
	  FROM prd_main p
	  JOIN prd_category pc ON (pc.id=p.category_id)
	  WHERE p.is_deleted='N' AND p.name !='' AND p.type!='Fees' AND md5(p.id)!=:prd_id AND p.parent_product_id = 0
	  GROUP BY p.id order by p.name ASC";
    $productArray=$pdo->selectGroup($productSql,array(":prd_id"=>$product_id),'category_name');

    $excludeProduct = array();
    $autoAssignProduct = array();
    $requiredProduct = array();
    $packagedProduct = array();
    $productCombinationRulesArr = array();
            
// $dbcombination;
    if(!empty($dbcombination)){
        foreach ($dbcombination as $key => $value) {
            if($value['combination_type']=='Excludes'){
                array_push($excludeProduct, $value['combination_product_id']);
            }
            if($value['combination_type']=='Required'){
                array_push($requiredProduct, $value['combination_product_id']);
            }
            if($value['combination_type']=='Auto Assign'){
                array_push($autoAssignProduct, $value['combination_product_id']);
            }
            if($value['combination_type']=='Packaged'){
                array_push($packagedProduct, $value['combination_product_id']);
            }            
        }
    }
    $productCombinationRulesArrmain= array_merge($excludeProduct,$autoAssignProduct,$requiredProduct,$packagedProduct);
    $sel_combination = "SELECT GROUP_CONCAT(combination_product_id) as id FROM prd_combination_rule
                        WHERE md5(product_id)=:product_id AND is_deleted='N'";
    $productCombinationRulesArr = $pdo->selectOne($sel_combination,array(":product_id"=>$product_id));
    $productCombinationRules = !empty($productCombinationRulesArr['id']) ? explode(',',$productCombinationRulesArr['id']) : array();

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js');
$template = 'agents_product_edit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
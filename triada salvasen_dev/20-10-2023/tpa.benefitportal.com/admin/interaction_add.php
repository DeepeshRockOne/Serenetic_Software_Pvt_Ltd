<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = checkIsset($_GET['agent_id']);
$interaction_detail_id = checkIsset($_GET['interaction_detail_id']);
$interaction_type = array();
$interaction_detail = $selected_products = array();
$agent_res = $pdo->selectOne("SELECT id,fname,lname,rep_id,DATE_FORMAT(current_time(), '%a., %b. %d, %Y  %r') as date FROM customer where md5(id)=:id and type='Agent' and is_deleted='N'",array(':id'=>$agent_id));

$interaction_type = $pdo->select("SELECT type,id from interaction where user_type='Agent' and is_deleted='N' ORDER BY type ASC");
$tz = new UserTimeZone('D m/d/Y @ g:i A T',$_SESSION['admin']['timezone']);

if(isset($_GET['type']) && $_GET['type']=='edit' && !empty($interaction_detail_id)){
    $interaction_detail = $pdo->selectOne("SELECT i.type,id.interaction_id as int_id,id.description,id.created_at,GROUP_CONCAT(distinct(ip.product_id)) as product_ids from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id) LEFT JOIN interaction_product ip ON(ip.interaction_detail_id=id.id and ip.is_deleted='N') where md5(id.id)=:interaction_detail_id and id.is_deleted='N'",array(":interaction_detail_id"=>$interaction_detail_id));

    $selected_products = explode(",",$interaction_detail['product_ids']);
}
    $company_arr = get_active_global_products_for_filter($agent_res['id'],true,false,false,false,false);

    $category = array();
    $category = $pdo->select("SELECT * FROM s_ticket_group where is_deleted='N' order by title ASC");

    $drop_down_html = '';
    $option_display = '';
    $prd_name = array();
    if(!empty($company_arr)){
        foreach ($company_arr as $key => $company){
            if($company){
                $drop_down_html .= '<optgroup label="'.$key.'">';
                    foreach ($company as $pkey => $row) {
                        if(empty($prd_name) || !in_array($row['name'],$prd_name)){
                            $prd_name[] = $row['name'];
                            $option_display = $row['name'];
                            if(in_array($row['id'],$selected_products)){
                                $drop_down_html .= '<option value="'.$row["id"].'" selected="selected">'.$option_display.'</option>';   
                            }else{
                                $drop_down_html .= '<option value="'.$row["id"].'">'.$option_display.'</option>';   
                            }
                        }
                    }
                $drop_down_html .= '</optgroup>';
            }
        }
    }
$exStylesheets = array(
    'thirdparty/multiple-select-master/multiple-select.css', 
);
$exJs = array(
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js', 
);

$template = "interaction_add.inc.php";
include_once 'layout/iframe.layout.php';
?>
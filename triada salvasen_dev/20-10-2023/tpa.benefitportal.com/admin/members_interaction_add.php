<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$memberId = checkIsset($_GET['memberId']);
$interaction_detail_id = checkIsset($_GET['interaction_detail_id']);
$interaction_type = array();
$interaction_detail = $selected_products = array();
$is_claim = isset($_GET['is_claim']) ? $_GET['is_claim'] : "N";
$claim_incr = "";

$memberRes = $pdo->selectOne("SELECT id,fname,lname,rep_id,DATE_FORMAT(current_time(), '%a., %b. %d, %Y  %r') as date FROM customer where md5(id)=:id and type IN('Customer','customer') and is_deleted='N'",array(':id'=>$memberId));

$interaction_type = $pdo->select("SELECT type,id from interaction where user_type='member' and is_deleted='N' ORDER BY type ASC");
$tz = new UserTimeZone('D m/d/Y @ g:i A T',$_SESSION['admin']['timezone']);

if(isset($_GET['type']) && $_GET['type']=='edit' && !empty($interaction_detail_id)){
    if($is_claim == 'Y'){
        $claim_incr .= " AND id.is_claim = 'Y'";
    }
    
    $interaction_detail = $pdo->selectOne("SELECT i.type,id.interaction_id as int_id,id.description,id.created_at,GROUP_CONCAT(distinct(ip.product_id)) as product_ids from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id) LEFT JOIN interaction_product ip ON(ip.interaction_detail_id=id.id and ip.is_deleted='N') where md5(id.id)=:interaction_detail_id and id.is_deleted='N' $claim_incr",array(":interaction_detail_id"=>$interaction_detail_id));

    $selected_products = explode(",",$interaction_detail['product_ids']);
}

    $company_arr = array('Global Products' => array());
    
    $productSql = "SELECT p.id,p.product_code,p.name as prdName,p.type,p.parent_product_id,pc.title as category_name
                    FROM prd_main p
                    JOIN website_subscriptions ws ON (ws.product_id=p.id AND ws.customer_id=:memberId)
                    LEFT JOIN prd_category pc ON(pc.id=p.category_id and p.is_deleted='N')
                    WHERE p.is_deleted='N' AND p.status IN('Active','Suspended')
                    GROUP BY p.id ORDER BY pc.title ASC";
    $productRes = $pdo->select($productSql, array(":memberId" => $memberRes['id']));

    $category = array();
    $category = $pdo->select("SELECT * FROM s_ticket_group where is_deleted='N' order by title ASC");
    
    if ($productRes){
        foreach($productRes as $key => $row) {
            if($row['category_name'] != ""){
                $company_arr[$row['category_name']][] = $row;
            }else{
                $company_arr['Global Products'][] = $row;                
            }

            if (empty($company_arr['Global Products'])) {
                unset($company_arr['Global Products']);
            }

            if (empty($company_arr[$row['category_name']])) {
                unset($company_arr[$row['category_name']]);
            }
        }
    }
    ksort($company_arr);
    $drop_down_html = '';
    $option_display = '';
    foreach ($company_arr as $key => $company){
        if($company){
            $drop_down_html .= '<optgroup label="'.$key.'">';
                foreach ($company as $pkey => $row) {
                $option_display = $row['prdName'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : '');
                if(in_array($row['id'],$selected_products)){
                    $drop_down_html .= '<option value="'.$row["id"].'" selected="selected">'.$option_display.'</option>';   
                }else{
                    $drop_down_html .= '<option value="'.$row["id"].'">'.$option_display.'</option>';   
                }
                }
            $drop_down_html .= '</optgroup>';
        }
    }

$exStylesheets = array( 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js');

$template = "members_interaction_add.inc.php";
include_once 'layout/iframe.layout.php';
?>
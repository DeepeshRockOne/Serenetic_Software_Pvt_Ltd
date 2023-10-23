<?php

if(!isset($_GET['user_name'])) {
    include_once dirname(__FILE__) . '/includes/connect.php';
    $pb_row = array();
    if (isset($_SESSION['agents']['id']) || isset($_SESSION['groups']['id']) || isset($_SESSION['admin']['id']) ) {
        $page_builder_id = $_GET['page_builder_id'];
        $pb_sql = "SELECT pg.*,pi.image_name as cover_image 
                    FROM page_builder pg 
                    LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
                    WHERE md5(pg.id)=:page_builder_id";
        $pb_row = $pdo->selectOne($pb_sql,array(":page_builder_id"=>$page_builder_id));
    }
    if(empty($pb_row)) {
        setNotifyError("Sorry! You have no rights to access this page.");
        redirect($AGENT_HOST,true);
        exit();
    }
}

$product_ids = $pb_row['product_ids'];
$prd_category_res = array();
if(!empty($product_ids)) {
    $prd_sql = "SELECT p.id as product_id,p.name as product_name,p.category_id,pc.title as category_name,min(pm.price) as price,pc.short_description,pc.category_image
        FROM prd_main p
        JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
        JOIN prd_category pc ON (pc.id=p.category_id)
        JOIN prd_matrix pm ON(pm.product_id = p.id AND pm.is_deleted='N')
        LEFT JOIN prd_descriptions pd ON (pd.product_id = p.id)
        LEFT JOIN customer c ON(c.id=apr.agent_id)
        WHERE 
        p.status='Active' AND 
        p.type!='Fees' AND 
        p.is_deleted='N' AND 
        apr.agent_id = :agent_id AND
        IF(c.type = 'Agent',
            p.product_type IN ('Direct Sale Product','Add On Only Product'),
            IF(c.type ='Group',p.product_type IN ('Group Enrollment'),0)
        ) AND
        p.id IN(".$product_ids.")
        GROUP BY p.id 
        ORDER BY category_name,product_name,price ASC";
    $prd_where = array(":agent_id" => $pb_row['agent_id']);
    $prd_res = $pdo->select($prd_sql,$prd_where);    

    if(!empty($prd_res)) {
        foreach ($prd_res as $prd_row) {
            if(!isset($prd_category_res[$prd_row['category_id']])) {
                $prd_category_res[$prd_row['category_id']] = array(
                    'category_id' => $prd_row['category_id'],
                    'category_name' => $prd_row['category_name'],
                    'category_image' => $prd_row['category_image'],
                    'short_description' => $prd_row['short_description'],
                    'prd_res' => array($prd_row),
                );
            } else {
                $prd_category_res[$prd_row['category_id']]['prd_res'][] = $prd_row;
            }
        }
    }
}

$spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon,c.user_name ,c.type
            FROM customer c 
            JOIN customer_settings cs ON(cs.customer_id = c.id)
            WHERE c.id=:id";
$spon_where = array(":id" => $pb_row['agent_id']);
$spon_row = $pdo->selectOne($spon_sql,$spon_where);


$website_enroll_link = 'javascript:void(0);';

if($spon_row['type'] == 'Agent'){
    $website_enroll_link = $AAE_WEBSITE_HOST . '/' . $pb_row['user_name'];
}else if($spon_row['type'] == 'Group'){
    $website_enroll_link = $AAE_WEBSITE_HOST . '/' . $pb_row['user_name'];
}

$member_services_cell_phone = get_app_settings('member_services_cell_phone');
$member_services_email = get_app_settings('member_services_email');

$template = 'prd_preview.inc.php';
include 'tmpl/' . $template;
?>
<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$agent_id = $_SESSION["agents"]["id"];
$page_builder_id = $_GET['page_builder_id'];
$pb_sql = "SELECT pg.*,pi.image_name as cover_image 
            FROM page_builder pg 
            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
            WHERE md5(pg.id)=:page_builder_id";
$pb_row = $pdo->selectOne($pb_sql,array(":page_builder_id"=>$page_builder_id));
$product_ids = $pb_row['product_ids'];

$prd_category_res = array();

if(!empty($product_ids)) {
    $prd_sql = "SELECT p.id as product_id,p.name as product_name,p.category_id,pc.title as category_name,min(pm.price) as price,pc.short_description,pc.category_image
        FROM prd_main p
        JOIN agent_product_rule apr ON (p.id=apr.product_id AND apr.status ='Contracted' AND apr.is_deleted='N')
        JOIN prd_category pc ON (pc.id=p.category_id)
        JOIN prd_matrix pm ON(pm.product_id = p.id AND pm.is_deleted='N')
        LEFT JOIN prd_descriptions pd ON (pd.product_id = p.id)
        WHERE 
        p.status='Active' AND 
        p.type!='Fees' AND 
        p.is_deleted='N' AND 
        apr.agent_id = :agent_id AND
        p.product_type IN ('Direct Sale Product','Add On Only Product') AND
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
$template = 'prd_preview.inc.php';
include 'tmpl/' . $template;
?>
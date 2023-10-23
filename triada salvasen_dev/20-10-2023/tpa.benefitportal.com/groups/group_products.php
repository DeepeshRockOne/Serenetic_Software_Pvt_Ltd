<?php
include_once __DIR__ . '/includes/connect.php';
$tz = new UserTimeZone('m/d/Y', $_SESSION['groups']['timezone']);
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if(!empty($_GET['id'])){
    $sqlGroupProduct = "SELECT billing_type FROM customer_group_settings where MD5(customer_id) = :customer_id";
    $resGroupProduct = $pdo->selectOne($sqlGroupProduct,array(":customer_id"=>$_GET['id']));
}
$per_page = '';
if($is_ajaxed){
    
    $sch_params[":group_id"] = $_GET['id'];
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }

    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    $query_string.="&#gp_products";
    $options = array(
        'results_per_page' => 10,
        'url' => 'group_products.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    
    $options = array_merge($pageinate_html, $options);
    try {

            $selProduct ="SELECT apr.product_id,apr.agent_id,
            apr.id AS apr_id,apr.status,apr.created_at,apr.updated_at,pm.name as name,pm.product_code as product_code,pm.id AS pid,pm.status AS product_status,pm.pricing_model AS pricing_model,apr.product_billing_type
            FROM agent_product_rule apr
            LEFT JOIN prd_main pm ON(pm.id=apr.product_id AND pm.status!='Inactive' AND pm.type!='Fees' AND pm.product_type='Group Enrollment' AND pm.is_deleted='N' ) 
            WHERE MD5(apr.agent_id)=:group_id AND apr.is_deleted='N' 
            GROUP BY apr.product_id ORDER BY FIELD(pm.status,'Active','Pending','Suspended','Extinct'),FIELD(apr.status,'Contracted','Pending Approval','Suspended','Extinct'),pm.name ASC";

    $paginate_product = new pagination($page, $selProduct, $options);
    if ($paginate_product->success == true) {
        $fetchProduct = $paginate_product->resultset->fetchAll();
        $totalProduct = count($fetchProduct);
    }
    include_once 'tmpl/group_products.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}else{
    include_once 'tmpl/group_products.inc.php';
}
?>
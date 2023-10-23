<?php
include_once __DIR__ . '/includes/connect.php';

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$resource_name = checkIsset($_GET['resource_name']);
$resource_category = checkIsset($_GET['resource_category']);

$per_page = '';
$incr = '';
$sch_params = array();

$resource_name = cleanSearchKeyword($resource_name);  
 
if($is_ajaxed){

if(!empty($resource_name)){
    $incr .= " AND (r.name LIKE :name)";
    $sch_params[':name'] = "%" . makeSafe($resource_name) . "%";
}

if(!empty($resource_category)){
    $incr .= " AND r.category = :category";
    $sch_params[":category"] = $resource_category;
}

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$query_string.="&#resources";
$options = array(
    'results_per_page' => 25,
    'url' => 'system_resources_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {

            $selResources ="SELECT r.id,r.name,r.file_name,r.category
            FROM system_resources r
            WHERE r.is_deleted='N' $incr
            GROUP BY r.id ORDER BY FIELD(category, 'API',category),name ASC";

    $paginate_resource = new pagination($page, $selResources, $options);
    if ($paginate_resource->success == true) {
        $fetchResources = $paginate_resource->resultset->fetchAll();
        $totalResources = count($fetchResources);
    }
    include_once 'tmpl/system_resources_listing.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}else{
    $level = $row['agent_coded_level'];
    include_once 'tmpl/system_resources_listing.inc.php';
}
?>
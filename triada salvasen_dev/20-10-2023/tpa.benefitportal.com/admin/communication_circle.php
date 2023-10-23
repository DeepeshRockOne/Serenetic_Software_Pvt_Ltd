<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(66);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Circles';
$breadcrumbes[1]['link'] = 'communication_circle.php';
$page_title = "Circle";

$is_deleted = checkIsset($_POST['is_deleted']); 
$id = checkIsset($_POST['id']); 
if(!empty($is_deleted) && $is_deleted == 'Y' && !empty($id)){
    $response['status'] = 'success';
    $checKDB = $pdo->selectOne("SELECT id from admin_circle where md5(id)=:id AND is_deleted='N'",array(":id"=>$id));

    if(!empty($checKDB['id'])){

        include_once dirname(__FILE__) .'/adminCircle.class.php';
        $adminCircle = new adminCircle();
        $adminCircle->deleteAdminCircle($checKDB['id']);
        $response['message'] = 'Circle Deleted Successfully!';
    }else{
        $response['message'] = 'Circle not Deleted!';
        $response['status'] = 'fail';

    }
    header("content-type: application/json");
    echo json_encode($response);
    exit;
}
$is_status = checkIsset($_POST['is_status']); 
if(!empty($is_status) && $is_status == 'Y' && !empty($id)){
    $response['status'] = 'success';
    $status = $_POST['status'];
    $checKDB = $pdo->selectOne("SELECT id,name from admin_circle where md5(id)=:id AND is_deleted='N'",array(":id"=>$id));

    if(!empty($checKDB['id'])){

        include_once dirname(__FILE__) .'/adminCircle.class.php';
        $adminCircle = new adminCircle();

        $updateArr = array(
            "name" => $checKDB['name'],
            'status' => $status,
        );
        $adminCircle->updateAdminCircleStatus($checKDB['id'],$updateArr);

        $response['message'] = 'Circle updated Successfully!';
    }else{
        $response['message'] = 'Circle not Updated!';
        $response['status'] = 'fail';

    }
    header("content-type: application/json");
    echo json_encode($response);
    exit;
}

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$sch_params = array();
$incr = '';
$SortBy = "ac.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;
$table_incr = '';

if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$circleName = isset($_GET['circleName']) ? $_GET['circleName'] : '';

$circleName = cleanSearchKeyword($circleName); 
   
if($is_ajaxed){
    if(!empty($circleName)){
        $sch_params[":circleName"] =  "%" . makeSafe($circleName) . "%";
        $incr.=' AND ( ac.name LIKE :circleName OR ass.display_id LIKE :circleName ) ';
        $table_incr.=" LEFT JOIN admin ass ON(ass.id=aac.admin_id) ";
    }
if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'communication_circle.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
    try {

        $sel_sql = "SELECT ac.* ,res.totalAdmin,a.fname,a.lname,a.display_id,MAX(acc.created_at) as last_message_at
        from admin_circle ac 
        JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N' AND aac.admin_id!=0)
        LEFT JOIN(
            (SELECT COUNT(id) as totalAdmin,circle_id FROM assigned_admin_circle WHERE is_deleted='N' GROUP BY circle_id)
        ) as res ON(res.circle_id=ac.id)
        LEFT JOIN admin a ON(a.id=ac.created_by_admin_id AND a.is_deleted='N')
        $table_incr
        LEFT JOIN admin_circle_chat acc ON(
                            acc.circle_id=ac.id AND 
                            acc.is_deleted='N'
                        )
        WHERE ac.is_deleted='N' $incr GROUP BY ac.id ORDER BY  $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);

        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }

    /*   * ****************    Export Code End ******************** */
    include_once 'tmpl/communication_circle.inc.php';
    exit;
}


$template = 'communication_circle.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

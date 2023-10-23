<?php
include_once __DIR__ . '/includes/connect.php';
agent_has_access(25);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Manage Websites";
$breadcrumbes[1]['class'] = "Active";
$agent_id = $_SESSION['agents']['id'];

if(isset($_POST['id']) && isset($_POST['operation'])) {
	$res = array();
	if($_POST['operation'] == "delete") {
		$query = "SELECT * FROM page_builder WHERE md5(id)=:id and is_deleted='N'";
        $pb_row = $pdo->selectOne($query,array(':id' => $_POST['id']));
        if(!empty($pb_row)) {
        	$up_params = array(
                'is_deleted' => 'Y',
                'updated_at' => 'mysqlfunc_NOW()'
            );
            $up_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $pb_row['id']
                )
            );
            $pdo->update('page_builder', $up_params, $up_where);

        	
            $up_where = array(
                'clause' => 'page_builder_id=:id',
                'params' => array(
                    ':id' => $pb_row['id']
                )
            );
            $pdo->update('page_builder_images', $up_params, $up_where);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                    'title' => $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => ' deleted Website ',
                'ac_red_2' => array(
                    'href' => 'javascript:void(0);',
                    'title' => $pb_row['page_name'],
                ),
            );
            $desc = json_encode($desc);
            activity_feed(3, $_SESSION['agents']['id'], 'Agent', $pb_row['id'], 'page_builder', 'Website Deleted', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
            $res['status'] = 'success';
			$res['msg'] = 'Website Deleted Successfully';
			echo json_encode($res);
			exit();
        }
	}
	if($_POST['operation'] == "change_publish_status") {
		$query = "SELECT * FROM page_builder WHERE md5(id)=:id and is_deleted='N'";
        $pb_row = $pdo->selectOne($query,array(':id' => $_POST['id']));
        if(!empty($pb_row)) {
        	$status = $_POST['status'];
        	$up_params = array(
                'status' => $status,
                'updated_at' => 'mysqlfunc_NOW()'
            );
            $up_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $pb_row['id']
                )
            );
            $pdo->update('page_builder', $up_params, $up_where);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                    'title' => $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => ' changed Website ',
                'ac_red_2' => array(
                    'href' => 'page_builder.php?id='.md5($pb_row['id']),
                    'href' => 'javascript:void(0);',
                    'title' => $pb_row['page_name'],
                ),
                'ac_message_2' => ' publish status from '.($pb_row['status'] == "Active"?"Published":"Unpublished").' to '.($status == "Active"?"Published":"Unpublished"),
            );
            $desc = json_encode($desc);
            activity_feed(3, $_SESSION['agents']['id'], 'Agent', $pb_row['id'], 'page_builder', 'Website Publish Status Changed', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
            
            $res['status'] = 'success';
			$res['msg'] = 'Website Publish Status Changed Successfully';
			echo json_encode($res);
			exit();
        }
	}
	$res['status'] = 'fail';
	$res['msg'] = 'Website not found';
	echo json_encode($res);
	exit();
}

$sch_params = array();
$incr = '';
$SortBy = "pb.created_at";
$SortDirection = "DESC";
$currSortDirection = "DESC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
    $has_querystring = true;
    $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
    $has_querystring = true;
    $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

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
    'url' => 'manage_website.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {
	try {
	    $sel_sql = "SELECT pb.*,md5(pb.id) as id
	              FROM page_builder pb
	              WHERE pb.is_deleted = 'N' AND pb.agent_id='" . $agent_id . "' " . $incr . " 
				  ORDER BY  $SortBy $currSortDirection";
	    $paginate = new pagination($page, $sel_sql, $options);

	    if ($paginate->success == true) {
	        $fetch_rows = $paginate->resultset->fetchAll();
	        $total_rows = count($fetch_rows);
	    }
	} catch (paginationException $e) {
	    echo $e;
	    exit();
	}
	include_once 'tmpl/manage_website.inc.php';
	exit;
}
$template = 'manage_website.inc.php';
include_once 'layout/end.inc.php';
?>
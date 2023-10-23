<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Providers";
$breadcrumbes[1]['link'] = 'providers.php';
$breadcrumbes[1]['class'] = "Active";
$page_title = "Providers";
$user_groups = "active";

$company_arr = get_active_global_products_for_filter();

if (isset($_GET['status']) && isset($_GET['pro_id'])) {
	$providers_id = $_GET['pro_id'];
	$pro_status = $_GET['status'];
	$providers_res = $pdo->selectOne("SELECT id, status, display_id FROM providers WHERE md5(id) = :providers_id", array(":providers_id" => $providers_id));

	if(!empty($providers_res)) {
		$updateSql = array('status' => makeSafe($pro_status));
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($providers_res['id'])));
		$provider_inc_id = $providers_res['id'];
		//************* Activity Code Start *************
		$oldVaArray = $providers_res;
		$NewVaArray = $updateSql;
		unset($oldVaArray['id']);

		$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
		if(!empty($checkDiff)){
			$activityFeedDesc['ac_message'] =array(
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
					'title'=>$_SESSION['admin']['display_id']),
				'ac_message_1' =>' Updated Provider ',
			); 
			
			$extraJson = array();
			foreach ($checkDiff as $key1 => $value1) {
				$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
			}

			$activityFeedDesc['ac_message']['ac_red_2']=array(
				'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($provider_inc_id),
				'title'=>$providers_res['display_id']
			); 
			
			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $provider_inc_id, 'provider','Admin Updated Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
		}
		//************* Activity Code End *************
		$pdo->update("providers", $updateSql, $where);
		setNotifySuccess("Providers status changed successfully!");
		redirect("providers.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("providers.php");
	}
}

if (isset($_GET['is_deleted']) && isset($_GET['pro_id'])) {
	$providers_id = $_GET['pro_id'];
	$providers_res = $pdo->selectOne("SELECT id,display_id FROM providers WHERE md5(id) = :providers_id", array(":providers_id" => $providers_id));

	if(!empty($providers_res)) {
		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($providers_res['id'])));
		$pdo->update("providers", $updateSql, $where);

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Deleted Providers ',
			'ac_red_2'=>array(
		  	'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($providers_res['id']),
		  	'title'=>$providers_res['display_id'],
			),
		); 

  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $providers_res['id'], 'provider','Deleted Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

		setNotifySuccess("Provider deleted successfully!");
		redirect("providers.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("providers.php");
	}
}

$sch_params = array();
$incr='';
$SortBy = "p.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

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
$provider_id = isset($_GET['provider_id']) ? $_GET['provider_id'] : '';
$provider_name = isset($_GET['provider_name']) ? $_GET['provider_name'] : '';
$provider_status = isset($_GET['provider_status']) ? $_GET['provider_status'] : '';
$join_range = isset($_GET['join_range']) ? $_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"]:"";
$todate = isset($_GET["todate"]) ? $_GET["todate"]:"";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"]:"";
$provider_product = isset($_GET['provider_product']) ? $_GET['provider_product'] : array();

$provider_id = cleanSearchKeyword($provider_id);
$provider_name= cleanSearchKeyword($provider_name); 
 
if (!empty($provider_id)) {
	$provider_id = str_replace(" ", "", $provider_id);
	$provider_id = explode(',', $provider_id);
	$provider_id = "'" . implode("','", $provider_id) . "'";
	$incr .= " AND p.display_id IN($provider_id)";
}

if (!empty($provider_name)) {
	$sch_params[":provider_name"] = "%" . makeSafe($provider_name) . "%";
	$incr .= " AND p.name LIKE :provider_name";
}

if(!empty($provider_status)){
	$sch_params[":provider_status"] = makeSafe($provider_status);
	$incr .= " AND p.status = :provider_status";	
}

if(!empty($join_range)){
  if($join_range == "Range" && !empty($fromdate) && !empty($todate)){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(p.created_at) >= :fromdate AND DATE(p.created_at) <= :todate";
  }else if($join_range == "Exactly" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.created_at) = :added_date";
  }else if($join_range == "Before" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.created_at) < :added_date";
  }else if($join_range == "After" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.created_at) > :added_date";
  }
}

if(!empty($provider_product)){
	if(count($provider_product) > 0){
		$incr .= " AND sp.product_id IN (" . implode(",", $provider_product) . ")";
	}
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
 
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
  
$options = array(
	'results_per_page' => $per_page,
	'url' => 'providers.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($is_ajaxed) {

	if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'provider_export'){
		if($fromdate !='' && $todate != '') {
        $no_days=0;
        if($fromdate != '' && $todate != '') {
            $date1 = date_create($fromdate);
            $date2 = date_create($todate);
            $diff = date_diff($date1,$date2);
            $no_days = $diff->format("%a");
        }
        if($no_days > 62) {
            echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
            exit();
        }
  	}

		$job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Provider Export","provider_export",$incr,$sch_params,'','provider_export');
		$reportDownloadURL = $AWS_REPORTING_URL['provider_export']."&job_id=".$job_id;
	    echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
	    exit;
	}

	try {
		$sel_sql = "SELECT p.id, p.display_id, p.name, p.status, p.created_at, count(DISTINCT (pm.id)) as prd_total
					FROM providers as p
					JOIN sub_provider as sp ON (p.id = sp.providers_id)
					LEFT JOIN prd_main pm ON(pm.id = sp.product_id AND pm.is_deleted = 'N')
					WHERE p.is_deleted='N' and sp.is_deleted='N' " . $incr . " 
					GROUP BY p.id ORDER BY $SortBy $currSortDirection";

		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/providers.inc.php';
	exit;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache, 'thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js');

$template = 'providers.inc.php';
include_once 'layout/end.inc.php';
?>
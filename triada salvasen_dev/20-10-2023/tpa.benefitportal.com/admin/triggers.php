<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Triggers';
$breadcrumbes[2]['link'] = 'triggers.php';


// get company code start
$companyRes = $pdo->select("SELECT id,company_name FROM prd_company WHERE is_deleted = 'N' ORDER BY company_name ASC");

$sch_params = array();
$incr = '';
$SortBy = "t.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (!empty($_GET["sort_by"])) {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (!empty($_GET["sort_direction"])) {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';


$triggerDispIds = !empty($_GET['triggerDispIds']) ? $_GET['triggerDispIds'] : '';
$searchRange = (isset($_GET["searchRange"])) ? $_GET["searchRange"] : "";
$addedDate = (isset($_GET["addedDate"])) ? $_GET["addedDate"] : "";
$fromDate = (isset($_GET["fromDate"])) ? $_GET["fromDate"] : "";
$toDate = (isset($_GET["toDate"])) ? $_GET["toDate"] : "";

$title = !empty($_GET["title"]) ? $_GET["title"] : '';
$userGroup = !empty($_GET["userGroup"]) ? $_GET["userGroup"] : array();
$company = !empty($_GET["company"]) ? $_GET["company"] : '';
$type = !empty($_GET["type"]) ? $_GET["type"] : array();
$statusSearch = !empty($_GET['statusSearch']) ? $_GET['statusSearch'] : '';
$subject = !empty($_GET['subject']) ? $_GET['subject'] : '';

$title = cleanSearchKeyword($title);
$subject = cleanSearchKeyword($subject);
$triggerDispIds = cleanSearchKeyword($triggerDispIds); 
 
// Search Code Start
	if (!empty($triggerDispIds)) {
	  $triggerDispIds = str_replace(" ", "", $triggerDispIds);
	  $triggerDispIds = explode(',', $triggerDispIds);
	  $triggerDispIds = "'" . implode("','", $triggerDispIds) . "'";
	  $incr.=" AND t.display_id IN ($triggerDispIds)";
	}

	if(!empty($searchRange)){
	  if($searchRange == "Range"){
	    $sch_params[':fromDate'] = date("Y-m-d",strtotime($fromDate));
	    $sch_params[':toDate'] = date("Y-m-d",strtotime($toDate));
	    $incr.=" AND DATE(t.created_at) >= :fromDate AND DATE(t.created_at) <= :toDate";
	  }else if($searchRange == "Exactly"){
	    $sch_params[':addedDate'] = date("Y-m-d",strtotime($addedDate));
	    $incr.=" AND DATE(t.created_at) = :addedDate";
	  }else if($searchRange == "Before"){
	    $sch_params[':addedDate'] = date("Y-m-d",strtotime($addedDate));
	    $incr.=" AND DATE(t.created_at) < :addedDate";
	  }else if($searchRange == "After"){
	    $sch_params[':addedDate'] = date("Y-m-d",strtotime($addedDate));
	    $incr.=" AND DATE(t.created_at) > :addedDate";
	  }
	}

	if (!empty($title)){
	  $sch_params[':title'] = "%" . makeSafe($title) . "%";
	  $incr .= " AND t.title LIKE :title";
	}

	if(!empty($userGroup)){
	 	$userGroupNames = "'" . implode("','", $userGroup) . "'";
	  	$incr.=" AND t.user_group IN ($userGroupNames)";
	}

	if(!empty($company)){
	  $sch_params[':company'] = makeSafe($company);
	  $incr .= " AND c.id = :company";
	}

	if(!empty($type)){
		$types = "'".implode("','", $type)."'";
	  $incr .= " AND t.type IN (".$types.")";
	}

	if (!empty($statusSearch)) {
	  $sch_params[':statusSearch'] = makeSafe($statusSearch);
	  $incr .= " AND t.status = :statusSearch";
	}
	if (!empty($subject)) {
	  // $sch_params[':statusSearch'] = makeSafe($statusSearch);
	  $incr .= " AND t.email_subject LIKE '%".$subject."%'";
	}

/* ------- Update trigger status code start --------- */
	if(isset($_GET['action']) && ($_GET['action'] == 'updStatus')){
	  $status = $_GET['status'];
	  $triggerId = $_GET['id'];
	  if (!empty($triggerId) && !empty($status)){
	    $triggerSql = "SELECT id,status,display_id FROM triggers WHERE md5(id)=:id AND is_deleted='N'";
	    $triggerParams = array(":id" => $triggerId);
	    $triggerRes = $pdo->selectOne($triggerSql,$triggerParams);

	    if(!empty($triggerRes['id'])){
		    $updateSql = array('status' => makeSafe($status));
		    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($triggerRes['id'])));
		    $pdo->update("triggers", $updateSql, $where);

		    
		     	/* ------- Update trigger status code start --------- */

			     $activityFeedDesc['ac_message'] =array(
	                    'ac_red_1'=>array(
	                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
	                      'title'=>$_SESSION['admin']['display_id']),
	                    'ac_message_1' =>' Updated Trigger ',
	                    'ac_red_2'=>array(
	                      'href'=>'manage_trigger.php?id='.$triggerId,
	                      'title'=>$triggerRes['display_id'],
	                    ),
	                  ); 

			     $activityFeedDesc['key_value']['desc_arr']['Status']='From '.$triggerRes['status'].' To '.$status;
           
                 activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerRes['id'], 'triggers','Admin updated Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
            //************* Activity Code End   *************
	    }
	    $response = array('status' => 'success');
	    echo json_encode($response);
	    exit();
	  }
	}
/* ------- Update trigger status code ends ---------- */


/* ------- Delete trigger code start --------- */
if(isset($_GET['action']) && ($_GET['action'] == 'delTrigger')){
  $triggerId = $_GET['id'];
  
  if (!empty($triggerId)){
    $triggerSql = "SELECT id,display_id FROM triggers WHERE md5(id)=:id AND is_deleted='N'";
    $triggerParams = array(":id" => $triggerId);
    $triggerRes = $pdo->selectOne($triggerSql,$triggerParams);
  
    if(!empty($triggerRes['id'])){
	    $updateSql = array('is_deleted' => "Y");
	    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($triggerRes['id'])));
	    $pdo->update("triggers", $updateSql, $where);

	    $description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' deleted trigger '.$triggerRes['display_id'],
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerRes['id'], 'triggers','Deleted Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    }
    setNotifySuccess("Trigger deleted successfully");
    redirect("triggers.php");
  }
}
/* ------- Delete trigger code ends --------- */


if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {

  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';


$options = array(
  'results_per_page' => $per_page,
  'url' => 'triggers.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);


if ($is_ajaxed) {

	if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_trigger'){

		if($fromDate !='' && $toDate != '') {
        $no_days=0;
        if($fromDate != '' && $toDate != '') {
            $date1 = date_create($fromDate);
            $date2 = date_create($toDate);
            $diff = date_diff($date1,$date2);
            $no_days = $diff->format("%a");
        }
        
        if($no_days > 62) {
            echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
            exit();
        }
  	}

		$job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Trigger Export","trigger_export",$incr,$sch_params,'','trigger_export');
	    $reportDownloadURL = $AWS_REPORTING_URL['trigger_export']."&job_id=".$job_id;
	    echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
	    exit;

	}

  try {
  
    $sel_sql = "SELECT md5(t.id) as id,t.display_id,t.created_at,t.title,t.user_group,c.company_name,t.type,t.status,t.is_default
                FROM triggers t
                LEFT JOIN prd_company c ON(t.company_id=c.id)
                WHERE t.is_deleted = 'N'" . $incr . " GROUP BY t.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

  include_once 'tmpl/triggers.inc.php';
  exit;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.maskedinput.min.js');

$page_title = "Email";
$template = 'triggers.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

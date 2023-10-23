<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Commissions";
$breadcrumbes[2]['link'] = 'payment_commissions.php';
$breadcrumbes[3]['title'] = "Regenerate";
$breadcrumbes[3]['link'] = 'regenerate_commissions.php';


$incr = "";
$sch_params = array();

$has_querystring = false;
$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$orderId = checkIsset($_GET['orderId']);
$agentIdName = checkIsset($_GET['agentIdName']);

  $join_range = checkIsset($_GET['join_range']);
  $fromdate = checkIsset($_GET["fromdate"]);
  $todate = checkIsset($_GET["todate"]);
  $added_date = checkIsset($_GET["added_date"]);

  if($join_range != ""){
    if($join_range == "Range" && $fromdate!='' && $todate!=''){
      $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
      $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
      $incr.=" AND DATE(rc.created_at) >= :fromdate AND DATE(rc.created_at) <= :todate";
    }else if($join_range == "Exactly" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(rc.created_at) = :added_date";
    }else if($join_range == "Before" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(rc.created_at) < :added_date";
    }else if($join_range == "After" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(rc.created_at) > :added_date";
    }
  }

$isJoin = false;
$joinIncr = "";

$orderId = cleanSearchKeyword($orderId);
$agentIdName = cleanSearchKeyword($agentIdName); 

if (!empty($orderId)) {
	$sch_params[':orderId'] = makeSafe($orderId);
	$incr .= " AND o.display_id = :orderId";
	$isJoin = true;
}

if (!empty($agentIdName)) {
	$sch_params[':agentIdName'] = "%" . makeSafe($agentIdName) . "%";
	$incr .= " AND (s.rep_id LIKE :agentIdName OR s.fname LIKE :agentIdName or s.lname LIKE :agentIdName or CONCAT(s.fname,s.lname) LIKE :agentIdName)";
	$isJoin = true;
}

if($isJoin){
	$joinIncr = "LEFT JOIN orders o ON FIND_IN_SET(o.id,rc.order_ids)
                LEFT JOIN customer c ON(o.customer_id=c.id)
                LEFT JOIN customer s ON(c.sponsor_id=s.id)";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'regenerate_commissions.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	
	// Read Commisssions Payables activity code start
	  $description['ac_message'] =array(
	    'ac_red_1'=>array(
	      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	      'title'=>$_SESSION['admin']['display_id'],
	    ),
	    'ac_message_1' => ' read regenerate commissions page',
	  ); 

	  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'regenerated_commission','Regenerate Commissions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	// Read Commisssions Payables activity code ends

	try {
		$sel_sql = "SELECT 
					rc.id,
					rc.created_at as regenerationDate,
					rc.commission_amount as commAmt,
					rc.status,
					IF(rc.order_ids != '',(CHAR_LENGTH(rc.order_ids) -
					CHAR_LENGTH(REPLACE(rc.order_ids, ',', '')) + 1),COUNT(DISTINCT(ro.order_id)))
					 as orderCounts,

					a.display_id as adminDispId,
					CONCAT(a.fname,' ',a.lname) as adminName
				FROM regenerated_commission rc
				JOIN admin a ON(rc.admin_id=a.id) 
				LEFT JOIN regenerated_order_commissions ro ON(rc.id=ro.request_id)
				$joinIncr
				WHERE rc.id > 0 AND rc.status!='Cancelled' $incr GROUP BY rc.id ORDER BY rc.id DESC";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/regenerate_commissions.inc.php';
	exit;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'regenerate_commissions.inc.php';
include_once 'layout/end.inc.php';
?>
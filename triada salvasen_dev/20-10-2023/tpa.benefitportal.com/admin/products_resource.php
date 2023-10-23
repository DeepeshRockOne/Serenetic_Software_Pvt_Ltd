<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Products";
$breadcrumbes[2]['title'] = "Resources";
$breadcrumbes[1]['link'] = 'products_resource.php';
$breadcrumbes[1]['class'] = "Active";
$page_title = "resources";
$user_groups = "active";

$selResource = "SELECT id,display_id 
				FROM resources 
				WHERE is_deleted='N'
				ORDER BY id DESC";
$resResource = $pdo->select($selResource);

$sch_params = array();
$incr='';
$SortBy = "r.created_at";
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

$res_id = checkIsset($_GET['res_id']);
$status = checkIsset($_GET['is_active']);
if(!empty($res_id) && !empty($status)){
	$old_status = checkIsset($_GET['old_status']);
	$update_param = array();
	$update_param['updated_at'] = 'msqlfunc_NOW()';
	$update_param['status'] = $status;
	$upd_where = array(
		'clause' => 'md5(id) = :id',
		'params' => array(
		  ':id' => $res_id,
		),
	  );
	$pdo->update('resources',$update_param,$upd_where);
	$res_rec = $pdo->selectOne('SELECT id,display_id from resources where md5(id)=:id and is_deleted="N"',array(":id"=>$res_id));
	$description['ac_message'] =array(
		'ac_red_1'=>array(
		  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		  'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' updated resource ',
		'ac_red_2'=>array(
			'href'=>$ADMIN_HOST.'/resource_add.php?resource_id='.md5($res_rec['id']),
			'title'=>$res_rec['display_id'],
		),
		'ac_message_2' =>' from resource '.$old_status.' to '.$status,
	  ); 
	  
	activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_rec['id'], 'resource_status_update','Resource Status Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	setNotifySuccess("Resource status changed successfully");
	redirect("products_resource.php");
}

$res_id = checkIsset($_GET['res_id']);
$is_deleted = checkIsset($_GET['is_deleted']);
if(!empty($res_id) && !empty($is_deleted)){
	$resource_res = $pdo->selectOne("SELECT id,display_id FROM resources where md5(id)=:id AND is_deleted='N'",array(":id"=>$res_id));
	if(!empty($resource_res)){
		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($resource_res['id'])));
		$pdo->update("resources", $updateSql, $where);

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Deleted Resources ',
			'ac_red_2'=>array(
			'href'=>$ADMIN_HOST.'/resource_add.php?resource_id='.md5($resource_res['id']),
		  	'title'=>$resource_res['display_id'],
			),
		); 

  		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resource_res['id'], 'resources','Deleted Resources', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

		setNotifySuccess("Resource deleted successfully!");
		redirect("products_resource.php");
	}else{
		setNotifyError("No record Found!");
		redirect("products_resource.php");
	}
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if ($is_ajaxed) {
$resource_ids = isset($_GET['resource_ids']) ? $_GET['resource_ids'] : array();
$resource_name = isset($_GET['resource_name']) ? $_GET['resource_name'] : '';
$resource_status = isset($_GET['resource_status']) ? $_GET['resource_status'] : '';
$join_range = isset($_GET['join_range']) ? $_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"]:"";
$todate = isset($_GET["todate"]) ? $_GET["todate"]:"";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"]:"";
$resource_product = isset($_GET['resource_product']) ? $_GET['resource_product'] : array();
$user_group = checkIsset($_GET['user_group']);
$resource_type = checkIsset($_GET['resource_type']) ? $_GET['resource_type'] : array();

$resource_name = cleanSearchKeyword($resource_name); 
 
if (!empty($resource_ids)) {
	$incr .= " AND r.id IN(".implode(',', $resource_ids).")";
}

if (!empty($resource_name)) {
	$sch_params[":resource_name"] = "%" . makeSafe($resource_name) . "%";
	$incr .= " AND r.name LIKE :resource_name";
}

if(!empty($resource_status)){
	$sch_params[":resource_status"] = makeSafe($resource_status);
	$incr .= " AND r.status = :resource_status";	
}
if(!empty($resource_type)){
	$resource_type = "'" . implode("','", $resource_type) . "'";
	$incr .= " AND r.type IN($resource_type)";
}
if(!empty($resource_product)){
	$incr .= " AND p.id IN (".implode(",",$resource_product).")";	
}
if(!empty($user_group)){
	$sch_params[':user_group'] = $user_group;
	$incr .= " AND user_group = :user_group";
}

if(!empty($join_range)){
  if($join_range == "Range" && !empty($fromdate) && !empty($todate)){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(r.created_at) >= :fromdate AND DATE(r.created_at) <= :todate";
  }else if($join_range == "Exactly" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(r.created_at) = :added_date";
  }else if($join_range == "Before" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(r.created_at) < :added_date";
  }else if($join_range == "After" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(r.created_at) > :added_date";
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
	'url' => 'products_resource.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

	if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_resource'){

		require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
		require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

		$objPHPExcel = new PHPExcel();
		$index = 0;

		$sel_sql = "SELECT  display_id, r.name as name, r.type, user_group, effective_date AS active_date, 
		COUNT(DISTINCT (pm.id)) AS prd_total,r.status,GROUP_CONCAT(pm.name) AS product_names, r.created_at FROM resources r
		LEFT JOIN res_products rp ON(rp.res_id=r.id)
		LEFT JOIN prd_main pm ON(pm.id=rp.product_id) WHERE r.is_deleted='N' AND pm.is_deleted='N' " . $incr . " GROUP BY r.id ORDER BY $SortBy $currSortDirection";

		$res_resource = $pdo->select($sel_sql,$sch_params);

		$objPHPExcel->setActiveSheetIndex($index);
		$index++;

		$objPHPExcel->createSheet();

		$objPHPExcel->getActiveSheet()->setTitle("Admins");

		$i = 0;
		/*-------- Heading ----------*/
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Resource Id');
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Created At');
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'Resource Name');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'User Group');
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'Active Date');
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, 'Products total');
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 'Products name');
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'Resource Type');
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, 'Status');

		$i++;
		if (!empty($res_resource)) {
			foreach ($res_resource as $res) {
			
				$created_date=date('m-d-Y',strtotime($res['created_at']));
				$res_type = $res['type'] == 'id_card' ? 'Id Card' : $res['type'] ;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $res['display_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $created_date);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $res['name']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $res['user_group']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $res['active_date']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $res['prd_total']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $res['product_names']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $res_type);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $res['status']);
				$i++;
			}

		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"No record found.");
		}

		ob_start();
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$objWriter->save("php://output");
		$xlsData = ob_get_contents();
		ob_end_clean();

		$response =  array(
			'op' => 'ok',
			'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
		);
		die(json_encode($response));
	}
	try {
		$sel_sql = "SELECT md5(r.id) as id, r.display_id, r.name, r.type, user_group, effective_date as active_date,termination_date, rp2.prd_total,r.status, r.created_at 
				FROM resources r 
				LEFT JOIN res_products rp ON(rp.res_id=r.id) 
				LEFT JOIN (
					SELECT count(DISTINCT (rp2.product_id)) as prd_total,rp2.res_id FROM res_products rp2 JOIN prd_main p ON(p.id=rp2.product_id AND p.is_deleted='N' AND p.status IN('Active','Suspended')) GROUP BY rp2.res_id 
				) rp2 ON(rp2.res_id = rp.res_id)
				LEFT JOIN prd_main p ON(p.id = rp.product_id AND p.is_deleted = 'N') 
				WHERE r.is_deleted='N' " . $incr . " 
				GROUP BY r.id 
				ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/products_resource.inc.php';
	exit;
}

$product_res = get_active_global_products_for_filter();

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js');

$template = 'products_resource.inc.php';
include_once 'layout/end.inc.php';
?>
<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(10);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Book of Business';
$breadcrumbes[2]['title'] = 'Leads';
$breadcrumbes[2]['link'] = 'lead_listing.php';
$page_title = "Leads";

$agent_id = $_SESSION['agents']['id'];

// $lead_tag_res = get_lead_tags();
$lead_tag_res = $pdo->select("SELECT id,opt_in_type as tag from leads where is_deleted='N' GROUP BY opt_in_type");

$sch_params = array();
$incr = '';
$SortBy = "l.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$sponsor_incr = "";

if($agent_id){
    $displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
    if(!empty($displayDirectEnroll) && in_array('Leads', $displayDirectEnroll)){
        $sponsor_agent_ids = get_direct_loa_agents($agent_id,true);
    } else {
        $sponsor_agent_ids = get_downline_agents($agent_id,true);
    }
    //pre_print($sponsor_agent_ids);
    $sponsor_incr .= " AND l.sponsor_id IN($sponsor_agent_ids)";
}

$subAgent = checkIsset($_SESSION['agents']['is_sub_agent']);
$subAgentId = !empty($_SESSION['agents']['sub_agent_id']) ? $_SESSION['agents']['sub_agent_id'] : 0;

$resSubAgent = array();
if($subAgent == "Y" && !empty($subAgentId)){
    $selSubAgent =  "SELECT id FROM sub_agent WHERE id=:id AND is_deleted='N' AND edit_enrollment='Y'";
    $resSubAgent = $pdo->selectOne($selSubAgent,array(":id" => $subAgentId));
}

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
$lead_id = isset($_GET['lead_id']) ? $_GET['lead_id'] : array();
$lead_name = isset($_GET['lead_name']) ? $_GET["lead_name"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$lead_type = isset($_GET['lead_type']) ? $_GET["lead_type"] : '';
$lead_tag = isset($_GET['lead_tag']) ? $_GET["lead_tag"] : array();
$leads_status = isset($_GET['leads_status']) ? $_GET['leads_status'] : array();

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"] : "";
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();

$lead_name = cleanSearchKeyword($lead_name);
$email = cleanSearchKeyword($email);
$phone = cleanSearchKeyword($phone); 
 
if (!empty($lead_id)) {
    $lead_id = "'" . implode("','", makeSafe($lead_id)) . "'";
    $incr .= " AND l.lead_id IN ($lead_id)";
}

if ($lead_name) {
    $sch_params[':lead_name'] = "%" . makeSafe($lead_name) . "%";
    $incr .= " AND (l.fname LIKE :lead_name OR l.lname LIKE :lead_name OR CONCAT(l.fname,' ',l.lname) LIKE :lead_name)";
}

if ($email != "") {
    $sch_params[':email'] = "%" . makeSafe($email) . "%";
    $incr .= " AND l.email LIKE :email";
}

if ($phone != "") {
    $sch_params[':phone'] = "%" . makeSafe($phone) . "%";
    $incr .= " AND l.cell_phone LIKE :phone";
}

if ($lead_type != "") {
    $sch_params[':lead_type'] = "%" . makeSafe($lead_type) . "%";
    $incr .= " AND l.lead_type LIKE :lead_type";
}

if(!empty($leads_status)) {
    $leads_status = "'" . implode("','", makeSafe($leads_status)) . "'";
    $incr .= " AND l.status IN ($leads_status)";
}

if (!empty($lead_tag)) {
    $lead_tag = "'" . implode("','", makeSafe($lead_tag)) . "'";
    $incr .= " AND l.opt_in_type IN ($lead_tag)";
}

if ($join_range != "") {
    if ($join_range == "Range" && $fromdate != '' && $todate != '') {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(l.created_at) >= :fromdate AND DATE(l.created_at) <= :todate";
    } else if ($join_range == "Exactly" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(l.created_at) = :added_date";
    } else if ($join_range == "Before" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(l.created_at) < :added_date";
    } else if ($join_range == "After" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(l.created_at) > :added_date";
    }
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
    'url' => 'lead_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {

    if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_lead'){

        require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
        require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

        $objPHPExcel = new PHPExcel();
        $index = 0;

        $sel_sql = "SELECT l.*,md5(l.id) as id,s.rep_id as sponsor_rep_id,CONCAT(s.fname,' ',s.lname) as sponsor_name,s.business_name
                  FROM leads l
                  JOIN customer s ON (s.id = l.sponsor_id)
                  WHERE l.is_deleted = 'N' $sponsor_incr " . $incr . " 
                  ORDER BY  $SortBy $currSortDirection";

        $lead_res = $pdo->select($sel_sql,$sch_params);

        $objPHPExcel->setActiveSheetIndex($index);
        $index++;

        $objPHPExcel->createSheet();

        $objPHPExcel->getActiveSheet()->setTitle("Leads");

        $i = 0;
        /*-------- Heading ----------*/
        $i++;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Lead Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Added Date');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, 'Lead Type');
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 'Added By ID');
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'Added By Name');
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, 'Lead Tag');
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, 'Status');

        $i++;
        if (!empty($lead_res)) {
            foreach ($lead_res as $lead_row) {            
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $lead_row['lead_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, date('m/d/Y',strtotime($lead_row['created_at'])));
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $lead_row['fname']." ".$lead_row['lname']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $lead_row['cell_phone']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $lead_row['email']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $lead_row['lead_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $lead_row['sponsor_rep_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $lead_row['sponsor_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $lead_row['opt_in_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $lead_row['status']);
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

        $description = array();
        $description['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' => ' Created lead export file',
        );
        $desc = json_encode($description);
        activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Created Lead Export File', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

        die(json_encode($response));
    }

    try {
        $sel_sql = "SELECT l.*,md5(l.id) as id,s.business_name,s.rep_id as spnsorRepId,CONCAT(s.fname,' ',s.lname) as sponsorName,c.status as customer_status,ss.agent_coded_level
                  FROM leads l
                  LEFT JOIN customer c ON (c.id = l.customer_id)
                  JOIN customer s ON (s.id = l.sponsor_id)
                  LEFT JOIN customer_settings ss ON (s.id = ss.customer_id)
                  WHERE l.is_deleted = 'N' $sponsor_incr " . $incr . " 
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
    include_once 'tmpl/lead_listing.inc.php';
    exit;
}

$lead_sql = "SELECT l.id,l.lead_id,l.fname,l.lname FROM leads l WHERE l.is_deleted = 'N' $sponsor_incr";
$lead_res = $pdo->select($lead_sql);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);
$template = 'lead_listing.inc.php';
include_once 'layout/end.inc.php';
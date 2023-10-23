<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(8);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Members";
$breadcrumbes[2]['link'] = 'member_listing.php';
$breadcrumbes[3]['title'] = "Members Interactions";
$breadcrumbes[3]['link'] = 'member_interactions.php';

$sch_params = array();
$incr = '';
$SortBy = "id.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;

$agent_id = $_SESSION['agents']['id'];

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if ($is_ajaxed) {

    
    $join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
    $fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
    $todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
    $added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : "";

    $enroll_agent = isset($_GET['enroll_agent']) ? $_GET['enroll_agent'] : array();
    $member_id = isset($_GET['member_id']) ? $_GET['member_id'] : array();
    $agency_name = isset($_GET['agency_name']) ? $_GET['agency_name'] : array();
    $interaction_type = isset($_GET['interaction_type']) ? $_GET['interaction_type'] : array();

    if (!empty($enroll_agent)) {
        $incr .= " AND s.id IN (".implode(',',$enroll_agent).") ";
    }

    if (!empty($member_id)) {
        $incr .= " AND c.id IN (".implode(',',$member_id).") ";
    }

    if (!empty($agency_name)) {
        $agenct_ids = implode(',', makeSafe($agency_name));
        $incr .= " AND s.id IN ($agenct_ids)";
    }

    if(!empty($agent_id)){
        $sch_params[':sponsor_id'] = '%,' . $agent_id . ',%';
        $sch_params[':s_id'] = $agent_id;
        $incr .= " AND (c.sponsor_id=:s_id OR c.upline_sponsors LIKE :sponsor_id) ";
    }

    if (!empty($interaction_type)) {
        $incr .= " AND i.id IN (".implode(',',$interaction_type).") ";
    }

    if ($join_range != "") {
        if ($join_range == "Range" && $fromdate != '' && $todate != '') {
            $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
            $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
            $incr .= " AND DATE(id.created_at) >= :fromdate AND DATE(id.created_at) <= :todate";
        } else if ($join_range == "Exactly" && $added_date != '') {
            $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
            $incr .= " AND DATE(id.created_at) = :added_date";
        } else if ($join_range == "Before" && $added_date != '') {
            $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
            $incr .= " AND DATE(id.created_at) < :added_date";
        } else if ($join_range == "After" && $added_date != '') {
            $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
            $incr .= " AND DATE(id.created_at) > :added_date";
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
        'url' => 'member_interactions.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );

    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);
    $incr = isset($incr) ? $incr : '';

    try {

        $sel_sql = "SELECT 
                    id.id as intId,i.type as intType,id.created_at,id.description,
                    c.id as mid,c.rep_id as memberId,CONCAT(c.fname,' ',c.lname) as memberName,c.cell_phone,c.email,
                    s.rep_id as agentId,CONCAT(s.fname,' ',s.lname) as agentName,
                    a.display_id as adminId,CONCAT(a.fname,' ',a.lname) as adminName
                    FROM interaction_detail id
                    JOIN interaction i ON(i.id=id.interaction_id and i.user_type='member' and i.is_deleted='N')
                    JOIN customer c ON(c.id=id.user_id AND c.is_deleted='N')
                    LEFT JOIN customer s ON(s.id=c.sponsor_id AND s.is_deleted='N')
                    LEFT JOIN admin a ON(a.id=id.admin_id AND a.is_deleted='N')
                    WHERE id.is_deleted='N' AND id.is_claim='N' $incr
                    GROUP BY id.id
                    ORDER BY  $SortBy $SortDirection, c.id DESC";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    include_once 'tmpl/member_interactions.inc.php';
    exit;
}

$enroll_agent_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as name,
                    IF(c.type='Agent',cs.company_name,c.business_name) as agencyName
                    FROM customer c
                    JOIN customer cus ON(cus.sponsor_id=c.id AND cus.type='customer')
                    JOIN customer_settings cs ON(cs.customer_id=c.id)
                    JOIN interaction_detail id ON(id.user_id=cus.id AND id.is_deleted='N' AND id.is_claim='N')
                    WHERE c.type IN('Agent','Group') AND c.is_deleted = 'N' AND (c.id=:agentId OR (c.upline_sponsors LIKE('%,".$agent_id.",%'))) GROUP BY c.id ORDER BY c.fname, c.lname";
$enroll_agent_res = $pdo->select($enroll_agent_sql,array(":agentId" => $agent_id));

$agency_sql = "SELECT 
                    GROUP_CONCAT(c.id) as repIds,
                    IF(c.type='Agent',cs.company_name,c.business_name) as agencyNameDis
                    FROM customer c
                    JOIN customer_settings cs ON(cs.customer_id=c.id)
                    WHERE c.type IN('Agent','Group') AND c.is_deleted = 'N' AND (c.id=:agentId OR (c.upline_sponsors LIKE('%,".$agent_id.",%'))) GROUP BY agencyNameDis ORDER BY agencyNameDis ASC";
$agency_res = $pdo->select($agency_sql,array(":agentId" => $agent_id));

$memberRes = $pdo->select("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name 
                    FROM customer c 
                    JOIN interaction_detail id ON(id.user_id=c.id AND id.is_deleted='N' AND id.is_claim='N') 
                    WHERE c.type='customer' and c.is_deleted='N' AND (sponsor_id=:agentId OR (upline_sponsors LIKE('%,".$agent_id.",%'))) GROUP BY c.id",array(":agentId" => $agent_id));
                    
$interactionrRes = $pdo->select("SELECT id,type FROM interaction WHERE user_type='member' AND is_deleted='N' order by type ASC");

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'member_interactions.inc.php';
include_once 'layout/end.inc.php';
?>

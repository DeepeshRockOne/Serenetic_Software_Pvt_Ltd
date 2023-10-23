<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(8);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Members";
$breadcrumbes[2]['link'] = 'member_listing.php';
$agent_id = $_SESSION['agents']['id'];

$sch_params = array();
$incr = '';
$tbl_incr = '';
$having = "";
$tble_incr = "";
$agnt_prd = "";
$license_incr = "";
$qry_incr = '';
$sponsorSetting = "";
$SortBy = "c.joined_date";
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
$rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : array();
$name = isset($_GET['name']) ? $_GET["name"] : '';
$cell_phone = isset($_GET['cell_phone']) ? $_GET["cell_phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$status = isset($_GET['status']) ? $_GET['status'] : array();
$state = isset($_GET['state']) ? $_GET['state'] : array();
$products = isset($_GET['products']) ? $_GET['products'] : array();
$product_status = isset($_GET['product_status']) ? $_GET['product_status'] : array();
$tree_agent_id = isset($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : array();
$enroll_agent = isset($_GET['enroll_agent']) ? $_GET['enroll_agent'] : array();
$dependent_id = isset($_GET['dependent_id']) ? $_GET['dependent_id'] : '';
$sponsor_id = isset($_GET['sponsor_id']) ? $_GET['sponsor_id'] : '';

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewMember = !empty($_GET["viewMember"]) ? $_GET["viewMember"] : 'todayMember';
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();

if($viewMember == "todayMember" && empty($join_range)){
  //$join_range = "Exactly";
}

if ($is_ajaxed) {
$effective_join_range = isset($_GET['effective_join_range']) ? $_GET['effective_join_range'] : "";
$effective_from_date = isset($_GET["effective_from_date"]) ? $_GET["effective_from_date"] : "";
$effective_to_date = isset($_GET["effective_to_date"]) ? $_GET["effective_to_date"] : "";
$effective_date = isset($_GET["effective_date"]) ? $_GET["effective_date"] : "";

$billing_join_range = isset($_GET['billing_join_range']) ? $_GET['billing_join_range'] : "";
$billing_from_date = isset($_GET["billing_from_date"]) ? $_GET["billing_from_date"] : "";
$billing_to_date = isset($_GET["billing_to_date"]) ? $_GET["billing_to_date"] : "";
$billing_date = isset($_GET["billing_date"]) ? $_GET["billing_date"] : "";

$select_alert = checkIsset($_GET['select_alert']);
$extra_join = "";

$rep_id = cleanSearchKeyword($rep_id);
$name = cleanSearchKeyword($name);
$email = cleanSearchKeyword($email);
$cell_phone = cleanSearchKeyword($cell_phone); 
$dependent_id = cleanSearchKeyword($dependent_id); 
 
if (isset($_SESSION['company_id']) && $_SESSION['company_id'] != "") {
    $sch_params[':company_id'] = makeSafe($_SESSION['company_id']);
    $incr .= " AND c.company_id = :company_id";
}

if (!empty($rep_id)) {
    $rep_id = explode(',', trim($rep_id));
    $rep_id = array_map('trim', $rep_id);
    $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
    $incr .= " AND c.rep_id IN ($rep_id)";
}

if ($name) {
    $sch_params[':name'] = "%" . makeSafe($name) . "%";
    $incr .= " AND ((c.fname LIKE :name or c.lname LIKE :name) or (CONCAT(c.fname,' ',c.lname) like :name)) ";
}

if ($email != "") {
    $sch_params[':email'] = "%" . makeSafe($email) . "%";
    $incr .= " AND c.email LIKE :email";
}

if ($cell_phone != "") {
    $sch_params[':cell_phone'] = "%" . makeSafe($cell_phone) . "%";
    $incr .= " AND c.cell_phone LIKE :cell_phone";
}

if (!empty($status)) {
    if(in_array('Active',$status)) {
        // $status[] = 'On Hold Failed Billing';
    }
    if(in_array('Pending',$status)) {
        $status[] = 'Pending';
        $status[] = 'Post Payment';
    }
    if(in_array('Inactive',$status)) {
        // $status[] = 'Inactive Failed Billing';
        // $status[] = 'Inactive Member Chargeback';
    }
    $status = "'" . implode("','", makeSafe($status)) . "'";
    $incr .= " AND c.status IN ($status)";
}

if (!empty($state)) {
    $state = "'" . implode("','", makeSafe($state)) . "'";
    $incr .= " AND c.state IN ($state)";
}

if (!empty($products)) {
    $products = "'" . implode("','", makeSafe($products)) . "'";
    $incr .= " AND (p.id IN ($products) OR p.parent_product_id IN ($products))";
}

if (!empty($product_status)) {
    $product_status = get_policy_db_status($product_status);
    $product_status = "'" . implode("','", makeSafe($product_status)) . "'";
    $incr .= " AND ws.status IN ($product_status)";
}

if (!empty($enroll_agent)) {
    $enroll_agent = "'" . implode("','", makeSafe($enroll_agent)) . "'";
    $incr .= " AND c.sponsor_id IN ($enroll_agent)";
}
if (!empty($sponsor_id)) {
    $incr .= " AND md5(c.sponsor_id) = '".$sponsor_id."'";
}

if(!empty($tree_agent_id)){
    $incr .= " AND (s.id IN(".implode(",",$tree_agent_id).") OR (s.sponsor_id IN (".implode(",",$tree_agent_id).") AND scs.agent_coded_id = 1)) ";
}

// if (!empty($tree_agent_id)) {
//     if (count($tree_agent_id) > 0) {
//         $incr .= " AND (";
//         foreach ($tree_agent_id as $key => $value) {
//             if (end($tree_agent_id) == $value) {
//                 $incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
//             } else {
//                 $incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
//             }
//         }
//         $incr .= ")";
//     }
// }

if (!empty($dependent_id)) {
    $dependent_id = explode(',', trim($dependent_id));
    $dep_arr = $dependent_id = array_map('trim', $dependent_id);
    $dependent_id = "'" . implode("','", makeSafe($dependent_id)) . "'";
    $extra_join .= " LEFT JOIN customer_dependent_profile as cdp on(cdp.customer_id=c.id AND cdp.is_deleted='N')";
    $incr .= " AND ( cdp.display_id IN ($dependent_id)";

    foreach($dep_arr as $key => $val){
        $sch_params[':dfname'.$key] = "%" . makeSafe($val) . "%";
        $incr .=" OR ( cdp.fname LIKE :dfname".$key." OR cdp.lname LIKE :dfname".$key.") ";
    }

    $incr .=" ) ";
}

if ($join_range != "") {
    if ($join_range == "Range" && $fromdate != '' && $todate != '') {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(c.joined_date) >= :fromdate AND DATE(c.joined_date) <= :todate";
    } else if ($join_range == "Exactly" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) = :added_date";
    } else if ($join_range == "Before" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) < :added_date";
    } else if ($join_range == "After" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) > :added_date";
    }
}

if ($effective_join_range != "") {
    if ($effective_join_range == "Range" && $effective_from_date != '' && $effective_to_date != '') {
        $sch_params[':effective_from_date'] = date("Y-m-d", strtotime($effective_from_date));
        $sch_params[':effective_to_date'] = date("Y-m-d", strtotime($effective_to_date));
        $incr .= " AND DATE(ws.eligibility_date) >= :effective_from_date AND DATE(ws.eligibility_date) <= :effective_to_date";
    } else if ($effective_join_range == "Exactly" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) = :effective_date";
    } else if ($effective_join_range == "Before" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) < :effective_date";
    } else if ($effective_join_range == "After" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) > :effective_date";
    }
}

if ($billing_join_range != "") {
    if ($billing_join_range == "Range" && $billing_from_date != '' && $billing_to_date != '') {
        $sch_params[':billing_from_date'] = date("Y-m-d", strtotime($billing_from_date));
        $sch_params[':billing_to_date'] = date("Y-m-d", strtotime($billing_to_date));
        $incr .= " AND DATE(ws.next_purchase_date) >= :billing_from_date AND DATE(ws.next_purchase_date) <= :billing_to_date";
    } else if ($billing_join_range == "Exactly" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) = :billing_date";
    } else if ($billing_join_range == "Before" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) < :billing_date";
    } else if ($billing_join_range == "After" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) > :billing_date";
    }
}
if(!empty($displayDirectEnroll) && in_array('Members', $displayDirectEnroll)){
    $incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
    $sch_params[':agent_id']=$agent_id;
}else{
    $incr .= " AND c.upline_sponsors LIKE '%,$agent_id,%' ";
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
    'url' => 'member_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
    try {

        $sel_sql = "SELECT $qry_incr
                    c.rep_id,
                    c.joined_date,
                    md5(c.id) as id,
                    c.fname, 
                    c.lname, 
                    c.email, 
                    c.status,
                    c.cell_phone,
                    IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as sponsor_name,
                    md5(c.sponsor_id) as sponsor_id,
                    s.rep_id as sponsor_rep_id,
                    p2.total_products,
                    AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,scs.company
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN customer_settings scs ON(scs.customer_id=s.id)
                  LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                  LEFT JOIN (
                    SELECT COUNT(DISTINCT p2.id) as total_products,ws2.customer_id
                    FROM website_subscriptions ws2
                    JOIN prd_main p2 ON(p2.id=ws2.product_id and p2.is_deleted='N' AND p2.type!='Fees')
                    GROUP BY ws2.customer_id
                  ) p2 ON(p2.customer_id = c.id)
                  LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                  $extra_join
                  WHERE c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending')
                  " . $incr . " 
                  GROUP BY c.id 
                  ORDER BY  $SortBy $SortDirection";
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
    include_once 'tmpl/member_listing.inc.php';
    exit;
}

// $tree_agent_sql = "SELECT id,rep_id,fname,lname
// 					FROM customer 
// 					where type='Agent' AND is_deleted = 'N' 
//                     AND (id=:agentId OR (upline_sponsors LIKE('%,".$agent_id.",%'))) ORDER BY fname, lname";
// $tree_agent_res = $pdo->select($tree_agent_sql,array(":agentId" => $agent_id));

$enroll_agent_sql = "SELECT id,rep_id,fname,lname,IF(type='Group',business_name,CONCAT(fname,' ',lname)) as name
                    FROM customer 
                    WHERE type IN('Agent','Group') AND is_deleted = 'N' AND (id=:agentId OR (upline_sponsors LIKE('%,".$agent_id.",%'))) ORDER BY fname, lname";
$enroll_agent_res = $pdo->select($enroll_agent_sql,array(":agentId" => $agent_id));

$filter_prd_res = get_active_global_products_for_filter($_SESSION['agents']['id']);

$select_state = "SELECT * FROM `states_c` WHERE country_id in(:id) order by name ASC";
$states = $pdo->select($select_state, array(":id" => 231));

$ActiveTotal = 0;
$PendingTotal = 0;
$InactiveTotal = 0;
$HoldTotal = 0;
$tmpIncr = "";
$tmpSchparams = array();
$tmpSponsorSetting = "";

if(!empty($displayDirectEnroll) && in_array('Members', $displayDirectEnroll)){
    $tmpSponsorSetting = " LEFT JOIN customer s ON (s.id = c.sponsor_id) LEFT JOIN customer_settings scs ON (scs.customer_id = s.id)";
    $tmpIncr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
    $tmpSchparams[':agent_id']=$agent_id;
}else{
    $tmpIncr .= " AND c.upline_sponsors LIKE '%,$agent_id,%' ";
}
$member_summery_sql = "SELECT c.status,count(c.status) as total_status 
            FROM customer c
            ".$tmpSponsorSetting."
            WHERE c.type='Customer' AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending') ".$tmpIncr." GROUP BY c.status";
$member_summery_res = $pdo->select($member_summery_sql,$tmpSchparams);
if(!empty($member_summery_res)){
	foreach($member_summery_res as $summary){
        $display_status = get_member_display_status($summary['status']);
		if($display_status == "Active") {
            $ActiveTotal += $summary['total_status'];
        }
        // if($display_status == "Pending") {
        //     $PendingTotal += $summary['total_status'];
        // }
        if($display_status == "Inactive") {
            $InactiveTotal += $summary['total_status'];
        }
        if($display_status == "Hold") {
            $HoldTotal += $summary['total_status'];
        }
	}
}
$member_summery_arr = array(
    'Active' => $ActiveTotal,
    // 'Pending' => $PendingTotal,
    'Inactive' => $InactiveTotal,
    'Hold' => $HoldTotal,
);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'member_listing.inc.php';
include_once 'layout/end.inc.php';
?>

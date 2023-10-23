<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
$module_access_type = has_access(8);

/* notification code start */
$function = new functionsList();
if (isset($_REQUEST["noti_id"])) {
    openAdminNotification($_REQUEST["noti_id"]);
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "User Groups";
$breadcrumbes[2]['title'] = "Members";
$breadcrumbes[2]['link'] = 'member_listing.php';
$page_title = "Members";
$user_groups = "active";

$sch_params = array();
$incr = '';
$SortBy = "c.joined_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$extra_export_arr = array();
$report_sch_param = array();
$report_incr = '';

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
// $rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : array();
$rep_id = !empty($_GET['rep_id']) ? explode(",", $_GET['rep_id']) : "";
$name = isset($_GET['name']) ? $_GET["name"] : '';
$cell_phone = isset($_GET['cell_phone']) ? $_GET["cell_phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$status = isset($_GET['status']) ? $_GET['status'] : array();
$state = isset($_GET['state']) ? $_GET['state'] : array();
$products = isset($_GET['products']) ? $_GET['products'] : array();
$product_status = isset($_GET['product_status']) ? $_GET['product_status'] : array();
$tree_agent_id = isset($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : array();
$enroll_agent = !empty($_GET['enroll_agent']) ? explode(",", $_GET['enroll_agent']) : "";
$enr_agent = isset($_GET['enr_agent']) ? $_GET['enr_agent'] : '';
$dependent_id = isset($_GET['dependent_id']) ? $_GET['dependent_id'] : '';

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewMember = !empty($_GET["viewMember"]) ? $_GET["viewMember"] : 'todayMember';

if($viewMember == "todayMember" && empty($join_range)){
  //$join_range = "Exactly";
}
$agentID ="";
if(!empty($enr_agent)){
    $query_sql = $pdo->selectOne("SELECT id FROM customer WHERE rep_id = :id",array(':id'=>$enr_agent));
    $agentID = isset($query_sql['id']) ? $query_sql['id'] : "";
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
if (isset($_SESSION['company_id']) && $_SESSION['company_id'] != "") {
    $sch_params[':company_id'] = makeSafe($_SESSION['company_id']);
    $incr .= " AND c.company_id = :company_id";
}


if(!empty($rep_id)){
    $rep_id = array_map('trim',$rep_id);
    $incr .= " AND c.rep_id IN ('".implode("','",$rep_id)."')";
    $report_incr .= " AND res.PRIMARY_MEMBER_ID IN ('".implode("','",$rep_id)."') ";
}

// if (!empty($rep_id)) {
//     $rep_id = explode(',', trim($rep_id));
//     $rep_id = array_map('trim', $rep_id);
//     $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
//     $incr .= " AND c.rep_id IN ($rep_id)";
//     $report_incr .= " AND res.PRIMARY_MEMBER_ID IN ($rep_id) ";
// }

$name = cleanSearchKeyword($name);
$email = cleanSearchKeyword($email);
$cell_phone = cleanSearchKeyword($cell_phone);
$dependent_id = cleanSearchKeyword($dependent_id); 
 
if ($name) {
    $sch_params[':name'] = "%" . makeSafe($name) . "%";
    $incr .= " AND ((c.fname LIKE :name or c.lname LIKE :name) or (CONCAT(c.fname,' ',c.lname) like :name)) ";

    $report_sch_param[':name'] = "%" . makeSafe($name) . "%";
    $report_incr .= " AND ((res.cfname LIKE :name or res.clname LIKE :name) or (CONCAT(res.cfname,' ',res.clname) like :name)) ";
}

if ($email != "") {
    $sch_params[':email'] = "%" . makeSafe($email) . "%";
    $incr .= " AND c.email LIKE :email";

    $report_sch_param[':email'] = "%" . makeSafe($email) . "%";
    $report_incr .= " AND res.EMAIL LIKE :email";
}

if ($cell_phone != "") {
    $sch_params[':cell_phone'] = "%" . makeSafe($cell_phone) . "%";
    $incr .= " AND c.cell_phone LIKE :cell_phone";

    $report_sch_param[':cell_phone'] = "%" . makeSafe($cell_phone) . "%";
    $report_incr .= " AND res.PHONE LIKE :cell_phone";
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

    $report_incr .= " AND res.cstatus IN ($status) ";
}

if (!empty($state)) {
    $state = "'" . implode("','", makeSafe($state)) . "'";
    $incr .= " AND c.state IN ($state)";

    $report_incr .= " AND res.state IN ($state) ";
}

if (!empty($products)) {
    $products = "'" . implode("','", makeSafe($products)) . "'";
    $incr .= " AND (p.id IN ($products) OR p.parent_product_id IN ($products))";

    $report_incr .= " AND (prd.id IN ($products) OR prd.parent_product_id IN ($products))";
}

if (!empty($product_status)) {
    $product_status = get_policy_db_status($product_status);
    $product_status = "'" . implode("','", makeSafe($product_status)) . "'";
    $incr .= " AND ws.status IN ($product_status)";
    $report_incr .= " AND w.status IN ($product_status) ";
}

 if(!empty($enroll_agent)){
    $enroll_agent = array_map('trim',$enroll_agent);
    $incr .= " AND c.sponsor_id IN (".implode(",",$enroll_agent).")";
    $report_incr .= "  AND res.sponsor_id IN (".implode(",",$enroll_agent).") ";
  }

if(!empty($tree_agent_id)){
    $incr .= " AND (s.id IN(".implode(",",$tree_agent_id).") OR (s.sponsor_id IN (".implode(",",$tree_agent_id).") AND scs.agent_coded_id = 1)) ";
}

if (!empty($tree_agent_id)) {
    if (count($tree_agent_id) > 0) {
        // $incr .= " AND (";
        // foreach ($tree_agent_id as $key => $value) {
        //     if (end($tree_agent_id) == $value) {
        //         $incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
        //     } else {
        //         $incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
        //     }
        // }
        // $incr .= ")";

        $report_incr .= " AND (";
        foreach ($tree_agent_id as $key => $value) {
            if (end($tree_agent_id) == $value) {
                $report_incr .= " res.upline_sponsors LIKE '%," . $value . ",%'";
            } else {
                $report_incr .= " res.upline_sponsors LIKE '%," . $value . ",%' OR";
            }
        }
        $report_incr .= ")";
    }
}

if (!empty($dependent_id)) {
    $dependent_id = explode(',', trim($dependent_id));
    $dep_arr = $dependent_id = array_map('trim', $dependent_id);
    $dependent_id = "'" . implode("','", makeSafe($dependent_id)) . "'";
    $extra_join .= " LEFT JOIN customer_dependent_profile as cdp on(cdp.customer_id=c.id AND cdp.is_deleted='N')";
    $incr .= " AND ( cdp.display_id IN ($dependent_id)";
    $report_incr .="  AND ( res.ddisplay_id IN ($dependent_id) ";

    foreach($dep_arr as $key => $val){
        $sch_params[':dfname'.$key] = "%" . makeSafe($val) . "%";
        $incr .=" OR ( cdp.fname LIKE :dfname".$key." OR cdp.lname LIKE :dfname".$key.") ";

        $report_sch_param[':dfname'.$key] = "%" . makeSafe($val) . "%";
        $report_incr .= "  OR ( res.dfname LIKE :dfname".$key." OR res.dlname LIKE :dfname".$key.")  ";
    }
    $incr .=" ) ";
    $report_incr .=" ) ";
}

$getfromdate = '';
$gettodate = '';
    
if ($join_range != "") {
    if ($join_range == "Range" && $fromdate != '' && $todate != '') {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(c.joined_date) >= :fromdate AND DATE(c.joined_date) <= :todate";

        $report_sch_param[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $report_sch_param[':todate'] = date("Y-m-d", strtotime($todate));
        $report_incr .= " AND DATE(res.joined_date) >= :fromdate AND DATE(res.joined_date) <= :todate";

        $getfromdate = $fromdate;
        $gettodate = $todate;
    } else if ($join_range == "Exactly" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) = :added_date";

        $report_sch_param[':added_date'] = date("Y-m-d", strtotime($added_date));
        $report_incr .= " AND DATE(res.joined_date) = :added_date";

        $getfromdate = $added_date;
        $gettodate = $added_date;
    } else if ($join_range == "Before" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) < :added_date";
        
        $report_sch_param[':added_date'] = date("Y-m-d", strtotime($added_date));
        $report_incr .= " AND DATE(res.joined_date) < :added_date";

        $getfromdate = $added_date;
        $gettodate = date('Y-m-d');
    } else if ($join_range == "After" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(c.joined_date) > :added_date";

        $report_sch_param[':added_date'] = date("Y-m-d", strtotime($added_date));
        $report_incr .= " AND DATE(res.joined_date) > :added_date";
        $getfromdate = date('Y-m-d');
        $gettodate = $added_date;
    }
}

if ($effective_join_range != "") {
    if ($effective_join_range == "Range" && $effective_from_date != '' && $effective_to_date != '') {
        $sch_params[':effective_from_date'] = date("Y-m-d", strtotime($effective_from_date));
        $sch_params[':effective_to_date'] = date("Y-m-d", strtotime($effective_to_date));
        $incr .= " AND DATE(ws.eligibility_date) >= :effective_from_date AND DATE(ws.eligibility_date) <= :effective_to_date";

        $report_sch_param[':effective_from_date'] = date("Y-m-d", strtotime($effective_from_date));
        $report_sch_param[':effective_to_date'] = date("Y-m-d", strtotime($effective_to_date));
        $report_incr .= " AND DATE(w.eligibility_date) >= :effective_from_date AND DATE(w.eligibility_date) <= :effective_to_date";
    } else if ($effective_join_range == "Exactly" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) = :effective_date";

        $report_sch_param[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $report_incr .= " AND DATE(w.eligibility_date) = :effective_date";
    } else if ($effective_join_range == "Before" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) < :effective_date";

        $report_sch_param[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $report_incr .= " AND DATE(w.eligibility_date) < :effective_date";
    } else if ($effective_join_range == "After" && $effective_date != '') {
        $sch_params[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $incr .= " AND DATE(ws.eligibility_date) > :effective_date";

        $report_sch_param[':effective_date'] = date("Y-m-d", strtotime($effective_date));
        $report_incr .= " AND DATE(w.eligibility_date) > :effective_date";
    }
}

if ($billing_join_range != "") {
    if ($billing_join_range == "Range" && $billing_from_date != '' && $billing_to_date != '') {
        $sch_params[':billing_from_date'] = date("Y-m-d", strtotime($billing_from_date));
        $sch_params[':billing_to_date'] = date("Y-m-d", strtotime($billing_to_date));
        $incr .= " AND DATE(ws.next_purchase_date) >= :billing_from_date AND DATE(ws.next_purchase_date) <= :billing_to_date";

        $report_sch_param[':billing_from_date'] = date("Y-m-d", strtotime($billing_from_date));
        $report_sch_param[':billing_to_date'] = date("Y-m-d", strtotime($billing_to_date));
        $report_incr .= " AND DATE(w.next_purchase_date) >= :billing_from_date AND DATE(w.next_purchase_date) <= :billing_to_date";
    } else if ($billing_join_range == "Exactly" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) = :billing_date";

        $report_sch_param[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $report_incr .= " AND DATE(w.next_purchase_date) = :billing_date";
    } else if ($billing_join_range == "Before" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) < :billing_date";

        $report_sch_param[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $report_incr .= " AND DATE(w.next_purchase_date) < :billing_date";
    } else if ($billing_join_range == "After" && $billing_date != '') {
        $sch_params[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $incr .= " AND DATE(ws.next_purchase_date) > :billing_date";

        $report_sch_param[':billing_date'] = date("Y-m-d", strtotime($billing_date));
        $report_incr .= " AND DATE(w.next_purchase_date) > :billing_date";
    }
}

    $export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

    if(!empty($export_val)){

        include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
        
        if($getfromdate!='' && $gettodate != '') {

            $no_days=0;
            if($getfromdate!= '' && $gettodate!='') {
                $date1 = date_create($getfromdate);
                $date2 = date_create($gettodate);
                $diff = date_diff($date1,$date2);
                $no_days=$diff->format("%a");
            }
            
            if($no_days>62) {
                echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
                exit();
            }
        }
        $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"MASKED Member Summary","member_summary",$report_incr, $report_sch_param,$extra_export_arr,'member_summary');
        echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
        exit;
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

        $sel_sql = "SELECT
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

$selectize = true;
// $tree_agent_sql = "SELECT id,rep_id,fname,lname
//                  FROM customer 
//                  where type IN('Agent','Group') AND is_deleted = 'N'";
// $tree_agent_res = $pdo->select($tree_agent_sql);

$filter_prd_res = get_active_global_products_for_filter();

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);


$template = 'member_listing.inc.php';
include_once 'layout/end.inc.php';

<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'member_listing.php';
$breadcrumbes[1]['title'] = 'Members';

$sch_params = array();
$incr = '';
$tbl_incr = '';
$having = "";
$tble_incr = "";
$agnt_prd = "";
$license_incr = "";
$qry_incr = '';
$SortBy = "c.joined_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$extra_join = '';
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : ''; 


$group_id = isset($_SESSION['groups']['id']) ? $_SESSION['groups']['id'] : '';
if(!empty($group_id)){
    $incr .= " AND c.sponsor_id = :sponsor_id ";
    $sch_params[':sponsor_id'] = $group_id;
}

$rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : array();
$name = isset($_GET['name']) ? $_GET["name"] : '';
$cell_phone = isset($_GET['cell_phone']) ? $_GET["cell_phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$products = isset($_GET['products']) ? $_GET['products'] : array();
$product_status = isset($_GET['product_status']) ? $_GET['product_status'] : array();
$status = isset($_GET['status']) ? $_GET['status'] : array();
$state = isset($_GET['state']) ? $_GET['state'] : array();
$dependent_id = isset($_GET['dependent_id']) ? $_GET['dependent_id'] : '';

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
// $today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : '';
// $viewMember = !empty($_GET["viewMember"]) ? $_GET["viewMember"] : 'todayMember';

// if($viewMember == "todayMember" && empty($join_range)){
//   $join_range = "Exactly";
// }

if($is_ajaxed){

    $billing_join_range = isset($_GET['billing_join_range']) ? $_GET['billing_join_range'] : "";
    $billing_from_date = isset($_GET["billing_from_date"]) ? $_GET["billing_from_date"] : "";
    $billing_to_date = isset($_GET["billing_to_date"]) ? $_GET["billing_to_date"] : "";
    $billing_date = isset($_GET["billing_date"]) ? $_GET["billing_date"] : "";


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
    
    $name = cleanSearchKeyword($name);
    $cell_phone = cleanSearchKeyword($cell_phone);
    $email = cleanSearchKeyword($email);
    $dependent_id = cleanSearchKeyword($dependent_id); 
     
    if (!empty($rep_id)) {
        $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
        $incr .= " AND c.rep_id IN ($rep_id)";
    }

    if ($name) {
        $sch_params[':name'] = "%" . makeSafe($name) . "%";
        $incr .= " AND ((c.fname LIKE :name or c.lname LIKE :name) or (CONCAT(c.fname,' ',c.lname) like :name)) ";
    }

    
    if ($cell_phone != "") {
        $sch_params[':cell_phone'] = "%" . makeSafe($cell_phone) . "%";
        $incr .= " AND c.cell_phone LIKE :cell_phone";
    }

    if ($email != "") {
        $sch_params[':email'] = "%" . makeSafe($email) . "%";
        $incr .= " AND c.email LIKE :email";
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

    if (!empty($status)) {
        $status = "'" . implode("','", makeSafe($status)) . "'";
        $incr .= " AND c.status IN ($status)";
    }

    if (!empty($state)) {
        $state = "'" . implode("','", makeSafe($state)) . "'";
        $incr .= " AND c.state IN ($state)";
    }

    if (!empty($dependent_id)) {
        $dependent_id = explode(',', trim($dependent_id));
        $dep_arr = $dependent_id = array_map('trim', $dependent_id);
        $dependent_id = "'" . implode("','", makeSafe($dependent_id)) . "'";
        $extra_join .= " LEFT JOIN customer_dependent_profile as cdp on(cdp.customer_id=c.id AND cdp.is_deleted='N')";
        $incr .= " AND ( cdp.display_id IN ($dependent_id) ";
    
        foreach($dep_arr as $key => $val){
            $sch_params[':dfname'.$key] = "%" . makeSafe($val) . "%";
            $incr .=" OR ( cdp.fname LIKE :dfname".$key." OR cdp.lname LIKE :dfname".$key.") ";
        }
        $incr .=" ) ";
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
                        AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password
                      FROM customer c
                      LEFT JOIN customer as s on(s.id= c.sponsor_id)
                      LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                      LEFT JOIN (
                        SELECT COUNT(DISTINCT p2.id) as total_products,ws2.customer_id
                        FROM website_subscriptions ws2
                        JOIN prd_main p2 ON(p2.id=ws2.product_id and p2.is_deleted='N' AND p2.type!='Fees')
                        GROUP BY ws2.customer_id
                      ) p2 ON(p2.customer_id = c.id)
                      LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                      $extra_join
                      WHERE c.type='Customer'  AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending')
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
    include_once 'tmpl/member_listing.inc.php';
    exit;
}

$filter_prd_res = get_active_global_products_for_filter($_SESSION['groups']['id'],false,false,true,true);
// $tree_agent_sql = "SELECT id,rep_id,fname,lname
// 					FROM customer 
// 					where type='Agent' AND is_deleted = 'N' AND (upline_sponsors LIKE('%,".$_SESSION['groups']['id'].",%'))";
// $tree_agent_res = $pdo->select($tree_agent_sql);

$member_sql = "SELECT id,rep_id,fname,lname FROM customer  WHERE type='Customer' AND is_deleted = 'N' AND status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment') AND sponsor_id=:sponsor_id";
$member_res = $pdo->select($member_sql,array(":sponsor_id"=>$group_id));

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);

$template = 'member_listing.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

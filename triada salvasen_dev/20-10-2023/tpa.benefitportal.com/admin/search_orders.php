<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(11);
$affiliates_menu = has_menu_access(4);
$agent_menu = has_menu_access(5);
$member_menu = has_menu_access(8);
$all_order_menu = has_menu_access(12);
$retun_order_menu = has_menu_access(13);

$sch_params = array();
$SortBy = "created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if ($_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if ($_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = $_GET['is_ajaxed'];
$order_search = $_GET['order_search'];

if(!empty($order_search)){
	$sch_params['search_prm'] = "%".trim($order_search)."%";
	$incr .= " AND (o.display_id LIKE :search_prm OR o.status LIKE :search_prm) ";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? ($_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'search_orders.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

$sum_incr = $incr;
$sum_params = $sch_params;

$productSql="SELECT p.*,c.company_name FROM prd_main p 
            LEFT JOIN prd_company c ON (c.id = p.company_id)
            where p.parent_product_id=0 AND p.is_deleted='N' and p.status='Active' ORDER BY name ASC";
$productRes=$pdo->select($productSql);

$company_arr=array();
if($productRes){
    foreach ($productRes as $key => $row) {
    	if($row['type']=='Kit'){
        		$row['company_name']= 'Product Kits';
        }
        if (!is_array($company_arr[$row['company_name']])) {
                $company_arr[$row['company_name']] = array();
        }
        array_push($company_arr[$row['company_name']], $row);
    }
}

$totalSql = "SELECT temp_ord.site_load, SUM(temp_ord.product_total) AS o_product_total,
         SUM(temp_ord.shipping_charge) AS o_shipping_charge_total,
         SUM(temp_ord.tax_charge) AS o_tax_charge_total,
         SUM(temp_ord.sub_total) AS o_sub_total,
         SUM(temp_ord.discount) AS o_discount_total,
         SUM(temp_ord.grand_total) AS o_grand_total
  FROM (
    SELECT o.site_load, o.product_total, o.shipping_charge, o.tax_charge, o.sub_total, o.discount, o.grand_total FROM orders o
    LEFT JOIN order_details od ON(o.id= od.order_id AND od.is_deleted='N')
    LEFT JOIN customer c ON (c.id = o.customer_id)
    LEFT JOIN website_subscriptions w ON(c.id=w.customer_id)
    LEFT JOIN customer s ON (c.sponsor_id = s.id)
    LEFT JOIN prd_company cmp ON(cmp.id = o.company_id)
    LEFT JOIN order_billing_info obi ON(obi.customer_id = c.id)
    LEFT JOIN customer_enrollment ce ON(ce.customer_id=c.id AND ce.subscription_id=w.id)
    WHERE o.id>0 AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' " . $sum_incr . "
    GROUP BY o.id
  ) temp_ord
  GROUP BY temp_ord.site_load";
$totalRows = $pdo->select($totalSql, $sum_params);

if ($is_ajaxed) {
	try {
		$sel_sql = "SELECT o.*, c.rep_id as c_rep_id, CONCAT(c.fname,' ',c.lname) as c_name,s.business_name as s_business_name, c.state as c_state, c.type as c_type, s.rep_id as s_rep_id, CONCAT(s.fname,' ',s.lname) as s_name, c.sponsor_id, s.type as s_type,cmp.company_name
              FROM orders o
              LEFT JOIN order_details od ON(o.id= od.order_id AND od.is_deleted='N')
              LEFT JOIN customer c ON (c.id = o.customer_id)
              LEFT JOIN website_subscriptions w ON(c.id=w.customer_id)
              LEFT JOIN customer s ON (c.sponsor_id = s.id)
              LEFT JOIN prd_company cmp ON(cmp.id = o.company_id)
              LEFT JOIN order_billing_info obi ON(obi.customer_id = c.id)
              LEFT JOIN customer_enrollment ce ON(ce.customer_id=c.id AND ce.subscription_id=w.id)
              WHERE o.id > 0 AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' " . $incr . "
              GROUP BY o.id
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
	include_once 'tmpl/search_orders.inc.php';
	exit;
}
 

$page_title = "All Orders";

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/clipboard/clipboard.min.js','thirdparty/multiple-select-master/jquery.multiple.select.js');
$template = 'search_orders.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
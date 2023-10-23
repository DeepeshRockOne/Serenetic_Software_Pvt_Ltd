<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//has_access(34);
has_access(1);
$affiliates_menu = has_menu_access(4);
$agent_menu = has_menu_access(5);
$member_menu = has_menu_access(8);
$group_menu = has_menu_access(6);
$SortBy = "c.joined_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : '';
$is_ajaxed_customers = isset($_GET['is_ajaxed_customers']) ? $_GET['is_ajaxed_customers'] : '';
$has_querystring = false;
$sch_params = array();
$incr = isset($incr) ? $incr : '';
if ($gsearch != "") {
  $sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
  $sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
  $sch_params[':rep_id'] = makeSafe($gsearch);
  $sch_params[':user_id'] = makeSafe($gsearch);
  $sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
  $incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone)";
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
  'url' => 'global_customers.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_customers){
  $sel_sql = "SELECT c.rep_id,
                    c.joined_date,
                    md5(c.id) as id,
                    c.fname, 
                    c.lname, 
                    c.email, 
                    c.status,
                    c.cell_phone,
                    s.fname as s_fname,
                    s.lname as s_lname,
                    md5(c.sponsor_id) as sponsor_id,
                    s.rep_id as sponsor_rep_id,
                    COUNT(p.id) as total_products,
                    AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,scs.company
                  FROM customer c
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
                  LEFT JOIN customer_settings scs ON(scs.customer_id=s.id)
                  LEFT JOIN website_subscriptions ws on(ws.customer_id=c.id)
                  LEFT JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N' AND p.type!='Fees')
                  WHERE c.type='Customer'  AND c.is_deleted = 'N' AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Quotes','Pending Validation','Post Payment','Pending')
                  " . $incr . " 
                  GROUP BY c.id 
                  ORDER BY  $SortBy $SortDirection";
  
  $paginate = new pagination($page, $sel_sql, $options);
  if ($paginate->success == true) {
    $fetch_rows = $paginate->resultset->fetchAll();
    $total_rows = count($fetch_rows);
  } 
}


include_once 'tmpl/global_customers.inc.php';
?>
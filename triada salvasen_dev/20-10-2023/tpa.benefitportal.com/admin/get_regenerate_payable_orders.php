<?php
  include_once dirname(__FILE__) . '/layout/start.inc.php';

  $incr = $field_incr = $tbl_incr = '';
  $sch_params = array();
  $has_querystring = false;

  $is_ajaxed = checkIsset($_GET['is_ajaxed']);

  $select_type = checkIsset($_GET['select_type']);

  $join_range = checkIsset($_GET['join_range']);
  $fromdate = checkIsset($_GET["fromdate"]);
  $todate = checkIsset($_GET["todate"]);
  $added_date = checkIsset($_GET["added_date"]);
  $start_date = checkIsset($_GET["start_date"]);
  $end_date = checkIsset($_GET["end_date"]);

  $sel_orders = checkIsset($_GET['orders']);
  $products = checkIsset($_GET['products'],'arr');
  $period = checkIsset($_GET['period']);

  if($select_type == "specific_date_range"){
    if($join_range == "Range" && $fromdate!='' && $todate!=''){
        $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
        $incr.=" AND DATE(o.created_at) >= :fromdate AND DATE(o.created_at) <= :todate";
    }else if($join_range == "Exactly" && $added_date!=''){
        $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(o.created_at) = :added_date";
    }else if($join_range == "Before" && $added_date!=''){
        $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(o.created_at) < :added_date";
    }else if($join_range == "After" && $added_date!=''){
        $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
        $incr.=" AND DATE(o.created_at) > :added_date";
    }
    
    $tbl_incr = ' JOIN sub_transactions st ON(st.order_id=o.id AND st.transaction_id=t.id AND st.product_id=od.product_id) ';
    $field_incr .= ' ,GROUP_CONCAT(distinct(od.product_id)) as productIds,GROUP_CONCAT(distinct(od.plan_id)) AS plan_ids,o.customer_id,od.plan_id ';

  }else if($select_type == 'specific_order' && !empty($sel_orders)){
    $tbl_incr = ' JOIN sub_transactions st ON(st.order_id=o.id AND st.transaction_id=t.id AND st.product_id=od.product_id) ';
    $incr.=" AND o.id IN(".$sel_orders.") ";
    $field_incr .= ' ,GROUP_CONCAT(distinct(od.product_id)) as productIds,GROUP_CONCAT(distinct(od.plan_id)) AS plan_ids,o.customer_id,od.plan_id ';
    $has_querystring = true;
  } else if($select_type == 'all_order_specific_product' && !empty($products)){
    $tbl_incr .= ' JOIN sub_transactions st ON(st.order_id=o.id AND st.transaction_id=t.id AND st.product_id=od.product_id) ';
    $incr.=" AND od.product_id IN(".implode(',',$products).")";
    $field_incr .= ' ,GROUP_CONCAT(distinct(od.product_id)) as productIds,GROUP_CONCAT(distinct(od.plan_id)) AS plan_ids,o.customer_id,od.plan_id';
    $has_querystring = true;
  }

if (count($sch_params) > 0) {
  $has_querystring = true;
}

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'get_regenerate_payable_orders.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT 
                o.display_id as odrDispId,
                o.created_at as odrDate,
                c.rep_id as mbrDispId,
                CONCAT(c.fname,' ',c.lname) as mbrName,
                s.rep_id as agentDispId,
                CONCAT(s.fname,' ',s.lname) as agentName,
                o.is_renewal as saleType,
                o.status as odrStatus,
                o.transaction_id as transactionId,
                MIN(od.renew_count) AS minCov,
                MAX(od.renew_count) AS maxCov,
                pym.name as processorName,
                o.grand_total as odrTotal,
                c.id as mbrId,
                o.id as odrID
              $field_incr
            FROM orders o 
            JOIN transactions t ON(t.order_id=o.id)
            JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
            JOIN customer c ON (c.id = o.customer_id)
            JOIN customer s ON (c.sponsor_id = s.id)
            LEFT JOIN payment_master pym ON(pym.id=o.payment_master_id)
            $tbl_incr
            WHERE o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' " . $incr . "
          GROUP BY o.id
          ORDER BY o.id DESC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/get_regenerate_payable_orders.inc.php';
  exit;
}
?>
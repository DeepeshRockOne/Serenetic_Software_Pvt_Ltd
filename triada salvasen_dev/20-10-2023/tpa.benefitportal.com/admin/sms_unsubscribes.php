<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
  
  $incr = '';
  $sch_params = array();
  $has_querystring = false;


  $sysNumber = $TwilioNumber;
  
  $sms_ajaxed = isset($_GET['sms_ajaxed']) ? $_GET['sms_ajaxed'] : '';
  $phone = isset($_GET['phone']) ? $_GET['phone'] : '';
  $phone_added_date = isset($_GET['phone_added_date']) ? $_GET['phone_added_date'] : '';

  $phone = cleanSearchKeyword($phone); 
   
  if ($phone != "") {
    $sch_params[':phone'] = "%" . makeSafe($phone) . "%";
    $incr .= " AND u.phone LIKE :phone";
  }

  if ($phone_added_date != "") {
    $sch_params[':added_date'] = date("Y-m-d",strtotime($phone_added_date));
    $incr .= " AND DATE(u.added_date) = :added_date";
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
      'url' => 'sms_unsubscribes.php?sms_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($sms_ajaxed) {
  try {
    $sel_sql = "SELECT md5(id) as id,type,phone,added_date
             FROM unsubscribes u
             WHERE u.type ='sms' AND u.is_deleted='N' $incr
             GROUP BY u.id 
             ORDER BY u.id DESC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/sms_unsubscribes.inc.php';
  exit;
}
?>
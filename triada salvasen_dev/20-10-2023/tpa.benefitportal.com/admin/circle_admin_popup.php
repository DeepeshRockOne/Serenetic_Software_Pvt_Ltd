<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(66);

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$sch_params = array();
$incr = '';
$SortBy = "ac.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;

$id = checkIsset($_GET['id']);
$name = getname("admin_circle",$id,'name','md5(id)');
$is_ajaxed = checkIsset($_GET['is_ajaxed']);

if(!empty($id)){
    $sch_params[':id'] = $id;
    $incr.=" AND md5(ac.id)=:id "; 
}
    $sel_sql = "SELECT ac.id ,a.fname,a.lname,a.display_id,a.status
    from admin_circle ac 
    JOIN assigned_admin_circle aac ON(aac.circle_id=ac.id AND aac.is_deleted='N' AND aac.admin_id!=0)
    LEFT JOIN admin a ON(a.id=aac.admin_id AND a.is_deleted='N')
    WHERE ac.is_deleted='N' $incr GROUP BY aac.id ORDER BY  $SortBy $SortDirection";
    $fetch_rows = $pdo->select($sel_sql,$sch_params);
    $total_rows = count($fetch_rows);

$template = 'circle_admin_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
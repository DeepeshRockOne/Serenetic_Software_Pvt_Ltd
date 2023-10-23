<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Healthy Steps";
$breadcrumbes[1]['link'] = 'healthy_steps.php';


$sch_params=array();
$incr=''; 
$SortBy = "p.create_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$status_change = checkIsset($_GET['status_change']);
if($status_change == 'Y'){

    $product_id = $_GET['product_id'];
    $health_id = $_GET['health_id'];
    $status = $_GET['status'];
    $old_status = $_GET['old_status'];

    $query = "SELECT name,product_code FROM prd_main WHERE id = :id and is_deleted='N'";
    $srow = $pdo->selectOne($query, array(":id"=>$product_id));


    $update_params = array("status"=>$status);
    $pdo->update("prd_main",$update_params,array("clause"=>"id=:id","params"=>array(":id"=>$product_id)));

    $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated Healthy Steps status '.$srow['name'],
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/add_globalhealthy_steps.php?product_id='.md5($product_id).'&health_id='.md5($health_id),
            'title'=>' ('.$srow['product_code'].')',
        ),
      ); 
      $description['description'] = "From ".$old_status.' To '.$status;
  
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'prd_fees','Admin Updated Healthy Steps', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    echo json_encode(array("status"=>"success","message"=>"Staus updated Successfully!"));
    exit;
}        
$sql="SELECT p.id as pid,pf.id as pfid,md5(p.id) as id,count(DISTINCT paf.product_id) as total_products,md5(pf.id) as health_id,p.create_date,p.name,p.product_code,p.is_fee_on_commissionable,p.is_member_benefits,
pm.pricing_effective_date,pm.pricing_termination_date,p.status 
FROM prd_main p
LEFT JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N')
LEFT JOIN prd_assign_fees paf ON(paf.fee_id=p.id and paf.is_deleted='N')
LEFT JOIN prd_fees pf ON(pf.id=paf.prd_fee_id and pf.is_deleted='N' and pf.setting_type='Healthy Step')
WHERE p.type='Fees' AND p.product_type='Healthy Step' and p.record_type='Primary' and p.is_deleted='N'   GROUP BY p.id  ORDER BY $SortBy $currSortDirection";
$fetch_rows = $pdo->select($sql);
$total_rows = count($fetch_rows);

$template = 'healthy_steps.inc.php';
include_once 'layout/end.inc.php';
?>

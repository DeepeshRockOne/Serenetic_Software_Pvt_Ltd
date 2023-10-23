<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Payables";
$breadcrumbes[2]['link'] = 'account_payable.php';
$breadcrumbes[3]['title'] = "Regenerate";
$breadcrumbes[3]['link'] = 'regenerate_payable.php';
$breadcrumbes[3]['class'] = "Active";

$productSql="SELECT p.id,p.name,p.product_code,p.type,if(p.product_type='Healthy Step','Healthy Step',c.title) as title
            FROM prd_main p 
            LEFT JOIN prd_category c ON (c.id = p.category_id)
            where (p.type!='Fees' OR p.product_type='Healthy Step') AND p.is_deleted='N' AND p.status IN('Active','Suspended')  GROUP BY p.id ORDER BY name ASC";
$productRes = $pdo->selectGroup($productSql,array(),'title');

$start_date = date('m/d/Y',strtotime(get_pay_period_weekly().' -6 days'));
$end_date = date('m/d/Y',strtotime(get_pay_period_weekly()));

$description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' read add regenerate payables page'
  );
$desc=json_encode($description);
activity_feed(3,$_SESSION['admin']['id'], 'Admin' ,$_SESSION['admin']['id'], 'Admin', 'Admin Read Payable Page',$_SESSION['admin']['name'],"",$desc);
$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'add_regenerate_payable.inc.php';
include_once 'layout/end.inc.php';
?>



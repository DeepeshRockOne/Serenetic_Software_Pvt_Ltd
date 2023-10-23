<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";

$commObj = new Commission();

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Commissions";
$breadcrumbes[2]['link'] = 'payment_commissions.php';
$breadcrumbes[3]['title'] = "Regenerate";
$breadcrumbes[3]['link'] = 'regenerate_commissions.php';

$productSql="SELECT p.id,p.name,p.product_code,p.type,if(p.product_type='Healthy Step','Healthy Step',c.title) as title
            FROM prd_main p 
            LEFT JOIN prd_category c ON (c.id = p.category_id)
            where (p.type!='Fees' OR p.product_type='Healthy Step') AND p.is_deleted='N' AND p.status='Active'  GROUP BY p.id ORDER BY name ASC";
$productRes = $pdo->selectGroup($productSql,array(),'title');


$today = date("Y-m-d");
$weeklyPayPeriod = $commObj->getWeeklyPayPeriod($today);
$monthlyPayPeriod = $commObj->getMonthlyPayPeriod($today);

$weeklyPeriodStart = date('m/d/Y', strtotime('-6 days', strtotime($weeklyPayPeriod)));
$weeklyPeriodEnd = date('m/d/Y',strtotime($weeklyPayPeriod));

$monthlyCommPeriod = date('M Y',strtotime($monthlyPayPeriod));

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'add_regenerate_commissions.inc.php';
include_once 'layout/end.inc.php';
?>

<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(42);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Providers";
$breadcrumbes[1]['link'] = 'providers.php';
$breadcrumbes[2]['title'] = "+ Provider";
$breadcrumbes[2]['link'] = 'add_providers.php';
$page_title = "Providers";
$user_groups = "active";

$providers_res = array();
if(isset($_GET['providers_id']) && !empty($_GET['providers_id'])){
  $providers_id = $_GET['providers_id'];
  $providers_res = $pdo->selectOne("SELECT id, name, display_id FROM providers WHERE md5(id) = :providers_id and is_deleted='N'", array(":providers_id" => $providers_id));

  if(!empty($providers_res)){
    $provider_name = $providers_res['name'];
    $display_id = $providers_res['display_id'];

    $sub_providers_res = $pdo->select("SELECT sp.id,group_concat(p.id) as product_id, sp.group_id, sp.url
          FROM sub_provider as sp
          LEFT JOIN prd_main p ON(p.id = sp.product_id AND p.is_deleted = 'N' AND p.record_type='primary') 
          WHERE sp.providers_id = :provider_id AND sp.is_deleted = 'N' group by sp.group_id", array(":provider_id" => $providers_res['id']));

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Read Provider ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($providers_res['id']),
          'title'=>$providers_res['display_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $providers_res['id'], 'provider','Read Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

  } else {
    setNotifyError("No record Founnd!");
    redirect("providers.php");
  }
}

$select_product = "SELECT p.id,p.name,p.category_id,pc.title,p.type,p.product_code
                  FROM prd_main as p
                  JOIN prd_category as pc ON (p.category_id = pc.id) 
                  LEFT JOIN prd_product_builder_validation pbv ON(pbv.product_id=p.id AND p.status='Pending')
                  WHERE p.is_deleted = 'N' AND p.record_type='primary' AND (pbv.errorJson IS NULL OR pbv.errorJson = '[]') ORDER BY p.name ASC";
$product_res = $pdo->select($select_product);

$productArray = array();
if(!empty($product_res) && count($product_res) > 0){
  $company_arr = array();
  foreach ($product_res as $key => $row) {
    $productArray[$row['id']]['name'] = $row['name'];
    $productArray[$row['id']]['product_code'] = $row['product_code'];
    if ($row['type'] == 'Kit') {
      $row['title'] = 'Product Kits';
    }
    if (!array_key_exists($row['title'], $company_arr)) {
      $company_arr[$row['title']] = array();
    }
    array_push($company_arr[$row['title']], $row);
  }
}

if(empty($providers_res)){
  include_once __DIR__ . '/../includes/function.class.php';
  $functionsList = new functionsList();
  $display_id=$functionsList->generateProviderDisplayID();
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache,);
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,);

$template = 'add_providers.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';

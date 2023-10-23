<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$cd_profile_id = $_GET['id'];
$customer_dep = $pdo->select("SELECT c.id,c.eligibility_date,p.name from customer_dependent c LEFT JOIN prd_main p ON(p.id=c.product_id and p.is_deleted='N') where md5(c.cd_profile_id)=:cd_profile_id AND c.status!='Termed' order by p.name ASC",array(":cd_profile_id"=>$cd_profile_id));

$dep_info = $pdo->selectOne("SELECT fname,lname from customer_dependent_profile where md5(id)=:cd_profile_id and is_deleted='N'",array(":cd_profile_id"=>$cd_profile_id));

$template = 'depedents_active_product.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
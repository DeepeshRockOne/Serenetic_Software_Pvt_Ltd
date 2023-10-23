<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$customer_id = $_POST['id'];
$member_sql = "SELECT md5(cp.id) as id,cp.display_id,cp.fname,cp.lname,IF(cp.relation IN ('Husband','Wife'),'Spouse','Child') AS crelation,cp.birth_date,cp.created_at,cp.gender,if(cd.terminationDate is null,GROUP_CONCAT(p.name SEPARATOR ', '),'') as products 
            FROM  customer_dependent_profile cp 
            LEFT JOIN customer_dependent cd ON(cd.cd_profile_id = cp.id and cd.is_deleted='N') 
            LEFT JOIN prd_main p ON(p.id=cd.product_id and p.is_deleted='N')
            where md5(cp.customer_id)=:customer_id and cp.is_deleted='N'  GROUP BY cp.id order by FIELD (crelation,'Spouse','Child') ASC, cp.created_at DESC";
$member_dependents = $pdo->select($member_sql,array(":customer_id"=>$customer_id));

include_once 'tmpl/member_depedents_tab.inc.php';
?>
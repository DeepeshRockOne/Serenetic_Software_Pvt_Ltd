<?php

include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . "/UserTimezone.php";

    $timezone = 'CDT';
    if(!empty($_SESSION['admin']['timezone'])){
        $timezone = $_SESSION['admin']['timezone'];
    }else if(!empty($_SESSION['agents']['timezone'])){
        $timezone = $_SESSION['agents']['timezone'];
    }else if(!empty($_SESSION['groups']['timezone'])){
        $timezone = $_SESSION['groups']['timezone'];
    }
    $tz = new UserTimeZone('m/d/Y @ h:i A T', $timezone);

    $interaction_id = $_GET['id'];
    $userType = isset($_GET['user_type']) ? $_GET['user_type'] : 'Agent';
    // $userType = !empty($userType) ? $userType : "Agent";
    $interaction = $pdo->selectOne("SELECT i.type,CONCAT(c.fname,' ',c.lname) as  customer_name ,c.rep_id,id.created_at,id.status,GROUP_CONCAT(p.name) as products,id.description,CONCAT(a.fname,' ',a.lname) as admin_name,a.display_id ,ip.product_id,id.id as idid 
    FROM interaction_detail id 
    LEFT JOIN interaction i ON(i.id=id.interaction_id and i.is_deleted='N')
    LEFT JOIN customer c ON(c.id=id.user_id and c.is_deleted='N') 
    LEFT JOIN admin a ON(a.id=id.admin_id and a.is_deleted='N') 
    LEFT JOIN interaction_product ip ON(ip.interaction_detail_id = id.id and ip.is_deleted='N') 
    LEFT JOIN prd_main p ON (p.id=ip.product_id)
    WHERE md5(id.id)=:id and id.is_deleted='N' and id.status='Active' ",array(":id"=>$interaction_id));

$template = "interactions_note.inc.php";
include_once 'layout/iframe.layout.php';
?>
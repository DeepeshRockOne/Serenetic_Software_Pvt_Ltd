<?php
include_once dirname(__FILE__) . '/includes/connect.php';

$interactionId = isset($_GET['id']) ? $_GET['id'] : '';

if(!empty($interactionId)){
    $interactionDetSel = "SELECT c.rep_id,id.description,c.id,i.type from interaction_detail id JOIN interaction i ON(i.id=id.interaction_id AND i.is_deleted='N') JOIN customer c ON(c.id=id.user_id AND c.type='customer') 
    AND id.is_deleted='N' AND id.is_claim='N' AND md5(id.id)=:id";
    $resInteraction = $pdo->selectOne($interactionDetSel,array(":id"=>$interactionId));
    if(empty($resInteraction['id'])){
        echo "Interaction Detaill Not Found!";
        exit;
    }
}else{
    echo "Interaction Detaill Not Found!";
    exit;
}


$template = 'member_interaction_content.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
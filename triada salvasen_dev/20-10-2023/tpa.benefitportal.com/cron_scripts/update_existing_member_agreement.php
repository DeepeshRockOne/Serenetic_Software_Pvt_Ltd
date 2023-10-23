<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/function.class.php';

$functionsList = new functionsList();

$subscrition_sql  = "SELECT od.subscription_ids as sub_id,od.id as order_id ,cus.id as cus_id 
            FROM orders od 
            JOIN customer cus ON (cus.id=od.customer_id)
            WHERE od.is_renewal='N'";
$subscrition_res = $pdo->select($subscrition_sql);


if(!empty($subscrition_res)){
    foreach($subscrition_res as $res_sub){
        $member_agreement_sql = "SELECT mt.id FROM member_terms_agreement AS mt JOIN (
        SELECT MIN(id) AS mtID FROM member_terms_agreement WHERE customer_id=:customer_id AND order_id =:order_id
        ) AS mmtOld ON (mmtOld.mtID = mt.id)";
        $member_agreement_res = $pdo->selectOne($member_agreement_sql,array(":customer_id"=>$res_sub['cus_id'],":order_id"=>$res_sub['order_id']));
	    
        if(!empty($member_agreement_res)){
            $web_sub_id = explode(",",$res_sub['sub_id']);
            foreach($web_sub_id as $value_res){
                $update_params["agreement_id"] = $member_agreement_res['id'];

                $upd_where = array(
                    'clause' => 'id = :id',
                    'params' => array(
                        ':id' => $value_res,
                    ),
                );

			    $pdo->update('website_subscriptions', $update_params, $upd_where);
            }
        }
    }
}

echo "<br>Completed";
dbConnectionClose();
exit;
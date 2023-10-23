<?php
/**
 * Script is used for Store Active member at end of the month
 */
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$today = date('Y-m-d');
$endOfMonthDate = date('Y-m-t');

// if today is not end of the month then exit;
if($today != $endOfMonthDate){
    echo "Not end of month.";
    exit;
}
//select active members
$selActiveMember = "SELECT COUNT(DISTINCT c.id) AS 'TotalActiveMember'
    FROM customer c
    JOIN website_subscriptions ws ON(ws.product_type!='Fees' AND  ws.customer_id=c.id AND ws.status='Active')
    WHERE (ws.termination_date IS NULL OR termination_date >= :endOfMonthDate) 
    AND c.status='Active' AND c.is_deleted='N'
    AND DATE(c.created_at) <= :endOfMonthDate HAVING COUNT(ws.id)>0";
$resActiveMemnber = $pdo->selectOne($selActiveMember,array(":endOfMonthDate" => $endOfMonthDate));

    $selExist = "SELECT id FROM active_members WHERE month_date=:month_date AND is_deleted='N'";
    $schParam = array(":month_date"=>$endOfMonthDate);
    $resExists = $pdo->selectOne($selExist,$schParam);
    if(empty($resExists['id'])){
        $ins_params = array(
            "month_date" => $endOfMonthDate,
            "active_members" => !empty($resActiveMemnber['TotalActiveMember']) ? $resActiveMemnber['TotalActiveMember'] : 0,
        );
        $pdo->insert("active_members",$ins_params);
    }
echo "Completed";
dbConnectionClose();
exit;
?>
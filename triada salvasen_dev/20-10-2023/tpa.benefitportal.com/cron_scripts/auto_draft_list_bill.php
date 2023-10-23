<?php
exit;
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
//cron_scripts/auto_draft_list_bill.php
$listBillObj = new ListBill();
$today = date('Y-m-d');
echo "Date : ".$today."<br/>";

/*---------- System script status code start -----------*/
$cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
$cronWhere = array(":script_code" => "auto_draft_list_bill");
$cronRow = $pdo->selectOne($cronSql,$cronWhere);
if(!empty($cronRow)){
    $cronWhere = array(
        "clause" => "script_code=:script_code", 
        "params" => array(
            ":script_code" => 'auto_draft_list_bill'
        )
    );
    $pdo->update('system_scripts',array("is_running" => "Y","status"=>"Running","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
}
/*---------- System script status code ends -----------*/

/*---------- Update Auto Draft Date -----------*/
$tmp_date = new DateTime($today);
$next_auto_draft_date = addMonth($tmp_date,'1');

$cgs_sql = "SELECT cgs.id FROM customer_group_settings cgs WHERE cgs.is_auto_draft_set = 'Y' AND cgs.auto_draft_date='$today'";
$cgs_res = $pdo->select($cgs_sql);
if(!empty($cgs_res)) {
    foreach ($cgs_res as $key => $cgs_row) {
        $upd_data = array(
            'auto_draft_date' => date('Y-m-d',strtotime($next_auto_draft_date)),
            'updated_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $cgs_row['id']
            )
        );
        $pdo->update("customer_group_settings", $upd_data, $update_where);
    }
}
$gc_sql = "SELECT gc.id FROM group_company gc WHERE gc.is_auto_draft_set='Y' AND gc.auto_draft_date='$today'";
$gc_res = $pdo->select($gc_sql);
if(!empty($gc_res)) {
    foreach ($gc_res as $key => $gc_row) {
        $upd_data = array(
            'auto_draft_date' => date('Y-m-d',strtotime($next_auto_draft_date)),
            'updated_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $gc_row['id']
            )
        );
        $pdo->update("group_company", $upd_data, $update_where);
    }
}
/*----------/Update Auto Draft Date -----------*/

/*---------- Take List Bill Payment -----------*/
$list_bill_sql = "SELECT lb.id as list_bill_id,IF(lb.company_id > 0,gc.billing_id,cgs.billing_id) as billing_id
                  FROM list_bills lb
                  JOIN customer_group_settings cgs ON(cgs.customer_id = lb.customer_id)
                  LEFT JOIN group_company gc ON(gc.id = lb.company_id)
                  WHERE 
                  (cgs.is_auto_draft_set = 'Y' OR (lb.company_id > 0 AND gc.is_auto_draft_set='Y')) AND
                  lb.status='open' AND 
                  lb.is_deleted='N' AND 
                  (
                    (DATE(lb.next_purchase_date)='$today' AND lb.total_attempts=0) OR 
                    (DATE(lb.next_attempt_at)='$today' AND lb.total_attempts>0)
                  )";
$list_bill_res = $pdo->select($list_bill_sql);
foreach ($list_bill_res as $list_bill_row) {
    $other_params = array();
    $location = "auto_draft_list_bill_cron";
    $pay_lb_res = $listBillObj->pay_list_bill($list_bill_row['list_bill_id'],$list_bill_row['billing_id'],$location,$other_params);
}
/*----------/Take List Bill Payment -----------*/

/*--------- System script status code start ----------*/
if(!empty($cronRow)){
    $cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "auto_draft_list_bill");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);  
    
    $cronUpdParams = array("is_running" => "N","status"=>"Active","next_processed"=>date("Y-m-d H:i:s",strtotime("+1 day", strtotime($cronRow['last_processed']))));
    $cronWhere = array(
        "clause" => "script_code=:script_code", 
        "params" => array(
            ":script_code" => 'auto_draft_list_bill'
        )
    );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
}
/*---------- System script status code ends -----------*/

echo "<br>Process Complete";
dbConnectionClose();
?>
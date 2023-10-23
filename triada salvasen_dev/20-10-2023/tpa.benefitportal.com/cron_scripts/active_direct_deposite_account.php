<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$today = date('Y-m-d');

$activate_new_accounts = $pdo->select("SELECT id,effective_date,customer_id,status FROM direct_deposit_account where effective_date =:date and status='Inactive'",array(":date"=>$today));

if(!empty($activate_new_accounts)){

    foreach($activate_new_accounts as $account){

        $accounts = $pdo->selectOne("SELECT id,status,effective_date from direct_deposit_account where customer_id = :id and status='Active'",array(":id"=>$account['customer_id']));

        if(!empty($accounts['id']) && count($accounts) > 0){
            $upd_old_param = array(
                "status" => 'Inactive',
                "updated_at" => 'msqlfunc_NOW()'
            );
            $where = array(
                "clause" => "id=:id",
                "params" => array(":id"=>$accounts['id'])
            );
            //update record to inactive
            $pdo->update("direct_deposit_account",$upd_old_param,$where);
        }

        $upd_param = array(
            "status" => 'Active',
            "updated_at" => 'msqlfunc_NOW()'
        );

        $where1 = array(
            "clause" => "id=:id and effective_date=:date",
            "params" => array(":id"=>$account['id'],":date"=>$account['effective_date'])
        );
        //update record to active
        $pdo->update("direct_deposit_account",$upd_param,$where1);

    }
}

echo "<br>Completed";
dbConnectionClose();
exit;
?>
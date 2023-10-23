<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/includes/connect.php"; 

/*
    Script to create and process new request for commission_request created today before 30 minutes and is currently in pending stage
*/

$req_sql = "SELECT * FROM commission_requests WHERE STATUS='Pending' AND reprocess_id=0 AND DATE(created_at)=CURDATE() AND TIME(created_at) < TIME(NOW() - INTERVAL 30 MINUTE)";
$req_row = $pdo->select($req_sql);

if(!empty($req_row)) {
    foreach($req_row as $row){

        $request_id=add_commission_request($row['operation'],json_decode($row['request_params'],true),json_decode($row['extra_params'],true));

        /**  Mark existing request as reprocessed and update new request id **/
	    $update_data = array(
			'reprocess_id' => $request_id,
		);
		$update_where = array("clause" => "id=:id", "params" => array(":id" => $row['id']));
		$pdo->update("commission_requests",$update_data,$update_where);	
        /********* */

        sleep(3);
    }

    /*************Send Email ***********/
        $Message='Total Pending Commission Requests:'.count($req_row);
     	$DEFAULT_ORDER_EMAIL = array("punit.ladani@serenetic.in","dharmesh.nakum@serenetic.in");
        trigger_mail_to_email($Message, $DEFAULT_ORDER_EMAIL, $SITE_NAME ." : Pending Commissions Requests");
	/*************************************/
}
echo "<br>Completed";
dbConnectionClose();
exit;
?>

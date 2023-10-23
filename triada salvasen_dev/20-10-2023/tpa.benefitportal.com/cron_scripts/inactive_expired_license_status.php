<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);

/*
* NOTE :
* Inactive : if agent license expiration date is in past then update license status from Active to Inactive
*/

$today = date('Y-m-d');

$selLicenses = "SELECT l.id,l.selling_licensed_state,
                a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName
                FROM agent_license l
                JOIN customer a ON(l.agent_id=a.id)
                WHERE l.is_deleted='N' AND l.license_not_expire='N' 
                AND l.license_status='Active' 
                AND (l.license_exp_date IS NOT NULL AND DATE(l.license_exp_date) < :expiration_date)";
$resLicenses = $pdo->select($selLicenses,array(":expiration_date"=>$today));

if(!empty($resLicenses)){
    foreach ($resLicenses as $license) {
        $updParams = array("license_status" => "Inactive");
        $updWhere = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $license['id'],
            ),
        );
        $pdo->update("agent_license",$updParams,$updWhere);

        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'agent_detail_v1.php?id=' . md5($license['agentId']),
                'title' => $license['agentDispId'],
            ),
            'ac_message_1' => ' license state '.$license["selling_licensed_state"].' status updated from Active to Inactive',
        );
        $desc['ChangedBy'] = "Status updated by ".$DEFAULT_SITE_NAME.", Due to license expiration.";
        $desc = json_encode($desc);
        activity_feed(3,$license['agentId'],'Agent',$license['id'],'agent_license','Agent License Updated','','',$desc);

    }
}

echo count($resLicenses)." Completed";
dbConnectionClose();
exit;
?>
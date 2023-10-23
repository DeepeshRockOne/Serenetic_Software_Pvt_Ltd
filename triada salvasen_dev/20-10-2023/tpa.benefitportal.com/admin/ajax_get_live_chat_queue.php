<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$chatFrom = !empty($_POST['chatFrom']) ? $_POST['chatFrom'] : '';
$incr = "";
$schParams =array();

if(!empty($chatFrom)){
	$incr .=" AND sb_u.app_user_type = :app_user_type";
	$schParams[':app_user_type']=$chatFrom;
}
$sqlQueue = "SELECT sb_c.id as conversationID,sb_u.first_name,sb_u.last_name,sb_u.app_user_type,sb_c.creation_time,sb_u.app_user_id,md5(app_user_id) as encry,c.rep_id FROM $LIVE_CHAT_DB.sb_conversations sb_c
			JOIN $LIVE_CHAT_DB.sb_users sb_u ON (sb_u.id = sb_c.user_id)
			LEFT JOIN customer c ON (c.id = sb_u.app_user_id AND sb_u.app_user_type in ('Agent','Group','Customer'))
			WHERE sb_c.assign_id is null $incr AND sb_c.status_code NOT IN (3,4) ORDER BY sb_c.id DESC";
$resQueue = $pdo->select($sqlQueue,$schParams);


ob_start();

if(!empty($resQueue)){
	foreach ($resQueue as $key => $value) { ?>
		<?php 
		$dStart = new DateTime($value['creation_time']);
		$dEnd = new DateTime(gmdate("Y-m-d H:i:s"));
		$dDiff = $dStart->diff($dEnd);
		$hour = $dDiff->format('%h');
		$min = $dDiff->format('%i');

		$allow_link_click = true;
		if($value['app_user_type']=="Agent"){
    		$access ="Agent Portal";
    		$badge_class = "agent";
    		$url = "agent_detail_v1.php?id=".$value['encry'];
		}else if($value['app_user_type']=="Group"){
			$access ="Group Portal";
			$badge_class = "group";
			$url = "groups_details.php?id=".$value['encry'];
		}else if($value['app_user_type']=="Customer"){
			$access ="Member Portal";
			$badge_class = "member";
			$url = "members_details.php?id=".$value['encry'];
		}else if($value['app_user_type']=="Website"){
			$access ="External Website";
			$badge_class = "ex_web";
			$url="javascript:void(0)";
			$allow_link_click = false;
		}
		?>
		<tr>
	        <td>
	          <div class="queue_bedge <?= $badge_class ?>">
	            <?php 
              		$tmpString = $value['first_name'] .' '. $value['last_name']; 
              		$tmpExplode = explode(" ", $tmpString);
              	?>
                <?= strtoupper(substr($tmpExplode[0], 0, 1).substr($tmpExplode[1], 0, 1)); ?>
	          </div>
	        </td>
	        <td class="fw500"><?= $value['first_name'] .' '.$value['last_name'] ?> 
	            <?php if($allow_link_click) { ?>
	            	- <a href="<?= $url ?>"  target="_BLANK" class="fw300 red-link pn"><?= $value['rep_id'] ?></a>
	            <?php } ?> 
	        </td>
	        <td> <?= $access ?></td>
	        <td><a href="javascript:void(0);" class="btn btn-success assignQueue" data-conversation-id="<?= $value['conversationID'] ?>"><i class="fa fa-circle fa-lg" aria-hidden="true"></i> &nbsp;Pick From Queue</a></td>
	        <td class="text-center"> <?= $hour > 0 ? $hour .' hr ' : '' ?> <?= $min > 0 ? $min .' mins.' : '' ?> </td>
      	</tr>
	<?php }
}else{
	 ?>
	 	<tr>
	 		<td></td>
	 		<td></td>
	 		<td class="text-center">No Record(s) Found</td>
	 		<td></td>
	 		<td></td>
	 	</tr>
	 <?php
}

$res['queueCount'] = count($resQueue);


$html = ob_get_clean();
$res['html'] = $html;

header('Content-Type:appliaction/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
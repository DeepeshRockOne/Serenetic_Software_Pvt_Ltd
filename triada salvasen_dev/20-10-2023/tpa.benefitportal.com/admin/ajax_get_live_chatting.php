<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$chatFrom = !empty($_POST['chatFrom']) ? $_POST['chatFrom'] : '';
$adminDetail = !empty($_POST['adminDetail']) ? $_POST['adminDetail'] : '';
$incr = "";
$schParams =array();

if(!empty($chatFrom)){
	$incr .=" AND sb_u.app_user_type = :app_user_type";
	$schParams[':app_user_type']=$chatFrom;
}
if(!empty($adminDetail)){
	$incr .=" AND ((a.fname LIKE :name or a.lname LIKE :name) or (CONCAT(a.fname,' ',a.lname) like :name) OR (a.display_id like :name)) ";
	$schParams[':name']= '%'.$adminDetail.'%';
}

$sqlQueue = "SELECT sb_c.id as conversationID,sb_u.first_name,sb_u.last_name,sb_u.app_user_type,sb_c.creation_time,sb_u.app_user_id,md5(sb_u.app_user_id) as encry,c.rep_id,a.fname as admin_fname,a.lname as admin_lname,a.id as admin_id,a.display_id as admin_display_id,md5(a.id) as admin_encry,
	AES_DECRYPT(sb_m.message,'Gserw54sf533sS') as message,sb_m.creation_time as message_time,sb_c.assign_id,sb_c.assist_id
		FROM $LIVE_CHAT_DB.sb_conversations sb_c
			JOIN $LIVE_CHAT_DB.sb_users sb_u ON (sb_u.id = sb_c.user_id)
			LEFT JOIN customer c ON (c.id = sb_u.app_user_id AND sb_u.app_user_type in ('Agent','Group','Customer'))
			JOIN $LIVE_CHAT_DB.sb_users sb_ua ON (sb_ua.id = sb_c.assign_id AND sb_ua.app_user_type in ('Admin'))
			LEFT JOIN admin a ON (a.id = sb_ua.app_user_id)
			JOIN $LIVE_CHAT_DB.sb_messages sb_m ON (sb_c.id = sb_m.conversation_id AND sb_m.creation_time IN (SELECT MAX(creation_time) latest_creation_time FROM $LIVE_CHAT_DB.sb_messages GROUP BY conversation_id))
		WHERE sb_c.assign_id is not null AND sb_c.status_code NOT IN (3,4) $incr ORDER BY sb_m.id desc";
$resQueue = $pdo->select($sqlQueue,$schParams);


ob_start();

if(!empty($resQueue)){
	foreach ($resQueue as $key => $value) { ?>
		<?php 
		$dStart = new DateTime($value['message_time']);
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
	        <td class="fw500">
	          <div class="phone-control-wrap">
	            <div class="phone-addon w-55 text-left">
	              <div class="queue_bedge <?= $badge_class ?>">
	              	<?php 
	              		$tmpString = $value['first_name'] .' '. $value['last_name']; 
	              		$tmpExplode = explode(" ", $tmpString);
	              	?>
	                <?= strtoupper(substr($tmpExplode[0], 0, 1).substr($tmpExplode[1], 0, 1)); ?>
	              </div>
	            </div>
	            <div class="phone-addon text-left">
	              	<?= $value['first_name'] .' '.$value['last_name'] ?> 
	              	<?php if($allow_link_click) { ?>
	            	- <a href="<?= $url ?>"  target="_BLANK" class="fw300 red-link pn"><?= $value['rep_id'] ?></a>
	            	<?php } ?> 
	              	<p class="mn fs12 fw300"><i><?= $value['message'] ?></i> <strong><?= $hour > 0 ? $hour .' hr ' : '' ?> <?= $min > 0 ? $min .' mins.' : '' ?></strong></p>
	            </div>
	          </div>
	        </td>
	        <td> <?= $access ?></td>
	        <td>
	        	<?php if($_SESSION['sb-session']['id']!= $value['assign_id'] && empty($value['assist_id'])){ ?>
	        		<a href="javascript:void(0);" class="btn btn-info w-90 assistConversation" 	data-conversation-id="<?= $value['conversationID'] ?>">Assist</a>
	        	<?php } ?>
	        </td>
	        <td class="text-right">
	        	<?php if(!empty($value['admin_display_id'])){ ?>
	        		<a href="admin_profile.php?id=<?= $value['admin_encry'] ?>" target="_BLANK" class="fw500 red-link pn"><?= $value['admin_display_id'] ?></a><br><?= $value['admin_fname'] .' '. $value['admin_lname'] ?>
	        	<?php }else{ ?>
	        		-
	        	<?php } ?>
	        </td>
      	</tr>
	<?php }
}else{
	 ?>
	 	<tr>
	 		<td></td>
	 		<td></td>
	 		<td class="text-center">No Record(s) Found</td>
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
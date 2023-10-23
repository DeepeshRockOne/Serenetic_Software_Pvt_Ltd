<?php
if (!isset($lastId)) {
	$lastId = 0;
}
if (!isset($limit)) {
	$limit = 20;
}
$notifications = getAllAdminNotification("", 0, $lastId, $limit);
$notifications_received_total = count($notifications);
//this is used to count unread message only
$notifications_total = count(getAllAdminNotification(" AND n.is_unread='N'", 0));
ob_start();
if ($notifications_received_total > 0) {
	$exists = false;
	foreach ($notifications as $key => $value) {
		// echo count($notifications);
		// echo $key + 1 . "-" . $limit;

		if ($key + 1 == $limit) {
			break;
		}

		$notification_user_href = gnuAdmin($value["href"], $value["id"]);
		$isOpenColorBox = false;
		/** Parsing href start*/
		$notification_url = $notification_user_href;
		$notification_query_str = parse_url($notification_url, PHP_URL_QUERY);
		parse_str($notification_query_str, $notification_query_params);
		if (is_array($notification_query_params)) {
			if (array_key_exists("voice_mail_id", $notification_query_params)) {
				//if notification for voice mail then goes here
				$voice_mail_id = $notification_query_params["noti_gid"];
				$isOpenColorBox = true;
			}
		}
		/** Parsing href end*/
		$notification_final_string = str_replace(
			array(
				"{short_msg}",
			),
			array(
				(isset($value["comment_text"]) ? $value["comment_text"] : ''),
			),
			(isset($value["template_text"]) ? $value["template_text"] : '')
		);

		if($value['noti_template_id'] == 7){
			$notification_final_string = str_replace('{agent_name}', $value['senderhandle'], $value["template_text"]);
			$member_name_res = $pdo->selectOne("SELECT fname,lname FROM customer WHERE id = :id", array(':id' => $value['extra']));
			$notification_final_string = str_replace('{member_name}', ($member_name_res['fname'] . ' ' . $member_name_res['lname']), $notification_final_string);
		}

		$display = '';
		if(in_array($value['noti_template_id'],array(10,11,12))){
			if(in_array($_SESSION['admin']['type'],array('Executive','Development'))){
				$display = 'style="display:block"';
			}else{
				$display = 'style="display:none"';
				if(count($notifications) == 1 && !$exists){	
					echo "<p class='text-center'>You're all caught up and have no alerts.</p>";
				}
			}
		}

		?>
    <div class="notification_full <?=getAdminOpenedClass($value["is_opened"])?>" data-noti="<?=$value["id"]?>" data-colorbox="<?=$isOpenColorBox?>" data-href="<?=$notification_user_href?>"  <?=$display?>>
      <div class="mail-contnet waves-effect redirectNotification">
      	<?php if($value['noti_template_id'] == 7) { ?>
      		<h5><?=$member_name_res['fname'] . ' ' . $member_name_res['lname']?></h5>
        <?php } else { ?>
        	<h5><?=$value["senderhandle"]?></h5>
        <?php } ?>
        <span class="mail-desc"><?=$notification_final_string?></span>
        <span class="time"><?=getAdminTime(date("Y-m-d H:i:s", strtotime($value["created_at"])));?></span>
      </div>
      <a href="Javascript:void(0)" class="remove_notification"><i class="ti-close"></i></a>
    </div>
<!-- <div class="notification_full noti_processor" data-noti="" data-colorbox="" data-href="">
	<div class="mail-contnet waves-effect redirectNotification">
		<span class="mail-desc">The Merchant Processor [[Merchant Processor Name]] has
		reached 90% of the Sales Threshold.</span>
		<a class="link">Go to Merchant Processors</a>
	</div>
	<a href="Javascript:void(0)" class="remove_notification"><i class="ti-close"></i></a>
</div> -->

    <?php }
} else {?>
        <p class="text-center">You're all caught up and have no alerts.</p>
      <?php }
$listNotification = ob_get_clean();
?>

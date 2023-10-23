<?php

if (!isset($lastId)) {
	$lastId = 0;
}
if (!isset($limit)) {
	$limit = "LIMIT 0,5";
}
$notifications = getAllNotification("", $_SESSION["agents"]["id"], $lastId, $limit);
$notifications_received_total = count($notifications);
//this is used to count unread message only
$notifications_total = count(getAllNotification("AND n.is_unread='N'", $_SESSION["agents"]["id"]));
ob_start();
if ($notifications_received_total > 0) {
	foreach ($notifications as $key => $value) {
		// echo count($notifications);
		// echo $key + 1 . "-" . $limit;

		if ($key + 1 == $limit) {
			break;
		}

		$notification_user_href = gnu($value["href"], $value["id"]);
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
			if (array_key_exists("reject_id", $notification_query_params)) {
				//pre_print("sdkjfchk	");
				$isOpenColorBox = true;
			}
		}
		/** Parsing href end*/
		$extend_date = "";
		$extend_state = "";
		if (isJson($value["extra"])) {
			$extraData = json_decode($value["extra"], true);
			$extend_date = (!empty($extraData["dt"]) ? $extraData["dt"] : "");
			$extend_state = (!empty($extraData["state"]) ? $extraData["state"] : "");
		} else {
			$extend_date = ucwords($value["extra"]);
		}
		$notification_final_string = str_replace(
			array(
				"{short_msg}",
				"{extend_date}", "{of_state}",
				"{days}",
				"{extra_data}",
			),
			array(
				'',
				$extend_date, $extend_state,
				$value["extra"],
				$value["extra"],
			),
			$value["template_text"]
		);

		?>
		<div class="notification_full <?=getOpenedClass($value["is_opened"])?>" data-noti="<?=$value["id"]?>" data-colorbox="<?=$isOpenColorBox?>" data-href="<?=$notification_user_href?>">
			<div class="mail-contnet waves-effect redirectNotification">
				
          		<?php 
          		if ($value['sender_type'] != "Admin") {
					echo '<h5>'.$value["senderhandle"].'</h5>';
				}
				?>
				<span class="mail-desc"><?=str_replace('?', "'", utf8_decode($notification_final_string))?></span>
              	<span class="time"><?=getTime(date("Y-m-d H:i:s", strtotime($value["created_at"])));?></span>
			</div>
			<a href="Javascript:void(0)" class="noti_close remove_notification"><i class="ti-close"></i></a>
	    </div>
        <?php }
} else {?>
        <p class="text-center">You're all caught up and have no alerts.</p>
      <?php }
$listNotification = ob_get_clean();
?>

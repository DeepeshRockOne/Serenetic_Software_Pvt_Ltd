<?php
/** Notification function start */
/**
 * [humanTiming return human redable time on time onject]
 * @param  [type] $time [pass time object int value]
 * @return [type]       [human redable time]
 * @author HR
 */
function humanTimingAdmin($time) {
	$time = time() - $time; // to get the time since that moment
	$tokens = array(
		31536000 => 'y',
		2592000 => 'mo.',
		604800 => 'w',
		86400 => 'd',
		3600 => 'h',
		60 => 'm',
		1 => 's',
	);
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) {
			continue;
		}
		$numberOfUnits = floor($time / $unit);
		if ($text == 's') {
			return '';
		} else {
			return $numberOfUnits . '' . $text . (($numberOfUnits > 1) ? '' : '');
		}
	}
}
/**
 * [getTime used to get human redable format times]
 * @param  [type]  $date         [date string]
 * @param  boolean $seconds [if on date param you pass directly time() then pass its true default false]
 * @return [type]                [human redable time]
 * @author HR
 */
function getAdminTime($date, $seconds = false) {
	if ($seconds == false) {
		$date = strtotime($date);
	}
	$time = humanTimingAdmin($date);
	if ($time == "") {
		$time = "Now";
	} else {
		$time .= " ago";
	}

	return $time;
}

/**
 * [addNotification Generate Notification for hoole]
 * @param [type]  $recipient_id [users whos watch this notification]
 * @param [type]  $template_id   [display starting text]
 * @param [type]  $href         [url]
 * @param [type]  $comment_id   [if comment then comment id]
 * @param integer $sender_id    [users whos created this notifications]
 * @author HR
 */

function addAdminNotification($recipient_id, $template_id=1, $href = "#", $comment_id = 0, $force_display = 'N', $sender_id = 0, $extraData = "") {
	global $pdo;

	/*if (is_array($recipient_id) && empty($recipient_id)) {
		return 0;
	}
	if (!is_array($recipient_id) && $recipient_id <= 0) {
		return 0;
	}*/
	if ($sender_id == 0) {
      if(isset($_SESSION["agents"]["id"])){
				$sender_id = $_SESSION["agents"]["id"];
      }
	}
	//if ($sender_id > 0) {
	if (1) {
		/**
		 * [$detail_id this id always create but when recipient id is in array then we only create one single entry for that]
		 * @var [create entry in table]
		 */
		$detail_id = $pdo->insert("admin_notifications_details", array(
			"noti_template_id" => $template_id,
			"comment_id" => $comment_id,
			"href" => $href,
			"extra" => $extraData,
		)
		);
		if (!is_array($recipient_id)) {
			$recipient_id = array($recipient_id);
		}
		foreach ($recipient_id as $id) {
			$notification_array = array(
				"sender_id" => $sender_id,
				"recipient_id" => $id,
				"noti_detail_id" => $detail_id,
				"force_display" => $force_display,
				"created_at" => "msqlfunc_NOW()",
			);
			//check recipient notification
			/*$getNotificaionSetting = getNotificaionSetting($id);
			if (trim($getNotificaionSetting) == "notification_off") {
				$notification_array["is_unread"] = "Y";
			}*/
			$pdo->insert("admin_notifications", $notification_array);
		}
		return 1;
	} else {
		return 0;
	}
}
function deleteAdminNotification($recipient_id, $template_id, $href = "#", $comment_id = 0, $sender_id = 0) {
	global $pdo;
	if ($sender_id == 0) {
		$sender_id = $_SESSION["agents"]["id"];
	}
	if ($sender_id > 0) {
		/**
		 * [$detail_id this id always create but when recipient id is in array then we only create one single entry for that]
		 * @var [create entry in table]
		 */
		if (!is_array($recipient_id)) {
			$recipient_id = array($recipient_id);
		}
		$notification_detail_id = 0;
		foreach ($recipient_id as $recipient_id_single) {
			$deleteSelectQuery = $pdo->selectOne("SELECT n.sender_id,n.recipient_id,nd.id from admin_notifications n LEFT JOIN admin_notifications_details nd ON(nd.id=n.noti_detail_id) WHERE n.sender_id=:sender_id AND n.recipient_id=:recipient_id AND nd.href=:href AND nd.comment_id=:comment_id",
				array(
					":sender_id" => $sender_id,
					":recipient_id" => $recipient_id_single,
					":href" => $href,
					":comment_id" => $comment_id,
				));
			$notification_detail_id = $deleteSelectQuery["id"];
			if ($notification_detail_id > 0) {
				break;
			}
		}
		if ($notification_detail_id > 0) {
			$pdo->delete("delete from admin_notifications where noti_detail_id=:noti_detail_id", array(":noti_detail_id" => $notification_detail_id));
			$detail_id = $pdo->delete("delete from admin_notifications_details where id=:notification_detail_id", array(":notification_detail_id" => $notification_detail_id)
			);
		}
		return 1;
	} else {
		return 0;
	}
}
/**
 * [removeNotification remove notification by notiid]
 * @param  [type] $noti_id [notification id]
 * @return [type]          [void]
 * @author HR
 */
function removeAdminNotification($noti_id, $sender_id = 0) {
	global $pdo;
	if ($sender_id == 0) {
		$sender_id = $_SESSION["agents"]["id"];
	}
	$result = $pdo->selectOne("select noti_detail_id from admin_notifications where id=:id AND recipient_id=:recipient_id", array(":id" => $noti_id, ":recipient_id" => $sender_id));

	if (!empty($result)) {
		$notification_detail_id = $result["noti_detail_id"];
		$pdo->delete("delete from admin_notifications where noti_detail_id=:noti_detail_id", array("noti_detail_id" => $notification_detail_id));
		$detail_id = $pdo->delete("delete from admin_notifications_details where id=:notification_detail_id", array(":notification_detail_id" => $notification_detail_id));
	}
}
/**
 * [openNotification used to set flag of open on notification]
 * @param  [type] $notificationId [unique notification id]
 * @return [type]                 [void]
 * @author HR
 */
function openAdminNotification($notificationId, $recipient_id = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$updateParams = array(
		'is_opened' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND sender_id=:recipient_id',
		'params' => array(
			":id" => $notificationId,
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('admin_notifications', $updateParams, $where);
	readAdminNotification($notificationId, $recipient_id);
}
function openAllAdminNotification($recipient_id = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$updateParams = array(
		'is_opened' => 'Y',
	);
	$where = array(
		'clause' => 'sender_id=:recipient_id',
		'params' => array(
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('admin_notifications', $updateParams, $where);
	readAdminNotification(0, $recipient_id);
}
/**
 * [readNotification used to set read flag on notification]
 * @param  integer $notificationId [unique notification id]
 * @return [type]                  [void]
 * @author HR
 */
function readAdminNotification($notificationId = 0, $recipient_id = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$where = array();
	if ($notificationId == 0) {
		$where = array(
			'clause' => 'sender_id = :recipient_id',
			'params' => array(
				":recipient_id" => $recipient_id,
			),
		);
	} else {
		$where = array(
			'clause' => 'id = :id',
			'params' => array(
				":id" => $notificationId,
			),
		);
	}
	$updateParams = array(
		'is_unread' => 'Y',
	);

	$pdo->update('admin_notifications', $updateParams, $where);
}
function getAdminOpenedClass($status) {
	return ($status == 'N' ? "noti_unopened" : "");
}
/**
 * [gnu get notification url]
 * @param  [type] $url [description]
 * @return [type]      [description]
 * @author HR
 */
function gnuAdmin($url, $notification_id) {
	global $ADMIN_HOST;
	return str_replace(array("{HOST}"), array($ADMIN_HOST), $url) . "&noti_id=" . $notification_id;

}
function getAllAdminNotification($incr = "", $recipient_id = 0, $lastId = 0, $cnt = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$LIMIT = "";
	if ($cnt > 0) {
		$cnt = $cnt + 1;
		$LIMIT = "LIMIT 0,$cnt";
	}

	if ($lastId > 0) {
		$incr .= "AND n.id<$lastId";
	}

	$notifications_sql = "SELECT n.*,nd.href,nd.noti_template_id,nd.comment_id,nt.template_text,CONCAT_WS(' ',sender.fname,sender.lname) as senderhandle,CONCAT_WS(' ',recipient.fname,recipient.lname) as recipienthandle,nd.extra FROM admin_notifications n
	LEFT JOIN customer recipient on(n.recipient_id=recipient.id)
	LEFT JOIN customer sender on(n.sender_id=sender.id)
	JOIN admin_notifications_details nd on(nd.id=n.noti_detail_id)
	JOIN admin_notifications_template nt on(nt.id=nd.noti_template_id)
	WHERE n.recipient_id=:recipient_id
	AND (n.sender_id !=:recipient_id OR force_display='Y')
	AND n.is_deleted='N'
	$incr ORDER BY n.id DESC $LIMIT";
	//AND (n.created_at >= NOW()- INTERVAL 14 DAY)
	return $pdo->select($notifications_sql, array(":recipient_id" => $recipient_id));
}
function clearAdminNotification($notificationId, $recipient_id = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND recipient_id=:recipient_id',
		'params' => array(
			":id" => $notificationId,
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('admin_notifications', $updateParams, $where);
}
function clearAdminAllNotification($recipient_id = 0) {
	global $pdo;
	if ($recipient_id == 0) {
		$recipient_id = 0;
	}
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'recipient_id=:recipient_id',
		'params' => array(
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('admin_notifications', $updateParams, $where);
}
/*--- Agent Notifications Functions ---*/
/**
 * [getTime used to get human redable format times]
 * @param  [type]  $date         [date string]
 * @param  boolean $seconds [if on date param you pass directly time() then pass its true default false]
 * @return [type]                [human redable time]
 * @author HR
 */
function getTime($date, $seconds = false) {
	if ($seconds == false) {
		$date = strtotime($date);
	}
	$time = humanTiming($date);
	if ($time == "") {
		$time = "Now";
	} else {
		$time .= " ago";
	}

	return $time;
}
/**
 * [humanTiming return human redable time on time onject]
 * @param  [type] $time [pass time object int value]
 * @return [type]       [human redable time]
 * @author HR
 */
function humanTiming($time) {
	$time = time() - $time; // to get the time since that moment
	$tokens = array(
		31536000 => 'y',
		2592000 => 'mo.',
		604800 => 'w',
		86400 => 'd',
		3600 => 'h',
		60 => 'm',
		1 => 's',
	);
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) {
			continue;
		}
		$numberOfUnits = floor($time / $unit);
		if ($text == 's') {
			return '';
		} else {
			return $numberOfUnits . '' . $text . (($numberOfUnits > 1) ? '' : '');
		}
	}
}
function gnu($url, $notification_id) {
	global $ADMIN_HOST,$AGENT_HOST,$GROUP_HOST,$CUSTOMER_HOST;
	return str_replace(array("{ADMIN}","{AGENT}","{GROUP}","{CUSTOMER}"), array($ADMIN_HOST,$AGENT_HOST,$GROUP_HOST,$CUSTOMER_HOST),$url) . "&noti_id=" . $notification_id;

}
function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}
function getOpenedClass($status) {
	return ($status == 'N' ? "noti_unopened" : "");
}
function getAllNotification($incr = "",$recipient_id = 0, $lastId = 0, $cnt = "") {
	global $pdo;
	$LIMIT = "";
	if (!empty($cnt)) {
		$LIMIT = $cnt;
	}

	if ($lastId > 0) {
		$incr .= "AND n.id<$lastId";
	}

	$notifications_sql = "SELECT n.*,nd.href,nd.noti_template_id,nd.comment_id,nt.template_text,IF(n.sender_type='Admin',CONCAT_WS(' ',sender1.fname,sender1.lname),CONCAT_WS(' ',sender.fname,sender.lname)) as senderhandle,CONCAT_WS(' ',recipient.fname,recipient.lname) as recipienthandle,nd.extra FROM users_notifications n
	LEFT JOIN customer recipient on(n.recipient_id=recipient.id)
	LEFT JOIN customer sender on(n.sender_id=sender.id AND n.sender_type != 'Admin')
	LEFT JOIN admin sender1 on(n.sender_id=sender1.id AND n.sender_type='Admin')
	JOIN users_notifications_details nd on(nd.id=n.noti_detail_id)
	JOIN users_notifications_template nt on(nt.id=nd.noti_template_id)
	WHERE n.recipient_id=:recipient_id
	AND (n.sender_id != :recipient_id OR force_display='Y')
	AND n.is_deleted='N'
	$incr ORDER BY n.id DESC $LIMIT";
	$ar=$pdo->select($notifications_sql, array(":recipient_id" => $recipient_id));
	return $ar;
}

function clearNotification($notificationId,$recipient_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND recipient_id=:recipient_id',
		'params' => array(
			":id" => $notificationId,
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('users_notifications', $updateParams, $where);
}

function clearAllNotification($recipient_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'recipient_id=:recipient_id',
		'params' => array(
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('users_notifications', $updateParams, $where);
}

/**
 * [readNotification used to set read flag on notification]
 * @param  integer $notificationId [unique notification id]
 * @return [type]                  [void]
 * @author HR
 */
function readNotification($notificationId = 0, $recipient_id = 0) {
	global $pdo;
	$where = array();
	if ($notificationId == 0) {
		$where = array(
			'clause' => 'recipient_id = :recipient_id',
			'params' => array(
				":recipient_id" => $recipient_id,
			),
		);
	} else {
		$where = array(
			'clause' => 'id = :id',
			'params' => array(
				":id" => $notificationId,
			),
		);
	}
	$updateParams = array(
		'is_unread' => 'Y',
	);

	$pdo->update('users_notifications', $updateParams, $where);
}
/**
 * [addNotification Generate Notification for hoole]
 * @param [type]  $recipient_id [users whos watch this notification]
 * @param [type]  $template_id   [display starting text]
 * @param [type]  $href         [url]
 * @param [type]  $comment_id   [if comment then comment id]
 * @param integer $sender_id    [users whos created this notifications]
 * @author HR
 */
function addNotification($recipient_id,$template_id, $href = "#", $comment_id = 0, $force_display = 'N', $sender_id = 0, $sender_type = '', $extraData = "") {
	global $pdo;
	
	/**
	 * [$detail_id this id always create but when recipient id is in array then we only create one single entry for that]
	 * @var [create entry in table]
	 */
	$detail_data = array(
		"noti_template_id" => $template_id,
		"comment_id" => $comment_id,
		"href" => $href,
		"extra" => $extraData,
	);
	$detail_id = $pdo->insert("users_notifications_details",$detail_data);

	if (!is_array($recipient_id)) {
		$recipient_id = array($recipient_id);
	}

	foreach ($recipient_id as $id) {
		$notification_array = array(
			"sender_id" => $sender_id,
			"sender_type" => $sender_type,
			"recipient_id" => $id,
			"noti_detail_id" => $detail_id,
			"force_display" => $force_display,
			"created_at" => "msqlfunc_NOW()",
		);
		$pdo->insert("users_notifications", $notification_array);
	}
	return 1;
}
function deleteNotification($recipient_id, $template_id, $href = "#", $comment_id = 0, $sender_id = 0) {
	global $pdo;
	
	if ($sender_id > 0) {
		/**
		 * [$detail_id this id always create but when recipient id is in array then we only create one single entry for that]
		 * @var [create entry in table]
		 */
		if (!is_array($recipient_id)) {
			$recipient_id = array($recipient_id);
		}
		$notification_detail_id = 0;
		foreach ($recipient_id as $recipient_id_single) {
			$deleteSelectQuery = $pdo->selectOne("SELECT n.sender_id,n.recipient_id,nd.id from users_notifications n LEFT JOIN users_notifications_details nd ON(nd.id=n.noti_detail_id) WHERE n.sender_id=:sender_id AND n.recipient_id=:recipient_id AND nd.href=:href AND nd.comment_id=:comment_id",
				array(
					":sender_id" => $sender_id,
					":recipient_id" => $recipient_id_single,
					":href" => $href,
					":comment_id" => $comment_id,
				));
			$notification_detail_id = $deleteSelectQuery["id"];
			if ($notification_detail_id > 0) {
				break;
			}
		}
		if ($notification_detail_id > 0) {
			$pdo->delete("delete from users_notifications where noti_detail_id=:noti_detail_id", array(":noti_detail_id" => $notification_detail_id));
			$detail_id = $pdo->delete("delete from users_notifications_details where id=:notification_detail_id", array(":notification_detail_id" => $notification_detail_id)
			);
		}
		return 1;
	} else {
		return 0;
	}
}
/**
 * [removeNotification remove notification by notiid]
 * @param  [type] $noti_id [notification id]
 * @return [type]          [void]
 * @author HR
 */
function removeNotification($noti_id, $sender_id = 0) {
	global $pdo;
	$result = $pdo->selectOne("select noti_detail_id from users_notifications where id=:id AND recipient_id=:recipient_id", array(":id" => $noti_id, ":recipient_id" => $sender_id));

	if (!empty($result)) {
		$notification_detail_id = $result["noti_detail_id"];
		$pdo->delete("delete from users_notifications where noti_detail_id=:noti_detail_id", array("noti_detail_id" => $notification_detail_id));
		$detail_id = $pdo->delete("delete from users_notifications_details where id=:notification_detail_id", array(":notification_detail_id" => $notification_detail_id));
	}
}
/**
 * [openNotification used to set flag of open on notification]
 * @param  [type] $notificationId [unique notification id]
 * @return [type]                 [void]
 * @author HR
 */
function openNotification($notificationId, $recipient_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_opened' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND recipient_id=:recipient_id',
		'params' => array(
			":id" => $notificationId,
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('users_notifications', $updateParams, $where);

	readNotification($notificationId, $recipient_id);
}
function openAllNotification($recipient_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_opened' => 'Y',
	);
	$where = array(
		'clause' => 'recipient_id=:recipient_id',
		'params' => array(
			":recipient_id" => $recipient_id,
		),
	);
	$pdo->update('users_notifications', $updateParams, $where);
	readNotification(0, $recipient_id);
}
/*---/Agent Notifications Functions ---*/

// Quote Notification start
function getAllQuoteNotification($incr = "", $agent_id = 0, $lastId = 0, $cnt = 0) {
	global $pdo;
	$LIMIT = "";

	if ($cnt > 0) {
		$cnt = $cnt + 1;
		$LIMIT = "LIMIT 0,$cnt";
	}
	if ($lastId > 0) {
		$incr .= "AND qnd.id<$lastId";
	}
	$quote_notifications_sql = "SELECT qnd.*, l.fname, l.lname, qn.text
			FROM quote_notification_details as qnd
			JOIN quote_notification as qn ON (qnd.quote_noti_id = qn.id)
			JOIN lead_quote_details as lqd ON (lqd.id = qnd.quote_id AND lqd.status = 'Pending' AND lqd.is_assisted_enrollment = 'N')
			JOIN leads as l on(l.id = qnd.lead_id AND lqd.lead_id = l.id AND l.is_deleted = 'N')
			WHERE qnd.agent_id=:agent_id AND qnd.is_deleted='N' AND qnd.created_at >= (DATE(NOW()) - INTERVAL 14 DAY) $incr ORDER BY qnd.id DESC $LIMIT";

	return $pdo->select($quote_notifications_sql, array(":agent_id" => $agent_id));
}

function getAllEnrollNotification($incr = "", $agent_id = 0, $lastId = 0, $cnt = 0) {
	global $pdo;
	$LIMIT = "";

	if ($cnt > 0) {
		$cnt = $cnt + 1;
		$LIMIT = "LIMIT 0,$cnt";
	}
	if ($lastId > 0) {
		$incr .= "AND qnd.id<$lastId";
	}
	$quote_notifications_sql = "SELECT qnd.*, l.fname, l.lname, qn.text
			FROM quote_notification_details as qnd
			JOIN quote_notification as qn ON (qnd.quote_noti_id = qn.id)
			JOIN lead_quote_details as lqd ON (lqd.id = qnd.quote_id AND lqd.status = 'Pending' AND lqd.is_assisted_enrollment = 'Y')
			JOIN leads as l on(l.id = qnd.lead_id AND lqd.lead_id = l.id AND l.is_deleted = 'N')
			WHERE qnd.agent_id=:agent_id AND qnd.is_deleted='N' AND qnd.created_at >= (DATE(NOW()) - INTERVAL 14 DAY) $incr ORDER BY qnd.id DESC $LIMIT";

	return $pdo->select($quote_notifications_sql, array(":agent_id" => $agent_id));
}

function openQuoteNotification($notificationId, $agent_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_read' => 'Y',
	);

	$where = array(
		'clause' => 'id = :id AND agent_id=:agent_id',
		'params' => array(
			":id" => $notificationId,
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}

function clearQuoteNotification($notificationId, $agent_id = 0) {
	global $pdo;

	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND agent_id=:agent_id',
		'params' => array(
			":id" => $notificationId,
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}

function clearAllQuoteNotification($agent_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'agent_id=:agent_id',
		'params' => array(
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}
// Quote Notification end

// Enroll Notification start

function openEnrollNotification($notificationId, $agent_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_read' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND agent_id=:agent_id',
		'params' => array(
			":id" => $notificationId,
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}

function clearEnrollNotification($notificationId, $agent_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'id = :id AND agent_id=:agent_id',
		'params' => array(
			":id" => $notificationId,
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}

function clearAllEnrollNotification($agent_id = 0) {
	global $pdo;
	$updateParams = array(
		'is_deleted' => 'Y',
	);
	$where = array(
		'clause' => 'agent_id=:agent_id',
		'params' => array(
			":agent_id" => $agent_id,
		),
	);
	$pdo->update('quote_notification_details', $updateParams, $where);
}
// Enroll Notification end
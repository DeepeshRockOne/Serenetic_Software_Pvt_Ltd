<?php include_once "layout/start.inc.php";
$member_id = 0;
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	if ($member_id > 0||$member_id==0) {
		if (isset($_POST['action'])) {
			extract($_POST);
			if ($action == "noti" || $action == "noti_remaining") {
				$limit = $action == "noti" ? 20 : 20;
				$lastId = $lid;
				//readAdminNotification(0, $member_id);
				include_once __DIR__ . "/layout/header_notification.inc.php";
				$data["html"] = trim($listNotification);
				$data["count"] = $notifications_received_total;
				$data["limit"] = $limit;
				$data["code"] = 200;
				$data["msg"] = -1;
			} else if ($action == "get") {
				$data["code"] = 200;
				$data["msg"] = -1;
				$notificationCount = 0;
				$rows = getAllAdminNotification("AND n.is_unread='N'", $member_id);
				if(count($rows) > 0){
						foreach($rows as $row){
							if(in_array($row['noti_template_id'],array(10,11,12))){
								if(in_array($_SESSION['admin']['type'],array('Executive','Development'))){
									$notificationCount+=1;
								}
							}else{
								$notificationCount+=1;
							}
					}
				}
				$data["notificationCount"] = $notificationCount;
			} else if ($action == "allopen") {
				$data["code"] = 200;
				$data["msg"] = -1;
				openAllAdminNotification($member_id);
			} else if ($action == "hidenoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				openAdminNotification($id, $member_id);
			}else if ($action == "allClear") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearAdminAllNotification($member_id);
			} else if ($action == "clearNoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearAdminNotification($id, $member_id);
			} else {
				$data["msg"] = "Sorry! You have no rights to access this page.";
				$data["code"] = 100;
			}
		} else {
			$data["msg"] = "Sorry! You have no rights to access this page.";
			$data["code"] = 100;
		}

	} else {
		$data["msg"] = "Sorry! You have no rights to access this page.";
		$data["code"] = 100;
	}
} else {
	$data["msg"] = "Sorry! You have no rights to access this page.";
	$data["code"] = 100;
}
echo json_encode($data);
dbConnectionClose();
exit;
?>
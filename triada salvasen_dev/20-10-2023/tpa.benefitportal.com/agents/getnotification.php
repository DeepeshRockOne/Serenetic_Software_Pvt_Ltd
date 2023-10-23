 <?php include_once "includes/connect.php";
$member_id = $_SESSION["agents"]["id"];
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	if ($member_id > 0) {
		if (isset($_POST['action'])) {
			extract($_POST);
			if ($action == "noti" || $action == "noti_remaining") {
				$limit = $action == "noti" ? 'LIMIT 20':'LIMIT 20';
				$lastId = isset($lid)?$lid:0;
				//readNotification(0, $member_id);
				include_once __DIR__ . "/layout/header_notification.inc.php";
				$data["html"] = trim($listNotification);
				$data["count"] = $notifications_received_total;
				$data["limit"] = $limit;
				$data["code"] = 200;
				$data["msg"] = -1;
			} else if ($action == "get") {
				$data["code"] = 200;
				$data["msg"] = -1;
				$data["notificationCount"] = count(getAllNotification("AND n.is_unread='N'", $member_id, 0,'LIMIT 20'));
			} else if ($action == "allopen") {
				$data["code"] = 200;
				$data["msg"] = -1;
				openAllNotification($member_id);
			} else if ($action == "hidenoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				openNotification($id, $member_id);
			} else if ($action == "allClear") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearAllNotification($member_id);
			} else if ($action == "clearNoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearNotification($id, $member_id);
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
exit;

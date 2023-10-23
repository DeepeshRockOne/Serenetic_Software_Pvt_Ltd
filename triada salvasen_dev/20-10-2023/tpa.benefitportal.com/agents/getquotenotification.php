 <?php include_once "includes/connect.php";
$member_id = $_SESSION["agents"]["id"];
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	if ($member_id > 0) {
		if (isset($_POST['action'])) {
			extract($_POST);
			if ($action == "noti" || $action == "noti_remaining") {
				$limit = $action == "noti" ? 20 : 20;
				$lastId = $lid;
				include_once __DIR__ . "/layout/header_quote_notification.inc.php";
				$data["html"] = trim($listQuoteNotification);
				$data["count"] = $quote_notifications_received_total;
				$data["limit"] = $limit;
				$data["code"] = 200;
				$data["msg"] = -1;
			} else if ($action == "get") {
				$data["code"] = 200;
				$data["msg"] = -1;
				$data["quoteNotificationCount"] = count(getAllQuoteNotification("AND qnd.is_read='N'", $member_id, 0, 20));
			} else if ($action == "allopen") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearAllQuoteNotification($member_id);
			} else if ($action == "hidenoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearQuoteNotification($id, $member_id);
			} else if ($action == "allClear") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearAllQuoteNotification($member_id);
			} else if ($action == "clearNoti") {
				$data["code"] = 200;
				$data["msg"] = -1;
				clearQuoteNotification($id, $member_id);
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
?>
<?php

include_once dirname(__DIR__) . "/includes/connect.php";

sleep(rand(1,3));
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

foreach ($obj as $key => $value) {
  sleep(rand(1,3));
  $user_id = !empty($value["user_id"]) ? $value["user_id"] : "";
  if(!empty($user_id)){
    $splitUserId = explode('_', $user_id);
    if ($splitUserId[0] == "salvasen") {
        $insLog = array(
                  'log_id' => $splitUserId[1],
                  'status' => $value['event'],
                  'code' => $user_id,
                  'response' => json_encode($value),
                  'created_at' => date('Y-m-d H:i:s', $value['timestamp']),
                );
        if(!empty($value["ip"])){
          $insLog["ip_address"] = $value["ip"];
        }
      $pdo->insert("email_log_details", $insLog);
    }
  }
}

?>
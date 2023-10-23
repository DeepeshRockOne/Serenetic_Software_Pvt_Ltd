<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();

$res = array();
$days = array();
$daysData = array();

for ($i = 1; $i <= date('t'); $i++) {
  $day = ($i<=9) ? '0'.$i : $i;
  $days[] =  date('m').'/'.$i;
  $date = date('Y-m').'-'.$day;

  $res = $LiveChat->get_chats_per_day($date);
  $total_served = $res["totalServed"];
  $total_members = $res["totalMembers"];
  $total_agents = $res["totalAgents"];
  $total_groups = $res["totalGroups"];
  $total_websites = $res["totalWebsites"];

  $daysData[] = array(
        "name" => date("M d, Y",strtotime($date)),
        "y" => (int)$total_served,
        'total_served' => $total_served,
        'total_members' => $total_members,
        'total_agents' => $total_agents,
        'total_groups' => $total_groups,
        'total_websites' => $total_websites,
    );
}
$res["livechat_bar_chart"] = array('days' => $days,'daysData' => $daysData);
header("Content-type: application/json;");
echo json_encode($res);
dbConnectionClose();
exit;
?>
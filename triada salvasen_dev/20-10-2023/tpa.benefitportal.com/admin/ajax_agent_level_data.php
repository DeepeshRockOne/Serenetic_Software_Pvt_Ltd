<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/function.class.php';

$function = new functionsList();
$id = checkIsset($_POST['id']);
$data = checkIsset($_POST['data']);

$agentSql = "SELECT c.id,CONCAT(c.fname,' ',c.lname) as name, c.rep_id, cs.agent_coded_id, c.business_name, c.type 
				FROM customer c
				JOIN customer_settings cs ON (c.id = cs.customer_id)
				WHERE md5(c.id) = :id";
$agentParam = array(":id" => $id);
$agentRes = $pdo->selectOne($agentSql, $agentParam);

$agentId = checkIsset($agentRes['id']);
$agentName = checkIsset($agentRes['name']);
$agentBusinessName = checkIsset($agentRes['business_name']);
$agentType = checkIsset($agentRes['type']);
$agentDispId = checkIsset($agentRes['rep_id']);
$agentCodedId = checkIsset($agentRes['agent_coded_id']);

$select_div = '<div class="theme-form pr"><div class="phone-control-wrap"><div class="phone-addon">';
$select_div .= '<select class="form-control has-value agent_level_change"  id="agent_level_change_' . $id . '" data-old_show="' . $data . '" data-old_lvl_id="' . $agentCodedId . '" data-old_lvl_val="' . $agentCodedRes[$agentCodedId]['level'] . '">';
foreach ($function->get_agent_level_range($id) as $level) {
	$select_div .= '<option value="' . $level['level'] . '"  data-id="' . $level['id'] . '" ' . (($level['id'] == $agentCodedId) ? 'selected' : '') . '>' . $level['level_heading'] . '</option>';
}

$select_div .= '</select>';
$select_div .= '<label>Select</label>';
$select_div .= '</div><div class="phone-addon"><span id="cancel_level_select_' . $id . '" class="cancel_level_select"><i class="fa fa-times"></i><span></div></div>';

header("Content-type: apllication/json");
echo json_encode($select_div);
dbConnectionClose();
exit;
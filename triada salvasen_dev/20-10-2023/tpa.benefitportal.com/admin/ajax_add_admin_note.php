<?php
include_once 'layout/start.inc.php';

$response = array();
$validate = new Validation();

$note_desc = $_POST['note_desc'];
$delete = isset($_GET['delete']) ? $_GET['delete'] : '';
$admin_reminder_id = $_POST['admin_reminder_id'];

if($delete != 'Y'){
	$validate->string(array('required' => true, 'field' => 'note_desc', 'value' => $note_desc), array('required' => 'Note description is required'));

	if ($validate->isValid()) {

		$note_where = array(
			"clause" => "admin_id = :id AND is_deleted = :flag",
			"params" => array(
				":id" => $_SESSION['admin']['id'],
				":flag" => 'N',
			),
		);

		$pdo->update("admin_reminder", array('updated_at' => 'msqlfunc_NOW()','is_deleted'=>'Y'), $note_where);


		$note_info = array(
			'admin_id' => $_SESSION['admin']['id'],
			'description' => $note_desc,
			'updated_at' => "msqlfunc_NOW()",
			"created_at" => "msqlfunc_NOW()"
		);
		$note_id = $pdo->insert('admin_reminder', $note_info);
		$response['status'] = 'success';
		$response['id'] = $note_id;
	} else {
		$response['status'] = 'fail';
	}
} else {

	$admin_reminder = $pdo->selectOne("SELECT * FROM admin_reminder WHERE admin_id = :admin_id AND id = :id AND is_deleted = 'N'", array(":id" => $admin_reminder_id, ":admin_id" => $_SESSION['admin']['id']));

	if(!empty($admin_reminder)){
		$note_where = array(
			"clause" => "id = :id AND is_deleted = :flag",
			"params" => array(
				":id" => $admin_reminder['id'],
				":flag" => 'N',
			),
		);
		$pdo->update("admin_reminder", array('updated_at' => 'msqlfunc_NOW()','is_deleted'=>'Y'), $note_where);
		$response['status'] = 'success';
	} else {
		$response['status'] = 'fail';
	}

}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
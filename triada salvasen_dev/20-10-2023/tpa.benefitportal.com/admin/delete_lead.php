<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

if (isset($_POST['lead_ids'])) {
    if (count($_POST['lead_ids']) > 0) {
        foreach ($_POST['lead_ids'] as $lead_id) {
            $query = "SELECT * FROM leads WHERE md5(id)=:id and is_deleted='N'";
            $lead_row = $pdo->selectOne($query,array(':id' => $lead_id));

            if(!empty($lead_row)) {
                $up_params = array(
                    'is_deleted' => 'Y',
                    'updated_at' => 'mysqlfunc_NOW()'
                );
                $up_where = array(
                    'clause' => 'id=:id',
                    'params' => array(
                        ':id' => $lead_row['id']
                    )
                );
                $pdo->update('leads', $up_params, $up_where);

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => ' deleted Lead  ' . ($lead_row['fname'] . ' ' . $lead_row['lname']) . '(',
                    'ac_red_2' => array(
                        'href' => 'javascript:void(0);',
                        'title' => $lead_row['lead_id'],
                    ),
                    'ac_message_2' => ')'
                );
                $desc = json_encode($desc);
                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Lead Deleted', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
            }
        }
        $res['status'] = 'success';
        $res['msg'] = 'Leads deleted successfully';
    } else {
        $res['msg'] = 'Lead not found';
        $res['status'] = 'fail';
    }
} else {
    $id = $_POST['id'];
    $res = array();
    $query = "SELECT * FROM leads WHERE md5(id)=:id and is_deleted='N'";
    $lead_row = $pdo->selectOne($query, array(':id' => $id));

    if (!empty($lead_row)) {
        $update_params = array(
            'is_deleted' => 'Y',
            'updated_at' => 'mysqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($lead_row['id'])
            )
        );
        $pdo->update("leads", $update_params, $update_where);

        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                'title' => $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => ' deleted Lead ' . ($lead_row['fname'] . ' ' . $lead_row['lname']) . '(',
            'ac_red_2' => array(
                'href' => 'javascript:void(0);',
                'title' => $lead_row['lead_id'],
            ),
            'ac_message_2' => ')'
        );

        $desc = json_encode($desc);

        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Lead Deleted', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

        $res['status'] = 'success';
        $res['msg'] = 'Lead Deleted Successfully';

    } else {
        $res['status'] = 'error';
        $res['msg'] = 'Lead not found';
    }
}
header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>


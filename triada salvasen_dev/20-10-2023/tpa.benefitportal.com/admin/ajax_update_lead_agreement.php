<?php  
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$res = array();
$lead_agreement = $_POST['lead_agreement'];

$sql_t = "SELECT * FROM `app_settings` WHERE setting_key='lead_agreement'";
$res_t = $pdo->selectOne($sql_t);

if (!empty($res_t) && !empty($lead_agreement)) {

    $update_params = array(
        'setting_value' => $lead_agreement,
        'updated_at'=>'msqlfunc_NOW()'
    );
    $update_where = array(
        'clause' => "setting_key=:setting_key",
        'params' => array(
            ':setting_key'=>'lead_agreement',
        )
    );

    $pdo->update('app_settings', $update_params, $update_where);

    if($lead_agreement != $res_t['setting_value']) {
        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title' => $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => 'Lead Agreement Updated'
        );
        $desc["ac_description_link"] = array(
            'Old Agreement : ' => array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'View','data-desc'=>htmlspecialchars($res_t['setting_value']),'data-encode'=>'no'),

            ' New Agreement : ' => array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'View','data-desc'=>htmlspecialchars($lead_agreement),'data-encode'=>'no'),
        );
        $desc = json_encode($desc);

        $extra = array();
        $extra['old_lead_agreement'] =  $res_t['setting_value'];
        $extra['new_lead_agreement'] =  $lead_agreement;
        $extra = json_encode($extra);
        activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'admin', 'Lead Agreement Updated','','', $desc,'',$extra);
    }

    $res['status'] = 'success';
    $res['msg'] = 'Agreement Saved Successfully.';

} else if(!empty($lead_agreement)){

    $params = array(
        'admin_id'=> $_SESSION['admin']['id'],
        'setting_key'=> 'lead_agreement',
        'setting_value'=> $lead_agreement,
        'created_at'=>'msqlfunc_NOW()'
    );
    $pdo->insert('app_settings',$params);

    $res['status'] = 'success';
    $res['msg'] = 'Agreement Saved Successfully.';

    $desc = array();
    $desc['ac_message'] = array(
        'ac_red_1' => array(
            'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title' => $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => 'Lead Agreement Updated'
    );
    $desc["ac_description_link"] = array(
        'View Lead Agreement'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'Description','data-desc'=>htmlspecialchars($lead_agreement),'data-encode'=>'no'),
    );
    $desc = json_encode($desc);

    $extra = array();
    $extra['old_lead_agreement'] =  '';
    $extra['new_lead_agreement'] =  $lead_agreement;
    $extra = json_encode($extra);
    activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'admin', 'Lead Agreement Updated','','', $desc,'',$extra);
} else {
    $res['status'] = 'fail';
    $res['msg'] = 'Agreement not updated.'; 
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
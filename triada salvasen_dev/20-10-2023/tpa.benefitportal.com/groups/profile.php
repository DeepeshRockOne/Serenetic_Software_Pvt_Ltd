<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'My Profile';
$breadcrumbes[1]['link'] = 'javascript:void(0);';
$function = new functionsList();
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['groups']['timezone']);
$id = md5($_SESSION['groups']['id']);
$group_id = $id;

$select_user = "SELECT md5(c.id) as id,c.id as _id,c.business_name,c.rep_id,c.sponsor_id,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id,c.cell_phone,c.email,c.fname,c.lname,c.status,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.term_reason,cs.signature_file
    FROM customer c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    WHERE md5(c.id)= :id AND c.type IN ('Group') AND c.is_deleted='N'";
$where = array(':id' => makeSafe($id));
$row = $pdo->selectOne($select_user, $where);


$desc = array();
$desc['ac_message'] =array(
'ac_red_1'=>array(
    'href' => 'groups_details.php?id='.md5($_SESSION['groups']['id']),
    'title' => $_SESSION['groups']['rep_id'],
),
'ac_message_1' =>'  read Group Profile',
);
$desc = json_encode($desc);
activity_feed(3,$_SESSION['groups']['id'],'Group',$_SESSION['groups']['id'], 'Group', 'Group Read Profile',$_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache,
	'thirdparty/dropzone/css/basic.css'
);
$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
	'thirdparty/dropzone/dropzone.min.js',
	'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
	'js/password_validation.js'.$cache,
);
$template = 'profile.inc.php';
include_once 'layout/end.inc.php';
?>
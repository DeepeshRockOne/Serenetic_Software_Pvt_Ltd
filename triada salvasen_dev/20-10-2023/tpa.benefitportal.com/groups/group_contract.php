<?php
include_once __DIR__ . '/includes/connect.php';
if (isset($_GET['code'])) {
	$code = $_GET['code'];
	
	$checkLink_query = "SELECT c.business_name,c.id,c.fname,c.lname,c.email,c.cell_phone,c.rep_id,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,s.fname as s_fname,s.lname as s_lname,s.email as s_email,s.cell_phone as s_cell_phone FROM customer c
	JOIN customer_settings cs ON (cs.customer_id = c.id)
	JOIN customer s on (c.sponsor_id = s.id)
	WHERE md5(c.rep_id)=:invite_key AND (c.status='Invited' OR cs.recontract_status='Pending') AND c.type='Group'";
	$Linkwhere = array(':invite_key' => $code);
	$group_res = $pdo->selectOne($checkLink_query, $Linkwhere);
     
	if (!$group_res) {
		setNotifyError('Sorry! Group contract not found');
		redirect($GROUP_HOST);
	} elseif ($group_res['difference'] > 168) {
		setNotifyError('Group Contract link has expired');
		
		redirect($GROUP_HOST.'/index.php?link=expired&key='.$code);
	}
	
}

$exJs = array('js/password_validation.js'.$cache,
'thirdparty/masked_inputs/jquery.inputmask.bundle.js');
$exStylesheets = array('css/landing.css');
$template = 'group_contract.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>

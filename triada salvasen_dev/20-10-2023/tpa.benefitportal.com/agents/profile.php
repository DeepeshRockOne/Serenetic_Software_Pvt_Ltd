<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'My Profile';
$breadcrumbes[1]['link'] = 'javascript:void(0);';
$function = new functionsList();
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['agents']['timezone']);
$agent_id_org = $_SESSION['agents']['id'];
$agent_id = md5($agent_id_org);

if($_SESSION['agents']['is_sub_agent'] == 'Y' && $_SESSION['agents']['sub_agent_id'] > 0){
    agent_has_access(27);
}

$_SESSION["agents"]["not_show_license_expired"] = 'Y';
$_SESSION["agents"]["not_show_license_expiring"] = 'Y';
// $_SESSION["agents"]["not_show_eo_expired"] = 'Y';
$_SESSION["agents"]["not_show_eo_expiring"] = 'Y';

$agent_sql = "SELECT md5(c.id) as id,c.id as _id,c.email,c.cell_phone,c.rep_id,c.sponsor_id,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.is_branding,cs.brand_icon,c.status,cs.account_type,cs.company_name,cs.company_address,cs.company_address_2,cs.company_city,cs.company_state,cs.company_zip,cs.w9_pdf,c.address,c.address_2,c.fname,cs.tax_id,c.lname,cs.agent_coded_level,cs.agent_coded_id,c.city,c.state,c.zip,c.birth_date,c.type,cs.npn,cs.is_contract_approved,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,s.id as sid,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id,scs.agent_coded_level as s_level,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.term_reason,cs.signature_file,cs.is_2fa,cs.is_ip_restriction,cs.allowed_ip,cs.send_otp_via,cs.via_email,cs.via_sms,cs.is_address_verified,cs.allow_download_agreement,cs.agent_contract_file
    FROM `customer` c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    LEFT JOIN customer_settings scs on(scs.customer_id = s.id)
    WHERE md5(c.id)=:id";
$agent_where = array(':id' => $agent_id);
$agent_row = $pdo->selectOne($agent_sql, $agent_where);
$contract_business_image = !empty($agent_row["brand_icon"])?$agent_row["brand_icon"]:"";

$selDoc = "SELECT e_o_coverage,by_parent,by_parent,e_o_amount,e_o_expiration,e_o_document,process_commission FROM agent_document WHERE md5(agent_id)=:agent_id";
$whrDoc = array(":agent_id" => $agent_id);
$resDoc = $pdo->selectOne($selDoc, $whrDoc);

$dd_sql = "SELECT * from direct_deposit_account WHERE customer_id=:customer_id ORDER BY id DESC";
$dd_where = array(":customer_id" => $agent_id_org);
$dd_row = $pdo->selectOne($dd_sql, $dd_where);
if ($dd_row) {
    $d_bank_name = $dd_row['bank_name'];
    $d_account_number = $dd_row['account_number'];
    $d_account_type = $dd_row['account_type'];
    $d_routing_number = $dd_row['routing_number'];

    $short_d_account_number = ($d_account_number!='') ? "(*". substr($d_account_number, -4).")":'';
    $direct_deposit_detail = '<li class="text-success"><i class="fa fa-check-circle"></i></li><li>'.$d_bank_name.'</li><li class="text-capitalize">'.$d_account_type.'</li><li>ABA Routing ('.$d_routing_number.')</li><li>Account Number '.$short_d_account_number.'</li><li><b class="text-blue">Active</b></li>';
}

$IS_NOT_LOA_AGENT = true;
if($agent_row['agent_coded_level'] == 'LOA') {
    $IS_NOT_LOA_AGENT = false;
}
//pre_print($agent_row['agent_coded_level']);

$desc = array();
$desc['ac_message'] =array(
'ac_red_1'=>array(
    'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
    'title' => $_SESSION['agents']['rep_id'],
),
'ac_message_1' =>'  read agent My Profile Page',
);
$desc = json_encode($desc);
activity_feed(3,$_SESSION['agents']['id'],'Agent',$agent_row['_id'], 'Agent', 'Agent Read My Profile Page',"","",$desc);

$exStylesheets = array(
    'thirdparty/multiple-select-master/multiple-select.css'.$cache,
    'thirdparty/dropzone/css/basic.css'
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js',
    'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
    'thirdparty/dropzone/dropzone.min.js',
    'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
    'js/password_validation.js'.$cache
);
$template = 'profile.inc.php';
include_once 'layout/end.inc.php';
?>


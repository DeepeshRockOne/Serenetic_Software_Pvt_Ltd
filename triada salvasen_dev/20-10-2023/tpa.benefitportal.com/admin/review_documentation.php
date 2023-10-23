<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] ='Agents';
$breadcrumbes[1]['link'] = 'agent_listing.php';
$breadcrumbes[2]['title'] = 'Agent Profile';
$breadcrumbes[2]['link'] = 'agent_detail_v1.php?id='.$_GET['id'];

$id = checkIsset($_GET['id']);

    $select_user = "SELECT md5(c.id) as eid,c.id as id,c.email,c.cell_phone,c.rep_id,c.sponsor_id,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.is_branding,cs.brand_icon,agent_coded_level,c.status,cs.account_type,cs.company_name,cs.company_address,cs.company_address_2,cs.company_city,cs.company_state,cs.company_zip,w9_pdf,c.address,c.address_2,c.fname,cs.tax_id,c.lname,c.city,c.state,c.zip,c.birth_date,c.type,cs.npn,cs.is_contract_approved,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id
    FROM `customer` c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    WHERE md5(c.id)= :id AND c.type IN ('Agent') AND c.status='Pending Approval'";
    $where = array(':id' => makeSafe($id));
    $row = $pdo->selectOne($select_user, $where);
    $agent_id = $row['id'];

    if (empty($row)) {
        setNotifyError('Customer does not exist');
        redirect("agent_listing.php");
    }

    $selDoc = "SELECT e_o_coverage,by_parent,by_parent,e_o_amount,e_o_expiration,e_o_document,process_commission FROM agent_document WHERE md5(agent_id)=:agent_id";
    $whrDoc = array(":agent_id" => $id);
    $resDoc = $pdo->selectOne($selDoc, $whrDoc);

    $selDirect = "SELECT account_type,bank_name,routing_number,account_number FROM direct_deposit_account WHERE md5(customer_id)=:agent_id";
    $whrDirect = array(":agent_id" => $id);
    $resDirect = $pdo->selectOne($selDirect, $whrDoc);

    $description['ac_message'] =array(
    'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
    ),
    
    'ac_message_1' =>' Review Documentation On Agent '.$row['fname'].' '.$row['lname'],
    'ac_red_2'=>array(
        'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.$row['eid'],
        'title'=>"(".$row['rep_id'].")",
    ),
    );
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $row['id'], 'Agent','Review Documentation', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

$sch_params[":agent_id"] = $id;
if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => 10,
    'url' => 'review_documentation.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {
    $selADoc = "SELECT id,selling_licensed_state,license_status ,license_active_date,license_num,license_exp_date,license_not_expire,license_type,license_auth FROM agent_license WHERE md5(agent_id)=:agent_id AND is_deleted='N'";
    // $whrADoc = array(":agent_id" => $id);
    // $resADoc = $pdo->select($selADoc, $whrADoc);

    $paginate = new pagination($page, $selADoc, $options);
    if ($paginate->success == true) {
        $fetchDocs = $paginate->resultset->fetchAll();
        $totalDocs = count($fetchDocs);
    }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
$google_api = true;

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js');
$page_title = "Agent Profile";
$template = 'review_documentation.inc.php';
include_once 'layout/end.inc.php';
?>
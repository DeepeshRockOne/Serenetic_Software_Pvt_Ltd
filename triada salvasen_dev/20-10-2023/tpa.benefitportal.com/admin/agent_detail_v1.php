<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
has_access(5,true);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] ='Agents';
$breadcrumbes[1]['link'] = 'agent_listing.php';
$breadcrumbes[2]['title'] = 'Agent Details Page';
$breadcrumbes[2]['link'] = 'agent_detail_v1.php?id='.$_REQUEST['id'];
$function = new functionsList();
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
/* notification code start */
if (isset($_REQUEST["noti_id"])) {
	openAdminNotification($_REQUEST["noti_id"],$_REQUEST['id']);
}
/* notification code end */

$user_type = 'Agent';
$show_pass = isset($_POST['show_pass']) ? $_POST['show_pass'] : '';
if($show_pass!='')
{
  $id = $_REQUEST['id'];
  $res_enity =$pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where md5(id)=:id",array(":id"=>$id));
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  Read Agent '.$res_enity['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.$id,
      'title'=> $res_enity['rep_id'],
    ),
    'ac_message_2'=>') password.',
  );
  $desc=json_encode($description);
	activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $res_enity['id'], 'Agent', 'Admin Read Agent Password',$_SESSION['admin']['name'],"",$desc);
	exit;
}

$id = checkIsset($_GET['id']);
$agent_id = $id;
$row = array();

$interaction_ajax = isset($_POST['interaction_ajax']) ? $_POST['interaction_ajax'] : "";
$note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";

if (!isset($_GET['pages'])) {
    $interaction_search_keyword = isset($_REQUEST['interaction_search_keyword']) ? $_REQUEST['interaction_search_keyword'] :'';

    $interaction_incr = '';

    $interaction_search_keyword = cleanSearchKeyword($interaction_search_keyword); 
     
    if($interaction_search_keyword != '')
    {
      $interaction_incr = " AND  id.description like '%$interaction_search_keyword%' AND md5(id.user_id)=:agent_id ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$interaction_search_keyword))
    {
      $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
      $interaction_search_keyword = $tz->getDate($interaction_search_keyword);
      $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
      $interaction_incr = " AND  id.created_at like '%$interaction_search_keyword%' AND md5(id.user_id)=:agent_id ";
    }

    $interaction = $pdo->select("SELECT id.created_at,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,id.id as int_id,id.description,i.type  from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id and i.user_type='Agent' and i.is_deleted='N') LEFT JOIN admin a ON (a.id=id.admin_id and a.is_deleted='N') where md5(id.user_id) = :agent_id and id.is_deleted='N' and id.status='Active' $interaction_incr ORDER BY id.updated_at desc",array(":agent_id"=>$agent_id));

    if($interaction_search_keyword !== '' || $interaction_ajax == 'Y'){
      $interaction_desc =" <div class='activity_wrap_interaction activity_wrap'>";
              if(count($interaction) > 0){
              foreach($interaction as $note) {
                $interaction_desc.='<div class="media">';
                $interaction_desc .= '<div class="media-body fs14 br-n">';
                $interaction_desc .= '<p class="fw500 text-primary mn">'.ucwords($note['type']).'</p>';
                $interaction_desc .= '<p class="text-light-gray mn">';
                $interaction_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>";	
                $interaction_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $interaction_desc.='<div class="media-right text-nowrap">';
                $interaction_desc.='<a href="javascript:void(0)" data-href="'.$HOST.'/interactions_note.php?id='.md5($note['int_id']).'&type=view" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $interaction_desc.='<a href="javascript:void(0)" data-href="interaction_add.php?interaction_detail_id='.md5($note['int_id']).'&type=edit&agent_id='.$_GET['id'].'" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $interaction_desc.='<a href="javascript:void(0)" onclick="delete_interaction('.$note['int_id'].')"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                $interaction_desc .="</div></div>";
              }
            }else{
              $interaction_desc .='<p class="text-center mn"> No Interaction Found! </p>';
            }
      $interaction_desc .="</div>";
      echo $interaction_desc;
      exit;
    }

    $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] :'';

    $note_incr = '';

    $note_search_keyword = cleanSearchKeyword($note_search_keyword); 
     
    if($note_search_keyword != '')
    {
      $note_incr = " AND  n.description like '%$note_search_keyword%' AND md5(n.customer_id)=:agent_id ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$note_search_keyword))
    {
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_search_keyword = $tz->getDate($note_search_keyword);
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_incr = " AND  n.created_at like '%$note_search_keyword%' AND md5(n.customer_id)=:agent_id ";
    }

    $notes = $pdo->select("SELECT af.id as ac_id,n.created_at,n.description,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,n.id as note_id from note n LEFT JOIN admin a ON(a.id=n.admin_id and a.is_deleted='N') LEFT JOIN activity_feed af ON(af.entity_id=n.id and af.entity_type='note') where md5(n.agent_id)=:agent_id and md5(n.customer_id)=:agent_id and n.is_deleted='N' $note_incr order by n.id desc limit 50",array(":agent_id"=>$agent_id));

    if($note_search_keyword !== '' || $note_ajax == 'Y'){
      $note_desc =" <div class='activity_wrap_note activity_wrap'>";
              if(count($notes) > 0){
              foreach($notes as $note) {
                $note_desc.='<div class="media">';
                $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                $note_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>";	
                $note_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $note_desc.='<div class="media-right text-nowrap">';
                $note_desc.='<a href="javascript:void(0);" id="edit_note_id" data-original-title="View" onclick=edit_note_agent('.$note['note_id'].','.'"view"'.') data-value="Agent"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent('.$note['note_id'].','."''".')" data-value="Agent"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note('.$note['note_id'].','.$note['ac_id'].')" data-value="Agent"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                $note_desc .="</div></div>";
              }
            }else{
              $note_desc .='<p class="text-center mn"> No Notes Found. </p>';
            }
      $note_desc .="</div>";
      echo $note_desc;
      exit;
    }

    $select_user = "SELECT md5(c.id) as id,c.id as _id,c.email,c.cell_phone,c.rep_id,c.sponsor_id,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.is_branding,cs.brand_icon,c.status,cs.account_type,cs.company_name,cs.company_address,cs.company_address_2,cs.company_city,cs.company_state,cs.company_zip,cs.w9_pdf,c.address,c.address_2,c.fname,cs.tax_id,c.lname,cs.agent_coded_level,cs.agent_coded_id,c.city,c.state,c.zip,c.birth_date,c.type,cs.npn,cs.is_contract_approved,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,s.id as sid,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id,scs.agent_coded_level as s_level,scs.agent_coded_id as s_level_id,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.term_reason,cs.signature_file,cs.advance_on,cs.graded_on
    FROM `customer` c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    LEFT JOIN customer_settings scs on(scs.customer_id = s.id)
    WHERE md5(c.id)= :id AND c.type IN ('Agent') AND c.is_deleted='N'";
    $where = array(':id' => makeSafe($id));
    $row = $pdo->selectOne($select_user, $where);
    if (empty($row)) {
        setNotifyError('Customer does not exist');
        redirect("agent_listing.php");
    }else{
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  read Agent '.$row['fname'].' '.$row['lname'].' (',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.$id,
          'title'=> $row['rep_id'],
        ),
        'ac_message_2' =>')',
      );
      $desc=json_encode($description);
      activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $row['_id'], 'Agent', 'Admin Read Agent Details.',$_SESSION['admin']['name'],"",$desc);
    }

    $password = $row['stored_password'];

    $status_class="";

    if($row['status'] == 'Suspended'){
      $status_class = 'Abandoned';
    }else if($row['status'] == 'Terminated'){
      $status_class = 'Unqualified';
    }
    // $selDoc = "SELECT e_o_coverage,by_parent,by_parent,e_o_amount,e_o_expiration,e_o_document,process_commission FROM agent_document WHERE md5(agent_id)=:agent_id";
    // $whrDoc = array(":agent_id" => $agent_id);
    // $resDoc = $pdo->selectOne($selDoc, $whrDoc);

    // $selDirect = "SELECT account_type,bank_name,routing_number,account_number FROM direct_deposit_account WHERE md5(customer_id)=:agent_id";
    // $whrDirect = array(":agent_id" => $id);
    // $resDirect = $pdo->selectOne($selDirect, $whrDoc);
}

    // $type = $row['type'];
    $status = $row['status'];
    // $contract_business_image=!empty($row["brand_icon"])?$row["brand_icon"]:"";

// $selectRule = "SELECT cs.id,cs.advance_on FROM customer_settings cs  WHERE md5(cs.customer_id)=:agent_id order by id desc";
// $resRule = $pdo->selectOne($selectRule,array(":agent_id" => $agent_id));
// $is_on = !empty($resRule['advance_on']) ? $resRule['advance_on'] : 'N';
$advance_on = !empty($row['advance_on']) ? $row['advance_on'] : 'N';
$graded_on = !empty($row['graded_on']) ? $row['graded_on'] : 'N';

$new_license_res = $pdo->select("SELECT * FROM agent_license WHERE md5(agent_id)=:agent_id AND new_request='Y' AND is_deleted='N'", array(":agent_id" => $agent_id));
$is_new_license_request = "N";
if(count($new_license_res) > 0){
    $is_new_license_request = "Y";
} else {
    $is_new_license_request = "N";
}
$google_api = true;
$advance_history = $pdo->select("SELECT created_at,is_on from advance_comm_rule_history where md5(agent_id)=:agent_id and is_deleted='N' order by id desc",array(":agent_id"=>$agent_id));
$graded_history = $pdo->select("SELECT created_at,is_on from graded_comm_rule_history where md5(agent_id)=:agent_id and is_deleted='N' order by id desc",array(":agent_id"=>$agent_id));

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache,'thirdparty/dropzone/css/basic.css');

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js','thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,'thirdparty/dropzone/dropzone.min.js', 'thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$page_title = "Agent Profile";
$template = 'agent_detail_v1.inc.php';
include_once 'layout/end.inc.php';
?>
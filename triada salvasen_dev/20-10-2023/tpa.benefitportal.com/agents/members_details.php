<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(8);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Members";
$breadcrumbes[2]['link'] = 'member_listing.php';
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['agents']['timezone']);
$memberId = $_GET['id'];

$has_full_access = agent_has_member_access($memberId);

$show_pass = isset($_POST['show_pass']) ? $_POST['show_pass'] : '';
if($show_pass!='')
{
  $id = $_REQUEST['id'];
  $res_enity =$pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where md5(id)=:id",array(":id"=>$id));
  $description['ac_message'] =array(
    'ac_red_1'=>array(
        'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' =>'  Read Member '.$res_enity['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/members_details.php?id='.$id,
      'title'=> $res_enity['rep_id'],
    ),
    'ac_message_2'=>') password.',
  );
  $desc=json_encode($description);
  activity_feed(3,$_SESSION['agents']['id'], 'Agent' , $res_enity['id'], 'customer', 'Agent Read Member Password',"","",$desc);
  exit;
}
$communication_ajax = isset($_POST['communication_ajax']) ? $_POST['communication_ajax'] : "";
$interaction_ajax = isset($_POST['interaction_ajax']) ? $_POST['interaction_ajax'] : "";
// Interaction code Start
  $interaction_search_keyword = isset($_REQUEST['interaction_search_keyword']) ? $_REQUEST['interaction_search_keyword'] :'';

  $interaction_incr = '';

  $interaction_search_keyword = cleanSearchKeyword($interaction_search_keyword); 
   
  if($interaction_search_keyword != '')
  {
    $interaction_incr = " AND  (id.description like '%$interaction_search_keyword%' OR i.type like '%$interaction_search_keyword%' ) AND md5(id.user_id)=:memberId ";
  }

  if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$interaction_search_keyword))
  {
    $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
    $interaction_search_keyword = $tz->getDate($interaction_search_keyword);
    $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
    $interaction_incr = " AND  id.created_at like '%$interaction_search_keyword%' AND md5(id.user_id)=:memberId ";
  }

  $interaction = $pdo->select("SELECT id.created_at,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,id.id as int_id,id.description,i.type  from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id and i.user_type='member' and i.is_deleted='N') LEFT JOIN admin a ON (a.id=id.admin_id and a.is_deleted='N') where md5(id.user_id) = :memberId and id.is_deleted='N' and id.status='Active'  AND id.is_claim = 'N' $interaction_incr ORDER BY id.updated_at desc",array(":memberId"=>$memberId));

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
              $interaction_desc.='<a href="javascript:void(0)" data-href="'.$HOST.'/interactions_note.php?id='.md5($note['int_id']).'&type=view&userType=member" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
              // $interaction_desc.='<a href="javascript:void(0)" data-href="members_interaction_add.php?interaction_detail_id='.md5($note['int_id']).'&type=edit&memberId='.$_GET['id'].'" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
              // $interaction_desc.='<a href="javascript:void(0)" onclick="delete_interaction('.$note['int_id'].')"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
              $interaction_desc .="</div></div>";
            }
          }else{
            $interaction_desc .='<p class="text-center mn"> No Interaction Found! </p>';
          }
    $interaction_desc .="</div>";
    echo $interaction_desc;
    exit;
  }
// Interaction code End

//Note Code start
    $note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
    $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] : '';
    $note_search_keyword = cleanSearchKeyword($note_search_keyword);  
    $extra_params = array();
    $extra_params['note_search_keyword'] = $note_search_keyword;
    $notes_res = get_note_section_data('agent',$memberId,'customer',$extra_params);
    if ($note_search_keyword !== '' || $note_ajax == 'Y') {
        $note_desc = " <div class='activity_wrap_note activity_wrap' >";
        if (count($notes_res) > 0) {
            foreach ($notes_res as $note) {
                $note_desc .= '<div class="media">';
                $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                $note_desc .= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') . "</p>";
                $note_desc .= '<p class="mn">' . note_custom_charecter('agent','customer',$note['description'], 400, $note['added_by_name'], $note['added_by_rep_id'], $note['added_by_id'],$note['added_by_detail_page']) . '</p></div>';
                $note_desc .= '<div class="media-right text-nowrap">';
                $note_desc .= '<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note(' . $note['note_id'] . ',' . '"view"' . ') data-value="Customer"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                if($has_full_access == true) {
                $note_desc .= '<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note(' . $note['note_id'] . ','."''".')" data-value="Customer"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $note_desc .= '<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(' . $note['note_id'] . ',' . $note['ac_id'] . ')" data-value="Customer"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                }
                $note_desc .= "</div></div>";
            }
        } else {
            $note_desc .= '<p class="text-center mn"> No Notes Found. </p>';
        }
        $note_desc .= "</div>";
        echo $note_desc;
        exit;
    }
//Note Code End

// $email_list = $pdo->select("SELECT id,display_id,title,type FROM triggers where user_group IN('member','other') and is_deleted='N' and status='Active' ORDER BY title ASC");
$row = $pdo->selectOne("SELECT c.id as org_id,md5(c.id)  as id,c.fname,c.lname,c.rep_id,c.address,c.address_2,c.city,c.state,c.zip,c.birth_date,c.cell_phone,c.gender,c.email,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password, c.status,c.sponsor_id,IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as sponsor_name,s.rep_id as sponsor_rep_id,s.type as sponsor_type from customer c JOIN customer s on(c.sponsor_id = s.id) where md5(c.id)=:id and c.type='Customer' and c.is_deleted='N'",array(":id"=>$memberId));

if($communication_ajax == 'Y'){
  $comm_incr = "";
  $cust_id = $row['org_id'];
  $email = $row['email'];
  $cell_phone = $row['cell_phone'];

  getEmailSMSTable($email,$cell_phone,$has_full_access);
  exit;
}

if(empty($row['id'])){
  setNotifyError("No member found!");
  redirect("member_listing.php");
}else if(in_array($row['status'], array('Post Payment','Pending Validation'))){
  setNotifyError("No Payment Found");
  redirect("member_listing.php");
}

$status_class="";
if(in_array($row['status'],array('Hold','Post Payment'))){
  $status_class = 'Abandoned';
}else if(in_array($row['status'],array('Inactive','Inactive Failed Billing','Inactive Member Chargeback'))){
  $status_class = 'Unqualified';
}

$status_class = get_member_status_class($row['status']);

$password = $row['stored_password'];

/*-------- Sponsor Billing Type -----------*/
$sponsor_billing_method = "individual";
if($row['sponsor_type'] == "Group") {
    $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
    $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$row['sponsor_id']));
    if(!empty($resBillingType)){
        $sponsor_billing_method = $resBillingType['billing_type'];
    }
}
/*--------/Sponsor Billing Type -----------*/

/*--- Activity Feed -----*/
if(!empty($row)) {
  $desc = array();
    $desc['ac_message'] =array(
        'ac_red_1'=>array(
            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' =>'  Read Member '.$row['fname'].' '.$row['lname'].'(',
        'ac_red_2'=>array(
            'href'=> 'members_details.php?id='.$row['id'],
            'title'=> $row['rep_id'],
        ),
        'ac_message_2'=>')',
    );
    $desc=json_encode($desc);
    activity_feed(3,$_SESSION['agents']['id'],'Agent', $row['org_id'], 'customer', 'Agent Read Member Details.',"","",$desc);
}
/*---/Activity Feed -----*/

$exStylesheets = array(
  'thirdparty/multiple-select-master/multiple-select.css',
  'thirdparty/bootstrap-tables/css/bootstrap-table.min.css'
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/masked_inputs/jquery.maskedinput.min.js',
    'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
    'thirdparty/price_format/jquery.price_format.2.0.js',
    'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
    'thirdparty/bootstrap-tables/js/bootstrap-table.min.js'
);
$template = 'members_details.inc.php';
include_once 'layout/end.inc.php';
?>

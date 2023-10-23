<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
has_access(6);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[1]['link'] = 'groups_listing.php';
$breadcrumbes[2]['title'] = 'Groups';
$function = new functionsList();

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
/* notification code start */
if (isset($_REQUEST["noti_id"])) {
	openAdminNotification($_REQUEST["noti_id"],$_REQUEST['id']);
}


$user_type = 'Group';
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
    'ac_message_1' =>'  Read Group '.$res_enity['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/groups_details.php?id='.$id,
      'title'=> $res_enity['rep_id'],
    ),
    'ac_message_2'=>') password.',
  );
  $desc=json_encode($description);
	activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $res_enity['id'], 'Group', 'Admin Read Group Password',$_SESSION['admin']['name'],"",$desc);
	exit;
}
$interaction_ajax = isset($_POST['interaction_ajax']) ? $_POST['interaction_ajax'] : "";
$note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
$id = checkIsset($_GET['id']);
$group_id = $id;
$row = array();

// Interaction code Start
    $interaction_search_keyword = isset($_REQUEST['interaction_search_keyword']) ? $_REQUEST['interaction_search_keyword'] :'';

    $interaction_incr = '';

    $interaction_search_keyword = cleanSearchKeyword($interaction_search_keyword); 
      
    if($interaction_search_keyword != '')
    {
      $interaction_incr = " AND  (id.description like '%$interaction_search_keyword%' OR i.type like '%$interaction_search_keyword%' ) AND md5(id.user_id)=:group_id ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$interaction_search_keyword))
    {
      $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
      $interaction_search_keyword = $tz->getDate($interaction_search_keyword);
      $interaction_search_keyword = date('Y-m-d',strtotime($interaction_search_keyword));
      $interaction_incr = " AND  id.created_at like '%$interaction_search_keyword%' AND md5(id.user_id)=:group_id ";
    }

    $interaction = $pdo->select("SELECT id.created_at,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,id.id as int_id,id.description,i.type  from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id and i.user_type='Group' and i.is_deleted='N') LEFT JOIN admin a ON (a.id=id.admin_id and a.is_deleted='N') where md5(id.user_id) = :group_id and id.is_deleted='N' and id.status='Active' $interaction_incr ORDER BY id.updated_at desc",array(":group_id"=>$group_id));

    if($interaction_search_keyword !== '' || $interaction_ajax == 'Y'){
      $interaction_desc =" <div class='activity_wrap_interaction activity_wrap' >";
              if(count($interaction) > 0){
              foreach($interaction as $note) {
                $interaction_desc.='<div class="media">';
                $interaction_desc .= '<div class="media-body fs14 br-n">';
                $interaction_desc .= '<p class="fw500 text-primary mn">'.ucwords($note['type']).'</p>';
                $interaction_desc .= '<p class="text-light-gray mn">';
                $interaction_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>";	
                $interaction_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $interaction_desc.='<div class="media-right text-nowrap">';
                $interaction_desc.='<a href="javascript:void(0)" data-href="'.$HOST.'/interactions_note.php?id='.md5($note['int_id']).'&type=view&userType=Group" class="interactions_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $interaction_desc.='<a href="javascript:void(0)" data-href="group_interaction_add.php?interaction_detail_id='.md5($note['int_id']).'&type=edit&group_id='.$_GET['id'].'" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
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
// Interaction code End

//Note Code start
    $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] :'';

    $note_incr = '';

    $note_search_keyword = cleanSearchKeyword($note_search_keyword); 

    if($note_search_keyword != '')
    {
      $note_incr = " AND  n.description like '%$note_search_keyword%' AND md5(n.customer_id)=:group_id ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$note_search_keyword))
    {
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_search_keyword = $tz->getDate($note_search_keyword);
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_incr = " AND  n.created_at like '%$note_search_keyword%' AND md5(n.customer_id)=:group_id ";
    }
    
    //We are displaying notes which are added by admin only
    $notes = $pdo->select("SELECT af.id as ac_id,n.created_at,n.description,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,n.id as note_id from note n JOIN admin a ON(a.id=n.admin_id and a.is_deleted='N') LEFT JOIN activity_feed af ON(af.entity_id=n.id and af.entity_type='note') where md5(n.customer_id)=:group_id and n.is_deleted='N' $note_incr order by n.id desc limit 50",array(":group_id"=>$group_id));

    if($note_search_keyword !== '' || $note_ajax == 'Y'){
      $note_desc =" <div class='activity_wrap_note activity_wrap' >";
              if(count($notes) > 0){
              foreach($notes as $note) {
                $note_desc.='<div class="media">';
                $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                $note_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>";	
                $note_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $note_desc.='<div class="media-right text-nowrap">';
                $note_desc.='<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note_agent('.$note['note_id'].','.'"view"'.') data-value="Group"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent('.$note['note_id'].')" data-value="Group"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note('.$note['note_id'].','.$note['ac_id'].')" data-value="Group"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                $note_desc .="</div></div>";
              }
            }else{
              $note_desc .='<p class="text-center mn"> No Notes Found. </p>';
            }
      $note_desc .="</div>";
      echo $note_desc;
      exit;
    }
//Note Code End

$email_list = $pdo->select("SELECT id,display_id,title,type FROM triggers where user_group IN('group','other') and is_deleted='N' and status='Active' ORDER BY title ASC");




$select_user = "SELECT md5(c.id) as id,c.id as _id,c.business_name,c.rep_id,c.sponsor_id,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id,c.cell_phone,c.email,c.fname,c.lname,c.status,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.term_reason,cs.signature_file
    FROM customer c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    WHERE md5(c.id)= :id AND c.type IN ('Group') AND c.is_deleted='N'";
$where = array(':id' => makeSafe($id));
$row = $pdo->selectOne($select_user, $where);
if (empty($row)) {
    setNotifyError('Group does not exist');
    redirect("groups_listing.php");
}else{
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  read Group '.$row['fname'].' '.$row['lname'].' (',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/groups_details.php?id='.$id,
      'title'=> $row['rep_id'],
    ),
    'ac_message_2' =>')',
  );
  $desc=json_encode($description);
  activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $row['_id'], 'Group', 'Admin Read Group Details.',$_SESSION['admin']['name'],"",$desc);
}

$password = $row['stored_password'];

$status_class="";

if($row['status'] == 'Suspended'){
	$status_class = 'Abandoned';
}else if($row['status'] == 'Terminated'){
	$status_class = 'Unqualified';
}




    
$status = $row['status'];
   
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache,'thirdparty/dropzone/css/basic.css');
$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/formatCurrency/jquery.formatCurrency-1.4.0.js','thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,'thirdparty/dropzone/dropzone.min.js', 'thirdparty/jquery-match-height/js/jquery.matchHeight.js');


$template = 'groups_details.inc.php';
include_once 'layout/end.inc.php';
?>

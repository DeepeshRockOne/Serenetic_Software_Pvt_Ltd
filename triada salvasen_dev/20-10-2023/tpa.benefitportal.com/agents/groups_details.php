<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';

// setNotifyError("you dont have access to this page");        
// echo "<script>window.parent.location = 'groups_listing.php'; window.location = 'groups_listing.php'; </script>";
// exit;

agent_has_access(9);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';
$function = new functionsList();

$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['agents']['timezone']);

$user_type = 'Group';
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
    'ac_message_1' =>'  Read Group '.$res_enity['name'].'(',
    'ac_red_2'=>array(
      'href'=> 'groups_details.php?id='.$id,
      'title'=> $res_enity['rep_id'],
    ),
    'ac_message_2'=>') password.',
  );
  $desc=json_encode($description);
	activity_feed(3,$_SESSION['agents']['id'], 'Agent',$res_enity['id'],'Group','Agent Read Group Password',"","",$desc);
	exit;
}

$note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
$id = checkIsset($_GET['id']);
$group_id = $id;
$row = array();

//Note Code start
    $note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
    $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] : '';
    $note_search_keyword = cleanSearchKeyword($note_search_keyword);  
    $extra_params = array();
    $extra_params['note_search_keyword'] = $note_search_keyword;
    $notes_res = get_note_section_data('agent',$group_id,'group',$extra_params);
    if ($note_search_keyword !== '' || $note_ajax == 'Y') {
        $note_desc = " <div class='activity_wrap_note activity_wrap' >";
        if (count($notes_res) > 0) {
            foreach ($notes_res as $note) {
                $note_desc .= '<div class="media">';
                $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                $note_desc .= $tz->getDate($note['created_at'], 'D., M. d, Y @ h:i A') . "</p>";
                $note_desc .= '<p class="mn">' . note_custom_charecter('agent','customer',$note['description'], 400, $note['added_by_name'], $note['added_by_rep_id'], $note['added_by_id'],$note['added_by_detail_page']) . '</p></div>';
                $note_desc .= '<div class="media-right text-nowrap">';
                $note_desc .= '<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note(' . $note['note_id'] . ',' . '"view"' . ') data-value="Group"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $note_desc .= '<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note(' . $note['note_id'] . ')" data-value="Group"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $note_desc .= '<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(' . $note['note_id'] . ',' . $note['ac_id'] . ')" data-value="Group"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
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
      'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
      'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' =>'  read Group '.$row['fname'].' '.$row['lname'].' (',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/groups_details.php?id='.$id,
      'title'=> $row['rep_id'],
    ),
    'ac_message_2' =>')',
  );
  $desc=json_encode($description);
  activity_feed(3,$_SESSION['agents']['id'], 'Agent' , $row['_id'], 'Group', 'Agent Read Group Details.',"","",$desc);
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

<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(8,true);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[2]['title'] = 'Members';
$breadcrumbes[2]['link'] = 'member_listing.php';
$breadcrumbes[3]['title'] = 'Member Details Page';

$memberId = $_GET['id'];
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
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
    'ac_message_1' =>'  Read Member '.$res_enity['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/members_details.php?id='.$id,
      'title'=> $res_enity['rep_id'],
    ),
    'ac_message_2'=>') password.',
  );
  $desc=json_encode($description);
  activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $res_enity['id'], 'customer', 'Admin Read Member Password',$_SESSION['admin']['name'],"",$desc);
  // activity_feed(3,$res_enity['id'], 'customer' , $res_enity['id'], 'customer', 'Admin Read Member Password',$res_enity['name'],"",$desc);
  exit;
}
$interaction_ajax = isset($_POST['interaction_ajax']) ? $_POST['interaction_ajax'] : "";
$claim_ajax = isset($_POST['claim_ajax']) ? $_POST['claim_ajax'] : "";
$note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
$communication_ajax = isset($_POST['communication_ajax']) ? $_POST['communication_ajax'] : "";
$eticket_ajax = isset($_POST['eticket_ajax']) ? $_POST['eticket_ajax'] : "";
$trigger_id = isset($_POST['trigger_id']) ? $_POST['trigger_id'] : "";

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

    $interaction = $rpdo->select("SELECT id.created_at,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,id.id as int_id,id.description,i.type  from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id and i.user_type='member' and i.is_deleted='N') LEFT JOIN admin a ON (a.id=id.admin_id and a.is_deleted='N') where md5(id.user_id) = :memberId and id.is_deleted='N' and id.status='Active'  AND id.is_claim = 'N' $interaction_incr ORDER BY id.updated_at desc",array(":memberId"=>$memberId));

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
                $interaction_desc.='<a href="javascript:void(0)" data-href="members_interaction_add.php?interaction_detail_id='.md5($note['int_id']).'&type=edit&memberId='.$_GET['id'].'" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
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
// Claim code start
 $claim_search_keyword = isset($_REQUEST['claim_search_keyword']) ? $_REQUEST['claim_search_keyword'] :'';

    $claim_incr = '';

    $claim_search_keyword = cleanSearchKeyword($claim_search_keyword); 
      
    if($claim_search_keyword != '')
    {
      $claim_incr = " AND  (id.description like '%$claim_search_keyword%' OR i.type like '%$claim_search_keyword%' ) AND md5(id.user_id)=:memberId ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$claim_search_keyword))
    {
      $claim_search_keyword = date('Y-m-d',strtotime($claim_search_keyword));
      $claim_search_keyword = $tz->getDate($claim_search_keyword);
      $claim_search_keyword = date('Y-m-d',strtotime($claim_search_keyword));
      $claim_incr = " AND  id.created_at like '%$claim_search_keyword%' AND md5(id.user_id)=:memberId ";
    }

    $claims = $pdo->select("SELECT id.created_at,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id,id.id as int_id,id.description,i.type  from interaction_detail id LEFT JOIN interaction i ON(i.id=id.interaction_id and i.user_type='member' and i.is_deleted='N') LEFT JOIN admin a ON (a.id=id.admin_id and a.is_deleted='N') where md5(id.user_id) = :memberId and id.is_deleted='N' and id.status='Active' AND id.is_claim = 'Y' $claim_incr ORDER BY id.updated_at desc",array(":memberId"=>$memberId));

    if($claim_search_keyword !== '' || $claim_ajax == 'Y'){
      $claim_desc =" <div class='activity_wrap_claim activity_wrap'>";
              if(count($claims) > 0){
              foreach($claims as $note) {
                $claim_desc.='<div class="media">';
                $claim_desc .= '<div class="media-body fs14 br-n">';
                $claim_desc .= '<p class="fw500 text-primary mn">'.ucwords($note['type']).'</p>';
                $claim_desc .= '<p class="text-light-gray mn">';
                $claim_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>"; 
                $claim_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $claim_desc.='<div class="media-right text-nowrap">';
                $claim_desc.='<a href="javascript:void(0)" data-href="'.$HOST.'/interactions_note.php?id='.md5($note['int_id']).'&type=view&userType=member" class="claim_note"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $claim_desc.='<a href="javascript:void(0)" data-href="members_interaction_add.php?interaction_detail_id='.md5($note['int_id']).'&type=edit&memberId='.$_GET['id'].'&is_claim=Y" class="account_note_popup_new"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $claim_desc.='<a href="javascript:void(0)" onclick="delete_claim('.$note['int_id'].')"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
                $claim_desc .="</div></div>";
              }
            }else{
              $claim_desc .='<p class="text-center mn"> No claim Found! </p>';
            }
      $claim_desc .="</div>";
      echo $claim_desc;
      exit;
    }
// Claim code end
//Note Code start
    $note_search_keyword = isset($_REQUEST['note_search_keyword']) ? $_REQUEST['note_search_keyword'] :'';

    $note_incr = '';

    $note_search_keyword = cleanSearchKeyword($note_search_keyword); 
     
    if($note_search_keyword != '')
    {
      $note_incr = " AND  n.description like '%$note_search_keyword%' AND md5(n.customer_id)=:memberId ";
    }

    if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$note_search_keyword))
    {
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_search_keyword = $tz->getDate($note_search_keyword);
      $note_search_keyword = date('Y-m-d',strtotime($note_search_keyword));
      $note_incr = " AND  n.created_at like '%$note_search_keyword%' AND md5(n.customer_id)=:memberId ";
    }

    //We need to display notes which are added by admin only
    $notes = $pdo->select("SELECT af.id as ac_id,n.created_at,n.description,CONCAT(a.fname,' ',a.lname) as admin_name,a.id as admin_id,a.display_id as display_id,n.id as note_id,'admin_profile.php' as url 
                  FROM note n 
                  JOIN admin a ON(a.id=n.admin_id and a.is_deleted='N')
                  LEFT JOIN activity_feed af ON(af.entity_id=n.id and af.entity_type='note') 
                  WHERE md5(n.customer_id)=:memberId and n.is_deleted='N' $note_incr order by n.id desc limit 50",array(":memberId"=>$memberId));

    if($note_search_keyword !== '' || $note_ajax == 'Y'){
      $note_desc =" <div class='activity_wrap_note activity_wrap'>";
              if(count($notes) > 0){
              foreach($notes as $note) {
                $note_desc.='<div class="media">';
                $note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
                $note_desc.= $tz->getDate($note['created_at'],'D., M. d, Y @ h:i A')."</p>";  
                $note_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['admin_name'],$note['display_id'],$note['admin_id']).'</p></div>';
                $note_desc.='<div class="media-right text-nowrap">';
                $note_desc.='<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note_agent('.$note['note_id'].','.'"view"'.') data-value="Customer"><i class="fa fa-eye fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_agent('.$note['note_id'].')" data-value="Customer"><i class="fa fa-edit fa-lg"></i></a> &nbsp;';
                $note_desc.='<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note('.$note['note_id'].','.$note['ac_id'].')" data-value="Customer"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
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
$row = $pdo->selectOne("SELECT c.id as org_id,md5(c.id)  as id,c.fname,c.lname,c.rep_id,c.address,c.address_2,c.city,c.state,c.zip,c.birth_date,c.cell_phone,c.gender,c.email,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password, c.status,c.sponsor_id,IF(s.type='Group',s.business_name,CONCAT(s.fname,' ',s.lname)) as sponsor_name,s.rep_id as sponsor_rep_id,s.type as sponsor_type from customer c JOIN customer s on(c.sponsor_id = s.id) where md5(c.id)=:id and c.type='Customer' and c.is_deleted='N'",array(":id"=>$memberId));

if($communication_ajax == 'Y'){
  $comm_incr = "";
  $cust_id = $row['org_id'];
  $email = $row['email'];
  $cell_phone = $row['cell_phone'];
  getEmailSMSTable($email,$cell_phone);
  exit;  
}

$email_list = $pdo->select("SELECT id,display_id,title,type FROM triggers where user_group IN('member','other') and is_deleted='N' and status='Active' ORDER BY title ASC");

if($eticket_ajax == 'Y'){
  
  $eticket_sql = "SELECT s.*,if(s.user_type='Admin',concat(a.display_id,' - ',a.fname,' ',a.lname),concat(c.rep_id,' - ',c.fname,' ',c.lname)) as name,GROUP_CONCAT(CONCAT(aa.fname,' ',aa.lname,'_',aa.id)) as admin_names,sc.title as groupName
        FROM s_ticket s 
        LEFT JOIN admin a ON(a.id=s.user_id and s.user_type='Admin')
        JOIN customer c ON(c.id=s.user_id)
        LEFT JOIN s_ticket_group sc ON(sc.id=s.group_id and sc.is_deleted='N')
        LEFT JOIN s_ticket_assign_admin sa ON(sa.s_ticket_group_id = sc.id and sa.is_deleted='N')
        LEFT JOIN admin aa ON(aa.id=sa.admin_id and aa.is_deleted='N')
        where s.user_id = :user_id GROUP BY s.id order by s.updated_at desc";
  $eticket_res = $pdo->select($eticket_sql,array(':user_id' => $row['org_id']));

  $html = "";
  ob_start();
  ?>
  <table class="<?=$table_class?>" data-mobile-responsive="true" id="eticket_list_table">
    <thead>
      <tr>
        <th>ID/Last Updated</th>
        <th>Requester</th>
        <th>Assignee</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>  
    <tbody>
  <?php
  if($eticket_res){
    foreach ($eticket_res as $key => $rows) { ?>
      <tr>
        <td><a href="javascript:void(0);" class="fw500 text-action open_conversation_link" data-target="#open_conversation_<?=md5($rows['id'])?>"><?=$rows['tracking_id']?></a><br><?=getCustomDate($rows['updated_at'])?></td>
        <td>
        <?=$rows['name']?>
        </td>
        <td>
          <div class="theme-form pr">
            <select id="changeAssignee_<?=$rows['id']?>" name="changeAssignee" data-category_id="<?=$rows['group_id']?>" data-tracking_id="<?=$rows['id']?>" data-oldval="<?=$rows['assigned_admin_id']?>" class="form-control <?=!empty($rows['admin_names']) ? 'has-value' : '' ?>" data-live-search="true">
                <?php if($rows['assigned_admin_id'] == 0) { ?>
                  <option value="" hidden <?=  $rows['assigned_admin_id'] =='0' ? 'selected="selected" ' : ''?>></option>
                <?php } ?>
              <optgroup label="GROUP - <?=$rows['groupName']?>">
                  <?php if(!empty($rows['admin_names'])){
                    $adminNames = explode(',',$rows['admin_names']);
                    foreach($adminNames as $name){
                      $textName = explode('_',$name);
                      ?>
                    <option value="<?=$textName[1]?>" <?=$textName[1] == $rows['assigned_admin_id'] ? 'selected="selected" '  : ''?>><?=$textName[0]?></option>
                  <?php }} ?>
              </optgroup>
            </select>
            <label>Select</label>
          </div>
        </td>
        <td>
          <div class="theme-form  pr">
            <select class="form-control <?=!empty($rows['status']) ? 'has-value' : '' ?>" data-category_id="<?=$rows['group_id']?>" data-tracking_id="<?=$rows['id']?>" data-oldval="<?=$rows['status']?>" id="changeStatus_<?=$rows['id']?>" onchange="changeStatus($(this))">
              <option hidden></option>
              <option value="New" <?=$rows['status'] == 'New' ? 'selected="selected" ' : '' ?>>New</option>
              <option value="Working" <?=$rows['status'] == 'Working' ? 'selected="selected" ' : '' ?>>Working</option>
              <option value="Open" <?=$rows['status'] == 'Open' ? 'selected="selected" ' : '' ?>>Open</option>
              <option value="Reassigned" <?=$rows['status'] == 'Reassigned' ? 'selected="selected" ' : '' ?>>Reassigned</option>
              <option value="Abandoned (Admin)" <?=$rows['status'] == 'Abandoned (Admin)' ? 'selected="selected" ' : '' ?>>Abandoned (Admin)</option>
              <option value="Abandoned (User)" <?=$rows['status'] == 'Abandoned (User)' ? 'selected="selected" ' : '' ?>>Abandoned (User)</option>
              <option value="Completed" <?=$rows['status'] == 'Completed' ? 'selected="selected" ' : '' ?>>Completed</option>
            </select>
            <label>Select</label>
          </div>
        </td>
        <td class="icons">
          <a href="javascript:void(0);" class="open_conversation_preview" data-href="open_conversation_preview.php?s_ticket_id=<?=md5($rows['id'])?>&view=1" data-toggle="tooltip" ata-placement="top" title="" aria-hidden="true" data-original-title="View Eticket" data-id ="<?=$log['tracking_id']?>"><i class="fa fa-external-link"></i></a>
        </td>
      </tr>
    <?php }
  }else{ ?>
    <tr >
      <td colspan="5">
        No record(s) found
      </td>
    </tr>
  <?php } ?>
  </tbody>
  </table>
<?php 
  $html = ob_get_clean();
  echo $html;
  exit();
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
}else if(in_array($row['status'],array('Inactive','Inactive Failed Billing'))){
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

/*--------Check is Gap+ Product------------*/
$gapResult = '';
$isGapPrd = false; 
if($row['sponsor_type'] == "Group"){
  $gapSql = "SELECT p.is_gap_plus_product,c.is_compliant 
                FROM customer c
                JOIN website_subscriptions w ON (w.customer_id=c.id) 
                JOIN prd_main p ON (p.id=w.product_id AND p.is_gap_plus_product='Y')
                WHERE w.customer_id = :customer_id";
  $gapResult = $pdo->selectOne($gapSql,array(":customer_id"=>$row['org_id']));
  if(!empty($gapResult) && $gapResult['is_gap_plus_product'] == 'Y'){
    $isGapPrd = true;
  }

}
/*--------/Check is Gap+ Product------------*/

/*--- Activity Feed -----*/
    $desc = array();
    $desc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  Read Member '.$row['fname'].' '.$row['lname'].'(',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/members_details.php?id='.$row['id'],
            'title'=> $row['rep_id'],
        ),
        'ac_message_2'=>')',
    );
    $desc=json_encode($desc);
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $row['org_id'], 'customer', 'Admin Read Member Details.',$_SESSION['admin']['name'],"",$desc);
/*---/Activity Feed -----*/

/* Pending aae if products added*/
$enroll_sql = "SELECT lq.*,o.sub_total,o.grand_total,o.status as order_status,o.future_payment,o.post_date
                    FROM leads l 
                    JOIN lead_quote_details lq ON(l.id = lq.lead_id AND lq.is_assisted_enrollment = 'Y') 
                    JOIN orders o ON(o.id = lq.order_ids) 
                    WHERE md5(l.customer_id)=:customer_id AND 
                    lq.status IN ('Pending') AND 
                    (lq.order_ids != '' AND lq.order_ids IS NOT NULL) ORDER BY lq.expire_time DESC LIMIT 2";
$enroll_where = array(":customer_id"=>$row['id']);
$enroll_res = $pdo->select($enroll_sql,$enroll_where);
$active_aae_id = 0;
if(!empty($enroll_res)) {
    foreach ($enroll_res as $key => $enroll_row) {
      if(strtotime(date('Y-m-d H:i:s')) < strtotime($enroll_row['expire_time'])) {
          $active_aae_id = $enroll_row['id'];
      }
    }
}

// sponsor special text start
$sp_sql = "SELECT is_special_text_display,special_text_display FROM customer_settings WHERE customer_id=:customer_id";
$sp_res = $pdo->selectOne($sp_sql,[':customer_id' => $row['sponsor_id']]);
// sponsor special text end

// $google_api = true;
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

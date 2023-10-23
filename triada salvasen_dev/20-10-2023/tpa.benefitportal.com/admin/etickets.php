<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[2]['title'] = "E-Ticket";
$breadcrumbes[2]['link'] = 'etickets.php';

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$change_assignee = isset($_GET['change_assignee']) ? $_GET['change_assignee'] : '';
$change_status = isset($_GET['change_status']) ? $_GET['change_status'] : '';
$sch_params = array();
$incr='';
$SortBy = "s.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$REAL_IP_ADDRESS = get_real_ipaddress();
if($change_assignee){
    $response = array();
    $tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $chkId = $pdo->selectOne("SELECT id,tracking_id from s_ticket where id=:id and group_id=:category_id",array(":id"=>$tracking_id,':category_id'=>$category_id));
    if(!empty($chkId['id'])){
        $admin_id = isset($_GET['id']) ? $_GET['id'] : '';
        $upd_param = array(
            "assigned_admin_id" =>$admin_id,
            "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address']
        );
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(":id"=>$tracking_id)
        );
        $s_ticket_update = $pdo->update('s_ticket',$upd_param,$upd_where,true);

        send_e_ticket_mail_to_assigne($chkId['id']);
        
        if(!empty($s_ticket_update['assigned_admin_id'])){

            $old = $pdo->selectOne("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee from admin where id=:id",array(":id"=>$s_ticket_update['assigned_admin_id']));
            $new = $pdo->selectOne("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee from admin where id=:id",array(":id"=>$upd_param['assigned_admin_id']));

            updateEticketActivity($chkId,$new,$old);
        }
        $response['status'] = 'success';
        $response['msg'] = 'E-ticket Group Assignee updated successfully.';
    }else{
        $response['status'] = 'fail';
        $response['msg'] = 'Something went wrong.';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if($change_status){
    $response = array();
    $tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $value =  isset($_GET['value']) ? $_GET['value'] : '';
    $chkId = $pdo->selectOne("SELECT id,tracking_id from s_ticket where id=:id and group_id=:category_id",array(":id"=>$tracking_id,':category_id'=>$category_id));
    if(!empty($chkId['id']) && !empty($value)){
        $admin_id = isset($_GET['id']) ? $_GET['id'] : '';
        $upd_param = array(
            "status" =>$value,
            "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address']
        );
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(":id"=>$tracking_id)
        );
        $sticekt_update = $pdo->update('s_ticket',$upd_param,$upd_where,true);
        if(!empty($sticekt_update['status'])){
            updateEticketActivity($chkId,$upd_param,$sticekt_update);
        }
        $response['status'] = 'success';
        $response['msg'] = 'E-ticket Status updated successfully.';
    }else{
        $response['status'] = 'fail';
        $response['msg'] = 'Something went wrong.';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
$summernote = true;
$groups = $pdo->select("SELECT * FROM `s_ticket_group` WHERE is_deleted='N' ORDER BY title ASC");
if($is_ajaxed){
    $incr = '';
    $tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : array();
    $groups_ids = isset($_GET['groups']) ? $_GET['groups'] : '';
    $assignee_id = isset($_GET['assignee_id']) ? $_GET['assignee_id'] : '';
    $join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
    $fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
    $todate = isset($_GET["todate"])?$_GET["todate"]:"";
    $added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
    $user_id = isset($_GET["user_id"])?$_GET["user_id"]:"";
    $status = isset($_GET["status"])?$_GET["status"]:"";
    $company = isset($_GET['company']) ? $_GET['company'] : array();
    if($join_range != ""){
        if($join_range == "Range" && $fromdate!='' && $todate!=''){
          $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
          $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
          $incr.=" AND DATE(s.created_at) >= :fromdate AND DATE(s.created_at) <= :todate";
        }else if($join_range == "Exactly" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(s.created_at) = :added_date";
        }else if($join_range == "Before" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(s.created_at) < :added_date";
        }else if($join_range == "After" && $added_date!=''){
          $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
          $incr.=" AND DATE(s.created_at) > :added_date";
        }
      }

    $update_join_range = isset($_GET['update_join_range'])?$_GET['update_join_range']:"";
    $update_fromdate = isset($_GET["update_fromdate"])?$_GET["update_fromdate"]:"";
    $update_todate = isset($_GET["update_todate"])?$_GET["update_todate"]:"";
    $update_added_date = isset($_GET["update_added_date"])?$_GET["update_added_date"]:"";

    if($update_join_range != ""){
        if($update_join_range == "Range" && $update_fromdate!='' && $update_todate!=''){
          $sch_params[':ufromdate'] = date("Y-m-d",strtotime($update_fromdate));
          $sch_params[':utodate'] = date("Y-m-d",strtotime($update_todate));
          $incr.=" AND DATE(s.updated_at) >= :ufromdate AND DATE(s.updated_at) <= :utodate";
        }else if($update_join_range == "Exactly" && $update_added_date!=''){
          $sch_params[':uadded_date'] = date("Y-m-d",strtotime($update_added_date));
          $incr.=" AND DATE(s.updated_at) = :uadded_date";
        }else if($update_join_range == "Before" && $update_added_date!=''){
          $sch_params[':uadded_date'] = date("Y-m-d",strtotime($update_added_date));
          $incr.=" AND DATE(s.updated_at) < :uadded_date";
        }else if($update_join_range == "After" && $update_added_date!=''){
          $sch_params[':uadded_date'] = date("Y-m-d",strtotime($update_added_date));
          $incr.=" AND DATE(s.updated_at) > :uadded_date";
        }
      }

    
    if (!empty($tracking_id)) {
        $tracking_id = explode(',',$tracking_id);
        $tracking_id = array_map('trim',$tracking_id);
        $incr .= " AND s.id IN ('".implode("','",$tracking_id)."')";
    }

    $tbl_incr = '';
    if (!empty($assignee_id)) {
        $assignee_id = explode(',',$assignee_id);
        $assignee_id = array_map('trim',$assignee_id);
        $incr .= " AND s.assigned_admin_id IN(".implode(',',$assignee_id).")";
    }

    if(!empty($groups_ids)){
        $groups_ids = explode(',',$groups_ids);
        $groups_ids = array_map('trim', $groups_ids);
        $incr .= " AND s.group_id IN(".implode(',',$groups_ids).")";
    }
    $hincr = '';
    if(!empty($user_id)){
        $user_id = explode(',',$user_id);
        $user_id = array_map('trim', $user_id);
        $incr.=" AND s.user_id IN(".implode(',',$user_id).")";
    }

    if(!empty($company)){
        $company = explode(',',$company);
        $imploded_company = "'" . implode("','", str_replace("'", '',$company)) . "'";
        $incr .= " AND cs.company IN ($imploded_company)";
    }

    if(!empty($status)){
        $status = "'" . implode("','", makeSafe($status)) . "'";
        $incr .= " AND s.status IN($status)";
    }

    $per_page=10;
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
      $has_querystring = true;
      $per_page = $_GET['pages'];
    }
    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
  
    $options = array(
        'results_per_page' => $per_page,
        'url' => 'etickets.php?is_ajaxed=1&' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params
    );
  
    $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);

    try {
        $sel_sql = "SELECT s.*,if(s.user_type='Admin',concat(a.display_id,' - ',a.fname,' ',a.lname),concat(c.rep_id,' - ',c.fname,' ',c.lname)) as name,GROUP_CONCAT(CONCAT(aa.fname,' ',aa.lname,'_',aa.id)) as admin_names,sc.title as groupName,cs.company
        FROM s_ticket s 
        $tbl_incr
        LEFT JOIN admin a ON(a.id=s.user_id and s.user_type='Admin')
        LEFT JOIN customer c ON(c.id=s.user_id and s.user_type!='Admin')
        LEFT JOIN customer_settings cs ON (c.sponsor_id = cs.customer_id)
        LEFT JOIN s_ticket_group sc ON(sc.id=s.group_id and sc.is_deleted='N')
        LEFT JOIN s_ticket_assign_admin sa ON(sa.s_ticket_group_id = sc.id and sa.is_deleted='N')
        LEFT JOIN admin aa ON(aa.id=sa.admin_id and aa.is_deleted='N')
        where s.user_id!=0 $incr GROUP BY s.id $hincr order by $SortBy $SortDirection 
        ";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    
    include_once 'tmpl/etickets.inc.php';
    exit;
}

function updateEticketActivity($s_ticket_arr,$newParam = array(),$oldParams = array(),$user_url = false){
    global $ADMIN_HOST;

    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated E-Ticket (',
        'ac_red_2'=>array(
            'href'=> $user_url ? $ADMIN_HOST.'/open_conversation_preview.php?s_ticket_id='.md5($s_ticket_arr['id']).'&view=1' : '',
            'title'=> $s_ticket_arr['tracking_id'],
        ),
        'ac_message_2' =>')<br>',
    );
    foreach($oldParams as $key => $value){
        $description['key_value']['desc_arr'][$key] = ' Updated from '.$value.' to '.$newParam[$key];    
    }
    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket',$_SESSION['admin']['name'],"",json_encode($description));
}
$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,"thirdparty/ajax_form/jquery.form.js", "thirdparty/ckeditor/ckeditor.js");

$template = 'etickets.inc.php';
include_once 'layout/end.inc.php';
?>



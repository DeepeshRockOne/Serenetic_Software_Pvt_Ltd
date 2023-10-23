<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '';
$categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : '';
if($is_ajaxed && !empty($categoryId)){
    $response['status']  = 'fail';
    $assignAdmins = $pdo->select("SELECT a.fname,a.lname,a.display_id,a.id from s_ticket_assign_admin sa LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') where sa.is_deleted='N' and sa.s_ticket_group_id=:category_id order by fname ASC",array(":category_id"=>$categoryId));
    $data_html = '<select class="form-control" name="assigne_admins" id="assigne_admins" data-live-search="true">';
    $data_html .= '<option hidden></option>';
    if(!empty($assignAdmins)){
        foreach($assignAdmins as $admin){
        $data_html .= '<option value="'.$admin['id'].'">'.$admin['display_id'].' - '.$admin['fname'].' '.$admin['lname'].'</option>';
        }
        $response['status'] = 'success';
    }

    $data_html.='</select><label>Assignee</label><p class="error error_assigne_admins"></p>';
    $response['data_html'] = $data_html;

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$userType = isset($_POST['type']) ? $_POST['type'] : '';
if($is_ajaxed && !empty($userType)){
    $response['status']  = 'fail';
    $userArr = array();
    if($userType == 'Admin'){
       $userArr = $pdo->select("SELECT fname,lname,display_id as rep_id,id from admin where is_deleted='N' order by fname ASC");
    }else{
        $userArr = $pdo->select("SELECT fname,lname,rep_id,id from customer where is_deleted='N' and type=:type order by fname ASC",array(":type"=>$userType));
    }

    $data_html = '<select class="form-control" name="userId" id="userId" data-live-search="true">';
    $data_html .= '<option hidden></option>';
    if(!empty($userArr)){
        foreach($userArr as $user){
        $data_html .= '<option value="'.$user['id'].'">'.$user['rep_id'].' - '.$user['fname'].' '.$user['lname'].'</option>';
        }
        $response['status'] = 'success';
    }

    $data_html.='</select><label>User name</label><p class="error error_userId"></p>';
    $response['data_html'] = $data_html;

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$category = array();
$category = $pdo->select("SELECT * FROM s_ticket_group where is_deleted='N' order by title ASC");

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,"thirdparty/ajax_form/jquery.form.js",);

$template = "add_etickets.inc.php";
include_once 'layout/iframe.layout.php';
?>
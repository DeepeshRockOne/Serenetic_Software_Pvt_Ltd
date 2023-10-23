<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

// $is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '';
// $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : '';
// if($is_ajaxed && !empty($categoryId)){
//     $response['status']  = 'fail';
//     $assignAdmins = $pdo->select("SELECT a.fname,a.lname,a.display_id,a.id from s_ticket_assign_admin sa LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') where sa.is_deleted='N' and sa.s_ticket_group_id=:category_id",array(":category_id"=>$categoryId));
//     $data_html = '<select class="form-control" name="assigne_admins" id="assigne_admins" data-live-search="true">';
//     $data_html .= '<option hidden></option>';
//     if(!empty($assignAdmins)){
//         foreach($assignAdmins as $admin){
//         $data_html .= '<option value="'.$admin['id'].'">'.$admin['fname'].' '.$admin['lname'].'('.$admin['display_id'].')'.'</option>';
//         }
//         $response['status'] = 'success';
//     }

//     $data_html.='</select><label>Assignee</label><p class="error error_assigne_admins"></p>';
//     $response['data_html'] = $data_html;

//     header('Content-Type: application/json');
//     echo json_encode($response);
//     exit;
// }

$agent_id = $_SESSION['agents']['id'];
$rows = $pdo->selectOne("SELECT fname,lname,rep_id,c.id,email,cell_phone,company_name FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) where c.is_deleted='N' and c.id=:id",array(":id"=>$agent_id));
$category = $pdo->select("SELECT * from s_ticket_group where is_deleted='N' order by title ASC");

$exJs = array("thirdparty/ajax_form/jquery.form.js");
$template = "add_support_ticket.inc.php";
include_once 'layout/iframe.layout.php';
?>
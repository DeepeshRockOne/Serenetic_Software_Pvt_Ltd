<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$is_ajaxed = checkIsset($_POST['is_ajaxed']);
$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_ajaxed){
    $response = array();
    $tracking_id = checkIsset($_POST['tracking_id']);
    $chkId = $pdo->selectOne("SELECT id ,tracking_id from s_ticket where id=:id",array(":id"=>$tracking_id));

    if(!empty($chkId['id'])){
        $admin_id = checkIsset($_POST['assigned_id']);
        $category_id = checkIsset($_POST['category_id']);
        $upd_param = array(
            "assigned_admin_id" =>$admin_id,
            "group_id" =>$category_id,
            "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address']
        );
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(":id"=>$tracking_id)
        );
       $s_ticket_update =  $pdo->update('s_ticket',$upd_param,$upd_where,true);

        if(!empty($s_ticket_update['assigned_admin_id'])){
            $description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Updated E-Ticket (',
                'ac_red_2'=>array(
                    'title'=> $chkId['tracking_id'],
                ),
                'ac_message_2' =>')<br>',
            );
            $oldAdmin = $pdo->selectOne("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee from admin where id=:id",array(":id"=>$s_ticket_update['assigned_admin_id']));
            $newAdmin = $pdo->selectOne("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee from admin where id=:id",array(":id"=>$upd_param['assigned_admin_id']));

            $oldCat = $pdo->selectOne("SELECT title as  'Group' from s_ticket_group where id=:id ",array(":id"=>$s_ticket_update['group_id']));
            $newCat = $pdo->selectOne("SELECT title as  'Group' from s_ticket_group where id=:id",array(":id"=>$category_id));

            $description['key_value']['desc_arr']['Assignee'] = ' Updated from '.$oldAdmin['Assignee'].' to '.$newAdmin['Assignee'];    
            $description['key_value']['desc_arr']['Group'] = ' Updated from '.$oldCat['Group'].' to '.$newCat['Group'];

            activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket',$_SESSION['admin']['name'],"",json_encode($description));
        }

        $response['status'] = 'success';
        $response['msg'] = 'E-ticket Group updated successfully.';
    }else{
        $response['status'] = 'fail';
        $response['msg'] = 'Something went wrong.';
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$category_id = checkIsset($_GET['category_id']);
$tracking_id = checkIsset($_GET['id']);
$assigned_admins = $pdo->select("SELECT a.fname,a.id,a.lname,a.display_id from s_ticket_assign_admin s LEFT JOIN admin a ON(a.id=s.admin_id and a.is_deleted='N') where s_ticket_group_id=:id and s.is_deleted='N'",array(":id"=>$category_id));
$name = getname('s_ticket_group',$category_id,'title');
$tr_display_id = getname('s_ticket',$tracking_id,'tracking_id');

$layout = 'iframe.layout.php';
$template = 'reassigned_etickets.inc.php';
include_once 'layout/end.inc.php';
?>

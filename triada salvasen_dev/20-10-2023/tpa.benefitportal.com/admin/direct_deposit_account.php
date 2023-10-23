<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$show_pass =checkIsset($_POST['show_pass']);
if($show_pass == 'show_pass'){
    if($_POST['f_digit_code'] == '5401' && !empty($_POST['number'])){
        $agent_id = $_POST['agent_id'];
        $number = $_POST['number'];
        $effective_date = $_POST['effective_date'];
        $account_detail = $pdo->selectOne("SELECT effective_date,termination_date,account_number FROM `direct_deposit_account` where md5(account_number)=:id and effective_date = :edate",array(":id"=>$number,":edate"=>$effective_date));
        header("Content-type:application/json");
        echo json_encode(array("number"=>$account_detail['account_number']));
        $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$agent_id));
        $description = array();
        $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'View Account Number for Direct Deposite Account In Agent ' .$agent_name['name'].' (',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
            'title'=> $agent_name['rep_id'],
        ),
        'ac_message_2' =>')<br>',
        );
        $termination_date = getCustomDate($account_detail['termination_date']) != '-' ? getCustomDate($account_detail['termination_date']) : 'Present' ;
        $description['description_account'] = "Account date range : ".getCustomDate($account_detail['effective_date']).' - '.$termination_date;
        $desc = json_encode($description);
	    activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_name['id'], 'Agent', 'Direct Deposite Account',$_SESSION['admin']['name'],"",$desc);
        exit;
    }else{
        header("Content-type:application/json");
        echo json_encode(array("invalid"=>"Invalid passcode"));
        exit;
    }
}
$id = $_GET['id'];
$agent_id = $_GET['agent_id'];
$account_detail = array();

$account_detail = $pdo->selectOne("SELECT md5(id) as id,account_type,effective_date,termination_date,status,bank_name,routing_number,account_number FROM `direct_deposit_account` where md5(id)=:id",array(":id"=>$id));

$account_number = !empty($account_detail['account_number']) ? $account_detail['account_number'] : '' ; 

$template = 'direct_deposit_account.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

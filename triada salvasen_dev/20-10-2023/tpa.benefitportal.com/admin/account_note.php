<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$customer_id = $_REQUEST['id'];
$reply_id = checkIsset($_GET['reply_id']);
$note_id = checkIsset($_GET['note_id']);
$type = $_GET['type'];
$from = checkIsset($_GET['from']) !='' ? $_GET['from'] : $_SERVER['HTTP_REFERER'];
$show = checkIsset($_GET['show']);
$tz = new UserTimeZone('m/d/Y @ g:i A T',$_SESSION['admin']['timezone']);

if($reply_id != ''){
	$reply_note = $pdo->selectOne('SELECT * FROM note WHERE id = :id', array(':id' => $reply_id));
	$reply_note_subject = $reply_note['title'];
}

if($note_id){
	$note_res = $pdo->selectOne("SELECT * , DATE_FORMAT(created_at, '%a., %b. %d, %Y @  %r') as date,created_at as cdate FROM note WHERE id = :id", array(':id' => $note_id));
	if(!empty($note_res['admin_id'])) {
		$admin_name = $pdo->selectOne("SELECT concat(fname,' ',lname) as name,display_id from admin where id=:id",array(":id"=>$note_res['admin_id']));	
	}
	if(!empty($note_res['agent_id'])) {
		$admin_name = $pdo->selectOne("SELECT concat(fname,' ',lname) as name,rep_id as display_id from customer where id=:id",array(":id"=>$note_res['agent_id']));	
	}
}

if($type=='Admin' && $customer_id!=''){
	$res_name = $pdo->selectOne("SELECT concat(fname,' ',lname) as name,DATE_FORMAT(current_time(), '%a., %b. %d, %Y @  %r') as date, display_id,now() as cdate from admin where md5(id) = :id",array("id"=>$customer_id));
}

if($type!='Admin' && $customer_id!=''){
	$res_name = $pdo->selectOne("SELECT concat(fname,' ',lname) as name,DATE_FORMAT(current_time(), '%a., %b. %d, %Y @  %r') as date, rep_id as display_id,now() as cdate from customer where md5(id) = :id AND type=:type",array("id"=>$customer_id,":type"=>$type));
}
if($type=='Lead' && $customer_id!=''){
	$res_name = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,DATE_FORMAT(current_time(), '%a., %b. %d, %Y @  %r') as date, lead_id as display_id,now() as cdate from leads where md5(id) = :id",array("id"=>$customer_id));
}
if($type=='Group' && $customer_id!=''){
	$res_name = $pdo->selectOne("SELECT business_name as name,DATE_FORMAT(current_time(), '%a., %b. %d, %Y @  %r') as date, rep_id as display_id,now() as cdate from customer where md5(id) = :id AND type=:type",array("id"=>$customer_id,":type"=>$type));
}
if($type=='Participants' && $customer_id!=''){
	$res_name = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,DATE_FORMAT(current_time(), '%a., %b. %d, %Y @  %r') as date, participants_id as display_id,now() as cdate from participants where md5(id) = :id",array("id"=>$customer_id));
}
$exJs = array(
    'thirdparty/ajax_form/jquery.form.js'
);

$page_title = "Account Note";
$template = "account_note.inc.php";
include_once 'layout/iframe.layout.php';
?>
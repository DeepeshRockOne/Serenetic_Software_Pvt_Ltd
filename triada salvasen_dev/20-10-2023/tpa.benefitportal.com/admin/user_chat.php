<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Dashboard';
$breadcrumbes[1]['link'] = 'support_dashboard.php';
$breadcrumbes[2]['title'] = 'Chat Queue';
$breadcrumbes[2]['link'] = 'chat_queue.php';
$breadcrumbes[3]['title'] = 'Ongoing Chat';


/* echo "<pre>";
  print_R($_GET);
  echo "</pre>";
  exit; */

// operators
$opt_sql = "SELECT * FROM $WEBIM_DB.chatoperator WHERE istatus=0";
$opt_rows = $pdo->select($opt_sql);

$operators = array();
foreach ($opt_rows as $opt_row) {
  $operators[$opt_row['operatorid']] = ucwords($opt_row['vclocalename']);
}

if ($_GET['openchat'] == 'yes') {

  $thread = $_GET['thread'];

  $sel = "SELECT * FROM $WEBIM_DB.chatthread WHERE threadid =:threadid";
  $whr = array(':threadid' => $thread); 
  $res = $pdo->selectOne($sel,$whr);
  
  if($res['agentId'] == 0){

      $update_params = array(
          'istate' => 1,
          'agentId' => $_SESSION['operator']['operatorid'],
          'agentName' => $_SESSION['operator']['name']
      );
      $update_where = array(
          'clause' => 'threadid = :threadid',
          'params' => array(
              ':threadid' => makeSafe($thread)
          )
      );
      $pdo->update("$WEBIM_DB.chatthread", $update_params, $update_where);
      //echo '<script type="text/javascript">window.close();</script>';
      //exit;
  }

  if ($_GET['assign_operator']) {

    $thread_id = $_GET['threadid'];
    
    $ins_params = array(
        'threadid' => makeSafe($thread_id),
        'dtmcreated' => 'msqlfunc_NOW()',
    );
    
    $ins_params['ikind'] = 4;
    $ins_params['tmessage'] = "We have assigned you to another support services agent.";
    
    $pdo->insert("$WEBIM_DB.chatmessage", $ins_params);
    
    $update_params = array(
        'istate' => 1,
        'agentId' => $_GET['assign_operator'],
        'agentName' => $operators[$_GET['assign_operator']]
    );
    $update_where = array(
        'clause' => 'threadid = :threadid',
        'params' => array(
            ':threadid' => makeSafe($thread_id)
        )
    );
    $pdo->update("$WEBIM_DB.chatthread", $update_params, $update_where);
    echo '<script type="text/javascript">window.close();</script>';
    exit;
  }

  $churl = $_GET['chaturl'];

  $chats = explode("=", substr($_GET['chaturl'], strpos($_GET['chaturl'], '?') + 1));
  $threadid = $chats[1];

  $chthr = "SELECT agentId, customer_id, istate, user_type, user_sub_type, userName
            FROM $WEBIM_DB.chatthread
            WHERE threadid = :threadid";
  $chthwh = array(":threadid" => makeSafe($chats[1]));

  $chrow = $pdo->selectOne($chthr, $chthwh);
  if (!$chrow) {
    redirect('chat_queue.php');
  }

  $agentid = $chrow['agentId'];
  $chcustid = $chrow['customer_id'];
  $user_type = ($chrow['user_type'])?$chrow['user_type']:'Guest';
  $subuser_type = $user_sub_type = $chrow['user_sub_type'];
  $webim_chat_status = $chrow['istate'];
}


$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js', 'thirdparty/simscroll/jquery.slimscroll.min.js');
$template = 'user_chat.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
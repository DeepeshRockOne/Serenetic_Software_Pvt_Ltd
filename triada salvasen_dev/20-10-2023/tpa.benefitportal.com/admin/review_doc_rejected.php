<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();

$contract_status = $_GET['contract_status'];
$agent_id = $_GET['agent_id'];

if (isset($_POST['save'])) {
    $reject_msg = $_POST['reject_msg'];
    $agent_id = $_POST['agent_id'];
    $contract_status = $_POST['contract_status'];
    $validate->string(array('required' => true, 'field' => 'reject_msg', 'value' => $reject_msg), array('required' => 'Reject Message is required'));
  
    if ($validate->isValid()) {
      if($contract_status == 'Pending Resubmission'){
        $param = array(
          'status' => "Pending Documentation",
          'updated_at' => 'msqlfunc_NOW()',
        );
        $upd_where = array(
          'clause' => 'md5(id) = :id',
          'params' => array(
          ':id' => $agent_id,
          ),
        );
        $cs_upd_params = array(
          'is_contract_approved' => $contract_status,
          'reject_text' => $reject_msg,
        );
        $cs_upd_where = array(
          'clause' => 'md5(customer_id) = :id',
          'params' => array(
          ':id' => $agent_id,
          ),
        );
        $pdo->update("customer",$param,$upd_where);
        $pdo->update("customer_settings",$cs_upd_params,$cs_upd_where);
        // Agent - Enrollment Rejected
        $trigger_id = 18;
        $description = array();
          // $description['trigger_id'] = $trigger_id;
          // $description['admin_id'] = $_SESSION['admin']['id'];
        $agent_name = $pdo->selectOne("SELECT id,fname,lname,CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$agent_id));
        $description['ac_message'] = array(
          'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>'  Rejected Documentation on Agent '.$agent_name['name'].' (',
          'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
              'title'=> $agent_name['rep_id'],
          ),
          'ac_message_2' =>')<br>',
          );
        $description['reason'] = $reject_msg;
        $description = json_encode($description);
        activity_feed(3,  $_SESSION['admin']['id'], 'Admin', $agent_name['id'], 'Agent', 'Agent Documentation Rejected', $agent_name['fname'], $agent_name['lname'],$description);

        // trigger SmartTag replace
        $resAgent = array();
        $resEnrollAgent = array();
        if(!empty($agent_id)){
          $selAgent = "SELECT id,rep_id,fname,lname,CONCAT(fname,' ',lname) as name,email,cell_phone,sponsor_id FROM customer WHERE md5(id)=:id AND type='Agent'";
          $paramsAgent = array(":id" => $agent_id);
          $resAgent = $pdo->selectOne($selAgent,$paramsAgent);
        }

        if(!empty($resAgent['sponsor_id'])){
          $selEnrollAgent = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,email,cell_phone FROM customer WHERE id=:id AND type='Agent'";
          $paramsEnrollAgent = array(":id" => $resAgent['sponsor_id']);
          $resEnrollAgent = $pdo->selectOne($selEnrollAgent,$paramsEnrollAgent);
        }

        $REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $fname = !empty($resAgent['fname']) ? $resAgent['fname'] : '';
        $lname = !empty($resAgent['lname']) ? $resAgent['lname'] : '';
        $email = !empty($resAgent['email']) ? $resAgent['email'] : '';
        $params = array();
        $params['fname'] = !empty($resAgent['fname']) ? $resAgent['fname'] : '';
        $params['lname'] = !empty($resAgent['lname']) ? $resAgent['lname'] : '';
        $params['Email'] = !empty($resAgent['email']) ? $resAgent['email'] : '';
        $params['Phone'] = !empty($resAgent['cell_phone']) ? $resAgent['cell_phone'] : '';
    
        $params['Agent'] = !empty($resAgent['name']) ? $resAgent['name'] : '';
        $params['ParentAgent'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
        
        $params['USER_IDENTITY'] = array('rep_id' => $agent_id, 'cust_type' => 'Agent', 'location' => $REQ_URL);

        $params['AgentName'] = $fname.' '.$lname;

        $params['EnrollingAgentDisplayName'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
        $params['EnrollingAgentDisplayEmail'] = !empty($resEnrollAgent['email']) ? $resEnrollAgent['email'] : '';
        $params['EnrollingAgentDisplayPhone'] = !empty($resEnrollAgent['cell_phone']) ? $resEnrollAgent['cell_phone'] : '';

        $params['link'] = $AGENT_HOST;

        $smart_tags = get_user_smart_tags($agent_id,'agent');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }
        trigger_mail($trigger_id, $params, $email);
      }
      setNotifySuccess("Message sent successfully!");
      redirect('agent_detail_v1.php?id='.$agent_id,true);
    }
  }

$errors = $validate->getErrors();

// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
$exJs = array(
'thirdparty/ckeditor/ckeditor.js'
);

$template = 'review_doc_rejected.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
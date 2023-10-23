<?php
include_once 'layout/start.inc.php';
$validate = new Validation();

$quickLabel = checkIsset($_POST['quickreplyLabel']);
$quickDesc = checkIsset($_POST['quickreplyDesc']);
$quickId = checkIsset($_POST['quickId']);
$insertQuick = $updateQuick = false;
$activityDesc = array();
$categoryId = checkIsset($_POST['categoryId']);
$is_deleted = checkIsset($_POST['is_deleted']);

if(!empty($categoryId) && $is_deleted=='Y'){
    $category = $pdo->selectOne("SELECT id,title from s_ticket_quick_reply where md5(id)=:id and is_deleted='N'",array(":id"=>$categoryId));
    if(!empty($category['id'])){
        $pdo->update("s_ticket_quick_reply",array("is_deleted"=>'Y'),array("clause"=>'id=:id','params'=>array(":id"=>$category['id'])));

        $description['ac_message'] = array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' Deleted E-Ticket Quick reply '.$category['title'].'(',
            'ac_red_2'=>array(
            //   'href'=> $ADMIN_HOST.'/manage_etickets.php',
              'title'=> $category['title'],
            ),
            'ac_message_2' =>')<br>',
          );
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket Group',$_SESSION['admin']['name'],"",json_encode($description));

        $response['status'] ='success';
    }else{
        $response['status'] ='fail';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


$validate->string(array('required' => true, 'field' => 'quickreplyLabel', 'value' => $quickLabel), array('required' => 'Quick Reply Label is required'));

$validate->string(array('required' => true, 'field' => 'quickreplyDesc', 'value' => $quickDesc), array('required' => 'Quick Reply Description is required'));

if(!empty($quickLabel)){
    $qincr = '';
    $sch_param = array(":title"=>$quickLabel);
    if(!empty($quickId)){
        $qincr = ' AND md5(id)!=:id ';
        $sch_param[":id"] = $quickId;
    }
    $resGroup = $pdo->selectOne("SELECT title,id from s_ticket_quick_reply where is_deleted='N' AND title = :title $qincr " ,$sch_param);
    if(!empty($resGroup['id'])){
        $validate->setError("quickreplyLabel","This Quick Reply Label is already associated with another Quick Reply.");
    }
}

if($validate->isValid()){
    $existQuick = array();
    if($quickId!='')
        $existQuick = $pdo->selectOne("SELECT id from s_ticket_quick_reply where md5(id)=:id and is_deleted='N'",array(":id"=>$quickId));
    if(!empty($existQuick['id'])){
        $updParam = array(
            'title' => $quickLabel,
            'description' => htmlspecialchars($quickDesc)
        );
        $updWhere = array(
            "clause" => " id=:id ",
            "params" => array(":id"=>$existQuick['id'])
        );
        $s_ticket_quick_reply = $pdo->update("s_ticket_quick_reply",$updParam,$updWhere,true);

        if(!empty($s_ticket_quick_reply)){
            $updateQuick = true;
            $activityDesc = $s_ticket_quick_reply;
        }
        $response['msg'] = 'Quick Reply Updated Successfully.';
    }else{
        $insParam = array(
            'title' => $quickLabel,
            'description' => htmlspecialchars($quickDesc)
        );
        $pdo->insert("s_ticket_quick_reply",$insParam);
        $insertQuick = true;
        $response['msg'] = 'New Quick Reply Added Successfully.';
    }


    if($insertQuick){
        $label = ' Created new E-ticket Quick Reply ';
    }else if($updateQuick){
        $label = ' Updated E-ticket Quick Reply ';
    }
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$label.$quickLabel.'(',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/manage_etickets.php',
          'title'=> $quickLabel,
        ),
        'ac_message_2' =>')<br>',
      );
      if(!empty($activityDesc)){
        if(!empty($activityDesc['title'])){
            $description['descTitle'] = 'Label Updated from '.$activityDesc['title'].' To '.$quickLabel;
        }
        if(!empty($activityDesc['description'])){
            $description["ac_description_link"] = array(
                'From'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>checkIsset($activityDesc['description']),'data-encode'=>'no'),
                'To'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>htmlspecialchars($quickDesc),'data-encode'=>'no'),
            );
        }
      }
      $desc = json_encode($description);
      if($label !='' && !empty($label)){
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket Quick Reply',$_SESSION['admin']['name'],"",$desc);
      }

    $response['status'] = 'success';
}else{
    $errors = $validate->getErrors();
	$response['status'] = 'fail';
	$response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
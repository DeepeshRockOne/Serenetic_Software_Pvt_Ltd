<?php include_once dirname(__FILE__) . '/layout/start.inc.php';

  $response = array();
  
  $group_classes_id = !empty($_POST['id']) ? $_POST['id'] : '';
  $sqlMember="SELECT id FROM customer_settings where md5(class_id) = :group_classes_id";
  $resMember=$pdo->selectOne($sqlMember,array(":group_classes_id"=>$group_classes_id));

  

  $sqlLeads="SELECT id FROM leads where md5(group_classes_id) = :group_classes_id";
  $resLeads=$pdo->selectOne($sqlLeads,array(":group_classes_id"=>$group_classes_id));

  $sqlClass="SELECT id,class_name FROM group_classes where md5(id) = :group_classes_id";
  $resClass=$pdo->selectOne($sqlClass,array(":group_classes_id"=>$group_classes_id));


  if (empty($resLeads) && empty($resMember)) {
    $params=array(
      'is_deleted'=>'Y',
    );
    $whr = array(
      'clause' => 'md5(id) = :id',
      'params' => array(
          ':id' => $group_classes_id,
      ),
    );
    $pdo->update("group_classes",$params,$whr);

    $paramspaydates=array(
      'is_deleted'=>'Y',
    );
    $wherepaydates = array(
      'clause' => 'md5(class_id) = :id',
      'params' => array(
          ':id' => $group_classes_id,
      ),
    );
    $pdo->update("group_classes_paydates",$paramspaydates,$wherepaydates);
    
    setNotifySuccess("Class Removed Successfully");

    $description['ac_message'] = array(
        'ac_red_1' => array(
            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
          'title'=>$_SESSION['groups']['rep_id'],
        ),
        'ac_message_1' => ' deleted Class ',
        'ac_red_2' => array(
            'title' => $resClass['class_name'],
        ),
    );
    $desc = json_encode($description);

    activity_feed(3, $_SESSION['groups']['id'], 'Group', $resClass['id'], 'group_classes', 'Group Deleted Class', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
    
    $response['status'] = "success";
    
  } else {
    $response['status'] = "fail";
    $response['msg'] = "Class Assigned To Member/Enrollee";
  }

  header('Content-type: application/json');
	echo json_encode($response);
  dbConnectionClose(); 
  exit;
?>
<?php
include_once 'layout/start.inc.php';
$validate = new Validation();

$adminIds = checkIsset($_POST['assign_admins'],'arr');
$groupName = checkIsset($_POST['groupName']);
$groupId = checkIsset($_POST['groupId']);
$categoryId = checkIsset($_REQUEST['categoryId']);
$is_deleted = checkIsset($_REQUEST['is_deleted']);
$is_confirm_delete = checkIsset($_REQUEST['is_confirm_delete']);
$insertGroup = $updateGroup = false;
$activityDesc = array();

$is_ajaxed_get_assignee = isset($_POST['is_ajaxed_get_assignee']) ? $_POST['is_ajaxed_get_assignee'] : '';
$categoryId = isset($_REQUEST['categoryId']) ? $_REQUEST['categoryId'] : '';
$ticket_id = isset($_REQUEST['ticket_id']) ? $_REQUEST['ticket_id'] : '';
$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
if($is_ajaxed_get_assignee && !empty($categoryId) && !empty($user_id)){
    $response['status']  = 'fail';
    $assignAdmins = $pdo->select("SELECT a.fname,a.lname,a.display_id,a.id from s_ticket_assign_admin sa LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') where sa.is_deleted='N' and sa.s_ticket_group_id=:category_id",array(":category_id"=>$categoryId));
    $data_html = '<select class="form-control" name="assigne_admins['.$user_id.']" id="assigne_admins_'.$user_id.'" data-live-search="true">';
    $data_html .= '<option hidden></option>';
    if(!empty($assignAdmins)){
        foreach($assignAdmins as $admin){
        $data_html .= '<option value="'.$admin['id'].'">'.$admin['display_id'].' - '.$admin['fname'].' '.$admin['lname'].'</option>';
        }
        $response['status'] = 'success';
    }
    $data_html.='</select><label>Assignee</label><p class="error" id="error_assigne_admins_'.$user_id.'"></p>';
    $response['data_html'] = $data_html;
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if(!empty($categoryId) && $is_deleted=='Y'){
    if(!empty($is_confirm_delete)){
        $response = array();
        $s_ticket_ids  = checkIsset($_POST['s_ticket_ids'],'arr') ;
        $group_name = checkIsset($_POST['group_name'],'arr') ;
        $assigne_admins = checkIsset($_POST['assigne_admins'],'arr') ;
        if(!empty($s_ticket_ids)){
            foreach($s_ticket_ids as $id => $value){
                if(empty($group_name[$id])){
                    $validate->setError("group_name_".$id,"Please select any Group.");
                }
                if(!empty($group_name[$id]) && empty($assigne_admins[$id])){
                    $validate->setError("assigne_admins_".$id,"Please select any Admin.");
                }
            }
        }
        if($validate->isValid()){
            $response['status'] = 'fail';

            $activity_desc = array();
            $oldcategory = $pdo->selectOne("SELECT id,title from s_ticket_group where md5(id)=:id and is_deleted='N'",array(":id"=>$categoryId));
            if(!empty($s_ticket_ids)){

                $oldAdmins = $pdo->select("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee,s.id
                FROM s_ticket s 
                LEFT JOIN admin a ON(a.id=s.assigned_admin_id)
                WHERE user_id!=0 and group_id=:category_id",array(":category_id"=>$oldcategory['id']));
                $oldAdminsArr = array();
                if(!empty($oldAdmins)){
                    foreach($oldAdmins as $ad){
                        $oldAdminsArr[$ad['id']] = $ad;
                    }
                }
                foreach($s_ticket_ids as $id => $value){
                    $upd_param_s_ticket = array(
                        'group_id' => $group_name[$id],
                        'assigned_admin_id' => $assigne_admins[$id],
                    );
                    $update_where = array(
                        'clause' => ' user_id = :id and group_id = :category_id ',
                        'params' => array(":id"=>$id,":category_id"=>$oldcategory['id'])
                    );
                    $pdo->update('s_ticket',$upd_param_s_ticket,$update_where);
                    if(!empty($oldAdmins)){

                        $userDetail = $pdo->select("SELECT s.tracking_id,if(s.user_type='Admin',concat(a.display_id,' - ',a.fname,' ',a.lname),concat(c.rep_id,' - ',c.fname,' ',c.lname)) as name,if(s.user_type='Admin','Admin',c.type) as type,s.id as id
                        FROM s_ticket s 
                        LEFT JOIN admin a ON(a.id=s.user_id and s.user_type='Admin')
                        LEFT JOIN customer c ON(c.id=s.user_id and s.user_type!='Admin')
                        WHERE s.user_id=:id and user_id!=0 and group_id=:category_id",array(":id"=>$id,":category_id"=>$group_name[$id]));
                        if(!empty($userDetail) && !empty($oldAdminsArr)){
                            $newcategory = $pdo->selectOne("SELECT id,title from s_ticket_group where id=:id and is_deleted='N'",array(":id"=>$upd_param_s_ticket['group_id']));
    
                            $newAdmin = $pdo->selectOne("SELECT CONCAT(fname,' ',lname,'(',display_id,')') as Assignee from admin where id=:id",array(":id"=>$upd_param_s_ticket['assigned_admin_id']));

                            foreach($userDetail as $user){
                                if($user['id'] == checkIsset($oldAdminsArr[$user['id']]['id'])){
                                    $activity_desc[] = 'E-Ticket : '.$user['tracking_id'].', User : '.$user['name'].'<br>';
                                    $activity_desc[] = '&nbsp;&nbsp;Category : From '.$oldcategory['title'].' to '.$newcategory['title'].'<br>';
                                    $activity_desc[] = '&nbsp;&nbsp;Assignee : From '.$oldAdminsArr[$user['id']]['Assignee'].' to '.$newAdmin['Assignee'].'<br>';
                                    unset($oldAdminsArr[$user['id']]);
                                }
                               
                            }
                        }
                        
                    }
                }
            }

            if(!empty($oldcategory['id'])){
                $pdo->update("s_ticket_group",array("is_deleted"=>'Y'),array("clause"=>'id=:id','params'=>array(":id"=>$oldcategory['id'])));
                $pdo->update("s_ticket_assign_admin",array("is_deleted"=>'Y'),array("clause"=>'s_ticket_group_id=:id','params'=>array(":id"=>$oldcategory['id'])));
                $description['ac_message'] = array(
                    'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Deleted E-Ticket group'.$oldcategory['title'].'(',
                    'ac_red_2'=>array(
                    //   'href'=> $ADMIN_HOST.'/manage_etickets.php',
                    'title'=> $oldcategory['title'],
                    ),
                    'ac_message_2' =>')<br>',
                );
                if(!empty($activity_desc)){
                    $description['upddescription'] = 'Updated following E-tickets : <br>';
                    foreach($activity_desc as $key => $value){
                        $description['description'.$key] = $value;
                    }
                }
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket Group',$_SESSION['admin']['name'],"",json_encode($description));
                $response['status'] ='success';
            }

        }else{
            $errors = $validate->getErrors();
            $response['status'] = 'errors';
            $response['errors'] = $errors;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;

    }else{        
        $user_res = $pdo->select("SELECT s.*,if(s.user_type='Admin',concat(a.display_id,' - ',a.fname,' ',a.lname),concat(c.rep_id,' - ',c.fname,' ',c.lname)) as name,if(s.user_type='Admin','Admin',c.type) as type,count(s.id) as total_tickets
        FROM s_ticket s 
        LEFT JOIN admin a ON(a.id=s.user_id and s.user_type='Admin')
        LEFT JOIN customer c ON(c.id=s.user_id and s.user_type!='Admin')
        WHERE md5(s.group_id)=:category_id and user_id!=0 GROUP BY s.user_id ",array(":category_id"=>$categoryId));
        $groups = $pdo->select("SELECT * FROM `s_ticket_group` WHERE is_deleted='N' and md5(id)!=:category_id ORDER BY title ASC",array(":category_id"=>$categoryId));

        $oldcategory = $pdo->selectOne("SELECT id,title from s_ticket_group where md5(id)=:id and is_deleted='N'",array(":id"=>$categoryId));

        $template = 'delete_eticket_groups.inc.php';
        $layout = 'iframe.layout.php';
        include_once 'layout/end.inc.php';
        exit;

    }
    exit;
}
if(empty($adminIds)){
    $validate->setError("assign_admins","Please select any Admin.");
}

$validate->string(array('required' => true, 'field' => 'groupName', 'value' => $groupName), array('required' => 'Group Name is required'));

if(!empty($groupName)){
    $gincr = '';
    $sch_param = array(":title"=>$groupName);
    if(!empty($groupId)){
        $gincr = ' AND md5(id)!=:id ';
        $sch_param[":id"] = $groupId;
    }
    $resGroup = $pdo->selectOne("SELECT title,id from s_ticket_group where is_deleted='N' AND title = :title $gincr " ,$sch_param);
    if(!empty($resGroup['id'])){
        $validate->setError("groupName","This group name is already associated with another Group name.");
    }
}


if($validate->isValid()){

    $existGroup = array();
    if($groupId!='')
        $existGroup = $pdo->selectOne("SELECT id from s_ticket_group where md5(id)=:id and is_deleted='N'",array(":id"=>$groupId));
    if(!empty($existGroup['id'])){
        $updParam = array(
            'title' => $groupName,
        );
        $updWhere = array(
            "clause" => " id=:id ",
            "params" => array(":id"=>$existGroup['id'])
        );
        $s_ticket = $pdo->update("s_ticket_group",$updParam,$updWhere,true);
        if(!empty($s_ticket['title'])){
            $activityDesc['title'] = $s_ticket['title'];
            $updateGroup = true;
        }
        $dbassignAdmin = $pdo->selectOne("SELECT GROUP_CONCAT(admin_id) as adminIds FROM s_ticket_assign_admin WHERE s_ticket_group_id=:id and is_deleted='N'",array(":id"=>$existGroup['id']));
        $deleteAdmin = $insertAdmin = array();
        if(!empty($dbassignAdmin['adminIds'])){
            $deleteAdmin = array_diff(explode(',',$dbassignAdmin['adminIds']),$adminIds);
        }
        
        if(!empty($deleteAdmin)){
            $deleteAdminWhere = array(
                "clause"=>" admin_id IN(".implode(',',$deleteAdmin).") AND is_deleted='N' AND s_ticket_group_id=:category_id ",
                "params"=>array(":category_id"=>$existGroup['id']),
            );
            $pdo->update("s_ticket_assign_admin",array("is_deleted"=>'Y'),$deleteAdminWhere);
            $updateGroup = true;
            $activityDesc['deleted_admin'] = implode(',',$deleteAdmin);
        }
        
        $insertAdmin = array_diff($adminIds,explode(',',$dbassignAdmin['adminIds']));
        if(!empty($insertAdmin)){
            $insAdminParam = array(
                "s_ticket_group_id" =>$existGroup['id'],
            );
            foreach($insertAdmin as $key => $value){
                $insAdminParam['admin_id'] = $value ;
                $pdo->insert("s_ticket_assign_admin",$insAdminParam);
            }   
            $updateGroup = true;
            $activityDesc['inserted_admin'] = implode(',',$insertAdmin);
        }
        $response['msg'] = 'Group Updated Successfully.';
    }else{
        $insParam = array(
            'title' => $groupName,
        );
        $insId = $pdo->insert("s_ticket_group",$insParam);

        if(!empty($adminIds)){
            $insAdminParam['s_ticket_group_id'] = $insId;
            foreach($adminIds as $value){
                $insAdminParam['admin_id'] = $value;
                $pdo->insert("s_ticket_assign_admin",$insAdminParam);
            } 
        }
        if(!empty($insId))
            $insertGroup = true;
        $response['msg'] = 'New Group Added Successfully.';
    }
    $response['status'] = 'success';

    $label = '';
    if($insertGroup){
        $label = ' Created new E-ticket Group ';
    }else if($updateGroup){
        $label = ' Updated E-ticket Group ';
    }
    
    $description['ac_message'] = array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$label.$groupName.'(',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/manage_etickets.php',
          'title'=> $groupName,
        ),
        'ac_message_2' =>')<br>',
      );
      if(!empty($activityDesc)){
        if(!empty($activityDesc['title'])){
            $description['desc_title'] = 'Title updated From : '.$activityDesc['title'].' To '.$groupName.'<br>';
        }
        if(!empty($activityDesc['deleted_admin'])){
            $deleted_admin = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',display_id,')') SEPARATOR ',<br>') as name from admin where is_deleted='N' and id in(".$activityDesc['deleted_admin'].") ");
            $description['desc_deleted'] = 'Unassigned Admin : <br>'.$deleted_admin['name'].'<br>';
        }
        if(!empty($activityDesc['inserted_admin'])){
            $deleted_admin = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',display_id,')') SEPARATOR ',<br>') as name from admin where is_deleted='N' and id in(".$activityDesc['inserted_admin'].") ");
            $description['desc_inserted'] = 'Assinged Admin :<br>'.$deleted_admin['name'].'<br>';
        }
      }
      $desc = json_encode($description);

      if($label !='' && !empty($label)){
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'] , 'Admin', 'E-Ticket Group',$_SESSION['admin']['name'],"",$desc);
      }
   

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
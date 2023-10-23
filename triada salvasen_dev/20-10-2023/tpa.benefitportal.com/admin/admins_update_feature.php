<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);
$id = $_REQUEST['id'];
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$old_type = isset($_REQUEST['old_type']) ? $_REQUEST['old_type'] : '' ;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '' ;
$access_level = isset($_REQUEST['access_level']) ? $_REQUEST['access_level'] : '' ;
$type_array = array("Agent Licensing", "Agent Support", "Enroller", "Member Services");
if($type != ''){
	if($type == 'Agent Licensing'){
		$feature_access_array = array(1,5,25,26,38,39,40);
	} else if($type == 'Agent Support'){
          $feature_access_array = array(1,5,6,7,8,11,12,20,49,13,14,25,26,38,39,40);
        } 
        else if($type == 'Member Services'){
          $feature_access_array = array(1,8,11,12,20,49,13,14,25,26,38,39,40);
        }
}

$sql_acl = "SELECT name FROM access_level ORDER BY name";
$res_acls = $pdo->select($sql_acl);

if (isset($_POST['submitaccess'])) {
	$features = $_POST['feature'];
	$sel_old_data = $pdo->selectOne("select feature_access,status,type, id from admin where md5(id)=:id",array(":id"=>$id));
	if (count($features) > 0) {
		$features = implode(',', array_unique($features));
	} else {
		$features = "";
	}
	$updateSql = array('feature_access' => makeSafe($features));
	if ($type != '') {
		$updateSql['type'] = makeSafe($type);
	}
	$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($id)));
	
	$pdo->update("admin", $updateSql, $where);
	setNotifySuccess("Access levels updated successfully");

	if($sel_old_data['feature_access'] != $features){

		$extra['old_type'] = $sel_old_data['feature_access'];
		$extra['new_type'] = $features;

		$data['access_lvl_name'] = $sel_old_data['type'].' from ';

		$feature = array_diff(explode(',',$extra['new_type']),explode(',',$extra['old_type']));
		$data['from'] = " blank to checked.";
		$features_id = implode("','",$feature);

		if(!empty($features_id)){
			$feature_name=$pdo->selectOne("SELECT GROUP_CONCAT(title SEPARATOR ', ') as features from feature_access where id IN('$features_id')");

			$data['features'] = (isset($feature_name['features'])?$feature_name['features']:'');
			$ro_feature_name = $pdo->selectOne("SELECT GROUP_CONCAT(' Read Only ',title) as features from feature_access where CONCAT('ro_',id) IN('$features_id')");
			if(!empty($ro_feature_name['features'])) {
				if(!empty($data['features'])) {
					$data['features'] .=",";
				}
				$data['features'] .= $ro_feature_name['features'];
			}
			$data['user_display_id'] = $_SESSION['admin']['display_id'];
			$entity = $pdo->selectOne('select fname,lname,display_id,id from admin where md5(id)=:id',array(':id'=>$id));
			$data['en_fname'] = $entity['fname'];
			$data['en_lname'] = $entity['lname'];
			$data['en_display_id'] = $entity['display_id'];
			$description['description'] = $_SESSION['admin']['display_id'].' updated '.$entity['fname'].' '.$entity['lname'].' '.$entity['display_id'].' '.$data['access_lvl_name'].' '.$data['features'].' '.$data['from'];
			activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $entity['id'], 'update_old_to_new_feature','Admin Feature Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($data));
		}	

		if(count($feature) == 0 || count($feature) > 0 )
		{
			$feature_blank = array_diff(explode(',',$extra['old_type']),explode(',',$extra['new_type']));
			$data['from'] = " checked to blank.";
			$data['features'] = "";
			$features_id = implode("','",$feature_blank);
			if(!empty($features_id)){
				$feature_name=$pdo->selectOne("SELECT GROUP_CONCAT(title SEPARATOR ', ') as features from feature_access where id IN('$features_id')");
				$data['features'] = (isset($feature_name['features'])?$feature_name['features']:"");

				$ro_feature_name = $pdo->selectOne("SELECT GROUP_CONCAT(' Read Only ',title) as features from feature_access where CONCAT('ro_',id) IN('$features_id')");
				if(!empty($ro_feature_name['features'])) {
					if(!empty($data['features'])) {
						$data['features'] .=",";
					}
					$data['features'] .= $ro_feature_name['features'];
				}
			}
			if(count($feature_blank) > 0){
				$data['user_display_id'] = $_SESSION['admin']['display_id'];
				$entity = $pdo->selectOne('select fname,lname,display_id,id from admin where md5(id)=:id',array(':id'=>$id));
				$data['en_fname'] = $entity['fname'];
				$data['en_lname'] = $entity['lname'];
				$data['en_display_id'] = $entity['display_id'];
				$description['description'] = $_SESSION['admin']['display_id'].' updated '.$entity['fname'].' '.$entity['lname'].' '.$entity['display_id'].' '.$data['access_lvl_name'].' '.$data['features'].' '.$data['from'];
				activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $entity['id'], 'update_old_to_new_feature','Admin Feature Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($data));
			}
		}

	}
	if($type!='' && $id!='' && $old_type!='')
	{
		$extra['from'] = ' from ';
		$extra['status_update'] = $old_type." to ".$type;
		$extra['user_display_id'] = $_SESSION['admin']['display_id'];
		$res_enity =$pdo->selectOne("SELECT fname,lname,display_id,id from admin where md5(id)=:id",array(":id"=>$id));
		$extra['en_fname'] = $res_enity['fname'];
		$extra['en_lname'] = $res_enity['lname'];
		$extra['en_display_id'] = $res_enity['display_id'];
		
		$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin level from '.$old_type." to ".$type;
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Access Level Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
	}

	if($action == 'all_users'){
		redirect("all_users.php", true);
	}else{
		redirect("admins.php", true);
	}
	
}

$acl_name = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_acl = "SELECT al.id as id,name,a.feature_access FROM admin a join access_level al ON(al.name=a.type) where md5(a.id)=:id and a.feature_access !='' ORDER BY al.id"; //TODO sql injection
    $selected = $pdo->selectOne($sql_acl,array(":id"=>$id));
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected['feature_access']);
        $acl_name = $selected['name'];
    }
}

$acl = [];
$acl_names = [];
$acl_features = [];
$sql_acl = "SELECT al.id as id,name,a.feature_access FROM admin a join access_level al ON(al.name=a.type) where md5(a.id)=:id and a.feature_access !='' ORDER BY al.id"; //TODO sql injection
$acls = $pdo->select($sql_acl,array(":id"=>$id));
foreach($acls as $acll){
    $acl_names[] = $acll['name'];
    $acl[$acll['id']] = $acll['name'];
    $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
}
$adminsSql = "SELECT feature_access,type,fname,lname,id,status from admin where md5(id)=:id";
$adminsParams = array(':id' => $id);
$adminsRow = $pdo->selectOne($adminsSql, $adminsParams);
$curAccess = explode(",", $adminsRow['feature_access']);

$exStylesheets = array('thirdparty/sweetalert2/sweetalert2.css');
$exJs = array('thirdparty/sweetalert2/sweetalert2.min.js', 'thirdparty\jquery_uniform/jquery.uniform.min.js');

$template = 'admins_update_feature.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
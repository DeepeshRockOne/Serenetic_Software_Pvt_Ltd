<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$is_delete = isset($_POST['is_delete']) ? $_POST['is_delete'] : '' ;
$is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '' ;
if($is_ajaxed && $is_delete){
    $action = $_POST['action'];
    $dep_id = $_POST['dep_id'];
    $customer_id = $_POST['id'];
    $response = array();
    $dep_rows = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,display_id from customer_dependent_profile where md5(id) =:id and md5(customer_id) = :customer_id",array(":id"=>$dep_id,":customer_id"=>$customer_id));
    if($action == 'Delete' && !empty($dep_rows['id'])){
        
        $upd_where = array(
            "clause" => " md5(id) = :id and md5(customer_id) = :customer_id ",
            "params" => array(
                ":id" => $dep_id,
                ":customer_id" => $customer_id
            )
        );
        $upd_where_dp = array(
            "clause" => " md5(cd_profile_id) = :id and md5(customer_id) = :customer_id ",
            "params" => array(
                ":id" => $dep_id,
                ":customer_id" => $customer_id
            )
        );
        $pdo->update('customer_dependent_profile',array("is_deleted"=>'Y'),$upd_where);
        $pdo->update('customer_dependent',array("is_deleted"=>'Y'),$upd_where_dp);
        $response['status'] = 'success';

        $rows = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,rep_id from customer where md5(id) =:id ",array(":id"=>$customer_id));

        $ac_desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>' Deleted dependent on member '.$rows['name'].'(',
            'ac_red_2'=>array(
                'href'=> 'members_details.php?id='.md5($rows['id']),
                'title'=> $rows['rep_id'],
            ),
            'ac_message_2'=>') <br> Dependent : '.$dep_rows['name'].'('.$dep_rows['display_id'].')',
        );  

        activity_feed(3,$_SESSION['agents']['id'],'Agent',$rows['id'], 'customer','Agent Deleted Dependent',"","",json_encode($ac_desc));
    } else {
        $response['status'] = 'fail';
    }
    header("Content-type: application/json");
    echo json_encode($response);
    exit;
}
$is_dep_ajaxed = isset($_POST['is_dep_ajaxed']) ? $_POST['is_dep_ajaxed'] : '' ;
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '' ;
if(!empty($customer_id) && $is_dep_ajaxed){
    $validate = new Validation();
    $relation = $_POST['relation'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $birth_date = $_POST['birth_date'];
    $ssn = phoneReplaceMain($_POST['ssn']);
    $is_ssn_edit = $_POST['is_ssn_edit'];
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
    $is_disabled = !empty($_POST['is_disabled']) && $_POST['is_disabled'] == 'on'  ? 'Y' : 'N';
    $action = $_POST['action'];
    $dep_id = checkIsset($_POST['dep_id']);

    $validate->string(array('required' => true, 'field' => 'relation', 'value' => $relation), array('required' => 'Relation is required'));

    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));

    $validate->string(array('required' => true, 'field' => 'birth_date', 'value' => $birth_date), array('required' => 'Date of Birth is required'));

    $validate->string(array('required' => true, 'field' => 'gender', 'value' => $gender), array('required' => 'Gender is required'));
    
    if (!$validate->getError('birth_date') && !empty($birth_date)) {
        list($mm, $dd, $yyyy) = explode('/', $birth_date);

        $primary_birth_date =  getname('customer',$customer_id,'birth_date','md5(id)');

		if (!checkdate($mm, $dd, $yyyy)) {
			$validate->setError('birth_date', 'Valid Date of Birth is required');
		}
		if (!$validate->getError('birth_date')) {
            $age_y = dateDifference($birth_date, '%y');
            $primary_age =  dateDifference($primary_birth_date, '%y');
			if ($age_y > 90) {
				$validate->setError('birth_date', 'You must be younger then 90 years of age');
			}else if($age_y > $primary_age && $relation == 'Child'){
                $validate->setError('birth_date', 'Child Age must be less then primary member.');
            }
		}
		if (strtotime($birth_date) > strtotime(date('Y-m-d'))) {
			$validate->setError('birth_date', 'select valid Date of Birth');
		}
		/*if (strtolower($relation) == 'child' && getAge($dob) >= 26) {
			            $validate->setError('dob', 'Child should be less than 26 years old');
		*/
    }

    if ($is_ssn_edit == "Y") {
        $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
    }else if(!empty($ssn)){
        $validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
    }

    $relation = getRelation($relation, $gender);

    if(!$validate->getError('fname') && !$validate->getError('lname') && !$validate->getError('gender') && !$validate->getError('dob')){

		$dependents_res = $pdo->selectOne("SELECT id FROM customer_dependent_profile where fname = :fname AND lname = :lname AND gender = :gender AND birth_date = :birth_date AND md5(customer_id) = :customer_id AND is_deleted = 'N'",array(':customer_id' => $customer_id,':fname' => $fname,':lname' => $lname,':birth_date' =>date('Y-m-d',strtotime($birth_date)),':gender' => $gender));
		if(!empty($dependents_res['id']) && md5($dependents_res['id']) != $dep_id){
			$validate->setError('fname', 'Dependent already exist');
		}
	}
	// if(in_array($relation, array('Wife','Husband'))){
	// 	$spouse_res = $pdo->selectOne("SELECT id FROM customer_dependent_profile where relation in('Wife','Husband') and md5(customer_id) = :customer_id and is_deleted = 'N'",array(':customer_id'=>$customer_id));

	// 	if(!empty($spouse_res['id']) && md5($spouse_res['id']) != $dep_id){
	// 		$validate->setError('relation', 'Spouse already exist');
	// 	}
    // }
    if ($validate->isValid()) {

        $customer_id =  getname('customer',$customer_id,'id','md5(id)');

        $dependent_params = array(
            'customer_id' => ($customer_id),
            'relation' => $relation,
            'fname' => makesafe($fname),
            'lname' => makesafe($lname),
            'birth_date' => date('Y-m-d', strtotime($birth_date)),
            'gender' => makesafe($gender),
            'is_disabled' => $is_disabled
        );
        if($is_ssn_edit == 'Y' || !empty($ssn)){
            $dependent_params['ssn'] = $ssn;
            $dependent_params['last_four_ssn'] = substr($ssn,-4);
        }
        $update_dep_arr = array();
        $activity_feed = false;
        if($action == 'Edit' && !empty($dep_id)){
            unset($dependent_params['customer_id']);

            $update_where = array(
                "clause" => " md5(id) = :id and customer_id = :customer_id ",
                "params" => array(
                    ":id" => $dep_id,
                    ":customer_id" => $customer_id
                )
            );
            $update_dep_arr = $pdo->update("customer_dependent_profile",$dependent_params,$update_where,true);
            if(!empty($update_dep_arr))
                $activity_feed = true;
            $response['msg'] = 'Dependent updated successfully!';
        }else{
            
            include_once dirname(__DIR__) .'/includes/function.class.php';
            $fnList = new functionsList();
            $dependant_display_id = $fnList->get_dependant_display_id();
            $dependent_params['display_id'] = $dependant_display_id;
            $ins_id = $pdo->insert('customer_dependent_profile', $dependent_params);
            
            $response['msg'] = 'Dependent Add successfully!';
            $activity_feed = true;
        }

        $ac_desc = array();

        $rows = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,rep_id from customer where id =:id ",array(":id"=>$customer_id));
       
        if($action == 'Edit'){
            $ac_ms_1 = ' Update Dependent details For Member ';
            $ac_action = 'Agent Update Dependent Detail';
        }else{
            $display_id = 
            $ac_ms_1 = ' Added new Dependent For Member ';
            $ac_action = 'Agent Added New Dependent';
        }
        $display_id = !empty($dependent_params['display_id']) ? $dependent_params['display_id'] : getname('customer_dependent_profile',$dep_id,'display_id','md5(id)');
        $ac_ms_2 = '<br>Dependent : '.$dependent_params['fname'].' '.$dependent_params['lname'].' ('.$display_id.')';

        $ac_desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>$ac_ms_1.$rows['name'].'(',
            'ac_red_2'=>array(
                'href'=> 'members_details.php?id='.md5($rows['id']),
                'title'=> $rows['rep_id'],
            ),
            'ac_message_2'=>')'.$ac_ms_2,
        );  
        
        if($activity_feed){
            if(!empty($update_dep_arr)){
                foreach($update_dep_arr as $key => $value){
                    if(!in_array($key,array('customer_id','ssn'))){
                        if(in_array($value,array('Y','N'))){
                            $value = $value == 'Y' ? 'selected' : 'unselected';
                            $dependent_params[$key] = $dependent_params[$key] == 'Y' ? 'selected' : 'unselected';
                        }
                        if($key == 'relation'){
                            $value = getRevRelation($value);
                            $dependent_params[$key] = getRevRelation($dependent_params[$key]);
                        }
                        if($key == 'birth_date'){
                            $value = date('m/d/Y',strtotime($value));
                            $dependent_params[$key] = date('m/d/Y',strtotime($dependent_params[$key]));
                        }
                        $ac_desc['key_value']['desc_arr'][$key] = ' From '.$value.' to '.$dependent_params[$key]; 
                    }
                }
            }
            activity_feed(3,$_SESSION['agents']['id'],'Agent',$customer_id, 'customer', $ac_action,"","",json_encode($ac_desc));
            // activity_feed(3,$rows['id'], 'customer' , $rows['id'], 'customer', $ac_action,$rows['name'],"",json_encode($ac_desc));
        }
        
        $response['status'] = 'success';
    }else{
        $errors = $validate->getErrors();
        $response['status'] = 'fail';
        $response['errors'] = $errors;
    }

    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}
$customer_id = $_GET['id'];
$dep_id = checkIsset($_GET['dep_id']);
$action = $_GET['action'];
$row = array();
$relation = '';
if($action == 'Edit'){
    $row = $pdo->selectOne("SELECT id,fname,lname,ssn as dssn,birth_date,gender,relation,is_disabled,display_id from customer_dependent_profile where md5(id)=:id and md5(customer_id)=:customer_id and is_deleted='N'",array(':id'=>$dep_id,":customer_id"=>$customer_id));
    $relation = getRevRelation($row['relation'],$row['gender']);
}

$total_dependent = $pdo->selectOne("SELECT count(id) as total from customer_dependent_profile where md5(customer_id)=:id and is_deleted='N'",array(":id"=>$customer_id));

$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js','thirdparty/masked_inputs/jquery.maskedinput.min.js');

$template = 'add_depedents.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
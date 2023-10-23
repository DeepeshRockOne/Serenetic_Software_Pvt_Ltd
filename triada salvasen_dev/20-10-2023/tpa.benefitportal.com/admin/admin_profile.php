<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$tmp_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$referer = basename($tmp_referer);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Admins Details';

$sql_acl = "SELECT md5(id) as id,name,dashboard,feature_access,created_at FROM access_level ORDER BY name";
$res_acl = $pdo->select($sql_acl);

$admin_id = $_SESSION['admin']['id'];
$id = $_REQUEST['id'];
$note_search_keayword = isset($_REQUEST['note_search_keayword']) ? $_REQUEST['note_search_keayword'] :'';
$note_ajax = isset($_POST['note_ajax']) ? $_POST['note_ajax'] : "";
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$note_incr='';
$show_pass = isset($_POST['show_pass']) ? $_POST['show_pass'] : '';
$level = isset($_POST['level']) ? $_POST['level'] : '';
if($show_pass!='')
{
	$extra['user_display_id'] =  $_SESSION['admin']['display_id'];
	$res_enity =$pdo->selectOne("SELECT id,fname,lname,display_id from admin where md5(id)=:id",array(":id"=>$id));
	$extra['en_fname'] = $res_enity['fname'];
	$extra['en_lname'] = $res_enity['lname'];
	$extra['en_display_id'] = $res_enity['display_id'];
	$description['ac_description'] = $_SESSION['admin']['display_id'].' read password '.$res_enity['fname'].' '.$res_enity['lname']."(".$res_enity['display_id'].")";
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res_enity['id'], 'Admin','Admin Read Password', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
	exit;
}

$note_search_keayword = cleanSearchKeyword($note_search_keayword); 
 
if($note_search_keayword != '')
{
	// $sch_params[':keyword'] = $note_search_keayword;
	$note_incr = " AND  n.description like '%$note_search_keayword%' AND md5(n.customer_id)=:id ";
}
if(preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/",$note_search_keayword))
{
	$note_search_keayword = date('Y-m-d',strtotime($note_search_keayword));
	$note_search_keayword = $tz->getDate($note_search_keayword);
	$note_search_keayword = date('Y-m-d',strtotime($note_search_keayword));
	$note_incr = " AND  n.created_at like '%$note_search_keayword%' AND md5(n.customer_id)=:id ";
}
	$sch_params[':id']=$id;

$res_notes = $pdo->select("SELECT a.id as admin_id,CONCAT(a.fname,' ',a.lname) as name,a.display_id as display_id,af.id as ac_id,n.id,n.admin_id,n.description,DATE_FORMAT(n.created_at, '%a., %b. %d,%Y @  %l:%i %p') as date,n.created_at as cdate,TIMESTAMPDIFF(HOUR,n.created_at,now()) as difference from note n LEFT JOIN activity_feed af ON(af.entity_id=n.id and af.entity_type='note') LEFT JOIN admin a ON(a.id=n.admin_id and a.is_deleted='N')  where md5(n.customer_id)=:id and n.user_type='admin' and n.is_deleted='N'  and md5(af.user_id)=:id $note_incr order by n.id desc limit 50",$sch_params);

if($note_search_keayword !== ''  || $note_ajax == 'Y'){
	$note_desc =" <div class='activity_wrap p-t-0'>";
					if(count($res_notes) > 0){
					foreach($res_notes as $note) {
						$note_desc.='<div class="media">';
						$note_desc .= '<div class="media-body fs14 br-n"><p class="text-light-gray mn">';
						$note_desc.= $tz->getDate($note['cdate'])."</p>";	
						$note_desc.='<p class="mn">'.custom_charecter($note['description'],400,$note['name'],$note['display_id'],$note['admin_id']).'</p></div>';
						$note_desc.='<div class="media-right text-nowrap">';
						
							if($_SESSION['admin']['id'] == $note['admin_id']){
								if($note['difference'] < 24){
								$note_desc.='<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_admin('.$note['id'].','."''".')" data-value="Admin"><i class="fa fa-edit fa-lg"></i></a>&nbsp;';
								}
								$note_desc.='<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note('.$note['id'].','.$note['ac_id'].')" data-value="Admin"><i class="fa fa-trash fa-lg"></i></a>&nbsp;';
						}
						$note_desc.='<a href="javascript:void(0);" id="edit_note_id" data-original-title="Edit" onclick=edit_note_admin('.$note['id'].','.'"view"'.') data-value="Admin"><i class="fa fa-eye fa-lg"></i></a>';
						$note_desc .="</div></div>";
					}
				}else{
					$note_desc .='<p class="text-center mn"> No Notes Found. </p>';
				}
	$note_desc .="</div>";
	echo $note_desc;
	exit;
}

if (md5($admin_id) != $id) {
	has_access(3);
}

$query = parse_url($referer, PHP_URL_QUERY);
parse_str($query, $params);
$test = isset($params['activeTab']) ? $params['activeTab'] : '';

$activeTab = ($test == "" ? "profile" : $test);
$user_type = 'Admin';

$validate = new Validation();

$select_user = "SELECT *,AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as definedPassword FROM `admin` WHERE md5(id)= :id";
$where = array(':id' => makeSafe($id));
$row = $pdo->selectOne($select_user, $where);

if (count($row) <= 0) {
	redirect("admins.php");
}

$export = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';
if($export!=''){

	// array('display_id'=>$_SESSION['admin']['display_id'])
	$extra['user_display_id'] = $_SESSION['admin']['display_id'];
	$description['ac_description'] = $_SESSION['admin']['display_id'].' created admin export file ';
	activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $row['id'], 'export_file','Admin Export file Created', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));

   $sel_sql = "SELECT af.entity_id,af.user_id,a.display_id,CONCAT(a.lname,' ',a.fname) as impact_name,af.entity_action,af.changed_at,af.ip_address,af.entity_type FROM activity_feed af LEFT JOIN admin a ON(a.id=af.user_id or a.id=af.entity_id) WHERE af.entity_action!='New Order' AND md5(a.id)=:id AND af.is_deleted ='N' AND af.user_type='Admin'";
	$res_activity = $pdo->select($sel_sql,array(":id"=>$id));

	require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
	require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

	$objPHPExcel = new PHPExcel();
	$index = 0;

	$objPHPExcel->setActiveSheetIndex($index);
	$index++;

	$objPHPExcel->getActiveSheet()->setCellValue('A1','Account:');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', $res_activity[0]['impact_name']." ".$res_activity[0]['display_id'] );
	
	$i = 3;

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Activity Type');
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Acccount Impacted');
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'Acccount Name');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'Time Stamp');
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'IP Address'); 
	
	$i++;
	if(count($res_activity) > 0)
	{

	   foreach($res_activity as $rows)
	   {
			$sel_impact='';
			$res_c=array();
			if($rows['entity_id']!=''){
				$sel_impact="SELECT rep_id,CONCAT(fname,' ',lname) as cimpact_name from customer where id='".$rows['entity_id']."'";
				$res_c=$pdo->selectOne($sel_impact);
			}
			
			if(count($res_c)==0 && $rows['entity_id']!=''){
				$sel_impact="SELECT display_id as rep_id,CONCAT(fname,' ',lname) as cimpact_name from admin where id='".$rows['entity_id']."'";
				$res_c=$pdo->selectOne($sel_impact);
			}

		   if($rows['entity_type'] == 'note')
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Note created');
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $rows['entity_action']);
			}

		   $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $rows['display_id']);
		   $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $rows['impact_name']);
		   if(count($res_c)>0)
		   {
		   $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $res_c['rep_id']);
		   $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $res_c['cimpact_name']);
		   }


		   $show_time=date_format(date_create($rows['changed_at']),"D.,M. d,Y @ h:i A");
		   $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $show_time);
		   $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $rows['ip_address']);
		   $i++;
	   }
	}

	$filename = "Activity history". date("Ymd", time()) . ".xls";

    ob_start();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  
    $objWriter->save("php://output");
    $xlsData = ob_get_contents();
    ob_end_clean();

    $response =  array(
            'op' => 'ok',
            'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
        );
    die(json_encode($response));

}
if(!empty($level) && !empty($id)){
	$update_params = array('type' => $level);
	$where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($id)));
	$pdo->update('admin',$update_params,$where);

	$extra['en_fname'] = $row['fname'];
	$extra['en_lname'] = $row['lname'];
	$extra['en_display_id'] = $row['display_id'];
	$extra['user_display_id'] = $_SESSION['admin']['display_id'];
	$description['ac_message'] =array(
	'ac_red_1'=>array(
		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		'title'=>$_SESSION['admin']['display_id'],
	),
	
	'ac_message_1' =>' Level changed '.$row['fname'].' '.$row['lname'],
	'ac_red_2'=>array(
		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($row['id']),
		'title'=>"(".$row['display_id'].")",
	),
	);
	activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $row['id'], 'level_changed','Admin Level Changed', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
	echo json_encode(array('status' => 'success'));
	exit();
}
if($row['id']!=$_SESSION['admin']['id']){
	$extra['en_fname'] = $row['fname'];
	$extra['en_lname'] = $row['lname'];
	$extra['en_display_id'] = $row['display_id'];
	$extra['user_display_id'] = $_SESSION['admin']['display_id'];
	
	$description['ac_message'] =array(
	'ac_red_1'=>array(
		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		'title'=>$_SESSION['admin']['display_id'],
	),
	
	'ac_message_1' =>' read account '.$row['fname'].' '.$row['lname'],
	'ac_red_2'=>array(
		'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($row['id']),
		'title'=>"(".$row['display_id'].")",
	),
	);
	activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $row['id'], 'read_account','Read Admin Account', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
}

$password = $row['definedPassword'];

$email = stripslashes($row['email']);
$cpassword = isset($row['password_chk']) ? stripslashes($row['password_chk']) : '';
$fname = stripslashes($row['fname']);
$lname = stripslashes($row['lname']);
$phone = $row['phone'];

$mobile_num = format_telephone($phone);
$profile_image = $row['photo'];
$admin_status = stripslashes($row['status']);
$display_id = $row['display_id'];
$type = $row['type'];

if (isset($_POST['submit'])) {
	$validate->string(array('required' => true, 'field' => 'birth_date', 'value' => $bdate), array('required' => 'Birthdate is required'));
	if ($validate->isValid()) {
		$insParams = array('birth_date' => date($DATE_FORMAT, strtotime(stripslashes($row['birth_date']))));
		$admin_bdate = $pdo->insert("admin", $insParams);
	}
}

$status_class="";
if($admin_status == 'Pending'){
	$status_class = 'Abandoned';
}else if($admin_status == 'Inactive'){
	$status_class = 'Unqualified';
}

$errors = $validate->getErrors();

$exStylesheets = array('thirdparty/colorbox/colorbox.css','thirdparty/switchery/dist/switchery.min.css');

$exJs = array('thirdparty/jquery_autotab/jquery.autotab-1.1b.js', 'thirdparty/masked_inputs/jquery.maskedinput.min.js', 'thirdparty/ajax_form/jquery.form.js','thirdparty/colorbox/jquery.colorbox.js','thirdparty/iPhonePassword/js/jQuery.dPassword.js','thirdparty/switchery/dist/switchery.min.js','js/password_validation.js'.$cache,'thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$page_title = "Admin Details";
$template = 'admin_profile.inc.php';
include_once 'layout/end.inc.php';
?>
<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$response = array();

$report_id = checkIsset($_POST['report_id']);
$portal = checkIsset($_POST['portal']);
$category_id = checkIsset($_POST['category_id']);
$report_key = checkIsset($_POST['report_key']);
$report_name = checkIsset($_POST['report_name']);
$export_file_report_name = checkIsset($_POST['export_file_report_name']);
$purpose_summary = checkIsset($_POST['purpose_summary']);
$definitions = checkIsset($_POST['definitions']);
$is_allow_schedule = isset($_POST['is_allow_schedule'])?'Y':'N';
$tmp_definitions = strip_tags($definitions);
$order_by = checkIsset($_POST['order_by']);
$file_type = checkIsset($_POST['file_type']);
$ftp_name = checkIsset($_POST['ftp_name']);

if(empty($report_id) && empty($report_key)) {
	$report_key = strtolower(str_replace(' ','_',$report_name));
}

$validate->string(array('required' => true, 'field' => 'portal', 'value' => $portal), array('required' => 'Please select portal'));
$validate->string(array('required' => true, 'field' => 'category_id', 'value' => $category_id), array('required' => 'Please select category'));
$validate->string(array('required' => true, 'field' => 'report_key', 'value' => $report_key), array('required' => 'Report Key is required'));
$validate->string(array('required' => true, 'field' => 'report_name', 'value' => $report_name), array('required' => 'Report Name is required'));
$validate->string(array('required' => true, 'field' => 'export_file_report_name', 'value' => $export_file_report_name), array('required' => 'Export File Report Name is required'));
$validate->string(array('required' => true, 'field' => 'purpose_summary', 'value' => $purpose_summary), array('required' => 'Purpose Summary is required'));
$validate->string(array('required' => true, 'field' => 'definitions', 'value' => $tmp_definitions), array('required' => 'Definitions is required'));

if(empty($validate->getError('report_key'))) {
	if(!empty($report_id)) {
		$report_key_exist = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)!=:id AND report_key=:report_key",array(':id'=>$report_id,':report_key'=>$report_key));
		if(!empty($report_key_exist)) {
			$validate->setError('report_key',"Report Key is already exist");
		}
	} else {
		$report_key_exist = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE report_key=:report_key",array(':report_key' => $report_key));
		if(!empty($report_key_exist)) {
			$validate->setError('report_key',"Report Key is already exist");
		}
	}
}

if ($validate->isValid()) {
	if(!empty($report_id)) {
		$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(':id' => $report_id));

		$update_params = array(
			'category_id' =>$category_id,
			'report_key' =>$report_key,
			'report_name' =>$report_name,
			'export_file_report_name' =>$export_file_report_name,
			'purpose_summary' =>$purpose_summary,
			'definitions' =>makeSafe($definitions),
			'is_allow_schedule' =>$is_allow_schedule,
			'order_by' => $order_by,
			'file_type' => $file_type,
			'ftp_name' => $ftp_name,
		);
		$upd_where = array(
	        'clause' => 'md5(id)=:id',
	        'params' => array(
	          ':id' => $report_id,
	        ),
	    );
	    $old_params = $pdo->update("$REPORT_DB.rps_reports", $update_params, $upd_where,true);

	    $flg = "true";
		$desc = array();
		$desc['ac_message'] =array(
		    'ac_red_1'=> array(
		        'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		        'title'=> $_SESSION['admin']['display_id'],
		    ),
		    'ac_message_1' =>' updated report <span class="text-action">'.$report_name.'</span>',
		);
		foreach($old_params as $key2 => $val){
			if(in_array($key2,array("definitions","order_by","file_type","ftp_name"))) {
				continue;
			}
			$tmp_key2 = ucwords(str_replace('_',' ',$key2));

			if($key2 == "category_id") {
				$tmp_key2 = "Category";
				$val = getname("$REPORT_DB.rps_category",$val,'title','id');
				$update_params[$key2] = getname("$REPORT_DB.rps_category",$update_params[$key2],'title','id');
			}

			
            $desc['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$update_params[$key2].".<br>";
            $flg = "false";
        }

		if(isset($old_params['definitions']) && strip_tags($definitions) != strip_tags($old_params['definitions'])) {
			$desc["ac_description_link"] = array(
	            'Old Definitions : ' => array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'View','data-desc'=>htmlspecialchars($old_params['definitions']),'data-encode'=>'no'),

	            'New Definitions : ' => array('href'=>'#javascript:void(0)','class'=>'descriptionPopup red-link','title'=>'View','data-desc'=>htmlspecialchars($definitions),'data-encode'=>'no'),
	        );
	        $flg = "false";
		}

		if($flg == "false"){
			$desc = json_encode($desc);
			activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'Admin','Updated Report.',"","",$desc);
		}

	    $response['status'] = "success";
		$response['message'] = "Report updated successfully";
		setNotifySuccess("Report updated successfully");
	} else {
		if(empty($order_by)) {
			$tmp_row = $pdo->selectOne("SELECT max(order_by) as order_by FROM $REPORT_DB.rps_reports WHERE category_id=:category_id",array(':category_id' => $category_id));

			$order_by = 1;
			if(!empty($tmp_row['order_by'])) {
				$order_by = $tmp_row['order_by'] + 1;
			}
		}

		$insert_params = array(
			'category_id' => $category_id,
			'report_key' => $report_key,
			'report_name' => $report_name,
			'export_file_report_name' => $export_file_report_name,
			'purpose_summary' => $purpose_summary,
			'definitions' => $definitions,
			'is_allow_schedule' => $is_allow_schedule,
			'order_by' => $order_by,
			'file_type' => $file_type,
			'ftp_name' => $ftp_name,
		);
		$insert_id = $pdo->insert("$REPORT_DB.rps_reports",$insert_params);

		$desc = array();
		$desc['ac_message'] =array(
		    'ac_red_1'=> array(
		        'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		        'title'=> $_SESSION['admin']['display_id'],
		    ),
		    'ac_message_1' =>' created report <span class="text-action">'.$report_name.'</span>',
		);
		$desc = json_encode($desc);
		activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'Admin','Created Report.',"","",$desc);

		$response['status'] = "success";
		$response['message'] = "Report added successfully";
		setNotifySuccess("Report added successfully");
		$response['insert_id'] = $insert_id;
	}
} else {
  	$response['status'] = "fail";
  	$response['errors'] = $validate->getErrors();  
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/cron_scripts/eligibility_file_functions.php";
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
include_once dirname(__DIR__) . '/includes/files.class.php';
$FilesClass = new FilesClass();
// $Rpdo = new PdoOptRP();
set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

$today = date('Y-m-d H:i:s');

$req_res = $pdo->select("SELECT br.*,AES_DECRYPT(br.password,'" . $CREDIT_CARD_ENC_KEY . "') as file_password,bf.file_name,bf.carrier_id,bf.period_type 
	FROM billing_requests br
	JOIN billing_files bf on(br.file_id = bf.id) 
	WHERE br.is_deleted='N' AND bf.cancel_processing = 'N' AND br.is_process_active='N' AND br.status='Pending' AND br.file_process_date<='$today' LIMIT 1");
if(!empty($req_res) && is_array($req_res)) {
	foreach ($req_res as $key => $req_row) {
		add_billing_request('billing_files',$req_row['id']);
	}
	exit();

	foreach ($req_res as $key => $req_row) {
		$billing_file_uploads = '';
		$BILLING_FILES_DIR = $UPLOAD_DIR.'billing_files';
		$csv_filename = '';
		$error_filename = '';
		$error_content = '';
		
		//  update file process status to active
		$req_where = array(
			"clause"=>"id=:id",
			"params"=>array(
			  ":id"=>$req_row['id'],
			)
		);
		$req_data = array(
			'status' => "Running",
			'is_process_active' => "Y",
			'updated_at' => "msqlfunc_NOW()",
		);
		$pdo->update("billing_requests",$req_data,$req_where);
	
		$file_id = isset($req_row['file_id']) ? $req_row['file_id'] : '';
		$file_type = isset($req_row['file_type']) ? $req_row['file_type'] : '';
		$generate_via = isset($req_row['generate_via']) ? $req_row['generate_via'] : '';
		$email = isset($req_row['email']) ? $req_row['email'] : '';
		$password = isset($req_row['password']) ? $req_row['password'] : '';
		$FTP = isset($req_row['ftp']) ? $req_row['ftp'] : '';
		$is_manual = isset($req_row['is_manual']) ? $req_row['is_manual'] : '';
		$prd_ids = $FilesClass->getBillingFilePrd($file_id,false);

		if(!empty($req_row['extra_params']) && $req_row['extra_params'] != "") {
			$extra_params = json_decode($req_row['extra_params'],true);
			if(is_array($extra_params) && !empty($extra_params)){
			// $start_date = isset($extra_params['start_date']) ? $extra_params['start_date'] : '';
			// $end_date = isset($extra_params['end_date']) ? $extra_params['end_date'] : '';
			$join_range = isset($extra_params['join_range']) ? $extra_params['join_range'] : '';
			$added_date = isset($extra_params['added_date']) ? $extra_params['added_date'] : '';
			$fromdate = isset($extra_params['fromdate']) ? $extra_params['fromdate'] : '';
			$todate = isset($extra_params['todate']) ? $extra_params['todate'] : '';
			}
		}

		$filter_options = array();
		if(!empty($req_row['filter_options']) && $req_row['filter_options'] != "") {
			$filter_options = json_decode($req_row['filter_options'],true);
		}

		$agent_ids_array = array();

		if($file_id != 0){
			include_once "billing_files/billing_files_generator/generate_billing_file.inc.php";
			$billing_file_uploads = $BILLING_FILES_DIR."/";
			
			if (!file_exists($billing_file_uploads)) {
			    mkdir($billing_file_uploads, 0777, true);
			}
			
			$csv_filename = "SE_" . $req_row['file_name'] . "_BILL_File" . date("mdY", time()).$req_row['id'].".xlsx";
			$xls_filename = "SE_" . $req_row['file_name'] . "_BILL_File" . date("mdY", time()).$req_row['id'].".xlsx";
			$error_filename = "SE_" . $req_row['file_name'] . "_BILL_File_Error_" . date("mdY", time()).$req_row['id'].".xlsx";
			$zip_filename = "SE_" . $req_row['file_name'] . "_BILL_File" . date("mdY", time()).$req_row['id'].".zip";
			
			
		}

         	       
	if(!empty($content) || 1){
			// $file_upload = file_put_contents($billing_file_uploads.''.$csv_filename,$content);
			if($req_row['generate_via'] == 'Email'){
				$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
				$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
				$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
				$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
				$objPHPExcel->getActiveSheet()->getProtection()->setPassword($req_row['file_password']);
			}

			$filename = $csv_filename;
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    		$objWriter->save($billing_file_uploads.$filename);
    		$objPHPExcel->disconnectWorksheets();
			$objPHPExcel->garbageCollect();
			unset($objPHPExcel);
			
	        $records=count($orderRows);
	      		$insertParams = array(
	                    'service_group_id' => $req_row['file_id'],
	                    'admin_id' => $req_row['user_id'],
	                    'file_name' => $csv_filename,
	                    'req_id' => $req_row['id'],
	                    'status' => 'Processed',
	                    'records'=>$records,
	                    'from_generator'=>'Y',
	                    'uploaded_by' => $req_row['user_id'],
	                    'uploaded_at' => 'msqlfunc_NOW()',
	                    'processed_by' => $req_row['user_id'],
	                    'processed_at' => 'msqlfunc_NOW()',
	                    'updated_at' => 'msqlfunc_NOW()',
	                    'created_at' => 'msqlfunc_NOW()',
	                );
        $history_file_id = $pdo->insert("billing_history", $insertParams);
      // insert in eligibility history table code ends

       

			$is_sent  = false;
			if($req_row['generate_via'] == 'Email'){
				
				$link = $HOST.'/billing_access.php?id='.md5($req_row['id']).'&fId='.md5($req_row['file_id']);
				$email = $req_row['email'];
				$trigger_id='93';
				// $trigger_id='10';
				$params = array();
				$params['link'] = $link;
				$params['FileName'] = $req_row['file_name'];

				$smart_tags = get_user_smart_tags($agent_row['id'],'agent');
                
                if($smart_tags){
                    $mail_data = array_merge($mail_data,$smart_tags);
                }

				// $attachments = array($BILLING_FILES_WEB . $csv_filename);
				trigger_mail($trigger_id, $params, $email);
				
			}else if($req_row['generate_via'] == 'FTP'){
				// connect and login to FTP server
				$ftp_server = "34.218.29.172";
				$ftp_username = "egfiles";
				$ftp_userpass = "jaf39KhYn";

				$sftp = new Net_SFTP($ftp_server);
				if (!$sftp->login($ftp_username, $ftp_userpass)) {
				    exit('Login Failed');
				}else{
					echo "connected";
				}

				$success = $sftp->put('/outbound/' . $csv_filename, 
                                $billing_file_uploads . $csv_filename, 
                                 NET_SFTP_LOCAL_FILE);
				// pre_print($success,false);

			}

			
			$req_where = array(
				"clause"=>"id=:id",
				"params"=>array(
				  ":id"=>$req_row['id'],
				)
			);
			$req_data = array(
				'processed_date' => date('Y-m-d'),
				'processed_file_name' => $csv_filename,
				'is_process_active' => "N",
				'status' => "Processed",
				'updated_at' => "msqlfunc_NOW()",
			);
			if($is_sent){
					$req_data['is_sent_to_email'] = 'Y';
					$req_data['zip_link'] = $zip_filename;
			}
			$pdo->update("billing_requests",$req_data,$req_where);
			//  update file process counter code start
			if($req_row['is_manual'] == 'N'){
				 	$sqlSchedule = "SELECT * FROM billing_schedule WHERE is_deleted='N' AND file_id=:file_id";
				 	$resSchedule = $pdo->selectOne($sqlSchedule,array(":file_id"=>$req_row['file_id']));
				 	$process_cnt = 0;
				 	$schedule_id = '';
				 	if(is_array($resSchedule) && !empty($resSchedule)){
				 		$process_cnt = $resSchedule['process_cnt'] + 1;
				 		$schedule_id = $resSchedule['id'];
				 	}
					if($schedule_id != ''){
								$upd_where = array(
									"clause"=>"id=:id",
									"params"=>array(
									  ":id"=>$schedule_id,
									)
								);
								$upd_data = array(
									'process_cnt' => $process_cnt,
									'last_processed' => "msqlfunc_NOW()",
								);
								$pdo->update("billing_schedule",$upd_data,$upd_where);

	
								//  next scheduled date code start
									$next_schedule_date = next_billing_schedule($schedule_id);
									if($next_schedule_date != ''){
										$req_where = array(
										"clause"=>"id=:id",
										"params"=>array(
										  ":id"=>$req_row['file_id'],
											)
										);
										$req_data = array(
											'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
											'last_processed' => date('Y-m-d H:i'),
										);
										$pdo->update("billing_files",$req_data,$req_where);
									}
								//  next scheduled date code ends
					}
			}else{
				$req_data = array(
					'last_processed' => date('Y-m-d H:i'),
				);
				$req_where = array("clause"=>"id=:id",
							"params"=>array(
							  ":id"=>$req_row['file_id'],
								)
							);
				$pdo->update("billing_files",$req_data,$req_where);
			}
			
		}
	// end foreach loop
	}
}
echo "Completed";
dbConnectionClose();
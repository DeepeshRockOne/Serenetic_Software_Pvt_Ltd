<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/cron_scripts/eligibility_file_functions.php";
require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
// $Rpdo = new PdoOptRP();
set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

$today = date('Y-m-d H:i:s');

$req_res = $pdo->select("SELECT fr.*,AES_DECRYPT(fr.password,'" . $CREDIT_CARD_ENC_KEY . "') as file_password,ff.file_name,ff.products 
	FROM fulfillment_requests fr
	JOIN fulfillment_files ff on(fr.file_id = ff.id) 
	WHERE ff.cancel_processing = 'N' AND fr.is_deleted='N' AND fr.is_process_active='N' AND fr.status='Pending' AND fr.file_process_date<='$today' LIMIT 1");

if(!empty($req_res) && is_array($req_res)) {

	foreach ($req_res as $key => $req_row) {
		add_fulfillment_request('fulfillment_files',$req_row['id']);
	}
	exit();

	foreach ($req_res as $key => $req_row) {
		$fulfillment_file_uploads = '';
		$FULFILLMENT_FILES_DIR = $UPLOAD_DIR.'fulfillment_files';
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
		$pdo->update("fulfillment_requests",$req_data,$req_where);
	
		$file_id = isset($req_row['file_id']) ? $req_row['file_id'] : '';
		$file_type = isset($req_row['file_type']) ? $req_row['file_type'] : '';
		$generate_via = isset($req_row['generate_via']) ? $req_row['generate_via'] : '';
		$email = isset($req_row['email']) ? $req_row['email'] : '';
		$password = isset($req_row['password']) ? $req_row['password'] : '';
		$FTP = isset($req_row['ftp']) ? $req_row['ftp'] : '';
		$is_manual = isset($req_row['is_manual']) ? $req_row['is_manual'] : ''; 
		$prd_ids = isset($req_row['products']) ? $req_row['products'] : ''; 

		if(!empty($req_row['extra_params']) && $req_row['extra_params'] != "") {
			$extra_params = json_decode($req_row['extra_params'],true);
			if(is_array($extra_params) && !empty($extra_params)){
			$since_date = isset($extra_params['since_date']) ? $extra_params['since_date'] : '';
			$to_date = isset($extra_params['to_date']) ? $extra_params['to_date'] : '';
			}
		}

		$agent_ids_array = array();

		if($file_id != 0){
			
			if($file_type == "full_file"){
				$csv_filename = "SE_FF_" . $req_row['file_name'] . "_Full_File_" . date("mdY", time()) ."_".$req_row['id'].".csv";
				$xls_filename = "SE__FF_" . $req_row['file_name'] . "_Full_File_" . date("mdY", time()) ."_".$req_row['id'].".xlsx";
				$error_filename = "SE_FF_" . $req_row['file_name'] . "_Full_File_Error_" . date("Ymd", time())."_".$req_row['id'].".csv";
				$zip_filename = "SE__FF" . $req_row['file_name'] . "_Full_File_" . date("mdY", time())."_".$req_row['id'].".zip";
			}else{
				$csv_filename = "SE_FF_" . $req_row['file_name'] . "_ACT_File_" . date("mdY", time())."_".$req_row['id'].".csv";
				$xls_filename = "SE_FF_" . $req_row['file_name'] . "_ACT_File_" . date("mdY", time())."_".$req_row['id'].".xlsx";
				$error_filename = "SE_FF_" . $req_row['file_name'] . "_ACT_File_Error_" . date("mdY", time())."_".$req_row['id'].".csv";
				$zip_filename = "SE_FF_" . $req_row['file_name'] . "_ACT_File_" . date("mdY", time())."_".$req_row['id'].".zip";
			}

			include_once "fulfillment_files/fulfillment_files_generator/generate_fulfillment_file.inc.php";
			$fulfillment_file_uploads = $FULFILLMENT_FILES_DIR."/";
			
			if (!file_exists($fulfillment_file_uploads)) {
			    mkdir($fulfillment_file_uploads, 0777, true);
			}
			
		}

         	       
	if(!empty($content)){
			$file_upload = file_put_contents($fulfillment_file_uploads.''.$csv_filename,$content);

			// insert in fulfillment history table code start
	  		$field_row = csvToArraywithFields($fulfillment_file_uploads.''.$csv_filename);
	        $records=count($field_row);
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
        $history_file_id = $pdo->insert("fulfillment_history", $insertParams);
      // insert in fulfillment history table code ends

       // insert in fulfillment error history table code start
	        if(!empty($error_content)){
		       	$fulfillment_error_file_uploads = $fulfillment_file_uploads.'error_files/';
		        $file_upload = file_put_contents($fulfillment_error_file_uploads.''.$error_filename,$error_content);
				// insert in fulfillment history table code start
			  	$field_row = csvToArraywithFields($fulfillment_error_file_uploads.''.$error_filename);
			    $records=count($field_row);
			      		$insertParams = array(
			                    'service_group_id' => $req_row['file_id'],
			                    'history_file_id' => $history_file_id,
			                    'admin_id' => $req_row['user_id'],
			                    'file_name' => $error_filename,
			                    'status' => 'Processed',
			                    'records'=>$records,
			                    'uploaded_by' => $req_row['user_id'],
			                    'uploaded_at' => 'msqlfunc_NOW()',
			                    'processed_by' => $req_row['user_id'],
			                    'processed_at' => 'msqlfunc_NOW()',
			                    'updated_at' => 'msqlfunc_NOW()',
			                    'created_at' => 'msqlfunc_NOW()',
			                );
		        $pdo->insert("fulfillment_error_files", $insertParams);
	        }
       // insert in fulfillment history table code ends


			$is_sent  = false;
			if($req_row['generate_via'] == 'Email'){
				// $upload_zip  = file_put_contents($csv_filename,$content);
				$password = $req_row['file_password'];
				$link = $HOST.'/fulfillment_access.php?id='.md5($req_row['id']).'&fId='.md5($req_row['file_id']);

				// pre_print($link,false);
				$email = $req_row['email'];
				$trigger_id='94';
				// $trigger_id='10';
				$params = array();
				$params['link'] = $link;
				$params['FileName'] = $req_row['file_name'];
				
				// $attachments = array($ELIGIBILITY_FILES_WEB . $csv_filename);
				trigger_mail($trigger_id, $params, $email);
				// }
				// unlink($csv_filename);
				// unlink($zip_filename);
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
                                $fulfillment_file_uploads . $csv_filename, 
                                 NET_SFTP_LOCAL_FILE);
				
			}

			if($file_upload){
					// check if file delievery is email or FTP
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
					$pdo->update("fulfillment_requests",$req_data,$req_where);
				//  update file process counter code start
				if($req_row['is_manual'] == 'N'){
					 	$sqlSchedule = "SELECT * FROM fulfillment_schedule WHERE is_deleted='N' AND file_id=:file_id";
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
									$pdo->update("fulfillment_schedule",$upd_data,$upd_where);

		
									//  next scheduled date code start
										$next_schedule_date = next_fulfillment_schedule($schedule_id);
										if($next_schedule_date != ''){
											$req_where = array(
											"clause"=>"id=:id",
											"params"=>array(
											  ":id"=>$req_row['file_id'],
												)
											);
											$req_data = array(
												'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
												'last_processed' => "msqlfunc_NOW()",
											);
											$pdo->update("fulfillment_files",$req_data,$req_where);
										}
									//  next scheduled date code ends
						}
				}else{
					$req_data = array(
						'last_processed' => "msqlfunc_NOW()",
					);
					$req_where = array("clause"=>"id=:id",
								"params"=>array(
								  ":id"=>$req_row['file_id'],
									)
								);
					$pdo->update("fulfillment_files",$req_data,$req_where);
				}
				//  update file process counter code ends
			}
		}
	// end foreach loop
	}
}
echo "Completed";
dbConnectionClose();
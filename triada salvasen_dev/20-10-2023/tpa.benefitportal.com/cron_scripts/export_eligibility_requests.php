<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/cron_scripts/eligibility_file_functions.php";
require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
// $Rpdo = new PdoOptRP();
set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

$today = date('Y-m-d H:i:s');

$req_res = $pdo->select("SELECT er.*,AES_DECRYPT(er.password,'" . $CREDIT_CARD_ENC_KEY . "') as file_password,ef.file_key,ef.file_name,ef.product_type,ef.products 
	FROM eligibility_requests er
	JOIN eligibility_files ef on(er.file_id = ef.id) 
	WHERE ef.cancel_processing = 'N' AND er.is_deleted='N' AND er.is_process_active='N' AND er.status='Pending' AND er.file_process_date<='$today' LIMIT 1");
//pre_print($req_res);
if(!empty($req_res) && is_array($req_res)) {

	foreach ($req_res as $key => $req_row) {
		add_eligibility_request('eligibility_files',$req_row['id']);
	}
	exit();

	foreach ($req_res as $key => $req_row) {
		$eligibility_file_uploads = '';
		$ELIGIBLITY_FILES_DIR = $UPLOAD_DIR.'eligibility_files';
		$csv_filename = '';
		$text_filename = '';
		$error_filename = '';
		$error_content = '';
		$file_format = 'csv';

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
		$pdo->update("eligibility_requests",$req_data,$req_where);
	
		$file_id = isset($req_row['file_id']) ? $req_row['file_id'] : '';
		$file_key = isset($req_row['file_key']) ? $req_row['file_key'] : '';
		$file_type = isset($req_row['file_type']) ? trim($req_row['file_type']) : '';
		$generate_via = isset($req_row['generate_via']) ? $req_row['generate_via'] : '';
		$email = isset($req_row['email']) ? $req_row['email'] : '';
		$password = isset($req_row['password']) ? $req_row['password'] : '';
		$FTP = isset($req_row['ftp']) ? $req_row['ftp'] : '';
		$is_manual = isset($req_row['is_manual']) ? $req_row['is_manual'] : ''; 
		$product_type = isset($req_row['product_type']) ? $req_row['product_type'] : ''; 
		$prd_ids = isset($req_row['products']) ? $req_row['products'] : 0; 

		if(!empty($req_row['extra_params']) && $req_row['extra_params'] != "") {
			$extra_params = json_decode($req_row['extra_params'],true);
			if(is_array($extra_params) && !empty($extra_params)){
			$since_date = isset($extra_params['since_date']) ? $extra_params['since_date'] : '';
			$to_date = isset($extra_params['to_date']) ? $extra_params['to_date'] : '';
			}
		}

		$agent_ids_array = array();

		if($file_id != 0){
			if($product_type == "Sub Products") {
				include_once "eligibility_files/eligibility_files_generator/generate_sub_products_file.inc.php";
			}else if($product_type == "Participants Products"){
				if($file_key == "LOOMIS_MEMBER_FILE"){
					include_once "eligibility_files/eligibility_files_generator/generate_loomis_member_file.inc.php";
				}else if($file_key == "LOOMIS_DEPENDENT_FILE"){
					include_once "eligibility_files/eligibility_files_generator/generate_loomis_dependent_file.inc.php";
				}else if($file_key == "LOOMIS_PRODUCT_FILE"){
					include_once "eligibility_files/eligibility_files_generator/generate_loomis_product_file.inc.php";
				}
			} else {
				if($file_key == "HEALTHY_STEP_ACCESS"){
					include_once "eligibility_files/eligibility_files_generator/generate_eligibility_file_common.inc.php";

				}else if($file_key == "HEALTHY_STEP_AUGEO"){
					$file_format = "text";
					include_once "eligibility_files/eligibility_files_generator/generate_eligibility_file_common.inc.php";

				}else if($file_key == "ASH"){
					include_once "eligibility_files/eligibility_files_generator/generate_ash_file.inc.php";
					
				}else if($file_key == "THE_CAPTIVE_FILE"){
					include_once "eligibility_files/eligibility_files_generator/the_captive_file.inc.php";
				}else{
					include_once "eligibility_files/eligibility_files_generator/generate_eligibility_file.inc.php";
				}
			}
			
			$eligibility_file_uploads = $ELIGIBLITY_FILES_DIR."/";
			
			if (!file_exists($eligibility_file_uploads)) {
			    mkdir($eligibility_file_uploads, 0777, true);
			}
			if($file_type == "full_file"){
				$csv_filename = "TRI_" . $req_row['file_name'] . "_Full_File" . date("mdYHis", time()) .".csv";
				$text_filename = "TRI_" . $req_row['file_name'] . "_Full_File" . date("mdYHis", time()).".txt";
				$xls_filename = "TRI_" . $req_row['file_name'] . "_Full_File" . date("mdYHis", time()) .".xlsx";
				$error_filename = "TRI_" . $req_row['file_name'] . "_Full_File_Error_" . date("YmdHis", time()).".csv";
				$zip_filename = "TRI_" . $req_row['file_name'] . "_Full_File" . date("mdYHis", time()).".zip";
			}else{
				$csv_filename = "TRI_" . $req_row['file_name'] . "_ACT_File" . date("mdYHis", time()).".csv";
				$text_filename = "TRI_" . $req_row['file_name'] . "_ACT_File" . date("mdYHis", time()).".txt";
				$xls_filename = "TRI_" . $req_row['file_name'] . "_ACT_File" . date("mdYHis", time()).".xlsx";
				$error_filename = "TRI_" . $req_row['file_name'] . "_ACT_File_Error_" . date("mdYHis", time()).".csv";
				$zip_filename = "TRI_" . $req_row['file_name'] . "_ACT_File" . date("mdYHis", time()).".zip";
			}
			
		}

         	       
	if(!empty($content)){
			

			$filename = $csv_filename;
			$file_upload = file_put_contents($eligibility_file_uploads.''.$filename,$content);

			if($file_format == "text") {
				$filename = $text_filename;
				$content = $txt_content;
				$file_upload = file_put_contents($eligibility_file_uploads.''.$filename,$content);
				if(empty($content)) {
					$file_upload = 1;
				}
			}


			// insert in eligibility history table code start
	  		$field_row = csvToArraywithFields($eligibility_file_uploads.''.$csv_filename);
	        $records=count($field_row);
	      		$insertParams = array(
	                    'service_group_id' => $req_row['file_id'],
	                    'admin_id' => $req_row['user_id'],
	                    'file_name' => $filename,
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
        	$history_file_id = $pdo->insert("eligibility_history", $insertParams);
      // insert in eligibility history table code ends

       // insert in eligibility error history table code start
	        if(!empty($error_content)){
		       	$eligibility_error_file_uploads = $eligibility_file_uploads.'error_files/';
		        $file_upload = file_put_contents($eligibility_error_file_uploads.''.$error_filename,$error_content);
				// insert in eligibility history table code start
			  	$field_row = csvToArraywithFields($eligibility_error_file_uploads.''.$error_filename);
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
		        $pdo->insert("eligibility_error_files", $insertParams);
	        }
       // insert in eligibility history table code ends


			$is_sent  = false;
			if($req_row['generate_via'] == 'Email'){
				//$upload_zip  = file_put_contents($filename,$content);
				//$password = $req_row['file_password'];
				// system("zip -P $password $zip_filename $filename");
				// $is_sent = copy($eligibility_file_uploads.''.$zip_filename,$zip_filename);
				// $fileURL= $HOST.'/uploads/eligibility_files/generate_zip_files.php';
				// $data = array( 
				// 	'file_name' => $zip_filename, 
				// 	'file_password' => $password, 
				// 	'csv_file_name'=>$filename,
				// 	'eligibility_file_uploads'=>$eligibility_file_uploads,
					
				// );
			
				// $ch = curl_init($fileURL);
				// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				// curl_setopt($ch, CURLOPT_POST, true);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				// $data1 = curl_exec($ch);
				// curl_close($ch);

				// $responseApi = json_decode($data1,true);
				// $is_sent = $responseApi['is_sent'];
				// if($is_sent){
				$link = $HOST.'/eligibility_access.php?id='.md5($req_row['id']).'&fId='.md5($req_row['file_id']);

				// pre_print($link,false);
				$email = $req_row['email'];
				$trigger_id='92';
				// $trigger_id='10';
				$params = array();
				$params['link'] = $link;
				$params['FileName'] = $req_row['file_name'];
				
				// $attachments = array($ELIGIBILITY_FILES_WEB . $filename);
				trigger_mail($trigger_id, $params, $email);
				// }
				// unlink($filename);
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

				$success = $sftp->put('/outbound/' . $filename, 
                                $eligibility_file_uploads . $filename, 
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
						'processed_file_name' => $filename,
						'is_process_active' => "N",
						'status' => "Processed",
						'updated_at' => "msqlfunc_NOW()",
					);
					if($is_sent){
							$req_data['is_sent_to_email'] = 'Y';
							$req_data['zip_link'] = $zip_filename;
					}
					$pdo->update("eligibility_requests",$req_data,$req_where);
				//  update file process counter code start
				if($req_row['is_manual'] == 'N'){
					 	$sqlSchedule = "SELECT * FROM eligibility_schedule WHERE is_deleted='N' AND file_id=:file_id";
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
									$pdo->update("eligibility_schedule",$upd_data,$upd_where);

		
									//  next scheduled date code start
										$next_schedule_date = next_eligibility_schedule($schedule_id);
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
											$pdo->update("eligibility_files",$req_data,$req_where);
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
					$pdo->update("eligibility_files",$req_data,$req_where);
				}
				//  update file process counter code ends
			}
		}
	// end foreach loop
	}
}
echo "Completed";
dbConnectionClose();
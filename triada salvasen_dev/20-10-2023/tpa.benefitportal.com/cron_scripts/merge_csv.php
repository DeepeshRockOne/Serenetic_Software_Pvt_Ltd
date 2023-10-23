<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/cron_scripts/eligibility_file_functions.php";
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
global $S3_KEY,$S3_SECRET,$S3_REGION,$S3_BUCKET_NAME;

require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

$incr = '';
$sch_param = array();
if($SITE_ENV!='Live'){
	$id = isset($_GET['id']) ? $_GET['id']: '';
	if(!empty($id)){
		$incr = 'AND er.id=:id';
		$sch_param = array(":id"=>$id);
	}
}

$upload_file_url = $UPLOAD_DIR.'temp';

$exports = $pdo->selectOne("SELECT er.*,rs.report_key FROM $REPORT_DB.export_requests er LEFT JOIN $REPORT_DB.rps_reports rs on(er.report_id = rs.id) where er.attempts_over = 'Y' and er.status = 'Running' $incr order by er.id desc",$sch_param);
// pre_print($exports);
if($exports){

	$numbers = array(0=>'a',1=>'b',2=>'c',3=>'d',4=>'e',5=>'f',6=>'g',7=>'h',8=>'i',9=>'j',10=>'k',11=>'l',12=>'m',13=>'n',14=>'o',15=>'p',16=>'q',17=>'r',18=>'s',19=>'t',20=>'u',21=>'v',22=>'w',23=>'x',24=>'y',25=>'z');

	$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
			':id' => $exports['id']
		)
	);
	$pdo->update("$REPORT_DB.export_requests",array("file_merge_requested"=>'Y'),$update_where);
	$s3Client = new S3Client([
          'version' => 'latest',
          'region'  => $S3_REGION,
          'credentials'=>array(
              'key'=> $S3_KEY,
              'secret'=> $S3_SECRET
          )
      ]);
	$s3Client->registerStreamWrapper();
			
	if (!file_exists($upload_file_url)) {
	    mkdir($upload_file_url, 0777, true);
	}	

	$temp_file_uploads = $upload_file_url . '/';

	$files_name = "";
	
	if($exports['files_name']){
		$files_name = explode(',', $exports['files_name']);
		unset($files_name[0]);
	}

	if($files_name){
		$is_first_file = true;
		$file_name = $temp_file_uploads . $exports['filename'];
		foreach ($files_name as $file) {
			$result = $s3Client->getObject(array(
		    	'Bucket' => $S3_BUCKET_NAME,
		    	'Key'    => $file
		  	));

			$file_contents = htmlspecialchars_decode($result['Body']);

		  	if($file_contents){

		  		if($is_first_file){

		  			$temp_file_name = $temp_file_uploads.'temp_'.$exports['filename'];
				  	
					$csv = fopen($temp_file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);

					$summary_json = array();
					if($exports['summary_json']){
						$summary_json = json_decode($exports['summary_json'],true);
					}

					$data = array();
					$fp = fopen($file_name, 'w');

					if($exports['report_key'] == 'payables_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==4){
							    	$data[0] = str_replace('0',$summary_json['Advances'], $data[0]);
								}
								if($i==5){
							    	$data[0] = str_replace('0',$summary_json['Commissions'], $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('0',$summary_json['Fee Commission'], $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('0',$summary_json['Carriers'], $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('0',$summary_json['Memberships'], $data[0]);
								}
								if($i==9){
							    	$data[0] = str_replace('0',$summary_json['PMPMs'], $data[0]);
								}
								if($i==10){
							    	$data[0] = str_replace('0',$summary_json['Vendors'], $data[0]);
								}

								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'], array('agent_export','agent_summery_export'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_history'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_license'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_merchant_assignment'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_eo_coverage'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['export_location'] == '__agent_payables_listing'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==4){
							    	$data[0] = str_replace('0',displayAmount($summary_json['Advances']), $data[0]);
								}
								if($i==5){
							    	$data[0] = str_replace('0',displayAmount($summary_json['Commissions']), $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('0',displayAmount($summary_json['PMPMs']), $data[0]);
								}
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_payment_transaction_report'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_paymentApproved_',number_format($summary_json['Payment Approved'], 2, '.', ''), $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('_paymentDeclined_',number_format($summary_json['Payment Declined'], 2, '.', ''), $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('_reversals_',number_format($summary_json['Reversals'], 2, '.', ''), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('_pendingSettlement_',number_format($summary_json['Pending Settlement'], 2, '.', ''), $data[0]);
								}
								
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_quick_sales_summary'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==7){
							    	$data[0] = str_replace('_PaymentApprovedSales_',displayAmount($summary_json['PaymentApprovedSales']), $data[0]);
							    	$data[0] = str_replace('_PaymentApprovedCommissions_',displayAmount($summary_json['PaymentApprovedCommissions']), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('_PaymentDeclinedSales_',displayAmount($summary_json['PaymentDeclinedSales']), $data[0]);
							    	$data[0] = str_replace('_PaymentDeclinedCommissions_',displayAmount($summary_json['PaymentDeclinedCommissions']), $data[0]);
								}
								if($i==9){
							    	$data[0] = str_replace('_ReversalsSales_',displayAmount($summary_json['ReversalsSales']), $data[0]);
							    	$data[0] = str_replace('_ReversalsCommissions_',displayAmount($summary_json['ReversalsCommissions']), $data[0]);
								}
								if($i==10){
							    	$data[0] = str_replace('_PendingSettlementSales_',displayAmount($summary_json['PendingSettlementSales']), $data[0]);
							    	$data[0] = str_replace('_PendingSettlementCommissions_',displayAmount($summary_json['PendingSettlementCommissions']), $data[0]);
								}
								
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_age_out'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'top_performing_agency'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_summary'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_verifications'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_paid_through'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_age_out'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_history'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_product_cancellations'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'life_insurance_beneficiaries'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'agent_member_persistency'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'], array('admin_product_persistency','agent_product_persistency'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==1){

									for ($n=0; $n < 24; $n++) { 
										$data[0] = str_replace('active_p_'.$numbers[$n],$summary_json['current_active'][$n], $data[0]);
									}
								}
								if($i==2){
									for ($n=0; $n < 24; $n++) {
										$stayActive = number_format((($summary_json['current_active'][$n] * 100) / $summary_json['active_policy']),2);
								    	$data[0] = str_replace('stay_active_p_'.$numbers[$n],$stayActive.'%', $data[0]);
								    }
								}

								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_history'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'daily_order_summary'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_summary'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'],array('admin_new_business_post_payments_org','agent_new_business_post_payments'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_ProjectNewSales_',displayAmount($summary_json['RETAIL_PRICE']), $data[0]);
								}
								$data = str_replace('"', '', $data);
								
								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_summary_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_history'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_next_billing_date'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_history_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_payment_outstanding_renewals'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_RemainingRenewels_',displayAmount($summary_json['RETAIL_RATE']), $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('_AverageDailyCollection_',displayPercentage($summary_json['approved_renewal_trans']), $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('_ProjectedCollection_',displayAmount($summary_json['project_collection']), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('_CapturedRenewals_',displayAmount($summary_json['collected_renewal_sales_amt']), $data[0]);
								}
								if($i==9){
									$data[0] = str_replace('_ProjectedTotal_',displayAmount($summary_json['project_total']), $data[0]);
							    	
								}
								
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_member_age_out'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'platform_pmpm'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_totalPolicy_',number_format($summary_json['rep_id'], 2, '.', ''), $data[0]);
								}
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_member_age_out_gp'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_payment_failed_payment_recapture_analytics'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_numberofDeclines_',$summary_json['NumberOfDeclines'], $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('_paymentDeclined_',displayAmount($summary_json['payment Declines']), $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('_numberofRecaptures_',($summary_json['NumberOfRecaptures']), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('_paymentRecaptured_',displayAmount($summary_json['payment Recaptured']), $data[0]);
								}
								
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_change_product'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'admin_payment_reversal_transactions'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==5){
							    	$data[0] = str_replace('_chargebacks_',displayAmount($summary_json['Chargebacks']), $data[0]);
								}
								if($i==6){
							    	$data[0] = str_replace('_paymentReturned_',displayAmount($summary_json['PaymentReturned']), $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('_refunds_',displayAmount($summary_json['Refunds']), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('_voids_',displayAmount($summary_json['Voids']), $data[0]);
								}
								
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_change_product_gp'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'payment_policy_overview'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_enroll_overview'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_enroll_overview_gp'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_full_coverage'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_full_coverage_gp'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'list_bill_overview'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'payment_nb_sales'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'list_bill_overview_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'payment_rb_sales'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'],array('agent_p2p_comparison','admin_payment_p2p_renewal_comparison'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'],array('agent_debit_ledger','admin_agent_debit_ledger'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==4){
							    	$data[0] = str_replace('_DebitBalance_',displayAmount($summary_json['BALANCE']), $data[0]);
								}								
								if($i==5){
							    	$data[0] = str_replace('_PapyoffMultiplier_',$summary_json['PayoffMultiplier'], $data[0]);
								}						
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'vendor_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'trigger_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'provider_export'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'group_interactions'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if($exports['report_key'] == 'member_interactions'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else if(in_array($exports['report_key'],array('membership_overview','carrier_overview','vendor_overview'))){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 10000, ",")) !== false) {
								$data = str_replace('"', '', $data);
								fputs($fp, implode(',', $data)."\n");
							}
							fclose($handle);
						}
					}else{
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}
					fclose($fp);
					if(file_exists($temp_file_name)){
						unlink($temp_file_name);
					}

					$is_first_file = false;

		  		}else{
		  			$csv = fopen($file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);
		  		}
		  	}
		}

		$xls_file = str_replace('csv', 'xls', $exports['filename']);
		if(file_exists($file_name)){

			$s3Client->putObject([
	  			'Bucket' => $S3_BUCKET_NAME,
	  			'Key' => $xls_file,
	  			'SourceFile' => $file_name,
	  			'ACL' => 'public-read',
	  			'ContentType'   => 'application/vnd.ms-excel'
	  		]);

			unlink($file_name);
		}

		$updParams = array(
	  		'status'=>'Processed',
	  		'filename'=>$xls_file,
	  		'proceed_at' => 'msqlfunc_NOW()',
	  		'attempts' => 0,
	  	);
	  	$updWhere = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $exports['id'],
			),
		);
		
		$pdo->update("$REPORT_DB.export_requests",$updParams,$updWhere);

		if($exports['is_manual'] == 'N') {

	    	/*--- Activity Feed ---*/
	    	$user_row = array();
			$user_link = '';
			if(in_array($exports['user_type'], array('Admin','admin','admins'))){
				$user_row = $pdo->selectOne("SELECT id,fname,lname,display_id as rep_id from admin where id=:id",array(":id"=>$exports['user_id']));
				$user_link = $ADMIN_HOST.'/admin_profile.php?id='.md5($exports['user_id']);
			} else {
				$user_row = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where id=:id",array(":id"=>$exports['user_id']));
				
				if(in_array($exports['user_type'],array('customer','customers','Customer'))){
					$user_link = $ADMIN_HOST.'/members_details.php?id='.md5($exports['user_id']);
				
				} elseif(in_array($exports['user_type'],array('groups','group','Group'))){
					$user_link = $ADMIN_HOST.'/groups_details.php?id='.md5($exports['user_id']);
				
				} elseif(in_array($exports['user_type'],array('agent' ,'agents','Agent'))){
					$user_link = $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($exports['user_id']);
				}
			}
			if(!empty($user_row) && !empty($user_link)) {
				$desc = array();
			    $desc['ac_message'] = array(
			        'ac_red_1'=>array(
			            'href'=> $user_link,
			            'title'=> $user_row['rep_id'],
			        ),
					'ac_message_1' => ' schedule created report <span class="text-action">'.$exports['report_name'].'</span>',
				);
				$desc = json_encode($desc);
				//$changed_at = date("Y-m-d H:i",strtotime($req_row['process_datetime']));
				activity_feed(3,$exports['user_id'],$exports['user_type'],$exports['user_id'],$exports['user_type'],'Schedule Created Report.',"","",$desc,"","");
			}
	    	/*---/Activity Feed ---*/

		    if($exports['generate_via'] == 'Email') {
		    	$email_arr = explode(';',$exports['email']);
		    	if(!empty($email_arr)) {
		    		foreach ($email_arr as $key => $tmp_mail) {
		                if (!filter_var($tmp_mail, FILTER_VALIDATE_EMAIL)) {
		                	unset($email_arr[$key]);
						}
			    	}
					$link = $HOST.'/report_access.php?id='.md5($exports['id']).'&fId='.md5($exports['report_id']);
					$trigger_id = 108;
					$params = array();
					$params['link'] = $link;
					$params['ReportName'] = $exports['report_name'];
					$params['fname'] = (isset($user_row['fname'])?$user_row['fname']:'');
					if(!empty($email_arr)) {
						trigger_mail($trigger_id,$params,$email_arr);	
					}
		    	}
		    	

			} else if($exports['generate_via'] == 'FTP') { //Need to review

				//Connect and login to FTP Server
				/*$ftp_server = "34.218.29.172";
				$ftp_username = "egfiles";
				$ftp_userpass = "jaf39KhYn";

				$sftp = new Net_SFTP($ftp_server);
				if (!$sftp->login($ftp_username,$ftp_userpass)) {
				    exit('Login Failed');
				} else {
					echo "connected";
				}
				$success = $sftp->put('/outbound/' . $csv_filename,$eligibility_file_uploads . $csv_filename,NET_SFTP_LOCAL_FILE);*/
			}

			// update file process counter code start
			if($exports['is_manual'] == 'N') {
			 	$sqlSchedule = "SELECT * FROM $REPORT_DB.rps_reports_schedule WHERE id=:id";
			 	$resSchedule = $pdo->selectOne($sqlSchedule,array(":id"=>$exports['schedule_id']));
			 	if(!empty($resSchedule)) {
				 	$process_cnt = $resSchedule['process_cnt'] + 1;
			 		$schedule_id = $resSchedule['id'];
			 		$next_schedule_date = next_rps_reports_schedule($schedule_id);
			 		$upd_where = array(
						"clause"=>"id=:id",
						"params"=>array(
						  	":id"=>$schedule_id,
						)
					);
					$upd_data = array(
						'process_cnt' => $process_cnt,
						'last_processed' => "msqlfunc_NOW()",
						'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
					);
					$pdo->update("$REPORT_DB.rps_reports_schedule",$upd_data,$upd_where);
			 	}
			}
	    }
		
	}
}

$billing_exports = $pdo->selectOne("SELECT er.* FROM billing_requests er where er.attempts_over = 'Y' and er.status = 'Running' $incr order by er.id desc",$sch_param);
if($billing_exports){

	$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
			':id' => $billing_exports['id']
		)
	);
	$pdo->update("billing_requests",array("file_merge_requested"=>'Y'),$update_where);
	$s3Client = new S3Client([
          'version' => 'latest',
          'region'  => $S3_REGION,
          'credentials'=>array(
              'key'=> $S3_KEY,
              'secret'=> $S3_SECRET
          )
      ]);
	$s3Client->registerStreamWrapper();
			
	if (!file_exists($upload_file_url)) {
	    mkdir($upload_file_url, 0777, true);
	}	

	$temp_file_uploads = $upload_file_url . '/';

	$files_name = "";
	
	if($billing_exports['files_name']){
		$files_name = explode(',', $billing_exports['files_name']);
		unset($files_name[0]);
	}

	if($files_name){
		$is_first_file = true;
		$file_name = $temp_file_uploads . $billing_exports['processed_file_name'];
		foreach ($files_name as $file) {
			$result = $s3Client->getObject(array(
		    	'Bucket' => $S3_BUCKET_NAME,
		    	'Key'    => $file
		  	));

			$file_contents = htmlspecialchars_decode($result['Body']);

		  	if($file_contents){

		  		if($is_first_file){

		  			$temp_file_name = $temp_file_uploads.'temp_'.$billing_exports['processed_file_name'];
				  	
					$csv = fopen($temp_file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);

					$summary_json = array();
					if($billing_exports['summary_json']){
						$summary_json = json_decode($billing_exports['summary_json'],true);
					}

					$data = array();
					$fp = fopen($file_name, 'w');

					if(1){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {
								if($i==6){
							    	$data[0] = str_replace('0',displayAmount($summary_json['TotalCredits']), $data[0]);
								}
								if($i==7){
							    	$data[0] = str_replace('0',displayAmount($summary_json['TotalDebits']), $data[0]);
								}
								if($i==8){
							    	$data[0] = str_replace('0',displayAmount($summary_json['TotalPayment']), $data[0]);
								}

								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else{
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}
					fclose($fp);
					if(file_exists($temp_file_name)){
						unlink($temp_file_name);
					}

					$is_first_file = false;

		  		}else{
		  			$csv = fopen($file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);
		  		}
		  	}
		}

		$xls_file = str_replace('csv', 'xls', $billing_exports['processed_file_name']);
		if(file_exists($file_name)){

			$s3Client->putObject([
	  			'Bucket' => $S3_BUCKET_NAME,
	  			'Key' => $xls_file,
	  			'SourceFile' => $file_name,
	  			'ACL' => 'public-read',
	  			'ContentType'   => 'application/vnd.ms-excel'
	  		]);

			unlink($file_name);
		}

		$updParams = array(
	  		'status'=>'Processed',
	  		'processed_file_name'=>$xls_file,
	  		'summary_json' => '',
	  		'from_limit' => 0,
	  	);
	  	$updWhere = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $billing_exports['id'],
			),
		);
		
		$pdo->update("billing_requests",$updParams,$updWhere);
	}
}

$eligibility_exports = $pdo->selectOne("SELECT er.*,ef.file_key FROM eligibility_requests er JOIN eligibility_files ef ON(er.file_id = ef.id) where er.attempts_over = 'Y' and er.status = 'Running' $incr order by er.id desc",$sch_param);
if($eligibility_exports){
	
	$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
			':id' => $eligibility_exports['id']
		)
	);
	$pdo->update("eligibility_requests",array("file_merge_requested"=>'Y'),$update_where);
	$s3Client = new S3Client([
          'version' => 'latest',
          'region'  => $S3_REGION,
          'credentials'=>array(
              'key'=> $S3_KEY,
              'secret'=> $S3_SECRET
          )
      ]);
	$s3Client->registerStreamWrapper();
			
	if (!file_exists($upload_file_url)) {
	    mkdir($upload_file_url, 0777, true);
	}	

	$temp_file_uploads = $upload_file_url . '/';

	$files_name = "";
	
	if($eligibility_exports['files_name']){
		$files_name = explode(',', $eligibility_exports['files_name']);
		unset($files_name[0]);
	}

	if($files_name){
		$is_first_file = true;
		$file_name = $temp_file_uploads . $eligibility_exports['processed_file_name'];
		foreach ($files_name as $file) {
			$result = $s3Client->getObject(array(
		    	'Bucket' => $S3_BUCKET_NAME,
		    	'Key'    => $ELIGIBILITY_FILES_PATH.$file
		  	));

			//$file_contents = htmlspecialchars_decode($result['Body']);
			$file_contents = $result['Body'];
			// pre_print($file_contents,false);
			// pre_print($file,false);
			// continue;

		  	if($file_contents){

		  		if($is_first_file){

		  			$temp_file_name = $temp_file_uploads.'temp_'.$eligibility_exports['processed_file_name'];
				  	
					$csv = fopen($temp_file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);

					$summary_json = array();
					if($eligibility_exports['summary_json']){
						$summary_json = json_decode($eligibility_exports['summary_json'],true);
					}

					$data = array();
					$fp = fopen($file_name, 'w');

					if($eligibility_exports['file_key'] == 'HEALTHY_STEP_AUGEO'){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								//$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else{
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								//$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}
					fclose($fp);
					if(file_exists($temp_file_name)){
						unlink($temp_file_name);
					}

					$is_first_file = false;

		  		}else{
		  			$csv = fopen($file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);
		  		}
		  	}
		}

		/*$xls_file = str_replace('csv', 'xls', $eligibility_exports['processed_file_name']);*/
		$xls_file = $eligibility_exports['processed_file_name'];
		$ContentType = 'text/csv';
		if(in_array($eligibility_exports['file_key'],array('HEALTHY_STEP_AUGEO','DAVIS_FILE'))){
			$xls_file = str_replace('csv', 'txt', $eligibility_exports['processed_file_name']);
			$ContentType = 'text/plain';
		}

		$total_rows = 0;
		if(file_exists($file_name)){

			$s3Client->putObject([
	  			'Bucket' => $S3_BUCKET_NAME,
	  			'Key' => $ELIGIBILITY_FILES_PATH.$xls_file,
	  			'SourceFile' => $file_name,
	  			'ACL' => 'public-read',
	  			'ContentType'   => $ContentType
	  		]);

	  		$field_row = csvToArraywithFields($file_name);
		    $total_rows = count($field_row);	
			unlink($file_name);
		}

		$updParams = array(
	  		'status'=>'Processed',
	  		'processed_file_name'=> $xls_file,
	  		'summary_json' => '',
	  		'from_limit' => 0,
	  	);
	  	$updWhere = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $eligibility_exports['id'],
			),
		);
		
		$pdo->update("eligibility_requests",$updParams,$updWhere);

		$updParams = array(
	  		'service_group_id'=>$eligibility_exports['file_id'],
	  		'records'=>$total_rows,
	  	);
	  	$updWhere = array(
			'clause' => 'req_id = :req_id',
			'params' => array(
				':req_id' => $eligibility_exports['id'],
			),
		);		
		$pdo->update("eligibility_history",$updParams,$updWhere);
	}
}

$fulfillment_exports = $pdo->selectOne("SELECT er.* FROM fulfillment_requests er JOIN fulfillment_files ef ON(er.file_id = ef.id) where er.attempts_over = 'Y' and er.status = 'Running' $incr order by er.id desc",$sch_param);

if($fulfillment_exports){

	$update_where = array(
		'clause' => 'id=:id',
		'params' => array(
			':id' => $fulfillment_exports['id']
		)
	);
	$pdo->update("fulfillment_requests",array("file_merge_requested"=>'Y'),$update_where);
	$s3Client = new S3Client([
          'version' => 'latest',
          'region'  => $S3_REGION,
          'credentials'=>array(
              'key'=> $S3_KEY,
              'secret'=> $S3_SECRET
          )
      ]);
	$s3Client->registerStreamWrapper();
			
	if (!file_exists($upload_file_url)) {
	    mkdir($upload_file_url, 0777, true);
	}	

	$temp_file_uploads = $upload_file_url . '/';

	$files_name = "";
	
	if($fulfillment_exports['files_name']){
		$files_name = explode(',', $fulfillment_exports['files_name']);
		unset($files_name[0]);
	}

	if($files_name){
		$is_first_file = true;
		$file_name = $temp_file_uploads . $fulfillment_exports['processed_file_name'];
		foreach ($files_name as $file) {
			$result = $s3Client->getObject(array(
		    	'Bucket' => $S3_BUCKET_NAME,
		    	'Key'    => $file
		  	));

			$file_contents = htmlspecialchars_decode($result['Body']);
			// pre_print($file_contents,false);
			// pre_print($file,false);
			// continue;

		  	if($file_contents){

		  		if($is_first_file){

		  			$temp_file_name = $temp_file_uploads.'temp_'.$fulfillment_exports['processed_file_name'];
				  	
					$csv = fopen($temp_file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);

					$summary_json = array();
					if($fulfillment_exports['summary_json']){
						$summary_json = json_decode($fulfillment_exports['summary_json'],true);
					}

					$data = array();
					$fp = fopen($file_name, 'w');

					if(1){
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}else{
						if (($handle = fopen($temp_file_name, "r")) !== FALSE) {
							$i=0;
							while (($data = fgetcsv($handle)) !== false) {		
								$data = str_replace('"', '', $data);

								fputs($fp, implode(',', $data)."\n");
							    $i++;
							}
							fclose($handle);
						}
					}
					fclose($fp);
					if(file_exists($temp_file_name)){
						unlink($temp_file_name);
					}

					$is_first_file = false;

		  		}else{
		  			$csv = fopen($file_name, 'a');

					if ($csv === false) {
						throw new Exception('Could not create file wrapper');
					}
					if (fwrite($csv, $file_contents,strlen($file_contents)) === false) {
						throw new Exception('Could not write contents to file wrapper');
					}
					fclose($csv);
		  		}
		  	}
		}

		$xls_file = str_replace('csv', 'xls', $fulfillment_exports['processed_file_name']);
		$ContentType = 'application/vnd.ms-excel';

		if(file_exists($file_name)){

			$s3Client->putObject([
	  			'Bucket' => $S3_BUCKET_NAME,
	  			'Key' => $xls_file,
	  			'SourceFile' => $file_name,
	  			'ACL' => 'public-read',
	  			'ContentType'   => $ContentType
	  		]);

			unlink($file_name);
		}

		$updParams = array(
	  		'status'=>'Processed',
	  		'processed_file_name'=> $xls_file,
	  		'summary_json' => '',
	  		'from_limit' => 0,
	  	);
	  	$updWhere = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $fulfillment_exports['id'],
			),
		);
		
		$pdo->update("fulfillment_requests",$updParams,$updWhere);
	}
}
echo "completed";
dbConnectionClose();
?>
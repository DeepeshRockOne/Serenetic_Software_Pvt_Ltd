<?php 
	include_once __DIR__ . '/includes/connect.php'; 
	
	$list_bill_id = $_POST['list_bill'];

	if(!empty($list_bill_id)){
		$sqlListBill = "SELECT lb.*,c.business_name as group_name,c.address,c.city,c.state,c.zip
						FROM list_bills lb 
						JOIN customer c ON(c.id = lb.customer_id)
						WHERE md5(lb.id)=:id";
		$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

		if($resListBill){
			$group_id = $resListBill['customer_id'];
			$group_name = $resListBill['group_name'];
			$group_company_id = $resListBill['company_id'];
			
			$credits_applied = $resListBill['credits_applied'];
			$list_bill_adjustment = $resListBill['adjustment'];

			$address = $resListBill['address'];
			$city = $resListBill['city'];
			$state = $resListBill['state'];
			$zip = $resListBill['zip'];

			$invoice_no = $resListBill['list_bill_no'];
			$invoice_date = !empty($resListBill['list_bill_date']) ? date('F d, Y',strtotime($resListBill['list_bill_date'])) : '-';
			
			$due_date = !empty($resListBill['due_date']) ? date('F d, Y',strtotime($resListBill['due_date'])) : '-';

			$invoice_total = !empty($resListBill['grand_total']) ? displayAmount($resListBill['grand_total'],2) : displayAmount(0, 2);

		 	$group_cmp_res = $pdo->select("SELECT id,name,location FROM group_company WHERE group_id = :group_id AND is_deleted = 'N'", array(":group_id" => $group_id));

			if(!empty($group_name)){
			    $group_company_res[0]['id'] = 0;
			    $group_company_res[0]['name'] = $group_name;
			    $group_company_res[0]['location'] = '';
			}

		  	if(!empty($group_cmp_res)){
			    foreach ($group_cmp_res as $key => $value) {
			      $group_company_res[$key+1] = $value;        
			    }
		  	}

		  	//********************** Dynamic Display List Bill Header Code Start **********************
				$total_arr = array();
				$prdCatSql="SELECT c.title,c.id
				      FROM prd_category c
				      JOIN prd_main p ON p.category_id=c.id
				      JOIN agent_product_rule ap ON ap.product_id=p.id
				      WHERE ap.agent_id=:id AND ap.is_deleted='N' GROUP BY c.id ORDER BY c.order_by";
				$prdCatRows=$pdo->select($prdCatSql,array(":id"=>$group_id));

				$product_total = count($prdCatRows);
				$product_arr = array();
				$categoryArr = array();
				$title_arr = array('ID','Name');

				if(!empty($prdCatRows)) {
				    foreach ($prdCatRows as $key => $value) {
				        array_push($title_arr, $value['title']);
				        array_push($product_arr, $value['id']);
				        $categoryArr[$value['title']] = $value['id'];
				    }
				}
				array_push($title_arr, 'Fee');
		        array_push($product_arr, '0');
		        $categoryArr['Fee'] = '0';
				array_push($title_arr, 'Total');
			//********************** Dynamic Display List Bill Header Code End   **********************
		}
	}
	//********************** Display Amendment Summary Code Start **********************
		$incr="";
		$rep_id = isset($_POST['rep_id']) ? $_POST['rep_id'] : array();

		if (!empty($rep_id)) {
	        $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
	        $incr .= " AND c.rep_id IN ($rep_id)";
	    }

		$sqlAmendment = "SELECT lba.*,c.id as cust_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.rep_id,lbd.transaction_type 
						FROM list_bill_amendment lba 
						JOIN list_bill_details lbd ON FIND_IN_SET(lbd.id,lba.list_bill_details_id)
						JOIN customer c ON(c.id = lba.customer_id) 
						WHERE lba.is_deleted='N' AND md5(lba.list_bill_id) = :id $incr GROUP BY lba.id";
		$resAmendment = $pdo->select($sqlAmendment,array(":id"=>$list_bill_id));

		$amendment_items_res = array();
		if(!empty($resAmendment)){
			foreach ($resAmendment as $key => $amendment) {
				
				$customer_id = $amendment['customer_id'];
				$category_id = $amendment['category_id'];
				$customer_name = $amendment['customer_name'];
				$rep_id = $amendment['rep_id'];
				$start_coverage_date = $amendment['start_coverage_date'];
			    $end_coverage_date = $amendment['end_coverage_date'];
			    $total = $amendment['amount'];
			    $transaction_type = $amendment['transaction_type'];

			    if(empty($amendment_items_res[$start_coverage_date][$customer_id]['total'])){
		            $amendment_items_res[$start_coverage_date][$customer_id]['total'] = 0;
		        }
	        	$amendment_items_res[$start_coverage_date][$customer_id]['customer_name'] = $customer_name;
		        $amendment_items_res[$start_coverage_date][$customer_id]['rep_id'] = $rep_id;
		        $amendment_items_res[$start_coverage_date][$customer_id]['start_coverage_period'] = $start_coverage_date;
			    $amendment_items_res[$start_coverage_date][$customer_id]['end_coverage_period'] = $end_coverage_date;
			    $amendment_items_res[$start_coverage_date][$customer_id]['cust_id'] = $customer_id;

			    $amendment_items_res[$start_coverage_date][$customer_id]['total'] +=  $total;
			    $amendment_items_res[$start_coverage_date][$customer_id]['transaction_type'] =  $transaction_type;

			    if(empty($amendment_items_res[$start_coverage_date][$customer_id][$category_id]['category_total'])){
		            $amendment_items_res[$start_coverage_date][$customer_id][$category_id]['category_total'] = 0;
			    }
		        $amendment_items_res[$start_coverage_date][$customer_id][$category_id]['category_total'] += $total;
			}
		}
	//********************** Display Amendment Summary Code End   **********************
	ob_start();
	?>
	<?php if(!empty($amendment_items_res)) { ?>
		<div class="invoice_bottom_wrap" style="background-color:#f5f5f5; padding: 30px;">
   			<p style="font-weight: bold;">Amendment Summary</p>
	     	<?php foreach ($amendment_items_res as $start_coverage_date => $ListBillData) { ?>
	            <p class="m-t-20" style="font-size: 14px; margin-bottom: 15px;">Coverage Period: <span style="color: #5694cc;"><b><?= date($DATE_FORMAT,strtotime($start_coverage_date)) .' - '. date($DATE_FORMAT, strtotime(date("Y-m-t",strtotime($start_coverage_date)))) ?> </b></span></p>

	            <div class="table-responsive" style="width: 100%">
	               	<table style="font-size: 12px;" cellpadding="0" cellspacing="0" width="100%">
	                  	<thead>
	                     	<tr>
		                        <?php if(!empty($title_arr)) {
	                              	foreach ($title_arr as $key => $value) { ?>
	                                 	<th style="background-color:#050606; padding: 10px; font-weight: bold; color: #fff;">
		                                    <?=$value?>    
	                                 	</th>
		                           	<?php } ?>
		                        <?php } ?>
	                     	</tr>
	                  	</thead>
	                  	<tbody>
                     		<?php if(!empty($ListBillData)) { ?>
	                        	<?php $row_count=0; ?>
		                        <?php foreach ($ListBillData as $customerID => $detail) { ?>
	                           		<?php if(!empty($detail) && !empty($detail['cust_id']) && !empty($detail['customer_name'])){ ?>
	                           			<?php $totalChecked = array(); ?>
	                              		<tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f1f1f1;' : '' ?>">
		                                	<td style="padding: 5px 10px;"><?= $detail['rep_id'] ?></td>
		                                 	<td style="padding: 5px 10px;"><?= $detail['customer_name'] ?></td>
		                                 	<?php if(!empty($product_arr)) { ?>
		                                    	<?php foreach ($product_arr as $titel_key => $category_id) { ?>
		                                          	<?php $tmpTotal = isset($detail[$category_id]['category_total']) ? ($detail[$category_id]['category_total']) : '0'; ?>
		                                       		<td class="<?= ($tmpTotal <= 0) ? '' : 'text-action' ?>" style="padding: 5px 10px;">
		                                       			<?php if($tmpTotal <= 0) { ?>
		                                             		<?= displayAmount(abs($tmpTotal),2)?>
		                                       			<?php }else{ ?>
		                                       				(<?= displayAmount(abs($tmpTotal),2)?>)
		                                       			<?php } ?>
		                                       		</td>
		                                    	<?php } ?>
		                                 	<?php } ?>
		                                 	<td class="<?= ($detail['total'] <= 0) ? '' : 'text-action' ?>" style="padding: 5px 10px; font-weight: bold; ">
		                                 		<?php if($detail['total'] <= 0) { ?>
	                                         		<?= displayAmount(abs($detail['total']),2)?>
	                                   			<?php }else{ ?>
	                                   				(<?= displayAmount(abs($detail['total']),2)?>)
	                                   			<?php } ?>
		                                 	</td>
		                              	</tr>
		                           	<?php } ?>
		                        <?php } ?>
	                     	<?php }else{ ?>
	                     	<?php } ?>
	                  	</tbody>
	               	</table>
	            </div>
	     	<?php } ?>
   		</div>
	<?php } ?>
	<?php
	$result = array();	
	$result['html'] = ob_get_clean();
	$result['status'] = "success"; 
  
	header('Content-type: application/json');
	echo json_encode($result); 
	dbConnectionClose();
	exit;
?>
<?php 
	include_once __DIR__ . '/includes/connect.php'; 
	
	$list_bill_id = $_POST['list_bill'];

	$group_company_res = array();

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
				array_push($title_arr, 'Admin Fee');
		        array_push($product_arr, '0');
		        $categoryArr['Fee'] = '0';
				array_push($title_arr, 'Total');
			//********************** Dynamic Display List Bill Header Code End   **********************
		}
	}


	$list_bill_items_res = array();


	//********************** Display List Bill Detail Code Start **********************
		$incr="";
		$rep_id = isset($_POST['rep_id']) ? $_POST['rep_id'] : array();

		if (!empty($rep_id)) {
	        $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
	        $incr .= " AND c.rep_id IN ($rep_id)";
	    }
		$list_bill_items_sql = "SELECT l.*,c.id as cust_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.rep_id,w.member_price,w.group_price,p.category_id,l.transaction_type
		      FROM list_bill_details l 
		      JOIN customer c ON(c.id = l.customer_id) 
		      JOIN website_subscriptions as w ON(c.id=w.customer_id AND w.id = l.ws_id) 
		      JOIN prd_main as p ON (l.product_id = p.id)
		      WHERE md5(l.list_bill_id) = :list_bill_id $incr ORDER BY c.fname ASC";
		$list_bill_items_where = array(':list_bill_id' => makeSafe($list_bill_id));
		$list_bill_res = $pdo->select($list_bill_items_sql,$list_bill_items_where);

		if(!empty($list_bill_res)){
		    foreach ($list_bill_res as $key => $value) {
		    	$is_cobra_coverage = $value['is_cobra_coverage'];
		    	$start_coverage_date = $value['start_coverage_date'];
		    	$end_coverage_date = $value['end_coverage_date'];
		    	$customer_id = $value['cust_id'];
		    	$transaction_type = $value['transaction_type'];
		    	$category_id = $value['category_id'];
		    	$customer_name = $value['customer_name'];
		    	
		    	$rep_id = $value['rep_id'];
		    	$total = $value['amount'];
		    	$enrolleePremium = $value['member_price'];
		    	$enrollerPermium = $value['group_price'];
		    	$website_id = $value['ws_id'];
		    	$list_bill_detail_id = $value['id'];
		    	
	      		
	      		
		        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['employee_total'])){
		            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['employee_total'] = 0;
		        }
		        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['group_employer_total'])){
		            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['group_employer_total'] = 0;
		        }
		        
		        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['total'])){
		            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['total'] = 0;
		        }
		        

	        	$list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['customer_name'] = $customer_name;

	        	$group_company_name = $group_company_res[0]['name'];
		        if(!empty($group_company_id)){
		          $found_key = array_search($group_company_id, array_column($group_company_res, 'id'));
		          $group_company_name = $group_company_res[$found_key]['name'];
		        }

		        if($transaction_type == 'refund'){
		        	$total = -1 * $total;
		        }
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['gp_company_name'] = $group_company_name;

		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['rep_id'] = $rep_id;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['start_coverage_period'] = $start_coverage_date;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['end_coverage_period'] = $end_coverage_date;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['cust_id'] = $customer_id;

		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['total'] +=  $total;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['employee_total'] +=  $enrolleePremium;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['group_employer_total'] +=  $enrollerPermium;
		        
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id]['transaction_type'] = $transaction_type;
		        

		        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['category_total'])){
		            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['category_total'] = 0;
		        }
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['category_total'] += $total;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['ws_ids'] = $website_id;
		        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['transaction_type'] = $transaction_type;
		        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['list_bill_details'])){
		        	$list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['list_bill_details'] = array();
		        }
		        array_push($list_bill_items_res[$is_cobra_coverage][$start_coverage_date][$customer_id][$category_id]['list_bill_details'], $list_bill_detail_id);
		    }
	  	}
	//********************** Display List Bill Detail Code End   **********************

	$sqlAmendment = "SELECT lba.* 
					FROM list_bill_amendment lba 
					WHERE lba.is_deleted='N' AND md5(lba.list_bill_id) = :id";
	$resAmendment = $pdo->select($sqlAmendment,array(":id"=>$list_bill_id));

	$amendmentArr = array();
	$amendment_items_res = array();
	if(!empty($resAmendment)){
		foreach ($resAmendment as $key => $amendment) {
			$customer_id = $amendment['customer_id'];
			$category_id = $amendment['category_id'];
			$start_coverage_date = $amendment['start_coverage_date'];
		    $tmpDate = strtotime($start_coverage_date);
			$amendmentArr[$tmpDate][$customer_id][$category_id]['id']=$amendment['id'];
		}
	}
	ob_start();
	?>
	<?php if(!empty($list_bill_items_res)) { ?>
      	<?php foreach ($list_bill_items_res as $is_cobra_coverage => $coverageArray) { ?>
         	<?php foreach ($coverageArray as $start_coverage_date => $ListBillData) { ?>
	            <?php  $encrDate = strtotime($start_coverage_date); ?>
	            <p class="m-t-20" style="font-size: 14px; margin-bottom: 15px;">Coverage Period: <span style="color: #5694cc;"><b><?= date($DATE_FORMAT,strtotime($start_coverage_date)) .' - '. date($DATE_FORMAT, strtotime(date("Y-m-t",strtotime($start_coverage_date)))) ?> </b></span></p>

	            <div class="table-responsive">
	               	<table class="<?=$table_class?>">
	                  	<thead>
	                     	<tr>
		                        <?php if(!empty($title_arr)) {
	                              	foreach ($title_arr as $key => $value) { ?>
	                                 	<th>
		                                    <?php if(in_array($value,array('ID','Name'))){ ?>
		                                       <?=$value?>    
		                                    <?php }else{ ?>
		                                       <?php $tmpKey = isset($categoryArr[$value]) ? $categoryArr[$value]  : ''; ?>
		                                       <div class="checkbox checkbox-custom mn">
		                                          <input type="checkbox" class="js-switch global_row_check global_row_check_<?= $encrDate ?> global_row_check_<?= $encrDate ?>_<?= $tmpKey ?>" data-date="<?= $encrDate ?>" value="<?= $key ?>" data-category = "<?= $tmpKey ?>"/>
		                                          <label for="select_dental_all"><?=$value?></label>
		                                       </div>
		                                    <?php } ?>
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
	                              		<tr>
		                                	<td><?= $detail['rep_id'] ?></td>
		                                 	<td><?= $detail['customer_name'] ?></td>
		                                 	<?php if(!empty($product_arr)) { ?>
		                                    	<?php foreach ($product_arr as $titel_key => $category_id) { ?>
		                                    		<?php 
		                                    			$tmpCatType = isset($detail[$category_id]['transaction_type']) ? ($detail[$category_id]['transaction_type']) : ''; 
		                                    			$tmpTotal = isset($detail[$category_id]['category_total']) ? ($detail[$category_id]['category_total']) : '0'; 

		                                    			$tmpDetailID = isset($detail[$category_id]['list_bill_details']) ? implode(",", $detail[$category_id]['list_bill_details']) : ''; 

		                                    			$tmpChecked = isset($amendmentArr[$encrDate][$detail['cust_id']][$category_id]) ? 'true' : 'false'; 
	                                          			array_push($totalChecked, $tmpChecked);
	                                          		?>
		                                       		<td class="<?= $tmpTotal < '0' ? 'text-danger' : '' ?>" style="padding: 5px 10px;">
		                                          		
		                                          		<div class="checkbox checkbox-custom mn">
		                                             		<input type="hidden" name="list_bill_ids[<?= $encrDate ?>][<?= $detail['cust_id'] ?>][<?= $category_id ?>]" value="<?= $tmpDetailID  ?>">
		                                             		<input type="hidden" name="amendment[<?= $encrDate ?>][<?= $detail['cust_id'] ?>][<?= $category_id ?>]" value="<?= (isset($amendmentArr[$encrDate][$detail['cust_id']][$category_id])) ? $amendmentArr[$encrDate][$detail['cust_id']][$category_id]['id'] : ''  ?>">

		                                             		<input type="checkbox" class="js-switch row_check row_check_<?= $encrDate ?>_<?= $category_id ?> row_check_cust_<?= $encrDate ?>_<?= $detail['cust_id'] ?>" data-date="<?= $encrDate ?>" data-category = "<?= $category_id ?>" data-customer-id="<?= $detail['cust_id'] ?>" value = "<?= $tmpTotal ?>" name="category[<?= $encrDate ?>][<?= $detail['cust_id'] ?>][<?= $category_id ?>]" <?= $tmpChecked == 'true' ? 'checked' : '' ?> />
		                                             		<label for="row_check_<?= $encrDate ?>_<?= $key ?>" style="<?= $tmpChecked == 'true' ? 'text-decoration: line-through;' : '' ?>"><?= displayAmount(abs($tmpTotal),2)?></label>
		                                          		</div>
		                                       		</td>
		                                    	<?php } ?>
		                                 	<?php } ?>
		                                 	<td class="<?= $detail['total'] < 0 ? 'text-danger' : '' ?>" style="padding: 5px 10px; font-weight: bold; ">
		                                    	<div class="checkbox checkbox-custom mn">
		                                       		<input type="checkbox" class="js-switch row_check row_check_<?= $encrDate ?>_ row_check_cust_<?= $encrDate ?>_<?= $detail['cust_id'] ?>" data-date="<?= $encrDate ?>" data-customer-id="<?= $detail['cust_id'] ?>" data-category = "" value = "<?= $detail['transaction_type'] == 'refund' ? -1 * $detail['total']  : $detail['total'] ?>" <?= !in_array("false", $totalChecked) ? 'checked' : '' ?>/>
		                                       		<label for="total_1" style="<?= !in_array("false", $totalChecked) ? 'text-decoration: line-through;' : '' ?> font-weight: bold;"><?= displayAmount(abs($detail['total']), 2); ?></label>
		                                    	</div>
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
      	<?php } ?>
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
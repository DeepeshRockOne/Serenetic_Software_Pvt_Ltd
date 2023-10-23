<?php
include_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/list_bill.class.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'view_listbill_statement.php';
$breadcrumbes[1]['title'] = 'Billing';
$ListBill = new ListBill();
$list_bill_id = $_GET['list_bill'];

$group_company_res = array();
$past_due_amount = 0;
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
		$admin_fee = $resListBill['admin_fee'];
		$past_due_amount = $resListBill['past_due_amount'];
		$credits_applied = $resListBill['credits_applied'];
		$list_bill_adjustment = $resListBill['adjustment'];

		$address = $resListBill['address'];
		$city = $resListBill['city'];
		$state = $resListBill['state'];
		$zip = $resListBill['zip'];

		$startCoverage = $resListBill['time_period_start_date'];
		$endCoverage = $resListBill['time_period_end_date'];
		$invoice_no = $resListBill['list_bill_no'];
		$invoice_date = !empty($resListBill['list_bill_date']) ? date('F d, Y',strtotime($resListBill['list_bill_date'])) : '-';
		
		$due_date = !empty($resListBill['due_date']) ? date('F d, Y',strtotime($resListBill['due_date'])) : '-';

		$csv_invoice_date = !empty($resListBill['list_bill_date']) ? date('m/d/Y',strtotime($resListBill['list_bill_date'])) : '-';
		
		$csv_due_date = !empty($resListBill['due_date']) ? date('m/d/Y',strtotime($resListBill['due_date'])) : '-';

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

			$title_arr = array('Member', 'Company/Location');

			if(!empty($prdCatRows)) {
			    foreach ($prdCatRows as $key => $value) {
			        array_push($title_arr, $value['title']);
			        array_push($product_arr, $value['id']);
			    }
			}
			array_push($title_arr, 'Admin Fee');
	        array_push($product_arr, '0');
			array_push($title_arr, 'Enrollee Premium');
			array_push($title_arr, 'Group Premium');
			array_push($title_arr, 'Total');
		//********************** Dynamic Display List Bill Header Code End   **********************
	}
}


$coverage_dates = array();
$cobra_coverage_dates = array();
$flag_array = array();
$list_bill_items_res = array();
$global_coverage_total = array(); 
$global_total = 0; 
$cobra_total = 0;
//********************** Code Start **********************
//********************** Code End   **********************

//********************** Display List Bill Detail Code Start **********************

	$list_bill_items_sql = "SELECT l.*,c.id as cust_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.rep_id,w.member_price,w.group_price,p.category_id,l.transaction_type,gc.pay_period
	      FROM list_bill_details l 
	      JOIN customer c ON(c.id = l.customer_id) 
	      JOIN website_subscriptions as w ON(c.id=w.customer_id AND w.id = l.ws_id) 
	      JOIN prd_main as p ON (l.product_id = p.id)
		  LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
		  LEFT JOIN group_classes gc ON(cs.class_id=gc.id)
	      WHERE md5(l.list_bill_id) = :list_bill_id ORDER BY l.start_coverage_date,l.id,c.fname,w.id ASC";
	$list_bill_items_where = array(':list_bill_id' => makeSafe($list_bill_id));
	$list_bill_res = $pdo->select($list_bill_items_sql,$list_bill_items_where);

	if(!empty($list_bill_res)){
	    foreach ($list_bill_res as $key => $value) {
      		if(!in_array($value['is_cobra_coverage'], $flag_array)){
	        	array_push($flag_array, $value['is_cobra_coverage']);
	      	}
	      	if($value['is_cobra_coverage'] == 'Y'){
	        	if(!in_array($value['start_coverage_date'], $cobra_coverage_dates)){
	          		array_push($cobra_coverage_dates, $value['start_coverage_date']);
	        	}
	      	} else {
	        	if(!in_array($value['start_coverage_date'], $coverage_dates)){
	          		array_push($coverage_dates, $value['start_coverage_date']);
	        	}
	      	}
	    }
  	}

  	if(!empty($flag_array)){
    	asort($flag_array);
    	foreach ($flag_array as $key => $is_cobra_coverage) {
      		if($is_cobra_coverage == 'Y'){
        		if(!empty(($cobra_coverage_dates))){
          			arsort($cobra_coverage_dates);
          			foreach ($cobra_coverage_dates as $key => $start_coverage_date) {
            			//$list_bill_items_res[$is_cobra_coverage][$start_coverage_date] = array();
          			}
        		}
      		} else {
        		if(!empty($coverage_dates)){
          			arsort($coverage_dates);
          			foreach ($coverage_dates as $key => $start_coverage_date) {
            			//$list_bill_items_res[$is_cobra_coverage][$start_coverage_date] = array();
          			}
        		}
      		}
    	}
  	}

	if(!empty($list_bill_res)){
	    foreach ($list_bill_res as $key => $value) {
	    	$index = 0;
	    	$is_cobra_coverage = $value['is_cobra_coverage'];
	    	$start_coverage_date = $value['start_coverage_date'];
	    	$end_coverage_date = $value['end_coverage_date'];
	    	$customer_id = $value['cust_id'];
	    	$transaction_type = $value['transaction_type'];
	    	$category_id = $value['category_id'];
	    	$customer_name = $value['customer_name'];
	    	
	    	$rep_id = $value['rep_id'];
	    	$total = $value['amount'];
	    	$enrolleePremium = $ListBill->get_plan_pay_period_price($value['member_price'],$value['pay_period']);
	    	$enrollerPermium = $value['group_price'];
	    	$website_id = $value['ws_id'];

      		if(isset($list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['category_total'])) {
      			$index++;

      		}
	        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['employee_total'])){
	            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['employee_total'] = 0;
	        }
	        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['group_employer_total'])){
	            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['group_employer_total'] = 0;
	        }
	        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['category_total'])){
	            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['category_total'] = 0;
	        }
	        if(empty($list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['total'])){
	            $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['total'] = 0;
	        }
	        
	        if(empty($global_coverage_total[$start_coverage_date.':'.$end_coverage_date])){
	            $global_coverage_total[$start_coverage_date.':'.$end_coverage_date] = array('total' => 0,'start_coverage_period'=>$start_coverage_date,'end_coverage_period' => $end_coverage_date);
	        }

        	$list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['customer_name'] = $customer_name;

        	$group_company_name = $group_company_res[0]['name'];
	        if(!empty($group_company_id)){
	          $found_key = array_search($group_company_id, array_column($group_company_res, 'id'));
	          $group_company_name = $group_company_res[$found_key]['name'];
	        }
	        if($transaction_type == 'refund'){
	        	$total = -1 * $total;
	        }
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['gp_company_name'] = $group_company_name;

	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['rep_id'] = $rep_id;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['start_coverage_period'] = $start_coverage_date;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['end_coverage_period'] = $end_coverage_date;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['cust_id'] = $customer_id;

	        
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['employee_total'] +=  $enrolleePremium;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['group_employer_total'] +=  $enrollerPermium;

	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['transaction_type'] = $transaction_type;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['category_total'] += $total;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['ws_ids'] = $website_id;
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index][$category_id]['transaction_type'] = $transaction_type;
	        
	        $list_bill_items_res[$is_cobra_coverage][$start_coverage_date.':'.$end_coverage_date][$customer_id.'-'.$index]['total'] +=  $total;
	        

	        if($is_cobra_coverage == 'Y'){
	        	$cobra_total += $total;
	        }else{
	        	$global_coverage_total[$start_coverage_date.':'.$end_coverage_date]['total'] += $total;
	        	$global_total += $total;
	        }
	    	
	    }
  	}
  	$global_total += $past_due_amount + $list_bill_adjustment + $cobra_total + $admin_fee; 
//********************** Display List Bill Detail Code End   **********************

//********************** Display Amendment Summary Code Start **********************
  	$amendment_total = 0;
	$sqlAmendment = "SELECT lba.*,c.id as cust_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.rep_id,lbd.transaction_type 
					FROM list_bill_amendment lba 
					JOIN list_bill_details lbd ON FIND_IN_SET(lbd.id,lba.list_bill_details_id)
					JOIN customer c ON(c.id = lba.customer_id) 
					WHERE lba.is_deleted='N' AND md5(lba.list_bill_id) = :id GROUP BY lba.id";
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

		    if(empty($amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['total'])){
	            $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['total'] = 0;
	        }
        	$amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['customer_name'] = $customer_name;
	        $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['rep_id'] = $rep_id;
	        $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['start_coverage_period'] = $start_coverage_date;
		    $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['end_coverage_period'] = $end_coverage_date;
		    $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['cust_id'] = $customer_id;

		    $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['total'] +=  $total;
		    $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id]['transaction_type'] =  $transaction_type;

		    if(empty($amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id][$category_id]['category_total'])){
	            $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id][$category_id]['category_total'] = 0;
		    }
	        $amendment_items_res[$start_coverage_date.':'.$end_coverage_date][$customer_id][$category_id]['category_total'] += $total;
	        $amendment_total += $total;
		}
	}
	$final_balance = $global_total - $amendment_total;
//********************** Display Amendment Summary Code End   ********************** 
 
/* pre_print($list_bill_items_res,false);
 pre_print($global_coverage_total,false);
 pre_print($global_adjustment_total,false);
 pre_print($global_total,false);
 pre_print($cobra_total,false);
 pre_print($past_due_amount);*/
$exStylesheets = array('css/mpdf_common_style.css');

if(!empty($_GET['action_type'])){
	if($_GET['action_type'] == 'pdf') {
		ob_start();
		include 'tmpl/view_listbill_statement.inc.php';
		$pdf_html_code = ob_get_clean();

  		
  		require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
  
  		$mpdf = new \Mpdf\Mpdf([
				    'mode' => 'utf-8',
				    'format' => 'A4-L',
				    'orientation' => 'L'
				]);
  		$stylesheet = file_get_contents('css/mpdf_common_style.css');
  		$mpdf->WriteHTML($stylesheet,1);
  		$mpdf->WriteHTML($pdf_html_code,2);
  		$mpdf->use_kwt = true;
  		$mpdf->shrink_tables_to_fit = 1;
  		$mpdf->Output("List Bill-".$invoice_no.".pdf",'D');
  		exit;
	}else if($_GET['action_type'] == 'view_pdf' ) {
		ob_start();
		include 'tmpl/view_listbill_statement.inc.php';
		$pdf_html_code = ob_get_clean();

  		
  		require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
  
  		$mpdf = new \Mpdf\Mpdf();
  		$stylesheet = file_get_contents('css/mpdf_common_style.css');
  		$mpdf->WriteHTML($stylesheet,1);
  		$mpdf->WriteHTML($pdf_html_code,2);
  		$mpdf->use_kwt = true;
  		$mpdf->shrink_tables_to_fit = 1;
  		$mpdf->Output("List Bill-".$invoice_no.".pdf",'I');
	}else if($_GET['action_type'] == 'export_excel') {
		if (!empty($list_bill_items_res)) {
	        $csv_line = "\n";
	        $csv_seprator = ",";
	        $field_seprator = '"';
	        $content = "";

	        $csv_title_arr = array('Coverage Period', 'COBRA Coverage', 'Company', 'Member Name','Member ID');
	        if(!empty($prdCatRows)) {
			    foreach ($prdCatRows as $key => $value) {
			        array_push($csv_title_arr, $value['title']);
			    }
			}
	        array_push($csv_title_arr, 'Fee');
	        array_push($csv_title_arr, 'Enrollee Premium');
	        array_push($csv_title_arr, 'Total');


	        $csv_amendment_title_arr = array('Coverage Period', 'Company', 'Member Name','Member ID');
	        if(!empty($prdCatRows)) {
			    foreach ($prdCatRows as $key => $value) {
			        array_push($csv_amendment_title_arr, $value['title']);
			    }
			}
	        array_push($csv_amendment_title_arr, 'Fee');
	        array_push($csv_amendment_title_arr, 'Enrollee Premium');
	        array_push($csv_amendment_title_arr, 'Total');

	        $list_credits_applied = !empty($credits_applied) ? displayAmount($credits_applied,2) : '$0.00'; 
	        $list_past_due_amount = !empty($past_due_amount) ? displayAmount($past_due_amount,2) : '$0.00';
	        $list_admin_fee = !empty($admin_fee) ? displayAmount($admin_fee,2) : '$0.00';
	        $list_adjustment = !empty($list_bill_adjustment) ? displayAmount($list_bill_adjustment,2) : '$0.00';
	        $list_global_total = !empty($global_total) ? displayAmount($global_total,2) : '$0.00';
	        
	        $content .= $field_seprator . 'Group Name' . $field_seprator . $csv_seprator .
	                    $field_seprator . $group_name . $field_seprator . $csv_seprator . $csv_line .
	                    $field_seprator . 'Group Address' . $field_seprator . $csv_seprator .
	                    $field_seprator . $address . $field_seprator . $csv_seprator . $csv_line;
	        $content .= $field_seprator .  '' . $field_seprator . $csv_seprator . 
	                    $field_seprator . $city . ' ' . $state . ' ' . $zip . $field_seprator . $csv_seprator . $csv_line . 
	                    $field_seprator . 'Summary' . $field_seprator . $csv_seprator . $csv_line . 
	                    $field_seprator . 'Invoice Date' . $field_seprator . $csv_seprator . 
	                    $field_seprator . $csv_invoice_date . $field_seprator . $csv_seprator . $csv_line . 
	                    $field_seprator . 'Due Date' . $field_seprator . $csv_seprator .
	                    $field_seprator . $csv_due_date . $field_seprator . $csv_seprator . $csv_line . 
	                    $field_seprator . 'List Bill' . $field_seprator . $csv_seprator .
	                    $field_seprator . $invoice_no . $field_seprator . $csv_seprator . $csv_line;

	                    if(!empty($global_coverage_total)) { 
                   			foreach ($global_coverage_total as $start_end_coverage_date => $coverage_total_data) { 
                   				$content .= $field_seprator . "Coverage Period " . date($DATE_FORMAT,strtotime($coverage_total_data['start_coverage_period'])) .' - '. date($DATE_FORMAT, strtotime($coverage_total_data['end_coverage_period'])) . $field_seprator . $csv_seprator . 
	              				$field_seprator . displayAmount($coverage_total_data['total'],2) . $field_seprator . $csv_seprator . $csv_line;
	              			}
	              		}
	        $content .= $field_seprator . 'COBRA Coverage' . $field_seprator . $csv_seprator .
	                  $field_seprator . displayAmount($cobra_total, 2) . $field_seprator . $csv_seprator . $csv_line .
	                  $field_seprator . 'Admin Fees' . $field_seprator . $csv_seprator . 
	                  $field_seprator . $list_admin_fee . $field_seprator . $csv_seprator . $csv_line .
	                  $field_seprator . 'Past Due Amount' . $field_seprator . $csv_seprator .
	                  $field_seprator . $list_past_due_amount . $field_seprator . $csv_seprator . $csv_line . 
	                  $field_seprator . 'Adjustment' . $field_seprator . $csv_seprator . 
	                  $field_seprator . $list_adjustment . $field_seprator . $csv_seprator . $csv_line .
	                  $field_seprator . 'Total ($)' . $field_seprator . $csv_seprator . 
	                  $field_seprator . $list_global_total . $field_seprator . $csv_seprator . $csv_line .$csv_line;

	        if(count($csv_title_arr)) {
	          foreach ($csv_title_arr as $key => $value) {
	            $content .= $field_seprator . $value . $field_seprator . $csv_seprator;
	          }
	        }
	        $content .= $csv_line;
	        foreach ($list_bill_items_res as $is_cobra_coverage => $coverageArray) {
          		foreach ($coverageArray as $start_end_coverage_date => $ListBillData) {
		            if (!empty($ListBillData)) {
		              foreach ($ListBillData as $customerID => $detail) {
		                if(!empty($detail) && !empty($detail['cust_id']) && !empty($detail['customer_name'])){
	                  		$content .= $field_seprator. date($DATE_FORMAT,strtotime($detail['start_coverage_period'])) .' - '.date($DATE_FORMAT,strtotime($detail['end_coverage_period'])) . $field_seprator . $csv_seprator . 
		                    	
		                    	$field_seprator.  $is_cobra_coverage . $field_seprator . $csv_seprator . 
	                      		$field_seprator. $detail['gp_company_name'] . $field_seprator . $csv_seprator.
	                      		$field_seprator. $detail['customer_name'] . $field_seprator . $csv_seprator.
	                      		$field_seprator. $detail['rep_id'] . $field_seprator . $csv_seprator;

		                      	if(!empty($product_arr)) {
			                      	foreach ($product_arr as $titel_key => $category_id) {
			                        	$prd_price = isset($detail[$category_id]['category_total']) ? $detail[$category_id]['category_total'] : '0';
			                        	$prd_price = ($detail['transaction_type'] == 'refund') ? $prd_price * -1 : $prd_price;
			                        	$content .= $field_seprator. displayAmount($prd_price,2) . $field_seprator . $csv_seprator;
			                    	}
		                      	} 
		                      	$tmp_employee_total = isset($detail['employee_total']) ? $detail['employee_total'] : 0;
		                      	$tmp_employee_total = ($detail['transaction_type'] == 'refund') ? $tmp_employee_total * -1 : $tmp_employee_total;

		                      	$tmp_total = isset($detail['total']) ? $detail['total'] : 0;
		                      	$tmp_total = ($detail['transaction_type'] == 'refund') ? $tmp_total * -1 : $tmp_total;
	                  		$content .= $field_seprator. displayAmount($tmp_employee_total) . $field_seprator . $csv_seprator.
	                  			$field_seprator. displayAmount($tmp_total, 2) . $field_seprator . $csv_seprator .$csv_line;
		                }
		              }
		            }
	          	}
	        }

	        if(!empty($csv_amendment_title_arr) && !empty($amendment_items_res)) {
	        	$content .=$csv_line;
	        	$content .= $field_seprator . 'Amendment Summary' . $field_seprator . $csv_seprator . $csv_line;
	          	foreach ($csv_amendment_title_arr as $key => $value) {
	            	$content .= $field_seprator . $value . $field_seprator . $csv_seprator;
	          	}

	          	$content .= $csv_line;
		        
          		foreach ($amendment_items_res as $start_coverage_date => $ListBillData) {
		            if (!empty($ListBillData)) {
		              foreach ($ListBillData as $customerID => $detail) {
		                if(!empty($detail) && !empty($detail['cust_id']) && !empty($detail['customer_name'])){
	                  		$content .= $field_seprator. date($DATE_FORMAT,strtotime($detail['start_coverage_period'])) .' - '.date($DATE_FORMAT,strtotime($detail['end_coverage_period'])) . $field_seprator . $csv_seprator . 
		                    	
	                      		$field_seprator. ' - ' . $field_seprator . $csv_seprator.
	                      		$field_seprator. $detail['customer_name'] . $field_seprator . $csv_seprator.
	                      		$field_seprator. $detail['rep_id'] . $field_seprator . $csv_seprator;

		                      	if(!empty($product_arr)) {
			                      	foreach ($product_arr as $titel_key => $category_id) {
			                        	$prd_price = isset($detail[$category_id]['category_total']) ? $detail[$category_id]['category_total'] : '0';
			                        	$prd_price = ($detail['transaction_type'] == 'refund') ? $prd_price * -1 : $prd_price;
			                        	$content .= $field_seprator. displayAmount($prd_price,2) . $field_seprator . $csv_seprator;
			                    	}
		                      	} 

		                      	$tmp_total = isset($detail['total']) ? $detail['total'] : 0;
		                      	$tmp_total = ($detail['transaction_type'] == 'refund') ? $tmp_total * -1 : $tmp_total;
	                  		$content .= $field_seprator. '-' . $field_seprator . $csv_seprator.
	                  			$field_seprator. displayAmount($tmp_total, 2) . $field_seprator . $csv_seprator .$csv_line;
		                }
		              }
		            }
	          	}

	          	$content .= $csv_line;
          	 	$content .= $field_seprator . 'Original Balance' . $field_seprator . $csv_seprator .
	                  $field_seprator . displayAmount($global_total, 2) . $field_seprator . $csv_seprator . $csv_line .
	                  $field_seprator . 'Amendment(s)' . $field_seprator . $csv_seprator . 
	                  $field_seprator . displayAmount($amendment_total,2) . $field_seprator . $csv_seprator . $csv_line .
	                  $field_seprator . 'Current Balance to be Paid' . $field_seprator . $csv_seprator . 
	                  $field_seprator . displayAmount($final_balance,2) . $field_seprator . $csv_seprator . $csv_line .$csv_line;
		        
	        }


	        $csv_filename = "List_Bill-".$invoice_no.".csv";
	        header('Content-Type: application/excel');
	        header('Content-disposition: attachment;filename=' . $csv_filename);
	        echo $content;
	        exit(); 
      	} else {
        	setNotifyError("No record found");
	        if(isset($_SESSION['groups']['id'])){
	          redirect($GROUP_HOST ."/view_listbill_statement.php?list_bill=".$list_bill_id);
	        } else {
	          redirect($ADMIN_HOST ."/view_listbill_statement.php?list_bill=".$list_bill_id);
	        }
	        exit();
      	}
	}
}else{
	$template = 'view_listbill_statement.inc.php';
	$layout = 'single.layout.php';
	include_once 'layout/end.inc.php';
}

?>

<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = !empty($_POST['list_bill']) ? $_POST['list_bill'] : '';
$category = !empty($_POST['category']) ? $_POST['category'] : array();
$amendment = !empty($_POST['amendment']) ? $_POST['amendment'] : array();
$list_bill_ids = !empty($_POST['list_bill_ids']) ? $_POST['list_bill_ids'] : array();


$sqlListBill = "SELECT lb.id,lb.list_bill_no,lb.customer_id,lb.items_total,lb.grand_total,lb.due_amount,lb.amendment FROM list_bills lb WHERE md5(id)=:id AND lb.status='open'";
$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$id));

$list_bill_id = $resListBill['id'];
$group_id = $resListBill['customer_id'];

$result = array();
$i=0;

$total_amendment = isset($resListBill['amendment']) ? $resListBill['amendment'] : 0;
$applied_amendment = 0;
if(!empty($category)){
	foreach ($category as $tmpKey => $data) {
		$start_coverage_date = date('Y-m-d',$tmpKey);
		$end_coverage_date = date('Y-m-t',strtotime($start_coverage_date));
		foreach ($data as $customer_id => $categoryData) {

			$sqlCustomer = "SELECT rep_id FROM customer where id=:id";
			$resCustomer = $pdo->selectOne($sqlCustomer,array(":id"=>$customer_id));
			$rep_id='';
			if(!empty($resCustomer)){
				$rep_id = $resCustomer['rep_id'];
			}

			foreach ($categoryData as $category_id => $amount) {
				$list_bill_detail_id = !empty($list_bill_ids[$tmpKey][$customer_id][$category_id]) ? $list_bill_ids[$tmpKey][$customer_id][$category_id] : 0;
				if(empty($amendment[$tmpKey][$customer_id][$category_id])){

					$product_code ='';

					$sqlList = "SELECT GROUP_CONCAT(p.product_code) as product_code FROM prd_main p
								JOIN list_bill_details lbd ON (p.id = lbd.product_id)
								WHERE lbd.id in (:id)";
					$resList = $pdo->selectOne($sqlList,array(":id"=>$list_bill_detail_id));

					if(!empty($resList) && !empty($resList['product_code'])){
						$product_code = $resList['product_code'];
					}$product_code ='';

					$sqlList = "SELECT GROUP_CONCAT(p.product_code) as product_code FROM prd_main p
								JOIN list_bill_details lbd ON (p.id = lbd.product_id)
								WHERE lbd.id in (:id)";
					$resList = $pdo->selectOne($sqlList,array(":id"=>$list_bill_detail_id));

					if(!empty($resList) && !empty($resList['product_code'])){
						$product_code = $resList['product_code'];
					}

					$insParams = array(
						'list_bill_id' => $list_bill_id,
						'list_bill_details_id' => $list_bill_detail_id,
						'group_id' => $group_id,
						'customer_id' => $customer_id,
						'start_coverage_date' => $start_coverage_date,
						'end_coverage_date' => $end_coverage_date,
						'category_id' => $category_id,
						'amount' => $amount,
					);
					$amendment_id = $pdo->insert("list_bill_amendment",$insParams);

					if(!empty($product_code)){
						$ac_description['ac_message'] = array(
		                    'ac_red_1'=>array(
		                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		                      'title'=>$_SESSION['admin']['display_id'],
		                    ),
		                    'ac_message_1' => "created Amendment ",
		                    'ac_red_2'=>array(
		                      'title'=> $rep_id,
		                    ),
		                    'ac_message_2' => ' for ',
		                    'ac_red_3'=>array(
		                      'title'=> $product_code,
		                    ),
		                    'ac_message_3' => ' in ',
		                    'ac_red_4'=>array(
		                      'title'=> date('m/d/Y',strtotime($start_coverage_date)) .' - '.date('m/d/Y',strtotime($end_coverage_date)),
		                    ),
		                );

						activity_feed(3,$_SESSION['admin']['id'], 'Admin', $group_id,'Group', 'List Bill Amendment Created', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($ac_description));
					}

					$total_amendment = $total_amendment + $amount;
					$applied_amendment = $applied_amendment + $amount;
					
				}
				
			}			
		}
	}
}



if(!empty($amendment)){
	foreach ($amendment as $tmpKey => $data) {
		foreach ($data as $customer_id => $categoryData) {

			$sqlCustomer = "SELECT rep_id FROM customer where id=:id";
			$resCustomer = $pdo->selectOne($sqlCustomer,array(":id"=>$customer_id));
			$rep_id='';
			if(!empty($resCustomer)){
				$rep_id = $resCustomer['rep_id'];
			}

			foreach ($categoryData as $category_id => $amendment_id) {
				if(!isset($category[$tmpKey][$customer_id][$category_id]) && !empty($amendment_id)){
					$sqlAmendment = "SELECT id,amount,list_bill_details_id,start_coverage_date,end_coverage_date FROM list_bill_amendment WHERE id=:id";
					$resAmendment = $pdo->selectOne($sqlAmendment,array(":id"=>$amendment_id));

					if(!empty($resAmendment)){
						$product_code ='';

						$sqlList = "SELECT GROUP_CONCAT(p.product_code) as product_code FROM prd_main p
									JOIN list_bill_details lbd ON (p.id = lbd.product_id)
									WHERE lbd.id in (:id)";
						$resList = $pdo->selectOne($sqlList,array(":id"=>$resAmendment['list_bill_details_id']));

						if(!empty($resList) && !empty($resList['product_code'])){
							$product_code = $resList['product_code'];
						}

						$updateParams=array(
							'is_deleted'=>'Y'
						);
						$updateWhere=array(
							'clause'=>'id=:id',
							'params'=>array(
								":id"=>$amendment_id,
							)
						);
						$pdo->update("list_bill_amendment",$updateParams,$updateWhere);

						if(!empty($product_code)){
							$ac_description['ac_message'] = array(
			                    'ac_red_1'=>array(
			                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			                      'title'=>$_SESSION['admin']['display_id'],
			                    ),
			                    'ac_message_1' => "removed Amendment ",
			                    'ac_red_2'=>array(
			                      'title'=> $rep_id,
			                    ),
			                    'ac_message_2' => ' for ',
			                    'ac_red_3'=>array(
			                      'title'=> $product_code,
			                    ),
			                    'ac_message_3' => ' in ',
			                    'ac_red_4'=>array(
			                      'title'=> date('m/d/Y',strtotime($resAmendment['start_coverage_date'])) .' - '.date('m/d/Y',strtotime($resAmendment['end_coverage_date'])),
			                    ),
			                );
							activity_feed(3,$_SESSION['admin']['id'], 'Admin', $group_id,'Group', 'List Bill Amendment Removed', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($ac_description));
						}
						

						$total_amendment = $total_amendment - $resAmendment['amount'];
						$applied_amendment = $applied_amendment - $resAmendment['amount'];
					}
				}
			}			
		}
	}
}

if(!empty($applied_amendment)){
	$updateParams=array(
		'grand_total'=> $resListBill['grand_total'] - $applied_amendment,
		'due_amount'=> $resListBill['due_amount'] - $applied_amendment,
		'amendment'=> $total_amendment,
	);
	$updateWhere=array(
		'clause'=>'md5(id)=:id',
		'params'=>array(
			":id"=>$id,
		)
	);
	$pdo->update("list_bills",$updateParams,$updateWhere);
}

setNotifySuccess("Amendment Updated Successfully");

header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>

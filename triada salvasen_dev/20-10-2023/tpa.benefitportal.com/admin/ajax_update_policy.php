<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
include_once dirname(__DIR__) . '/includes/member_setting.class.php';
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$policySetting = new policySetting();
$memberSetting = new memberSetting();
$response = array();
$validate = new Validation();

$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : 0;

$ws_row = $pdo->selectOne("SELECT w.*,p.product_type as p_type FROM website_subscriptions w JOIN prd_main p on(p.id = w.product_id) where w.id = :id",array(':id' => $ws_id));

$customer_row = $pdo->selectOne("SELECT * FROM customer where id = :id",array(':id' => $ws_row['customer_id']));

$primary_effective_date = isset($_POST['primary_effective_date']) ? $_POST['primary_effective_date'] : "";
$primary_termination_date = isset($_POST['primary_termination_date']) ? $_POST['primary_termination_date'] : "";

$is_term_date_set = false;
$is_term_date_removed = false;
$is_next_billing_date_changed = false;

$term_reason = isset($_POST['term_reason']) ? $_POST['term_reason'] : "";

$renew_count_arr = isset($_POST['renew_count']) ? $_POST['renew_count'] : array();
$start_coverage_period_arr = isset($_POST['start_coverage_period']) ? $_POST['start_coverage_period'] : array();
$end_coverage_period_arr = isset($_POST['end_coverage_period']) ? $_POST['end_coverage_period'] : array();

$next_billing_date = isset($_POST['next_billing_date']) ? $_POST['next_billing_date'] : "";
$order_detail_ids = isset($_POST['order_detail_ids']) ? $_POST['order_detail_ids'] : array();

$dependent_effective_date = isset($_POST['dependent_effective_date']) ? $_POST['dependent_effective_date'] : array();
$dependent_termination_date = isset($_POST['dependent_termination_date']) ? $_POST['dependent_termination_date'] : array();

$dependent_ids = isset($_POST['dependent_ids']) ? $_POST['dependent_ids'] : array();

if($order_detail_ids){
	$order_detail_ids = array_unique($order_detail_ids);
}

$validate->string(array('required' => true, 'field' => 'primary_effective_date', 'value' => $primary_effective_date), array('required' => 'Effective date is Required'));

foreach ($order_detail_ids as $order_id) {
	$validate->string(array('required' => true, 'field' => 'renew_count_' . $order_id, 'value' => $renew_count_arr[$order_id]), array('required' => 'Please select period'));

    $validate->string(array('required' => true, 'field' => 'start_coverage_period_' . $order_id, 'value' => $start_coverage_period_arr[$order_id]), array('required' => 'Please select date'));

	$validate->string(array('required' => true, 'field' => 'end_coverage_period_' . $order_id, 'value' => $end_coverage_period_arr[$order_id]), array('required' => 'Please select date'));
}

if(($primary_termination_date && strtotime($primary_termination_date) >= strtotime('now') && $ws_row['p_type'] != 'Healthy Step') || (empty($primary_termination_date) && $ws_row['p_type'] != 'Healthy Step')){
	$validate->string(array('required' => true, 'field' => 'next_billing_date', 'value' => $next_billing_date), array('required' => 'Next billing date is Required'));
	if(strtotime(date('m/d/Y',strtotime($ws_row['next_purchase_date']))) != strtotime(date('m/d/Y',strtotime($next_billing_date)))){
		$is_next_billing_date_changed = true;
	}
}

if(empty($ws_row['termination_date']) && !empty($primary_termination_date)){
	$is_term_date_set = true;
}else if((!empty($ws_row['termination_date']) && !empty($primary_termination_date)) && strtotime($ws_row['termination_date']) != strtotime($primary_termination_date)){
	$is_term_date_set = true;
}else if(!empty($ws_row['termination_date']) && empty($primary_termination_date)){
	$is_term_date_removed = true;
}

foreach ($dependent_ids as $dependent_id) {
	$validate->string(array('required' => true, 'field' => 'dependet_effective_date_' . $dependent_id, 'value' => $dependent_effective_date[$dependent_id]), array('required' => 'Please select date'));

	if(!$validate->getError('dependet_effective_date_' . $dependent_id)){
		if(!empty($primary_effective_date) && (strtotime($dependent_effective_date[$dependent_id]) < strtotime($primary_effective_date))){
			$validate->setError('dependet_effective_date_' . $dependent_id,"Date can not be before primary");
		}
	}

	if(!empty($dependent_termination_date)){
		if(!empty($primary_termination_date) && (strtotime($dependent_termination_date[$dependent_id]) > strtotime($primary_termination_date))){
			$validate->setError('dependet_termination_date_' . $dependent_id,"Date can not be after primary");
		}
	}

}



if($validate->isValid()){

	// if(strtotime($ws_row['eligibility_date']) != strtotime($primary_effective_date)){

	// 	$lowest_next_billing_date = get_customer_billing_date($ws_row['customer_id']);
 //        $member_payment_type=getname('prd_main',$ws_row['product_id'],'member_payment_type','id');

 //        $product_dates=$enrollDate->getCoveragePeriod($primary_effective_date,$member_payment_type);

 //        $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
 //        $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

 //        $product_dates = getProductBillingDates($primary_effective_date,$lowest_next_billing_date,$endCoveragePeriod);

 //        $update_params = array(
 //            'eligibility_date' => $product_dates->eligibilityDate,
 //            'next_purchase_date' => $product_dates->nextBillingDate,
 //            'start_coverage_period' => $startCoveragePeriod,
 //            'end_coverage_period' => $endCoveragePeriod,
 //            'updated_at' => 'msqlfunc_NOW()',
 //        );
 //        $ws_whr = array(
 //            "clause" => "id=:id",
 //            "params" => array(
 //                ":id" => $ws_row['id']
 //            )
 //        );
 //        $pdo->update('website_subscriptions', $update_params, $ws_whr);

 //        /*----- Update Order Detail Coverage Date ------------*/
 //        $order_detail_row = $pdo->selectOne("SELECT id FROM order_details WHERE order_id=:order_id AND plan_id=:plan_id",array(":order_id"=>$ws_row['last_order_id'],":plan_id"=>$ws_row['plan_id']));
 //        if(!empty($order_detail_row)) {
 //            $od_update_params = array(
 //                'start_coverage_period' => $startCoveragePeriod,
 //                'end_coverage_period' => $endCoveragePeriod,
 //                'updated_at' => 'msqlfunc_NOW()',
 //            );
 //            $od_whr = array(
 //                "clause" => "id=:id",
 //                "params" => array(
 //                    ":id" => $order_detail_row['id']
 //                )
 //            );
 //            $pdo->update('order_details', $od_update_params, $od_whr);
 //        }

 //        $dependets = $pdo->select("SELECT * FROM customer_dependent WHERE customer_id = :customer_id AND product_id = :product_id AND product_plan_id = :plan_id AND is_deleted = 'N'",array(":customer_id" => $ws_row['customer_id'],":product_id" => $ws_row['product_id'],":plan_id" => $ws_row['plan_id']));

 //        if($dependets){
 //            foreach ($dependets as $value) {
 //                if(strtotime($product_dates->eligibilityDate) > strtotime($value['eligibility_date'])){
 //                    $update_params = array(
 //                        'eligibility_date' => $product_dates->eligibilityDate,
 //                        'updated_at' => 'msqlfunc_NOW()',
 //                    );
 //                    $whr = array(
 //                        "clause" => "id=:id",
 //                        "params" => array(
 //                            ":id" => $value['id']
 //                        )
 //                    );
 //                    $pdo->update('customer_dependent', $update_params, $whr);
 //                }
 //            }
 //        }



 //        /*----- Update Order Detail Coverage Date ------------*/
 //        $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
 //        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
 //        $old_eligibility_date = $ws_row['eligibility_date'];
        
 //        $af_message = 'changed effective date';
 //        $af_desc = array();
 //        $af_desc['ac_message'] =array(
 //            'ac_red_1'=>array(
 //                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
 //                'title'=> $_SESSION['admin']['display_id'],
 //            ),
 //            'ac_message_1' => $af_message.' on ',
 //            'ac_red_2'=>array(
 //                'href'=> 'members_details.php?id='.md5($customer_row['id']),
 //                'title'=>$customer_row['rep_id'],
 //            ),
 //            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($old_eligibility_date).' to : '.displayDate($primary_effective_date),
 //        );
 //        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

 //    }
    if($is_term_date_set){
        $extra_params = array();
        $extra_params['location'] = "member_detail";
        $extra_params['portal'] = "admin";
        $termination_reason = $term_reason;
        $policySetting->setTerminationDate($ws_row['id'],$primary_termination_date,$termination_reason,$extra_params);
    }
    
    if($is_term_date_removed){
        $extra_params = array();
        $extra_params['location'] = "member_detail";
        $extra_params['portal'] = "admin";
        $extra_params['remove_healthy_step_term'] = true;
        $policySetting->removeTerminationDate($ws_row['id'],$extra_params);
    }

    if($is_next_billing_date_changed){
        $upd_next_purchase_date_data = array(
            'next_purchase_date' => date('Y-m-d', strtotime($next_billing_date)),
            'next_attempt_at' => 'NULL'
        );
        $upd_next_purchase_date_where = array(
            "clause" => "id=:id",
            "params" => array(":id" => $ws_row['id'])
        );
        $pdo->update("website_subscriptions", $upd_next_purchase_date_data, $upd_next_purchase_date_where);

        $message = 'Next billing date changed from "' . $ws_row['next_purchase_date'] . '" to "' . $next_billing_date . '"';
        $web_history_data = array(
            'customer_id' => $ws_row['customer_id'],
            'website_id' => $ws_row['id'],
            'product_id' => $ws_row['product_id'],
            'plan_id' => $ws_row['plan_id'],
            'order_id' => 0,
            'status' => 'Update',
            'message' => $message,
            'authorize_id' => '',
            'processed_at' => 'msqlfunc_NOW()',
            'created_at' => 'msqlfunc_NOW()',
        );
        $pdo->insert("website_subscriptions_history", $web_history_data);

        $customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
        $old_next_purchase_date = $ws_row['next_purchase_date'];
        
        $af_message = 'changed next billing date';
        $af_desc = array();
        $af_desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=> $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => $af_message.' on ',
            'ac_red_2'=>array(
                'href'=> 'members_details.php?id='.md5($customer_row['id']),
                'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
            ),
            'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Next billing date changed from : '.displayDate($old_next_purchase_date).' to : '.displayDate($next_billing_date),
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
	}

    $last_coverage_detail = array();
	foreach ($order_detail_ids as $detail_id) {
		$ordCoverageSql = "SELECT od.id as detail_id,od.renew_count,od.start_coverage_period,od.end_coverage_period,o.display_id as ordDispId,o.id as ordId,od.product_name as prdName,ws.id as sub_id,c.id as customerId,od.product_id,od.plan_id,o.status
		    FROM order_details od 
            JOIN orders o ON(o.id=od.order_id)
            JOIN customer c on (c.id=o.customer_id)
            JOIN website_subscriptions ws ON(ws.customer_id=o.customer_id AND ws.product_id=od.product_id)
            WHERE od.id=:detail_id AND od.is_deleted='N'";
        $ordParams = array(":detail_id" => $detail_id);
        $coverageRes = $pdo->selectOne($ordCoverageSql,$ordParams);

        if($coverageRes){
            $renew_count = $renew_count_arr[$detail_id];
            $start_coverage_period = date('Y-m-d',strtotime($start_coverage_period_arr[$detail_id]));
            $end_coverage_period = date('Y-m-d',strtotime($end_coverage_period_arr[$detail_id]));

            if(($renew_count != $coverageRes['renew_count']) || (strtotime($start_coverage_period) != strtotime($coverageRes['start_coverage_period'])) || (strtotime($end_coverage_period) != strtotime($coverageRes['end_coverage_period']))){

                if(!in_array($coverageRes['renew_count'],array('Payment Declined'))) {
                    if(count($last_coverage_detail) == 0) {
                        $last_coverage_detail = array(
                            'start_coverage_period' => $start_coverage_period,
                            'end_coverage_period' => $end_coverage_period,
                            'renew_count' => $renew_count,
                        );
                    } else {
                        if(strtotime($start_coverage_period) > strtotime($last_coverage_detail['start_coverage_period'])) {
                            $last_coverage_detail = array(
                                'start_coverage_period' => $start_coverage_period,
                                'end_coverage_period' => $end_coverage_period,
                                'renew_count' => $renew_count,
                            );  
                        }
                    }
                }

                $update_params = array(
                    "renew_count" => $renew_count,
                    "start_coverage_period" => date("Y-m-d",strtotime($start_coverage_period)),
                    "end_coverage_period" => date("Y-m-d",strtotime($end_coverage_period)),
                    "updated_at" => 'msqlfunc_NOW()'
                );
                $udpate_where = array(
                    "clause" => "id=:id",
                    "params" => array(
                        ":id" => $coverageRes['detail_id'],
                    ),
                );
                $pdo->update('order_details',$update_params,$udpate_where);
                
                $message = 'Order '.$coverageRes["ordDispId"].' Plan Periods Updated on Product '.$coverageRes["prdName"].'.  Initial Plan Periods : P'.$coverageRes["renew_count"] .' (' . date("m/d/Y", strtotime($coverageRes["start_coverage_period"])) . ' - ' . date("m/d/Y", strtotime($coverageRes["end_coverage_period"])) . '). Updated Plan Periods : P'.$renew_count.' (' . date("m/d/Y", strtotime($start_coverage_period)) . ' - ' . date("m/d/Y", strtotime($end_coverage_period)) . ').';
                
                $web_history_data = array(
                    'customer_id' => $coverageRes['customerId'],
                    'website_id' => $coverageRes['sub_id'],
                    'product_id' => $coverageRes['product_id'],
                    'plan_id' => $coverageRes['plan_id'],
                    'order_id' => $coverageRes["ordId"],
                    'status' => 'Update',
                    'message' => $message,
                    'authorize_id' => '',
                    'processed_at' => 'msqlfunc_NOW()',
                    'created_at' => 'msqlfunc_NOW()',
                );
                $pdo->insert("website_subscriptions_history", $web_history_data);
                
                $renew_count_af_str = '';
                if($renew_count != $coverageRes['renew_count']) {
                    $renew_count_af_str = 'Plan period changed from P'.$coverageRes["renew_count"] .' to P'.$renew_count.' <br/>';
                }
                $af_message = 'changed plan period';
		        $af_desc = array();
		        $af_desc['ac_message'] =array(
		            'ac_red_1'=>array(
		                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
		                'title'=> $_SESSION['admin']['display_id'],
		            ),
		            'ac_message_1' => $af_message.' on ',
		            'ac_red_2'=>array(
		                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
		                'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
		            ),
		            'ac_message_2' =>'<br/>Order : '.$coverageRes['ordDispId'].'<br/> Plan : '.display_policy($ws_row['id']).' <br/> '.$renew_count_af_str.' Start plan period changed from : '.displayDate($coverageRes['start_coverage_period']).' to : '.displayDate($start_coverage_period) . '<br/>' . 'End plan period changed from : '.displayDate($coverageRes['end_coverage_period']).' to : '.displayDate($end_coverage_period)
		        );
		        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
            }
        }
	}

    if(!empty($last_coverage_detail)) {
        $update_params = array(
            'renew_count' => ($last_coverage_detail['renew_count'] - 1),
            'start_coverage_period' => $last_coverage_detail['start_coverage_period'],
            'end_coverage_period' => $last_coverage_detail['end_coverage_period'],
        );
        $udpate_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $ws_row['id'],
            ),
        );        
        $pdo->update('website_subscriptions',$update_params,$udpate_where);
    }

    if(!empty($dependent_ids)){
        foreach ($dependent_ids as $dependent_id) {
            $dep_row = $pdo->selectOne("SELECT * FROM customer_dependent WHERE id = :id AND is_deleted = 'N'",array(':id' => $dependent_id));
            
            if($dep_row){

                if(strtotime($dep_row['eligibility_date']) != strtotime($dependent_effective_date[$dependent_id])){
                    $update_params = array('eligibility_date' => date('Y-m-d',strtotime($dependent_effective_date[$dependent_id])),'updated_at' => 'msqlfunc_NOW()');
                    $udpate_where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                    ":id" => $dependent_id,
                            ),
                    );
                    
                    $pdo->update('customer_dependent',$update_params,$udpate_where);

                    $af_message = 'changed effective date';
                    $af_desc = array();
                    $af_desc['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=> $_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' => $af_message.' on ',
                        'ac_red_2'=>array(
                            'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                            'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
                        ),
                        'ac_message_2' =>' <br/> Dependent ID : '.$dep_row['display_id']. '<br> Plan : '.display_policy($ws_row['id']).' <br/> Effective date changed from : '.displayDate($dep_row['eligibility_date']).' to : '.displayDate($dependent_effective_date[$dependent_id])
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

                }
                if(strtotime($dep_row['terminationDate']) != strtotime($dependent_termination_date[$dependent_id])){

                    $update_params = array('terminationDate' => date('Y-m-d',strtotime($dependent_termination_date[$dependent_id])),'status' => 'Termed','updated_at' => 'msqlfunc_NOW()');
                    $udpate_where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                    ":id" => $dependent_id,
                            ),
                    );
                    
                    $pdo->update('customer_dependent',$update_params,$udpate_where);

                    if(empty($dep_row['terminationDate'])){
                        $af_message = 'set termination date';
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                'title'=> $_SESSION['admin']['display_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
                            ),
                            'ac_message_2' =>' <br/> Dependent ID : '.$dep_row['display_id']. '<br> Plan : '.display_policy($ws_row['id']).' <br/> Termination Date : ' . displayDate($dependent_termination_date[$dependent_id]) 
                        );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
                    }else{
                        $af_message = 'changed termination date';
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                'title'=> $_SESSION['admin']['display_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
                            ),
                            'ac_message_2' =>' <br/> Dependent ID : '.$dep_row['display_id']. '<br> Plan : '.display_policy($ws_row['id']).' <br/> Termination Date changed From : ' . displayDate($dep_row['terminationDate']) . " To " . displayDate($dependent_termination_date[$dependent_id]) 
                        );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
                    }
                }

            }

        }
    }

    $response['status'] = 'success';
    setNotifySuccess("Plan updated successfully");
}else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
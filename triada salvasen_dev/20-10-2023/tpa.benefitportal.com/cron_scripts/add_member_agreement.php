<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/function.class.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';

$functionsList = new functionsList();
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$REAL_IP_ADDRESS = get_real_ipaddress();
$subscrition_sql  = "SELECT IF(o.id IS NOT NULL,o.created_at,ws.created_at) AS created_at,
				IF(o.id IS NOT NULL,subscription_ids,GROUP_CONCAT(DISTINCT ws.id)) AS subscription_ids,GROUP_CONCAT(DISTINCT ws.product_id) AS product_ids,c.rep_id,c.id AS cus_id,c.status,o.id AS order_id,cs.signature_file,o.ip_address 
				FROM customer c
				JOIN customer_settings cs ON (c.id = cs.customer_id)
				JOIN customer s ON(s.id=c.sponsor_id AND s.type='Group')
				JOIN website_subscriptions ws ON(ws.customer_id=c.id)
				LEFT JOIN orders o ON (o.customer_id = c.id AND o.is_renewal='N' AND o.status!='Pending Validation')
				LEFT JOIN member_terms_agreement AS mt ON (mt.customer_id = c.id AND ws.agreement_id=mt.id)
				WHERE mt.id IS NULL GROUP BY c.id,IF(o.id IS NOT NULL,o.id,DATE(ws.created_at))";
$subscrition_res = $pdo->select($subscrition_sql);

if(!empty($subscrition_res)){
    foreach($subscrition_res as $res_sub){
    	$subscription_ids = $res_sub['subscription_ids'];
        $primary_details = $billing_details = $dependents_details = $product_ids = array();
        $cust_sql = "SELECT c.id,fname,lname,email,type,country_id,birth_date,country_name,cell_phone,gender,address,address_2,city,state,zip,ip_address,rep_id,display_id,sponsor_id,level,upline_sponsors,status,cs.signature_file,created_at FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE  c.id=:customer_id";
		$customer_res = $pdo->selectOne($cust_sql,array(":customer_id" => $res_sub['cus_id']));
       if(!empty($customer_res)){
        $customerInfo = array(
            'id' => $customer_res['id'],
            'fname' => $customer_res['fname'],
            'lname' => $customer_res['lname'],
            'email' => $customer_res['email'],
            'type' => $customer_res['type'],
            'country_id' => $customer_res['country_id'],
            'birth_date'=>$customer_res['birth_date'],
            'country_name' => $customer_res['country_name'],
            'cell_phone' => $customer_res['cell_phone'],
            'gender' =>$customer_res['gender'],
            'address' => $customer_res['address'],
            'address_2' => $customer_res['address_2'],
            'city' => $customer_res['city'],
            'state' => $customer_res['state'],
            'zip' => $customer_res['zip'],
            'ip_address' => $customer_res['ip_address'],
            'rep_id' => $customer_res['rep_id'],
            'display_id' => $customer_res['display_id'],
            'sponsor_id' => $customer_res['sponsor_id'],
            'level' => $customer_res['level'],
            'upline_sponsors' => $customer_res['upline_sponsors'],
            'status' => $customer_res['status'],
            "signature_file" => $customer_res['signature_file'],
            "created_at" => $customer_res['created_at'],
        );
		$primary_details = $customerInfo;
        }
        	$dependet_where = array();
	        if(!empty($res_sub['order_id'])){
				$dependent_sql = "SELECT cd.*,pm.name as product_name, pm.product_code as product_code
					  FROM order_details as od
					  LEFT JOIN prd_main as pm ON(pm.id = od.product_id AND od.product_type='Normal')
					  JOIN customer_dependent as cd ON (od.website_id=cd.website_id)
					  WHERE od.order_id = :id AND od.is_deleted='N' GROUP BY cd.display_id";
				$dependet_where = array(":id" => $res_sub['order_id']);
	        }else{
	        	$dependent_sql = "SELECT cd.*
				  FROM customer_dependent as cd
				  WHERE cd.website_id IN(".$subscription_ids.") AND cd.is_deleted='N' GROUP BY cd.display_id";
	        }
	  
		   	$dependent_res = $pdo->select($dependent_sql,$dependet_where);  

			if(count($dependent_res) > 0){
				foreach($dependent_res as $dependents){
					$dependent_params = array(
						'id' => $dependents['id'],
						'display_id' => $dependents['display_id'],
						'customer_id' => $dependents['customer_id'],
						'relation' => $dependents['relation'],
						'fname' => $dependents['fname'],
						'mname' => $dependents['mname'],
						'lname' => $dependents['lname'],
						'email' => $dependents['email'],
						'phone' => $dependents['phone'],
						'birth_date' => date('Y-m-d', strtotime($dependents['birth_date'])),
						'gender' => $dependents['gender'],
						);
					$dependents_details[] = $dependent_params;
				} 
			}

            // Customer Billing Details Code Start
			$customer_billing_sql = "SELECT * FROM customer_billing_profile WHERE customer_id=:c_id and is_deleted='N'";
			$customer_billing_res = $pdo->selectOne($customer_billing_sql,array(":c_id" => $res_sub['cus_id'])); 
			// Customer Billing Details Code Ends
            if(!empty($customer_billing_res)){
			$billing_details = $customer_billing_res;
            }

            if(!empty($res_sub['order_id'])){
				$order_details_res = $pdo->select("SELECT od.product_id
	            FROM order_details as od
	            LEFT JOIN website_subscriptions as ws ON(ws.id=od.website_id  AND  ws.customer_id=:cus_id)
	            WHERE od.order_id = :id AND od.is_deleted='N' GROUP BY od.id", array(":id" => $res_sub['order_id'], ":cus_id" => $res_sub['cus_id']));
				foreach($order_details_res as $order){
	              $product_ids[] = $order['product_id'];
	            }
	            
            }else{
            	$product_ids = explode(',', $res_sub['product_ids']);
            }
          
          $product_id = implode(',',$product_ids);
          $terms_conditions_content = $functionsList->get_terms_conditions_content($product_id,$res_sub['cus_id']);
            $file_name = "";
            if($terms_conditions_content){
	            $s3Client = new S3Client([
	                'version' => 'latest',
	                'region'  => $S3_REGION,
	                'credentials'=>array(
	                    'key'=> $S3_KEY,
	                    'secret'=> $S3_SECRET
	                )
	            ]);
	            $s3Client->registerStreamWrapper();

	            $file_name = $customer_res['fname'] . $customer_res['lname'] . date('mdY') . time() . '.txt';
	            $file_name = str_replace(" ", "", $file_name);

	            $result = $s3Client->putObject(array(
	                'Bucket' => $S3_BUCKET_NAME,
	                'Key'    => $file_name,
	                'Body'   => $terms_conditions_content
	            ));

	        }

            $member_terms_res = $pdo->selectOne("SELECT id FROM `member_terms` WHERE is_default='Y'");
		    $member_terms_id = $member_terms_res['id'];

		    if(!empty($res_sub['order_id'])){
					$date_of_sign_res = $pdo->selectOne("SELECT ws.id,ws.created_at FROM `website_subscriptions` ws JOIN `order_details` od ON(od.website_id = ws.id)
                                WHERE od.order_id=:id AND ws.customer_id=:cus_id",
                                array(":id" => $res_sub['order_id'], ":cus_id" => $res_sub['cus_id']));
		    }else{
					$date_of_sign_res = $pdo->selectOne("SELECT ws.id,ws.created_at FROM `website_subscriptions` ws 
                                WHERE ws.customer_id=:cus_id AND ws.id IN(".$subscription_ids.")",
                                array(":cus_id" => $res_sub['cus_id']));
		    }
            
            $terms_params = array(
				'customer_id' => $res_sub['cus_id'],
				'order_id' => !empty($res_sub['order_id']) ? $res_sub['order_id'] : 0,
				'primary_details' => json_encode($primary_details),
				'dependent_details' => json_encode($dependents_details),
				'billing_details' => json_encode($billing_details),
				'agreement' => '',
                'member_terms_id' => $member_terms_id,
                'date_of_signature'=> $date_of_sign_res['created_at'],
				'agreement_file' => $file_name,
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'created_at' => $date_of_sign_res['created_at'],
			);

            $agreement_id = $pdo->insert('member_terms_agreement',$terms_params);

            if(!empty($subscription_ids)){
            	$subscriptionArr = explode(',', $subscription_ids);
            	foreach($subscriptionArr as $value_res){
	                $update_params["agreement_id"] = $agreement_id;
	                $upd_where = array(
	                    'clause' => 'id = :id',
	                    'params' => array(
	                        ':id' => $value_res,
	                    ),
	                );
				    $pdo->update('website_subscriptions', $update_params, $upd_where);
	            }
            }
    }
}

echo "<br>Completed";
dbConnectionClose();
exit;
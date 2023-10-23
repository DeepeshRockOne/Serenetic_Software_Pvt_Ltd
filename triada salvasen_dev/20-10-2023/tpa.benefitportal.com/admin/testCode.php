<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
 echo "test code";
$sql="SELECT id,agent_info FROM prd_descriptions";
$res=$pdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$id = $value['id'];
		$agent_info = !empty($value['agent_info']) ? explode(",",$value['agent_info']) : array();

		if(!empty($agent_info)){
			if (($key = array_search("No Sale State", $agent_info)) !== false) {
			    $agent_info[$key] = "Available State";

			    $agent_info_list = implode(",", $agent_info);

			    $upd_params = array("agent_info"=>$agent_info_list);
			    $upd_where = array("clause"=>"id=:id","params"=>array(":id"=>$id));
			    $pdo->update("prd_descriptions",$upd_params,$upd_where);
			    pre_print($upd_params,false);
			    pre_print($upd_where,false);
			    pre_print("______________",false);
			}
			
		}
	}
}
exit;

/*$sql="SELECT * FROM agent_coded_level";
$res=$pdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$level = $value['level'];
		$level_heading = $value['level_heading'];

		$upd_params = array(
			'level'=>$level_heading,
			'level_heading'=>$level
		);
		$upd_where = array(
			'clause'=>"id=:id",
			'params'=>array(":id"=>$value['id'])
		);

		$pdo->update('agent_coded_level',$upd_params,$upd_where);
	}
}*/

// $user = '811VZLIV6897';
// $address1="";
// $address2="SUITE K 29851 Aventura";
// $city="CA";
// $state="CA";
// $zip="92688";
// $xml_data = "<AddressValidateRequest USERID='$user'>" .
// "<Revision>1</Revision>".
// "<Address ID='0'>" .
// "<Address1>$address1</Address1>" .
// "<Address2>$address2</Address2>".
// "<City>$city</City>" .
// "<State>$state</State>" .
// "<Zip5>$zip</Zip5>" .
// "<Zip4></Zip4>" .
// "</Address>" .
// "</AddressValidateRequest>";


// $url = "http://production.shippingapis.com/ShippingAPI.dll?API=Verify";
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_POSTFIELDS,
//             'XML=' . $xml_data);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
// $output = curl_exec($ch);
// echo curl_error($ch);
// curl_close($ch);

// $array_data = json_decode(json_encode(simplexml_load_string($output)), true);

// $response = array();
// if(!empty($array_data['Address'])){
// 	if(!empty($array_data['Address']['Error'])){
// 		$response['status']='fail';
// 		$response['error_message']=$array_data['Address']['Error']['Description'];
// 	}else{
// 		$response['status']='success';
// 		$response['address']=$array_data['Address']['Address2'];
// 		$response['city']=$array_data['Address']['City'];
// 		$response['state']=$array_data['Address']['State'];
// 		$response['zip']=$array_data['Address']['Zip5'];
// 	}
	
// }
// print_r('<pre>');
// print_r($response);
// print_r('</pre>');
// echo PHP_EOL;
// ?>

<?php 

// $xml_data = "<CityStateLookupRequest USERID='$user'>" .
// "<ZipCode>" .
// "<Zip5>$zip</Zip5>" .
// "</ZipCode>" .
// "</CityStateLookupRequest>";

// $url = "http://production.shippingapis.com/ShippingAPI.dll?API=CityStateLookup";
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_POSTFIELDS,
//             'XML=' . $xml_data);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
// $output = curl_exec($ch);
// echo curl_error($ch);
// curl_close($ch);

// $array_data = json_decode(json_encode(simplexml_load_string($output)), true);

// print_r('<pre>');
// print_r($array_data);
// print_r('</pre>');
// echo PHP_EOL;
//935USTGL7449

// $customer_dependent = $pdo->select("SELECT created_at,cd_profile_id from customer_dependent where is_deleted='N' group by cd_profile_id");
// if(!empty($customer_dependent)){
// 	foreach($customer_dependent as $cs){
// 		$upd_params = array(
// 			'created_at' => $cs['created_at']
// 		);
// 		$where = array(
// 			"clause" => " id=:cd_profile_id ",
// 			"params" => array(":cd_profile_id"=>$cs['cd_profile_id'])
// 		);
// 		$pdo->update("customer_dependent_profile",$upd_params,$where);
// 	}
// }
// echo "Created at date update Completed";

// $website_subscription = $pdo->select("SELECT id,eligibility_date from website_subscriptions where 1");
// if(!empty($website_subscription)){
// 	foreach($website_subscription as $ws){
// 		$upd_params = array(
// 			'eligibility_date' => $ws['eligibility_date']
// 		);
// 		$where = array(
// 			"clause" => " website_id = :website_id ",
// 			"params" => array(":website_id"=>$ws['id'])
// 		);
// 		$pdo->update("customer_dependent",$upd_params,$where);
// 	}
// }
// echo "Eligibility Date update Completed";
// exit;

//Add agent into payment_master_assigned_agent
/*$merchant_processors = $pdo->select("SELECT * from payment_master where is_deleted='N' AND is_assigned_to_all_agent='Y'");
$agents = $pdo->selectOne("SELECT GROUP_CONCAT(id) as ids from customer where is_deleted='N' AND type='Agent'");
if(!empty($merchant_processors) && !empty($agents['ids'])){
	$agentIds = explode(',',$agents['ids']);
	foreach($merchant_processors as $processor){
		foreach($agentIds as $agent_id){
			$assigned_processor = $pdo->selectOne("SELECT id from payment_master_assigned_agent where payment_master_id=:id AND agent_id=:agent_id and is_deleted='N'",array(":id"=>$processor['id'],":agent_id"=>$agent_id));
			if(empty($assigned_processor['id'])){
				// echo "AgentId : ".$agent_id."<br>";
				// echo "ProcessorId : ".$processor['id'];
				// echo "<br><br>";
				$ins_param = array(
					"agent_id"=>$agent_id,
					"payment_master_id"=>$processor['id'],
					"created_at"=>$processor['created_at'],
				);
				$pdo->insert("payment_master_assigned_agent",$ins_param);
			}
		}
	}
}
echo "Completed";*/


//update Feature access
/*$agents = $pdo->select("SELECT c.rep_id,c.id,cs.agent_coded_id from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where type='Agent' AND c.is_deleted='N'");
if(!empty($agents)){
	foreach($agents as $agent){
		
		$upd_params = array(
					'feature_access' => $agentCodedRes[$agent['agent_coded_id']]['feature_access']
					);
		$where = array(
			"clause" => " id = :id ",
			"params" => array(":id"=>$agent['id'])
		);

		$pdo->update("customer",$upd_params,$where);
		echo "Feature updated : ".$agent['rep_id'];

	}
}
echo "<br>Completed";
exit;*/


?>
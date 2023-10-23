<?php
	include_once 'layout/start.inc.php';

	$response=array();
	$validate = new Validation();

	$product_id  = !empty($_POST['product']) ? $_POST['product'] : 0;
	$notifyAgents  = !empty($_POST['notifyAgents']) ? $_POST['notifyAgents'] : '';
	$notifyContent = !empty($_POST['notifyContent']) ? $_POST['notifyContent'] : '';
	$notifyAdmins = !empty($_POST['notifyAdmins']) ? $_POST['notifyAdmins'] : '';

	$validate->string(array('required' => true, 'field' => 'notifyError', 'value' => $notifyContent), array('required' => 'Notify Content is required'));

	if (preg_match('/^(<div><br><\/div>$)/',$notifyContent))
	{
	 $validate->setError("notifyError","Notify Content is required");
	}
	
	$emailList=array();

	$prdSql="SELECT product_code,name from prd_main where md5(id) = :id";
	$prdRes=$pdo->selectOne($prdSql,array(":id"=>$product_id));

	if($notifyAgents=='Y'){
		$sqlAgents = "SELECT c.email,c.fname,c.lname,c.rep_id as display_id,'Agent' as type FROM agent_product_rule apr 
			JOIN customer c ON (c.id=apr.agent_id AND c.type='Agent')
		where md5(apr.product_id)=:product_id AND apr.is_deleted='N' and apr.status='Contracted'";
		$resAgents = $pdo->select($sqlAgents,array(":product_id"=>$product_id));

		$emailList = array_merge($emailList,$resAgents);
	}
	if($notifyAdmins=='Y'){
		$sqlAdmins = "SELECT email,fname,lname,display_id,'Admin' as type FROM admin where status='Active' AND is_deleted='N'";
		$resAdmins = $pdo->select($sqlAdmins);
		$emailList = array_merge($emailList,$resAdmins);
	}

	if(empty($emailList)){
		$validate->setError("notifyError","No Recipient Found For this Mail");
	}
	if(empty($prdRes)){
		$validate->setError("notifyError","Product Not Found");	
	}

	if ($validate->isValid()) {
		
		foreach ($emailList as $key => $recipientArr) {
			$params=array(
				'fname'=>$recipientArr['fname'],
				'lname'=>$recipientArr['lname'],
				'email'=>$recipientArr['email'],
				'displayID'=>$recipientArr['display_id'],
				'productCode'=>$prdRes['product_code'],
				'productName'=>$prdRes['name'],
			);
			$subject = "Product Data Updated";
			trigger_mail_to_mail($params,$recipientArr['email'],3,$subject,$notifyContent);
		}

		$response['status'] = "Success";
	}else {
		$response['status'] = "fail";
		$response['errors'] = $validate->getErrors();
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
	dbConnectionClose();
	exit;
?>
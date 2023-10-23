<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/files.class.php';
$FilesClass = new FilesClass();
$validate = new Validation();
$response = array();

$products = checkIsset($_POST['products']);
$file_name = checkIsset($_POST['file_name']);
$file_id = checkIsset($_POST['file_id']);
$file_type = checkIsset($_POST['file_type']);
$carrier = checkIsset($_POST['carrier']);
$period_type = checkIsset($_POST['period_type']);

$validate->string(array('required' => true, 'field' => 'file_name', 'value' => $file_name), array('required' => 'File Name is required'));
if(!empty($file_name) && preg_match('/[\'^£$%&*()}{@#~?><>,|=¬]/',$file_name)) {
    $validate->setError('file_name','Special character not allowed');
}
$validate->string(array('required' => true, 'field' => 'file_type', 'value' => $file_type), array('required' => 'File Type is required'));
$validate->string(array('required' => true, 'field' => 'carrier', 'value' => $carrier), array('required' => 'Recipient is required'));
$validate->string(array('required' => true, 'field' => 'period_type', 'value' => $period_type), array('required' => 'Period is required'));

if(empty($products)){
	$validate->setError('products','Please select products');
}

if ($validate->isValid()) {

	if($file_id){
		$old_file = $pdo->selectOne("SELECT bf.products,bf.file_name,pf.name as carrier_name,bf.file_type,bf.period_type FROM billing_files bf JOIN prd_fees pf on (pf.id = bf.carrier_id) where bf.id = :id",array(':id' => $file_id));

		$old_prd = "";
		$new_prd = $products;
		$old_file_name = $old_file['file_name'];
		$old_carrier_name = $old_file['carrier_name'];
		$old_file_type = $old_file['file_type'];
		$old_period_type = $old_file['period_type'];

		if($old_file){
			$old_prd = $old_file['products'];
		}

		$new_carrier = getname('prd_fees',$carrier,'name','id');

		$update_params = array(
			'file_name' => $file_name,
			'products' => implode(',',$products),
			'file_type' => $file_type,
			'carrier_id' => $carrier,
			'period_type' => $period_type,
			'updated_at' => 'msqlfunc_NOW()'
		);

		$upd_where = array(
	        'clause' => 'id = :id',
	        'params' => array(
	          ':id' => $file_id,
	        ),
	    );
	    $pdo->update('billing_files', $update_params, $upd_where);


	    $old_prd = $FilesClass->getBillingFilePrd($file_id);
	    $FilesClass->updateBillingFilePrd($file_id,$products,$old_prd);

	    $str = '';
	    if(!empty($old_prd)){
	      $old_prd_array = $old_prd;
	      $new_prd_array = $new_prd;
	      $prd_diff = array_diff($new_prd_array,$old_prd_array);
	      if(count($prd_diff) > 0 && !empty($prd_diff)){
	        $agents = $pdo->select("SELECT product_code from prd_main where id IN(".implode(",",$prd_diff).")");
	        if(count($new_prd_array) > count($old_prd_array)){
	          $str.=" Admin added ";
	          foreach ($agents as $value) {
	              $str.=$value['product_code'];
	              if(count($agents) > 1)
	              $str.=" ,";
	          }
	            $str.=" on billing file ".$file_name."<br>";
	        }else{
	          
	          $old_products = $pdo->select("SELECT product_code from prd_main where id IN(".implode(",",$old_prd_array).")");
	          $new_products = $pdo->select("SELECT product_code from prd_main where id IN(".implode(",",$new_prd_array).")");
	          foreach($old_products as $op){
	            $str.=" Admin deleted ";
	            $str.=$op['product_code'];
	          }
	            $str.=" <br>";
	          foreach($new_products as $np){
	            $str.=" Admin added ";
	            $str.=$np['product_code'];
	          }
	            $str.=" on billing file ".$file_name."<br>";
	        }        
	      }else{
	        $prd_diff = array_diff($old_prd_array,$new_prd_array);
	        if(count($prd_diff) > 0){
	          $str.=" Admin deleted ";
	          $products = $pdo->select("SELECT product_code from prd_main where id IN(".implode(",",$prd_diff).")");
	          foreach ($products as $value) {
	            $str.=$value['product_code'];
	              if(count($products) > 1)
	              $str.=", ";
	          }
	            $str.=" on billing file ".$file_name."<br>";
	        }
	      }
	    }

	    $activityFeedDesc['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Updated Billing File ',
	      'ac_red_2'=>array(
	        //'href'=>  '',
	        'title'=>$file_name,
	      ),
	    ); 

	    if(strtolower($old_file_name) != strtolower($file_name)){
	    	$activityFeedDesc['key_value']['desc_arr']['File Name']="File Name updated From ".$old_file_name." to ".$file_name;
	    }
	    if(strtolower($old_carrier_name) != strtolower($new_carrier)){
	    	$activityFeedDesc['key_value']['desc_arr']['Carrier']="Carrier updated From ".$old_carrier_name." to ".$new_carrier;
	    }
	    if(strtolower($old_file_type) != strtolower($file_type)){
	    	$activityFeedDesc['key_value']['desc_arr']['File Type']="File Type updated From ".$old_file_type." to ".$file_type;
	    }
	    if(strtolower($old_period_type) != strtolower($period_type)){
	    	$activityFeedDesc['key_value']['desc_arr']['Period Type']="Period Type updated From ".$old_period_type." to ".$period_type;
	    }

	    if(!empty($str)){
	      $activityFeedDesc['key_value']['desc_arr']['Products']=$str;
	    }
	    if($activityFeedDesc){
	    	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $file_id, 'billing_files','Admin Updated Billing File', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
	    }

	    $response['status'] = "success";
		$response['message'] = "File updated successfully";
	}else{
		$insert_params = array(
			'file_name' =>$file_name,
			'products' => implode(',',$products),
			'file_type' => $file_type,
			'carrier_id' => $carrier,
			'period_type' => $period_type,
			'variation_products' => "",
			'ftp_name' => "",
			'status' => "Active",
			'next_scheduled' => "",
			'created_at' => 'msqlfunc_NOW()',
			'updated_at' => 'msqlfunc_NOW()'
		);

		$insert_id = $pdo->insert('billing_files',$insert_params);

		$FilesClass->updateBillingFilePrd($insert_id,$products);

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Created Billing File ',
	      'ac_red_2'=>array(
	        //'href'=>  '',
	        'title'=>$file_name,
	      ),
	    ); 
	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'billing_files','Admin Created Billing File', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

		$response['status'] = "success";
		$response['message'] = "File added successfully";
	}
}else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>
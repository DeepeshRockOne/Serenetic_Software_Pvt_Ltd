<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$products = checkIsset($_POST['products']);
$file_name = checkIsset($_POST['file_name']);
$file_id = checkIsset($_POST['file_id']);

$validate->string(array('required' => true, 'field' => 'file_name', 'value' => $file_name), array('required' => 'File Name is required'));
if(!empty($file_name) && preg_match('/[\'^£$%&*()}{@#~?><>,|=¬]/',$file_name)) {
    $validate->setError('file_name','Special character not allowed');
}

if(empty($products)){
	$validate->setError('products','Please select products');
}

if ($validate->isValid()) {

	if($file_id){
		$old_file = $pdo->selectOne("select products,file_name from fulfillment_files where id = :id",array(':id' => $file_id));

		$old_prd = "";
		$new_prd = $products;
		$old_file_name = $old_file['file_name'];

		if($old_file){
			$old_prd = $old_file['products'];
		}
		$update_params = array('file_name' => $file_name,'products' => implode(',',$products),'updated_at' => 'msqlfunc_NOW()');
		$upd_where = array(
	        'clause' => 'id = :id',
	        'params' => array(
	          ':id' => $file_id,
	        ),
	    );
	    $pdo->update('fulfillment_files', $update_params, $upd_where);


	    $str = '';
	    if(!empty($old_prd)){
	      $old_prd_array = explode(",",$old_prd);
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
	            $str.=" on fulfillment file ".$file_name."<br>";
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
	            $str.=" on fulfillment file ".$file_name."<br>";
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
	            $str.=" on fulfillment file ".$file_name."<br>";
	        }
	      }
	    }

	    $activityFeedDesc['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Updated Fulfillment File ',
	      'ac_red_2'=>array(
	        //'href'=>  '',
	        'title'=>$file_name,
	      ),
	    ); 

	    if(strtolower($old_file_name) != strtolower($file_name)){
	    	$activityFeedDesc['key_value']['desc_arr']['File Name']="File Name updated From ".$old_file_name." to ".$file_name;
	    }
	    if(!empty($str)){
	      $activityFeedDesc['key_value']['desc_arr']['Products']=$str;
	    }
	    if($activityFeedDesc){
	    	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $file_id, 'fulfillment_files','Admin Updated Fulfillment File', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
	    }

	    $response['status'] = "success";
		$response['message'] = "File updated successfully";
	}else{
		$insert_params = array(
			'file_name' =>$file_name,
			'products' => implode(',',$products),
			'variation_products' => "",
			'ftp_name' => "",
			'status' => "Active",
			'next_scheduled' => "",
			'created_at' => 'msqlfunc_NOW()',
			'updated_at' => 'msqlfunc_NOW()'
		);

		$insert_id = $pdo->insert('fulfillment_files',$insert_params);

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Created Fulfillment File ',
	      'ac_red_2'=>array(
	        //'href'=>  '',
	        'title'=>$file_name,
	      ),
	    ); 
	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insert_id, 'fulfillment_files','Admin Created Fulfillment File', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

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
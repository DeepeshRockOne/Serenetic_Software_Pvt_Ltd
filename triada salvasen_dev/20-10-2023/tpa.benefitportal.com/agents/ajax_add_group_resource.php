<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new Validation();
$resource_id = !empty($_POST['resource_id']) ? $_POST['resource_id'] : 0;
$group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : 0;
$label = $_POST['label'];
$url = $_POST['url'];

if($group_id != '' && $resource_id != ''){
	$selResource = "SELECT * FROM group_resource_link WHERE is_deleted='N' AND group_id=:group_id AND id=:resource_id";
   	$resResource = $pdo->selectOne($selResource,array(":group_id"=>$group_id,":resource_id"=>$resource_id));
}

$validate->string(array('required' => true, 'field' => 'label', 'value' => $label), array('required' => 'Label is required'));
$validate->string(array('required' => true, 'field' => 'url', 'value' => $url), array('required' => 'URL is required'));
if ($validate->isValid()) {
	$params = array(
		'group_id' => $group_id,
		'label' => $label,
		'url' => $url,
	);
	if(!empty($resResource)){
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($resResource['id'])
            )
        );
        $pdo->update("group_resource_link",$params,$update_where);
        $response['msg'] = "Resource updated Successfully";

        /*--- Activity Feed -----*/
        if(!empty($group_id)) {
        	$group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) AS name ,rep_id FROM customer WHERE id=:id",array(":id"=>$group_id));
        	$description = array();
			$description['ac_message'] = array(
		        'ac_red_1'=>array(
		            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
		            'title' => $_SESSION['agents']['rep_id'],
		        ),
		        'ac_message_1' =>'  updated resource link In Group '.$group_name['name'].' (',
		        'ac_red_2'=>array(
		            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
		            'title'=> $group_name['rep_id'],
		        ),
		        'ac_message_2' =>') Resource link : ',
		        'ac_red_3'=>array(
		            'href'=> $url,
		            'title'=> $label,
		        ),
	        );
	        $desc=json_encode($description);
	    	activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_name['id'],'Group','Agent Updated Group Resource Link',"","",$desc);
        }
		/*---/Activity Feed -----*/
	} else {
		$pdo->insert('group_resource_link', $params);
		$response['msg'] = "Resource added Successfully";

		/*--- Activity Feed -----*/
		if(!empty($group_id)) {
			$group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) AS name ,rep_id FROM customer WHERE id=:id",array(":id"=>$group_id));
			$description = array();
			$description['ac_message'] = array(
		        'ac_red_1'=>array(
		            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
		            'title' => $_SESSION['agents']['rep_id'],
		        ),
		        'ac_message_1' =>'  added resource link In Group '.$group_name['name'].' (',
		        'ac_red_2'=>array(
		            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
		            'title'=> $group_name['rep_id'],
		        ),
		        'ac_message_2' =>') Resource link : ',
		        'ac_red_3'=>array(
		            'href'=> $url,
		            'title'=> $label,
		        ),
	        );
	        $desc=json_encode($description);
	    	activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_name['id'],'Group','Agent Added Group Resource Link',"","",$desc);
		}
		/*---/Activity Feed -----*/
	}
	$response['status'] = 'success';
} else {
	$response['status'] = 'fail';
}
if (count($validate->getErrors()) > 0) {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>
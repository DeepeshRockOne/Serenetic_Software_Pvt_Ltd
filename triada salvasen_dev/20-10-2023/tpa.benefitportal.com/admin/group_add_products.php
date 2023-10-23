<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(6);
$group_id = !empty($_GET['group_id']) ? $_GET['group_id'] : '';

$groupSponsorId = 1;

$sponsorSql = "SELECT id,rep_id,sponsor_id FROM customer WHERE md5(id)=:group_id";
$sponsorParams = array(":group_id" => $group_id);
$sponsorRes = $pdo->selectOne($sponsorSql,$sponsorParams);

if(!empty($sponsorRes)){
	$group_id = $sponsorRes['id'];
	$groupSponsorId = $sponsorRes['sponsor_id'];
}


$productSql = "SELECT p.id,p.product_code,p.name as prdName,p.type,p.parent_product_id,pc.title as company_name,apr.status 
                FROM prd_main p
                JOIN agent_product_rule as apr ON(p.id=apr.product_id AND apr.status='Contracted' AND apr.is_deleted='N' AND apr.agent_id = :agent_id)
                JOIN prd_category pc ON(p.category_id=pc.id)
                where p.is_deleted='N'  AND p.id NOT IN(SELECT product_id FROM agent_product_rule WHERE is_deleted='N' AND status != 'Pending' AND agent_id=:group_id) AND p.product_type='Group Enrollment' AND p.status='Active' GROUP BY p.id ORDER BY company_name,p.name ASC ";
$productRes = $pdo->select($productSql, array(":agent_id" =>$groupSponsorId,":group_id" => $group_id));


$company_arr = array('Global Products' => array());

if ($productRes){
    foreach($productRes as $key => $row) {
        if($row['company_name'] != ""){
            $company_arr[$row['company_name']][] = $row;
        }else{
            $company_arr['Global Products'][] = $row;                
        }

        if (empty($company_arr['Global Products'])) {
            unset($company_arr['Global Products']);
        }

        if (empty($row['company_name'])) {
            unset($row['company_name']);
        }
    }
}
ksort($company_arr);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);

$validate = new Validation();

if (isset($_POST['save'])) {

	$products = !empty($_POST['products']) ? $_POST['products'] : array();
	$group_id = $_POST['group_id'];
	$groupDispId = getname('customer',$group_id,'rep_id','id');
	if (empty($products)) {
		$validate->setError("products", "Please select at least one product");
	}

	if ($validate->isValid()) {
		if(!empty($products)){
			
			foreach ($products as $key => $prd) {
				$exists = $pdo->selectOne("SELECT id from agent_product_rule WHERE agent_id = :agent_id and product_id=:product_id and is_deleted='N'",
					array(
						'agent_id' => $group_id,
						'product_id' => $prd,
					)
				);

				$prd_rule = array(
					'agent_id' => $group_id,
					'product_id' => $prd,
					'admin_id' => isset($_SESSION["admin"]["id"]) ? $_SESSION["admin"]["id"] : 0,
					'status' => 'Contracted',
					'created_at' => 'msqlfunc_NOW()',
				);

				if (!empty($exists['id'])) {
						unset($prd_rule['created_at']);
						$upd_where = array(
							'clause' => 'id=:id',
							'params' => array(
								':id' => $exists["id"],
							),
						);
						$pdo->update('agent_product_rule', $prd_rule, $upd_where);
						$insId = $exists['id'];
				} else {
						$insId = $pdo->insert("agent_product_rule", $prd_rule);
				}

				$prdDispId = getname('prd_main',$prd,'product_code','id');
				$activityFeedDesc['ac_message'] =array(
	                'ac_red_1'=>array(
	                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
	                  'title'=>$_SESSION['admin']['display_id'],
	                ),
	                'ac_message_1' =>' added Product ('. $prdDispId .') on Group',
	                'ac_red_2'=>array(
	                  'title'=>$groupDispId,
	                ),
              	); 
              	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $insId, 'agent_product_rule','Admin Added Group Product Rule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
			}
			
			
		}
		setNotifySuccess('Success, you successfully created a new product rule.');
		redirect("groups_details.php?id=".md5($group_id),true);
	}
}

$errors = $validate->getErrors();

$template = 'group_add_products.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
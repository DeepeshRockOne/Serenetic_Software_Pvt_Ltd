<?php
include_once __DIR__ . '/includes/connect.php';

$product_ids = !empty($_POST['product_ids']) ? (is_array($_POST['product_ids']) ? $_POST['product_ids'] : explode(',',$_POST['product_ids'])) : array();
$product_status = checkIsset($_POST['product_status']);
$group_id = checkIsset($_POST['group_id']);

$termination_date_sel = checkIsset($_POST['termination_date']);
$location = isset($_POST['location']) ? $_POST['location'] : 'admin';
$is_valid = true;
    $res_status = [];
    if(!empty($product_status) && strtolower($product_status) == 'extinct'){
        if($termination_date_sel == ''){
            $is_valid = false;
            $res_status['errors']['termination_date'] ='Please select termination date.';
        }
    }
    if($product_status!='' && !empty($product_ids) && $is_valid){
        $group = [];
        $id = getname('customer',$group_id,'id','md5(id)');
        $incr =$tblincr = '';
        $sch_param = array();
        
        $update_param = array(
            'status' => $product_status,
            'updated_at' => 'msqlfunc_NOW()',
        );
        
        $update_where = array(
            'clause' => " product_id IN (" . makeSafe(implode(",",$product_ids)) . ") AND agent_id = :agent_id ",
            'params' => array(
                'agent_id' => makeSafe($id),
            ),
        );
        
        //OP29-843 Update For Extinct Status Start
            if(strtolower($product_status) == 'extinct' && !empty($product_ids)){
                $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
                $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$id));
                $sponsor_billing_method = '';
                if(!empty($resBillingType)){
                    $sponsor_billing_method = $resBillingType['billing_type'];
                }
                updateGroupMemberPolicy($id,$termination_date_sel,'Extinct Product',$product_ids,false,'Admin',$sponsor_billing_method);
            }
        //OP29-843 Update For Extinct Status End
        $group_details = array();
        if(!empty($id)){
            $ids = $id;
            $group_dts = $pdo->select("SELECT c.id as id,GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')' )) AS names  FROM customer c WHERE c.id IN(".$ids.") and c.is_deleted='N' and c.type='Group' group by c.id");
            
            foreach($group_dts as $details){
                $product_details = $pdo->selectOne("SELECT GROUP_CONCAT('<br>&nbsp&nbsp&nbsp',p.name,' : ',apr.status,'') as products FROM agent_product_rule apr 
                JOIN customer c ON(c.id = apr.agent_id AND c.is_deleted='N') 
                JOIN prd_main p ON (p.id=apr.product_id AND p.is_deleted='N' and p.status='Active') WHERE apr.is_deleted='N' AND apr.agent_id=:id and apr.product_id IN(". makeSafe(implode(",",$product_ids)).")",array(":id"=>$details['id']));
                $group_details[] = "<strong>".ucfirst($details['names'])."</strong><br>Old Product Status : ".$product_details['products'];
            }
        }
        $pdo->update("agent_product_rule", $update_param, $update_where);

        $products = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as  products from prd_main where id IN(". makeSafe(implode(",",$product_ids)).")");
        $username = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$id));
        $description =array();
        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>'  Updated product status for Group : '.$username['name'].' (',
            'ac_red_2'=>array(
                'href'=> 'groups_details.php?id='.md5($id),
                'title'=> $username['rep_id'],
            ),
            'ac_message_2' =>')',
            );
        $description['key_value'] = array(
            "desc_arr" => array(
                "status" => ' Updated To '.$product_status,
            ),
        );
        if(strtolower($product_status) == 'extinct' && !empty($product_ids)){
            $description['desc_termDate'] = 'Term Date: '.$termination_date_sel;
        }
        $description['description'] = implode('<br>',$group_details);
        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'],'Admin', $id, 'Group', 'Product Status Updated.',$_SESSION['admin']['name'],"",$desc);

        $res_status['status'] = 'done';
    }else{
	    $res_status['status'] = "fail";
    }

    header('Content-Type: application/json');
    echo json_encode($res_status);
    dbConnectionClose();
    exit();

?>
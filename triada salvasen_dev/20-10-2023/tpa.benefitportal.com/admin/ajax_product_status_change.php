<?php
include_once __DIR__ . '/includes/connect.php';

$product_ids = checkIsset($_POST['product_ids'],'arr');
$downline = checkIsset($_POST['downline']);
$loa = checkIsset($_POST['loa']);
$product_status = checkIsset($_POST['product_status']);
$agent_id = checkIsset($_POST['agent_id']);

    $res_status = array();
    if(!empty($product_status) && !empty($product_ids) && !empty($agent_id)){
        $id = getname('customer',$agent_id,'id','md5(id)');

        if(!empty($downline)){
            $selAgents = "SELECT group_concat(c.id) as ids
                    FROM customer c 
                    WHERE c.type='Agent' AND c.upline_sponsors like '%,$id,%' AND c.is_deleted='N' AND c.status not in('Invited')";
            $resAgents = $pdo->selectOne($selAgents);
        }else if(!empty($loa)){
            $selAgents = "SELECT group_concat(c.id) as ids
                    FROM customer c 
                    LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
                    WHERE c.type='Agent' AND cs.agent_coded_level='LOA' AND  c.sponsor_id=:sponsor_id AND c.is_deleted='N' AND c.status not in('Invited')";
            $resAgents = $pdo->selectOne($selAgents,array(":sponsor_id" => $id));
        }

        $agents = !empty($resAgents['ids']) ? explode(",", $resAgents['ids']) : array();
        array_push($agents, $id);

        if(!empty($agents)){
            $agentIds = implode(",", $agents);
            
            $update_param = array(
                'status' => $product_status,
                'updated_at' => 'msqlfunc_NOW()',
            );

            $update_where = array(
                'clause' => " product_id IN (" . makeSafe(implode(",",$product_ids)) . ") AND agent_id IN (". makeSafe($agentIds) .") ",
                'params' => array(),
            );
       

            $agents_details = array();
            $agents_dts = $pdo->select("SELECT c.id as id,GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')','(',cs.agent_coded_level,')' )) AS names  FROM customer c LEFT JOIN customer_settings cs ON (cs.customer_id=c.id) WHERE c.id IN(".$agentIds.") and c.is_deleted='N' and c.type='Agent' group by c.id");

            foreach($agents_dts as $details){
                $product_details = $pdo->selectOne("SELECT GROUP_CONCAT('<br>&nbsp&nbsp&nbsp',p.name,' : ',apr.status,'') as products FROM agent_product_rule apr 
                JOIN customer c ON(c.id = apr.agent_id AND c.is_deleted='N') 
                JOIN prd_main p ON (p.id=apr.product_id AND p.is_deleted='N' and p.status='Active') 
                WHERE apr.is_deleted='N' AND apr.agent_id=:id and apr.product_id IN(". makeSafe(implode(",",$product_ids)).")",array(":id"=>$details['id']));
                $agents_details[] = "<strong>".ucfirst($details['names'])."</strong><br>Old Product Status : ".$product_details['products'];
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
                'ac_message_1' =>'  Updated product status for agent : '.$username['name'].' (',
                'ac_red_2'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($id),
                    'title'=> $username['rep_id'],
                ),
                'ac_message_2' =>')',
                );
                $description['key_value'] = array(
                    "desc_arr" => array(
                        "status" => ' Updated To '.$product_status,
                    ),
                );
           
             $description['description'] = implode('<br>',$agents_details);
            $desc=json_encode($description);
            activity_feed(3,$_SESSION['admin']['id'],'Admin', $id, 'Agent', 'Product Status Updated.',$_SESSION['admin']['name'],"",$desc);

            $res_status['status'] = 'done';
        }else{
         $res_status['status'] = "fail";
        }
    }else{
	    $res_status['status'] = "fail";
    }

    header('Content-Type: application/json');
    echo json_encode($res_status);
    dbConnectionClose();
    exit();

?>
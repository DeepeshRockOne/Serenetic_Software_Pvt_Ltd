<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(77);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Healthy Steps";
$breadcrumbes[1]['link'] = 'variation_healthy_steps.php';

$delete = checkIsset($_GET['delete']);
if($delete == 'Y'){

    $agent_id = checkIsset($_GET['agent_id']);
    $fee_id = checkIsset($_GET['fee_id']);

    //Select count assigned agents to the deleted variation Healthy step Fees
    $selAssignedAgent = "SELECT pf.id
    FROM agent_product_rule apr
    JOIN customer c ON(c.id=apr.agent_id AND c.is_deleted='N' AND c.type='Agent')
    JOIN customer_settings cs ON(c.id=cs.customer_id)
    JOIN prd_main p ON(p.id=apr.product_id AND p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Variation')
    JOIN prd_assign_fees paf ON(paf.fee_id = p.id AND paf.is_deleted='N')
    JOIN prd_fees pf ON(paf.prd_fee_id=pf.id AND pf.setting_type='Healthy Step Variation')
    WHERE pf.is_deleted='N' AND md5(pf.id)=:pfId AND md5(c.id)!=:agentId AND apr.is_deleted='N' AND cs.agent_coded_id!=1 AND p.is_deleted='N' GROUP BY c.id";
    $resAssignedAgent = $pdo->select($selAssignedAgent,array(":pfId"=>$fee_id,':agentId'=>$agent_id));
    
    $product_details = $pdo->selectOne("SELECT apr.id,c.fname,c.lname,c.rep_id,GROUP_CONCAT(distinct(apr.product_id)) as product_ids 
    from agent_product_rule apr 
    JOIN prd_main p ON(p.id=apr.product_id and p.type='Fees' and p.product_type='Healthy Step' and record_type='Variation' and p.is_deleted='N') 
    LEFT JOIN customer c ON(c.id=apr.agent_id and c.is_deleted='N' and c.type='Agent')  
    where apr.is_deleted='N' and md5(apr.agent_id)=:agent_id",array(":agent_id"=>$agent_id));
    $fee_res = $pdo->selectOne("SELECT id,display_id from prd_fees where md5(id)=:id and is_deleted='N'",array(":id"=>$fee_id));
    if(!empty($product_details['id'])){
        $response = array();
        $products = explode(',',$product_details['product_ids']);

        //If fees assigned to another agent then delete variation healthy step agent product rule only for That agent only, if there is assigned ony one agent then delete variation fees, matrix and related info
        if(count($resAssignedAgent) > 0){
            foreach($products as $variation_id){
                $upd_where = array(
                    "clause" => 'product_id=:prd_id AND md5(agent_id)=:agent_id',
                    "params" => array(":prd_id"=>$variation_id,":agent_id"=>$agent_id)
                );
                $update_param = array("is_deleted"=>'Y','updated_at'=>'msqlfunc_NOW()');
                $pdo->update("agent_product_rule",$update_param,$upd_where);
            }
        }else{
            foreach($products as $variation_id){
                $update_param = array("is_deleted"=>'Y','update_date'=>'msqlfunc_NOW()');
                $where = array(
                    "clause" => 'id=:prd_id',
                    "params" => array(":prd_id"=>$variation_id)
                );
                $pdo->update("prd_main",$update_param,$where);
        
                $upd_where = array(
                    "clause" => 'product_id=:prd_id',
                    "params" => array(":prd_id"=>$variation_id)
                );
        
                $pdo->update("prd_matrix",$update_param,$upd_where);
        
                unset($update_param['update_date']);
                $update_param['updated_at'] = 'msqlfunc_NOW()';
        
                $pdo->update("prd_member_portal_information",$update_param,$upd_where);
                $pdo->update("agent_product_rule",$update_param,$upd_where);
    
                $upd_where_state = array(
                    "clause" => 'healthy_steps_fee_id=:healthy_steps_fee_id',
                    "params" => array(":healthy_steps_fee_id"=>$variation_id)
                );
                $pdo->update("healthy_steps_states",$update_param,$upd_where_state);
        
                $upd_where_fees = array(
                    "clause" => 'fee_id=:prd_id',
                    "params" => array(":prd_id"=>$variation_id)
                );
    
                $pdo->update("prd_assign_fees",$update_param,$upd_where_fees);
            }

            $upd_param = array("is_deleted"=>'Y','updated_at'=>'msqlfunc_NOW()');
            $upd_where_fee = array(
                "clause" => 'md5(id)=:prd_id',
                "params" => array(":prd_id"=>$fee_id)
            );
            $pdo->update("prd_fees",$upd_param,$upd_where_fee);
        }

        $activityFeedDesc['ac_message'] =array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id']
            ),
            'ac_message_1' =>'Deleted Variation Healthy step for agent '.$product_details['fname'].' '.$product_details['lname']." (".$product_details['rep_id'].") ",
            'ac_red_2'=>array(
                // 'href'=> $ADMIN_HOST.'/variation_healthy_steps.php?fee_id='.md5($fee_res['id']),
                'title'=>$fee_res['display_id'],
            ),
        );

        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_res['id'], 'Variation Healthy Step','Variation Healthy Step Deleted', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));

        $response['status']='success';
        $response['message']='Record Deleted Successfully!';
    }else{
        $response['status']='fail';
        $response['message']='No record found!';
    }
    echo json_encode($response);
    exit;
}
$agent_ids = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(apr.agent_id)) as agent_ids 
                            from agent_product_rule apr 
                            JOIN prd_main p ON(p.id=apr.product_id and p.type='Fees' and p.product_type='Healthy Step' AND p.record_type='Variation' and p.is_deleted='N') where apr.is_deleted='N'
                            ");
$is_clone = (isset($_GET['is_clone']) && !empty($_GET['is_clone']) ? $_GET['is_clone'] : 'N');
$agent_res = array();
$qincr='';
if(!empty($agent_ids['agent_ids'])){
    $qincr = " AND c.id not in( ".$agent_ids['agent_ids'].") ";    
}
$agent_sql = "SELECT c.id,fname,lname,rep_id,cs.agent_coded_level FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id and cs.agent_coded_id != 1) where type='Agent' AND is_deleted = 'N' $qincr";
$agent_res = $pdo->select($agent_sql);

$fee_id = checkIsset($_GET['fee_id']);
$agent_id = checkIsset($_GET['agent_id']);

$display_id = $agent_status = $prd_status = '';
$resource_res = array();

if(!empty($fee_id)){
    $resource_res = $pdo->selectOne("SELECT c.id as agent_id,pf.created_at,pf.id as pf_id,pf.display_id,count(distinct(p.id)) as total_fees,GROUP_CONCAT(distinct(p.parent_product_id)) as product_ids,pf.status,c.fname,c.lname,c.rep_id,GROUP_CONCAT(distinct(p.is_fee_on_commissionable)) as fee_commissionable ,apr.status as agent_status
    FROM agent_product_rule apr
    JOIN prd_main p ON(p.id=apr.product_id and p.is_deleted='N' and p.type='Fees' and p.product_type='Healthy Step' AND p.record_type='Variation')
    JOIN prd_assign_fees paf ON(paf.fee_id = p.id and paf.is_deleted='N')
    JOIN prd_fees pf ON(paf.prd_fee_id=pf.id and pf.setting_type='Healthy Step Variation')
    JOIN customer c ON(c.id=apr.agent_id and c.is_deleted='N' and c.type='Agent')
    LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
    WHERE pf.is_deleted='N' and md5(pf.id)=:fee_id and md5(c.id)=:agent_id and cs.agent_coded_id!=1 AND apr.is_deleted='N' GROUP BY c.id",array(":fee_id"=>$fee_id,":agent_id"=>$agent_id));
    if(!empty($resource_res['agent_id'])){
        $display_id = $resource_res['display_id'];
        $prd_status = $resource_res['status'];
    }else{
        setNotifyError("No record found!");
        redirect("healthy_steps.php");
    }
}
if($is_clone == 'Y' || empty($resource_res)){
    include_once __DIR__ . '/../includes/function.class.php';
    $functionsList = new functionsList();
    $display_id=$functionsList->generateHealthyStepVariationDisplayID(true);
}
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'variation_healthy_steps.inc.php';
include_once 'layout/end.inc.php';
?>

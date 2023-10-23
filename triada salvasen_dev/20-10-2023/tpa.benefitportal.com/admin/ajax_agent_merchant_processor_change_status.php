<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
$processor_id = $_GET['processor_id'];
$new_status = $status = !empty($_GET['status']) ? $_GET['status'] : '';
$is_status = !empty($_GET['is_status']) ? $_GET['is_status'] : '';
$is_deleted = !empty($_GET['is_deleted']) ? $_GET['is_deleted'] : '';
$agent_id = checkIsset($_GET['agent_id']);
$type = checkIsset($_GET['type']);
$operation = checkIsset($_GET['operation']);
$response = array();
$agent_downline = checkIsset($_GET['downline']);
$agent_loa = checkIsset($_GET['loa']);
$incr = $tblincr = '';
$activity_feed_arr = $activity_description = $agents = $sch_param = array();
$activity_insert = $activity_update = false;
$main_agent_id = $id = getname('customer',$agent_id,'id','md5(id)');
$global_ach_status = checkIsset($_GET['global_ach_status']);

if(empty($agent_id) || empty($processor_id)){
    $response['status'] = 'error';
    $response['msg'] = 'Something went wrong';
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}

if(!empty($agent_downline)){
    $selAgents = "SELECT group_concat(c.id) as ids 
            FROM customer c 
            WHERE c.type='Agent' AND c.upline_sponsors like '%,$id,%' AND c.is_deleted='N'";
    $resAgents = $pdo->selectOne($selAgents);
}else if(!empty($agent_loa)){
    $selAgents = "SELECT group_concat(c.id) as ids 
            FROM customer c 
            LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
            WHERE c.type='Agent' AND cs.agent_coded_level='LOA' AND  c.sponsor_id=:sponsor_id AND c.is_deleted='N'";
    $resAgents = $pdo->selectOne($selAgents,array(":sponsor_id" => $id));
}


if(!empty($resAgents['ids'])){
    $agents = explode(',',$resAgents['ids']);
}
array_push($agents,$id);

if($operation == 'insert'){
    $res_processor = $pdo->selectOne("SELECT * from payment_master_assigned_agent where md5(payment_master_id)=:id and is_deleted='N' AND md5(agent_id)=:agent_id",array(":id"=>$processor_id,":agent_id"=>$agent_id));
        if(!empty($res_processor['id'])){
            $upd_param = array(
                "status" => 'Active',
            );
            $upd_where = array(
                "clause"=>"id=:id",
                "params"=>array(
                    ":id"=>$res_processor['id']
                )
            );
            $pdo->update("payment_master_assigned_agent",$upd_param,$upd_where);
            $response['msg'] = 'Payment processor Added Successfully!';
            $response['status'] = 'success';
            $activity_update = $activity_insert = true;
        }else{
        $id = getname('payment_master',$processor_id,'id','md5(id)');
        $agent_id = getname('customer',$agent_id,'id','md5(id)');
        $ins_param = array(
            'payment_master_id' => $id,
            'agent_id' => $agent_id,
        );
        $pdo->insert("payment_master_assigned_agent",$ins_param);
        $response['msg'] = 'Payment processor Added Successfully!';
        $response['status'] = 'success';
        $activity_update = $activity_insert = true;
    }
}else{

    if($type == 'Global'){
        foreach($agents as $agent_id){
            $query = "SELECT p.*,pmaa.id as assigned_id FROM payment_master p JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id=p.id) WHERE md5(p.id) = :id AND pmaa.agent_id=:agent_id AND p.type=:type AND pmaa.is_deleted='N' AND p.is_deleted='N'";
            $srow = $pdo->selectOne($query, array(":id" => $processor_id,":agent_id" => $agent_id,":type"=>$type));
        
            if(!empty($srow)){
                if($is_status == 'Y' || $is_deleted == 'Y'){
                    if($is_deleted == 'Y'){
                        $update_params = array(
                            'status' => 'Deleted',
                            'updated_at' => 'msqlfunc_NOW()'
                        );
                    }else{
                        if($global_ach_status == 'ach_status'){
                            $update_params = array(
                                'global_accept_ach_status' => makeSafe($status),
                                'updated_at' => 'msqlfunc_NOW()'
                            );
                        }else{
                            $update_params = array(
                                'status' => makeSafe($status),
                                'updated_at' => 'msqlfunc_NOW()'
                            );
                        }
                        
                    }
                    $update_where = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => makeSafe($srow['assigned_id'])
                        )
                    );
                    $activity_feed_arr['status_update'][$agent_id] = $pdo->update("payment_master_assigned_agent", $update_params, $update_where,true);
                    
                    if($status == 'Deleted'){
                        $response['status'] = 'success';
                        $response['msg'] = 'Processor Deleted Successfully!';
                    }else{
                        $response['status'] = 'success';
                        $response['msg'] = 'Status Updated Successfully!';
                    }
                }
            }else{
                $id = getname('payment_master',$processor_id,'id','md5(id)');
                // $agent_id = getname('customer',$agent_id,'id','md5(id)');
                if($status == '' || empty($status)){
                    $status='Deleted';
                }
                if($global_ach_status == 'ach_status'){
                    $ins_param = array("agent_id"=>$agent_id,"payment_master_id"=>$id,"global_accept_ach_status"=>$status);
                }else{
                    $ins_param = array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status);
                }
                $pdo->insert("payment_master_assigned_agent",$ins_param);
                $activity_feed_arr['status_update'][$agent_id] = array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status);
                $response['status'] = 'success';
                $response['msg'] = 'Status Updated Successfully!';
            }
            $activity_update = true;
        }
    }else{
        
        foreach($agents as $agent_id){
            $query = "SELECT p.*,pmaa.id as assigned_id,pmaa.agent_id as agent_id FROM payment_master p LEFT JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id=p.id AND pmaa.agent_id=:agent_id AND pmaa.is_deleted='N') WHERE md5(p.id) = :id AND ((pmaa.agent_id=:agent_id and pmaa.is_deleted='N') or is_assigned_to_all_agent='Y') AND p.type=:type AND p.is_deleted='N'";
            $srow = $pdo->selectOne($query, array(":id" => $processor_id,":agent_id" => $agent_id,":type"=>$type));
            if (!empty($srow)) {
                if($srow['is_assigned_to_all_agent'] =='Y' && $srow['agent_id'] != $agent_id){
                    $id = getname('payment_master',$processor_id,'id','md5(id)');
                    if($is_deleted == 'Y'){
                        $status ='Deleted';
                    }
                    // else{
                        // $status =$srow['status'];
                        // array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status)
                        if($global_ach_status == 'ach_status'){
                            $insert_param = array(
                                "agent_id"=>$agent_id,
                                "payment_master_id"=>$id,
                                'global_accept_ach_status' => makeSafe($status),
                            );
                        }else{
                            $insert_param = array(
                                "agent_id"=>$agent_id,
                                "payment_master_id"=>$id,
                                'status' => makeSafe($status),
                            );
                        }

                        $activity_feed_arr['status_update'][$agent_id] = array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status);
                    // }
                    // $agent_id = getname('customer',$agent_id,'id','md5(id)');
                    $pdo->insert("payment_master_assigned_agent",$insert_param);
                    $response['status'] = 'success';
                    $response['msg'] = 'Status Updated Successfully!';
                }else{
                    if($is_status == 'Y' || $is_deleted == 'Y'){
                        if($is_deleted == 'Y'){
                            $update_params = array(
                                'status' => 'Deleted',
                                'updated_at' => 'msqlfunc_NOW()'
                            );
                        }else{
                            // $update_params = array(
                            //     'status' => makeSafe($status),
                            //     'updated_at' => 'msqlfunc_NOW()'
                            // );
                            if($global_ach_status == 'ach_status'){
                                $update_params = array(
                                    'global_accept_ach_status' => makeSafe($status),
                                    'updated_at' => 'msqlfunc_NOW()'
                                );
                            }else{
                                $update_params = array(
                                    'status' => makeSafe($status),
                                    'updated_at' => 'msqlfunc_NOW()'
                                );
                            }

                        }
                        $update_where = array(
                            'clause' => 'id = :id',
                            'params' => array(
                                ':id' => makeSafe($srow['assigned_id'])
                            )
                        );
                        $activity_feed_arr['status_update'][$agent_id] = $pdo->update("payment_master_assigned_agent", $update_params, $update_where,true);
        
                        $response['status'] = 'success';
                        $response['msg'] = 'Status Updated Successfully!';
        
                    }else{
                        $update_params = array(
                            'is_deleted' => 'Y',
                            'updated_at' => 'msqlfunc_NOW()'
                        );
                        $update_where = array(
                            'clause' => 'id = :id',
                            'params' => array(
                                ':id' => makeSafe($srow['assigned_id'])
                            )
                        );
                        $pdo->update("payment_master_assigned_agent", $update_params, $update_where);
        
                        $response['status'] = 'success';
                        $response['msg'] = 'Processor Deleted Successfully!';
                    }
                }
                
            }else{
                $id = getname('payment_master',$processor_id,'id','md5(id)');
                $pdo->insert("payment_master_assigned_agent",array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status));
                $activity_feed_arr['status_update'][$agent_id] = array("agent_id"=>$agent_id,"payment_master_id"=>$id,"status"=>$status);
            }
            $activity_update = true;
        }
    }
}

if($activity_update){
    $ac_desc = '';
    $name = $pdo->selectOne("SELECT name,type from payment_master where md5(id)=:id and is_deleted='N'",array(":id"=>$processor_id));
    if($activity_insert){
        $ac_desc = ' Added new merchant processor in ';
    }elseif($is_deleted == 'Y' && !empty($agents)){
        $ac_desc = ' Deleted merchant processor in ';
    }else{
        $ac_desc = ' updated merchant processor status in ';
    }
    $acmsg = '';
    // if($type=='Global'){
        if($global_ach_status == 'ach_status'){
            $acmsg = ' ACH ';
        }else{
            $acmsg = ' CC ';
        }
    // }
    $row = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where id=:id and is_deleted='N'",array(":id"=>$main_agent_id));
    $activity_description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$ac_desc.$row['fname'].' '.$row['lname'].' (',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.$main_agent_id,
            'title'=> $row['rep_id'],
        ),
        'ac_message_2' =>')',
        'ac_message_3' =>  '<br>Merchant Processor : ',
        'ac_red_3'=>array(
            'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$name['type'].'&id='.$processor_id,
            'title'=> $name['name'].$acmsg,
        )
    );
    if($is_deleted == 'Y' && !empty($agents)){
        $deleted_agents = $pdo->selectOne("SELECT GROUP_CONCAT(concat(fname,' ',lname,'(',rep_id,')') SEPARATOR ',<br>') as name from customer where id IN(".implode(',',$agents).")");
        $activity_description['ac_message']['ac_message_4'] = "<br>Merchant processor deleted in agents : <br>".$deleted_agents['name'];
    }

    if(!empty($activity_feed_arr['status_update']) && $is_deleted!='Y'){
        foreach($activity_feed_arr['status_update'] as $key =>$value){
            $value['status'] = !empty($value['global_accept_ach_status']) ? $value['global_accept_ach_status'] : checkIsset($value['status']);
            if(!empty($value['status'])){
                if($value['status'] == 'Deleted')
                    $value['status'] = '';
                $agname = $pdo->selectOne("SELECT concat(fname,' ',lname,'(',rep_id,')') as name from customer where id=:id",array(":id"=>$key));
                $activity_description['description__'.$key] = $agname['name'].' :  Status updated from '.$value['status'].' to '.$new_status;
            }
        }
    }
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $main_agent_id, 'Agent', 'Agent Merchant Processor',  $_SESSION['admin']['fname'],  $_SESSION['admin']['lname'], json_encode($activity_description));
}

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>
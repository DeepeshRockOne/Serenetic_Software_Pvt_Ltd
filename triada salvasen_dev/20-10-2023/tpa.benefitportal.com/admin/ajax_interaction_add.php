<?php
include_once 'layout/start.inc.php';

    if(isset($_POST['type']) == 'delete' && !empty($_POST['interaction_detail_id'])){
        $interaction_detail_id = $_POST['interaction_detail_id'];
        $products = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(id)) as ids from interaction_product where  interaction_detail_id = :id and is_deleted='N'",array(":id"=>$interaction_detail_id));

        $ids = explode(",",$products['ids']);
        $agent_id = $_POST['agent_id'];
        $update_param = array(
            "updated_at" => 'msqlfunc_NOW()',
            "status"    =>  'Inactive',
            "is_deleted" => 'Y',
        );
        $update_where = array("clause"=>"id=:interaction_detail_id","params"=>array(":interaction_detail_id"=>$interaction_detail_id));
        $pdo->update("interaction_detail",$update_param,$update_where);

        $upd_param = array('is_deleted' => 'Y','updated_at'=>'msqlfunc_NOW()');
        foreach($ids as $id){
            $upd_where = array(
                "clause"=>" id = :id ",
                "params"=>array(":id"=>$id)
            );
            $pdo->update("interaction_product",$upd_param,$upd_where);
        }

        $agent_name=$pdo->selectOne("SELECT CONCAT(fname,' ',lname) as name,rep_id,id from customer where is_deleted='N' and md5(id)=:id",array(":id"=>$agent_id));
        $description =array();
        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>'  Deleted Interaction on '.$agent_name['name'].' (',
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.$agent_id,
                'title'=> $agent_name['rep_id'],
            ),
            'ac_message_2' =>')',
            );

        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_name['id'], 'Agent', 'Interaction Deleted.',$_SESSION['admin']['name'],"",$desc);

        // setNotifySuccess("Interaction deleted successfully!");
        $response['status'] = 'success';
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $agent_id  = $_POST['agent_id'];
    $agent_name  = $_POST['agent_name'];
    $agent_repid  = $_POST['rep_id'];
    $interaction_detail_id  = checkIsset($_POST['interaction_detail_id']);
    $admin_id = $_SESSION['admin']['id'];
    $products = checkIsset($_POST['products'],'arr');
    $description = $_POST['description'];
    $interaction_id = $_POST['interaction_id'];
    $interaction_type = $_POST['interaction_type'];
    $interaction_old_type = $_POST['interaction_old_type'];
    $response = $interaction_detail = array();
    $create_etickets = checkIsset($_POST['create_etickets']);
    $validate = new Validation();

    $REAL_IP_ADDRESS = get_real_ipaddress();
    // if(empty($products)){
    //     $validate->setError("products","Please select product.");
    // }

    if(!empty($create_etickets)){

        $group_id = $_POST['group_id'];
        $assigne_admins = checkIsset($_POST['assigne_admins']);
        $subject = $_POST['subject'];

        if(empty($group_id)){
            $validate->setError("group_id","Please select any Category.");
        }
        if(!empty($group_id) && empty($assigne_admins)){
            $validate->setError("assigne_admins","Please select any Admin.");
        }

        $validate->string(array('required' => true, 'field' => 'subject', 'value' => $subject), array('required' => 'Subject is required'));
        
    }

    $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));

    $validate->string(array('required' => true, 'field' => 'interaction_id', 'value' => $interaction_id), array('required' => 'Please select Interaction type.'));

    if($validate->isValid()){

            if(!empty($interaction_detail_id)){
                $interaction_detail = $pdo->selectOne("SELECT id from interaction_detail where md5(id) =:id and is_deleted='N' and  status='Active'",array(":id"=>$interaction_detail_id));
            }
            if(!empty($interaction_detail) && $interaction_detail['id'] > 0){
                $update_activity = array();
                $update_activity['ac_message'] = array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>'  Updated Interaction on '.$agent_name.' (',
                    'ac_red_2'=>array(
                      'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_id),
                      'title'=> $agent_repid,
                    ),
                    'ac_message_2' =>') : ',
                  );
                $update_param = array(
                    "interaction_id" => $interaction_id,
                    "description" => $description,
                    "updated_at" => 'msqlfunc_NOW()',
                );
                $update_where = array(
                        "clause"=>"id=:interaction_detail_id",
                        "params"=>array(":interaction_detail_id"=>$interaction_detail['id'])
                );

                $updated_details = $pdo->update("interaction_detail",$update_param,$update_where,true);

                $db_products = $pdo->select("SELECT id,product_id from interaction_product where interaction_detail_id=:interaction_detail_id and is_deleted='N'",array(":interaction_detail_id"=>$interaction_detail['id']));
                $dbProducts = array();
                if(!empty($db_products) && count($db_products) > 0){
                    foreach($db_products as $prd){
                        $dbProducts[$prd['id']] = $prd['product_id'] ;
                    }
                }
                
                $update_product = array_diff($dbProducts,$products);
                if(!empty($update_product) && count($update_product) > 0){
                    $upd_param = array('is_deleted' => 'Y','updated_at'=>'msqlfunc_NOW()');
                    foreach($update_product as $prd => $product){
                        $upd_where = array(
                            "clause"=>" id = :id ",
                            "params"=>array(":id"=>$prd)
                        );
                        $pdo->update("interaction_product",$upd_param,$upd_where);
                    }
                }
                $inserted_proructs = $deleted_proructs = array();
                
                if(!empty($update_product)){
                    $deleted_proructs = $pdo->selectOne("SELECT GROUP_CONCAT(name) as deleted_products from prd_main where id IN(".implode(',',$update_product).")");
                }

                $insert_product = array_diff($products,$dbProducts);
                foreach($insert_product as $product){
                    $ins_param = array(
                        "interaction_detail_id" => $interaction_detail['id'],
                        "product_id" => $product,
                        "updated_at" => 'msqlfunc_NOW()',
                        "created_at" => 'msqlfunc_NOW()',
                    );
                    $pdo->insert("interaction_product",$ins_param);
                }
                $inserted_proructs = array();
                if(!empty($insert_product)){
                    $inserted_proructs = $pdo->selectOne("SELECT GROUP_CONCAT(name) as inserted_products from prd_main where id IN(".implode(',',$insert_product).")");
                }

                $deleted_prd = !empty($deleted_proructs['deleted_products']) ? 'Deleted Products : '.$deleted_proructs['deleted_products'] : '';
                $inserted_prd = !empty($inserted_proructs['inserted_proructs']) ? 'Added Products : '.$inserted_proructs['inserted_proructs'] : '';
                $interaction_update = $interaction_old_type != $interaction_type ? 'From '.$interaction_old_type.' To '.$interaction_type  : 'No updates';

                $update_activity['key_value'] = array(
                    "desc_arr" => array(
                        "Type" => $interaction_update,
                        "description" => 'From '.checkIsset($updated_details['description']).' To '.$description,
                        "products" => $deleted_prd!='' || $inserted_prd!='' ? $deleted_prd .'<br>'.$inserted_prd : 'No updates',
                    ),
                );

                $desc=json_encode($update_activity);
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_id, 'Agent', 'Interaction Updated.',$_SESSION['admin']['name'],"",$desc);

                // setNotifySuccess("Interaction updated successfully!",true);
                $response['message'] = 'Interaction updated successfully!';
                $response['status'] = 'success';

            }else{
                $ins_param = array(
                    "admin_id" => $admin_id,
                    "user_id" => $agent_id,
                    "interaction_id" => $interaction_id,
                    "description" => $description,
                    "updated_at" => 'msqlfunc_NOW()',
                    "created_at" => 'msqlfunc_NOW()',
                );
    
                $eticket_description = $description;
                $inserted_id = $pdo->insert("interaction_detail",$ins_param);
    
                if(!empty($products) && count($products) > 0){
                    foreach($products as $product){
                        $ins_param = array(
                            "interaction_detail_id" => $inserted_id,
                            "product_id" => $product,
                            "updated_at" => 'msqlfunc_NOW()',
                            "created_at" => 'msqlfunc_NOW()',
                        );
                        $ins_id =$pdo->insert("interaction_product",$ins_param);
                    }
                }                
                // setNotifySuccess("Interaction added successfully!",true);
                $response['message'] = 'Interaction added successfully!';
                $response['status'] = 'success';

                $description =array();
                $description['ac_message'] = array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>'  created Interaction on '.$agent_name.' (',
                    'ac_red_2'=>array(
                      'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_id),
                      'title'=> $agent_repid,
                    ),
                    'ac_message_2' =>')',
                  );


                $desc=json_encode($description);
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $agent_id, 'Agent', 'Interaction Created.',$_SESSION['admin']['name'],"",$desc);

                if(!empty($create_etickets)){
                    include_once __DIR__ . '/../includes/function.class.php';
                    $functionList = new functionsList();
                    $sessionArr['admin'] = $_SESSION['admin'];
                    $eticket_description .= '<p>'.$_SESSION['admin']['display_id'].' - '.$_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname'].'</p>';
                    $real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                    $eTicketArr = $functionList->createNewTicket($sessionArr,$group_id,$subject,$assigne_admins,$eticket_description,$agent_id,'Agent',$real_ip_address,array(),'notes');
                    $response['status'] = 'success';
                    if(!empty($eTicketArr['ticket_id'])){
                        $tid_where = array(
                            "clause" => "id=:id",
                            "params" => array(":id"=>$inserted_id)
                        );
                        $pdo->update("interaction_detail",array("e_ticket_id"=>$eTicketArr['ticket_id']),$tid_where);
                    }
                }
            }
            
        
    }else{
        $response['status'] = 'fail';
        $response['errors'] = $validate->getErrors();
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    dbConnectionClose();
    exit;

?>
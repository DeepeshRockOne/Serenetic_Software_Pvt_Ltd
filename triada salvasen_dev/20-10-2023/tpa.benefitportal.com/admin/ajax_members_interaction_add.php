<?php
include_once 'layout/start.inc.php';
$type = 'Interaction';
if(isset($_REQUEST['is_claim']) && $_REQUEST['is_claim'] == 'Y'){
    $type = 'Claim';
}
    if(isset($_POST['type']) == 'delete' && !empty($_POST['interaction_detail_id'])){
        $interaction_detail_id = $_POST['interaction_detail_id'];
        $products = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(id)) as ids from interaction_product where  interaction_detail_id = :id and is_deleted='N'",array(":id"=>$interaction_detail_id));

        $ids = explode(",",$products['ids']);
        $memberId = $_POST['memberId'];
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

        $memberName=$pdo->selectOne("SELECT CONCAT(fname,' ',lname) as name,rep_id,id from customer where is_deleted='N' and md5(id)=:id",array(":id"=>$memberId));
        $description =array();
        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>'  Deleted '.$type.' on '.$memberName['name'].' (',
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/members_details.php?id='.$memberId,
                'title'=> $memberName['rep_id'],
            ),
            'ac_message_2' =>')',
            );

        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $memberName['id'], 'Customer', $type . ' Deleted.',$_SESSION['admin']['name'],"",$desc);

        // setNotifySuccess("$type deleted successfully!");
        $response['message'] = $type.' deleted successfully!';
        $response['status'] = 'success';
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $memberId  = $_POST['memberId'];
    $memberName  = $_POST['memberName'];
    $memberRepid  = $_POST['rep_id'];
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
    $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));

    $validate->string(array('required' => true, 'field' => 'interaction_id', 'value' => $interaction_id), array('required' => 'Please select '.$type.' type.'));

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
                    'ac_message_1' =>'  Updated '.$type.' on '.$memberName.' (',
                    'ac_red_2'=>array(
                      'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($memberId),
                      'title'=> $memberRepid,
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

                if(!empty($insert_product)){
                    $inserted_proructs = $pdo->selectOne("SELECT GROUP_CONCAT(name) as inserted_products from prd_main where id IN(".implode(',',$insert_product).")");
                }
                $deleted_prd = !empty($deleted_proructs['deleted_products']) ? 'Deleted Products : '.$deleted_proructs['deleted_products'] : '';
                $inserted_prd = !empty($inserted_proructs['inserted_products']) ? 'Added Products : '.$inserted_proructs['inserted_products'] : '';
                $interaction_update = $interaction_old_type != $interaction_type ? 'From '.$interaction_old_type.' To '.$interaction_type  : 'No updates';

                $update_activity['key_value'] = array(
                    "desc_arr" => array(
                        "Type" => $interaction_update,
                        // 'From '.checkIsset($updated_details['description']).' To '.$description,
                        "products" => $deleted_prd!='' || $inserted_prd!='' ? $deleted_prd .'<br>'.$inserted_prd : 'No updates',
                    ),
                );
                $update_activity["ac_description_link"] = array(
                    'From'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>checkIsset($updated_details['description']),'data-encode'=>'no'),
                    'To'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Description','data-desc'=>$description,'data-encode'=>'no'),
                );
                $desc=json_encode($update_activity);
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $memberId, 'Customer', $type . ' Updated.',$_SESSION['admin']['name'],"",$desc);

                // setNotifySuccess("$type updated successfully!",true);
                $response['status'] = 'success';
                $response['message'] = $type.' updated successfully!';

            }else{
                $ins_param = array(
                    "admin_id" => $admin_id,
                    "user_id" => $memberId,
                    "interaction_id" => $interaction_id,
                    "description" => $description,
                    "updated_at" => 'msqlfunc_NOW()',
                    "created_at" => 'msqlfunc_NOW()',
                );
                if($type == 'Claim'){
                    $ins_param['is_claim'] = 'Y';
                }
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
                // setNotifySuccess("$type added successfully!",true);
                $response['message'] = $type.' added successfully!';
                $response['status'] = 'success';

                $description = array();
                $description['ac_message'] = array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>'  created '.$type.' on '.$memberName.' (',
                    'ac_red_2'=>array(
                      'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($memberId),
                      'title'=> $memberRepid,
                    ),
                    'ac_message_2' =>')',
                  );


                $desc=json_encode($description);
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $memberId, 'Customer', $type.' Created.',$_SESSION['admin']['name'],"",$desc);
                if(!empty($create_etickets)){
                    include_once __DIR__ . '/../includes/function.class.php';
                    $functionList = new functionsList();
                    $sessionArr['admin'] = $_SESSION['admin'];
                    $eticket_description .= '<p>'.$_SESSION['admin']['display_id'].' - '.$_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname'].'</p>';
                    $real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                    $eTicketArr = $functionList->createNewTicket($sessionArr,$group_id,$subject,$assigne_admins,$eticket_description,$memberId,'customer',$real_ip_address,array(),'notes');
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
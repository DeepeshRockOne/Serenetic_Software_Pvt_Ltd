<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array();
$validate = new Validation();
$res = array();
$res["status"] = "fail";
$res['msg'] = "Something went wrong!";

$action = checkIsset($_POST['action']);
$select_type = checkIsset($_POST['select_type']);

$join_range = checkIsset($_POST['join_range']);
$fromdate = checkIsset($_POST["fromdate"]);
$todate = checkIsset($_POST["todate"]);
$added_date = checkIsset($_POST["added_date"]);

$orders = checkIsset($_POST['orders']);
$products = checkIsset($_POST['products'],'arr');
$period = checkIsset($_POST['period']);

$validate->string(array('required' => true, 'field' => 'select_type', 'value' => $select_type), array('required' => 'Please Select Order Type.'));

if(!empty($select_type)){
    if(in_array($select_type,array('specific_date_range'))){
        $validate->string(array('required' => true, 'field' => 'join_range', 'value' => $join_range), array('required' => 'Please select Order Date.'));
        if($join_range == "Range" && (empty($todate) || empty($fromdate))){
            $validate->setError("from_to","Please select From Date and To Date.");  
        }else{
            $diff = date_diff(date_create($fromdate),date_create($todate));
            $days = $diff->format("%R%a");
            if($days > 60){
                $validate->setError("from_to","Please select Range less then or equals to 60 days.");  
            }
            if($days < 0){
                $validate->setError("from_to","Please select Range greater then or equals to 1 days.");  
            }
        }
        if($join_range != "Range" && (empty($added_date))){
            $validate->setError("added_date","Please select Date."); 
        }
    }else if(in_array($select_type,array('specific_order'))){
        if(empty($orders)){
            $validate->setError("specific_order","Please select any Orders."); 
        }
    }else if($select_type == 'all_order_specific_product'){
        if(empty($products)){
            $validate->setError("products","Please select any Products."); 
        }
    }
}

$validate->string(array('required' => true, 'field' => 'period', 'value' => $period), array('required' => 'Please select Regenerate Commission Period.')); 

if($validate->isValid()){
    if($action == "validateOrder"){
        $res['status'] = 'validateOrder';
        $res['msg'] = "Fetching Orders";
        echo json_encode($res);
        exit;
    }else{
        $incr = '';
        $sch_params = array();

        if($select_type == "specific_date_range"){
            if($join_range == "Range" && $fromdate!='' && $todate!=''){
                $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
                $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
                $incr.=" AND DATE(o.created_at) >= :fromdate AND DATE(o.created_at) <= :todate";
            }else if($join_range == "Exactly" && $added_date!=''){
                    $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date));
                    $incr.=" AND DATE(o.created_at) = :fromdate";
            }else if($join_range == "Before" && $added_date!=''){
                    $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date));
                    $incr.=" AND DATE(o.created_at) < :fromdate";
            }else if($join_range == "After" && $added_date!=''){
                    $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date));
                    $incr.=" AND DATE(o.created_at) > :fromdate";
            }
        }else if($select_type == "specific_order"){
            $incr.=" AND o.id IN(".$orders.") ";
        }else if($select_type == "all_order_specific_product"){
            $incr.=" AND od.product_id IN(".implode(',',$products).") ";
        }

        $insParams = array(
            "admin_id" => $_SESSION["admin"]["id"],
            "comm_period" => $period,
            "status" => "Processing",
            "is_process_active" => "Y",
        );
        if(!empty($incr)){
            $insParams["query_string"] = $incr;
        }
        if(!empty($sch_params)){
            $insParams["query_params"] = json_encode($sch_params);
        }   
        $insId = $pdo->insert("regenerated_commission",$insParams);

            if($select_type == "specific_date_range"){
                $tmp = "";
                if($join_range == "Range"){
                    $tmp = getCustomDate($fromdate) .' - '. getCustomDate($todate);
                }else{
                    $tmp = getCustomDate($added_date);
                }
                $ac_message_1 = " regenerated commissions for all orders within a specific date range ".$tmp;
            }else if($select_type == "specific_order"){
                $resOrder = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(display_id)) as orderDispIds FROM orders WHERE id IN(".$orders.")");
                $ac_message_1 = " regenerated commissions for specific orders ".$resOrder["orderDispIds"];;
            }else if($select_type == "all_order_specific_product"){
                $resProducts = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(name)) as prdNames,GROUP_CONCAT(DISTINCT(product_code)) as prdCodes FROM prd_main WHERE id IN(".implode(',',$products).")");
                $ac_message_1 = " regenerated commissions for all orders with a specific product ".$resProducts["prdNames"];
            }

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
              ),
              'ac_message_1' => $ac_message_1,
            );
            activity_feed(3, $_SESSION['admin']['id'], 'Admin',$insId, 'regenerated_commission',"Regenerate Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
            
            $res["status"] = "success";
            $res["msg"] = "Commission Regenerate Request Added Successfully!";
            header('Content-type: application/json');
            echo json_encode($res);

            add_commission_request('regenerate_commissions',array('regenerate_comm_id' => $insId));
            exit;
    }
}else{
    $errors = $validate->getErrors();
    $res['status'] = 'errors';
    $res['errors'] = $errors;
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
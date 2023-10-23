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

$start_date = !empty($_POST["start_date"]) ? $_POST["start_date"] : '';
$end_date = !empty($_POST["end_date"]) ? $_POST["end_date"] : '';

$sel_orders = checkIsset($_POST['orders']);
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
        if(empty($sel_orders)){
            $validate->setError("specific_order","Please select any Orders."); 
        }
    } else if($select_type == 'all_order_specific_product'){
        if(empty($products)){
            $validate->setError("products","Please select any Products."); 
        }
    }
}
$validate->string(array('required' => true, 'field' => 'period', 'value' => $period), array('required' => 'Please select Regenerate Payable Period.')); 

if($validate->isValid()){
    if($action == "validateOrder"){
        $res['status'] = 'validateOrder';
        $res['msg'] = "Fetching Orders";
        echo json_encode($res);
        exit;
    }else{
        $incr = $date_range = $ac_message_1 = '';
        $sch_params = array();

        if(!empty($join_range)) {
            if($join_range =='Range'){
                $date_range = ' Range : '. $fromdate . ' - ' . $todate;
            }else if(!empty($added_date)){
                $date_range = ' ' . $join_range . ' : ' . $added_date;
            }else{
                $date_range = ' Period Earned';
            }
        }
        if($period == 'current_period'){
            $date_range .= ' <br> Current Period : ' . $start_date . ' - ' . $end_date;
        }else if($period == 'period_earned'){
            $date_range .= ' <br> Period Earned.';
        }

        if($select_type == "specific_date_range"){
            if($join_range == "Range" && $fromdate!='' && $todate!=''){
                $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate)) . ' 00:00:00';
                $sch_params[':todate'] = date("Y-m-d",strtotime($todate)) . ' 23:59:59';
                $incr.=" AND o.created_at >= :fromdate AND o.created_at <= :todate";
            }else if($join_range == "Exactly" && $added_date!=''){
                $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date)) . ' 00:00:00';
                $sch_params[':todate'] = date("Y-m-d",strtotime($added_date)) . ' 23:59:59';
                $incr.=" AND o.created_at >= :fromdate AND o.created_at <= :todate";
            }else if($join_range == "Before" && $added_date!=''){
                $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date)) . ' 00:00:00';
                $incr.=" AND o.created_at < :fromdate";
            }else if($join_range == "After" && $added_date!=''){
                $sch_params[':fromdate'] = date("Y-m-d",strtotime($added_date)) . ' 23:59:59';
                $incr .= " AND o.created_at > :fromdate";
            }

            $ac_message_1 = " regenerated payables for all orders within a specific date range" . $date_range;
        }else if($select_type == "specific_order"){
            $incr .= " AND o.id IN(".$sel_orders.") ";
            $resOrder = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(display_id)) as orderDispIds FROM orders WHERE id IN(" .$sel_orders.")");

            $ac_message_1 = " regenerated payables for specific orders " . $resOrder["orderDispIds"] . '<br>Date Range -' . $date_range;
        }else if($select_type == "all_order_specific_product"){
            $incr .= " AND od.product_id IN(".implode(',',$products).") ";

            $resProducts = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(name)) as prdNames,GROUP_CONCAT(DISTINCT(product_code)) as prdCodes FROM prd_main WHERE id IN(".implode(',',$products).")");
            $ac_message_1 = " regenerated payables for all orders with a specific product " . $resProducts["prdNames"] . '<br>Date Range -' . $date_range;
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
        $insId = $pdo->insert("regenerated_payable",$insParams);

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' => $ac_message_1,
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$insId, 'regenerated_payable',"Regenerate Payables", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        
        $res["status"] = "success";
        $res["msg"] = "Payable Regenerate Request Added Successfully!";
        header('Content-type: application/json');
        echo json_encode($res);

        add_commission_request('regenerate_payables',array('regenerate_payable_id' => $insId));
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
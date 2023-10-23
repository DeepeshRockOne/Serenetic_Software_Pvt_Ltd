<?php
include_once __DIR__ . '/includes/connect.php';

$validate = new Validation();
$res = array();
$customer_id = $_GET['customer_id'];
$product_id = $_GET['product_id'];
$prd_plan = $_GET['prd_plan'];

$prd_row = get_product_row($product_id);

$is_exist = array();
$is_exist_master = array();
if (!empty($_REQUEST['dependent_id'])) {
    $dependent_id = $_REQUEST['dependent_id'];
    $sel = "SELECT * FROM customer_dependent WHERE id !=:dependent_id AND customer_id=:customer_id AND product_id=:product_id AND is_deleted='N'";
    $arr = array(":customer_id" => $customer_id, ":product_id" => $product_id, ":dependent_id" => $dependent_id);
    $resDep = $pdo->select($sel, $arr);
} else {
    $sel = "SELECT * FROM customer_dependent WHERE customer_id=:customer_id AND product_id=:product_id AND is_deleted='N'";
    $arr = array(":customer_id" => $customer_id, ":product_id" => $product_id);
    $resDep = $pdo->select($sel, $arr);
}
$selCDP = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N'";
$arrCDP = array(":customer_id" => $customer_id);
$resDepCDP = $pdo->select($selCDP, $arrCDP);

if (count($resDep) > 0) {
    $spouse_dep = 0;
    $child_dep = 0;
    foreach ($resDep as $row) {
        if (strtolower($row['relation']) == 'wife' || strtolower($row['relation']) == 'husband') {
            $spouse_dep++;
            array_push($is_exist, 'S');
        }
        if (strtolower($row['relation']) == 'son' || strtolower($row['relation']) == 'daughter') {
            array_push($is_exist, 'C');
            $child_dep++;
        }
    }
    $dependant_cnt = $spouse_dep + $child_dep;

    if ($prd_plan == 4) {
        $family_plan_rule = $prd_row['family_plan_rule'];

        if($family_plan_rule=="Spouse And Child"){
            if ($spouse_dep > 0 && $child_dep > 0) {
                $res['status'] = "exist";
            } else {
                $res['status'] = "not_exist";
            }
            
        } else if($family_plan_rule=="Minimum One Dependent"){
            if ($dependant_cnt < 1) {
                $res['status'] = "not_exist";
            } else {
                $res['status'] = "exist";
            }

        } else if($family_plan_rule=="Minimum Two Dependent"){
            if($dependant_cnt < 2){
                $res['status'] = "not_exist";
            } else {
                $res['status'] = "exist";
            }
        }
    } elseif ($prd_plan == 2) {
        if ($child_dep == 0) {
            $res['status'] = "not_exist";
        } else {
            $res['status'] = "exist";
        }
    } elseif ($prd_plan == 3) {
        if ($spouse_dep == 0) {
            $res['status'] = "not_exist";
        } else {
            $res['status'] = "exist";
        }
    } elseif ($prd_plan == 5) {
        if ($dependant_cnt < 1) {
            $res['status'] = "not_exist";
        } else {
            $res['status'] = "exist";
        }
    } else {
        $res['status'] = "exist";
    }
} else {
    $res['status'] = "not_exist";
}

if (!empty($_REQUEST['dependent_id'])) {
    if ($res['status'] == "not_exist") {
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }
}

if ($res['status'] == "not_exist") {
    if ($resDepCDP) {      
        $res['prd_plan'] = $prd_plan;

        $spouse_dep = 0;
        $child_dep = 0;
        foreach ($resDepCDP as $row) {
            if (strtolower($row['relation']) == 'wife' || strtolower($row['relation']) == 'husband') {
                array_push($is_exist, 'S');
                $spouse_dep++;
            }
            if (strtolower($row['relation']) == 'son' || strtolower($row['relation']) == 'daughter') {
                array_push($is_exist, 'C');
                $child_dep++;
            }
        }
        $dependant_cnt = $spouse_dep + $child_dep;

        if ($prd_plan == 4) {
            $family_plan_rule = $prd_row['family_plan_rule'];

            if($family_plan_rule=="Spouse And Child"){
                if ($spouse_dep > 0 && $child_dep > 0) {
                    $res['status'] = "exist_in_master";
                } else {
                    $res['status'] = "not_exist";
                }
                
            } else if($family_plan_rule=="Minimum One Dependent"){
                if ($dependant_cnt < 1) {
                    $res['status'] = "not_exist";
                } else {
                    $res['status'] = "exist_in_master";
                }

            } else if($family_plan_rule=="Minimum Two Dependent"){
                if($dependant_cnt < 2){
                    $res['status'] = "not_exist";
                } else {
                    $res['status'] = "exist_in_master";
                }
            }
        } elseif ($prd_plan == 2) {
            if ($child_dep == 0) {
                $res['status'] = "not_exist";
            } else {
                $res['status'] = "exist_in_master";
            }
        } elseif ($prd_plan == 3) {
            if ($spouse_dep == 0) {
                $res['status'] = "not_exist";
            } else {
                $res['status'] = "exist_in_master";
            }
        } elseif ($prd_plan == 5) {
            if ($dependant_cnt < 1) {
                $res['status'] = "not_exist";
            } else {
                $res['status'] = "exist_in_master";
            }
        } else {
            $res['status'] = "exist_in_master";
        }
    } else {
        $res['status'] = "not_exist";
    }
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
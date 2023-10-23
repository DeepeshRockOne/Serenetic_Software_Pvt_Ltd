<?php
include_once __DIR__ . '/includes/connect.php';

    $dont_show_chk = $_POST['dont_show_chk'];
    $do_not_show_type = $_POST['type'];

    echo $do_not_show_type;

    $update_param = array();

    if($do_not_show_type == 'license_expired'){
        $update_param['not_show_license_expired'] = 'Y';
        $_SESSION["agents"]["not_show_license_expired"] = 'Y';
    }else if($do_not_show_type == 'license_expiring'){
        $update_param['not_show_license_expiring'] = 'Y';
        $_SESSION["agents"]["not_show_license_expiring"] = 'Y';
    }else if($do_not_show_type == 'eo_expired'){
        $update_param['not_show_eo_expired'] = 'Y';
        $_SESSION["agents"]["not_show_eo_expired"] = 'Y';
        $dont_show_chk = 'Y';
    }else if($do_not_show_type == 'eo_expiring'){
        $update_param['not_show_eo_expiring'] = 'Y';
        $_SESSION["agents"]["not_show_eo_expiring"] = 'Y';
    }
    
    $where = array(
        "clause" => "customer_id=:customer_id",
        "params" => array(
            "customer_id" => $_SESSION['agents']['id']
        ),
    );
    if(!empty($update_param) && $dont_show_chk == 'Y'){
        $pdo->update("customer_settings",$update_param,$where);
    }
    $response = array("success");


header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
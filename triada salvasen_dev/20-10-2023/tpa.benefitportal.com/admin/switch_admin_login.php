<?php

include_once (__DIR__) . '/includes/connect.php';

if (!empty($_REQUEST['id'])) {
    $selSql = "SELECT *, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as definedPassword FROM admin WHERE type='Super Admin' AND md5(id)=:id";
    $params = array(":id" => makeSafe($_REQUEST['id']));
    $custRow = $pdo->selectOne($selSql, $params);
    
    if (count($custRow) > 0) {
        $_SESSION['admin']['id'] = $custRow['id'];
        $_SESSION['admin']['name'] = $custRow['fname'] . $custRow['lname'];
        //$_SESSION['customer']['lname'] = $custRow['lname'];
        //$_SESSION['customer']['user_name'] = $custRow['user_name'];
        $_SESSION['admin']['email'] = $custRow['email'];
        $_SESSION['admin']['photo'] = $custRow['photo'];
        $_SESSION['admin']['type'] = $custRow['type'];
        //$_SESSION['customer']['cell_phone'] = $custRow['cell_phone'];
        //$_SESSION['customer']['rank'] = $custRow['rank'];
        //$_SESSION['customer']['email'] = $custRow['email'];
        //$_SESSION['customer']['site_load'] = $custRow['country_id'];
        //$_SESSION['customer']['profile_image'] = $custRow['profile_image'];
        $_SESSION['admin']['secret'] = base64_encode($password);
        $_SESSION['admin']['created_at'] = $custRow['created_at'];
        $_SESSION['admin']['updated_at'] = $custRow['updated_at'];
        redirect("dashboard.php");
    }
}
?>

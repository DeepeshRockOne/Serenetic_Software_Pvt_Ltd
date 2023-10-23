<?php
include_once __DIR__ . '/includes/connect.php';
$response = array();
$customer_id = $_REQUEST['customer_id'];
$customer_sql = "SELECT id,fname,email,lname,cell_phone,birth_date,gender,address,address_2,city,state,zip,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') AS ssn_itn_number FROM customer WHERE id=:customer_id";
$customer_row = $pdo->selectOne($customer_sql, array(':customer_id' => $customer_id));
if (!empty($customer_row)) {
    if(!empty(strtotime($customer_row['birth_date']))){
        $customer_row['birth_date'] = date('m/d/Y',strtotime($customer_row['birth_date']));
    }
    $response['customer_row'] = $customer_row;
    $response['status'] = "success";
} else {
    $response['status'] = "fail";
    $response['error_message'] = "Member not found";
}
echo json_encode($response);
dbConnectionClose();
exit;
?>
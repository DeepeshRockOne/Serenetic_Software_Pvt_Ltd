<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';

$apiCall = new Api();
$userName = !empty($_GET['user_name']) ? $_GET['user_name'] : '';
if(empty($userName)) {
    $pb_row = array();
    if (isset($_SESSION['agents']['id']) || isset($_SESSION['groups']['id']) || isset($_SESSION['admin']['id']) ) {
        $page_builder_id = $_GET['page_builder_id'];
        $data = [
            'pageBuilderId' => $page_builder_id,
            'api_key' => 'pageBuilderDetails'
        ];
        $apiResponse = $apiCall->ajaxApiCall($data,true);
        
        if($apiResponse['status'] == 'Success'){
            $pb_row = $apiResponse['data']['pageBuilderDetails'];
            $userName = $pb_row['user_name'];
        }
    }
    if(empty($pb_row)) {
        setNotifyError("Sorry! You have no rights to access this page.");
        redirect('404.php',true);
        exit();
    }
}

$product_ids = !empty($pb_row['product_ids']) ? $pb_row['product_ids'] : '';
$prd_category_res = array();
$sponsor_id = 0;
if(!empty($product_ids)) {

    $dataProducts = [
        'productIDs' => $product_ids,
        'agentID' => $pb_row['agent_id'],
        'api_key' => 'pageBuilderProductDetails'
    ];
    $apiResponseProducts = $apiCall->ajaxApiCall($dataProducts,true);

    if(!empty($apiResponseProducts['status']) && $apiResponseProducts['status'] == 'Success'){
        $prd_category_res = $apiResponseProducts['data']['pageBuilderProductResult'];
    }
    $sponsor_id = $pb_row['agent_id'];
}

$template = 'group_prd_preview.inc.php';
include 'tmpl/' . $template;
?>
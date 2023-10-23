<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';

has_access(6);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "User Groups";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';
$breadcrumbes[3]['title'] = "Manage Groups";
$breadcrumbes[3]['link'] = 'manage_groups.php';

$ajaxApiCall = new Api();
$groupIds = !empty($_GET['id']) ? $_GET['id'] : '';
$selectedBundle = '';
$SkipComparison = '';

    if(!empty($groupIds)){
        //get step-1 bundle data
        $params = array(
            'id' =>$groupIds,
            'api_key' =>'editBundleRecommendationList'
        );
        $bundleRes = $ajaxApiCall->ajaxApiCall($params,true);
        $fetch_rows = !empty($bundleRes['data']) ? $bundleRes['data']: array();
        $total_rows = count($fetch_rows);
        $data = !empty($fetch_rows['editRecordDetails']) ? $fetch_rows['editRecordDetails'] : [];
        $BundleCount= count($data);
       
        $groupId = !empty($fetch_rows) && !empty($fetch_rows['groupId']) ? $fetch_rows['groupId'] : 0;
        // /get step-1 bundle data

        //get all bundle
            $BundleParam = array('api_key' => 'getBundle','group_id'=> $groupId);
            $GetAllBundle =  $ajaxApiCall->ajaxApiCall($BundleParam,true);
            $GetAllBundleDetails = !empty($GetAllBundle['data']) ? $GetAllBundle['data'] : array();
        // /get all bundle

        //get step-2 question details
        if(!empty($data)){
                $QuestionsParamas = array(
                'id' =>$groupIds,
                'api_key' =>'getBundleQuestion'
            );
            $GetBundleQuestion =  $ajaxApiCall->ajaxApiCall($QuestionsParamas,true);
            $GetBundleQuestionDetails = !empty($GetBundleQuestion['data']) ? $GetBundleQuestion['data'] : array();
            
        }
        // /get step-2 question details

        //get ste-3 compare row data
        $Apiparams = array(
            'id' =>$groupIds,
            'api_key' =>'getAllCompareBundleData'
        );
        $getAllcompare = $ajaxApiCall->ajaxApiCall($Apiparams,true);
        $getAllCompareData = !empty($getAllcompare['data']) ? $getAllcompare['data'] : array();
        $bundleInformId = '';
        $BundleDetailsArr = $data;
        $bundleIdsArr = [];
        foreach ($BundleDetailsArr as $K => $V) {
           $bundleIdsArr[]=$V['id'];
        }
        if(!empty($getAllCompareData)){
            foreach ($getAllCompareData as $key => $value) {   
               $BundleData = json_decode($value['bundle_comparison_lable'],true);
            }
        }

        // /get ste-3 compare row data

        //get fimal save recommandation information
         $GetBundleInfo = array(
            'id' =>$groupIds,
            'api_key' =>'getBundleInformation'
        );
        $getBundleInformationDetails = $ajaxApiCall->ajaxApiCall($GetBundleInfo,true);
        $getBundleInformationDetails = !empty($getBundleInformationDetails) ? $getBundleInformationDetails: array();
        // pre_print($getBundleInformationDetails);
        if(!empty($getBundleInformationDetails)){
            $selectedBundle = $getBundleInformationDetails['top_recommanded'];
            $SkipComparison = $getBundleInformationDetails['skip_comparison'];
            $bundleInformId = $getBundleInformationDetails['bundle_information_id'];
        }
         
        //  /get final save recommandation information
    }else{
        $BundleCount = 0;
    }  
//******** get products by group id ******//
if(isset($_POST['api_key']) && $_POST['api_key'] == 'getGroupProduct' && !empty($_POST['groupId'])){
    
    // $filter_prd_res = get_active_global_products_for_filter($_POST['groupId'],false,false,true,true);
    $apiResponse = $ajaxApiCall->ajaxApiCall($_POST,true);
    $filter_prd_res = !empty($apiResponse['data']) ? $apiResponse['data'] : array();
    $option_html = '';
    if(!empty($filter_prd_res)){
        foreach($filter_prd_res as $key =>  $company){                       
            $option_html.='<optgroup label='.$key.'>';
                foreach ($company as $pkey =>$row) {
                    $option_html.='<option value="'.$row['id'].'">'.$row['name'] .' ('.$row['product_code'].')</option>';
                }
            $option_html.='</optgroup>';
        }
    }

    $response['option_html'] = $option_html;
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
//******** /get products by group id******//


//********get bundle Groups******//
$params = array(
    'id' =>$groupIds,
    'api_key' =>'getBundleGroup'
);
$GetBundleGroup =  $ajaxApiCall->ajaxApiCall($params,true);
$GetGroupDetails = !empty($GetBundleGroup['data']) ? $GetBundleGroup['data'] : array();
//******** /get bundle Groups******//
$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
                'thirdparty/bower_components/moment/moment.js');


$template = 'add_bundle_recommendation.inc.php';
include_once 'layout/end.inc.php';
?>

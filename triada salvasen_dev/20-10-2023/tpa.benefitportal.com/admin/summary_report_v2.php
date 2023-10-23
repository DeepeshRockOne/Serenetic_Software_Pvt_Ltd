<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once "../includes/reporting_function.php";
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Reports';
$breadcrumbes[1]['link'] = 'reports.php';
$breadcrumbes[2]['title'] = 'Top Performing Summary';

$is_renewal = 'N';
$page_title = "Top Performing Summary";
if (isset($_REQUEST["is_ajax"])) {
    $type = $_REQUEST["type"];
    $from_date = isset($_REQUEST["from_date"]) ? $_REQUEST["from_date"] : "";
    $to_date = isset($_REQUEST["to_date"]) ? $_REQUEST["to_date"] : "";
    if ($type != 'Custom Date') {
        $searchArray = getSearchArray($type, $from_date, $to_date);
    } else {
        $getfromdate = date('m/d/Y', strtotime($from_date));
        $gettodate = date('m/d/Y', strtotime($to_date));
        $searchArray = array("getfromdate" => $getfromdate, "gettodate" => $gettodate);
    }
    include 'tmpl/summary_report_v2.inc.php';
    exit;
} 
// elseif (isset($_REQUEST["export"])) {

//     $type = $_REQUEST["type"];
//     $from_date = isset($_REQUEST["from_date"]) ? $_REQUEST["from_date"] : "";
//     $to_date = isset($_REQUEST["to_date"]) ? $_REQUEST["to_date"] : "";

//     if ($type != 'Custom Date') {
//         $searchArray = getSearchArray($type, $from_date, $to_date);
//     } else {
//         $getfromdate = date('m/d/Y', strtotime($from_date));
//         $gettodate = date('m/d/Y', strtotime($to_date));
//         $searchArray = array("getfromdate" => $getfromdate, "gettodate" => $gettodate);
//     }

//     $content = '';
//     /*-------- Heading ----------*/
//     $content .= "Report Date" . $csv_seprator;
//     if(strtotime($searchArray['getfromdate']) == 0) {
//         $content .= 'All Time' . $csv_seprator;
//     } elseif(strtotime($searchArray['getfromdate']) ==  strtotime($searchArray['gettodate'])) {
//         $content .= date('F d Y', strtotime($searchArray['getfromdate'])) . $csv_seprator;
//     } else {
//         $content .= date('F d Y', strtotime($searchArray['getfromdate'])) .' To '. date('F d Y', strtotime($searchArray['gettodate'])) . $csv_seprator;
//     }
//     $content .= $csv_line;

//     /*-------- Top Summary ----------*/
//     $BusinessSummaryData = getBusinessSummaryDataV1('N',$searchArray);
    
//     $content .= "Total Premium" . $csv_seprator;
//     $content .= valid_cell_value(displayAmount($BusinessSummaryData['TotalPremium'],2)) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Avg Premium Per Holder" . $csv_seprator;
//     $content .= valid_cell_value(displayAmount($BusinessSummaryData['AvgPremiumPerHolder'],2)) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Total Policy Holders" . $csv_seprator;
//     $content .= valid_cell_value($BusinessSummaryData['TotalPolicyHolder']) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Total Policies" . $csv_seprator;
//     $content .= valid_cell_value($BusinessSummaryData['TotalPolicies']) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Avg Policies Per Holder" . $csv_seprator;
//     $content .= valid_cell_value($BusinessSummaryData['AvgPoliciesPerHolder']) . $csv_seprator;

//     $content .= $csv_line;
//     $content .= $csv_line;

//     /*-------- PER PRODUCT ----------*/
//     $content .= "Product Name" . $csv_seprator;
//     $content .= "Premiums" . $csv_seprator;
//     $content .= "Policy Holders" . $csv_seprator;
//     $content .= $csv_line;

//     $per_products_res = new_business_summary_per_products($searchArray);
//     if(!empty($per_products_res)) {
//         foreach ($per_products_res as $key => $per_products_row) {
//             $content .= valid_cell_value($per_products_row['product_id'].' '.$per_products_row['product_name'].' ('.$per_products_row['product_code'].')' . $csv_seprator);
//             $content .= valid_cell_value(displayAmount($per_products_row['total_premiums'],2)) . $csv_seprator;
//             $content .= valid_cell_value($per_products_row['policy_holders']) . $csv_seprator;
//             $content .= $csv_line;            
//         }
//     } else {
//         $content .= "No record(s) found" . $csv_seprator;
//         $content .= $csv_line;    
//     }
//     $content .= $csv_line; 
//     $content .= $csv_line; 

//     /*-------- PER AGENT ----------*/
//     $content .= "Agent Name" . $csv_seprator;
//     $content .= "Total Premium" . $csv_seprator;
//     $content .= "Avg Premium Per Holder" . $csv_seprator;
//     $content .= "Total Policy Holders" . $csv_seprator;
//     $content .= "Total Policies" . $csv_seprator;
//     $content .= "Avg Policies Per Holder" . $csv_seprator;
//     $content .= $csv_line;

//     $per_agents_res = new_business_summary_per_agents($searchArray);
//     if(!empty($per_agents_res)) {
//         foreach ($per_agents_res as $key => $per_agents_row) {

//             if($per_agents_row['total_premiums'] > 0) {
//                 $per_agents_row['avg_premium_per_holder'] = $per_agents_row['total_premiums']/$per_agents_row['total_policy_holders'];
//             } else {
//                 $per_agents_row['avg_premium_per_holder'] = 0;
//             }

//             if($per_agents_row['total_policies'] > 0) {
//                 $per_agents_row['avg_policies_per_holder'] = number_format((float)$per_agents_row['total_policies']/$per_agents_row['total_policy_holders'], 2, '.', '');
//             } else {
//                 $per_agents_row['avg_policies_per_holder'] = 0.00;
//             }

//             $content .= valid_cell_value($per_agents_row['agent_name'].' ('.$per_agents_row['agent_display_id'].')') . $csv_seprator;
//             $content .= valid_cell_value(displayAmount($per_agents_row['total_premiums'],2)) . $csv_seprator;
//             $content .= valid_cell_value(displayAmount($per_agents_row['avg_premium_per_holder'],2)) . $csv_seprator;
//             $content .= valid_cell_value($per_agents_row['total_policy_holders']) . $csv_seprator;
//             $content .= valid_cell_value($per_agents_row['total_policies']) . $csv_seprator;
//             $content .= $per_agents_row['avg_policies_per_holder'] . $csv_seprator;
//             $content .= $csv_line;            
//         }
//     } else {
//         $content .= "No record(s) found" . $csv_seprator;
//         $content .= $csv_line;    
//     }
//     $content .= $csv_line;
//     $content .= $csv_line;

//     /*-------- Commissions Summary ----------*/
//     $CommissionsSummaryData = getBusinessCommissionsSummaryData('N',$searchArray);
    
//     $content .= "Total Commission Earned" . $csv_seprator;
//     $content .= valid_cell_value(displayAmount($CommissionsSummaryData['TotalCommissions'],2)) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Commission" . $csv_seprator;
//     $content .= valid_cell_value(displayAmount($CommissionsSummaryData['NewBusinessCommissions'],2)) . $csv_seprator;
//     $content .= $csv_line;

//     $content .= "Advanced" . $csv_seprator;
//     $content .= valid_cell_value(displayAmount($CommissionsSummaryData['AdvanceCommissions'],2)) . $csv_seprator;
    
//     $content .= $csv_line;
//     $content .= $csv_line;
//     $content .= $csv_line;

//     /*-------- Commissions Per Agent ----------*/
//     $content .= "Agent Name" . $csv_seprator;
//     $content .= "Total Commission Earned" . $csv_seprator;
//     $content .= "Commission" . $csv_seprator;
//     $content .= "Advanced" . $csv_seprator;
//     $content .= $csv_line;
    
//     $comm_per_agents_res = new_business_commission_per_agents($searchArray);
//     if(!empty($comm_per_agents_res)) {
//         foreach ($comm_per_agents_res as $key => $comm_per_agents_row) {
//             $content .= valid_cell_value($comm_per_agents_row['agent_name'].' ('.$comm_per_agents_row['agent_display_id'].')') . $csv_seprator;
//             $content .= valid_cell_value(displayAmount($comm_per_agents_row['total_payment'],2)) . $csv_seprator;
//             $content .= valid_cell_value(displayAmount($comm_per_agents_row['new_business_payment'],2)). $csv_seprator;
//             $content .= valid_cell_value(displayAmount($comm_per_agents_row['advance_payment'],2)) . $csv_seprator;
//             $content .= $csv_line;            
//         }
//     } else {
//         $content .= "No record(s) found" . $csv_seprator;
//         $content .= $csv_line;    
//     }

//     header('Content-type: application/vnd.ms-excel');
//     header('Content-disposition: attachment;filename=NewBusinessSummary.xls');
//     echo $content;
//     exit();
// }
$exStylesheets = array();
$exJs = array('thirdparty/bower_components/moment/moment.js');
$template = 'summary_report_v2.inc.php';
include_once 'layout/end.inc.php';
?>
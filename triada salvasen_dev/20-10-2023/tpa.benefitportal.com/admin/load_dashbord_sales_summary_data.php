<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once "../includes/reporting_function.php";

if(isset($_POST['date'])) {
	$dates = $_POST['date'];
} else {
	$dates = array("getfromdate" => date("Y-m-d"),"gettodate" => date("Y-m-d"));
}

$filter_date = $_POST['filter_date'];
if($filter_date == 'Custom Date'){
	$searchArray = getSearchArray($filter_date,$dates['getfromdate'],$dates['gettodate']);
}else{
	$searchArray = getSearchArray($filter_date);
}

$summary = getDashboardSalesSummaryDataV2($searchArray);

$response_html = '';
if($summary){
	$response_html .= '<tr>';
  	$first_tooltip_data = '<span>New Business:</span>
						<span class="text-blue">'.displayAmount($summary['tooltip_NewBusiness'],2).'</span><br/>
						<span class="m-b-0">Renewals:</span>
						<span class="text-red">'.displayAmount($summary['tooltip_renewalBusiness'],2).'</span>';
	$second_tooltip_data = '<span>New Business:</span>
						<span class="text-blue">'.$summary['tooltip_avg_newbusiness'].' policies</span><br/>
						<span class="m-b-0">Renewal:</span>
						<span class="text-red">'.$summary['tooltip_avg_renewal_business'].' policies</span>';
	$third_tooltip_data = '<span>New Business:</span>
			<span class="text-blue">'.$summary['tooltip_avg_policy_new_business'].' policies</span><br/>
			<span class="m-b-0">Renewal:</span>
			<span class="text-red">'.$summary['tooltip_avg_policy_renewal_business'].' policies</span>';

	$tooltip_data = '<span>New Business:</span>
						<span class="text-blue">'.$summary['NewBusinessMember'].' members</span><br/>
						<span class="m-b-0">Termed:</span>
						<span class="text-red">'.($summary['TermedMember'] ? $summary['TermedMember'] : '0').' members</span>';

	$netfall_tooltip = '<span>Existing Products:</span>
								<span class="text-blue">'.$summary['fallOffDetails']['S'].'</span> <br/>
								<span class="m-b-0">New Products:</span>
								<span class="text-blue">'.$summary['fallOffDetails']['N'].'</span> <br/>
								<span class="m-b-0">Termed Products:</span>
								<span class="text-blue">'.$summary['fallOffDetails']['T'].'</span> <br/>
								';

	$response_html .= '<td>'.displayAmount($summary['TotalPremium'],2).'</td>';
    $response_html .= '<td>'.$summary['NewBusinessMember'].'/'.displayAmount($summary['NewBusiness'],2).'</td>';
	$response_html .= '<td>'.$summary['RenewalsMember'].'/'.displayAmount($summary['Renewals'],2).'</td>';
    $response_html .= "<td><a  href='javascript:void(0);' class='tooltip-custom' data-toggle='tooltip' data-html='true' data-placement='top' data-container='body' data-title='".$first_tooltip_data."'>".displayAmount($summary['AvgPremiumPerHolder'],2)."</a></td>";
    $response_html .= '<td>'.($summary['TotalMember'] != '' ? $summary['TotalMember'] : 0) .'</td>';
    $response_html .= "<td><a  href='javascript:void(0);' class='tooltip-custom' data-toggle='tooltip' data-html='true' data-placement='top' data-container='body' data-title='".$second_tooltip_data."'>".($summary['TotalPolicies'] != '' ? $summary['TotalPolicies'] : 0) ."</a></td>";
    $response_html .= "<td><a  href='javascript:void(0);' class='tooltip-custom' data-toggle='tooltip' data-html='true' data-placement='top' data-container='body' data-title='".$third_tooltip_data."'>".$summary['AvgPoliciesPerMember']."</a></td>";
    $response_html .= "<td><a  href='javascript:void(0);' class='tooltip-custom' data-toggle='tooltip' data-html='true' data-placement='top' data-container='body' data-title='".$netfall_tooltip."'>".$summary['NetFallOff']."</a></td>";
	$response_html .= '</tr>';
} else {
	$response_html .= "<tr><td colspan='3' style='text-align:center;'>No record(s) found</td></tr>";
}

$res['html_string'] = $response_html;

if($filter_date == "Today") {
	$res['sales_summary_from_to_date'] = date('F j, Y',strtotime($searchArray['getfromdate']));	

} elseif($filter_date == "Yesterday") {
	$res['sales_summary_from_to_date'] = date('F j, Y', strtotime("yesterday"));

} elseif($filter_date == "Last 7 Days") {	
	$res['sales_summary_from_to_date'] = date('F',strtotime($searchArray['getfromdate'])).' '.date('j',strtotime($searchArray['getfromdate'])).'-'.date('j',strtotime($searchArray['gettodate'])).', '.date('Y',strtotime($searchArray['getfromdate']));	

} elseif($filter_date == "This Month") {
	$res['sales_summary_from_to_date'] = date('F Y',strtotime($searchArray['getfromdate']));

} elseif($filter_date == "Last Month") {
	$res['sales_summary_from_to_date'] = date('F Y',strtotime("-1 month"));

} elseif($filter_date == "This Year") {
	$res['sales_summary_from_to_date'] = date('Y',strtotime($searchArray['getfromdate']));

} elseif($filter_date == "Last Year") {
	$res['sales_summary_from_to_date'] = date('Y',strtotime("-1 year"));

} else {
	$res['sales_summary_from_to_date'] = date('F j, Y',strtotime($searchArray['getfromdate'])).' - '.date('F j, Y',strtotime($searchArray['gettodate']));
}

$res['from_date'] = date('F d, Y',strtotime($searchArray['getfromdate']));
$res['to_date'] = date('F d, Y',strtotime($searchArray['gettodate']));
$res['fall'] =$summary['fallOff'];
$res['fallOfDetails'] = $summary['fallOffDetails'];
echo json_encode($res);
dbConnectionClose();
exit();
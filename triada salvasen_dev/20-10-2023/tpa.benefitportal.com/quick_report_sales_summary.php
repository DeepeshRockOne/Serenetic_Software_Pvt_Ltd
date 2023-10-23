<?php
include_once __DIR__ . '/includes/connect.php'; 
include_once __DIR__ . "/includes/reporting_function.php";

$user_type = $_REQUEST["user_type"];

if(isset($_GET['action']) && $_GET['action'] == 'export_rps_data') {
    include_once __DIR__ . '/includes/aws_reporting_api_url.php';
    include_once __DIR__ . '/includes/export_report.class.php';
    
    $config_data = array();
    if($user_type == "Admin") {
        $config_data = array(
            'user_id' => $_SESSION['admin']['id'],
            'user_type' => 'Admin',
            'user_rep_id' => $_SESSION['admin']['display_id'],
            'user_profile_page' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'timezone' => $_SESSION['admin']['timezone'],
            'file_type' => 'EXCEL',
            'report_location' => 'quick_report_sales_summary',
            'report_key' => 'admin_payment_transaction_report',
        );
    } else if($user_type == "Agent") {
        $config_data = array(
            'user_id' => $_SESSION['agents']['id'],
            'user_type' => 'Agent',
            'user_rep_id' => $_SESSION['agents']['rep_id'],
            'user_profile_page' => $AGENT_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'timezone' => $_SESSION['agents']['timezone'],
            'file_type' => 'EXCEL',
            'report_location' => 'quick_report_sales_summary',
            'report_key' => 'admin_payment_transaction_report',
        );
    }
    $_POST['transaction_or_effective_date'] = 'transaction_date';
    $exportreport = new ExportReport(0,$config_data);
    $response = $exportreport->run();
    echo json_encode($response);
    exit();
}

if (isset($_REQUEST["is_ajax"])) {
    $join_range = isset($_POST['join_range'])?$_POST['join_range']:"";
    $fromdate = isset($_POST["fromdate"])?$_POST["fromdate"]:"";
    $todate = isset($_POST["todate"])?$_POST["todate"]:"";
    $added_date = isset($_POST["added_date"])?$_POST["added_date"]:"";
    $join_range = strtolower($join_range);

    $sch_params = array();
    $incr = "";
    
    $column_incr = " DATE(t.created_at) ";

    if($join_range == "range") {
        if ($fromdate != "") {
            $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
            $incr .= " AND $column_incr >= :from_date";
        }
        if ($todate != "") {
            $sch_params[':to_date'] = date('Y-m-d', strtotime($todate));
            $incr .= " AND $column_incr <= :to_date";
        }
    } else {
        if ($added_date != "") {
            if($join_range == 'exactly') {
                $sch_params[':from_date'] = date('Y-m-d', strtotime($added_date));
                $incr .= " AND $column_incr = :from_date";

            } else if($join_range == 'before') {
                $sch_params[':from_date'] = date('Y-m-d', strtotime($added_date));
                $incr .= " AND $column_incr < :from_date";

            } else if($join_range == 'after') {
                $sch_params[':from_date'] = date('Y-m-d', strtotime($added_date));
                $incr .= " AND $column_incr > :from_date";
            }
        }
    }
    
    $upline_incr = "";
    if($user_type == "Agent" && !empty($_SESSION['agents']['id'])) {
        $upline_incr = " JOIN customer as downline ON (t.customer_id = downline.id AND downline.upline_sponsors LIKE CONCAT('%,',".$_SESSION['agents']['id'].",',%') AND (downline.type='Customer' OR downline.type='Group'))";
    }

    $sql = "SELECT 
                SUM(IF(t.credit > 0,t.credit,t.debit)) AS TotalPremium,
                SUM((t.new_business_members + t.renewal_members)) AS TotalPolicyHolder,

                SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_total,0)) AS TotalNewApprovedPremium,
                SUM(IF((t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order'),t.new_business_members,0)) AS TotalNewApprovedPolicyHolder,

                SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_total,0)) AS TotalRenewalApprovedPremium,
                SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'List Bill Order'),t.renewal_members,0)) AS TotalRenewalApprovedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Approved',t.credit,0)) AS TotalApprovedPremium,
                SUM(IF(t.transaction_status = 'Payment Approved',(t.new_business_members + t.renewal_members),0)) AS TotalApprovedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Declined',t.debit,0)) AS TotalDeclinedPremium,
                SUM(IF(t.transaction_status = 'Payment Declined',(t.new_business_members + t.renewal_members),0)) AS TotalDeclinedPolicyHolder,

                SUM(IF(t.transaction_status = 'Refund',t.debit,0)) AS TotalRefundedPremium,
                SUM(IF(t.transaction_status = 'Refund',(t.new_business_members + t.renewal_members),0)) AS TotalRefundedPolicyHolder,

                SUM(IF(t.transaction_status = 'Void',t.debit,0)) AS TotalVoidPremium,
                SUM(IF(t.transaction_status = 'Void',(t.new_business_members + t.renewal_members),0)) AS TotalVoidPolicyHolder,

                SUM(IF(t.transaction_status = 'Chargeback',t.debit,0)) AS TotalChargebackedPremium,
                SUM(IF(t.transaction_status = 'Chargeback',(t.new_business_members + t.renewal_members),0)) AS TotalChargebackedPolicyHolder,

                SUM(IF(t.transaction_status = 'Cancelled',t.debit,0)) AS TotalCancelledPremium,
                SUM(IF(t.transaction_status = 'Cancelled',(t.new_business_members + t.renewal_members),0)) AS TotalCancelledPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Returned',t.debit,0)) AS TotalPaymentReturnedPremium,
                SUM(IF(t.transaction_status = 'Payment Returned',(t.new_business_members + t.renewal_members),0)) AS TotalPaymentReturnedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Approved' AND ord.payment_type = 'CC',t.credit,0)) AS TotalCCApprovedPremium,
                SUM(IF(t.transaction_status = 'Payment Approved' AND ord.payment_type = 'CC',(t.new_business_members + t.renewal_members),0)) AS TotalCCApprovedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Declined' AND ord.payment_type = 'CC',t.debit,0)) AS TotalCCDeclinedPremium,
                SUM(IF(t.transaction_status = 'Payment Declined' AND ord.payment_type = 'CC',(t.new_business_members + t.renewal_members),0)) AS TotalCCDeclinedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Approved' AND ord.payment_type = 'ACH',t.credit,0)) AS TotalACHApprovedPremium,
                SUM(IF(t.transaction_status = 'Payment Approved' AND ord.payment_type = 'ACH',(t.new_business_members + t.renewal_members),0)) AS TotalACHApprovedPolicyHolder,

                SUM(IF(t.transaction_status = 'Payment Declined' AND ord.payment_type = 'ACH',t.debit,0)) AS TotalACHDeclinedPremium,
                SUM(IF(t.transaction_status = 'Payment Declined' AND ord.payment_type = 'ACH',(t.new_business_members + t.renewal_members),0)) AS TotalACHDeclinedPolicyHolder

                FROM transactions t $upline_incr
                JOIN 
                (
                    SELECT o.id,o.payment_type,ws.customer_id
                    FROM orders o 
                    JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                    JOIN website_subscriptions ws ON(ws.id = od.website_id)
                    JOIN prd_main p ON(p.id=od.product_id)
                    WHERE p.is_deleted = 'N' GROUP BY o.id 
                ) as ord ON ord.id = t.order_id 
                JOIN customer c ON (c.id=ord.customer_id)
                WHERE t.transaction_status IN('Payment Approved','Pending Settlement','Payment Declined','Cancelled','Chargeback','Payment Returned','Refund','Void') $incr";
    $row = $pdo->selectOne($sql, $sch_params);    

    $rps_data = array(
        'TotalPremium' => $row["TotalPremium"],
        'TotalPolicyHolder' => (!empty($row["TotalPolicyHolder"])?$row["TotalPolicyHolder"]:0),
        
        'TotalApprovedPremium' => $row["TotalApprovedPremium"],
        'TotalApprovedPolicyHolder' => (!empty($row["TotalApprovedPolicyHolder"])?$row["TotalApprovedPolicyHolder"]:0),

        'TotalNewApprovedPremium' => $row["TotalNewApprovedPremium"],
        'TotalNewApprovedPolicyHolder' => (!empty($row["TotalNewApprovedPolicyHolder"])?$row["TotalNewApprovedPolicyHolder"]:0),

        'TotalRenewalApprovedPremium' => $row["TotalRenewalApprovedPremium"],
        'TotalRenewalApprovedPolicyHolder' => (!empty($row["TotalRenewalApprovedPolicyHolder"])?$row["TotalRenewalApprovedPolicyHolder"]:0),

        'TotalDeclinedPremium' => $row["TotalDeclinedPremium"],
        'TotalDeclinedPolicyHolder' => (!empty($row["TotalDeclinedPolicyHolder"])?$row["TotalDeclinedPolicyHolder"]:0),

        'TotalRefundedPremium' => $row["TotalRefundedPremium"],
        'TotalRefundedPolicyHolder' => (!empty($row["TotalRefundedPolicyHolder"])?$row["TotalRefundedPolicyHolder"]:0),

        'TotalVoidPremium' => $row["TotalVoidPremium"],
        'TotalVoidPolicyHolder' => (!empty($row["TotalVoidPolicyHolder"])?$row["TotalVoidPolicyHolder"]:0),

        'TotalPaymentReturnedPremium' => $row["TotalPaymentReturnedPremium"],
        'TotalPaymentReturnedPolicyHolder' => (!empty($row["TotalPaymentReturnedPolicyHolder"])?$row["TotalPaymentReturnedPolicyHolder"]:0),

        'TotalChargebackedPremium' => $row["TotalChargebackedPremium"],
        'TotalChargebackedPolicyHolder' => (!empty($row["TotalChargebackedPolicyHolder"])?$row["TotalChargebackedPolicyHolder"]:0),
        
        'TotalCancelledPremium' => $row["TotalCancelledPremium"],
        'TotalCancelledPolicyHolder' => (!empty($row["TotalCancelledPolicyHolder"])?$row["TotalCancelledPolicyHolder"]:0),
        
        'TotalCCApprovedPremium' => $row["TotalCCApprovedPremium"],
        'TotalCCApprovedPolicyHolder' => (!empty($row["TotalCCApprovedPolicyHolder"])?$row["TotalCCApprovedPolicyHolder"]:0),
        
        'TotalCCDeclinedPremium' => $row["TotalCCDeclinedPremium"],
        'TotalCCDeclinedPolicyHolder' => (!empty($row["TotalCCDeclinedPolicyHolder"])?$row["TotalCCDeclinedPolicyHolder"]:0),
        
        'TotalACHApprovedPremium' => $row["TotalACHApprovedPremium"],
        'TotalACHApprovedPolicyHolder' => (!empty($row["TotalACHApprovedPolicyHolder"])?$row["TotalACHApprovedPolicyHolder"]:0),

        'TotalACHDeclinedPremium' => $row["TotalACHDeclinedPremium"],
        'TotalACHDeclinedPolicyHolder' => (!empty($row["TotalACHDeclinedPolicyHolder"])?$row["TotalACHDeclinedPolicyHolder"]:0),
    );

    include 'tmpl/quick_report_sales_summary.inc.php';
    exit;
}

$exStylesheets = array();
$exJs = array('thirdparty/bower_components/moment/moment.js');
$layout = 'iframe.layout.php';
$template = 'quick_report_sales_summary.inc.php';
include_once 'layout/end.inc.php';
?>
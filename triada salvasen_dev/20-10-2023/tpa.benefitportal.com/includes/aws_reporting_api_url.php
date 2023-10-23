<?php

$ADMIN_REPORTS_URL = "https://jtiyzjibn1.execute-api.us-west-1.amazonaws.com/prod";
$AGENT_REPORTS_URL = "https://ea44328g23.execute-api.us-west-1.amazonaws.com/prod";
$GROUP_REPORTS_URL = "https://ow2p0jngp3.execute-api.us-west-1.amazonaws.com/prod";
$MEMBER_REPORTS_URL = "https://0ubxnxgwjj.execute-api.us-west-1.amazonaws.com/prod";
$LEAD_REPORTS_URL = "https://tmw2mmfxx9.execute-api.us-west-1.amazonaws.com/prod";
$PRODUCT_REPORTS_URL = "https://0su8r9vzt8.execute-api.us-west-1.amazonaws.com/prod";
$COMMISSION_REPORTS_URL = "https://yizge2iv23.execute-api.us-west-1.amazonaws.com/prod";
$SALES_REPORTS_URL = "https://llw8txa05m.execute-api.us-west-1.amazonaws.com/prod";
$DASHBOARD_REPORTS_URL = "https://qudfeeahwf.execute-api.us-west-1.amazonaws.com/prod";

if($SITE_ENV=='Live'){

	$ADMIN_REPORTS_URL = "https://jtiyzjibn1.execute-api.us-west-1.amazonaws.com/prod";
	$AGENT_REPORTS_URL = "https://ea44328g23.execute-api.us-west-1.amazonaws.com/prod";
	$GROUP_REPORTS_URL = "https://ow2p0jngp3.execute-api.us-west-1.amazonaws.com/prod";
	$MEMBER_REPORTS_URL = "https://0ubxnxgwjj.execute-api.us-west-1.amazonaws.com/prod";
	$LEAD_REPORTS_URL = "https://tmw2mmfxx9.execute-api.us-west-1.amazonaws.com/prod";
	$PRODUCT_REPORTS_URL = "https://0su8r9vzt8.execute-api.us-west-1.amazonaws.com/prod";
	$COMMISSION_REPORTS_URL = "https://yizge2iv23.execute-api.us-west-1.amazonaws.com/prod";
	$SALES_REPORTS_URL = "https://llw8txa05m.execute-api.us-west-1.amazonaws.com/prod";
	$DASHBOARD_REPORTS_URL = "https://qudfeeahwf.execute-api.us-west-1.amazonaws.com/prod";

}else if ($SITE_ENV=='Stag' || $SITE_ENV=='Development') {

	$ADMIN_REPORTS_URL = "https://r11mvvn2m5.execute-api.us-west-1.amazonaws.com/qa";
	$AGENT_REPORTS_URL = "https://8n82206nue.execute-api.us-west-1.amazonaws.com/qa";
	$GROUP_REPORTS_URL = "https://0bha2tif6c.execute-api.us-west-1.amazonaws.com/qa";
	$MEMBER_REPORTS_URL = "https://adq8123gv2.execute-api.us-west-1.amazonaws.com/qa";
	$LEAD_REPORTS_URL = "https://1jo1vrrgv3.execute-api.us-west-1.amazonaws.com/qa";
	$PRODUCT_REPORTS_URL = "https://lecj3kswq2.execute-api.us-west-1.amazonaws.com/qa";
	$COMMISSION_REPORTS_URL = "https://vvzgxv8gwf.execute-api.us-west-1.amazonaws.com/qa";
	$SALES_REPORTS_URL = "https://sh96r0gfuj.execute-api.us-west-1.amazonaws.com/qa";
	$DASHBOARD_REPORTS_URL = "https://1jpv5lzuq8.execute-api.us-west-1.amazonaws.com/qa";

}else if ($SITE_ENV=='Local') {

	$ADMIN_REPORTS_URL = "http://localhost/triada-reports";
	$AGENT_REPORTS_URL = "http://localhost/triada-reports";
	$GROUP_REPORTS_URL = "http://localhost/triada-reports";
	$MEMBER_REPORTS_URL = "http://localhost/triada-reports";
	$LEAD_REPORTS_URL = "http://localhost/triada-reports";
	$PRODUCT_REPORTS_URL = "http://localhost/triada-reports";
	$COMMISSION_REPORTS_URL = "http://localhost/triada-reports";
	$SALES_REPORTS_URL = "http://localhost/triada-reports";
	$DASHBOARD_REPORTS_URL = "http://localhost/triada-reports";
}

$REPORT_PROJECT_NAME = "Sal";
/*
 * Note	
 * AWS_REPORTING_URL >> key must be same as report key column in rps_reports table
 * 	
 */
if ($SITE_ENV=='Local') {
	$AWS_REPORTING_URL=array(
		'admin_export'=>$ADMIN_REPORTS_URL."/adminExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_history'=>$ADMIN_REPORTS_URL."/adminHistoryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_failed_payment_recapture_analytics'=>$ADMIN_REPORTS_URL."/adminPFRecaptureAnalytics.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_reversal_transactions'=>$ADMIN_REPORTS_URL."/adminReversalTransactions.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_outstanding_renewals'=>$ADMIN_REPORTS_URL."/adminOutstandingRenewals.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_transaction_report'=>$ADMIN_REPORTS_URL."/adminTransactionReport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'deleteFile'=>$ADMIN_REPORTS_URL."/deleteFile.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eticket_overview'=>$ADMIN_REPORTS_URL."/eTicketOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eticket_script'=>$ADMIN_REPORTS_URL."/eTicketScript.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'active_member_export'=>$ADMIN_REPORTS_URL."/activeMemberExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		// Lamda API for Dashboard report Data
		'top_performing_agency'=>$DASHBOARD_REPORTS_URL."/topPerformingAgency.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'agent_export'=>$AGENT_REPORTS_URL."/agentSummeryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_summery_export'=>$AGENT_REPORTS_URL."/agentSummeryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_history'=>$AGENT_REPORTS_URL."/agentHistoryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_license'=>$AGENT_REPORTS_URL."/agentLicenseExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_merchant_assignment'=>$AGENT_REPORTS_URL."/agentMerchantExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_eo_coverage'=>$AGENT_REPORTS_URL."/agentEOCoverage.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_quick_sales_summary'=>$AGENT_REPORTS_URL."/agentQuickSalesSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_monthly_forecasting'=>$AGENT_REPORTS_URL."/agentMonthlyForecast.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__agent_payables_listing'=>$AGENT_REPORTS_URL."/agentPayablesExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		'group_export'=>$GROUP_REPORTS_URL."/groupExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'list_bill_overview'=>$GROUP_REPORTS_URL."/listBillOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'list_bill_overview_export'=>$GROUP_REPORTS_URL."/listBillOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_summary' => $GROUP_REPORTS_URL."/groupSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_summary_export'=>$GROUP_REPORTS_URL."/groupSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_history' => $GROUP_REPORTS_URL."/groupHistoryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_history_export' => $GROUP_REPORTS_URL."/groupHistoryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'member_summary'=>$MEMBER_REPORTS_URL."/paymentPolicyOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_policy_overview'=>$MEMBER_REPORTS_URL."/paymentPolicyOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_verifications'=>$MEMBER_REPORTS_URL."/memberVerifications.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_paid_through'=>$MEMBER_REPORTS_URL."/memberVerifications.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_age_out'=>$MEMBER_REPORTS_URL."/memberAgeOut.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_history'=>$MEMBER_REPORTS_URL."/memberHistoryExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_product_cancellations'=>$MEMBER_REPORTS_URL."/memberProdCancellation.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'pending_aae_export'=>$LEAD_REPORTS_URL."/pendingAaeExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'lead_summary' => $LEAD_REPORTS_URL."/leadSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'participants_summary' => $LEAD_REPORTS_URL."/participantsSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'participants_pmpm'=>$LEAD_REPORTS_URL."/participantsPMPM.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'product_overview' => $PRODUCT_REPORTS_URL."/productOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'carrier_overview' => $PRODUCT_REPORTS_URL."/carrierOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'membership_overview' => $PRODUCT_REPORTS_URL."/carrierOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'vendor_overview' => $PRODUCT_REPORTS_URL."/carrierOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'life_insurance_beneficiaries'=>$PRODUCT_REPORTS_URL."/lifeInsBeneficiaries.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'platform_pmpm'=>$PRODUCT_REPORTS_URL."/platformPMPM.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_product_persistency'=>$PRODUCT_REPORTS_URL."/agentProductPersistency.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_product_persistency'=>$PRODUCT_REPORTS_URL."/agentProductPersistency.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__company_offering_products'=>$PRODUCT_REPORTS_URL."/companyOfferingProducts.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__product_categories'=>$PRODUCT_REPORTS_URL."/productCategories.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__sub_products'=>$PRODUCT_REPORTS_URL."/subProducts.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__connected_products'=>$PRODUCT_REPORTS_URL."/connectedProducts.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		
		'agent_debit_balance'=>$COMMISSION_REPORTS_URL."/agentDebitBalance.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_agent_debit_balance' => $COMMISSION_REPORTS_URL."/agentDebitBalance.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_debit_ledger'=>$COMMISSION_REPORTS_URL."/agentDebitLedger.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_agent_debit_ledger' => $COMMISSION_REPORTS_URL."/agentDebitLedger.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'generate_commissions' => $COMMISSION_REPORTS_URL."/generateCommissions.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'regenerate_commissions' => $COMMISSION_REPORTS_URL."/regenerateCommissions.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'reverse_commissions' => $COMMISSION_REPORTS_URL."/reverseCommissions.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'advance_funding'=>$COMMISSION_REPORTS_URL."/advanceFunding.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'advance_collection'=>$COMMISSION_REPORTS_URL."/advanceFunding.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'commission_setup'=>$COMMISSION_REPORTS_URL."/commissionSetup.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'debit_balance_overview' => $COMMISSION_REPORTS_URL."/debitBalanceOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'commission_export'=>$COMMISSION_REPORTS_URL."/commissionExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'regenerate_payables' => $COMMISSION_REPORTS_URL."/regeneratePayables.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		'payables_export'=>$SALES_REPORTS_URL."/payablesExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_next_billing_date'=>$SALES_REPORTS_URL."/adminNextBillingDates.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_new_business_post_payments'=>$SALES_REPORTS_URL."/agentNBPostPayments.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_new_business_post_payments_org'=>$SALES_REPORTS_URL."/agentNBPostPayments.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_p2p_comparison'=>$SALES_REPORTS_URL."/agentP2PComparison.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_p2p_renewal_comparison'=>$SALES_REPORTS_URL."/agentP2PComparison.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_member_persistency'=>$SALES_REPORTS_URL."/agentMemberPersistency.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_member_persistency'=>$SALES_REPORTS_URL."/agentMemberPersistency.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_declines_summary'=>$SALES_REPORTS_URL."/agentDeclinesSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_interactions'=>$SALES_REPORTS_URL."/interactionsExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_interactions'=>$SALES_REPORTS_URL."/interactionsExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_interactions'=>$SALES_REPORTS_URL."/interactionsExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'daily_order_summary'=>$SALES_REPORTS_URL."/dailyOrderSummary.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payables_reconciliation' => $PRODUCT_REPORTS_URL."/payablesReconciliation.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_nb_sales'=>$SALES_REPORTS_URL."/paymentNewBusinessSales.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_rb_sales'=>$SALES_REPORTS_URL."/paymentNewBusinessSales.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_full_coverage' => $GROUP_REPORTS_URL."/groupFullCoverage.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_enroll_overview' => $GROUP_REPORTS_URL."/groupEnrollOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_member_age_out' => $GROUP_REPORTS_URL."/gmAgeOut.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_change_product' => $GROUP_REPORTS_URL."/gChangeProduct.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_full_coverage_gp' => $GROUP_REPORTS_URL."/groupFullCoverage.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_enroll_overview_gp' => $GROUP_REPORTS_URL."/groupEnrollOverview.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_member_age_out_gp' => $GROUP_REPORTS_URL."/gmAgeOut.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_change_product_gp' => $GROUP_REPORTS_URL."/gChangeProduct.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'billing_files' => $MEMBER_REPORTS_URL."/billingFiles.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eligibility_files' => $MEMBER_REPORTS_URL."/eligibilityFiles.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'fulfillment_files' => $MEMBER_REPORTS_URL."/fulfillmentFiles.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'trigger_export' => $ADMIN_REPORTS_URL."/triggerExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'provider_export' => $PRODUCT_REPORTS_URL."/providerExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'vendor_export' => $PRODUCT_REPORTS_URL."/vendorExport.php?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
	);
}else{
	$AWS_REPORTING_URL=array(

		'admin_export'=>$ADMIN_REPORTS_URL."/adminExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_history'=>$ADMIN_REPORTS_URL."/adminHistoryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_failed_payment_recapture_analytics'=>$ADMIN_REPORTS_URL."/adminPFRecaptureAnalytics?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_reversal_transactions'=>$ADMIN_REPORTS_URL."/adminReversalTransactions?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_outstanding_renewals'=>$ADMIN_REPORTS_URL."/adminOutstandingRenewals?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_transaction_report'=>$ADMIN_REPORTS_URL."/adminTransactionReport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'deleteFile'=>$ADMIN_REPORTS_URL."/deleteFile?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eticket_overview'=>$ADMIN_REPORTS_URL."/eTicketOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eticket_script'=>$ADMIN_REPORTS_URL."/eTicketScript?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'active_member_export'=>$ADMIN_REPORTS_URL."/activeMemberExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		// Lamda API for Dashboard report Data
		'top_performing_agency'=>$DASHBOARD_REPORTS_URL."/topPerformingAgency?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'agent_export'=>$AGENT_REPORTS_URL."/agentSummeryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_summery_export'=>$AGENT_REPORTS_URL."/agentSummeryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_history'=>$AGENT_REPORTS_URL."/agentHistoryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_license'=>$AGENT_REPORTS_URL."/agentLicenseExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_merchant_assignment'=>$AGENT_REPORTS_URL."/agentMerchantExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_eo_coverage'=>$AGENT_REPORTS_URL."/agentEOCoverage?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_quick_sales_summary'=>$AGENT_REPORTS_URL."/agentQuickSalesSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_monthly_forecasting'=>$AGENT_REPORTS_URL."/agentMonthlyForecast?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__agent_payables_listing'=>$AGENT_REPORTS_URL."/agentPayablesExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		'group_export'=>$GROUP_REPORTS_URL."/groupExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'list_bill_overview'=>$GROUP_REPORTS_URL."/listBillOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'list_bill_overview_export'=>$GROUP_REPORTS_URL."/listBillOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_summary' => $GROUP_REPORTS_URL."/groupSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_summary_export' => $GROUP_REPORTS_URL."/groupSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_history' => $GROUP_REPORTS_URL."/groupHistoryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_history_export' => $GROUP_REPORTS_URL."/groupHistoryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'member_summary'=>$MEMBER_REPORTS_URL."/paymentPolicyOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_policy_overview'=>$MEMBER_REPORTS_URL."/paymentPolicyOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_verifications'=>$MEMBER_REPORTS_URL."/memberVerifications?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_paid_through'=>$MEMBER_REPORTS_URL."/memberVerifications?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_age_out'=>$MEMBER_REPORTS_URL."/memberAgeOut?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_history'=>$MEMBER_REPORTS_URL."/memberHistoryExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_product_cancellations'=>$MEMBER_REPORTS_URL."/memberProdCancellation?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'pending_aae_export'=>$LEAD_REPORTS_URL."/pendingAaeExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'lead_summary' => $LEAD_REPORTS_URL."/leadSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'participants_summary' => $LEAD_REPORTS_URL."/participantsSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'participants_pmpm'=>$LEAD_REPORTS_URL."/participantsPMPM?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		'product_overview' => $PRODUCT_REPORTS_URL."/productOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'carrier_overview' => $PRODUCT_REPORTS_URL."/carrierOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'membership_overview' => $PRODUCT_REPORTS_URL."/carrierOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'vendor_overview' => $PRODUCT_REPORTS_URL."/carrierOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'life_insurance_beneficiaries'=>$PRODUCT_REPORTS_URL."/lifeInsBeneficiaries?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'platform_pmpm'=>$PRODUCT_REPORTS_URL."/platformPMPM?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_product_persistency'=>$PRODUCT_REPORTS_URL."/agentProductPersistency?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_product_persistency'=>$PRODUCT_REPORTS_URL."/agentProductPersistency?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__company_offering_products'=>$PRODUCT_REPORTS_URL."/companyOfferingProducts?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__product_categories'=>$PRODUCT_REPORTS_URL."/productCategories?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__sub_products'=>$PRODUCT_REPORTS_URL."/subProducts?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'__connected_products'=>$PRODUCT_REPORTS_URL."/connectedProducts?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,

		
		'agent_debit_balance'=>$COMMISSION_REPORTS_URL."/agentDebitBalance?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_agent_debit_balance' => $COMMISSION_REPORTS_URL."/agentDebitBalance?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_debit_ledger'=>$COMMISSION_REPORTS_URL."/agentDebitLedger?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_agent_debit_ledger' => $COMMISSION_REPORTS_URL."/agentDebitLedger?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'generate_commissions' => $COMMISSION_REPORTS_URL."/generateCommissions?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'regenerate_commissions' => $COMMISSION_REPORTS_URL."/regenerateCommissions?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'reverse_commissions' => $COMMISSION_REPORTS_URL."/reverseCommissions?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'advance_funding'=>$COMMISSION_REPORTS_URL."/advanceFunding?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'advance_collection'=>$COMMISSION_REPORTS_URL."/advanceFunding?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'commission_setup'=>$COMMISSION_REPORTS_URL."/commissionSetup?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'debit_balance_overview' => $COMMISSION_REPORTS_URL."/debitBalanceOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'commission_export'=>$COMMISSION_REPORTS_URL."/commissionExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'regenerate_payables' => $COMMISSION_REPORTS_URL."/regeneratePayables?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		
		'payables_export'=>$SALES_REPORTS_URL."/payablesExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_next_billing_date'=>$SALES_REPORTS_URL."/adminNextBillingDates?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_new_business_post_payments'=>$SALES_REPORTS_URL."/agentNBPostPayments?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_new_business_post_payments_org'=>$SALES_REPORTS_URL."/agentNBPostPayments?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_p2p_comparison'=>$SALES_REPORTS_URL."/agentP2PComparison?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_payment_p2p_renewal_comparison'=>$SALES_REPORTS_URL."/agentP2PComparison?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_member_persistency'=>$SALES_REPORTS_URL."/agentMemberPersistency?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'admin_member_persistency'=>$SALES_REPORTS_URL."/agentMemberPersistency?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_declines_summary'=>$SALES_REPORTS_URL."/agentDeclinesSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'agent_interactions'=>$SALES_REPORTS_URL."/interactionsExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'member_interactions'=>$SALES_REPORTS_URL."/interactionsExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_interactions'=>$SALES_REPORTS_URL."/interactionsExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'daily_order_summary'=>$SALES_REPORTS_URL."/dailyOrderSummary?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payables_reconciliation' => $PRODUCT_REPORTS_URL."/payablesReconciliation?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_nb_sales'=>$SALES_REPORTS_URL."/paymentNewBusinessSales?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'payment_rb_sales'=>$SALES_REPORTS_URL."/paymentNewBusinessSales?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_full_coverage' => $GROUP_REPORTS_URL."/groupFullCoverage?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_enroll_overview' => $GROUP_REPORTS_URL."/groupEnrollOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_member_age_out' => $GROUP_REPORTS_URL."/gmAgeOut?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_change_product' => $GROUP_REPORTS_URL."/gChangeProduct?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_full_coverage_gp' => $GROUP_REPORTS_URL."/groupFullCoverage?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_enroll_overview_gp' => $GROUP_REPORTS_URL."/groupEnrollOverview?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_member_age_out_gp' => $GROUP_REPORTS_URL."/gmAgeOut?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'group_change_product_gp' => $GROUP_REPORTS_URL."/gChangeProduct?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'billing_files' => $MEMBER_REPORTS_URL."/billingFiles?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'eligibility_files' => $MEMBER_REPORTS_URL."/eligibilityFiles?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'fulfillment_files' => $MEMBER_REPORTS_URL."/fulfillmentFiles?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'trigger_export' => $ADMIN_REPORTS_URL."/triggerExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'provider_export' => $PRODUCT_REPORTS_URL."/providerExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
		'vendor_export' => $PRODUCT_REPORTS_URL."/vendorExport?siteEnv=".$SITE_ENV."&site=".$REPORT_PROJECT_NAME,
	);
}
?>
<?php

if($SITE_ENV=='Live'){
	$groupEnrollmentAPIHost = "https://groupenrollment.benefitportal.com";
}else if ($SITE_ENV=='Stag'){
	$groupEnrollmentAPIHost = "https://groupenrollmentstag.benefitportal.com";
}else if ($SITE_ENV=='Development') {
	$groupEnrollmentAPIHost = "https://groupenrollmentdev.benefitportal.com";
}else if ($SITE_ENV=='Local') {
	$groupEnrollmentAPIHost = "http://groupenrollment.localhost:8000";	
}


//Must need to pass GET or POST Method
$API_URL_KEY = array(
	'cartSetting' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/cartSetting',
	),
	'getGroupProduct' => array(
		'method' => 'GET',
		'url'=>$groupEnrollmentAPIHost.'/getGroupProduct',
	),
	'saveBundleRecommendation' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/saveBundleRecommendation',
	),
	'deleteBundle' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/deleteBundle',
	),
	'checkAssignBundle' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/checkAssignBundle',
	),
	'bundleRecommendationsList' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/bundleRecommendationsList',
	),
	'getBundle' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getBundle',
	),
	'getBundleGroup' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getBundleGroup',
	),
	'getBundleQuestion' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getBundleQuestion',
	),
	'getEditBundleQuestion' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getEditBundleQuestion',
	),
	'editBundleRecommendationList' => array(
		'method' => 'GET',
		'url'=>$groupEnrollmentAPIHost.'/editBundleRecommendationList',
	),
	'getQuestion' => array(
		'method' => 'GET',
		'url'=>$groupEnrollmentAPIHost.'/getQuestion',
	),
	'getQuestionAnswerBundleDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getQuestionAnswerBundleDetails',
	),
	'saveBundleQuestions' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/saveBundleQuestions',
	),
	'getCartSettingGroups' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/cartSetting/groups',
	),
	'variationCartSettings' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/cartSetting/variation',
	),	
	'deleteBundleQuestion' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/deleteBundleQuestion',
    ),
	'saveBundleComparison' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/saveBundleComparison',
    ),
	'deleteBundleComaprison' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/deleteBundleComaprison',
    ),
	'getAllCompareBundleData' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/getAllCompareBundleData',
    ),
	'saveBundleInformation' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/saveBundleInformation',
    ),
	'getBundleInformation' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/getBundleInformation',
    ),
	'deleteBundleRecommandation' => array(
        'method' => 'POST',
        'url'=>$groupEnrollmentAPIHost.'/deleteBundleRecommandation',
    ),
	'globalCartSettings' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/cartSetting/global',
	),
	'variationDetete' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/cartSetting/variation/delete',
	),
	'pageBuilderDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/pageBuilderDetails',
	),
	'pageBuilderProductDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/pageBuilderProducts',
	),
	'pageBuilderCategoryProducts' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/pageBuilderCategoryProducts',
	),
	'pageBuilderContactDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/pageBuilderContactDetails/{userName}',
	),
	'checkEnrollmentID' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/checkEnrollmentID',
	),
	'getProductCategoryList' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getProductCategoryList',
	),
	'getGroupProductCategoryList' => array(
		'method' => 'GET',
		'url'=>$groupEnrollmentAPIHost.'/getGroupProductCategoryList',
	),
	'saveCategory' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/saveCategory',
	),
	'deleteCategory' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/deleteCategory',
	),
	'saveGroupProductCategoryOrder' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/saveGroupProductCategoryOrder',
	),
	'calculateTakeHomePay' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/calculateTakeHomePay',
	),
	'enrollmentSubmit' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/enrollmentSubmit',
	),
	'getGroupAndMemberInformation' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getGroupAndMemberInformation',
	),
	'getGroupDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getGroupDetails',
	),
	'getCoveragePeriod' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getCoveragePeriod',
	),
	'groupCompany' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/groupCompany',
	),
	'getEnrolleeClass' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getEnrolleeClass',
	),
	'getGroupMemberDetail' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getGroupMemberDetail',
	),
	'getLeadDetail' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getLeadDetail',
	),
	'getAdditionalInfo' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getAdditionalInfo',
	),
	'customeQuestionAnswer' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/customeQuestionAnswer',
	),
	'getChildField' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getChildField',
	),
	'getPrimaryMemberField' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getPrimaryMemberField',
	),
	'getSpouseField' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getSpouseField',
	),
	'getPrincipalBeneficiary' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getPrincipalBeneficiary',
	),
	'getContingentBeneficiary' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getContingentBeneficiary',
	),
	'getProductDetails' => array(
		'method' => 'POST',
		'url' => $groupEnrollmentAPIHost.'/getProductDetails',
	),
	'bundleQuestionsAnswers' => array(
		'method' => 'GET',
		'url'=>$groupEnrollmentAPIHost.'/bundleQuestionsAnswers/{userName}',
	),
	'getProducts' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getProducts/{userName}',
	),
	'productData' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/productData/{productID}',
	),

	"productAddToCart" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/productAddToCart/{userName}',
	),
	'bundleDetails' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/bundleDetails/{userName}',
	),
	'bundleComparision' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/bundleComparision/{userName}',
	),
	"cartTotalCalculate" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/cartTotalCalculate/{userName}',
	),
	"calculateRateQuestionsDetails" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/calculateRateQuestionsDetails/{userName}',
 	),
	"memberEnrollmentPlan" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/memberEnrollmentPlan',
	),
	"checkVerificationStatus" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/checkVerificationStatus',
	),
	"test_merchant_processor" =>array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/test_merchant_processor',
	),
	'getLeadQuoteDetail' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getLeadQuoteDetail',
	),
	'getOrderData' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getOrderData',
	),
	'checkMemberStatus' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/checkMemberStatus',
	),
	'getOrderDetailData' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getOrderDetailData',
	),
	'getDynamicRaw' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getDynamicRaw',
	),
	'getDependentData' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getDependentData',
	),
	'getCustomerBeneficiaryData' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getCustomerBeneficiaryData',
	),
	'getCustomerBillingProfile' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/getCustomerBillingProfile',
	),
	'validateBillingAddress' => array(
		'method' => 'POST',
		'url'=>$groupEnrollmentAPIHost.'/validateBillingAddress',
	)
);

?>
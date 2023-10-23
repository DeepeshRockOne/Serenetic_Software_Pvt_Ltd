<?php
include_once dirname(__DIR__) . "/includes/apiUrlKey.php";
include_once dirname(__DIR__) . "/includes/Api.class.php";

class groupMemberEnrollment {

	/**
	 * Get Group Information
	 */

	 public function getGroupAndMemberInformation($groupId,$member_id)
	 {
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$coveragePeriodArray = array();
		$data = array(
			'groupId' => $groupId,
			'memberRepId' => $member_id,
			'api_key' => 'getGroupAndMemberInformation'
		);
		$coveragePeriodArray = $ajaxApiCall->ajaxApiCall($data,true);
		
		return $coveragePeriodArray;
	 }

	public function getGroupDetails($groupId){
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$groupDetails = array();
		
		$data = array(
			'groupId' => $groupId,
			'api_key' => 'getGroupDetails'
		);
		$groupDetails = $ajaxApiCall->ajaxApiCall($data,true);

		return $groupDetails; 
	}

	public function get_coveragePeriod($group_id,$coverage_period=''){
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$coveragePeriodArray = array();
		$data = array(
			'coveragePeriod' => $coverage_period,
			'groupId' => $group_id,
			'api_key' => 'getCoveragePeriod'
		);
		$coveragePeriodArray = $ajaxApiCall->ajaxApiCall($data,true);
		
		return $coveragePeriodArray;
	}

	public function get_enrolleeClass($group_id,$enrollee_class=''){
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$enrolleeClassArray = array();
		
		$data = array(
			'enrolleeClass' => $enrollee_class,
			'groupId' => $group_id,
			'api_key' => 'getEnrolleeClass',
		);
		$enrolleeClassArray = $ajaxApiCall->ajaxApiCall($data,true);

		return $enrolleeClassArray; 
	}
	public function getGroupWaiveProductList($waive_checkbox,$group_waive_product){
	    $group_product_list=array();
	    if(!empty($waive_checkbox)){
	      if(!empty($waive_checkbox)){
	        foreach ($waive_checkbox as $key => $value) {
	          $group_product_list = array_merge($group_product_list,$group_waive_product[$value]);
	        }
	      }
	    }
	    return $group_product_list;
	}

	public function getAdditionalInfo($memberRepId){
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$additionalInfo = array();
		
		$data = array(
			'memberRepId' => $memberRepId,
			'api_key' => 'getAdditionalInfo'
		);
		$additionalInfo = $ajaxApiCall->ajaxApiCall($data,true);

		return $additionalInfo; 
	}

	public function bundleQuestionsAnswers($group_id){
		global $pdo,$API_URL_KEY;
		$ajaxApiCall = new Api();
		$question = array();
		
		$data = array(
			'userName' => $group_id,
			'api_key' => 'bundleQuestionsAnswers'
		);
		$question = $ajaxApiCall->ajaxApiCall($data,true);

		return $question; 
	}

	
}
?>
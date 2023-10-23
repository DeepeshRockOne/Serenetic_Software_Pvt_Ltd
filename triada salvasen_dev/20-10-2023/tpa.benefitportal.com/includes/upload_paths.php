<?php
$companySql = "SELECT * FROM company";
$companyRows = $pdo->select($companySql);
$SITE_SETTINGS = array();
if ($companyRows) {
	foreach ($companyRows as $company) {
		if($SITE_ENV=='Local'){
			$company['site_url'] = $HOST;
		}
		$my_setting = array(
			"HOST" => $company['site_url'],
			"NOTE_FILES" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'note_files' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/note_files/',
			),
			"CUST_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/customer/profile/',
			),
			"PHYSICAL_DOCUMENT" => array(
	          "upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'physical_document' . DIRECTORY_SEPARATOR,
	          "download" => $company['site_url'] . '/uploads/physical_document',
	      	),
			"ADMIN_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/admin/profile/',
			),
			"MEMBER_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'member' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/member/profile/',
			),
			"AGENT_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'agents' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/agents/profile/',
			),
			"CALL_CENTER_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'call_center' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/call_center/profile/',
			),
			"FRONTER_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'fronter' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/fronter/profile/',
			),
			"CALLER_PROFILE" => array(
                "upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'caller' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
                "download" => $company['site_url'] . '/uploads/caller/profile/',
            ),
			"CALL_CENTER_MANAGER_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'call_center_manager' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/call_center_manager/profile/',
			),
			"AFFILIATES_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'affiliates' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/affiliates/profile/',
			),
			"GROUP_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'groups' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/groups/profile/',
			),
			"ORGANIZATION_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'org' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/org/profile/',
			),
			"COMM_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'communication_site' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/communication_site/',
			),
			"SUPPORTER_PROFILE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'supporter' . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/supporter/profile/',
			),
			"DASHBOARD_SLIDER" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/dashboard',
			),
			"PRODUCT_IMAGE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/product',
			),
			"PRODUCT_THUMIMAGE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/product/thumb',
			),
			"TRIGGER_IMAGE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'trigger_image' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/trigger_image',
			),
			"AMBASSADOR_DOC" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'ambassador_doc' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/ambassador_doc',
			),
			"SIGNATURE" => array(
				"upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'signature' . DIRECTORY_SEPARATOR,
				"download" => $company['site_url'] . '/uploads/signature',
			),
			"ESIGN_TERMS_CONDITION" => array(
                "upload" => DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'eSignTermsCondition' . DIRECTORY_SEPARATOR,
                "download" => $company['site_url'] . '/uploads/eSignTermsCondition',
            ),
			"SHORT_NAME" => $company['short_name'],
		);
		$SITE_SETTINGS[$company['id']] = $my_setting;
	}
}

?>
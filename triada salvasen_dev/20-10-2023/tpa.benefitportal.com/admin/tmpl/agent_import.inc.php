<?php 
$AGENT_IMPORT_ARR = array(
	'Parent Agent' => array(
		array(
			'label' => 'PARENT AGENT ID',
			'file_label' => 'PARENT_AGENT_ID',
			'info' => 'Unique identifier of agent this agent falls underneath (Must be in system)',
			'field_name' => 'parent_agent_id'
		),
	),
	'Agent' => array(
		array(
			'label' => 'AGENT ID',
			'file_label' => 'AGENT_ID',
			'info' => 'Unique identifier of agent',
			'field_name' => 'agent_id'
		),
		array(
			'label' => 'AGENT LEVEL',
			'file_label' => 'AGENT_LEVEL',
			'info' => 'Agent Level',
			'field_name' => 'agent_level'
		),
		array(
			'label' => 'ACCOUNT TYPE',
			'file_label' => 'ACCOUNT_TYPE',
			'info' => 'Type of account (Agency Or Agent)',
			'field_name' => 'account_type'
		),
		array(
			'label' => 'AGENCY LEGAL NAME',
			'file_label' => 'AGENCY_LEGAL_NAME',
			'info' => 'Legal Name of Agency',
			'field_name' => 'company_name'
		),
		array(
			'label' => 'AGENCY ADDRESS',
			'file_label' => 'AGENCY_ADDRESS',
			'info' => 'Legal Address of Agency',
			'field_name' => 'company_address'
		),
		array(
			'label' => 'AGENCY ADDRESS2',
			'file_label' => 'AGENCY_ADDRESS2',
			'info' => 'Legal Address of Agency',
			'field_name' => 'company_address_2'
		),
		array(
			'label' => 'AGENCY CITY',
			'file_label' => 'AGENCY_CITY',
			'info' => 'Legal City of Agency',
			'field_name' => 'company_city'
		),
		array(
			'label' => 'AGENCY STATE',
			'file_label' => 'AGENCY_STATE',
			'info' => 'Legal State of Agency',
			'field_name' => 'company_state'
		),
		array(
			'label' => 'AGENCY ZIPCODE',
			'file_label' => 'AGENCY_ZIPCODE',
			'info' => 'Legal Zipcode of Agency',
			'field_name' => 'company_zip'
		),
		array(
			'label' => 'AGENCY EIN',
			'file_label' => 'AGENCY_EIN',
			'info' => 'Legal EIN of Agency',
			'field_name' => 'tax_id'
		),

		array(
			'label' => 'PRINCIPAL AGENT FIRSTNAME',
			'file_label' => 'PRINCIPAL_AGENT_FIRSTNAME',
			'info' => 'Firstname of Agent',
			'field_name' => 'fname'
		),
		array(
			'label' => 'PRINCIPAL AGENT LASTNAME',
			'file_label' => 'PRINCIPAL_AGENT_LASTNAME',
			'info' => 'Lastname of Agent',
			'field_name' => 'lname'
		),
		array(
			'label' => 'AGENT ADDRESS',
			'file_label' => 'AGENT_ADDRESS',
			'info' => 'Legal Address of Agent',
			'field_name' => 'address'
		),
		array(
			'label' => 'AGENT CITY',
			'file_label' => 'AGENT_CITY',
			'info' => 'Legal City of Agent',
			'field_name' => 'city'
		),
		array(
			'label' => 'AGENT STATE',
			'file_label' => 'AGENT_STATE',
			'info' => 'Legal State of Agent',
			'field_name' => 'state'
		),
		array(
			'label' => 'AGENT ZIPCODE',
			'file_label' => 'AGENT_ZIPCODE',
			'info' => 'Legal Zipcode of Agent',
			'field_name' => 'zip'
		),
		array(
			'label' => 'AGENT SSN',
			'file_label' => 'AGENT_SSN',
			'info' => 'Legal SSN Number of Agent',
			'field_name' => 'ssn'
		),
		array(
			'label' => 'AGENT DOB',
			'file_label' => 'AGENT_DOB',
			'info' => 'Date of Birth of Agent',
			'field_name' => 'birth_date'
		),

		array(
			'label' => 'AGENT PHONE',
			'file_label' => 'AGENT_PHONE',
			'info' => 'Phone number of agent',
			'field_name' => 'cell_phone'
		),
		array(
			'label' => 'AGENT EMAIL',
			'file_label' => 'AGENT_EMAIL',
			'info' => 'Email address of Agent',
			'field_name' => 'email'
		),

		array(
			'label' => 'AGENT STATUS',
			'file_label' => 'AGENT_STATUS',
			'info' => 'Status of Agent',
			'field_name' => 'status'
		),
		array(
			'label' => 'AGENT PRIMARY MERCHANT ACH',
			'file_label' => 'AGENT_PRIMARY_MERCHANT_ACH',
			'info' => 'Primary Merchant account of Agent For ACH Payments',
			'field_name' => 'payment_master_id'
		),
		array(
			'label' => 'AGENT PRIMARY MERCHANT CC',
			'file_label' => 'AGENT_PRIMARY_MERCHANT_CC',
			'info' => 'Primary Merchant account of Agent For CC Payments',
			'field_name' => 'ach_master_id'
		),

		array(
			'label' => 'NPN Number',
			'file_label' => 'NPN_NUMBER',
			'info' => 'Legal NPN Number',
			'field_name' => 'npn'
		),
		array(
			'label' => 'E&O Amount',
			'file_label' => 'E_O_AMOUNT',
			'info' => 'E&O Amount',
			'field_name' => 'e_o_amount'
		),
		array(
			'label' => 'E&O Expiration',
			'file_label' => 'E_O_EXPIRATION',
			'info' => 'E&O Expiration',
			'field_name' => 'e_o_expiration'
		),

		array(
			'label' => 'HIDE DISPLAY',
			'file_label' => 'HIDE_DISPLAY',
			'info' => 'Indicator if you do not want display name, display phone, or display email shown as a point of contact. (Yes or No)',
			'field_name' => 'display_in_member'
		),
		array(
			'label' => 'DISPLAY NAME',
			'file_label' => 'DISPLAY_NAME',
			'info' => 'Legal Display Name of Agent',
			'field_name' => 'public_name'
		),
		array(
			'label' => 'DISPLAY PHONE',
			'file_label' => 'DISPLAY_PHONE',
			'info' => 'Legal Display Phone of Agent',
			'field_name' => 'public_phone'
		),
		array(
			'label' => 'DISPLAY EMAIL',
			'file_label' => 'DISPLAY_EMAIL',
			'info' => 'Legal Display Email of Agent',
			'field_name' => 'public_email'
		),
		array(
			'label' => 'AGENT USERNAME',
			'file_label' => 'AGENT_USERNAME',
			'info' => 'Legal Username of Agent',
			'field_name' => 'username'
		),
		array(
			'label' => 'AGENT CUSTOM BRAND',
			'file_label' => 'AGENT_CUSTOM_BRAND',
			'info' => 'Indicator Of Customer Branding of Agent. (Yes or No)',
			'field_name' => 'is_branding'
		),
	),
	'Agent Access' => array(
		array(
			'label' => 'ACCESS ENROLL MEMBER',
			'file_label' => 'ACCESS_ENROLL_MEMBER',
			'info' => 'Indicator Of Agent Can Access Page Or Not',
			'field_name' => 'access_enroll_member'
		),
		array(
			'label' => 'ACCESS ENROLL AGENT',
			'file_label' => 'ACCESS_ENROLL_AGENT',
			'info' => 'Indicator Of Agent Can Access Page Or Not',
			'field_name' => 'access_enroll_agent'
		),
		array(
			'label' => 'ACCESS ENROLL GROUPS',
			'file_label' => 'ACCESS_ENROLL_GROUPS',
			'info' => 'Indicator Of Agent Can Access Page Or Not',
			'field_name' => 'access_enroll_groups'
		),

		array(
			'label' => 'ACCESS AAE APPLICATION WEBSITE',
			'file_label' => 'ACCESS_AAE_APPLICATION_WEBSITE',
			'info' => 'Indicator Of Agent Can Access Page Or Not',
			'field_name' => 'access_aae_enrollment_website'
		),
		array(
			'label' => 'ACCESS SELF APPLICATION WEBSITE',
			'file_label' => 'ACCESS_SELF_APPLICATION_WEBSITE',
			'info' => 'Indicator Of Agent Can Access Page Or Not',
			'field_name' => 'access_self_enrollment_Website'
		),

		array(
			'label' => 'ACCESS BOB AGENTS',
			'file_label' => 'ACCESS_BOB_AGENTS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_bob_agents'
		),
		array(
			'label' => 'ACCESS BOB MEMBERS',
			'file_label' => 'ACCESS_BOB_MEMBERS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_bob_members'
		),
		array(
			'label' => 'ACCESS BOB GROUPS',
			'file_label' => 'ACCESS_BOB_GROUP',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_bob_groups'
		),
		array(
			'label' => 'ACCESS BOB LEADS',
			'file_label' => 'ACCESS_BOB_LEADS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_bob_leads'
		),
		array(
			'label' => 'ACCESS BOB PENDING AAE',
			'file_label' => 'ACCESS_BOB_PENDING_AAE',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_bob_pending_aae'
		),


		array(
			'label' => 'ACCESS PROD REPORTING',
			'file_label' => 'ACCESS_PROD_REPORTING',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_prod_reporting'
		),
		array(
			'label' => 'ACCESS PROD COMMISSIONS',
			'file_label' => 'ACCESS_PROD_COMMISSIONS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_prod_commissions'
		),
		array(
			'label' => 'ACCESS PROD ORDERS',
			'file_label' => 'ACCESS_PROD_ORDERS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_prod_orders'
		),
		array(
			'label' => 'ACCESS PROD PRODUCTS',
			'file_label' => 'ACCESS_PROD_PRODUCTS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_prod_products'
		),
		array(
			'label' => 'ACCESS PROD TRANSACTIONS',
			'file_label' => 'ACCESS_PROD_TRANSACTIONS',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_prod_transactions'
		),

		array(
			'label' => 'ACCESS RESOURCES EMAIL BROADCASTER',
			'file_label' => 'ACCESS_RESOURCES_EMAIL_BROADCASTER',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_resources_email_broadcaster'
		),
		array(
			'label' => 'ACCESS RESOURCES SMS BROADCASTER',
			'file_label' => 'ACCESS_RESOURCES_SMS_BROADCASTER',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_resources_sms_broadcaster'
		),
		array(
			'label' => 'ACCESS RESOURCES COMMUNICATION QUEUE',
			'file_label' => 'ACCESS_RESOURCES_COMMUNICATION_QUEUE',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_resources_communication_queue'
		),
		array(
			'label' => 'ACCESS RESOURCES TRAINING',
			'file_label' => 'ACCESS_RESOURCES_TRAINING',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_resources_training'
		),
		array(
			'label' => 'ACCESS RESOURCES SUPPORT',
			'file_label' => 'ACCESS_RESOURCES_SUPPORT',
			'info' => 'Indicator Of Agent Can Access This Page Or Not',
			'field_name' => 'access_resources_support'
		),
	),
);

$columnCounter = 0;
foreach ($AGENT_IMPORT_ARR as $label => $fields) { ?>
	<div class="line_title">
	<h3><span><?=$label?></span></h3>
   </div>
   <div class="row">
      <?php
	  foreach($fields as $field) {?>
         <div class="col-sm-12 col-md-6">
            <div class="form-group height_auto">
               <div class="input-group resources_addon">
                  <div class="input-group-addon">
                     <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$field['info']?>"></i> <?=$field['label']?>
                  </div>
                  <div class="pr">
                     <select class="form-control select" name="<?=$field['field_name']?>" data-live-search="true">
                           <option data-hidden="true"></option>
                          <?php foreach ($row as $key => $value) {
                              $selectedOption = "";
                              $optn_val=$value;
                              if ($field['label'] == trim($value) || ($field['file_label'] == trim($value))) {
                                 $selectedOption = 'selected="selected"';
                              } else if ($columnCounter == $key) {
								$optn_val='None';
								$value='';
								$selectedOption = '';
							 } 
                           ?>
                           <option value="<?=$optn_val?>" <?=$selectedOption?>><?=$value?></option>
                        <?php } ?>
                     </select>
                     <label class="label-wrap">Select CSV Column</label>
                  </div>
               </div>
               <p class="error" id="err_<?=$field['field_name']?>"></p>
            </div>
         </div>
      <?php } ?>
	</div>
<?php } 

?>
<?php 
$CACHE_FILE_NAME = 'generatedCache.php';
$PRODUCT_CACHE_FILE_NAME = 'generatedProductCache.php';

$UPLOAD_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$CACHE_PATH_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'site_management' . DIRECTORY_SEPARATOR;

$OLD_COVERAGE_DIR = $UPLOAD_DIR.'old_coverage'.DIRECTORY_SEPARATOR;
$PHYSICAL_DOCUMENT_DIR = $UPLOAD_DIR.'physical_document'.DIRECTORY_SEPARATOR;
$TRIGGER_IMAGE_DIR = $UPLOAD_DIR . 'trigger_image' . DIRECTORY_SEPARATOR;
$CKEDITOR_DIR = $UPLOAD_DIR . 'ckeditor' . DIRECTORY_SEPARATOR;
$FEES_ATTACHMENS_DIR = $UPLOAD_DIR . 'fees_attachments' . DIRECTORY_SEPARATOR;
$ATTACHMENS_DIR = $UPLOAD_DIR . 'attachments' . DIRECTORY_SEPARATOR;
$PRICE_MATRIX_CSV_DIR = $UPLOAD_DIR . 'price_matrix_csv' . DIRECTORY_SEPARATOR;
$COLLATERAL_DOCUMENT_DIR = $UPLOAD_DIR . 'collateral_document' . DIRECTORY_SEPARATOR;
$AGENTS_DIR = $UPLOAD_DIR . 'agents' .DIRECTORY_SEPARATOR;
$AGENTS_PROFILE_DIR = $AGENTS_DIR . 'profile' .DIRECTORY_SEPARATOR;
$AGENTS_BRAND_ICON = $AGENTS_DIR . 'brand_icon' .DIRECTORY_SEPARATOR;
$AGENT_DOC_DIR = $AGENTS_DIR . 'agent_doc' .DIRECTORY_SEPARATOR;
$NOTE_DIR = $UPLOAD_DIR . 'note_files' . DIRECTORY_SEPARATOR;
$CATEGORY_IMAGE_DIR = $UPLOAD_DIR . 'category_image' . DIRECTORY_SEPARATOR;
$CSV_DIR = $UPLOAD_DIR . 'csv' . DIRECTORY_SEPARATOR;
$ELIGIBILITY_FILES_DIR = $UPLOAD_DIR . 'eligibility_files' . DIRECTORY_SEPARATOR;
$BILLING_FILES_DIR = $UPLOAD_DIR . 'billing_files' . DIRECTORY_SEPARATOR;
$FULFILLMENT_FILES_DIR = $UPLOAD_DIR . 'fulfillment_files' . DIRECTORY_SEPARATOR;
$ETICKET_DOCUMENT_DIR = $UPLOAD_DIR . 'e_ticket_files' . DIRECTORY_SEPARATOR;
$TMP_DIR = $UPLOAD_DIR . 'page_builder_tmp_files' . DIRECTORY_SEPARATOR;
$PAGE_COVER_DIR = $UPLOAD_DIR . 'page_cover_images' . DIRECTORY_SEPARATOR;
$PAGE_LOGO_DIR = $UPLOAD_DIR . 'page_logo_images' . DIRECTORY_SEPARATOR;
$GROPS_DIR = $UPLOAD_DIR . 'groups' .DIRECTORY_SEPARATOR;
$GROUPS_BRAND_ICON_DIR = $GROPS_DIR . 'brand_icon' .DIRECTORY_SEPARATOR;
$ACH_COMM_DIR = $UPLOAD_DIR . 'ACH_Payments' . DIRECTORY_SEPARATOR;
$CIRCLE_DOCUMENT_DIR = $UPLOAD_DIR . 'circle_files' . DIRECTORY_SEPARATOR;
$SUMMERNOTE_UPLOAD_DIR = $UPLOAD_DIR . 'summernote_images' . DIRECTORY_SEPARATOR;
$RESOURCE_DOCUMENT_DIR = $UPLOAD_DIR . 'resource_files' . DIRECTORY_SEPARATOR;
$SYSTEM_RESOURCES_DIR = $UPLOAD_DIR . 'system_resources' . DIRECTORY_SEPARATOR;
$LIST_BILL_PAYMENT_FILES_DIR = $UPLOAD_DIR . 'list_bill_payment_files' . DIRECTORY_SEPARATOR;
$LIVE_CHAT_UPLOADS_DIR = $UPLOAD_DIR . 'live_chat';
$PARTICIPANTS_CSV_DIR = $UPLOAD_DIR . 'participants_csv' . DIRECTORY_SEPARATOR;

$UPLOAD_WEB = $HOST;
$CACHE_PATH_WEB = $HOST. '/site_management/';

$AGENTS_BRAND_ICON_WEB = $UPLOAD_WEB . '/uploads/agents/brand_icon/';
$AGENT_DOC_WEB = $UPLOAD_WEB . '/uploads/agents/agent_doc/';
$OLD_COVERAGE_WEB = $UPLOAD_WEB . '/uploads/old_coverage/';
$PHYSICAL_DOCUMENT_WEB = $UPLOAD_WEB . '/uploads/physical_document/';
$TRIGGER_IMAGE_WEB = $UPLOAD_WEB . '/uploads/trigger_image/';
$CKEDITOR_WEB = $UPLOAD_WEB . '/uploads/ckeditor/';
$FEES_ATTACHMENS_WEB = $UPLOAD_WEB. '/uploads/fees_attachments/';
$ATTACHMENS_WEB = $UPLOAD_WEB. '/uploads/attachments/';
$PRICE_MATRIX_CSV_WEB = $UPLOAD_WEB . '/uploads/price_matrix_csv/';
$CATEGORY_IMAGE_WEB = $UPLOAD_WEB . '/uploads/category_image/';
$ELIGIBILITY_FILES_WEB = $UPLOAD_WEB . '/uploads/eligibility_files/';
$BILLING_FILES_WEB = $UPLOAD_WEB . '/uploads/billing_files/';
$FULFILLMENT_FILES_WEB = $UPLOAD_WEB . '/uploads/fulfillment_files/';
$CSV_WEB = $UPLOAD_WEB . '/uploads/csv/';
$ETICKET_DOCUMENT_WEB = $UPLOAD_WEB . '/uploads/e_ticket_files/';
$COLLATERAL_DOCUMENT_WEB = $UPLOAD_WEB . '/uploads/collateral_document/';
$TMP_WEB = $UPLOAD_WEB . '/uploads/page_builder_tmp_files/';
$PAGE_COVER_WEB = $UPLOAD_WEB . '/uploads/page_cover_images/';
$PAGE_LOGO_WEB = $UPLOAD_WEB . '/uploads/page_logo_images/';
$GROUPS_BRAND_ICON_WEB = $UPLOAD_WEB . '/uploads/groups/brand_icon/';
$ACH_COMM_WEB = $UPLOAD_WEB . '/uploads/ACH_Payments/';
$CIRCLE_DOCUMENT_WEB = $UPLOAD_WEB . '/uploads/circle_files/';
$SUMMERNOTE_WEB = $UPLOAD_WEB . '/uploads/summernote_images/';
$RESOURCE_DOCUMENT_WEB = $UPLOAD_WEB . '/uploads/resource_files/';
$SYSTEM_RESOURCES_WEB = $UPLOAD_WEB . '/uploads/system_resources/';
$LIST_BILL_PAYMENT_FILES_WEB = $UPLOAD_WEB . '/uploads/list_bill_payment_files/';
$LIVE_CHAT_UPLOADS_WEB = $UPLOAD_WEB . '/uploads/live_chat';
$PARTICIPANTS_CSV_WEB = $UPLOAD_WEB . '/uploads/participants_csv';


$callingCode=array("+1","+2","+91","+40");
$callingCodeReplace=array("","","","");
$CALLING_CODE='+1';

$START_KEYWORDS = array('START', 'YES', 'UNSTOP');
$STOP_KEYWORDS = array('STOP', 'STOPALL', 'UNSUBSCRIBE', 'CANCEL', 'END', 'QUIT');
$TELNYX_START_KEYWORDS = array('START','UNSTOP');
$TELNYX_STOP_KEYWORDS = array('STOP', 'STOPALL', 'UNSUBSCRIBE', 'CANCEL', 'END', 'QUIT','STOP ALL');
$PLIVO_START_KEYWORDS = array('START','YES','RESUME','UNSTOP','GO');
$PLIVO_STOP_KEYWORDS = array('STOP', 'STOPALL', 'UNSUBSCRIBE', 'UNSUB', 'CANCEL', 'END', 'QUIT','STOP ALL');

// Used for Trigger Template
$memberLoginPage = $CUSTOMER_HOST.'/';
$agentLoginPage = $HOST.'/agents/';
$groupLoginPage = $HOST.'/groups/';

// flag used for send emails from local
$sendEmailLocal = true;

$ALLOWED_SUBSCRIPTION_STATUS = array('Active','Inactive Failed Billing','Pending', 'Pending Payment','Cancel','Inactive Member Request','Post Payment','Inactive');
$REENROLL_SUBSCRIPTION_STATUS = array('Inactive Failed Billing','Cancel','Inactive Member Request','Inactive');

$testSmsPhoneNoArr = array(9712028991,9429548647);

$GOOGLE_MAP_KEY = "AIzaSyDG6Mj_kMv44fq94dPSdvv9RwPFsY5ISHY";

$AAE_WEBSITE_HOST = $HOST."/quote";
$ENROLLMENT_WEBSITE_HOST = $HOST."";
$GROUP_ENROLLMENT_WEBSITE_HOST = $HOST;

$SITE_NAME = "TPA";
$DEFAULT_SITE_NAME = "TPA"; //this will used to display site name on project
$SITE_EMAIL = 'no-reply-tpa@benefitportal.com';
$NOREPLY_EMAIL = "no-reply-tpa@benefitportal.com";
$SITE_PHONE = '2087190164';
$DEFAULT_SITE_URL ='tpa.benefitportal.com';
$DEFAULT_LOGO_IMAGE = 'info-guy.png?_v=1.02';
$POWERED_BY_TEXT = "Powered by Trinityhealth Plan Administrators";
$POWERED_BY_LOGO = $HOST."/images/powered-by.svg?_v=1.01";
$ETICKET_SUPPORT_EMAIL = 'support-tpa@benefitportal.com';//Need to set forwording rule on this email

$DEFAULT_CHAT_SITE_NAME = "TPA";
$DEFAULT_CHAT_TITLE = "TPA Chat";
$DEFAULT_CHAT_LOGO = "images/logo_white.svg";
$DEFAULT_CHAT_DESCRIPTION = "";

$SPECIAL_DISPLAY_TITLE ="Member of TPA";
$SPECIAL_DISPLAY_LOGO =$HOST."/images/logo_white.svg";

$ALT_SITE_NAME = "TPA";

$DEFAULT_COMM_APPROVE_EMAIL = array("dharmesh@cyberxllc.com");
$DEFAULT_COMM_FINAL_APPROVE_EMAIL = array("dharmesh@cyberxllc.com");
$DEFAULT_COMM_POST_EMAIL = array("dharmesh@cyberxllc.com");

$S3_FOLDER_ENV = "live";

if($SITE_ENV == 'Live'){
	$S3_FOLDER_ENV = 'live';
} else if($SITE_ENV == 'Stag' || $SITE_ENV == "Development"){
	$S3_FOLDER_ENV = 'dev';
} else {
	$S3_FOLDER_ENV = 'local';
} 

$SIGNATURE_FILE_PATH = $S3_FOLDER_ENV.'/signature/';
$AGENT_AGREEMENT_CONTRACT_FILE_PATH = $S3_FOLDER_ENV.'/agent_agreement_contract/';
$ADMIN_AGREEMENT_CONTRACT_FILE_PATH = $S3_FOLDER_ENV.'/admin_agreement_contract/';
$GROUP_AGREEMENT_CONTRACT_FILE_PATH = $S3_FOLDER_ENV.'/group_agreement_contract/';
$ELIGIBILITY_FILES_PATH = $S3_FOLDER_ENV. '/eligibility_files/';
$NACHA_FILES_PATH = $S3_FOLDER_ENV. '/nacha_files/';
$NACHA_FILES_SFTP_PATH = $S3_FOLDER_ENV. '/nacha_files_sftp/';
$MEMBER_DOCUMENT_PATH = $S3_FOLDER_ENV.'/member_document/'; 

$MEMBER_STATUS = array('Active','Pending','Hold','Inactive','Post Payment');
$POLICY_STATUS = array('Active','Pending','Inactive','Post Payment');
$MEMBER_ABONDON_STATUS = array('Customer Abandon','Pending Quote','Pending Validation',"Post Payment");

$TRANSACTION_APPROVED_STATUS = array("settledSuccessfully", "complete");
$TRANSACTION_WAIT_STATUS = array("pendingSettlement","capturedPendingSettlement", "pendingsettlement");
$TRANSACTION_FAILED_STATUS = array("settlementError","declined","expired","generalError","failedReview","canceled","verifying","failed");
$CACHE_SITE_NAME = 'TPABENEFIT';

$_BOOTSTRAP_TAGS_MIN_LENGTH = 3;
$MAX_CARD_NUMBER = 16;
$MIN_CARD_NUMBER = 12;

$MAX_ACCOUNT_NUMBER = 17;
$MIN_ACCOUNT_NUMBER = 6;

$SYMMETRYAPIKEY = "36qqxZqathMPJaXcUhFtHCq5usBN1esUjDHMdDoPky4De";
$SYMMETRYAPIURL = "https://calculators.symmetry.com/api/calculators";
$STATE_TAX_RATES = array("AL"=>5,"AK"=>0,"AZ"=>8,"AR"=>5.9,"CA"=>12.3,"CO"=>0,"CT"=>6.99,"DE"=>6.6,"FL"=>0,"GA"=>5.75,"HI"=>11,"ID"=>6.925,"IL"=>4.95,"IN"=>3.23,"IA"=>8.53,"KS"=>5.7,"KY"=>5,"LA"=>6,"ME"=>7.15,"MD"=>5.75,"MA"=>5,"MI"=>4.25,"MN"=>9.85,"MS"=>5,"MO"=>5.4,"MT"=>6.9,"NE"=>6.84,"NV"=>0,"NH"=>0,"NJ"=>10.75,"NM"=>5.9,"NY"=>8.82,"NC"=>5.25,"ND"=>2.9,"OH"=>4.797,"OK"=>5,"OR"=>9.9,"PA"=>3.07,"RI"=>5.99,"SC"=>7,"SD"=>0,"TN"=>0,"TX"=>0,"UT"=>4.95,"VT"=>8.75,"VA"=>5.75,"WA"=>0,"WV"=>6.5,"WI"=>7.65,"WY"=>0,"DC"=>8.95);

// Date settings
$FULL_DATE_FORMAT = 'm/d/Y H:i:s';
$DATE_FORMAT = 'm/d/Y';
$TIME_FORMAT = 'H:i:s';

$DEFAULT_TAX_STATE = "Texas";

$DEFAULT_ENROLLMENT_EMAIL = array('karan@cyberxllc.com');
$DEFAULT_ORDER_EMAIL = array("karan@cyberxllc.com");
$DEFAULT_TRIGGER_EMAIL = 'karan@cyberxllc.com';
$DEFAULT_TRIGGER_SMS = '+919712028991';

$CC_DECLINE_EMAIL = "karan@cyberxllc.com";

$CUSTOMER_ACTIVE_STATUS=array('Active','Inactive','Pending');
$AGENT_ABANDON_STATUS=array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Agent Abandon');

$pageinate_html = array(
    'text_prev' => '&lt;',
	'text_next' => '&gt;',
	'text_first' => '&lt;',
	'text_last' => '&gt;',
    'text_ellipses' => '...',
    'class_ellipses' => 'ellipses',
    'class_dead_links' => 'disabled',
    'class_live_links' => 'live-link',
    'class_current_page' => 'active',
);
$per_page = 25;
$csv_line = "\n";
$csv_seprator = "\t";
 
$table_class = "color-table info-table table table-striped";

$PRICE_TAG = '$';
$PRICE_FIELD = 'price';
$CUST_PRICE_FIELD = 'cust_price';
$MSP_PRICE_FIELD = 'msp_price';
$CUST_MSP_PRICE_FIELD = 'cust_usa_msp';

define("LEAD_EMAIL_RESET_DAYS",7);
define("LEAD_SMS_RESET_DAYS",30);

$node_boder_class = array();
//list of types for agentra
$node_boder_class['Affiliates'] = 'node-border-primary';
$node_boder_class['Ambassadors'] = 'node-border-blue';
$node_boder_class['Group'] = 'node-border-success';
$node_boder_class['Customer'] = 'node-border-danger';
$node_boder_class['Residents'] = 'node-border-success';
$node_boder_class['Agent'] = 'node-border-blue';

$SALARY_PASSWORD = '79V78Z';
$SMS_CONTENT = "You've expressed interest in a solution from op29. 2-4 msg/mo, no purch req. Std msg&data rates apply. Reply YES to confirm. To opt-out, text STOP";
$TEST_MO_NUMBER=array('8200629743','7405445244','9726973578','7838970163','8999909969','7984974053','9429548647','8200565507');
?>
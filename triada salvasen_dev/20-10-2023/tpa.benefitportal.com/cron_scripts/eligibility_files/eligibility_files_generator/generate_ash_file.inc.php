<?php
// ASH eligibility file
$sch_params = array();
$incr = '';
$generate_change_file = false;
if($file_type == "full_file"){
  $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
   $incr .= " AND w.eligibility_date <= :to_date";
}else if($file_type == "specific_member_file"){
  if($file_type == "specific_member_file"){
    if($memberBy == "member_id"){
      $incr .= " AND c.id IN (".$member_ids.") AND c.type='Customer'";
    }else if($memberBy == "member_name"){
      $incr .= " AND c.id IN (".$member_name.") AND c.type='Customer'";
    }
  }
}else if($file_type == "specific_agent_file"){
  $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
  $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
  $incr .= " AND (DATE(c.created_at) >= :since_date OR w.term_date_set >= :since_date OR w.eligibility_date >= :since_date)";
  $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)";
  $incr .= " AND s.id IN ('".$agent_ids."') AND s.type='Agent'";
}else if($file_type == "schedule_change_file" || $file_type == "add_change_file"){

    if($file_type == "add_change_file"){
    $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
    $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
    $incr .= " AND (DATE(c.created_at) >= :since_date OR w.term_date_set >= :since_date OR w.eligibility_date >= :since_date)";
    $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)";
  } 

  $file = (__DIR__) . "/" .$file_key . "_Main_File.csv";

  if(!file_exists($file)){
    fopen($file,"w");  
  }

   $csvData = csvToArraywithFields($file);
   if(!empty($csvData)){
    $generate_change_file = true;
   }
  if($generate_change_file){
    $ExistsMembers = $csvData;
    $ExistsMembersData = array();
    foreach ($ExistsMembers as $key => $row) {
      if(in_array($row['RECORD_CODE'],array(1))) {
        $ExistsMembersData[$row['UNIQUE_IDENTIFIER']] = $row;
      }
    }
    $ExistsMemberIds = array_column($csvData,'UNIQUE_IDENTIFIER');
    $ExistsMemberIds = array_unique($ExistsMemberIds);
    $ExistsDependents = $csvData;
    $ExistsDependentsData = array();
    foreach ($ExistsDependents as $key => $row) {
      if(!in_array($row['RECORD_CODE'],array(1))) {
          $ExistsDependentsData[$row['UNIQUE_IDENTIFIER'] . $row['RECORD_CODE'] . $row['PRODUCT_SUBCODE'] . $row['PRODUCT_CODE']] = $row;
      }
    }
  }
}

$selSql = "SELECT p.parent_product_id,ppt.id as product_subcode,c.lname,c.mname,c.fname,c.address,c.address_2,c.city,c.state,c.zip,c.cell_phone,c.birth_date, c.status,  
           w.product_id,c.rep_id as member_id,s.rep_id as agent_id,c.email,
           w.eligibility_date,w.termination_date,c.gender as gender,w.id as ws_id,w.website_id as policy_no
           FROM customer c
           JOIN customer s ON (s.id=c.sponsor_id)
           JOIN website_subscriptions w ON(w.customer_id = c.id)
           JOIN customer_enrollment ce ON (w.id=ce.website_id)
           JOIN prd_main p ON (p.id=w.product_id)
           JOIN prd_plan_type ppt ON(ppt.id=w.prd_plan_type_id)
           WHERE c.status IN ('Active','Inactive') AND p.id IN($prd_ids) AND c.is_deleted='N' AND p.is_deleted='N' $incr  GROUP BY w.id ORDER BY w.product_id DESC";
$orderRows = $pdo->select($selSql,$sch_params);

$RECORD_CODE = array(
    'Employee' => 1,
    'Spouse' => 2,
    'Child' => 3,
  );
  $RELATIONSHIP=array(
    'employee' => 'P',
    'husband' => 'S',
    'wife' => 'S',
    'son' => 'C',
    'daughter' => 'C',
    'other' => 'C',
  );
$GENDERS=array(
  'Male' => 'M',
  'Female' => 'F',
);
$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
$header_row = "";
$content = ""; // we are not going to add header row at this time
$eligibility_content = ""; // we are not going to add header row at this time

$header_row = 'CORP ID'. $csv_seprator
          .'AGENT ID' . $csv_seprator
          .'PRODUCT_CODE' . $csv_seprator
          .'PRODUCT_SUBCODE' . $csv_seprator
          .'ACTION CODE' . $csv_seprator
          .'ACTIVE / EFFECTIVE DATE' . $csv_seprator
          .'INACTIVE DATE' . $csv_seprator
          .'UNIQUE_IDENTIFIER' . $csv_seprator
          .'RECORD_CODE' . $csv_seprator
          .'FIRSTNAME' . $csv_seprator
          .'MIDDLEINITIAL' . $csv_seprator
          .'LASTNAME' . $csv_seprator
          .'DATE of BIRTH' . $csv_seprator
          .'GENDER' . $csv_seprator
          .'RELATIONSHIP' . $csv_seprator
          .'ADDRESS 1' . $csv_seprator
          .'ADDRESS 2 ' . $csv_seprator
          .'CITY' . $csv_seprator
          .'STATE' . $csv_seprator
          .'ZIPCODE' . $csv_seprator
          .'EMAIL_ADDRESS' . $csv_seprator
          .'PHONE_NUMBER' . $csv_seprator
          .'FAX NUMBER' . $csv_seprator
          .'DRIVERS LICENSE NUMBER' . $csv_seprator
          .'SOCIAL SECURITY NUMBER' . $csv_seprator
          .'PAYMENT PROCESS' . $csv_seprator
          .'PAYMENT TYPE' . $csv_seprator
          .'PAYMENT AMOUNT' . $csv_seprator
          .'CC TYPE' . $csv_seprator
          .'CC NUMBER' . $csv_seprator
          .'CC EXPIRATION MONTH' . $csv_seprator
          .'CC EXPIRATION YEAR' . $csv_seprator
          .'CC SECURITY CODE' . $csv_seprator
          .'DRAFT TYPE' . $csv_seprator
          .'DRAFT ROUTING NUMBER' . $csv_seprator
          .'DRAFT ACCOUNT NUMBER' . $csv_seprator
          .'DRAFT BANK NAME' . $csv_seprator
          .'PAYMENT FIRST NAME ' . $csv_seprator
          .'PAYMENT LAST NAME ' . $csv_seprator
          .'PAYMENT ADDRESS' . $csv_seprator
          .'PAYMENT CITY' . $csv_seprator
          .'PAYMENT STATE' . $csv_seprator
          .'PAYMENT ZIP CODE' . $csv_seprator
          .'BENEFICIARY NAME' . $csv_seprator
          .'BENEFICIARY ADDRESS' . $csv_seprator
          .'BENEFICIARY CITY' . $csv_seprator
          .'BENEFICIARY STATE' . $csv_seprator
          .'BENEFICIARY ZIP CODE' . $csv_seprator
          .'BENEFICIARY RELATIONSHIP' . $csv_seprator
          .'BENEFICIARY DOB' . $csv_seprator
          .'HIRE DATE' . $csv_seprator
          .'COMPANY' . $csv_seprator
          .'SOURCE' . $csv_seprator
          .'SOURCE DETAIL' . $csv_seprator
          .'POSITION' . $csv_seprator
          .'DEPARTMENT' . $csv_seprator
          .'DIVISION' . $csv_seprator
          .'WEBSITE' . $csv_seprator
          .'LEAD FLAG' . $csv_seprator
          .'USERNAME' . $csv_seprator
          .'PASSWORD' . $csv_seprator
          .'PIN' . $csv_seprator
          .'PHONE 2' . $csv_seprator
          .'PHONE 3' . $csv_seprator
          .'FIRST BILLING DATE' . $csv_seprator
          .'NEXT BILLING / RECURRING DATE' . $csv_seprator
          .'NEW MEMBER NOTE' . $csv_seprator
          .'NEW HISTORY NOTE' . $csv_seprator
          .'CREATED DATE' . $csv_seprator
          .'FULFILLMENT DATE' . $csv_seprator
          .'ORIGINAL SALE DATE' . $csv_seprator
          .'CODE (PLAN NUMBER)' . $csv_seprator
          .'TPV DATE TIME' . $csv_seprator
          .'TPV CODE' . $csv_seprator
          .'USER CREATED DATE' . $csv_seprator
          .'BENEFICIARY PHONE' . $csv_seprator
          .'SHIPPING USE PRIMARY ' . $csv_seprator
          .'SHIPPING FIRSTNAME' . $csv_seprator
          .'SHIPPING LASTNAME' . $csv_seprator
          .'SHIPPING ADDRESS' . $csv_seprator
          .'SHIPPING ADDRESS2 ' . $csv_seprator
          .'SHIPPING CITY ' . $csv_seprator
          .'SHIPPING STATE' . $csv_seprator
          .'SHIPPING ZIPCODE' . $csv_seprator
          .'SHIPPING INTERNATIONALSTATE' . $csv_seprator
          .'SHIPPING COUNTRY ' . $csv_seprator
          .'SHIPPING PHONE ' . $csv_seprator
          .'SHIPPING MESSAGE' . $csv_seprator
          .'SHIPPING TRACKINGCARRIER' . $csv_seprator
          .'SHIPPING TRACKINGCODE' . $csv_seprator
          .'PRODUCT AGENT ID' . $csv_seprator
          .'PRODUCT ENROLLER ID ' . $csv_seprator
          .'PRE-AUTH CODE ' . $csv_seprator
          .'PRODUCT FEE' . $csv_seprator
          .'HEIGHT' . $csv_seprator
          .'WEIGHT' . $csv_seprator
          .'PERIOD' . $csv_seprator
          .'LANGUAGE' . $csv_seprator
          .'LEADSTATUSDESCRIPTION' . $csv_seprator
          .'SMOKER' . $csv_seprator
          .'ENROLLMENT FEE' . $csv_seprator
          ."" . $csv_line; //Line of Business

$content .= $header_row;
if($generate_change_file){
  $eligibility_content .= $header_row;
}

if ($orderRows) {
  $prd_array=array(
      // 26092
      '92'=>array('product_code'=>'26092'),
      
      // 26093
      '93'=>array('product_code'=>'26093'),
      
      // 26094
      '94'=>array('product_code'=>'26094'),
      
      // 24910
      '169'=>array('product_code'=>'24910'),
      '173'=>array('product_code'=>'24910'),
      
      // 24911
      // '570'=>array('product_code'=>'24911'),
      // '571'=>array('product_code'=>'24911'),
      // '572'=>array('product_code'=>'24911'),
      // '573'=>array('product_code'=>'24911'),
      // '574'=>array('product_code'=>'24911'),
      // '575'=>array('product_code'=>'24911'),
      // 24912
      // '564'=>array('product_code'=>'24912'),
      // '565'=>array('product_code'=>'24912'),
      // '566'=>array('product_code'=>'24912'),
      // '576'=>array('product_code'=>'24912'),
      // '577'=>array('product_code'=>'24912'),
      // '578'=>array('product_code'=>'24912'),
      // 24914
      // '567'=>array('product_code'=>'24914'),
      // '568'=>array('product_code'=>'24914'),
      // '569'=>array('product_code'=>'24914'),
      // 26121
      '87'=>array('product_code'=>'26121'),
      '187'=>array('product_code'=>'26121'),
      // 26122
      '95'=>array('product_code'=>'26122'),
      // '524'=>array('product_code'=>'26122'),
      // 26123
      '96'=>array('product_code'=>'26123'),
      // '525'=>array('product_code'=>'26123'),
      // 26124
      '97'=>array('product_code'=>'26124'),
      // '526'=>array('product_code'=>'26124'),
      // 26103
      // '477'=>array('product_code'=>'26103'),
      // '527'=>array('product_code'=>'26103'),
      // 26104
      // '478'=>array('product_code'=>'26104'),
      // '528'=>array('product_code'=>'26104'),
      // 26105
      // '479'=>array('product_code'=>'26105'),
      // '529'=>array('product_code'=>'26105'),
      // 26106
      // '480'=>array('product_code'=>'26106'),
      // '530'=>array('product_code'=>'26106'),
      // 24916
      '171'=>array('product_code'=>'24916'),
      '174'=>array('product_code'=>'24916'),
      // '446'=>array('product_code'=>'24916'),
      // '391'=>array('product_code'=>'24916'),
      // '481'=>array('product_code'=>'24916'),
      // '485'=>array('product_code'=>'24916'),
      // '531'=>array('product_code'=>'24916'),
      // '535'=>array('product_code'=>'24916'),
      // '591'=>array('product_code'=>'24916'),
      // 26095
      '98'=>array('product_code'=>'26095'),
      // '486'=>array('product_code'=>'26095'),
      // '532'=>array('product_code'=>'26095'),
      // '536'=>array('product_code'=>'26095'),
      // 26096
      '99'=>array('product_code'=>'26096'),
      // '487'=>array('product_code'=>'26096'),
      // '533'=>array('product_code'=>'26096'),
      // '537'=>array('product_code'=>'26096'),
      // 26097
      '100'=>array('product_code'=>'26097'),
      // '488'=>array('product_code'=>'26097'),
      // '534'=>array('product_code'=>'26097'),
      // '538'=>array('product_code'=>'26097'),
      // 26125
      '89'=>array('product_code'=>'26125'),
      // '539'=>array('product_code'=>'26125'),
      // 26126
      '101'=>array('product_code'=>'26126'),
      // '540'=>array('product_code'=>'26126'),
      // 26127
      '102'=>array('product_code'=>'26127'),
      // '541'=>array('product_code'=>'26127'),
      // 26128
      '103'=>array('product_code'=>'26128'),
      // '542'=>array('product_code'=>'26128'),
      // 26107
      // '493'=>array('product_code'=>'26107'),
      // '543'=>array('product_code'=>'26107'),
      // 26108
      // '494'=>array('product_code'=>'26108'),
      // '544'=>array('product_code'=>'26108'),
      // 26109
      // '495'=>array('product_code'=>'26109'),
      // '545'=>array('product_code'=>'26109'),
      // 26110
      // '496'=>array('product_code'=>'26110'),
      // '546'=>array('product_code'=>'26110'),
      // 26912
      '90'=>array('product_code'=>'26912'),
      // '501'=>array('product_code'=>'26912'),
      // '547'=>array('product_code'=>'26912'),
      // '551'=>array('product_code'=>'26912'),
      // 26098
      '104'=>array('product_code'=>'26098'),
      // '502'=>array('product_code'=>'26098'),
      // '548'=>array('product_code'=>'26098'),
      // '552'=>array('product_code'=>'26098'),
      // 26099
      '105'=>array('product_code'=>'26099'),
      // '503'=>array('product_code'=>'26099'),
      // '549'=>array('product_code'=>'26099'),
      // '553'=>array('product_code'=>'26099'),
      // 26100
      '106'=>array('product_code'=>'26100'),
      // '504'=>array('product_code'=>'26100'),
      // '550'=>array('product_code'=>'26100'),
      // '554'=>array('product_code'=>'26100'),
      // 26129
      '91'=>array('product_code'=>'26129'),
      // '555'=>array('product_code'=>'26129'),
      // 26130
      '109'=>array('product_code'=>'26130'),
      // '556'=>array('product_code'=>'26130'),
      // 26131
      '110'=>array('product_code'=>'26131'),
      // '557'=>array('product_code'=>'26131'),
      // 26132
      '111'=>array('product_code'=>'26132'),
      // '558'=>array('product_code'=>'26132'),
      // 26111
      // '509'=>array('product_code'=>'26111'),
      // '559'=>array('product_code'=>'26111'),
      // 26112
      // '510'=>array('product_code'=>'26112'),
      // '560'=>array('product_code'=>'26112'),
      // 26113
      // '511'=>array('product_code'=>'26113'),
      // '561'=>array('product_code'=>'26113'),
      // 26114
      // '512'=>array('product_code'=>'26114'),
      // '562'=>array('product_code'=>'26114'),
      //Pro Share
      '172'=>array('product_code'=>'24912'),
      // '444'=>array('product_code'=>'24912'),
      // '447'=>array('product_code'=>'24912'),
      '175'=>array('product_code'=>'24912'),
      //HI25
      '176'=>array('product_code'=>'24913'),
      '179'=>array('product_code'=>'24913'),
      //HI50
      '177'=>array('product_code'=>'24914'),
      '180'=>array('product_code'=>'24914'),
      //HI75
      '178'=>array('product_code'=>'24915'),
      '181'=>array('product_code'=>'24915')
    );
  $gender='';
  foreach ($orderRows as $order) {
    if($order['gender']=='Male'){
      $gender='M';
    }elseif($order['gender']=='Female'){
      $gender='F';
    }

    // $order['product_subcode'];
    $prd_sub_array=array(
        '1'=>array('product_code'=>'51'),//Choice Share
        '2'=>array('product_code'=>'2025'),//Classic Share
        '3'=>array('product_code'=>'2024'),//Pro Share
        '4'=>array('product_code'=>'3391'),//HI25
      );

    $elig_date=$term_date=$product_code=$prd_subcode=$action_code="";

    if(!empty($order['termination_date']) && strtotime($order['termination_date']) > 0 && $order['termination_date']!='0000-00-00')
    {
      $term_date=date("m-d-Y",strtotime($order['termination_date']));
    }

    if(!empty($order['eligibility_date']) && strtotime($order['eligibility_date']) > 0)
    {
      $elig_date=date("m-d-Y",strtotime($order['eligibility_date']));
    }

    if(array_key_exists($order['product_subcode'],$prd_sub_array))
    {    
      $prd_subcode=$prd_sub_array[$order['product_subcode']]['product_code'];
      if(in_array($order['product_id'],array(86,88,90,173,174,175)))
      {
        if($order['product_subcode'] == 1)
        {
          $prd_subcode='115';
        }
        if($order['product_subcode'] == 4)
        {
            $prd_subcode='53';
        }
      }
    }

    if(array_key_exists($order['product_id'],$prd_array))
    {    
      $product_code=$prd_array[$order['product_id']]['product_code'];
    }else
    {    
      $product_code=$prd_array[$order['parent_product_id']]['product_code'];
    }
    
    if($order['status']=="Terminated" || $order['plan_status']=="Terminated" || !empty($term_date))
    {
        $action_code='T';
    }
    // $coverage= $order['coverage'] == 'M' ? 'N' : $order['coverage'];
    $order['fname'] = valid_csv_cell_value($order['fname']);
    $order['mname'] = valid_csv_cell_value($order['mname']);
    $order['lname'] = valid_csv_cell_value($order['lname']);
    $order['address'] = isset($order['address'])?valid_csv_cell_value($order['address']):'';
    $order['address_2'] = isset($order['address_2'])?valid_csv_cell_value($order['address_2']):'';
    
    $clm_aa='';
     //  check if member is already exists or new member
      $addMember = false;
      if($generate_change_file){
        if(in_array($order['member_id'],$ExistsMemberIds)){
          $is_Updated = compareASHRow($ExistsMembersData[$order['member_id']],$order);
          //  if member detail is updates then add member to eligibility file
          if($is_Updated === true) {
            $addMember = true;
          } else {
            $addMember = false;
          }
        }else{
          //  if new member then add member to eligibility file
          $clm_aa='LB';
          $addMember = true;
        }
      }  
    $tmp_row = '';
    $tmp_row .= '1347' . $csv_seprator .//CORP ID
        $field_seprator.'342962' .$field_seprator. $csv_seprator .//Agent Id
        $field_seprator.$product_code .$field_seprator. $csv_seprator .//PRODUCT CODE
        $field_seprator.$prd_subcode .$field_seprator. $csv_seprator .//PRODUCT SUBCODE
        $field_seprator.$action_code .$field_seprator. $csv_seprator . //ACTION CODE
        $field_seprator.$elig_date .$field_seprator. $csv_seprator .// EFFECTIVE DATE        
        $field_seprator.$term_date .$field_seprator. $csv_seprator .//INACTIVE DATE
        $field_seprator.$order['member_id'].$field_seprator. $csv_seprator .//UNIQUE IDENTIFIER
        $field_seprator.$RECORD_CODE['Employee'].$field_seprator. $csv_seprator .//RECORD CODE
        $field_seprator.$order['fname'].$field_seprator. $csv_seprator .//Firstname
        $field_seprator.$order['mname'].$field_seprator. $csv_seprator .//MiddleInitial
        $field_seprator.$order['lname'].$field_seprator. $csv_seprator .//Lastname
        $field_seprator.date("m-d-Y",strtotime($order['birth_date'])) .$field_seprator. $csv_seprator .//Date OF Birth Date
        $field_seprator.$GENDERS[$order['gender']] .$field_seprator. $csv_seprator.//gender
        $field_seprator.$RELATIONSHIP['employee'].$field_seprator. $csv_seprator .//Relationship
        $field_seprator.$order['address'].$field_seprator. $csv_seprator .//Address
        $field_seprator.$order['address_2'].$field_seprator. $csv_seprator .//Address
        $field_seprator.$order['city'].$field_seprator. $csv_seprator .//City
        $field_seprator.getname("states_c",$order['state'],"short_name","name") .$field_seprator. $csv_seprator .//State Abbreviation
        $field_seprator.$order['zip'] .$field_seprator. $csv_seprator .//Zip
        $field_seprator.$order['email'] .$field_seprator. $csv_seprator .//Email
        $field_seprator.$order['cell_phone'] .$field_seprator. $csv_seprator .//Cell Phone
        "" . $csv_seprator . //FAX NUMBER
        "" . $csv_seprator . //DRIVERS LICENSE NUMBER
        "" . $csv_seprator . //SOCIAL SECURITY NUMBER 
        "" . $csv_seprator . //PAYMENT PROCESS
        $field_seprator.$clm_aa .$field_seprator. $csv_seprator .//PAYMENT TYPE 
        "" . $csv_seprator . //PAYMENT AMOUNT
        "" . $csv_seprator . //CC TYPE
        "" . $csv_seprator . //CC NUMBER
        "" . $csv_seprator . //CC EXPIRATION MONTH
        "" . $csv_seprator . //CC EXPIRATION YEAR
        "" . $csv_seprator . //CC SECURITY CODE
        "" . $csv_seprator . //DRAFT TYPE
        "" . $csv_seprator . //DRAFT ROUTING NUMBER 
        "" . $csv_seprator . //DRAFT ACCOUNT NUMBER
        "" . $csv_seprator . //DRAFT BANK NAME
        "" . $csv_seprator . //PAYMENT FIRST NAME
        "" . $csv_seprator . //PAYMENT LAST NAME
        "" . $csv_seprator . //PAYMENT ADDRESS
        "" . $csv_seprator . //PAYMENT CITY 
        "" . $csv_seprator . //PAYMENT STATE
        "" . $csv_seprator . //PAYMENT ZIP CODE
        "" . $csv_seprator . //BENEFICIARY NAME
        "" . $csv_seprator . //BENEFICIARY ADDRESS
        "" . $csv_seprator . //BENEFICIARY CITY 
        "" . $csv_seprator . //BENEFICIARY STATE 
        "" . $csv_seprator . //BENEFICIARY ZIP CODE 
        "" . $csv_seprator . //BENEFICIARY RELATIONSHIP 
        "" . $csv_seprator . //BENEFICIARY DOB 
        "" . $csv_seprator . //HIRE DATE 
        "" . $csv_seprator . //COMPANY
        "" . $csv_seprator . //SOURCE
        "" . $csv_seprator . //SOURCE DETAIL
        "" . $csv_seprator . //POSITION 
        "" . $csv_seprator . //DEPARTMENT
        "" . $csv_seprator . //DIVISION 
        "" . $csv_seprator . //WEBSITE
        "" . $csv_seprator . //LEAD FLAG
        "" . $csv_seprator . //USERNAME
        "" . $csv_seprator . //PASSWORD
        "" . $csv_seprator . //PIN
        "" . $csv_seprator . //PHONE 2 
        "" . $csv_seprator . //PHONE 3 
        "" . $csv_seprator . //FIRST BILLING DATE 
        "" . $csv_seprator . //NEXT BILLING / RECURRING DATE 
        "" . $csv_seprator . //NEW MEMBER NOTE
        "" . $csv_seprator . //NEW HISTORY NOTE  
        "" . $csv_seprator . //CREATED DATE  
        "" . $csv_seprator . //FULFILLMENT DATE
        "" . $csv_seprator . //ORIGINAL SALE DATE 
        $order['policy_no'] . $csv_seprator . //CODE (POLICY NUMBER) 
        "" . $csv_seprator . //TPV DATE TIME 
        "" . $csv_seprator . //TPV CODE 
        "" . $csv_seprator . //USER CREATED DATE
        "" . $csv_seprator . //BENEFICIARY PHONE 
        "" . $csv_seprator . //SHIPPING USE PRIMARY
        "" . $csv_seprator . //SHIPPING FIRSTNAME
        "" . $csv_seprator . //SHIPPING LASTNAME 
        "" . $csv_seprator . //SHIPPING ADDRESS 
        "" . $csv_seprator . //SHIPPING ADDRESS2 
        "" . $csv_seprator . //SHIPPING CITY  
        "" . $csv_seprator . //SHIPPING STATE 
        "" . $csv_seprator . //SHIPPING ZIPCODE 
        "" . $csv_seprator . //NSHIPPING INTERNATIONAL STATE
        "" . $csv_seprator . //SHIPPING COUNTRY 
        "" . $csv_seprator . //SHIPPING PHONE  
        "" . $csv_seprator . //SHIPPING MESSAGE  
        "" . $csv_seprator . //SHIPPING TRACKINGCARRIER
        "" . $csv_seprator . //SHIPPING TRACKINGCODE 
        "" . $csv_seprator . //PRODUCT AGENT ID
        "" . $csv_seprator . //PRODUCT ENROLLER ID 
        "" . $csv_seprator . //PRE-AUTH CODE 
        "" . $csv_seprator . //PRODUCT FEE  
        "" . $csv_seprator . //HEIGHT
        "" . $csv_seprator . //WEIGHT
        "" . $csv_seprator . //PERIOD
        "" . $csv_seprator . //LANGUAGE
        "" . $csv_seprator . //LEADSTATUSDESCRIPTION
        "" . $csv_seprator . //SMOKER 
        "" . $csv_seprator . //ENROLLMENT FEE 
        "" . $csv_line; //Line of Business
        
      $content .= $tmp_row;
      if($addMember) {
        $eligibility_content .= $tmp_row;
      }

    //selecting dependent details
      $DepSql = "SELECT *,terminationDate as termination_date FROM customer_dependent WHERE website_id=:ws_id ORDER BY id ASC";
      $depRows = $pdo->select($DepSql, array(":ws_id" => $order['ws_id']));
      if ($depRows) {
        $count = 0;
        $child_code = 3;
        foreach ($depRows as $dep) {
          $count++;
          $dep['gender']=($dep['gender']=='Male'?'M':'F');
          $dep['relation'] = strtolower($dep['relation']);

          if(getRevRelation($dep['relation']) != 'Child' && !empty($RECORD_CODE[getRevRelation($dep['relation'])])) {
            $record_code = $RECORD_CODE[getRevRelation($dep['relation'])];
          } else {
            $record_code = $child_code;
          }

          if(empty($dep['eligibility_date'])){
            $dep_eligibility_date = date('m/d/Y',strtotime($order['eligibility_date']));
          }else{
            if(strtotime($dep['eligibility_date']) < strtotime($order['eligibility_date'])){
              $dep_eligibility_date = date('m/d/Y',strtotime($order['eligibility_date']));
            }else{
              $dep_eligibility_date = date('m/d/Y',strtotime($dep['eligibility_date']));
            }
          }

          $dep_termination_date = $term_date;
          if(!empty($dep['termination_date']) && strtotime($dep['termination_date']) > 0){
              $dep_termination_date = date('m-d-Y',strtotime($dep['termination_date']));
          }
          $dep_clm_aa='';
          // $dep_action_code="";
          // if($dep['status']=='Terminated')
          // {
          //     $dep_action_code='T';
          // }
           //  if member id exists then we compare the dependents details
          $addDependent = false;
          if($generate_change_file){
            if(in_array($order['member_id'],$ExistsMemberIds)){
              $is_Updated = dependentsASHRow($ExistsDependentsData[$order['member_id'] . $record_code . $prd_subcode . $product_code],$dep);
              if($is_Updated === true) {
                $addDependent = true;
              }else{
                $addDependent = false;
              }
            }else{
              $dep_clm_aa='LB';
              $addDependent = true;
            }
          }

          if($clm_aa != $dep_clm_aa){
            $dep_clm_aa = $clm_aa;
          }
        
        $tmp_row = '';
        $tmp_row .= '1347' . $csv_seprator .//CORP ID
        $field_seprator.'342962' .$field_seprator. $csv_seprator .//Agent Id
        $field_seprator.$product_code .$field_seprator. $csv_seprator .//PRODUCT CODE
        $field_seprator.$prd_subcode .$field_seprator. $csv_seprator .//PRODUCT SUBCODE
        $field_seprator.$action_code .$field_seprator. $csv_seprator . //ACTION CODE
        $field_seprator.date("m-d-Y",strtotime($dep_eligibility_date)) .$field_seprator. $csv_seprator .// EFFECTIVE DATE        
        $field_seprator.$dep_termination_date .$field_seprator. $csv_seprator .//INACTIVE DATE
        $field_seprator.$order['member_id'].$field_seprator. $csv_seprator .//UNIQUE IDENTIFIER
        $field_seprator.$record_code.$field_seprator. $csv_seprator .//RECORD CODE
        $field_seprator.$dep['fname'].$field_seprator. $csv_seprator .//Firstname
        $field_seprator.$dep['mname'].$field_seprator. $csv_seprator .//MiddleInitial
        $field_seprator.$dep['lname'].$field_seprator. $csv_seprator .//Lastname
        $field_seprator.date("m-d-Y",strtotime($dep['birth_date'])) .$field_seprator. $csv_seprator .//Date OF Birth Date
        $field_seprator.$dep['gender'] .$field_seprator. $csv_seprator.//gender
        $field_seprator.$RELATIONSHIP[$dep['relation']].$field_seprator. $csv_seprator .//Relationship
        "" . $csv_seprator .//Address
        "" . $csv_seprator .//Address
        "" . $csv_seprator .//City
        "" . $csv_seprator .//State Abbreviation
        "" . $csv_seprator .//Zip
        $field_seprator.$dep['email'] .$field_seprator. $csv_seprator .//Email
        $field_seprator.$dep['phone'] .$field_seprator. $csv_seprator .//Cell Phone
        "" . $csv_seprator . //FAX NUMBER
        "" . $csv_seprator . //DRIVERS LICENSE NUMBER
        "" . $csv_seprator . //SOCIAL SECURITY NUMBER 
        "" . $csv_seprator . //PAYMENT PROCESS
        $field_seprator.$clm_aa .$field_seprator. $csv_seprator .//PAYMENT TYPE 
        "" . $csv_seprator . //PAYMENT AMOUNT
        "" . $csv_seprator . //CC TYPE
        "" . $csv_seprator . //CC NUMBER
        "" . $csv_seprator . //CC EXPIRATION MONTH
        "" . $csv_seprator . //CC EXPIRATION YEAR
        "" . $csv_seprator . //CC SECURITY CODE
        "" . $csv_seprator . //DRAFT TYPE
        "" . $csv_seprator . //DRAFT ROUTING NUMBER 
        "" . $csv_seprator . //DRAFT ACCOUNT NUMBER
        "" . $csv_seprator . //DRAFT BANK NAME
        "" . $csv_seprator . //PAYMENT FIRST NAME
        "" . $csv_seprator . //PAYMENT LAST NAME
        "" . $csv_seprator . //PAYMENT ADDRESS
        "" . $csv_seprator . //PAYMENT CITY 
        "" . $csv_seprator . //PAYMENT STATE
        "" . $csv_seprator . //PAYMENT ZIP CODE
        "" . $csv_seprator . //BENEFICIARY NAME
        "" . $csv_seprator . //BENEFICIARY ADDRESS
        "" . $csv_seprator . //BENEFICIARY CITY 
        "" . $csv_seprator . //BENEFICIARY STATE 
        "" . $csv_seprator . //BENEFICIARY ZIP CODE 
        "" . $csv_seprator . //BENEFICIARY RELATIONSHIP 
        "" . $csv_seprator . //BENEFICIARY DOB 
        "" . $csv_seprator . //HIRE DATE 
        "" . $csv_seprator . //COMPANY
        "" . $csv_seprator . //SOURCE
        "" . $csv_seprator . //SOURCE DETAIL
        "" . $csv_seprator . //POSITION 
        "" . $csv_seprator . //DEPARTMENT
        "" . $csv_seprator . //DIVISION 
        "" . $csv_seprator . //WEBSITE
        "" . $csv_seprator . //LEAD FLAG
        "" . $csv_seprator . //USERNAME
        "" . $csv_seprator . //PASSWORD
        "" . $csv_seprator . //PIN
        "" . $csv_seprator . //PHONE 2 
        "" . $csv_seprator . //PHONE 3 
        "" . $csv_seprator . //FIRST BILLING DATE 
        "" . $csv_seprator . //NEXT BILLING / RECURRING DATE 
        "" . $csv_seprator . //NEW MEMBER NOTE
        "" . $csv_seprator . //NEW HISTORY NOTE  
        "" . $csv_seprator . //CREATED DATE  
        "" . $csv_seprator . //FULFILLMENT DATE
        "" . $csv_seprator . //ORIGINAL SALE DATE 
        $order['policy_no'] . $csv_seprator . //CODE (POLICY NUMBER) 
        "" . $csv_seprator . //TPV DATE TIME 
        "" . $csv_seprator . //TPV CODE 
        "" . $csv_seprator . //USER CREATED DATE
        "" . $csv_seprator . //BENEFICIARY PHONE 
        "" . $csv_seprator . //SHIPPING USE PRIMARY
        "" . $csv_seprator . //SHIPPING FIRSTNAME
        "" . $csv_seprator . //SHIPPING LASTNAME 
        "" . $csv_seprator . //SHIPPING ADDRESS 
        "" . $csv_seprator . //SHIPPING ADDRESS2 
        "" . $csv_seprator . //SHIPPING CITY  
        "" . $csv_seprator . //SHIPPING STATE 
        "" . $csv_seprator . //SHIPPING ZIPCODE 
        "" . $csv_seprator . //NSHIPPING INTERNATIONAL STATE
        "" . $csv_seprator . //SHIPPING COUNTRY 
        "" . $csv_seprator . //SHIPPING PHONE  
        "" . $csv_seprator . //SHIPPING MESSAGE  
        "" . $csv_seprator . //SHIPPING TRACKINGCARRIER
        "" . $csv_seprator . //SHIPPING TRACKINGCODE 
        "" . $csv_seprator . //PRODUCT AGENT ID
        "" . $csv_seprator . //PRODUCT ENROLLER ID 
        "" . $csv_seprator . //PRE-AUTH CODE 
        "" . $csv_seprator . //PRODUCT FEE  
        "" . $csv_seprator . //HEIGHT
        "" . $csv_seprator . //WEIGHT
        "" . $csv_seprator . //PERIOD
        "" . $csv_seprator . //LANGUAGE
        "" . $csv_seprator . //LEADSTATUSDESCRIPTION
        "" . $csv_seprator . //SMOKER 
        "" . $csv_seprator . //ENROLLMENT FEE 
        "" . $csv_line; //Line of Business

        if(getRevRelation($dep['relation']) == 'Child'){
          $child_code = $child_code + 1;
        }

        $content .= $tmp_row;
        if($addDependent) {
              $eligibility_content .= $tmp_row;
        }
        }
      }
      // Dependent rows ends
  }
}

if($content != '' && ($file_type == "schedule_change_file" || $file_type == "add_change_file")){
  $file_update = file_put_contents($file,$content);
  if($generate_change_file){
    $content = $eligibility_content;
  }
}
?>
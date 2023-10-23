<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
    $group_id = $id = md5($_SESSION['groups']['id']);
    //display company selection 
        $sqlGroupList = "SELECT lb.id FROM list_bills lb where md5(lb.customer_id) =:group_id AND lb.status='open'";
        $resGroupList = $pdo->selectOne($sqlGroupList,array(":group_id"=>$group_id));
        $disable_change = false;
        if(!empty($resGroupList)){
            $disable_change = true;
        }
    //display company selection 

    $group_sql = "SELECT md5(c.id) as id,c.id as _id,c.business_name,c.address,c.address_2,c.city,c.state,c.zip,c.business_phone,c.business_email,c.fname,c.lname,c.cell_phone,c.email,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.tpa_for_billing,cs.is_valid_address,cgs.group_size,cgs.group_in_year,cgs.ein,cgs.business_nature,cgs.sic_code,cgs.employer_company_common_owner,cgs.invoice_broken_locations,cs.signature_file,cs.is_2fa,cs.via_sms,cs.via_email,cs.send_otp_via,cs.is_ip_restriction,cs.allowed_ip,cs.agent_contract_file,c.status
        FROM customer c
        LEFT JOIN customer_settings cs on(cs.customer_id=c.id) 
        LEFT JOIN customer_group_settings cgs ON (cgs.customer_id = c.id)
        WHERE md5(c.id)=:id AND c.type IN ('Group')";
    $group_where = array(':id' => $group_id);
    $group_row = $pdo->selectOne($group_sql, $group_where);

    if(!empty($group_row)){ 
        $is_valid_address = $group_row['is_valid_address'];
        $group_name =  $group_row['business_name'];
        $business_address = $group_row['address'];
        $business_address_2 = $group_row['address_2'];
        $city = $group_row['city'];
        $state = $group_row['state'];
        $zipcode = $group_row['zip'];
        $business_phone = $group_row['business_phone'];
        $business_email = $group_row['business_email'];
        $no_of_employee = $group_row['group_size'];
        $years_in_business = $group_row['group_in_year'];
        $ein = $group_row['ein'];
        $nature_of_business = $group_row['business_nature'];
        $sic_code = $group_row['sic_code'];


        $sicCodeSql = 'SELECT * FROM `group_sic_code` WHERE business_id = :id';
        $where = array(':id' => makeSafe($nature_of_business));
        $sicCodeRes = $pdo->select($sicCodeSql,$where);

        $fname = $group_row['fname'];
        $lname = $group_row['lname'];
        $email = $group_row['email'];
        $phone = $group_row['cell_phone'];
        $username = $group_row['user_name'];
        

        $public_name = $group_row['public_name'];
        $public_phone = $group_row['public_phone'];
        $public_email = $group_row['public_email'];
        $found_state_id = 0;

        $group_company = $group_row['employer_company_common_owner'];
        $billing_broken = $group_row['invoice_broken_locations'];
    }

    $sel_Business = "SELECT * FROM group_nature_business WHERE id > 0 AND is_deleted='N'";
    $res_Business = $pdo->select($sel_Business);


    $selSql="SELECT id,name,ein,location FROM group_company WHERE md5(group_id)=:group_id and is_deleted='N'";
    $selCompanyRes=$pdo->select($selSql,array(":group_id"=>$group_id));

    $selSql="SELECT id,label,url FROM group_resource_link WHERE md5(group_id)=:group_id and is_deleted='N'";
    $selResourceRes=$pdo->select($selSql,array(":group_id"=>$group_id));

$tmpExJs = array(
  'thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js'
);

include_once 'tmpl/group_account_detail.inc.php';
?>
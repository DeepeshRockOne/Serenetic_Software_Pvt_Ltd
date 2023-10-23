<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
has_access(5);
    $agent_id = $id = $_REQUEST['id'];

    $select_user = "SELECT md5(c.id) as id,c.id as _id,c.email,c.cell_phone,c.rep_id,c.sponsor_id,c.public_name,c.public_email,c.public_phone,c.user_name,cs.display_in_member,cs.is_branding,cs.brand_icon,c.status,cs.account_type,cs.company,cs.company_name,cs.company_address,cs.company_address_2,cs.company_city,cs.company_state,cs.company_zip,cs.w9_pdf,c.address,c.address_2,c.fname,cs.tax_id,c.lname,cs.agent_coded_level,cs.agent_coded_id,c.city,c.state,c.zip,c.birth_date,c.type,cs.npn,cs.is_contract_approved,TIMESTAMPDIFF(HOUR,c.invite_at,now()) as difference,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn,s.id as sid,s.fname as s_fname,s.lname as s_lname,s.rep_id as s_rep_id,scs.agent_coded_level as s_level,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.term_reason,cs.signature_file,cs.is_special_text_display,cs.special_text_display,cs.is_2fa,cs.is_ip_restriction,cs.allowed_ip,cs.send_otp_via,cs.via_email,cs.via_sms,cs.allow_download_agreement,cs.agent_contract_file
    FROM `customer` c
    LEFT JOIN customer s on(s.id = c.sponsor_id)
    LEFT JOIN customer_settings cs on(cs.customer_id = c.id)
    LEFT JOIN customer_settings scs on(scs.customer_id = s.id)
    WHERE md5(c.id)= :id AND c.type IN ('Agent')";
    $where = array(':id' => makeSafe($id));
    $row = $pdo->selectOne($select_user, $where);

    $selDoc = "SELECT e_o_coverage,by_parent,by_parent,e_o_amount,e_o_expiration,e_o_document,process_commission FROM agent_document WHERE md5(agent_id)=:agent_id";
    $whrDoc = array(":agent_id" => $agent_id);
    $resDoc = $pdo->selectOne($selDoc, $whrDoc);

    $selDirect = "SELECT account_type,bank_name,routing_number,account_number FROM direct_deposit_account WHERE md5(customer_id)=:agent_id";
    $whrDirect = array(":agent_id" => $id);
    $resDirect = $pdo->selectOne($selDirect, $whrDoc);
    

    $type = $row['type'];
    $status = $row['status'];
    $contract_business_image=!empty($row["brand_icon"])?$row["brand_icon"]:"";

    $sql = "SELECT pf.name,pf.id,pls.sale_type,GROUP_CONCAT(distinct(pls.state_name)) as states 
        FROM agent_product_rule apr
        LEFT JOIN prd_main p on (p.id = apr.product_id)
        LEFT JOIN prd_fees pf on (pf.setting_type = 'Carrier' AND pf.status='Active' AND pf.id=p.carrier_id AND pf.is_deleted = 'N')
        LEFT JOIN prd_assign_fees pa ON (pa.product_id=p.id and pa.prd_fee_id=pf.id AND pa.is_deleted='N')
        LEFT JOIN prd_license_state pls ON(pls.product_id=p.id AND pls.is_deleted='N' )
        WHERE md5(apr.agent_id) = :agent_id AND p.is_deleted='N' AND (pf.use_appointments ='Y' OR pls.license_rule='Licensed and Appointed') AND apr.is_deleted = 'N' group by pf.id";
    $carrier_res = $pdo->select($sql,array(':agent_id' => $agent_id));      

include_once 'tmpl/agent_account_detail.inc.php';
?>
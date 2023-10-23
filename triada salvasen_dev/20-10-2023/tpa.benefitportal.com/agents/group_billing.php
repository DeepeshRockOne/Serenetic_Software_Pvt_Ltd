<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(9);
    $group_id = $id = $_REQUEST['id'];

    $group_sql = "SELECT md5(c.id) as id,c.id as _id,c.business_name,cgs.billing_type,cgs.auto_draft_date
        FROM customer c
        LEFT JOIN customer_group_settings cgs ON (cgs.customer_id = c.id)
        WHERE md5(c.id)=:id AND c.type IN ('Group')";
    $group_where = array(':id' => $group_id);
    $group_row = $pdo->selectOne($group_sql, $group_where);

    if(!empty($group_row)){ 
        $billing_type = $group_row['billing_type'];
        $group_name = $group_row['business_name'];
        
        //If company/Location Deleted then we will not display billing Profile
        $billingSql="SELECT md5(cbp.id) as id,cbp.created_at,cbp.fname,cbp.lname,cbp.payment_mode,cbp.last_cc_ach_no,
        if(cbp.payment_mode ='CC',cbp.card_type,'ACH') as card_type,cbp.is_default,gc.name as company_name
            FROM customer_billing_profile cbp
            JOIN group_company gc ON (gc.id = cbp.company_id and gc.is_deleted='N')
            where md5(cbp.customer_id)=:group_id and cbp.is_deleted='N' 
            order by cbp.created_at  DESC";
        $billingRes=$pdo->select($billingSql,array(":group_id"=>$group_id));
        //For Default Company
            $billingSql="SELECT md5(cbp.id) as id,cbp.created_at,cbp.fname,cbp.lname,cbp.payment_mode,cbp.last_cc_ach_no,
            if(cbp.payment_mode ='CC',cbp.card_type,'ACH') as card_type,cbp.is_default,'' as company_name
                FROM customer_billing_profile cbp
                where md5(cbp.customer_id)=:group_id and cbp.company_id=0 and cbp.is_deleted='N' 
                order by cbp.created_at  DESC";
            $billingRes2=$pdo->select($billingSql,array(":group_id"=>$group_id));
        //For Default Company
        $billingRes = array_merge_recursive($billingRes,$billingRes2);
    }
include_once 'tmpl/group_billing.inc.php';
?>
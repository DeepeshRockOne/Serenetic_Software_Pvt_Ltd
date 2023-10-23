<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
    $group_id = $id = $_REQUEST['id'];

    $group_sql = "SELECT md5(c.id) as id,c.id as _id,c.public_name,c.public_email,c.public_phone,c.user_name,cs.brand_icon,cs.is_branding,cs.display_in_member
        FROM customer c
        LEFT JOIN customer_settings cs on(cs.customer_id=c.id) 
        WHERE md5(c.id)=:id AND c.type IN ('Group')";
    $group_where = array(':id' => $group_id);
    $group_row = $pdo->selectOne($group_sql, $group_where);

    if(!empty($group_row)){ 
        $is_branding = $group_row['is_branding'];
        $public_name = $group_row['public_name'];
        $public_phone = $group_row['public_phone'];
        $public_email = $group_row['public_email'];
        $display_in_member =  $group_row['display_in_member'];
        $username = $group_row['user_name'];

        $currentImage='';
        $contract_business_image=!empty($group_row["brand_icon"])?$group_row["brand_icon"]:"";


        if (file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image) && $contract_business_image != "") {
          $currentImage=$GROUPS_BRAND_ICON_WEB . $contract_business_image;
        }

        $sqlWebsite= "SELECT pb.*,md5(pb.id) as id FROM page_builder pb WHERE pb.is_deleted = 'N' AND md5(pb.agent_id)=:group_id";
        $resWebsite=$pdo->select($sqlWebsite,array(":group_id"=>$group_id));
      
    }   

include_once 'tmpl/group_personal_brand_link.inc.php';
?>
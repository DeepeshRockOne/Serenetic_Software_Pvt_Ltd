<?php
include_once __DIR__ . '/includes/connect.php';

$res = array();

if(!empty($_REQUEST['agent_id'])) {
    $agent_id = $_REQUEST['agent_id'];
    $company_arr = array('Global Products' => array());
    
    $productSql = "SELECT p.id,p.product_code,p.name as prdName,p.type,p.parent_product_id,pc.title as company_name 
                    FROM prd_main p
                    JOIN agent_product_rule rp ON (rp.product_id=p.id AND rp.is_deleted='N' AND rp.status='Contracted' AND rp.agent_id=:agent_id)
                    LEFT JOIN prd_category pc ON (pc.id = p.category_id)
                    WHERE p.is_deleted='N' AND p.status='Active' AND p.type!='Fees' AND p.product_type ='Group Enrollment'
                    ORDER BY company_name,p.name ASC";
    $productRes = $pdo->select($productSql, array(":agent_id" => $agent_id));
    

    if ($productRes){
        foreach($productRes as $key => $row) {
            if($row['company_name'] != ""){
                $company_arr[$row['company_name']][] = $row;
            }else{
                $company_arr['Global Products'][] = $row;                
            }

            if (empty($company_arr['Global Products'])) {
                unset($company_arr['Global Products']);
            }

            if (empty($row['company_name'])) {
                unset($row['company_name']);
            }
        }
    }
    ksort($company_arr);

    $drop_down_html = '';
    $option_display = '';
    foreach ($company_arr as $key => $company){
        if($company){
            $drop_down_html .= '<optgroup label="'.$key.'">';
                foreach ($company as $pkey => $row) {
                $option_display = $row['prdName'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : '');
                $drop_down_html .= '<option value="'.$row["id"].'">'.$option_display.'</option>';
                }
            $drop_down_html .= '</optgroup>';
        }
    }    

    $res['status'] = 'success';
    $res['products_drop_down_html'] = $drop_down_html;
    $level_html = '<option value="" disabled selected hidden> </option>';    
}else{
    $res['status'] = 'fail';
    $res['message'] = "Agent not found";
}
header('Content-Type:application/json');
echo json_encode($res);
exit;
?>
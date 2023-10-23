<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$tz = new UserTimeZone('m/d/Y', $_SESSION['agents']['timezone']);
$is_prd_ajaxed = isset($_GET['is_prd_ajaxed']) ? $_GET['is_prd_ajaxed'] : '';
$per_page = '';
if (!empty($is_prd_ajaxed)) {
    $sch_params = array();
    $sch_params[":agent_id"] = $_GET['id'];

    if (count($sch_params) > 0) {
        $has_querystring = true;
    }

    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }

    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    $query_string .= "&#agp_products";
    $options = array(
        'results_per_page' => 10,
        'url' => 'agent_products.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    
    $level = $_GET['level'];
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);
    try {
        $selProduct = "SELECT COUNT(DISTINCT(pmap.id)) AS ext_plus,
            IFNULL(adv_comm_var.advFeeId,adv_comm.advFeeId) as advFeeId,
            IFNULL(adv_comm_var.advRuleCreatedAt,adv_comm.advRuleCreatedAt) as advRuleCreatedAt,
            IFNULL(adv_comm_var.advRuleId,adv_comm.advRuleId) as advRuleId,
            IFNULL(adv_comm_var.advance_month,adv_comm.advance_month) as advance_month,
            IFNULL(adv_comm_var.charged_to,adv_comm.charged_to) as charged_to,
            IFNULL(adv_comm_var.advRuleType,adv_comm.advRuleType) as advRuleType,

            MD5(pmpm.id) AS pmid,pmcr.updated_at AS pm_as_of_date,pmcr.id,
            pmcr.amount_calculated_on AS pm_amt_type,MIN(pt.amount) AS amount,apr.product_id,apr.agent_id,
            apr.id AS apr_id,apr.status,apr.created_at,apr.updated_at,pm.name as name,pm.product_code as product_code,pm.id AS pid,cr.commission_json,prm.plan_type,
            cr.commission_on,MIN(prm.plan_type) AS min_plans,
            pm.status AS product_status,
            pm.product_type,
            pm.pricing_model AS pricing_model
            FROM agent_product_rule apr
            LEFT JOIN prd_main pm ON(pm.id=apr.product_id AND pm.status!='Inactive') 
            LEFT JOIN prd_matrix prm ON(prm.product_id = pm.id AND prm.is_deleted='N')
            LEFT JOIN agent_commission_rule acr ON(acr.agent_id= apr.agent_id AND acr.product_id=apr.product_id AND acr.is_deleted='N')
            LEFT JOIN commission_rule cr ON(cr.id= acr.commission_rule_id AND cr.is_deleted='N')

             LEFT JOIN (SELECT pfd.id AS advFeeId,pfd.update_date as advRuleCreatedAt,pf.id AS advRuleId,pfd.advance_month,pf.charged_to,pf.rule_type as advRuleType,pf.agent_id,paf.product_id
                   FROM prd_assign_fees paf
                   JOIN prd_fees pf ON(paf.prd_fee_id=pf.id AND pf.is_deleted='N')
                   JOIN prd_main pfd ON(paf.fee_id=pfd.id AND pfd.is_deleted='N')
                   WHERE pf.rule_type='Variation' AND pf.status='Active' AND paf.is_deleted = 'N' AND pf.setting_type='ServiceFee'
            ) AS adv_comm_var ON(adv_comm_var.product_id = apr.product_id AND adv_comm_var.agent_id=apr.agent_id)

            LEFT JOIN (SELECT pfd.id AS advFeeId,pfd.update_date as advRuleCreatedAt,pf.id AS advRuleId,pfd.advance_month,pf.charged_to,pf.rule_type as advRuleType,pf.agent_id,paf.product_id
                   FROM prd_assign_fees paf
                   JOIN prd_fees pf ON(paf.prd_fee_id=pf.id AND pf.is_deleted='N')
                   JOIN prd_main pfd ON(paf.fee_id=pfd.id AND pfd.is_deleted='N')
                   WHERE paf.is_deleted = 'N' AND pf.setting_type='ServiceFee'
            ) AS adv_comm ON(adv_comm.product_id = apr.product_id)

            LEFT JOIN pmpm_commission pmpm ON(pmpm.agent_id=apr.agent_id AND pmpm.status='Active' AND pmpm.is_deleted='N')
            LEFT JOIN pmpm_commission_rule_assign_product pmap ON(pmap.commission_id = pmpm.id AND  pmap.product_id=apr.product_id AND pmap.is_deleted='N')
            LEFT JOIN pmpm_commission_rule pmcr ON(pmcr.id=pmap.rule_id AND pmcr.is_deleted='N' AND pmcr.commission_id!=0)
            LEFT JOIN pmpm_commission_rule_plan_type pt ON(pt.commission_id=pmpm.id AND pt.rule_id=pmcr.id AND pt.is_deleted='N' AND pt.commission_id!=0)
            WHERE MD5(apr.agent_id)=:agent_id AND apr.is_deleted='N' AND pm.is_deleted='N' AND pm.type!='Fees'
            GROUP BY apr.product_id ORDER BY FIELD(pm.status,'Active','Pending','Suspended','Extinct'),FIELD(apr.status,'Contracted','Pending Approval','Suspended','Extinct'),pm.name ASC";

        $paginate_product = new pagination($page, $selProduct, $options);
        if ($paginate_product->success == true) {
            $fetchProduct = $paginate_product->resultset->fetchAll();
            $totalProduct = count($fetchProduct);
        }
        include_once 'tmpl/agent_products.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    exit();
} else {
    include_once 'tmpl/agent_products.inc.php';
}
?>
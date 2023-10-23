<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = checkIsset($_GET['agent_id']);
$product_id = checkIsset($_GET['product_id']);
$commid = checkIsset($_GET['id']);
$ids = checkIsset($_GET['ids']);
$per_page = 5;
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$incr = "";

if($is_ajaxed){
    $sch_params[":agent_id"] = $agent_id;
    $sch_params[":prd_id"] = $product_id;

    if(!empty($ids)){
        $incr.=" AND pcr.id in ($ids)";
      }
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }

    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    $query_string.="&#agp_products";
    $options = array(
        'results_per_page' => 5,
        'url' => 'agents_pmpm.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);

    try {
        $selProduct ="SELECT pcr.id as rule_id, md5(pmc.id) as id, pcr.commission_id,pcr.display_id,pcr.created_at,pcr.amount_calculated_on,
        COUNT(DISTINCT pcraa.agent_id) AS total_agents,pcr.effective_date,pcr.termination_date,pcr.earned_on_new_business,pcr.earned_on_renewal,pcrpt.amount 
        FROM agent_product_rule apr
        LEFT JOIN pmpm_commission pmc ON(pmc.agent_id=apr.agent_id)
        JOIN pmpm_commission_rule pcr ON(pcr.commission_id=pmc.id)
        JOIN pmpm_commission_rule_plan_type pcrpt ON (pcr.id = pcrpt.rule_id AND pcrpt.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_product pcrap ON (pcr.id = pcrap.rule_id AND pcrap.product_id=apr.product_id AND pcrap.is_deleted = 'N')
        JOIN pmpm_commission_rule_assign_agent pcraa ON (pcr.id = pcraa.rule_id AND pcraa.is_deleted = 'N')
        WHERE  MD5(apr.agent_id) = :agent_id AND pmc.status='Active' AND apr.product_id=:prd_id AND apr.is_deleted='N'
        AND pcr.is_deleted='N' $incr GROUP BY pcr.id";
        $paginate = new pagination($page, $selProduct, $options);
        if ($paginate->success == true) {
            $fetchComm = $paginate->resultset->fetchAll();
            $totalRes = count($fetchComm);
        }
        include_once 'tmpl/agents_pmpm.inc.php';
        exit;
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}

$sel_product = $agent_detail= [];
if(!empty($agent_id)){
    $agproduct = $pdo->select("SELECT pmt.product_id as id,p.name  FROM agent_product_rule apr
    LEFT JOIN prd_main p ON(p.id=apr.product_id)
    LEFT JOIN pmpm_commission pm ON(pm.agent_id=apr.agent_id)
    LEFT JOIN pmpm_commission_rule_assign_product pmt ON(pmt.commission_id=pm.id AND pmt.product_id=apr.product_id)
    WHERE md5(apr.agent_id)=:id AND pmt.is_deleted='N' GROUP BY apr.product_id",array(":id"=>$agent_id));
}

if(!empty($agent_id)){
    $agent_detail = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where md5(id)=:id and type='Agent' and is_deleted='N'",array(":id"=>$agent_id));
}

if(!empty($commid)){
   $comm_ids = $pdo->selectOne("SELECT pc.id,GROUP_CONCAT(pcr.id) AS ids,pc.status 
    FROM pmpm_commission pc
    LEFT JOIN pmpm_commission_rule pcr ON(pcr.commission_id = pc.id AND pcr.is_deleted = 'N')
    WHERE md5(pc.id) = :id AND pc.is_deleted = 'N'",array(":id"=>$commid));
}
$template = 'agents_pmpm.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
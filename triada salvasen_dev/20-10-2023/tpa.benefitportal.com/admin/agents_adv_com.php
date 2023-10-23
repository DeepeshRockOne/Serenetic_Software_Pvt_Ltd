<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = checkIsset($_GET['agent_id']);
$product_id = checkIsset($_GET['product_id']);
$charged = checkIsset($_GET['charged']);
$type = checkIsset($_GET['type']);
$incr ='';
$sch_params = array();

$per_page = 5;
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
 
if(!empty($product_id)){
    $incr.= " AND paf.product_id=:prdId";
    $sch_params[":prdId"] = $product_id;
}
if(!empty($agent_id)){
    $incr.= " AND (pf.rule_type='Global' OR md5(pf.agent_id) = :agentId)";
    $sch_params[":agentId"] = $agent_id;
}

if($is_ajaxed){
  
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
        'url' => 'agents_adv_com.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);

    try {
        $selAdvComm = "SELECT paf.created_at as added_date,pmt.price_calculated_on,pmt.pricing_effective_date as effective_date,pmt.pricing_termination_date as termination_date,pm.advance_month,pf.charged_to,pmt.pricing_model,pmt.price,pm.id as advFeeId,pf.id as advRuleId,pf.rule_type
                    FROM prd_fees pf
                    JOIN prd_assign_fees paf ON(pf.id=paf.prd_fee_id AND paf.is_deleted='N')
                    JOIN prd_main pm ON(paf.fee_id=pm.id AND pm.is_deleted='N')
                    LEFT JOIN prd_matrix pmt ON(pm.id=pmt.product_id ANd pmt.is_deleted='N')
                    WHERE pf.is_deleted='N' AND pf.setting_type='ServiceFee' $incr GROUP BY pm.id
                    ORDER BY FIELD(pf.rule_type,'Variation','Global') ASC";
        $paginate = new pagination($page, $selAdvComm, $options);
        if ($paginate->success == true) {
            $fetchComm = $paginate->resultset->fetchAll();
            $totalRes = count($fetchComm);
        }
        include_once 'tmpl/agents_adv_com.inc.php';
        exit;
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}

$agent_detail= array();
if(!empty($product_id)){
    $agproduct = $pdo->select("SELECT p.id as id,p.name from prd_main p 
            WHERE p.id=:id  AND  p.is_deleted='N' GROUP BY p.id ",array(":id"=>$product_id));
}

if(!empty($agent_id)){
    $agent_detail = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer where md5(id)=:id and type='Agent' and is_deleted='N'",array(":id"=>$agent_id));
}

$template = 'agents_adv_com.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
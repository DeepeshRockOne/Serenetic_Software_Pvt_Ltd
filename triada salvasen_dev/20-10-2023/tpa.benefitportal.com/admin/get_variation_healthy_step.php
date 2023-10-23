<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);

$status_change = checkIsset($_GET['status_change']);
if(!empty($status_change) && $status_change=='Y'){
    $fee_id = checkIsset($_GET['product_id']);
    $status = checkIsset($_GET['status']);

    $query = "SELECT display_id,status FROM prd_fees WHERE id = :id and is_deleted='N'";
    $srow = $pdo->selectOne($query, array(":id"=>$fee_id));

    $update_params = array("status"=>$status);
    $pdo->update("prd_fees",$update_params,array("clause"=>"id=:id","params"=>array(":id"=>$fee_id)));

    $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated Variation Healthy Steps status '.$srow['display_id'],
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/variation_healthy_steps.php?fee_id='.md5($fee_id),
            'title'=>' ('.$srow['display_id'].')',
        ),
      ); 
      $description['description'] = "From ".$srow['status'].' To '.$status;
  
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'Variation Healthy Step','Admin Updated Variation Healthy Steps', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    echo json_encode(array("status"=>"success","message"=>"Staus updated Successfully!"));
    exit;

}
$sch_params = array();
$incr=""; 
$is_ajaxed = checkIsset($_REQUEST['is_ajaxed']) ;
if($is_ajaxed){
    $agent_id = checkIsset($_GET['rep_id']);
    $agent_id = cleanSearchKeyword($agent_id);  
    if(!empty($agent_id)){
        $sch_params[':agent_id'] = $agent_id;
        $incr.='AND c.rep_id=:agent_id ';
    }

    $healthy_steps_variation = checkIsset($_GET['healthy_steps_variation']);
    if(!empty($healthy_steps_variation)){
        $sch_params[':display_id'] = $healthy_steps_variation;
        $incr.='AND pf.id=:display_id ';
    }

    $has_querystring =false;
    if (count($sch_params) > 0) {
        $has_querystring = true;
    }

    $per_page=6;
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
        $has_querystring = true;
        $per_page = $_GET['pages'];
    }
    
    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    $options = array(
        'results_per_page' =>$per_page,
        'url' => 'get_variation_healthy_step.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params,
    );
    
    $page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);
        try {
        $selRecords ="SELECT pf.display_id,c.id as agent_id,pf.created_at,md5(pf.id) as pf_id,pf.id as pfid,count(distinct(p.id)) as total_fees,GROUP_CONCAT(distinct(p.id)) as product_ids,pf.status,c.fname,c.lname,c.rep_id,GROUP_CONCAT(distinct(p.is_fee_on_commissionable)) as fee_commissionable 
        FROM agent_product_rule apr
        LEFT JOIN prd_main p ON(p.id=apr.product_id and p.type='Fees' and p.product_type='Healthy Step' AND p.record_type='Variation')
        LEFT JOIN prd_assign_fees paf ON(paf.fee_id = p.id and paf.is_deleted='N')
        LEFT JOIN prd_fees pf ON(paf.prd_fee_id=pf.id and pf.setting_type='Healthy Step Variation')
        LEFT JOIN customer c ON(c.id=apr.agent_id and c.is_deleted='N' and c.type='Agent')
        LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
        WHERE pf.is_deleted='N' $incr AND apr.is_deleted='N' and cs.agent_coded_id!=1 and p.is_deleted='N' GROUP BY c.id";
    
        $paginate_records = new pagination($page, $selRecords, $options);
        if ($paginate_records->success == true) {
            $fetchRecords = $paginate_records->resultset->fetchAll();
            $totalRecords = count($fetchRecords);
        }
        } catch (paginationException $e) {
            echo $e;
            exit();
        }
    include_once 'tmpl/get_variation_healthy_step.inc.php';
    exit;
}else{
    $varition_healthy_step  = $pdo->select("SELECT pf.id,pf.display_id 
    from prd_fees pf
    JOIN prd_assign_fees paf ON(pf.id=paf.prd_fee_id AND paf.is_deleted='N')
    JOIN agent_product_rule apr on(apr.product_id=paf.fee_id and apr.is_deleted='N')
    where setting_type='Healthy Step Variation' and pf.is_deleted='N' group by pf.id
        ");
    include_once 'tmpl/get_variation_healthy_step.inc.php';
} ?>
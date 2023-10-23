<?php
include_once __DIR__ . '/includes/connect.php';
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$per_page = '';
if($is_ajaxed){

$sch_params[":agent_id"] = $_GET['agent_id'];

if (count($sch_params) > 0) {
    $has_querystring = true;
}
if (isset($_GET['merchant_pages']) && $_GET['merchant_pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['merchant_pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
    'results_per_page' => 10,
    'url' => 'agent_merchant_processor.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);
$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
    try {

        $type='Variation';
        $selProcessor = "SELECT p.*,md5(p.id) as id,p.id as pid,if(pmaa.id is not null ,pmaa.status,p.status) as status,
        if(pmaa.id is not null ,pmaa.global_accept_ach_status,p.status) as global_accept_ach_status
        FROM payment_master p 
        LEFT JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id=p.id and md5(pmaa.agent_id)=:agent_id and pmaa.is_deleted='N') 
        where ((md5(pmaa.agent_id)=:agent_id and pmaa.is_deleted='N') OR is_assigned_to_all_agent='Y') AND p.is_deleted='N'
        AND p.status='Active' and p.type='Variation'
        AND if(pmaa.id is not null,pmaa.status!='Deleted',1)
        GROUP BY p.id 
        ORDER BY FIELD(p.is_assigned_to_all_product,'Y','N'),order_by ASC";
        $resPro = $pdo->selectOne($selProcessor,$sch_params);
        if(empty($resPro) || $resPro['is_assigned_to_all_product'] == 'N'){
            $pincr = !empty($resPro['pid']) ? ' OR p.id ='.$resPro['pid'] : ''  ;
            $selProcessor ="SELECT p.*,md5(p.id) as id,p.id as pid,if(pmaa.id is not null ,pmaa.status,p.status) as status,
            if(pmaa.id is not null ,pmaa.global_accept_ach_status,p.status) as global_accept_ach_status
            FROM payment_master p 
            LEFT JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id=p.id and md5(pmaa.agent_id)=:agent_id and pmaa.is_deleted='N') 
            where ((md5(pmaa.agent_id)=:agent_id and pmaa.is_deleted='N') OR is_assigned_to_all_agent='Y') AND p.is_deleted='N'
            AND p.status='Active'
            AND if(pmaa.id is not null,pmaa.status!='Deleted',1)
            $pincr 
            GROUP BY p.id 
            ORDER BY FIELD(p.is_assigned_to_all_product,'Y','N'),order_by  ASC";
            $type='Global';
        }
        
        $paginate_processor = new pagination($page, $selProcessor, $options);
        if ($paginate_processor->success == true) {
            $fetchProcessor1 = $paginate_processor->resultset->fetchAll();
            $totalProcessor = count($fetchProcessor1);
            $fetchProcessor = $exists = $p_ids = $ids = array();
            $incr = '';
            $schPr = array();
            if($type=='Variation'){
                foreach($fetchProcessor1 as $pid){
                    if($pid['is_assigned_to_all_product'] =='Y' && empty($exists)){
                        array_push($fetchProcessor,$pid);
                        array_push($exists,$pid['id']);
                    }else{
                        if($pid['is_assigned_to_all_product'] =='Y')
                            array_push($p_ids,$pid['pid']);
    
                        if($pid['is_assigned_to_all_product'] =='N')
                            array_push($fetchProcessor,$pid);
                    }
                    array_push($ids,$pid['pid']);
                    $schPr[':type']  = $pid['type'];
                    $incr = " AND type=:type ";
                }
                
                if(!empty($p_ids)){
                    $incr .= " AND type='Variation' AND id IN(".implode(',',$p_ids).") AND is_assigned_to_all_product='N' ";
                }else{
                    $incr .= " AND type='Variation' AND id NOT IN(".implode(',',$ids).")  ";
                    if(!empty($exists)){
                        $incr .= " AND is_assigned_to_all_product='N' ";
                    }
                }
                
            }else{

                foreach($fetchProcessor1 as $pid){
                    array_push($ids,$pid['pid']);  
                }
                if(!empty($ids)){
                    $incr .= " AND type='Variation' AND id NOT IN(".implode(',',$ids).") ";
                }
                $fetchProcessor = $fetchProcessor1;
            }

            $processor_sql = "SELECT name,description,merchant_id,md5(id) as id,type FROM payment_master WHERE is_deleted='N' AND status='Active' $incr ";
            $merchant_processors = $pdo->select($processor_sql,$schPr);
            
        }
        include_once 'tmpl/agent_merchant_processor.inc.php';
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
}
?>
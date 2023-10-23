<?php 
include_once __DIR__ . '/includes/connect.php';
$type = checkIsset($_POST['type']);
$is_ajaxed = checkIsset($_POST['is_ajaxed']);
$search = checkIsset($_POST['search']);
$html = "<div class='col-sm-6 col-md-4'>No record Found!</div>";
$incr = '';
if($is_ajaxed){

    $sch_param[':portal_type'] = $type;
    if(!empty($search)){
        $sch_param[":search"] = $search;
        $incr .= " AND module_name = :search ";
    }
    $selRes = "SELECT * from portal_resources where is_deleted='N' AND portal_type=:portal_type $incr 
    ORDER BY  
    CASE 
        WHEN portal_type = 'admin' 
            THEN FIELD(module_name,'dashboard','User Groups','Communications','Products','Commissions','Payment','Reporting','Eligibility Files','Fullfillment','smartE')
        WHEN portal_type = 'agent' 
            THEN FIELD(module_name,'dashboard','Enroll','Website','Book of Business','My Production','Resources')
        WHEN portal_type = 'member' 
            THEN FIELD(module_name,'dashboard','add_product','my_account')
        WHEN portal_type = 'group' 
            THEN FIELD(module_name,'dashboard','Users','Billing','Resources')
    END";
    $allPortalRsources = $pdo->select($selRes,$sch_param);
                        

    if(!empty($allPortalRsources)){
    ob_start();
        $class = '';
        $exitstArr = array();
        $currentModule = '';
        $i=1;
        $se_class = 0;
        foreach($allPortalRsources as $portal){            
            if($i == 2 || ($i - $se_class == 3)  ){
                $class = 'danger';
                $se_class = $i;
            }else if($i%3 == 0){
                $class = 'primary';
            }else{
                $class = 'info';
            }
                if(empty($exitstArr) || !in_array($portal['module_name'],$exitstArr)){ 
                    $i++;  
            ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="resources_box">
                            <div class="panel panel-default <?= $class ?>">
                            <div class="panel-heading"><?=ucfirst($portal['module_name'])?></div>
                            <div class="panel-body">
                            <ul>
                <?php 
                    $currentModule = $portal['module_name'];
                    foreach($allPortalRsources as $val){ 
                        if($val['module_name'] == $currentModule){ ?> 
                            <li><a href="javascript:void(0);" data-href="view_resources.php?id=<?=md5($val['id'])?>" class="view_resources"><?=$val['resource_name']?></a></li>        
                <?php array_push($exitstArr,$portal['module_name']); } } ?>
                            </ul>
                            </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>        
        <?php  } 
    $html = ob_get_contents();
    ob_get_clean();
    }
}
$response['html'] = $html;
$response['type'] = $type;
$response['status'] = 'success';
header("Content-Type: application/json");
echo json_encode($response);
dbConnectionClose();
exit;
?>
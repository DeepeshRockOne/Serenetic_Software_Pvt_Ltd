<?php
include_once 'layout/start.inc.php';
 
$group_id=!empty($_POST['group_id']) ? $_POST['group_id'] : 0;
$coverage_id=!empty($_POST['id']) ? $_POST['id'] : 0;
$active_member = 0;
$active_policy = 0;

$sqlCoverage = "SELECT id,coverage_period_name,coverage_period_start,coverage_period_end FROM group_coverage_period WHERE is_deleted='N' AND status='Active' AND id=:coverage_id";
$resCoverage = $pdo->selectOne($sqlCoverage,array(":coverage_id"=>$coverage_id));

$selSql="SELECT md5(gco.id) as id,gco.class_id,gc.class_name,gco.products,gco.open_enrollment_start,gco.open_enrollment_end,gc.pay_period,gco.status
  FROM group_coverage_period_offering gco 
  JOIN group_classes gc ON (gc.id = gco.class_id)
  WHERE gco.group_coverage_period_id=:coverage_id and gco.is_deleted='N' ";
$selRes=$pdo->select($selSql,array(":coverage_id"=>$coverage_id));

$sqlMember="SELECT count(c.id) as active_member 
  FROM customer c 
  JOIN customer_settings cs ON (c.id=cs.customer_id)
  WHERE c.status='Active' AND c.sponsor_id=:group_id AND cs.group_coverage_period_id =:coverage_id AND c.is_deleted='N'";
$resMember=$pdo->selectOne($sqlMember,array(":group_id"=>$group_id,":coverage_id"=>$coverage_id));

if(!empty($resMember) && !empty($resMember['active_member'])){
  $active_member = $resMember['active_member'];
}

$sqlPolicy="SELECT count(c.id) as active_policy 
  FROM customer c 
  JOIN customer_settings cs ON (c.id=cs.customer_id)
  JOIN website_subscriptions ws ON (c.id = ws.customer_id)
  JOIN prd_main p ON (p.id = ws.product_id AND p.type='Normal')
  WHERE c.status='Active' AND ws.status='Active' AND c.sponsor_id=:group_id AND cs.group_coverage_period_id =:coverage_id AND c.is_deleted='N'";
$resPolicy=$pdo->selectOne($sqlPolicy,array(":group_id"=>$group_id,":coverage_id"=>$coverage_id));

if(!empty($resPolicy) && !empty($resPolicy['active_policy'])){
  $active_policy = $resPolicy['active_policy'];
}
ob_start(); ?>


  <div class="panel panel-default panel-block">
      <div class="panel-body">
        <div class="pull-left">
              <h4 class="m-b-15 m-t-0"><?= $resCoverage['coverage_period_name'] ?></h4>
              <p class="m-b-15 text-gray"><?= date('F d, Y',strtotime($resCoverage['coverage_period_start'])) .' - '. date('F d, Y',strtotime($resCoverage['coverage_period_end'])) ?></p>
          </div>
        <div class="pull-right">
          <a href="<?= $GROUP_HOST ?>/add_coverage_periods.php" class="pull-right red-link fw600">+ Plan Period</a>
        </div>
        <div class="clearfix"></div>
          <div class="table-responsive">
              <table class="table table-striped  fs12 text-center color-table info-table">
                  <thead>
                      <tr>
                          <th class="text-left">Class</th>
                          <th>Products</th>
                          <th># Enrolled</th>
                          <th># Waived</th>
                          <th width="100px">Total</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($selRes)) { ?>
                      <?php foreach ($selRes as $key => $value) { ?>
                        <?php 
                          $total_enrolled = 0;
                          $enrolledSql = "SELECT c.id FROM customer c
                              JOIN customer_settings cs ON (c.id = cs.customer_id)
                              JOIN website_subscriptions ws ON (c.id = ws.customer_id)
                              WHERE cs.group_coverage_period_id=:coverage_id AND cs.class_id = :class_id AND c.sponsor_id=:group_id AND c.is_deleted='N' group by c.id";
                          $enrolledRes = $pdo->select($enrolledSql,array(":coverage_id"=>$coverage_id,":group_id"=>$group_id,":class_id"=>$value['class_id']));

                          if(!empty($enrolledRes)){
                            $total_enrolled = count($enrolledRes);
                          }

                          $total_waived = 0;
                          $waivedSql = "SELECT c.id FROM customer c
                              JOIN customer_settings cs ON (c.id = cs.customer_id)
                              JOIN customer_waive_coverage cwc ON (c.id = cwc.customer_id)
                              LEFT JOIN website_subscriptions ws ON (c.id = ws.customer_id)
                              WHERE cs.group_coverage_period_id=:coverage_id AND cs.class_id = :class_id AND c.sponsor_id=:group_id AND c.is_deleted='N' AND ws.id IS NULL group by c.id";
                          $waivedRes = $pdo->select($waivedSql,array(":coverage_id"=>$coverage_id,":group_id"=>$group_id,":class_id"=>$value['class_id']));

                          if(!empty($waivedRes)){
                            $total_waived = count($waivedRes);
                          }

                          $total_enrollees = 0;
                          $enrolleeSql = "SELECT count(l.id) as total_enrollees FROM leads l 
                          WHERE l.is_deleted = 'N' AND l.sponsor_id=:group_id AND l.group_classes_id = :class_id and l.group_coverage_id =:coverage_id";
                          $enrolleeRes = $pdo->selectOne($enrolleeSql,array(":coverage_id"=>$coverage_id,":group_id"=>$group_id,":class_id"=>$value['class_id']));
                          if(!empty($enrolleeRes) && !empty($enrolleeRes['total_enrollees'])){
                            $total_enrollees = $enrolleeRes['total_enrollees'];
                          }
                        ?>
                        <tr>
                          <td class="text-left"><?= $value['class_name'] ?></td>
                          <td class="icons"> 
                              <a href="javascript:void(0)" data-href="coverage_popup.php?id=<?= $value['id'] ?>&group_id=<?= $group_id ?>" class="coverage_popup"><i class="fa fa-eye"></i></a>
                          </td> 
                          <td><a href="javascript:void(0)" class="text-red"><?= $total_enrolled ?></a></td>
                          <td><a href="javascript:void(0)" class="text-red"><?= $total_waived ?></a></td>
                          <td><a href="javascript:void(0)" class="text-red"><?= $total_enrolled + $total_waived ?>/<?= $total_enrollees ?></a></td>
                      </tr>
                      <?php } ?>
                    <?php }else{ ?>
                      <tr>
                        <td colspan="5" class="text-center">No Record(s) Found.</td>
                      </tr>
                    <?php } ?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>


<?php
  
$result = array();	
$result['html'] = ob_get_clean();
$result['status'] = "success"; 
$result['active_member'] = $active_member; 
$result['active_policy'] = $active_policy; 
$result['group_id'] = $group_id; 
$result['coverage_id'] = $coverage_id; 
  
header('Content-type: application/json');
echo json_encode($result); 
dbConnectionClose();
exit;
?>
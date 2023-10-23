<?php
include_once 'layout/start.inc.php';
 
$coverage_id=!empty($_POST['coverage']) ? $_POST['coverage'] : 0;
$is_clone=!empty($_POST['is_clone']) ? $_POST['is_clone'] : 'N';

$selSql="SELECT md5(gco.id) as id,gc.class_name,gco.products,gco.open_enrollment_start,gco.open_enrollment_end,gc.pay_period,gco.status FROM group_coverage_period_offering gco 
  JOIN group_classes gc ON (gc.id = gco.class_id)
  WHERE md5(gco.group_coverage_period_id)=:coverage_id and gco.is_deleted='N'";
$selRes=$pdo->select($selSql,array(":coverage_id"=>$coverage_id));


ob_start(); ?>


  <table class="<?=$table_class?>">
      <thead>
          <tr>
              <th class="text-left">Class</th>
              <th class="text-center"># Products</th>
              <th >Open Enrollment Start/End</th>
              <th>Pay Period</th>
              <th>Status</th>
              <?php if($is_clone!='Y'){ ?><th width="90px">Actions</th> <?php } ?>
          </tr>
      </thead>
      <tbody>
        <?php if(!empty($selRes)) { 
            foreach ($selRes as $key => $value) { ?>
              <?php $products_arr = explode(",", $value['products']) ?>
              <tr>
                    <td><?= $value['class_name'] ?></td>
                    <td class="text-center"><a href="javascript:void(0)" data-href="coverage_periods_product.php?offering=<?= $value['id'] ?>" class="text-action fw500 coverage_periods_product" data-toggle="tooltip" title="View"><?= count($products_arr) ?></a></td>
                    <td><?= date($DATE_FORMAT,strtotime($value['open_enrollment_start'])).' - '. date($DATE_FORMAT,strtotime($value['open_enrollment_end'])) ?></td>
                    <td><?= $value['pay_period'] ?></td>
                    <td class="theme-form">
                        <div class="theme-form w-200 pr">
                          <?php if($is_clone!='Y'){ ?>  
                          <select class="form-control has-value offering_status" id="offering_status_<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" data-status="<?= $value['status'] ?>" onchange="changeStatus('<?= $value['id']; ?>', this.value, '<?=$value['status'];?>')">
                                  <option></option>
                                  <option value="Active" <?= $value['status'] == "Active" ? "selected" : '' ?>>Active</option>
                                  <option value="Inactive" <?= $value['status'] == "Inactive" ? "selected" : '' ?>>Inactive</option>
                          </select>
                          <label>Select</label>
                          <?php }else{ ?>
                            <?= $value['status'] ?>
                          <?php } ?>
                        </div>
                    </td>
                    <?php if($is_clone!='Y'){ ?>
                        <td class="icons">
                            <a href="javascript:void(0)" data-href="offering_coverage_periods.php?offering=<?= $value['id'] ?>&coverage=<?= $coverage_id ?>&clone=Y" data-toggle="tooltip" title="Clone" class="clone_offering"><i class="fa fa-clone"></i></a>
                            <a href="javascript:void(0)" data-href="offering_coverage_periods.php?offering=<?= $value['id'] ?>&coverage=<?= $coverage_id ?>" data-toggle="tooltip" title="Edit" class="edit_offering"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0)" data-id="<?= $value['id'] ?>" data-toggle="tooltip" title="Delete" class="delete_offering"><i class="fa fa-trash"></i></a>
                        </td>
                    <?php } ?>
              </tr>
            <?php } ?>
        <?php }else{ ?>
          <tr><td colspan="6" class="text-center">No Record(s) Found</td></tr>
        <?php } ?>
        <?php if($is_clone!='Y'){ ?>
          <tr>
              <td colspan="6">
                  <a href="javascript:void(0);" data-href="offering_coverage_periods.php" class="btn btn-info" id="offering_coverage_periods">+ Offering</a>
              </td>
          </tr>
        <?php } ?>
      </tbody>
  </table>


<?php
  
$result = array();	
$result['html'] = ob_get_clean();
$result['status'] = "success"; 
  
header('Content-type: application/json');
echo json_encode($result); 
dbConnectionClose();
exit;
?>
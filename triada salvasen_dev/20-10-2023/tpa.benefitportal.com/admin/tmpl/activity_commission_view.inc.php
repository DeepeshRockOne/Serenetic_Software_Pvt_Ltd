<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">Commission </strong> <span class="fw300">
        <?= $type ?></span>
      </p>
    </div>
  </div>
  <div class="panel-body">
    <div id="enrollment_orders_his_con" class="table-responsive text-center">
      <table class="<?= $table_class ?>">
        <thead>
          <?php if ($commission_jsonType == 'Plan') { ?>
            <th width="<?= floor(100 / count($agentCodedRes) + 1) ?>" class="text-center">Plan Type</th>
          <?php } ?>
          <?php foreach ($agentCodedRes as $key => $level) { ?>
            <th width="<?= floor(100 / count($agentCodedRes)) ?>" class="text-center"><?= $level['level_heading'] ?></th>
          <?php } ?>
        </thead>
        <tbody>
          <?php if ($commission_jsonType == 'Product') { ?>
            <tr>
              <?php foreach ($commission_json as $commissionKey => $commissionValue) {
                if($commissionValue['amount_type'] == "Percentage") { 
                  $Perc_icon = '%';
                  $Amnt_icon = '';
                }else{
                  $Perc_icon = '';
                  $Amnt_icon = '$';
                } ?>
                <td class="text-center"><?= $Amnt_icon . $commissionValue['amount'] . $Perc_icon ?></td> 
              <?php } ?>
            </tr>
          <?php }else{ ?>
            <?php foreach ($commission_json as $planKey => $planValue) { ?>
              <tr>
                <td><?= $prdPlanTypeArray[$planKey]['title'] ?></td>
                <?php foreach ($planValue as $commissionKey => $commissionValue) { 
                  if($commissionValue['amount_type'] == "Percentage") { 
                    $Perc_icon = '%';
                    $Amnt_icon = '';
                  }else{
                    $Perc_icon = '';
                    $Amnt_icon = '$';
                  } ?>
                  <td class="text-center"><?= $Amnt_icon . $commissionValue['amount'] . $Perc_icon ?></td> 
                <?php } ?>
              </tr>
            <?php } ?>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="text-center m-t-20">
    		<a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
    </div>
  </div>
</div>
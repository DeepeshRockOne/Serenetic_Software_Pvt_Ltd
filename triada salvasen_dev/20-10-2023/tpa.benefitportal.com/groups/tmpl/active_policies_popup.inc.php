<div class="panel panel-default ">
 <div class="panel-heading">
   <div class="panel-title">
     <h4 class="mn">Total Active Policies - <span class="fw300">(<?= $total_policy ?>)</span></h4>
   </div>
 </div>
 <div class="panel-body">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-center"># Enrolled</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($resPolicy)) { ?>
            <?php foreach ($resPolicy as $key => $value) { ?>
              <tr>
                <td><?= $value['name'] ?></td>
                <td class="text-center"><?= $value['enrolled'] ?></td>
              </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr><td colspan="2" class="text-center">No Record(s) Found.</td></tr>
          <?php } ?>
        </tbody>
      </table>
      <hr class="mtn">
      <div class="text-center">
         <a href="active_policies_popup.php?group_id=<?=$group_id?>&id=<?=$coverage_id?>&action_type=export_excel" class="btn red-link"><i class="fa fa-download"></i> Export</a>
      </div>
 </div>
</div>
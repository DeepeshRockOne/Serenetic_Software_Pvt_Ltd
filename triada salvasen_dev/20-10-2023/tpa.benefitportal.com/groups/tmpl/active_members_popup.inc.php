<div class="panel panel-default ">
 <div class="panel-heading">
   <div class="panel-title">
     <h4 class="mn">Total Active Members - <span class="fw300">(<?= $total_member ?>)</span></h4>
   </div>
 </div>
 <div class="panel-body">
      <table class="<?=$table_class?> table-action">
        <thead>
          <tr>
            <th>ID</th>
            <th>Added Date</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($resMember)) { ?>
            <?php foreach ($resMember as $key => $value) { ?>
              <tr>
                <td><a href="javascript:void(0)" class="text-red"><?= $value['rep_id'] ?></a></td>
                <td><?= date('m/d/Y',strtotime($value['joined_date'])) ?></td>
                <td><?= $value['fname'] .' '. $value['lname'] ?></td>
              </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr><td colspan="3" class="text-center">No Record(s) Found.</td></tr>
          <?php } ?>
        </tbody>
      </table>
      <hr class="mtn">
      <div class="text-center">
         <a href="active_members_popup.php?group_id=<?=$group_id?>&id=<?=$coverage_id?>&action_type=export_excel" class="btn red-link"><i class="fa fa-download"></i> Export</a>
      </div>
 </div>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">Assigned Admins -   </strong> <span class="fw300"><?=$name?></span></p>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr>
            <th>Admin ID</th>
            <th>Admin Name</th>
            <th width="100px" >Status</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?=$rows['display_id']?></td>
            <td><?=$rows['fname'].' '.$rows['lname']?></td>
            <td ><?=$rows['status']?></td>
          </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="3" align="center">No record(s) found</td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="text-center m-t-20">
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
    </div>
  </div>
</div>

<style type="text/css">
.sub-panel h1{ font-size:16px; line-height:normal; font-weight:400;}
</style>

<div class="panel panel-default panel-popup">
  <div class="panel-heading">
    <div class="panel-title"> <i class="fa fa-time"></i> <span><?php echo $res_group['title']!="" ? $res_group['title'] : "";?></span> </div>
  </div>
  <?php include("notify.inc.php"); ?>
  <div class="panel-body">
    <div class="sub-panel">
      <h1><span><?= ($mode == 'ADD' ? 'Add Email' : 'Update Email' ) ?></span></h1>
      <form id="frm_category" class="form-horizontal" name="frm_category" action="test_email_group_addresses.php?id=<?php echo $group_id;?>" method="POST">
        <div class="form-group">
          <label for="fields" class="col-sm-1 control-label">Email</label>
          <div class="col-sm-4">
            <input type="text" id="email" class="form-control" name="email" value='<?php echo $email ?>' />
            <?php if (isset($errors['email'])): ?>
            <ul class="parsley-error-list"><li class="required"><?php echo $errors['email'] ?></li></ul>
            <?php endif; ?>
            
            <?php if($edit_id!=""){?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_id?>"/>
            <?php }?>
          </div>
          <div class="col-sm-3">
            <button type="submit" name="cat_save" id="cat_save" class="btn btn-info">Save</button>
          </div>
        </div>
      </form>
    </div>
    <div class="table-responsive">
      <table cellpadding="0" cellspacing="0" class="<?php echo $table_class; ?>">
        <thead>
          <tr>
            <th>Email</th>
            <th>Date/Time</th>
            <th>Manage</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (!empty($fetch_rows)) { ?>
          <?php  foreach ($fetch_rows as $rows) {  ?>
          <tr>
            <td class=""><?php echo $rows['email'] ?></td>
            <td class=""><?php echo retrieveDate($rows['created_at']); ?></td>
            <td class="icons"><a href="test_email_group_addresses.php?id=<?php echo $group_id; ?>&edit_id=<?php echo $rows['id']; ?>" title="" data-toggle="tooltip" data-original-title="Edit Test Group" ><i class="fa fa-edit"></i></a> <a href="<?php echo url_for('test_email_group_addresses.php?id='.$group_id.'&del_id=' . $rows['id']); ?>" title="" data-toggle="tooltip" data-original-title="Delete Email" onClick="return confirmAction();"><i class="fa fa-trash"></i></a></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td colspan="2" align="center">No record(s) found</td>
          </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>

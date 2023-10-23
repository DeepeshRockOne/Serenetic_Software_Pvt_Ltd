<?php include("notify.inc.php"); ?>
<div class="panel panel-default panel-block popup" >
  <div class="panel-heading">
    <div>
      <i class="icon-list-alt"></i>
      <h1><span>Are you sure you want to delete trigger template ?</span></h1>
    </div>
  </div>
  <div class="panel-body">
    <form id="frm_category" class="form-horizontal " name="frm_category" method="POST">
      <h4><span><?php echo !empty($srow) ? 'Note : Template Already used' : ' ' ?> </span></h4>
      <div class="col-xs-4">
        <div class="form-group" id="select_new" style="display:<?php echo !empty($srow) ? 'block' : 'none' ?>">
          <label>Select New Template</label>
            <select name="template" class="form-control" id="template">
              <option value="">Select</option>
              <?php foreach ($rows as $key => $data) { ?>
                <option value="<?php echo $data['id'] ?>"><?php echo $data['title']; ?></option>
              <?php } ?>
            </select>   
            <ul class="parsley-error-list">             
              <?php if (isset($errors['template'])): ?>
                <li class="required"><?php echo $errors['template'] ?></li>
              <?php endif; ?>
            </ul>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-xs-12">
        <div class="form-group">
          <button type="submit" name="save" id="save" class="btn btn-icon btn-info">Yes</button>
          <button type="button" name="cancel" id="cancel" onclick="window.parent.$.colorbox.close()" class="btn btn-icon btn-default">No</button>
        </div>
      </div>
    </form>
  </div>
</div>
 
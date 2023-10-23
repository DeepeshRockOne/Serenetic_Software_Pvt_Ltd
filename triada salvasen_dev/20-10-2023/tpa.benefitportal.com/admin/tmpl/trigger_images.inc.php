<?php include("notify.inc.php"); ?>
<div class="triggers_images">
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
       <?php
      $trigger = "active";
      include("br_broadcaster_tabs.inc.php");
      ?> 
    </ul>
  </div>
  <div class="panel panel-default panel-block brdr-none">
    <div class="panel-heading">
      <div>
        <i class="fa fa-envelope"></i>
        <h1><span> &nbsp;Manage Trigger Images</span></h1>
      </div>
    </div>
  </div>
  <div class="tabbing">
    <ul class="nav nav-tabs panel panel-default panel-block m-b-0">
      <?php $trigger_img = 'active'; ?>
      <?php include_once('triggers_tabs.inc.php'); ?>  
    </ul>
  </div>  
  <div class="panel panel-default panel-block panel-title-block">
    <div class="list-group">
      <div class="list-group-item">         
        <div class="panel panel-default panel-block">
          <div class="panel-heading">
            <div><h1><span><?= ($mode == 'ADD' ? 'Add New Image' : 'Update Image' ) ?></span></h1></div>
            <div class="clearfix">&nbsp;</div>
            <form id="frm_category" class="" name="frm_category" action="" method="POST" enctype="multipart/form-data"> 
              <div class=" row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= $title ?>"/>
                    <?php if (isset($errors['title'])): ?>
                      <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Company</label>
                    <select id="company_id" name="company_id" class="form-control <?php echo isset($errors['company_id']) ? 'parsley-error' : '' ?>">
                        <option value="">-- Select company --</option>
                        <?php foreach ($company_res AS $key => $row) { ?>
                            <option value="<?= $row['id'] ?>" <?= $company_id == $row['id'] ? 'selected' : '' ?>><?= $row['company_name'] ?></option>
                        <?php } ?>
                    </select>
                    <?php if (isset($errors['company_id'])): ?>
                      <ul class="parsley-error-list"><li class="required"><?php echo $errors['company_id'] ?></li></ul>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                  </div>
                </div>  
                <div class="clearfix">&nbsp;</div>
                <div class="col-md-6">

                  <?php 
                    $TRIGGER_IMAGE_DIR = $SITE_SETTINGS[$company_id]['TRIGGER_IMAGE']['upload'];
                    $TRIGGER_IMAGE_WEB = $SITE_SETTINGS[$company_id]['TRIGGER_IMAGE']['download'];
                  if (remote_file_exists($TRIGGER_IMAGE_DIR . $old_img,$company_id) && $old_img != "") { ?> 
                    <div class="form-group">
                      <a href="<?php echo $TRIGGER_IMAGE_WEB ."/". $old_img; ?>" target="_blank">
                        <img src="<?php echo $TRIGGER_IMAGE_WEB ."/". $old_img; ?>" height="50" class="img-thumbnail"> 
                      </a>  
                    </div>
                  <?php } ?>
                  <div data-provides="fileinput" class="fileinput fileinput-new input-group">
                  	<div class="form-control" data-trigger="fileinput">
                    	<i class="glyphicon glyphicon-file fileinput-exists"></i>
											<span class="fileinput-filename"></span>
                    </div>
                    
                    <span class="btn btn-info input-group-addon text-white btn-file">
                      <span class="fileinput-new">Select Image</span>
                      <span class="fileinput-exists">Change</span>
                      <input type="hidden" value="" name="p_img_hid" id="p_img_hid" />
                      <input type="file" name="t_img" id="t_img" tabindex="6" />
                    </span> 
                    <span class="fileinput-filename"></span> 
                    <a style="float: none" data-dismiss="fileinput" class="close fileinput-exists" href="javascript:void(0)">×</a> 
                  </div>
                  <?php if (isset($errors['t_img'])): ?>
                    <ul class="parsley-error-list"><li class="required"><?php echo $errors['t_img'] ?></li></ul>
                  <?php endif; ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">  
                  <button type="submit" name="save" id="save" class="btn btn-info">Save</button>
                  <?php if ($mode == 'ADD') { ?> 
                    <button type="button" name="cancle" id="cancle" class="btn btn-default" onclick="window.location = 'trigger_images.php'">Clear</button>
                  <?php } else { ?>
                    <button type="button" name="cancle" id="cancle" class="btn btn-default" onclick="window.location = 'trigger_images.php'">Cancel</button>
                  <?php } ?>
                </div>
              </div>
            </form>

          </div> 
        </div>
      </div>       
      <div class="list-group-item">
        <div class="table-responsive">
          <table class="detail_table <?=$table_class?>">
            <thead>
              <tr>
                <th class="index">ID</th>
                <th>Title</th>              
                <th width="300px">Image</th>              
                <th>Date/Time</th>             
                <th class="icons" width="100px">Manage</th>             
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($rows)) { ?>
                <?php foreach ($rows as $key => $row) { 
                    $TRIGGER_IMAGE_DIR = $SITE_SETTINGS[$row['company_id']]['TRIGGER_IMAGE']['upload'];
                    $TRIGGER_IMAGE_WEB = $SITE_SETTINGS[$row['company_id']]['TRIGGER_IMAGE']['download'];
                    ?>        
                  <tr>
                    <td class="index"><?php echo $row['id']; ?></td>
                    <td class="index"><?php echo $row['title']; ?></td>
                    <td class=""><img src="<?php echo remote_file_exists($TRIGGER_IMAGE_DIR . $row['src'],$row['company_id'])?$TRIGGER_IMAGE_WEB ."/". $row['src']:'-'; ?>" class="img-responsive" /></td>
                    <td class=""><?php echo retrieveDate($row['created_at']); ?></td>             
                    <td class="icons">
                      <a href="trigger_images.php?id=<?php echo $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="Edit Image" ><i class="fa fa-edit"></i></a> 
                      <a href="delete_template_image.php?image_id=<?php echo $row['id']; ?>" title="" id="<?= $row['id']; ?>" class="<?= $row['total_used'] > 0 ? 'check_template' : 'ajax_check_template' ?>" data-toggle="tooltip" data-original-title="Delete Image" ><i class="fa fa-trash"></i></a>
                    </td>             
                  </tr>              
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="4">No record(s) found</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script language="javascript" type="text/javascript">
  $(document).ready(function () {
    $(".check_template").colorbox({href: $(this).href, iframe: true, width: '700px', height: '450px'});
    $('.ajax_check_template').click(function (e) {
      e.preventDefault();
      var obj = $(this);
      var id = obj.attr('id');

      if (confirm('Are you sure you want to delete?')) {
        $.ajax({
          url: $(this).attr('href'),
          type: 'post',
          data: {'image_id': id, 'is_ajax': 'Y'},
          dataType: 'json',
          success: function (res) {
            if (res.status == 'fail') {
              alert('Invalid operation');
            } else if (res.status == 'success') {
              window.location.reload();
            }
          }
        });
      }
    });
  });

</script>

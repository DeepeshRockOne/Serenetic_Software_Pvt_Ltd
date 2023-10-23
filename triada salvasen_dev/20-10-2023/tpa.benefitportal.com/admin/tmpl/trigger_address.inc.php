<?php include("notify.inc.php"); ?>
<div class="trigger-address">
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
       <?php
      $trigger = "active";
      include("br_broadcaster_tabs.inc.php");
      ?> 
    </ul>
  </div>
  <div class="panel panel-default panel-block ">
    <div class="panel-heading">
      <div>
        <i class="fa fa-envelope"></i>
        <h1><span>Manage Trigger Address</span></h1>
      </div>
    </div>
  </div>
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab">
      <?php $trigger_address = 'active'; ?>
      <?php include_once('triggers_tabs.inc.php'); ?>  
    </ul>
  </div>  
  <div class="panel panel-default panel-block ">
     
               
         
          <div class="panel-heading">
            <h4 class="section-title"><?= ($mode == 'ADD' ? 'Add New Address' : 'Update Address' ) ?></h4>
              <form id="frm_category" class="form_wrap"  name="frm_category" action="" method="POST"> 
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= $title ?>"/>
                    <?php if (isset($errors['title'])): ?>
                      <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                  </div>
                </div>
                <div class="clearfix"></div>  
                <div class="col-md-4">
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
                <div class="clearfix"></div>
                <div class="col-md-12 ">
                  <div class="form-group height_auto">
                    <textarea id="address" class="form-control ckeditor" name="address"><?php echo $address; ?></textarea>
                    <?php if (isset($errors['address'])): ?>
                      <ul class="parsley-error-list"><li class="required"><?php echo $errors['address'] ?></li></ul>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                  </div>
                  </div> 
                  <div class="col-md-4">  
                    <button type="submit" name="save" id="save" class="btn btn-info">Save</button>
                    <?php if ($mode == 'ADD') { ?> 
                      <button type="button" name="cancle" id="cancle" class="btn btn-default" onclick="window.location = 'trigger_address.php'">Clear</button>
                    <?php } else { ?>
                      <button type="button" name="cancle" id="cancle" class="btn btn-default" onclick="window.location = 'trigger_address.php'">Cancel</button>
                    <?php } ?>
                  </div>
              </form>
                          
          </div> 
         
             
      <div class="panel-body">
      	<div class="col-sm-12">
        <div class="table-responsive">
          <table class="<?php echo $table_class; ?>">
            <thead>
              <tr>
                <th style="width: 5%;" class="index">ID</th>
                <th style="width: 5%;">Title</th>
                <th style="width: 55%;">Address</th>              
                <th style="width: 20%;">Date/Time</th>             
                <th style="width: 20%;" class="icons">Manage</th>             
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($rows)) { ?>
                <?php foreach ($rows as $key => $row) { ?>        
                  <tr>
                    <td class="index"><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td class=""><?php echo html_entity_decode($row['address']); ?></td>
                    <td class=""><?php echo retrieveDate($row['created_at']); ?></td>             
                    <td class="icons">
                      <a href="trigger_address.php?id=<?php echo $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="Edit Address" ><i class="fa fa-edit"></i></a> 
                      <a href="delete_template_address.php?address_id=<?php echo $row['id']; ?>" title="" id="<?= $row['id']; ?>" class="<?= $row['total_used'] > 0 ? 'check_template' : 'ajax_check_template' ?>" data-toggle="tooltip" data-original-title="Delete Address" ><i class="fa fa-trash"></i></a>
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
    CKEDITOR.replace('address', {
      allowedContent: true,
      toolbar: [
        ['Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Link', 'Unlink', ],
      ]

    });
    $('.ajax_check_template').click(function (e) {
      e.preventDefault();
      var obj = $(this);
      var id = obj.attr('id');

      if (confirm('Are you sure you want to delete?')) {
        $.ajax({
          url: $(this).attr('href'),
          type: 'post',
          data: {'address_id': id, 'is_ajax': 'Y'},
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

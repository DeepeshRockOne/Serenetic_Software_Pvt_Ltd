<?php include("notify.inc.php"); ?>
<div class="triggers">
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
      <?php $trigger="active";
      include("br_broadcaster_tabs.inc.php");
      ?>
    </ul>
  </div>
  <div class="panel panel-default panel-block ">
    <div class="panel-heading">
      <div>
        <i class="fa fa-envelope" style="top:-1px;"></i>&nbsp;&nbsp;
        <h1>
        <span>Manage Triggers</span>
        </h1>
      </div>
    </div>
  </div>
  <div class="tabbing-tab">
    <ul class="nav nav-tabs customtab">
      <?php $triggers = 'active'; ?>
      <?php include_once('triggers_tabs.inc.php'); ?>
    </ul>
  </div>
  <div class="panel panel-default panel-block ">
    <div class="list-group">
      <div class="list-group-item clearfix">
        <form id="frm_category" name="frm_category" class="form-horizontal " action="" method="POST">
          <div class="row">
            <div class="col-sm-4">
              <div class="panel panel-default panel-block panel-title-block m-b-10">
                <div class="panel-heading p-b-0">
                  Search
                </div>
                <div class="panel-body">
                  <div class="">
                    <div class="form-group m-b-10">
                      <label for="fields" class="col-sm-4 control-label">Category</label>
                      <div class="col-sm-8">
                        <select onchange="javascript:location.href='?cat_id='+this.value;"  class="form-control select2 placeholder" >
                          <option value="">&nbsp;</option>
                          <?php if(!empty($rsTrigger)){ ?>
                            <?php foreach ($rsTrigger as $key => $value) { ?>
                              <option value="<?=$value['id']?>" <?php if($value['id']==$cat_id){echo 'selected';}?>><?=$value['title']?>(<?=$value['company_name']?>)</option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-8">
              <div class="panel panel-default panel-block panel-title-block m-b-10">
                <div class="panel-heading p-b-0">
                  Add Category
                </div>
                <div class="panel-body">
                  <div class="col-md-5">
                    <div class="form-group m-b-10">
                      <div class="phone-control-wrap">
                        <div class="phone-addon"><label for="fields">Select Company</label></div>
                        <div class="phone-addon">
                          <select class="form-control select2 placeholder" name="company_id">
                            <option value="0">&nbsp;</option>
                            <?php if(!empty($rsCompany)){ ?>
                              <?php foreach ($rsCompany as $key => $value) { ?>
                                <option value="<?=$value['id']?>"><?=$value['company_name']?></option>
                              <?php } ?>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="form-group m-b-10">
                      <div class="phone-control-wrap">
                        <div class="phone-addon"><label for="fields">Category Name</label></div>
                        <div class="phone-addon">
                          <input type="text" id="cat_title" class="form-control" name="cat_title" value='<?php echo $cat_title ?>' />
                          <?php if(isset($errors['cat_title'])): ?>
                          <ul class="parsley-error-list"><li class="required"><?php echo $errors['cat_title'] ?></li></ul>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2 text-center">
                    <button type="submit" name="cat_save" id="cat_save" class="btn btn-info">Save</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="list-group-item">
        <?php
        $category_id = 0;
        $sid = 0;
        ?>
        <?php if($strTotalPerPage > 0){ ?>
        <?php foreach ($rs AS $key=>$rows) { ?>
        <?php if($category_id == 0){ ?>
        <?php $sid=1; ?>
        <h4><?php echo $rows['category']. " (".$rows['company_name'].") " ?>
        <a href="trigger_add.php?category_id=<?=$rows['category_id']?>&company_id=<?= $rows['company_id']?>" class="fa fa-plus" title="" data-toggle="tooltip" data-original-title="Add New Trigger"><i></i></a>
        </h4>
        <div class="table-responsive">
          <table class="<?=$table_class?>">
            <thead>
              <tr>
                <th>ID</th>
                <th class="index">System ID</th>
                <th width="28%">Title</th>
                <th>Email</th>
                <th>SMS</th>
                <th>Status</th>
                <th>Programming Status</th>
                <th class="icons">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php } elseif($category_id != $rows['category_id']){ ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php $sid=1; ?>
      <div class="list-group-item">
        <h4><?php echo $rows['category']. " (".$rows['company_name'].") " ?>
        <a href="trigger_add.php?category_id=<?=$rows['category_id']?>&company_id=<?= $rows['company_id']?>" title="" data-toggle="tooltip" data-original-title="Add New Trigger"><i class="fa fa-plus"></i></a>
        </h4>
        <div class="table-responsive">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" class="<?=$table_class?>">
            <thead>
              <tr>
                <th>ID</th>
                <th class="index">System ID</th>
                <th width="28%">Title</th>
                <th>Email</th>
                <th>SMS</th>
                <th>Status</th>
                <th>Programming Status</th>
                <th class="icons">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php } ?>
              <?php if($rows['id']) { ?>
              <tr>
                <td><?php echo $rows['id'];?></td>
                <td class="index"><a href="trigger_view.php?id=<?php echo $rows['id']; ?>" class="colorbox_popup" title="View Trigger Details" ><?php echo $rows['category_id'] . "." . $sid; ?></a></td>
                <td style="text-align:left;"><?php echo stripslashes($rows['title']); ?></td>
                <td class="">
                  <a href="trigger_edit.php?id=<?php echo $rows['id']; ?>" class="colorbox_popup" title="Edit Trigger" ><?php echo $rows['type'] == "Email" || $rows['type'] == "Both" ? "Yes": "No"; ?></a>
                </td>
                <td class="">
                  <a href="trigger_edit.php?id=<?php echo $rows['id']; ?>" class="colorbox_popup" title="Edit Trigger" ><?php echo $rows['type'] == "SMS" || $rows['type'] == "Both" ? "Yes": "No"; ?></a>
                </td>
                <td class="center">
                  <form method="get">
                    <select style="width:150px !important;" name="s_status"  class="s_status11 form-control select2 placeholder" status-id="<?php echo $rows['id']; ?>" >
                      <option value="Active" <?php if($rows['status']=="Active"){echo 'selected';}?>>Active</option>
                      <option value="Inactive" <?php if($rows['status']=="Inactive"){echo 'selected';}?>>Inactive</option>
                    </select>
                  </form>
                </td>
                <td class=""><?php echo ( ($rows['programming_status'] == 'Programming Completed' ) ? 'Completed' : 'Pending' ) ?></td>
                <td class="icons">
                  <a href="trigger_edit.php?id=<?php echo $rows['id']; ?>" class="colorbox_popup" ><i class="fa fa-edit"></i></a>
                  <a href="javascript:void(0);" class="delete_slider" deleted_id="<?= $rows['id']; ?>" data-toggle="tooltip"><i class="fa fa-trash"></i></a>
                  <a href="trigger_test.php?id=<?php echo $rows['id']; ?>" class="colorbox_popup" data-toggle="tooltip"><span class="btn btn-xs btn-info icon-xs"><i class="fa fa-search"></i>Test</span></a>
                </td>
              </tr>
              <?php
              $sid = $sid + 1;
              } else { ?>
              <tr>
                <td colspan="7">No Triggers available under this categroy.</td>
              </tr>
              <?php }
              $category_id = $rows['category_id'];
              } ?>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  

  $(document).off('click', '.colorbox_popup');
  $(document).on('click', '.colorbox_popup', function (e) {
    e.preventDefault();

    $.colorbox({
      className: 'col_responsive',
      href: $(this).attr('href'),
      iframe: true,
      width: '1000px',
      height: '600px'
    });
  });

  $(document).off('click', '.delete_slider');
  $(document).on('click', '.delete_slider', function (e) {
    e.preventDefault();
    var id = $(this).attr('deleted_id');
    swal({
      text: "Delete Dashboard Slider: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      showCloseButton: true
    }).then(function() {
      $.ajax({
        url: 'delete_triggers.php',
        data: {id: id},
        method: 'POST',
        success: function(data) {
          if (data.status == 'success') {
            window.location = 'triggers.php';
          } else {
            setNotifyError('Something is wrong here');
          }
        }
      });
    }, function (dismiss) {
       
    });
  });

  $(document).off('change', '.s_status11');
  $(document).on('change', '.s_status11', function (e) {
    e.stopPropagation();
    
    var status_id = $(this).attr('status-id');
    var s_status = $(this).val();
    
    swal({
      text: "Change Status:Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      showCloseButton: true
    }).then(function() {
      $.ajax({
        url: 'change_trigger_status.php',
        type: 'POST',
        data: {id: status_id,status: s_status},
        dataType: "json",
        success: function(res) {
          if (res.status == "success") {
            setNotifySuccess(res.msg)
          }else{
            setNotifyError(res.msg);
          }
        }
      });
    }, function (dismiss) {
      window.location.reload();
    })
  });

</script>

<?php if ($is_ajaxed) { ?>
  <div class="clearfix tbl_filter">
    <?php if ($total_rows > 0) {?>
      <div class="pull-left">
        <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
          <div class="form-group mn">
            <label for="user_type">Records Per Page </label>
          </div>
          <div class="form-group mn">
            <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
              <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
              <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
              <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
              <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
            </select>
          </div>
        </div>
      </div>
      <div class="pull-right">
        <div class="m-b-15">
          <a class="btn btn-default" href="add_access_level.php"> Manage Admins</a>
          <a class="btn btn-action" href="add_new_admin.php">+ Admin</a>
        </div>
      </div>
      <?php }?>
  </div>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr class="data-head">
        <th><a href="javascript:void(0);" data-column="id" data-direction="<?php echo $SortBy == 'id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
        <th><a href="javascript:void(0);" data-column="fname" data-direction="<?php echo $SortBy == 'fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name/Phone/Email</a></th>
        <th><a href="javascript:void(0);" data-column="type" data-direction="<?php echo $SortBy == 'type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Level</a></th>
        <th >Access</th>
        <th width="15%"><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
        <th  width="130px" >Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
        <tr>
          <td>
            <a href="javascript:void(0);" data-id='<?=$rows['id']?>' id="links1" class="admin_profile text-red">
              <strong ><?php echo $rows['display_id']; ?></strong>
            </a><br />
            <?php echo date('m/d/Y', strtotime($rows['created_at'])); ?>
          </td>
          <td>
            <strong ><?php echo $rows['fname']; ?> <?php echo $rows['lname']; ?></strong><br />
            <?php echo $rows['email']; ?><br />
            <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['phone']);  ?>
          </td>
          <td>
            <div class="theme-form pr w-200">
              <select name="access_level" id="change_type11_<?php echo $rows['id']; ?>" data-old_type="<?=$rows['type']?>" class="change_type11 form-control has-value select2 placeholder"   >
                <?php
                   $acname = array();
                 if(!empty($res_acls)){ ?>
                  <?php foreach($res_acls as $ac){?>
                    <option value="<?=$ac['name'];?>" <?=($rows['type'] == $ac['name'] ? 'selected' : '')?> ><?=$ac['name'];?></option>
                  <?php
                  array_push($acname,$ac['name']);
                }
                  if(!in_array($rows['type'],$acname)){
                ?>
                  <option value='' selected></option>
                <?php } }?>
              </select>
              <label for="access_level">Select</label>
            </div>
          </td>
          <td class="icons">
            <a href="admins_update_feature.php?id=<?php echo $rows['id'] ?>" class="access_popup" data-toggle="tooltip" title="Update Access Levels"><i class="fa fa-wrench"></i></a>
          </td>
          <td>
            <?php if ($rows['status'] == "Pending") { ?>
              <?php if ($rows['invited_difference'] > 168) {?>
                <a href="reinvite_admin.php?id=<?php echo $rows['id']; ?>" class="re_invite_popup btn btn-action-o w-130" data-toggle="tooltip" title="Re-invite"><i class="fa fa-envelope re_invite_icon" aria-hidden="true"></i>&nbsp; Re-Invite</a>
              <?php }else{ ?>
                <a href="reinvite_admin.php?id=<?php echo $rows['id']; ?>" class="re_invite_popup btn btn-info  w-130" data-toggle="tooltip" title="Link Active for 3<br> more Days" data-html="true"><i class="material-icons fs16 v-align-middle">send</i>&nbsp; Invited</a>
              <?php } }else { ?>
              <div class="theme-form pr w-200">
                <select name="admin_status" class="form-control admin_status has-value" data-old_status="<?=$rows['status']?>" id="admin_status_<?=$rows['id'];?>"  >
                  <option value="Pending"   <?php if ($rows['status'] == 'Pending') { echo "selected='selected'"; } ?>>Pending</option>
                  <option value="Active"    <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?>>Active</option>
                  <option value="Inactive" <?php if ($rows['status'] == 'Inactive') { echo "selected='selected'"; } ?>>Inactive</option>
                </select>
                <label for="admin_status">Select</label>
              </div>
            <?php }?>
          </td>
          <td class="icons">
            <?php if($rows['status'] != "Pending"){?>
              <a href="admin_profile.php?id=<?php echo $rows['id'] ?>" target="_blank" data-toggle="tooltip" data-trigger="hover" title="Edit"><i class="fa fa-eye " ></i></a>
            <?php } ?>
            <?php if(in_array($rows['status'], array('Pending','Inactive'))){?>
              <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" title="Delete" onclick="delete_admin('<?=$rows['id']?>')"><i class="fa fa-trash" aria-hidden="true"></i>
              </a>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="6">No record(s) found</td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="6">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
</div>
<?php }else{ ?>
<div class="panel panel-default panel-block panel-title-block">
  <form id="frm_search" action="admins.php" method="GET" autocomplete="off">
    <div class="panel-left">
      <div class="panel-left-nav">
        <ul>
          <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="panel-right">
      <div class="panel-heading">
        <div class="panel-search-title"> 
          <span class="clr-light-blk">SEARCH</span>
        </div>
      </div>
      <div class="panel-wrapper collapse in">
        <div class="panel-body theme-form">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group height_auto">
                  <input type="text" name="display_id" id="display_id" class="listing_search" value="<?=checkIsset($display_id)?>" />
                  <label>ID Number(s)</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row" id="show_date">
                <div id="date_range" class="col-md-12">
                  <div class="form-group">
                    <select class="form-control" id="join_range" name="join_range">
                      <option value=""> </option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                    </select>
                    <label>Added Date</label>
                  </div>
                </div>
                <div class="select_date_div col-md-9" style="display:none">
                  <div class="form-group">
                    <div id="all_join" class="input-group"> 
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                    </div>
                    <div  id="range_join" style="display:none;">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> 
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                          </div>
                        </div>
                        <div class="phone-addon">
                          <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> 
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
              <div class="form-group ">
                <input type="text" class="form-control listing_search" name="fname" value="<?php echo $fname ?>">
                <label>First Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                <input type="text" class="form-control listing_search" name="lname" value="<?php echo $lname ?>">
                <label>Last Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                <input type="text" class="form-control listing_search" onkeypress="return isNumberKey(event)" maxlength='10' name="phone" value="<?php echo $phone ?>">
                <label>Phone</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" class="form-control listing_search" name="email" value="<?php echo $email ?>">
                <label>Email</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="leveltype[]"  id="a_level" multiple="multiple"  >
                    <?php if(!empty($res_acls)){ ?>
                      <?php foreach($res_acls as $ac){ ?>
                        <option value="<?=$ac['name'];?>" <?=($a_level == $ac['name'] ? 'selected' : '')?> ><?=$ac['name'];?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label> Level</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="member_status[]"  id="member_status" multiple="multiple"  >
                    <option value="Pending" <?=$s_member_status == 'Pending'?"selected='selected'":''?>> Pending </option>
                    <option value="Active" <?=$s_member_status == 'Active'?"selected='selected'":''?>> Active </option>
                    <option value="Inactive" <?=$s_member_status == 'Inactive'?"selected='selected'":''?>> Inactive </option>
                  </select>
                  <label>Status</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="_access[]"  id="_access" multiple="multiple"  >
                    <?php foreach($features_arr as $feature) { ?>
                      <?php $flabel = $feature["title"] == 'Eligibility Files' ? 'Eligibility' : $feature["title"];?>
                      <?php if (!empty($feature['child'])) { ?>
                          <optgroup label="<?= $flabel ?>">
                          <?php foreach ($feature['child'] as $child) {?>
                              <?php $label = $child["title"] == 'Types' ? 'Categories' : $child["title"]; ?>
                              <?php $label = $label == 'Pending Eligibility' ? 'Generator' : $label; ?>
                              <?php $label = $label == 'Eligibility History' ? 'History' : $label; ?>
                              <option value="<?=$child['id']?>" ><?=$label?></option>
                          <?php } ?>
                          </optgroup>
                      <?php } else { ?>
                          <optgroup label="<?= $flabel ?>">
                          <option value="<?=$feature['id']?>" class="hidden" style="display: none;"><?=$flabel?></option>
                          </optgroup>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Access</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                <select class="form-control" name="refine_filter" >
                  <option value="" disabled selected hidden> </option>
                  <option value="read_write">Read & Write</option>
                  <option value="blank">Blank</option>
                </select>
                <label>Refine Filter</label>
              </div>
            </div>
          </div>
          <div class="panel-footer clearfix ">
        <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
        <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'admins.php'"> <i class="fa fa-search-plus"></i> View All </button>
        <button type="button" name="export_admin" id="export_admin" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
        <input type="hidden" name="export" id="export" value=""/>
        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
        <input type="hidden" name="page" id="nav_page" value="" />
        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
      </div>
        </div>
      </div>
      
    </div>
  </form>
  <div class="search-handle">
    <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
  </div>
</div>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data" class="list-group-item"> </div>
  </div>
</div>
<script type="text/javascript">

$(document).ready(function() {
  dropdown_pagination('ajax_data');

  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });
  
  $("#_access").multipleSelect();

  $("#a_level, #member_status").multipleSelect({
      selectAll: false,
      filter: false
  });

  $(document).keypress(function (e) {
    if (e.which == 13) {
      ajax_submit();
    }
  });

  ajax_submit();

  initSelectize('display_id','AdminID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
});

$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  $('.date_picker').val('');
  if($(this).val() == ''){
    $('.select_date_div').hide();
    $('#date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#date_range').removeClass('col-md-12').addClass('col-md-3');
    $('.select_date_div').show();
    if ($(this).val() == 'Range') {
      $('#range_join').show();
      $('#all_join').hide();
    } else {
      $('#range_join').hide();
      $('#all_join').show();
    }
  }
});

$(document).off('click', '.re_invite_popup');
$(document).on('click', '.re_invite_popup', function (e) {
  e.preventDefault();
  $.colorbox({
    href: $(this).attr('href'),
    iframe: true, 
    width: '768px', 
    height: '240px'
  });
});

$(document).off('click', '.access_popup');
$(document).on('click', '.access_popup', function(e) {
  e.preventDefault();
  $.colorbox({
    href: $(this).attr('href'),
    iframe: true,
    width: '500px',
    height: '600px',
    onClosed: function() {
      ajax_submit();
    }
  });
});

  $(document).off('change', '.admin_status');
$(document).on('change', '.admin_status', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('admin_status_', '');
  var idstr = $(this).attr('id');
  var admin_status = $(this).val();
  var old_status = $(this).attr('data-old_status');
  swal({
    text: "<br>Change Status: Are you sure?",
    showCancelButton: true,
    confirmButtonText: "Confirm",
  }).then(function() {
    window.location = 'admins.php?admin_id=' + id + '&member_status_c=' + admin_status + '&old_status=' + old_status;
  }, function(dismiss) {
      $("#"+idstr).val(old_status);
      $("#"+idstr).selectpicker('render');
});
});

$(document).off('change', '.change_type11');
$(document).on('change', '.change_type11', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('change_type11_', '');
  var type = $(this).val();
  var old_type = $(this).attr('data-old_type');
  if ($(this).val() == 'Special Administrator' || $(this).val() == 'Agent Licensing' || $(this).val() == 'Agent Support' || $(this).val() == 'Enroller' || $(this).val() == 'Member Services') {
    $('#super_id').val(id);
    $('#super_type').val(type);
    $.colorbox({
      iframe: true,
      href: 'admins_update_feature.php?id=' + id + '&type=' + type + '&old_type=' + old_type,
      width: '500px',
      height: '800px',
      onClosed: function() {
        ajax_submit();
      }
    });
  } else {
    swal({
      text: "<br>Change Admin Level: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm"
    }).then(function() {
      window.location = 'admins.php?id=' + id + '&type=' + type + '&old_type=' + old_type;
      },function(dismiss){
        $('#change_type11_'+id).val(old_type);
        $('#change_type11_'+id).selectpicker('render');
        return false;
    });
  }
});

$(document).off("submit", "#frm_search");
$(document).on("submit", "#frm_search", function(e) {
  $('#nav_page').val(1);
  e.preventDefault();
  disable_search();
});

$(document).off('click', '#ajax_data tr.data-head a');
$(document).on('click', '#ajax_data tr.data-head a', function(e) {
  e.preventDefault();
  $('#sort_by_column').val($(this).attr('data-column'));
  $('#sort_by_direction').val($(this).attr('data-direction'));
  ajax_submit();
});

$(document).off('click', '#ajax_data ul.pagination li a');
$(document).on('click', '#ajax_data ul.pagination li a', function(e) {
  e.preventDefault();
  $('#ajax_loader').show();
  $('#ajax_data').hide();
  $.ajax({
    url: $(this).attr('href'),
    type: 'GET',
    success: function(res) {
      $('#ajax_loader').hide();
      $('#ajax_data').html(res).show();
      $('[data-toggle="tooltip"]').tooltip();
      common_select();
    }
  });
});
$(document).off('click', '#export_admin');
$(document).on('click', '#export_admin', function(e) {
    confirm_export_data(function() {
        $("#export").val('export_admin');
        $('#ajax_loader').show();
        $('#is_ajaxed').val('1');
        var params = $('#frm_search').serialize();
        $.ajax({
            url: $('#frm_search').attr('action'),
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                $('#ajax_loader').hide();
                $("#export").val('');
                if(res.status == "success") {
                    confirm_view_export_request();
                } else {
                    setNotifyError(res.message);
                }
            }
        });
    });
});

$(document).off('click', '.admin_profile');
$(document).on('click', '.admin_profile', function(e) {
  $id = $(this).data('id');
  $timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  window.open('admin_profile.php?id=' + $id + '&timezone=' + $timezone, '_blank');
});

function disable_search() {
  if ($(".listing_search").filter(function() {
      return $(this).val();
    }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
      return $(this).val();
      0
    }).length > 0)) {
    ajax_submit();
  } else {
    swal('Oops!!', 'Please Enter Data To Search', 'error');
  }
}

function ajax_submit() {
  $('#ajax_loader').show();
  $('#ajax_data').hide();
  $('#is_ajaxed').val('1');
  $("#export").val('');
  var params = $('#frm_search').serialize();
  var cpage = $('#nav_page').val();
  $.ajax({
    url: $('#frm_search').attr('action'),
    type: 'GET',
    data: params,
    success: function(res) {
      $('#ajax_loader').hide();
      $('#ajax_data').html(res).show();
      common_select();
      $('[data-toggle="tooltip"]').tooltip();
    }
  });
  return false;
}

function delete_admin(admin_id) {
  swal({
    text: '<br>Delete Admin: Are you sure?',
    showCancelButton: true,
    confirmButtonText: 'Confirm',
    cancelButtonText: 'Cancel',
  }).then(function() {
    $.ajax({
      url: "ajax_delete_admin.php",
      type: 'GET',
      data: {id: admin_id},
      success: function(res) {
        if (res.status == 'success') {
          setNotifySuccess(res.msg);
          setTimeout(function(){ 
            ajax_submit(); 
          }, 1000);
        }else{
          setNotifyError(res.msg);
        }
      }
    });
  }, function(dismiss) {
  });
}

function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57)){
    return false;
  }
  return true;
}
</script>
<?php }?>

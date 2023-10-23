<?php if($is_ajaxed_admins){ ?>
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
              <a href="admin_profile.php?id=<?php echo $rows['id'] ?>" target="_blank" data-toggle="tooltip" title="Edit"><i class="fa fa-eye " ></i></a>
            <?php } ?>
            <?php if(in_array($rows['status'], array('Pending','Inactive'))){?>
              <a href="javascript:void(0);" data-toggle="tooltip" title="Delete" onclick="delete_admin('<?=$rows['id']?>')"><i class="fa fa-trash" aria-hidden="true"></i>
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

<?php } else { ?>

    <form id="frm_search_admins" action="global_admins.php" method="GET" class="sform" >    
      <input type="hidden" name="search_type" id="search_type" value="" />
      <input type="hidden" name="is_ajaxed_admins" id="is_ajaxed_admins" value="1" />
      <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
  </form>

 <div class="panel-body">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
       <div class="loader"></div>
    </div>
    <div id="ajax_data" style="display: none;"> </div>
 </div>
<script type="text/javascript">

$(document).off('change', '.admin_status');
$(document).on('change', '.admin_status', function(e) {
  e.stopPropagation();
  var id = $(this).attr('id').replace('admin_status_', '');
  var admin_status = $(this).val();
  var old_status = $(this).attr('data-old_status');
  swal({
    text: "<br>Change Status: Are you sure?",
    showCancelButton: true,
    confirmButtonText: "Confirm",
  }).then(function() {
    window.location = 'admins.php?admin_id=' + id + '&member_status_c=' + admin_status + '&old_status=' + old_status;
  });
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
        ajax_submit_admins();
      }
    });
  } else {
    swal({
      text: "Make this Change: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm"
    }).then(function() {
      // ajax_submit_admins();
      window.location = 'admins.php?id=' + id + '&type=' + type + '&old_type=' + old_type;
    });
  }
});

$(document).off('click', '.admin_profile');
$(document).on('click', '.admin_profile', function(e) {
  $id = $(this).data('id');
  $timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  window.open('admin_profile.php?id=' + $id + '&timezone=' + $timezone, '_blank');
});  
$(document).off('click','.access_popup');
  $(document).on('click','.access_popup',function (e){
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '500x', 
      height: '800PX',
      onClosed:function(){
          // window.location='all_users.php'
      }
    });
  });

$(document).on('change','.admin_change_access_level',function (e) {
    var id = $(this).attr('id').replace('admin_change_access_level_', '');
    var type = $(this).val();
    var old_type = $(this).attr('data-old_type');
    if($(this).val()=='Special Administrator'|| $(this).val()=='Agent Licensing' || $(this).val()=='Agent Support'|| $(this).val()=='Enroller' || $(this).val()=='Member Services'){
        $('#super_id').val(id);
        $('#super_type').val(type);
        open_access_level(id,type,old_type);
                  
    }else{

    swal({
        text: "<br>Change Admin Level: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
    }).then(function () {
        window.location = 'all_users.php?id=' + id + '&type=' + type +'&old_type='+old_type;
    }, function (dismiss) {
        window.location.reload();
    })
    }
});
open_access_level = function(id,type,old_type){
  $.colorbox({
          iframe: true , 
          href: 'admins_update_feature.php?id='+id+'&type='+type+'&old_type='+old_type+'&action='+'all_users', 
          width: '500x', 
          height: '800PX'}
  );
}
$(document).on('click','.popup',function (e){
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '800px', 
      height: '303px'
    });
  });
    
$(document).ready(function() {
  // Copy invitation link in clipboard
  ajax_submit_admins();
  $(document).off('click', '#ajax_data tr.data-head a');
  $(document).on('click', '#ajax_data tr.data-head a', function (e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      ajax_submit_admins();
  });
  
  $(document).off('click', '#ajax_data ul.pagination li a');
  $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function (res) {
              $('#ajax_loader').hide();
              $('#ajax_data').html(res).show();
              common_select();
              $('[data-toggle="tooltip"]').tooltip();
          }
      });
  });


  var clipboard = new Clipboard('.popup_link');
  
  // Open reinvite popup
  
});
function delete_admin(admin_id) {
  swal({
      text: '<br>Delete Admin: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
  }).then(function () {
      $.ajax({
          url: "ajax_delete_admin.php",
          type: 'GET',
          data: {id: admin_id},
          success: function (res) {
              if (res.status == 'success')
              {
                  setNotifySuccess(res.msg);
                  window.location.reload();
              }
          }
      });
  }, function (dismiss) {
      window.location.reload();
  })
}
function ajax_submit_admins() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed_admins').val('1');

    var params = $('#frm_search_admins').serialize();
    var all_usersFrm = $('#all_usersFrm').serialize();
    params += '&'+all_usersFrm;
    $.ajax({
        url: $('#frm_search_admins').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    return false;
  }

</script>
<?php } ?>
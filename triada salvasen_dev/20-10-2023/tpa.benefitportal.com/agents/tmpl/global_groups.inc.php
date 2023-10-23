<?php if ($is_ajaxed_groups) { ?> 
  <div class="table-responsive">
      <table class="<?=$table_class?> ">
          <thead>
              <tr class="data-head">
                  <th><a href="javascript:void(0);" data-column="c.joined_date" data-direction="<?php echo $SortBy == 'c.joined_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date ID</a></th><th><a href="javascript:void(0);" data-column="c.business_name" data-direction="<?php echo $SortBy == 'c.business_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Group Name</a></th>
                  <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
                  <th><a href="javascript:void(0);" data-column="s.fname" data-direction="<?php echo $SortBy == 's.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enrolling Agent</a></th>
                  <th class="text-center"><a href="javascript:void(0);" data-column="total_products" data-direction="<?php echo $SortBy == 'total_products' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Products</a></th>
                  <th class="text-center"><a href="javascript:void(0);" data-column="total_members" data-direction="<?php echo $SortBy == 'total_members' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Members</a></th>
                  <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
              </tr>
          </thead>
          <tbody>
              <?php if ($total_rows > 0) { ?>
                  <?php foreach ($fetch_rows as $rows) { ?>
                      <tr>
                          <td>
                              <a href="javascript:void(0);" class="text-red">
                              <strong class="fw600"><?php echo $rows['rep_id']; ?></strong></a></br>
                              <?php echo empty($rows['joined_date']) ? date('m/d/Y', strtotime($rows['invite_at']))  : date('m/d/Y', strtotime($rows['joined_date'])); ?>
                          </td>
                          <td><?= $rows['business_name'] ?></td>
                          <td>
                            <a href="javascript:void(0);" class="text-red"><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></a>
                              <br />
                              <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['cell_phone']) ?><br/>
                              <?php echo $rows['email']; ?><br />
                          </td>
                          <td>
                              <strong><?php echo stripslashes($rows['s_fname'] . ' ' . $rows['s_lname']); ?></strong> 
                              <br/>
                              <?php echo $rows['sponsor_rep_id']; ?><br />
                          </td>

                          <td class="text-center"><a href="javascript:void(0);" class="fw600 text-red group_products" data-id="<?=$rows['id'];?>"><?=$rows['total_products']?></a></td>

                          <td class="text-center"><a href="javascript:void(0);" class="fw600 text-red group_members" data-id="<?=$rows['id'];?>"><?=$rows['total_members']?></a></td>

                          <td class="w-200">
                              <?php if (in_array($rows['status'],array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Group Abandon'))) { ?>
                                  <?php if($rows['status'] != 'Invited'){ ?>
                                      <a href="javascript:void(0)" class="group_status" id="group_status_<?=$rows['id']?>" data-status="<?=$rows["status"] == 'Active'?'Contracted':$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content=""> <?=$rows["status"] == 'Active'?'Contracted': $rows["status"]?></a>
                                  
                                  <?php } else if(($rows['status'] == 'Invited') && ($rows['invite_time_diff'] >168)){ ?>
                                      <a href= "reinvite_group.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="btn btn-action-o w-130 resend_popup" data-toggle="tooltip" title="Re-invite"  id = "group_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content="" style="color: red;"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; <?=$rows["invite_time_diff"] > 168 ? 'Expired':'Re-invited'?></a>
                                  <?php } else { ?>
                                      <a href= "reinvite_group.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="btn btn-action-o w-130 resend_popup" data-toggle="tooltip" title="Re-invite"  id = "group_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""> <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; <?=$rows["status"] == 'Active'?'Contracted': 'Re-invite'?></a>
                                  <?php } ?>
                              <?php }else if(in_array($rows['status'], array('Contracted','Suspended','Terminated','Active'))){?>
                                  <div class="theme-form pr">
                                      <select name="member_status" class="form-control member_status has-value" id="member_status_<?=$rows['id'];?>">
                                          <option value="Active" <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?> disabled="disabled">Contracted</option>
                                          <option value="Suspended" <?php if ($rows['status'] == 'Suspended') { echo "selected='selected'"; } ?> disabled="disabled">Suspended</option>
                                          <option value="Terminated" <?php if ($rows['status'] == 'Terminated') { echo "selected='selected'"; } ?> disabled="disabled">Terminated</option>
                                      </select>
                                      <label>Status</label>
                                  </div>
                              <?php } else {
                                  echo $rows['status'];
                              }?>                              
                          </td>
                      </tr>
                  <?php }?>
              <?php } else {?>
                  <tr>
                      <td colspan="8" align="center">No record(s) found</td>
                  </tr>
              <?php }?>
          </tbody>
          <?php if ($total_rows > 0) { ?>
              <tfoot>
              <tr>
                      <td colspan="8">
                          <?php echo $paginate->links_html; ?>
                      </td>
                  </tr>
              </tfoot>
          <?php } ?>
      </table>
  </div>
<?php } else { ?>
 
      <form id="frm_search" action="global_groups.php" method="GET" class="theme-form">
          <input type="hidden" name="is_ajaxed_groups" id="is_ajaxed_groups" value="1"/>
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection?>">
          <input type="hidden" name="search_type" id="search_type" value="" />
      </form>
 
  <div class="panel-body">
          <div id="ajax_loader" class="ajex_loader" style="display: none;">
              <div class="loader"></div>
          </div>
          <div id="ajax_data" ></div>
  </div>
 
  <script type="text/javascript">

    $(document).off('click', '.group_products');
      $(document).on('click', '.group_products', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.colorbox({
          href: "agent_products_popup.php?id=" + id,
          iframe: true,
          width: '800px',
          height: '450px'
        });
      });
      
      $(document).ready(function() {

          ajax_submit();
      });

      $(document).off('click', '.resend_popup');
      $(document).on('click', '.resend_popup', function (e) {
          e.preventDefault();
          $.colorbox({
            href: $(this).attr('href'),
            iframe: true, 
            width: '768px', 
            height: '240px'
          })  
      });

      function get_status($id) {
          $('#group_status_' + $id).html('Re-Invite');
          $("#group_status_" + $id).css('color', '#2C4C80');
          $("#group_status_as_" + $id).css('color', '#2C4C80');
      }

      $(document).off('change', '.member_status');
      $(document).on("change", ".member_status", function(e) {
          e.stopPropagation();
          var id = $(this).attr('id').replace('member_status_', '');
          var member_status = $(this).val();
          swal({
              text: "Change Status: Are you sure?",
              showCancelButton: true,
              confirmButtonText: "Confirm",
          }).then(function() {
              if (member_status == 'Terminated' || member_status == 'Suspended') {
                  $.colorbox({
                      iframe: true,
                      href: "<?=$ADMIN_HOST?>/reason_change_group_status.php?id=" + id + "&status=" + member_status,
                      width: '600px',
                      height: '400px',
                      trapFocus: false,
                      closeButton: false,
                      overlayClose: false,
                      escKey: false,
                      onClosed: function() {
                          $.ajax({
                              url: "reason_change_group_status.php",
                              type: 'POST',
                              dataType: 'json',
                              data: {
                                  customer_id: id,
                                  action: 'OldStatus'
                              },
                              success: function(data) {
                                  if (data.status == 'success') {
                                      $status = data.member_status;
                                      $('.member_status [value=' + $status + ']').attr('selected', 'true');
                                  }
                              }
                          });
                      }
                  });
              } else {
                  $.ajax({
                      url: 'change_group_status.php',
                      data: {
                          id: id,
                          status: member_status
                      },
                      method: 'POST',
                      dataType: 'json',
                      success: function(res) {
                          if (res.status == "success") {
                              setNotifySuccess(res.msg);
                          }else{
                              setNotifyError(res.msg);
                              ajax_submit();
                          }
                      }
                  });
              }
          }, function(dismiss) {
              ajax_submit();
          })
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
             
      $(document).off("submit","#frm_search");
      $(document).on("submit","#frm_search",function(e){
          e.preventDefault();
          disable_search();
      });

      function delete_group(group_id) {
          swal({
             
              text: 'Delete Group: Are you sure?',
              showCancelButton: true,
              cancelButtonText: 'Cancel',
          }).then(function() {
              $("#ajax_loader").show();
              $.ajax({
                  url: "ajax_delete_group.php",
                  type: 'GET',
                  data: {
                      id: group_id,
                      search:'Y'
                  },
                  dataType: 'JSON',
                  success: function(res) {
                      if (res.status == 'success') {
                          setNotifySuccess(res.msg);
                          ajax_submit();
                      } else {
                          setNotifyError(res.msg);
                      }
                  }
              });
          }, function(dismiss) {})
      }

      function ajax_submit() {
          $('#ajax_loader').show();
          $('#ajax_data').hide();
          $('#is_ajaxed_groups').val('1');
          var params = $('#frm_search').serialize();
          var all_usersFrm = $('#all_usersFrm').serialize();
          params += '&'+all_usersFrm;
          $.ajax({
              url: $('#frm_search').attr('action'),
              type: 'GET',
              data: params,
              success: function(res) {
                  $('#ajax_loader').hide();
                  $('#ajax_data').html(res).show();
                  $('[data-toggle="tooltip"]').tooltip();
                  common_select();
                  
              }
          });
          return false;
      }

      function isNumberKey(evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode
          if (charCode > 31 && (charCode < 48 || charCode > 57)){
              return false;
          }
          return true;
      }

  </script>
<?php } ?>
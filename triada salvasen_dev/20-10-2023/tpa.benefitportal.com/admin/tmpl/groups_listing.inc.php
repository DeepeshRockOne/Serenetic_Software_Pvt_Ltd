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
    <?php }?>
    <div class="pull-right">
        <div class="m-b-15">
            <a class="btn btn-default m-r-5" href="manage_groups.php"> Manage Groups</a>
            <a class="btn btn-action mn" href="invite_group.php">+ Group</a>
        </div>
    </div>
  </div>
  <div class="table-responsive">
      <table class="<?=$table_class?> ">
          <thead>
              <tr class="data-head">
                  <th><a href="javascript:void(0);" data-column="c.created_at" data-direction="<?php echo $SortBy == 'c.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date ID</a></th><th><a href="javascript:void(0);" data-column="c.business_name" data-direction="<?php echo $SortBy == 'c.business_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Group Name</a></th>
                  <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
                  <th><a href="javascript:void(0);" data-column="s.fname" data-direction="<?php echo $SortBy == 's.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enrolling Agent</a></th>
                  <th class="text-center"><a href="javascript:void(0);" data-column="total_products" data-direction="<?php echo $SortBy == 'total_products' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Products</a></th>
                  <th class="text-center"><a href="javascript:void(0);" data-column="total_members" data-direction="<?php echo $SortBy == 'total_members' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Members</a></th>
                  <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
                  <th width="130px" >Actions</th>
              </tr>
          </thead>
          <tbody>
              <?php if ($total_rows > 0) { ?>
                  <?php foreach ($fetch_rows as $rows) { ?>
                      <tr>
                          <td>
                              <a href="groups_details.php?id=<?=$rows['id']?>" target="_blank"  class="text-action">
                              <strong class="fw500"><?php echo $rows['rep_id']; ?></strong></a></br>
                              <?=date('m/d/Y', strtotime($rows['created_at'])); ?>
                          </td>
                          <td><?= $rows['business_name'] ?></td>
                          <td>
                            <a href="groups_details.php?id=<?=$rows['id']?>" target="_blank"  class="text-action fw500"><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></a>
                              <br />
                              <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['cell_phone']) ?><br/>
                              <?php echo $rows['email']; ?><br />
                          </td>
                          <td>
                              <a href="agent_detail_v1.php?id=<?=md5($rows['sponsor_id'])?>" target="_blank"  class="text-action">
                              <strong class="fw500"><?php echo $rows['sponsor_rep_id']; ?></strong></a>
                              <br />
                              <?php echo stripslashes($rows['s_fname'] . ' ' . $rows['s_lname']); ?>
                          </td>

                          <td class="text-center"><a href="javascript:void(0);" class="fw500 text-action group_products" data-id="<?=$rows['id'];?>"><?=$rows['total_products']?></a></td>

                          <td class="text-center"><a href="member_listing.php?viewMember=allMember&enr_agent=<?=$rows['groupDispId']?>" target="_blank" data-agent_id="<?=$rows['groupDispId']?>" class="fw500 text-action" ><?=$rows['total_members']?></a></td>

                          <td class="w-200">
                              <?php if (in_array($rows['status'], array('Invited', 'Pending Approval', 'Pending Contract', 'Pending Documentation', 'Group Abandon'))) { ?>
                                  <?php if($rows['status'] != 'Invited'){ ?>
                                      <a href="javascript:void(0)" class="group_status" id = "group_status_<?=$rows['id']?>" data-status="<?=$rows["status"] == 'Active'?'Contracted':$rows["status"]?>" data-toggle="popover" data-user_id ="<?=$rows["id"]?>"  data-trigger="hover" data-content=""> <?=$rows["status"] == 'Active'?'Contracted': $rows["status"]?></a>
                                  <?php } else if(($rows['status'] == 'Invited') && ($rows['invite_time_diff'] >168)){ ?>
                                      <a href= "reinvite_group.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="btn btn-action-o w-130 resend_popup" data-toggle="tooltip" title="Re-invite"  id = "group_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; <?=$rows["invite_time_diff"] > 168 ? 'Re-invite':'Re-invite'?></a>
                                  <?php } else { ?>
                                      <a href= "reinvite_group.php?id=<?php echo $rows['id']; ?><?php if ($rows['invite_time_diff'] > 168) {echo '&status=expired';}?>"  class="btn btn-action-o w-130 resend_popup" data-toggle="tooltip" title="Re-invite"  id = "group_status_<?=$rows['id']?>"  data-id='reinvite_<?=$rows['id']?>' data-content=""> <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; <?=$rows["status"] == 'Active'?'Contracted': 'Re-invite'?></a>
                                  <?php } ?>
                              <?php }else if(in_array($rows['status'], array('Contracted','Suspended','Terminated','Active'))){?>
                                  <div class="theme-form pr">
                                      <select name="member_status" class="form-control member_status has-value" id="member_status_<?=$rows['id'];?>" data-old_member_status="<?=$rows['status'] ?>">
                                          <option value="Active" <?php if ($rows['status'] == 'Active') { echo "selected='selected'"; } ?>>Contracted</option>
                                          <option value="Suspended" <?php if ($rows['status'] == 'Suspended') { echo "selected='selected'"; } ?>>Suspended</option>
                                          <option value="Terminated" <?php if ($rows['status'] == 'Terminated') { echo "selected='selected'"; } ?>>Terminated</option>
                                      </select>
                                      <label>Status</label>
                                  </div>
                              <?php } else {
                                  echo $rows['status'];
                              }?>                              
                          </td>
                          <td class="icons">
                                  <a href="groups_details.php?id=<?=$rows['id']?>" target="_blank" data-toggle="tooltip" data-trigger="hover" title="View Profile"><i
                                  class="fa fa-eye"></i></a>
                                  <?php if (!in_array($rows['status'], array('Invited')) && $rows["stored_password"] != "") { ?>
                                      <a data-toggle="tooltip" data-trigger="hover" href="switch_login.php?id=<?php echo $rows['id']; ?>" target="blank" title="Access Group Site"><i class="fa fa-lock"></i></a>
                                  <?php }?>

                                  <?php if($rows['status']=="Invited" || $rows['status']=="Pending Documentation") { ?>
                                  <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" title="Delete" onclick="delete_group('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
                              <?php } ?>
                          </td>
                      </tr>
                  <?php }?>
              <?php } else {?>
                  <tr>
                      <td colspan="9" align="center">No record(s) found</td>
                  </tr>
              <?php }?>
          </tbody>
          <?php if ($total_rows > 0) { ?>
              <tfoot>
              <tr>
                      <td colspan="9">
                          <?php echo $paginate->links_html; ?>
                      </td>
                  </tr>
              </tfoot>
          <?php } ?>
      </table>
  </div>
<?php } else { ?>
  <div class="panel panel-default panel-block panel-title-block">
      <form id="frm_search" action="groups_listing.php" method="GET" class="theme-form" autocomplete="off">
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
                          <div class="col-md-6 col-sm-12">
                              <div class="form-group">
                                      <select class="se_multiple_select listing_search" name="rep_id[]" id="rep_id" multiple="multiple">
                                          <?php if(!empty($tree_group_res)){ ?>
                                              <?php foreach($tree_group_res as $value){ ?>
                                                  <option value="<?=$value['rep_id']?>"><?=$value['rep_id'].' - '.$value['business_name']?></option>
                                              <?php } ?>
                                          <?php } ?>
                                      </select>
                                      <label>Group ID(s)</label>
                              </div>
                          </div>
                          <div class="col-md-6 col-sm-12">
                            <div class="row" id="show_date">
                              <div id="date_range" class="col-md-12 col-sm-12">
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
                              <div class="select_date_div col-md-9 col-sm-12" style="display:none">
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
                              <div class="form-group">
                                  <input type="text" class="form-control listing_search" name="group_name" value="<?php echo $group_name ?>">
                                  <label>Group Name</label>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="form-group">
                                  <input type="text" class="form-control listing_search" name="contact_name" id="contact_name" value="">
                                  <label>Contact Name</label>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="form-group">
                                  <input type="text" class="form-control listing_search" name="phone" maxlength='10' onkeypress="return isNumberKey(event)" value="<?php echo $phone ?>">
                                  <label>Phone</label>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="form-group">
                                  <input type="text" class="form-control listing_search" name="email" value="<?php echo $email ?>">
                                  <label>Email</label>
                              </div>
                          </div>
                          <?php echo getAgencySelect('tree_agent_id'); /*<div class="col-sm-6">
                              <div class="form-group">
                                      <select class="se_multiple_select listing_search" name="tree_agent_id[]" id="tree_agent_id" multiple="multiple">
                                          <?php if(!empty($tree_agent_res)){ ?>
                                              <?php foreach($tree_agent_res as $value){ ?>
                                                  <option value="<?=$value['id']?>"><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                              <?php } ?>
                                          <?php } ?>
                                      </select>
                                      <label>Tree ID(s)</label>
                              </div>
                          </div>*/ ?> 

                          <div class="col-sm-6">
                              <div class="form-group">
                                  <input type="text" class="form-control listing_search" name="agent_name" value="<?php echo $agent_name ?>">
                                  <label>Agent Name</label>
                              </div>
                          </div>
                          
                          <div class="clearfix"></div>
                          <div class="col-sm-6">
                                  <div class="form-group">
                                      <select name="combination_product[]" id="agent_select_product" class=" combination_product_select  listing_search se_multiple_select"  multiple="multiple">
                                          <?php if(isset($excludePrdList) && !empty($excludePrdList)){ ?>
                                              <?php foreach ($excludePrdList as $key => $row) { ?>
                                                  <optgroup label='<?=$key?>'>
                                                      <?php if(!empty($row)){ ?>
                                                          <?php foreach ($row as $key1 => $row1) { ?>
                                                              <option value="<?= $row1['id'] ?>" <?=!empty($combination_product) && in_array($row1['id'],$combination_product)?'selected="selected"':''?> data-id="combination_product_check_<?= $row1['id'] ?>"><strong><?= $row1['name'] .' ('.$row1['product_code'].')' ?></strong></option>
                                                          <?php } ?>
                                                      <?php } ?>
                                                  </optgroup>
                                              <?php } ?>
                                          <?php } ?>
                                      </select>
                                      <label>Assigned Product(s)</label>
                              </div>
                          </div>
                          
                          <div class="col-sm-6">
                              <div class="form-group">
                                      <select class="se_multiple_select listing_search" name="member_id[]" id="member_id" multiple="multiple">
                                          <?php if(!empty($tree_member_res)){ ?>
                                              <?php foreach($tree_member_res as $value){ ?>
                                                  <option value="<?=$value['rep_id']?>"><?=$value['rep_id'].' - '.$value['fname'].' '.$value['lname']?></option>
                                              <?php } ?>
                                          <?php } ?>
                                      </select>
                                      <label>Member ID(s)</label>
                              </div>
                          </div>
                          <div class="clearfix"></div>
                          <div class="col-sm-6">
                              <div class="form-group">
                                  <select name="group_status[]" id="group_status" class="se_multiple_select listing_search" multiple="multiple"
                                       >
                                      <option value="Invited" >Invited</option>
                                      <option value="Pending Documentation" >Pending Documentation</option>
                                      <option value="Active" >Contracted</option>
                                      <option value="Suspended" >Suspended</option>
                                      <option value="Terminated" >Terminated</option>
                                  </select>
                                  <label>Status</label>
                              </div>
                          </div>
                         
                          
                      </div>
                      <div class="panel-footer">
                          <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
                          </button>
                          <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'groups_listing.php'"><i class="fa fa-search-plus"></i>  View All
                          </button>
                          <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
                          <input type="hidden" name="export_val" id="export_val" value="">
                          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
                          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
                          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
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
          <div id="ajax_loader" class="ajex_loader" style="display: none;">
              <div class="loader"></div>
          </div>
          <div id="ajax_data" class="panel-body"></div>
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
      dropdown_pagination('ajax_data');

          $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
          });

          ajax_submit();

          $("#rep_id, #tree_agent_id, #member_id, #group_status").multipleSelect({
              selectAll: false,
              filter:true
          });

          $("#agent_select_product").multipleSelect({
          });
      });

      $(document).off('change', '#join_range');
      $(document).on('change', '#join_range', function(e) {
          e.preventDefault();
          $('.date_picker').val('');
          if ($(this).val() == '') {
              $('.select_date_div').hide();
              $('#date_range').removeClass('col-md-3').addClass('col-md-12');
          } else {
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

        $(document).off('click', '#export');
        $(document).on('click', '#export', function (e) {
            e.stopPropagation();

            confirm_export_data(function() {
                $("#export_val").val(1);
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
                        $("#export_val").val('');
                        if(res.status == "success") {
                            confirm_view_export_request();
                        } else {
                            setNotifyError(res.message);
                        }
                    }
                });
            });
        });
      function get_status($id) {
        $('#group_status' + $id).html('Re-Invite');
      }
      $(document).off('change', '.member_status');
      $(document).on("change", ".member_status", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('member_status_', '');
        var new_status = $(this).val();
        var old_status = $(this).attr('data-old_member_status');

        $colorboxHeight = "240px";
        if(new_status === 'Active'){
            $colorboxHeight = "240px";
        }else if(new_status === 'Suspended'){
            $colorboxHeight = "330px";
        }else if(new_status === 'Terminated'){
            $colorboxHeight = "480px";
        }

        $href = "change_group_status.php?group_id="+id+"&new_status="+new_status+"&old_status="+old_status+"&from=listing_page";
        $.colorbox({
            iframe:true,
            width: "500px",
            height: $colorboxHeight,
            closeButton: false,
            href: $href,
            overlayClose: false,
            escKey: false,
            onClosed : function(){
            }
        });
        return false;
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
              text: 'Delete Record: Are you sure?',
              showCancelButton: true,
              confirmButtonText: "Confirm",
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
          $('#is_ajaxed').val('1');
          var params = $('#frm_search').serialize();
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

      function refreshGroupStatus(memberId,Status){
            $('#member_status_'+memberId).val(Status);
            $('#member_status_'+memberId).attr('data-old_member_status',Status);
            $('#member_status_'+memberId).selectpicker('render');
            $.colorbox.close();
            return false;
        }

  </script>
<?php } ?>
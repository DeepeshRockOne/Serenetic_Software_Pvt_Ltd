<script type="text/javascript">
  $('.add_products').colorbox({
        iframe: true,
        width: '500px',
        height: '350px'
      });
</script>
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
            <a class="btn btn-action mn" href="invite_group.php"><i class="fa fa-plus"></i> Group</a>
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
                              <a href="groups_details.php?id=<?=$rows['id']?>" class="text-action" target="_blank">
                              <strong class="fw500"><?php echo $rows['rep_id']; ?></strong></a></br>
                              <?=date('m/d/Y', strtotime($rows['created_at'])); ?>
                          </td>
                          <td><?= $rows['business_name'] ?></td>
                          <td>
                            <a href="javascript:void(0);" class="text-action fw500"><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></a>
                              <br />
                              <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['cell_phone']) ?><br/>
                              <?php echo $rows['email']; ?><br />
                          </td>
                          <td>
                              <a href="javascript:void(0);" class="text-action">
                              <strong class="fw500"><?php echo $rows['sponsor_rep_id']; ?></strong></a>
                              <br />
                              <?php echo stripslashes($rows['s_fname'] . ' ' . $rows['s_lname']); ?>
                          </td>

                          <td class="text-center"><a href="javascript:void(0);" class="fw500 text-action group_products" data-id="<?=$rows['id'];?>"><?=$rows['total_products']?></a></td>

                          <td class="text-center"><a href="member_listing.php?viewMember=allMember&sponsor_id=<?=$rows['id'];?>" class="fw500 text-action group_members" data-id="<?=$rows['id'];?>"><?=$rows['total_members']?></a></td>

                          <td class="w-200">
                            <?=$rows['status'] == "Active" ? "Contracted" : $rows['status']?>
                          </td>

                          <td class="icons">
                            <?php 
                                if($_SESSION['agents']['id'] == $rows['sponsor_id'] || (!empty($rows['sponsor_coded_level']) && $rows['sponsor_coded_level'] == 'LOA' && $_SESSION['agents']['id'] == $rows['s_sponsor_id'])) { ?>
                                  <?php if (!in_array($rows['status'], array('Invited')) && $rows["stored_password"] != "") { ?>
                                      <a data-toggle="tooltip" data-trigger="hover" href="switch_login.php?id=<?php echo $rows['id']; ?>" title="Access Group Site" target="_blank"><i class="fa fa-lock"></i></a>
                                      
                                  <?php }?>
                                  <?php if(in_array($rows['status'], array('Contracted','Suspended','Terminated','Active'))){ ?>
                                    <a data-toggle="tooltip" data-trigger="hover" href="group_add_products.php?group_id=<?php echo $rows['id']; ?>" title="Add Products" class="add_products"><i class="fa fa-plus"></i></a>
                                  <?php } ?>
                            <?php }else '-'; ?>
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
  <div class="container  m-t-30">
    <div class="panel panel-default panel-block panel-title-block">
        <form id="frm_search" action="groups_listing.php" method="GET" class="theme-form">
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
                                <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="rep_id[]" id="rep_id" multiple="multiple">
                                            <?php if(!empty($tree_group_res)){ ?>
                                                <?php foreach($tree_group_res as $value){ ?>
                                                    <option value="<?=$value['rep_id']?>"><?=$value['rep_id'].' - '.$value['business_name']?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label>ID Number(s)</label>
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
                            <?php echo getAgencySelect('tree_agent_id',$_SESSION['agents']['id'],'Agent'); /*<div class="col-sm-6">
                                <div class="form-group ">
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
                                <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="agent_name[]" id="agent_name" multiple="multiple">
                                            <?php if(!empty($downline_agent_res)){ ?>
                                                <?php foreach($downline_agent_res as $value){ ?>
                                                    <option value="<?=$value['agentId']?>"><?=$value['agentDispId'].' - '.$value['agentName']?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label>Enrolling Agent</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                    <div class="form-group ">
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
                                        <label>Assigned Products</label>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group ">
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
                                <div class="form-group ">
                                    <select name="group_status" id="group_status" class="form-control listing_search"
                                         >
                                        <option value=""> </option>
                                        <option value="Invited" >Invited</option>
                                        <option value="Pending Documentation" >Pending Documentation</option>
                                        <option value="Pending Approval" >Pending Approval</option>
                                        <option value="Pending Contract" >Pending Contract</option>
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
                            <button type="button" name="" id="" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
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
    dropdown_pagination('ajax_data')
          $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
          });

          ajax_submit();

          $("#rep_id, #tree_agent_id, #member_id, #agent_name").multipleSelect({
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

      $(document).off('change', '.member_status');
      $(document).on("change", ".member_status", function(e) {
          e.stopPropagation();
          var id = $(this).attr('id').replace('member_status_', '');
          var member_status = $(this).val();
          swal({
              text: "Are you sure you want to change this status ?",
              showCancelButton: true,
              confirmButtonText: "Yes",
          }).then(function() {
              if (member_status == 'Terminated' || member_status == 'Suspended') {
                  $.colorbox({
                      iframe: true,
                      href: "<?=$AGENT_HOST?>/reason_change_group_status.php?id=" + id + "&status=" + member_status,
                      width: '600px',
                      height: '260px',
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
             
              text: 'Are you sure you want to delete Group ?',
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

  </script>
<?php } ?>
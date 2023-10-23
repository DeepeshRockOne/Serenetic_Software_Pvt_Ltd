<?php if ($is_ajaxed) { ?>
  <input type="hidden" name="new_added_date" value="<?=$added_date?>" id="new_addded_date">
  <input type="hidden" name="new_join_range" value="<?=$join_range?>" id="new_join_range">
  <div class="clearfix">
    <?php if ($total_rows > 0) { ?>
      <div class="clearfix m-b-15 tbl_filter">
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
          <a class="btn btn-action mn" target="_blank" href="member_interactions.php">Interactions</a>
        </div>
      </div>
      <?php } ?>
  </div>
  <div class="table-responsive">
    <table class="<?= $table_class ?>">
      <thead>
        <tr class="data-head">
          <th><a href="javascript:void(0);" data-column="c.joined_date" data-direction="<?php echo $SortBy == 'c.joined_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
          <th><a href="javascript:void(0);" data-column="c.fname" data-direction="<?php echo $SortBy == 'c.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a></th>
          <th width="10%"><a href="javascript:void(0);" data-column="scs.company" data-direction="<?php echo $SortBy == 'scs.company' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Company</a></th>
          <th><a href="javascript:void(0);" data-column="c.sponsor_id" data-direction="<?php echo $SortBy == 'c.sponsor_id' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enrolling Agent ID/Name</a></th>
          <th width="25%" class="text-center"><a href="javascript:void(0);" data-column="total_products" data-direction="<?php echo $SortBy == 'total_products' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Products</a></th>
          <th><a href="javascript:void(0);" data-column="c.status" data-direction="<?php echo $SortBy == 'c.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
          <th width="90px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td><a href="members_details.php?id=<?php echo $rows['id']; ?>" target="_blank" class="fw500 text-action"><?php echo $rows['rep_id']; ?></a><br><?php echo date('m/d/Y', strtotime($rows['joined_date'])); ?></td>
              <td>
                <strong><?php echo $rows['fname'] . " " . $rows['lname']; ?></strong><br>
                <?php echo isset($rows['cell_phone']) ? format_telephone($rows['cell_phone']) : ''; ?><br>
                <?php echo $rows['email']; ?>
              </td>
              <td><?=$rows['company']?></td>
              <td><a href="javascript:void(0)" data-href="member_tree_popup.php?agent_id=<?php echo $rows['sponsor_id']; ?>&member_id=<?=$rows['id']?>&type=Member" class="fw500 text-action member_tree_popup"><?php echo $rows['sponsor_rep_id']; ?></a><br><?php echo $rows['sponsor_name']; ?></td>
              <td class="text-center"><a href="javascript:void(0);" class="member_product_popover fw500 text-action" data-id="<?php echo $rows['id']; ?>"><?php echo $rows['total_products']; ?></a>
                <div id="popover_content_wrapper_<?php echo $rows['id']; ?>" style="display: none">
                </div>

              </td>
              <td><?=get_member_display_status($rows['status']);?></td>
              <td class="icons">
                <a href="members_details.php?id=<?php echo $rows['id']; ?>" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="View"><i class="fa fa-eye"></i></a>
                <?php if($rows['status'] != 'Hold'){ ?>
                  <a href="member_enrollment.php?customer_id=<?php echo $rows['id']; ?>" target="_blank" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Add Products"><i class="fa fa-plus"></i></a>
                <?php } ?>  
              </td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="10" align="center">No record(s) found</td>
          </tr>
        <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
        <tfoot>
          <tr>
            <td colspan="10">
              <?php echo $paginate->links_html; ?>
            </td>
          </tr>
        </tfoot>
      <?php } ?>
    </table>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      contentCalled = false;
      $('.member_product_popover').popover({
        html: true,
        container: 'body',
        trigger: 'click',
        template: '<div class="popover full_width_popover"><div class="arrow"></div><div class="popover-content"></div></div>',
        placement: 'auto top',
        content: function() {
          var id_val = $(this).attr('data-id');
          getPopoverData(id_val)
          return $('#popover_content_wrapper_' + id_val).html();
        }
      });

      function getPopoverData(id_val) {
        if (!contentCalled) {
          contentCalled = true;
          return " ";
        } else {
          $.ajax({
            url: 'get_member_products_popover.php',
            data: {
              id: id_val
            },
            method: 'GET',
            async: false,
            success: function(res) {
              contentCalled = false;              
              $('#popover_content_wrapper_' + id_val).html(res);
            }
          });
        }
      }

      $('body').on('click', function (e) {
          $('.member_product_popover').each(function () {
              // hide any open popovers when the anywhere else in the body is clicked
              if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                  $(this).popover('hide');
              }
          });
      });


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
          $.ajax({
            url: 'change_member_status.php',
            data: {
              id: id,
              status: member_status
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
              if (res.status == "success") {
                setNotifySuccess(res.msg);
              } else {
                setNotifyError(res.msg);
                ajax_submit();
              }
            }
          });
        }, function(dismiss) {
          ajax_submit();
        })
      });

    });
    $(document).off('click',".member_tree_popup");
    $(document).on('click',".member_tree_popup",function(e){
        $href = $(this).attr('data-href');
        $.colorbox({
            iframe:true,
            href:$href,
            width: '900px',
            height: '650px',
            onClosed :function(e){
              ajax_submit();
            }
        });
    });
  </script>
<?php } else { ?>
<div class="container m-t-30">
  <div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="member_listing.php" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="viewMember" id="viewMember" value="<?=checkIsset($viewMember)?>">
    <input type="hidden" name="sponsor_id" id="sponsor_id" value="<?=checkIsset($sponsor_id)?>">
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
                  <input type="text" name="rep_id" id="rep_id" class="form-control listing_search">
                  <label>ID Number(s)</label>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="row">
                  <div id="date_range" class="col-md-12 col-sm-12">
                    <div class="form-group">
                      <select class="form-control listing_search" id="join_range" name="join_range">
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
                        <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker listing_search" />
                      </div>
                      <div id="range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="todate" id="todate" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="name" id="name" class="form-control listing_search">
                  <label>Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="cell_phone" id="cell_phone" maxlength='10' onkeypress="return isNumberKey(event)"  class="form-control listing_search">
                  <label>Phone</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="email" id="email" class="form-control listing_search">
                  <label>Email</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                    <select name="products[]" id="products" class=" products  listing_search se_multiple_select" multiple="multiple">
                      <?php foreach ($filter_prd_res as $key=>$company) { ?>
                      <optgroup label='<?= $key ?>'>
                         <?php foreach ($company as $pkey =>$row) { ?>
                         <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                         <?php } ?>
                      </optgroup>
                      <?php } ?>
                    </select>
                    <label>Products</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <select class="listing_search se_multiple_select" multiple="multiple" name="product_status[]" id="product_status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Pending">Pending</option>
                  </select>
                  <label>Product Status</label>
                </div>
              </div>
              <?php echo getAgencySelect('tree_agent_id',$_SESSION['agents']['id'],'Agent'); /*<div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="tree_agent_id[]" id="tree_agent_id" multiple="multiple">
                    <?php if (!empty($tree_agent_res)) { ?>
                      <?php foreach ($tree_agent_res as $value) { ?>
                        <option value="<?= $value['id'] ?>"><?= $value['rep_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'] ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Agent Tree</label>
                </div>
              </div>*/ ?>
              <div class="col-md-6 col-sm-12">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="status[]" id="status" multiple="multiple">
                    <option value="Active">Active</option>
                    <?php //<option value="Pending">Pending</option> ?>
                    <option value="Hold">Hold</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                  <label>Status</label>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="row">
                  <div id="select_effective_date_div" class="col-md-12 col-sm-12">
                    <div class="form-group ">
                      <select class="form-control listing_search" id="effective_join_range" name="effective_join_range">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Effective Date</label>
                    </div>
                  </div>
                  <div class="show_effective_date col-md-9 col-sm-12" style="display:none">
                    <div class="form-group ">
                      <div id="effective_all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="effective_date" id="effective_date" value="" class="form-control date_picker listing_search" />
                      </div>
                      <div id="effective_range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="effective_from_date" id="effective_from_date" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="effective_to_date" id="effective_to_date" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="enroll_agent[]" id="enroll_agent" data-live-search="true" multiple="multiple">
                    <?php if (!empty($enroll_agent_res)) { ?>
                      <?php foreach ($enroll_agent_res as $value) { ?>
                        <option value="<?= $value['id'] ?>"><?= $value['rep_id'] . ' - ' . $value['name'] ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Enrolling Agent</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="state[]" id="state" multiple="multiple" data-live-search="true">
                    <?php if (!empty($states)) { ?>
                      <?php foreach ($states as $state) { ?>
                        <option value="<?= $state['name'] ?>"><?= $state['name'] ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <label>State</label>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group ">
                  <input type="text" name="dependent_id" id="dependent_id" class="form-control listing_search">
                  <label>Dependent</label>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="row">
                  <div id="select_billing_date_div" class="col-md-12 col-sm-12">
                    <div class="form-group ">
                      <select class="form-control listing_search" id="billing_join_range" name="billing_join_range">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label class="label-wrap">Next Billing Date</label>
                    </div>
                  </div>
                  <div class="show_billing_date col-md-9 col-sm-12" style="display:none">
                    <div class="form-group ">
                      <div id="billing_all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="billing_date" id="billing_date" value="" class="form-control date_picker listing_search" />
                      </div>
                      <div id="billing_range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="billing_from_date" id="billing_from_date" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="billing_to_date" id="billing_to_date" value="" class="form-control date_picker listing_search" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer clearfix">
              <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search
              </button>
              <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onclick="window.location.href='member_listing.php?viewMember=allMember'" ><i class="fa fa-search-plus"></i> View All
              </button>
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
              <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
              <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
              <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
            </div>
          </div>
        </div>
      </div>
      <div class="search-handle">
        <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
        <h4 class="m-t-0 m-b-15">Member Summary</h4>
          <div class="table-responsive">
              <table class="<?=$table_class?> table-action text-center">
                  <thead>
                      <tr>
                          <th>Active #</th>
                          <?php //<th>Pending #</th> ?> 
                          <th>Inactive</th>
                          <th>Hold</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <td><?=$member_summery_arr['Active']?></td>
                          <?php /*<td><?=$member_summery_arr['Pending']?></td> */ ?> 
                          <td><?=$member_summery_arr['Inactive']?></td>
                          <td><?=$member_summery_arr['Hold']?></td>
                      </tr>
                  </tbody>
              </table>
          </div>
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
    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
      e.preventDefault();
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

    $(document).ready(function() {
      dropdown_pagination('ajax_data')

      $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });

      ajax_submit();

      $("#agent_level, #tree_agent_id, #state, #enroll_agent").multipleSelect({
        selectAll: false,
        filter: true
      });

      $(document).on('click','.personal_production_report',function(e){
          $href=$(this).attr('data-href');
          $.colorbox({
              iframe: true,
              href:$href,
              width: '900px', 
              height: '800px'
          });
      });
    });



    $(document).off('change', '#effective_join_range');
    $(document).on('change', '#effective_join_range', function(e) {
      e.preventDefault();
      if ($(this).val() == '') {
        $('.show_effective_date').hide();
        $('#select_effective_date_div').removeClass('col-md-3').addClass('col-md-12');
      } else {
        $('#select_effective_date_div').removeClass('col-md-12').addClass('col-md-3');
        $('.show_effective_date').show();
        if ($(this).val() == 'Range') {
          $('#effective_range_join').show();
          $('#effective_all_join').hide();
        } else {
          $('#effective_range_join').hide();
          $('#effective_all_join').show();
        }
      }
    });

    $(document).off('change', '#billing_join_range');
    $(document).on('change', '#billing_join_range', function(e) {
      e.preventDefault();
      if ($(this).val() == '') {
        $('.show_billing_date').hide();
        $('#select_billing_date_div').removeClass('col-md-3').addClass('col-md-12');
      } else {
        $('#select_billing_date_div').removeClass('col-md-12').addClass('col-md-3');
        $('.show_billing_date').show();
        if ($(this).val() == 'Range') {
          $('#billing_range_join').show();
          $('#billing_all_join').hide();
        } else {
          $('#billing_range_join').hide();
          $('#billing_all_join').show();
        }
      }
    });
    $(document).off("submit", "#frm_search");
    $(document).on("submit", "#frm_search", function(e) {
      $('#viewMember').val("allMember");
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
          common_select();
        }
      });
    });

    $(document).ready(function() {
      $("#status, #product_status").multipleSelect({
        selectAll: false
      });
      $("#products").multipleSelect({});
    });

    function ajax_submit() {
      disableButton($("#search"));
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
          todaysMember();
          $("[data-toggle=popover]").each(function(i, obj) {
            $(this).popover({
              html: true,
              placement: 'auto bottom',
              content: function() {
                var id = $(this).attr('data-user_id')
                return $('#popover_content_' + id).html();
              }
            });
          });
          enableButton($("#search"));
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

    todaysMember = function(){
      var odrDisplay = $("#viewMember").val();
      newDate = $("#new_addded_date").val();
      new_join_range = $("#new_join_range").val();
      var join = 'Exactly';
      var today = '';
      if(newDate !=='' && newDate !== undefined){
        today = newDate;
        join = new_join_range;
      }else{
        today = '<?=$added_date?>';
      }      
      if(odrDisplay == "todayMember"){
        $('#join_range').val(join).trigger('change');
        $("#added_date").val(today);
      }
    }
  </script>
<?php } ?>
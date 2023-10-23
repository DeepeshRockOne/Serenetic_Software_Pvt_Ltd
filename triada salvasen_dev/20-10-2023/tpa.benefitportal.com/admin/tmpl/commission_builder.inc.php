<?php if ($is_ajaxed) { ?>
  <div class="clearfix tbl_filter">
    <div id="top_paginate_cont" class="pull-left">
            <div class="form-inline" id="DataTables_Table_0_length">
              <div class="form-group">
                <label for="user_type">Records Per Page </label>
              </div>
              <div class="form-group">
                <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);
                    ajax_submit();">
                  <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                  <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                  <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                  <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                </select>
              </div>
            </div>
          </div>
        <div class="pull-right m-b-15">
          <a href="add_commission_rule.php" class="btn btn-action">+ Commission </a>
        </div>
  </div>
  <div class="table-responsive">
    <table class="<?= $table_class ?>">
      <thead>
        <tr class="data-head">
          <th>ID/Added Date</th>
          <th>Product Name/ID</th>
          <th class="text-center">Commissions</th>
          <th class="text-center">Agents #</th>
          <th class="text-center">Variation</th>
          <th width="150px">Status</th>
          <th width="100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($fetch_rows)) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td>
                <a href="add_commission_rule.php?commission=<?= $rows['id'] ?>" class="text-red fw600">
                  <?= $rows['rule_code'] ?>
                </a><br />
                <?= date($DATE_FORMAT,strtotime($rows['created_at'])) ?>
              </td>
              <td>
                <a href="javascript:void(0);" data-id="<?= $rows['prod_id'] ?>" class="product_add_link text-red fw600">
                  <?php echo $rows['name']; ?>
                </a><br />
                <?php echo $rows['product_code']; ?>
              </td>
              <td class="text-center">
                <a href="commission_per_level.php?commission=<?= $rows['id'] ?>" data-toggle="tooltip" data-trigger="hover" title="View" class="commission_per_level_popup">
                  <i class="fa fa-eye fs18"></i>
                </a>
              </td>
              <td class="text-center">
        		    <a href="commission_agents_assigned.php?commission=<?= $rows['id'] ?>&total_agents=<?= $rows['agent_total'] ?>" class="text-red fw600 commission_agents_assigned">
                  <?= $rows['agent_total'] ?>
                </a>
              </td>
              <td class="text-center">
                  <a href="variation_detail.php?commission=<?= $rows['id'] ?>" class="variation_popup text-red fw600"><?= $rows['variation_total'] ?></a>
                  <a href="add_commission_rule.php?parentCommission=<?= $rows['id'] ?>" class="text-red fw600">&nbsp;&nbsp; +</a></td>
              <td>
                <div class="theme-form pr">
                  <select name="member_status" class="form-control member_status has-value" id="<?= $rows['id']; ?>" data-oldVal="<?= $rows['status'] ?>">
                    <option value="Active" <?php echo $rows['status'] == 'Active'?"selected":'';?>>Active</option>
                    <option value="Inactive" <?php echo $rows['status'] == 'Inactive'?"selected":'';?>>Inactive</option>
                  </select>
                  <label>Select</label>
                </div>
              </td>
              <td class="icons text-right">
                  <a href="add_commission_rule.php?commission=<?= $rows['id']?>&is_clone=Y" data-toggle="tooltip" data-trigger="hover" title="Duplicate"><i class="fa fa-clone" ></i></a>
                  <a href="add_commission_rule.php?commission=<?= $rows['id'] ?>" data-toggle="tooltip" data-trigger="hover" title="Edit"><i class="fa fa-edit" ></i></a>
                  <?php if($rows['status'] == 'Inactive'){ ?>
                  <a href="javascript:void(0)" data-id="<?= $rows['id'] ?>" class="delete_rule" data-toggle="tooltip" data-trigger="hover" title="Delete"><i class="fa fa-trash"></i></a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="7">No record(s) found</td>
          </tr>
        <?php } ?>
      </tbody>
      <?php if (!empty($fetch_rows)) { ?>
        <tfoot>
          <tr>
            <td colspan="7"><?php echo $paginate->links_html; ?></td>
          </tr>
        </tfoot>
      <?php } ?>
    </table>
  </div>
<?php } else { ?>
  <?php include_once('notify.inc.php'); ?>
  <div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="commission_builder.php" method="GET" autocomplete="off">
      <div class="panel-left">
        <div class="panel-left-nav">
          <ul>
              <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
            </ul>
        </div>
      </div>
      <div class="panel-right">
        <div class="panel-heading"> 
          <div class="panel-search-title"><span class="clr-light-blk">SEARCH</span></div>
        </div>

        <div class="panel-wrapper collapse in">
          <div class="panel-body theme-form">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                    <select name="commissionIds[]" class="se_multiple_select listing_search"  multiple="multiple">
                        <?php if(!empty($resCommRules)){ ?>
                            <?php    foreach ($resCommRules as $comm) { ?>
                                <option value="<?= $comm['id'] ?>" <?=!empty($commissionIds) && in_array($comm['id'],$commissionIds)?'selected="selected"':''?>><?=$comm['rule_code']?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <label>ID Number(s)</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row" id="show_date">
                  <div id="date_range" class="col-md-12">
                    <div class="form-group">
                      <select class="form-control" id="searchDate" name="searchDate">
                        <option value=""></option>
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
              <!-- adding date feild end -->
              <div class="col-sm-6">
                <div class="form-group">
                    <select name="searchProduct[]" id="searchProduct" class="se_multiple_select listing_search"  multiple="multiple">
                        <?php if(!empty($productSearchList)){ ?>
                            <?php foreach ($productSearchList as $key=>$company) { ?>
                                  <optgroup label='<?= $key ?>'>
                                    <?php    foreach ($company as $pkey =>$row) { ?>
                                        <option value="<?= $row['id'] ?>" <?=!empty($product_id) && in_array($row['id'],$product_id)?'selected="selected"':''?>><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                    <?php } ?>
                                  </optgroup>
                                <?php } ?>
                        <?php } ?>
                    </select>
                    <label>Products</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="form-control listing_search" id="searchStatus" name="searchStatus">
                    <option value=""></option>
                  	<option>Active</option>
                      <option>Inactive</option>
                  </select>
                  <label>Status</label>
                </div>
              </div>
            </div>
            
            <div class="panel-footer clearfix">
              <button type="submit" class="btn btn-info" name="search" id="search"> <i class="fa fa-search"></i> Search </button>
              <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'commission_builder.php'"> <i class="fa fa-search-plus"></i> View All </button>
              <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
              <input type="hidden" name="export_val" id="export_val" value=""/>
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
              <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
            </div>
          </div>
        </div>
      </div>
    </form>
    <!--  minus-panel-->   
      <div class="search-handle">
        <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
      </div>
    <!--  minus-panel- end-->
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
    dropdown_pagination('ajax_data')
      $(".date_picker").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true
      });
      
      ajax_submit();

      $('.se_multiple_select').multipleSelect({
        
      });
      
    });

    $(document).off('change', '#searchDate');
    $(document).on('change', '#searchDate', function(e) {
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
    $(document).off("submit","#frm_search");
    $(document).on("submit","#frm_search",function(e){
        e.preventDefault();
        disable_search();
    });
    $(document).off('click', '.product_add_link');
    $(document).on('click', '.product_add_link', function(e) {
        e.preventDefault();
        var product_id = $(this).attr('data-id');       
        window.location="product_builder.php?product_id=" + product_id;
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
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    });

    $(document).off('click', '.delete_rule');
    $(document).on('click', '.delete_rule', function(e) {
      e.preventDefault();

      $commission = $(this).attr('data-id');

      swal({
        text: "Delete Record: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then(function() {
        $.ajax({
          url: '<?= $ADMIN_HOST ?>/ajax_delete_commission_rule.php',
          data: {commission:$commission},
          dataType:'JSON',
          method: 'POST',
          success: function(data) {
            if (data.status == 'success') {
              window.location = 'commission_builder.php';
            } else {
              setNotifyError("Something went wrong");

            }
          }
        });
      });
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

    $(document).off('change', '.member_status');
    $(document).on('change', '.member_status', function(e) {
      e.stopPropagation();
      $commission = $(this).attr('id');
      $status = $(this).val();
      $oldStatus = $(this).attr('data-oldVal');
      swal({
        text: "Change Status: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then(function() {
        $.ajax({
          url: "<?= $ADMIN_HOST ?>/ajax_commission_status_change.php",
          data: {commission:$commission,status:$status},
          dataType:'JSON',
          type: 'POST',
          success: function(res) {
            if(res.status=='success'){
                setNotifySuccess(res.msg);
                window.location.reload();
            }
          }
        });
      }, function(dismiss) {
          $("#"+$commission).val($oldStatus);
          $("#"+$commission).selectpicker('refresh');
      });
    });

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
          $('[data-toggle="popover"]').popover({html:true});
          $('[data-toggle="tooltip"]').tooltip();
          $(".commission_per_level_popup").colorbox({iframe: true, width: '800px', height: '400px'});
          $(".commission_agents_assigned").colorbox({iframe: true, width: '800px', height: '400px'});
          common_select();
        }
      });
      
      return false;
    }

    
  </script>
<?php } ?>
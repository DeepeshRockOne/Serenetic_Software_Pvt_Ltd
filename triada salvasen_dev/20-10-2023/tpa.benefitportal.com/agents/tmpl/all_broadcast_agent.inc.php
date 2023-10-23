<style type="text/css">
.iframe .dropdown.bootstrap-select .dropdown-menu.open{max-height:75px!important; min-height: 100%!important;}
.iframe .dropdown.bootstrap-select .dropdown-menu .inner.open{max-height:75px!important; min-height: 100%!important;}
</style>
<?php if ($is_history_ajaxed) { ?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th>ID/Add Date</th>
          <th>Broadcast Name</th>
          <th>Broadcast Type</th>
          <th>User Group</th>
          <th>Status</th>
          <th class="text-center">Delivered #</th>
          <th width="100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) { ?>
            <?php 
              $link="";
              if($rows['type'] == 'email') { 
                $link = "add_email_broadcast.php?broadcaster_id=".md5($rows['id']);
              }else if($rows['type'] == 'sms') {
                $link = "add_sms_broadcast.php?broadcaster_id=".md5($rows['id']);
              } 
              $broadcastType = ($rows['type'] == "sms" ? "Text Message" : "Email");
            ?>

            <tr>
              <td><a href="javascript:void(0)" class="fw500 text-red" onclick="parent.redirect_page('<?=$link?>')"><?php echo $rows['display_id']; ?></a><br /><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?></td>
              <td><?=$rows['brodcast_name']; ?></td>
                <td><?=$broadcastType?></td>
                <td><?=$rows['user_type']?></td>
                <td><?=$rows['status']?></td>
                <td class="text-center"><?=$rows['total_users'] . '/' . $rows['total_sent']?></td>
              <td class="icons">
                <?php if($rows['status'] != "Completed"){ ?>
                 <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Edit" onclick="parent.redirect_page('<?=$link?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <?php } ?>
                <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Duplicate" onclick="parent.redirect_page('<?=$link?>&is_clone=Y')"><i class="fa fa-clone" aria-hidden="true" ></i></a>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" onclick="parent.delete_broadcaster('<?= md5($rows['id']); ?>')"><i class="fa fa-trash" aria-hidden="true"></i></a>
              </td>
            </tr>
          <?php } ?>
        <?php } else {?>
          <tr>
            <td colspan="9">No record(s) found</td>
          </tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
        <tfoot>
          <tr>
            <td colspan="9"><?php echo $paginate->links_html; ?></td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
  </div>
  <script type="text/javascript">
    $(document).keypress(function (e) {
      if (e.which == 13) {
        ajax_history_submit();
      }
    });
  </script>
<?php } else { ?>
  <?php include_once 'notify.inc.php';?>
  <div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="all_broadcast_agent.php" method="GET" class="sform">
      <div class="clearfix"></div>
      <div class="clearfix tbl_filter m-t-5">
        <div class="pull-left">
         <h4 class="m-t-0">All Broadcasts</h4>
        </div>
        <div class="pull-right">
          <div class="m-b-15">
            <div class="note_search_wrap auto_size" id="searching_div" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" id="close_search_div" class="search_close_btn text-light-gray">X</a>
                  </div>
                </div>
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <input type="text" class="form-control" name="search_broadcaster_id" id="search_broadcaster_id" autocomplete="off">
                    <label>Broadcaster ID</label>
                  </div>
                </div>
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <input type="text" class="form-control" name="search_broadcaster_name" id="search_broadcaster_name" autocomplete="off">
                    <label>Broadcaster Name</label>
                  </div>
                </div>
                <div class="phone-addon w-200">
                  <div class="form-group height_auto mn">
                    <select class="form-control" id="join_range" name="join_range">
                      <option value="" data-value="empty_value"> </option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                    </select>
                    <label>Added Date</label>
                  </div>
                </div>
                <div class="select_date_div phone-addon" style="display:none">
                  <div class="form-group height_auto mn">
                    <div id="all_join" class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" autocomplete="off" />
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
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="btn btn-info" onclick="ajax_history_submit();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="history_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
          </div>
        </div>
      </div>
      <div class="panel-wrapper collapse in" style="display: none;">
        <div class="panel-footer clearfix">
          <button type="button" class="btn btn-info" name="search" id="search" onclick="ajax_history_submit()"> <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'all_broadcast_agent.php'"> <i class="fa fa-search-plus"></i> View All </button>
          <input type="text" name="is_history_ajaxed" id="is_history_ajaxed" value="1" />
          <input type="text" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="text" name="sort_history_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="text" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_history_submit();">
                    <option value="10" <?php echo (!empty($_GET['pages']) && ($_GET['pages'] == 10)) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo ((!empty($_GET['pages']) && ($_GET['pages'] == 25)) || empty($_GET['pages'])) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo (!empty($_GET['pages']) && ($_GET['pages'] == 50)) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo (!empty($_GET['pages']) && ($_GET['pages'] == 100)) ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block" style="min-height:210px; ">
    <div class="list-group">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_histroy_data" class=""> </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
    dropdown_pagination('ajax_data')

      ajax_history_submit();

      $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });

    });

    $(document).off('click', '#ajax_histroy_data ul.pagination li a');
    $(document).on('click', '#ajax_histroy_data ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajax_histroy_data').hide();
      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_histroy_data').html(res).show();
          frame_name = 'br_history_iframe';
          parent.resizeIframe($("body").height() + 20, frame_name);
          common_select();
        }
      });
    });

    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if($(this).val() == ''){
        $('.select_date_div').hide();
        $('#date_range').removeClass('col-sm-3').addClass('col-sm-12');
      }else{
        $('#date_range').removeClass('col-sm-12').addClass('col-sm-3');
        $('.select_date_div').show();
        if ($(this).val() == 'Range') {
          $('#range_join').show();
          $('#all_join').hide();
        } else {
          $('#range_join').hide();
          $('#all_join').show();
        }
      }
      frame_name = 'br_history_iframe';
      parent.resizeIframe($("body").height() + 20, frame_name);
    });

    $(document).off("click", "#history_search_btn");
    $(document).on("click", "#history_search_btn", function(e){
      e.stopPropagation();
      $("#searching_div").show();
      $(this).hide();
      frame_name = 'br_history_iframe';
      parent.resizeIframe($("body").height() + 20, frame_name);
    });

    $(document).off("click","#close_search_div");
    $(document).on("click","#close_search_div", function(){
      $("#search_broadcaster_id").val('');
      $("#search_broadcaster_name").val('');
      $('#join_range option:eq(0)').attr('selected','selected').change();
      $("#added_date").val('');
      $("#fromdate").val('');
      $("#todate").val('');
      $("#history_search_btn").show();
      $("#searching_div").hide();
      ajax_history_submit();
    });

    function ajax_history_submit() {
      $('#ajax_loader').show();
      $('#ajax_histroy_data').hide();
      $('#is_history_ajaxed').val('1');
      var params = $('#frm_search').serialize();
      $.ajax({
        url: $('#frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_histroy_data').html(res).show();
          frame_name = 'br_history_iframe';
          parent.resizeIframe($("body").height() + 20, frame_name);
          common_select();
          fRefresh();
        }
      });
      return false;
    }
  </script>
<?php }?>
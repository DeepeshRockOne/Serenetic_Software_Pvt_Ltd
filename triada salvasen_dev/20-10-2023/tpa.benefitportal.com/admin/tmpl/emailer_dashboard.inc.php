<?php if ($is_ajaxed) { ?>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>  
      <tr>
        <th>Sent Date</th>
        <th>From Email</th>
        <th>To Email</th>
        <th class="text-center">Message</th>
        <th class="text-center">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
        <tr>
          <td><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?></td>
          <td><?php echo $rows['from_email']; ?></td>
          <td><?php echo $rows['to_email']; ?></td>
          <td class="icons text-center">
            <a href="<?=$HOST?>/send_email_content.php?log_id=<?= $rows['id'] ?>" class="emailer_content" data-toggle="tooltip" data-placement="top" title="Email Content">
              <i class="fa fa-eye "></i>
            </a>
          </td>
          <?php if(!empty($rows['details_status'])){
            if(in_array($rows['details_status'], array('bounce','deferred','dropped'))){
              $rows['status'] = 'fail';
            }else{
              $rows['status'] = 'success';
            }
          }?>
          <td class="icons text-center">
            <a href="<?=$HOST?>/send_email_activity.php?log_id=<?= $rows['id'] ?>&email=<?=  $rows['to_email'] ?>" class="emailer_content" data-toggle="tooltip" ata-placement="top" title="<?= ($rows['status']=='fail')?'Fail':'Success' ?>" aria-hidden="true" >
              <?php if($rows['status']=='fail'){ ?>
                <div class="text-action"><img src="images/icons/icon-alert.svg" width="18px"></div>
              <?php }else{ ?>
                <div class="text-success"><i class="fa fa-check-circle fa-lg"></i></div>
              <?php } ?>
            </a>
          </td>  
        </tr>
        <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="5">No record(s) found</td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="5">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
</div>
<?php }else{ ?> 
<div class="row m-b-20">
  <div class="col-sm-4">
    <div class="email_tab_box">
      <a href="emailer_broadcaster.php" class="btn btn-action">Email Broadcaster</a>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="email_tab_box">
      <a href="triggers.php" class="btn btn-info">Email Triggers</a>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="email_tab_box">
      <a href="emailer_template.php" class="btn btn-cyan">Email Templates</a>
    </div>
  </div>
</div>
<div class="white-box">
  <form id="frm_search" action="emailer_dashboard.php" method="GET" autocomplete="off">
    <input type="hidden" name="viewEmail" id="viewEmail" value="<?=checkIsset($viewEmail)?>">
    <div class="clearfix tbl_filter  m-b-10">
      <div class="pull-left ">
        <h4>Most Recent Activity</h4>
      </div>
      <div class="pull-right">
          <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="page" id="nav_page" value="" />
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
      </div>
      <div class="clearfix"></div>
      <div class="m-b-0 d-block">
          <div class="note_search_wrap" id="search_div" style="display: none; max-width: 100%;">
            <div class="row theme-form">
              <div class="col-md-2">
                <div class="phone-control-wrap">
                  <div class="phone-addon w-30">
                    <div class="form-group height_auto">
                      <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                    </div>
                  </div>
                  <div class="phone-addon">
                    <div class="form-group height_auto">
                      <input type="text" id="email" name="email" class="form-control listing_search" value="<?= $email ?>" >
                      <label>Email</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row" id="show_date">
                  <div id="date_range" class="col-md-12">
                    <div class="form-group  height_auto">
                      <select class="form-control" id="join_range" name="join_range">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Sent Date</label>
                    </div>
                  </div>
                  <div class="select_date_div col-md-9" style="display:none">
                    <div class="form-group height_auto">
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
                              <div class="pr">
                                <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                              </div>
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group height_auto">
                  <select class="form-control listing_search" id="status" name="status">
                    <option value=""></option>
                    <option value="success" <?=$status == 'success'?"selected='selected'":''?>> Success </option>
                    <option value="fail" <?=$status == 'fail'?"selected='selected'":''?>> Fail </option>
                  </select>
                  <label>Status</label>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group height_auto">
                  <button type="submit" class="btn btn-info search_button btn-block" name="search" id="search" >Search</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </form>
  <div id="ajax_loader" class="ajex_loader" style="display: none;">
    <div class="loader"></div>
  </div>
  <div id="ajax_data"> </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    dropdown_pagination('ajax_data')
    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    }); 

    $(document).keypress(function (e) {
      if (e.which == 13) {
        ajax_submit();
      }
    });
    ajax_submit();
  
  });

  $(document).off("click", ".search_btn");
  $(document).on("click", ".search_btn", function(e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
  });
  $(document).off("click", ".search_close_btn");
  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    $("#search_div").hide();
    $(".search_btn").show();
  });

  $(document).off('click', '.emailer_content');
  $(document).on('click', '.emailer_content', function (e) {
    e.preventDefault();
    $.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '900px', 
      height: '580px'
    });
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

  $(document).off("submit", "#frm_search");
  $(document).on("submit", "#frm_search", function(e) {
    $('#nav_page').val(1);
    $('#viewEmail').val("allEmails");
    e.preventDefault();
    disable_search();
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

  viewEmail = function(){
    var odrDisplay = $("#viewEmail").val();
    var today = "<?=$added_date?>";
    if(odrDisplay == "todayEmail"){
      $('#join_range').val('Exactly').trigger('change');
      $("#added_date").val(today);
    }
    $(".search_btn").click();
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
        viewEmail();
        common_select();
        $('[data-toggle="tooltip"]').tooltip()
      }
    });
    return false;
  }
 
</script>
<?php }?>

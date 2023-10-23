<?php if ($popup_is_ajaxed) { ?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th>Product Name</th>
          <th>Product ID</th>
          <th class="text-center">URL</th>
          <th width="130px">Current Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td><?=$rows['name']?></td>
              <td><?=$rows['product_code']?></td>
              <td class="text-center">
                <?php if(!empty($rows['url_str'])) {
                  $url_array = explode(",", $rows['url_str']);
                  if(count($url_array) > 0){
                    foreach ($url_array as $key => $value) { ?>
                      <a href="<?=$value?>" target="_blank"><i class="fa fa-link fs18 text-blue"></i></a>
                    <?php }
                  } 
                } ?>
              </td>
              <td >Active</td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="4">No record(s) found</td>
          </tr>
        <?php } ?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
        <tfoot>
          <tr>
            <td colspan="4">
              <?php echo $paginate->links_html; ?>
            </td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
    <div class="text-center">
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
    </div>
  </div>
<?php } else{ ?>
  <div class="panel panel-default panel-block">
    <form id="popup_frm_search" action="providers_prd_popup.php" method="GET">
      <div class="panel-heading">
        <div class="panel-title">
          <p class="fs18"><strong class="fw500"><?=$providers_res['name'] . ' ('.$providers_res['display_id'] .') - '?> </strong> <span class="fw300">(<?=$providers_res['prd_total']?>) Products</span></p>
        </div>
      </div>
      <div class="panel-footer clearfix" style="display: none;">
        <input type="hidden" name="export" id="export" value=""/>
        <input type="hidden" name="popup_is_ajaxed" id="popup_is_ajaxed" value="1" />
        <input type="hidden" name="provider_id" id="provider_id" value="<?=$providers_res['id']?>" />
        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
        <input type="hidden" name="page" id="nav_page" value="" />
        <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
        <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data"> </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
    dropdown_pagination('ajax_data')
      $(document).keypress(function (e) {
        if (e.which == 13) {
          popup_ajax_submit();
        }
      });

      popup_ajax_submit();
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
    });

    $(document).off("submit", "#popup_frm_search");
    $(document).on("submit", "#popup_frm_search", function(e) {
      $('#nav_page').val(1);
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


    // $(document).off('click', '#export_admin');
    // $(document).on('click', '#export_admin', function(e) {
    //   swal({
    //     title: 'Are you sure?',
    //     text: 'You want to export to Excel All Admins',
    //     type: 'success',
    //     showCancelButton: true,
    //     confirmButtonColor: "#0088cc",
    //     confirmButtonText: 'Yes, export it!',
    //     cancelButtonText: 'No, cancel!',
    //   }).then(function() {
    //     $("#export").val('export_admin');
    //     $('#ajax_loader').show();
    //     $('#popup_is_ajaxed').val('1');
    //     var params = $('#popup_frm_search').serialize();
    //     $.ajax({
    //       url: $('#popup_frm_search').attr('action'),
    //       type: 'GET',
    //       data: params,
    //       dataType: 'json',
    //       success: function() {
    //         $('#ajax_loader').hide();
    //         $("#export").val('');
    //       }
    //     }).done(function(data) {
    //       var $a = $("<a>");
    //       $a.attr("href", data.file);
    //       $("body").append($a);
    //       $a.attr("download", "Admins_Record.xls");
    //       $a[0].click();
    //       $a.remove();
    //       $('#ajax_loader').hide();
    //     });
    //   }, function(dismiss) {

    //   })
    // });


    function disable_search() {
      if ($(".listing_search").filter(function() {
          return $(this).val();
        }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
          return $(this).val();
          0
        }).length > 0)) {
        popup_ajax_submit();
      } else {
        swal('Oops!!', 'Please Enter Data To Search', 'error');
      }
    }

    function popup_ajax_submit() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#popup_is_ajaxed').val('1');
      $("#export").val('');
      var params = $('#popup_frm_search').serialize();
      var cpage = $('#nav_page').val();
      $.ajax({
        url: $('#popup_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function(res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
        }
      });
      return false;
    }

</script>
<?php }?>
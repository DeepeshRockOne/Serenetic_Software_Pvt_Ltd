<?php if ($is_ajaxed) { ?>
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <div class="panel-title">
        <p class="fs18"><strong class="fw500"><?=ucfirst($file_name);?> - </strong> <span class="fw300">(<?=$paginate->total_results?>) Pending Records</span></p>
      </div>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b-15">
        <form name="member_search" id="member_search">
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
          <div class="text-right">
            <div class="note_search_wrap" id="search_div" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon text-right">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                  </div>
                </div>
                <div class="phone-addon w-300">
                  <div class="form-group height_auto mn">
                    <input type="text" name="member_id" class="form-control">
                    <label>Member ID</label>
                  </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" id="btn_submit" class="btn btn-info">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
          </div>
        </form>
      </div>
      <div class="table-responsive">
        <table class="<?= $table_class ?>">
          <thead>
            <tr>
              <th>ID/Member Name</th>
              <th>ID/Product Name</th>
            </tr>
          </thead>
          <tbody>
            <?php if($membership_data){ 
              foreach ($membership_data as $key => $value) { ?>
               <tr>
                <td><a href="javascript:void(0);"><?=$value['rep_id']?></a><br><?=$value['member_name']?></td>
                <td><a href="javascript:void(0);"><?=$value['product_code']?></a><br><?=$value['product_name']?></td>
              </tr>
              <?php } ?> 
            <?php }else{ ?>
              <tr><td colspan="4" class="text-center">No Records Found</td></tr>
            <?php } ?>
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
        <div class="text-center"> <a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link">Close</a> </div>
      </div>
    </div>
  </div>
<?php }else{ ?>
  <form id="frm_search" action="fulfillment_records_popup.php" method="GET">
    <input type="hidden" name="id" id="id" value="<?=$file_id?>" />
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="is_export" id="is_export" value="" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="sort" id="sort_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="direction" id="sort_direction" value="<?=$SortDirection;?>" />
  </form>
 
    <div  class="ajex_loader" style="display:none"></div>
    <div id="ajax_data"> </div>
  
  <script type="text/javascript">
    $(document).ready(function() {
    dropdown_pagination('ajax_data')
  
      ajax_submit();
      
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
            common_select();
          }
        });
        return false;
      }
    });
</script>
<?php } ?>

<script type="text/javascript">
    $(document).on("click", ".search_btn", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div").css('display', 'inline-block');
    });
    $(document).on("click", ".search_close_btn", function(e) {
      e.preventDefault();
      $("#search_div").hide();
      $(".search_btn").show();
    });
    $(document).on("click", "#btn_submit", function(e) {
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

    function ajax_submit() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#is_ajaxed').val('1');
      
      var params = $('#member_search').serialize();
      $.ajax({
        url: $('#member_search').attr('action'),
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

<?php if ($is_ajaxed) { ?>
  <script type="text/javascript">
    // $(document).on('ready',function(){
      $(".billing_schedule").colorbox({
        iframe:true,
        width:"650px",
        height:"670px",
      });

      $(".manually_billing").colorbox({iframe: true, width: '700px', height: '385px'});

      $(".billing_product").colorbox({
        iframe:true,
        width:"768px",
        height:"360px",
      });

      $(".add_billing").colorbox({
        iframe:true,
        width:"650px",
        height:"570px",
      });
    // });
  </script>
  <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>File Name</th>
            <th class="text-center" width="20%">Products</th>
            <th>Last Processed</th>
            <th width="20%">Next Scheduled</th>
            <th class="text-center" >Processed Files</th>
            <th width="130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total_rows > 0) {
            foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td><?=$rows['file_name']?></td>
              <td class="text-center">
                <a href="billing_product.php?id=<?=$rows['id']?>" class="billing_product text-action fw500"><?=$rows['products']?></a>
              </td>
              <td><?=strtotime($rows['last_processed']) > 0 ? date('m/d/Y h:i A',strtotime($rows['last_processed'])) . " EST" : "-" ?></td>
              <td><?=strtotime($rows['next_scheduled']) > 0 && strtotime($rows['next_scheduled']) >= strtotime('now') ? date('m/d/Y h:i A',strtotime($rows['next_scheduled'])) . " EST" : "-" ?></td>
              <td class="text-center">
                <a href="billing_processed_file.php?id=<?=$rows['id']?>" class="text-action fw500"><?=$rows['total_files']?></a>
              </td>
              <td class="icons">
                <a href="billing_schedule.php?id=<?=$rows['id']?>" data-toggle="tooltip" data-trigger="hover" title="Schedule" class="billing_schedule" ><i class="fa fa-calendar"></i></a>
                <a href="manually_billing.php?id=<?=$rows['id']?>" class="manually_billing" data-toggle="tooltip" data-trigger="hover" title="Manually Generate"><i class="fa fa-download"></i></a>
                <a href="add_billing_files.php?id=<?=$rows['id']?>" class="add_billing" data-toggle="tooltip" data-trigger="hover" title="Edit"><i class="fa fa-edit"></i></a>
              </td>
            </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr>
              <td colspan="5">No Records</td>
            </tr>
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
    </div>
  </div>
</div>
<?php }else{ ?>
  <?php include_once 'notify.inc.php';?>
  <form id="frm_search" action="billing_files.php" method="GET" class="sform">
    <div class="panel panel-default panel-block advance_info_div">
      <div class="panel-body">
        <div class="phone-control-wrap ">
          <div class="phone-addon w-130 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="130px">
          </div>
          <div class="phone-addon text-left">
                <div class="info_box">
                  <p class="m-t-0 fw600">Billing Files</p>
                  <p class="fs14 ">Creating an electronic billing file is a clean, easy, and streamlined approach to getting a unified file to a vendor properly and efficiently every time. Your system allows you to create a general
file at will, or if you need specific formatting please contact us today.</p>
                  <div class="responsive_btn">
                    <a href="add_billing_files.php" class="add_billing btn btn-action">+ Billing File</a>
                    <a href="billing_export_requests.php" class="btn btn-info">Export Requests</a>
                  </div>
                </div>
              </div>
              <div class="visible-xs m-b-15"></div>
              <!-- <div class="col-sm-6">
                <div class="info_box">
                  <p class="m-t-0 fw600">Process Files</p>
                  <p class="fs14 ">An billing file can be processed by scheduling the file to be run automatically or admins can manually run a file whenever needed.  All manual files, once requested, can be found on the Export Requests page by clicking the button below:</p>
                </div>
              </div> -->
        </div>
      </div>
    </div>
    <div class="panel panel-default panel-block panel-title-block" style="display: none;">
      <div class="panel-wrapper collapse in">
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'email_broadcaster_detail_page.php'"> <i class="fa fa-search-plus"></i> View All </button>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_submit();">
                    <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="clearfix tbl_filter">
      <div class="pull-left ">
        <h4 class="m-t-7">Billing Files</h4>
      </div>
    <div class="pull-right">
      <div class="m-b-15">
        <div class="note_search_wrap" id="search_div" style="display: none; max-width: 100%;">
          <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
              <div class="form-group">
              <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
              </div>
            </div>
            <div class="phone-addon w-300">
              <div class="form-group">
                <input type="text" name="file_name" class="form-control">
                <label>File Name</label>
              </div>
            </div>
             <div class="phone-addon w-300 text-left">
              <div class="form-group">
                <select class="se_multiple_select" name="products[]"  id="products" multiple="multiple" >
                  <?php foreach ($company_arr as $key=>$company) { ?>
                  <optgroup label='<?= $key ?>'>
                     <?php foreach ($company as $pkey =>$row) { ?>
                     <option value="<?= $row['id'] ?>"><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                     <?php } ?>
                  </optgroup>
                  <?php } ?>
                </select>
                <label>Product(s)</label>
              </div>
            </div>
          
            <div class="phone-addon w-80">
              <div class="form-group">
              <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
              </div>
            </div>
          </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
      </div>
    </div>
    </div>
     <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data"> </div>
  </div>
</div>
</form>
  
   


<script type="text/javascript">
$(document).ready(function(){
  ajax_submit();
  dropdown_pagination('ajax_data')
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

$(document).on('click','.search_button',function(){
  ajax_submit();
});
$(document).on('click','.search_close_btn',function(){
  window.location.reload();
});

$("#products").multipleSelect({
  });
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

  $(".add_billing").colorbox({
    iframe:true,
    width:"650px",
    height:"570px",
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
        $('#is_ajaxed').val('');
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        $('[data-toggle="tooltip"]').tooltip();
        $("#products").multipleSelect("refresh");
        common_select();
      }
    });
    return false;
  }
</script>
<?php } ?>
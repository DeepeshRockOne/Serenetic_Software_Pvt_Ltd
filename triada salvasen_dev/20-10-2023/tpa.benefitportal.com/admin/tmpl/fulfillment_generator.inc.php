<?php if ($is_ajaxed) { ?>
  <script type="text/javascript">
    // $(document).on('ready',function(){
      $(".fulfillment_schedule").colorbox({
        iframe:true,
        width:"650px",
        height:"670px",
      });

      $(".manually_fulfillment").colorbox({
        iframe:true,
        width:"1024px",
        height:"480px",
      });

      $(".fulfillment_product").colorbox({
        iframe:true,
        width:"768px",
        height:"360px",
      });

      $(".fulfillment_records").colorbox({
        iframe:true,
        width:"768px",
        height:"600px",
      });

      $(".add_fulfillment").colorbox({
        iframe:true,
        width:"650px",
        height:"320px",
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
            <th width="">Next Scheduled</th>
            <th class="text-center" width="">Pending Records</th>
            <th class="text-center" >Processed Files</th>
            <th width="130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total_rows > 0) {
            foreach ($fetch_rows as $rows) { ?>
            <tr>
              <td><?=$rows['file_name']?></td>
              <?php
                $total_products = $rows['products'] ? count(explode(',', $rows['products'])) : 0;
              ?>
              <td class="text-center">
                <a href="fulfillment_product.php?id=<?=$rows['id']?>" class="fulfillment_product text-action fw500"><?=$total_products?></a>
              </td>
              <td><?=strtotime($rows['last_processed']) > 0 ? $tz->getDate($rows['last_processed']) : "-" ?></td>
              <td><?=strtotime($rows['next_scheduled']) > 0 && strtotime($rows['next_scheduled']) >= strtotime('now') ? date('m/d/Y h:i A',strtotime($rows['next_scheduled'])) ." ". $current_timzone : "-" ?></td>
              <?php 

                $total_files = $pdo->selectOne("SELECT COUNT(id) as total_files FROM fulfillment_history where service_group_id = :id AND status = 'Processed' AND is_deleted = 'N'",array(':id' => $rows['id']));

                $total_records = $pdo->selectOne("SELECT count(DISTINCT ce.id) as total_records 
                  FROM fulfillment_files ff
                  JOIN website_subscriptions w on(FIND_IN_SET(w.product_id,ff.products) AND w.status not in('Pending Declined','Pending Payment'))
                  JOIN customer_enrollment ce on(ce.website_id = w.id AND ce.is_fulfillment = 'N')
                  JOIN customer c on(c.id = w.customer_id AND c.status NOT IN('Pendind Validation','Customer Abandon','Pending'))
                  where ff.id = :id AND ff.is_deleted = 'N' GROUP by ff.id order by ff.file_name",array(':id' => $rows['id']));

              ?>
              <td class="text-center"><a href="fulfillment_records_popup.php?id=<?=$rows['id']?>" class="fulfillment_records text-action fw500"><?=isset($total_records['total_records']) ? $total_records['total_records'] : 0?></a></td>
              
              <td class="text-center">
                <a href="fulfillment_processed_file.php?id=<?=$rows['id']?>" class="text-action fw500"><?=$total_files['total_files']?></a>
              </td>
              <td class="icons">
                <a href="fulfillment_schedule.php?id=<?=$rows['id']?>" data-toggle="tooltip" data-trigger="hover" title="Schedule" class="fulfillment_schedule" ><i class="fa fa-calendar"></i></a>
                <a href="manually_fulfillment.php?id=<?=$rows['id']?>" class="manually_fulfillment" data-toggle="tooltip" data-trigger="hover" title="Manually Generate"><i class="fa fa-download"></i></a>
                <a href="add_fulfillment.php?id=<?=$rows['id']?>" class="add_fulfillment" data-toggle="tooltip" data-trigger="hover" title="Edit"><i class="fa fa-edit"></i></a>
              </td>
            </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr>
              <td colspan="7">No Records.</td>
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
  </div>
</div>
<?php }else{ ?>
  <?php include_once 'notify.inc.php';?>
  <form id="frm_search" action="fulfillment_generator.php" method="GET">
    <div class="panel panel-default panel-block advance_info_div">
      <div class="panel-body">
        <div class="phone-control-wrap ">
          <div class="phone-addon w-130 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="130px">
          </div>
          <div class="phone-addon text-left">
            <div class="row">
              <div class="col-sm-12">
                <div class="info_box">
                  <p class="m-t-0 fw600">Fulfillment Files</p>
                  <p class="fs14 ">Creating an electronic fulfillment file is a clean, easy, and streamlined approach to getting a unified file to a vendor properly and efficiently every time. Your system allows you to create a general file at will, or if you need specific formatting please contact us today.</p>
                  <div class="responsive_btn">
                    <a href="add_fulfillment.php" class="add_fulfillment btn btn-action">+ Fulfillment</a>
                    <a href="fulfillment_export_requests.php" class="btn btn-info">Export Requests</a>
                  </div>
                </div>
              </div>
              <div class="visible-xs m-b-15"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default panel-block panel-title-block" style="display: none;">
      <div class="panel-wrapper collapse in">
        <div class="panel-footer clearfix">
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
    <div class="pull-left">
      <h4 class="m-t-7">Fulfillment Files</h4>
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
            <div class="phone-addon w-200">
              <div class="form-group">
                <input type="text" name="file_name" class="form-control">
                <label>File Name</label>
              </div>
            </div>
             <div class="phone-addon w-300 text-left">
              <div class="form-group">
                <select class="se_multiple_select" name="products[]"  id="products" multiple="multiple" >
                  <?php if(!empty($productRes)){ ?>
                          <?php foreach ($productRes as $key=> $category) { ?>
                            <?php if(!empty($category)){ ?>
                        <optgroup label='<?= $key ?>'>
                          <?php foreach ($category as $pkey => $row) { ?>
                            <option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['id'], $assigned_products)) ? 'disabled="disabled"' : '' ?> >
                              <?= $row['name'] .' ('.$row['product_code'].')'?>    
                            </option>
                          <?php } ?>
                        </optgroup>
                          <?php } ?>
                        <?php } ?>
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

$(document).on('click','.search_button',function(){
  ajax_submit();
});

$(document).on('click','.search_close_btn',function(){
  window.location.reload();
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
          common_select()
        }
      });
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

  $(".add_fulfillment").colorbox({
    iframe:true,
    width:"650px",
    height:"330px",
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
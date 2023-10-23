<?php if ($is_ajaxed) { ?>
  <div class="clearfix">
    <?php if ($total_rows > 0) { ?>
        <div class="pull-left m-b-15">
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
      <?php } ?>
  </div>
  
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
         <tr>
            <th>Plan ID/Added Date</th>
            <th>Member ID/Name</th>
            <th>Product ID/Name</th>
            <th width="70px">Action</th>
         </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
           <tr>
              <td><a href="policy_details.php?ws_id=<?=md5($rows['id'])?>" target="blank" class="text-red fw500"><?=$rows['website_id']?></a><br>
              <?=displayDate($rows['created_at'])?></td>
              <td><a href="members_details.php?id=<?=md5($rows['customer_id'])?>" target="blank" class="text-red fw500"><?=$rows['rep_id']?></a><br>
              <?=$rows['member_name']?></td>
              <td><a href="javascript:void(0);"  class="text-red fw500"><?=$rows['product_code']?></a><br><?=$rows['name']?></td>
            
              <td class="icons text-center"><a href="<?=$HOST?>/payment_failed_coverages_details.php?websiteId=<?=md5($rows['id'])?>&customerId=<?=md5($rows['customer_id'])?>" target="blank" data-toggle="tooltip" data-trigger="hover" data-original-title="Coverage Details" 
                class="coverage_popup"><i class="fa fa-list"></i></a></td>
           </tr>
         <?php } ?>
        <?php }else {?>
          <tr>
              <td colspan="4" class="text-center">No record(s) found</td>
          </tr>
      <?php }?>
      </tbody>
      <?php if ($total_rows > 0) { ?>
      <tfoot>
         <tr>
            <td colspan="4">
               <?php echo $paginate->links_html; ?>
            </td>
         </tr>
      </tfoot>
      <?php } ?>
    </table>
  </div>
<?php }else{ ?>
  <div class="panel panel-default panel-block panel-title-block ">
     <form id="frm_search" action="payment_failed_coverages.php" method="GET">
        <div class="panel-left">
           <div class="panel-left-nav">
              <ul>
                 <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
              </ul>
           </div>
        </div>
        <div class="panel-right">
           <div class="panel-heading">
              <div class="panel-search-title"> <span class="clr-light-blk">SEARCH</span></div>
           </div>
           <div class="panel-wrapper collapse in" aria-expanded="true">
              <div class="panel-body theme-form">
                 <div class="row">
                    <div class="col-md-6 col-sm-12">
                       <div class="form-group height_auto">
                          <input class="listing_search" name="policy_ids" id="policy_ids" />
                          <label>Plan ID(s)</label>
                       </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                       <div class="row">
                          <div id="date_range" class="col-md-12 listing_search">
                             <div class="form-group height_auto">
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
                    <div class="col-md-6">
                       <div class="form-group height_auto">
                          <input class="listing_search" name="member_id" id="member_id" />
                          <label>Member ID(s)</label>
                       </div>
                    </div>
                    <div class="col-md-6">
                       <div class="form-group height_auto">
                              <select name="product[]" id="product" multiple="multiple" class="listing_search se_multiple_select">  
                                <?php foreach ($company_arr as $key=>$company) { ?>
                                  <optgroup label='<?= $key ?>'>
                                    <?php    foreach ($company as $pkey =>$row) { ?>
                                        <option value="<?= $row['id'] ?>" <?=!empty($product_id) && in_array($row['id'],$product_id)?'selected="selected"':''?>><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                                    <?php } ?>
                                  </optgroup>
                                <?php } ?>
                              </select>
                              <label>Products</label>
                          </div>
                    </div>
                 </div>
                 <div class="panel-footer clearfix">
                    <button type="button" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
                    <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'payment_failed_coverages.php'"> <i class="fa fa-search-plus"></i> View All</button>
                    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                    <input type="hidden" name="export_val" id="export_val" value="" />
                    <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
                    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
                    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
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

      $("#product").multipleSelect({});
      ajax_submit();
      initSelectize('policy_ids','PolicyID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
      initSelectize('member_id','MemberID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
      $("#products").multipleSelect({});
      $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });


      $(document).off('change', '#join_range');
      $(document).on('change', '#join_range', function(e) {
        e.preventDefault();
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

      $(document).off('click', '#ajax_data ul.pagination li a');
      $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();

        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function (res) {
            $('#ajax_loader').hide();
            $('[data-toggle="tooltip"]').tooltip();
            $('#ajax_data').html(res).show();
            common_select();
          }
        });
      });

      $(document).on('click', '#search', function (e) {
        // e.preventDefault();
        ajax_submit();
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
          $('.coverage_popup').colorbox({iframe: true, width: '900px', height: '700px'});
          $('[data-toggle="tooltip"]').tooltip();
          common_select();
        }
      });
      return false;
    }
  </script>
<?php } ?>


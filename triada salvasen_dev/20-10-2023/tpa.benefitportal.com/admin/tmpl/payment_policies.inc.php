<?php if ($is_ajaxed) { ?>
  <script type="text/javascript">
    $('.order_receipt').colorbox({iframe: true, width: '900px', height: '700px'});

    $('#pages').on('change',function(){
      $('#per_pages').val($(this).val());
      ajax_submit();        
    });

  </script>
  
     <div class="clearfix">
        <div class="pull-left">
           <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
              <div class="mn">
                 <!-- <label for="user_type">Records Per Page </label> -->
                 <h4 class="m-b-15 m-t-0">Plans</h4>
              </div>
              <!-- <div class="form-group mn">
                 <select size="1" id="pages" class="form-control" >
                    <option value="10" <?=$per_page == 10 ? "selected='selected'" : ""?>>10</option>
                    <option value="25" <?=$per_page == 25 ? "selected='selected'" : ""?>>25</option>
                    <option value="50" <?=$per_page == 50 ? "selected='selected'" : ""?>>50</option>
                    <option value="100" <?=$per_page == 100 ? "selected='selected'" : ""?>>100</option>
                 </select>
              </div> -->
           </div>
        </div>
        <div class="pull-right">
          <div class="m-b-15">
            <a class="btn btn-action mn" target="_blank" href="payment_failed_coverages.php">Missed Plan Payments</a>
          </div>
        </div>
     </div>
     <div class="table-responsive">
     <table class="<?=$table_class?>">
        <thead>
           <tr>
              <th >Plan ID/Added Date</th>
              <th>Member ID/Name</th>
              <th>Product ID/Name</th>
              <th >Effective Date</th>
              <th>Status</th>
              <th>Next Billing Date</th>
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
                <td><?=displayDate($rows['eligibility_date'])?></td>
                <td>
                  <?php
                  $tmp_status = get_policy_display_status($rows['status']);
                  ?>
                  <select class="form-control w_status" name="w_status" data-id="<?=$rows['id']?>">
                    <option value=""></option>
                    <option value="Active" <?=$tmp_status == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
                    <option value="Inactive" <?=$tmp_status == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
                    <option value="Pending" <?=$tmp_status == 'Pending' ? "selected = 'selected'" : ""?>>Pending</option>
                  </select>
                </td>
                <td><?=displayDate($rows['next_purchase_date'])?></td>
                <td class="icons text-center"><a href="policy_details.php?ws_id=<?=md5($rows['id'])?>" target="blank" data-toggle="tooltip" data-trigger="hover" data-original-title="Plan Details"><i class="fa fa-list"></i></a></td>
             </tr>
           <?php } ?>
          <?php }else {?>
            <tr>
                <td colspan="9">No record(s) found</td>
            </tr>
        <?php }?>
        </tbody>
        <tfoot>
           <tr>
              <td colspan="9">
                 <?php echo $paginate->links_html; ?>
              </td>
           </tr>
        </tfoot>
     </table>
  </div>
<?php }else{ ?>
  <div class="panel panel-default panel-block panel-title-block advance_info_div">
    <div class="panel-body ">
        <div class="phone-control-wrap ">
          <div class="phone-addon w-90">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
          </div>
          <div class="phone-addon text-left lato_font">Retrieve information on all policies using the search fields above or clicking the button below to show all activity.  By default only todays policies are appearing.
            <div class="clearfix m-t-15">
              <a href="payment_policies.php" class="btn btn-info">All Policies</a>
            </div>
          </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-block panel-title-block ">
     <form id="frm_search" action="payment_policies.php" method="GET">
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
                                   <input type="text" name="order_added_date" id="added_date" value="" class="form-control date_picker" />
                                </div>
                                <div  id="range_join" style="display:none;">
                                   <div class="phone-control-wrap">
                                      <div class="phone-addon">
                                         <label class="mn">From</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="order_fromdate" id="fromdate" value="" class="form-control date_picker" />
                                         </div>
                                      </div>
                                      <div class="phone-addon">
                                         <label class="mn">To</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="order_todate" id="todate" value="" class="form-control date_picker" />
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
                    <div class="clearfix"></div>
                    <div class="col-md-6 col-sm-12">
                       <div class="row">
                          <div id="next_billing_date_range" class="col-md-12">
                             <div class="form-group ">
                                <select class="form-control listing_search" id="next_billing_range" name="next_billing_range">
                                   <option value=""> </option>
                                   <option value="Range">Range</option>
                                   <option value="Exactly">Exactly</option>
                                   <option value="Before">Before</option>
                                   <option value="After">After</option>
                                </select>
                                <label>Next Billing Date</label>
                             </div>
                          </div>
                          <div class="select_next_billingdate_div col-md-9 col-sm-12" style="display:none">
                             <div class="form-group ">
                                <div id="next_billing_all_join" class="input-group">
                                   <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                   <input type="text" name="next_billing_added_date" id="next_billing_added_date" value="" class="form-control date_picker" />
                                </div>
                                <div  id="next_billing_range_join" style="display:none;">
                                   <div class="phone-control-wrap">
                                      <div class="phone-addon">
                                         <label class="mn">From</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="next_billing_fromdate" id="next_billing_fromdate" value="" class="form-control date_picker" />
                                         </div>
                                      </div>
                                      <div class="phone-addon">
                                         <label class="mn">To</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="next_billing_todate" id="next_billing_todate" value="" class="form-control date_picker" />
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
                        <select class="form-control" name="status">
                          <option></option>
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                          <option value="Pending">Pending</option>
                          <!-- <option value="On Hold Failed Billing">On Hold Failed Billing</option>
                          <option value="Inactive Failed Billing">Inactive Failed Billing</option>
                          <option value="Inactive Member Request">Inactive Member Request</option> -->
                        </select>
                        <label>Status</label>
                      </div>
                    </div>
                 </div>
                 <div class="panel-footer clearfix">
                    <button type="button" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
                    <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'payment_policies.php'"> <i class="fa fa-search-plus"></i> View All</button>
                    <button type="button" class="btn red-link" id="export"> <i class="fa fa-download"></i> Export</button>
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
     
       $(document).off('change', '.w_status');
       $(document).on('change', '.w_status', function(e) {
        e.stopImmediatePropagation();
        ws_id = $(this).data('id');
        status = $(this).val();
          swal({
              text: 'Change Status: Are you sure?',
              showCancelButton: true,
              confirmButtonText: 'Confirm',
              cancelButtonText: 'Cancel',
          }).then(function (e) {

               $('#ajax_loader').show();
               $('#ajax_data').hide();
               $.ajax({
                 url: 'payment_policies.php',
                 type: 'POST',
                 data: {ws_id:ws_id,status:status},
                 success: function(res) {
                   $('#ajax_loader').hide();
                   window.location.reload();
                 }
               });
          }, function (dismiss) {
              window.location.reload();
          })
      });

       $(document).off('change', '#next_billing_range');
       $(document).on('change', '#next_billing_range', function(e) {
         e.preventDefault();
         if($(this).val() == ''){
           $('.select_next_billingdate_div').hide();
           $('#next_billing_date_range').removeClass('col-md-3').addClass('col-md-12');
         }else{
           $('#next_billing_date_range').removeClass('col-md-12').addClass('col-md-3');
           $('.select_next_billingdate_div').show();
           if ($(this).val() == 'Range') {
             $('#next_billing_range_join').show();
             $('#next_billing_all_join').hide();
           } else {
             $('#next_billing_range_join').hide();
             $('#next_billing_all_join').show();
           }
         }
       });
     
     
     $(document).ready(function() {
    dropdown_pagination('ajax_data')

       $("#product").multipleSelect({});
       ajax_submit();
        initSelectize('policy_ids','PolicyID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
        initSelectize('member_id','MemberID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);

       $("#admin_id").multipleSelect({
            selectAll: false,
       });
     
       $("#products").multipleSelect({
       });
     
       $("#status").multipleSelect({
          selectAll: false,
          filter : false
       });

       $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
      });
     
     });

   $(document).off('click', '#export');
   $(document).on('click', '#export', function (e) {
      e.stopPropagation();

      confirm_export_data(function() {
         $("#export_val").val('1');
         $('#ajax_loader').show();
         // $('#is_ajaxed').val('1');
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
   
     // $(document).off("submit", "#frm_search");
     //  $(document).on("submit", "#frm_search", function(e) {
     //    e.preventDefault();
     //    disable_search();
     //  });

    // function disable_search() {
    //   if ($(".listing_search").filter(function() {
    //       return $(this).val();
    //     }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
    //       return $(this).val();
    //       0
    //     }).length > 0)) {
    //     ajax_submit();
    //   } else {
    //     swal('Oops!!', 'Please Enter Data To Search', 'error');
    //   }
    // }

     $(document).on('click', '#search', function (e) {
      // e.preventDefault();
      ajax_submit();
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
           $('[data-toggle="tooltip"]').tooltip();
           common_select();
         }
       });
       return false;
     }
  </script>
<?php } ?>


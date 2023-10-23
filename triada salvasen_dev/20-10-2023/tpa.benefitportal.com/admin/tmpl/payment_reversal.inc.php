<?php if ($is_ajaxed) { ?>
  <script type="text/javascript">
    var not_win = '';
      $(".order_receipt").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=1024,height=630");
      if(not_win.closed) {  
        alert('closed');  
      } 
    });
    $('#pages').on('change',function(){
      $('#per_pages').val($(this).val());
      ajax_submit();        
    });

  </script>
  <!-- Reverse summary code start -->
   <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-10">
        <h4 class="m-t-0">Order Summary</h4>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th>Gross Sales</th>
              <th>Chargebacks</th>
              <th>Refunds</th>
              <th>Voids</th>
              <th>Payment Returned</th>
              <th width="150px" class="bg-warning text-black text-center">Net Sales</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($grossSaleCCAmt)?> / ACH: <?=displayAmount($grossSaleACHAmt)?></span>" data-html="true">
                    <?=$grossSaleCnt?>/<span><?=displayAmount($grossSaleAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=dispReversalAmt($cbCCAmt)?> / ACH: <?=dispReversalAmt($cbACHAmt)?></span>" data-html="true">
                    <?=$cbTransCnt?>/<span><?=dispReversalAmt($cbAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=dispReversalAmt($refundCCAmt)?> / ACH: <?=dispReversalAmt($refundACHAmt)?></span>" data-html="true">
                    <?=$refundTransCnt?>/<span><?=dispReversalAmt($refundAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=dispReversalAmt($voidCCAmt)?> / ACH: <?=dispReversalAmt($voidACHAmt)?></span>" data-html="true">
                    <?=$voidTransCnt?>/<span><?=dispReversalAmt($voidAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=dispReversalAmt($returnedCCAmt)?> / ACH: <?=dispReversalAmt($returnedACHAmt)?></span>" data-html="true">
                    <?=$returnedTransCnt?>/<span><?=dispReversalAmt($returnedAmt)?></span>
                </a>
              </td>
              <td class="text-center">
                <a href="javascript:void(0);">
                    <?php 
                      if($netSaleAmt < 0){
                        $netSaleAmt = abs($netSaleAmt);
                        $netAmtText = "<span class='text-red'>(".displayAmount($netSaleAmt).")</span>";
                      }else{
                        $netAmtText  = "<span>".displayAmount($netSaleAmt)."</span>";
                      }
                    ?>
                    <span><?=$netAmtText?></span>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Order summary code ends -->
  <div class="panel panel-default panel-block">
    <div class="panel-body">
     <div class="clearfix m-b-15 tbl_filter">
        <div class="pull-left">
           <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
              <div class="form-group mn">
                 <label for="user_type">Records Per Page </label>
              </div>
              <div class="form-group mn">
                 <select size="1" id="pages" class="form-control" >
                    <option value="10" <?=$per_page == 10 ? "selected='selected'" : ""?>>10</option>
                    <option value="25" <?=$per_page == 25 ? "selected='selected'" : ""?>>25</option>
                    <option value="50" <?=$per_page == 50 ? "selected='selected'" : ""?>>50</option>
                    <option value="100" <?=$per_page == 100 ? "selected='selected'" : ""?>>100</option>
                 </select>
              </div>
           </div>
        </div>
        <div class="pull-right">
           <a href="add_payment_reversal.php" class="btn btn-action m-l-5">Reverse Order</a>
        </div>
     </div>
      <div class="table-responsive">
     <table class="<?=$table_class?>">
        <thead>
           <tr>
              <th>Order ID</th>
              <th>Reversal Date</th>
              <th>Original Order Date</th>
              <th>Member ID/Name</th>
              <th>Admin ID/Name</th>
              <th>Transaction Status/ID</th>
              <th class="text-center">Reversal Amount</th>
              <th>Reversal Type</th>
              <th>Reversal Method</th>
              <th  class="text-center">Actions</th>
           </tr>
        </thead>
        <tbody>
          <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $rows) { ?>
             <tr>
                <td><a href="javascript:void(0);" data-href="transaction_receipt.php?transId=<?=md5($rows['t_id'])?>"  class="text-red fw500 order_receipt"><?=$rows['display_id']?></a></td>
                <td><?=displayDate($rows['created_at'])?></td>
                <td><?=displayDate($rows['order_date'])?></td>
                <td><a href="members_details.php?id=<?=md5($rows['cust_id'])?>" target="blank" class="text-red fw500"><?=$rows['rep_id']?></a><br><?=$rows['member_name']?></td>
                <td><a href="javascript:void(0);"  class="text-red fw500"><?=$rows['admin_display_id']?></a><br><?=$rows['admin_name']?></td>

                <?php 
                  $status = "";
                  if($rows['status'] == 'Refund Order'){
                    $status = "Refund";
                  }else if($rows['status'] == 'Void Order'){
                    $status = 'Void';
                  }else {
                    $status = $rows['status'];
                  }

                 ?>


                <td><a href="javascript:void(0);" class="text-red fw500"><?=$status?><br /><?=$rows['transactionID']?></a></td>
                <td class="text-center"><a href="javascript:void(0);"  class="text-red fw500">(<?=displayAmount($rows['refund_amount'],2)?>)</a></td>
                <td><?=$rows['return_type']?></td>
                <td><?=$rows['reversalMethod']?></td>
                <td class="icons text-center"><a href="javascript:void(0);" data-href="transaction_receipt.php?transId=<?=md5($rows['t_id'])?>" data-toggle="tooltip" data-trigger="hover" class="order_receipt" data-placement="top" data-container="body" title="Receipt" data-html="true"><i class="fa fa-file-text"></i></a></td>
             </tr>
           <?php } ?>
          <?php }else {?>
            <tr>
                <td colspan="10">No record(s) found</td>
            </tr>
        <?php }?>
        </tbody>
        <tfoot>
           <tr>
              <td colspan="10">
                 <?php echo $paginate->links_html; ?>
              </td>
           </tr>
        </tfoot>
     </table>
  </div>
  </div>
</div>
<?php }else{ ?>
  <div class="panel panel-default panel-block panel-title-block ">
     <form id="frm_search" action="payment_reversal.php" method="GET" autocomplete="off">
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
                    <div class="col-md-6">
                       <div class="form-group height_auto">
                          <input type="text" id="orderIds" name="orderIds" value="" class="listing_search">
                          <label>Order ID(s)</label>
                       </div>
                    </div>
                    <div class="col-md-6">
                       <div class="row">
                          <div id="date_range" class="col-md-12 listing_search">
                             <div class="form-group">
                                <select class="form-control" id="join_range" name="join_range">
                                   <option value=""> </option>
                                   <option value="Range">Range</option>
                                   <option value="Exactly">Exactly</option>
                                   <option value="Before">Before</option>
                                   <option value="After">After</option>
                                </select>
                                <label>Order Date</label>
                             </div>
                          </div>
                          <div class="select_date_div col-md-9" style="display:none">
                             <div class="form-group">
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
                       <div class="row">
                          <div id="reversal_date_range" class="col-md-12">
                             <div class="form-group">
                                <select class="form-control listing_search" id="reversal_range" name="reversal_range">
                                   <option value=""> </option>
                                   <option value="Range">Range</option>
                                   <option value="Exactly">Exactly</option>
                                   <option value="Before">Before</option>
                                   <option value="After">After</option>
                                </select>
                                <label>Reversal Date</label>
                             </div>
                          </div>
                          <div class="select_reversaldate_div col-md-9" style="display:none">
                             <div class="form-group">
                                <div id="reversal_all_join" class="input-group">
                                   <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                   <input type="text" name="reversal_added_date" id="reversal_added_date" value="" class="form-control date_picker" />
                                </div>
                                <div  id="reversal_range_join" style="display:none;">
                                   <div class="phone-control-wrap">
                                      <div class="phone-addon">
                                         <label class="mn">From</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="reversal_fromdate" id="reversal_fromdate" value="" class="form-control date_picker" />
                                         </div>
                                      </div>
                                      <div class="phone-addon">
                                         <label class="mn">To</label>
                                      </div>
                                      <div class="phone-addon">
                                         <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="reversal_todate" id="reversal_todate" value="" class="form-control date_picker" />
                                         </div>
                                      </div>
                                   </div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-6">
                       <div class="form-group height_auto">
                          <input class="listing_search" name="member_id" id="member_id" />
                          <label>Member ID/Name</label>
                       </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-sm-6">
                       <div class="form-group ">
                          <select class="se_multiple_select listing_search" name="status[]"  id="status" multiple="multiple" >
                             <option value="Chargeback">Chargeback</option>
                             <option value="Payment Returned">Payment Returned</option>
                             <option value="Refund Order">Refund</option>
                             <option value="Void Order">Void</option>
                          </select>
                          <label>Transaction Status</label>
                       </div>
                    </div>
                    <div class="col-sm-6">
                       <div class="form-group height_auto">
                          <input class="listing_search form-control" name="transaction_id" id="transaction_id" />
                          <label>Transaction ID</label>
                       </div>
                    </div>
                    <div class="col-sm-6">
                       <div class="form-group ">
                          <select class="se_multiple_select listing_search" name="admin_id[]"  id="admin_id" multiple="multiple" >
                             <?php if($admins){ 
                                foreach ($admins as $admin) { ?>
                             <option value="<?=$admin['id']?>"><?=$admin['display_id'] . " - " .$admin['admin_name']?></option>
                             <?php } 
                           }
                                ?>
                          </select>
                          <label>Admin Name/ID</label>
                       </div>
                    </div>
                    <div class="col-sm-6">
                       <div class="form-group ">
                          <select class="form-control listing_search" name="reversal_type"  id="reversal_type">
                             <option value=""></option>
                             <option value="Full">Full</option>
                             <option value="Partial">Partial</option>
                          </select>
                          <label>Reversal Type</label>
                       </div>
                    </div>
                    <div class="col-sm-6">
                       <div class="form-group ">
                          <select class="form-control listing_search" name="reversal_method"  id="reversal_method">
                             <option value=""></option>
                             <option value="Original Payment">Original Payment</option>
                             <option value="Cheque">Check</option>
                          </select>
                          <label>Reversal Method</label>
                       </div>
                    </div>
                 </div>
                 <div class="panel-footer clearfix">
                    <button type="button" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
                    <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'payment_reversal.php'"> <i class="fa fa-search-plus"></i> View All</button>
                    <button type="button" class="btn red-link" id="btn_export"> <i class="fa fa-download"></i> Export</button>
                    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                    <input type="hidden" class="listing_search" name="export" id="export" value="" />
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
  <div id="ajax_loader" class="ajex_loader" style="display: none;">
     <div class="loader"></div>
  </div>
  <div id="ajax_data"> </div>

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
     
     $(document).off('change', '#reversal_range');
     $(document).on('change', '#reversal_range', function(e) {
         e.preventDefault();
         if($(this).val() == ''){
           $('.select_reversaldate_div').hide();
           $('#reversal_date_range').removeClass('col-md-3').addClass('col-md-12');
         }else{
           $('#reversal_date_range').removeClass('col-md-12').addClass('col-md-3');
           $('.select_reversaldate_div').show();
           if ($(this).val() == 'Range') {
             $('#reversal_range_join').show();
             $('#reversal_all_join').hide();
           } else {
             $('#reversal_range_join').hide();
             $('#reversal_all_join').show();
           }
         }
       });
     
     
      $(document).ready(function() {
       dropdown_pagination('ajax_data')

        ajax_submit();

        $(document).off('click', '#btn_export');
        $(document).on('click', '#btn_export', function(e) {
          confirm_export_data(function() {
            $("#export").val('transaction_export');
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
                $("#export").val('');
                if(res.status == "success") {
                  confirm_view_export_request();
                } else {
                  setNotifyError(res.message);
                }
              }
            });
          });
        });

      $('#orderIds').selectize({
         plugins: ['remove_button'],
         persist: false,
         createOnBlur:true,
         create: true
         
      });
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


<div class="white-box">
    <div id="enrollment" class="tab-pane active">
        <div class="panel-collapse panel-popup" id="collapseEnrollmentOrders">        
            <div class="panel-heading">
            	<div class="panel-title"><i class="fa fa-history clr-light-blk"></i>
              <span class="clr-light-blk">Sales History</span>
              </div>
            </div>
            <div class="panel-body">
                <div class="">            
                    <form method="GET" id="enrollment_orders_his_search_form" class="row">
                        <input type="hidden" value="<?=$id?>" name="id">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Order ID:</label>
                                <input type="text" name="orders_id" id="enrollment_orders_his_id" value="" class="form-control">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quick Search:</label>
                                <!-- <select class="form-control select2 placeholder select2-offscreen" name="enrollment_orders_his_custom_date" id="enrollment_orders_his_custom_date"> -->
                                <select class="form-control select2 placeholder select2-offscreen" name="custom_date" id="enrollment_orders_his_custom_date">

                                    <option value="">&nbsp;</option>
                                    <option value="Today">Today</option>
                                    <option value="Yesterday">Yesterday</option>
                                    <option value="Last7Days">Last 7 Days</option>
                                    <option value="ThisMonth">This Month</option>
                                    <option value="LastMonth">Last Month</option>
                                    <option value="ThisYear">This Year</option>
                                    <option value="Range">Range</option>
                                </select>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div id="enrollment_orders_his_range" style="display:none">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>From date:</label>
                                    <input type="text" name="fromdate" id="enrollment_orders_his_fromdate" value="" class="datetimepicker-range form-control">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To date:</label>
                                    <input type="text" name="todate" id="enrollment_orders_his_todate" value="" class="datetimepicker-range form-control">
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group text-center">
                            <div class="form-group">
                                <button id="enrollment_orders_his_search" name="search" type="button" class="btn btn-info" onclick="$('#enrollment_orders_his_search_form').submit();"> <i class="fa fa-search"></i> Search </button>
                                <button id="enrollment_orders_his_viewall" name="viewall" type="button" class="btn btn-info" onclick="window.location.href='user_sales_history.php?id=<?=$id?>';"> <i class="fa fa-search-plus"></i> View All </button>
                            </div>
                        </div>
                    </form>            
                    <div id="enrollment_orders_his_con" class="">
                        <table width="100%" cellspacing="0" cellpadding="0" class="table table-bordered nomargin color-table info-table">
                            <thead>
                                    <tr>
                                        <th width="25%">Order ID</th>
                                        <th width="25%">Amount Charged</th>
                                        <th width="25%">Status</th>
                                        <th width="25%">Order Date</th>
                                    </tr>
                            </thead>
                                    
                           <?php  if ($total_rows > 0) { 
                            foreach ($fetch_rows as $rows) { ?>
                            <?php }
                            }
                            ?>
                            <tr>
                                
                            <tbody class="enrollment_orders_data">
                                <?php  if ($total_rows > 0) { 
                                foreach ($fetch_rows as $rows) { ?>
                                   <tr>
                                      <td width="25%">
                                      <a href="order_receipts.php?order_id=<?php echo $rows['id']; ?>" class="popup"><?=$rows['display_id']?></a>
                                        
                                      </td>
                                      <td width="25%"><?=$rows['grand_total']?></td>
                                      <td width="25%"><?=$rows['status']?></td>
                                      <td width="25%"><?=date('m-d-Y h:i:s A',strtotime($rows['created_at']))?></td>
                                  </tr> 

                                <?php }
                                } else { ?>

                                <tr>
                                    <td colspan="4">No Record(s).</td>
                                </tr>

                               <?php  } ?>
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
                                        
                            </tr>
                            <!-- <tr>
                                <td>
                                    <div class="animation_image" style="display:none" align="center">
                                        <img src="images/130.gif">
                                    </div>
                                </td>
                            </tr> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  $(document).on("change","#enrollment_orders_his_custom_date",function(){
      if ($(this).val() == "Range"){
         $('#enrollment_orders_his_range').show(); 
      } else{
         $('#enrollment_orders_his_range').hide(); 
      }
  });

  $(document).ready(function () {
        $("#enrollment_orders_his_fromdate").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });

    });

    $(document).ready(function () {
        $("#enrollment_orders_his_todate").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });
    });

</script>
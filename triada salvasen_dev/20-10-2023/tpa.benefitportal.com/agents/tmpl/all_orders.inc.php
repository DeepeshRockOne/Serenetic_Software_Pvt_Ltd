<?php if ($is_ajaxed) { ?>
  <!-- Order summary code start -->
   <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-10">
        <h4 class="m-t-0">Order Summary</h4>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th>All Orders</th>
              <th>New Business</th>
              <th>Renewals</th>
              <th>Declined</th>
              <th>Reversed</th>
              <th width="150px" class="bg-warning text-black">Post Payment</th>
              <th width="150px" class="bg-warning text-black">Pending Settlement</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($allCcOdrAmt)?> / ACH: <?=displayAmount($allAchOdrAmt)?></span>" data-html="true">
                    <?=$allOdrCnt?>/<span><?=displayAmount($allOdrAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($nbCcOdrAmt)?> / ACH: <?=displayAmount($nbAchOdrAmt)?></span>" data-html="true">
                    <?=$nbOdrCnt?>/<span><?=displayAmount($nbOdrAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($renewCcOdrAmt)?> / ACH: <?=displayAmount($renewAchOdrAmt)?></span>" data-html="true">
                    <?=$renewOdrCnt?>/<span><?=displayAmount($renewOdrAmt)?></span>
                </a>
              </td>
              <td>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($decCcOdrAmt)?> / ACH: <?=displayAmount($decAchOdrAmt)?></span>" data-html="true">
                    <?=$decOdrCnt?>/<span><?=displayAmount($decOdrAmt)?></span>
                </a>
              </td>
              <td>
                <?php
                $ccTitle = $revCcOdrAmt > 0 ? "<span class='text-red'>(".displayAmount($revCcOdrAmt).")</span>" : displayAmount($revCcOdrAmt);
                $achTitle = $revAchOdrAmt > 0 ? "<span class='text-red'>(".displayAmount($revAchOdrAmt).")</span>" : displayAmount($revAchOdrAmt);
                $revOdrTotal = $revOdrAmt > 0 ? "<span class='text-red'>(".displayAmount($revOdrAmt).")</span>" : "<span>".displayAmount($revOdrAmt)."</span>";

                ?>

                
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=$ccTitle?> / ACH: <?=$achTitle?></span>" data-html="true">
                    <?=$revOdrCnt?>/<?=$revOdrTotal?>
                </a>
              </td>
               <td class="text-center">
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($postCcOdrAmt)?> / ACH: <?=displayAmount($postAchOdrAmt)?></span>" data-html="true">
                    <?=$postOdrCnt?>/<span><?=displayAmount($postOdrAmt)?></span>
                </a>
              </td>
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-container="body" 
                title="<span class='text-blue'>CC: <?=displayAmount($pendCcOdrAmt)?> / ACH: <?=displayAmount($pendAchOdrAmt)?></span>" data-html="true">
                    <?=$pendOdrCnt?>/<span><?=displayAmount($pendOdrAmt)?></span>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Order summary code ends -->
  <!-- Order List Code Start -->
   <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-15">
        <div class="pull-left">
        <h4 class="">Recent Activity</h4>
        </div>
        <?php if ($total_rows > 0) {?>
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);orderSubmit();">
                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
              </select>
            </div>
          </div>
        </div>
        <?php }?>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th>ID/Added Date</th>
              <th>Member Name/ID</th>
              <th>Enrolling Agent/ID</th>
              <th >Sale Type</th>
              <th >Status</th>
              <th class="text-center" width="150px">Post Date</th>
              <th class="text-right">Order Total</th>
              <th width="50px" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $order) { 
              $orderStatus = checkIsset($order['odrStatus']);
              $amtTxtClass = "";
              if(in_array($orderStatus, array("Post Payment","Pending Settlement"))){
                $amtTxtClass = "text-warning";
              }else if(in_array($orderStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
                $amtTxtClass = "text-action";
              }

              $attempt_order = $checkOrderChargeOptions = $regenerate_order = $allowedProcessed = $is_regenerate = false;

              if(in_array($order['odrStatus'], array("Cancelled", "Payment Declined","Refund", "Void","Payment Returned"))){
                  $allowedProcessed = getAllowedProcessedMain($order["odrID"]);
              }
              if(in_array($order['odrStatus'], array("Refund", "Void","Payment Returned"))){
                  $is_regenerate = true;
                  $checkOrderChargeOptions = $enrollDate->checkOrderChargeOptions($order['odrID'],$is_regenerate);
                  $regenerate_order = check_order_can_regenerate_or_not($order['odrID']);
              }
              if(in_array($order['odrStatus'], array("Cancelled", "Payment Declined","Payment Returned"))){
                  $attempt_order = check_order_can_attempt_again_or_not($order['odrID']);
              }
              $statusArr = checkIsset($updOrderStatusOptions[$orderStatus],'arr');
            ?>
            <tr>
              <td><a href="javascript:void(0);" class="text-red fw500 odrReceipt" data-odrId="<?=md5($order["odrID"])?>"><?=$order['odrDispId']?></a><br><?=date("m/d/Y",strtotime($order['odrDate']))?></td>
              <td>
                <?php if($order["mbrStatus"] == "Post Payment"){ ?>
                  <?=$order['leadName']?><br> <a href="lead_details.php?id=<?=md5($order['leadId'])?>" target="_blank" class="text-red fw500"><?=$order['leadDispId']?></a>
                <?php }else{ ?>
                    <?php if($order["user_type"] == "Group"){ ?>
                        <?=$order['mbrName']?><br> <a href="javascript:void(0);" class="text-red fw500"><?=$order['mbrDispId']?></a>
                    <?php } else { ?>
                        <?=$order['mbrName']?><br> <a href="members_details.php?id=<?=md5($order['mbrId'])?>" target="_blank" class="text-red fw500"><?=$order['mbrDispId']?></a>
                    <?php } ?>
                <?php } ?>
              </td>
              <td><?=$order['agentDispId']?><br/><a href="javascript:void(0);" class="text-red fw500"><?=$order['agentName']?></a></td>
              <td><?=get_sale_type_by_is_renewal($order['saleType'])?></td>
              <td><?=$orderStatus?></td>
              <td class="text-center"><?php echo $order['isPostOrder'] == "Y" && strtotime($order["postDate"]) > 0 ? date("m/d/Y",strtotime($order["postDate"])) : "-";?></td>
              <td class="<?=$amtTxtClass?> text-right">
                <?php 
                  if(in_array($orderStatus, array("Refund","Void","Chargeback","Payment Returned"))){
                    echo "(".displayAmount($order["odrTotal"],2).")";
                  } else {
                    echo displayAmount($order["odrTotal"],2);
                  }
                ?>
                </td>
                <td class="icons text-center"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Receipt" class="odrReceipt" data-odrId="<?=md5($order['odrID'])?>"><i class="fa fa-file-text" aria-hidden="true"></i></a>
              </td>
            </tr>
            <?php }?>
            <?php } else {?>
            <tr>
              <td colspan="8" align="center">No record(s) found</td>
            </tr>
            <?php }?>
          </tbody>
          <?php if ($total_rows > 0) { ?>
          <tfoot>
          <tr>
            <td colspan="8">
              <?php echo $paginate->links_html; ?>
            </td>
          </tr>
          </tfoot>
          <?php } ?>
        </table>
      </div>
    </div>
     </div>
  <!-- Order List Code ends -->
<?php }else{ ?>
<div class="container m-t-30">
  <div class="panel panel-default panel-block panel-title-block advance_info_div">
    <div class="panel-body ">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-90">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="70px">
        </div>
        <div class="phone-addon text-left lato_font">Retrieve information on specific orders using the search fields below or view everything by clicking the All Orders button. By default, only today’s orders are displayed.
          <div class="clearfix m-t-15">
            <a href="javascript:void(0);" class="btn btn-info" id="viewAllOdr">All Orders</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-block panel-title-block">
    <form id="orderFrm" action="all_orders.php" method="GET" class="theme-form">
      <input type="hidden" name="viewOdr" id="viewOdr" value="<?=checkIsset($viewOdr)?>">
      <div class="panel-left">
        <div class="panel-left-nav">
          <ul>
            <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
          </ul>
        </div>
      </div>
      <div class="panel-right">
        <div class="panel-heading">
          <div class="panel-search-title">
            <span class="clr-light-blk">SEARCH</span>
          </div>
        </div>
        <div class="panel-wrapper collapse in">
          <div class="panel-body theme-form">
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <input type="text" name="orderIds" value="<?=checkIsset($orderIds)?>" class="form-control listing_search">
                  <label>Order ID</label>
                </div>
              </div>

              <div class="col-md-6 col-sm-12">
                <div class="row">
                  <div id="date_range" class="col-md-12 col-sm-12">
                    <div class="form-group">
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
                  <div class="select_date_div col-md-9 col-sm-12" style="display:none">
                    <div class="form-group">
                      <div id="all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="added_date" id="added_date" class="form-control date_picker" />
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

              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="mbrName" class="form-control listing_search" value="<?=checkIsset($mbrName)?>">
                  <label>Member/Lead Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="mbrIds" class="form-control listing_search" value="<?=checkIsset($mbrIds)?>">
                  <label>Member/Lead ID</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search mulSelect" name="enrollingAgents[]" id="enrollingAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id']. ' - ' .$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Enrolling Agent ID</label>
                </div>
              </div>
              <?php echo getAgencySelect('treeAgents',$_SESSION['agents']['id'],'Agent'); /*<div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search mulSelect" name="treeAgents[]" id="treeAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id']. ' - ' .$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Tree ID</label>
                </div>
              </div>*/ ?>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="se_multiple_select listing_search mulSelect" name="paymentType[]" id="paymentTypeSel" multiple="multiple">
                    <option value="CC">Credit Card</option>
                    <option value="ACH">ACH</option>
                  </select>
                  <label>Payment Type</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="se_multiple_select listing_search mulSelect" name="saleType[]" id="saleTypeSel" multiple="multiple">
                    <option value="N">New Business</option>
                    <option value="Y">Renewal</option>
                    <option value="L">List Bill</option>
                  </select>
                  <label>Sale Type</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <input type="text" name="lastCcAchNo" value="<?=checkIsset($lastCcAchNo)?>" class="form-control listing_search" id="lastCcAchNo" maxlength="4" onkeypress="return isNumberKey(event)">
                  <label>Last 4 Payment Method (CC/ACH)</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <div class="group_select">
                    <select name="products[]" class="products listing_search se_multiple_select" multiple="multiple" id="products">
                        <?php if(!empty($companyArr) && count($companyArr) > 0) {
                          foreach ($companyArr as $key => $company) { ?>
                            <optgroup label="<?= $key ?>">
                              <?php foreach ($company as $pkey => $row) { ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name']?></option>
                              <?php } ?>
                            </optgroup>
                          <?php } 
                        } ?>
                      </select>
                    <label>Products</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group ">
                    <select class="se_multiple_select listing_search mulSelect" name="odrStatus[]" id="odrStatus" multiple="multiple">
                      <option value="Cancelled">Cancelled</option>
                      <option value="Chargeback">Chargeback</option>
                      <option value="Pending Settlement">Pending Settlement</option>
                      <option value="Payment Approved">Payment Approved</option>
                      <option value="Payment Declined">Payment Declined</option>
                      <option value="Payment Returned">Payment Returned</option>
                      <option value="Post Payment">Post Payment</option>
                      <option value="Refund">Refund</option>
                      <option value="Void">Void</option>
                    </select>
                    <label>Status</label>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="row">
                  <div id="post_date_range" class="col-md-12 col-sm-12">
                    <div class="form-group">
                      <select class="form-control" id="post_join_range" name="post_join_range">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Post Date</label>
                    </div>
                  </div>
                  <div class="post_select_date_div col-md-9 col-sm-12" style="display:none">
                    <div class="form-group">
                      <div id="post_all_join" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="post_added_date" id="post_added_date" class="form-control post_date_picker" />
                      </div>
                      <div  id="post_range_join" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="post_fromdate" id="post_fromdate" value="" class="form-control post_date_picker" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="post_todate" id="post_todate" value="" class="form-control post_date_picker" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer clearfix">
              <button type="submit" class="btn btn-info" name="" id="" > <i class="fa fa-search"></i> Search </button>
              <button type="button" class="btn btn-info btn-outline" name="" id="viewAllBtn" onclick="window.location.href='all_orders.php?viewOdr=allOdr'"> <i class="fa fa-search-plus"></i> View All </button>
              <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
              <input type="hidden" name="export_val" id="export_val" value="">
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
              <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
              <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
              <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
            </div>
          </div>
        </div>
      </div>
      <div class="search-handle">
        <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
      </div>
  </div>
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data"></div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    dropdown_pagination('ajax_data')
    orderSubmit();
    $(".mulSelect,#treeAgents").multipleSelect({
      selectAll: false,
    });

    $("#products").multipleSelect({
      selectAll: true,
    });

    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $(".post_date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $(document).off('click', '#viewAllOdr');
    $(document).on('click', '#viewAllOdr', function(e) {
      $('#viewOdr').val("allOdr");
      $('#join_range').val('').trigger('change');
      $("#added_date").val();
      orderSubmit();
    });

    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if ($(this).val() == '') {
        $('.select_date_div').hide();
        $('#date_range').removeClass('col-md-3').addClass('col-md-12');
      } else {
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

    $(document).off('change', '#post_join_range');
    $(document).on('change', '#post_join_range', function(e) {
      e.preventDefault();
      $('.post_date_picker').val('');
      if ($(this).val() == '') {
        $('.post_select_date_div').hide();
        $('#post_date_range').removeClass('col-md-3').addClass('col-md-12');
      } else {
        $('#post_date_range').removeClass('col-md-12').addClass('col-md-3');
        $('.post_select_date_div').show();
        if ($(this).val() == 'Range') {
          $('#post_range_join').show();
          $('#post_all_join').hide();
        } else {
          $('#post_range_join').hide();
          $('#post_all_join').show();
        }
      }
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      orderSubmit();
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
           fRefresh();
           common_select();
        }
      });
    });
    $(document).off("submit", "#orderFrm");
    $(document).on("submit", "#orderFrm", function(e) {
      e.preventDefault();
      $('#viewOdr').val("allOdr");
      if ($(".listing_search").filter(function() {
          return $(this).val();
        }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
          return $(this).val();
        }).length > 0) || ($("#post_join_range").val() != '' && $(".post_date_picker").filter(function() {
          return $(this).val();
        }).length > 0) ) {
        orderSubmit();
      } else {
        swal('Oops!!', 'Please Enter Data To Search', 'error');
      }
    });

    $(document).off('click', '.odrReceipt');
    $(document).on('click', '.odrReceipt', function(e) {
      var odrId = $(this).attr("data-odrId");
      openOdrReceipt(odrId);
    });

  });

  /* Reprocess Order dropdown option start */
  function Reprocess(id,customer_id) {
      $.colorbox({
        href: '<?=$HOST?>/reprocess_order.php?location=agent&customer_id='+customer_id+'&orderId='+id,
        iframe: true,
        width: '920px',
        height: '600px',
        fastIframe: false,
        onClosed:function(){
          $("#odrActionSel_"+id).val('').change();
        }
      });
    }
  /* Reprocess Order dropdown option end */

  /* Regenerate Order dropdown option start */
  function Regenerate(id,customer_id) {
      $.colorbox({
        href: 'regenerate_order.php?customer_id='+customer_id+'&orderId='+id,
        iframe: true,
        width: '920px',
        height: '600px',
        fastIframe: false,
        onClosed:function(){
          $("#odrActionSel_"+id).val('').change();
        }
      });
    }
  /* Regenerate Order dropdown option end */
  
  openOdrReceipt = function(odrId){
    $href = "order_receipt.php?orderId="+odrId;
    var not_win = window.open($href, "myWindow", "width=1024,height=630");
    if(not_win.closed) {  
      alert('closed');  
    } 
  }

  viewOrder = function(){
    var odrDisplay = $("#viewOdr").val();
    var today = "<?=$added_date?>";
    if(odrDisplay == "todayOdr"){
      $('#join_range').val('Exactly').trigger('change');
      $("#added_date").val(today);
    }
  }

  orderSubmit = function() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#orderFrm').serialize();

    $.ajax({
      url: $('#orderFrm').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        viewOrder();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
        fRefresh();
      }
    });
    return false;
  }

  chargeBackStatus = function(orderId,newStatus) {
    $.colorbox({
      href: 'chargeback_reason.php?orderId='+orderId+'&orderStatus='+newStatus,
      iframe: true,
      width: '650px',
      height: '310px',
      fastIframe: false,
      onClosed:function(){
        $("#odrActionSel_"+orderId).val('').change();
      }
    });
  }

  paymentReturnedStatus = function(orderId,oldStatus,newStatus) {
    swal({
      text: "Change Order Status to " + newStatus +" : Are you sure?" ,
      input: 'textarea',
      inputPlaceholder: 'Reason For Status Change',
      width: 600,
      showCancelButton: true,
      confirmButtonText: "Confirm"
    }).then(function(e) {
      reason = e;
      $.ajax({
        url: 'ajax_change_order_status.php',
        data: {
          orderId: orderId,
          reason: reason,
          oldStatus: oldStatus,
          newStatus: newStatus
        },
        method: 'POST',
        dataType: 'json',
        success: function(res) {
          if (res.status == "success") {
            setTimeout(function() {
              window.location.href = 'all_orders.php';
            }, 1000);
            setNotifySuccess("Order status updated successfully...");
          } else {
            setTimeout(function() {
              window.location.reload();
            }, 1000);
            parent.setNotifyError(res.msg);
          }
        }
      });
    });
  }

  updateOrderStatus = function(orderId,oldStatus,newStatus){
    swal({
      text: "Change Order Status to " + newStatus +" : Are you sure?" ,
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
       $('#ajax_loader').show();
      $.ajax({
        url: 'ajax_change_order_status.php',
        data: {
          orderId: orderId,
          oldStatus: oldStatus,
          newStatus: newStatus
        },
        method: 'POST',
        dataType: 'json',
        success: function(res) {
           $('#ajax_loader').hide();
          if (res.status == "success") {
            setTimeout(function() {
              window.location.href = 'all_orders.php';
            }, 1000);
            setNotifySuccess("Order status updated successfully...");
          }else{
            if(typeof(res.status) !== 'undefined'){
              setNotifyError(res.msg);
            }
          }
        }
      });
    }, function(dismiss) {
      window.location.reload();
    });
  }

  function isNumberKey(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)){
          return false;
      }
      return true;
  }

  $(document).off('click', '#export');
  $(document).on('click', '#export', function(e) {
      confirm_export_data(function() {
          $("#export_val").val('order_export');
          $('#ajax_loader').show();
          $('#is_ajaxed').val('1');
          var params = $('#orderFrm').serialize();
          $.ajax({
              url: $('#orderFrm').attr('action'),
              type: 'GET',
              data: params,
              dataType: 'json',
              success: function(res) {
                  $('#ajax_loader').hide();
                  $("#export_val").val('');
                  if(res.status == "success") {
                    confirm_view_export_request(true,'agent');
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
  });

 
  </script>
  <?php } ?>
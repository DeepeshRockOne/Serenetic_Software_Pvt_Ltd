<?php if ($is_ajaxed) { ?>
  <!-- Order List Code Start -->
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-10">
        <h4 class="m-t-0 pull-left">Activity</h4>
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
              <th class="text-center">Plan Period</th>
              <th >Status</th>
              <th >Order Total</th>
              <th class="text-center">Alerts</th>
              <th width="180px">Actions</th>
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

              $attempt_order = $checkOrderChargeOptions = $regenerate_order = $allowedProcessed = $is_regenerate = $allowVoid = $allowMakePayment = false;

              if(in_array($order['odrStatus'],array("Payment Declined")) && $order['is_renewal'] = 'Y'){
                $allowMakePayment = allowMakePayment($order["odrID"]);
              }
              if(in_array($order['odrStatus'],array("Pending Settlement"))){
                $allowVoid = is_ach_voidable($order["odrID"]);
              }
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
              <td><a href="javascript:void(0);" class="text-red fw500 member_product_popover odrReceipt" data-id="<?=$order['odrID']?>" data-odrId="<?=md5($order["odrID"])?>"><?=$order['odrDispId']?></a><br><?=date("m/d/Y",strtotime($order['odrDate']))?>
                <div id="popover_content_wrapper_<?php echo $order['odrID']; ?>" style="display: none">
                </div>
              </td>
              <?php if($module_access_type == "rw") { ?>
              <td>
                <?php if($order["mbrStatus"] == "Post Payment"){ ?>
                  <?=$order['leadName']?><br> <a href="lead_details.php?id=<?=md5($order['leadId'])?>" target="_blank" class="text-red fw500"><?=$order['leadDispId']?></a>
                <?php }else{ ?>
                    <?php if($order["user_type"] == "Group"){ ?>
                        <?=$order['mbrName']?><br> <a href="groups_details.php?id=<?=md5($order['mbrId'])?>" target="_blank" class="text-red fw500"><?=$order['mbrDispId']?></a>
                    <?php } else { ?>
                        <?=$order['mbrName']?><br> <a href="members_details.php?id=<?=md5($order['mbrId'])?>" target="_blank" class="text-red fw500"><?=$order['mbrDispId']?></a>
                    <?php } ?>
                <?php } ?>
              </td>
              <?php } else { ?>
              <td>
                <?php if($order["mbrStatus"] == "Post Payment"){ ?>
                <?=$order['leadName']?><br> <a href="javascript:void(0);" class="text-red fw500"><?=$order['leadDispId']?></a>
                <?php }else{ ?>
                <?=$order['mbrName']?><br> <a href="javascript:void(0);" class="text-red fw500"><?=$order['mbrDispId']?></a>
                <?php } ?>
              </td>
              <?php } ?>
              <?php if($module_access_type == "rw") { ?>
                <td><?=$order['agentDispId']?><br> <a href="agent_detail_v1.php?id=<?=md5($order['agentId'])?>" target="_blank" class="text-red fw500"><?=$order['agentName']?></a></td>
              <?php } else { ?>
                <td><?=$order['agentDispId']?><br/><a href="javascript:void(0);" class="text-red fw500"><?=$order['agentName']?></a></td>
              <?php } ?>
              
              <td><?=get_sale_type_by_is_renewal($order['saleType'])?></td>
              <td class="text-center">
                <?php if($order['saleType'] != 'L'){
                  if($order["minCov"] != $order["maxCov"]){
                    echo "P".$order["minCov"]." +";
                  }else{
                    echo "P".$order["minCov"];
                  }
                } ?>
              </td>  
              <td>
                <?php 
                  if($order["is_list_bill_order"] == "Y") {
                    $statusArr = array(); 
                  }
                  if($module_access_type == "rw" && !empty($statusArr)){ 
                ?>
                  <div class="theme-form pr w-200">
                    <select class="form-control updOdrStatus " data-orderId="<?=md5($order["odrID"])?>" data-oldStatus="<?=$orderStatus?>">
                  <?php foreach ($statusArr as $key => $status) { ?>
                    <option value="<?=$status?>"><?=$status?></option>
                  <?php } ?>
                    </select>
                  <label>Status</label>
                </div>
                <?php
                  }else{
                ?>
                <?=$orderStatus?>
                <?php    
                  }
                ?>
              </td>
              <td class="<?=$amtTxtClass?>">
                <?php 
                  if(in_array($orderStatus, array("Refund","Void","Chargeback","Payment Returned"))){
                    echo "(".displayAmount($order["odrTotal"],2).")";
                  }else{
                    echo displayAmount($order["odrTotal"],2);
                  }
                ?>
              </td>
              <td class="text-center fs18">
                <?php if($orderStatus == "Payment Approved"){ ?>
                  <i class="fa fa-check-circle text-success fa-lg" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Successful"></i>
                <?php }else{ ?>
                  <?php 
                    if($orderStatus == "Payment Declined"){
                      $order['orderNote'] = get_declined_reason_from_tran_response($order['processorResponse'],false);
                    }
                  ?>
                  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="<?=checkIsset($order['orderNote'])?>"><i class="fa fa-exclamation-circle fa-lg"></i></a>
                <?php } ?>
              </td>
              <td>
                <div class="theme-form pr w-160">
                  <select class="form-control odrActionSel" id="odrActionSel_<?=md5($order["odrID"])?>" data-customer_id="<?=md5($order["odrMbrId"])?>" data-orderId="<?=md5($order["odrID"])?>" data-oldStatus="<?=$orderStatus?>">
                    <option value=""></option>
                    <option value="odrReceipt">Receipt</option>
                    <?php if($module_access_type == "rw" && $order["is_list_bill_order"] == "N") { ?>
                        <?php if($attempt_order && $allowedProcessed){ ?>
                            <option value="Reprocess">Reprocess Order</option>
                        <?php } ?>
                        <?php if($is_regenerate && $checkOrderChargeOptions && $allowedProcessed && $regenerate_order){ ?>
                            <option value="Regenerate">Regenerate Order</option>
                        <?php } ?>
                        <?php if($orderStatus == "Post Payment"){ ?>
                          <option value="editPostDate">Edit Post Date</option>
                        <?php } ?>

                        <?php if($orderStatus == "Payment Approved"){ ?>
                          <option value="reverseOrder">Reverse Order</option>
                        <?php } ?>
                        <?php if($orderStatus == "Pending Settlement" && $allowVoid){ ?>
                          <option value="reverseOrder">Reverse Order</option>
                        <?php } ?>
                        <?php if($orderStatus == "Payment Declined" && $allowMakePayment){ ?>
                          <option value="makepayment">Make Payment</option>
                        <?php } ?>
                    <?php } ?>
                     
                  </select>
                  <label>Select</label>
                </div>
              </td>
            </tr>
            <?php }?>
            <?php } else {?>
            <tr>
              <td colspan="9" align="center">No record(s) found</td>
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
  <script type="text/javascript">
    contentCalled = false;
    $('.member_product_popover').popover({
      html: true,
      container: 'body',
      trigger: 'manual',
      template: '<div class="popover"><div class="arrow"></div><div class="popover-content"></div></div>',
      placement: 'top',
      content: function() {
        var id_val = $(this).attr('data-id');
        getPopoverData(id_val)
        return $('#popover_content_wrapper_' + id_val).html();
      }
    }).on("mouseenter", function() {
      var _this = this;
      $(this).popover("show");
      $(".popover").on("mouseleave", function() {
        $(_this).popover('hide');
      });
    }).on("mouseleave", function() {
      var _this = this;
      setTimeout(function() {
        if (!$(".popover:hover").length) {
          $(_this).popover("hide");
        }
      }, 300);
    });

    function getPopoverData(id_val) {
      if (!contentCalled) {
        contentCalled = true;
        return " ";
      } else {
        $.ajax({
          url: 'get_orders_transactions_popover.php',
          data: {
            id: id_val
          },
          method: 'GET',
          async: false,
          success: function(res) {
            contentCalled = false;              
            $('#popover_content_wrapper_' + id_val).html(res);
          }
        });
      }
    }

    $('body').on('click', function (e) {
        $('.member_product_popover').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    $(document).off("click",".commission_popup");
    $(document).on("click",".commission_popup",function(){
      $ord_id = $(this).data('id');
      $.colorbox({
            href: "payable_popup.php?order_id=" + $ord_id,
            iframe: true,
            width: '1080px', 
            height: '600px',
            trapFocus:false,
            onClosed:function(){
              // $("#odrActionSel_"+orderId).val('').change();
            }
        });
    });
  </script>
  <!-- Order List Code ends -->
<?php }elseif($is_ajaxed_count){ ?>
  <div class="table-responsive" id="countData">
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
  <!-- Order summary code ends -->
<?php } else { ?>
  <div class="panel panel-default panel-block panel-title-block advance_info_div">
    <div class="panel-body ">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-90">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" width="50px" height="70px">
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
              <div class="col-md-6">
                <div class="form-group height_auto">
                  <input name="orderIds" id="orderIds" class="listing_search" type="text" value="<?= checkIsset($orderIds) ?>"/>
                  <label>Order ID(s)</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="row">
                  <div id="date_range" class="col-md-12">
                    <div class="form-group">
                      <select class="form-control" id="join_range" name="join_range">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Date</label>
                    </div>
                  </div>
                  <div class="select_date_div col-md-9" style="display:none">
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

              <?php /*<div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="mbrName" class="form-control listing_search" value="<?=checkIsset($mbrName)?>">
                  <label>Member Name</label>
                </div>
              </div>*/ ?>
              <div class="clearfix"></div>
              <div class="col-sm-6">
                <div class="form-group height_auto">
                  <input name="mbrIds" id="mbrIds" type="text" class="listing_search" value="<?= checkIsset($mbrIds) ?>"/>
                  <label>Member ID/Name(s)</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group height_auto">
                  <input name="enrollingAgents" id="enrollingAgents" type="text" class="listing_search" value="<?= checkIsset($enrollingAgents) ?>"/>
                  <label>Enrolling Agent ID/Name(s)</label>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-sm-6">
                  <div class="form-group height_auto">
                      <input name="treeAgents" id="treeAgents" type="text" class="listing_search" value="<?= checkIsset($treeAgents) ?>" />
                      <label>Agency</label>
                  </div>
              </div>
              <?php /*echo getAgencySelect('treeAgents');
              <div class="col-sm-6">
                <div class="form-group height_auto">
                  <input name="treeAgents" id="treeAgents" type="text" class="listing_search" value="<?= checkIsset($treeAgents) ?>"/>
                  <label>Tree Agent ID/Name(s)</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group height_auto">
                  <select class="se_multiple_select listing_search " name="enrollingAgents[]" id="enrollingAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id']. ' - ' .$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Enrolling Agent ID</label>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search " name="treeAgents[]" id="treeAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id']. ' - ' .$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Tree ID</label>
                </div>
              </div> */?>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="se_multiple_select listing_search " name="paymentType[]" id="paymentTypeSel" multiple="multiple">
                    <option value="CC">Credit Card</option>
                    <option value="ACH">ACH</option>
                  </select>
                  <label>Payment Type</label>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="se_multiple_select listing_search " name="saleType[]" id="saleTypeSel" multiple="multiple">
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
                    <input type="text" name="products" value="<?=checkIsset($products)?>" class="listing_search selectized" id="products">
                    <label>Products</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                    <select class="se_multiple_select listing_search " name="odrStatus[]" id="odrStatus" multiple="multiple">
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
            </div>
            <div class="panel-footer clearfix">
              <button type="submit" class="btn btn-info" name="" id="" > <i class="fa fa-search"></i> Search </button>
              <button type="button" class="btn btn-info btn-outline" name="" id="viewAllBtn" onclick="window.location.href='all_orders.php?viewOdr=allOdr'"> <i class="fa fa-search-plus"></i> View All </button>
              <?php if($module_access_type == "rw") { ?>
              <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
              <?php } ?>
              <input type="hidden" name="export_val" id="export_val" value="">
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
              <input type="hidden" name="is_ajaxed_count" id="is_ajaxed_count" value="0"/>
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

<!-- Order summary code start -->
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix m-b-10">
        <h4 class="m-t-0">Order Summary</h4>
      </div>
      <div id="ajax_data_count"></div>
      <div class="dashboard_loader" id="ajax_loader_count" style="display: none;">
        <div class="ajex_loader" ></div>
      </div>
    </div>
  </div>
  <div id="ajax_data_table"></div>
  <div id="ajax_loader_table" class="ajex_loader" style="display: none;">
    <div class="loader"></div>
  </div>

<script type="text/javascript">
  $(document).ready(function() {
    var execute=function(){
    fRefresh();
    }
    dropdown_pagination(execute,'ajax_data_table');
    orderSubmit();

    $('#orderIds').selectize({
      plugins: ['remove_button'],
      persist: false,
      createOnBlur:true,
      create: true
      
    });
    initSelectize('mbrIds','MemberID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
    initSelectize('enrollingAgents','AgentID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
    initSelectize('products','agentProductsID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
    initSelectize('treeAgents','treeAgentID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
    // initSelectize('treeAgents','AgentID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);

    $(".se_multiple_select").multipleSelect({
      selectAll: false,
    });

    $(".date_picker").datepicker({
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
    $(document).off('click', '#ajax_data_table tr.data-head a');
    $(document).on('click', '#ajax_data_table tr.data-head a', function(e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      orderSubmit();
    });
    $(document).off('click', '#ajax_data_table ul.pagination li a');
    $(document).on('click', '#ajax_data_table ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader_table').show();
      $('#ajax_data_table').hide();
      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function(res) {
          $('#ajax_loader_table').hide();
          $('#ajax_data_table').html(res).show();

          $('[data-toggle="tooltip"]').tooltip();
          common_select();
          fRefresh();
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
        }).length > 0)) {
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

    $(document).off('change', '.updOdrStatus');
    $(document).on('change', '.updOdrStatus', function(e) {
      e.stopPropagation();
      var orderId = $(this).attr('data-orderId');
      var oldStatus = $(this).attr('data-oldStatus');
      var newStatus = $(this).val();
      if (newStatus == "Chargeback" || newStatus == "Payment Returned") {
        $href= "add_payment_reversal.php?orderId="+orderId;
        window.open($href,"_blank");
      } else {
        updateOrderStatus(orderId,oldStatus,newStatus);
      }
    });

    $(document).off('change', '.odrActionSel');
    $(document).on('change', '.odrActionSel', function(e) {
      e.stopPropagation();
      var action = $(this).val();
      var orderId = $(this).attr("data-orderId");
      var oldStatus = $(this).attr("data-oldStatus");
      var customer_id = $(this).attr("data-customer_id");
     $(".odrActionSel option[value='']").attr('selected', true)
      if(action == "odrReceipt"){
        $(this).val("");
        $(this).selectpicker("refresh");
        openOdrReceipt(orderId);
        fRefresh();
      }else if (action == 'Reprocess') {
        Reprocess(orderId,customer_id);
      } else if (action == 'Regenerate') {
        Regenerate(orderId,customer_id);
      }else if (action == "editPostDate"){
        $href= "<?=$HOST?>/edit_order_post_date.php?location=admin&orderId="+orderId;
        $.colorbox({
            href: $href,
            iframe: true,
            width: '500px', 
            height: '400px',
            trapFocus:false,
            onClosed:function(){
              $("#odrActionSel_"+orderId).val('').change();
            }
        });
      }else if(action == "reverseOrder"){
        $href= "add_payment_reversal.php?orderId="+orderId;
        window.open($href,"_blank");
      }else if (action == "makepayment"){
        $href= "<?=$HOST?>/make_payment.php?location=admin&id="+customer_id;
        $.colorbox({
            href: $href,
            iframe: true,
            width: '768px',
            height: '530px',
            trapFocus:false,
            onClosed:function(){
              $("#odrActionSel_"+orderId).val('').change();
            }
        });
      }
    });

    $(document).off('click', '#export');
    $(document).on('click', '#export', function(e) {
        confirm_export_data(function() {
            $("#export_val").val('order_export');
            $('#ajax_loader_table').show();
            $('#is_ajaxed').val('1');
            var params = $('#orderFrm').serialize();
            $.ajax({
                url: $('#orderFrm').attr('action'),
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(res) {
                    $('#ajax_loader_table').hide();
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

  });

  /* Reprocess Order dropdown option start */
  function Reprocess(id,customer_id) {
      $.colorbox({
        href: '<?=$HOST?>/reprocess_order.php?customer_id='+customer_id+'&orderId='+id,
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
    $('#ajax_loader_table').show();
    $('#ajax_data_table').hide();
    $('#is_ajaxed').val('1');
    $('#is_ajaxed_count').val('0');
    var params = $('#orderFrm').serialize();

    $.ajax({
      url: $('#orderFrm').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader_table').hide();
        $('#ajax_data_table').html(res).show();
        orderSubmitCount();
        viewOrder();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
        fRefresh();
      }
    });
    return false;
  }

  orderSubmitCount = function() {
    $('#ajax_loader_count').show();
    $('#ajax_data_count').hide();
    $('#is_ajaxed').val('0');
    $('#is_ajaxed_count').val('1');
    var params = $('#orderFrm').serialize();

    $.ajax({
      url: $('#orderFrm').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
      $('#ajax_loader_count').hide();
        $('#ajax_data_count').html(res).show();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    return false;
  }
  
  updateOrderStatus = function(orderId,oldStatus,newStatus){
    swal({
      text: "<br>Change Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
       $('#ajax_loader_table').show();
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
           $('#ajax_loader_table').hide();
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



 
  </script>
  <?php } ?>
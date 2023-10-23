<div class="container m-t-30">
<?php if ($is_ajaxed) { ?>
  <!-- Transaction List Code Start -->
    <div class="panel-body">
      <div class="clearfix tbl_filter m-b-10">
        <div class="pull-left">
          <h4 class="m-t-7">Recent Activity</h4>
        </div>
        <?php if ($total_rows > 0) {?>
        <div class="pull-right">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);transSubmit();">
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
              <th>Order ID/Added Date</th>
              <th>Member Name/ID</th>
              <th>Enrolling Agent/ID</th>
              <th>Sale Type</th>
              <th>Status/Transaction ID</th>
              <th class="text-center">Plan Period</th>
              <th>Order Total</th>
              <th class="text-center">Alerts</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $transRow) { 
              $transStatus = checkIsset($transRow['transStatus']);
              $amtTxtClass = "";
              if(in_array($transStatus, array("Post Payment","Pending Settlement"))){
                $amtTxtClass = "text-warning";
              }else if(in_array($transStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
                $amtTxtClass = "text-action";
              }
              $link = $transRow['sponsorType'] == 'Group' ? 'groups_details.php?id='.md5($transRow['agentId']) : $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($transRow['agentId']);
              $link = 'javascript:void(0);';
            ?>
              <tr>
              <td><a href="javascript:void(0);" data-href="transaction_receipt.php?transId=<?=md5($transRow["transTblId"])?>" class="text-red fw500 transReceipt" data-original-title="Receipt"><?=$transRow['odrDispId']?></a><br><?=date("m/d/Y",strtotime($transRow['transDate']))?></td>
              <td>
                <?php if($transRow["mbrStatus"] == "Post Payment"){ ?>
                  <?=$transRow['leadName']?><br> <a href="lead_details.php?id=<?=md5($transRow['leadId'])?>" target="_blank" class="text-red fw500"><?=$transRow['leadDispId']?></a>
                <?php }else{ ?>
                    <?php if($transRow["user_type"] == "Group"){ ?>
                        <?=$transRow['mbrName']?><br> <a href="javascript:void(0);" class="text-red fw500"><?=$transRow['mbrDispId']?></a>
                    <?php } else { ?>
                        <?=$transRow['mbrName']?><br> <a href="members_details.php?id=<?=md5($transRow['mbrId'])?>" target="_blank" class="text-red fw500"><?=$transRow['mbrDispId']?></a>
                    <?php } ?>
                <?php } ?>
              </td>
              <td><?=$transRow['agentDispId']?><br> <a href="javascript:void(0);" class="text-red fw500"><?=$transRow['agentName']?></a></td>
              <td><?=get_sale_type_by_is_renewal($transRow['saleType'])?></td>
              <td ><span class="<?=$amtTxtClass?>"><?=$transStatus?></span><br/><span class="text-action"><strong><?=$transRow["transId"]?></strong></span></td>
              <td class="text-center">
                 <?php if($transRow['saleType'] != 'L'){
                    if($transRow["minCov"] != $transRow["maxCov"]){
                      echo "P".$transRow["minCov"]." +";
                    }else{
                      echo "P".$transRow["minCov"];
                    }
                  } ?>
              </td>
              <td class="<?=$amtTxtClass?>"><?=displayAmount($transRow["odrTotal"],2)?></td>
              <td class="text-center fs18">
                <?php if($transStatus == "Payment Approved"){ ?>
                  <i class="fa fa-check-circle text-success fa-lg" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Successful"></i></td>
                <?php }else{ ?>
                  <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="<?=checkIsset($transRow['transNote'])?>"><i class="fa fa-exclamation-circle fa-lg"></i></a></td>
                <?php } ?>
              <td class="icons text-center">
                <a data-href="transaction_receipt.php?transId=<?=md5($transRow["transTblId"])?>" data-toggle="tooltip" data-placement="top" title="" class="transReceipt" data-original-title="Receipt"><i class="fa fa-file-text"></i></a>
              </td>
            </tr>
            <?php }?>
            <?php } else {?>
            <tr>
              <td colspan="10" align="center">No record(s) found</td>
            </tr>
            <?php }?>
          </tbody>
           <?php if ($total_rows > 0) { ?>
          <tfoot>
          <tr>
            <td colspan="10">
              <?php echo $paginate->links_html; ?>
            </td>
          </tr>
          </tfoot>
          <?php } ?>
        </table>
      </div>
    </div>
  <!-- Transaction List Code Ends -->
<?php }else{ ?>
  <!-- transaction page top details start -->
      <div class="panel panel-default panel-block advance_info_div">
        <div class="panel-body">
          <div class="phone-control-wrap ">
            <div class="phone-addon w-90">
              <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
            </div>
                <div class="phone-addon text-left">
                  <p class="fs14">Retrieve information on all transactions using the search fields or clicking the button below to show all activity. By default only todays transactions are appearing.</p>
                   <a href="javascript:void(0);" class="btn btn-info" onclick="window.location.href='payment_transaction.php?viewTrans=allTrans'">All Transactions</a>
            </div>
          </div>
        </div>
      </div>
   <!-- transaction page top details ends -->
   <!-- transaction page search code start -->

   <!-- transaction page search code ends -->
   <div class="panel panel-default panel-block panel-title-block">
    <form id="transFrm" action="payment_transaction.php" method="GET" class="theme-form">
      <input type="hidden" name="viewTrans" id="viewTrans" value="<?=checkIsset($viewTrans)?>">
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
                <div class="form-group">
                  <input type="text" name="transIds" class="form-control listing_search" value="<?=checkIsset($transIds)?>">
                  <label>Transaction ID</label>
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
                  <label>Member Name</label>
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
                  <select class="se_multiple_select listing_search" name="enrollingAgents[]" id="enrollingAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id'].' - '.$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Enrolling Agent ID</label>
                </div>
              </div>
              <?php echo getAgencySelect('treeAgents',$_SESSION['agents']['id'],'Agent'); /*<div class="col-sm-6">
                <div class="form-group ">
                  <select class="se_multiple_select listing_search" name="treeAgents[]" id="treeAgents" multiple="multiple" >
                    <?php if(!empty($agentRes)){ ?>
                        <?php foreach($agentRes as $value){ ?>
                            <option value="<?=$value['id']?>"><?=$value['rep_id'].' - '.$value['name']?></option>
                        <?php } ?>
                    <?php } ?>
                  </select>
                  <label>Tree ID</label>
                </div>
              </div>*/ ?>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="form-control listing_search" name="paymentType" id="paymentTypeSel">
                    <option></option>
                    <option value="CC">Credit Card</option>
                    <option value="ACH">ACH</option>
                  </select>
                  <label>Payment Type</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <select class="form-control listing_search" name="saleType" id="saleTypeSel">
                    <option></option>
                    <option value="N">New Business</option>
                    <option value="Y">Renewal</option>
                    <option value="L">List Bill</option>
                  </select>
                  <label>Sale Type</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                  <input type="text" name="lastCcAchNo" value="<?=checkIsset($lastCcAchNo)?>" class="form-control listing_search" id="lastCcAchNo">
                  <label>Last 4 Payment Method (CC/ACH)</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                    <select name="products[]" class="se_multiple_select listing_search" multiple="multiple" id="products">
                        <?php if(!empty($companyArr) && count($companyArr) > 0) {
                          foreach ($companyArr as $key => $company) { ?>
                            <optgroup label="<?= $key ?>">
                              <?php foreach ($company as $pkey => $row) { ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name'] . ' (' . $row['product_code'] . ') ' ?></option>
                              <?php } ?>
                            </optgroup>
                          <?php } 
                        } ?>
                      </select>
                    <label>Products</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group ">
                    <select class="se_multiple_select listing_search" name="transStatus[]" id="transStatus" multiple="multiple">
                      <option value="Payment Approved">Payment Approved</option>
                      <option value="Payment Declined">Payment Declined</option>
                      <option value="Chargeback">Chargeback</option>
                      <option value="Payment Returned">Payment Returned</option>
                      <option value="Void">Void</option>
                      <option value="Refund">Refund</option>
                      <option value="Post Payment">Post Payment</option>
                      <option value="Pending Settlement">Pending Settlement</option>
                      <option value="Cancelled">Cancelled</option>
                    </select>
                    <label>Status</label>
                </div>
              </div>
            </div>
            <div class="panel-footer clearfix">
              <button type="submit" class="btn btn-info" name="" id="" > <i class="fa fa-search"></i> Search </button>
              <button type="button" class="btn btn-info btn-outline" name="" id="viewAllBtn" onclick="window.location.href='payment_transaction.php?viewTrans=allTrans'"> <i class="fa fa-search-plus"></i> View All </button>
              <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i>Export </button>
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
  <div class="panel panel-default panel-block">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data"></div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  dropdown_pagination('ajax_data')
  transSubmit();

  $(".se_multiple_select").multipleSelect({
    selectAll: false,
  });
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
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
      $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function(e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      transSubmit();
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
           
            var not_win = '';
            $(".transReceipt").on('click', function() {
              $href = $(this).attr('data-href');
              var not_win = window.open($href, "myWindow", "width=1024,height=630");
              if (not_win.closed) {
                alert('closed');
              }
            });

            $('[data-toggle="tooltip"]').tooltip();
            common_select();
            fRefresh();
        }
      });
    });

     $(document).off("submit", "#transFrm");
    $(document).on("submit", "#transFrm", function(e) {
      e.preventDefault();
      $('#viewTrans').val("allTrans");
      if ($(".listing_search").filter(function() {
          return $(this).val();
        }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
          return $(this).val();
        }).length > 0)) {
        transSubmit();
      } else {
        swal('Oops!!', 'Please Enter Data To Search', 'error');
      }
    });
});

 viewTransaction = function(){
    var transDisplay = $("#viewTrans").val();
    var today = "<?=$added_date?>";
    if(transDisplay == "todayTrans"){
      $('#join_range').val('Exactly').trigger('change');
      $("#added_date").val(today);
    }
  }

  transSubmit = function() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#transFrm').serialize();

    $.ajax({
      url: $('#transFrm').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        
        var not_win = '';
        $(".transReceipt").on('click', function() {
          $href = $(this).attr('data-href');
          var not_win = window.open($href, "myWindow", "width=1024,height=630");
          if (not_win.closed) {
            alert('closed');
          }
        });

        viewTransaction();
        $('[data-toggle="tooltip"]').tooltip();
        common_select();
        fRefresh();
      }
    });
    return false;
  }

  $(document).off('click', '#export');
  $(document).on('click', '#export', function(e) {
      confirm_export_data(function() {
          $("#export_val").val('transaction_export');
          $('#ajax_loader').show();
          $('#is_ajaxed').val('1');
          var params = $('#transFrm').serialize();
          $.ajax({
              url: $('#transFrm').attr('action'),
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


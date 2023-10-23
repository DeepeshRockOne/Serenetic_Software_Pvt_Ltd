<?php if (!empty($_GET["is_ajax"])) { ?>
  <script type="text/javascript">
      getmerchantAccounts('<?=$ADMIN_HOST?>/global_merchant_accounts.php?<?=$_SERVER['QUERY_STRING']?>',"global_merchant_accounts");
      getmerchantAccounts('<?=$ADMIN_HOST?>/variation_merchant_accounts.php?<?=$_SERVER['QUERY_STRING']?>',"variation_merchant_accounts");  
  </script>
<?php } else { ?>
<div id="member_access">
  <div class="panel panel-default panel-block panel-title-block">
    <form id="search_from" action="merchant_processor.php" method="GET" class="sform" autocomplete="off">
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
          <div class="panel-wrapper collapse in">
            <div class="panel-body theme-form">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <select class="se_multiple_select" name="processor_names[]"  id="processor_name" multiple="multiple" >
                      <?php if(!empty($payment_master_res)){
                        foreach ($payment_master_res as $processor) {
                      ?>
                         <option value="<?=$processor["id"]?>"><?=$processor["name"]?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                    <label>Processor(s)</label>
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
                        <label>Added Date</label>
                      </div>
                    </div>
                    <div class="select_date_div col-md-9" style="display:none">
                      <div class="form-group">
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
                <div class="col-sm-6">
                  <div class="form-group">
                    <input type="text" name="processor_mid" class="form-control">
                    <label>MID</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                      <select class="form-control" name="payment_method_type">
                          <option value=""></option>
                          <option value="CC">CC</option>
                          <option value="ACH">ACH</option>
                      </select>
                    <label>Payment</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <select class="se_multiple_select" name="status[]"  id="status" multiple="multiple" >
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Closed">Closed</option>
                    </select>
                    <label>Status</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <select class="se_multiple_select" name="agent_id[]"  id="agent_id" multiple="multiple" >
                      <?php if(!empty($agent_res)): ?>
                        <?php foreach($agent_res as $agent): ?>
                          <option value="<?=$agent['id']?>"><?=$agent['rep_id']?> - <?=$agent['fname'].' '.$agent['lname']?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                    <label>Agent Name/ID</label>
                  </div>
                </div>
              </div>
              <div class="panel-footer clearfix">
                <button type="button" class="btn btn-info" name="search" onclick="getSearchDetails()" > <i class="fa fa-search"></i> Search </button>
                <button type="button" class="btn btn-info btn-outline" name="viewall" onClick="window.location = 'merchant_processor.php'"> <i class="fa fa-search"></i> View All </button>
                <!-- <button type="button" name="" id="" class="btn red-link"> <i class="fa fa-download"></i> Export </button> -->
              </div>
            </div>
          </div>
        </div>
          <input type="hidden" name="is_ajax" id="is_ajax" value="1"/>
    </form>
  </div>
  <div class="white-box" id="global_merchant_accounts">
  </div>
  <div class="white-box" id="variation_merchant_accounts">
  </div>
  <div class="outputData"></div>
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

$(document).ready(function() {
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });
  $("#status,#processor_name").multipleSelect({
       selectAll: false,
  });
  $("#agent_id").multipleSelect({
       selectAll: false,
  });
  $("#products").multipleSelect({
       
  });
  getSearchDetails();
});

$(document).off("click",".transaction_popup");
$(document).on("click",".transaction_popup",function(){
  $processor_id = $(this).attr("id");
  parent.$.colorbox({
    href:'transaction_popup.php?id='+$processor_id, 
    iframe: true,
    width: '800px',
    height: '450px'
    });
});

$(document).off("click",".view_assigned_agents_variation");
$(document).on("click",".view_assigned_agents_variation",function(){
  varition_id = $(this).attr('data-id');
  $.colorbox({href:'variant_assign_processor_popup.php?payment_id=' + varition_id, 
  iframe: true, 
  width: '915px', 
  height: '600px'
  });
}); 

$(document).off("click",".view_assigned_agents");
$(document).on("click",".view_assigned_agents",function(){
  $payment_id = $(this).attr('data-id');
  $.colorbox({
    href:'view_assigned_agents.php?payment_id='+$payment_id, 
    iframe: true, 
    width:'800px',
    height:"625px",
  });
}); 

function getSearchDetails() {
      var params = $('#search_from').serialize();
      $.ajax({
        url: "merchant_processor.php",
        method: "GET",
        data: params,
        beforeSend: function () {
            $("#ajax_loader").show();
        },
        success: function (res) {
          $("#ajax_loader").hide();
          $("#member_access .outputData").html(res);
        }
      });
}

getmerchantAccounts = function(report_url,report_div){
      $('#ajax_loader').show();
      $.ajax({
          url: report_url,
          type: 'GET',
          success: function (res) {
              if(report_div === 'variation_merchant_accounts' )
                setTimeout(function(){ $('#ajax_loader').hide(); }, 2000);
              $('#member_access #'+report_div).html(res);
              $('#ajax_global_data .processor_status').addClass('form-control');
              $('.processor_status').selectpicker('refresh');
          }
      });
  }

</script>
<?php } ?>

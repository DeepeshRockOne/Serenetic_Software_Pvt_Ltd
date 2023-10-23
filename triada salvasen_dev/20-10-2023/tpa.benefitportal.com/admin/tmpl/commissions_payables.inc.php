  <!-- Payment Div Code Start -->
  <form id="paymentFrm" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <div class="white-box">
      <div class="clearfix tbl_filter">
          <div class="pull-left">
          <h4 class="m-t-7">Ready For Payment</h4>
          </div>
          <div class="pull-right">
            <div class="note_search_wrap auto_size" id="search_div_payment" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="search_close_btn_payment">X</a>
                  </div>
                </div>
                <div class="phone-addon">
                     <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="agentId">
                       <label>Agent ID</label>
                     </div>
                </div>
                <div class="phone-addon">
                   <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="agentName">
                       <label>Principal Agent</label>
                   </div>
                </div>
                <div class="phone-addon w-200">
                   <div class="form-group height_auto mn ">
                     <select class="form-control" id="join_range_payment" name="join_range">
                      <option></option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                     </select>
                     <label>Select</label>
                   </div>
                </div>
                <div class="phone-addon">
                  <div class="select_date_div_payment" style="display:none">
                    <div class="form-group height_auto mn">
                      <div id="all_join_payment" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                      </div>
                      <div  id="range_join_payment" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                <label>From</label>
                              </div>
                            </div>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                <label>To</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="loadPaymentDiv();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0)" class="search_btn" id="search_btn_payment"><i class="fa fa-search fa-lg text-blue"></i></a>
          </div>
      </div>
      <div id="paymentDiv"></div>
    </div>
  </form>
  <!-- Payment Div Code Ends -->

  <!-- Paid Div Code Start -->
  <form id="paidFrm" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <div class="white-box">
      <div class="clearfix tbl_filter">
          <div class="pull-left">
          <h4 class="m-t-7">Paid</h4>
          </div>
          <div class="pull-right m-b-15">
            <div class="note_search_wrap auto_size" id="search_div_paid" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="search_close_btn_paid">X</a>
                  </div>
                </div>
                <div class="phone-addon">
                     <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="adminId">
                       <label>Admin ID</label>
                     </div>
                </div>
                <div class="phone-addon">
                   <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="adminName">
                       <label>Admin Name</label>
                   </div>
                </div>
                <div class="phone-addon w-200">
                   <div class="form-group height_auto mn ">
                     <select class="form-control" id="join_range_paid" name="join_range">
                      <option></option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                     </select>
                     <label>Select</label>
                   </div>
                </div>
                <div class="phone-addon">
                  <div class="select_date_div_paid" style="display:none">
                    <div class="form-group height_auto mn">
                      <div id="all_join_paid" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                      </div>
                      <div id="range_join_paid" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                <label>From</label>
                              </div>
                            </div>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                <label>To</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="loadPaidDiv();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0)" class="search_btn" id="search_btn_paid"><i class="fa fa-search fa-lg text-blue"></i></a>
          </div>
      </div>
      <div id="paidDiv"></div>
    </div>
  </form>
  <!-- Paid Div Code Ends -->

  <!-- Wallet Div Code Ends -->
  <form id="walletFrm" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <div class="white-box">
      <div class="clearfix tbl_filter">
          <div class="pull-left">
          <h4 class="m-t-7">Wallet</h4>
          </div>
          <div class="pull-right m-b-15">
            <div class="note_search_wrap auto_size" id="search_div_wallet" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="search_close_btn_wallet">X</a>
                  </div>
                </div>
                <div class="phone-addon">
                     <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="agentId">
                       <label>Agent ID</label>
                     </div>
                </div>
                <div class="phone-addon">
                   <div class="form-group height_auto mn">
                       <input type="text" class="form-control" name="agentName">
                       <label>Principal Agent</label>
                   </div>
                </div>
                <div class="phone-addon w-200">
                   <div class="form-group height_auto mn ">
                     <select class="form-control" id="join_range_wallet" name="join_range">
                      <option></option>
                      <option value="Range">Range</option>
                      <option value="Exactly">Exactly</option>
                      <option value="Before">Before</option>
                      <option value="After">After</option>
                     </select>
                     <label>Select</label>
                   </div>
                </div>
                <div class="phone-addon">
                  <div class="select_date_div_wallet" style="display:none">
                    <div class="form-group height_auto mn">
                      <div id="all_join_wallet" class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" />
                      </div>
                      <div id="range_join_wallet" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                <label>From</label>
                              </div>
                            </div>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <div class="pr">
                                <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                <label>To</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                    <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="loadWalletDiv();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0)" class="search_btn" id="search_btn_wallet"><i class="fa fa-search fa-lg text-blue"></i></a>
          </div>
      </div>
      <div id="walletDiv"></div>
    </div>
  </form>
  <!-- Wallet Div Code Ends -->

<script type="text/javascript">
$(document).ready(function(){
    loadPaymentDiv();
    dropdown_pagination('walletDiv','paidDiv','paymentDiv')


    $(".date_picker").datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true
    });

    $(document).off("click", "#search_btn_payment");
    $(document).on("click", "#search_btn_payment", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div_payment").css('display', 'inline-block');
    });

    $(document).off("click", "#search_close_btn_payment");
    $(document).on("click", "#search_close_btn_payment", function(e) {
      e.preventDefault();
      $("#search_div_payment").hide();
      $("#search_btn_payment").show();
      window.location.reload();
    });

    $(document).off('change', '#join_range_payment');
    $(document).on('change', '#join_range_payment', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if($(this).val() == ''){
        $('.select_date_div_payment').hide();
      }else{
        $('.select_date_div_payment').show();
        if ($(this).val() == 'Range') {
          $('#range_join_payment').show();
          $('#all_join_payment').hide();
        } else {
          $('#range_join_payment').hide();
          $('#all_join_payment').show();
        }
      }
    });

    $(document).off("click", "#search_btn_paid");
    $(document).on("click", "#search_btn_paid", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div_paid").css('display', 'inline-block');
    });

    $(document).off("click", "#search_close_btn_paid");
    $(document).on("click", "#search_close_btn_paid", function(e) {
      e.preventDefault();
      $("#search_div_paid").hide();
      $("#search_btn_paid").show();
      window.location.reload();
    });

    $(document).off('change', '#join_range_paid');
    $(document).on('change', '#join_range_paid', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if($(this).val() == ''){
        $('.select_date_div_paid').hide();
      }else{
        $('.select_date_div_paid').show();
        if ($(this).val() == 'Range') {
          $('#range_join_paid').show();
          $('#all_join_paid').hide();
        } else {
          $('#range_join_paid').hide();
          $('#all_join_paid').show();
        }
      }
    });

    $(document).off("click", "#search_btn_wallet");
    $(document).on("click", "#search_btn_wallet", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div_wallet").css('display', 'inline-block');
    });

    $(document).off("click", "#search_close_btn_wallet");
    $(document).on("click", "#search_close_btn_wallet", function(e) {
      e.preventDefault();
      $("#search_div_wallet").hide();
      $("#search_btn_wallet").show();
      window.location.reload();
    });

    $(document).off('change', '#join_range_wallet');
    $(document).on('change', '#join_range_wallet', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if($(this).val() == ''){
        $('.select_date_div_wallet').hide();
      }else{
        $('.select_date_div_wallet').show();
        if ($(this).val() == 'Range') {
          $('#range_join_wallet').show();
          $('#all_join_wallet').hide();
        } else {
          $('#range_join_wallet').hide();
          $('#all_join_wallet').show();
        }
      }
    });

   $(".commissions_export_pay").colorbox({iframe: true, width: '400px', height: '227px'});
   $(".commission_wallet_history").colorbox({iframe: true, width: '990px', height: '300px'});
});

  loadPaymentDiv = function() {
    $('#paymentDiv').hide();
    var params = $("#paymentFrm").serialize();
    $.ajax({
      url: 'commission_ready_payment.php',
      type: 'GET',
      data: params,
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#paymentDiv').html(res).show();
        common_select();
        loadPaidDiv();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }

  loadPaidDiv = function() {
    $('#paidDiv').hide();
    var params = $("#paidFrm").serialize();
    $.ajax({
      url: 'commission_paid_files.php',
      type: 'GET',
      data: params,
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#paidDiv').html(res).show();
        common_select();
        loadWalletDiv();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }

  loadWalletDiv = function() {
    $('#walletDiv').hide();
    var params = $("#walletFrm").serialize();
    $.ajax({
      url: 'commission_wallet.php',
      type: 'GET',
      data: params,
      beforeSend:function(){
        $("#ajax_loader").show();
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#walletDiv').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }


</script>
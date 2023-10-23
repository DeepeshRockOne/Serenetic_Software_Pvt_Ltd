<?php if (!empty($_POST["is_ajax"])) { ?>
    <div class="table-responsive">
        <table class="<?=$table_class?>">
            <tbody>
                <tr>
                    <td class="fw500">Total</td>
                    <td class="fw500"><?=displayAmount($rps_data['TotalPremium'])?></td>
                </tr>
                <tr>
                    <td class="fw500">Approved</td>
                    <td>
                        <span class="fw500"><?=$rps_data['TotalApprovedPolicyHolder']?> / <?=displayAmount($rps_data['TotalApprovedPremium'])?></span>&nbsp; &nbsp; (New Business : <span class="fw500"><?=$rps_data['TotalNewApprovedPolicyHolder']?> / <?=displayAmount($rps_data['TotalNewApprovedPremium'])?></span> )&nbsp;&nbsp;&nbsp; (Renewal Business: <span class="fw500"><?=$rps_data['TotalRenewalApprovedPolicyHolder']?> / <?=displayAmount($rps_data['TotalRenewalApprovedPremium'])?></span> )
                    </td>
                </tr>
                <tr>
                    <td class="fw500">Cancelled</td>
                    <td class="fw500 text-warning"><?=$rps_data['TotalCancelledPolicyHolder']?> / <?=displayAmount($rps_data['TotalCancelledPremium'])?></td>
                </tr>
                <tr>
                    <td class="fw500">Declined</td>
                    <td class="fw500 text-warning"><?=$rps_data['TotalDeclinedPolicyHolder']?> / <?=displayAmount($rps_data['TotalDeclinedPremium'])?></td>
                </tr>
                <tr>
                    <td class="fw500">Refund</td>
                    <td class="fw500 text-action"><?=$rps_data['TotalRefundedPolicyHolder']?> / (<?=displayAmount($rps_data['TotalRefundedPremium'])?>)</td>
                </tr>
                <tr>
                    <td class="fw500">Void</td>
                    <td class="fw500 text-action"><?=$rps_data['TotalVoidPolicyHolder']?> / (<?=displayAmount($rps_data['TotalVoidPremium'])?>)</td>
                </tr>
                <tr>
                    <td class="fw500">Payment<br> Returned (ACH)</td>
                    <td class="fw500 text-action"><?=$rps_data['TotalPaymentReturnedPolicyHolder']?> / (<?=displayAmount($rps_data['TotalPaymentReturnedPremium'])?>)</td>
                </tr>
                <tr>
                    <td class="fw500">Chargeback</td>
                    <td class="fw500 text-action"><?=$rps_data['TotalChargebackedPolicyHolder']?> / (<?=displayAmount($rps_data['TotalChargebackedPremium'])?>)</td>
                </tr>
                <tr>
                    <td class="fw500">ACH</td>
                    <td class="fw500">
                        <?=$rps_data['TotalACHApprovedPolicyHolder']?> / <?=displayAmount($rps_data['TotalACHApprovedPremium'])?><br>
                        <span class="text-warning"><?=$rps_data['TotalACHDeclinedPolicyHolder']?> / <?=displayAmount($rps_data['TotalACHDeclinedPremium'])?></span>
                    </td>
                </tr>
                <tr>
                    <td class="fw500">CC</td>
                    <td class="fw500">
                        <?=$rps_data['TotalCCApprovedPolicyHolder']?> / <?=displayAmount($rps_data['TotalCCApprovedPremium'])?><br>
                        <span class="text-warning"><?=$rps_data['TotalCCDeclinedPolicyHolder']?> / <?=displayAmount($rps_data['TotalCCDeclinedPremium'])?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-right m-t-30">
        <a href="javascript:void(0);" id="btn_export_rps_data" class="btn btn-action">Export</a>
    </div>
<?php } else { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="mn">Quick Sales Summary</h4>
        </div>
        <div class="panel-body">
            <form method="POST" name="frm_rps" class="form_wrap quick_report" id="frm_rps" enctype="multipart/form-data">
                <input type="hidden" name="is_ajax" value="Y">
                <input type="hidden" name="user_type" value="<?=$user_type?>">
                <div class="row theme-form">
                    <div id="date_range" class="col-sm-2">
                       <div class="form-group height_auto m-b-5">
                          <select class="form-control" id="join_range" name="join_range">
                                <option value="Exactly" selected="">Exactly</option>
                                <option value="Before">Before</option>
                                <option value="After">After</option>
                                <option value="Range">Range</option>
                          </select>
                          <label>Date Type</label>
                          <p class="error"><span class="error_join_range"></span></p>
                       </div>
                    </div>
                    <div class="select_date_div col-sm-8" >
                        <div class="form-group height_auto m-b-5">
                            <div id="all_join">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <div class="pr">
                                        <input type="text" name="added_date" id="added_date" value="<?=date('m/d/Y');?>" class="form-control date_picker" placeholder="MM / DD / YYYY"  />
                                    </div>
                                </div>
                                <p class="error text-left"><span class="error_added_date"></span></p>
                            </div>

                            <div  id="range_join" style="display:none;">
                                <div class="phone-control-wrap form-group height_auto m-b-5">
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <div class="pr">
                                             <input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
                                             <label>From Date</label>
                                          </div>
                                       </div>
                                        <p class="error text-left"><span class="error_fromdate"></span></p>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group">
                                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <div class="pr">
                                             <input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
                                             <label>To Date</label>
                                          </div>
                                       </div>
                                        <p class="error text-left"><span class="error_todate"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 m-b-5">
                        <button class="btn btn-action btn-block" id="btn_load_rps_data" type="button">Submit</button>
                    </div>
                </div>
                <div id="rps_data"></div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            load_rps_data();

            $(document).off('click', '#btn_export_rps_data');
            $(document).on('click', '#btn_export_rps_data', function(e) {
                parent.confirm_export_data(function() {
                    $('#ajax_loader').show();
                    $.ajax({
                        url: 'quick_report_sales_summary.php?action=export_rps_data',
                        type: 'GET',
                        data: $("#frm_rps").serialize(),
                        dataType: 'json',
                        success: function(res) {
                            $('#ajax_loader').hide();
                            $("#export").val('');
                            if(res.status == "success") {
                                <?php if($user_type == "Agent") {?>
                                confirm_view_export_request(true,'agent');
                                <?php } else { ?>
                                confirm_view_export_request(true);
                                <?php } ?>
                                

                            } else if(res.status == 'custom_error') {
                                
                                parent.setNotifyError(res.message);

                            } else {                                
                                var is_error = true;
                                $.each(res.errors, function(key, value) {
                                    $('.error_' + key).parent("p.error").show();
                                    $('.error_' + key).html(value).show();
                                    if (is_error == true && $('.error_' + key).length > 0) {
                                        is_error =false;
                                        $('html, body').animate({
                                            scrollTop: parseInt($('.error_' + key).offset().top) - 100
                                        }, 1000);
                                    }
                                });
                            }
                        }
                    });
                });
            });

            $(document).off('click', '#btn_load_rps_data');
            $(document).on('click', '#btn_load_rps_data', function(e) {
                var is_error = false;
                $('.error span').html('');
                if ($("#join_range").val() == '') {
                    $('.error_join_range').html('Please select Date Type');
                    is_error = true;
                }
                if ($("#join_range").val() == 'Range') {
                    if($("#fromdate").val() == "") {
                        $('.error_fromdate').html('Please select From Date');
                        is_error = true;
                    }
                    if($("#todate").val() == "") {
                        $('.error_todate').html('Please select To Date');
                        is_error = true;
                    }
                } else {
                    if($("#added_date").val() == "") {
                        $('.error_added_date').html('Please select Date');
                        is_error = true;
                    }
                }
                if(is_error == false) {
                    load_rps_data();
                }
            });

            $(document).off('change', '#join_range');
            $(document).on('change', '#join_range', function(e) {
                e.preventDefault();
                if($(this).val() == ''){                    
                    $('#range_join').hide();
                    $('#all_join').show();
                } else {
                    if ($(this).val() == 'Range') {
                        $('#range_join').show();
                        $('#all_join').hide();
                    } else {
                        $('#range_join').hide();
                        $('#all_join').show();
                    }
                }
            });

            $(".date_picker").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
                autoclose:true,
            });
        });

        function load_rps_data() {
            $.ajax({
                url: "<?=$HOST?>/quick_report_sales_summary.php",
                method: "POST",
                beforeSend: function () {
                    $("#ajax_loader").show();
                },
                data: $("#frm_rps").serialize(),
                success: function (res) {
                    $("#ajax_loader").hide();
                    $("#rps_data").html(res);
                }
            });
        }
    </script>
<?php } ?>
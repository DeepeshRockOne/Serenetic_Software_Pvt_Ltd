<form method="POST" action="" name="frmReinstate" class="reinstate_tab_wrap" id="frmReinstate">
    <input type="hidden" name="step" id="step" value="1">
    <input type="hidden" name="location" id="location" value="<?= $location ?>">
    <input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id ?>">
    <input type="hidden" name="is_subscription_changed" id="is_subscription_changed" value="Y">
    <div class="panel panel-default reinstate_panel">
        <div class="cust_tab_ui cust_tab_ui_sm">
            <ul class="nav nav-tabs nav-justified nav-noscroll reinstate_product">
                <li class="active">
                    <a data-toggle="tab" href="#product_tab" class="btn_step_heading" data-step="1">
                        <div class="column-step ">
                            <div class="step-number">1</div>
                            <div class="step-title">Choose Product(s)</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#coverage_tab" class="btn_step_heading" data-step="2">
                        <div class="column-step ">
                            <div class="step-number">2</div>
                            <div class="step-title">Details/Coverage Period(s)</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#reinstate_tab" class="btn_step_heading" data-step="3">
                        <div class="column-step ">
                            <div class="step-number">3</div>
                            <div class="step-title">Summary/Reinstate</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div id="product_tab" class="tab-pane fade in active">
                <div class="panel-body">
                    <div class="text-center">
                        <h4 class="m-b-30">Reinstate Product(s) for ID: <span class="text-action"><?= $rep_id ?></span>
                        </h4>
                        <p class="fs16 m-b-30">Please choose which product(s) the member wants reinstated.</p>
                        <div class="form-group">
                            <div class="btn-group colors" data-toggle="buttons">
                                <?php
                                foreach ($terminated_subscriptions as $key => $subscription_row) {
                                    ?>
                                    <label class="btn btn-info btn-outline m-b-5 btn_reinstate_subscription" data-ws_id="<?= $subscription_row['ws_id'] ?>">
                                        <input type="checkbox" name="reinstate_subscriptions[]" class="js-switch reinstate_subscriptions" autocomplete="off" value="<?=$subscription_row['ws_id'] ?>" data-product_id="<?=$subscription_row['product_id'] ?>"><?=$subscription_row['product_name'].' '.$subscription_row['product_code']?>
                                        <br><span class="text-action"><strong><?=$subscription_row['website_id']?></strong></span>
                                    </label>
                                    <?php
                                }
                                ?>
                            </div>
                            <p class="error"><span id="error_reinstate_subscriptions"></span></p>
                        </div>
                        <p class="fs10"><span id="total_selected_prd">0</span>
                            of <?= count($terminated_subscriptions) ?> Selected</p>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <div class="reinstate_righttab_footer text-right">
                        <button type="button" class="btn btn-action btn_submit" data-step="1">Next</button>
                        <a href="javascript:void(0);" class="btn red-link" onclick="window.parent.$.colorbox.close()">Cancel</a>
                    </div>
                </div>
            </div>
            <div id="coverage_tab" class="tab-pane">
                <div class="reinstate_choose_period">
                    <div class="panel-body">
                        <div class="coverage_periods_section"></div>
                        <div class="reinstate_righttab_footer text-right pr">
                            <button type="button" class="btn btn-action btn_submit" data-step="2">Next</button>
                            <a href="javascript:void(0);" class="btn red-link" onclick="window.parent.$.colorbox.close()">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="reinstate_tab" class="tab-pane fade ">
                <div class="reinstate_choose_period">
                    <div class="panel-body">
                        <div class="single_reinstate_choose_period">
                            <div class="reinstate_billing_summary_section" style="display: none;">
                                <h4 class="">Summary</h4>
                                <div class="table-responsive reinstate_billing_summary"></div>    
                            </div>                            
                            <div class="reinstate_choose_product_blank"></div>
                            <h4 class="m-t-20">Next Billing Date</h4>
                            <div class="table-responsive reinstate_next_billing_summary"></div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="button" class="btn btn-action btn_submit" data-step="3">Reinstate Product(s)
                            </button>
                            <a href="javascript:void(0);" class="btn red-link" onclick="window.parent.$.colorbox.close()">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).off("click",".btn_save_billing_profile");
        $(document).on("click",".btn_save_billing_profile", function () {
            var btn_obj = $(this);
            btn_obj.prop('disabled', true);
            $("#ajax_loader").show();
            $('.error span').html('');
            $.ajax({
                url: '<?= $HOST ?>/admin/ajax_save_billing_profile',
                data: $(".new_payment_method_section:visible :input").serialize(),
                type: 'POST',
                dataType: "json",
                success: function (res) {
                    btn_obj.prop('disabled', false);
                    $("#ajax_loader").hide();

                    if (res.status == "success") {
                        var option_html = "<option value='" + res.billing_id + "'>" + res.billing_label + "</option>";
                        $("select.billing_profile").each(function (index, element) {
                            $(this).find("option:last").before(option_html);
                            if ($(this).val() == "new_payment_method") {
                                $(this).val(res.billing_id);
                            }
                        });

                        $(".new_payment_method_section:visible").fadeOut(function () {
                            $(this).remove();
                        });
                        setNotifySuccess("Billing method added successfully");

                    } else if (res.status == "error") {
                        var is_error = true;
                        $.each(res.errors, function (index, value) {
                            $('#error_' + index).html(value).show();
                            if (is_error) {
                                var offset = $('#error_' + index).offset();
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop + 0;
                                $('html,body').animate({
                                    scrollTop: totalScroll
                                }, 1200);
                                is_error = false;
                            }
                        });
                    }
                }
            });
        });

        $(document).on('click', '.chk_fee', function () {
            var price = $(this).attr('data-fee');
            var coverage = $(this).attr('data-coverage');
            var total_amount = $('.coverage_total_amount_' + coverage).attr('data-coverage_total_amount');

            if (this.checked) {
                total_amount = parseFloat(total_amount) + parseFloat(price);
            } else {
                total_amount = parseFloat(total_amount) - parseFloat(price);
                
            }
            $('.coverage_total_amount_' + coverage).text('$' + (total_amount).toFixed(2));
            $('.coverage_total_amount_' + coverage).attr('data-coverage_total_amount',total_amount);
        });

        $(document).on('change', '.payment_method', function (e) {
            if ($(this).val() == "CC") {
                $(".ach_method_section").slideUp();
                $(".cc_method_section").slideDown();

            } else if ($(this).val() == "ACH") {
                $(".cc_method_section").slideUp();
                $(".ach_method_section").slideDown();

            } else {
                // $(".cc_method_section").slideUp();
                // $(".ach_method_section").slideUp();
            }
        });

        $(document).off('change', 'select.billing_profile');
        $(document).on('change', 'select.billing_profile', function () {
            var this_obj = $(this);
            var old_value = $(this);

            if (this_obj.val() == "new_payment_method") {
                if ($(".new_payment_method_section").is(":visible")) {
                    $(".payment_method").focus();
                    this_obj.val('');
                } else {
                    var payment_method_section = $(".new_payment_method_section").clone();
                    this_obj.closest(".coverage_period_row").after(payment_method_section);
                    $('[name="is_default"]:visible').uniform();
                    $('.payment_method').selectpicker('refresh');
                }
            } else {
                var is_display_new_pm_section = false;
                $("select.billing_profile").each(function (index, element) {
                    if ($(this).val() == "new_payment_method") {
                        is_display_new_pm_section = true;
                    }
                });

                if (is_display_new_pm_section == false) {
                    $(".new_payment_method_section:visible").fadeOut(function () {
                        $(this).remove();
                    });
                }
            }
        });

        $(document).off('change','.reinstate_subscriptions');
        $(document).on('change','.reinstate_subscriptions',function(e){
         var product_id = $(this).data('product_id');
         var ws_id = $(this).val();
         if($(this).is(":checked")) {
           $(".reinstate_subscriptions[data-product_id='"+product_id+"'][value!='"+ws_id+"']").prop("checked",false);
           $(".reinstate_subscriptions[data-product_id='"+product_id+"'][value!='"+ws_id+"']").closest('.btn_reinstate_subscription').removeClass('active');
         }
         
         var total_product = 0;
         var selected_products_count = 0;
            $('.reinstate_subscriptions').each(function () {
                if($(this).parents('label').hasClass('active') === true){
                   selected_products_count++; 
                }
                $("#total_selected_prd").text(selected_products_count);
            });            
            $("#is_subscription_changed").val("Y");
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            <?php if(count($terminated_subscriptions) == 0) { echo "return false;"; } ?>
            var new_step = $(e.target).data("step");
            var current_step = $(e.relatedTarget).data("step");
            if(new_step < current_step) {
            } else {
                //reinstate_product($('.btn_submit[data-step="'+current_step+'"]'));    
            }            
        });

        $(document).off("click",".btn_submit");
        $(document).on("click",".btn_submit", function () {
            reinstate_product($(this));
        });
    });

    function reinstate_product(btn_obj) {
        <?php if(count($terminated_subscriptions) == 0) { echo "return false;"; } ?>

        $("#step").val(btn_obj.attr('data-step'));
        btn_obj.prop('disabled', true);
        $("#ajax_loader").show();
        $('.error span').html('');

        $.ajax({
            url: '<?= $HOST ?>/ajax_reinstate_product.php',
            data: $("#frmReinstate").serialize(),
            type: 'POST',
            dataType: "json",
            success: function (res) {
                btn_obj.prop('disabled', false);
                $("#ajax_loader").hide();

                if (typeof (res.coverage_periods_html) !== 'undefined') {
                    $(".coverage_periods_section").html(res.coverage_periods_html);
                    var last_coverage_end_date = res.last_coverage_end_date;
                    $(".payment_date").datepicker({
                        startDate: "<?=date('m/d/Y')?>",
                        endDate: last_coverage_end_date,
                        orientation: "bottom",
                    });
                    $("#is_subscription_changed").val("N");

                    $(".billing_profile").addClass('form-control');
                    $(".billing_profile").selectpicker({
                        container: 'body',
                        style: 'btn-select',
                        noneSelectedText: '',
                        dropupAuto: false,
                    });
                    
                    common_select();
                    fRefresh();
                }

                if (typeof (res.reinstate_billing_summary) !== 'undefined') {
                    $(".reinstate_billing_summary").html(res.reinstate_billing_summary);
                    $(".reinstate_billing_summary_section").show();
                }

                if (typeof (res.reinstate_next_billing_summary) !== 'undefined') {
                    $(".reinstate_next_billing_summary").html(res.reinstate_next_billing_summary);
                }

                if (res.status == "success") {
                    $(".reinstate_product [data-toggle='tab'][data-step='" + (parseFloat($("#step").val()) + 1) + "']").trigger("click");

                } else if (res.status == "error") {
                    var is_error = true;
                    $.each(res.errors, function (index, value) {
                        $('#error_' + index).html(value).show();

                        if (is_error) {
                            $("[href='#" + $('#error_' + index).parents(".tab-pane").attr("id") + "']").trigger("click");
                            is_error = false;

                            var offset = $('#error_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 200;
                            $('html,body').animate({
                                scrollTop: totalScroll
                            }, 1200);
                        }
                    });
                } else if (res.status == "payment_error") {
                    parent.swal({
                        title: "Payment Failed",
                        html: res.payment_error,
                        type: "error",
                        showCancelButton: true,
                        cancelButtonText: "Update Billing",
                        confirmButtonText: "Cancel Reinstate",
                        allowOutsideClick: false,
                    }).then(function () {
                        parent.$.colorbox.close();
                    }, function (dismiss) {
                        $(".reinstate_product [data-toggle='tab'][data-step='2']").trigger("click");
                    });
                } else if (res.status == "payment_success") {
                    window.parent.location.reload();
                } else {
                    
                }
            }
        });
    }
</script>
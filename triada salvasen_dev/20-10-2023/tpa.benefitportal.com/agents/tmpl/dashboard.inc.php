<div class="container m-t-30">
<style type="text/css">
    .flot-chart .flow-chart-info{
        max-width: 215px;
    }
    .highcharts-tooltip> span> span{
        padding-bottom: 5px;
        width: 80px;
        display: inline-block;
        text-align: right;
    }
    .highcharts-tooltip> span> span.sales_label{
        text-align: left;
    }
</style>
<form id="form_dashboard">
<div class="dashboard_topbar clearfix pr">
    <div class="row">
        <div class="col-lg-4 col-md-2 ">
            <p class="fs14 text-uppecase mn text-light-gray">WELCOME</p>
            <p class="fs18  mn text-action fw500"><?= ($businessNameHeader != "") ? $businessNameHeader : $_SESSION['agents']['fname'] . " " . $_SESSION['agents']['lname'] ?></p>
        </div>
        <div class="col-lg-8 col-md-10 dashboard_topbar_right top_header_section">
            <div class="dash_top_counter">
                <p class="fs14 text-light-gray text-uppercase mn">Gross sales</p>
                <p class="fw600 text-action"><span class="gross_sales">$0.00</span></p>
            </div>
            <div class="dash_top_counter">
                <p class="fs14 text-light-gray text-uppercase mn">New Business</p>
                <p class="fw600 text-action "><span class="new_business_members">0</span>/<span class="new_business_sales">$0.00</span></p>
            </div>
            <div class="dash_top_counter">
                <p class="fs14 text-light-gray text-uppercase mn">Renewals</p>
                <p class="fw600 text-action"><span class="renewals_members">0</span>/<span class="renewals_sales">$0.00</span></p>
            </div>
            <div class="dash_top_counter">
                <p class="fs14 text-light-gray text-uppercase mn">Active Members</p>
                <p class="fw600 text-action "><span class="active_members">0</span></p>
            </div>
            <div class="dash_top_counter">
                <a href="javascript:void(0);" class="btn btn-info pull-right" id="btn_select_date"><?=date('m/d/Y')?></a>
            </div>
        </div>
    </div>
    <div class="custom-data-wrap theme-form" id="custom-date-toggle" style="display: none;">
        <div id="date_range" class="col-md-12">
            <div class="form-group">
                <select class="form-control m-b-15" id="join_range" name="join_range">
                    <option value=""></option>
                    <option value="Range">Range</option>
                    <option value="Exactly" selected>Exactly</option>
                    <option value="Before">Before</option>
                    <option value="After">After</option>
                </select>
                <label>Date Type</label>
                <p class="error error_join_range"></p>
            </div>
        </div>
        <div class="select_date_div col-md-9" style="display:none">
            <div class="form-group  mn ">
                <div id="all_join">
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="added_date" id="added_date" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_added_date text-left"></p>
                        </div>
                        <div class="phone-addon w-65">
                            <button type="button" class="btn btn-action btn_set_date"  id="all_btn_set_date">Submit</button>
                        </div>
                    </div>
                </div>
                <div id="range_join" style="display:none;">
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="fromdate" id="fromdate" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_fromdate text-left"></p>
                        </div>
                        <div class="phone-addon">
                            <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="pr">
                                    <input type="text" name="todate" id="todate" value="<?=date('m/d/Y')?>" class="form-control date_picker"/>
                                    <label>Date</label>
                                </div>
                            </div>
                            <p class="error error_todate text-left"></p>
                        </div>
                        <div class="phone-addon w-65">
                            <button type="button" class="btn btn-action btn_set_date" id="range_btn_set_date">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default dashboard-panel gross_sales_panel">
    <div class="panel-heading fs14 panel-selection br-n">
        <label class="radio-inline"><input type="radio" name="sales_type" value="Gross Sales" checked="">Gross Sales</label>
        <label class="radio-inline"><input type="radio" name="sales_type" value="Net Sales">Net Sales</label>
        <span class="p-15 text-light-gray fs20 fw300">|</span>
        <label class="checkbox-inline"><input type="checkbox" name="include_new_business" value="1" checked="">New Business</label>
        <label class="checkbox-inline"><input type="checkbox" name="include_renewals" value="1" checked="">Renewals</label>
    </div>

    <div class="panel-body p-t-0">
        <div class="row">
            <div class="col-md-3 ">
                <div class="dash_netsales_box gross_net_sales_section">
                    <div class="text-center">
                        <p class="fw500">
                            <span class="sales_type_lable">Gross Sales</span>
                            <i class="fa fa-info-circle fa-lg gross_net_sales_tooltip" rel="tooltip" data-placement="bottom" data-toggle="tooltip"></i>
                        </p>
                        <p class="fw600 fs26">
                            <span class="gross_net_sales_amt">$0.00</span>
                        </p>
                        <div class="br-t p-t-10 m-b-10"></div>
                        <table cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td>Average Sale</td>
                                <td class="average_sale">$0.00</td>
                            </tr>
                            <tr class="gross_net_sales_more_tr">
                                <td colspan="2" class="text-right"><a href="javascript:void(0);" class="text-info gross_net_sales_more_link"><u>More</u></a></td>
                            </tr>
                            <tr class="gross_net_sales_more_tr_1" style="display:none">
                                <td>Avg Policy/Member</td>
                                <td class="avg_policy_per_member">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ">
                <div class="dash_top_performing">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="theme-form max-w175">
                                <div class="form-group">
                                    <select name="agencies_or_agents" id="agencies_or_agents" class="form-control">
                                        <option data-hidden="true"></option>
                                        <option value="Agencies">Agencies</option>
                                        <option value="Agents" selected>Agents</option>
                                    </select>
                                    <label>Top Performing</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 text-right">
                            <label class="radio-inline m-b-0"><input type="radio" name="top_agents_short_by" value="Members">Members</label>
                            <label class="radio-inline m-b-0"><input type="radio" name="top_agents_short_by" value="Sales" checked="">Sales</label>
                        </div>
                    </div>
                    <div class=" table-responsive panel-body-scroll m-t-10" style="max-height:150px;">
                        <table class="<?= $table_class ?>">
                            <tbody class="tbody_top_performing_agents"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="dash_top_flowchart premiums_chart_section">
                    <div class="flot-chart">
                        <div class="flot-chart-content" id="flot-pie-chart"></div>
                        <div class="flow-chart-info  fw600 fs12"><span class="sales_type_lable">Gross Sales</span></div>
                    </div>
                    <div>
                        <table cellpadding="0" cellspacing="0" width="100%;">
                            <tbody>
                            <tr>
                                <td><i class="fa fa-square text-action "></i></td>
                                <td class="fw500">Premiums</td>
                                <td class="text-right total_premiums">$0.00</td>
                                <td class="text-right fw500 w-55 total_premiums_per">(0%)</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-square text-info "></i></td>
                                <td class="fw500">Healthy Steps</td>
                                <td class="text-right total_healthy_steps">$0.00</td>
                                <td class="text-right fw500 w-55 total_healthy_steps_per">(0%)</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-square  " style="color: #bbb;"></i></td>
                                <td class="fw500">Fees</td>
                                <td class="text-right total_fees">$0.00</td>
                                <td class="text-right fw500 w-55 total_fees_per">(0%)</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-lg-4 col-md-6 ">
        <div class="panel panel-default dashboard-panel dashboard-sales-panel">
            <div class="panel-heading">
                <div class="clearfix m-t-5">
                    <div class="pull-left">
                        <span class="fw600 text-left fs14"><span class="sales_type_lable">Gross Sales</span></span>
                    </div>
                    <div class="pull-right text-center">
                        <ul class="list-inline  mn morris_chart_label">
                            <li>
                                <h5 class="mn"><i class="fa fa-square m-r-5 text-info"></i>Last Year Sales
                                </h5>
                            </li>
                            <li>
                                <h5 class="mn"><i class="fa fa-square text-action m-r-5"></i>This Year Sales</h5>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="stacked" style="height: 320px;"></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-md-6 ">
        <div class="panel panel-default dashboard-panel dashboard-state-panel">
            <div class="panel-heading clearfix panel-selection fs14">
                <div class="row">
                    <div class="col-md-4">
                        <div class="theme-form">
                            <div class="form-group">
                                <select name="state" id="state" class="form-control dash_form_control" data-live-search="true">
                                    <option value="">State</option>
                                    <?php if (!empty($allStateRes)) { ?>
                                    <?php foreach ($allStateRes as $state) { ?>
                                    <option value="<?= $state["name"]; ?>"><?php echo $state['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 m-t-7 text-right ">
                        <label class="radio-inline" style="font-weight: bold;">Sort by :</label>
                        <label class="radio-inline"><input type="radio" name="top_state_short_by" value="Members">Members</label>
                        <label class="radio-inline"><input type="radio" name="top_state_short_by" value="Sales" checked="">Sales</label>
                    </div>
                </div>
            </div>
            <div id="top_performing_state_section" class="panel-body table-responsive" style="max-height:310px;">
                <table class="<?= $table_class ?>">
                    <tbody class="tbody_top_performing_state">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-md-6 ">
        <div class="panel panel-default dashboard-panel dashboard-product-panel">
            <div class="panel-heading clearfix panel-selection fs14">
                <div class="row">
                    <div class="col-md-4">
                        <div class="theme-form">
                            <div class="form-group">
                                <select name="product" id="product" class="form-control dash_form_control" data-live-search="true">
                                    <option>Products</option>
                                    <?php 
                                    if(!empty($companyArr) && count($companyArr) > 0) {
                                        foreach ($companyArr as $key => $company) {
                                            foreach ($company as $pkey => $row) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] . ' (' . $row['product_code'] . ') ' ?></option>
                                    <?php   } 
                                        } 
                                    } 
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 m-t-7 text-right ">
                        <label class="radio-inline"><input type="radio" name="top_products_short_by" value="Policies">Policies</label>
                        <label class="radio-inline"><input type="radio" name="top_products_short_by" value="Sales" checked="">Sales</label>
                    </div>

                </div>
            </div>
            <div id="top_performing_products_section" class="panel-body" style="max-height:310px;">
                <div class="dash_product_progress table-responsive">
                    <table cellspacing="0" cellspacing="0" width="100%">
                        <tbody class="tbody_top_performing_products"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default dashboard-panel renewal_summary_section">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">Renewal Summary</h4>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="dash_renewal_box">
                    <p class="text-gray m-b-15">Remaining Renewals</p>
                    <div class="phone-control-wrap">
                        <div class="phone-addon text-left">
                            <h4 class="mn remaining_renewals_amt">-</h4>
                            <p class="mn remaining_renewals_trans"></p>
                        </div>
                        <div class="phone-addon w-55">
                            <img src="<?=$HOST?>/images/icons/stopwatch.svg<?= $cache ?>" height="33px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="dash_renewal_box">
                    <p class="text-gray m-b-15">Avg Daily Collection</p>
                    <div class="phone-control-wrap">
                        <div class="phone-addon text-left">
                            <h4 class="mn avg_daily_collection">-</h4>
                        </div>
                        <div class="phone-addon w-55">
                            <img src="<?=$HOST?>/images/icons/folder.svg<?= $cache ?>" height="33px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="dash_renewal_box">
                    <p class="text-gray m-b-15">Projected Collection</p>
                    <div class="phone-control-wrap">
                        <div class="phone-addon text-left">
                            <h4 class="mn ren_proj_collection_amt">-</h4>
                            <p class="mn ren_proj_collection_trans"></p>
                        </div>
                        <div class="phone-addon w-55">
                            <img src="<?=$HOST?>/images/icons/projector.svg<?= $cache ?>" width="40px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="dash_renewal_box">
                    <p class="text-gray m-b-15">Projected Monthly Total</p>
                    <div class="phone-control-wrap">
                        <div class="phone-addon text-left">
                            <h4 class="mn ren_proj_monthly_total_amt">-</h4>
                            <p class="mn ren_proj_monthly_total_trans"></p>
                        </div>
                        <div class="phone-addon w-55">
                            <img src="<?=$HOST?>/images/icons/monthly-projector.svg<?= $cache ?>" height="33px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default dashboard-panel" <?=$is_loa_agent == true?'style="display: none;"':'';?>>
         <div class="panel-heading clearfix panel-selection">
            <div class="chart-head fs14">
                <div>
                  <strong class="text-uppercase fs18 v-align-middle m-r-15">Commission</strong>
                </div>
               <div class="pull-left">
                  <ul class="list-inline morris_chart_label mn">
                     <li>
                        <h5 class="mn"><i class="fa fa-square m-r-5 text-info"></i>Last Year Commission</h5>
                     </li>
                     <li>
                        <h5 class="mn"><i class="fa fa-square text-action m-r-5"></i>This Year Commission</h5>
                     </li>
                  </ul>
               </div>
               <div class="pull-right">
                  <label class="radio-inline">
                  <input type="radio" name="commission_duration" value="weekly" checked=""> Weekly
                  </label>
                  <label class="radio-inline">
                  <input type="radio" name="commission_duration" value="monthly"> Monthly
                  </label>
               </div>
            </div>
         </div>
         <div class="panel-body">
            <div id="commission_chart" class="m-b-25" style="height: 370px;"></div>
         </div>
      </div>
</form>
</div>
<div class="gross_net_sales_tooltip_section" style="display: none;">
    <h4 class="text-white m-t-0 text-uppercase selected_date_title"></h4>
    <table cellspacing="0" cellpadding="0" width="100%" style= "font-size: 12px;">
        <tbody>
            <tr>
                <td>Gross Sales:</td>
                <td class="text-right gross_sales"></td>
            </tr>
            <tr>
                <td>Reversals:</td>
                <td class="text-right reversals_sales"></td>
            </tr>
            <tr>
                <td>Net Sales:</td>
                <td class="text-right net_sales"></td>
            </tr>
            <tr style="border-top:1px solid #fff;">
                <td class="text-nowrap">Pending Settlement:</td>
                <td class="text-right"><span class="pending_settlement_trans"></span>/<span class="pending_settlement_amt"></span></td>
            </tr>
            <tr>
                <td class="text-nowrap">Payment Returned:</td>
                <td class="text-right"><span class="payment_returned_trans"></span>/<span class="payment_returned_amt"></span></td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var ajax_cnt = 0;
    var more_link_ajax_call = false;
    function scrollIntoView(element, container) {
        var containerTop = $(container).scrollTop(); 
        var containerBottom = containerTop + $(container).height(); 
        var elemTop = element.offsetTop;
        var elemBottom = elemTop + $(element).height(); 
        if (elemTop < containerTop) {
        $(container).scrollTop(elemTop);
        } else if (elemBottom > containerBottom) {
        $(container).scrollTop(elemBottom - $(container).height());
        }
    }
    $(document).ready(function () {
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true
        });

        $(".gross_net_sales_tooltip").tooltip({
            title: $('.gross_net_sales_tooltip_section').html(),
            trigger: 'hover',
            html: 'true',
            placement: 'top',
            template: '<div class="tooltip net_sales_tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });

        $('.panel-body-scroll').slimscroll({height: '100%', width: '100%'});
        var top_perf_product_scroll = $('#top_performing_products_section').slimscroll({height: '100%', width: '100%'});
        var top_perf_state_scroll = $('#top_performing_state_section').slimscroll({height: '100%', width: '100%'});
       
        $('.dashboard-state-panel, .dashboard-product-panel').matchHeight({
            target: $('.dashboard-sales-panel')
        });

        $(document).off('change', '#join_range');
        $(document).on('change', '#join_range', function(e) {
            e.preventDefault();
            if($(this).val() == ''){
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

        $("#join_range").val("Exactly").change();

        $("#btn_select_date,.btn_hide_date_selection").click(function () {
            $('#custom-date-toggle').slideToggle('slow');
        });

        $(document).off('click', '.btn_set_date');
        $(document).on('click', '.btn_set_date', function(e) {
            var is_error = false;
            more_link_ajax_call = false;
            $('.error').html('');
            $(".gross_net_sales_more_tr_1").hide();
            $(".gross_net_sales_more_tr").show();

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
                $('#custom-date-toggle').slideToggle('slow');
                var selected_date = '';
                if ($("#join_range").val() == 'Range') {
                    selected_date += $("#fromdate").val() + ' - ' + $("#todate").val();
                } else {
                    if($("#join_range").val() != "Exactly") {
                        selected_date = $("#join_range").val() + ' ' + $("#added_date").val();    
                    } else {
                        selected_date = $("#added_date").val();
                    }                   
                }
                $("#btn_select_date").html(selected_date);

                load_all_section_data(true,$(this));
            }
        });

        $(document).off('change', 'input[name="sales_type"]');
        $(document).on('change', 'input[name="sales_type"]', function(e) {
            var sales_type = $(this).val();
            $(".sales_type_lable").html(sales_type);
            load_all_section_data(false);
            load_sales_bar_chart();
            if(more_link_ajax_call){
                load_dashboard_data('gross_net_sales_more');
            }
        });

        $(document).off('change', 'input[name="include_new_business"]');
        $(document).on('change', 'input[name="include_new_business"]', function(e) {
            load_all_section_data(false);
            load_sales_bar_chart();
            if(more_link_ajax_call){
                load_dashboard_data('gross_net_sales_more');
            }
        });

        $(document).off('change', 'input[name="include_renewals"]');
        $(document).on('change', 'input[name="include_renewals"]', function(e) {
            load_all_section_data(false);
            load_sales_bar_chart();
            if(more_link_ajax_call){
                load_dashboard_data('gross_net_sales_more');
            }
        });

        $(document).off('change', '#agencies_or_agents, input[name="top_agents_short_by"]');
        $(document).on('change', '#agencies_or_agents, input[name="top_agents_short_by"]', function(e) {
            load_dashboard_data('top_performing_agents');
        });

        $(document).off('change', 'input[name="top_state_short_by"]');
        $(document).on('change', 'input[name="top_state_short_by"]', function(e) {
            load_dashboard_data('top_performing_state');
        });

        $(document).off('change', '#product');
        $(document).on('change', '#product', function(e) {
            var prd_id = $(this).val();
            if($("#top_performing_product_"+prd_id).length > 0) {
                var scrollToVal = $('#top_performing_products_section').scrollTop() + $("#top_performing_product_"+prd_id).position().top;
                top_perf_product_scroll.slimScroll({scrollTo: scrollToVal +"px"});
            } else {
                top_perf_product_scroll.slimScroll({ scrollTo: '0' });
            }
            
        });

        $(document).off('change', '#state');
        $(document).on('change', '#state', function(e) {
            var state = $(this).val();
            if($("#top_performing_state_"+state).length > 0) {
                var scrollToVal = $('#top_perf_state_scroll').scrollTop() + $("#top_performing_state_"+state).position().top;
                top_perf_state_scroll.slimScroll({scrollTo: scrollToVal +"px"});
            } else {
                top_perf_state_scroll.slimScroll({ scrollTo: '0' });
            }
            
        });

        $(document).off('change', 'input[name="top_products_short_by"]');
        $(document).on('change', 'input[name="top_products_short_by"]', function(e) {
            load_dashboard_data('top_performing_products');
        });

        $(document).off('change', 'input[name="commission_duration"]');
        $(document).on('change', 'input[name="commission_duration"]', function(e) {
            load_commission_chart();
        });

        /*--- Load All Data ---*/
        load_all_section_data(true);
        load_sales_bar_chart();
        load_renewal_summary();
        load_commission_chart();
    });

    function load_renewal_summary()
    {
        load_dashboard_data('renewal_summary');
    }

    function load_sales_bar_chart()
    {
        load_dashboard_data('sales_bar_chart');
    }

    function load_commission_chart()
    {   
        <?php if($is_loa_agent == true) { ?>
        return true;
        <?php } ?>
        
        $('#commission_chart').html('<div class="text-center" style="line-height: 340px;"><i class="fa fa-spin fa-spinner fa-3x "></i></div>');
        load_dashboard_data('commission_chart');
    }

    function load_all_section_data(load_top_header,btnId)
    {
        if(btnId ==='' || btnId === undefined){
            btnId = $("#all_btn_set_date");
        }
        disableButton(btnId);
        if(typeof(load_top_header) !== "undefined") {
            load_dashboard_data('top_header');
        }

        load_dashboard_data('gross_net_sales');
        load_dashboard_data('top_performing_agents');
        load_dashboard_data('premiums_chart');
        load_dashboard_data('top_performing_state');
        var loadSuccess = load_dashboard_data('top_performing_products');
        if(loadSuccess === true){
            enableButton(btnId);
        }
    }

    function load_dashboard_data(report)
    {   
        ajax_cnt++;
        $.ajax({
            url: "<?=$HOST?>/load_dashboard_report_data.php?portal=agent&report="+report,
            method: "POST",
            beforeSend: function () {          
                $("#ajax_loader").show();
            },
            data: $("#form_dashboard").serialize(),
            dataType:'json',
            success: function (res) {       
                ajax_cnt--;
                if(ajax_cnt <= 0) {
                    $("#ajax_loader").hide();
                }

                if(typeof(res.top_header) !== "undefined") {
                    var top_header_data = res.top_header;
                    $.each(top_header_data,function(index,value){
                        if($(".top_header_section ."+index).length > 0) {
                            $(".top_header_section ."+index).html(value);
                        }
                    });
                }
                if(typeof(res.gross_net_sales) !== "undefined") {
                    var gross_net_sales_data = res.gross_net_sales;
                    $.each(gross_net_sales_data,function(index,value){
                        if($(".gross_net_sales_section ."+index).length > 0) {
                            $(".gross_net_sales_section ."+index).html(value);
                        }
                        if($(".gross_net_sales_tooltip_section ."+index).length > 0) {
                            $(".gross_net_sales_tooltip_section ."+index).html(value);
                        }
                    });                 
                    $(".gross_net_sales_tooltip").attr('data-original-title',$('.gross_net_sales_tooltip_section').html());
                }
                if(typeof(res.gross_net_sales_more) !== "undefined") {
                    var gross_net_sales_more_data = res.gross_net_sales_more;
                    $.each(gross_net_sales_more_data,function(index,value){
                        if($(".gross_net_sales_section ."+index).length > 0) {
                            $(".gross_net_sales_section ."+index).html(value);
                        }
                    });
                    $(".gross_net_sales_more_tr").hide();
                    $(".gross_net_sales_more_tr_1").show();
                }
                if(typeof(res.top_performing_agents) !== "undefined") {
                    $(".tbody_top_performing_agents").html(res.top_performing_agents);
                    if(typeof(res.agent_res) !== "undefined") {
                    } else {

                    }
                }
                if(typeof(res.premiums_chart) !== "undefined") {
                    var premiums_chart_data = res.premiums_chart;
                    $.each(premiums_chart_data,function(index,value){
                        if($(".premiums_chart_section ."+index).length > 0) {
                            $(".premiums_chart_section ."+index).html(value);
                        }
                    });

                    $.plot($("#flot-pie-chart"), premiums_chart_data.chart_data, {
                        series: {
                            pie: {
                                innerRadius: 0.5,
                                show: true,
                                label: {
                                    show: true,
                                    formatter: function (label, series) {
                                        var percent = parseFloat(series.percent);
                                        return '';
                                        return percent.toFixed(2);
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
                        }
                    });
                }

                if(typeof(res.sales_bar_chart) !== "undefined") {
                    var sales_bar_chart_data = res.sales_bar_chart;
                    draw_sales_bar_chart(sales_bar_chart_data);
                }

                if(typeof(res.commission_chart) !== "undefined") {
                    var commission_chart_data = res.commission_chart;
                    draw_commission_chart(commission_chart_data);
                }

                if(typeof(res.top_performing_state) !== "undefined") {
                    $(".tbody_top_performing_state").html(res.top_performing_state);
                    if(typeof(res.state_options) !== "undefined") {
                        $("#state").html(res.state_options);
                    } else {
                        $("#state").html('<option value="">State</option>');
                    }
                    /*$("#state").multipleSelect({
                        filter: true
                    });*/
                    $("#state").selectpicker('refresh');
                }

                if(typeof(res.top_performing_products) !== "undefined") {
                    $(".tbody_top_performing_products").html(res.top_performing_products);

                    if(typeof(res.product_options) !== "undefined") {
                        $("#product").html(res.product_options);
                    } else {
                        $("#product").html('<option value="">Products</option>');
                    }
                    /*$("#product").multipleSelect({
                        filter: true
                    });*/
                    $("#product").selectpicker('refresh');

                }

                if(typeof(res.renewal_summary) !== "undefined") {
                    var renewal_summary_data = res.renewal_summary;
                    $.each(renewal_summary_data,function(index,value){
                        if($(".renewal_summary_section ."+index).length > 0) {
                            $(".renewal_summary_section ."+index).html(value);
                        }
                    });
                }
            }
        });
        return true;
    }

    function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '.00';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        //return x1 + x2;
        return x1;
    }

    function draw_sales_bar_chart(chart_data)
    {
        var months = chart_data.months;
        var curr_year_sales = chart_data.curr_year_sales;
        var pre_year_sales = chart_data.pre_year_sales;

        var chart = Highcharts.chart('stacked', {
            chart: {
                type: 'column'
            },
            lang: {
            thousandsSep: ','
          },
            title: false,
            subtitle: false,
            plotOptions: {
                series: {
                    grouping: false,
                    borderWidth: 0
                }
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false,
                floating: true
            },
            tooltip: {
                shared: true,
                backgroundColor: 'rgba(0,0,0,0.72)',
                borderRadius: 3,
                shadow: false,
                borderWidth: 0,
                useHTML: true,
                outside:false,
                VerticalAlignValue :  "top",
                /*positioner: function () {
                    return { x: 0, y: 0 };
                },*/
                //headerFormat: '<span style="font-size: 14px; font-weight:bold;">{point.point.display_month}</span><br/>',
                //pointFormat: '<span style="color:{point.color}">\u25CF</span>New Business {point.y}<br/>',
                formatter: function () {
                    if(typeof(this.points[1]) === 'undefined') {
                        return false;
                    }

                    var tooltip_html = '<span class="sales_label" style="font-size: 14px; font-weight:bold;">'+this.points[0].point.name+'</span> <span style="font-size: 14px; font-weight:bold;">'+this.points[0].point.year+'</span> <span style="font-size: 14px; font-weight:bold;">'+this.points[1].point.year+'</span> <br/>';

                    tooltip_html += '<span class="sales_label">New Business: </span> <span>'+this.points[0].point.new_business_sales_amt+'</span> <span>'+this.points[1].point.new_business_sales_amt+'</span> <br/>';
                    tooltip_html += '<span class="sales_label">Renewals: </span> <span>'+this.points[0].point.renewal_sales_amt+'</span> <span>'+this.points[1].point.renewal_sales_amt+'</span> <br/>';
                    tooltip_html += '<span class="sales_label" style="font-size: 12px; font-weight:bold;">Gross Sales: </span> <span style="font-size: 12px;">'+this.points[0].point.gross_sales_amt+'</span> <span style="font-size: 12px;">'+this.points[1].point.gross_sales_amt+'</span> <br/>';
                    tooltip_html += '<span class="sales_label">Reversals: </span> <span>'+this.points[0].point.reversals_amt+'</span> <span>'+this.points[1].point.reversals_amt+'</span> <br/>';
                    tooltip_html += '<span class="sales_label" style="font-size: 12px; font-weight:bold;">Net Sales: </span> <span style="font-size: 12px;">'+this.points[0].point.net_sales_amt+'</span> <span style="font-size: 12px;">'+this.points[1].point.net_sales_amt+'</span> <br/>';
                    return tooltip_html;
                },
                style: {
                    color: '#fff',
                    fontFamily: 'Roboto'
                }
            },
            xAxis: {
                categories: months,
                labels: {
                    style: {
                        color: '#A2A2A2',
                        fontFamily: 'Roboto'
                    }
                }
            },
            yAxis: [{
                title: false,
                showFirstLabel: false,
                allowDecimals: true,
                labels: {
                    style: {
                        color: '#5d5d5d',
                        fontFamily: 'Roboto'
                    },
                    //format: '${value:,.2f}'
                    formatter: function () {
                        return '$' + addCommas(this.value);
                    } 
                }
            }],
            series: [
                {
                color: '#0086C2',
                pointPlacement: -0.2,
                allowDecimals: true,
                linkedTo: 'main',
                data: pre_year_sales.slice(),
                name: 'Last Year Sales'
                },{
                color: '#d94948',
                name: 'This Year Sales',
                id: 'main',
                allowDecimals: true,
                dataSorting: {
                    enabled: false,
                    matchByName: false
                },
                dataLabels: [{
                    enabled: false,
                    inside: false,
                    style: {
                        fontSize: '16px'
                    }
                }],
                data: curr_year_sales.slice()
            }],
            exporting: {
                allowHTML: true
            }
        });
    }

    function draw_commission_chart(chart_data) {
        console.log(chart_data);
        $('#commission_chart').html('');

        var x_axis_values = chart_data.x_axis_values;
        var curr_year_sales = chart_data.curr_year_sales;
        var pre_year_sales = chart_data.pre_year_sales;

        var chart = Highcharts.chart('commission_chart', {
            chart: {
                type: 'area'
            },
            lang: {
                thousandsSep: ','
            },
            title: false,
            subtitle: false,
            plotOptions: {
                series: {
                    fillOpacity: 0.5,
                    grouping: true,
                    borderWidth: 1,
                }
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false,
                floating: true
            },
            tooltip: {
                shared: true,
                backgroundColor: 'rgba(0,0,0,0.72)',
                borderRadius: 3,
                shadow: false,
                borderWidth: 0,
                useHTML: true,
                outside:false,
                VerticalAlignValue :  "top",
                formatter: function () {
                    if(typeof(this.points[1]) === 'undefined') {
                        return false;
                    }

                    var tooltip_html = '<span class="sales_label" style="font-size: 14px; font-weight:bold;">'+this.points[0].point.name+'</span> <span style="font-size: 14px; font-weight:bold;">'+this.points[0].point.year+'</span> <span style="font-size: 14px; font-weight:bold;">'+this.points[1].point.year+'</span> <br/>';
                    tooltip_html += '<span class="sales_label">New Business: </span> <span>'+this.points[0].point.new_business_total+'</span> <span>'+this.points[1].point.new_business_total+'</span> <br/>';
                    
                    tooltip_html += '<span class="sales_label">Renewals: </span> <span>'+this.points[0].point.renewal_total+'</span> <span>'+this.points[1].point.renewal_total+'</span> <br/>';
                    
                    tooltip_html += '<span class="sales_label">PMPMs: </span> <span>'+this.points[0].point.pmpm_total+'</span> <span>'+this.points[1].point.pmpm_total+'</span> <br/>';

                    tooltip_html += '<span class="sales_label">Advances: </span> <span>'+this.points[0].point.advance_total+'</span> <span>'+this.points[1].point.advance_total+'</span> <br/>';

                    tooltip_html += '<span class="sales_label" style="font-size: 12px; font-weight:bold;">Gross Commissions: </span> <span style="font-size: 12px;">'+this.points[0].point.gross_total+'</span> <span style="font-size: 12px;">'+this.points[1].point.gross_total+'</span> <br/>';
                    
                    tooltip_html += '<span class="sales_label">Reversals: </span> <span>'+this.points[0].point.reversals_total+'</span> <span>'+this.points[1].point.reversals_total+'</span> <br/>';

                    tooltip_html += '<span class="sales_label">Fees: </span> <span>'+this.points[0].point.fee_total+'</span> <span>'+this.points[1].point.fee_total+'</span> <br/>';
                    
                    tooltip_html += '<span class="sales_label" style="font-size: 12px; font-weight:bold;">Net Commissions: </span> <span style="font-size: 12px;">'+this.points[0].point.net_total+'</span> <span style="font-size: 12px;">'+this.points[1].point.net_total+'</span> <br/>';
                    return tooltip_html;
                },
                style: {
                    color: '#fff',
                    fontFamily: 'Roboto'
                }
            },
            xAxis: {
                categories: x_axis_values,
                labels: {
                    style: {
                        color: '#A2A2A2',
                        fontFamily: 'Roboto'
                    }
                }
            },
            yAxis: [{
                title: false,
                showFirstLabel: false,
                allowDecimals: true,
                labels: {
                    style: {
                        color: '#5d5d5d',
                        fontFamily: 'Roboto'
                    },
                    formatter: function () {
                        return '$' + addCommas(this.value);
                    } 
                }
            }],
            series: [
                {
                color: '#0086C2',
                pointPlacement: -0.2,
                allowDecimals: true,
                linkedTo: 'main',
                data: pre_year_sales.slice(),
                name: 'Last Year Sales'
                },{
                color: '#d94948',
                name: 'This Year Sales',
                id: 'main',
                allowDecimals: true,
                dataSorting: {
                    enabled: false,
                    matchByName: false
                },
                dataLabels: [{
                    enabled: false,
                    inside: false,
                    style: {
                        fontSize: '16px'
                    }
                }],
                data: curr_year_sales.slice()
            }],
            exporting: {
                allowHTML: true
            }
        });
    }

    $(document).off('click','.gross_net_sales_more_link');
    $(document).on('click','.gross_net_sales_more_link',function(e){
        e.preventDefault();
        more_link_ajax_call = true;
        load_dashboard_data('gross_net_sales_more');
    });
</script>
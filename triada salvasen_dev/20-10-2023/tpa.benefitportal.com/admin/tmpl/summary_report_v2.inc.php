<?php if (!empty($_POST["is_ajax"])) {  ?>
    <script type="text/javascript">
        var export_link = "<?=$ADMIN_HOST?>/summary_report_v2.php?export=Y&type=<?=$type?>&from_date=<?=$searchArray['getfromdate']?>&to_date=<?=$searchArray['gettodate']?>";
        $("#btn_export").attr("href",export_link);

        <?php if(strtotime($searchArray['getfromdate']) == 0) { ?>
            $('#selected_date').text("All Time");
        <?php } elseif(strtotime($searchArray['getfromdate']) ==  strtotime($searchArray['gettodate'])) { ?>
            $('#selected_date').text("<?=date('F d, Y', strtotime($searchArray['gettodate']))?>");
        <?php } else { ?>
        $('#selected_date').text("<?=date('F d, Y', strtotime($searchArray['getfromdate']))?> - <?=date('F d, Y', strtotime($searchArray['gettodate']))?>");
        <?php } ?>
    </script>
    <div class="clearfix"></div>

    <div class="row" id="report_data">
        <div class="col-lg-12">
            <div class="agents_section white-box">
                <h4 class="box-title-cust">TOP PERFORMING AGENTS</h4>
                <?php
                    include "top_performing_agents_v2.inc.php";
                ?>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="agents_section white-box">
                <h4 class="box-title-cust">TOP PERFORMING ORGANIZATIONS</h4>
                <?php
                    include "top_performing_organizations.inc.php";
                ?>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="agents_section white-box">
                <h4 class="box-title-cust">TOP PERFORMING PRODUCTS</h4>
                <?php
                    include "top_performing_products_v2.inc.php";
                ?>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
<?php } else { ?>

    <style type="text/css">
        .datepicker-days.weeklyCommission tr:hover td:first-child, .datepicker-days.weeklyCommission tr:hover td:last-child {
            background-color: #0095d9;
            color: #fff !important;
        }

        .datepicker-days.weeklyCommission tr:hover td:first-child:hover, .datepicker-days.weeklyCommission tr:hover td:last-child:hover {
            background-color: #0095d9;
            color: #fff !important;
        }

        .datepicker-days.weeklyCommission tr:hover td {
            border-top: 1px solid #0095d9;
            border-bottom: 1px solid #0095d9;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .report_main_table tr td, .report_main_table tr th {
            padding: 5px 5px;
        }

        .no_border .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td, .table > thead > tr > th {
            border: none;
        }

        th {
            font-weight: 700;
        }
    </style>

    <div id="new_business_summary_report">
        <div class="panel panel-default panel-block">
            <div class="panel-heading">
                <h4 class="box-title pull-left fw-300"><?= $page_title ?> &nbsp;
                    <div class="country-date pos_rel">
                        <div class="dropdown-toggle">Date <i class="fa fa-angle-right"></i>
                            <span id="searched_type">Today</span>
                        </div>
                        <ul role="menu" class="dropdown-menu pull-right">
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Today"
                                       data-val="Today">Today</a></li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Yesterday"
                                   data-val="Yesterday">Yesterday</a>
                            </li>
                            <!-- <li><a class="custom_date" href="javascript:void(0)" data-Date="This Week"
                                   data-val="This Week">This Week</a></li> -->
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Last 7 Days" data-val="Last 7 Days">Last 7 Days</a></li>
                           <!--  <li><a class="custom_date" href="javascript:void(0)" data-Date="Last Week"
                                   data-val="Last Week">Last Week</a> -->
                            </li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="This Month"
                                   data-val="This Month">This Month</a></li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Last Month"
                                   data-val="Last Month">Last Month</a>
                            </li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="This Year"
                                   data-val="This Year">This Year</a>
                            </li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Last Year"
                                   data-val="Last Year">Last Year</a>
                            </li>
                            <!-- <li><a class="custom_date" href="javascript:void(0)" data-Date="All Time"
                                   data-val="All Time">All Time</a> -->
                            </li>
                            <li><a class="custom_date" href="javascript:void(0)" data-Date="Custom Date"
                                   data-val="Custom">Custom</a></li>
                        </ul>
                    </div>
                </h4>
                <!-- <a href="javascript:void(0);" id="btn_export" class="btn btn-action pull-right">
                    <i class="fa fa-download"></i> Export
                </a> -->
            </div>
            <div class="panel-body">
                <p id="select_day">
                    <strong>Selected Date:</strong> &nbsp; <span id="selected_date"></span>
                </p>

                <div id="custom_date_div" class="custom_date_div" style="display: none;">
                    <div class="form-inline">
                        <div class="input-group">
                            <div class="input-group-addon">Select Date</div>
                            <input type="text" name="fromdate" id="fromdate" value=""
                                   class="datetimepicker-range form-control" size="10" placeholder="From Date">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input type="text" name="todate" id="todate" value="" class="datetimepicker-range form-control"
                                   size="10" placeholder="To Date">
                            <span class="input-group-addon" style="border-left: 0; border-right: 0;"><i class="glyphicon glyphicon-calendar"></i></span>

                            <input type="hidden" name="fromdate" id="fromdate" value=""
                                   class="datetimepicker-range form-control" size="10" placeholder="From Date">
                            <input type="hidden" name="todate" id="todate" value=""
                                   class="datetimepicker-range form-control"
                                   size="10" placeholder="To Date">
                        </div>
                        <div class="form-group m-l-10 m-t-18">
                            <a href="javascript:void(0)" class="btn btn-info" id="submit_btn">Submit</a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>


            </div>
        </div>
        <div class="outputData"></div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            getNewBusinessSummaryReport("", "", "");

            $(document).on("click", "#new_business_summary_report #snap_shot", function () {
                var report_id = 25;
                $.colorbox({
                    href: 'recepient_email_report.php?report_id=' + report_id,
                    iframe: true,
                    width: '70%',
                    height: '80%'
                });
            });

            $("#new_business_summary_report #fromdate,#todate").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                endDate: '+0d',
            }).on('changeDate', function (e) {
                $('#new_business_summary_report #fromdate').val(this.value);
            });

            $('#new_business_summary_report #submit_btn').click(function () {
                if ($("#input_custom_date").val() == '') {
                    $(this).closest('.form-inline').addClass('has-error');
                } else {
                    $(this).closest('.form-inline').removeClass('has-error');
                    getNewBusinessSummaryReport('Custom', $('#new_business_summary_report #fromdate').val(), $('#new_business_summary_report #todate').val());
                }
            });

            $('#new_business_summary_report #input_custom_date').keyup(function (e) {
                e.preventDefault();
            });

            $('#new_business_summary_report .custom_date').click(function () {
                var val = $(this).attr('data-val');

                $("#searched_type").html($(this).attr('data-Date'));
                if (val == 'Custom') {
                    $('#select_day').hide();
                    $('#new_business_summary_report #custom_date_div').fadeIn('slow');
                } else {
                  $('#select_day').show();
                    $('#new_business_summary_report #fromdate').val('');
                    $('#new_business_summary_report #todate').val('');
                    $('#new_business_summary_report #input_custom_date').val('');
                    $('#new_business_summary_report #custom_date_div').fadeOut('slow');
                    getNewBusinessSummaryReport(val);
                }
            });
        });

        function getNewBusinessSummaryReport(date,from_date, to_date) {
            $.ajax({
                url: "summary_report_v2.php",
                method: "POST",
                beforeSend: function () {
                    $("#ajax_loader").show();
                },
                data: {
                    filter_date: date,
                    is_ajax: 1,
                    from_date: from_date,
                    to_date: to_date,
                    type: $("#new_business_summary_report #searched_type").html()
                },
                success: function (res) {
                    $("#ajax_loader").hide();
                    $("#new_business_summary_report .outputData").html(res);
                }
            });
        }
    </script>
<?php } ?>
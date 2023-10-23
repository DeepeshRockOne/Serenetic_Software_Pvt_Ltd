<style type="text/css">
  .hrm_summary_expand {
    margin: 0px auto;
    padding: 30px;
    background-color: #f8f8f8;
  }

  .hrm_summary .table tbody tr td a[data-toggle=collapse].collapsed:before {
    content: '\e61a'
  }

  .hrm_summary .table tbody tr td a[data-toggle=collapse]:before {
    font-weight: bold;
    content: '\e622';
    margin: 0px auto;
    display: block;
    font-family: 'themify';
    font-size: 14px;
    width: 25px
  }
</style>
<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="pendingHrmFrm" action="weekly_pending_hrm_payments.php" method="GET">
            <input type="hidden" name="pay_period" value="<?= $pay_period ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Pending</h4>
                </div>
                <div class="pull-right">
                    <div class="m-b-15">
                        <div class="note_search_wrap auto_size" id="pending_search_div" style="display: none; max-width: 100%;">
                            <div class="phone-control-wrap theme-form">
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="pending_search_close_btn  text-light-gray">X</a>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupId" id="pgroupId">
                                        <label>Group ID</label>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupName" id="pgroupName">
                                        <label>Group Name</label>
                                    </div>
                                </div>
                                <div class="phone-addon w-80">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="pendingHRMPaymentData();">Search</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0)" class="pending_search_btn search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
                    </div>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="weekly_pending_hrm_payments_div"></div>
        </form>
    </div>
</div>

<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="nonCompliantHrmFrm" action="weekly_non_compliant_hrm_payments.php" method="GET">
            <input type="hidden" name="pay_period" value="<?= $pay_period ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Non Complaint</h4>
                </div>
                <div class="pull-right">
                    <div class="m-b-15">
                        <div class="note_search_wrap auto_size" id="non_compliant_search_div" style="display: none; max-width: 100%;">
                            <div class="phone-control-wrap theme-form">
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="non_compliant_search_close_btn  text-light-gray">X</a>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupId" id="ncgroupId">
                                        <label>Group ID</label>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupName" id="ncgroupName">
                                        <label>Group Name</label>
                                    </div>
                                </div>
                                <div class="phone-addon w-80">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="nonCompliantHRMPaymentData();">Search</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0)" class="non_compliant_search_btn search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
                    </div>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="weekly_non_compliant_hrm_payments_div"></div>
        </form>
    </div>
</div>

<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="completedHrmFrm" action="weekly_completed_hrm_payments.php" method="GET">
            <input type="hidden" name="pay_period" value="<?= $pay_period ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Completed</h4>
                </div>
                <div class="pull-right">
                    <div class="m-b-15">
                        <div class="note_search_wrap auto_size" id="completed_search_div" style="display: none; max-width: 100%;">
                            <div class="phone-control-wrap theme-form">
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="completed_search_close_btn text-light-gray">X</a>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupId" id="groupId">
                                        <label>Group ID</label>
                                    </div>
                                </div>
                                <div class="phone-addon">
                                    <div class="form-group height_auto mn">
                                        <input type="text" class="form-control" name="groupName" id="groupName">
                                        <label>Group Name</label>
                                    </div>
                                </div>
                                <div class="phone-addon w-80">
                                    <div class="form-group height_auto mn">
                                        <a href="javascript:void(0);" class="btn btn-info search_button btn-block" onclick="completedHRMPaymentData();">Search</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:void(0)" class="completed_search_btn search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
                    </div>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="weekly_completed_hrm_payments_div"></div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        dropdown_pagination('weekly_pending_hrm_payments_div', 'weekly_completed_hrm_payments_div','weekly_non_compliant_hrm_payments_div');
    });

    $(document).ready(function() {
        pendingHRMPaymentData();
        nonCompliantHRMPaymentData();
        completedHRMPaymentData();
    });
    $(document).off("click", ".pending_search_btn");
    $(document).on("click", ".pending_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#pending_search_div").css('display', 'inline-block');
    });

    $(document).off("click", ".pending_search_close_btn");
    $(document).on("click", ".pending_search_close_btn", function(e) {
        e.preventDefault();
        $("#pending_search_div").hide();
        $(".pending_search_btn").show();
        $("#pgroupId").val('');
        $("#pgroupName").val('');
        pendingHRMPaymentData();
    });

    $(document).off("click", ".non_compliant_search_btn");
    $(document).on("click", ".non_compliant_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#non_compliant_search_div").css('display', 'inline-block');
    });

    $(document).off("click", ".non_compliant_search_close_btn");
    $(document).on("click", ".non_compliant_search_close_btn", function(e) {
        e.preventDefault();
        $("#non_compliant_search_div").hide();
        $(".non_compliant_search_btn").show();
        $("#ncgroupId").val('');
        $("#ncgroupName").val('');
        nonCompliantHRMPaymentData();
    });

    $(document).off("click", ".completed_search_btn");
    $(document).on("click", ".completed_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#completed_search_div").css('display', 'inline-block');
    });

    $(document).off("click", ".completed_search_close_btn");
    $(document).on("click", ".completed_search_close_btn", function(e) {
        e.preventDefault();
        $("#completed_search_div").hide();
        $(".completed_search_btn").show();
        $("#groupId").val('');
        $("#groupName").val('');
        completedHRMPaymentData();
    });

    $(document).off('click', '#weekly_pending_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#weekly_pending_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#weekly_pending_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_pending_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    $(document).off('click', '#weekly_non_compliant_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#weekly_non_compliant_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#weekly_non_compliant_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_non_compliant_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    $(document).off('click', '#weekly_completed_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#weekly_completed_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#weekly_completed_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_completed_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    pendingHRMPaymentData = function() {
        var params = $("#pendingHrmFrm").serialize();
        $.ajax({
            url: 'weekly_pending_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_pending_hrm_payments_div').html(res);
                $('#hrm_pending_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }

    nonCompliantHRMPaymentData = function() {
        var params = $("#nonCompliantHrmFrm").serialize();
        $.ajax({
            url: 'weekly_non_compliant_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_non_compliant_hrm_payments_div').html(res);
                $('#hrm_non_compliant_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }

    completedHRMPaymentData = function() {
        var params = $("#completedHrmFrm").serialize();
        $.ajax({
            url: 'weekly_completed_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weekly_completed_hrm_payments_div').html(res);
                $('#hrm_completed_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }
</script>
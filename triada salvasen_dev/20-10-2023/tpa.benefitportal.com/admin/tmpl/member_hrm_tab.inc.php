<!--------------------GAP-ACH-information--start-code---------------------->
<?php include_once '../tmpl/member_gap_ach_information.inc.php'; ?>
<!--------------------GAP-ACH-information--end-code------------------------>

<p class="agp_md_title">HRM Payment</p>
<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="pendingHrmFrm" action="member_pending_hrm_payments.php" method="GET">
            <input type="hidden" name="customer_id" value="<?= checkIsset($customer_id) ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Pending</h4>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="member_pending_hrm_payments_div"></div>
        </form>
    </div>
</div>

<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="nonCompliantHrmFrm" action="member_non_compliant_hrm_payments.php" method="GET">
            <input type="hidden" name="customer_id" value="<?= checkIsset($customer_id) ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Non Complaint</h4>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="member_non_compliant_hrm_payments_div"></div>
        </form>
    </div>
</div>

<div class="panel panel-default panel-block panel-title-block">
    <div class="panel-body">
        <form id="completedHrmFrm" action="member_completed_hrm_payments.php" method="GET">
            <input type="hidden" name="customer_id" value="<?= checkIsset($customer_id) ?>" />
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
            <div class="clearfix tbl_filter">
                <div class="pull-left">
                    <h4 class="m-t-0">Completed</h4>
                </div>
            </div>
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="member_completed_hrm_payments_div"></div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        dropdown_pagination('member_pending_hrm_payments_div', 'member_completed_hrm_payments_div','member_non_compliant_hrm_payments_div');
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

    $(document).off('click', '#member_pending_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#member_pending_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#member_pending_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_pending_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    $(document).off('click', '#member_non_compliant_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#member_non_compliant_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#member_non_compliant_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_non_compliant_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    $(document).off('click', '#member_completed_hrm_payments_div ul.pagination li a');
    $(document).on('click', '#member_completed_hrm_payments_div ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#member_completed_hrm_payments_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_completed_hrm_payments_div').html(res).show();
                common_select();
                fRefresh();
            }
        });
    });

    function pendingHRMPaymentData(){
        var params = $("#pendingHrmFrm").serialize();
        $.ajax({
            url: 'member_pending_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_pending_hrm_payments_div').html(res);
                $('#hrm_pending_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }

    function nonCompliantHRMPaymentData(){
        var params = $("#nonCompliantHrmFrm").serialize();
        $.ajax({
            url: 'member_non_compliant_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_non_compliant_hrm_payments_div').html(res);
                $('#hrm_non_compliant_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }

    function completedHRMPaymentData(){
        var params = $("#completedHrmFrm").serialize();
        $.ajax({
            url: 'member_completed_hrm_payments.php?<?= $_SERVER['QUERY_STRING'] ?>',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#member_completed_hrm_payments_div').html(res);
                $('#hrm_completed_table').bootstrapTable().removeClass("table-hover");
                common_select();
            }
        });
    }
</script>
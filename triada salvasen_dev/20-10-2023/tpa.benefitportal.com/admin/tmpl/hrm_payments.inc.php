<form id="frm_search" method="GET" class="theme-form" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <div id="weeklyHRMPaymentDiv"></div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        dropdown_pagination('weeklyHRMPaymentDiv');
        loadStatement();
    });

    loadStatement = function() {
        weeklyHRMPaymentStatement();
    }

    weeklyHRMPaymentStatement = function() {
        $('#weeklyHRMPaymentDiv').hide();
        var params = $("#frm_search").serialize();
        $.ajax({
            url: 'weekly_hrm_payments_statement.php',
            type: 'GET',
            data: params,
            beforeSend: function() {
                $("#ajax_loader").show();
            },
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weeklyHRMPaymentDiv').html(res).show();
                common_select();
            }
        });
    }
</script>
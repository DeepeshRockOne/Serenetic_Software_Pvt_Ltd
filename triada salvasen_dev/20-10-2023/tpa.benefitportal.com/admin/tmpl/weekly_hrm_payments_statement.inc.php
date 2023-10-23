<div class="white-box">
    <div class="clearfix tbl_filter">
        <div class="pull-left">
            <h4 class="m-t-0 ">Weekly HRM Payments</h4>
        <?php if ($total_rows > 0) { ?>
                <div class="form-inline mt-2" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn height_auto">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn height_auto">
                        <select size="1" id="pages" name="weekly_pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);weeklyHRMPaymentStatement();">
                            <option value="10" <?php echo isset($_GET['weekly_pages']) && $_GET['weekly_pages'] == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo (isset($_GET['weekly_pages']) && $_GET['weekly_pages'] == 25) || (isset($_GET['weekly_pages']) && $_GET['weekly_pages'] == "") ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo isset($_GET['weekly_pages']) && $_GET['weekly_pages'] == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo isset($_GET['weekly_pages']) && $_GET['weekly_pages'] == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                    </div>
                </div>
        <?php } ?>
        </div>
        <?php if($SITE_ENV != 'Live'){?>
        <div class="pull-right m-t-30">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group mn height_auto">
                    <input type="hidden" name="debug" id="debug" value="" class="form-control" />
                    <input type="text" name="group_id" id="group_id" value="" class="form-control" />
                    <label>Group</label>
                    <p class="error" id="error_group_id"></p>
                </div>
                <div class="form-group mn height_auto">
                    <div class="input-group"> 
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <div class="pr">
                            <input type="text" name="paydate" id="paydate" value="" class="form-control date_picker" />
                            <label>Pay date</label>
                        </div>
                    </div>
                    <p class="error" id="error_paydate"></p>
                </div>
                <div class="form-group mn height_auto">
                    <button type="button" class="btn btn-default" id="generateHRM">Generate HRM Payment</button>
                    <p class="error" id="error_button"></p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="table-responsive m-t-10">
        <table class="<?= $table_class ?> text-nowrap">
            <thead>
                <tr>
                    <th width="30%">Period</th>
                    <th width="30%">Groups</th>
                    <th width="30%">Members</th>
                    <th width="10%">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                    foreach ($fetch_rows as $rows) {
                        $startPayPeriod = date('m/d/Y', strtotime('-6 days', strtotime($rows['payPeriod'])));;
                        $endPayPeriod = date('m/d/Y', strtotime($rows['payPeriod']));
                        $currentPayPeriod = $startPayPeriod .' - '. $endPayPeriod;
                ?>
                        <tr>
                            <td class="text-nowrap" width="30%"><a href="weekly_hrm_payment_status.php?pay_period=<?= $rows['payPeriod'] ?>" class="fw500 text-action" data-toggle="tooltip" title="VIEW" data-placement="bottom"><?= $currentPayPeriod; ?></a></td>
                            <td width="30%"><?= $rows['groupId']; ?></td>
                            <td width="30%"><?= $rows['memberId']; ?></td>
                            <td width="10%"><?= dispCommAmt($rows['totalAmount']) ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9" class="text-center">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php
            if ($total_rows > 0) { ?>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <?php echo $paginate->links_html; ?>
                        </td>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).off('click', '#weeklyHRMPaymentDiv ul.pagination li a');
    $(document).on('click', '#weeklyHRMPaymentDiv ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#weeklyHRMPaymentDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#weeklyHRMPaymentDiv').html(res).show();
                common_select();
            }
        });
    });
    $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
    $(document).off('click','#generateHRM');
    $(document).on('click','#generateHRM',function(e){
    e.preventDefault();
    var group_id = $('#group_id').val();
    var date = $('#paydate').val();
    var debug = $('#debug').val();
    $("#ajax_loader").show();
    $.ajax({
        url: 'ajax_generate_hrm.php',
        type: 'POST',
        data:{
            group_id: group_id,
            paydate: date,
            debug: debug
        },
        success: function(res) {
            $("#ajax_loader").hide();
            $(".error").hide();
            if(res.status == 'fail'){
                $.each(res.errors, function(index, error) {
                    $('#error_' + index).html(error).show();
                });
            }else if(res.status == 'success'){
                window.location.reload();
            }
        }
    });
    return false;
    });
</script>
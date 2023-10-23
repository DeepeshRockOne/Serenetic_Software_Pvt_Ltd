<?php if ($is_ajaxed) { ?>
    <?php if ($total_rows > 0) { ?>
        <div class="clearfix m-b-20">
            <div id="hrmCompliantBtns" style="display: none;">
                <div class="pull-left">
                    <?php if ($module_access_type == "rw") { ?>
                        <input type="hidden" name="debug" value="" id="debug">
                        <a href="javascript:void(0);" id="moveToComlianBtn" class="btn btn-info">Move To Compliant</a>
                        <a href="javascript:void(0);" id="denyToComlianBtn" class="btn btn-info btn-outline">Cancel</a>
                    <?php } ?>
                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                        <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#compliant_per_pages').val(this.value);$('#nav_page').val(1);nonCompliantHRMPaymentData();">
                            <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="table-responsive hrm_summary">
        <table class="<?= $table_class ?>">
            <thead>
                <tr>
                    <?php if ($module_access_type == "rw") { ?>
                        <th>
                            <div class="checkbox checkbox-table">
                                <input type="checkbox" id="moveAllToCompliantBox" class="js-switch" data-toggle="tooltip" title="Select All" />
                                <label for="moveAllToCompliantBox">&nbsp;</label>
                            </div>
                        </th>
                    <?php } ?>
                    <th>Group Name</th>
                    <th>Members</th>
                    <th>Total</th>
                    <th class="text-center">Summary</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                    foreach ($fetch_rows as $rows) { ?>
                        <tr>
                            <?php if ($module_access_type == "rw") { ?>
                                <td>
                                    <div class="checkbox checkbox-table group_compliant_check" id="group_compliant_check_<?= $rows['groupId'] ?>">
                                        <input type="checkbox" name="hrm_compliant_groups[<?= $rows['groupId'] ?>]" class="js-switch moveCompliantToSpecGroupBox" id="cs_move_to_compliant_<?= $rows['groupId'] ?>" value="<?= $rows['groupId'] ?>" />
                                        <label for="cs_move_to_compliant_<?= $rows['groupId'] ?>">&nbsp;</label>
                                    </div>
                                </td>
                            <?php } ?>
                            <td><a href="javascript:void(0);" class="fw500"><?= $rows["groupRepId"] ?></a><br><?= $rows["groupName"] ?></td>
                            <td><?= $rows['memberId'] ?></td>
                            <td><?= dispCommAmt($rows['totalAmount']) ?></td>
                            <td class="icons text-center">
                                <span data-toggle="tooltip" title="EXPAND" data-container="body" class="toolCompliantTip_<?= $rows['groupId'] ?> compliantToolTips">
                                    <a href="#compliant_summary_<?= $rows['groupId'] ?>" data-toggle="collapse" class="collapsed collapse_compliant_trigger showMemberCompliantDetail" data-id="<?= $rows['groupId'] ?>" data-pay-date="<?= $rows['pay_date']; ?>" data-compliant="<?=$rows['is_compliant']?>"></a>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="pn" colspan="6">
                                <div id="compliant_summary_<?= $rows['groupId'] ?>" class="panel-collapse collapse hrm_summary_expand collapse_compliant">
                                    <table class="<?= $table_class ?> table-danger" data-toggle="table" id="hrm_non_compliant_table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="checkbox checkbox-table">
                                                        <input type="checkbox" id="compliant_to_all_member_<?= $rows['groupId'] ?>" class="js-switch compliant_to_all_member" data-id="<?= $rows['groupId'] ?>" />
                                                        <label for="compliant_to_all_member_<?= $rows['groupId'] ?>"></label>
                                                    </div>
                                                </th>
                                                <th>Member ID</th>
                                                <th>Member Name</th>
                                                <th>Pay Date</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="memberCompliantResponse_<?= $rows['groupId'] ?>">
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                    <tr>
                        <td colspan="6"><?php echo $paginate->links_html; ?></td>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
    <input type="hidden" name="pages" id="compliant_per_pages" value="<?= $per_page; ?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
<?php } ?>


<script type="text/javascript">
    $(document).ready(function() {

        $(document).off("click", "#moveAllToCompliantBox");
        $(document).on("click", "#moveAllToCompliantBox", function() {
            if ($(this).is(':checked')) {
                $('.moveCompliantToSpecGroupBox').prop('checked', true);
                $("#hrmCompliantBtns").show();
            } else {
                $('.moveCompliantToSpecGroupBox').prop('checked', false);
                if ($('.moveComplinatToSpecMemberBox:checked').length > 0) {
                    $("#hrmCompliantBtns").show();
                } else {
                    $("#hrmCompliantBtns").hide();
                }
            }
        });

        $(document).off("change", ".moveCompliantToSpecGroupBox");
        $(document).on("change", ".moveCompliantToSpecGroupBox", function() {
            if ($('.moveCompliantToSpecGroupBox:checked').length == $('.moveCompliantToSpecGroupBox').length) {
                $('#moveAllToCompliantBox').prop('checked', true);
            } else {
                $('#moveAllToCompliantBox').prop('checked', false);
                if ($('.moveComplinatToSpecMemberBox:checked').length > 0) {
                    $("#hrmCompliantBtns").show();
                } else {
                    $("#hrmCompliantBtns").hide();
                }
            }
            $('.moveCompliantToSpecGroupBox').each(function(e) {
                if ($(this).is(":checked")) {
                    $("#hrmCompliantBtns").show();
                }
            });
        });

        $(document).off("click", ".compliant_to_all_member");
        $(document).on("click", ".compliant_to_all_member", function() {
            var groupId = $(this).attr('data-id');
            if ($(this).is(':checked')) {
                $('#compliant_summary_' + groupId + ' .moveComplinatToSpecMemberBox').prop('checked', true);
                $("#hrmCompliantBtns").show();
            } else {
                $('#compliant_summary_' + groupId + ' .moveComplinatToSpecMemberBox').prop('checked', false);
                if ($('.moveCompliantToSpecGroupBox:checked').length > 0) {
                    $("#hrmCompliantBtns").show();
                } else {
                    $("#hrmCompliantBtns").hide();
                }
            }
        });

        $(document).off("change", ".moveComplinatToSpecMemberBox");
        $(document).on("change", ".moveComplinatToSpecMemberBox", function() {
            var groupId = $(this).attr('data-id');
            if ($('#compliant_summary_' + groupId + ' .moveComplinatToSpecMemberBox:checked').length == $('#compliant_summary_' + groupId + ' .moveComplinatToSpecMemberBox').length) {
                $('#compliant_to_all_member_' + groupId).prop('checked', true);
            } else {
                $('#compliant_to_all_member_' + groupId).prop('checked', false);
                if ($('.moveCompliantToSpecGroupBox:checked').length > 0) {
                    $("#hrmCompliantBtns").show();
                } else {
                    $("#hrmCompliantBtns").hide();
                }
            }
            $('.moveComplinatToSpecMemberBox').each(function(e) {
                if ($(this).is(":checked")) {
                    $("#hrmCompliantBtns").show();
                }
            });
        });

        $(document).off("click", "#moveToComlianBtn");
        $(document).on('click', '#moveToComlianBtn', function(e) {
            e.preventDefault();

            var groupIds = $('input:checkbox:checked.moveCompliantToSpecGroupBox').map(function() {
                return this.value;
            }).get();
            var memberIds = $('input:checkbox:checked.moveComplinatToSpecMemberBox').map(function() {
                return this.value;
            }).get();
            var payDates = $('input:checkbox:checked.moveComplinatToSpecMemberBox').map(function() {
                return $(this).data('pay-date');
            }).get();
            var pay_period = '<?= $pay_period; ?>';
            var debug = $("#debug").val();
            parent.swal({
                text: '<br>Approve Non-compliant HRM Payment: Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
            }).then(function() {
                $.ajax({
                    url: 'ajax_pay_group_hrm.php',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        groupIds: groupIds,
                        pay_period: pay_period,
                        hrm_payment_duration: 'weekly',
                        memberIds: memberIds,
                        nonCompliant : 'Y',
                        payDates:payDates,
                        debug:debug,
                    },
                    beforeSend: function() {
                        $("#ajax_loader").show();
                    },
                    success: function(res) {
                        $("#ajax_loader").hide();
                        if (res.status == 'success') {
                            parent.swal({
                                text: '<br>Non-compliant HRM Payment: Successful',
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: 'Close',
                            }).then(function() {
                                window.parent.location.reload();
                            }, function(dismiss) {
                                window.parent.location.reload();
                            });
                        } else {
                            parent.swal({
                                text: '<br>Non-compliant HRM Payment: Failed',
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: 'Close',
                            }).then(function() {
                                window.parent.location.reload();
                            }, function(dismiss) {
                                window.parent.location.reload();
                            });
                        }
                    }
                });
            }, function(dismiss) {});
        });

        $(document).off("click", "#denyToComlianBtn");
        $(document).on('click', '#denyToComlianBtn', function(e) {
            e.preventDefault();
            nonCompliantHRMPaymentData();
        });

        $(document).off("click", ".showMemberCompliantDetail");
        $(document).on("click", ".showMemberCompliantDetail", function(e) {
            e.preventDefault();
            var groupId = $(this).attr('data-id');
            var pay_date = $(this).attr('data-pay-date');
            var compliant = $(this).attr('data-compliant');
            var pay_period = '<?= $pay_period; ?>';
            /* set to defaul all groups tab*/
            $('.collapse_compliant_trigger').addClass('showMemberCompliantDetail');
            $('.collapse_compliant_trigger').removeClass('hideMemberCompliantDetail');
            $('.collapse.collapse_compliant').collapse('hide');
            $('.compliant_to_all_member').prop('checked', false);
            $('.moveComplinatToSpecMemberBox').prop('checked', false);

            $('div.group_compliant_check').show();
            /***/

            $(this).removeClass('showMemberCompliantDetail');
            $(this).addClass('hideMemberCompliantDetail');
            $("#group_compliant_check_" + groupId).hide();

            $.ajax({
                url: 'ajax_member_datails.php',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    groupId: groupId,
                    pay_date: pay_date,
                    hrm_payment_duration: 'weekly',
                    status: 'NonCompliant',
                    compliant : compliant,
                    pay_period : pay_period,
                },
                success: function(res) {
                    if (res.status == 'success') {
                        $("#memberCompliantResponse_" + groupId).html(res.html);
                        $(".compliantToolTips").attr("title", "EXPAND");
                        $(".toolCompliantTip_" + groupId).attr("title", "CLOSE");
                    }
                }
            });
        });

        $(document).off("click", ".hideMemberCompliantDetail");
        $(document).on("click", ".hideMemberCompliantDetail", function(e) {
            e.preventDefault();
            var groupId = $(this).attr('data-id');
            $(this).removeClass('hideMemberCompliantDetail');
            $(this).addClass('showMemberCompliantDetail');
            $("#memberCompliantResponse_"+groupId).html('');
            $("#hrmCompliantBtns").hide();
            $('div.group_compliant_check').show();
            $(".toolCompliantTip_" + groupId).attr("title", "EXPAND");
            if ($('.moveCompliantToSpecGroupBox:checked').length > 0) {
                $("#hrmCompliantBtns").show();
            }
        });
    });
</script>
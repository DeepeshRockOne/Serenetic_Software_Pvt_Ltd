<?php if ($is_ajaxed) { ?>
    <?php if ($total_rows > 0) { ?>
        <div class="clearfix m-b-20">
            <div id="hrmActionBtns" style="display: none;">
                <div class="pull-left">
                    <?php if ($module_access_type == "rw") { ?>
                        <a href="javascript:void(0);" id="approveToPayBtn" class="btn btn-info">Approve Payment</a>
                        <a href="javascript:void(0);" id="denyToPayBtn" class="btn btn-info btn-outline">Cancel</a>
                    <?php } ?>
                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                        <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);pendingHRMPaymentData();">
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
                    <?php /*if ($module_access_type == "rw") { ?>
                        <th>
                            <div class="checkbox checkbox-table">
                                <input type="checkbox" id="payToAllGroupBox" class="js-switch" data-toggle="tooltip" title="Select All" />
                                <label for="payToAllGroupBox">&nbsp;</label>
                            </div>
                        </th>
                    <?php } */ ?>
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
                            <?php /*if ($module_access_type == "rw") { ?>
                                <td>
                                    <div class="checkbox checkbox-table group_check" id="group_check_<?= $rows['groupId'] ?>">
                                        <input type="checkbox" name="hrm_groups[<?= $rows['groupId'] ?>]" class="js-switch payToSpecGroupBox" id="cs_approve_to_pay_<?= $rows['groupId'] ?>" value="<?= $rows['groupId'] ?>" />
                                        <label for="cs_approve_to_pay_<?= $rows['groupId'] ?>">&nbsp;</label>
                                    </div>
                                </td>
                            <?php } */ ?>
                            <td><a href="javascript:void(0);" class="fw500"><?= $rows["groupRepId"] ?></a><br><?= $rows["groupName"] ?></td>
                            <td><?= $rows['memberId'] ?></td>
                            <td><?= dispCommAmt($rows['totalAmount']) ?></td>
                            <td class="icons text-center">
                                <span data-toggle="tooltip" title="EXPAND" data-container="body" class="toolTip_<?= $rows['groupId'] ?> pendingToolTips">
                                    <a href="#pending_summary_<?= $rows['groupId'] ?>" data-toggle="collapse" class="collapsed collapse_trigger showMemberDetail" data-id="<?= $rows['groupId'] ?>" data-pay-date="<?= $rows['pay_date']; ?>" data-compliant="<?= $rows['is_compliant'] ?>"></a>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="pn" colspan="6">
                                <div id="pending_summary_<?= $rows['groupId'] ?>" class="panel-collapse collapse hrm_summary_expand collapse_pending">
                                    <table class="<?= $table_class ?> table-danger" data-toggle="table" id="hrm_pending_table">
                                        <thead>
                                            <tr>
                                                <?php /*
                                                <th>
                                                    <div class="checkbox checkbox-table">
                                                        <input type="checkbox" id="pay_to_all_member_<?= $rows['groupId'] ?>" class="js-switch pay_to_all_member" data-id="<?= $rows['groupId'] ?>" />
                                                        <label for="pay_to_all_member_<?= $rows['groupId'] ?>"></label>
                                                    </div>
                                                </th>
                                                */ ?>
                                                <th>Member ID</th>
                                                <th>Member Name</th>
                                                <th>Pay Date</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="memberResponse_<?= $rows['groupId'] ?>">
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
    <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
<?php } ?>


<script type="text/javascript">
    $(document).ready(function() {

        $(document).off("click", "#payToAllGroupBox");
        $(document).on("click", "#payToAllGroupBox", function() {
            if ($(this).is(':checked')) {
                $('.payToSpecGroupBox').prop('checked', true);
                $("#hrmActionBtns").show();
            } else {
                $('.payToSpecGroupBox').prop('checked', false);
                if ($('.payToSpecMemberBox:checked').length > 0) {
                    $("#hrmActionBtns").show();
                } else {
                    $("#hrmActionBtns").hide();
                }
            }
        });

        $(document).off("change", ".payToSpecGroupBox");
        $(document).on("change", ".payToSpecGroupBox", function() {
            if ($('.payToSpecGroupBox:checked').length == $('.payToSpecGroupBox').length) {
                $('#payToAllGroupBox').prop('checked', true);
            } else {
                $('#payToAllGroupBox').prop('checked', false);
                if ($('.payToSpecMemberBox:checked').length > 0) {
                    $("#hrmActionBtns").show();
                } else {
                    $("#hrmActionBtns").hide();
                }
            }
            $('.payToSpecGroupBox').each(function(e) {
                if ($(this).is(":checked")) {
                    $("#hrmActionBtns").show();
                }
            });
        });

        $(document).off("click", ".pay_to_all_member");
        $(document).on("click", ".pay_to_all_member", function() {
            var groupId = $(this).attr('data-id');
            if ($(this).is(':checked')) {
                $('#pending_summary_' + groupId + ' .payToSpecMemberBox').prop('checked', true);
                $("#hrmActionBtns").show();
            } else {
                $('#pending_summary_' + groupId + ' .payToSpecMemberBox').prop('checked', false);
                if ($('.payToSpecGroupBox:checked').length > 0) {
                    $("#hrmActionBtns").show();
                } else {
                    $("#hrmActionBtns").hide();
                }
            }
        });

        $(document).off("change", ".payToSpecMemberBox");
        $(document).on("change", ".payToSpecMemberBox", function() {
            var groupId = $(this).attr('data-id');
            if ($('#pending_summary_' + groupId + ' .payToSpecMemberBox:checked').length == $('#pending_summary_' + groupId + ' .payToSpecMemberBox').length) {
                $('#pay_to_all_member_' + groupId).prop('checked', true);
            } else {
                $('#pay_to_all_member_' + groupId).prop('checked', false);
                if ($('.payToSpecGroupBox:checked').length > 0) {
                    $("#hrmActionBtns").show();
                } else {
                    $("#hrmActionBtns").hide();
                }
            }
            $('.payToSpecMemberBox').each(function(e) {
                if ($(this).is(":checked")) {
                    $("#hrmActionBtns").show();
                }
            });
        });

        $(document).off("click", "#approveToPayBtn");
        $(document).on('click', '#approveToPayBtn', function(e) {
            e.preventDefault();

            var groupIds = $('input:checkbox:checked.payToSpecGroupBox').map(function() {
                return this.value;
            }).get();
            var memberIds = $('input:checkbox:checked.payToSpecMemberBox').map(function() {
                return this.value;
            }).get();
            var pay_period = '<?= $pay_period; ?>';
            parent.swal({
                text: '<br>Approve HRM Payment: Are you sure?',
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
                    },
                    beforeSend: function() {
                        $("#ajax_loader").show();
                    },
                    success: function(res) {
                        $("#ajax_loader").hide();
                        if (res.status == 'success') {
                            parent.swal({
                                text: '<br>Approve HRM Payment: Successful',
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
                                text: '<br>Approve HRM Payment: Failed',
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

        $(document).off("click", "#denyToPayBtn");
        $(document).on('click', '#denyToPayBtn', function(e) {
            e.preventDefault();
            pendingHRMPaymentData();
        });

        $(document).off("click", ".showMemberDetail");
        $(document).on("click", ".showMemberDetail", function(e) {
            e.preventDefault();
            var groupId = $(this).attr('data-id');
            var compliant = $(this).attr('data-compliant');
            var pay_period = '<?= $pay_period; ?>';
            /* set to defaul all groups tab*/
            $('.collapse_trigger').addClass('showMemberDetail');
            $('.collapse_trigger').removeClass('hideMemberDetail');
            $('.collapse.collapse_pending').collapse('hide');
            $('.pay_to_all_member').prop('checked', false);
            $('.payToSpecMemberBox').prop('checked', false);

            $('div.group_check').show();
            /***/

            $(this).removeClass('showMemberDetail');
            $(this).addClass('hideMemberDetail');
            $("#group_check_" + groupId).hide();

            $.ajax({
                url: 'ajax_member_datails.php',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    groupId: groupId,
                    hrm_payment_duration: 'weekly',
                    status: 'Pending',
                    compliant: compliant,
                    pay_period: pay_period,
                },
                success: function(res) {
                    if (res.status == 'success') {
                        $("#memberResponse_" + groupId).html(res.html);
                        $(".pendingToolTips").attr("title", "EXPAND");
                        $(".toolTip_" + groupId).attr("title", "CLOSE");
                    }
                }
            });
        });

        $(document).off("click", ".hideMemberDetail");
        $(document).on("click", ".hideMemberDetail", function(e) {
            e.preventDefault();
            var groupId = $(this).attr('data-id');
            $(this).removeClass('hideMemberDetail');
            $(this).addClass('showMemberDetail');
            $("#hrmActionBtns").hide();
            $('div.group_check').show();
            $(".toolTip_" + groupId).attr("title", "EXPAND");
            if ($('.payToSpecGroupBox:checked').length > 0) {
                $("#hrmActionBtns").show();
            }
        });
    });
</script>
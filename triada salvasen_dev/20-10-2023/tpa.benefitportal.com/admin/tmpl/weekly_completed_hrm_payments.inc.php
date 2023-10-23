<?php if ($is_ajaxed) { ?>
    <?php if ($total_rows > 0) { ?>
        <div class="clearfix m-b-20">
            <div id="hrmReverseBtns" style="display: none;">
                <div class="pull-left">
                    <?php /*if ($module_access_type == "rw") { ?>
                        <a href="javascript:void(0);" id="generate_nacha" class="btn btn-info">Generate NACHA</a>
                        <!-- <a href="javascript:void(0);" id="reverseBtn" class="btn btn-info">Reverse Payment</a> -->
                        <a href="javascript:void(0);" id="denyBtn" class="btn btn-info btn-outline">Cancel</a>
                    <?php }*/  ?>
                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                        <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                        <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#compl_per_pages').val(this.value);$('#nav_page').val(1);completedHRMPaymentData();">
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
                                <input type="checkbox" id="generateNachaGroup" class="js-switch" data-toggle="tooltip" title="Select All" />
                                <label for="generateNachaGroup">&nbsp;</label>
                            </div>
                        </th>
                    <?php }*/ ?>
                    <th>Group Name</th>
                    <th>Members</th>
                    <th>Pay Date</th>
                    <th>Total</th>
                    <th class="text-center">Summary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) {
                    foreach ($fetch_rows as $key => $rows) { ?>
                        <tr>
                            <?php /*if ($module_access_type == "rw") { ?>
                                <td>
                                    <div class="checkbox checkbox-table group_complete_check" id="group_complete_check_<?= $key.$rows['groupId'] ?>">
                                        <input type="checkbox" name="cs_approve_to_pay[<?= $rows['groupId'] ?>]" class="js-switch genNachaToSpecGroupBox" id="cs_approve_to_complete_<?= $rows['groupId'] ?>" value="<?= $rows['groupId'] ?>" data-pay-date="<?=$rows['pay_date']?>" />
                                        <label for="cs_approve_to_complete_<?= $rows['groupId'] ?>">&nbsp;</label>
                                    </div>
                                </td>
                            <?php }*/ ?>
                            <td><a href="javascript:void(0);" class="fw500"><?= $rows["groupRepId"] ?></a><br><?= $rows["groupName"] ?></td>
                            <td><?= $rows['memberId']; ?></td>
                            <td><?= displayDate($rows['pay_date']); ?></td>
                            <td><?= dispCommAmt($rows['totalComm']) ?></td>
                            <td class="icons text-center">
                                <span data-toggle="tooltip" title="EXPAND" data-container="body" class="toolCompleteTip_<?= $rows['groupId'] ?> completeToolTips">
                                    <a href="#completed_summary_<?= $key.$rows['groupId'] ?>" data-toggle="collapse" class="collapsed collapse_complete_trigger showMemberCompletedDetail" data-key="<?= $key?>" data-id="<?= $rows['groupId'] ?>" data-nachaid="<?=$rows['nachafile']?>" data-pay-date="<?= $rows['pay_date']; ?>"></a>
                                </span>
                            </td>
                            <td class="icons">
                                <?php if(!empty($rows['nacha_file'])){ ?>
                                <a href="<?= $HOST ?>/downloads3bucketfile.php?file_path=<?= urlencode($NACHA_FILES_PATH) ?>&file_name=<?= urlencode($rows['nacha_file'] . '.' . $rows['file_type']) ?>" class="manually_billing" data-toggle="tooltip" title="NACHA FILE"><i class="fa fa-download"></i></a>
                                <?php } if(!empty($rows['controlfile'])) {?>
                                <a href="<?= $HOST ?>/downloads3bucketfile.php?file_path=<?= urlencode($NACHA_FILES_PATH) ?>&file_name=<?= urlencode($rows['controlfile'] . '.' . $rows['file_type']) ?>" class="manually_billing" data-toggle="tooltip" title="CONTROL FILE"><i class="fa fa-download"></i></a>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td class="pn" colspan="6">
                                <div id="completed_summary_<?= $key.$rows['groupId'] ?>" class="panel-collapse collapse hrm_summary_expand collapse_complete">
                                    <table class="<?= $table_class ?> table-danger" data-toggle="table" id="hrm_completed_table">
                                        <thead>
                                            <tr>
                                                <?php /*
                                                <th>
                                                    <div class="checkbox checkbox-table">
                                                        <input type="checkbox" id="reverse_to_all_member_<?= $rows['groupId'] ?>" class="js-switch reverse_to_all_member" data-key="<?=$key?>" data-id="<?= $rows['groupId'] ?>" />
                                                        <label for="reverse_to_all_member_<?= $rows['groupId'] ?>"></label>
                                                    </div>
                                                </th>
                                                */ ?>
                                                <th>Member ID</th>
                                                <th>Member Name</th>
                                                <th>Pay Date</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="memberCompletedResponse_<?= $key . $rows['groupId'] ?>">
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">No record(s) found</td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                    <tr>
                        <td colspan="7"><?php echo $paginate->links_html; ?></td>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
    <input type="hidden" name="pages" id="compl_per_pages" value="<?= $per_page; ?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
<?php } ?>


<script type="text/javascript">
$(document).ready(function() {

    $(document).off("click", "#generateNachaGroup");
    $(document).on("click", "#generateNachaGroup", function() {
        if ($(this).is(':checked')) {
            $('.genNachaToSpecGroupBox').prop('checked', true);
            $("#hrmReverseBtns").show();
        } else {
            $('.genNachaToSpecGroupBox').prop('checked', false);
            if ($('.genNachaToSpecMemberBox:checked').length > 0) {
                $("#hrmReverseBtns").show();
            } else {
                $("#hrmReverseBtns").hide();
            }
        }
    });

    $(document).off("change", ".genNachaToSpecGroupBox");
    $(document).on("change", ".genNachaToSpecGroupBox", function() {
        if ($('.genNachaToSpecGroupBox:checked').length == $('.genNachaToSpecGroupBox').length) {
            $('#generateNachaGroup').prop('checked', true);
        } else {
            $('#generateNachaGroup').prop('checked', false);
            if ($('.genNachaToSpecMemberBox:checked').length > 0) {
                $("#hrmReverseBtns").show();
            } else {
                $("#hrmReverseBtns").hide();
            }
        }
        $('.genNachaToSpecGroupBox').each(function(e) {
            if ($(this).is(":checked")) {
                $("#hrmReverseBtns").show();
            }
        });
    });

    $(document).off("click", ".reverse_to_all_member");
    $(document).on("click", ".reverse_to_all_member", function() {
        var groupId = $(this).attr('data-id');
        var key = $(this).attr('data-key');
        if ($(this).is(':checked')) {
            $('#completed_summary_' + key + groupId + ' .genNachaToSpecMemberBox').prop('checked', true);
            $("#hrmReverseBtns").show();
        } else {
            $('#completed_summary_' + key  + groupId + ' .genNachaToSpecMemberBox').prop('checked', false);
            if ($('.genNachaToSpecGroupBox:checked').length > 0) {
                $("#hrmReverseBtns").show();
            } else {
                $("#hrmReverseBtns").hide();
            }
        }
    });

    $(document).off("change", ".genNachaToSpecMemberBox");
    $(document).on("change", ".genNachaToSpecMemberBox", function() {
        var groupId = $(this).attr('data-id');
        var key = $(this).attr('data-key');
        if ($('#completed_summary_' + groupId + ' .genNachaToSpecMemberBox:checked').length == $('#completed_summary_' + groupId + ' .genNachaToSpecMemberBox').length) {
            $('#reverse_to_all_member_' + groupId).prop('checked', true);
        } else {
            $('#reverse_to_all_member_' + groupId).prop('checked', false);
            if ($('.genNachaToSpecGroupBox:checked').length > 0) {
                $("#hrmReverseBtns").show();
            } else {
                $("#hrmReverseBtns").hide();
            }
        }
        $('.genNachaToSpecMemberBox').each(function(e) {
            if ($(this).is(":checked")) {
                $("#hrmReverseBtns").show();
            }
        });
    });

    /*$(document).off("click", "#reverseBtn");
    $(document).on('click', '#reverseBtn', function(e) {
        var groupIds = $('input:checkbox:checked.genNachaToSpecGroupBox').map(function() {
            return this.value;
        }).get();
        var memberIds = $('input:checkbox:checked.genNachaToSpecMemberBox').map(function() {
            return this.value;
        }).get();
        var pay_period = '<?= $pay_period; ?>';
        parent.swal({
            text: '<br>Reverse HRM Payment: Are you sure?',
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
                    action: 'revCompletedHRMPayment',
                    memberIds: memberIds,
                },
                beforeSend: function() {
                    $("#ajax_loader").show();
                },
                success: function(res) {
                    $("#ajax_loader").hide();
                    if (res.status == 'success') {
                        parent.swal({
                            text: '<br>Reverse HRM Payment: Successful',
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
                            text: '<br>Reverse HRM Payment: Failed',
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
    });*/

    $(document).off("click", "#generate_nacha");
    $(document).on('click', '#generate_nacha', function(e) {
        var groupIds = $('input:checkbox:checked.genNachaToSpecGroupBox').map(function() {
            return this.value;
        }).get();
        var memberIds = $('input:checkbox:checked.genNachaToSpecMemberBox').map(function() {
            return this.value;
        }).get();

        var payDate = $('input:checkbox:checked.genNachaToSpecGroupBox').map(function() {
            return $(this).data('pay-date');
        }).get();
        
        var pay_period = '<?= $pay_period; ?>';
        parent.swal({
            text: '<br>Generate Nacha: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }).then(function() {
            $.ajax({
                url: 'ajax_generate_nacha.php',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    groupIds: groupIds,
                    payDate: payDate,
                    pay_period: pay_period,
                    memberIds: memberIds,
                    hrm_payment_duration: 'weekly',
                },
                beforeSend: function() {
                    $("#ajax_loader").show();
                },
                success: function(res) {
                    $("#ajax_loader").hide();
                    if (res.status == 'success') {
                        parent.swal({
                            text: '<br>Generate NACHA: Successful',
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Close',
                        }).then(function() {
                            window.location.href = 'nacha_files.php';
                        }, function(dismiss) {
                            window.location.href = 'nacha_files.php';
                        });
                    } else {
                        parent.swal({
                            text: '<br>Generate NACHA: Failed',
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

    $(document).off("click", "#denyBtn");
    $(document).on('click', '#denyBtn', function(e) {
        e.preventDefault();
        completedHRMPaymentData();
    });

    $(document).off("click", ".showMemberCompletedDetail");
    $(document).on("click", ".showMemberCompletedDetail", function(e) {
        e.preventDefault();
        var groupId = $(this).attr('data-id');
        var pay_period = '<?= $pay_period; ?>';
        var pay_date = $(this).attr('data-pay-date');
        var key = $(this).attr('data-key');
        var nachaid = $(this).attr('data-nachaid');
        
        /* set to defaul all groups tab*/
        $('.collapse_complete_trigger').addClass('showMemberCompletedDetail');
        $('.collapse_complete_trigger').removeClass('hideMemberCompletedDetail');
        $('.collapse.collapse_complete').collapse('hide');
        $('.reverse_to_all_member').prop('checked', false);
        $('.genNachaToSpecMemberBox').prop('checked', false);
        $('div.group_complete_check').show();
        /***/
        $(this).removeClass('showMemberCompletedDetail');
        $(this).addClass('hideMemberCompletedDetail');
        $("#group_complete_check_" + key+groupId).hide();
        $.ajax({
            url: 'ajax_member_datails.php',
            type: 'GET',
            dataType: 'JSON',
            data: {
                groupId: groupId,
                pay_date: pay_date,
                pay_period: pay_period,
                hrm_payment_duration: 'weekly',
                status: 'Completed',
                nachaid:nachaid,
                compliant: 'Y',
                key: key,
            },
            success: function(res) {
                if (res.status == 'success') {
                    $("#memberCompletedResponse_"+key + groupId).html(res.html);
                    $(".completeToolTips").attr("title", "EXPAND");
                    $(".toolCompleteTip_" + groupId).attr("title", "CLOSE");
                    common_select();
                }
            }
        });
    });
    $(document).off("click", ".hideMemberCompletedDetail");
    $(document).on("click", ".hideMemberCompletedDetail", function(e) {
        e.preventDefault();
        var groupId = $(this).attr('data-id');
        $(this).removeClass('hideMemberCompletedDetail');
        $(this).addClass('showMemberCompletedDetail');
        $("#hrmReverseBtns").hide();
        $('div.group_complete_check').show();
        $(".toolCompleteTip_" + groupId).attr("title", "EXPAND");
        if ($('.genNachaToSpecGroupBox:checked').length > 0) {
            $("#hrmReverseBtns").show();
        }
    });
});
</script>
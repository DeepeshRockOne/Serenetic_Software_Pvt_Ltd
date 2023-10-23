<?php if ($is_ajaxed_leads) { ?>
    <div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th>
                    <a href="javascript:void(0);" data-column="l.created_at"
                       data-direction="<?php echo $SortBy == 'l.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added
                        Date</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.fname"
                       data-direction="<?php echo $SortBy == 'l.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a>
                </th>
                <th width="20%">
                    <a href="javascript:void(0);" data-column="l.opt_in_type" data-direction="<?php echo $SortBy == 'l.opt_in_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Enrollee Tag</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.status" data-direction="<?php echo $SortBy == 'l.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a>
                </th>
                <th width="90px" class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td>
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" class="text-red">
                                <strong class="fw600"><?php echo $rows['lead_id']; ?></strong></a></br>
                            <?php echo empty($rows['created_at']) ? date('m/d/Y', strtotime($rows['invite_at'])) : date('m/d/Y', strtotime($rows['created_at'])); ?>
                        </td>
                        <td>
                            <strong><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></strong><br>
                            <?php 
                                $format_telephone = format_telephone($rows['cell_phone']);
                                if(!empty($format_telephone)) {
                                    echo $format_telephone.'<br/>';
                                }
                                echo $rows['email'];
                            ?>
                        </td>
                        <td>
                            <div class="theme-form pr w-200">
                                 <select class="form-control lead_tag listing_search <?=!empty($rows['opt_in_type'])?'has-value':''?>" id="lead_tag_<?=$rows['id'];?>">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($lead_tag_res)) {
                                        foreach ($lead_tag_res as $key => $lead_tag_row) {
                                            ?>
                                            <option value="<?= $lead_tag_row['tag'] ?>" <?php echo $rows['opt_in_type'] == $lead_tag_row['tag'] ? "selected" : ""; ?>><?= $lead_tag_row['tag'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td>
                            <div class="theme-form pr w-160">
                                <select class="form-control lead_status <?php echo in_array($rows['status'], array("New", "Working", "Open", "Unqualified", "Abandoned", "Converted")) ? "has-value" : ""; ?>"
                                 id="lead_status_<?=$rows['id'];?>">
                                    <option></option>
                                    <option value="New" <?php echo $rows['status'] == "New" ? "selected" : ""; ?>>New
                                    </option>
                                    <option value="Working" <?php echo $rows['status'] == "Working" ? "selected" : ""; ?>>
                                        Working
                                    </option>
                                    <option value="Open" <?php echo $rows['status'] == "Open" ? "selected" : ""; ?>>
                                        Open
                                    </option>
                                    <option value="Unqualified" <?php echo $rows['status'] == "Unqualified" ? "selected" : ""; ?>>
                                        Unqualified
                                    </option>
                                    <option value="Abandoned" <?php echo $rows['status'] == "Abandoned" ? "selected" : ""; ?>>
                                        Abandoned
                                    </option>
                                    <option value="Converted" <?php echo $rows['status'] == "Converted" ? "selected" : ""; ?>>
                                        Converted
                                    </option>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="icons text-left">
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" data-toggle="tooltip" title="Details">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" align="center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                    <td colspan="6">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <form id="frm_search_leads" action="global_enrollees.php" method="GET" class="sform" >
    <input type="hidden" name="search_type" id="search_type" value="" />
    <input type="hidden" name="is_ajaxed_leads" id="is_ajaxed_leads" value="1" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    </form>
    <div class="panel-body">
        <div id="ajax_loader" class="ajex_loader" style="display: none;">
            <div class="loader"></div>
        </div>
        <div id="ajax_data" style="display: none;"> </div>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function () {
    $(".date_picker").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });

    ajax_submit_leads();

    // $("#lead_id").multipleSelect({
    //     selectAll: false
    // });
    // $("#classSearch, #lead_status, #lead_tag").multipleSelect({
    //     selectAll: false,
    //     filter: false
    // });

    $(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function (e) {
        e.preventDefault();
        if ($(this).val() == '') {
            $('.select_date_div').hide();
            $('#date_range').removeClass('col-md-3').addClass('col-md-12');
        } else {
            $('#date_range').removeClass('col-md-12').addClass('col-md-3');
            $('.select_date_div').show();
            if ($(this).val() == "Range") {
                $('#range_join').show();
                $('#all_join').hide();
            } else {
                $('#range_join').hide();
                $('#all_join').show();
            }
        }
    });

    $(document).off('click', '#export_lead');
    $(document).on('click', '#export_lead', function(e) {
        swal({
            text: 'Export to excel all Leads: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function() {
            $('#ajax_loader').show();
            $("#export").val('export_lead');
            $('#is_ajaxed').val('1');
            var params = $('#frm_search').serialize();
            $.ajax({
                url: $('#frm_search').attr('action'),
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function() {
                    $('#ajax_loader').hide();
                    $("#export").val('');
                }
            }).done(function(data) {
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "Leads.xls");
                $a[0].click();
                $a.remove();
                $('#ajax_loader').hide();
            });
        }, function(dismiss) {

        });
    });

    $(document).off('click', '.btn_invite_agent_group');
    $(document).on("click", ".btn_invite_agent_group", function(e) {
        var id = $(this).attr('data-id');
        swal({
            html: "<h4 class='m-b-0'>Select the type of invite below</h4>" +
                "<br>" +
                '<a href="invite_agent.php?lead_id='+id+'" class="btn btn-info">Agent Invite</a>' +
                '<a href="invite_group.php?lead_id='+id+'" class="btn btn-action m-l-10">Group Invite</a>' + 
                '<small><a href="javascript:void(0);" class="red-link m-l-10" onClick="swal.close();">Cancel</a></small>',
            showCancelButton: false,
            showConfirmButton: false
        });
        /*swal({
            text: "Select the type of invite below",
            showCancelButton: true,
            cancelButtonText: "Agent Invite",
            confirmButtonText: "Group Invite"
        }).then(function() {

        }, function(dismiss) {
            
        });*/
    });

    $(document).off('change', '.lead_status');
    $(document).on("change", ".lead_status", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('lead_status_', '');
        var lead_status = $(this).val();
        swal({
            text: "Change Status: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function() {
            $.ajax({
                url: 'change_lead_status.php',
                data: {
                    id: id,
                    status: lead_status
                },
                method: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status == "success") {
                        setNotifySuccess(res.msg);
                    }else{
                        setNotifyError(res.msg);
                        ajax_submit_leads();
                    }
                }
            });
        }, function(dismiss) {
            ajax_submit_leads();
        })
    });

    $(document).off('change', '.lead_tag');
    $(document).on("change", ".lead_tag", function(e) {
        e.stopPropagation();
        var id = $(this).attr('id').replace('lead_tag_', '');
        var lead_tag = $(this).val();
        swal({
            text: "Change Tag: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function() {
            $.ajax({
                url: 'change_lead_tag.php',
                data: {
                    id: id,
                    lead_tag: lead_tag
                },
                method: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status == "success") {
                        setNotifySuccess(res.msg);
                    }else{
                        setNotifyError(res.msg);
                        ajax_submit_leads();
                    }
                }
            });
        }, function(dismiss) {
            ajax_submit_leads();
        })
    });

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function (e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit_leads();
    });

    $(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();
            }
        });
    });

    $(document).off("submit", "#frm_search");
    $(document).on("submit", "#frm_search", function (e) {
        e.preventDefault();
        disable_search();
    });
});

function ajax_submit_leads() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed_leads').val('1');
    var params = $('#frm_search_leads').serialize();
    var all_usersFrm = $('#all_usersFrm').serialize();
    params += '&' + all_usersFrm;
    $.ajax({
      url: $('#frm_search_leads').attr('action'),
      type: 'GET',
      data: params,
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
      }
    });
    return false;
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
}
</script>
<?php } ?>
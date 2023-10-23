<?php if ($is_ajaxed) { ?>
    <div class="clearfix tbl_filter">
         <?php if ($total_rows > 0) { ?>
                <div class="pull-left">
                    <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                        <div class="form-group mn">
                            <label for="">Records Per Page </label>
                        </div>
                        <div class="form-group mn">
                            <select size="1" id="pages" name="pages" class="form-control select2 placeholder"
                                    onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                                <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>
                                    10
                                </option>
                                <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>
                                    25
                                </option>
                                <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>
                                    50
                                </option>
                                <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>
                                    100
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="pull-right">
                <div class="m-b-15">
                    <a class="btn btn-default" href="leads_import_summary.php">Import Summary</a>

                    <a class="btn btn-action" href="group_add_csv_enrollee.php">+ Enrollee</a>
                </div>
            </div>
    </div>
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
                            <a href="javascript:void(0);" onclick="deleteEnrollee('<?=$rows['id']?>');" data-toggle="tooltip" title="Delete">
                                <i class="fa fa-trash"></i>
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
    <div class="container m-t-30">
        <div class="panel panel-default panel-block panel-title-block">
            <form id="frm_search" action="group_enrollees.php" method="GET" class="theme-form">
                <div class="panel-left">
                    <div class="panel-left-nav">
                        <ul>
                            <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="panel-right">
                    <div class="panel-heading">
                        <div class="panel-search-title">
                            <span class="clr-light-blk">SEARCH</span>
                        </div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body theme-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="lead_id[]" id="lead_id"
                                                multiple="multiple">
                                            <?php if (!empty($lead_res)) { ?>
                                                <?php foreach ($lead_res as $value) { ?>
                                                    <option value="<?= $value['lead_id'] ?>"><?= $value['lead_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label>ID Number(s)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div id="date_range" class="col-md-12">
                                            <div class="form-group">
                                                <select class="form-control" id="join_range" name="join_range">
                                                    <option value=""></option>
                                                    <option value="Range">Range</option>
                                                    <option value="Exactly">Exactly</option>
                                                    <option value="Before">Before</option>
                                                    <option value="After">After</option>
                                                </select>
                                                <label>Added Date</label>
                                            </div>
                                        </div>
                                        <div class="select_date_div col-md-9" style="display:none">
                                            <div class="form-group">
                                                <div id="all_join" class="input-group">
                                                    <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                    <input type="text" name="added_date" id="added_date" value=""
                                                           class="form-control date_picker"/>
                                                </div>
                                                <div id="range_join" style="display:none;">
                                                    <div class="phone-control-wrap">
                                                         <div class="phone-addon">
                                                            <label class="mn">From</label>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i
                                                                            class="fa fa-calendar"></i></span>
                                                                <input type="text" name="fromdate" id="fromdate"
                                                                           value="" class="form-control date_picker"/>
                                                            </div>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <label class="mn">To</label>
                                                        </div>
                                                        <div class="phone-addon">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i
                                                                            class="fa fa-calendar"></i></span>
                                                                <input type="text" name="todate" id="todate" value=""
                                                                           class="form-control date_picker"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <input type="text" name="lead_name" value="<?php echo $lead_name ?>" class="form-control listing_search">
                                        <label>Name</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <input type="text" class="form-control listing_search" name="phone" maxlength='10' onkeypress="return isNumberKey(event)" value="<?php echo $phone ?>">
                                        <label>Phone</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <input type="text" class="form-control listing_search" name="email" value="<?php echo $email ?>">
                                        <label>Email</label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="lead_tag[]" id="lead_tag" multiple="multiple">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($lead_tag_res)) {
                                                foreach ($lead_tag_res as $key => $lead_tag_row) {
                                                    ?>
                                                    <option value="<?= $lead_tag_row['tag'] ?>"><?= $lead_tag_row['tag'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label>Enrollee Tag</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="classSearch[]" id="classSearch" multiple="multiple">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($resClass)) {
                                                foreach ($resClass as $key => $classRow) {
                                                    ?>
                                                    <option value="<?= $classRow['id'] ?>"><?= $classRow['class_name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label>Class</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <select class="se_multiple_select listing_search" name="leads_status[]" id="lead_status" multiple="multiple">
                                            <option value="New">New</option>
                                            <option value="Working">Working</option>
                                            <option value="Open">Open</option>
                                            <option value="Unqualified">Unqualified</option>
                                            <option value="Abandoned">Abandoned</option>
                                            <option value="Converted">Converted</option>
                                        </select>
                                        <label>Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer clearfix">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <button type="button" class="btn btn-info btn-outline" onClick="window.location = 'group_enrollees.php'">
                                    <i class="fa fa-search-plus"></i> View All
                                </button>
                                <button type="button" name="export_lead" id="export_lead" class="btn red-link"><i class="fa fa-download"></i> Export</button>
                                <input type="hidden" name="export" id="export" value=""/>
                                <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                                <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                                <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
                                <input type="hidden" name="sort_direction" id="sort_by_direction"
                                       value="<?= $SortDirection; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-handle">
                    <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
                </div>
            </form>
        </div>
        <div class="panel panel-default panel-block">
            <div class="panel-body">
                <div id="ajax_loader" class="ajex_loader" style="display: none;">
                    <div class="loader"></div>
                </div>
                <div id="ajax_data" ></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
    dropdown_pagination('ajax_data')
            $(".date_picker").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });

            ajax_submit();

            $("#lead_id").multipleSelect({
                selectAll: false
            });
            $("#classSearch, #lead_status, #lead_tag").multipleSelect({
                selectAll: false,
                filter: false
            });

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
                                ajax_submit();
                            }
                        }
                    });
                }, function(dismiss) {
                    ajax_submit();
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
                                ajax_submit();
                            }
                        }
                    });
                }, function(dismiss) {
                    ajax_submit();
                })
            });

            $(document).off('click', '#ajax_data tr.data-head a');
            $(document).on('click', '#ajax_data tr.data-head a', function (e) {
                e.preventDefault();
                $('#sort_by_column').val($(this).attr('data-column'));
                $('#sort_by_direction').val($(this).attr('data-direction'));
                ajax_submit();
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

        function ajax_submit() {
            $('#ajax_loader').show();
            $('#ajax_data').hide();
            $('#is_ajaxed').val('1');
            var params = $('#frm_search').serialize();
            $.ajax({
                url: $('#frm_search').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();
                    $('[data-toggle="tooltip"]').tooltip();
                    common_select();
                    $("[data-toggle=popover]").each(function (i, obj) {
                        $(this).popover({
                            html: true,
                            placement: 'auto bottom',
                            content: function () {
                                var id = $(this).attr('data-user_id')
                                return $('#popover_content_' + id).html();
                            }
                        });
                    });
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
        function deleteEnrollee(id){
            swal({
                text: "Delete Record: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel"
            }).then(function() {
                $.ajax({
                    url: 'group_enrollees.php',
                    data: {
                        id: id,
                        action: 'delete'
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.message);
                            ajax_submit();
                        }else{
                            setNotifyError(res.message);
                            ajax_submit();
                        }
                    }
                });
            }, function(dismiss) {
                ajax_submit();
            })
        }
    </script>
<?php } ?>
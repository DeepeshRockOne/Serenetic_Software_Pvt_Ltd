<?php if ($is_ajaxed) { ?>
    <div class="clearfix">
        <?php if ($total_rows > 0) { ?>
            <div style="display: none;" class="pull-left m-b-15" id="participants_operation">
                <button type="button" class="btn red-link v-align-top" id="btnDelMultiple">Delete</button>
            </div>
        <?php } ?>
        <div class="pull-right">
            <div class="m-b-15">
            <a class="btn btn-default" href="manage_participants.php" target="_blank">Manage Participants</a>
            <a class="btn btn-action" href="add_participants.php">+ Participants</a>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th width="100px" class="text-center">
                    <div class="checkbox checkbox-custom checkbox-table">
                        <input id="chk_all" type="checkbox" name="chk_all" id="chk_all">
                        <label for="chk_all"></label>
                    </div>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="p.created_at"
                       data-direction="<?php echo $SortBy == 'p.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="p.fname"
                       data-direction="<?php echo $SortBy == 'p.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="p.participants_type"
                       data-direction="<?php echo $SortBy == 'p.participants_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Type</a>
                </th>
                <th width="15%">
                    <a href="javascript:void(0);" data-column="a.fname"
                       data-direction="<?php echo $SortBy == 'a.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Added By/ID</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="p.participants_tag"
                       data-direction="<?php echo $SortBy == 'p.participants_tag' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Participant Tag</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="p.status"
                       data-direction="<?php echo $SortBy == 'p.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a>
                </th>
                <th width="90px" class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($total_rows > 0) { ?>
                <?php foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td class="text-center">
                            <div class="checkbox checkbox-custom checkbox-table">
                                <input type="checkbox" name="check_record[]" id="check_record_<?= $rows['id'] ?>"
                                       data-id='<?= $rows['id'] ?>' class="check_record" value='<?= $rows['id'] ?>'>
                                <label for="check_record"></label>
                            </div>
                        </td>
                        <td>
                            <a href="participants_details.php?id=<?= $rows['id'] ?>" target="_blank" class="text-red"><strong class="fw600"><?php echo $rows['participants_id']; ?></strong></a></br><?=displayDate($rows['addedDate'])?>
                        </td>
                        <td>
                            <strong><?=stripslashes($rows['name'])?></strong><br>
                            <?php
                            $format_telephone = format_telephone($rows['cell_phone']);
                            if (!empty($format_telephone)) {
                                echo $format_telephone . '<br/>';
                            }
                            echo $rows['email'];
                            ?>
                        </td>
                        <td><?php echo $rows['participants_type']; ?></td>
                        <td>
                            <a href="admin_profile.php?id=<?= md5($rows['adminId']) ?>" target="_blank"
                            class="text-red"><strong class="fw600"><?=$rows['adminDispId']?></strong></a>
                            </br>
                            <?=$rows['adminName']?>
                        </td>
                        <td>
                            <div class="theme-form pr w-200">
                                <select class="form-control participants_tag listing_search" data-id="<?=$rows['id']?>">
                                    <option></option>
                                    <?php
                                    if (!empty($participants_tag_res)) {
                                        foreach ($participants_tag_res as $tagRow) {
                                            ?>
                                            <option value="<?= $tagRow['tag'] ?>" <?php echo $rows['participants_tag'] == $tagRow['tag'] ? "selected" : ""; ?>><?= $tagRow['tag'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td>
                            <div class="theme-form pr w-130">
                                <select class="form-control participants_status" data-id="<?=$rows['id']?>">
                                <option></option>
                                <option value="New" <?=$rows['status'] == "New" ? "selected" : ""?>>New
                                </option>
                                <option value="Working" <?=$rows['status'] == "Working" ? "selected" : ""?>>
                                    Working
                                </option>
                                <option value="Open" <?=$rows['status'] == "Open" ? "selected" : ""?>>
                                    Open
                                </option>
                                <option value="Unqualified" <?=$rows['status'] == "Unqualified" ? "selected" : ""?>>
                                    Unqualified
                                </option>
                                <option value="Abandoned" <?=$rows['status'] == "Abandoned" ? "selected" : ""?>>
                                    Abandoned
                                </option>
                                <option value="Converted" <?=$rows['status'] == "Converted" ? "selected" : ""?>>
                                    Converted
                                </option>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="icons text-left">
                            <a href="participants_details.php?id=<?= $rows['id'] ?>" target="_blank" data-toggle="tooltip" data-trigger="hover" title="Details"><i class="fa fa-eye"></i></a>
                            <a href="javascript:void(0);" class="btn_delete" data-id="<?= $rows['id'] ?>"
                               data-toggle="tooltip" data-trigger="hover" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="8" align="center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <div class="panel panel-default panel-block panel-title-block">
    <form id="frm_search" action="participants_listing.php" method="GET" class="theme-form" autocomplete="off">
        <div class="panel-left">
            <div class="panel-left-nav">
                <ul><li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
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
                        <div class="form-group height_auto">
                        <input type="text" class="form-control listing_search" name="participants_id"
                                   value="<?php echo $participants_id ?>">
                        <label>ID Number(s)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group height_auto">
                        <input type="text" class="form-control listing_search" name="employee_id"
                                   value="<?php echo $employee_id ?>">
                        <label>Emp. Alternate ID</label>
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
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <input type="text" name="participants_name" value="<?php echo $participants_name ?>"
                                   class="form-control listing_search">
                            <label>Name</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <input type="text" class="form-control listing_search" name="phone" maxlength='10'
                                   onkeypress="return isNumberKey(event)" value="<?php echo $phone ?>">
                            <label>Phone</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <input type="text" class="form-control listing_search" name="email"
                                   value="<?php echo $email ?>">
                            <label>Email</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <select class="se_multiple_select listing_search" name="participants_type" id="participants_type_srch" multiple="multiple">
                                <option value="Member">Member</option>
                            </select>
                            <label>Type</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <select class="se_multiple_select listing_search" name="participants_tag[]" id="participants_tag_srch" multiple="multiple">
                                <?php
                                if (!empty($participants_tag_res)) {
                                    foreach ($participants_tag_res as $key => $tagRow) {
                                ?>
                                    <option value="<?= $tagRow['tag'] ?>"><?= $tagRow['tag'] ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label>Participant Tag</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group height_auto">
                            <select class="se_multiple_select listing_search" name="participants_status[]"
                                    id="participants_status_srch" multiple="multiple">
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
                    <div class="clearfix tbl_search_filter">
                     <div class="pull-left">
                        <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-info btn-outline" onClick="window.location = 'participants_listing.php'"><i class="fa fa-search-plus"></i> View All</button>
                        <button type="button" name="export_participants" id="export_participants" class="btn red-link"><i class="fa fa-download"></i> Export</button>
                        <input type="hidden" name="export" id="export" value=""/>
                        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                        <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                        <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
                        <input type="hidden" name="sort_direction" id="sort_by_direction"
                           value="<?= $SortDirection; ?>"/>
                     </div>
                     <div class="pull-right">
                        <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                           <div class="form-group mn height_auto">
                              <label for="">Records Per Page </label>
                           </div>
                           <div class="form-group mn height_auto">
                              <select size="1" id="pages" name="pages"
                                 class="form-control select2 placeholder"
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
                    </div>
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
            <div id="ajax_data"></div>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function () {
        var execute=function(){
            fRefresh();
        }
        dropdown_pagination(execute,'ajax_data');
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true
        });

        ajax_submit();

        $("#participants_type_srch, #participants_status_srch, #participants_tag_srch").multipleSelect({
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
                    fRefresh();
                }
            });
        });

        $(document).off("submit", "#frm_search");
        $(document).on("submit", "#frm_search", function (e) {
            e.preventDefault();
            disable_search();
        });

        $(document).off('click', '#export_participants');
        $(document).on('click', '#export_participants', function(e) {
            e.stopPropagation();
            confirm_export_data(function() {
                $("#export").val('export_participants');
                $('#ajax_loader').show();
                $('#is_ajaxed').val('1');
                var params = $('#frm_search').serialize();
                $.ajax({
                    url: $('#frm_search').attr('action'),
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(res) {
                        $('#ajax_loader').hide();
                         $("#export").val('');
                        if(res.status == "success") {
                            confirm_view_export_request();
                        } else {
                            setNotifyError(res.message);
                        }
                    }
                });
            });
        });

        $(document).off('change', '.participants_status');
        $(document).on("change", ".participants_status", function (e) {
            e.stopPropagation();
            var ptId = $(this).attr('data-id');
            var participants_status = $(this).val();
            swal({
                text: "Change Status: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                $.ajax({
                    url: 'ajax_participants_operations.php',
                    data: {
                        ptId: ptId,
                        status: participants_status,
                        action:"changeStatus"
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.message);
                        } else {
                            setNotifyError(res.message);
                            ajax_submit();
                        }
                    }
                });
            }, function (dismiss) {
                ajax_submit();
            })
        });

        $(document).off('change', '.participants_tag');
        $(document).on("change", ".participants_tag", function (e) {
            e.stopPropagation();
            var ptId = $(this).attr('data-id');
            var participants_tag = $(this).val();
            swal({
                text: "Change Tag: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                $.ajax({
                    url: 'ajax_participants_operations.php',
                    data: {
                        ptId: ptId,
                        participants_tag: participants_tag,
                        action:"changeTag"
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.message);
                        } else {
                            setNotifyError(res.message);
                            ajax_submit();
                        }
                    }
                });
            }, function (dismiss) {
                ajax_submit();
            })
        });

        $(document).off('click', '#chk_all');
        $(document).on('click', '#chk_all', function () {
            if ($(this).prop('checked') == true) {
                $(".check_record").prop("checked", true);
                $("#participants_operation").show();
            } else {
                $(".check_record").prop("checked", false);
                $("#participants_operation").hide();
            }
        });

        $(document).off('click', '.check_record');
        $(document).on('click', '.check_record', function () {
            var len = $('[name="check_record[]"]:checked').length;
            if (len > 0) {
                $("#participants_operation").show();
            } else {
                $("#participants_operation").hide();
            }
        });

        $(document).off('click', '.btn_delete');
        $(document).on("click", ".btn_delete", function (e) {
            var ptId = $(this).attr('data-id');
            swal({
                text: "Delete Participants: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                $.ajax({
                    url: 'ajax_participants_operations.php',
                    data: {
                        ptId: ptId,
                        action:"deleteParticipants"
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == "success") {
                            setNotifySuccess(res.msg);
                        } else {
                            setNotifyError(res.msg);
                        }
                        ajax_submit();
                    }
                });
            }, function (dismiss) {

            });
        });

        $(document).off('click', '#btnDelMultiple');
        $(document).on('click', '#btnDelMultiple', function () {
            var ptId = [];
            $("input[name='check_record[]']:checked").each(function () {
                ptId.push($(this).val());
            });

            swal({
                text: "Delete Participants: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function () {
                $('#ajax_loader').show();
                $.ajax({
                    url: "ajax_participants_operations.php",
                    type: 'POST',
                    data: {
                        ptId: ptId,
                        action:"deleteParticipants"
                    },
                    dataType: 'json',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        if (res.status == 'success') {
                            setNotifySuccess(res.message);
                        }
                        ajax_submit();
                    }
                });
            }, function (dismiss) {
                ajax_submit();
            });
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
                fRefresh();
            }
        });
        return false;
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<?php } ?>
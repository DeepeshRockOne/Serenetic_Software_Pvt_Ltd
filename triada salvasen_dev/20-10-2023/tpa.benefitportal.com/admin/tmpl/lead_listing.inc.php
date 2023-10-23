<?php if ($is_ajaxed) { ?>
    <input type="hidden" name="curr_ajax_url" id="curr_ajax_url" value="<?=$curr_page_url;?>">
    <div class="clearfix lead_filter">
        <?php if ($total_rows > 0) { ?>
            <div style="display: none;" class="pull-left m-b-15" id="lead_operation">
            <span  id="assigned_to_multiple_td">
            <button type="button" class="btn btn-info" id="assigned_to_multiple"><i
               class="icon-user-following"></i> Assign Leads</button>
            </span>
            <span id="assigned_to" style="display: none;" class="assign_lead_filter">
                <div class="phone-control-wrap m-l-5 theme-form">
                    <div class="phone-addon">
                        <div class="pr">
                            <select name="assign_user" id="assign_user" class="form-control select2"
                                data-live-search="true">
                                <option data-hidden="true"></option>
                                <?php
                                if (!empty($agent_res)) {
                                foreach ($agent_res as $key => $agent_row) {
                                ?>
                                <option value="<?= $agent_row['id'] ?>"><?= $agent_row['rep_id'] . ' - ' . $agent_row['name'] ?></option>
                                <?php
                                }
                                }
                                ?>
                            </select>
                            <label>Select Agent</label>
                        </div>
                    </div>
                    <div class="phone-addon w-90 v-align-top">
                        <button type="button" class="btn btn-success btn-block" id="assigned_btn"><i
                        class="icon-user-following"></i> Assign</button>
                    </div>
                </div>
            </span>
             <button type="button" class="btn red-link v-align-top" id="btn_delete_multiple_lead">
             Delete
            </button>
         </div>
            <?php } ?>
            <div class="pull-right">
                <div class="m-b-15">
                    <a class="btn btn-default" href="manage_leads.php" target="_blank">Manage Leads</a>

                    <a class="btn btn-action" href="add_leads.php">+ Leads</a>
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
                    <a href="javascript:void(0);" data-column="l.created_at"
                       data-direction="<?php echo $SortBy == 'l.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added
                        Date</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.fname"
                       data-direction="<?php echo $SortBy == 'l.fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Details</a>
                </th>
                <th >
                    <a href="javascript:void(0);" data-column="l.lead_type"
                       data-direction="<?php echo $SortBy == 'l.lead_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Lead
                        Type</a></th>
                <th width="15%">
                    <a href="javascript:void(0);" data-column="sponsor_name"
                       data-direction="<?php echo $SortBy == 'sponsor_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Added
                        By/ID</a>
                </th>
                <th >
                    <a href="javascript:void(0);" data-column="l.opt_in_type"
                       data-direction="<?php echo $SortBy == 'l.opt_in_type' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Lead
                        Tag</a>
                </th>
                <th>
                    <a href="javascript:void(0);" data-column="l.status"
                       data-direction="<?php echo $SortBy == 'l.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a>
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
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" class="text-red">
                                <strong class="fw600"><?php echo $rows['lead_id']; ?></strong></a></br>
                            <?php echo empty($rows['created_at']) ? date('m/d/Y', strtotime($rows['invite_at'])) : date('m/d/Y', strtotime($rows['created_at'])); ?>
                        </td>
                        <td>
                            <strong><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></strong><br>
                            <?php
                            $format_telephone = format_telephone($rows['cell_phone']);
                            if (!empty($format_telephone)) {
                                echo $format_telephone . '<br/>';
                            }
                            echo $rows['email'];
                            ?>
                        </td>
                        <td><?php echo $rows['lead_type']; ?></td>
                        <td>
                            <?php if($rows['sponsor_type'] == "Agent") { ?>
                                <a href="agent_detail_v1.php?id=<?= md5($rows['sponsor_id']) ?>" target="_blank"
                                   class="text-red"><strong
                                            class="fw600"><?php echo $rows['sponsor_rep_id']; ?></strong></a></br>
                            <?php } elseif($rows['sponsor_type'] == "Group") { ?>
                                <a href="groups_details.php?id=<?= md5($rows['sponsor_id']) ?>" target="_blank"
                                   class="text-red"><strong
                                            class="fw600"><?php echo $rows['sponsor_rep_id']; ?></strong></a></br>
                            <?php } else { ?>
                                <a href="javascript:void(0);" class="text-red"><strong class="fw600"><?php echo $rows['sponsor_rep_id']; ?></strong></a></br>
                            <?php } ?>
                            <?php echo $rows['sponsor_name']; ?>
                        </td>
                        <td>
                            <div class="theme-form pr w-200">
                                <select class="form-control lead_tag listing_search <?= !empty($rows['opt_in_type']) ? 'has-value' : '' ?>"
                                        id="lead_tag_<?= $rows['id']; ?>">
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
                            <div class="theme-form pr w-130">
                                <select class="form-control lead_status <?php echo in_array($rows['status'], array("New", "Working", "Open", "Unqualified", "Abandoned", "Converted")) ? "has-value" : ""; ?>"
                                        id="lead_status_<?= $rows['id']; ?>">
                                    <?php 
                                        if($rows['status'] == "Unqualified") {
                                            ?>
                                            <option value="Unqualified" <?php echo $rows['status'] == "Unqualified" ? "selected" : ""; ?>>
                                                Unqualified
                                            </option>
                                            <?php
                                        } else {
                                            ?>
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
                                            <?php
                                        }
                                    ?>
                                </select>
                                <label>Select</label>
                            </div>
                        </td>
                        <td class="icons text-left">
                            <a href="lead_details.php?id=<?= $rows['id'] ?>" target="_blank" data-toggle="tooltip" data-trigger="hover"
                               title="Details">
                                <i class="fa fa-eye"></i>
                            </a>
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
        <form id="frm_search" action="lead_listing.php" method="GET" class="theme-form" autocomplete="off">
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
                                <div class="form-group height_auto">
                                <input name="lead_id" id="lead_id" type="text" class="listing_search" value="<?= checkIsset($lead_id) ?>"/>
                                <label>Lead ID/Name(s)</label>
                                </div>
                            </div>
                            <?php /*<div class="col-md-6">
                                <div class="form-group">
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
                            </div> */ ?>
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
                            <!-- <div class="col-sm-6">
                                <div class="form-group ">
                                    <input type="text" name="lead_name" value="<?php echo $lead_name ?>"
                                           class="form-control listing_search">
                                    <label>Name</label>
                                </div>
                            </div> -->
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
                                <div class="form-group">
                                    <select class="se_multiple_select listing_search" name="lead_type" id="lead_type"
                                            multiple="multiple">
                                        <option value="Agent/Group">Agent/Group</option>
                                        <option value="Member">Member</option>
                                    </select>
                                    <label>Lead Type</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="se_multiple_select listing_search" name="lead_tag[]" id="lead_tag"
                                            multiple="multiple">
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
                                    <label>Lead Tag</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="se_multiple_select listing_search" name="leads_status[]"
                                            id="lead_status" multiple="multiple">
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
                        <button type="submit" class="btn btn-info">
                        <i class="fa fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-info btn-outline"
                           onClick="window.location = 'lead_listing.php'">
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
                     <div class="pull-right">
                        <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                           <div class="form-group mn">
                              <label for="">Records Per Page </label>
                           </div>
                           <div class="form-group mn">
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
      dropdown_pagination('ajax_data');

            $(".date_picker").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true
            });

            ajax_submit();

            initSelectize('lead_id','LeadID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
            // $("#lead_id").multipleSelect({
            //     selectAll: false
            // });
            $("#lead_type, #lead_status, #lead_tag").multipleSelect({
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
                  e.stopPropagation();
                confirm_export_data(function() {
                    $("#export").val('export_lead');
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

            $(document).off('click', '.btn_invite_agent_group');
            $(document).on("click", ".btn_invite_agent_group", function (e) {
                var id = $(this).attr('data-id');
                swal({
                    html: "<h4 class='m-b-0'>Select the type of invite below</h4>" +
                        "<br>" +
                        '<a href="invite_agent.php?lead_id=' + id + '" class="btn btn-info">Agent Invite</a>' +
                        '<a href="invite_group.php?lead_id=' + id + '" class="btn btn-action m-l-10">Group Invite</a>' +
                        '<small><a href="javascript:void(0);" class="red-link m-l-10" onClick="swal.close();">Cancel</a></small>',
                    showCancelButton: false,
                    showConfirmButton: false
                });
            });

            $(document).off('click', '.btn_delete');
            $(document).on("click", ".btn_delete", function (e) {
                var id = $(this).attr('data-id');
                swal({
                    text: "Delete Lead: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $.ajax({
                        url: 'delete_lead.php',
                        data: {
                            id: id,
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

            $(document).off('change', '.lead_status');
            $(document).on("change", ".lead_status", function (e) {
                e.stopPropagation();
                var id = $(this).attr('id').replace('lead_status_', '');
                var lead_status = $(this).val();
                swal({
                    text: "Change Status: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $.ajax({
                        url: 'change_lead_status.php',
                        data: {
                            id: id,
                            status: lead_status
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == "success") {
                                setNotifySuccess(res.msg);
                            } else {
                                setNotifyError(res.msg);
                                ajax_submit();
                            }
                        }
                    });
                }, function (dismiss) {
                    ajax_submit();
                })
            });

            $(document).off('change', '.lead_tag');
            $(document).on("change", ".lead_tag", function (e) {
                e.stopPropagation();
                var id = $(this).attr('id').replace('lead_tag_', '');
                var lead_tag = $(this).val();
                swal({
                    text: "Change Tag: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $.ajax({
                        url: 'change_lead_tag.php',
                        data: {
                            id: id,
                            lead_tag: lead_tag
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == "success") {
                                setNotifySuccess(res.msg);
                            } else {
                                setNotifyError(res.msg);
                                ajax_submit();
                            }
                        }
                    });
                }, function (dismiss) {
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

            /*----- Delete Mutiple & Assign Leads -----*/
            $(document).off('click', '#chk_all');
            $(document).on('click', '#chk_all', function () {
                if ($(this).prop('checked') == true) {
                    $(".check_record").prop("checked", true);
                    $("#lead_operation").show();

                } else {
                    $(".check_record").prop("checked", false);
                    $("#lead_operation").hide();
                    $("#assigned_to").hide('slow');
                    $("#assigned_to_multiple_td").show('slow');

                }

            });
            $(document).off('click', '.check_record');
            $(document).on('click', '.check_record', function () {
                var id = $(this).data('id');
                var len = $('[name="check_record[]"]:checked').length;

                if (len > 0) {
                    $("#lead_operation").show();
                } else {
                    $("#lead_operation").hide();
                    $("#assigned_to").hide('slow');
                    $("#assigned_to_multiple_td").show('slow');

                }
            });

            $(document).off('click', '#btn_delete_multiple_lead');
            $(document).on('click', '#btn_delete_multiple_lead', function () {
                var lead_ids = [];
                $("input[name='check_record[]']:checked").each(function () {
                    lead_ids.push($(this).val());
                });

                swal({
                    text: "Delete Lead(s): Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $('#ajax_loader').show();
                    $.ajax({
                        url: "delete_lead.php",
                        type: 'POST',
                        data: {lead_ids: lead_ids},
                        dataType: 'json',
                        success: function (res) {
                            $('#ajax_loader').hide();
                            if (res.status == 'success') {
                                setNotifySuccess(res.msg);
                                redirect_after_delete();
                            }
                        }
                    });
                }, function (dismiss) {
                    ajax_submit();
                });
            });

            $(document).off('click', '#assigned_to_multiple');
            $(document).on('click', "#assigned_to_multiple", function () {
                $("#assigned_to_multiple_td").hide('slow');
                $("#assign_user").val("");
                $("#assigned_to").show('slow');
            });

            $(document).off('click', '#assigned_btn');
            $(document).on('click', "#assigned_btn", function () {
                sponsor_id = $("#assign_user").val();
                if (sponsor_id != '') {
                    var curr_ajax_url = $("#curr_ajax_url").val();
                    var lead_ids = [];
                    $("input[name='check_record[]']:checked").each(function () {
                        lead_ids.push($(this).val());
                    });
                    
                    swal({
                        text: "Reassign Leads: Are you sure?",
                        showCancelButton: true,
                        confirmButtonText: "Confirm"
                    }).then(function () {
                        $('#ajax_loader').show();
                        $.ajax({
                            url: "ajax_assigne_lead.php",
                            type: 'POST',
                            data: {lead_ids: lead_ids,sponsor_id:sponsor_id},
                            dataType: 'json',
                            success: function (res) {
                                $('#ajax_loader').hide();
                                if (res.status == 'success') {
                                    setNotifySuccess(res.msg);
                                    redirect_after_delete();
                                }
                            }
                        });
                    }, function (dismiss) {
                        ajax_submit();
                    })
                } else {
                     swal({
                        text: "<br>Agent Selection: Agent required to reassign leads",
                        showCancelButton: true,
                        showConfirmButton:false,
                        cancelButtonText: "Close"
                    });
                }
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

        function redirect_after_delete() {
            var curr_ajax_url = $("#curr_ajax_url").val();
            var assigned_to_visible = $("#assigned_to").is(":visible");
            $('#ajax_loader').show();
            $('#ajax_data').hide();
            $('#is_ajaxed').val('1');
            $.ajax({
                url: curr_ajax_url,
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();

                    if(assigned_to_visible == true) {
                        $("#assigned_to_multiple_td").hide('slow');
                        $("#assign_user").val("");
                        common_select();
                        $("#assigned_to").show('slow');
                    }
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
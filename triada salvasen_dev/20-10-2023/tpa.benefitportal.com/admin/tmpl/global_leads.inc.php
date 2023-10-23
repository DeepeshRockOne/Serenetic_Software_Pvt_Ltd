<?php if($is_ajaxed_leads){ ?>
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
<form id="frm_search_leads" action="global_leads.php" method="GET" class="sform" >
<input type="hidden" name="export" id="export" value=""/>
<input type="hidden" name="is_ajaxed_leads" id="is_ajaxed_leads" value="1"/>
<input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
<input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
<input type="hidden" name="sort_direction" id="sort_by_direction"
   value="<?= $SortDirection; ?>"/>
</form>
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
            ajax_submit();

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

            $(document).off("submit", "#frm_search_leads");
            $(document).on("submit", "#frm_search_leads", function (e) {
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
                }  
                else {
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
            $('#is_ajaxed_leads').val('1');
            var params = $('#frm_search_leads').serialize();
            var all_usersFrm = $('#all_usersFrm').serialize();
            params += '&'+all_usersFrm;
            $.ajax({
                url: $('#frm_search_leads').attr('action'),
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
            $('#is_ajaxed_leads').val('1');
            $.ajax({
                url: curr_ajax_url,
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();

                    if(assigned_to_visible == true) {
                        common_select();
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
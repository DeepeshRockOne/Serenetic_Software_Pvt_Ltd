<style type="text/css">
    .popover {
        max-width: 600px;
    }
</style>

<?php if($is_ajaxed_participants){ ?> 
    <div class="clearfix">
        <?php if ($total_rows > 0) { ?>
            <div style="display: none;" class="pull-left m-b-15" id="participants_operation">
                <button type="button" class="btn red-link v-align-top" id="btnDelMultiple">Delete</button>
            </div>
        <?php } ?>
    </div>
    <input type="hidden" name="curr_ajax_url" id="curr_ajax_url" value="<?=$curr_ajax_url['link_url'];?>">
    <div class="table-responsive">
        <table class="<?=$table_class?> ">
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
            <?php
if ($total_rows > 0) {
    foreach ($fetch_rows as $rows) { ?>
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
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="10">No record(s) found</td>
                </tr>
            <?php }?>
            </tbody>
            <?php if ($total_rows > 0) {?>
                <tfoot>
                <tr>
                    <td colspan="10">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
                </tfoot>
            <?php }?>
        </table>
    </div>

    <?php } else { ?>

        <form id="frm_search_participants" action="global_participants.php" method="GET" class="sform" >    
          <input type="hidden" name="search_type" id="search_type" value="" />
          <input type="hidden" name="is_ajaxed_participants" id="is_ajaxed_participants" value="1" />
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
   
<script type="text/javascript">
    $(document).ready(function () {
       

        ajax_submit_participants();

        $(document).off('click', '#ajax_data tr.data-head a');
            $(document).on('click', '#ajax_data tr.data-head a', function (e) {
                e.preventDefault();
                $('#sort_by_column').val($(this).attr('data-column'));
                $('#sort_by_direction').val($(this).attr('data-direction'));
                //$('#frm_search_participants').submit();
                ajax_submit_participants();
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
                            ajax_submit_participants();
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
                            ajax_submit_participants();
                        }
                    }
                });
            }, function (dismiss) {
                ajax_submit_participants();
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
                            totalCount = $('#participants_id').data('counter');
                            totalCount = ((totalCount - 1) < 0 ? 0 : (totalCount - 1));
                            $('#participants_id').html('Participants ('+totalCount+')');
                            $('#participants_id').data('counter',totalCount);
                            setNotifySuccess(res.msg);
                        } else {
                            setNotifyError(res.msg);
                        }
                        ajax_submit_participants();
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
                            totalCount = $('#participants_id').data('counter');
                            totalCount = ((totalCount - ptId.length) < 0 ? 0 : (totalCount - ptId.length));
                            $('#participants_id').html('Participants ('+totalCount+')');
                            $('#participants_id').data('counter',totalCount);
                            setNotifySuccess(res.message);
                        }
                        ajax_submit_participants();
                    }
                });
            }, function (dismiss) {
                ajax_submit_participants();
            });
        });
    });

    function ajax_submit_participants() {
            $('#ajax_loader').show();
            $('#ajax_data').hide();
            $('#is_ajaxed_participants').val('1');
            var params = $('#frm_search_participants').serialize();
            var all_usersFrm = $('#all_usersFrm').serialize();
            params += '&'+all_usersFrm;
            $.ajax({
                url: $('#frm_search_participants').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#ajax_data').html(res).show();
                    $("[data-toggle=popover]").each(function(i, obj) {
                      $(this).popover({
                        html: true,
                        placement:'auto bottom',
                        content: function() {
                          var id = $(this).attr('data-user_id')                          
                          // $('#popover_content_'+id).find('.status_div.text-success').prevAll().addClass("text-success");  
                          return $('#popover_content_'+id).html();
                        }
                      });
                    });
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
<?php if ($is_ajaxed) { ?>
    <div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4>Personalized Website(s)</h4>
    </div>
    <div class="pull-right">
        <div class="m-b-15">
            <a href="page_builder.php" class="btn btn-action">+ Website</a>
        </div>
    </div>
    </div>
    <div class="table-responsive">
        <table class="<?=$table_class?>">
            <thead>
                <tr>
                    <th>Added Date</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th width="120px">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_rows > 0) { ?>
                    <?php foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td><?=date('m/d/Y', strtotime($rows['created_at']));?></td>
                        <td><?=!empty($rows['page_name'])?$rows['page_name']:"N/A";?></td>
                        <td>
                            <?php if(!empty($rows['user_name'])){ ?>
                                <a href="<?=$ENROLLMENT_WEBSITE_HOST.'/'.$rows['user_name']?>" class="text-action fw500" target="_blank"><?=$ENROLLMENT_WEBSITE_HOST.'/'.$rows['user_name']?></a>
                            <?php } else { echo "N/A"; } ?>
                        </td>
                        <td>
                            <?php
                                if($rows['status'] == "Draft" || $rows['status'] == "") {
                                    echo "Draft";

                                } elseif($rows['status'] == "Active" || $rows['status'] == "Inactive") {
                                ?>
                                <select name="is_published" class="form-control is_published" id="website_status_<?=$rows['id'];?>">
                                    <option value="Active" <?= ($rows['status'] == 'Active') ? "selected='selected'" : ""     ?>>Published </option>
                                    <option value="Inactive" <?= ($rows['status'] == 'Inactive') ? "selected='selected'" : ""     ?>>Unpublished</option>
                                </select>
                                <?php
                                } else {
                                    echo $rows['status'];
                                }
                            ?>
                        </td>
                        <td class="icons">
                            <a href="<?=$HOST?>/prd_preview.php?page_builder_id=<?=$rows['id'];?>" target="_blank"><i class="fa fa-eye"></i></a>
                            <a href="page_builder.php?id=<?=$rows['id'];?>"><i class="fa fa-edit"></i></a>
                            <?php if($rows['status'] == "Draft" || $rows['status'] == "") { ?>
                            <a href="javascript:void(0);" data-id="<?=$rows['id'];?>" class="btn_delete_website"><i class="fa fa-trash"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="5" align="center">No record(s) found</td>
                </tr>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $paginate->links_html; ?>
                </td>
            </tr>
            </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <div class="container m-t-30">
        <div class="panel panel-default panel-block">
            <form id="frm_search" action="manage_website.php" method="GET" class="theme-form">
                <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
                <input type="hidden" name="sort_direction" id="sort_by_direction"
                       value="<?= $SortDirection; ?>"/>
                <div class="panel-body">
                    <div id="ajax_data"></div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
    dropdown_pagination('ajax_data')
            ajax_submit();

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

            $(document).off('click', '.btn_delete_website');
            $(document).on("click", ".btn_delete_website", function (e) {
                var id = $(this).attr('data-id');
                swal({
                    text: "Delete Website: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function () {
                    $.ajax({
                        url: 'manage_website.php',
                        data: {
                            id: id,
                            operation: 'delete',
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

            $(document).off('change', '.is_published');
            $(document).on("change", ".is_published", function(e) {
                e.stopPropagation();
                var id = $(this).attr('id').replace('website_status_', '');
                var publish_status = $(this).val();
                swal({
                    text: "Change Published Status: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(function() {
                    $.ajax({
                        url: 'manage_website.php',
                        data: {
                            id: id,
                            status: publish_status,
                            operation:'change_publish_status',
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
    </script>
<?php } ?>
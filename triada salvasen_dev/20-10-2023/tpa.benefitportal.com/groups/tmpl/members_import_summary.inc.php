<?php if ($is_ajaxed) { ?>
    <div class="clearfix m-b-15">
        <div class="pull-left">
            <label class="mn"><i class="fa fa-square fa-lg text-warning"></i>&nbsp; Pending </label>
            <label class="mn"><i class="fa fa-square fa-lg text-action m-l-10"></i>&nbsp; Cancelled </label> 
        </div>
        <div class="pull-right">
            <strong>Next Import : </strong> <i class="text-light-gray"><?=$next_import_time;?></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="<?= $table_class ?>">
            <thead>
            <tr class="data-head">
                <th>Added Date</th>
                <!-- <th>Tag</th> -->
                <th>Total Records</th>
                <th>Imported Records</th>
                <th>Error(s)</th>
                <th width="90px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) {
                        $tmp_class = '';
                        if($rows['status'] == 'Pending') {
                            $tmp_class = 'text-warning';
                        } elseif($rows['status'] == 'Cancel') {
                            $tmp_class = 'text-action';                            
                        }
                    ?>
                    <tr class="<?=$tmp_class;?>">
                        <td><?= $tz->getDate($rows['created_at'], 'n/j/Y @ g:i a') ?></td>
                        <td><?= $rows['total_records'] ?></td>
                        <td><?= $rows['total_processed_records'] ?></td>
                        <td><?= $rows['total_errors'] ?></td>
                        <td class="icons">
                            <?php if ($rows['status'] == 'Pending') { ?>                            
                            <a href="javascript:void(0);" data-id="<?= md5($rows['id']) ?>" class="btn_cancel_import" data-toggle="tooltip" title="Cancel Import"><i class="fa fa-times-circle-o"></i></a>
                            <?php } ?>

                            <a href="csv_error_log_member.php?id=<?= md5($rows['id']) ?>" class="color_box_popup cboxElement" data-toggle="tooltip" title="View Error Log"><i class="fa fa-exclamation-triangle"></i></a>

                            <?php if ($rows['status'] != 'Pending') { ?>                            
                                <a href="javascript:void(0);" data-id="<?= md5($rows['id']) ?>" class="btn_delete_import" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" class="text-center">No record(s) found</td>
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
        <div class="panel panel-default panel-block">
            <div class="panel-body">
                <form id="member_frm_search" action="members_import_summary.php" method="GET" class="sform">
                    <input type="hidden" name="is_ajaxed" id="member_is_ajaxed" value="1"/>
                    <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                    <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>"/>
                    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>"/>
                </form>                
                <div id="member_ajax_data"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            dropdown_pagination('member_ajax_data')
            member_ajax_submit();

            setInterval(function () {
                member_ajax_submit();
            }, 20000);

            $(document).on('click','.btn_cancel_import',function(){
                var file_id = $(this).attr('data-id');
                swal({
                    text: "Cancel Import: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: 'Confirm'
                }).then(function () {
                    $('#ajax_loader').show();
                    $.ajax({
                        url: "cancel_csv_member_import.php?id=" + file_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if(res.status == "success") {
                                setNotifySuccess(res.msg);
                            } else {
                                setNotifyError(res.msg);
                            }
                            member_ajax_submit();
                        }
                    });
                }, function (dismiss) {

                });
            });

            $(document).on('click','.btn_delete_import',function(){
                var file_id = $(this).attr('data-id');
                swal({
                    text: "Delete Import Request: Are you sure?",
                    showCancelButton: true,
                    confirmButtonText: 'Confirm'
                }).then(function () {
                    $('#ajax_loader').show();
                    $.ajax({
                        url: "cancel_csv_member_import.php?id=" + file_id +"&action=delete",
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if(res.status == "success") {
                                setNotifySuccess(res.msg);
                            } else {
                                setNotifyError(res.msg);
                            }
                            member_ajax_submit();
                        }
                    });
                }, function (dismiss) {

                });
            });

            

            $(document).off('click', '#member_ajax_data ul.pagination li a');
            $(document).on('click', '#member_ajax_data ul.pagination li a', function (e) {
                e.preventDefault();
                $('#ajax_loader').show();
                $('#member_ajax_data').hide();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#member_ajax_data').html(res).show();
                        common_select();
                    }
                });
            });
        });

        function member_ajax_submit() {
            $('#ajax_loader').show();
            $('#member_ajax_data').hide();
            $('#member_is_ajaxed').val('1');
            var params = $('#member_frm_search').serialize();
            $.ajax({
                url: $('#member_frm_search').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#member_ajax_data').html(res).show();
                    common_select();
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
            return false;
        }
    </script>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.color_box_popup').colorbox({iframe: true, width: '950px', height: '500px'});
    });
</script>
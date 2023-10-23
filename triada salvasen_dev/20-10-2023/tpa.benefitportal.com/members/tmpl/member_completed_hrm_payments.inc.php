<?php if (!empty($is_ajaxed)) { ?>
    <div class="table-responsive m-b-30">
        <table class="<?= $table_class ?>">
            <thead>
            <tr>
                <th>Group Name</th>
                <th>Plan</th>
                <th>Payment Duration</th>
                <th>Total</th>
                <th>Pay Date</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) {
                        $currentPayPeriod = displayDate($rows['start_coverage_date']) .' - '. displayDate($rows['end_coverage_date']);
                    ?>
                    <tr>
                        <td><a href="javascript:void(0);" class="fw500"><?= $rows["grpRepId"] ?></a><br><?= $rows["groupName"] ?></td>
                        <td><?= $currentPayPeriod ?></td>
                        <td><?= $rows['hrm_payment_duration'] ?></td>
                        <td><?= dispCommAmt($rows['totalAmount']) ?></td>
                        <td><?= displayDate($rows['pay_date']) ?></td>
                    </tr>
            <?php 
                }
            } else {
                ?>
                <tr>
                    <td colspan="7" align="center">No Record(s).</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                <tr>
                    <td colspan="5"><?php echo $paginate->links_html; ?></td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <form method="GET" action="member_completed_hrm_payments.php" id="member_hrm" class="row">
        <input type="hidden" name="is_ajaxed_hrm" id="is_ajaxed_hrm" value="1"/>
        <input type="hidden" name="hrm_pages" id="hrm_per_pages" value="" />
        <input type="hidden" name="hrm_sort_by" id="hrm_sort_by_column" value=""/>
        <input type="hidden" name="hrm_sort_direction" id="hrm_sort_by_direction" value=""/>
    </form>
    <div id="member_hrm_ajax_data" class=""></div>
    <script type="text/javascript">
        $(document).ready(function () {
            dropdown_pagination('member_hrm_ajax_data')
            ajax_submit();
            $(document).off('click', '#member_hrm_ajax_data ul.pagination li a');
            $(document).on('click', '#member_hrm_ajax_data ul.pagination li a', function (e) {
                e.preventDefault();
                $('#ajax_loader').show();
                $('#member_hrm_ajax_data').hide();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#member_hrm_ajax_data').html(res).show();
                        common_select();
                    }
                });
            });
        });

        function ajax_submit() {
            $('#ajax_loader').show();
            $('#member_hrm_ajax_data').hide();
            $('#is_ajaxed_hrm').val('1');
            var params = $('#member_hrm').serialize();
            $.ajax({
                url: $('#member_hrm').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#member_hrm_ajax_data').html(res).show();
                    common_select();
                }
            });
            return false;
        }
    </script>
<?php } ?>
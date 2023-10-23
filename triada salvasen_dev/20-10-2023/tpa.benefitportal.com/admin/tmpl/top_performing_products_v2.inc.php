<?php if (isset($is_ajaxed)) {
    ?>
    <div class="table-responsive">
        <table border="0" cellspacing="0" class="table table-striped" width="100%">
            <thead>
            <tr class="data-head">
                <th>Product Name</th>
                <th>Total Premium</th>
                <th>Total Policies</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($fetch_rows[0]['product_name'])) {
                foreach ($fetch_rows as $key => $rows) {
                    ?>
                    <tr>
                        <td><?= $rows['product_name'] ?> <br/>(<?= $rows['product_code'] ?>)</td>
                        <td><?= displayAmount($rows['total_premiums'],2) ?></td>
                        <td><?= $rows['total_policies'] ?></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td colspan="7" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
            </tbody>
            <?php if ($paginate->total_pages > 1) { ?>
                <tfoot>
                <tr>
                    <td colspan="7"><?php echo $paginate->links_html; ?></td>
                </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
<?php } else { ?>
    <form id="top_performing_products_v2_frm_search" action="<?=$ADMIN_HOST?>/top_performing_products_v2.php" method="GET" class="sform">
        <input type="hidden" name="is_ajaxed" id="top_performing_products_v2_is_ajaxed" value="1">
        <input type="hidden" name="getfromdate" value="<?=$searchArray['getfromdate']?>">
        <input type="hidden" name="gettodate" value="<?=$searchArray['gettodate']?>">
        <input type="hidden" name="is_renewal" value="<?=$is_renewal;?>">
    </form>

    <div id="top_performing_products_v2_ajax_data"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            top_performing_products_v2_ajax_submit();
            $(document).off('click','#top_performing_products_v2_ajax_data .live-link a');
            $(document).on('click','#top_performing_products_v2_ajax_data .live-link a',function(e){
                e.preventDefault();
                $('#ajax_loader').show();
                var link_href = $(this).attr('href');
                $.ajax({
                    url: link_href,
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#top_performing_products_v2_ajax_data').html(res);
                    }
                });

            });
        });

        function top_performing_products_v2_ajax_submit() {
            $('#ajax_loader').show();
            
            var params = $('#top_performing_products_v2_frm_search').serialize();
            $.ajax({
                url: $('#top_performing_products_v2_frm_search').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#top_performing_products_v2_ajax_data').html(res).show();
                }
            });
        }
    </script>
<?php } ?>
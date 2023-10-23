<?php if ($is_ajaxed) {
    ?>
    <div class="table-responsive">
        <table border="0" cellspacing="0" class="table table-striped" width="100%">
            <thead>
            <tr class="data-head">
                <th width="20%">Agent Name/Id</th>
                <th>New Subscribers</th>
                <th>New Policies Written</th>
                <th>New Business Premiums</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($total_rows > 0) {
                foreach ($fetch_rows as $key => $rows) { ?>
                    <tr>
                        <td><?= $rows['s_fname'].' '.$rows['s_lname'] ?> <br/>(<?= $rows['s_rep_id'] ?>)</td>
                        <td><?=getAgentsSubscribersCount($rows['s_id'], $searchArray);?></td>
                        <td><?=getAgentPoliciesWritten($rows['s_id'], $searchArray);?></td>
                        <td><?= '$' . money_format('%!i',$rows['total_sales']) ?></td>
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
    <form id="top_performing_agents_frm_search" action="<?=$ADMIN_HOST?>/top_performing_agents.php" method="GET" class="sform">
        <input type="hidden" name="is_ajaxed" id="top_performing_agents_is_ajaxed" value="1">
        <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
        <input type="hidden" name="getfromdate" value="<?=$searchArray['getfromdate']?>">
        <input type="hidden" name="gettodate" value="<?=$searchArray['gettodate']?>">
        <input type="hidden" name="is_renewal" value="<?=$is_renewal;?>">
    </form>
    <div id="top_performing_agents"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            top_performing_agents_ajax_submit();
            $(document).off('click','#top_performing_agents .live-link a');
            $(document).on('click','#top_performing_agents  .live-link a',function(e){
                e.preventDefault();
                $('#ajax_loader').show();
                var link_href = $(this).attr('href');
                $.ajax({
                    url: link_href,
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#top_performing_agents').html(res).show();
                    }
                });

            });
        });

        function top_performing_agents_ajax_submit() {
            $('#ajax_loader').show();
            var params = $('#top_performing_agents_frm_search').serialize();
            $.ajax({
                url: $('#top_performing_agents_frm_search').attr('action'),
                type: 'GET',
                data: params,
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#top_performing_agents').html(res).show();
                }
            });
            return false;
        }
    </script>
<?php } ?>
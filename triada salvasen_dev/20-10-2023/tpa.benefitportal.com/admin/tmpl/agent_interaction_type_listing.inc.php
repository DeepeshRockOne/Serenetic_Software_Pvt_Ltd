<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead> 
            <tr>
              <th>Type</th>
              <th width="90px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <tr>
                        <td><?= $rows['type']; ?></td>
                        <td class="icons">
                            <a href="javascript:void(0)" class="editInteraction" data-toggle="tooltip" data-trigger="hover" data-original-title="Edit" data-id="<?= $rows['id'] ?>" data-user_type='agent'><i class="fa fa-pencil-square-o"></i></a>
                            <a href="javascript:void(0)" class="deleteInteraction" data-toggle="tooltip" data-trigger="hover" data-original-title="Delete" data-id="<?= $rows['id'] ?>" data-user_type='agent'><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="3" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#agent_interaction_div ul.pagination li a');
    $(document).on('click', '#agent_interaction_div ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#agent_interaction_div').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#agent_interaction_div').html(res).show();
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });
</script>
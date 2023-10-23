<div class="panel panel-default panel-block ">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">Product List For -  <span class="fw300"><?= $name ?></span></h4>
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="<?=$table_class?> text-nowrap">
                <thead>
                <tr class="data-head">
                    <th>Product Name</th>
                    <th width="210px">Product Code</th>
                    <th width="110px">Current Status</th>
                </tr>
                </thead>
                <tbody>
                    <?php if ($total_rows > 0) {
                        foreach ($fetch_rows as $rows) { ?>
                            <tr>
                                <td><?= $rows['name']; ?></td>
                                <td><?= $rows['product_code']; ?></td>
                                <td><?= $rows['status']; ?></td>
                                
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
    </div>
  </div>
  <script>

$(document).ready(function(){
  common_select()
});
    $(document).off('change', '.pagination_select');
  $(document).on('change', '.pagination_select', function(e) {
      e.preventDefault();
      $('panel-body').html('');
      var page_url = $(this).find('option:selected').attr('data-page_url');
      window.location.href=page_url
      common_select();
  });
</script>
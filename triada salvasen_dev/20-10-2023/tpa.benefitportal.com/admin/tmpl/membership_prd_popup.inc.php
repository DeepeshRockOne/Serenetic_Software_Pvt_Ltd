<?php if ($is_ajaxed) { ?>
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <div class="panel-title">
        <p class="fs18"><strong class="fw500"><?=ucfirst($membership_name);?> - </strong> <span class="fw300">(<?=$total_rows?>) Products</span></p>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="<?= $table_class ?>">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Product ID</th>
              <th class="text-center">Current Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($membership_data){ 
              foreach ($membership_data as $key => $value) { ?>
               <tr>
                <td><?=$value['name']?></td>
                <td><?=$value['product_code']?></td>
                <td class="text-center"><?=$value['status']?></td>
              </tr>
              <?php } ?> 
            <?php }else{ ?>
              <tr><td colspan="4" class="text-center">No Products Found</td></tr>
            <?php } ?>
          </tbody>
          <?php if ($total_rows > 0) {?>
                <tfoot>
      <tr>
        <td colspan="7">
         <?php echo $paginate->links_html; ?>
          </td>
      </tr>
    </tfoot>
            <?php }?>
        </table>
        <div class="text-center"> <a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link">Close</a> </div>
      </div>
    </div>
  </div>
<?php }else{ ?>
  <form id="frm_search" action="membership_prd_popup.php" method="GET">
    <input type="hidden" name="id" id="id" value="<?=$_GET['id']?>" />
    <input type="hidden" name="fee_id" id="fee_id" value="<?=$_GET['fee_id']?>" />
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="is_export" id="is_export" value="" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="sort" id="sort_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="direction" id="sort_direction" value="<?=$SortDirection;?>" />
  </form>
    <div  class="ajex_loader" style="display:none"></div>
    <div id="ajax_data"> </div>
  <script type="text/javascript">
    $(document).ready(function() {
      dropdown_pagination('ajax_data')

      ajax_submit();
      
      function ajax_submit() {
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $('#is_ajaxed').val('1');
        
        var params = $('#frm_search').serialize();
        $.ajax({
          url: $('#frm_search').attr('action'),
          type: 'GET',
          data: params,
          success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            common_select();
          }
        });
        return false;
      }
    });

    $(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function(res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
                common_select();
            }
        });
    });
</script>
<?php } ?>

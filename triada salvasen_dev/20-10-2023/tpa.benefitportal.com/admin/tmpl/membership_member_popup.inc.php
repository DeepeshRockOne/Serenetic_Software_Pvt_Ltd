<?php if ($is_ajaxed) { ?>
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <div class="panel-title">
        <p class="fs18"><strong class="fw500"><?=ucfirst($membership_name);?> - </strong> <span class="fw300">Active Members (<?=$total_rows?>)</span></p>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="<?= $table_class ?>">
          <thead>
            <tr>
              <th>Member ID</th>
              <th>Member Name</th>
              <th class="text-center">Active Members Per ID</th>
              <th width="100px">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($membership_data){ 
              foreach ($membership_data as $key => $value) { ?>
               <tr>
                <td><?=$value['rep_id']?></td>
                <td><?=$value['customer_name']?></td>
                <td class="text-center"><?=$value['members_per_id']?></td>
                <td class="text-right"><?=$value['status']?></td>
              </tr>
              <?php } ?> 
            <?php }else{ ?>
              <tr><td colspan="4" class="text-center">No Members Found</td></tr>
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
          <?php }?>
        </table>
        <div class="text-center m-t-20">
          <a href="javascript:void(0);" name="export" id="export" class="btn btn-action">Export</a>
          <a href="javascript:void(0);" class="btn red-link m-l-15" onclick='parent.$.colorbox.close(); return false;'>Close</a>
        </div>
      </div>
    </div>
  </div>
<?php }else{ ?>
  <form id="frm_search" action="membership_member_popup.php" method="GET">
    <input type="hidden" name="id" id="id" value="<?=$_GET['id']?>" />
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
  
      ajax_submit();
      $(document).on('click', '#export', function () {
        swal({
            text: 'Export to Excel All Members: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $('#is_ajaxed').val('0');
            $('#is_export').val('1');
            $('#frm_search').submit();
        }, function (dismiss) {
            window.location.reload();
        })
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
          success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            common_select();
          }
        });
        return false;
      }
    });
</script>
<?php } ?>

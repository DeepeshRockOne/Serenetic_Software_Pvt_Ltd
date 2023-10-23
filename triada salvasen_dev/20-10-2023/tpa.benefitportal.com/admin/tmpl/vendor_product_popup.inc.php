<?php if ($is_ajaxed) { ?>
  <div class="panel panel-default panel-block">
    <div class="panel-heading">
      <div class="panel-title">
        <p class="fs18"><strong class="fw500"><?=ucfirst($vendor_name);?> - </strong> <span class="fw300">(<?=$total_rows?>) Products</span></p>
      </div>
    </div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="<?= $table_class ?>">
          <thead>
            <tr>
              <td colspan="3"><div class="pull-right">
                  <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                    <div class="form-group mn">
                      <label for="user_type">Records Per Page </label>
                    </div>
                    <div class="form-group mn">
                      <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);ajax_submit();">
                        <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                      </select>
                    </div>
                  </div>
                </div>
            </tr>
            <tr>
              <th>Product Name</th>
              <th>Product ID</th>
              <th class="text-center">Current Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($vendor_data){ 
              foreach ($vendor_data as $key => $value) { ?>
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
            <div class="pull-left">
              <div class="dataTables_info">Showing
                <?=((($page-1)*$per_page))+1;?>
                to
                <?=$paginate->total_results<(($page)*$per_page) ? $paginate->total_results : (($page)*$per_page);?>
                of
                <?=$paginate->total_results;?>
                records</div>
            </div>
            <div class="pull-right">
              <div class="new_pagination">
                <ul class="pagination pagination-md">
                  <li class="prev"><!--<span>&lt;</span>--><a href="javascript:void(0);" data-val="-1">&lt;</a></li>
                  <li> <span class="page_plus">
                    <select id="cpage2" class="form-control" onchange="$('#nav_page').val(this.value);ajax_submit();">
                    
                      <?php foreach(range(1,$paginate->total_pages) as $value){?>
                      <option value="<?=$value;?>">
                      <?=$value;?>
                      </option>
                      <?php }?>
                    </select>
                    </span> </li>
                  <li class="live-link"><a href="javascript:void(0);" data-val="+1">&gt;</a></li>
                </ul>
              </div>
            </div>
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
  <form id="frm_search" action="vendor_product_popup.php" method="GET">
    <input type="hidden" name="id" id="id" value="<?=$_GET['id']?>" />
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="is_export" id="is_export" value="" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="sort" id="sort_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="direction" id="sort_direction" value="<?=$SortDirection;?>" />
  </form>
  <div class="panel panel-default panel-block">
  <div class="panel-body">
    <div  class="ajex_loader" style="display:none"></div>
    <div id="ajax_data"> </div>
  </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
  
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
            $('.add_vendor_fee').colorbox({
              iframe: true, width: '855px', height: '90%',
              onClosed:function(){
                //ajax_submit();
                window.location.reload();
              }
            });
          }
        });
        return false;
      }
    });
</script>
<?php } ?>

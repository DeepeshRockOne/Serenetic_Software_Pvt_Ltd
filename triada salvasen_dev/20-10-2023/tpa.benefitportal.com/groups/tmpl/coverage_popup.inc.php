<?php if ($is_ajaxed) { ?>
  <div class="clearfix">
    <?php if ($total_rows > 0) {?>
        <div class="pull-right" style="display: none">
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
    <?php }?>
  </div> 
  <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-center">Current Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total_rows > 0) { ?>
            <?php foreach ($fetch_rows as $rows) { ?>
              <tr> 
                <td><?= $rows['name'] ?></td>
                <td class="text-center"><?= $rows['status'] ?></td>
              </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr>
                <td colspan="2" class="text-center">No record(s) found.</td>
            </tr>
          <?php } ?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
          <tfoot>
              <tr>
                      <td colspan="9">
                          <?php echo $paginate->links_html; ?>
                      </td>
                  </tr>
          </tfoot>
        <?php } ?>
      </table>
  </div>
  <div class="panel-body">
      <div class="text-center">
         <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>
   </div>
<?php }else{ ?>
  <div class="panel panel-default ">
    <form id="frm_search" action="coverage_popup.php" method="GET" class="theme-form">
      <input type="hidden" name="export_val" id="export_val" value="">
      <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
      <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
      <input type="hidden" name="group_id" id="group_id" value="<?=$group_id;?>"/>
      <input type="hidden" name="id" id="id" value="<?=$id;?>"/>
       <div class="panel-heading">
         <div class="panel-title">
           <h4 class="mn"><?= $class_name ?> - <span class="fw300">(<?= $product_total ?>) Products</span></h4>
         </div>
       </div>
       
    </form>
  </div>
  <div class="panel panel-default panel-block">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
          <div class="loader"></div>
      </div>
      <div id="ajax_data" class="panel-body"></div>
  </div>
  <script type="text/javascript">
      $(document).ready(function() {
    dropdown_pagination('ajax_data')
          ajax_submit();
      });

      $(document).off('click', '#ajax_data tr.data-head a');
      $(document).on('click', '#ajax_data tr.data-head a', function(e) {
          e.preventDefault();
          $('#sort_by_column').val($(this).attr('data-column'));
          $('#sort_by_direction').val($(this).attr('data-direction'));
          ajax_submit();
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
                  $('[data-toggle="tooltip"]').tooltip();
                  common_select();
              }
          });
      });
             
      $(document).off("submit","#frm_search");
      $(document).on("submit","#frm_search",function(e){
          e.preventDefault();
          disable_search();
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
                  $('[data-toggle="tooltip"]').tooltip();
                  common_select();
                  
              }
          });
          return false;
      }
  </script>
<?php } ?>


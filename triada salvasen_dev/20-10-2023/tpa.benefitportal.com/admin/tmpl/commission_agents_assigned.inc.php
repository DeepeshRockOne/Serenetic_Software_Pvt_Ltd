<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">Assigned Agents -   </strong> <span class="fw300"><?= $res_sql['name'] .' ('.$total_agents.')'?></span></p>
    </div>
  </div>
  <div class="panel-body">
    <form id="frm_search" action="commission_agents_assigned.php" method="GET" >
        <input type="hidden" name="pages" id="per_pages" value="10" />
    </form>
  	<div class="table-responsive">
    	<table class="<?= $table_class ?>">
        	<thead>
                <!-- <div id="top_paginate_cont" class="pull-right">
                  <div class="col-md-12">
                    <div class="form-inline" id="DataTables_Table_0_length">
                      <div class="form-group">
                        <label for="user_type">Records Per Page </label>
                      </div>
                      <div class="form-group">
                        <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);
                            ajax_submit();">
                          <option value="10" <?= !empty($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                          <option value="25" <?= empty($_GET['pages']) || $_GET['pages'] == 25  ? 'selected' : ''; ?>>25</option>
                          <option value="50" <?= !empty($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                          <option value="100" <?= !empty($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div> -->
            	<tr>
                	<th>Agent ID</th>
                    <th>Agent Name</th>
                    <th width="200px" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($fetch_rows)) { ?>
                    <?php foreach ($fetch_rows as $key => $value) { ?>
                        <tr>
                            <td><?= $value['rep_id'] ?></td>
                            <td><?= $value['fname'].' '.$value['lname'] ?></td>
                            <td class="text-center"><?= $value['status'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            <?php if ($total_rows > 0) { ?>
                <tfoot>
                  <tr>
                    <td colspan="7"><?php echo $paginate->links_html; ?></td>
                  </tr>
                </tfoot>
            <?php } ?>
        </table>
    </div>
    <div class="text-center m-t-20">
    		<a href="javascript:void(0);" class="btn btn-action">Export</a>
       		<a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
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
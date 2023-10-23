<style type="text/css">
.iframe .dropdown.bootstrap-select .dropdown-menu.open{max-height:75px!important; min-height: 100%!important;}
.iframe .dropdown.bootstrap-select .dropdown-menu .inner.open{max-height:75px!important; min-height: 100%!important;}
</style>
<?php if ($is_ajaxed) { ?>
  <div class="table-responsive">
    <table class="<?=$table_class?>">
      <thead>
        <tr class="data-head">
          <th>ID</th>
          <th>Broadcast Name</th>
          <th>  Broadcast Type</th>
          <th>Last Modified</th>
          <th>User Group</th>
          <th>Status</th>
          <th width="100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total_rows > 0) {
          foreach ($fetch_rows as $rows) { ?>
                <?php 
                  $link="";
                  if($rows['type'] == 'email') { 
                    $link = "add_email_broadcast.php?broadcaster_id=".md5($rows['id']);
                  }else if($rows['type'] == 'sms') {
                    $link = "add_sms_broadcast.php?broadcaster_id=".md5($rows['id']);
                  } 

                  $broadcastType = ($rows['type'] == "sms" ? "Text Message" : "Email");
                ?>
            <tr>
              <td>
                <a href="javascript:void(0)" class="fw500 text-red" onclick="parent.redirect_page('<?=$link?>')"><?=$rows['display_id']; ?></a>
              </td>
              <td><?=$rows['brodcast_name']; ?></td>
              <td><?=$broadcastType?></td>
              <td><?=date('m/d/Y', strtotime($rows['updated_at'])); ?></td>
              <td><?=$rows['user_type']?></td>
              <td><?php echo $rows['status']; ?> </td>
              <td class="icons">
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Edit" onclick="parent.redirect_page('<?=$link?>')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" onclick="parent.delete_broadcaster('<?= md5($rows['id']); ?>')"><i class="fa fa-trash" aria-hidden="true"></i></a>
              </td>
            </tr>
          <?php } ?>
        <?php } else {?>
          <tr>
            <td colspan="9">No record(s) found</td>
          </tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
        <tfoot>
          <tr>
            <td colspan="9"><?php echo $paginate->links_html; ?></td>
          </tr>
        </tfoot>
      <?php }?>
    </table>
  </div>
  <script type="text/javascript">
    $(document).keypress(function (e) {
      if (e.which == 13) {
        ajax_submit();
      }
    });
  </script>
<?php } else { ?>
  <?php include_once 'notify.inc.php';?>
  <div class="panel panel-default panel-block panel-title-block" style="display: none;">
    <form id="frm_search" action="draft_broadcast_group.php" method="GET" class="sform">
      <div class="panel-wrapper collapse in">
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="search" id="search" > <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'draft_broadcast_agent.php'"> <i class="fa fa-search-plus"></i> View All </button>
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          <div id="top_paginate_cont" class="pull-right">
            <div class="col-md-12">
              <div class="form-inline text-right" id="DataTables_Table_0_length">
                <div class="form-group">
                  <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group">
                  <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value); ajax_submit();">
                    <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="panel panel-default panel-block">
    <div class="list-group">
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
        <div class="loader"></div>
      </div>
      <div id="ajax_data" class=""> </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
    dropdown_pagination('ajax_data')
      ajax_submit();

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
            frame_name = 'br_queue_iframe';
            parent.resizeIframe($("body").height() + 20, frame_name);
            common_select();
          }
        });
      });
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
          frame_name = 'br_queue_iframe';
          parent.resizeIframe($("body").height() + 20, frame_name);
          common_select();
        }
      });
      return false;
    }
  </script>
<?php }?>
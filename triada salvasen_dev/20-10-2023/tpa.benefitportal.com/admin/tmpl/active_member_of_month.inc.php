<?php if ($is_ajaxed) { ?>
    <div class="clearfix tbl_filter m-b-15">
        <div class="pull-left">
            <h4 class="mn">Active Members</h4>
            <p class="mn">Displaying Active Member's at end of the month.</p>
        </div>
        <?php if ($total_rows > 0) { ?>
            <div class="pull-right">
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <?php if($module_access_type == "rw") { ?>
                  <button type="button" name="export" id="export" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
                <?php } ?> &nbsp;
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
        <?php } ?>
    </div>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr class="data-head">
        <th><a href="javascript:void(0);" data-column="month_date" data-direction="<?php echo $SortBy == 'month_date' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Month</a></th>
        <th width="150px"><a href="javascript:void(0);" data-column="active_members" data-direction="<?php echo $SortBy == 'active_members' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Active Members</a></th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) { ?>
        <?php foreach ($fetch_rows as $rows) { ?>
        <tr>
          <td>
            <strong ><?php echo date('F Y',strtotime($rows['month_date'])); ?></strong>
          </td>
          <td>
            <strong ><?php echo $rows['active_members']; ?></strong>
          </td>
        </tr>
        <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="2">No record(s) found</td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="2">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
</div>
<?php }else{ ?>
<div class="panel panel-default panel-block panel-title-block">
  <form id="frm_search" action="active_member_of_month.php" method="GET" autocomplete="off">
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
    <input type="hidden" name="page" id="nav_page" value="" />
    <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
    <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    <input type="hidden" name="export_val" id="export_val" value="">
  </form>
</div>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div id="ajax_loader" class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="ajax_data" class="list-group-item"> </div>
  </div>
</div>
<script type="text/javascript">

$(document).ready(function() {
    dropdown_pagination('ajax_data');
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

function ajax_submit() {
  $('#ajax_loader').show();
  $('#ajax_data').hide();
  $('#is_ajaxed').val('1');
  $("#export").val('');
  var params = $('#frm_search').serialize();
  var cpage = $('#nav_page').val();
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

$(document).off('click', '#export');
$(document).on('click', '#export', function (e) {
    e.stopPropagation();
    confirm_export_data(function() {
        $("#export_val").val(1);
        $('#ajax_loader').show();
        $('#is_ajaxed').val('1');
        var params = $('#frm_search').serialize();
        $.ajax({
            url: $('#frm_search').attr('action'),
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                $('#ajax_loader').hide();
                $("#export_val").val('');
                if(res.status == "success") {
                    confirm_view_export_request();
                } else {
                    setNotifyError(res.message);
                }
            }
        });
    });
});
</script>
<?php }?>

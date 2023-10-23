<?php if ($is_ajaxed) { ?>
  <div class="clearfix m-b-20 text-right">
     <button type="button" name="export" id="export" class="btn btn-action"> <i class="fa fa-download"></i> Export</button>
  </div>
  <table data-toggle="table" data-height="330" data-mobile-responsive="true" class="<?=$table_class?>">
      <thead>
        <tr>
          <th data-field="Agent_id">Agent ID</th>
          <th data-field="Agent_name">Agent Name</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($total_rows > 0) { 
  				foreach ($fetch_rows as $rows) { ?>
  					<tr>
  						<td><?php echo $rows['rep_id']; ?></td>
  						<td><?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?></td>
  					</tr>
  				<?php }?>
        <?php } else {?>
          <tr><td colspan="11" align="center">No record(s) found</td></tr>
        <?php }?>
      </tbody>
      <?php if ($total_rows > 0) {?>
	      <tfoot>
	        <tr>
	          <td colspan="20"><?php echo $paginate->links_html; ?></td>
	        </tr>
	      </tfoot>
      <?php }?>
  </table>
<?php } else { ?>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18">
        <strong class="fw500">Agents Assigned -</strong> <span class="fw300"> <?=$payment_master_res['name']?> </span>
      </p>
    </div>
  </div>
  <div class="panel-body">
  <form id="frm_search" action="view_assigned_agents.php" method="GET" class="sform">
    <div class="theme-form">
      <div class="row">
            <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>   
            <input type="hidden" name="payment_id" id="payment_id" value="<?=$payment_master_id?>"/>
            <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
            <input type="hidden" name="export_val" id="export_val" value="">
          <div class="col-sm-6">
            <div class="form-group">
                  <select class="se_multiple_select" name="agent_ids[]"  id="agent_ids" multiple="multiple" >
                    <?php if(!empty($agnts_res)){ ?>
                      <?php foreach($agnts_res as $agent){ ?>
                        <option value="<?=$agent['agentId']?>"><?=$agent['agentDispId']?> - <?=$agent['agentName']?></option>
                      <?php }?>
                    <?php } ?>
                  </select>
                  <label>Search Agent ID(s)</label>
                </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <button type="submit" class="btn btn-info" name="search" id="search"><i class="fa fa-search"></i> Search</button>
            </div>
          </div>
      </div>
      <p><span class="fw500 m-b-20"><?=$total_agents?> agents are assigned to</span> <?=$payment_master_res['name']?> </p>
    </div>
    </form>
    <div id="ajax_data_assigned_agents"></div>
    <div class="text-center m-t-20">
      
      <a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  dropdown_pagination('ajax_data_assigned_agents')

  $("#agent_ids").multipleSelect({
       selectAll: false,
  });
  ajax_submit();
});

$(document).on("submit","#frm_search",function(e){
  e.preventDefault();
  ajax_submit();
});

$(document).off('click', '#export');
    $(document).on('click', '#export', function (e) {
        e.stopPropagation();

        parent.confirm_export_data(function() {
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
                        parent.confirm_view_export_request();
                    } else {
                        setNotifyError(res.message);
                    }
                }
            });
        });
    });

$(document).off('click', '#ajax_data_assigned_agents tr.data-head a');
$(document).on('click', '#ajax_data_assigned_agents tr.data-head a', function (e) {
  e.preventDefault();
  $('#sort_by_column').val($(this).attr('data-column'));
  $('#sort_by_direction').val($(this).attr('data-direction'));
  //$('#frm_search').submit();
  ajax_submit();
});

$(document).off('click', '#ajax_data_assigned_agents ul.pagination li a');
$(document).on('click', '#ajax_data_assigned_agents ul.pagination li a', function (e) {
  e.preventDefault();
  $('#ajax_loader').show();
  $('#ajax_data_assigned_agents').hide();
  $.ajax({
    url: $(this).attr('href'),
    type: 'GET',
    success: function (res) {
      $('#ajax_loader').hide();
      $('#ajax_data_assigned_agents').html(res).show();
      common_select();
    }
  });
});

function ajax_submit() {
  $('#ajax_loader').show();
  $('#ajax_data_assigned_agents').hide();
  $('#is_ajaxed').val('1');
  var params = $('#frm_search').serialize();
  $.ajax({
    url: $('#frm_search').attr('action'),
    type: 'GET',
    data: params,
    success: function (res) {
      $('#ajax_loader').hide();
      $('#ajax_data_assigned_agents').html(res).show();
      common_select();
      $("[data-toggle=popover]").each(function(i, obj) {
        $(this).popover({
          html: true,
          placement:'auto bottom',
          content: function() {
            var id = $(this).attr('data-user_id');
            return $('#popover_content_'+id).html();
          }
        });
      });
    }
  });
  return false;
}

</script>
<?php } ?>
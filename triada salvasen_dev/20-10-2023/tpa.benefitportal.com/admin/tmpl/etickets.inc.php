<?php if($is_ajaxed){ ?>
<div class="clearfix m-b-15 tbl_filter">
  <div class="pull-left">
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
  <div class="pull-right">
    <a href="manage_etickets.php" class="btn btn-default">Manage E-Tickets</a>
    <a href="javascript:void(0)" class="btn btn-action add_etickets">+ Ticket</a>
  </div>
</div>
<div class="table-responsive">
  <table class="<?=$table_class?> etickets-striped">
    <thead>
      <tr>
        <th>ID/Added Date</th>
        <th>Requester</th>
        <th>Company</th>
        <th>Last Updated</th>
        <th>Group</th>
        <th width="250px">Assignee</th>
        <th>Status</th>
        <th class="text-center" width="150px">Open Conversation</th>
      </tr>
    </thead>
    <tbody>
    <?php if(!empty($fetch_rows) && $total_rows > 0) {
                foreach($fetch_rows as $rows) {?>
      <tr>
        <td><a href="javascript:void(0);" class="fw500 text-action open_conversation_link" data-target="#open_conversation_<?=md5($rows['id'])?>"><?=$rows['tracking_id']?></a><br><?=getCustomDate($rows['created_at'])?></td>
        <td>
        <?=$rows['name']?><br>
          <a href="javascript:void(0);" class="fw500 text-action"><?=$rows['subject']?></a>
        </td>
        <td><?=($rows['company'] ? $rows['company'] : "-")?></td>
        <td class="updated_at_<?=md5($rows['id'])?>"><?=getCustomDate($rows['updated_at'])?></td>
        <td>
          <div class="theme-form w-200 pr">
            <select class="form-control reassigned_action <?=!empty($groups) ? 'has-value' :'' ?>" id="reassigned_action_<?=$rows['id']?>" data-id='<?=$rows['id']?>' data-category_id="<?=$rows['group_id']?>">
              <option data-hidden="true"></option>
              <?php if(!empty($groups)){
                foreach($groups as $group){ ?>
                  <option value="<?=$group['id']?>" <?=$group['id'] == $rows['group_id'] ? 'selected="selected" ' : ''?>><?=$group['title']?></option>
              <?php } } ?>
            </select>
            <label>Select</label>
          </div>
        </td>
        <td>
          <div class="theme-form w-250 pr">
            <select id="changeAssignee_<?=$rows['id']?>" name="changeAssignee" onchange="changeAssignee($(this))" data-category_id="<?=$rows['group_id']?>" data-tracking_id="<?=$rows['id']?>" data-oldval="<?=$rows['assigned_admin_id']?>" class="form-control" data-live-search="true">
                <?php if($rows['assigned_admin_id'] == 0) { ?>
                  <option value="" data-hidden="true" <?=  $rows['assigned_admin_id'] =='0' ? 'selected="selected" ' : ''?>></option>
                <?php } ?>
              <optgroup label="GROUP - <?=$rows['groupName']?>">
                  <?php if(!empty($rows['admin_names'])){
                    $adminNames = explode(',',$rows['admin_names']);
                    foreach($adminNames as $name){
                      $textName = explode('_',$name);
                      ?>
                    <option value="<?=$textName[1]?>" <?=$textName[1] == $rows['assigned_admin_id'] ? 'selected="selected" '  : ''?>><?=$textName[0]?></option>
                  <?php }} ?>
              </optgroup>
            </select>
            <label>Select</label>
          </div>
        </td>
        <td>
          <div class="theme-form w-200 pr">
            <select class="form-control" data-category_id="<?=$rows['group_id']?>" data-tracking_id="<?=$rows['id']?>" data-oldval="<?=$rows['status']?>" id="changeStatus_<?=$rows['id']?>" onchange="changeStatus($(this))">
              <option data-hidden="true"></option>
              <option value="New" <?=$rows['status'] == 'New' ? 'selected="selected" ' : '' ?>>New</option>
              <option value="Working" <?=$rows['status'] == 'Working' ? 'selected="selected" ' : '' ?>>Working</option>
              <option value="Open" <?=$rows['status'] == 'Open' ? 'selected="selected" ' : '' ?>>Open</option>
              <option value="Reassigned" <?=$rows['status'] == 'Reassigned' ? 'selected="selected" ' : '' ?>>Reassigned</option>
              <option value="Abandoned (Admin)" <?=$rows['status'] == 'Abandoned (Admin)' ? 'selected="selected" ' : '' ?>>Abandoned (Admin)</option>
              <option value="Abandoned (User)" <?=$rows['status'] == 'Abandoned (User)' ? 'selected="selected" ' : '' ?>>Abandoned (User)</option>
              <option value="Completed" <?=$rows['status'] == 'Completed' ? 'selected="selected" ' : '' ?>>Completed</option>
            </select>
            <label>Select</label>
          </div>
        </td>
        <td class="icons text-center">
          <a href="javascript:void(0);" data-target="#open_conversation_<?=md5($rows['id'])?>" class="open_conversation_link"><i class="fa fa-caret-right caret_class" id="caret_class_<?=md5($rows['id'])?>" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Open"></i></a>
        </td>
      </tr>
      <tr>
        <td colspan="7" class="pn">
          <div class="open_conversation_collapse collapse" id="open_conversation_<?=md5($rows['id'])?>"></div>
        </td>
      </tr>
      <?php } } ?>
    </tbody>
    <tfoot>
          <tr>
          <?php if($total_rows > 0 && !empty($fetch_rows)) { ?>
              <td colspan="8">
              <?php echo $paginate->links_html; ?>
              </td>
          <?php }else echo "<td colspan='8'>No record found!</td>"; ?>
          </tr>
      </tfoot>
  </table>
</div>
<?php }else { ?>
<div class="panel panel-default panel-block panel-title-block">
<div class="panel-left">
  <div class="panel-left-nav">
    <ul>
      <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
    </ul>
  </div>
</div>
<div class="panel-right">
  <div class="panel-heading">
    <div class="panel-search-title">
      <span class="clr-light-blk">SEARCH</span>
    </div>
  </div>
  <div class="panel-wrapper collapse in">
    <form name="eticketForm" id="eticketForm" action="etickets.php">
      <div class="panel-body theme-form">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="form-group">
              <input name="tracking_id" id="fl_tracking_id" type="text" class="listing_search"/>
              <label>Ticket ID(s)</label>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="row">
              <div id="date_range" class="col-md-12 col-sm-12">
                <div class="form-group">
                  <select class="form-control listing_search" id="join_range" name="join_range">
                    <option value=""> </option>
                    <option value="Range">Range</option>
                    <option value="Exactly">Exactly</option>
                    <option value="Before">Before</option>
                    <option value="After">After</option>
                  </select>
                  <label>Added Date</label>
                </div>
              </div>
              <div class="col-md-9 col-sm-12" style="display:none" id="select_date_div">
                <div class="form-group">
                  <div id="all_join" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <div class="pr">
                      <input type="text" class="form-control date_picker listing_search" name="added_date" />
                    </div>
                  </div>
                  <div  id="range_join" style="display:none;">
                    <div class="phone-control-wrap">
                      <div class="phone-addon">
                        <label class="mn">From</label>
                      </div>
                      <div class="phone-addon">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <div class="pr">
                            <input type="text" class="form-control listing_search date_picker"  name="fromdate"  />
                          </div>
                        </div>
                      </div>
                      <div class="phone-addon">
                        <label class="mn">To</label>
                      </div>
                      <div class="phone-addon">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <div class="pr">
                            <input type="text" class="form-control listing_search date_picker"  name="todate"  />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <input name="user_id" id="user_id" type="text" class="listing_search"/>
                <label>Requester</label>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
              <div class="form-group height_auto">
                  <input name="company" id="company" type="text" class="listing_search"/>
                  <label>Company</label>
              </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="row">
              <div id="update_date_range" class="col-md-12 col-sm-12">
                <div class="form-group">
                  <select class="form-control listing_search" id="update_join_range" name="update_join_range">
                    <option value=""> </option>
                    <option value="Range">Range</option>
                    <option value="Exactly">Exactly</option>
                    <option value="Before">Before</option>
                    <option value="After">After</option>
                  </select>
                  <label>Last Update Date</label>
                </div>
              </div>
              <div class="col-md-9 col-sm-12" style="display:none" id="update_select_date">
                <div class="form-group">
                  <div id="update_all_join" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <div class="pr">
                      <input type="text" name="update_added_date" class="form-control listing_search date_picker" />
                    </div>
                  </div>
                  <div  id="update_range_join" style="display:none;">
                    <div class="phone-control-wrap">
                      <div class="phone-addon">
                        <label class="mn">From</label>
                      </div>
                      <div class="phone-addon">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <div class="pr">
                            <input type="text" name="update_fromdate" class="form-control listing_search date_picker" />
                          </div>
                        </div>
                      </div>
                      <div class="phone-addon">
                        <label class="mn">To</label>
                      </div>
                      <div class="phone-addon">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          <div class="pr">
                            <input type="text" name="update_todate" class="form-control listing_search date_picker" />
                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input name="groups" id="groups" type="text" class="listing_search"/>
              <label>Group</label>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-6">
            <div class="form-group">
              <input name="assignee_id" id="assignee_id" type="text" class="listing_search"/>
              <label>Assignee</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <select class="se_multiple_select listing_search" name="status[]"  id="status" multiple="multiple" >
              <option value="New">New</option>
              <option value="Working">Working</option>
              <option value="Open">Open</option>
              <option value="Reassigned">Reassigned</option>
              <option value="Abandoned (Admin)">Abandoned (Admin)</option>
              <option value="Abandoned (User)">Abandoned (User)</option>
              <option value="Completed">Completed</option>
              </select>
              <label>Status</label>
            </div>
          </div>
        </div>
        <div class="panel-footer clearfix">
          <button type="submit" class="btn btn-info" name="btn_search" id="btn_search" > <i class="fa fa-search"></i> Search </button>
          <button type="button" class="btn btn-info btn-outline" onclick="location.reload();"> <i class="fa fa-search-plus"></i> View All </button>
        </div>
      </div>
      <input type="hidden" name="is_ajaxed" value="1" id="is_ajaxed">
      <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
      <input type="hidden" name="page" id="nav_page" value="" />
      <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
      <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
    </form>
  </div>
</div>
<div class="search-handle">
  <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
</div>
</div>
<div class="white-box" id="ajax_eticket_data">
</div>
<script type="text/javascript">
$(document).ready(function(e){
  dropdown_pagination('ajax_eticket_data')

  initSelectize('user_id','ticketRequesterID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  initSelectize('assignee_id','ticketAdminID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  initSelectize('fl_tracking_id','ticketID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  initSelectize('groups','ticketGroupID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  initSelectize('company','ticketCompanyID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  $("#status ").multipleSelect({
      selectAll: false,
      filter: false
  });
});
$(document).off('click','.add_etickets');
$(document).on('click','.add_etickets',function(e){
    //$.colorbox({href:'add_etickets.php',iframe: true, width: '550px', height: '605px'});
    window.open('add_etickets.php', "myWindow", "width=550,height=635");
});
$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  if($(this).val() == ''){
    $('#select_date_div').hide();
    $('#date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#date_range').removeClass('col-md-12').addClass('col-md-3');
    $('#select_date_div').show();
    if ($(this).val() == 'Range') {
      $('#range_join').show();
      $('#all_join').hide();
    } else {
      $('#range_join').hide();
      $('#all_join').show();
    }
  }
});

$(document).off('change', '#update_join_range');
$(document).on('change', '#update_join_range', function(e) {
  e.preventDefault();
  if($(this).val() == ''){
    $('#update_select_date').hide();
    $('#update_date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#update_date_range').removeClass('col-md-12').addClass('col-md-3');
    $('#update_select_date').show();
    if ($(this).val() == 'Range') {
      $('#update_range_join').show();
      $('#update_all_join').hide();
    } else {
      $('#update_range_join').hide();
      $('#update_all_join').show();
    }
  }
});

$(document).ready(function() {
  ajax_submit();
  $(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
  });
});

$(document).off("submit","#eticketForm");
$(document).on("submit","#eticketForm",function(e){
    e.preventDefault();
    disable_search();
});

$(document).off('click', '.open_conversation_link');
$(document).on('click', '.open_conversation_link', function(e) {
  e.preventDefault();
  var $id = $(this).attr('data-target').replace("#open_conversation_",'');
  $('.open_conversation_collapse').collapse('hide');
  $attr = $('.open_conversation_collapse').attr('aria-expanded');
  if($('#open_conversation_'+$id).html() == ""){
    $('.open_conversation_collapse').html("");
    $(".caret_class").removeClass('fa-caret-down');
    $(".caret_class").addClass('fa-caret-right');
    $("#caret_class_"+$id).toggleClass('fa-caret-right fa-caret-down');
    $.ajax({
      url: 'open_conversation_preview.php',
      type: 'POST',
      data:{s_ticket_id:$id},
      beforeSend : function(e){
        $('#ajax_loader').show();
      },
      success: function(res) {
          $('#ajax_loader').hide();
          $('#open_conversation_'+$id).html(res);
          $('#open_conversation_'+$id).collapse('toggle');
      }
    });
  }else{
    $("#caret_class_"+$id).toggleClass('fa-caret-down fa-caret-right');
    $('.open_conversation_collapse').collapse('hide');
    $('.open_conversation_collapse').html("");
  }
});

$(document).off('click', '#ajax_eticket_data ul.pagination li a');
$(document).on('click', '#ajax_eticket_data ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#ajax_eticket_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_eticket_data').html(res).show();
            common_select();
            fRefresh();
        }
    });
});

$(document).off('change', '.reassigned_action');
$(document).on('change', '.reassigned_action', function(e) {
  e.stopPropagation();
  var id = $(this).attr('data-id');
  var category_id = $(this).attr('data-category_id');
  var this_val = $(this).val();
  $.colorbox({
    href: 'reassigned_etickets.php?category_id='+this_val+'&id='+id,
    iframe: true,
    width: '515px',
    height: '500px',
    fastIframe: false,
    onClosed:function(e){
      $("#reassigned_action_"+id).val(category_id);
      $("#reassigned_action_"+id).selectpicker('render');
    }
  });
});

changeAssignee = function($this){
  var $tracking_id = $this.attr('data-tracking_id');
  var $category_id = $this.attr('data-category_id');
  var value = $this.val();
  var oldvalue = $this.attr('data-oldval');
  swal({
      text: "Change Assignee: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
      $.ajax({
        url:'etickets.php',
        data:{
          change_assignee : 1,
          id:value,
          tracking_id:$tracking_id,
          category_id:$category_id,
        },
        dataType:'json',
        type:'get',
        beforeSend : function(e){
          $("#ajax_loader").show();
        },
        success : function(res){
          $("#ajax_loader").hide();
          if(res.status == 'success'){
            parent.setNotifySuccess(res.msg);
            parent.ajax_submit();
          }else{
            parent.setNotifyError(res.msg);
            parent.ajax_submit();
          }
        }
      });
    }, function(dismiss) {
          $("#changeAssignee_"+$tracking_id).val(oldvalue);
          $("#changeAssignee_"+$tracking_id).selectpicker('render');
    });
}

changeStatus = function($this){
  var $tracking_id = $this.attr('data-tracking_id');
  var $category_id = $this.attr('data-category_id');
  var value = $this.val();
  var oldvalue = $this.attr('data-oldval');
  swal({
      text: "Change Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function() {
      $.ajax({
        url:'etickets.php',
        data:{
          change_status : 1,
          value:value,
          tracking_id:$tracking_id,
          category_id:$category_id,
        },
        dataType:'json',
        type:'get',
        beforeSend : function(e){
          $("#ajax_loader").show();
        },
        success : function(res){
          $("#ajax_loader").hide();
          if(res.status == 'success'){
            parent.setNotifySuccess(res.msg);
            parent.ajax_submit();
          }else{
            parent.setNotifyError(res.msg);
            parent.ajax_submit();
          }
        }
      });
    }, function(dismiss) {
          $("#changeStatus_"+$tracking_id).val(oldvalue);
          $("#changeStatus_"+$tracking_id).selectpicker('render');
    });
}

ajax_submit = function(){
  $.ajax({
    url : 'etickets.php',
    data : $("#eticketForm").serialize(),
    type : 'GET',
    beforeSend : function(e){
      $('#ajax_loader').show();
      $('#ajax_eticket_data').hide();
    },
    success : function(res){
      $('#ajax_loader').hide();
      $("#ajax_eticket_data").html(res).show();
      common_select();
      fRefresh();
    }
  });
}
</script>
<?php } ?>


<?php if ($is_ajaxed) {?>
<div class="panel panel-default panel-block">
  <div class="panel-body">
 <div class="clearfix tbl_filter">
  <div class="pull-left">
    <h4 class="m-t-7">Triggers</h4>
  </div>
    <div class="pull-right">
      <div class="m-b-15">
        <a href="manage_trigger.php" class="btn btn-action" >+ Trigger</a>
      </div>
    </div>
  </div>
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr class="data-head">
            <th><a href="javascript:void(0);" data-column="t.created_at" data-direction="<?php echo $SortBy == 't.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">ID/Added Date</a></th>
            <th><a href="javascript:void(0);" data-column="t.title" data-direction="<?php echo $SortBy == 't.title' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name</a></th>
            <th><a href="javascript:void(0);">User Group</a></th>
            <th><a href="javascript:void(0);">Company</a></th>
            <th><a href="javascript:void(0);">Type</a></th>
             <th class="text-center">Preview</th>
            <th width="150px"><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
            <th width="130px" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            if ($total_rows > 0) { 
            foreach ($fetch_rows as $rows) { 
          ?>

          <tr>
            <td>
              <a href="manage_trigger.php?id=<?=$rows['id']?>"  class="fw500 text-red"><?=$rows['display_id']?></a><br><?=date("m/d/Y",strtotime($rows['created_at']))?>
            </td>
            <td><?=$rows['title']?></td>
            <td><?=!empty($rows['user_group']) ? ucfirst($rows['user_group']) : '-'?></td>
            <td><?=!empty($rows['company_name']) ? $rows['company_name'] : '-'?></td>
            <td><?=$rows['type']?></td>
            <td class="text-center icons">
              <a href="email_trigger_preview.php?id=<?=$rows['id']?>" class="previewEmail"><i class="fa fa-eye"></i></a>
            </td>
            <td>
              <div class="theme-form pr">
                <select name="status" class="form-control has-value" id="triggerStatusSel" onchange="updTriggerStatus('<?=$rows['id']?>',$(this))">
                  <option value="Active" <?=$rows['status'] == 'Active' ? 'selected="selected"' : ''?>>Active</option>
                  <option value="Inactive" <?=$rows['status'] == 'Inactive' ? 'selected="selected"' : ''?>>Inactive</option>
                </select>
                <label>Select</label>
              </div>
            </td>
            <td class="icons text-right">
              <a href="manage_trigger.php?id=<?=$rows['id']?>&action=Clone" target="_blank" title="Clone Email Trigger" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-clone" aria-hidden="true"></i></a>
              <a href="manage_trigger.php?id=<?=$rows['id']?>&action=Edit" title="Edit Email Trigger" data-toggle="tooltip" data-trigger="hover"><i class="fa fa-edit"></i></a>
              <!-- System Default (Requireds) Triggers not allowed to Delete -->
              <a href="javascript:void(0);" title="Delete" data-toggle="tooltip" data-trigger="hover" onclick="delTrigger('<?=$rows['id']?>')"><i class="fa fa-trash"></i></a>
              <!-- <?php if($rows['is_default'] != 'Y'){ ?> -->
            <!-- <?php } ?> -->
            </td>
          </tr>
          <?php }
              } else {
          ?>
            <tr>
                <td colspan="8" class="text-center">No record(s) found</td>
            </tr>
        <?php }?>
        </tbody>
         <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="8">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php } ?>
      </table>
    </div>
  </div>
</div>
<?php } else {
  ?>

    <?php include_once 'notify.inc.php';?>

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
      <form id="triggerSearch" action="triggers.php" method="GET" autocomplete="off"> <div class="panel-body theme-form">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="triggerDispIds" value="<?=!empty($triggerDispIds) ? $triggerDispIds : ''?>" name="" class="form-control listing_search">
                <label>ID Number(s)</label>
              </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                  <div id="setDateDiv" class="col-md-12">
                    <div class="form-group">
                      <select class="form-control" id="searchRangeSel" name="searchRange" value="<?!empty($searchRange) ? $searchRange : ''?>">
                        <option value=""> </option>
                        <option value="Range">Range</option>
                        <option value="Exactly">Exactly</option>
                        <option value="Before">Before</option>
                        <option value="After">After</option>
                      </select>
                      <label>Added Date</label>
                    </div>
                  </div>
                  <div id="dateRangeDiv" class="col-md-9" style="display:none">
                    <div class="form-group">
                      <div id="allJoinDiv" class="input-group"> 
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="addedDate" id="addedDate" class="form-control listing_search" />
                      </div>
                      <div id="rangeJoinDiv" style="display:none;">
                        <div class="phone-control-wrap">
                          <div class="phone-addon">
                            <label class="mn">From</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group"> 
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="fromDate" id="fromDate" value="" class="form-control listing_search" />
                            </div>
                          </div>
                          <div class="phone-addon">
                            <label class="mn">To</label>
                          </div>
                          <div class="phone-addon">
                            <div class="input-group"> 
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" name="toDate" id="toDate" value="" class="form-control listing_search" />
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
                <input type="text" name="title" value="<?=!empty($title) ? $title : ''?>" id="title" class="form-control listing_search">
                <label>Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                <select class="se_multiple_select listing_search" name="userGroup[]" id="userGroupSel" multiple="multiple">
                  <option value="agent">Agent</option>
                  <option value="group">Group</option>
                  <option value="member">Member</option>
                  <option value="other">Other</option>
                </select>
                <label>User Group</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                 <select class="form-control listing_search" name="company" id="companySel">
                    <option></option>
                    <?php 
                      if(!empty($companyRes)){
                        foreach ($companyRes as $company) {
                      ?>
                        <option value="<?=$company['id']?>"><?=$company['company_name']?></option>
                      <?php
                        }
                      }
                    ?>
                  </select>
                <label>Company</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group ">
                <select class="se_multiple_select listing_search" name="type[]" id="typeSel" multiple="multiple">
                  <option value="Email">Email</option>
                  <option value="SMS">Text Message</option>
                  <option value="Both">Email & Text Message</option>
                </select>
                <label>Trigger Type</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <select class="form-control listing_search" name="statusSearch" id="statusSel">
                  <option></option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
                <label>Status</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text"class="form-control listing_search" name="subject" id="subject">
                <label>Email Subject</label>
              </div>
            </div>
          </div>
          <div class="panel-footer clearfix">
            <button type="submit" class="btn btn-info" name="search" id="searchTriggerBtn"> <i class="fa fa-search"></i> Search </button>
            <button type="button" class="btn btn-info btn-outline" onclick="window.location.href='triggers.php'"> <i class="fa fa-search-plus" ></i> View All </button>
            <button type="button" class="btn red-link" name="export_trigger" id="export_trigger"> <i class="fa fa-download"></i> Export </button>
              <input type="hidden" name="export" id="export" value=""/>
              <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
              <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
              <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
              <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />


          </div>
        </div>
    </div>
  </div>
  <div class="search-handle">
    <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a>
  </div>
</div>
<div id="ajaxData"></div>

<script type="text/javascript">
  $(document).ready(function(){
    dropdown_pagination('ajaxData')
    triggerSearch();
  // initialization code start
    $("#typeSel, #userGroupSel").multipleSelect({
         selectAll: false,
         filter:false
    });
    $("#addedDate").mask("99/99/9999");
    $("#fromDate").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
    $("#toDate").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
    $("#addedDate").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
  });

  $(document).off('change', '#searchRangeSel');
  $(document).on('change', '#searchRangeSel', function(e) {
    e.preventDefault();
    $('#addedDate').val('');
    var dateSel = $("#searchRangeSel option:selected").val();
    if(dateSel == ''){
      $('#dateRangeDiv').hide();
      $('#setDateDiv').removeClass('col-md-3').addClass('col-md-12');
    }else{
      $('#setDateDiv').removeClass('col-md-12').addClass('col-md-3');
      $('#dateRangeDiv').show();
      if (dateSel == 'Range') {
        $('#rangeJoinDiv').show();
        $('#allJoinDiv').hide();
      } else {
        $('#rangeJoinDiv').hide();
        $('#allJoinDiv').show();
      }
    }
  });

  $(document).off('click', '#ajaxData tr.data-head a');
  $(document).on('click', '#ajaxData tr.data-head a', function (e) {
      e.preventDefault();
      $('#sort_by_column').val($(this).attr('data-column'));
      $('#sort_by_direction').val($(this).attr('data-direction'));
      triggerSearch();
  });

  $(document).off('click', '#ajaxData ul.pagination li a');
  $(document).on('click', '#ajaxData ul.pagination li a', function (e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#ajaxData').hide();
      $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function (res) {
              $('#ajax_loader').hide();
               $('[data-toggle="tooltip"]').tooltip();
              $('#ajaxData').html(res).show();
              common_select();
                $('.previewEmail').colorbox({
                  iframe:true,
                  height:"590px",
                  width:"685px",
                });
          }
      });
  });

   $(document).off("submit", "#triggerSearch");
  $(document).on("submit", "#triggerSearch", function(e) {
    e.preventDefault();
    if ($(".listing_search").filter(function() {
            return $(this).val();
        }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
            return $(this).val();
        }).length > 0)) {
        triggerSearch();
    } else {
        swal('Oops!!', 'Please Enter Data To Search', 'error');
    }
  });

  $(document).off('click', '#export_trigger');
  $(document).on('click', '#export_trigger', function(e) {
      confirm_export_data(function() {
          $("#export").val('export_trigger');
          $('#ajax_loader').show();
          $('#is_ajaxed').val('1');
          var params = $('#triggerSearch').serialize();
          $.ajax({
              url: $('#triggerSearch').attr('action'),
              type: 'GET',
              data: params,
              dataType: 'json',
              success: function(res) {
                  $('#ajax_loader').hide();
                  $("#export").val('');
                  if(res.status == "success") {
                      confirm_view_export_request();
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
  });


  triggerSearch = function(){
        $('#ajax_loader').show();
        $('#ajaxData').hide();
        $('#is_ajaxed').val('1');
        var params = $('#triggerSearch').serialize();
        $.ajax({
            url: $('#triggerSearch').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#ajaxData').html(res).show();
                $('[data-toggle="tooltip"]').tooltip();
                common_select();

                $('.previewEmail').colorbox({
                  iframe:true,
                  height:"590px",
                  width:"685px",
                });
            }
        });
        return false;
  }

  updTriggerStatus = function(triggerId,obj) {
      var triggerStatus = $(obj).val();
      swal({
          text: '<br>Change Status: Are you sure?',
          showCancelButton: true,
          confirmButtonText: "Confirm",
      }).then(function () {
          // window.location = 'triggers.php?action=updStatus&id=' + triggerId + '&status=' + triggerStatus;
          $.ajax({
            url: 'triggers.php',
            type: 'GET',
            data: {action:'updStatus',id:triggerId,status:triggerStatus},
            dataType : 'JSON',
            success: function (res) {
                $('#ajax_loader').hide();
                if(res.status == 'success'){
                  setNotifySuccess('Trigger status updated successfully');
                }else{
                  setNotifyError('Something went wrong');
                }
                
            }
        });
        return false;
      });
  }

  delTrigger = function(triggerId) {
      swal({
          text: '<br>Delete Record: Are you sure?',
          showCancelButton: true,
          confirmButtonText: "Confirm",
      }).then(function () {
          window.location = 'triggers.php?action=delTrigger&id=' + triggerId;
      });
  }

</script>
<?php }?>
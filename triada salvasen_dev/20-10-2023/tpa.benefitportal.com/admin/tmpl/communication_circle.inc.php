<?php if($is_ajaxed){ ?> 
<div class="panel panel-default panel-block">
  <div class="panel-body">
  <div class="clearfix tbl_filter">
      <h4 class="m-t-7">Circles</h4>
      </div>
    <div class="clearfix tbl_filter">
      <div class="pull-right">
        <div class="m-b-15">
          <div class="note_search_wrap auto_size" id="search_div" style="<?=checkIsset($_GET['circleName'])!='' ? 'display: inline-block; max-width: 100%;' : 'display: none; max-width: 100%;'?>">
            <div class="phone-control-wrap theme-form">
              <div class="phone-addon">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="search_close_btn text-light-gray" onclick="getCircleData('');">X</a>
                </div>
              </div>
              <div class="phone-addon">
                <div class="form-group height_auto mn">
                <input type="text" id="circleName"  class="required form-control <?=checkIsset($_GET['circleName'])!='' ? 'has-value' : ''?>" value="<?=checkIsset($_GET['circleName'])?>" >
                <label>Circle Name or Admin ID</label>
                </div>
              </div>
              <div class="phone-addon w-80">
                <div class="form-group height_auto mn">
                <a href="javascript:void(0);" class="btn btn-info search_button">Search</a>
                </div>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" class="search_btn" style="<?=checkIsset($_GET['circleName'])!='' ? 'display: none;'  : 'display: inline-block;'?>" ><i class="fa fa-search fa-lg text-blue"></i></a>
          <a href="add_circle.php" class="btn btn-action m-l-5" style="display:inline-block;" >+ Circle</a>
        </div>
        
      </div>
    <!-- </div>
    <div class="clearfix"> -->
    <?php if ($total_rows > 0) { ?>
        <div class="pull-left">
          <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
            <div class="form-group mn">
              <label for="user_type">Records Per Page </label>
            </div>
            <div class="form-group mn">
              <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);getCircleData('');">
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
            <th><a href="javascript:void(0);" data-column="created_at" data-direction="<?php echo $SortBy == 'created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Added</a></th>
            <th><a href="javascript:void(0);" data-column="name" data-direction="<?php echo $SortBy == 'name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Circle Name</a></th>
            <th><a href="javascript:void(0);" data-column="fname" data-direction="<?php echo $SortBy == 'fname' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Created By</a></th>
            <th class="text-center" width="15%"><a href="javascript:void(0);" data-column="totalAdmin" data-direction="<?php echo $SortBy == 'totalAdmin' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Admins</a></th>
            <th width="20%"><a href="javascript:void(0);" data-column="status" data-direction="<?php echo $SortBy == 'status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Status</a></th>
            <th width="15%"><a href="javascript:void(0);" data-column="last_message_at" data-direction="<?php echo $SortBy == 'last_message_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Last Message Date</a></th>
            <th width="130px">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($total_rows > 0) { ?>
          <?php foreach ($fetch_rows as $rows) { ?>
         <tr>
           <td><?=$tz->getDate($rows['created_at'],'m/d/Y')?></td>
           <td><?=$rows['name']?></td>
           <td><?=$rows['fname'].' '.$rows['lname']?></td>
           <td class="text-center"><a href="javascript:void(0)" data-href="circle_admin_popup.php?id=<?=md5($rows['id'])?>" class="fw500 text-action circle_admin_popup"><?=$rows['totalAdmin']?></a></td>
           <td>
             <div class="theme-form pr w-200">
              <select class="form-control has-value circle_status" name="status" id="circle_status_<?=md5($rows['id'])?>">
               <option value="Active" <?=$rows['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
               <option value="Inactive" <?=$rows['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
              <label>Select</label>
             </div>
           </td>
           <td><?=getCustomDate($rows['last_message_at'])?></td>
           <td class="icons ">
              <a href="add_circle.php?id=<?=md5($rows['id'])?>" data-toggle="tooltip" data-title="Edit"><i class="fa fa-edit"></i></a>
              <!-- <a href="javascript:void(0)" data-toggle="tooltip" data-title="Download"><i class="fa fa-download"></i></a> -->
              <a href="javascript:void(0)" class="delete_admin_circle" onclick="deleteAdminCircle('<?=md5($rows['id'])?>')" data-toggle="tooltip" data-trigger="hover" data-title="Delete"><i class="fa fa-trash"></i></a>
            </td>
         </tr>
         <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="7" align="center">No record(s) found</td>
          </tr>
        <?php } ?>
        </tbody>
        <?php if ($total_rows > 0) { ?>
          <tfoot>
            <tr>
              <td colspan="7">
                <?php echo $paginate->links_html; ?>
              </td>
            </tr>
          </tfoot>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
<?php }else{ ?>
<div class="panel panel-default panel-block advance_info_div">
  <div class="panel-body">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-130">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="120px">
        </div>
        <div class="phone-addon text-left"> <p class="fs14 m-b-20">Circles are an internal chat function between admins only.</p>
          <div class="info_box info_box_max_width">
            <p class="fs14 mn">Admins may be assigned to multiple circles and be removed without interrupting the existing chat.</p>
          </div>
        </div>
      </div>
  </div>
</div>
<form id="frm_search" name="frm_search" action="">
  <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
  <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>" />
  <input type="hidden" name="sort_by" id="sort_by_column" value="<?= $SortBy; ?>" />
  <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?= $SortDirection; ?>" />
</form>
<div id="ajax_data">
</div>
<script type="text/javascript">
$(document).off("click", ".search_btn");
$(document).on("click", ".search_btn", function(e) {
  e.preventDefault();
  $(this).hide();
  $("#search_div").css('display', 'inline-block');
});


$(document).off("click", ".search_button");
$(document).on("click", ".search_button", function(e) {
  e.preventDefault();
  if($('#circleName').val() == ''){
    $('#circleName').focus();
    // search_button
  }else{
    getCircleData($('#circleName').val());
  }
});

$(document).off("click", ".search_close_btn");
$(document).on("click", ".search_close_btn", function(e) {
  e.preventDefault();
  $("#search_div").hide();
  $(".search_btn").show();

});
$(document).ready(function(){
  dropdown_pagination('ajax_data')
  getCircleData('');
  
});
$(document).off("click",".circle_admin_popup");
$(document).on("click",".circle_admin_popup",function(e){
  e.preventDefault();
  var $href = $(this).attr('data-href');
  $.colorbox({
    href:$href,
    iframe: true, 
    width: '800px', 
    height: '400px'
  });
})

$(document).off('click', '#ajax_data tr.data-head a');
$(document).on('click', '#ajax_data tr.data-head a', function(e) {
  e.preventDefault();
  $('#sort_by_column').val($(this).attr('data-column'));
  $('#sort_by_direction').val($(this).attr('data-direction'));
  getCircleData('');
});

$(document).off('click', '#ajax_data ul.pagination li a');
  $(document).on('click', '#ajax_data ul.pagination li a', function(e) {
    e.preventDefault();
  $('#ajax_loader').show();
    $('#ajax_data').hide();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      data:{"is_ajaxed":1},
      success: function(res) {
        $('#ajax_loader').hide();
        $('#ajax_data').html(res).show();
        common_select();
      }
    });
  });
  
function getCircleData(circleName){
  $incr = '';
  if(circleName!='' && circleName!='undefined'){
    $incr ='?circleName='+circleName;
  }
  $.ajax({
    url:'communication_circle.php'+$incr,
    data:$('#frm_search').serialize(),
    type:'get',
    beforeSend:function(e){
      $("#ajax_loader").show();
      $("#ajax_data").hide();
    },
    success:function(res){
      $("#ajax_loader").hide();
      $("#ajax_data").html(res).show();
      common_select();
      $('[data-toggle="tooltip"]').tooltip();
    }
  })
}

$(document).off('change','.circle_status');
$(document).on('change','.circle_status',function(e){
  e.preventDefault();
  var id = $(this).attr('id').replace('circle_status_','');
  var status = $(this).val();
  $.ajax({
      url:'communication_circle.php',
      data:{id:id,is_status:'Y',status:status},
      type:'post',
      beforeSend:function(e){
        $("#ajax_loader").show();
        $("#ajax_data").hide();
      },
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status == 'success'){
          setNotifySuccess(res.message);
        }else{
          setNotifyError(res.message);
        }
        getCircleData('');
      }
    });
});

function deleteAdminCircle(id){
  swal({
    text: 'Delete Circle: Are you sure?',
    //type: 'warning',
    showCancelButton: true,
    //confirmButtonColor: "#DD6B55",
    confirmButtonText: 'Confirm',
    cancelButtonText: 'Cancel',
  }).then(function() {
    $.ajax({
      url:'communication_circle.php',
      data:{id:id,is_deleted:'Y'},
      type:'post',
      beforeSend:function(e){
        $("#ajax_loader").show();
        $("#ajax_data").hide();
      },
      success:function(res){
        $("#ajax_loader").hide();
        if(res.status == 'success'){
          setNotifySuccess(res.message);
        }else{
          setNotifyError(res.message);
        }
        getCircleData('');
      }
    });
  }, function(dismiss) {
  });
}
</script>
<?php } ?>

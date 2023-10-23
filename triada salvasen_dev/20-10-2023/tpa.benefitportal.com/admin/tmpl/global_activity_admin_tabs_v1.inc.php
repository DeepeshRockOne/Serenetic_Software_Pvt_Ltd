<?php if($is_ajaxed_admins){ ?>
        <?php include_once 'activity_history_popup.inc.php'; ?>
<?php }else{ ?>
    <div class="panel panel-default panel-block panel-title-block">
  <form id="frm_search_admins" action="global_activity_admin_tabs.php" method="GET">
    <div class="panel-left">
      <div class="panel-left-nav">
        <ul>
          <li class="active"><a href="javascript:void(0);"><i class="fa fa-search"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="panel-right">
      <div class="panel-heading">
        <div class="panel-search-title"> <span class="clr-light-blk">SEARCH HISTORY</span></div>
      </div>
      <div class="panel-wrapper collapse in">
        <div class="panel-body theme-form">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group"> 
                
                <!-- <select class="form-control" name="activity_type" id="activity_type">
              <option></option>
              <?php if(count($actionRes)>0){
                foreach ($actionRes as $rows) {
                    $actions_arr = explode(",",$rows['ea']); 
                    }
                    foreach ($actions_arr as $key => $value) {
                   ?>
                <option value="<?=$value?>"><?=$value?></option>
              <?php 
              }
              } ?>
            </select> -->
                <select class="form-control" name="activity_type" id="activity_type">
                  <option value="" disabled selected hidden> </option>
                  <?php foreach ($activity_type_res as $val) { ?>
                  <option value="<?php echo $val['entity_action'] ?>"><?php echo $val['entity_action']; ?></option>
                  <?php } ?>
                </select>
                <label>Activity Type</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="form-control" id="join_range" name="join_range">
                      <option value="" disabled selected hidden> </option>
                      <option value="exactly">Exactly</option>
                      <option value="before">Before</option>
                      <option value="after">After</option>
                      <option value="range">Range</option>
                    </select>
                    <label>Added Date</label>
                  </div>
                </div>
                <div class="col-md-10">
                  <div class="form-group">
                    <div id="all_join" class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input  type="text" name="join_date" id="join_date" class="form-control date_mask"  placeholder="MM / DD / YYYY">
                    </div>
                    <div  id="range_join" style="display:none;">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <label class="mn">From</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input  type="text" class="form-control date_mask" name="date_from" id="date_from" placeholder="MM / DD / YYYY">
                          </div>
                        </div>
                        <div class="phone-addon">
                          <label class="mn">To</label>
                        </div>
                        <div class="phone-addon">
                          <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input  type="text" class="form-control date_mask" id="to_date" name="to_date" placeholder="MM / DD / YYYY">
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
                <input type="text" id="fname" name="fname"  class="form-control" >
                <label>Admin First Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" id="lname" name="lname" class="form-control" >
                <label>Admin Last Name:</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="impacted_name" id="impacted_name"  class="form-control" >
                <label>Account Impacted Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="ip_address" id="ip_address"  class="form-control" >
                <label>IP Address</label>
              </div>
            </div>
          </div>
          <div class="panel-footer clearfix">
            <div class="pull-left">
              <button type="button" class="btn btn-info" name="search" id="search"  data-value="search" onclick="ajax_submit_admins()" > <i class="fa fa-search"></i> Search </button>
              <button type="button" class="btn btn-info btn-outline" name="viewall" id="viewall" onClick="window.location = 'global_activity_history.php'"> Clear Search </button>
            </div>
            <input type="hidden" name="search_type" id="search_type" value="" />
            <input type="hidden" name="is_ajaxed_admins" id="is_ajaxed_admins" value="1" />
            <input type="hidden" name="is_ajaxed_search" id="is_ajaxed_search" value="" />
            <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
            <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
            <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="search-handle"> <a href="#" data-perform="panel-collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></a> </div>
</div>
<div class="panel panel-default panel-block">
        <div class="list-group">
            <div id="ajax_loader" class="ajex_loader" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="ajax_data" class="list-group-item"></div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){

      $(".export_history").colorbox({iframe: true,width:'1100px', height:'600px'});
      $(".download_history").click(function(){
      // alert();
      cus_id=$(this).data('id');
      user_type=$(this).data('type');
      $('#ajax_loader').show();
      $.ajax({
          url: 'export_activity_history.php',
          type: 'POST',
          dataType:'json',
          data: {customer_id:cus_id,usertype:user_type,export_single_history:"export_activity"}
        }).done(function(data){
          $('#ajax_loader').hide();
            var $a = $("<a>");
              $a.attr("href",data.file);
              $("body").append($a);
              $a.attr("download","Activity History.xls");
              $a[0].click();
              $a.remove();
        });
    });
    $(".download").click(function(){
      // alert();
      cus_id=$(this).data('id');
      user_type=$(this).data('type');
      time=$(this).data('time');
      entity_type=$(this).data('entity_type');
      $('#ajax_loader').show();
      $.ajax({
          url: 'export_activity_history.php',
          type: 'POST',
          dataType:'json',
          data: {customer_id:cus_id,usertype:user_type,time:time,entity_type:entity_type,export_single_history:"export_activity"}
        }).done(function(data){
            var $a = $("<a>");
              $a.attr("href",data.file);
              $("body").append($a);
              $a.attr("download","Activity History.xls");
              $a[0].click();
              $a.remove();
              $('#ajax_loader').hide();
        });
    });
    });
  </script>
  
<script type="text/javascript">  

$(document).ready(function() {
  ajax_submit_admins();
});

function resetForm() {
  $("#search_frm").find("input[type=text], textarea").attr("value", "");
  $("#search_frm").find("select option").removeAttr("selected");
  $.uniform.update();
}
function ajax_submit_admins() {
  $('#ajax_loader').show();
  $('#ajax_data').hide();
  $('#is_ajaxed_admins').val('1');
//   $('#is_ajaxed_search').val('1');

  var params = $('#frm_search_admins').serialize();
  var all_usersFrm = $('#all_usersFrm').serialize();
  params += '&'+all_usersFrm;
  $.ajax({
      url: $('#frm_search_admins').attr('action'),
      type: 'GET',
      data: params,
      success: function (res) {
          $('#ajax_loader').hide();
          $('#ajax_data').html(res).show();
          common_select();
          
      }
  });
  return false;
}
$(document).ready(function() {
$('.date_mask').inputmask('99/99/9999');
 $(".select-multiselect").multipleSelect({
    width: '100%',
    //filter:true
});
});
$(document).on('change','#join_range',function(){
if($(this).val() == 'range'){
                $('#range_join').show();
                $('#all_join').hide();
}else{
                $('#range_join').hide();
                $('#all_join').show();
}
});
</script>
<?php } ?>
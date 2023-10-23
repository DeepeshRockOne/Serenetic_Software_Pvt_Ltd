<?php if($is_all_activity_popup == 'Y'){ ?>
        <?php include_once 'activity_history_popup.inc.php'; ?>
<?php }else{ ?>
<div class="panel panel-default panel-block panel-title-block global_activity">
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
                  <div class="col-md-6">
                     <div class="form-group">
                        <select class="form-control listing_search" name="activity_type" id="activity_type" data-live-search="true">
                           <option value=""></option>
                           <?php if(!empty($activity_type_res)){
                               foreach ($activity_type_res as $val) { ?>
                           <option value="<?php echo $val['entity_action'] ?>"><?php echo $val['entity_action']; ?></option>
                           <?php } }?>
                        </select>
                        <label>Activity Type</label>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                        <div id="date_range" class="col-md-12">
                           <div class="form-group">
                              <select class="form-control" id="join_range" name="join_range">
                                 <option value=""> </option>
                                 <option value="exactly">Exactly</option>
                                 <option value="before">Before</option>
                                 <option value="after">After</option>
                                 <option value="range">Range</option>
                              </select>
                              <label>Added Date</label>
                           </div>
                        </div>
                        <div class="select_date_div col-md-9" style="display:none">
                           <div class="form-group">
                              <div id="all_join" class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                 <input  type="text" name="join_date" id="join_date" class="form-control date_picker"  placeholder="MM / DD / YYYY">
                              </div>
                              <div  id="range_join" style="display:none;">
                                 <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                       <label class="mn">From</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input  type="text" class="form-control date_picker" name="date_from" id="date_from" placeholder="MM / DD / YYYY">
                                       </div>
                                    </div>
                                    <div class="phone-addon">
                                       <label class="mn">To</label>
                                    </div>
                                    <div class="phone-addon">
                                       <div class="input-group"> <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                          <input  type="text" class="form-control date_picker" id="to_date" name="to_date" placeholder="MM / DD / YYYY">
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
                        <input type="text" class="listing_search" name="activity_by" id="activity_by" placeholder="">
                        <label>Activity By</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" class="listing_search" name="impacted_name" id="impacted_name" placeholder="">
                        <label>Impacted Account</label>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="form-group">
                        <input type="text" class="listing_search" name="ip_address" id="ip_address" placeholder="">
                        <label>IP Address</label>
                     </div>
                  </div>
               </div>
               <div class="panel-footer clearfix">
                  <div class="pull-left">
                     <button type="button" class="btn btn-info" name="search" id="btn_ajax_submit_admins" data-value="search"> <i class="fa fa-search"></i> Search </button>
                  </div>
                  <input type="hidden" name="search_type" id="search_type" value="" />
                  <input type="hidden" name="is_ajaxed_admins" id="is_ajaxed_admins" value="" />
                  <input type="hidden" name="is_all_activity_popup" id="is_all_activity_popup" value="Y" />
                  <input type="hidden" name="type" id="type" value="Admin" />
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
   <div class="panel-body">
   <h6 id="msg_error" style="text-align: center;">"Please enter search criteria for desired results"</h6>
      <div id="ajax_loader" class="ajex_loader" style="display: none;">
         <div class="loader"></div>
      </div>
      <div id="ajax_data"></div>
   </div>
</div>
<script type="text/javascript">  
var from_limit=0;
$(document).ready(function() {
   initSelectizeDropDown('activity_by','globalActivityAdmin',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
   initSelectizeDropDown('impacted_name','globalActivityAdmin',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
   initSelectizeDropDown('ip_address','IPaddressAdmin',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
  //ajax_submit_admins();
  $(document).off("click","#btn_ajax_submit_admins");
  $(document).on("click","#btn_ajax_submit_admins",function(){
      if ($(".listing_search").filter(function() {
          return $(this).val();
        }).length > 0 || ($("#join_range").val() != '' && $(".date_picker").filter(function() {
          return $(this).val();
        }).length > 0)) {
        ajax_submit_admins();
      } else {
        swal('Oops!!', 'Please Enter Data To Search', 'error');
      }
  });
  setTimeout(function(){
      common_select();
  },1000);
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
          $('#msg_error').hide();
          common_select();
          
      }
  });
  return false;
}
$(document).ready(function() {
// $('.date_picker').inputmask('99/99/9999');
  $(".date_picker").datepicker({
     changeDay: true,
     changeMonth: true,
     changeYear: true,
     container: "body"
  });
 
});
$(document).off('change', '#join_range');
$(document).on('change', '#join_range', function(e) {
  e.preventDefault();
  $('.date_picker').val('');
  if($(this).val() == ''){
    $('.select_date_div').hide();
    $('#date_range').removeClass('col-md-3').addClass('col-md-12');
  }else{
    $('#date_range').removeClass('col-md-12').addClass('col-md-3');
    $('.select_date_div').show();
    if ($(this).val() == 'range') {
      $('#range_join').show();
      $('#all_join').hide();
    } else {
      $('#range_join').hide();
      $('#all_join').show();
    }
  }
});
</script>
<?php } ?>
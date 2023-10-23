<?php if($is_ajaxed) { ?>
<!-- bundle tab -->
<div class="panel panel-table">
    <div class="table-responsive">
        <table class="<?=$table_class?>">
           <thead>
              <tr>
                 <th>Added Date</th>
                 <th>ID/Group Name</th>
                 <th width="130px">Actions</th>
              </tr>
           </thead>
           <tbody>
           		<?php if(!empty($total_rows)){
           		    foreach ($fetch_rows as $rows) { ?>
           		  	<tr>
           		  		<td><?= $rows['created_at']; ?></td>
           		  		<td>
           		  			<a href="groups_details.php?id=<?=md5($rows['customer_id'])?>" target="_blank"  class="text-action">
                              <strong class="fw500"><?php echo $rows['rep_id']; ?></strong></a></br>
                              <?php echo stripslashes($rows['fname'] . ' ' . $rows['lname']); ?>
                        </td>
                        <td class='icons'>
		                     <a href="add_bundle_recommendation.php?id=<?=md5($rows['customer_id'])?>" data-toggle='tooltip' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>
		                     <a href='javascript:void(0);' data-toggle='tooltip' data-id="<?= md5($rows['customer_id']) ?>" title='Delete' class="DeleteBundle"><i class='fa fa-trash' aria-hidden='true'></i></a>
	                   </td>
           		  	</tr>
           		  	<?php } ?>

           		<?php }else{ ?>
	               <tr>
	                  <td colspan="3" class="text-center">No Record(s) found</td>
	               </tr>
	            <?php } ?>
           </tbody>
             <tfoot>
	            <tr>
	               <?php if ($total_rows > 0) {?> 
	               <td colspan="3">
	                  <?php echo $paginageLinks; ?>
	               </td>
	               <?php }?>
	            </tr>
         </tfoot>
        </table>
    </div>
</div>
<!-- bundle tab end -->
<?php } else {?>
<div class="panel panel-default">
	<form action="bundle_recommendations_tab.php"   action="POST" name="ajax_bundle_data_frm">
		<input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
	   	<input type="hidden" name="perPages" id="perPages" value="<?=$per_page;?>" />
      <input type="hidden" name="page" id="page" value="1" />
	   	<div class="tab-content">
			<div id="bundle_builder">
		      <div class="panel-body">
		        <div class="clearfix tbl_filter">
               <div class="pull-left">
                  <h4 class="m-t-7">Bundle Recommendations</h4>
               </div>
               <div class="pull-right">
                  <a href="javascript:void(0);" class="search_btn" id="bundle_search"><i class="fa fa-search fa-lg text-blue"></i></a>
                  <a href="add_bundle_recommendation.php" class="btn btn-action m-l-5" style="display:inline-block;" >+ Bundle Recommendation</a>
               </div>
               <div class="clearfix"></div>
                <div class="d-block m-b-10 m-t-10">
                  <div class="note_search_wrap  cart_search_div" id="cart_search_div" style="display: none; max-width: 100%;">
                     <div class="row theme-form">
                        <div class="col-md-1 text-right">
                           <div class="form-group height_auto">
                               <a href="javascript:void(0);" id="bundle_search_close" class="search_close_btn text-light-gray">X</a>
                            </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group height_auto mn">
                              <input type="text" class="form-control" id="assign_group" name="assign_group">
                              <label>ID/Group Name</label>
                            </div>
                        </div>
                        <div class="col-md-7">
                           <div class="row">
                             <div id="bundle_date_range" class="col-sm-12">
                                <div class="form-group height_auto mn">
                                   <select class="form-control" id="joinRangeDate" name="join_range_date">
                                      <option value=""> </option>
                                      <option value="Range">Range</option>
                                      <option value="Exactly">Exactly</option>
                                      <option value="Before">Before</option>
                                      <option value="After">After</option>
                                    </select>
                                   <label>Added Date</label>
                                </div>
                             </div>   
                             <div class="select_date_div col-sm-9" style="display:none">
                               <div class="form-group">
                                 <div id="bundle_all_join" class="input-group"> 
                                   <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                   <input type="text" name="added_date" id="bundleaddedDate" value="" class="form-control date_picker" />
                                 </div>
                                 <div  id="bundle_range_join" style="display:none;">
                                   <div class="phone-control-wrap">
                                     <div class="phone-addon">
                                       <div class="input-group"> 
                                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                         <div class="pr">
                                            <input type="text" name="bundlefromDate" id="bundlefromDate" value="" class="form-control date_picker" />
                                            <label>From</label>
                                         </div>
                                       </div>
                                     </div>
                                     <div class="phone-addon">
                                       <div class="input-group"> 
                                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                         <div class="pr">
                                            <input type="text" name="bundletoDate" id="bundletoDate" value="" class="form-control date_picker" />
                                            <label>To</label>
                                         </div>
                                       </div>
                                     </div>
                                   </div>
                                 </div>
                               </div>
                             </div>
                           </div> 
                        </div>
                        <div class="col-md-2">
                            <div class="form-group height_auto">
                               <a href="javascript:void(0);" class="btn btn-info btn-block search_button" id="bundle_search_button">Search</a>
                            </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
		        <div id="ajax_bundle_data"></div>
		      </div>
		    </div>
		</div>		
	</form>
</div>
<script type="text/javascript">
$(document).off("click","#bundle_search");
$(document).on("click", "#bundle_search", function(e) {
  e.preventDefault();
  $(this).hide();
  $("#bundle_search_div").css('display', 'inline-block');
});

$(document).off("click", "#bundle_search_close");
	$(document).on("click", "#bundle_search_close", function(e) {
	  e.preventDefault();
	  $("#bundle_search_div").hide();
	  $("#bundle_search").show();
     getBundleData();
});


$(document).off('click', '.DeleteBundle');
$(document).on('click', '.DeleteBundle',function(e){
    var group_id = $(this).attr('data-id');
    swal({
         text: 'Delete Record: Are you sure?',
         showCancelButton: true,
         confirmButtonText: 'Confirm',
      }).then(function() {
        $("#ajax_loader").show();
          $.ajax({
             url: '<?=$HOST?>' + '/ajax_api_call.php',
             data:{group_id:group_id, api_key:"deleteBundleRecommandation"},
             method:"POST",
             dataType: 'JSON',
             success :function(res){
              $("#ajax_loader").hide();
              window.location.href="manage_groups.php";
             }
          });
        }, function(dismiss){

      });
});


$(document).ready(function(){
	$(".date_picker").datepicker({
	    changeDay: true,
	    changeMonth: true,
	    changeYear: true
  	});
   $(document).off('change', '#joinRangeDate');
   $(document).on('change', '#joinRangeDate', function(e) {
       e.preventDefault();
       $('.date_picker').val('');
       if ($(this).val() == '') {
           $('.select_date_div').hide();
           $('#bundle_date_range').removeClass('col-sm-3').addClass('col-sm-12');
       } else {
           $('#bundle_date_range').removeClass('col-sm-12').addClass('col-sm-3');
           $('.select_date_div').show();
           if ($(this).val() == 'Range') {
               $('#bundle_range_join').show();
               $('#bundle_all_join').hide();
           } else {
               $('#bundle_range_join').hide();
               $('#bundle_all_join').show();
           }
       }
   });
   getBundleData();
   var assign_group = $("#assign_group").val();
   var bundle_join_range = $("#joinRangeDate").val();
   var bundle_added_date = $("#bundleaddedDate").val();
   var bundle_fromdate = $("#fromDate").val();
   var bundle_todate = $("#bundletoDate").val();
   var is_ajaxed = $('#is_ajaxed').val();
   var perPages = $("#perPages").val();

   data = {
      api_key : 'bundleRecommendationsList',
      assign_group : assign_group,
      joinRange : bundle_join_range,
      addedDate : bundle_added_date,
      fromDate : bundle_fromdate,
      toDate : bundle_todate,
      is_ajaxed : is_ajaxed,
      perPages : perPages
   };
   api_dropdown_pagination('ajax_bundle_data',data);
});

$("#bundle_search_button").off('click');
$("#bundle_search_button").on('click',function(){
      var assign_group = $("#assign_group").val();
      var join_range = $("#joinRangeDate").val();
      var added_date = $("#bundleaddedDate").val();
      var fromdate = $("#bundlefromDate").val();
      var todate = $("#bundletoDate").val();

      var search = false;
      if(assign_group != ""){
         search = true;
      }
      if(join_range != ""){
         if(join_range == "Range" &&  fromdate != "" && todate != ""){
            search = true;
         }
         if(join_range != "Range" && added_date != ""){
            search = true;
         }
      }

      if(search){
         getBundleData(assign_group,join_range,added_date,fromdate,todate);
      }else{
         swal({
           text: '<br>Error: Data entry missing',
           showCancelButton: true,
           showConfirmButton: false,
           cancelButtonText: 'Close'
         });
      }
});

$(document).off('click', '#ajax_bundle_data ul.pagination li a');
   $(document).on('click', '#ajax_bundle_data ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader').show();
      var assign_group = $("#assign_group").val();
      var join_range = $("#joinRange").val();
      var added_date = $("#addedDate").val();
      var fromdate = $("#fromDate").val();
      var todate = $("#toDate").val();

      var is_ajaxed = $('#is_ajaxed').val();
      var perPages = $("#perPages").val();
      var page = $(this).attr('data-page');
      $("#page").val(page);
      $.ajax({
         url: $(this).attr('href'),
         data:{
            api_key : 'bundleRecommendationsList',
            assign_group : assign_group,
            joinRange : join_range,
            addedDate : added_date,
            fromDate : fromdate,
            toDate : todate,
            is_ajaxed : is_ajaxed,
            perPages : perPages,
            page : page
         },
         type: 'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_bundle_data').html(res).show();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
            $("input[type='checkbox']").not('.js-switch').uniform();
         }
   });
});

function getBundleData(assign_group = "",join_range = "",added_date = "",fromdate = "",todate=""){
	$('#ajax_loader').show();
	var is_ajaxed = $('#is_ajaxed').val();
	var perPages = $("#perPages").val();
	$.ajax({
	 	url: 'bundle_recommendations_tab.php',
	 	type: 'POST',
	 	data: { 
	 		api_key : 'bundleRecommendationsList',
	 		assign_group : assign_group,
	 		joinRange : join_range,
	 		addedDate : added_date,
	 		fromDate : fromdate,
	 		toDate : todate,
	 		is_ajaxed : is_ajaxed,
	 		perPages : perPages
	 		 },
	 	success: function(res) {
	 		$('#ajax_loader').hide();
	 		$('#ajax_bundle_data').html(res).show();
	 		common_select();
	 	}
		});
}
</script>
<?php } ?>
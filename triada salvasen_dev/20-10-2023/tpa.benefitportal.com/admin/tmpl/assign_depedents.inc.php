<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Assign Dependent</h4>
	</div>
	<div class="panel-body">
		<form name="frm_add_dep" id="frm_add_dep">
			<input type="hidden" name="ws_id" value="<?=$ws_id?>">
		<div class="row theme-form">
			<?php if($dep_res){ ?>
				<?php if($tier_change_required == false){ ?>	
				    <div class="form-group height_auto">
		              <select class="form-control" name="assigned_dependents" id="assign_depedents" >
		              	<option data-hidden="true"></option>
		                <?php foreach ($dep_res as $k => $v) { ?>
		                	<option value="<?=$v['id']?>"><?=$v['name']?></option>
		                <?php } ?>
		              </select>
		              <label>Select Dependent</label>
		              <p class="error"><span id="err_assign_dependents"></span></p>
		            </div>
		        <?php } ?>

	            <?php if($tier_change_required == false){ ?>	
					<div class="form-group">
						<select class="form-control" name="effective_date">
							<option data-hidden="true"></option>
							<?php foreach ($coverage_periods as $coverage) { ?>
								<option value="<?=$coverage['value']?>"><?=$coverage['text']?></option>
							<?php } ?>
						</select>
						<label>Effective Date</label>
					<p class="error"><span id="err_effective_date"></span></p>	
					</div>
				<?php } else { ?>
					<div class="form-group text-center">
						<a href="javascript:void(0);" class="text-action fw500"><i class="fa fa-exclamation-circle"></i>  In order to add dependent(s), you must change plan tier.</a>
						<div class="text-center m-t-10">
							<!-- <a href="javascript:void(0);" class="btn btn-info btn-outline change_benefit">Change Benefit Tier</a> -->
						</div>
					</div>
				<?php } ?>
			<?php }else{ ?>
				<div class="form-group text-center m-b-20">
					<p>No Dependent(s) Found</p>
					<a href="javascript:void(0)" class="btn btn-info btn-outline add_dependets">+ Dependents</a>
				</div>

			<?php } ?>
				
		</div>
		<div class="text-center m-t-20">
			<?php if($tier_change_required == false && !empty($dep_res)){ ?>
				<button class="btn btn-action" type="submit">Save</button>
			<?php } ?>
			<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
		</div>
		</form>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('.change_benefit').on('click',function(){
		parent.$.fn.colorbox.close();
		window.parent.scrollToDiv($('#prd_details_expanded'), 0,'member_products_tab.php','prd_details_expanded');
	});

	$('.add_dependets').on('click',function(){
		parent.$.colorbox({
			href: 'add_depedents.php?id=<?=md5($ws_row['customer_id'])?>&action=Add',
			iframe: true,
			width: '768px',
			height: '450px',
			overlayClose: false,
			escKey:false,
			fastIframe: false,
	    });
	});	

	$('#frm_add_dep').on('submit',function(e){
      e.preventDefault();
      $('#ajax_loader').show();
      var params = $('#frm_add_dep').serialize();
      $.ajax({
        url: 'ajax_assign_dependents.php',
        type: 'POST',
        data: params,
        dataType: 'JSON',
        success: function(res) {
          if(res.status == 'success'){
            $('#is_ajax').val('');
            $('#ajax_loader').hide();
            parent.$.fn.colorbox.close();
            parent.location.reload();
          }else if (res.status == "fail") {
	            $('#ajax_loader').hide();
	            is_error = true;
	            $.each(res.errors, function (index, value) {
	                if (typeof(index) !== "undefined") {
	                    $("#err_" + index).html(value);             
	                } else {
	                    console.log("Not found Element => #err_" + index);
	                }
	            });
	        }
          
        }
      });
  	});
	
});

</script>

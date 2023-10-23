<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">+ Variation</h4>
	</div>
	<div class="panel-body">
		<input type="hidden" name="id" id="id" value="<?=$cart_id?>">
		<div class="row theme-form">
			<div class="col-sm-6">
				<div class="form-group">
					<select class="form-control" id="groupId" <?= !empty($group_id) ? "disabled" : "" ?>>
						<option data-hidden="true"></option>
						<?php if(!empty($group_id)){ ?>
							<option value="<?= $group_id ?>" selected> <?= $group_repId." - ".$group_name ?></option>
						<?php } ?>
					</select>
					<label>Assign Group</label>
					<p class="error" id="error_groupId"></p>
				</div>
			</div>
		</div>
		<hr class="m-t-0" />
		<h4 class="m-t-0">Variation Cart Settings</h4>
		<div class="row theme-form">
			<div class="col-lg-3">
				<p class="m-b-15"><i class="fa fa-check-circle fa-lg" aria-hidden="true"></i> Default Cart</p>
				<div class="bg_light_gray p-10 text-center b-all">
					<h4 class="m-t-20">Cart</h4>
					<p class="m-b-30">Enrollee cost per day period (Bi-Monthly)</p>
					<div class="form-group height_auto">
						<select class="form-control">
							<option>Member Only</option>
						</select>
						<label>Dental Elite 1500</label>
					</div>
					<h4 class="fs16 m-t-0"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> Total</h4>
					<h2>$18.95/<span class="fs14">pay period</span></h2>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="clearfix m-b-10">
					<div class="pull-left">
						<label><input id="pay_calc" type="checkbox" value="<?=$pay_calc?>" <?= $pay_calc == 'Y' ? "checked" : ""; ?>>Take Home Pay Calculator</label>
					</div>
					<div class="pull-right">
						<i class="fa fa-info-circle fa-lg" data-toggle="tooltip" data-container="body" title="Explanation for what the tax calculator is/does…"></i>
					</div>
				</div>
				<div class="p-10 text-center b-all">
					<h4 class="m-t-20 m-b-20">Take Home Pay</h4>
					<div class="home_pay_amt">
						<div class="div_table">
							<div class="table_row">
								<div class="table_cell">
									<div class="bg-success p-15 text-left">
										<h2 class="mn text-white">$3,400.00</h2>
									</div>
								</div>
								<div class="table_cell">
									<div class="bg_light_success p-20">
										<i class="fa fa-eye fs24" aria-hidden="true"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="preview_div" style="display:none" class="clearfix m-t-15 text-center">
					<a href="preview_details.php" class="btn btn-info preview_defaults" data-toggle="tooltip" data-placement="bottom" title="Preview/Set Defaults">Preview/Set Defaults</a>
				</div>
			</div>
			<div id="calc_setting_div" style="display:none" class="col-lg-6">
				<p>Take Home Pay Calculator Settings</p>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
								<div class="pr">
									<input  type="text" class="form-control date_picker" id="effectiveDate" name="effective_date" value="<?=$effective_date?>">
									<label class="label-wrap" style="font-size: 13px;">Effective Date (MM/DD/YYYY)</label>
								</div>
							</div>
							<p class="error" id="error_effectiveDate"></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
								<div class="pr">
									<input  type="text" class="form-control date_picker" id="terminationDate" name="termination_date" value="<?=$termination_date?>">
									<label class="label-wrap" style="font-size: 13px;">Termination Date (MM/DD/YYYY)</label>
								</div>
							</div>
							<p class="error" id="error_terminationDate"></p>
						</div>
					</div>
				</div>
				<div class="row">
                    <div class="col-sm-12">
                        <div class="form-inline">
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">Cart Option </label>
                              <p class="error" id="error_cartType"></p>
                           </div>
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">
                              <input type="radio" name="cart_type" id="cart_type" value="cart_only" <?= $cart_type == "cart_only" ? "checked" : ""; ?>>Cart Only
                              </label>
                           </div>
                           <div class="form-group v-align-top height_auto">
                              <label class="mn">
                              <input type="radio" name="cart_type" id="cart_type" value="both" <?= $cart_type == "both" ? "checked" : ""; ?>>Both
                              </label>
                           </div>
                        </div>
                    </div>
                 </div>
				<div class="text-center">
					<a href="javascript:void(0);" id="save_btn" class="btn btn-action">Save</a>
					<a href="javascript:void(0);" id="cancel_btn" class="btn red-link">Cancel</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){ 
	  	$(".date_picker").datepicker({
	    	changeDay: true,
	    	changeMonth: true,
	    	changeYear: true
	  	});

	  	getGroupData();

	  	if($("#pay_calc").val() == 'Y'){
	  		$("#preview_div").show();
	    	$("#calc_setting_div").show();
	  	}else{
	  		$("#preview_div").hide();
	    	$("#calc_setting_div").hide();
	  	}
	  	
	});

	$("#cancel_btn").off('click');
	$("#cancel_btn").on('click',function(){
		$("#preview_div").hide();
	   	$("#calc_setting_div").hide();
	   	$("#pay_calc").prop('checked',false);
	   	$(".checker").find('span').removeClass('checked');
	});

   	$("#save_btn").off("click");
   	$("#save_btn").on("click",function(){
      	var effectiveDate = $("#effectiveDate").val();
      	var terminationDate = $("#terminationDate").val();
      	var groupId = $("#groupId").val();
      	var type = "Variation";
      	var id = $("#id").val();
      	var payCalc = 'Y';
      	var cartType = $("input[name='cart_type']:checked").val();
      	$('#ajax_loader').show();
      	$(".error").html("");
      	$.ajax({
         	url :'<?=$HOST?>/ajax_api_call.php' ,
         	type : 'POST',
         	data : {
                  effectiveDate : effectiveDate,
                  terminationDate : terminationDate,
                  id : id,
                  type : type,
                  groupId : groupId,
                  payCalc : payCalc,
                  cartType : cartType,
                  api_key : 'cartSetting',
                  },
         	dataType : 'json',
         	success: function(res){
            	$('#ajax_loader').hide();
            	if(res.status=="Success"){
               		setNotifySuccess(res.message);
               		window.location = "manage_groups.php";
               	}else if(res.status=="fail"){
               		setNotifyError(res.message);
               		window.location.reload();
            	}else{
               		var is_error = true;
	               	$.each(res.data, function (index, value) {
	                	$('#error_' + index).html(value).show();
	                	if(is_error){
	                    	var offset = $('#error_' + index).offset();
	                    	var offsetTop = offset.top;
	                    	var totalScroll = offsetTop - 150;
	                    	$('body,html').animate({scrollTop: totalScroll}, 1200);
	                    	is_error = false;
	                	}
	               	});
            	}
         	}
      	});
  	});

  	$(document).off('click', '.preview_defaults');
	$(document).on('click', '.preview_defaults', function (e) {
	    e.preventDefault();
	    $.colorbox({
	      	href: $(this).attr('href'),
	      	iframe: true, 
	      	width: '910px', 
	      	height: '600px',
	      	closeButton: false
	    });
	});

  	$("#pay_calc").off("change");
	$("#pay_calc").on("change",function(){
	    $("#preview_div").hide();
	    $("#calc_setting_div").hide();
	    if(this.checked){
	        $("#preview_div").show();
	        $("#calc_setting_div").show();
	     }
	});

	function getGroupData(){
		$('#ajax_loader').show();
		$.ajax({
         	url :'<?=$HOST?>/ajax_api_call.php' ,
         	type : 'POST',
         	data : {api_key : 'getCartSettingGroups'},
         	dataType : 'json',
         	success: function(res){
         		$('#ajax_loader').hide();
         		if(res.status == "Success"){
	         		var len = res.data.length;
	         		for(var i=0;i<len;i++){
	         			var id = res.data[i]['id'];
	         			var rep_id = res.data[i]['rep_id'];
	         			var name = res.data[i]['business_name'];

	         			$("#groupId").append("<option value='"+id+"'> "+rep_id+" - "+name+"</option>");
	         		}
	         		$("#groupId").selectpicker('refresh');
	         	}
         	}

		});
	}

</script>
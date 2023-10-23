<script src="https://maps.googleapis.com/maps/api/js?key=<?=$GOOGLE_MAP_KEY?>&libraries=places" async defer></script>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Company</h4>
		</div>
	</div>
	<div class="panel-body">
		<form id="company_form" name="company_form" >
			<input type="hidden" name="is_valid_address" id="is_valid_address" value="<?=!empty($address)?'Y':''?>">
			<input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>">
			<input type="hidden" name="company_id" id="company_id" value="<?= $company_id ?>">
			<input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
			<p class="fs16 fw500 m-b-20">Company Information</p>
			<div class="theme-form">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="name" class="form-control" value="<?= !empty($name) ? $name : '' ?>">
							<label>Location/Company<em>*</em></label>
							<p class="error" id="error_name"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="ein" class="form-control ein_mask" value="<?= !empty($ein) ? $ein : '' ?>">
							<label>EIN/FEIN<em>*</em></label>
							<p class="error" id="error_ein"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="location" class="form-control" value="<?= !empty($location) ? $location : '' ?>">
							<label>Location Code<em>*</em></label>
							<p class="error" id="error_location"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="address" id="address" class="form-control" value="<?= !empty($address) ? $address : '' ?>" placeholder="">
							<label>Address<em>*</em></label>
							<p class="error" id="error_address"></p>
							<input type="hidden" name="old_address" id="old_address" value="<?= !empty($address) ? $address : '' ?>">
						</div> 
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" class="form-control" name="address_2" id="address_2" value="<?= !empty($address_2) ? $address_2 : '' ?>" onkeypress="return block_special_char(event)" />
                			<label>Address 2 (suite, apt)</label>
							<p class="error" id="error_address_2"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="city" id="city" class="form-control" value="<?= !empty($city) ? $city : '' ?>">
							<label>City<em>*</em></label>
							<p class="error" id="error_city"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<select class="form-control" name="state" id="state">
								<option data-hidden="true"></option>
								<?php foreach ($allStateRes as $key => $value) { ?>
				                  <option value="<?= $value['id'] ?>" <?= !empty($state) && $state == $value['id'] ? "selected" : '' ?>><?= $value['name'] ?></option>
				               <?php } ?>
							</select>
							<label>State<em>*</em></label>
							<p class="error" id="error_state"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="zip" id="zip" class="form-control" value="<?= !empty($zip) ? $zip : '' ?>">
							<label>Zip Code<em>*</em></label>
							<p class="error" id="error_zip"></p>
							<input type="hidden" name="old_zip" id="old_zip" value="<?= !empty($zip) ? $zip : '' ?>">
						</div>
					</div>
				</div>
				<p class="fs16 fw500 m-b-20">Contact</p>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="contact" id="contact" class="form-control" value="<?= !empty($contact) ? $contact : '' ?>">
							<label>Contact Name<em>*</em></label>
							<p class="error" id="error_contact"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="phone" class="form-control phone_mask" value="<?= !empty($phone) ? $phone : '' ?>">
							<label>Contact Phone<em>*</em></label>
							<p class="error" id="error_phone"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="email" class="form-control no_space" value="<?= !empty($email) ? $email : '' ?>">
							<label>Contact Email<em>*</em></label>
							<p class="error" id="error_email"></p>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<input type="text" name="title" class="form-control" value="<?= !empty($title) ? $title : '' ?>">
							<label>Title<em>*</em></label>
							<p class="error" id="error_title"></p>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix text-center">
				<button type="button" class="btn btn-action" id="save_comapny">Save</button>
				<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
		</div>
		</form>
	</div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>

<script type="text/javascript">
	$site_location = '<?= $SITE_ENV ?>';
	$(document).ready(function() {
		checkEmail();
		$(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
      $(".ein_mask").inputmask({"mask": "99-9999999",'showMaskOnHover': false});
		
	});

	$(document).off("click","#save_comapny");
	$(document).on("click","#save_comapny",function(){
		$is_address_ajaxed = $('#is_address_ajaxed').val();
		if($is_address_ajaxed == 1){
			updateAddress();
		}else{
			ajaxSaveAccountDetails();	
		}
	});

	
	function ajaxSaveAccountDetails(){
		$('.error').html('');
		$('.form-group').removeClass('has-error');
		disableButton($(this));
		$.ajax({
			url:'ajax_add_group_compamy.php',
			data:$("#company_form").serialize(),
			dataType:'JSON',
			type:'POST',
			success:function(res){
				enableButton($("#save_comapny"));
				if(res.status=="success"){
					window.parent.$.colorbox.close();
				}else if(res.status=="fail"){
					var is_error = true;
                  	$.each(res.errors, function (index, value) {
                    	$('#error_' + index).closest('.form-group').addClass('has-error');
                      	$('#error_' + index).html(value).show();
                      	if(is_error){
                         	var offset = $('#error_' + index).offset();
                         	var offsetTop = offset.top;
                         	var totalScroll = offsetTop - 50;
                         	$('body,html').animate({scrollTop: totalScroll}, 1200);
                          	is_error = false;
                      	}
                  	});
				}
			}
		});

	}

	function updateAddress(){
		$.ajax({
	      url : "ajax_add_group_compamy.php",
	      type : 'POST',
	      data:$("#company_form").serialize(),
	      dataType:'json',
	      beforeSend :function(e){
	         $("#ajax_loader").show();
	         $(".error").html('');
	      },success(res){
	      	 enableButton($("#save_comapny"));
	         $("#is_address_ajaxed").val("");
	         $("#ajax_loader").hide();
	         $(".suggested_address_box").uniform();
	         if(res.zip_response_status =="success"){
	            $("#state").val(res.state).addClass('has-value');
	            $("#city").val(res.city).addClass('has-value');
	            $("#is_address_verified").val('N');
	            ajaxSaveAccountDetails();
	         }else if(res.address_response_status =="success"){
	            $(".suggestedAddressEnteredName").html($("#contact").val());
	            $("#state").val(res.state).addClass('has-value');
	            $("#city").val(res.city).addClass('has-value');
	            $(".suggestedAddressEntered").html(res.enteredAddress);
	            $(".suggestedAddressAPI").html(res.suggestedAddress);
	            $("#is_valid_address").val('Y');
	            $.colorbox({
	                  inline:true,
	                  href:'#suggestedAddressPopup',
	                  height:'500px',
	                  width:'650px',
	                  escKey:false, 
	                  overlayClose:false,
	                  closeButton:false,
	                  onClosed:function(){
	                     $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();
	                     
	                     if($suggestedAddressRadio=="Suggested"){
	                        $("#address").val(res.address).addClass('has-value');
	                        $("#address_2").val(res.address2).addClass('has-value');
	                        $("#is_address_verified").val('Y');
	                     }else{
	                        $("#is_address_verified").val('N');
	                     }
	                     ajaxSaveAccountDetails();
	                  },
	            });
	         }else if(res.status == 'success'){
	            $("#is_address_verified").val('N');
	            ajaxSaveAccountDetails();
	         }else{
	            $.each(res.errors,function(index,error){
	               $("#error_"+index).html(error).show();
	           });
	         }
	         $("#state").selectpicker('refresh');
	      }
	   });
	}

	$(document).on("click","#cancel",function(){
		window.parent.$.colorbox.close();
	});

	$(document).on('focus','#address,#zip',function(){
	 	$('#is_address_ajaxed').val(1);
	});

</script>
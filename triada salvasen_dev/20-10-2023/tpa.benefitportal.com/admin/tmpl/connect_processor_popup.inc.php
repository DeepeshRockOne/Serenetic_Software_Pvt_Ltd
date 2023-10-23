<div class="panel panel-default panel-block Font_Roboto connect_processor" id="test_processor_div">
	<div class="panel-heading"><h4 class="panel-title fs16">Test Processor   - <span class="fw300" class="processor_name_title_id"><?=$payment_master_res['name']?></span></h4></div>
	<div class="panel-body sform theme-form">
		<form method="POST" name="frm_category" class="form_wrap " id="frm_category" enctype="multipart/form-data">
			<input type="hidden" name="pay_id" id="pay_id" value="<?=$payment_master_id?>">
			<div class="row theme-form">
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" name="payment_mode" id="payment_mode">
							<option data-hidden="true"></option>
							<option value="CC">CC</option>
							<option value="ACH">ACH</option>
						</select>
						<label>Payment Method</label>
						<p class="error error_payment_mode"></p>
					</div>
				</div>
			</div>
		<div class="row billing_info" id="card_div">
			<h4>Credit Card Details</h4>
	      <div class="col-xs-6">
	        <div class="form-group">
	          <input type="text" class="form-control" name="fname_on_card">
			  <label>First Name</label>
	          <span class="error error_preview" id="error_fname_on_card"></span>
	        </div>
	      </div>
	      <div class="col-xs-6">
	        <div class="form-group">
	          <input type="text" class="form-control" name="lname_on_card">
			  <label>Last Name</label>
	          <span class="error error_preview" id="error_lname_on_card"></span>
	        </div>
	      </div>
	      <div class="col-xs-12">
	        <div class="form-group">
	          <select class="form-control" name="card_type">
	           <option value="" disabled hidden selected></option>
				<?php if(empty($payment_master_res['acceptable_cc'])){?>
					<option value="Amex">AMERICAN EXPRESS</option>
					<option value="Discover">DISCOVER</option>
					<option value="MasterCard">MASTERCARD</option>
					<option value="Visa">VISA</option>
				<?php }else{ 
						$payment_card_type = explode(',',$payment_master_res['acceptable_cc']);
					?>
					<?php foreach($payment_card_type as $ctype){  ?>
						<option value="<?=$ctype?>"><?= ucwords(str_replace('_',' ',$ctype))?></option>
					<?php } ?>
				<?php } ?>
	          </select>
			  <label>Select Card Type</label>
	          <span class="error error_preview" id="error_card_type"></span>
	        </div>
	      </div>
	      <div class="col-sm-6 col-xs-12">
	        <div class="form-group">
	          <input type="text" class="form-control" name="card_number" oninput="isValidNumber(this)" maxlength="16">
			  <label>Card Number</label>
	          <span class="error error_preview" id="error_card_number"></span>
	        </div>
	      </div>
	      <div class="col-sm-6 col-xs-12">
	        <div class="row">
	          <div class="col-sm-6 col-xs-12">
	            <div class="form-group">
	              <select class="form-control" name="bill_month">
	                  <option value="" selected hidden disabled></option>
	                  <?php foreach($month_names as $key => $name) { ?>
	                  <option value="<?=$key+1?>"><?=$name?></option>
	                  <?php } ?>
	              </select>
				  <label>Expiration Month</label>
	              <span class="error error_preview" id="error_bill_month"></span>
	            </div>
	          </div>
	          <div class="col-sm-6 col-xs-12">
	            <div class="form-group">
	              <select class="form-control" name="bill_year">
				  <option value="" selected hidden disabled></option> 
	                <?php for ($i = date('Y'); $i <= date('Y') + 15; $i++) { ?>
	                  <option value="<?= $i ?>"><?= $i ?></option>
	                <?php } ?>
	              </select>
				  <label>Expiration Year</label>
	              <span class="error error_preview" id="error_bill_year"></span>
	            </div>
	          </div>
	        </div>
	      </div>
	      <?php if(checkIsset($payment_master_res["require_cvv"]) == "Y"){ ?>
	       <div class="col-sm-6 col-xs-12">
	        <div class="form-group">
	          <input type="text" class="form-control" name="cvv_no" maxlength="4" oninput="isValidNumber(this)" value="<?=checkIsset($cvv_no)?>">
			  <label>CVV Number</label>
	          <span class="error error_preview" id="error_cvv_no"></span>
	        </div>
	      </div>
	  	  <?php } ?>
		</div>
		<div id="bank_draft_div" class=" billing_info row">
			<h4>Bank Details</h4>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="ach_name" value="" class="form-control">
					<label>Full Name<i class="text-red">*</i></label>
					<span class="error error_ach_name"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="bank_name" value="" class="form-control">
					<label>Bank Name<i class="text-red">*</i></label>
					<span class="error error_bank_name"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<select class="form-control" name="account_type" id="account_type">
						<option data-hidden="true"></option>
						<option value="checking">Checking</option>
						<option value="savings">Saving</option>
					</select>
					<label>Account Type<i class="text-red">*</i></label>
					<span class="error error_account_type"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="routing_number" id="routing_number" class="form-control" oninput="isValidNumber(this)" maxlength='9'>
					<label>Routing Number<i class="text-red">*</i></label>
					<span class="error error_routing_number"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="account_number" id="account_number" class="form-control" oninput="isValidNumber(this)" maxlength="17">
					<label>Account Number<i class="text-red">*</i></label>
					<span class="error error_account_number"></span>
				</div>
			</div>
		</div>
		<div class="row" id="addressRow" style="display:none">
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="address1" id="address1" class="form-control">
					<label>Address 1</label>
					<span class="error error_address1"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="address2" id="address2" class="form-control">
					<label>Address 2<i class="text-red"></i></label>
					<span class="error error_address2"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="city" id="city" class="form-control">
					<label>City</label>
					<span class="error error_city"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<select name="state" id="state"  class="form-control selected_license_states"  >
						<option value=""></option>
						<?php if ($allStateRes) {?>
							<?php foreach ($allStateRes as $state) { ?>
								<option value="<?=$state["short_name"];?>"><?php echo $state['name']; ?></option>
							<?php }?>
						<?php }?>
					</select>
					<label>State</label>
					<span class="error error_state"></span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<input type="text" name="zip" id="zip" oninput="isValidNumber(this)" maxlength="5" class="form-control">
					<label>Zip Code</label>
					<span class="error error_zip"></span>
				</div>
			</div>
		</div>
		<div class="row processor_btn">
		<div class="col-xs-12 m-t-15">
	        <div class="form-group height_auto">
	          <label>Amount <span class="text-gray">(this is a real transaction that will go through to test this Merchant Processor)</span></label>
	          <div class="input-group">
	            <span class="input-group-addon">$</span>
	            <input type="text" name="card_amount" class="form-control priceControl" placeholder="10.00">
	          </div>
	          <div class="clearfix"></div>
	          <span class="error error_preview" id="error_card_amount"></span>
	        </div>
		  </div>
	      <div class="col-xs-12 m-t-15 text-center">
	        <a href="javascript:void(0);" class="btn btn-info" id="submit_test_processor">Test Processor</a>
			<a href="javascript:void(0);" class="btn text-red" onclick="parent.$.colorbox.close()">Cancel</a>
		  </div>
		</div>
	  </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".billing_info").hide();
	$(".processor_btn").hide();
	$('#frm_category').ajaxForm({
		beforeSend: function () {
			$("#ajax_loader").show();
			$(".error").html('').hide();
		},
		dataType: 'json',
		success: function (res) {
    	if (res.status == "fail") {
      	$('#ajax_loader').hide();
    		var is_error = true;
        $.each(res.errors, function (index, error) {
          $('#error_' + index).html(error).show();
		  $('.error_' + index).html(error).show();
          if (is_error) {
            is_error = false;
          }
      	});
      } else {
        setTimeout(function() {
        	$('#ajax_loader').hide();
        	if(res.payment_status == 'Success'){
	        	window.parent.$.colorbox({
				      iframe: true,
				      width: '768px',
				      href: 'success_processor_transaction.php?pay_id='+$("#pay_id").val()+'&is_success=Y',
				      height: '450px'
				    });
        	} else {
        		window.parent.$.colorbox({
				      iframe: true,
				      width: '768px',
				      href: 'success_processor_transaction.php?pay_id='+$("#pay_id").val()+'&is_success=N&errorMessage='+res.payment_error,
				      height: '450px'
				    });
        	}
        },2000);
      }
    },
	    error: function () {
	      alert('Due to some technical error file couldn\'t uploaded.');
	    }
	});
	$(document).on('click', '#submit_test_processor', function () {
		$('#frm_category').submit();
  	});
});

$(document).off('change','#payment_mode');
$(document).on('change','#payment_mode',function(e){
	$(".billing_info").hide();
	$(".processor_btn").show();
	if($(this).val() == 'ACH'){
		$('#bank_draft_div').show();
	}else{
		$('#card_div').show();
	}
	$("#addressRow").show();
});


  $('.priceControl').priceFormat({
    prefix: '',
    suffix: '',
    centsSeparator: '.',
    thousandsSeparator: '',
    limit: false,
    centsLimit: 2,
  });
</script>
<style type="text/css">
.form-inline .form-group{height: initial;}
.d-flex { display:flex}
.merchant_line_label{ margin-top:8px; margin-right:10px;}
.merchant_line_label:after { content:""; position:absolute; right:; top:16px; width:100%; border-bottom:1px solid #5d5d5d;}
.merchant_line_label label { white-space:normal;}
@media (max-width:767px){
.merchant_line_label { margin-top:0; margin-bottom:8px;} 
.d-flex { display:block; }
.d-flex > div { margin-left:35px;}
.d-flex > div:first-child { margin-left:0px;}
}
</style>
<div class="panel panel-default  panel-space">
<form action="ajax_create_merchant_processor.php" class="theme-form" role="form" method="post" name="form_submit" id="form_submit" enctype="multipart/form-data" novalidate>
	<input type="hidden" name="btn_clicked" id="btn_clicked" value="">
	<input type="hidden" name="payment_master_id" id="payment_master_id" value="<?=!empty($payment_master_id) ?$payment_master_id : '' ?>">
	<input type="hidden" name="type" id="type" value="<?=!empty($type) ? $type : '' ?>">
	<input type="hidden" name="gateway_name" id="gateway_name" value="<?=!empty($gateway_name) ? $gateway_name : '' ?>">

	<?php if(!empty($variation_product_id_arr) && count($variation_product_id_arr) > 0) {
			foreach ($variation_product_id_arr as $key => $value) { ?>
			<input type="hidden" name="variation_product_id_<?=$key?>" class="variation_product_ids" value='<?=!empty($value) ? $value : '' ?>'>
	<?php } } ?>
	
	<?php  if(!empty($agent_downline_id_arr) && count($agent_downline_id_arr) > 0) {
			foreach ($agent_downline_id_arr as $key => $value) { ?>
			<input type="hidden" name="agent_downline_id_<?=$key?>" class="agent_downline_ids" value='<?=!empty($value) ? $value : '' ?>'>
	<?php } } ?>
	
	<?php if(!empty($agents_loa_id_arr) && count($agents_loa_id_arr) > 0) {
			foreach ($agents_loa_id_arr as $key => $value) { ?>
			<input type="hidden" name="agent_loa_id_<?=$key?>" class="agent_loa_ids" value='<?=!empty($value) ? $value : '' ?>'>
	<?php } } ?>
		<div class="panel-heading">
			<div class="panel-title">
				<p class="fs16 mn"><strong class="fw500">Merchant Details</strong></p>
			</div>
		</div>
		<div class="panel-body">
			<input name="user" type="text" style="display:none"/>
			<input name="pass" type="password" autocomplete="new-password" style="display:none"/>

	    <div class="row ">
	      <div class="col-md-3 col-sm-6">
	        <div class="form-group">
	          <input type="text" name="processor_name" id="processor_name" class="form-control tblur" value="<?=!empty($processor_name) ? $processor_name : ''?>">
              <label>Merchant Processor Name<em>*</em></label>
	          <span class="error error_preview" id="error_processor_name"></span>
	        </div>
	      </div>
	      <div class="col-md-3 col-sm-6">
	        <div class="form-group">
	          <input type="text" name="merchant_id" class="form-control tblur" value="<?=!empty($merchant_id) ? $merchant_id : ''?>">
              <label>Merchant ID<em>*</em></label>
	          <span class="error error_preview" id="error_merchant_id"></span>
	        </div>
	      </div>

	      <div class="col-md-3 col-sm-6">
	        <div class="form-group">	          
	          <select class="form-control tblur" name="gateway_id" id="gateway_id">
	            <option value=""></option>
	            <?php if(count($gateway_details_res) > 0){ 
	            	foreach ($gateway_details_res as $value) { ?>
	            		<option value="<?=$value['id']?>" data-name="<?=$value['gateway_name']?>" data-url="<?=$value['gateway_url']?>" <?=(!empty($gateway_id) && $gateway_id == $value['id']) ? 'selected="selected"' : ''?>><?=$value['gateway_name']?></option>
	            	<?php } 
	        	}?>
	          </select>
              <label>Gateway Name<em>*</em></label>
	          <span class="error error_preview" id="error_gateway_name"></span>
	        </div>
	      </div>

	      <div class="col-md-3 col-sm-6 api_key_div" style="display:<?=($gateway_id == 1 || $gateway_id == 0 || $gateway_id == 5) ? 'none' : 'block'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="api_key" id="api_key" class="form-control tblur" value="<?=!empty($api_key) ? $api_key : '' ?>">
			  			<label>API Key<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('api_popup','api_key')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		<span class="error error_preview" id="error_api_key"></span>
	      		<div style="display:none" id="api_popup" class="password_popup_content">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_api"></div>
							<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('api_key','showing_api','api_popup');">Unlock</button></div>
						</div>
					</div>
	      	</div>
	      </div>

	      <div class="col-md-3 col-sm-6 service_key_div" style="display:<?=($gateway_id == 5) ? 'block' : 'none'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="service_key" id="service_key" class="form-control tblur" value="<?=!empty($service_key) ? $service_key : '' ?>">
			  			<label>Service Key<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('service_key_popup','service_key')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		<span class="error error_preview" id="error_service_key"></span>
	      		<div style="display:none" id="service_key_popup" class="password_popup_content">
					<div class="phone-control-wrap">
						<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_service_key"></div>
						<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('service_key','showing_service_key','service_key_popup');">Unlock</button></div>
					</div>
				</div>
	      	</div>
	      </div>

	      <div class="col-md-3 col-sm-6 transaction_key_div" style="display:<?=($gateway_id == 1) ? 'block' : 'none'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="transaction_key" id="transaction_key" class="form-control tblur" value="<?=!empty($transaction_key) ? $transaction_key : '' ?>">
			  			<label>Transaction Key<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('transaction_popup','transaction_key')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		<span class="error error_preview" id="error_transaction_key"></span>
	      		<div style="display:none" id="transaction_popup" class="password_popup_content">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_transaction"></div>
							<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('transaction_key','showing_transaction','transaction_popup');">Unlock</button></div>
						</div>
					</div>
	      	</div>
	      </div>
		  
	      <div class="col-md-3 col-sm-6 user_name_div" id="user_name_div" style="display:<?=($gateway_id == 4 || $gateway_id == 2 || $gateway_id == 5) ? 'block' : 'none'?>">
	        <div class="form-group">
	          <input type="text" name="user_name" class="form-control tblur" value="<?=!empty($user_name) ? $user_name : '' ?>">
              <label>User Name<em>*</em></label>
	          <span class="error error_preview" id="error_user_name"></span>
	        </div>
	      </div>

	      <div class="col-md-3 col-sm-6 password_div" id="password_div" style="display:<?=($gateway_id == 2 || $gateway_id == 3 || $gateway_id == 5) ? 'block' : 'none'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="password" id="password" class="form-control tblur" value="<?=!empty($password) ? $password : '' ?>">
			  			<label>Password<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('password_popup','password')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		<span class="error error_preview" id="error_password"></span>
	      			<div style="display:none" id="password_popup" class="password_popup_content">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_pass"></div>
							<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('password','showing_pass','password_popup');">Unlock</button></div>
						</div>
					</div>
	      	</div>
	      </div>

	      <div class="col-md-3 col-sm-6 login_id_div" id="login_id_div" style="display:<?=($gateway_id == 1) ? 'block' : 'none'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="login_id" id="login_id" class="form-control tblur" value="<?=!empty($login_id) ? $login_id : '' ?>">
			  			<label>Login ID<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('login_popup','login_id')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		 <span class="error error_preview" id="error_login_id"></span>
	      		 	<div style="display:none" id="login_popup" class="password_popup_content">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_login_id"></div>
							<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('login_id','showing_login_id','login_popup');">Unlock</button></div>
						</div>
					</div>
	      	</div>
	      </div>

	      <div class="col-md-3 col-sm-6 api_pin_div" id="api_pin_div" style="display:<?=($gateway_id == 4) ? 'block' : 'none'?>">
	      	<div class="phone-control-wrap password_unlock">
	      		<div class="phone-addon">
	      			<div class="form-group">
	      				<input type="password" name="api_pin" id="api_pin" class="form-control tblur" value="<?=!empty($api_pin) ? $api_pin : '' ?>">
			  			<label>API Pin<em>*</em></label>
	      			</div>
	      		</div>
	      		<div class="phone-addon w-30 v-align-top">
	      			<a href="javascript:void(0);" onclick="showHiddenPopup('api_pin_popup','api_pin')"><i class="fa fa-eye fs18 p-t-7"></i></a>
	      		</div>
	      		<span class="error error_preview" id="error_api_pin"></span>
						<div style="display:none" id="api_pin_popup" class="password_popup_content">
							<div class="phone-control-wrap">
								<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_api_pin"></div>
								<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('api_pin','showing_api_pin','api_pin_popup');">Unlock</button></div>
							</div>
						</div>
	      	</div>
	      </div>

	      <div class="col-md-3 col-sm-6 secret_key_div" id="secret_key_div" style="display:<?=($gateway_id == 6) ? 'block' : 'none'?>">
	      		<div class="phone-control-wrap password_unlock">
		      		<div class="phone-addon">
		      			<div class="form-group">
		      				<input type="password" name="secret_key" id="secret_key" class="form-control tblur" value="<?=!empty($secret_key) ? $secret_key : '' ?>">
				  			<label>Secret Key<em>*</em></label>
		      			</div>
		      		</div>
		      		<div class="phone-addon w-30 v-align-top">
		      			<a href="javascript:void(0);" onclick="showHiddenPopup('secret_key_popup','secret_key')"><i class="fa fa-eye fs18 p-t-7"></i></a>
		      		</div>
		      		<span class="error error_preview" id="error_secret_key"></span>
					<div style="display:none" id="secret_key_popup" class="password_popup_content">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_secret_key"></div>
							<div class="phone-addon w-65"><button type="button" class="btn btn-info" onclick="showHiddenKey('secret_key','showing_secret_key','secret_key_popup');">Unlock</button></div>
						</div>
					</div>
	      		</div>
	      </div>

	      <div class="col-md-3 col-sm-6 url_div" id="url_div">
	        <div class="form-group">
	          <select class="form-control tblur" name="url" id="url">
	            <option value="" selected  disabled hidden></option>
				<?php
				 if(!empty($uniq_url_arr) && count($uniq_url_arr) > 0) {
	            	foreach ($uniq_url_arr as $key => $value) { ?>
	            		<option value="<?=$value['url']?>" data-id="<?=$value['url']?>" <?=(!empty($live_url) && $live_url == $value['url']) ? 'selected="selected"' : ''?>><?=$value['url']?></option>
				<?php } }
					foreach ($urlOptionaArr as $key => $value) {?>
						<option value="<?=$value?>" style="display:none" data-custom="true" data-id="<?=$key?>"><?=$value?></option>
				<?php } ?>
	          	<option value="new_url" data-id="0">Add new url</option>
	          </select>
              <label>URL<em>*</em></label>
	          <span class="error error_preview" id="error_url"></span>
	        </div>
	      </div>
	      <div class="col-md-3 col-sm-6" id="new_url_div" style="display: none;">
	        <div class="form-group">
	          <input type="text" name="new_url" class="form-control tblur">
            <label>New URL<em>*</em></label>
	        	<span class="error error_preview" id="error_new_url"></span>
	        </div>
	      </div>
	      <div class="clearfix"></div>
	      
	      <div class="col-md-3 col-sm-6">
	        <div class="form-group">
	          <input type="text" name="monthly_threshold_sale" id="monthly_threshold_sale" class="form-control tblur" value="<?=!empty($monthly_threshold_sale) ? $monthly_threshold_sale : '' ?>">
              <label>Monthly Threshold for Sales<em>*</em></label>
	          <span class="error error_preview" id="error_monthly_threshold_sale"></span>
	        </div>
	      </div>
	      <div class="col-md-9 col-sm-6">
	        <div class="form-group">
	          <input name="description" type="text" class="form-control tblur" value="<?=!empty($description) ? $description : '' ?>">
              <label>Description</label>
	          <span class="error error_preview" id="error_description"></span>
	        </div>
	      </div>
	  	</div>
		<p class="fs16 m-t-10 fw500 m-b-25">Settings</p>
        <div class="d-flex m-b-25">
        	<div class="merchant_line_label"  style="min-width:220px;">
				<label><input type="checkbox" name="accept_ach_value" value="<?=(!empty($accept_ach_value) && $accept_ach_value == 'Y') ? 'Yes' : 'No' ?>" id="accept_ach_value" <?=$accept_ach_value=='Y' ? 'checked' : ''?>>
				<span class="m-l-5">Accept ACH </span>
				</label>
			</div>
            <div class="merchant_line_label" id="accept_ach_div" style="<?=(($type != 'Variation') && !empty($accept_ach_value) && $accept_ach_value == 'Y') ? '' : 'display: none;' ?>">
				<div class="d-inline">
					<label class="label-input"><input type="checkbox" name="is_accept_ach_default" type="checkbox" <?=(($type != 'Variation') && !empty($accept_ach_value) && ($accept_ach_value == 'Y') && ($is_accept_ach_default == 'Y')) ? 'checked' : '' ?>>
					<span class="m-l-5">Make this Merchant Processor the new default processor for ACH</span>
					</label>
				</div>
			</div>
        </div> 
        <div class="d-flex m-b-25">
        	<div class="merchant_line_label"  style="min-width:220px;">
				<label><input type="checkbox" name="accept_cc_value" value="<?=(!empty($accept_cc_value) && $accept_cc_value == 'Y') ? 'Yes' : 'No' ?>" id="accept_cc_value" <?=$accept_cc_value=='Y' ? 'checked' : ''?>>
				<span class="m-l-5">Accept Credit/Debit</span>
				</label>
			</div> 
            <div style="flex-grow:1;<?=(($type != 'Variation') && !empty($accept_cc_value) && $accept_cc_value == 'Y') ? '' : 'display: none;' ?>" id="accept_cc_div"> 
                <div class="merchant_line_label">
                    <div class="d-inline">
						<label class="label-input"><input name="is_accept_cc_default" type="checkbox" <?=(($type != 'Variation') && !empty($accept_cc_value) && ($accept_cc_value == 'Y') && ($payment_master_res['is_default_for_cc'] == 'Y')) ? 'checked' : '' ?>>
	                    <span class="m-l-5">Make this Merchant Processor the new default processor for CC </span>
	                    </label>
	                </div>
                </div>
            </div>
             <div>
					<div class="form-inline" id="acceptable_cc_div" style="<?=$accept_cc_value=='Y' ? ' min-width:280px;' : 'display:none'?> <?=(($type != 'Variation') && !empty($accept_cc_value) && $accept_cc_value == 'Y') ? 'min-width:180px;max-width:305px' : '' ?>"> 
						<div class="form-group"  style="min-width:180px;<?=(($type != 'Variation') && !empty($accept_cc_value) && $accept_cc_value == 'Y') ? 'max-width:180px' : '' ?> " >
							<select class="se_multiple_select" name="acceptable_cc[]"  id="acceptable_cc" multiple="multiple" >
								<option value="Amex" <?=in_array('Amex',$acceptable_cc_arr) ? 'selected' : ''?>>AMERICAN EXPRESS</option>
								<option value="Discover" <?=in_array('Discover',$acceptable_cc_arr) ? 'selected' : ''?>>DISCOVER</option>
								<option value="MasterCard" <?=in_array('MasterCard',$acceptable_cc_arr) ? 'selected' : ''?>>MASTERCARD</option>
								<option value="Visa" <?=in_array('Visa',$acceptable_cc_arr) ? 'selected' : ''?>>VISA</option>
								<!-- <option value="chase" <?=in_array('chase',$acceptable_cc_arr) ? 'selected' : ''?>>CHASE</option> -->
								<!-- <option value="citi_bank" <?=in_array('citi_bank',$acceptable_cc_arr) ? 'selected' : ''?>>CITIBANK</option>
								<option value="capital_one" <?=in_array('capital_one',$acceptable_cc_arr) ? 'selected' : ''?>>CAPITAL ONE</option> -->
							</select>
							<label>Select Acceptable CC</label>                            
							<span class="error error_acceptable_cc" id="error_acceptable_cc"></span>
						</div>  
                        <label class="m-l-5"><input type="checkbox" name="require_cvv" id="require_cvv" <?=!empty($payment_master_res['require_cvv']) && $payment_master_res['require_cvv'] == 'Y' ? 'checked' : ''  ?> > Require CVV?  </label> 
					</div>
				</div>
        </div>
        <div class="d-flex m-b-25">
        	<div class="merchant_line_label"  style="min-width:220px;">
				<label>
				<input type="checkbox" name="sale_threshold"  value="<?=(!empty($payment_master_res['is_sales_threshold']) && $payment_master_res['is_sales_threshold'] == 'Y') ? 'Yes' : 'No' ?>" id="sale_threshold" <?=(!empty($payment_master_res['is_sales_threshold']) && $payment_master_res['is_sales_threshold'] == 'Y') ? 'checked' : ''?> />
				<span class="m-l-5">Sales Threshold Alert</span>
				</label>
			</div>
            <div id="sale_threshold_alert_div" style="<?=(!empty($payment_master_res['is_sales_threshold']) && $payment_master_res['is_sales_threshold'] == 'Y') ? '' : 'display: none;' ?>">
			<div class="form-inline">
					<div class="form-group  mw-125">
						<input type="number" name="sale_threshold_alert" value="<?=!empty($payment_master_res) && !empty($payment_master_res['sales_threshold_value']) && $payment_master_res['sales_threshold_value'] > 0 ? $payment_master_res['sales_threshold_value'] : '' ?>" min="1" max="99" onKeyUp="if(this.value>99){this.value='99';}else if(this.value<0){this.value='0';}" class="form-control mw-125">
						<label>Percentage</label>
						<div class="clearfix"></div>
						<p class="error error_preview" id="error_sale_threshold_alert"></p>
					</div>
				<div class="form-group "> 
						<label class="m-l-5">
							Alert when sales reach this monthly %
						</label> 
				</div>
			</div>
			</div>
        </div>
        <div class="d-flex m-b-25">
        	<div class="merchant_line_label"  style="min-width:220px;">
				<label><input type="checkbox" name="refund_threshold"  value="<?=(!empty($payment_master_res['is_refund_threshold']) && $payment_master_res['is_refund_threshold'] == 'Y') ? 'Yes' : 'No' ?>" id="refund_threshold" <?=(!empty($payment_master_res['is_refund_threshold']) && $payment_master_res['is_refund_threshold'] == 'Y') ? 'checked' : '' ?>  >
				<span class="m-l-5">Refund Threshold Alert</span>
				</label>
			</div>
            <div id="refund_threshold_alert_div" style="<?=(!empty($payment_master_res['is_refund_threshold']) && $payment_master_res['is_refund_threshold'] == 'Y') ? '' : 'display: none;' ?>"> 
			<div class="form-inline">
					<div class="form-group  mw-125">
						<input type="number" name="refund_threshold_alert" value="<?=!empty($payment_master_res['refund_threshold_value']) && $payment_master_res['refund_threshold_value'] > 0 ? $payment_master_res['refund_threshold_value'] : '' ?>" min="1" max="99" onKeyUp="if(this.value>99){this.value='99';}else if(this.value<0){this.value='0';}"  class="form-control mw-125">
						<label>Percentage</label>
						<div class="clearfix"></div>
						<p class="error error_preview" id="error_refund_threshold_alert"></p>
					</div>
				<div class="form-group ">
                    <label class="m-l-5">
                    Alert when refunds/voids reach this monthly %
                    </label> 
				</div>
			</div>
			</div>
        </div> 
        <div class="d-flex m-b-25">
        	<div class="merchant_line_label" style="min-width:220px;">
				<label><input type="checkbox" name="chargeback_threshold"  value="<?=(!empty($payment_master_res['is_chargeback_threshold']) && $payment_master_res['is_chargeback_threshold'] == 'Y') ? 'Yes' : 'No' ?>" id="chargeback_threshold" <?=(!empty($payment_master_res['is_chargeback_threshold']) && $payment_master_res['is_chargeback_threshold'] == 'Y') ? 'checked' : '' ?>>
				<span class="m-l-5">Chargeback Threshold Alert</span>
				</label>
			</div>
            <div class="" id="chargeback_threshold_alert_div" style="<?=(!empty($payment_master_res['is_chargeback_threshold']) && $payment_master_res['is_chargeback_threshold'] == 'Y') ? '' : 'display: none;' ?>">
			<div class="form-inline">
					<div class="form-group  mw-125">
						<input type="number" name="chargeback_threshold_alert" value="<?=!empty($payment_master_res['chargeback_threshold_value']) && $payment_master_res['chargeback_threshold_value'] > 0 ? $payment_master_res['chargeback_threshold_value'] : '' ?>" min="1" max="99" onKeyUp="if(this.value>99){this.value='99';}else if(this.value<0){this.value='0';}" class="form-control mw-125">
						<label>Percentage</label>
						<div class="clearfix"></div>
						<p class="error error_preview" id="error_chargeback_threshold_alert"></p>
					</div>
				<div class="form-group ">
                    <label class="m-l-5">
                     Alert when chargebacks reach this monthly %
                    </label>
				</div>
			</div>
			</div>
        </div>
         
	    <div style="<?=(!empty($type) && $type == 'Variation') ? '' : 'display: none;';?>">
	    	<p class="fs16 m-t-25 fw500 ">Assign Agents</p>
		    <div class="m-b-25">
	            <p>How would you like to assign agents to this Merchant Variation?</p>
	            <div class="radio-question">
		            <label class="radio-inline"><input type="radio" name="assinged_to_agent" class="assinged_to_agent" value="all" <?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'Y') ? "checked" : '') ?>> All Agents</label>
		            <label class="radio-inline"><input type="radio" name="assinged_to_agent" class="assinged_to_agent" value="selected" <?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'N') ? "checked" : '') ?>> Select Agent(s)</label>
		        </div>
	            <span class="error error_preview" id="error_assinged_to_agent"></span>
	          </div>
	      <div  id="select_agent_div" style="<?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'N') ? "" : "display: none") ?>">
	        <div class="row ">
	          <div class="col-sm-6">
	            <div class="form-group">
		            <select name="agents[]" id="agents" multiple="multiple" class="se_multiple_select searchMultipleSelect">
	                <?php if(!empty($agent_res) && count($agent_res) > 0) {
	                	foreach ($agent_res as $key =>$row) { ?>
	                 		<option value="<?= $row['id'] ?>" <?= (!empty($agent_ids) && in_array($row['id'], $agent_ids)) ? 'selected="selected"' : '' ?> ><?=$row['rep_id'].' - '. $row['fname'].' ' .$row['lname']?></option>
	               		<?php } 
	               	} ?>
	              </select>
                  <label>Select Agents</label>
	              <span class="error error_preview" id="error_agents"></span>
	            </div>
	          </div>
	        </div>
	      </div>
	      <div id="select_agent_display_div"> </div>
	      <p class="fs16 fw500">Assign Products</p>
	      <div class="m-b-25">
	            <p>How would you like to assign products to this Merchant Variation?<br><i class="text-light-gray">(If an order includes a product that is not assigned to this Variation, then the order will revert back to using the default processor.)</i></p>
	            <div class="radio-question">
		            <label class="radio-inline"><input type="radio" name="assinged_to_product" class="assinged_to_product" value="all" <?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'Y') ? "checked" : '') ?>> All Products</label>
		            <label class="radio-inline"><input type="radio" name="assinged_to_product" class="assinged_to_product" value="selected" <?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'N') ? "checked" : '') ?>> Select Product(s)</label>
		        </div>
	            <span class="error error_preview" id="error_assinged_to_product"></span>
	          </div>
	      <div id="select_product_div" style="<?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'N') ? "" : "display: none") ?>">
	        <div class="row ">
	          <div class="col-sm-6">
	            <div class="form-group">
              	<select name="products[]" id="products" multiple="multiple" class="se_multiple_select searchMultipleSelect">                     
                  <?php foreach ($company_arr as $key=>$company) { ?>
                  	<optgroup label='<?= $key ?>'>
                    	<?php foreach ($company as $pkey =>$row) { ?>
                    		<option value="<?= $row['id'] ?>" <?= (!empty($product_ids) && in_array($row['id'], $product_ids)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['id'], $assigned_products)) ? 'disabled="disabled"' : '' ?> ><?= $row['name'] .' ('.$row['product_code'].')'?></option>
                    	<?php } ?>
                  	</optgroup>
                	<?php } ?>     
               	</select>
                <label>Select Products</label>
              	<span class="error error_preview" id="error_products"></span>
              </div>
	          </div>
	        </div>
	      </div>
	      <div id="select_product_display_div" style="display: none;"></div>
	    </div>
	    <div class="clearfix">
	    	<div class="pull-left responsive_btn">
	    		<a href="javascript:void(0);" class="btn btn-info connect_processor_popup" id="connect_processor">Test Processor </a>
	    		<a href="javascript:void(0);" class="btn btn-default" id="connect_processor_history">Test Processor History</a>
	    	</div>
	    </div>
		<div class="m-t-30 text-center">
            <a href="javascript:void(0);" class="btn btn-action" id="save_processor">Save</a>
            <a href="javascript:void(0);" class="btn red-link" id="cancel_btn">Cancel</a>
         </div>
		</div>
</form>
</div>
<script type="text/javascript">
$(document).ready(function () {

	$("#search_agent, #acceptable_cc").multipleSelect({
       selectAll: false,
  });

	$("#test_processor").prop("disabled", true);
	$("#products").multipleSelect({
	      selectAll: false,
	      onClick: function (view) {
	      	getProductDetails('N');
	      },
	      onCheckAll: function (e) {
	      	getProductDetails('N');
	      },
	      onUncheckAll: function () {
	      	getProductDetails('N');
	      },
	      onOptgroupClick: function () {
	      	getProductDetails('N');
		  },
		  onTagRemove:function(){
			getProductDetails('N');
		  }
	});
    $("#agents").multipleSelect({
	  selectAll: false,
	  onChange: function () {
      	getAgentDetails('N');
      },
      onClick: function (view) {
      	getAgentDetails('N');
      },
      onCheckAll: function () {
      	getAgentDetails('N');
      },
      onUncheckAll: function () {
      	getAgentDetails('N');
	  },
	  onTagRemove:function(){
		getAgentDetails('N');
	  }
    });

	<?php if(!empty($payment_master_id)) {
			if(($payment_master_res['is_assigned_to_all_product'] == 'N')) { ?>
			$("#products").multipleSelect("refresh");
			getProductDetails('Y');
	<?php } if(($payment_master_res['is_assigned_to_all_agent'] == 'N')) { ?>
				$("#agents").multipleSelect("refresh");
				getAgentDetails('Y');
	<?php } } ?>

    $(document).off('change','#accept_ach_value');
	$(document).on('change','#accept_ach_value', function (event) {
        var state = $(this).is(":checked");
	      if(state) {
            $('#accept_ach_value').val('Yes');
	      	<?php if($type != 'Variation') { ?>
	        	$("#accept_ach_div").show();
	        <?php } ?>
	      } else {
            $('#accept_ach_value').val('No');
	        <?php if($type != 'Variation') { ?>
	        	$("#accept_ach_div").hide();
	        <?php } ?>
	      }
	});

    $(document).off('change','#accept_cc_value');
    $(document).on('change','#accept_cc_value', function (event) {
    var state = $(this).is(":checked");
      if(state) {
        $('#accept_cc_value').val('Yes');
        <?php if($type != 'Variation') { ?>
        	$("#accept_cc_div").show();
        <?php } ?>
        	$("#acceptable_cc_div").show();
      } else {
        $('#accept_cc_value').val('No');
        <?php if($type != 'Variation') { ?>
        	$("#accept_cc_div").hide();
        <?php } ?>
		$("#acceptable_cc_div").hide();
      }
    });
    $(document).off('change','#sale_threshold');
	$(document).on('change','#sale_threshold', function (event) {
        var state = $(this).is(":checked");
      if(state) {
        $('#sale_threshold').val('Yes');
        $("#sale_threshold_alert_div").show();
      } else {
        $('#sale_threshold').val('No');
        $("#sale_threshold_alert_div").hide();
      }
    });

    $(document).off('change','#refund_threshold');
    $(document).on('change','#refund_threshold', function (event) {
    var state = $(this).is(":checked");
      if(state) {
        $('#refund_threshold').val('Yes');
        $("#refund_threshold_alert_div").show();
      } else {
        $('#refund_threshold').val('No');
        $("#refund_threshold_alert_div").hide();
      }
    });
    
    $(document).off('change','#chargeback_threshold');
    $(document).on('change','#chargeback_threshold', function (event) {
        var state = $(this).is(":checked");
      if(state) {
        $('#chargeback_threshold').val('Yes');
        $("#chargeback_threshold_alert_div").show();
      } else {
        $('#chargeback_threshold').val('No');
        $("#chargeback_threshold_alert_div").hide();
      }
    });

    $(document).off("click",".agent_selected");
    $(document).on("click",".agent_selected", function(){
    	$product_id=$(this).attr('data-id');
    	swal({
           text: '<br>Delete Record: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
           cancelButtonText: 'Cancel',
       }).then(function () {
			$("#agents option[value='"+$product_id+"']").prop("selected", false);
			$("#agents").multipleSelect("refresh");
    		getAgentDetails('N');
    	}, function (dismiss) { 
       });
    });

    $(document).off("click",".product_selected");
    $(document).on("click",".product_selected", function(){
    	$product_id=$(this).attr('data-id');
    	swal({
           text: '<br>Delete Record: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
           cancelButtonText: 'Cancel',
       }).then(function () {
			$("#products option[value='"+$product_id+"']").prop("selected", false);
			$("#products").multipleSelect("refresh");
    		getProductDetails('N');
    	}, function (dismiss) { 
       });
    });

    $(document).off("click","#cancel_btn");
    $(document).on("click","#cancel_btn", function(){
    	window.location.href = "merchant_processor.php";
    });

    $(document).off("click","#cancel_test_btn");
    $(document).on("click","#cancel_test_btn", function(){
    	$.colorbox.close();
    });


    $(document).off('click',".assinged_to_product");
    $(document).on('click',".assinged_to_product",function(){
    	if($(this).val() == 'selected'){
    		$("#select_product_display_div").show();
    		$("#select_product_div").show();
    	} else {
    		$("#products").multipleSelect('uncheckAll');
    		$("#products").multipleSelect("refresh");
    		$("#select_product_display_div").html('').hide();
    		$("#select_product_div").hide();
    	}
    });

    $(document).off('click',".assinged_to_agent");
    $(document).on('click',".assinged_to_agent",function(){
    	if($(this).val() == 'selected'){
    		$("#select_agent_display_div").show();
    		$("#select_agent_div").show();
    	} else {
    		$("#agents").multipleSelect('uncheckAll');
    		$("#agents").multipleSelect("refresh");
    		$("#select_agent_display_div").html('').hide();
    		$("#select_agent_div").hide();
    	}
    });

    $(document).off('change',"#url");
    $(document).on('change',"#url",function(){
    	if($(this).val() == 'new_url'){
    		// $("#url_div").removeClass("col-sm-3").addClass("col-sm-3");
    		// $("#user_name_div").removeClass("col-sm-3").addClass("col-sm-3");
    		// $("#password_div").removeClass("col-sm-3").addClass("col-sm-3");
    		$("#new_url_div").show();
    	} else {
    		// $("#url_div").removeClass("col-sm-3").addClass("col-sm-4");
    		// $("#user_name_div").removeClass("col-sm-3").addClass("col-sm-4");
    		// $("#password_div").removeClass("col-sm-3").addClass("col-sm-4");
    		$("#new_url_div").hide();
    	}
    });

    $('#monthly_threshold_sale').blur(function () {
      $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(null);
      $(this).formatCurrency({colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: 2});
    }).keyup(function (e) {
      var e = window.event || e;
      var keyUnicode = e.charCode || e.keyCode;
      if (e !== undefined) {
        switch (keyUnicode) {
          case 16:
            break; // Shift
          case 17:
            break; // Ctrl
          case 18:
            break; // Alt
          case 27:
            this.value = '';
            break; // Esc: clear entry
          case 35:
            break; // End
          case 36:
            break; // Home
          case 37:
            break; // cursor left
          case 38:
            break; // cursor up
          case 39:
            break; // cursor right
          case 40:
            break; // cursor down
          case 78:
            break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
          case 110:
            break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
          case 190:
            break; // .
          default:
            $(this).formatCurrency({colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true});
        }
      }
    }).bind('decimalsEntered', function (e, cents) {
      if (String(cents).length > 2) {
        var errorMsg = 'Please do not enter any cents (0.' + cents + ')';
        $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(errorMsg);
        console.log('Event on decimals entered: ' + errorMsg);
      }
    });

    $(document).off('change',".tblur");
    $(document).on("change",".tblur", function(){
    	if($(this).val() != '' && $(this).val() != undefined){
    		$("#error_" + $(this).attr('name')).val('').hide();
    	}
    });

    $(document).off('click',"#connect_processor");
    $(document).on("click","#connect_processor", function(){
    	if(($(this).prop("disabled") === false) || ($(this).prop("disabled") === undefined)){
			// $("#btn_clicked").val('C');
			
			$("#btn_clicked").val('T');
			console.log($("#btn_clicked").val());
    		$('#form_submit').submit();
    	}
    });

    $(document).off('click',"#connect_processor_history");
    $(document).on("click","#connect_processor_history", function(){
		$("#processor_name_title_id").html($("#processor_name").val());
		$link = 'test_processor_history_popup.php?pay_id=' + $("#payment_master_id").val()+"&pro_name="+$("#processor_name").val();
		$.colorbox({
          href : $link,
          iframe: 'true', 
          width: '900px', 
          height: '600px'
        }); 
    });

    $(document).off('click',"#save_processor");
    $(document).on("click","#save_processor", function(){
    	$("#btn_clicked").val('S');
    	$('#form_submit').submit();
    });

    $(document).off('change', "#gateway_id");
    $(document).on('change', "#gateway_id", function(){
    	$gateway_id = $(this).val();
    	$gateway_name = $("#gateway_id option[value='"+$(this).val()+"']").attr("data-name");
    	$("#gateway_name").val($gateway_name);
    	if($gateway_id != '' && $gateway_id != undefined){
    		// Authorize
    		if($gateway_id == 1){
    			$(".transaction_key_div").show().fadeIn();    			
    			$(".login_id_div").show().fadeIn();    			
    			
    			$(".api_key_div").hide().fadeOut();
    			$(".api_pin_div").hide().fadeOut();
    			$(".user_name_div").hide().fadeOut();
    			$(".password_div").hide().fadeOut();
    			$(".service_key_div").hide().fadeOut();
    			$(".secret_key_div").hide().fadeOut();
				changeUrl($gateway_id);
    		}else if($gateway_id == 2 || $gateway_id == 3){
    			// NMI or C&H
    			$(".user_name_div").show().fadeIn();
    			$(".password_div").show().fadeIn();
    			$(".api_key_div").show().fadeIn();

    			$(".transaction_key_div").hide().fadeOut();    			
    			$(".login_id_div").hide().fadeOut();    			
    			$(".api_pin_div").hide().fadeOut();
    			$(".service_key_div").hide().fadeOut();
    			$(".secret_key_div").hide().fadeOut();
				changeUrl($gateway_id);
    		}else if($gateway_id == 4){
    			// USAePay
    			$(".user_name_div").show().fadeIn();
    			$(".api_pin_div").show().fadeIn();
    			$(".api_key_div").show().fadeIn();

    			$(".transaction_key_div").hide().fadeOut();    			
    			$(".login_id_div").hide().fadeOut();    			
    			$(".password_div").hide().fadeOut();
    			$(".service_key_div").hide().fadeOut();
    			$(".secret_key_div").hide().fadeOut();
    			changeUrl($gateway_id);
    		}else if($gateway_id == 5){
    			// PayByCliq
    			$(".user_name_div").show().fadeIn();
    			$(".password_div").show().fadeIn();
    			$(".service_key_div").show().fadeIn();

    			$(".api_key_div").hide().fadeOut();
    			$(".transaction_key_div").hide().fadeOut();    			
    			$(".login_id_div").hide().fadeOut();    			
    			$(".api_pin_div").hide().fadeOut();
    			$(".secret_key_div").hide().fadeOut();
    			changeUrl($gateway_id);
    		}else if($gateway_id == 6){
    			// PayByCliq
    			$(".api_key_div").show().fadeIn();
    			$(".secret_key_div").show().fadeIn();

    			$(".user_name_div").hide().fadeOut();
    			$(".transaction_key_div").hide().fadeOut();    			
    			$(".login_id_div").hide().fadeOut();    			
    			$(".api_pin_div").hide().fadeOut();
    			$(".service_key_div").hide().fadeOut();
    			changeUrl($gateway_id);
    		}
	    	$("#new_url_div").hide();
    	} else {
    		$(".transaction_key_div").hide().fadeOut();    			
    		$(".login_id_div").hide().fadeOut();    			
			$(".api_key_div").hide().fadeOut();
			$(".api_pin_div").hide().fadeOut();
			$(".user_name_div").hide().fadeOut();
			$(".password_div").hide().fadeOut();
			$(".service_key_div").hide().fadeOut();
			$(".secret_key_div").hide().fadeOut();

    		$("#url").val('');
	    	$("#url option[data-custom='true']").hide();
	    	$("#new_url_div").hide();
	    	$("#url option[value='']").show();
	    	$("#url option[value='new_url']").show();
			$("#url").selectpicker('refresh');
    	}
    });

   function changeUrl(gatewayId){
		$("#url option[data-custom='true']").hide();
		$("#url option[data-id='"+gatewayId+"']").show();
		$("#url  option[data-id='"+gatewayId+"']").prop("selected",true);
		$("#url").addClass('has-value');
		$("#url").selectpicker('refresh');
   }

    $('#form_submit').ajaxForm({
      beforeSend: function () {
      	$("#ajax_loader").show();
      	$(".error").html('').hide();
      },
      dataType: 'json',
      success: function (res) {
      	$("#ajax_loader").hide();
      	if (res.status == 'fail') {
          var is_error = true;
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
            if (is_error) {
              scrollToElement($('#error_' + index));
              is_error = false;
            }
        	});
        } else {
         if(res.pay_master_id != '' && res.pay_master_id != undefined){
        			$("#payment_master_id").val(res.pay_master_id);
        		}
        	if($("#btn_clicked").val() == 'C'){
        		$("#connect_processor").prop('disabled',true).addClass("btn-default").removeClass("btn-action");
        		$("#test_processor").prop('disabled',false).removeClass("btn-default").addClass("btn-info");
        		
        	} else if($("#btn_clicked").val() === 'T'){
				// alert(res.status);
        		if(res.status !== '' && res.status !== undefined){
        			if(res.status == 'Success' || res.status == 'success') {
						$("#processor_name_title_id").html($("#processor_name").val());
						    	$link = 'connect_processor_popup.php?pay_id=' + $("#payment_master_id").val();
						    	$.colorbox({
						      href : $link,
						      iframe: 'true', 
						      width:'768px',
                        height:"570px",
						    }); 
        			} 
					// else {
        			// 	$.colorbox({width: '650px',height: '500',inline: true, href: "#fail_test_processor_div",escKey:false, overlayClose:false, closeButton:false});
        			// }
        		}
        	} else if(res.status === 'create') {
        		setNotifySuccess("New Merchant processor Created Successfully!");
        		window.location.href = 'merchant_processor.php';
        	}else{
            setNotifySuccess("Processor Updated Successfully!");
        		window.location.href = 'merchant_processor.php';
         }
        }
      },
      error: function () {
        alert('Due to some technical error file couldn\'t uploaded.');
      }
    });
	});

	function scrollToElement(e) {
    add_scroll = 0;
    element_id = $(e).attr('id');
    var offset = $(e).offset();
    var offsetTop = offset.top;
    var totalScroll = offsetTop - 200 + add_scroll;
    $('body,html').animate({
        scrollTop: totalScroll
    }, 1200);
  }

  function getProductDetails(is_edit){
  	$product_value = $("#products").multipleSelect('getSelects');
  	var product_variation_checked_arr = {};
  	$(".products_variation:checked").each(function(index,value){
  		product_variation_checked_arr[$(this).attr('data-id')] = 'Y';
  	});
  	if(is_edit == 'Y'){
	  	$(".variation_product_ids").each(function(index,value){
	  		product_variation_checked_arr[$(this).val()] = 'Y';
	  	});
	  }
	  var payment_id = $("#payment_master_id").val();
    if($product_value.length > 0){
	    $.ajax({
        url: "ajax_get_product_details.php",
        type: "POST",
        dataType: "json",
        data: {product_value:$product_value,payment_id:payment_id},
        success: function (res) {
          if(res.status == 'success'){
          	$("#select_product_display_div").html(res.data_html).show();
          } else {
          	$("#select_product_display_div").html('').hide();
		  }
		  $(".products_variation").uniform();
		  $('[data-toggle="tooltip"]').tooltip();
        }
	    });	
    } else {
    	$("#select_product_display_div").html('').hide();
    }
  }

  function getAgentDetails(is_edit){
	  $agent_value = $("#agents").multipleSelect('getSelects');
	  console.log($agent_value);
	  var agent_downline_checked_arr = {};
	  var agent_loa_checked_arr = {};
  	$(".agents_downline:checked").each(function(index,value){
  		agent_downline_checked_arr[$(this).attr('data-id')] = 'Y';
  	});
  	if(is_edit == 'Y'){
	  	$(".agent_downline_ids").each(function(index,value){
	  		agent_downline_checked_arr[$(this).val()] = 'Y';
		  });
		  
		$(".agent_loa_ids").each(function(index,value){
	  		agent_loa_checked_arr[$(this).val()] = 'Y';
	  	});
	  }
	  var payment_id = $("#payment_master_id").val();
    if($agent_value.length > 0){
	    $.ajax({
        url: "ajax_get_agent_details.php",
        type: "POST",
        dataType: "json",
        data: {agent_value:$agent_value,agent_downline_val:agent_downline_checked_arr,agent_loa_val:agent_loa_checked_arr,payment_id:payment_id},
        success: function (res) {
          if(res.status == 'success'){
          	$("#select_agent_display_div").html(res.data_html).show();
          } else {
          	$("#select_agent_display_div").html('').hide();
		  }
		  $(".agents_downline").uniform();
		  $(".agents_loa").uniform();
		  $('[data-toggle="tooltip"]').tooltip();
        }
	    });	
    } else {
    	$("#select_agent_display_div").html('').hide();
    }
  }

function showHiddenPopup(type,key){
	var processor_id = '<?= isset($_GET['id']) ? $_GET['id'] : '' ?>';
	if(processor_id == '' || processor_id==undefined){
		if($("#"+key).attr('type') === 'password'){
			$("#"+key).attr('type','text');
		}else{
			$("#"+type).hide();
			$("#"+key).attr('type','password');
		}
		return false;
	}
	if($("#"+type).is(":hidden") && $("#"+key).attr('type') === 'password'){
		$("#"+type).show();
		$("#"+key).attr('type','password');
	}else{
		$("#"+type).hide();
		$("#"+key).attr('type','password');
	}
}

function showHiddenKey(keyType,fieldType,popup){
	var processor_id = '<?= isset($_GET['id']) ? $_GET['id'] : '' ?>';
	var processorType = '<?= isset($_GET['type']) ? $_GET['type'] : '' ?>';

	if(processor_id === '' || processor_id === undefined){
		$("#"+keyType).attr('type','text');
		return false;
	}
	if(keyType === undefined || keyType === ''){
		return false;
	}
	if($("#"+fieldType).val() === '5401'){
		$.ajax({
			url: "add_merchant_processor.php",
			type: "POST",
			dataType: "json",
			data: {
				processor_id:processor_id,
				processorType:processorType,
				keyType:keyType,
				is_show_key : 1,
			},
			success: function (res) {
				$("#"+popup).hide();
				$("#"+fieldType).val('');
				if(res.status == 'success'){
					$("#"+keyType).attr('type','text');
				} else {
					$("#"+keyType).attr('type','password');
				}
			}
		});
	}else{
		$("#"+popup).hide();
	}
}
</script>
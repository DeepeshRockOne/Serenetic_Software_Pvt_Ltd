<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Address Verification</h4>
		</div>
	</div>
	<div class="panel-body">
		<div class="not_found_content m-b-30">
			<div class="media">
				<div class="media-left">
					<span class="material-icons">info</span>
				</div>
				<div class="media-body">
					<h4 class="media-heading">We have slightly modified the address entered. If correct, please use the suggested address to ensure accurate delivery.</h4>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6">
				<div class="suggested_address">
					 <input type="radio" name="suggestedAddressRadio" class="js-switch" id="suggested_address_radio" value="Entered">
					 <label class="suggested_address_box" for="suggested_address_radio">
					 	<h4 class="m-t-0 m-b-5">Address entered</h4>
					 	<p class="mn"><span class="suggestedAddressEnteredName"></span><br><span class="suggestedAddressEntered"></span><br> United States (US)</p>
					 </label>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="suggested_address">
					 <input type="radio" name="suggestedAddressRadio" class="js-switch" id="suggested_address_radio_2" checked="" value="Suggested">
					 <label class="suggested_address_box" for="suggested_address_radio_2">
					 	<h4 class="m-t-0 m-b-5">Suggested address</h4>
					 	<p class="mn"><span class="suggestedAddressEnteredName"></span><br><span class="suggestedAddressAPI"></span><br> United States (US)</p>
					 </label>
				</div>
			</div>
		</div>
		<div class="text-center">
			<a href="javascript:void(0);" class="btn btn-action" id="suggestedAddressBtn">Use selected address</a>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).off("click","#suggestedAddressBtn");
	$(document).on("click","#suggestedAddressBtn",function(){
			$.colorbox.close();
	});
</script>
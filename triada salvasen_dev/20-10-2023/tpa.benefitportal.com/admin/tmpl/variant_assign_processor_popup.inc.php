<div class="panel panel-default panel-block Font_Roboto">
  <div class="panel-heading">
    <h4 class="mn">Agent(s) Assigned to Processor  - <span class="fw300"><?=$payment_master_res['name']?></span></h4>
  </div>
  <form action="ajax_update_merchant_processor.php" class="theme-form" role="form" method="post" name="form_submit" id="form_submit" enctype="multipart/form-data" novalidate>
  	<input type="hidden" name="payment_master_id" id="payment_master_id" value="<?=!empty($payment_master_id) ?$payment_master_id : '' ?>">
	  <div class="panel-body">
	  				<p class="fs16 fw500 m-b-20">Assign Agents</p>
	  				<div class="m-b-25">
	  				<p>How would you like to assign agents to this Merchant Variation?</p>
	  				<label class="radio-inline"><input type="radio" disabled="disabled" name="assinged_to_agent" class="assinged_to_agent" value="all" <?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'Y') ? "checked" : '') ?>> All Agents</label>
					<label class="radio-inline"><input type="radio" disabled="disabled" name="assinged_to_agent" class="assinged_to_agent" value="selected" <?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'N') ? "checked" : '') ?>>Select Agent(s)</label>
	          		<span class="error error_preview" id="error_assinged_to_agent"></span>
	        	</div>
		<div id="select_agent_div" style="<?=(!empty($payment_master_res['is_assigned_to_all_agent'] && $payment_master_res['is_assigned_to_all_agent'] == 'N') ? "" : "display: none") ?>">
			<div class="row ">
				<?php if(!empty($agent_downline_id_arr) && count($agent_downline_id_arr) > 0) {
					foreach ($agent_downline_id_arr as $key => $value) { ?>
						<input type="hidden" name="agent_downline_id_<?=$key?>" class="agent_downline_ids" value='<?=!empty($value) ? $value : '' ?>'>
				<?php } } ?>
				<?php if(!empty($agents_loa_id_arr) && count($agents_loa_id_arr) > 0) {
							foreach ($agents_loa_id_arr as $key => $value) { ?>
							<input type="hidden" name="agent_loa_id_<?=$key?>" class="agent_loa_ids" value='<?=!empty($value) ? $value : '' ?>'>
					<?php } } ?>
				<div class="col-sm-6">
					<div class="form-group">
						<select name="agents[]" id="agents" multiple="multiple" disabled="disabled" readonly="readonly" class="se_multiple_select searchMultipleSelect" style="width: 100%">
							<?php if(!empty($agent_res) && count($agent_res) > 0) {
								foreach ($agent_res as $key =>$row) { ?>
								<option value="<?= $row['id'] ?>" <?= (!empty($agent_ids) && in_array($row['id'], $agent_ids)) ? 'selected="selected"' : '' ?> ><?= $row['rep_id'] .' - '.$row['fname'].' ' .$row['lname']?></option>
								<?php } 
							} ?>
						</select>
						<label>Select Agents</label>
					</div>
					<span class="error error_preview" id="error_agents"></span>
				</div>
			</div>
		</div>
			<div id="select_agent_display_div"> </div>
		<?php if(!empty($variation_product_id_arr) && count($variation_product_id_arr) > 0) {
					foreach ($variation_product_id_arr as $key => $value) { ?>
					<input type="hidden" name="variation_product_id_<?=$key?>" class="variation_product_ids" value='<?=!empty($value) ? $value : '' ?>'>
			<?php } } ?>
		<div id="processor_assign_product">
			<p class="fs16 m-t-25 fw500 m-b-20">Assign Products</p>
			<div class="m-b-25">
			<p>Would you like this merchant account to be for all products or only specific products?</p>
				<label class="radio-inline"><input type="radio" name="assinged_to_product" class="assinged_to_product" disabled="disabled" value="all" <?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'Y') ? "checked" : '') ?>> All Products</label>
	            <label class="radio-inline"><input type="radio" name="assinged_to_product" class="assinged_to_product" disabled="disabled" value="selected" <?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'N') ? "checked" : '') ?>> Select Product(s)</label>
	            <span class="error error_preview" id="error_assinged_to_product"></span>
			</div>
			<div class="row " id="select_product_div" style="<?=(!empty($payment_master_res['is_assigned_to_all_product'] && $payment_master_res['is_assigned_to_all_product'] == 'N') ? "" : "display: none") ?>">
				<div class="col-sm-6">
					<div class="form-group">
						<select name="products[]" id="products" disabled="disabled" multiple="multiple" class="se_multiple_select searchMultipleSelect" >
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
			<div id="select_product_display_div"> </div>
		<div class="text-center m-t-30 clearfix">
			<!-- <a href="javascript:void(0);" class="btn btn-action" id="save_date">Save</a> -->
			<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			<a href="javascript:void(0);" id="export" class="btn btn-action"> <i class="fa fa-download"></i> Export</a>
		</div>
	  </div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function () {
	  $("#agents").multipleSelect({
	  	selectAll: false
	    // onClick: function (view) {
	    //   getAgentDetails('N');
	    // },
	    // onCheckAll: function () {
	    //   getAgentDetails('N');
	    // },
	    // onUncheckAll: function () {
	    //   getAgentDetails('N');
	    // },
		// onOptgroupClick: function () {
		// 	getAgentDetails('N');
		// },
		// onTagRemove:function(){
		// 	getAgentDetails('N');
		// }
	  });

	  $("#products").multipleSelect({
	      selectAll: false
	    //   onClick: function (view) {
	    //   	getProductDetails('N');
	    //   },
	    //   onCheckAll: function (e) {
	    //   	getProductDetails('N');
	    //   },
	    //   onUncheckAll: function () {
	    //   	getProductDetails('N');
	    //   },
	    //   onOptgroupClick: function () {
	    //   	getProductDetails('N');
		//   },
		//   onTagRemove:function(){
		// 	getProductDetails('N');
		//   }
	});

	<?php if(!empty($payment_master_id)) {
			if(($payment_master_res['is_assigned_to_all_product'] == 'N')) { ?>
			$("#products").multipleSelect("refresh");
			getProductDetails('Y');
	<?php } 
			if(($payment_master_res['is_assigned_to_all_agent'] == 'N')) { ?>
				$("#agents").multipleSelect("refresh");
				getAgentDetails('Y');
	<?php } } ?>
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

	// $(document).off("click",".agent_selected");
	// $(document).on("click",".agent_selected", function(){
	// $product_id=$(this).attr('data-id');
	// $("#agents option[value='"+$product_id+"']").prop("selected", false);
	// $("#agents").multipleSelect("refresh");
	// getAgentDetails('N');
	// });

	// $(document).off("click",".product_selected");
    // $(document).on("click",".product_selected", function(){
    // 	$product_id=$(this).attr('data-id');
    // 	$("#products option[value='"+$product_id+"']").prop("selected", false);
    // 	$("#products").multipleSelect("refresh");
    // 	getProductDetails('N');
    // });

	// $(document).off("click","#save_date");
	// $(document).on("click","#save_date", function(){
	// $("#form_submit").submit();
	// });

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
        	parent.$.colorbox.close();
        	parent.setNotifySuccess("Agent(s) Assigned to Processor successfully.");
        }
      },
      error: function () {
        alert('Due to some technical error file couldn\'t uploaded.');
      }
    });


	});

	function getAgentDetails(is_edit){
	  $agent_value = $("#agents").multipleSelect('getSelects');
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
		    data: {agent_value:$agent_value,agent_downline_val:agent_downline_checked_arr,agent_loa_val:agent_loa_checked_arr,display:'display',payment_id:payment_id},
			beforSend : function(e){
				$("#ajax_loader").show();
			},
		    success: function (res) {
				$("#ajax_loader").hide();
		      if(res.status == 'success'){
		        $("#select_agent_display_div").html(res.data_html).show();
		      } else {
		        $("#select_agent_display_div").html('').hide();
			  }			  
				// $(".agents_downline").uniform();
				// $(".agents_loa").uniform();
		    }
	    }); 
	  } else {
	    $("#select_agent_display_div").html('').hide();
	  }
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
			data: {product_value:$product_value,variation_pro_val:product_variation_checked_arr,display:'display',payment_id:payment_id},
			beforSend : function(e){
				$("#ajax_loader").show();
			},
			success: function (res) {
			$("#ajax_loader").hide();
			if(res.status == 'success'){
				$("#select_product_display_div").html(res.data_html).show();
			} else {
				$("#select_product_display_div").html('').hide();
			}
			// $(".products_variation").uniform();
			}
			});	
		} else {
			$("#select_product_display_div").html('').hide();
		}
	}

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

	$(document).off('click', '#export');
	$(document).on('click', '#export', function (e) {
		e.stopPropagation();
		parent.confirm_export_data(function() {
			$('#ajax_loader').show();
			var payment_id = $("#payment_master_id").val();	  
			var params = {export_val:1,is_ajaxed:1,payment_id:payment_id};
			$.ajax({
				url: "",
				type: 'GET',
				data: params,
				dataType: 'json',
				success: function(res) {
					$('#ajax_loader').hide();
					$("#export_val").val('');
					if(res.status == "success") {
						parent.confirm_view_export_request();
					} else {
						setNotifyError(res.message);
					}
				}
			});
		});
	});

</script>
<div class="panel panel-block panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Billing File</h4>
		</div>
	</div>
	<div class="panel-body">
		<form name="frm_add_file" id="frm_add_file" method="POST" action="">
			<input type="hidden" name="file_id" value="<?=$file_id?>">
			<div class="theme-form">
				<div class="form-group">
					<input type="text" class="form-control" name="file_name" value="<?=$file_name?>">
					<label>File Name</label>
					<p class="error"><span id="error_file_name"></span></p>
				</div>
				<div class="form-group">
					<select class="form-control" name="file_type" id="file_type">
						<option data-hidden="true"></option>
						<option value="Carrier" <?=$file_type == 'Carrier' ? "selected='selected'" : ""?>>Carrier</option>
						<option value="Membership" <?=$file_type == 'Membership' ? "selected='selected'" : ""?>>Membership</option>
						<option value="Product" <?=$file_type == 'Product' ? "selected='selected'" : ""?>>Product Fee</option>
						<option value="Vendor" <?=$file_type == 'Vendor' ? "selected='selected'" : ""?>>Vendor</option>
					</select>
					<label>File Type</label>
					<p class="error"><span id="error_file_type"></span></p>
				</div>
				<div class="form-group">
					<select class="form-control" name="carrier" id="carrier">
						<option data-hidden="true"></option>
						<?php if($carriers){
							foreach ($carriers as $k => $v) { ?>
								<option value="<?=$v['id']?>" <?=$v['id'] == $carrier ? "selected='selected'" : ""?>><?=$v['name']?></option>
						<?php }
						} ?>
					</select>
					<label>Recipient</label>
					<p class="error"><span id="error_carrier"></span></p>
				</div>
				<div class="form-group ">
						<select class="se_multiple_select" name="products[]" id="billing_product" multiple="multiple">
							<?php if(!empty($productRes)){ ?>
			                    <?php foreach ($productRes as $key=> $category) { ?>
			                      <?php if(!empty($category)){ ?>
			                  <optgroup label='<?= $key ?>'>
			                    <?php foreach ($category as $pkey => $row) { ?>
			                      <option value="<?= $row['id'] ?>" <?= (!empty($products) && in_array($row['id'], $products)) ? 'selected="selected"' : '' ?> >
			                        <?= $row['name'] .' ('.$row['product_code'].')'?>    
			                      </option>
			                    <?php } ?>
			                  </optgroup>
			                    <?php } ?>
			                  <?php } ?>
			                <?php } ?>
						</select>
						<label>Product(s)</label>
						<p class="error"><span id="error_products"></span></p>
				</div>
				
				<div class="form-group  text-right">
					<a href="<?=$CSV_WEB . "PAYMENTS_BILLING_FILE.xlsx"?>" class="btn red-link pn">Download Template</a>
				</div>

				<div class="form-group height_auto">
					<p>Generate file by period:</p>
					<div class="m-b-10">
					  <label class="mn"><input type="radio" name="period_type" value="coverage_period" <?=$period_type == 'coverage_period' ? 'checked' : ''?>> Monthly Plan Period </label>
					</div>
					<div class="m-b-10">
					  <label class="mn"><input type="radio" name="period_type" value="pay_period" <?=$period_type == 'pay_period' ? 'checked' : ''?>> Monthly Pay Period </label>
					</div>
					<p class="error"><span id="error_period_type"></span></p>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="text-center">
				<a href="javascript:void(0);" class="btn btn-action" id="add_file">Save</a>
				<a href="javascript:void(0);"  onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $("#billing_product").multipleSelect({
  });

  $('#file_type').on('change',function(){
  	var file_type = $(this).val();
  	$.ajax({
      url: 'ajax_get_recipient.php',
      dataType: 'JSON',
      data: {file_type : file_type},
      type: 'GET',
      success: function(res) {
        $("#ajax_loader").hide();
        
        if (res.status == "success") {
          $('#carrier').html(res.content);
          $('#carrier').selectpicker('refresh');
        } else {
          
        }
      }
    });

  });

  $('#carrier').on('change',function(){
  	var carrier = $(this).val();
  	var name = $('#file_type').val();
  	$.ajax({
      url: 'ajax_get_recipient.php',
      dataType: 'JSON',
      data: {carrier : carrier, name : name},
      type: 'GET',
      success: function(res) {
        $("#ajax_loader").hide();
        
        if (res.status == "success") {
          $('#billing_product').html(res.content);
          $('#billing_product').multipleSelect('refresh');
        } else {
          
        }
      }
    });

  });

  $(document).on("click", "#add_file", function() {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_add_billing_file.php',
      dataType: 'JSON',
      data: $("#frm_add_file").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error span').html('');
        if (res.status == "success") {
          window.parent.setNotifySuccess(res.message);	
          window.parent.$.colorbox.close();
          window.parent.ajax_submit();
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              if (typeof(offset) === "undefined") {
                console.log("Not found : " + index);
              } else {
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 195;
                $('body,html').animate({
                  scrollTop: totalScroll
                }, 1200);
                is_error = false;
              }
            }
          });
        }
      }
    });
  });
});
</script>
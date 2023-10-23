<div class="panel panel-block panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Eligibility File</h4>
		</div>
	</div>
	<div class="panel-body">
		<form name="frm_add_file" id="frm_add_file" method="POST" action="">
			<input type="hidden" name="file_id" value="<?=$file_id?>">
			<div class="theme-form">
				<div class="form-group">
					<input type="text" class="form-control" name="file_name" value="<?=$file_name?>">
					<label>File Name</label>
					<p class="error" id="error_file_name"></p>
				</div>
				<div class="form-group  ">
						<select class="form-control" name="product_type" id="elegibility_product_type">
							<option></option>
							<option value="Main Products" <?=$product_type == "Main Products"?"selected":"";?>>Main Products</option>
							<option value="Sub Products" <?=$product_type == "Sub Products"?"selected":"";?>>Sub Products</option>
							<option value="Participants Products" <?=$product_type == "Participants Products"?"selected":"";?>>Participants Products</option>
						</select>
						<label>Product Type</label>
						<p class="error" id="error_product_type"></p>
				</div>
				<div class="main_products_section" style="<?=$product_type == "Main Products"?"":"display: none;";?>">
					<div class="form-group ">
							<select class="se_multiple_select" name="main_products[]" id="elegibility_main_product" multiple="multiple">
								<?php if(!empty($productRes)){ ?>
				                    <?php foreach ($productRes as $key=> $category) { ?>
				                      <?php if(!empty($category)){ ?>
				                  <optgroup label='<?= $key ?>'>
				                    <?php foreach ($category as $pkey => $row) { ?>
				                      <option value="<?= $row['id'] ?>" <?= (!empty($products) && in_array($row['id'], $products)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['id'], $assigned_products)) ? 'disabled="disabled"' : '' ?> >
				                        <?= $row['name'] .' ('.$row['product_code'].')'?>    
				                      </option>
				                    <?php } ?>
				                  </optgroup>
				                    <?php } ?>
				                  <?php } ?>
				                <?php } ?>
							</select>
							<label>Product(s)</label>
							<p class="error" id="error_main_products"></p>
					</div>
					<div class="m-b-25 text-right">
						<a href="<?=$CSV_WEB . "eligibility_template.csv"?>" class="btn red-link pn">Download Template</a>
					</div>
				</div>
				<div class="sub_products_section"  style="<?=$product_type == "Sub Products"?"":"display: none;";?>">
					<div class="form-group " >
							<select class="se_multiple_select" name="sub_products[]" id="elegibility_sub_product" multiple="multiple">
								<?php if(!empty($subProductRes)){ ?>
				                    <?php foreach ($subProductRes as $key=> $category) { ?>
				                      <?php if(!empty($category)){ ?>
				                  <optgroup label='<?= $key ?>'>
				                    <?php foreach ($category as $pkey => $row) { ?>
				                      <option value="<?= $row['id'] ?>" <?= (!empty($products) && in_array($row['id'], $products)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['id'], $assigned_products)) ? 'disabled="disabled"' : '' ?> >
				                        <?= $row['name'] .' ('.$row['product_code'].')'?>    
				                      </option>
				                    <?php } ?>
				                  </optgroup>
				                    <?php } ?>
				                  <?php } ?>
				                <?php } ?>
							</select>
							<label>Product(s)</label>
							<p class="error" id="error_sub_products"></p>
					</div>
					<div class="m-b-25 text-right">
						<a href="<?=$CSV_WEB . "sub_eligibility_template.csv"?>" class="btn red-link pn">Download Template</a>
					</div>
				</div>
				<div class="participants_products_section"  style="<?=$product_type == "Participants Products"?"":"display: none;";?>">
					<div class="form-group">
							<select class="se_multiple_select" name="participants_products[]" id="elegibility_participants_product" multiple="multiple">
								<?php if(!empty($participantsProductRes)){ ?>
				                    <?php foreach ($participantsProductRes as $key=> $row) { ?>
				                      <option value="<?= $row['product_code'] ?>" <?= (!empty($products) && in_array($row['product_code'], $products)) ? 'selected="selected"' : '' ?> <?= (!empty($assigned_products) && in_array($row['product_code'], $assigned_products)) ? 'disabled="disabled"' : '' ?> >
				                        <?= $row['product_code']?>    
				                      </option>
				                    <?php } ?>
				                  <?php } ?>
							</select>
							<label>Product(s)</label>
							<p class="error" id="error_participants_products"></p>
					</div>
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:void(0);" id="add_file" class="btn btn-action">Save</a>
				<a href="javascript:void(0)"  onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $("#elegibility_main_product,#elegibility_sub_product,#elegibility_participants_product").multipleSelect({
       selectAll: false,
  });

  $(document).off("change", "#elegibility_product_type");
  $(document).on("change", "#elegibility_product_type", function() {
  	var product_type = $(this).val();
  	$(".main_products_section").hide();
  	$(".sub_products_section").hide();
  	$(".participants_products_section").hide();

  	if(product_type == "Main Products") {
  		$(".main_products_section").show();
  	} else if(product_type == "Sub Products") {
  		$(".sub_products_section").show();
  	}else if(product_type == "Participants Products") {
  		$(".participants_products_section").show();
  	}
  });

  $(document).on("click", "#add_file", function() {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_add_eligibility_file.php',
      dataType: 'JSON',
      data: $("#frm_add_file").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        $('.error').html('');
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
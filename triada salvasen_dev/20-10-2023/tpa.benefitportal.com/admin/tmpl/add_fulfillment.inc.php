<div class="panel panel-block panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Fulfillment File</h4>
		</div>
	</div>
	<div class="panel-body">
		<form name="frm_add_file" id="frm_add_file" method="POST" action="">
			<input type="hidden" name="file_id" value="<?=$file_id?>">
			<div class="theme-form">
				<div class="form-group">
					<input type="text" class="form-control" name="file_name" value="<?=$file_name?>">
					<label>File Name</label>
				</div>
				<div class="form-group">
						<select class="se_multiple_select" name="products[]" id="elegibility_product" multiple="multiple">
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
				</div>
				<div class="m-b-25 text-right">
					<a href="<?=$CSV_WEB . "FULFILLMENT_TEMPLATE.csv"?>" class="btn red-link pn">Download Template</a>
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
  $("#elegibility_product").multipleSelect({
       selectAll: false,
  });

  $(document).on("click", "#add_file", function() {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_add_fulfillment_file.php',
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
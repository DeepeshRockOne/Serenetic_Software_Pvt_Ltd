 <div class="panel panel-default panel-popup ">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">
        <?php if(!empty($id)){ ?>
          Edit Sub Product
        <?php }else{ ?>
          + Sub Product
        <?php } ?>
      </h4>
    </div>			
  </div>
  <div class="panel-body">
    <form id="sub_product_form" class="theme-form" name="sub_product_form" method="POST">
      <input type="hidden" name="sub_id" id="sub_id" value="<?= $id ?>">
      <div class="row">
        <div class="col-sm-8">
          <div class="form-group">
            <select name="carrier_id" id="carrier_id" class="form-control">
              <option value=""></option>
              <?php if(!empty($carrierRows )){ ?>
                <?php foreach ($carrierRows as $crow) {?>
                    <option value="<?php echo $crow["id"]; ?>" <?= (!empty($carrier_id) && $crow['id'] == $carrier_id) ? 'selected=selected' : ''; ?>><?php echo $crow["name"].' ('.$crow["display_id"].')'; ?></option>
                <?php } ?>
              <?php } ?>
            </select>
            <label>Product Carrier</label>
            <p class="error" id="error_carrier_id"></p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-8">
          <div class="form-group">
            <input type="text" id="product_code" class="form-control" name="product_code" value='<?= (!empty($product_code))?$product_code:'' ?>' />      
            <label>Product Code</label>
            <p class="error" id="error_product_code"></p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-8">
          <div class="form-group">
            <input type="text" id="product_name" class="form-control" name="product_name" value='<?= (!empty($product_name))?$product_name:'' ?>' />      
            <label>Product Name</label>
            <p class="error" id="error_product_name"></p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            <select name="status" class="form-control" id="status">
              <option value="" ></option>
              <option value="Active" <?php echo (!empty($status) && $status=='Active')?"selected='selected'":''?>>Active</option>
              <option value="Inactive" <?php echo (!empty($status) && $status=='Inactive')?"selected='selected'":''?>>Inactive</option>
            </select>   
            <label>Status</label>
            <p class="error" id="error_status"></p>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
        <div class="text-center">
          <button class="btn btn-action" type="button" name="save" id="save">Save</button>
          <button class="btn red-link" type="button" onclick='parent.$.colorbox.close()' >Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).on("click", "#save", function() {
    $formId = $("#sub_product_form");
    $action = 'ajax_sub_products_edit.php';
    $('.error').html('');
    $.ajax({
      url: $action,
      type: 'POST',
      dataType: 'json',
      data: $formId.serialize(),
      beforeSend: function() {
        $("#ajax_loader").show();
      },
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == 'success') {
          parent.$.colorbox.close();
          window.parent.setNotifySuccess(res.msg);
        } else {
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            var offset = $('#error_' + index).offset();
            if (typeof(offset) === "undefined") {
              console.log("Not found : " + index);
            } else {
              var offsetTop = offset.top;
              var totalScroll = offsetTop - 195;
              $('body,html').animate({
                scrollTop: totalScroll
              }, 1200);
            }
          });
        }
      }
    });
  });
</script>
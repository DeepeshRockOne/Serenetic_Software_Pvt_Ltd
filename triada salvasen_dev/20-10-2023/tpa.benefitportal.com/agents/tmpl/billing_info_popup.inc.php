<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4>Billing Address - <span class="fw300"> Edit</span></h4>
  </div>
  <div class="panel-body "> 
     <div class="row theme-form">
      <div class="col-sm-4">
       <div class="form-group">
        <input type="text" name="" id="" value="" class="form-control" *>
        <label>Name*</label>
       </div>
      </div>
      <div class="col-sm-8">
       <div class="form-group">
        <input type="text" name="bill_address" id="bill_address"
         value="<?= !empty($billing_data['address']) ? $billing_data['address'] : '' ?>"
         class="required form-control tblur" *>
        <label>Address*</label>
        <span class="error error_preview" id="error_bill_address"></span></div>
      </div>
      <div class="col-sm-4">
       <div class="form-group">
        <input type="text" name="bill_city" id="bill_city"
         value="<?= !empty($billing_data['city']) ? $billing_data['city'] : '' ?>"
         class="required form-control tblur" *>
        <label>City*</label>
        <span class="error error_preview" id="error_bill_city"></span></div>
      </div>
      <div class="col-sm-4">
       <div class="form-group height_auto">
        <select id="bill_state" name="bill_state" class="tblur form-control">
         <option value=""></option>
			 <?php if (count($bill_state_res) > 0) { ?>
             <?php foreach ($bill_state_res as $key => $value) { ?>
             <option
             value="<?= $value["name"]; ?>" <?= !empty($billing_data['state']) && $billing_data['state'] == $value["name"] ? 'selected="selected"' : '' ?>><?php echo $value['name']; ?></option>
             <?php } ?>
             <?php } ?>
            </select>
        <label>State*</label>
        <span class="error error_preview" id="error_bill_state"></span></div>
      </div>
      <div class="col-sm-4">
       <div class="form-group height_auto ">
        <input type="text" name="bill_zip" id="bill_zip"
         value="<?= !empty($billing_data['zip']) ? $billing_data['zip'] : '' ?>"
         class="required form-control tblur"
         maxlength="<?php echo $bill_country == 231 ? '5' : '7'; ?>" *>
        <label>Zip/Postal Code*</label>
        <span class="error error_preview" id="error_bill_zip"></span></div>
      </div>
      <div class="col-sm-12 text-center">
      	 <input name="" type="button" class="btn btn-action" value="Save" />
      </div>
     </div> 
  </div>
  
</div>

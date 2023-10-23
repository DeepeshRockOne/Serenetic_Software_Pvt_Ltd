<div class="panel panel-default theme-form">
   <div class="clearfix p-15 br-b">
      <span class="pull-left fs18 fw500 m-t-5">
      Pay Now
      </span>
      <div class="pull-left w-130  p-l-10 pr">
         <div class="form-group height_auto mn ">
            <select class="form-control">
               <option value=""></option>
               <option>List-10001</option>
            </select>
            <label>List Bill</label>
         </div>
      </div>
   </div>
   <div class="paynow_top_wrap">
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group height_auto m-b-5">
               <select class="form-control">
                  <option></option>
                  <option>+ New Payment Method</option>
                  <option>VISA *5599</option>
                  <option>ACH *7777</option>
               </select>
               <label>Select Payment Method</label>
            </div>
         </div>
         <div class="col-sm-6">
            <table cellspacing="0" cellspacing="0" class="fs12 pull-right">
               <tbody>
                  <tr>
                     <td style="padding: 3px 15px;">Due Date:</td>
                     <td style="padding: 3px 15px;">02/28/2020</td>
                  </tr>
                  <tr>
                     <td style="padding: 3px 15px;">Balance Due:</td>
                     <td style="padding: 3px 15px; font-weight: bold; font-size: 14px;">$2,832.92</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class="panel-body ">
      <p class="m-b-15">+ New Payment Method</p>
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="" class="form-control">
               <label>Name On Card</label>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <select class="form-control">
                  <option></option>
                  <option>Visa</option>
                  <option>American Express</option>
                  <option>MasterCard</option>
               </select>
               <label>Card Type</label>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="" class="form-control">
               <label>Card Number</label>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" name="" class="form-control" id="datepicker">
                     <label>Expiration Date</label>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" name="" class="form-control">
                     <label>CVV</label>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="text-center">
         <a href="javascript:void(0);" class="btn btn-info">Save</a>
         <a href="javascript:void(0);" class="btn red-link">Cancel</a>
      </div>
      <hr>
      <p class="">Amount Being Charged</p>
      <div class="form-group">
         <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            <input type="text" class="form-control" readonly="readonly" placeholder="$2,832.92">
         </div>
      </div>
       <div class="text-center ">
         <a href="javascript:void(0);" class="btn btn-action">Pay Now</a>
         <a href="javascript:void(0);" class="btn red-link">Cancel</a>
      </div>
   </div>
</div>

<script type="text/javascript">
  $(document).ready(function() { 
         $("#datepicker").datepicker( {
    format: "mm/yyyy",
    startView: "months", 
    minViewMode: "months"
});
  });
</script>
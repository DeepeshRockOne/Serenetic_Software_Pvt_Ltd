<div class="panel panel-default theme-form">
   <div class="panel-heading">
      <div class="panel-title">
         <h4 class="mn">Make Payment</h4>
      </div>
   </div>
   <div class="panel-body ">
      <p class="fw500">Select a payment option below to make a payment.</p>
      <div class="form-group height_auto ">
         <div class="m-b-15">
            <label class="mn"><input type="radio" name="optradio"> ACH/CC </label>
         </div>
         <div class="m-b-15">
            <label class="mn"><input type="radio" name="optradio"> Record Payment (Check)</label>
         </div>
      </div>
   </div>
   <div class="text-center ">
      <a href="groups_pay_now.php" class="btn btn-action groups_pay_now">Next</a>
      <a href="javascript:void(0);" class="btn red-link">Cancel</a>
   </div>
   <div class="paynow_top_wrap m-t-30">
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group height_auto m-b-5">
               <select class="form-control">
                  <option></option>
                  <option>List-10001</option>
                  <option>List-10002</option>
                  <option>List-10003</option>
               </select>
               <label>List Bill</label>
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
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               <input type="text" name="" class="form-control">
               <label>Amount Received (USD)</label>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <div class="pr">
                     <input type="text" class="form-control">
                     <label>Payment Date</label>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-12">
            <div class="form-group">
               <input type="text" name="" class="form-control">
               <label>Check #</label>
            </div>
         </div>
         <div class="col-sm-12">
            <div class="custom_drag_control">
               <span class="btn btn-action">Upload Attatchment</span>
               <input type="file" class="gui-file" id="" name="" multiple>
               <input type="text" class="gui-input" placeholder="Choose File">
            </div>
            <div class="clearfix m-t-7 m-b-25">
               <span class="text-left text-light-gray">You may upload a maximum of 5 files, 5MB each.</span>
               <a class="pull-right red-link" href="javascript:void(0);">+ Attatchment</a>
            </div>
            <div class="form-group">
               <label class="mn"><input type="checkbox" value=""> Send an email receipt?</label>
            </div>
         </div>
      </div>
      <div class="text-center ">
         <a href="javascript:void(0);" class="btn btn-action">Record Payment</a>
         <a href="javascript:void(0);" class="btn red-link">Cancel</a>
      </div>
   </div>
</div>
<script type="text/javascript">
$(document).off('click', '.groups_pay_now');
  $(document).on('click', '.groups_pay_now', function (e) {
    e.preventDefault();
    window.parent.$.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '768px', 
      height: '500px'
    });
  });
</script>
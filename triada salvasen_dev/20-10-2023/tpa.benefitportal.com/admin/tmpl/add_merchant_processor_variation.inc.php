<div class="panel panel-default  panel-space">
   <form action="" role="form" method="post"  name="" id="" enctype="multipart/form-data">
      <div class="panel-heading">
         <div class="panel-title">
            <p class="fs16 mn"><strong class="fw500">Merchant Variation Details</strong></p>
         </div>
      </div>
      <div class="panel-body theme-form">
         <div class="row">
            <div class="col-md-3 col-sm-6">
               <div class="form-group">
                  <input type="text" name="" class="form-control">
                  <label>Merchant Variation Name<em>*</em></label>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group">
                  <input type="text" name="" class="form-control">
                  <label>Merchant ID<em>*</em></label>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group">
                  <input type="text" name="" class="form-control">
                  <label>API Key<em>*</em></label>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group">
                  <select class="form-control">
                     <option></option>
                     <option>Authorize</option>
                     <option>SecurePay</option>
                  </select>
                  <label>Gateway Name<em>*</em></label>
               </div>
            </div>
            <div class="col-md-3 col-sm-6">
               <div class="form-group">
                  <input type="text" name="" class="form-control">
                  <label>Monthly Threshold for Sales<em>*</em></label>
               </div>
            </div>
            <div class="col-md-9 col-sm-6">
               <div class="form-group">
                  <input type="text" name="" class="form-control">
                  <label>Description</label>
               </div>
            </div>
         </div>
         <p class="fs16 m-t-10 fw500 m-b-30">Settings</p>
         <div class="m-b-20">
               <div class="merchant_line_label">
                  <label><input type="checkbox" value="">
                  <span class="m-l-5">Accept ACH</span>
                  </label>
            </div>
         </div>
        <div class="m-b-20">
               <div class="merchant_line_label">
                  <label><input type="checkbox" value="">
                  <span class="m-l-5">Accept Credit/Debit</span>
                  </label>
            </div>
         </div>
         <div class="m-b-20">
               <div class="merchant_line_label">
                  <label><input type="checkbox" value="">
                  <span class="m-l-5">Sales Threshold Alert</span>
                  </label>
            </div>
         </div>
         <div class="m-b-20">
               <div class="merchant_line_label">
                  <label><input type="checkbox" value="">
                  <span class="m-l-5">Refund/Void Threshold Alert</span>
                  </label>
            </div>
         </div>
         <div class="m-b-20">
               <div class="merchant_line_label">
                  <label><input type="checkbox" value="">
                  <span class="m-l-5">Chargeback Threshold Alert</span>
                  </label>
            </div>
         </div>
         
         <div id="processor_assign_agent">
         <p class="fs16 m-t-30 fw500 m-b-20">Assign Agents</p>
         <div class="form-group height_auto ">
           <p>How would you like to assign agents to this Merchant Variation?</p>
           <label class="radio-inline"><input type="radio" name="optradio">All Agents</label>
           <label class="radio-inline"><input type="radio" name="optradio">Specific Agent(s)</label>
         </div>
         <div class="row">
           <div class="col-sm-4">
             <div class="form-group height_auto ">
              <select class="se_multiple_select" name=""  id="search_agent" multiple="multiple" >
                <option>A1234567</option>
                <option>A1234567</option>
                <option>A1234567</option>
                <option>A1234567</option>
                <option>A1234567</option>
              </select>
              <label>Search Agent(s)</label>
            </div>
           </div>
         </div>
         <div class="table-responsive">
           <table class="<?=$table_class?>">
             <thead>
               <tr>
                 <th>Agent ID</th>
                 <th>Name</th>
                 <th class="text-center">Include Downline?</th>
                 <th class="text-center">Include LOA?</th>
                 <th class="text-center" width="100px">Action</th>
               </tr>
             </thead>
             <tbody>
               <tr>
                 <td>A1234567</td>
                 <td>Eugene Carter</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
               <tr>
                 <td>A1234567</td>
                 <td>Eugene Carter</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
               <tr>
                 <td>A1234567</td>
                 <td>Eugene Carter</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
               <tr>
                 <td>A1234567</td>
                 <td>Eugene Carter</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
       <div id="processor_assign_product">
         <p class="fs16 m-t-30 fw500 m-b-20">Assign Products</p>
         <div class="form-group height_auto ">
           <p>Would you like this merchant account to be for all products or only specific products?</p>
           <label class="radio-inline"><input type="radio" name="optradio">All Products</label>
           <label class="radio-inline"><input type="radio" name="optradio">Specific Product(s)</label>
         </div>
         <div class="row">
           <div class="col-sm-4">
             <div class="form-group height_auto">
              <div class="group_select">
                <select class="se_multiple_select" name="products"  id="products" multiple="multiple" >
                  <optgroup label="Product1">
                    <option>Sub Product</option>
                    <option>Sub Product</option>
                    <option>Sub Product</option>
                  </optgroup>
                </select>
                <label>Products</label>
              </div>
            </div>
           </div>
         </div>
         <div class="table-responsive">
           <table class="<?=$table_class?>">
             <thead>
               <tr>
                 <th>Product ID</th>
                 <th>Name</th>
                 <th class="text-center">Include Variation?</th>
                 <th class="text-center" width="100px">Action</th>
               </tr>
             </thead>
             <tbody>
               <tr>
                 <td>BID_1500</td>
                 <td>BrightIdea Dental 1500</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
               <tr>
                 <td>BID_1500</td>
                 <td>BrightIdea Dental 1500</td>
                 <td class="text-center"><input type="checkbox" name=""></td>
                 <td class="icons text-center">
                   <a href="javascript:Void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                 </td>
               </tr>
             </tbody>
           </table>
         </div>
       </div>
          <div class="m-t-30">
            <a href="test_processor.php" class="btn btn-info test_processor"></i>Test Processor</a>
         </div>
            <div class="m-t-30 text-center">
            <a href="javascript:Void(0);" class="btn btn-action">Save</a>
            <a href="javascript:Void(0);" class="btn red-link"></i>Cancel</a>
         </div>
      </div>
   </form>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $("#search_agent, #acceptable_cc").multipleSelect({
       selectAll: false,
  });
   $("#products").multipleSelect({
       
  });

  $('.test_processor').colorbox({
      iframe:true,
      width:'768px',
      height:"450px",
  });
});
</script>
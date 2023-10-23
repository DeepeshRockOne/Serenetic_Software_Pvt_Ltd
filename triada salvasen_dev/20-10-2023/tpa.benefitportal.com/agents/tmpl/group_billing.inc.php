
<p class="agp_md_title">Group Billing Preference <i class="fa fa-info-circle text-info" aria-hidden="true"></i></p>
<div class="row theme-form">
 <div class="col-sm-6">
    <div class="form-group">
      <select class="form-control <?= !empty($billing_type) ? 'has-value' : '' ?>" name="billing_type" id="billing_type">
         <option data-hidden="true"></option>
         <option value="individual" <?= !empty($billing_type) && $billing_type == 'individual' ? 'selected' : '' ?> >Individual</option>
         <option value="list_bill" <?= !empty($billing_type) && $billing_type == 'list_bill' ? 'selected' : '' ?> >List Bill</option>
         <?php if(!empty($billing_type) && $billing_type == 'TPA'){?>
            <option value="TPA"  <?= !empty($billing_type) && $billing_type == 'TPA' ? 'selected' : '' ?>>TPA (Admin Only)</option>
         <?php }?>
      </select>
      <label>Select Group Billing</label>
      <p class="error" id="error_billing_type"></p>
    </div>
 </div>
</div>
<div id="list_bill_div" style="<?= !empty($billing_type) && $billing_type == 'list_bill' ? '' : 'display: none' ?>">
   <p class="agp_md_title">Billing Profile</p>
   <div class="table-responsive m-b-15">
     <table class="<?=$table_class?>">
       <thead>
         <tr>
           <th>Added Date</th>
           <th>Company</th>
           <th>Payment Method</th>
           <!-- <th class="text-center" >Default</th> -->
           <th width="50px">Actions</th>
         </tr>
       </thead>
       <tbody>
         <?php if(!empty($billingRes)) { ?>
            <?php foreach ($billingRes as $key => $info) { ?>
              <tr id="billing_tr_<?=$info['id']?>">
                <td><?= date('m/d/Y',strtotime($info['created_at'])) ?></td>
                <td><?= $info['company_name'] != '' ? $info['company_name'] : $group_name ?></td>
                <td>
                  <?php if($info['payment_mode']=="Check") {
                    echo $info['payment_mode'];
                  }else{ echo $info['card_type'].' (*'.$info['last_cc_ach_no'].')';
                  } ?>
                  
                </td>
                <?php /*<td class="text-center"><a href="javasacript:void(0);"><input type="radio" data-billing-id="<?=$info['id']?>" class="change_default" name="is_default" <?=$info['is_default'] == 'Y' ? 'checked="checked"' : ''?> value="Y" id="is_default_<?=$info['id']?>"></a></td> */ ?>
                
                <td class="icons">
                  <a href="javascript:void(0)" data-billing-id="<?=$info['id']?>" data-toggle="tooltip" data-placement="top" title="Edit" class="groups_edit_billing"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <?php if($info['is_default'] !='Y') { ?>
                     <a href="javascript:void(0)" data-billing-id="<?=$info['id']?>" data-toggle="tooltip" data-placement="top" title="Delete" class="groups_delete_billing"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
         <?php }else{ ?> 
            <tr><td colspan="5" class="text-center">No Record(s) Found</td></tr>
         <?php } ?>
       </tbody>
     </table>
   </div>
   <div class="clearfix text-right">
     <a href="javascript:void(0)" class="btn btn-action" id="groups_add_billing">+ Billing Profile</a>
   </div>
    <?php /*
        <hr>
           <div class="clearfix m-b-10">
             <p class="agp_md_title pull-left m-t-7">Billing - List Bill Invoice</p>
             <div class="pull-right text-right">
                <span class="m-b-0 m-t-7 m-r-10"><strong>Next Billing:</strong>   <span class="text-success m-l-10"> 04/28/2019  |  4 Products  |  $289.00</span></span>
                <a href="groups_make_payment.php" class="btn btn-success groups_make_payment">Make Payment</a>
             </div>
           </div>
           <div class="table-responsive">
             <table class="<?=$table_class?>">
               <thead>
                 <tr>
                   <th>Date</th>
                   <th>List Bill #</th>
                   <th>Group Name</th>
                   <th class="text-center">Company Name</th>
                   <th>Status</th>
                   <th class="text-right">Balance</th>
                   <th class="text-center">Adjustment</th>
                   <th width="90px">Actions</th>
                 </tr>
               </thead>
               <tbody>
                 <tr>
                   <td>01/01/2020</td>
                   <td><a href="javascript:void(0);" class="text-action fw500">List-90000</a></td>
                   <td>North ldaho College</td>
                   <td class="text-center">-</td>
                   <td>Open</td>
                   <td class="text-right">$2,488.50</td>
                   <td class="text-center">-</td>
                   <td class="icons">
                     <a href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                     <a href="javascript:void(0);"><i class="fa fa-download"></i></a>
                   </td>
                 </tr>
                 <tr>
                   <td>12/01/2019</td>
                   <td><a href="javascript:void(0);" class="text-action fw500">List-90517</a></td>
                   <td>North ldaho College</td>
                   <td class="text-center">-</td>
                   <td class="text-success">Paid</td>
                   <td class="text-right">$2,488.50</td>
                   <td class="text-center">-</td>
                   <td class="icons">
                     <a href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                     <a href="javascript:void(0);"><i class="fa fa-download"></i></a>
                   </td>
                 </tr>
               </tbody>
               <tfoot>
                <tr>
                   <td colspan="8">
                      <div class="row table-footer-row">
                         <div class="col-sm-12">
                            <div class="pull-left">
                               <div class="dataTables_info">1 to 2 of 2 Records</div>
                            </div>
                            <div class="pull-right">
                               <div class="dataTables_paginate paging_bs_normal">
                                  <ul class="pagination pagination-md">
                                     <li class="prev disabled"><span>&lt;</span></li>
                                     <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                                     <li class="disabled"><span>&gt;</span></li>
                                  </ul>
                               </div>
                            </div>
                         </div>
                      </div>
                   </td>
                </tr>
             </tfoot>
             </table>
           </div>
           <div class="clearfix"></div>
        <p class="agp_md_title ">Payment Made</p>
           <div class="table-responsive">
              <table class="<?=$table_class?>">
                 <thead>
                    <tr>
                       <th>List Bill #</th>
                       <th>Payment Date</th>
                       <th>Transaction #</th>
                       <th>Payment Method</th>
                       <th>Amount</th>
                       <th>Actions</th>
                    </tr>
                 </thead>
                 <tbody>
                    <tr>
                       <td>
                          <a href="javascript:void(0);" class="fw500 text-action">List-90000</a>
                       </td>
                       <td>01/01/2020</td>
                       <td class="fw500 text-action">250890000</td>
                       <td>ACH</td>
                       <td class="fw500 text-action">($2,488.50)</td>
                       <td><a href="javascript:void(0);">Receipt</a></td>
                    </tr>
                    <tr>
                       <td>
                          <a href="javascript:void(0);" class="fw500 text-action">List-90000</a>
                       </td>
                       <td>01/01/2020</td>
                       <td class="fw500 text-action">250890000</td>
                       <td>ACH</td>
                       <td class="fw500 text-action">($2,488.50)</td>
                       <td><a href="javascript:void(0);">Receipt</a></td>
                    </tr>
                    <tr>
                       <td>
                          <a href="javascript:void(0);" class="fw500 text-action">List-90000</a>
                       </td>
                       <td>01/01/2020</td>
                       <td class="fw500 text-action">250890000</td>
                       <td>ACH</td>
                       <td class="fw500 text-action">($2,488.50)</td>
                       <td><a href="javascript:void(0);">Receipt</a></td>
                    </tr>
                 </tbody>
                <tfoot>
                   <tr>
                      <td colspan="6">
                         <div class="row table-footer-row">
                            <div class="col-sm-12">
                               <div class="pull-left">
                                  <div class="dataTables_info">1 to 2 of 2 Records</div>
                               </div>
                               <div class="pull-right">
                                  <div class="dataTables_paginate paging_bs_normal">
                                     <ul class="pagination pagination-md">
                                        <li class="prev disabled"><span>&lt;</span></li>
                                        <li class="live-link active"><a href="javascript:void(0);">1</a></li>
                                        <li class="disabled"><span>&gt;</span></li>
                                     </ul>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </td>
                   </tr>
                </tfoot>
              </table>
           </div>
        <hr> */
    ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
    
});
$(document).off("change","#billing_type");
$(document).on("change","#billing_type",function(e){
  e.preventDefault();
  $val=$(this).val();

  swal({
      text: "Change Group Billing: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
  }).then(function() {
      $.ajax({
          url: 'ajax_change_group_billing.php',
          data: {
              id: '<?= $group_id ?>',
              billing_type: $val
          },
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              if (res.status == "success") {
                  setNotifySuccess(res.msg);
                  $("#list_bill_div").hide();
                  if($val=="list_bill"){
                    $("#list_bill_div").show();
                  }
              }else{
                  setNotifyError(res.msg);
              }
          }
      });
  }, function(dismiss) {
  })
});

$(document).off("click","#groups_add_billing");
$(document).on("click","#groups_add_billing",function(){
  $group_id = '<?= $group_id ?>';
  $href= "groups_add_billing.php?group_id="+$group_id;
  $.colorbox({href:$href,iframe: true, width: '768px', height: '500px'});
});


$(document).off("click",".groups_edit_billing");
$(document).on("click",".groups_edit_billing",function(){
  $group_id = '<?= $group_id ?>';
  $billing_id = $(this).attr('data-billing-id');
  $href= "groups_add_billing.php?group_id="+$group_id+'&billing_id='+$billing_id;
  $.colorbox({href:$href,iframe: true, width: '768px', height: '500px'});
});

$(document).off("click",".groups_delete_billing");
$(document).on("click",".groups_delete_billing",function(){
  $group_id = '<?= $group_id ?>';
  $billing_id = $(this).attr('data-billing-id');
  $href= "ajax_groups_delete_billing.php";

  swal({
      text: "Delete  Group Billing: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Yes",
  }).then(function() {
      $.ajax({
          url: $href,
          data: {
              id: $group_id,
              billing: $billing_id
          },
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              if (res.status == "success") {
                  setNotifySuccess(res.msg);
                  $("#billing_tr_"+$billing_id).remove();
              }else{
                  setNotifyError(res.msg);
              }
          }
      });
  }, function(dismiss) {
  })
});

  
/*$(document).off("change",".change_default");
$(document).on("change",".change_default",function(e){
  e.preventDefault();
  $group_id = '<?= $group_id ?>';
  $billing_id = $(this).attr('data-billing-id');

  swal({
      text: "Are you sure you want to change Default Billing Profile?",
      showCancelButton: true,
      confirmButtonText: "Yes",
  }).then(function() {
      $.ajax({
          url: 'ajax_change_default_group_billing.php',
          data: {
              id: $group_id,
              billing: $billing_id
          },
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              if (res.status == "success") {
                  setNotifySuccess(res.msg);
                  ajax_get_group_data('group_billing.php','gp_billing');
              }else{
                  setNotifyError(res.msg);
              }
          }
      });
  }, function(dismiss) {
  })
});*/
</script>
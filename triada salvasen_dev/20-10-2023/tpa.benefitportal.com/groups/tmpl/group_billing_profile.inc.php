
<p class="agp_md_title">Group Billing Preference <i class="fa fa-info-circle text-info" aria-hidden="true"></i></p>
<div class="row theme-form">
 <div class="col-sm-6">
    <div class="form-group">
      <select class="form-control <?= !empty($billing_type) ? 'has-value' : '' ?>" name="billing_type" id="billing_type" disabled>
         <option data-hidden="true"></option>
         <option value="individual" <?= !empty($billing_type) && $billing_type == 'individual' ? 'selected' : '' ?> >Individual</option>
         <option value="list_bill" <?= !empty($billing_type) && $billing_type == 'list_bill' ? 'selected' : '' ?> >List Bill</option>
         <option value="TPA"  <?= !empty($billing_type) && $billing_type == 'TPA' ? 'selected' : '' ?>
         >TPA (Admin Only)</option>
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
           <!-- <th width="50px">Actions</th> -->
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
                <?php /*<td class="text-center"><a href="javasacript:void(0);"><input type="radio" data-billing-id="<?=$info['id']?>" class="change_default" name="is_default" <?=$info['is_default'] == 'Y' ? 'checked="checked"' : ''?> value="Y" id="is_default_<?=$info['id']?>" disabled></a></td>*/ ?>
                
<!--                 <td class="icons">
                  <a href="javascript:void(0)" data-billing-id="<?=$info['id']?>" data-toggle="tooltip" data-placement="top" title="Edit" class="groups_edit_billing"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <?php if($info['is_default'] !='Y') { ?>
                     <a href="javascript:void(0)" data-billing-id="<?=$info['id']?>" data-toggle="tooltip" data-placement="top" title="Delete" class="groups_delete_billing"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  <?php } ?>
                </td> -->
              </tr>
            <?php } ?>
         <?php }else{ ?> 
            <tr><td colspan="5" class="text-center">No Record(s) Found</td></tr>
         <?php } ?>
       </tbody>
     </table>
   </div>
   <div class="clearfix text-right">
     <!-- <a href="javascript:void(0)" class="btn btn-action" id="groups_add_billing">+ Billing Profile</a> -->
   </div>
    
</div>
<script type="text/javascript">
$(document).ready(function(){
    
});
$(document).off("change","#billing_type");
$(document).on("change","#billing_type",function(e){
  e.preventDefault();
  $val=$(this).val();

  swal({
      text: "Are you sure you want to change Group Billing ?",
      showCancelButton: true,
      confirmButtonText: "Yes",
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
      text: "Are you sure you want to delete this Group Billing ?",
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
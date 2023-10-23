<p class="agp_md_title">Billing Profile</p>
<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <th>Added Date</th>
         <th>Name</th>
         <th>Type</th>
         <th width="17%">Payment Method</th>
         <th class="text-center" >Default</th>
         <th width="90px">Actions</th>
      </thead>
      <tbody>
      <?php if(!empty($billing_info) && count($billing_info) > 0) {
            foreach($billing_info as $info)  { ?>
            <tr>
               <td><?=getCustomDate($info['created_at'])?></td>
               <td><?=$info['fname'].' '.$info['lname']?></td>
               <td><?=$info['card_type'].' (*'.$info['last_cc_ach_no'].')'?></td>
               <td><?=$info['payment_mode']?></td>
               <td class="text-center"><a href="javasacript:void(0);"><input type="radio" data-bill_id="<?=$info['id']?>" class="change_default" name="is_default" <?=$info['is_default'] == 'Y' ? 'checked="checked"' : ''?> value="Y" id="is_default_<?=$info['id']?>"></a></td>
               <td class="icons">
                  <a href="<?=$HOST?>/add_billing_profile.php?location=group&id=<?=$customer_id?>&action=Edit&bill_id=<?=$info['id']?>" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" class="edit_billing_profile"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <?php if($info['is_default'] !='Y' && count($billing_info) > 1) { 
                     //OP29-738 Remove ability for Group to do delete billing method if no other exists
                  ?>
                     <a href="javascript:void(0)" data-bill_id="<?=$info['id']?>" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Delete" class="delete_billing_profile"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  <?php } ?>
               </td>
            </tr>
      <?php }} ?>
      </tbody>
   </table>
</div>
<br>
<div class="text-right">
   <a href="<?=$HOST?>/add_billing_profile.php?location=group&id=<?=$customer_id?>&action=Add" class="add_billing_profile btn btn-action">+ Billing Method</a>
</div>
<hr>


<script type="text/javascript">
$(document).ready(function() {
   $(".edit_billing_profile").colorbox({iframe: true, width: '768px', height: '600px'});
   $(".add_billing_profile").colorbox({iframe: true, width: '768px', height: '600px'});

   $(document).off('click','.delete_billing_profile');
   $(document).on('click','.delete_billing_profile',function(e){
      var $bill_id = $(this).attr('data-bill_id');
         swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
            allowEscapeKey: false,
            allowOutsideClick: false
         }).then(function() {
            $.ajax({
               url:'<?=$HOST?>/add_billing_profile.php?location=group',
               method : 'POST',
               data : {
                  id:'<?=$customer_id?>',
                  bill_id:$bill_id,
                  action:"Delete",
                  is_delete : 1,
                  is_ajaxed_delete : 1
               },
               success:function(res){
                  $("#ajax_loader").hide();
                  if(res.status == 'success'){
                     ajax_get_member_data('member_billing_tab.php','billing_tab','<?=$customer_id?>');
                     setNotifySuccess("Billing Profile deleted successfully!");
                  }else{
                     ajax_get_member_data('member_billing_tab.php','billing_tab','<?=$customer_id?>');
                     setNotifyError("Something went wrong.");
                  }
                     
               }
            });
         }, function(dismiss) {
            $("#agent_level").val(old_val);
            $("#agent_level").selectpicker('render');
         });
   });

   $(document).off('click','.change_default');
   $(document).on('click','.change_default',function(e){
      var $bill_id = $(this).attr('data-bill_id');
         swal({
            text: "Default Billing: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
            allowEscapeKey: false,
            allowOutsideClick: false
         }).then(function() {
            $.ajax({
               url:'member_billing_tab.php',
               method : 'POST',
               data : {
                  id:'<?=$customer_id?>',
                  bill_id:$bill_id,
                  action:"updDefault"
               },
               dataType:'json',
               success:function(res){
                  $("#ajax_loader").hide();
                  if(res.status == 'success'){
                     setNotifySuccess("Default Billing Profile Set successfully!");
                     ajax_get_member_data('member_billing_tab.php','billing_tab','<?=$customer_id?>');
                  }else{
                     setNotifyError("Something went wrong.");
                  }
               }
            });
         }, function(dismiss) {
         });
   });
});
</script>
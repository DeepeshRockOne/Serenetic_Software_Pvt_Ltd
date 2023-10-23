<p class="agp_md_title">Dependents</p>
<div class="table-responsive">
   <table class="<?=$table_class?>">
      <thead>
         <th>ID/Added Date</th>
         <th>First Name</th>
         <th>Last Name</th>
         <th>Relationship</th>
         <th>Date of Birth</th>
         <th>Age</th>
         <th class="text-center">Active Products</th>
         <th width="90px">Actions</th>
      </thead>
      <tbody>
         <?php if(!empty($member_dependents) && count($member_dependents) > 0) {
            foreach($member_dependents as $dep)  { ?>
            <tr>
               <td>
                  <a href="add_depedents.php?id=<?=$customer_id?>&dep_id=<?=$dep['id']?>&action=Edit" class="edit_depedents fw500 text-action">
                  <?=$dep['display_id']?></a> <br><?=getCustomDate($dep['created_at'])?>
               </td>
               <td><?=$dep['fname']?> </td>
               <td><?=$dep['lname']?></td>
               <td><?=$dep['crelation']?></td>
               <td><?=getCustomDate($dep['birth_date'])?></td>
               <td><?=calculateAge($dep['birth_date'])?></td>
               <td class="icons text-center">
                  <a href="depedents_active_product.php?id=<?=$dep['id']?>" class="depedents_active_product"><i class="fa fa-eye" aria-hidden="true"></i></a>
               </td>
               <td class="icons">
                  <a href="add_depedents.php?id=<?=$customer_id?>&dep_id=<?=$dep['id']?>&action=Edit"  data-toggle="tooltip" data-placement="top" title="Edit" class="edit_depedents"><i class="fa fa-edit" aria-hidden="true"></i></a>
                  <a href="javascript:void(0)" data-dep_id="<?=$dep['id']?>" data-toggle="tooltip" data-placement="top" title="Delete" data-products='<?=$dep['products']?>' data-name="<?=$dep['fname'].' '.$dep['lname'].' ('.$dep['display_id'].')'?>" class="delete_depedents"><i class="fa fa-trash" aria-hidden="true"></i></a>
               </td>
            </tr>
         <?php } }else echo "<tr><td colspan='8'>No record found!</td></tr>" ?>
      </tbody>
   </table>
</div>
<div class="text-right m-t-15">
   <a href="add_depedents.php?id=<?=$customer_id?>&action=Add" class="add_depedents btn btn-action">+ Dependent</a>
</div>
<hr>
<div style="display:none">
   <div class="panel panel-default  mn panel-shadowless" id="dep_product_div">
      <div class="panel-heading br-b">
         <h4 class="mn">Remove Dependent - <span class="fw300" id="dep_name"></span></h4>
      </div>
      <div class="panel-body">
         <div class="text-center m-b-15">
            Notice: To remove this dependent, terminate dependent on <a href="javascript:void(0);" class="text-action product_text"></a>.
         </div>
         <div class="text-center">
            <a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
         </div>
      </div>
   </div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
   $(".depedents_active_product").colorbox({iframe: true, width: '360px', height: '330px'});
   $(".edit_depedents").colorbox({iframe: true, width: '768px', height: '450px',overlayClose: false,escKey:false});
   $(".add_depedents").colorbox({iframe: true, width: '768px', height: '450px',overlayClose: false,escKey:false});

   $(document).off('click','.delete_depedents');
   $(document).on('click','.delete_depedents',function(e){
      var $product = $(this).attr('data-products');
      var $name = $(this).attr('data-name');
      var $dep_id = $(this).attr('data-dep_id');
      if($product !== '' && $product!==undefined){
         $(".product_text").text($product);
         $("#dep_name").text($name);
         $.colorbox({
            href:'#dep_product_div',
            inline: true, 
            width: '567px', 
            height: '210px',
            overlayClose: false,
            escKey:false
         });
      }else{
         swal({
            text: "Delete Dependent: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm",
            allowEscapeKey: false,
            allowOutsideClick: false
         }).then(function() {
            
            $.ajax({
               url:'add_depedents.php',
               method : 'POST',
               data : {
                  id:'<?=$customer_id?>',
                  dep_id:$dep_id,
                  action:"Delete",
                  is_delete : 1,
                  is_ajaxed : 1
               },
               success:function(res){
                  $("#ajax_loader").hide();
                  if(res.status == 'success'){
                     ajax_get_member_data('member_depedents_tab.php','dependents_tab','<?=$customer_id?>');
                     setNotifySuccess("Dependent deleted successfully!");
                  }else{
                     ajax_get_member_data('member_depedents_tab.php','dependents_tab','<?=$customer_id?>');
                     setNotifyError("Something went wrong.");
                  }
                     
               }
            });
         }, function(dismiss) {
            $("#agent_level").val(old_val);
            $("#agent_level").selectpicker('render');
         });
      }
   });
});
</script>
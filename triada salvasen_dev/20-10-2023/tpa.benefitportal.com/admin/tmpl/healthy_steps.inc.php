<div class="panel panel-default panel-block advance_info_div">
   <div class="panel-body">
      <div class="phone-control-wrap ">
         <div class="phone-addon w-90 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
         </div>
         <div class="phone-addon text-left">
            <div class="info_box_max_width">
            <p class="m-t-0 fw600 m-b-5 fs16">Instructions</p>
            <p class="fs14 mn">A healthy step is a one-time fee  charged on application. Global healthy steps will be assigned to all agents unless a variation is setup below.</p>
         </div>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div class="clearfix m-b-15">
         <div class="pull-left">
            <h4 class="m-t-7">Global Healthy Steps</h4>
         </div>
         <div class="pull-right">
            <a href="add_globalhealthy_steps.php" class="btn btn-action">+ Healthy Step</a>
         </div>
      </div>
      <div class="table-responsive">
         <table class="<?=$table_class?>">
            <thead>
               <tr>
                  <th>ID/Added Date</th>
                  <th>Name</th>
                  <th class="text-center">Products</th>
                  <th class="text-center">Commissionable</th>
                  <th>Effective Date</th>
                  <th class="text-center" >Termination Date</th>
                  <th width="200px">Status</th>
                  <th width="130px">Action</th>
               </tr>
            </thead>
            <tbody>
               <?php if($total_rows > 0 && !empty($fetch_rows)){ ?>
                  <?php foreach($fetch_rows as $row) {?>
                     <tr>
                        <td>
                           <a href="add_globalhealthy_steps.php?product_id=<?=$row['id']?>&health_id=<?=$row['health_id']?>" class="text-action fw500"><?=$row['product_code']?></a><br>
                           <?=$tz->getDate($row['create_date'],'m/d/Y')?>
                        </td>
                        <td><?=$row['name']?></td>
                        <td class="text-center"><a href="healthy_product.php?product_id=<?=$row['id']?>&health_id=<?=$row['health_id']?>" class="text-action healthy_product fw500"><?=$row['total_products']?></a></td>
                        <td class="text-center"><?=$row['is_fee_on_commissionable'] == 'Y' ? 'Yes' : 'No';?></td>
                        <td><?=getCustomDate($row['pricing_effective_date'])?></td>
                        <td class="text-center"><?=getCustomDate($row['pricing_termination_date'])?></td>
                        <td>
                           <div class="theme-form pr w-130">
                              <select class="form-control change_status" id="change_status_<?=$row['pid']?>" data-health_id="<?=$row['pfid']?>" name="change_status" data-old_status="<?=$row['status']?>">
                                 <option data-hidden="true"></option>
                                 <option value='Active' <?= $row['status'] == 'Active' ? 'selected' : ''; ?> >Active</option>
                                 <option value='Inactive' <?= $row['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                              </select>
                              <label>Select</label>
                           </div>
                        </td>
                        <td class="icons">
                           <a href="add_globalhealthy_steps.php?product_id=<?=$row['id']?>&health_id=<?=$row['health_id']?>&is_clone=Y" data-toggle="tooltip" data-trigger="hover" data-title="Duplicate"><i class="fa fa-clone"></i></a>
                           <a href="add_globalhealthy_steps.php?product_id=<?=$row['id']?>&health_id=<?=$row['health_id']?>" data-toggle="tooltip" data-trigger="hover" data-title="Edit"><i class="fa fa-edit"></i></a>
                           <a href="javascript:void(0)" onclick='delete_fee(<?=$row["pid"]?>,<?=$row["pfid"]?>)' data-toggle="tooltip" data-trigger="hover" data-title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                     </tr>
               <?php } } else{
                  echo "<tr><td colspan='8'>No rows found!</td></tr>";
               } ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<div class="panel panel-default panel-block">
   <div class="panel-body">
      <div id="variation_healthy_step">
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).off("click", ".search_btn");
   $(document).on("click", ".search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#search_div").css('display', 'inline-block');
   });

   $(document).off("click", ".search_close_btn");
   $(document).on("click", ".search_close_btn", function(e) {
     e.preventDefault();
     $("#search_div").hide();
     $(".search_btn").show();
     get_variation_healthy_step();
   });
   $(document).ready(function () {
      get_variation_healthy_step();
     $(".healthy_product").colorbox({iframe: true, width: '600px', height: '380px'});
   });

   $(document).off("change",'.change_status');
   $(document).on("change",'.change_status',function(e){
      var $val = $(this).val();
      var old_val = $(this).attr('data-old_status')
      var $id  = $(this).attr('id').replace("change_status_",'');
      var health_id  = $(this).attr('data-health_id');

      swal({
            text: 'Change Status: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
         }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
               url: "healthy_steps.php",
               dataType:'JSON',
               type: 'GET',
               data: {product_id: $id,health_id:health_id,status:$val,old_status:old_val,status_change:'Y'},
               success: function (res) {
                     $("#ajax_loader").hide();
                     if (res.status == 'success'){
                        setNotifySuccess(res.message);
                        window.location.reload();
                     }
               }
            });
         }, function (dismiss) {
            $('#change_status_'+$id).val(old_val);
         });
   });

   function delete_fee(product_id,health_id) {
          swal({
              text: 'Delete Record: Are you sure?',
              showCancelButton: true,
              confirmButtonText: 'Confirm',
              cancelButtonText: 'Cancel',
          }).then(function () {
            $href= "delete_healthy_steps.php?product_id="+product_id+"&health_id="+health_id
            $.colorbox({iframe: true,href:$href, width: '600px', height: '380px'});
          }, function (dismiss) {
              window.location.reload();
          })

      }
   get_variation_healthy_step = function(){
      $("#ajax_loader").show();
      $.ajax({
         url : 'get_variation_healthy_step.php',
         method : 'POST',
         dataType : 'html',
         data :{},
         success : function(res){
            $("#ajax_loader").hide();
               $("#variation_healthy_step").html(res);
         }
      })
   }
</script>
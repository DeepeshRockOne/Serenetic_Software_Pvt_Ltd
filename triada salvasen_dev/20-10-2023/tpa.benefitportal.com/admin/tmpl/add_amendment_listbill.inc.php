<div class="panel panel-default">
   <div class="panel-heading clearfix br-b">
      <div class="pull-left">
         <h4 class="m-t-7 m-b-0">Invoice - <span class="fw300"><?= $resListBill['list_bill_no'] ?></span></h4>
      </div>
      <div class="pull-right">
         <a href="javascript:void(0);" class="btn blue-link p-l-0 p-r-0" id="btn_print_list_bill_amendment"><i class="fa fa-print fs16" aria-hidden="true" style="display: none"></i></a>
         <a href="view_listbill_statement.php?list_bill=<?= $list_bill_id ?>" class="btn red-link">Back</a>
      </div>
   </div>
   <div class="panel-body" >
      <div class="row">
         <div style="max-width: 865px; margin: 0px auto; font-size: 12px;" >
            <form id="add_amendment_listbill_frm" action="">
               <input type="hidden" name="list_bill" id="list_bill" value="<?= $list_bill_id ?>">
               <div class="invoice_top_wrap" style="  border: 5px solid #74A7E5;">
                  <table cellpadding="0" cellspacing="0" width="100%">
                     <tbody>
                        <tr>
                           <td width="60px;" style="padding: 15px; background-color: #FAFBFB;">
                              <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" height="60px">
                           </td>
                           <td style="padding: 15px; background-color: #FAFBFB;">Make amendment(s) on the payments below by clicking the checkboxes next to the categories on the correct record.</td>
                        </tr>
                     </tbody>
                  </table>
                  <div style="padding: 30px;">
                     <h3 style="margin-top: 0px; font-weight: bold; font-size: 16px;">Search</h3>
                     <div class="theme-form">
                        <div class="form-group">
                           <select class="se_multiple_select" name="rep_id[]" id="rep_id"
                                   multiple="multiple">
                               <?php if (!empty($resMember)) { ?>
                                   <?php foreach ($resMember as $value) { ?>
                                       <option value="<?= $value['rep_id'] ?>" <?= !empty($rep_id) && in_array($value['rep_id'], $rep_id) ? 'selected' : '' ?> ><?= $value['rep_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'] ?></option>
                                   <?php } ?>
                               <?php } ?>
                           </select>
                           <label>Member Name/ID</label>
                        </div>
                        <hr>
                     </div>
                     <div id="list_bill_amendment_html"></div>
                  </div>
                  <div id="list_bill_amendment_summary_html"></div>
                  
               </div>
            </form>
            <div style="background-color:#fff;  text-align: center;   border-radius: 2px;    margin-top: 30px;">
              *Amendment(s) made on this invoice will be visible on current statement only and used solely for payment calculations. All amendment(s) must be made on the members’ record(s) to be reflective on the next invoice as balances will be forwarded to next statement. 
            </div>
            <div class="text-center m-t-30">
                  <a href="javascript:void(0);" class="btn btn-action" id="btn_confirm">Confirm</a>
                   <a href="view_listbill_statement.php?list_bill=<?= $list_bill_id ?>" class="btn red-link" id="btn_cancel">Cancel</a>
            </div>
         </div>
      </div>
   </div>
</div>
  

<script type="text/javascript">
   $(document).ready(function() {
      load_list_bill_amendment();
      load_list_bill_amendment_summary();
      
      $("#rep_id").multipleSelect({
         selectAll: false,
         onClick:function(e){
            load_list_bill_amendment();
            load_list_bill_amendment_summary();
         },
         onTagRemove:function(e){
            load_list_bill_amendment();
            load_list_bill_amendment_summary();
         }
      });
   });
   $(document).off("click","#btn_confirm");
   $(document).on("click","#btn_confirm",function(){
      $("#ajax_loader").show();
      $.ajax({
         url:'<?= $ADMIN_HOST ?>/ajax_add_amendment_listbill.php',
         dataType:'JSON',
         data:$("#add_amendment_listbill_frm").serialize(),
         type:'POST',
         success:function(res){
            $("#ajax_loader").hide();
            window.location.href= "view_listbill_statement.php?list_bill="+$("#list_bill").val();
         }
      });

   });

   $(document).off("click",".global_row_check");
   $(document).on("click",".global_row_check",function(){
      $date = $(this).attr('data-date');
      $category = $(this).attr('data-category');

      if($(this).is(":checked")){
         $(".row_check_"+$date+"_"+$category).prop("checked",true);
      }else{
         $(".row_check_"+$date+"_"+$category).prop("checked",false);
      }
      $checked = $(this).is(":checked");

      if($category == ''){
         $(".global_row_check_"+$date).each(function(){
            $tmp_date = $(this).attr('data-date');
            $tmp_category = $(this).attr('data-category');

            if($checked){
               $(this).prop("checked",true);
               $(".row_check_"+$tmp_date+"_"+$tmp_category).prop("checked",true);
            }else{
               $(this).prop("checked",false);
               $(".row_check_"+$tmp_date+"_"+$tmp_category).prop("checked",false);
            }
         });
      }
   });

   $(document).off("click",".row_check");
   $(document).on("click",".row_check",function(){
      $date = $(this).attr('data-date');
      $category = $(this).attr('data-category');
      $customer_id = $(this).attr('data-customer-id');

      if(!$(this).is(":checked")){
         $(".global_row_check_"+$date+"_"+$category).prop("checked",false);
      }

      $checked = $(this).is(":checked");

      if($category == ''){
         $(".row_check_cust_"+$date+"_"+$customer_id).each(function(){
            $tmp_date = $(this).attr('data-date');
            $tmp_category = $(this).attr('data-category');

            if($checked){
               $(this).prop("checked",true);
            }else{
               $(this).prop("checked",false);
               $(".global_row_check_"+$tmp_date+"_"+$tmp_category).prop("checked",false);
            }
         });
      }
   });

   load_list_bill_amendment = function(){
      $("#ajax_loader").show();
      $.ajax({
         url:'<?= $HOST ?>/ajax_load_list_bill_amendment.php',
         dataType:'JSON',
         data:$("#add_amendment_listbill_frm").serialize(),
         type:'POST',
         success:function(res){
            $("#ajax_loader").hide();
            $("#list_bill_amendment_html").html(res.html);
         }
      })

   }

   load_list_bill_amendment_summary = function(){
      $("#ajax_loader").show();
      $.ajax({
         url:'<?= $HOST ?>/ajax_load_list_bill_amendment_summary.php',
         dataType:'JSON',
         data:$("#add_amendment_listbill_frm").serialize(),
         type:'POST',
         success:function(res){
            $("#ajax_loader").hide();
            $("#list_bill_amendment_summary_html").html(res.html);
         }
      })

   }

 
</script>
<div class="panel panel-default">
   <div class="panel-heading clearfix br-b">
      <div class="pull-left">
         <h4 class="m-t-7 m-b-0">Invoice - <span class="fw300"><?= $resListBill['list_bill_no'] ?></span></h4>
      </div>
      <div class="pull-right">

        <?php if($resListBill['status']=='open'){ ?>
         <?php if($resListBill['due_amount'] > 0){ ?>
          <a href="<?=$HOST?>/pay_bill.php?location=admin&list_bill_id=<?=$list_bill_id?>" class="btn btn-action pay_bill">+ Pay Now</a>
         <?php } ?>
          <a href="add_amendment_listbill.php?list_bill=<?= $list_bill_id ?>" class="btn btn-default">+ Amendment</a>
        <?php } ?>
        <a href="javascript:void(0);" class="btn blue-link p-l-0 p-r-0" id="btn_print_list_bill"><i class="fa fa-print fs16" aria-hidden="true"></i></a>
        <a href="payment_listbills.php" class="btn red-link">Back</a>
      </div>
   </div>
   <div class="panel-body">
      <div class="row">
         <div style="max-width: 865px; margin: 0px auto; font-size: 12px;">
            
            <iframe id="list_bill_iframe" src="<?=$HOST?>/view_listbill_statement.php?list_bill=<?=$list_bill_id?>" onload="autoResize(this);" frameborder="0" width="100%" scrolling="no"></iframe>
            
            <form id="list_bill_notes_form" name="list_bill_notes_form" action="">
              <input type="hidden" name="list_bill" id="list_bill" value="<?=$list_bill_id?>">
              <div class="theme-form m-t-30">
                 <div class="form-group height_auto">
                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Notes…"><?= $notes ?></textarea>
                 </div>
              </div>
            </form>
            <div class="text-center m-t-30">
               <a href="javascript:void(0);" class="btn btn-action" id="add_notes">Save</a>
            </div>
         </div>
      </div>
   </div>
</div>
   
<script type="text/javascript">
   $(document).ready(function(){
      $(".pay_bill").colorbox({iframe:true, width:"800px",height:"500px"});
   })

   $(document).on('click','#btn_print_list_bill',function(){
         var frm = document.getElementById('list_bill_iframe').contentWindow;
         frm.focus();// focus on contentWindow is needed on some ie versions
         frm.print();
         return false;
  });

   function autoResize(i) {
        var iframe_obj = document.getElementById('list_bill_iframe');
        var iframeHeight =(iframe_obj).contentWindow.document.body.scrollHeight;
        (iframe_obj).height = iframeHeight + 20;

        if (typeof(i) !== 'undefined') {
            setTimeout(function () {
                autoResize();
            }, 10);
        }
    }

    $(document).off("click","#add_notes");
    $(document).on("click","#add_notes",function(){
      $("#ajax_loader").show();
      $.ajax({
         url:'<?= $ADMIN_HOST ?>/ajax_list_bill_notes.php',
         dataType:'JSON',
         data:$("#list_bill_notes_form").serialize(),
         type:'POST',
         success:function(res){
            $("#ajax_loader").hide();
            setNotifySuccess(res.msg);
         }
      });

   });
</script>
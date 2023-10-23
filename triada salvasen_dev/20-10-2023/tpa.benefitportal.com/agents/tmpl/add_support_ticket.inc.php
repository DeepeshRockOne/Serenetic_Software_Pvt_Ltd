<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4 class="mn">+ Ticket</h4>
  </div>
  <div class="panel-body">
    <form action="" id="add_eticket_form" name="add_eticket_form">
    <input type="hidden" name="userId" value="<?=$agent_id?>">
      <div class="theme-form">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="fname" class="form-control" value="<?=$rows['fname']?>" readonly>
              <label>First Name</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="lname" class="form-control"  value="<?=$rows['lname']?>" readonly>
              <label>Last Name</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="email" class="form-control"  value="<?=$rows['email']?>" readonly>
              <label>Email</label>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="text" name="phone" class="form-control"  value="<?=$rows['cell_phone']?>" readonly>
              <label>Phone</label>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <input type="text" name="company_name" class="form-control" value="<?=checkIsset($rows['company_name'])?>" readonly>
              <label>Company Name</label>
            </div>
          </div>
          <div class="col-sm-12">
          <div class="br-b m-b-25"></div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
              <select class="form-control" data-live-search="true" id="category" name="category">
                <option hidden></option>
                <?php if(!empty($category)) {
                  foreach($category as $cat){?>
                  <option value="<?=$cat['id']?>"><?=$cat['title']?></option>
                <?php } } ?>
              </select>
              <label>Category</label>
              <p class="error error_category"></p>
            </div>
          </div>
          <div class="col-sm-12">
          <!-- <div id="assignee_div"></div> -->
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <input type="text" name="subject" class="form-control">
              <label>Subject</label>
              <p class="error error_subject"></p>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group height_auto">
              <textarea class="form-control" rows="5" name="description" placeholder="Type something"></textarea>
              <p class="error error_description"></p>
            </div>
          </div>
          <div class="row">
            <div class="pull-left col-sm-4">
              <a href="javascript:void(0);" class="btn btn-action" onclick="$('#add_eticket_form').submit()">Send</a>
              <a href="javascript:void(0);" class="btn red-link" onclick="window.close();">Cancel</a>
            </div>
            <div class="pull-left col-sm-7">
              <input type="file" name="docFile" id="docFile" value="" class="hidden" onchange="$('#docFilelabel').text($(this).val());$('#removeFile').show()">
              <label for="" id="docFilelabel"></label><a href="javascript:void(0)" id="removeFile" style="display:none" onclick="$('#docFile').val('');$(this).hide();$('#docFilelabel').text('')">&nbsp;&nbsp;<i class='fa fa-times-circle'></i></a>
              <p class="error error_docFile"></p>
            </div>
            <div class="col-sm-1 pull-right">
              <a href="javascript:void(0);" class="text-light-gray" onclick="$('#docFile').click()"><i class="fa fa-paperclip fa-2x"></i></a>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
// $(document).off('change','#category');
// $(document).on('change','#category',function(e){
//   $("#assignee_div").html("");
//   $("#assignee_div").removeClass('form-group');
//   $.ajax({
//     url:"add_support_ticket.php",
//     data:{
//       is_ajaxed :1,
//       categoryId : $(this).val(),
//     },
//     dataType:'json',
//     type:'post',
//     beforeSend :function(e){
//       $("#ajax_loader").show();
//     },
//     success:function(res){
//       $("#ajax_loader").hide();
//       if(res.status == 'success'){
//           $("#assignee_div").html(res.data_html);
//       }else{
//         $("#assignee_div").html(res.data_html);
//       }
//       $("#assignee_div").addClass('form-group');
//       common_select();
//     }
//   });
// });

$('#add_eticket_form').ajaxForm({
  beforeSend: function(e) {
      $("#ajax_loader").show();
  },
  beforeSubmit:function(arr, $form, options){
  },
  url:"<?= $AGENT_HOST ?>/ajax_add_etickets.php",
  type: 'post',
  dataType: 'json',
  success: function(res) {
    $(".error").html('').hide();
    $("#ajax_loader").hide();
    if (res.status == 'success') {
      window.onunload = refreshParent;
      window.close();
    } else if (res.status == 'fail') {
      $.each(res.errors, function (index, error) {
        $('.error_' + index).html(error).show();
      });
    }
  }
});

function refreshParent() {
    window.opener.location.reload();
}
</script>
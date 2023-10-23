<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Ticket -  <span class="fw300">by Admin</span></h4>
	</div>
	<div class="panel-body">
		<p class="m-b-15"><strong><?=$_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']?> -</strong> <?=$_SESSION['admin']['display_id']?></p>
    <form action="" id="add_eticket_form" name="add_eticket_form">
      <div class="theme-form">
        <div class="form-group">
          <select class="form-control" data-live-search="true" id="userType" name="userType" onchange="getuserData($(this).val())">
            <option hidden></option>
            <option value="Admin" selected='selected'>Admin</option>
            <option value="Agent">Agent</option>
            <option value="customer">Member</option>
            <option value="group">Group</option>
          </select>
          <label>User type</label>
          <p class="error error_userType"></p>
        </div>
        <div id="selectuserDiv"></div>
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
        <div id="assignee_div"></div>
        <div class="form-group">
          <input type="text" name="subject" class="form-control">
          <label>Subject</label>
          <p class="error error_subject"></p>
        </div>
        <div class="form-group height_auto">
          <textarea class="form-control radius-zero" name="description" rows="7" placeholder="Type something"></textarea>
          <p class="error error_description"></p>
          <input type="file" name="docFile" id="docFile" value="" class="hidden" onchange="$('#docFilelabel').text($(this).val());$('#removeFile').show()">
          <div class="bg_light_gray p-10 table_br_gray br-t-n">
              <a href="javascript:void(0);" class="text-light-gray fs18 link_paperclip" onclick="$('#docFile').click()">
                <i class="fa fa-paperclip fa-rotate-90" aria-hidden="true"></i>
              </a>
              <label for="" id="docFilelabel"></label><a href="javascript:void(0)" id="removeFile" style="display:none" onclick="$('#docFile').val('');$(this).hide();$('#docFilelabel').text('')">&nbsp;&nbsp;<i class='fa fa-times-circle'></i></a>
              <p class="error error_docFile"></p> 
          </div>
        </div>
      </div>
      <div class="text-center m-t-20">
        <a href="javascript:void(0);" class="btn btn-action" id="savEticket">Save</a>
        <a href="javascript:void(0);" class="btn red-link" onclick='javascript:window.close();'>Close</a>
      </div>
    </form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(e){
  var $user = $("#userType").val();
  getuserData($user);
});
$(document).off('change','#category');
$(document).on('change','#category',function(e){
  $("#assignee_div").html("");
  $("#assignee_div").removeClass('form-group');
  $.ajax({
    url:"add_etickets.php",
    data:{
      is_ajaxed :1,
      categoryId : $(this).val(),
    },
    dataType:'json',
    type:'post',
    beforeSend :function(e){
      $("#ajax_loader").show();
    },
    success:function(res){
      $("#ajax_loader").hide();
      if(res.status == 'success'){
          $("#assignee_div").html(res.data_html);
      }else{
        $("#assignee_div").html(res.data_html);
      }
      $("#assignee_div").addClass('form-group');
      common_select();
    }
  });
});

$(document).off('click',"#savEticket");
$(document).on('click',"#savEticket",function(e){
  $("#add_eticket_form").submit();
});

$('#add_eticket_form').ajaxForm({
  beforeSend: function(e) {
      $("#ajax_loader").show();
  },
  beforeSubmit:function(arr, $form, options){
  },
  url:"<?= $ADMIN_HOST ?>/ajax_add_etickets.php",
  type: 'post',
  dataType: 'json',
  success: function(res) {
    $(".error").html('').hide();
    $("#ajax_loader").hide();
    if (res.status == 'success') {
      window.onunload = refreshParent;
      window.close();
      /*parent.$.colorbox.close();
      parent.setNotifySuccess('E-Ticket Added Successfully.');
      parent.ajax_submit();*/
    } else if (res.status == 'fail') {
      $.each(res.errors, function (index, error) {
        $('.error_' + index).html(error).show();
      });
    }
  }
});

getuserData = function(type){
  $("#selectuserDiv").html("");
  $("#selectuserDiv").removeClass('form-group');
  $.ajax({
  url:"add_etickets.php",
    data:{
      is_ajaxed :1,
      type:type,
    },
    dataType:'json',
    type:'post',
    beforeSend :function(e){
      $("#ajax_loader").show();
    },
    success:function(res){
      $("#ajax_loader").hide();
      if(res.status == 'success'){
          $("#selectuserDiv").html(res.data_html);
      }else{
        $("#selectuserDiv").html(res.data_html);
      }
      $("#selectuserDiv").addClass('form-group');
      common_select();
    }
  });
}
function refreshParent() 
{
    window.opener.location.reload(true);
}
</script>
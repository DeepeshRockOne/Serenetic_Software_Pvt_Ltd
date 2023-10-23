<div class="row chat_wrapper">
	<div class="col-sm-4 pn">
    <div class="panel profile-info <?=$status_class?>">
      <div class="panel-header">
          <div class="media">
            <div class="media-body"> 
              <p class="mn fs10"><strong>ADMIN</strong></p>             
              <h4 class="mn font-normal"><?=ucfirst($_SESSION['admin']['fname']).' '.ucfirst($_SESSION['admin']['lname'])?></h4>
            </div>
            <div class="media-right">
              <div class="dropdown">
                <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" class="btn btn-white btn-sm text-black" aria-expanded="false"><?=str_replace('_',' ',$status)?> &nbsp;<i class="fa fa-sort"></i>                             
                </button>
                <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dLabel">
                 <li><a href="javascript:void(0);" class="admin_circle_status <?=$status == 'Active' ? 'hidden' : '' ?>" data-status="Active">Active</a></li>
                  <li><a href="javascript:void(0);" class="admin_circle_status <?=$status == 'Away' ? 'hidden' : '' ?>" data-status="Away">Away</a></li>
                  <li><a href="javascript:void(0);" class="admin_circle_status <?=$status == 'Do_Not_Distrub' ? 'hidden' : '' ?>" data-status="Do_Not_Distrub" >Do Not Disturb</a></li>
                  <li><a href="javascript:void(0);" class="admin_circle_status <?=$status == 'Invisible' ? 'hidden' : '' ?>" data-status="Invisible">Invisible</a></li>
                </ul>
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="contact_list" id="contact_list">
    </div>
  </div>
  <div class="col-sm-8 pn">
    <div class="chat_right">
    <div class="ajex_loader" style="display: none;">
      <div class="loader"></div>
    </div>
    <div id="chat_right_window"></div>
    <div id="start_div" class="start_chat_wrap">
      <div class="start_chat_text">Click On Circle Name to Start Chat</div>
    </div>
    </div>
  </div>
</div>
<input type="hidden" name="last_id" id="last_id">
<input type="hidden" name="new_chat_id" id="new_chat_id">
<input type="hidden" name="circle_id" id="circle_id">
<script type="text/javascript">
var stop = setTimeInterval();
$(document).ready(function() {
  getContactList();
});

$(document).off('click',".show_old_record");
$(document).on('click',".show_old_record",function(e){
  e.preventDefault();
  getOldestChat();
  $(this).remove();
});

$(document).off('click',".show_ajex");
$(document).on('click',".show_ajex",function(e){
  e.preventDefault();
  $("#start_div").hide();
  var circle_id = $("#circle_id").val();
  var id = $(this).attr('data-id');
  if(id !== circle_id){
    $(".chat_right .ajex_loader").show();
  }
});
// $(document)
//     .on('focus.autoExpand', 'textarea.autoExpand', function(){
//         var savedValue = this.value;
//         this.value = '';
//         this.baseScrollHeight = this.scrollHeight;
//         this.value = savedValue;
//     })
//     .on('input.autoExpand', 'textarea.autoExpand', function(){
//         var minRows = this.getAttribute('data-min-rows')|0, rows;
//         this.rows = minRows;
//         rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 22);
//         this.rows = minRows + rows;
//     });

function getCircleChat(id){
  var circle_id = $("#circle_id").val();
  if(id !== circle_id){
      $.ajax({
      url:"circle_chat.php",
      data:{id:id,chat_ajax:1},
      type:"post",
      dataType:"json",
      beforeSend :function(e){
        $(".mCSB_container").html('');
      },
      success:function(res){
        getContactList();
        $(".ajex_loader").hide();
        $("#chat_right_window").html(res.html);
        $("#chat_window_scroll").mCustomScrollbar({
          axis:"y",
          theme:"dark" 
        }).mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
        $("#last_id").val(res.last_id);
        $("#circle_id").val(id);
      }
    });
   
  }else{
    $(".ajex_loader").hide();
  }
  $(".ajex_loader").hide();
}
function getContactList(){
  var id = $("#save_chat").attr('data-id');
  if(id!=='' && id!== undefined){
    id = $("#save_chat").attr('data-id');
  }else{
    id='';
  }
  $.ajax({
    url:"circle_chat.php",
    data:{id:id,contact_ajax:1},
    type:"post",
    dataType:'json',
    beforeSend :function(e){

    },
    success:function(res){
      $("#contact_list").html(res.html);
      $("#new_chat_id").val(res.new_chat_id);
    }
  });
}

function getNewChat(){
  var last_id = $("#last_id").val();
  var id = $("#save_chat").attr('data-id');
  $.ajax({
    url:"circle_chat.php",
    data:{id:id,last_id:last_id,get_new_chat:1},
    type:"post",
    dataType:'json',
    beforeSend :function(e){
    },
    success:function(res){      
      $(".mCSB_container").append(res.html);
      $("#last_id").val(res.last_id);
      $("#chat_window_scroll").mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
    }
  });
}

function getOldestChat(){
  var oldest_chat_id = $("#oldest_chat_id").val();
  var id = $("#save_chat").attr('data-id');
  $.ajax({
    url:"circle_chat.php",
    data:{id:id,oldest_chat_id:oldest_chat_id,get_oldest_chat:1},
    type:"post",
    dataType:'json',
    beforeSend :function(e){
      $(".chat_right .ajex_loader").show();
    },
    success:function(res){      
      $(".ajex_loader").hide();
      $(".mCSB_container").prepend(res.html);
      if($(".chat_day").siblings('.get_old_chat').html().trim() === ""){
        $(".get_old_chat").remove();
      }
      $("#oldest_chat_id").val(res.oldest_id);
      // $("#chat_window_scroll").mCustomScrollbar("scrollTo","top",{scrollInertia:0});
    }
  });
}

function getStateOfChat() {
    new_chat_id = $("#new_chat_id").val();
		$.ajax({
			type: "POST",
			url: "circle_chat.php",
			data: {new_chat_id:new_chat_id},
			dataType: "json",	
			success: function(res) {
        if(res.status === "true"){
          getContactList();
          getNewChat();
        }
      }
		});
}

//member status code start
$(document).off('click', '.admin_circle_status');
$(document).on("click", ".admin_circle_status", function(e) {
  e.stopPropagation();
  var id = '<?=$_SESSION['admin']['id']?>';
  var admin_circle_status = $(this).attr('data-status');
  swal({
      text: "Change Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
  }).then(function() {
      $.ajax({
        url: 'circle_chat.php',
        data: {
            id: id,
            status: admin_circle_status,
            admin_circle_status:1,
        },
        method: 'POST',
        dataType: 'json',
        beforeSend : function(e){
            $("#ajax_loader").show();
        },
        success: function(res) {
            $("#ajax_loader").hide();
            if (res.status == "success") {
              window.location.reload();
              setNotifySuccess(res.message);
            }else{
              setNotifyError(res.message);
            }
        }
      });
  }, function(dismiss) {
  })
});
//member status code end

function setTimeInterval(){
  return setInterval(getStateOfChat, 3000);
}
</script>
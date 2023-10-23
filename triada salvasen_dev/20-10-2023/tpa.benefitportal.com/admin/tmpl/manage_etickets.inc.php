<div class="panel panel-default panel-block" id="group_div">
</div>
<div class="panel panel-default panel-block" id="quick_reply_div">
</div>
<script type="text/javascript">
$(document).ready(function(){
    dropdown_pagination('group_div','quick_reply_div');

  get_eticket_groups();
  get_eticket_quick_reply();
});
get_eticket_groups = function() {
  var $name = $("#group_div #groupName").val();
  var $admin_id = $("#group_div #admindisplayId").val();
  $('#group_div').hide();
  $.ajax({
    url: 'get_eticket_groups.php',
    type: 'GET',
    data: {
      is_ajaxed: 1,
      name:$name,
      rep_id:$admin_id,
    },
    beforeSend:function(){
      $("#ajax_loader").show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#group_div').html(res).show();
      common_select();
    }
  });
}
get_eticket_quick_reply = function() {
  var $label = $("#quick_reply_div #replyName").val();
  $('#quick_reply_div').hide();
  $.ajax({
    url: 'get_eticket_quick_reply.php',
    type: 'GET',
    data: {
      is_ajaxed: 1,
      name : $label,
    },
    beforeSend:function(){
      $("#ajax_loader").show();
    },
    success: function(res) {
      $('#ajax_loader').hide();
      $('#quick_reply_div').html(res).show();
      common_select();
    }
  });
}
</script>
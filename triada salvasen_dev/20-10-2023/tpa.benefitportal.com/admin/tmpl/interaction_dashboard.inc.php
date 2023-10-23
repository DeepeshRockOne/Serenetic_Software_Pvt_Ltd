<div class="panel panel-default panel-block advance_info_div">
  <div class="panel-body">
  <div class="phone-control-wrap ">
    <div class="phone-addon w-130">
      <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="130px">
    </div>
    <div class="phone-addon text-left"> <p class="fs14 m-b-20"> Interactions are varying communications between an admin and another user account. An interaction could be phone discussion, viewing of account, update made, etc., that an admin does to an agent, group, or member account.</p>
    <div class="info_box info_box_max_width">
          <p class="fs14 mn">Set the interaction dropdown options for each user group below.  This will streamline consistency and simplify reporting when the admin only has these options to choose from per interaction.</p>
    </div>
  </div>
  </div>
  </div>
</div>
<?php 
  if(!empty($interactionArr)){
    foreach ($interactionArr as $interaction) { 
?>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix">
        <div class="pull-left">
          <h4 class="fs16 m-t-7"><?=ucfirst($interaction['user_type'])?> Interaction(s)</h4>
        </div>
        <div class="pull-right">
          <div class="m-b-15">
            <a href="manage_interaction.php?user_type=<?=$interaction['user_type']?>" data-user_type='<?=$interaction['user_type']?>' class="manage_interaction  btn btn-action">+ <?=ucfirst($interaction['user_type'])?> Interaction</a>
          </div>
        </div>
        </div>
        <div id="<?=$interaction['id']?>_div"></div>
    </div>
  </div>
<?php 
    } 
  } 
?>

<script type="text/javascript">
  $(document).ready(function(){
    dropdown_pagination('agent_interaction_div','group_interaction_div','member_interaction_div')

    agent_interaction();
    group_interaction();
    member_interaction();
  });

    $(document).off("click",".manage_interaction");
    $(document).on("click",".manage_interaction",function(e){
      e.preventDefault();
      $user_type=$(this).attr('data-user_type');
      $link = $(this).attr('href');
      $.colorbox({
          href: $link,
          iframe: true, 
          width: '550px', 
          height: '230px',
          onClosed:function(){
            if($user_type == 'agent'){
              agent_interaction();
            }else if($user_type == 'group'){
              group_interaction();
            }else if($user_type == 'member'){
              member_interaction();  
            }
          }
      });
    });

    $(document).off("click",".editInteraction");
    $(document).on("click",".editInteraction",function(){
        $id=$(this).attr('data-id');
        $user_type=$(this).attr('data-user_type');
        $link='manage_interaction.php?id='+$id+'&user_type='+$user_type;
        $.colorbox({
            href: $link,
            iframe: true, 
            width: '600px', 
            height: '250px',
            onClosed:function(){
                if($user_type == 'agent'){
                  agent_interaction();
                }else if($user_type == 'group'){
                  group_interaction();
                }else if($user_type == 'member'){
                  member_interaction();  
                }
            }
        });
    });

    $(document).off("click",".deleteInteraction");
    $(document).on("click",".deleteInteraction",function(){
        $id=$(this).attr('data-id');
        $user_type=$(this).attr('data-user_type');
        swal({
            text: 'Delete Record: Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url:'ajax_delete_interaction_type.php',
                dataType:'JSON',
                type:'POST',
                data:{id:$id},
                success:function(res){
                  if(res.status='success'){
                      window.parent.setNotifySuccess("Interaction Type Deleted Successfully");
                       if($user_type == 'agent'){
                          window.parent.agent_interaction();
                        }else if($user_type == 'group'){
                          window.parent.group_interaction();
                        }else if($user_type == 'member'){
                          window.parent.member_interaction();  
                        }
                  }
                  $("#ajax_loader").hide();
                }
            });
        }, function (dismiss) {
        });
    });

    agent_interaction = function() {
      $('#agent_interaction_div').hide();
      $.ajax({
        url: 'agent_interaction_type_listing.php',
        type: 'GET',
        data: {
          is_ajaxed: 1,
        },
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#agent_interaction_div').html(res).show();
          common_select();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    }
    group_interaction = function() {
      $('#group_interaction_div').hide();
      $.ajax({
        url: 'group_interaction_type_listing.php',
        type: 'GET',
        data: {
          is_ajaxed: 1,
        },
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#group_interaction_div').html(res).show();
          common_select();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    }
    member_interaction = function() {
      $('#member_interaction_div').hide();
      $.ajax({
        url: 'member_interaction_type_listing.php',
        type: 'GET',
        data: {
          is_ajaxed: 1,
        },
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#member_interaction_div').html(res).show();
          common_select();
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    }
</script>
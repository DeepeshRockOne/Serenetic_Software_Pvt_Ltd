<style type="text/css">
  /*.bootstrap-tagsinput input{
    display: block;
    float:   left;
  }*/
</style>
<?php if (!empty($_GET["is_ajax"])) { ?>
  <div class="clearfix"></div>
 
  <div class="row" id="report_data">     

    <?php if((!empty($member_search) || !empty($member_multiple_search)) && $member_menu) {?>
    <div class="col-lg-12">
      <div class="recent_members_section white-box">
        <h4 class="box-title">Search Members</h4>
        <iframe onload="$('#ajex_loader').show();" id="member_search_iframe" src="<?= $HOST; ?>/admin/search_members.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%"></iframe>
      </div>
    </div>
    <?php } ?>

    <?php if(!empty($order_search) && $order_menu) {?>
    <div class="col-lg-12">
      <div class="product_section white-box">
        <h4 class="box-title">Search Orders</h4>
        <iframe onload="$('#ajex_loader').show();" id="order_search_iframe" src="<?= $HOST; ?>/admin/search_orders.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%"></iframe>
      </div>
    </div>
    <?php } ?>

    <?php if(!empty($ticket_search) && $ticket_menu) {?>
    <div class="col-lg-12">
      <div class="product_section white-box">
        <h4 class="box-title">Search Interaction Management</h4>
        <iframe onload="$('#ajex_loader').show();" id="ticket_search_iframe" src="<?= $HOST; ?>/admin/search_ticket.php?<?= $_SERVER['QUERY_STRING'] ?>" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%"></iframe>
      </div>
    </div>
    <?php } ?>

    <?php if($member_menu) { ?>
      <div class="col-lg-12">
        <div class="members_section white-box">
          <h4 class="box-title">Recent Search Members</h4>
            <iframe onload="$('#ajex_loader').show();" id="recent_member_iframe" src="<?= $HOST; ?>/admin/search_members.php?recent=Y" frameborder="0" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" width="100%"></iframe>
        </div>
      </div>
    <?php } ?>

  </div>
  <div class="clearfix"></div>
<?php } else { ?>
  <div id="member_access">
    <div class="row">
      <div class="col-md-8">  
        <div class="white-box">
          <h3 class="box-title">Search Options</h3>
          <form id="search_from" role="form" action="" name="search_from" enctype="multipart/form-data">
            <input type="hidden" name="is_ajax" id="is_ajax" value="1"/>
            <div class="row">
              <div class="col-sm-6">
                <?php if($member_menu) { ?>
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search Members/ Name/ Email/ Phone" name="member_search" id="member_search"> 
                  </div>
                <?php } ?>
                <?php if($order_menu) { ?>
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search Orders" name="order_search" id="order_search"> 
                  </div>
                <?php } ?>
                <?php if($ticket_menu) { ?>
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search Interaction Management" name="ticket_search" id="ticket_search"> 
                  </div>
                <?php } ?>
              </div>
              <?php if($member_menu) { ?>
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="bs-taginput-textarea">
                      <textarea class="form-control multiple_search" id="tag_input" data-role="tagsinput" placeholder="" name="member_multiple_search"></textarea>
                      <span class="placeholder-content">Search multiple Member, Names, Email, Phone...</span>
                      <small>Separate each new entry by comma</small>
                      
                    </div>
                  </div>
                </div>
              <?php } ?>
              <div class="error" id="searching_error"></div>
              <div class="col-sm-12 text-right">
                <input type="button" value="Clear Search" id="clear_search_member" class="btn btn-default"/>
                <input type="button" value="Search" id="search_members" class="btn btn-info"/>
              </div>
            </div>  
          </form>
        </div>
      </div>
      <div class="col-md-4">
        <form class="add_note" name="add_note" id="add_note" action="POST" enctype="multipart/form-data">
          <div class="white-box">
            <h3 class="box-title">Reminder/Notes</h3>
            <div class="form-group">
              <input type="hidden" name="admin_reminder_id" value="<?=$admin_reminder['id']?>" id="admin_reminder_id">
              <textarea class="form-control" rows="6" placeholder="Add notes" id="note_desc" name="note_desc"><?=isset($admin_reminder['description']) ? $admin_reminder['description'] : ''?></textarea>
              <span id="note_desc_error" class="error error_preview"></span>           
            </div>
            <div class="text-right">
              <input type="button" value="Cancel" class="btn btn-default m-r-5" id="note_canel_btn"/>
              <input type="button" value="Save" id="note_btn" class="btn btn-info"/>
            </div>
          </div>
        </form>
      </div>  
    </div>
    <div class="outputData"></div>  
  </div>
  

  <script type="text/javascript">
    $(document).ready(function () {
      getSearchDetails();
      var counter = 0;
      $("#ticket_search").mask("E-999999");

      $('#member_access #search_members').click(function () {
        getSearchDetails();
      });
    });

    function getSearchDetails() {
      var params = $('#search_from').serialize();
      $.ajax({
        url: "member_access.php",
        method: "GET",
        data: params,
        beforeSend: function () {
            $("#ajax_loader").show();
        },
        success: function (res) {
          $("#ajax_loader").hide();
          $("#member_access .outputData").html(res);
        }
      });
    }

    $(document).on('click','#clear_search_member', function(e){
      $("#member_search").val('');
      $("#order_search").val('');
      $(".multiple_search").val('');
      $("#ticket_search").val('');
      $("#tag_input").tagsinput('destroy');
      $(".placeholder-content").show();
      $('#tag_input').tagsinput({
        confirmKeys: [44],
        addOnBlur : false
      });
      getSearchDetails();
    });

    $(function() {
      $('#tag_input').tagsinput({
        confirmKeys: [44],
        addOnBlur : false
      });
    });

    $(document).on('keypress','#member_search', function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        if($(this).val() != ''){
          getSearchDetails();
        }
      }
    });

    $(document).on('keypress','#order_search', function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        if($(this).val() != ''){
          getSearchDetails();
        }
      }
    });

    $(document).on('keypress','#ticket_search', function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode == '13'){
        if($(this).val() != '' && $(this).val() != 'E-______' ){
          getSearchDetails();
        }
      }
    });

    $(document).on('keypress','.bs-taginput-textarea', function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if($("#tag_input").val() != ''){
        if(keycode == '13'){
          getSearchDetails();
          string_value = $("#tag_input").val() + ',';
          $("#tag_input").tagsinput('destroy');
          $('#tag_input').tagsinput({
            confirmKeys: [44],
            addOnBlur : false
          });
          $.each(string_value.split(",").slice(0,-1), function(index, item) {
            $("#tag_input").tagsinput('add', item);
          });
        }
      } else {
        if(keycode != 9){
          $('.placeholder-content').hide();
        }
      }
    });

    $(document).on('click','#note_btn', function(){
      if($('#note_desc').val() != ''){
        params = $('#add_note').serialize();
        $("#note_desc_error").html('');
        $.ajax({
          url: "ajax_add_admin_note.php",
          method: "POST",
          data: params,
          beforeSend: function () {
            $("#ajax_loader").show();
          },
          success: function (res) {
            $("#ajax_loader").hide();
            if(res.status == 'success'){
              $('#admin_reminder_id').val(res.id);
              setNotifySuccess("Reminder is added successfully");
            } else {
              $("#note_desc_error").html('Note description is required.');
            }
          }
        });
      } else {
        $("#note_desc_error").html('Note description is required.');
      }
    }); 

    $(document).on('click','#note_canel_btn', function(){
      if($('#admin_reminder_id').val() != ""){
        params = $('#add_note').serialize();
        swal({
          text: 'Delete Reminder: Are you sure?',
          showCancelButton: true,
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel',
        }).then(function() {
          $.ajax({
            url: "ajax_add_admin_note.php?delete=Y",
            method: "POST",
            data: params,
            beforeSend: function () {
              $("#ajax_loader").show();
            },
            success: function (res) {
              $("#ajax_loader").hide();
              if(res.status == 'success'){
                $('#note_desc').val('');
                $("#note_desc_error").html('');
                $('#admin_reminder_id').val('');
                setNotifySuccess("Reminder is removed successfully");
                $('#note_desc').val('');
              } else {
                setNotifyError("Remider no found");
              }
            }
          });
        }, function(dismiss) {
          window.location.reload();
        });
      } else {
        $("#note_desc_error").html();
        $("#note_desc").val('');
      }
    });

    $(document).on('itemRemoved', '.bs-taginput-textarea', function(event) {
      if($('.multiple_search').val() == ''){
        $('.placeholder-content').show();
      }
    });

    $(document).on('focusout', '.bs-taginput-textarea', function(){
      if($("#tag_input").val() != ''){
        $('.placeholder-content').hide();
      } else {
        $('.placeholder-content').show();
        $("#tag_input").tagsinput('destroy');
        $('#tag_input').tagsinput({
          confirmKeys: [44],
          addOnBlur : false
        });
      }
    });

  </script>
<?php } ?>

<script type="text/javascript">
  resizeIframe = function ($height, $frm_name) {
    $("#"+$frm_name)[0].style.height = $height + 'px';
  };
  function parent_window_colorbox(params) {
    $.colorbox(params);
  }
</script>
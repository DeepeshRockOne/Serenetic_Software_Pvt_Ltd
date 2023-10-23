<style type="text/css">
  .resizable{
    max-height: 225px !important;
  }
</style>
<div class="panel panel-default">
  <div id="open_account_search">
    <form action="ajax_activity_feed_customer.php?id=<?php echo $customer_id; ?>&for=customer" method="POST" id="acc_feed_search_form" class="row">
      <div class="col-md-4">
          <div class="form-group">
            <label>Activity Type:</label>
            <select class="form-control select2-offscreen" name="activity_type" id="activity_type">
                <option value=""> select </option> 
                <?php $sel_activity_type = "SELECT entity_action FROM activity_feed WHERE entity_type='Customer' Group By entity_action";
                      $activity_type_res = $pdo->select($sel_activity_type);
                if($activity_type_res){ 
                    foreach ($activity_type_res as $val){ ?>
                <option value="<?php echo $val['entity_action'] ?>" <?php echo $val['entity_action'] == $activity_type?'selected':''; ?>><?php echo $val['entity_action']; ?></option>
                <?php } } ?>
            </select>    
          <div class="clearfix"></div>
          </div>
      </div>  
      <div class="col-md-4">
        <div class="form-group">
          <label>Quick Search:</label>
          <select class="form-control select2 placeholder select2-offscreen" name="acc_his_custom_date" id="acc_his_custom_date">              
            <option value="">&nbsp;</option>
            <option value="Today">Today</option>
            <option value="Yesterday">Yesterday</option>
            <option value="Last7Days">Last 7 Days</option>
            <option value="ThisMonth">This Month</option>
            <option value="LastMonth">Last Month</option>
            <option value="ThisYear">This Year</option>
            <option value="Range">Range</option>
          </select>
          <div class="clearfix"></div>
        </div>
      </div>
      <div id="dt_range">
        <div class="col-md-4">
          <div class="form-group">
            <label>From date:</label>
            <input type="text" name="acc_his_fromdate" id="acc_his_fromdate" value="" class="datetimepicker-range form-control">
          <div class="clearfix"></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label>To date:</label>
            <input type="text" name="acc_his_todate" id="acc_his_todate" value="" class="datetimepicker-range form-control">
          </div>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="form-group text-center">
        <div class="form-group">
          <button id="pay_search" name="search" type="button" class="btn btn-info" onclick="ajax_load_activity_feed()">
            <i class="fa fa-search"></i> Search
          </button>
          <button id="pay_viewall" name="viewall" type="button" class="btn btn-info" onclick="ajax_reload_activity_feed()">
            <i class="fa fa-search-plus"></i> View All
          </button>
        </div>
      </div>
    </form>
  </div>
    <div id="" class="tab-pane list-group">
      <div class="button-demo">
        <div class="panel panel-default p-l-10 p-r-10">                   
          <div class="custom_notes_area" style="display : <?php echo ($note_id == '') ? 'block' : 'none'?>">
              <form name="frm_note_add_main" id="frm_note_add_main" method="POST" action="general_detail_add_note.php?id=<?=$customer_id;?>" enctype="multipart/form-data">
                  <div class="panel panel-default brdr-gray p-10">
                  <div class="clearfix">    
                  <div class="col-md-4 p-l-0">  
                  <input type="text" name="note_title" placeholder="Add Title" class="form-control pull-left">
                  </div>               
                      <h4 class="pull-right m-b-0">Add New Note <a href="javascript:void(0)"  title="Add document with Note" id="note_file_link"><i class="m-l-20 fa fa-upload right_ic"></i></a></h4>
                      </div>
                      <div class="clearfix"></div>
                      <div class="post_style_input m-b-0">
                          <div class="form-group m-b-0">
                              <textarea name="note_text" cols="3" class="form-control resizable" rows="3" placeholder="Notes" id="note_text"></textarea>
                              <input type="file" name="note_file" id="note_file" style="display: none">
                              <!-- <span class="input-group-addon" id="basic-addon2"><a href="javascript:void(0)" id="post_note">Post</a></span> -->
                              <a href="javascript:void(0)" id="post_note" class="btn btn-info m-t-10">Post</a>
                          </div>
                      </div>
                  </div>
              </form>
            <div class="clearfix"></div>
            <!-- <div id="note_disp_container"></div>     -->            
          </div>
          <div class="custom_edit_notes_area" style="display : <?php echo ($note_id == '') ? 'none' : 'block'?>">
              <form name="frm_note_edit_main" id="frm_note_edit_main" method="POST" action="general_detail_edit_note.php?id=<?=$customer_id;?>" enctype="multipart/form-data">
                <input type="hidden" id="note_id" name="note_id" value="<?=$note_res['id']?>">
                  <div class="panel panel-default brdr-gray p-10">
                  <div class="clearfix">    
                  <div class="col-md-4 p-l-0">  
                  <input type="text" name="note_title" id="note_edit_title" placeholder="Edit Title" class="form-control pull-left" value="<?=$note_res['title']?>">
                  </div>               
                      <h4 class="pull-right m-b-0">Edit New Note <a href="javascript:void(0)"  title="Add document with Note" id="note_file_edit_link"><i class="m-l-20 fa fa-upload right_ic"></i></a></h4>
                      </div>
                      <div class="clearfix"></div>
                      <div class=" p-t-10"> 
                      <div class="clearfix file_name_div" style="display: <?php echo ($note_res['file_name']!= '')? 'block' : 'none'; ?>">
                      <div class="pull-left col-md-11 p-l-0">
                        <input type="text" name="note_file_name_text" id="note_file_name_text" placeholder="File Name" class="form-control " value="<?=$note_res['file_name']?>">
                        </div>
                        <div class="pull-right col-md-1 p-t-10">
                      <!-- <div class="col-md-10 p-l-0 p-t-10 pull-right"> -->
                          <a href="javascript:void(0)" class="delete_file"><i class="fa fa-close "></i></a>
                          <input type="hidden" name="delete_file_value" value="N" id="delete_file_value">
                        </div>
                        </div>
                      <!-- </div> -->
                      </div>
                      <div class="clearfix"></div>
                      <div class="post_style_input m-b-0">
                          <div class="form-group m-b-0">
                              <textarea name="note_text" cols="3" class="form-control resizable" rows="3" placeholder="Notes" id="note_edit_text"><?=$note_res['description']?></textarea>
                              <input type="file" name="note_file" id="note_file_edit" style="display: none">
                              <!-- <span class="input-group-addon" id="basic-addon2"><a href="javascript:void(0)" id="post_note">Post</a></span> -->
                              <a href="javascript:void(0)" id="edit_post_note" class="btn btn-info m-t-10">Edit</a>
                              <a href="javascript:void(0)" id="edit_post_note_cancel" class="btn btn-info m-t-10">Cancel</a>
                          </div>
                      </div>
                  </div>
              </form>
            <div class="clearfix"></div>
            <!-- <div id="note_disp_container"></div>     -->            
          </div>
        </div>
      </div>
    </div>

  <div class="inner_accor" id="sec_acct_his">
      Loading...
  </div>
</div>
<script type="text/javascript">
  

  $(document).ready(function() {
    $('#dt_range').hide();
    $('#acc_his_custom_date').change(function(){
      if($('#acc_his_custom_date').val() == 'Range') {
        $('#dt_range').fadeIn('slow');
      } else {
        $('#dt_range').fadeOut('slow');
      }
    });
  });

  

  $(document).ready(function() {
     ajax_load_activity_feed();
  });
  function ajax_load_activity_feed() {
    var activity_type = $("#activity_type").val();
    var acc_his_custom_date = $("#acc_his_custom_date").val();
    var acc_his_fromdate = $("#acc_his_fromdate").val(); 
    var acc_his_todate = $("#acc_his_todate").val();
    $('#ajax_loader').show();
    $.ajax({
      url: "ajax_activity_feed_customer.php",
      data: {id: '<?= $customer_id ?>',user_type:'<?=$user_type;?>',activity_type:activity_type,acc_his_custom_date:acc_his_custom_date,acc_his_fromdate:acc_his_fromdate,acc_his_todate:acc_his_todate,for:'customer'},
      type: 'POST',
      success: function(res) {
        $('#ajax_loader').hide();
        $("#sec_acct_his").html(res);
        //$('#sec_acct_his').slimscroll({height: 600, width: '100%',allowPageScroll :true});
        //load_account_history();
        $('.datetimepicker-range').datetimepicker({
          format: 'mm/dd/yyyy',
          minView: 'month',
          maxView: 'month',
          autoclose: true
        });
        $('.datetimepicker').hide();
      }
    });
  }
  function ajax_reload_activity_feed() {
    $("#activity_type").val("");
    $("#acc_his_custom_date").val("");
    $("#acc_his_fromdate").val(""); 
    $("#acc_his_todate").val("");
    $('#ajax_loader').show();
    $.ajax({
      url: "ajax_activity_feed_customer.php",
      data: {id: '<?= $customer_id ?>',user_type:'<?=$user_type;?>',for:'customer'},
      type: 'POST',
      success: function(res) {
        $('#ajax_loader').hide();
        $("#sec_acct_his").html(res);
        //$('#sec_acct_his').slimscroll({height: 600, width: '100%',allowPageScroll :true});
        //load_account_history();
        $('.datetimepicker-range').datetimepicker({
          format: 'mm/dd/yyyy',
          minView: 'month',
          maxView: 'month',
          autoclose: true
        });
        $('.datetimepicker').hide();
      }
    });
  }

</script>
<script type="text/javascript">
  $("#post_note").click(function(){
   
    $("#frm_note_add_main").submit();
  });

  $(".delete_file").click(function(){
    $("#delete_file_value").val("Y");
    $(".file_name_div").hide();
  });

  $("#edit_post_note").click(function(){
    $("#frm_note_edit_main").submit();
  });

  $("#note_file_link").click(function(){
    $("#note_file").click();
  });

  $("#note_file_edit_link").click(function(){
    $("#note_file_edit").click();
  });

  $('#frm_note_add_main').ajaxForm({
    //url: 'customer_detail_add_note.php',
    type: 'POST',
    dataType: 'json',
    success: function(res) {
      if (res.status == 'success') {
       swal({   
            title: "Success!",   
            text: "Note Saved Successfully",   
            type: "success"
            }).then(function(){  
            // alert("sdfsdf"); 
              // $("#frm_note_add_main").trigger("reset");
               // $("#open_account_search").reload();
               //document.getElementById("open_account_search").contentWindow.location.reload(true);
              //window.location = 'customer_detail.php?id=1144';
               window.location.reload();
            // $("#note_show").click();
       
        });
      } else if (res.status == 'fail') {
        swal({   
            title: "Error",   
            text: res.msg,   
            type: "warning",   
            }).then(function(){   

        });
      }
    }
  });


  $('#frm_note_edit_main').ajaxForm({
    //url: 'customer_detail_add_note.php',
    type: 'POST',
    dataType: 'json',
    success: function(res) {
      if (res.status == 'success') {
       swal({   
            title: "Success!",   
            text: "Note Saved Successfully",   
            type: "success"
            }).then(function(){  
            
              window.location.href = "customer_detail.php?id=" + <?=$_REQUEST['id']?>;
       
        });
      } else if (res.status == 'fail') {
        swal({   
            title: "Error",   
            text: res.msg,   
            type: "warning",   
            }).then(function(){   

        });
      }
    }
  });
  
  $("#edit_post_note_cancel").click(function(){
     window.location.href = "customer_detail.php?id=" + <?=$_REQUEST['id']?>;
  });

  $(document).on('click','.lead_opt_action',function(e){
    //$('.lead_opt_action').click(function(e){
        e.preventDefault();
        var obj = $(this);
        $.ajax({
          url: $(obj).attr('href'),
          type: 'GET',
          success: function (res) {
            $('#leadcontainer').fadeOut('slow', function () {
              $('#leadcontainer').html(res).fadeIn('slow');
            });
          }
        });
    });
</script>
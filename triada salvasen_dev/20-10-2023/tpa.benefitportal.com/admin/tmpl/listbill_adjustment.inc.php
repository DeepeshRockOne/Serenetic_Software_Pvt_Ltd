<div class="panel panel-default panel-block">
  <div class="panel-body advance_info_div">
    <div class="phone-control-wrap">
      <div class="phone-addon w-90 v-align-top">
        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="85px">
      </div>
      <div class="phone-addon text-left">
        <p class="fs14 m-b-20">Select the button below to choose the type of adjustment that you want to make.</p>
        <div class="info_box_max_width theme-form">
          <div class="phone-control-wrap">
            <div class="phone-addon">
              <div class="form-group height_auto m-b-15">
                <select class="form-control" id="adjustment_selection">
                  <option value=""></option>
                  <option value="Regenerate">Regenerate List Bill</option>
                  <option value="Adjustment">Global Adjustment</option>
                </select>
                <label>Select Adjustment Type</label>
              </div>
            </div>
            <div class="phone-addon w-80">
              <div class="form-group height_auto m-b-15">
                <a href="javascript:void(0);" class="btn btn-action btn-block" id="adjustment_selection_submit">Submit</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default" id="regenerate_div" style="display: none;">
  <form id="listbill_regenerate_frm" name="listbill_regenerate_frm" action="" method="POST">
    <div class="panel-body">
      <h4 class="m-t-0 m-b-25">Regenerate List Bill</h4>
      <div class="row theme-form">
        <div class="col-sm-6">
          <div class="form-group">
            <select class="form-control" data-live-search="true" id="regenerate_group_id" name="regenerate_group_id">
                <option data-hidden="true"></option>
                <?php if(!empty($resGroup)){ ?>
                  <?php foreach ($resGroup as $key => $value) { ?>
                    <option value="<?= $value['id'] ?>"><?= $value['rep_id'] ?> - <?= $value['business_name'] ?></option>    
                  <?php } ?>
                <?php } ?>
            </select>
            <label>Group Name/ID</label>
            <p class="error" id="error_regenerate_group_id"></p>
          </div>
          <div class="form-group">
            <select class="form-control" data-live-search="true" id="list_bill_id" name="list_bill_id">
            </select>
            <label>List Bill ID</label>
            <p class="error" id="error_list_bill_id"></p>
          </div>
        </div>
      </div>
      <div class="text-center">
      <p class="m-b-30 p-b-10 fs16 m-t-15">By clicking 'Regenerate' below, old list bill will be removed from view of group and a new list bill based on current data inside the system will generate.  The existing list bill will remain for viewing purposes only.</p>
      <div class="clearfix">
        <a href="javascript:void(0);" class="btn btn-action" id="save_regenerate">Regenerate</a>
        <a href="javascript:void(0);" class="btn red-link" id="cancel_regenerate">Cancel</a>
      </div>
    </div>
  </form>
  </div>
</div>

<div class="panel panel-default" id="adjustment_div" style="display: none;">
  <form id="listbill_adjustment_frm" name="listbill_adjustment_frm" action="" method="post">
    <div class="panel-body">
      <h4 class="m-t-0 m-b-25">Global Adjustment</h4>
      <div class="row theme-form">
        <div class="col-sm-6">
          <div class="form-group">
            <select class="form-control" data-live-search="true" id="group_id" name="group_id">
              <option data-hidden="true"></option>
              <?php if(!empty($resGroup)){ ?>
                <?php foreach ($resGroup as $key => $value) { ?>
                  <option value="<?= $value['id'] ?>"><?= $value['rep_id'] ?> - <?= $value['business_name'] ?></option>    
                <?php } ?>
              <?php } ?>
            </select>
            <label>Group Name/ID</label>
            <p class="error" id="error_group_id"></p>
          </div>
          <div class="form-group">
            <select class="form-control" data-live-search="true" id="adjustment_list_bill_id" name="adjustment_list_bill_id">
            </select>
            <label>List Bill ID</label>
            <p class="error" id="error_adjustment_list_bill_id"></p>
          </div>
          <div class="form-group">
            <select class="form-control" id="adjustment_type" name="adjustment_type">
              <option data-hidden="true"></option>
              <option value="Credit">Credit (Positive)</option>
              <option value="Debit">Debit (Negative)</option>
            </select>
            <label>Transaction Type</label>
            <p class="error" id="error_adjustment_type"></p>
          </div>
          <div class="form-group">
            <input type="text" name="adjustment_amount" id="adjustment_amount" class="form-control formatPricing">
            <label>Amount</label>
            <p class="error" id="error_adjustment_amount"></p>
          </div>
          <div class="form-group height_auto">
            <textarea class="form-control" rows="3" placeholder="Note" id="adjustment_note" name="adjustment_note"></textarea>
            <p class="error" id="error_adjustment_note"></p>
          </div>
        </div>
      </div>
      <div class="text-center">
        <div class="clearfix m-t-15">
          <a href="javascript:void(0);" class="btn btn-action" id="save_adjustment">Save</a>
          <a href="javascript:void(0);" class="btn red-link" id="cancel_adjustment">Cancel</a>
        </div>
      </div>
    </div>
  </form>
</div>


<script type="text/javascript">
  $(document).off("click","#adjustment_selection_submit");
  $(document).on("click","#adjustment_selection_submit",function(e){
    e.stopPropagation();
    $val = $("#adjustment_selection").val();

    $("#regenerate_div").hide();
    $("#adjustment_div").hide();

    if($val=="Regenerate"){
      $("#regenerate_div").show();
    }else if($val == "Adjustment"){
      $("#adjustment_div").show();
    }
  });

  $(document).off("change","#regenerate_group_id");
  $(document).on("change","#regenerate_group_id",function(e){
    e.stopPropagation();
    load_listbill($(this).val(),'Regenerate');
  });
  

  $(document).off("click","#save_regenerate");
  $(document).on("click","#save_regenerate",function(e){
    e.stopPropagation();
    $('.error').html('');
    $("#ajax_loader").show();
    $.ajax({
      url:'<?= $ADMIN_HOST ?>/ajax_listbill_regenerate.php',
      dataType:'JSON',
      type:'POST',
      data:$("#listbill_regenerate_frm").serialize(),
      success:function(res){
        $("#ajax_loader").hide();

        if(res.status=="success"){
            window.location.href = "payment_listbills.php";
        }else{
          var is_error = true;
          $.each(res.errors, function (index, value) {
            $('#error_' + index).html(value).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 50;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    })
  });
  $(document).off("click","#cancel_regenerate");
  $(document).on("click","#cancel_regenerate",function(e){
    e.stopPropagation();
    window.location.href = "payment_listbills.php";
  });

  $(document).off("change","#group_id");
  $(document).on("change","#group_id",function(e){
    e.stopPropagation();
    load_listbill($(this).val(),'Adjustment');
  });



  $(document).off("click","#save_adjustment");
  $(document).on("click","#save_adjustment",function(e){
    e.stopPropagation();
    $('.error').html('');
    $("#ajax_loader").show();
    $.ajax({
      url:'<?= $ADMIN_HOST ?>/ajax_listbill_adjustment.php',
      dataType:'JSON',
      type:'POST',
      data:$("#listbill_adjustment_frm").serialize(),
      success:function(res){
        $("#ajax_loader").hide();

        if(res.status=="success"){
            window.location.href = "payment_listbills.php";
        }else{
          var is_error = true;
          $.each(res.errors, function (index, value) {
            $('#error_' + index).html(value).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 50;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    })
  });
  $(document).off("click","#cancel_adjustment");
  $(document).on("click","#cancel_adjustment",function(e){
    e.stopPropagation();
    window.location.href = "payment_listbills.php";
  });

  $(document).ready(function(){
    $("#listbill_adjustment_frm .formatPricing").priceFormat({
        prefix: '',
        suffix: '',
        centsSeparator: '.',
        thousandsSeparator: ',',
        limit: false,
        centsLimit: 2,
    });
  });

  load_listbill = function($group_id,$type){
    $('.error').html('');
    $("#ajax_loader").show();
    $.ajax({
      url:'<?= $ADMIN_HOST ?>/ajax_load_listbill.php',
      dataType:'JSON',
      type:'POST',
      data:{group_id:$group_id},
      success:function(res){
        $("#ajax_loader").hide();
        if($type=="Adjustment"){
          $("#adjustment_list_bill_id").html(res.html);
          $("#adjustment_list_bill_id").selectpicker('refresh');
        }else if($type == "Regenerate"){
          $("#list_bill_id").html(res.html);
          $("#list_bill_id").selectpicker('refresh');
        }
      }
    })
  }
</script>
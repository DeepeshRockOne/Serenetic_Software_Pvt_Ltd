<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Adjustment -<span class="fw300"> Period - <?=$startPayPeriod?> - <?=$endPayPeriod?></span></h4>
	</div>
	<div class="panel-body">
    <form id="adjustFrm" name="adjustFrm" href="ajax_adjustment_commission.php">
      <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1">
      <input type="hidden" name="pay_period" id="pay_period" value="<?=$pay_period?>">
      <input type="hidden" name="commission_duration" id="commission_duration" value="<?=$commission_duration?>">
		<div class="theme-form">
      <div class="form-group">
        <select class="form-control" name="agent_id" id="agent_id" data-live-search="true">
            <option></option>
          <?php if(!empty($resAgents)){ 
            foreach ($resAgents as $agents) {
          ?>
          <option value="<?=$agents['id']?>"><?=$agents["agentName"]?> (<?=$agents["agentRepId"]?>)</option>
          <?php }
            } 
          ?>
        </select>
        <label>Agent ID/Name<em>*</em></label>
        <span class="error" id="error_agent_id"></span>
      </div>
      <div class="form-group">
      	<select class="form-control" name="adjustment_type" id="adjustment_type">
      		<option></option>
      		<option value="overallComm">Overall Commissions</option>
      		<option value="orderSpec">Order Specific</option>
      	</select>
      	<label>Adjustment Type<em>*</em></label>
        <span class="error" id="error_adjustment_type"></span>
      </div>
      <div class="form-group" id="specOrderDiv">
        <select class="se_multiple_select" name="orderIds[]" id="orderSel" multiple="multiple" >
          
        </select>
        <label>Order ID<em>*</em></label>
        <span class="error" id="error_orderIds"></span>
      </div>
      <div class="form-group">
      	<select class="form-control" name="transaction_type" id="transaction_type">
      		<option></option>
      		<option value="Debit" class="text-action">Debit (Decrease)</option>
      		<option value="Credit">Credit (Increase)</option>
      	</select>
      	<label>Transaction Type<em>*</em></label>
        <span class="error" id="error_transaction_type"></span>
      </div>

      <div class="form-group">
      	<div class="input-group">
			    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
			    <div class="pr">
			    <input type="text" class="form-control" name="amount" id="amount" onkeypress="return isNumber(event)" >
			    <label>Amount 0.00<em>*</em></label>
		     </div>
	      </div>
         <span class="error" id="error_amount"></span>
      </div>

      <div class="form-group height_auto">
      	 <textarea class="form-control" placeholder="Note*" rows="3" id="note" name="note" maxlength="255"></textarea>
        <span class="error" id="error_note"></span> 
      </div>

      <div class="text-center">
        <button class="btn btn-action" type="submit">Save</button>
     	  <a href="javascript:void(0);" class="btn red-link" onClick="parent.$.colorbox.close();
              return false;">Cancel</a>
      </div>
		</div>
	</div>
</div>


<script type="text/javascript">
$(document).ready(function() {
  common_select();
  $("#specOrderDiv").hide();
  $("#orderSel").multipleSelect({
       selectAll: false,
  });

  $(document).off("change","#adjustment_type");
  $(document).on("change","#adjustment_type",function(e){
    $adjustType = $(this).val();
    if($adjustType == "orderSpec"){
      getAgentOrders();
      $("#specOrderDiv").show();
    }else{
      $("#specOrderDiv").hide();
    }

  });

  $(document).off("change","#agent_id");
  $(document).on("change","#agent_id",function(e){
    $agent_id = $(this).val();
   
    if($agent_id != ''){
      if($("#adjustment_type").val() == "orderSpec"){
         getAgentOrders();
      }
    }
  });

  $("#adjustFrm").on("submit",function(e){
    e.preventDefault();
     adjustCommission();
  });


  $(document).on("change","#transaction_type",function(){
    $value=$(this).val();
    $("#amount").removeClass("text-success");
    $("#amount").removeClass("text-action");

    if($value=="Credit"){
      $("#amount").addClass("text-success");
    }else if($value == "Debit"){
      $("#amount").addClass("text-action");
    }else{
      $("#amount").val('');
    }
  });
});
  
  function adjustCommission() {
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    $(".error").html('').hide();
    var params = $('#adjustFrm').serialize();
      $.ajax({
        url: 'ajax_adjustment_commission.php',
        type: 'GET',
        dataType:'JSON',
        data: params,
        beforeSend: function () {
          $("#ajax_loader").show();
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if(res.status=='success'){
              parent.setNotifySuccess("Adjustment Added Successfully");
             setTimeout(function(){
               window.parent.location.reload();
             }, 500);
          }else if(res.status=='fail'){
            var is_error = true;
             $.each(res.errors, function (index, value) {
                 $('#error_' + index).html(value).show();
                 if (is_error) {
                     is_error = false;
                     var offset = $('#error_' + index).offset();
                     var offsetTop = offset.top;
                     var totalScroll = offsetTop + 350;
                     $('body').animate({
                         scrollTop: totalScroll
                     }, 1200);
                 }
             });
            
          }else{
            parent.$.colorbox.close();
            parent.setNotifyError("Something went wrong");
          }
        }
      });
      return false;
  }

  function getAgentOrders(){
     var params = $("#adjustFrm").serialize();
      $.ajax({
        url: 'ajax_agent_adjustment_orders.php',
        type: 'GET',
        data: params,
        dataType:'html',
        beforeSend:function(){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $('#ajax_loader').hide();
          $('#orderSel').html(res)
          common_select();
           $('#orderSel').multipleSelect('refresh');
        }
      });
  }

  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    return true;
  }
</script>
<div class="panel panel-default panel-block panel-space">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">+ Healthy Step - <span class="fw300">Variation</span></h4>
    </div>
  </div>
  <div class="panel-body">
    <form action="" id="assign_variation_fees">
      <input type="hidden" name="agent_id" id="agent_id" value="<?=checkIsset($resource_res['agent_id'])?>">
      <input type="hidden" name="cagent_id" id="cagent_id" value="">
      <input type="hidden" name="fee_ids" id="fee_ids" value="<?=checkIsset($resource_res['product_ids'])?>">
      <input type="hidden" name="fee_id" id="fee_id" value="<?=checkIsset($resource_res['pf_id'])?>">
      <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
      <div class="theme-form">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group height_auto">
              <select class="form-control" name="receiving_agent" data-live-search="true" id="receiving_agent" <?php if(!empty($resource_res['agent_id']) && $is_clone=="N") echo "disabled='true'"; ?>>
                <option data-hidden="true"></option>
                <?php if( $is_clone=="N") { ?>
                <?php if(!empty($agent_res) && empty($resource_res['agent_id'])){

                  foreach($agent_res as $agent){ ?> 
                    <option value="<?=$agent['id']?>"><?=$agent['rep_id'].' - '.$agent['fname'].' '.$agent['lname']?></option>
                 <?php } } else { ?>
                  <option value="<?=checkIsset($resource_res['agent_id'])?>" selected="selected"><?=checkIsset($resource_res['rep_id']).' - '.checkIsset($resource_res['fname']).' '.checkIsset($resource_res['lname'])?></option>
                <?php } } else{ 
                  if(!empty($agent_res)){
                  foreach($agent_res as $agent){ ?> 
                    <option value="<?=$agent['id']?>"><?=$agent['rep_id'].' - '.$agent['fname'].' '.$agent['lname']?></option>
                <?php } } } ?>
              </select>
              <label>Agent Receiving Variation</label>
              <p class="error" id="error_agent_id"></p>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group height_auto">
              <input type="text" name="display_id" class="form-control" value="<?=$display_id?>">
              <label>ID (Must be unique)</label>
              <p class="error" id="error_display_id"></p>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group height_auto">
              <select class="form-control" id="status_main" name="status_main">
                <option value="Active" <?=$prd_status == 'Active' ? 'selected="selected"' : '';?>>Active</option>
                <option value="Inactive" <?=$prd_status == 'Inactive' ? 'selected="selected"' : '';?>> Inactive</option>
              </select>
              <label>Status</label>
              <p class="error" id="error_status_main"></p>
            </div>
          </div>
        </div>
        <p class="fw500 m-t-20 fs16 m-b-20">Variation Healthy Steps</p>
      </div>
      <div id="variation_healthy_step">
      </div>
      <p class="error" id="error_healthy_steps"></p>
      <div class="text-center clearfix m-b-30">
        <button type="button" class="btn btn-action" id="save_variation">Save</button>
        <a href="healthy_steps.php" class="btn red-link">Cancel</a>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var fee_ids = $("#fee_ids").val();  
  var fee_id = $("#fee_id").val();  
    variation_healthy_step(fee_ids,'',fee_id);
});

$(document).off('change','#receiving_agent');
$(document).on('change','#receiving_agent',function(e){
  $("#agent_id").val($(this).val());
  $("#cagent_id").val($(this).val());
});

$(document).off('click','#save_variation');
$(document).on('click','#save_variation',function(e){
	var $data = $("#assign_variation_fees").serializeArray();
  $('.error').html("");
  $("#ajax_loader").show();
	$.ajax({
		url:'ajax_assign_healthy_step_variation.php',
		data:$data,
		dataType : 'json',
		type:'post',
		success :function(res){
			$(".error").html('');
      $("#ajax_loader").hide();
			if(res.status == 'success'){	
        setNotifySuccess(res.message,true);			
				window.location = res.redirect_url;
        fRefresh();
			}else if(res.status == 'fail'){
        $('.error').show();
        var is_error = true;
				$.each(res.errors, function(index, value) {
					$('#error_' + index).html(value).show();
              if(is_error){
                  var offset = $('#error_' + index).offset();
                  if(typeof(offset) === "undefined"){
                      console.log("Not found : "+index);
                  }else{
                      var offsetTop = offset.top;
                      var totalScroll = offsetTop - 195;
                      $('body,html').animate({
                          scrollTop: totalScroll
                      }, 1200);
                      is_error = false;
                  }
              }
          });
      }
		}
	});
});


$(document).off('click','#healthy_steps_popup');
$(document).on('click','#healthy_steps_popup',function(e){
  var $val  = $(this).attr('data-href');
  var agent_name = $("#receiving_agent :selected").text();
  var agent_id = $("#receiving_agent").val();
  $href = $val+'&agent_name='+agent_name+'&agent_id='+agent_id;
  if(agent_id !=='' && agent_id !== undefined){
    $("#healthy_steps_popup").colorbox({
        href:$href,
        iframe: true,
        width: '800px',
        height: '450px'
      });
  }else{
    $("#healthy_steps_popup").colorbox({
        href:$val,
        iframe: true,
        width: '800px',
        height: '450px'
      });
    return false;
  }
});
variation_healthy_step = function(fee_ids='',delete_id='',fee_id=''){
  $("#ajax_loader").show();
  var agent_id= $("#agent_id").val();
  var added_fee_ids = $("#fee_ids").val();
  $.ajax({
    url : 'ajax_load_variation_healthy_steps.php',
    method : 'get',
    data :{is_ajaxed : 1,fee_ids:fee_ids,added_fee_ids:added_fee_ids,agent_id:agent_id,delete_id:delete_id,fee_id:fee_id},
    dataType : 'json',
    success : function(res){
      $("#ajax_loader").hide();
      if(res.status == 'success'){
        $("#variation_healthy_step").html(res.varition_fee_div);
        fRefresh();
      }
    }
  });
}

$(document).off('click', '#variation_healthy_step ul.pagination li a');
$(document).on('click', '#variation_healthy_step ul.pagination li a', function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $("#is_ajaxed").val(1);
    $('#variation_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        data:{is_ajaxed:1},
        success: function(res) {
            $('#ajax_loader').hide();
            $('#variation_healthy_step').html(res.varition_fee_div).show();
            fRefresh();
        }
    });
});

function delete_fee(id){
  swal({
        text: 'Delete Record: Are you sure?',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
    }).then(function() {
      var fee_ids = $("#variation_healthy_step #available_fees").val();
      variation_healthy_step(fee_ids,id);
    }, function(dismiss) {})
}
</script>
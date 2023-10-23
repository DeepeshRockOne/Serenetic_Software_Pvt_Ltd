<style type="text/css">
	.assigned_healthy_step .assigned_healthy_left input[type='radio']:checked + span{color:#5694cc;}
	.assigned_healthy_step .assigned_healthy_left input[type=radio]{opacity: 0; margin: 0px; position: absolute;}
</style>
<div class="panel panel-default panel-block assigned_healthy_step">
	<div class="panel-heading">
		<h4 class="mn">Assigned Healthy Steps - <span class="fw300"><?=$agent_name?></span></h4>
	</div>
	<div class="panel-body">
	<?php if($agent_name == ''){
			echo "<span class='error'>Please Select Any Agent!</span>";
			exit;
		} ?>
		<p class="fw500 m-b-20">Select options to assign to this agent :</p>
		<?php if(!empty($healthy_step)) { ?>
			<?php $i=0; ?>
		<div class="row br-t br-b">
			<form name="variation_step" id="variation_step">
			<input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">
			<input type="hidden" name="fee_id" id="fee_id" value="<?=$fee_id?>">
				<div class="col-sm-3 assigned_healthy_left" >
					<?php foreach($healthy_step as $step){?>
						<?php $i++; ?>
					<label class="p-10 mn"><input type="checkbox" name="healthy_steps[<?=$step['id']?>]" id="steps_<?=$step['id']?>" class="select_step js-switch" value="<?=$step['id']?>" <?=$i==1 ? "checked='checked'" : ""?>>
						<span for="steps_<?=$step['id']?>"><?=$step['name'].' ('.$step['stepPrdCode'].')'?></span></label>
					<?php } ?>
				</div>
				<div class="col-sm-9">
					<?php foreach($healthy_step as $step){?>
					<div class="p-15 show_details" id="show_details_<?=$step['id']?>" <?=$i==1 ? "style='display:block'" : "style='display:none'"?>>
						<h5 class="m-b-25"><?=$step['name']?></h5>
						<div class="theme-form">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group height_auto">
									<div class="group_select">
										<select class="se_multiple_select" name="products<?=$step['id']?>[<?=$step['id']?>]"  id="products_<?=$step['id']?>" multiple="multiple" disabled="disabled">
										<?php if(!empty($productRes)){ ?>
											<?php foreach ($productRes as $key=> $category) { ?>
												<?php if(!empty($category)){ ?>
													<optgroup label='<?= $key ?>'>
														<?php foreach ($category as $pkey => $row) { ?>
														<option value="<?= $row['id'] ?>" <?= (!empty($step['products']) && in_array($row['id'], explode(',',$step['products']))) ? 'selected="selected"' : '' ?> >
															<?= $row['name'] .' ('.$row['product_code'].')'?>    
														</option>
														<?php } ?>
													</optgroup>
												<?php } ?>
											<?php } ?>
										<?php } ?>
										</select>
										<label>Products</label>
									</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group height_auto">
										<div class="input-group"> 
											<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
											<div class="pr">
												<input type="text" class="form-control" name="effective_date<?=$step['id']?>[<?=$step['id']?>]" id="effective_date_<?=$step['id']?>" value="<?=getCustomDate($step['pricing_effective_date'])?>" readonly="">
												<label>Effective Date(MM/DD/YYYY)</label>
											</div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<div class="form-group ">
									<input type="text" name="fee_price<?=$step['id']?>[<?=$step['id']?>]" class="form-control" value="<?=checkIsset($step['price'])?>" readonly="">
									<label>Fee Price</label>
								</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group ">
									<input type="text" name="commissionable_amount<?=$step['id']?>[<?=$step['id']?>]" class="form-control" value="<?=checkIsset($step['commission_amount'])?>" readonly="">
									<label>Commissionable Price</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="p-15" id="default_view">
						<div class="text-center text-light-gray">Select Healthy Step on left to view</div>
					</div>
				</div>

			</form>
		</div>
		<p class="error" id="error_healthy_steps"></p>
		<p class="error" id="error_agent_id"></p>
		<?php } ?>
	</div>
	<div class="text-center">
	<?php if($type!='show') {?><button type='button' class="btn btn-action" id="assign_variation">Assign</button><?php } ?>
		<a href="javascript:void(0)" class="btn red-link" onclick="window.parent.$.colorbox.close()"><?=$type!='show' ? 'Cancel' : 'Close'?></a>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $(".se_multiple_select").multipleSelect({
  	width: '100%'
  });
  getCheckedSteps();
});

$(document).off('click','#assign_variation');
$(document).on('click','#assign_variation',function(e){
	var $data = $("#variation_step").serialize();
	$.ajax({
		url:'healthy_steps_variation_fee.php',
		data:$data,
		dataType : 'json',
		type:'post',
		success :function(res){
			$(".error").html('');
			if(res.status == 'success'){
				window.parent.variation_healthy_step(res.fee_ids);
				window.parent.$.colorbox.close()
			}else if(res.status == 'fail'){
				$.each(res.errors, function(key, value) {
					$('#error_' + key).html(value).show();
				});
			}
		}
	})
});

$(document).off("change",".select_step");
$(document).on("change",".select_step",function(e){
	// $("input.select_step").prop("checked", false);

	var $id = $(this).attr("id").replace("steps_","");
	var val = $(this).is(":checked");
	if(val){
		$("#default_view").hide();
		// $(".show_details").hide();
		$("#show_details_"+$id).show();
	}else{
		$("#show_details_"+$id).hide();
	}
});

getCheckedSteps = function(){
$(".select_step").each(function(e){
	if($(this).is(":checked")){
		var $id1 = $(this).attr("id").replace("steps_","");
		$(".show_details").hide();
		$("#show_details_"+$id1).show();
		$("#default_view").hide();
		return false;
	}else{
		$("#default_view").show();
		$(".show_details").hide();
		// return false;
	}
});
}
</script>
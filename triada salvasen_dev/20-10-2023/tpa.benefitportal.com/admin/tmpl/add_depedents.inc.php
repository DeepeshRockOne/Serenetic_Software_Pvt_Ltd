<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn"><?=$action == 'Edit' ? "Edit Dependent's Details" : '+ Dependent'?> - <span class="fw300"> <?=$action == 'Edit' ? $row['fname'].' '.$row['lname'].' ('.$row['display_id'].')' : 'Dependent '.($total_dependent['total']+1) ?></span></h4>
	</div>
	<div class="panel-body">
		<form action="" id="dependent_form" name="dependent_form">
			<input type="hidden" name="customer_id" value="<?=$customer_id?>" id="customer_id">
			<input type="hidden" name="dep_id" value="<?=$dep_id?>" id="dep_id">
			<input type="hidden" name="is_dep_ajaxed" value="1" id="is_dep_ajaxed">
			<input type="hidden" name="action" value="<?=$action?>" id="action">
			<div class="row theme-form">
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" name="relation" id="relation">
							<option data-hidden="true"></option>
							<option value="Child" <?= $relation == 'Child' ? 'selected' : '' ?>>Child</option>
							<option value="Spouse" <?= $relation == 'Spouse' ? 'selected' : '' ?>>Spouse</option>
						</select>
						<label>Relation*</label>
						<p class="error error_relation"></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="fname" id="fname" value="<?=checkIsset($row['fname'])?>" class="form-control">
						<label>First Name</label>
						<p class="error error_fname"></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="lname" id="lname"  value="<?=checkIsset($row['lname'])?>"  class="form-control">
						<label>Last Name</label>
						<p class="error error_lname"></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="phone-control-wrap">
					<?php if($action == 'Edit' && !empty($row['dssn'])){ ?>
					<div class="phone-addon">
						<div class="form-group">
							<input type="text" name="ssn" id="display_ssn"  readonly='readonly'  class="form-control <?= !empty($row['dssn']) ? 'has-value' : ''?>" value="<?= !empty($row['dssn']) ? secure_string_display_format($row['dssn'], 4) : ""; ?>">
							<?=!empty($row['dssn']) ? '<label>SSN / ITIN NUMBER</label>' : '' ?>
							<input type="text" class="form-control" id="ssn" name="ssn" value="" style="display:none" />
							<?=empty($row['dssn']) ? '<label>SSN / ITIN NUMBER</label>' : '' ?>
							
							<input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value=''/>
							<p class="error error_ssn"></p>
						</div>
					</div>
					<div class="phone-addon w-30">
						<div class="m-b-25">
							<a href="javascript:void(0)" id="edit_ssn" class="text-action icons" style="display:block">
							<i class="fa fa-edit fa-lg"></i></a>
							<a href="javascript:void(0)" id="cancel_ssn" class="text-action icons" style="display:none">
							<i class="fa fa-remove fa-lg"></i></a>
						</div>
					</div>
					<?php } else { ?>
						<div class="form-group">
							<input type="text" name="ssn" id="ssn" class="form-control " value="">
							<label>SSN / ITIN NUMBER</label>
							<input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
							<p class="error error_ssn"></p>
						</div>
					<?php } ?>
					</div>
				</div>
				<!-- <div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="ssn" id="ssn" class="form-control">
						<label>SSN / ITIN NUMBER</label>
					</div>
				</div> -->
				<div class="col-sm-6">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<div class="pr">
								<input id="birth_date" type="text" class="form-control" value="<?=checkIsset($row['birth_date']) !='' ? getCustomDate($row['birth_date']) : ''?>" name="birth_date" >
								<label>DOB (MM/DD/YYYY)</label>
							</div>
						</div>
						<p class="error error_birth_date"></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" name="gender" id="gender">
							<option data-hidden="true"></option>
							<option value="Male" <?= !empty($row['gender']) && $row['gender']=='Male' ? 'selected' : '' ?>>Male</option>
							<option value="Female" <?= !empty($row['gender']) && $row['gender']=='Female' ? 'selected' : '' ?>>Female</option>
						</select>
						<label>Legal Sex/Gender*</label>
						<p class="error error_gender"></p>
					</div>
				</div>
			</div>
			<h4>Disability</h4>
			<div class="phone-control-wrap m-b-25">
				<div class="phone-addon w-30 v-align-top">
					<input type="checkbox" id="is_disabled" name="is_disabled"  <?=checkIsset($row['is_disabled']) !='' && $row['is_disabled'] == 'Y' ? 'checked="checked"' : '' ?>  >
				</div>
				<div class="phone-addon text-left">Dependents with documented disabilities will not be denied plan or have current plan terminated for reaching age limitation. Dependent has documented disability.</div>
			</div>
			<div class="text-center">
				<a href="javascript:void(0)" class="btn btn-info" id="add_edit_dependent">Save</a>
				<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</form>
	</div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(e){
	$('#birth_date').inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
	$('#ssn').inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
	$('#edit_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').hide();
      $('#ssn').show();
      $('#is_ssn_edit').val('Y');
      $('#cancel_ssn').show();

   });

   $('#cancel_ssn').click(function () {
      $(this).hide();
      $('#display_ssn').show();
      $('#ssn').hide();
      $('#is_ssn_edit').val('N');
      $('#edit_ssn').show();
      $('#error_ssn').html('');
   });
});

$(document).off('click','#add_edit_dependent');
$(document).on('click','#add_edit_dependent',function(e){
	e.preventDefault();
	$.ajax({
		url : "add_depedents.php",
		type : 'POST',
		data:$("#dependent_form").serialize(),
		dataType:'json',
		beforeSend :function(e){
			$("#ajax_loader").show();
		},
		success : function(res){
			$("#ajax_loader").hide();
			$(".error").html("");
			if(res.status =='success'){
				parent.$.colorbox.close();
				parent.ajax_get_member_data('member_depedents_tab.php','dependents_tab','<?=$customer_id?>');
				parent.setNotifySuccess(res.msg);
			}else{
			if(res.product_popup !== undefined && res.product_popup == 'product_popup' && res.products !== undefined){
				$("#conflict_product").text('');
				$("#conflict_product").text(res.products);
				$("#state_span").text($("#state").val());
				$.colorbox({
					href:'#address_change',
					inline:true,
					width: '585px', 
					height: '330px'
				});
			}
			$.each(res.errors,function(index,error){
				$(".error_"+index).html(error).show();
			});
			}
		}
		});
})
</script>
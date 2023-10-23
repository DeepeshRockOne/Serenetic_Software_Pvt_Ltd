<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Minimum Group Contribution - <span class="fw300">Variation</span></h4>
	</div>
	<form  method="POST" id="manage_group_form" enctype="multipart/form-data"  autocomplete="off">
		<input type="hidden" name="rule_id" id="rule_id" value="<?= $rule_id ?>">
		<div class="panel-body theme-form">
			<p class="m-b-20">Assign this Variation to a group below.</p>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<select class="se_multiple_select" id="assign_group" name="assign_group[]" multiple="multiple">
							<?php if(!empty($resGroup)) { ?>
								<?php foreach ($resGroup as $key => $value) { ?>
									<option value="<?= $value['id'] ?>" <?= !empty($group_id) && $group_id == $value['id'] ? 'selected' : '' ?>> <?= $value['rep_id'] ." - ". $value['business_name'] ?> </option>
								<?php } ?>
							<?php } ?>
						</select>
						<label>Assign Group(s)</label>
						<p class="error" id="error_assign_group"></p>
					</div>
				</div>
			</div>
			<hr class="m-t-0">
			<p class="m-b-15">Is there a minimum group contribution required for any product(s)?</p>
			<div class="m-b-25">
				<div class="m-b-10">
					<label class="mn"><input type="radio" name="minimum_group_contribution" value="Y" <?= !empty($minimum_group_contribution) && $minimum_group_contribution =='Y' ? 'checked' : '' ?>> Yes</label>
				</div>
				<div class="m-b-0">
					<label class="mn"><input type="radio" name="minimum_group_contribution" value="N" <?= !empty($minimum_group_contribution) && $minimum_group_contribution =='N' ? 'checked' : '' ?>> No</label>
				</div>
				<p class="error" id="error_minimum_group_contribution"></p>
				<p class="error" id="error_general_minimum_group_contribution"></p>
			</div>

			<div id="minimum_group_contribution_div" style="<?= !empty($minimum_group_contribution) && $minimum_group_contribution =='Y' ? '' : 'display: none' ?>">
				<div id="minimum_group_contribution_main_div">
					<?php if(!empty($resGroupContributionSetting)){ ?>
						<?php foreach ($resGroupContributionSetting as $key => $value) { ?>
							<div id="minimum_group_contribution_inner_div_<?= $value['id'] ?>" class="minimum_group_contribution_inner_div" data-id="<?= $value['id'] ?>">
								<?php $contribution_products = !empty($value['products']) ? explode(",", $value['products']) : '' ?>
								<div class="row">
									<div class="col-lg-3 col-sm-3">
										<div class="form-group">
												<select class="se_multiple_select products added_products" id="products_<?= $value['id'] ?>" name="products[<?= $value['id'] ?>][]" multiple="multiple">
													<?php foreach ($company_arr as $key => $company){
												        if($company){ ?>
												            <optgroup label="<?= $key ?>">
												                <?php foreach ($company as $pkey => $row) {
												                	$option_display = $row['name'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
												                	<option value="<?= $row["id"] ?>" data-id="<?= $value['id'] ?>" <?= !empty($contribution_products) && in_array($row['id'], $contribution_products) ? 'selected' : '' ?> <?= !empty($contribution_products) && !in_array($row['id'], $contribution_products) && !empty($all_products) && in_array($row['id'], $all_products) ? 'disabled' : '' ?>> <?= $option_display ?></option>
												                <?php } ?>
												            </optgroup>
												        <?php }
												    } ?>
												</select>
												<label> Select Product(s)</label>
											<p class="error" id="error_products_<?= $value['id'] ?>"></p>
										</div>
									</div>
									<div class="col-lg-3 col-sm-3">
										<div class="form-group  text-center">
											<div class="m-t-5 hidden-xs"></div>
											<label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" class="contribution_type" data-id="<?= $value['id'] ?>" value="Fixed" <?= !empty($value['contribution_type']) && $value['contribution_type'] == "Fixed" ? "checked" : "" ?>> Fixed Amount</label>
											<label class="radio-inline"><input type="radio" name="contribution_type[<?= $value['id'] ?>]" class="contribution_type" data-id="<?= $value['id'] ?>" value="Percentage" <?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "checked" : "" ?>> Percentage</label>
											<p class="error" id="error_contribution_type_<?= $value['id'] ?>" ></p>
										</div>
									</div>
									<div class="visible-md visible-sm "></div>
									<div class="col-lg-3 col-sm-3">
										<div class="phone-control-wrap">
											<div class="phone-addon">
												<div class="form-group ">
													<div class="input-group w-100">
														<span class="input-group-addon" id="Fixed_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Fixed" ? "" : "display: none" ?>"><i class="fa fa-usd"></i></span>
														<div class="pr">
															<input type="text" class="form-control" name="contribution[<?= $value['id'] ?>]" value="<?=  isset($value['contribution']) ? $value['contribution'] : '' ?>">
															<label>Set Contribution</label>
														</div>
														<span class="input-group-addon" id="Percentage_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "" : "display: none" ?>"><i class="fa fa-percent"></i></span>
													</div>
													<p class="error" id="error_contribution_<?= $value['id'] ?>"></p>
												</div>
											</div>
											<div class="phone-addon w-30 ">
												<div class="form-group ">
													<span class="text-light-gray fw500"><a href="javascript:void(0);" id="remove_minimum_group_contribution_inner_div_<?= $value['id'] ?>" class="remove_minimum_group_contribution_inner_div" data-id="<?= $value['id'] ?>"> X </a></span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-sm-3" id="percentage_calculate_by_div_<?= $value['id'] ?>" style="<?= !empty($value['contribution_type']) && $value['contribution_type'] == "Percentage" ? "" : "display: none" ?>">
										<p>What will this percentage be calculated by?</p>
											<div class="m-b-10">
												<label class="mn label-input"><input type="radio" name="percentage_calculate_by[<?= $value['id'] ?>]" value="member_only_tier_apply_to_all" class="percentage_calculate_by" <?= !empty($value['percentage_calculate_by']) && $value['percentage_calculate_by'] == "member_only_tier_apply_to_all" ? "checked" : "" ?>> Member Only tier applied to all plan tiers</label>
											</div>
											<div class="m-b-10">
												<label class="mn"><input type="radio" name="percentage_calculate_by[<?= $value['id'] ?>]" value="each_benefit_tier" class="percentage_calculate_by" <?= !empty($value['percentage_calculate_by']) && $value['percentage_calculate_by'] == "each_benefit_tier" ? "checked" : "" ?>> Each plan tier</label>
											</div>
											<p class="error" id="error_percentage_calculate_by_<?= $value['id'] ?>"></p>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="clearfix p-b-20">
					<a href="javascript:void(0);" class="red-link" id="add_minimum_group_contribution">+ Min Group Contribution</a>
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:void(0);" class="btn btn-action" id="save">Save</a>
				<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
			</div>
		</div>
	</form>
</div>
<div id="minimum_group_contribution_clone_div" style="display: none;">
	<div id="minimum_group_contribution_inner_div_~number~" class="minimum_group_contribution_inner_div" data-id="~number~">
		<div class="row">
			<div class="col-lg-3 col-sm-3">
				<div class="form-group ">
						<select class="se_multiple_select products" id="products_~number~" name="products[~number~][]" multiple="multiple">
							<?php foreach ($company_arr as $key => $company){
						        if($company){ ?>
						            <optgroup label="<?= $key ?>">
						                <?php foreach ($company as $pkey => $row) {
						                	$option_display = $row['name'].' '. (!empty($row["product_code"]) ? '('.$row["product_code"].')' : ''); ?>
						                	<option value="<?= $row["id"] ?>" data-id="~number~" <?= !empty($all_products) && in_array($row['id'], $all_products) ? 'disabled' : '' ?>> <?= $option_display ?></option>
						                <?php } ?>
						            </optgroup>
						        <?php }
						    } ?>
						</select>
						<label> Select Product(s)</label>
					<p class="error" id="error_products_~number~"></p>
				</div>
			</div>
			<div class="col-lg-3 col-sm-3">
				<div class="form-group  text-center">
					<div class="m-t-5 hidden-xs"></div>
					<label class="radio-inline"><input type="radio" name="contribution_type[~number~]" class="contribution_type" data-id="~number~" value="Fixed"> Fixed Amount</label>
					<label class="radio-inline"><input type="radio" name="contribution_type[~number~]" class="contribution_type" data-id="~number~" value="Percentage"> Percentage</label>
					<p class="error" id="error_contribution_type_~number~"></p>
				</div>
			</div>
			<div class="col-lg-3 col-sm-3">
				<div class="phone-control-wrap">
					<div class="phone-addon">
						<div class="form-group ">
							<div class="input-group w-100">
								<span class="input-group-addon" id="Fixed_div_~number~" style="display: none"><i class="fa fa-usd"></i></span>
								<div class="pr">
									<input type="text" class="form-control" name="contribution[~number~]">
									<label>Set Contribution</label>
								</div>
								<span class="input-group-addon" id="Percentage_div_~number~" style="display: none"><i class="fa fa-percent"></i></span>
							</div>
							<p class="error" id="error_contribution_~number~"></p>
						</div>
					</div>
					<div class="phone-addon w-30 ">
						<div class="form-group ">
							<span class="text-light-gray fw500"><a href="javascript:void(0);" id="remove_minimum_group_contribution_inner_div_~number~" class="remove_minimum_group_contribution_inner_div" data-id="~number~"> X </a></span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-3" id="percentage_calculate_by_div_~number~" style="display: none">
				<p>What will this percentage be calculated by?</p>
					<div class="m-b-10">
						<label class="mn label-input"><input type="radio" name="percentage_calculate_by[~number~]" value="member_only_tier_apply_to_all" class="percentage_calculate_by"> Member Only tier applied to all plan tiers</label>
					</div>
					<div class="m-b-10">
						<label class="mn"><input type="radio" name="percentage_calculate_by[~number~]" value="each_benefit_tier" class="percentage_calculate_by"> Each plan tier</label>
					</div>
					<p class="error" id="error_percentage_calculate_by_~number~"></p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var $contribution_count = 0;
	$(document).ready(function(){
		
		$("#assign_group").multipleSelect({
	  	});

	  	$is_added_products = '<?= $is_added_products ?>';
			
		if($is_added_products=="true"){
			$(".added_products").multipleSelect({
				selectAll: false,
				onClick:function(e){
					
					$id = e.data.id;
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$(".products [value='"+$productName+"']").prop('disabled',true);
						$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
					}else{
						$(".products [value='"+$productName+"']").prop('disabled',false);
					}
					$("#manage_group_form .products").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;
					$id = e.data.id;
					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$(".products [value='"+$productName+"']").prop('disabled',true);
								$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
							}else{
								$(".products [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#manage_group_form .products").multipleSelect('refresh');
					
				},
				onTagRemove:function(e){
					$productName = e.value;
					$(".products [value='"+$productName+"']").prop('disabled',false);
					
					$("#manage_group_form .products").multipleSelect('refresh');
					
				}
		  	});
		  	$("#manage_group_form .contribution_type").not('.js-switch').uniform();
		  	$("#manage_group_form .percentage_calculate_by").not('.js-switch').uniform();
		}
	});

	//******************** Contribution Code Start **********************
  		
  		$(document).on("click", "#add_group_contribution_variation", function(e) {
		    e.preventDefault();
		    $.colorbox({
			  	href:'add_group_contribution_variation.php',
			  	iframe:true,
			  	width:"875px;",
			  	height:"500px;",
		  	});
		});

		$(document).on("change","input[name=minimum_group_contribution]",function(){
			$val=$(this).val();
			$("#minimum_group_contribution_div").hide();
			if($val=="Y"){
				$("#minimum_group_contribution_div").show();
				add_minimum_group_contribution();
			}else{
				$("#minimum_group_contribution_main_div").html('');
			}
		});

		$(document).on("click", "#add_minimum_group_contribution", function() {
			add_minimum_group_contribution();
		});

		$(document).on("click", ".remove_minimum_group_contribution_inner_div", function() {
			$id = $(this).attr('data-id');
			if($id <= 0){
				$("#minimum_group_contribution_inner_div_"+$id).remove();
			}else{
				swal({
		            text: "Delete Contribution: Are you sure?",
		            showCancelButton: true,
		            confirmButtonText: "Confirm",
		        }).then(function() {
		           $("#ajax_loader").show();
		            $.ajax({
		                url:'ajax_delete_contribution.php',
		                dataType:'JSON',
		                type:'POST',
		                data:{id:$id},
		                success:function(res){
		                    if(res.status='success'){
		                        setNotifySuccess("Contribution Deleted Successfully");
		                        $("#minimum_group_contribution_inner_div_"+$id).remove();
		                    }
		                    $("#ajax_loader").hide();
		                }
		            });
		        }, function (dismiss) {
		        }); 
			}
		});

		$(document).on("click",".contribution_type",function(){
			$val=$(this).val();
			$id = $(this).attr('data-id');
			$("#Fixed_div_"+$id).hide();
			$("#Percentage_div_"+$id).hide();
			$("#percentage_calculate_by_div_"+$id).hide();
			if($val=="Fixed"){
				$("#Fixed_div_"+$id).show();
			}else if($val=="Percentage"){
				$("#Percentage_div_"+$id).show();
				$("#percentage_calculate_by_div_"+$id).show();
			}
		});

		add_minimum_group_contribution = function(){
			$contribution_count=$contribution_count+1;
			$number = "-"+$contribution_count;
			html = $('#minimum_group_contribution_clone_div').html();
			html = html.replace(/~number~/g,$number)
            $('#minimum_group_contribution_main_div').append(html);

			$("#products_"+$number).multipleSelect({
				selectAll: false,
				onClick:function(e){
					
					$id = e.data.id;
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$(".products [value='"+$productName+"']").prop('disabled',true);
						$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
					}else{
						$(".products [value='"+$productName+"']").prop('disabled',false);
					}
					$("#manage_group_form .products").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;
					$id = e.data.id;
					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$(".products [value='"+$productName+"']").prop('disabled',true);
								$("#products_"+$id+" [value='"+$productName+"']").prop('disabled',false);
							}else{
								$(".products [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#manage_group_form .products").multipleSelect('refresh');
					
				},
				onTagRemove:function(e){
					$productName = e.value;
					$(".products [value='"+$productName+"']").prop('disabled',false);
					
					$("#manage_group_form .products").multipleSelect('refresh');
					
				}
		  	});
		  	$("#manage_group_form .contribution_type").not('.js-switch').uniform();
		  	$("#manage_group_form .percentage_calculate_by").not('.js-switch').uniform();
		}
  	//******************** Contribution Code End   **********************

  	//******************** Button Code Start **********************
		$(document).on("click","#save",function(){
			$("#ajax_loader").show();
			$(".error").html("");
			$.ajax({
				url:'ajax_add_group_contribution_variation.php',
				dataType:'JSON',
				data:$("#manage_group_form").serialize(),
				type:"POST",
				success:function(res){
					$("#ajax_loader").hide();
					if(res.status=="success"){
						window.parent.setNotifySuccess("Group Contribution Variation Added Successfully");
						window.parent.$.colorbox.close();
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
			});
		});
		$(document).on("click","#cancel",function(){
			window.parent.$.colorbox.close();
		});
	//******************** Button Code End   **********************
</script>
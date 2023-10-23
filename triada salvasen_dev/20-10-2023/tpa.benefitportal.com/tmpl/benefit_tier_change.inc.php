<div class="panel panel-default">
    <?php if($action == "policy_change") { ?>
    <div class="panel-heading">
        <h4 class="mn"><i class="fa fa-ticket m-r-10" aria-hidden="true"></i>Policy Change - <span class="fw300"><?=$prd_row['name']?></span></h4>
    </div>
    <div class="bg_light_gray p-10 fw500 text-center">
        <span><?=$prd_row['name']?></span> 
        <span class="text-success p-l-15 p-r-15 fw300"><i class="fa fa-arrow-circle-o-right fa-lg" aria-hidden="true"></i></span>
        <span><?=$new_prd_row['name']?></span> 
    </div>
    <?php } elseif($action == "benefit_amount_change") { ?>
     <div class="panel-heading">
        <h4 class="mn"><i class="fa fa-ticket m-r-10" aria-hidden="true"></i>Benefit Amount Change - <span class="fw300"><?=$prd_row['name']?></span></h4>
    </div>   
    <?php } else { ?>
    <div class="panel-heading">
        <h4 class="mn"><i class="fa fa-ticket m-r-10" aria-hidden="true"></i>Benefit Tier Change - <span class="fw300"><?=$prd_row['name']?></span></h4>
    </div>
    <div class="bg_light_gray p-10 fw500 text-center">
        <span><?=$cur_plan_type_title?></span> 
        <span class="text-success p-l-15 p-r-15 fw300"><i class="fa fa-arrow-circle-o-right fa-lg" aria-hidden="true"></i></span>
        <span><?=$new_plan_type_title?></span> 
    </div>
    <?php } ?>
	<div class="panel-body">
		<div class="theme-form">
			<form method="POST" name="frm_tier_change" class="form_wrap " id="frm_tier_change" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="<?= $action ?>"/>
                <input type="hidden" name="location" id="location" value="<?= $location ?>"/>
                <input type="hidden" name="customer_id" id="customer_id" value="<?= $ws_row['customer_id'] ?>"/>
                <input type="hidden" name="new_prd_id" id="new_prd_id" value="<?= $new_prd_id ?>"/>
                <input type="hidden" name="new_plan_id" id="new_plan_id" value="<?= $new_plan_id ?>"/>
                <input type="hidden" name="new_plan_type" id="new_plan_type" value="<?= $new_plan_type ?>"/>
                <input type="hidden" name="ws_id" id="ws_id" value="<?= $ws_id ?>"/>
                <input type="hidden" name="life_event" id="life_event" value="<?= $life_event ?>"/>
                <input type="hidden" name="is_take_charge" id="is_take_charge" value="N"/>
				<div class="form-group">
                    <?php
                    $disabled_plan_type = '';
                    if($action == "benefit_tier_change") {
                        $disabled_plan_type = 'disabled';
                    }
                    ?>
					<select name="plan_type" id="plan_type" class="form-control" <?=$disabled_plan_type?>>
						<option data-hidden="true"></option>
						<?php foreach ($prd_benefit_tier_res as $row) { ?>
	                            <option value="<?php echo $row["id"]; ?>" <?= $row['id']==$new_plan_type?'selected':'';?>><?php echo $row["title"]; ?></option>
	                   <?php } ?>
					</select>
					<label>Benefit Tier</label>
					<p class="error"><span id="err_new_plan_id"></span></p>
				</div>

				<?php if($new_plan_type > 1 || $action == "policy_change" || $action == "benefit_amount_change") { ?>
                <div id="dependent_section" style="display: none;">
    				<p class="fw500">Dependents To Be Added</p>
    				<div class="form-group">
    					<select class="se_multiple_select" name="dependant[]" id="dependent" multiple="multiple">
    						<?php 
    						if(!empty($dep_res)) {
    							foreach ($dep_res as $dep) { 
    								$relation = "";
                                    $selected = "";
    								if(in_array(strtolower($dep['relation']), array('son','daughter'))){
    									$relation = 'Child';
    								} else {
    									$relation = "Spouse";
    								}
                                    if(!empty($selected_dep_ids) && in_array($dep['id'],$selected_dep_ids)) {
                                        $selected = "selected";
                                    }
    								?>
    								<option value="<?=$dep['id']?>" <?=$selected?>><?=$dep['display_id'] . ' - '.$dep['fname'] ." ". $dep['lname'] . " (".$relation . ")"?></option>
    							<?php 
    							}
    						} 
    						?>
    					</select>
    					<label>Select Dependents</label>
    					<p class="error"><span id="err_dependant"></span></p>
    				</div>
                </div>
                <p class="error" style="margin-bottom: 28px !important;" id="err_pricing"></p>
				<?php } ?>
                <?php if($action == "benefit_amount_change") { ?>
                    <p class="fw500">Benefit Amount</p>
                    <div class="table-responsive br-n">
                        <table class="<?=$table_class?>">
                            <thead>
                                <tr>
                                    <th>Enrollee(s)</th>
                                    <th>Existing</th>
                                    <th width="130px">New</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?=$cust_row['fname'].' '.$cust_row['lname']?></td>
                                    <td><?=displayAmount($ws_row['benefit_amount']);?></td>
                                    <td>
                                        <?php if($std_product){ ?>
                                            <input type="text" name="primary_benefit_amount" id="primary_benefit_amount" class="form-control pricing_input">
                                            <input type="hidden" name="benefit_amount_percentage" id="benefit_amount_percentage" value="">
                                        <?php }else{ ?>
                                            <select name="primary_benefit_amount" id="primary_benefit_amount" class="form-control selectpicker_input">
                                                <option data-hidden="true"></option>
                                                <?php 
                                                    if(!empty($prd_benefit_amount['Primary'])) {
                                                        foreach ($prd_benefit_amount['Primary'] as $key => $benefit_amount) {
                                                            ?>
                                                            <option value="<?=$benefit_amount?>" <?=$ws_row['benefit_amount'] == $benefit_amount?"selected=selected":"" ?>><?=displayAmount($benefit_amount);?></option>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php if($all_dep_res){ 
                                    foreach ($all_dep_res as $dep_row) { ?>
                                        <tr class="dep_benefit_amount_row dep_benefit_amount_row_<?=$dep_row['id']?>" <?=!empty($selected_dep_ids) && in_array($dep_row['id'],$selected_dep_ids)?"":'style="display:none;"'?>>
                                            <td><?=$dep_row['name']?></td>
                                            <td><?=!empty($dep_row['benefit_amount'])?displayAmount($dep_row['benefit_amount']):'-';?></td>
                                            <td>
                                                <select name="dep_benefit_amount[<?=$dep_row['id']?>]" id="dep_benefit_amount_<?=$dep_row['id']?>" class="form-control selectpicker_input">
                                                    <option data-hidden="true"></option>
                                                    <?php 
                                                        if(!empty($prd_benefit_amount[$dep_row['crelation']])) {
                                                            foreach ($prd_benefit_amount[$dep_row['crelation']] as $key => $benefit_amount) {
                                                                ?>
                                                                <option value="<?=$benefit_amount?>" <?=$dep_row['benefit_amount'] == $benefit_amount?"selected=selected":"" ?>><?=displayAmount($benefit_amount);?></option>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>                                   
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <p class="error" style="margin-bottom: 28px !important;" id="err_benefit_amount"></p>
                    </div>
                <?php } ?>
				<p class="fw500">Effective Date</p>
				<div class="form-group">
					<select class="form-control tier_change_date" id="tier_change_date" name="tier_change_date">
						<option data-hidden="true"></option>
						<?php foreach ($date_selection_options as $coverage) { ?>
                            <?php if($location != 'admin'){
                                $today = date("Y-m-d");
                                
                                if(date('Y-m-d',strtotime('+30 days',strtotime($setEffectiveDate))) <= date('Y-m-d', strtotime($today))){
                                    if(date('Y-m-d',strtotime($coverage['value'])) <= date('Y-m-d', strtotime($today))){
                                        continue;
                                    }
                                }
                            } ?>
                    	<option value="<?=$coverage['value'];?>"><?=$coverage['text'];?></option>
	                  	<?php } ?>
					</select>
					<label>Select</label>
					<p class="error"><span id="err_tier_change_date"></span></p>
				</div>

				<div class="m-b-30 table-responsive br-n">
					<table cellspacing="0" cellspacing="5" width="100%">
						<tbody>
							<tr>
								<td>
									<p>Current <?=$member_payment_type?> Premium: <span class="fw700 p-l-15 old_plan_price"><?=displayAmount($cur_plan_price)?></span></p>
									<p>New <?=$member_payment_type?> Premium:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="fw700 p-l-15 text-action new_plan_price"><?=displayAmount($new_plan_price)?></span></p>
								</td>
								<?php if($cur_plan_price == $new_plan_price) { ?>
								<td class="text-center fw700" valign="top">
									<span class="transaction_label"></span><br>
									<span class="text-success fw300 transaction_amount"></span>
								</td>
                                <?php } elseif($cur_plan_price > $new_plan_price) { ?>
                                <td class="text-center fw700" valign="top">
                                    <span class="transaction_label">Savings</span> <br>
                                    <span class="text-success fw300 transaction_amount"><?=displayAmount(($cur_plan_price - $new_plan_price))?></span>
                                </td>
                                <?php } else { ?>
								<td class="text-center fw700" valign="top">
									<span class="transaction_label">Increase</span> <br>
									<span class="text-success fw300 transaction_amount"><?=displayAmount(($new_plan_price - $cur_plan_price))?></span>
								</td>
								<?php } ?>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row m-t-10 ">
                    <div class="col-xs-12 billing_profiles" style="display: none;">
                        <p class="fw500">Payment Method</p>
						<div class="form-group">
							<select class="form-control" name="billing_profile" id="payment_method" data-size="5">
								<option data-hidden="true"></option>
                                <?php if($sponsor_billing_method == "individual") { ?>
                                    <?php 
                                    foreach ($billing_res as $key => $billing_row) {
                                        $option_text = '';
                                        if($billing_row['payment_mode'] == "CC") {
                                            $option_text = $billing_row['card_type'].' *'.$billing_row['last_cc_ach_no'];
                                        
                                        } elseif($billing_row['payment_mode'] == "ACH") {
                                            $option_text = 'ACH *'.$billing_row['last_cc_ach_no'];
                                        }
                                        if($billing_row['is_default'] == 'Y') {
                                            $option_text .= " (Default)";
                                        }
                                     ?>
                                    <option value="<?=$billing_row['id']?>"><?=$option_text;?></option>
                                    <?php 
                                    }
                                    ?>
                                <?php } else { ?>
                                    <?php
                                    if($sponsor_billing_method == "list_bill") {
                                        echo '<option value="list_bill" selected="selected">List Bill</option>';

                                    } else if($sponsor_billing_method == "TPA") {
                                        echo '<option value="TPA" selected="selected">TPA (Admin Only)</option>';
                                    }
                                    ?>
                                <?php } ?>
							</select>
							<label>Select</label>
                        	<p class="error"><span id="err_billing_profile"></span></p>
						</div>
                        <div class="clearfix"></div>
                    </div>
	            </div>
				<div class="text-center">
                    <?php // if(!empty($new_plan_id)) { ?>
                    <button class="btn btn-action" id="final_save" name="final_save" type="button">Update
                    </button>
                    <?php // } ?>
                    <a href="javascript:void(0);" class="btn red-link" name="cancel" onclick="window.parent.$.colorbox.close()">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
    var old_plan_price = <?=$cur_plan_price?>;
	$(document).ready(function(){
        if(<?=$new_plan_type;?> > 1) {
            $("#dependent_section").show();
        } else {
            $("#dependent_section").hide();
        }

        $(".selectpicker_input").selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: false,
        });

        $('#primary_benefit_amount').on('keyup',function(event){
            // console.log(event.keyCode);
            // console.log($(this).val());
            // if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) ||
            //     event.keyCode == 46 ||
            //     event.keyCode == 110) {
            //     calculate_policy_rate();

            // } else {
            //     event.preventDefault();
            // }
            var $this = $(this);
            $this.val($this.val().replace(/[^\d.]/g, ''));
            if($this.val() != ''){
                calculate_policy_rate();
            }
        });

        // $(".pricing_input").priceFormat({
        //     prefix: '',
        //     suffix: '',
        //     centsSeparator: '.',
        //     thousandsSeparator: ',',
        //     limit: false,
        //     centsLimit: 2,
        // });

        $("#dependent").multipleSelect({
            selectAll: false,
            filter: false,
            onClick: function (view) {
                if(view.selected) {
                    $("tr.dep_benefit_amount_row_"+view.value).show();
                } else {
                    $("tr.dep_benefit_amount_row_"+view.value).hide();
                }
            }
	    });

        $(document).off('click','.multiple_selection_choice_remove');
        $(document).on('click','.multiple_selection_choice_remove',function(){
            var dep_id = $(this).attr('data-title');
            $("tr.dep_benefit_amount_row_"+dep_id).hide();
        });

        $(document).off('change','#plan_type');
        $(document).on('change','#plan_type', function(e) {
            var $new_plan_type = $(this).val();
            var $total_dep = <?=$total_dep;?>;
            var $spouse_dep = <?=$spouse_dep;?>;
            var $child_dep = <?=$child_dep;?>;
            var $family_plan_rule = '<?=$new_prd_row['family_plan_rule']?>';
            var $dep_status = "exist";
            
            if ($new_plan_type == "4") {
                if($family_plan_rule == "Spouse And Child"){
                    if ($spouse_dep > 0 && $child_dep > 0) {
                    } else {
                        $dep_status = "not_exist";
                    }

                } else if($family_plan_rule == "Minimum One Dependent"){
                    if ($total_dep < 1) {
                        $dep_status = "not_exist";
                    }

                } else if($family_plan_rule == "Minimum Two Dependent"){
                    if($total_dep < 2){
                        $dep_status = "not_exist";
                    }
                }
            } else if ($new_plan_type == "2") {
                if ($child_dep == 0) {
                    $dep_status = "not_exist";
                }

            } else if ($new_plan_type == "3") {
                if ($spouse_dep == 0) {
                    $dep_status = "not_exist";
                }

            } else if ($new_plan_type == "5") {
                if ($total_dep == 0) {
                    $dep_status = "not_exist";
                }
            }

            if ($dep_status == 'not_exist') {
                parent.swal({
                    text: "To make this change, you must first have qualified dependent(s) added.Click below to add dependent(s).",
                    showCancelButton: true,
                    confirmButtonText: '+ Dependent(s)'
                }).then(function () {
                    scrollToDiv($('#dependents_tab'), 0,'member_depedents_tab.php','dependents_tab');
                }, function (dismiss) {
                    $("#plan_type").val("<?=$new_plan_type;?>").change();
                    $("#new_plan_type").val("<?=$new_plan_type;?>");
                });
            } else {
                $("#new_plan_type").val($new_plan_type);
            }

            if($new_plan_type > 1) {
                var dep_html = '';
                var all_dep = <?=json_encode($all_dep_res,true);?>;
                var old_selected = $('#dependent').val();
                var dependent_has_value = false;

                $('tr.dep_benefit_amount_row').hide();

                $.each(all_dep,function(index,value){
                    var selected = '';
                    if(old_selected !== "undefined" && jQuery.inArray(value.id,old_selected) !== -1) {
                        var selected = 'selected';
                        dependent_has_value = true;
                    }
                    if ($new_plan_type == "2") {
                        if(value.relation == "daughter" || value.relation == "son") {
                            if(selected == 'selected') {
                                $('tr.dep_benefit_amount_row_'+value.id).show();
                            }
                            dep_html += "<option value='"+value.id+"' "+selected+">"+value.name+" ("+value.crelation+")</option>";
                        }
                    } else if ($new_plan_type == "3") {
                        if(value.relation == "husband" || value.relation == "wife") {
                            if(selected == 'selected') {
                                $('tr.dep_benefit_amount_row_'+value.id).show();
                            }
                            dep_html += "<option value='"+value.id+"' "+selected+">"+value.name+" ("+value.crelation+")</option>";
                        }
                    } else {
                        if(selected == 'selected') {
                            $('tr.dep_benefit_amount_row_'+value.id).show();
                        }
                        dep_html += "<option value='"+value.id+"' "+selected+">"+value.name+" ("+value.crelation+")</option>";
                    }
                });
                if(dependent_has_value == false) {
                    $('#dependent').removeClass('has-value');
                    $('#dependent').closest('.form-group').find('.se_multiple_select').removeClass('has-value');
                }
                $('#dependent').html(dep_html);
                $('#dependent').multipleSelect('refresh');
                $("#dependent_section").show();

                calculate_policy_rate();   
            } else {
                $('tr.dep_benefit_amount_row').hide();
                $("#dependent_section").hide();
                calculate_policy_rate();
            }            
        });
        
		$(document).on('change', '#tier_change_date', function(e) {
			tier_change_charge();
		});

        $(document).off('change','.selectpicker_input');
        $(document).on('change','.selectpicker_input', function(e) {
            calculate_policy_rate();
        });

        $(document).off('change', '#dependent');
        $(document).on('change', '#dependent', function(e) {
            calculate_policy_rate();
        });

        $(document).off('change', '#tier_change_date');
        $(document).on('change', '#tier_change_date', function(e) {
            tier_change_charge();
        });

        $(document).off('change', '#payment_method');
		$(document).on('change','#payment_method',function(){
            if($(this).val() == 'new_credit_card') {
                $('.new_bank_draft_section').slideUp();
                $('.new_credit_card_section').slideDown();

            } else if ($(this).val() == 'new_bank_draft') {
                $('.new_credit_card_section').slideUp();
                $('.new_bank_draft_section').slideDown();

            } else {
                $('.new_bank_draft_section').slideUp();
                $('.new_credit_card_section').slideUp();
            }
        });

        $(document).off('click', '#final_save');
        $(document).on('click', '#final_save', function () {
            parent.disableButton($(this));
            $('#ajax_loader').show();
            $.ajax({
                url: 'benefit_tier_change.php',
                method: 'POST',
                data: $("#frm_tier_change").serialize(),
                dataType: 'json',
                success: function (res) {
                    $("#err_dependant").html('');
                    $("[id^='err_']").html('');
                    if (res.status == "fail") {
                        $('#ajax_loader').hide();
                        parent.enableButton($("#final_save"));
                        var is_error = true;
                        $.each(res.errors, function (index, value) {
                            if (typeof(index) !== "undefined") {
                                $("#err_" + index).html(value);
                                if (is_error) {
                                    var offset = $("#err_" + index).offset();
                                    var offsetTop = offset.top;
                                    var totalScroll = offsetTop - 200;
                                    $('body,html').animate({
                                        scrollTop: totalScroll
                                    }, 1200);
                                    is_error = false;
                                }                            
                            }
                        });
                    } else {
                        if(res.status == 'success'){
                        	window.parent.$.colorbox.close()
                            parent.window.location.reload();
                        }
                    }
                }
            });
        });

        calculate_policy_rate();
	});
	
	function calculate_policy_rate()
    {
        $("[id^='err_']").html('');
        $("#final_save").prop('disabled',true);
        $("#ajax_loader").show();
        var dep_ids = $("#dependent").val();
        var dep_benefit_amount = $("[name='dep_benefit_amount']").val();
        var primary_benefit_amount = $("#primary_benefit_amount").val();
        $.ajax({
            url: 'ajax_get_product_price_detail.php',
            method: 'POST',
            data: $("#frm_tier_change").serialize(),
            dataType: 'json',
            success: function (res) {
                $("#ajax_loader").hide();
                if (res.status == "fail") {
                    $("#new_plan_id").val('');
                    if(typeof(res.benefit_amount_error) !== "undefined") {
                        $("#err_benefit_amount").html(res.benefit_amount_error);
                    } else {
                        $("#err_pricing").html(res.error);
                    }
                } else {
                    $("#final_save").prop('disabled',false);
                    $("#new_plan_id").val(res.plan_id);
                    $('.new_plan_price').html(res.new_plan_price);
                    if(typeof(res.benefit_amount_percentage) !== "undefined") {
                        $('#benefit_amount_percentage').val(res.benefit_amount_percentage);
                    }
                    if(res.plan_price_diff_org != 0) {
                        $('.transaction_amount').html(res.plan_price_diff);
                        $('.transaction_label').html(res.transaction_label);
                    } else {
                        $('.transaction_amount').html('');
                        $('.transaction_label').html('');   
                    }
                }
            }
        });
    }
    function tier_change_charge() {
		$("[id^='err_']").html('');
        var tier_change_date = $("#tier_change_date").val();
        if(tier_change_date != "") {
            if($("#new_plan_id").val() == '') {
                return false;    
            }
            var new_plan_id = $("#new_plan_id").val();

            $("#ajax_loader").show();
            $.ajax({
                url: 'ajax_tier_change_charge.php',
                method: 'POST',
                data:{ws_id:'<?=$ws_id?>',plan_id:new_plan_id,'tier_change_date':tier_change_date},
                dataType: 'json',
                success: function (res) {
                    $("#ajax_loader").hide();
                    if(res.is_take_charge == true) {
                        $("#is_take_charge").val('Y');
                        $(".billing_profiles").show();
                        $("#final_save").html("Charge");
                    } else {
                        $("#is_take_charge").val('N');
                        $(".billing_profiles").hide();
                        $("#final_save").html("Update");
                    }

                    if(res.transaction_type == 'refund') {
                        //$('.transaction_amount').addClass('text-danger').removeClass('text-success');
                    }
                    
                    if(res.transaction_type == 'charge') {
                    	//$('.transaction_amount').addClass('text-success').removeClass('text-danger');
                    }
                }
            });
        } else {
            $("#final_save").html("Save");
        }
    }
</script>
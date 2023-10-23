<div class="panel panel-default panel-block">
    <div class="panel-heading">
        <h4 class="mn">+ ListBill - <span class="fw300">Variation</span></h4>
    </div>
    <form method="POST" id="manage_listbill_form" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="rule_id" id="rule_id" value="<?=$rule_id?>">
        <div class="panel-body theme-form">
            <p class="m-b-20">Assign this Variation to a group below.</p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <select class="form-control" data-live-search="true" id="assign_group" name="assign_group">
                            <option data-hidden="true"></option>
                            <?php if (!empty($resGroup)) { ?>
                                <?php foreach ($resGroup as $key => $value) { ?>
                                    <option value="<?= $value['id'] ?>" <?= !empty($group_id) && $group_id == $value['id'] ? 'selected' : '' ?>> <?= $value['rep_id'] . " - " . $value['business_name'] ?> </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <label>Assign Group(s)</label>
                        <p class="error" id="error_assign_group"></p>
                    </div>
                </div>
            </div>
            <hr class="m-t-0">
            <p class="m-b-20">Variation Billing Setting.</p>
            <p>Select Days Prior to Pay Period for Variation List Bill Generation.</p>
            <input type="hidden" name="billing_setting" id="billing_setting" value="days_prior_pay_period">
            <div class="row" id="prior_pay_period_div">
                <div class="col-sm-4">
                    <div class="form-group">
                        <select class="form-control" id="variation_prior_day_select" name="variation_prior_day">
                            <option value=""></option>
                            <?php foreach ($prior_days_range as $day) { ?>
                                <option value="<?= $day ?>" <?=($day==$days_prior_pay_period) ? 'selected' : ''?> ><?= $day ?></option>
                            <?php } ?>
                        </select>
                        <label>Select Days Prior to pay period</label>
                        <p class="error" id="error_variation_prior_day"></p>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="m-b-30">
                        <p>Should system auto set payment received if using a inside the system?</p>
                        <div class="m-b-10">
                            <label class="mn"><input type="radio" id="auto_payment_inside_system" name="set_auto_payment_inside_system" value="Y" <?=($auto_set_payment_received_inside_sys=='Y') ? 'Checked' :'' ?> >Yes</label>
                        </div>
                        <div class="m-b-10">
                            <label class="mn"><input type="radio" id="auto_payment_inside_system" name="set_auto_payment_inside_system" value="N" <?=($auto_set_payment_received_inside_sys=='N') ? 'Checked' : '' ?>>No</label>
                        </div>
                        <p class="error" id="error_set_auto_payment_inside_system"></p>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="m-b-30">
                        <p>Should system auto set payment received if using a third-party payment system?</p>
                        <div class="m-b-10">
                            <label class="mn"><input type="radio" id="auto_payment" name="set_auto_payment" value="Y" <?=($auto_set_payment_received=='Y') ? 'Checked' :'' ?> >Yes</label>
                        </div>
                        <div class="m-b-10">
                            <label class="mn"><input type="radio" id="auto_payment" name="set_auto_payment" value="N" <?=($auto_set_payment_received=='N') ? 'Checked' : '' ?>>No</label>
                        </div>
                        <p class="error" id="error_set_auto_payment"></p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group" id="auto_payment_days_div" style="<?=($auto_set_payment_received=='Y' || $auto_set_payment_received_inside_sys=='Y') ? '' : 'display:none' ?>">
                        <select class="form-control" id="auto_payment_days" name="auto_payment_days" >
                            <option value=""></option>
                            <?php foreach ($auto_pay_day_range as $day) { ?>
                                <option value="<?= $day ?>" <?=($day==$auto_pay_day) ? 'selected' : ''?> ><?= $day ?></option>
                            <?php } ?>
                        </select>
                        <label>Select Days for auto payment</label>
                        <p class="error" id="error_auto_payment_days"></p>
                    </div>
                </div>
            </div>
            <p class="error" id="error_class_paydates"></p>
            <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-action" id="save">Save</a>
                    <a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
            </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() { 
        common_select();
    });

    $(document).off("change", "#auto_payment");
    $(document).on("change", "#auto_payment", function() {
        $('#auto_payment_days').selectpicker('val','');
        $val = $(this).val();
        if ($val == "Y") {
            $('#auto_payment_days_div').show();
            $("input[name='set_auto_payment_inside_system'][value='Y']").parent().removeClass('checked');
            $("input[name='set_auto_payment_inside_system'][value='Y']").attr('checked',false);
            $("input[name='set_auto_payment_inside_system'][value='N']").parent().addClass('checked');
            $("input[name='set_auto_payment_inside_system'][value='N']").attr('checked',true);
        } else {
            $check_val = $("#auto_payment_inside_system").is(":checked");
            if(!($check_val)){
                $('#auto_payment_days_div').hide();
            }
        }
        fRefresh();
    });

    $(document).off("change", "#auto_payment_inside_system");
    $(document).on("change", "#auto_payment_inside_system", function() {
        $('#auto_payment_days').selectpicker('val','');
        $val = $(this).val();
        if ($val == "Y") {
            $('#auto_payment_days_div').show();
            $("input[name='set_auto_payment'][value='Y']").parent().removeClass('checked');
            $("input[name='set_auto_payment'][value='Y']").attr('checked',false);
            $("input[name='set_auto_payment'][value='N']").parent().addClass('checked');
            $("input[name='set_auto_payment'][value='N']").attr('checked',true);
        } else {
            $check_val = $("#auto_payment").is(":checked");
            if(!($check_val)){
                $('#auto_payment_days_div').hide();
            }
        }
        fRefresh();
    });



    //******************** Button Code Start **********************
	$(document).on("click","#save",function(){
		$("#ajax_loader").show();
		$(".error").html("");
		$.ajax({
			url:'ajax_add_listbill_option_variation.php',
			dataType:'JSON',
			data:$("#manage_listbill_form").serialize(),
			type:"POST",
			success:function(res){
				$("#ajax_loader").hide();
				if(res.status=="success"){
					window.parent.setNotifySuccess("List Bill Options Variation "+res.Activity+" Successfully");
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
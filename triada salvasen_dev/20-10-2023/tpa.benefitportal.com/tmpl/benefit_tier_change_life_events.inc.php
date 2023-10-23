<div class="panel panel-default">
    <?php if($action == "policy_change") { ?>
    <div class="panel-heading">
        <h4 class="mn"><i class="fa fa-ticket m-r-10" aria-hidden="true"></i>Policy Change - <span class="fw300"><?=$prd_row['name']?></span></h4>
    </div>
    <div class="bg_light_gray p-10 fw500 text-center">
        <span><?=$prd_row['name']?></span>
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
			<form method="GET" name="frm_tier_change" class="form_wrap " id="frm_tier_change" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="<?= $action ?>"/>
                <input type="hidden" name="location" id="location" value="<?= $location ?>"/>
                <input type="hidden" name="ws_id" id="ws_id" value="<?= $ws_id ?>"/>
                <div class="row m-l-40 m-r-40">
                    <div class="col-xs-12">
                        <p>In order for a policy change to be made to this product, one of the qualifying life event must have occurred to this primary policy holder.  Please select the life event that is allowing this change, if no event of this occurred, this change is not allowed.</p>
        				<div class="form-group">
                            <select name="life_event" id="life_event" class="form-control">
                                <option data-hidden="true"></option>
                                <?php
                                    if(!empty($prd_conn_data['life_event_options'])) {
                                        foreach ($prd_conn_data['life_event_options'] as $option) {
                                            if(empty($LifeEventsOption[$option])) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?=$option?>"><?=$LifeEventsOption[$option]?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                            <label>Life Event</label>
                            <p class="error"><span id="err_life_event"></span></p>
        				</div>
                        <div class="form-group policy_div">
                            <select name="new_prd_id" id="new_prd_id" class="form-control">
                                <option data-hidden="true"></option>
                                <?php
                                    if(!empty($prd_conn_data['conn_prd'])) {
                                        foreach ($prd_conn_data['conn_prd'] as $prd_row) {
                                            ?>
                                            <option value="<?=$prd_row['id']?>" data-order_by="<?=$prd_row['order_by'] > $prd_conn_data['order_by']?"downgrade":"upgrade"?>" style="display: none;"><?=$prd_row['name']?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                            <label>Policy Change</label>
                            <p class="error"><span id="err_new_prd_id"></span></p>
                        </div>

        				<div class="text-center">
                            <button class="btn btn-action" id="final_save" name="final_save" type="button">Continue
                            </button>
                            <a href="javascript:void(0);" class="btn red-link" name="cancel" onclick="window.parent.$.colorbox.close()">Cancel</a>
        				</div>
                    </div>
                </div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
    var upgrade_life_event_options = <?=json_encode($prd_conn_data['upgrade_life_event_options'])?>;
    var downgrade_life_event_options = <?=!empty($prd_conn_data['downgrade_life_event_options'])?json_encode($prd_conn_data['downgrade_life_event_options']):"''"?>;
	$(document).ready(function(){
        $(document).off('change', '#life_event');
        $(document).on('change', '#life_event', function () {
            var life_event = $(this).val();
            var order_by = $("select#new_prd_id option:selected").attr('data-order_by');

            $(".policy_div").show();
            $("select#new_prd_id option").hide();

            if(jQuery.inArray(life_event,upgrade_life_event_options) !== -1 && jQuery.inArray(life_event,downgrade_life_event_options) !== -1) {
                $("select#new_prd_id option").show();

            } else if(jQuery.inArray(life_event,upgrade_life_event_options) !== -1) {
                $("select#new_prd_id option[data-order_by='upgrade']").show();

                if(order_by == "downgrade") {
                    $("select#new_prd_id").val('');
                    $("select#new_prd_id").removeClass('has-value');
                }
            } else if(jQuery.inArray(life_event,downgrade_life_event_options) !== -1) {
                $("select#new_prd_id option[data-order_by='downgrade']").show();

                if(order_by == "upgrade") {
                    $("select#new_prd_id").val('');
                    $("select#new_prd_id").removeClass('has-value');
                }
            }

            $("select#new_prd_id").selectpicker('refresh');
        });
        $(document).off('click', '#final_save');
        $(document).on('click', '#final_save', function () {
            $("[id^='err_']").html('');
            var is_error = false;
            if($("#life_event").val() == "") {
                $("#err_life_event").html('Please select Live Event');
                is_error = true;
            }
            if($("#new_prd_id").val() == "") {
                $("#err_new_prd_id").html('Please select Policy');
                is_error = true;   
            }
            if(is_error == false) {
                $("#ajax_loader").show();
                $("#frm_tier_change").submit();
            }
        });
	});
</script>
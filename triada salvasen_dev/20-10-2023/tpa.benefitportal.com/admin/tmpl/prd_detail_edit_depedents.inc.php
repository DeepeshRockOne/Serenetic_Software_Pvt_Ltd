<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Edit - <span class="fw300"><?=$name?> (<?=$display_id?>)</span></h4>
	</div>
	<div class="panel-body">
		<div class="row theme-form">
			<form id="frm_category" name="frm_category" action="" method="POST">
				<input type="hidden" name="id" value="<?=$dependent_id?>">
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" class="form-control" name="effective_date" id="effective_date" value="<?=$effective_date?>">
						<label>Effective Date</label>
						<p class="error"><span id="err_effective_date"></span></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<select class="form-control" id="termination_date" name="termination_date">
							<option value=""></option>
							<?php if($coverage_periods){
			                    foreach ($coverage_periods as $coverage) { ?>
			                        <option value="<?=$coverage['value']?>" <?=$coverage['value'] == $termination_date ? "selected='selected'" : ""?>><?=$coverage['text']?></option>
			                  <?php }
			                  } ?>
						</select>
						<label>Termination Date</label>
						<p class="error"><span id="err_termination_date"></span></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="active_since_date" id="active_since_date" class="form-control" value="<?=$active_since_date?>">
						<label>Active Member Since Date</label>
						<p class="error"><span id="err_active_since_date"></span></p>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="added_date" id="added_date" class="form-control" value="<?=$added_date?>">
						<label>Added Date</label>
						<p class="error"><span id="err_added_date"></span></p>
					</div>
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:void(0)" class="btn btn-action save" id="final_save">Save</a>
				<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#added_date").datepicker({
	        changeDay: true,
	        changeMonth: true,
	        changeYear: true,
	        endDate: '<?=date("m/d/Y")?>',
	    });

		$("#active_since_date").datepicker({
	        changeDay: true,
	        changeMonth: true,
	        changeYear: true,
	        endDate: '<?=date("m/d/Y")?>',
	    });

	    $("#effective_date").datepicker({
	        changeDay: true,
	        changeMonth: true,
	        changeYear: true,
	        startDate: '<?=date("m/d/Y",strtotime($ws_eligibility))?>',

	    });
		$(document).on('click', '#final_save', function () {
			$('#ajax_loader').show();
            $.ajax({
                method: 'POST',
                data: $("#frm_category").serialize(),
                dataType: 'json',
                success: function (res) {
                	if(res.status == 'success'){
                		window.parent.$.colorbox.close();
                		parent.location.reload();
                	}else if (res.status == "fail") {
                        $('#ajax_loader').hide();
                        is_error = true;
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
                            } else {
                                console.log("Not found Element => #err_" + index);
                            }
                        });
                    }
                }
            });

		});
	});
</script>
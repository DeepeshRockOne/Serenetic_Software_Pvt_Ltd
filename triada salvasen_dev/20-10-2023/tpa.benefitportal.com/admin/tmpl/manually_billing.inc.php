<div class="panel panel-default panel-block generate_report_panel">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Generate - <span class="fw300"><?=$file_name?></span></h4>
		</div>
	</div>
	<div class="panel-body">
		<!-- <p class="fw600 m-b-15">Coverage Month</p> -->
		<div class="theme-form row">
			<form method="" action="" id="frmSchedule" name="frmSchedule">
				<input type="hidden" name="file" value="<?=$file_id?>">
				<input type="hidden" name="generate_via" value="Download">
				<div class="col-xs-12">
					<div class="row">
						<div id="date_range" class="col-xs-12">
							<div class="form-group">
								<select class="form-control" id="join_range" name="join_range">
									<option value=""> </option>
									<option value="Range">Range</option>
									<option value="Exactly">Exactly</option>
									<option value="Before">Before</option>
									<option value="After">After</option>
								</select>
								<label>Added Date</label>
								<span class="error" id="error_join_range"></span>
							</div>
						</div>
						<div class="select_date_div col-xs-9" style="display:none">
							<div class="form-group">
							<div id="all_join" class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input type="text" name="added_date" id="added_date" class="form-control date_picker" />
								
							</div>
							<span class="error text-left" id="error_added_date"></span>
							<div  id="range_join" style="display:none;">
								<div class="phone-control-wrap">
								<div class="phone-addon">
									<label class="mn">From</label>
								</div>
								<div class="phone-addon">
									<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="text" name="fromdate" id="fromdate" value="" class="form-control date_picker" />
									</div>
									<span class="error text-left" id="error_fromdate"></span>
								</div>
								
								<div class="phone-addon">
									<label class="mn">To</label>
								</div>
								<div class="phone-addon">
									<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="text" name="todate" id="todate" value="" class="form-control date_picker" />
									</div>
									<span class="error text-left" id="error_todate"></span>
								</div>
								</div>
							</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel-footer text-center">
        <a href="javascript:void(0);" class="btn btn-action" id="schedule">Export</a>
		<a href="javascript:void(0);" onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
   </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var date = new Date();
		var MaxDate= new Date(date.getFullYear(), date.getMonth() + 1, 0);
		$(".date_picker").datepicker({
		    endDate: MaxDate
		});

		<?php /*$("#ftp_div1").fadeOut().hide();
		$("#email_div1").fadeOut().hide();
		  $(document).on('change','#generate_via1',function(){
		    $generate_via = $(this).val();
		    if($generate_via == 'Email'){
		      $("#email_div1").fadeIn().show();
		      $("#ftp_div1").fadeOut().hide();
		    }else if($generate_via == 'FTP'){
		       $("#ftp_div1").fadeIn().show();
		       $("#email_div1").fadeOut().hide();
		    }else{
		      $("#email_div1").fadeOut().hide();
		      $("#ftp_div1").fadeOut().hide();
		    }
		});*/ ?>

		$(document).on("click","#schedule",function(){
		    billing_schedule($(this));
		});  
	});

	$(document).off('change', '#join_range');
    $(document).on('change', '#join_range', function(e) {
      e.preventDefault();
      $('.date_picker').val('');
      if ($(this).val() == '') {
        $('.select_date_div').hide();
        $('#date_range').removeClass('col-xs-3').addClass('col-xs-12');
      } else {
        $('#date_range').removeClass('col-xs-12').addClass('col-xs-3');
        $('.select_date_div').show();
        if ($(this).val() == 'Range') {
          $('#range_join').show();
          $('#all_join').hide();
        } else {
          $('#range_join').hide();
          $('#all_join').show();
        }
      }
    });

function billing_schedule(btn_obj) {
    btn_obj.prop('disabled',true);
    parent.$("#ajax_loader").show();
    $('span.error').html('');
    $.ajax({
        url: '<?= $HOST ?>/admin/ajax_billing_requests.php',
        data: $("#frmSchedule").serialize(),
        type: 'POST',
        dataType: "json",
        success: function(res) {
            btn_obj.prop('disabled',false);
            parent.$("#ajax_loader").hide();
            if (res.status == "success") {
               	parent.swal({
				    text: "Export Data: You request has been submitted!",
				    showCancelButton: true,
				    confirmButtonText: "View Export(s)",
				    cancelButtonText: 'Close',
				}).then(function() {
					setTimeout(function(portal){
						parent.window.open("<?=$ADMIN_HOST?>/billing_export_requests.php",'_blank');	
						parent.$.colorbox.close();
					},300);
				}, function(dismiss) {
					parent.$.colorbox.close();
				});
            } else if(res.status=="fail") {
                var is_error = true;
                $.each(res.errors, function (index, value) {
                    $('#error_' + index).html(value).show();
                });
            
            }
        }
    });
}	
</script>

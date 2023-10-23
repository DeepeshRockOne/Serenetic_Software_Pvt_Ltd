<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Edit Post Date -  <span class="fw300"> Order <?=$odrDispId?></span></h4>
	</div>
	<div class="panel-body">
	<form id="postOrderFrm" action="edit_order_post_date.php" method="GET" class="theme-form">
		<input type="hidden" name="orderId" id="orderId" value="<?=$orderId?>">
		<input type="hidden" name="location" id="location" value="<?=$location?>">
		<div class="theme-form">
			<div class="form-group" id="postDatePicker">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<div class="pr">
						<input id="postDate" type="text" class="form-control" name="post_date" value="<?=$postDate?>">
						<label>Post Date (MM/DD/YYYY)</label>
					</div>
				</div>
				<span class="error" id="error_post_date"></span>
				<div class="text-right">
					<?php if($odrRes['is_renewal'] == "Y") { ?>
						<a href="javascript:void(0);" class="btn red-link" id="cancel_post_date">Cancel Post Payment</a>
					<?php } else { ?>
						<a href="javascript:void(0);" class="btn red-link" id="cancel_post_date">Cancel Application</a>
					<?php } ?>
					
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:void(0)" class="btn btn-info" id="btnSave">Save</a>
				<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</div>
	</form>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	var oldPostDate = $("#postDate").val();

	$("#postDate").datepicker({
		startDate: "<?=date("m/d/Y")?>",
		endDate: "<?=$endDate?>",
		// orientation: "top",
		enableOnReadonly: true,
		// container: "#postDatePicker",
		autoclose: true,
	});

	$('#postDate').on('changeDate', function(e) {
		if ($(this).val() == "<?=date('m/d/Y')?>") {
			parent.swal({
				text: "Set Today's Date and Attempt Order Now: Are you sure?",
				showCancelButton: true,
				confirmButtonText: 'Confirm',
			}).then(function() {

			}, function(dismiss) {
				$('#postDate').datepicker('update', oldPostDate);
			});
		}
	});

	$(document).on('click', "#btnSave", function() {
		$("#ajax_loader").show();
		var params = $('#postOrderFrm').serialize();
		$.ajax({
			url: $('#postOrderFrm').attr('action'),
			type: 'GET',
			data: params,
			dataType: 'JSON',
			success: function(res) {
				$("#ajax_loader").hide();
				if(res.status == 'success'){
					parent.setNotifySuccess(res.msg);
					parent.$.colorbox.close();
				}else if (res.status == 'success_attempt') {
					window.parent.location.reload();
				}else if (res.status == 'fail') {
		          var is_error = true;
		          $.each(res.errors, function(index, error) {
		            $('#error_' + index).html(error);
		            if (is_error) {
		              var offset = $('#error_' + index).offset();
		              if (typeof(offset) === "undefined") {
		                console.log("Not found : " + index);
		              } else {
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
	$(document).off('click', "#cancel_post_date");
	$(document).on('click', "#cancel_post_date", function() {
		 parent.swal({
          text: "Cancel <?=$odrRes['is_renewal'] == "Y"?"Post Payment":"Application"?>: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
        }).then(function() {
            $("#ajax_loader").show();
            $orderId = $('#orderId').val();
			$location = $('#location').val();
			$("#ajax_loader").show();
			$.ajax({
				url: '<?=$HOST?>/ajax_cancel_post_date.php',
				type: 'Post',
				data: {order_id:$orderId,location:$location},
				dataType: 'JSON',
				success: function(res) {
					$("#ajax_loader").hide();
					if(res.status == 'success'){
						parent.setNotifySuccess(res.message);
						window.parent.location.reload();
						parent.$.colorbox.close();
					}else if (res.status == 'not_found') {
						parent.setNotifyError(res.message);
						parent.$.colorbox.close();
					}
				}
			});
        }, function(dismiss) {
          
        });
	});
});
</script>
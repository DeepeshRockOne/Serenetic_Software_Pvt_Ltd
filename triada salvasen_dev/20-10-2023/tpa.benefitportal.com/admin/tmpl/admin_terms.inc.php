<div class="panel panel-default">
	<div class="panel-body">
		<?php /*include 'ajax_terms.php' */?>
		<?=$res_t['terms']?>
		<div class="text-center">
			<button type="submit" name="" id="agree-btn" href="" class="btn btn-info">Agree To Terms</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#agree-btn').click(function(){
		window.parent.$("#checkbox_signup").attr("checked", true);
		$.uniform.update();
		// $("#check_agree").checked(true);
		parent.$.colorbox.close();
	});

</script>

<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Joinder Agreement</h4>
	</div>
	<div class="panel-body">
			<?php if(!empty($agreementContent)) {?>

				<div class="timeline-panel">
					<div class="timeline-body">
						<?=$agreementContent?>
					</div>
				</div>
				<hr>
		<?php } ?>
	</div>
	<div class="text-center bg-white m-b-25">
		<a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="red-link">Close</a>
	</div>
</div>

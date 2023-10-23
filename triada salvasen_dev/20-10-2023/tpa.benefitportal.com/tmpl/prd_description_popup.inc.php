<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Product Details - <?=$description['name'] ? "(" . $description['name'] .")" : ""?></h4>
	</div>
	<div class="panel-body">
			<?php if(!empty($description['description'])) {?>

				<div class="timeline-panel">
					<div class="timeline-body">
						<?=$description['description']?>
					</div>
				</div>
				<hr>
			<?php } ?>
			<?php if(!empty($description['limitations_exclusions'])) {?>
				<h4>Limitations and Exclusions :</h4>
				<div class="timeline-panel">
					<div class="timeline-body">
						<?=$description['limitations_exclusions']?>
					</div>
				</div>
				<hr>
			<?php } ?>
	</div>
</div>
<?php /*
<script type="text/javascript">
	// $(document).ready(function(){
	// 	$id = <?=$temp_prd_id?>;
	// 	if(parent.$('#product_check_' + $id + ':checked').length > 0){
	// 		$('.note-editor-checkbox').prop('checked',true);
	// 		$.uniform.update();
	// 	}
	// });
</script>
*/ ?>
<!-- <?php if(!empty($terms_condition)) { ?> -->
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Terms and Conditions - <?=$terms_condition['name'] ? "(" . $terms_condition['name'] .")" : ""?></h4>
	</div>
	<div class="panel-body">
			<?php if(!empty($terms_condition['terms_condition'])) {?>

				<div class="timeline-panel">
					<div class="timeline-body">
						<?=$terms_condition['terms_condition']?>
					</div>
				</div>
				<hr>
		<?php } ?>
	</div>
</div>
<!-- <?php } ?> -->
<script type="text/javascript">
	$(document).ready(function(){
		$id = <?=$temp_prd_id?>;
		if(parent.$('#product_check_' + $id + ':checked').length > 0){
			$('.note-editor-checkbox').prop('checked',true);
			$.uniform.update();
		}
	});
</script>
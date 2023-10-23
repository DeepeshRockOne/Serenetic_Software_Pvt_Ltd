

<?php if(!empty($product_id)) { ?>
<div class="panel panel-default">
	<div class="panel-heading br-b">Enrollment Page</div>
	<?php if(!empty($description['id']) && !empty($description['description']) ) { ?>
	<div class="panel-body panel-shadowless">
		<?=$description['description']?>
	</div>
	<?php }else{ ?>
	<div class="panel-body panel-shadowless">
		No information found!
	</div>
	<?php } ?>
</div>
	<?php }else{ ?>

	<div class="panel panel-default">
	<div class="panel-heading br-b">Member Agreement</div>
		<?php if(!empty($member_terms['terms'])) { ?>
			<div class="panel-body panel-shadowless">
				<?=$member_terms['terms']?>
			</div>
		<?php } ?>
	</div>

<?php } ?>

<?php /*

<?php if(!empty($terms_condition)) { ?>
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Terms and Conditions</h4>
	</div>
	<div class="panel-body">
		<?php foreach($terms_condition as $terms){?>
			<?php if(!empty($terms['terms_condition'])) {

				$smart_tags = get_user_smart_tags($terms['id'],'product');
	            if($smart_tags){
	            	foreach ($smart_tags as $key => $value) {
	            		$terms['terms_condition'] = str_replace("[[" . $key . "]]", $value, $terms['terms_condition']);
	            	}
	            }

				?>

				<div class="timeline-panel">
					<div class="timeline-heading">
					<h4 class="timeline-title"><?=$terms['name']?></h4>
					</div>
					<div class="timeline-body">
						<?=$terms['terms_condition']?>
					</div>
				</div>
				<hr>
		<?php } } ?>
	</div>
</div>
<?php } ?>
<?php if(!empty($product_list) || !empty($order_id) || !empty($display_member_terms)) { ?>
<div class="panel panel-default">
	<div class="panel-heading br-b">Member Agreement</div>
	<?php if(!empty($member_terms['terms'])) { ?>
		<div class="panel-body panel-shadowless">
			<?=$member_terms['terms']?>
		</div>
	<?php } ?>
</div>
<?php } ?>

<?php if(empty($terms_condition)) { ?>
<div class="panel panel-default">
	<div class="panel-heading br-b">Enrollment Page</div>
	<?php if(!empty($description['id']) && !empty($description['description']) ) { ?>
	<div class="panel-body panel-shadowless">
		<?=$description['description']?>
	</div>
	<?php }else{ ?>
	<div class="panel-body panel-shadowless">
		No information found!
	</div>
	<?php } ?>
</div>
	<?php } ?>

*/ ?>

<div class="text-center bg-white m-b-25">
	<a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="red-link">Close</a>
</div>
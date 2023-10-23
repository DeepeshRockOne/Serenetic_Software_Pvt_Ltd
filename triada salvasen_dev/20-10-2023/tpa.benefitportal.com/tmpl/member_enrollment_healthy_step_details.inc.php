<div class="panel panel-default healthy_step_benefit">
	<div class="panel-heading bg_dark_primary">
		<h4 class="mn text-white">Healthy Steps Benefits</h4>
	</div>
	<ul class="nav nav-tabs">
	  	<?php if(!empty($resDetails)) { ?>
	  		<?php $i = 0 ?>
			<?php foreach ($resDetails as $key => $value) { ?>
				<li class="<?= $i==0 ? 'active' : '' ?>"><a data-toggle="tab" href="#healthy_<?= $value['id'] ?>"><?= $value['healthy_step_name'] ?></a></li>
				<?php $i++ ?>
			<?php } ?>
		<?php } ?>
	</ul>
<div class="panel-body">
	<div class="tab-content mn">
	    <?php if(!empty($resDetails)) { ?>
	  		<?php $i = 0 ?>
			<?php foreach ($resDetails as $key => $value) { ?>
					<div id="healthy_<?= $value['id'] ?>" class="tab-pane fade in <?= $i==0 ? 'active' : '' ?>">
						<?= $value['healthy_step_description'] ?>
					</div>
				<?php $i++ ?>
			<?php } ?>
		<?php } ?>
  	</div>
  </div>
</div>
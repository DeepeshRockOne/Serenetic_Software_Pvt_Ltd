<div class="panel panel-default panel-block ">
	<div class="panel-heading">
		<div class="panel-title">
			<p class="fs18"><strong class="fw500">Email Content </strong> <span class="fw300"><?=!empty($brodcast_name) ? $brodcast_name : 'T1234'?></span></p>
    </div>
  </div>
  <div class="panel-body">
  	<div class="row theme-form">
  		<div class="col-sm-6">
  			<div class="form-group">
  				<input type="text" class="form-control" value="<?=!empty($subject) ? $subject : ''?>" readonly="readonly">
  				<label>Subject</label>
  			</div>
  		</div>
  	</div>
    <div class="form-group height_auto">
    	<?php if(!empty($mail_content)) { ?>
    		<div class="thumbnail">
        	<?php echo htmlspecialchars_decode($mail_content);?>
    		</div>
			<?php } else { ?>
				<textarea class="form-control" rows="6">
        	Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
        </textarea>
      <?php } ?>
    </div>
    <div class="text-center">
      <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
    </div>
  </div>
</div>
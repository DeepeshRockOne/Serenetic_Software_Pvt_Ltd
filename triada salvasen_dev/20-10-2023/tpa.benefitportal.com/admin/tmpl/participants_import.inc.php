<?php 
if(!empty($PARTICIPANTS_IMPORT_ARR)){
	$columnCounter = 0;
	foreach ($PARTICIPANTS_IMPORT_ARR as $label => $fields) { 
?>
	<div class="line_title">
		<h3><span><?=$label?></span></h3>
	</div>
	<div class="row">
		<?php foreach ($fields as $k => $field) { ?>
			<div class="col-sm-12 col-md-6">
				<div class="form-group">
					<div class="input-group resources_addon">
					   <div class="input-group-addon">
					      <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$field['info']?>"></i> <?=$field['label']?>
					   </div>
					   <div class="pr">
					      	<select class="form-control select" name="fields[<?=$field['field_name']?>]" data-live-search="true">
					         	<option data-hidden="true"></option>
					         	<?php foreach ($row as $key => $value) {
					         		$selectedOption = "";
		                            if ($field['label'] == trim($value)) {
		                              $selectedOption = 'selected="selected"';
		                            } elseif ($columnCounter == $key) {
		                              $selectedOption = 'selected="selected"';
		                           	} 
			                  	?>

					         	<option value="<?=$value?>" <?php echo $selectedOption; ?>><?=$value?></option>
					         <?php } 
					         $columnCounter++; ?>>
					      </select>
					      <label>Select CSV Column</label>
					   </div>
					</div>
					<p class="error" id="error_<?=$field['field_name']?>"></p>
				</div>
			</div>
		<?php } ?>
	</div>
<?php 
	} 
}
?>
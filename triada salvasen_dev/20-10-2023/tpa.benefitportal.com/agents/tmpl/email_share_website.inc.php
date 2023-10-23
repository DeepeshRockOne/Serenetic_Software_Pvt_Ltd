<div class="panel panel-default ">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Share Website - <span class="fw300">Chris Pearson</span></h4>
		</div>
	</div>
	<div class="panel-body">
		<h4 class="m-b-20 fs18">Email</h4>
		<div class="theme-form">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="" class="form-control">
						<label>From</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="" class="form-control">
						<label>To</label>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<input type="text" name="" class="form-control">
						<label>Subject</label>
					</div>
				</div>
			</div>
			<div class="form-group height_auto">
				<textarea class="summernote"></textarea>
			</div>
			
		</div>
	</div>
	<div class="panel-footer text-center">
		<a href="javascript:void(0);" class="btn btn-action">Send</a>
		<a href="javascript:void(0);" class="btn red-link">Back</a>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('.summernote').summernote({
	  toolbar: $SUMMERNOTE_TOOLBAR,
	  disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
	  focus: true, // set focus to editable area after initializing summernote
	  height:250,
	  callbacks: {
	    onImageUpload: function(image) {
	      editor = $(this);
	      uploadImageContent(image[0], editor);
	    },
	    onMediaDelete : function(target) {
	        deleteImage(target[0].src);
	        target.remove();
	    }
	  }
	});
});
</script>
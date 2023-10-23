<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Resource - <span class="fw300"><?=$res['resource_name']?></span></h4>
	</div>
	<div class="panel-body">
		<div class="text-center">
			<h4 class="fs16 m-b-20">Resource - <?=$res['resource_name']?></h4>
			<p class="m-b-20">Choose to view or share the resource below. To remove click <a href="javascript:void(0);" class="red-link pn" id="remove-resources">here.</a></p>
			<div class="clearfix m-b-10">
				<a href="<?= $RESOURCE_DOCUMENT_WEB.$res['file'] ?>" class="btn btn-info" target="_blank">View</a>
				<a href="share_resources.php?id=<?=$id?>" class="btn btn-action share_resources">Share</a>
			</div>
		</div>
	</div>
	<div class="text-center">
		<a href="javascript:void(0)" class="red-link" onclick="window.parent.$.colorbox.close()">Cancel</a>
	</div>
</div>

<script type="text/javascript">
$(document).off('click', '.share_resources');
$(document).on('click', '.share_resources', function (e) {
  e.preventDefault();
  window.parent.$.colorbox({
    href: $(this).attr('href'),
    iframe: true, 
    width: '768px', 
    height: '80%'
  });
});

    $('#remove-resources').click(function(e){
        var id ='<?=$id?>';
    	  // parent.$.colorbox.close();
        parent.swal({   
            text: "<p class='fs18'>Remove Resource - <br><?=$res['resource_name']?>: Are you sure?</p>",   
            showCancelButton: true,   
            confirmButtonText: "Confirm",   
            closeOnConfirm: false 
        }).then(function(e) {
            $.ajax({
              url: "add_resources.php",
              type: 'POST',
              data: {id: id,is_delete:1,is_ajaxed:1},
              dataType:"json",
              beforeSend :function(e) {
                $("#ajax_loader").show();
              },
              success: function(res) {
                $("#ajax_loader").hide();
                if (res.status == 'success') {
                  parent.setNotifySuccess(res.msg,true);
                  parent.$.colorbox.close();
                  parent.location.reload();
                }else{
                  parent.setNotifyError(res.msg);
                  parent.$.colorbox.close();
                  parent.location.reload();
                }
              }
            });
          }, function(dismiss) {
        });
    });

</script>
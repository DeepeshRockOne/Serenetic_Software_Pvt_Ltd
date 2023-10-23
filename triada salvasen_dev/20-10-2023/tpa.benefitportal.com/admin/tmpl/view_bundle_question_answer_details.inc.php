<?php if($is_ajaxed){?>
<div class="table-responsive">
	<table class="<?=$table_class?>">
		<thead>
			<tr>
				<th>Answer</th>
				<th>Bundle</th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($total_rows)){
					foreach ($fetch_rows as $rows) { ?>
						<tr>
							<td><?=$rows['answer'];?></td>
							<td><?=$rows['bundle_label'];?></td>
						</tr>
					<?php } ?>
			<?php }else { ?>
					<tr>
						<td colspan = "2" class="text-center">No Record(s) found</td>
					</tr>
		  <?php } ?>
		</tbody>
      <tfoot>
		      <tr>
		         <?php if ($total_rows > 0) {?> 
		         <td colspan="3">
		            <?php echo $paginageLinks; ?>
		         </td>
		         <?php } ?>
		      </tr>
      </tfoot>
	</table>
</div>
<div class="text-center">
	<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
</div>
<?php  } else { ?>
<div class="panel panel-default panel-block">
	<form id="bundle_frm" action="view_bundle_question_answer_details.php" method="POST">
			<div class="panel-heading">
				<div class="panel-title">
					<p class="fs18">
						 <strong class="fw500">Question :</strong> 
						 <span class="fw300"><?=$question;?></span> 
					</p>
				</div>
			</div>
			<div class="panel-footer clearfix" style="display: none;">
				<input type="hidden" name="page" id="page" value="1" />
				<input type="hidden" name="perPages" id="perPages" value="25" />
				<input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
				<input type="hidden" name="id" id="id" value="<?=$question_id;?>" />
			</div>
	</form>
</div>
<div class="panel panel-default panel-block">
	<div class="panel-body">
		<div id="ajax_loader" class="ajex_loader" style="display: none;">
			<div class="loader"></div>
		</div>
		<div id="ajax_data"> </div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
 		popup_bundle_submit();

 		var perPages = $("#perPages").val();
 		var question_id = $("#id").val();
 		var is_ajaxed = $("#is_ajaxed").val();
 		var data = {
 			api_key : 'getQuestionAnswerBundleDetails',
 			id: question_id,
 			is_ajaxed : is_ajaxed, 
 			perPages : perPages
 		}
 		api_dropdown_pagination('ajax_data',data);
});

$(document).off('click', '#ajax_data ul.pagination li a');
$(document).on('click', '#ajax_data ul.pagination li a', function(e) {
   e.preventDefault();
   $('#ajax_loader').show();
    var is_ajaxed = $('#is_ajaxed').val();
    var question_id = $("#id").val();
    var perPages = $("#perPages").val();
    var page = $(this).attr('data-page');
    $("#page").val(page);
    
      $.ajax({
         url: $(this).attr('href'),
         data:{
		 			api_key : 'getQuestionAnswerBundleDetails',
		 			id: question_id,
		 			is_ajaxed : is_ajaxed, 
		 			perPages : perPages,
		 			page : page
		 		},
         type: 'POST',
         success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
            $("input[type='checkbox']").not('.js-switch').uniform();
         }
      });
});
function popup_bundle_submit(){
   	$('#ajax_loader').show();
   	$.ajax({
   		url :$('#bundle_frm').attr('action'),
   		type: 'POST',
      data: $('#bundle_frm').serialize(),
   		success :function(res){
   			$('#ajax_loader').hide();
        		$('#ajax_data').html(res).show();
        		common_select();
   		}
   	});
}
</script>
<?php } ?>

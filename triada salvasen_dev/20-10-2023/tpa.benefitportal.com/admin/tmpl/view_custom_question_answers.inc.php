<div class="panel panel-default panel-block popup-height">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500"><?=$display_label ?> </strong> <span class="fw300"> - Answers</span></p>
    </div>
  </div>
  <div class="panel-body">
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr class="data-head">
              <th>Answer</th>
              <th>Eligible if selected</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($resAnswers)) { ?>
              <?php foreach ($resAnswers as $rows) { ?>
                <tr>
                  <td><?= $rows['answer']?> </td>
                  <td><?= $rows['answer_eligible'] == 'Y' ? 'Yes' : 'No'?> </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2">No Answers found</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
        </div>
      </div>
  </div>
</div>
<script>
	$(document).ready(function(){
		var get_height = $('.popup-height').outerHeight() + 20;
		if(get_height < 700){
		console.log(get_height);
			parent.$.colorbox.resize({
				height: get_height+'px'
			});
		}
	});
</script>
<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="fs18 mn">+ Custom Question</h4>
    </div>
  </div>
  <div class="panel-body">
    <form  method="POST" id="customQuestionForm" enctype="multipart/form-data"  autocomplete="off">
      <input type="hidden" name="question" value="<?= $id ?>">
      <div class="theme-form">
        <div class="form-group height_auto">
          <select class="form-control" name="control_type" id="control_type">
            <option></option>
            <option value="radio" <?= !empty($control_type) && $control_type=="radio" ? 'selected' : '' ?>>Yes/No</option>
            <option value="select_multiple" <?= !empty($control_type) && $control_type=="select_multiple" ? 'selected' : '' ?>>Multiple Choice (Can select multiple answers)</option>
            <option value="select" <?= !empty($control_type) && $control_type=="select" ? 'selected' : '' ?>>Multiple Choice (Can select only one answer)</option>
            <option value="textarea" <?= !empty($control_type) && $control_type=="textarea" ? 'selected' : '' ?>>Short Answer</option>
          </select>
          <label>Question Type</label>
          <p class="error" id="error_control_type"></p>
        </div>
        <div class="form-group height_auto p-b-20">
          <input type="text" name="display_label" id="display_label" class="form-control" value="<?= !empty($display_label) ? $display_label : '' ?>">
          <label>Ask Question</label>
          <p class="error" id="error_display_label"></p>
        </div>

        <div id="answer_div" style="<?= !empty($control_type) && in_array($control_type,array('radio','select_multiple','select')) ? '' : 'display: none' ?>">
          <hr class="m-b-30">
          <div id="main_answer_div">
            <?php if(!empty($resAnswers)) { ?>
              <?php foreach ($resAnswers as $key => $value) { ?>
                <div class="row answer_div" id="answer_div_<?= $value['id'] ?>">
                  <div class="col-sm-8">
                    <div class="form-group height_auto">
                      <input type="text" name="answers[<?= $value['id'] ?>]" class="form-control" value="<?= $value['answer'] ?>">
                      <label>Answer</label>
                      <p class="error" id="error_answers<?= $value['id'] ?>"></p>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group height_auto">
                      <div class="phone-control-wrap">
                        <div class="phone-addon">
                          <select class="form-control" id="answer_eligible_<?= $value['id'] ?>" name="answer_eligible[<?= $value['id'] ?>]">
                            <option value=""></option>
                            <option value="Y" <?= $value['answer_eligible'] == 'Y' ? 'selected' : '' ?>>Yes</option>
                            <option value="N" <?= $value['answer_eligible'] == 'N' ? 'selected' : '' ?>>No</option>
                          </select>
                          <label>Eligible If Selected</label>
                          <p class="error" id="error_answer_eligible<?= $value['id'] ?>"></p>
                        </div>
                        <?php if($key > 0) { ?>
                        <div class="phone-addon" style="width: 20px" id="remove_div_<?= $value['id'] ?>">
                            <a href="javascript:void(0);" class="text-light-gray fs16 remove_answer" data-id="<?= $value['id'] ?>" data-remove-id="<?= $value['id'] ?>">X</a>
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>
          <div class="clearfix">
            <a href="javascript:void(0);" id="add_answer" class="btn btn-info">+ Answer</a>
          </div>
        </div>
         <div class="clearfix m-t-20 text-center">
             <a href="javascript:void(0);" class="btn btn-action" id="save_question">Save</a>
             <a href="javascript:void(0);" class="btn red-link" id="close_question">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<div id="answer_dynamic_div" style="display: none">
  <div class="row answer_div" id="answer_div_~number~">
    <div class="col-sm-8">
      <div class="form-group height_auto">
        <input type="text" name="answers[~number~]" class="form-control">
        <label>Answer</label>
        <p class="error" id="error_answers~number~"></p>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group height_auto">
        <div class="phone-control-wrap">
          <div class="phone-addon">
            <select id="answer_eligible_~number~" name="answer_eligible[~number~]">
              <option value=""></option>
              <option value="Y">Yes</option>
              <option value="N">No</option>
            </select>
            <label>Eligible If Selected</label>
            <p class="error" id="error_answer_eligible~number~"></p>
          </div>
          <div class="phone-addon" style="width: 20px" id="remove_div_~number~">
              <a href="javascript:void(0);" class="text-light-gray fs16 remove_answer" data-id="~number~" data-remove-id="">X</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).on("click","#add_answer",function(){
    loadAnswerDiv();
  });

  $(document).on("change","#control_type",function(){
    $val =$(this).val();

    $("#answer_div").hide();
    $("#main_answer_div").html('');
    if($val=="select_multiple" || $val=="select" || $val == "radio"){
      $("#answer_div").show();
      loadAnswerDiv();
    }
  });

  $(document).on("click",".remove_answer",function(){
    $id=$(this).attr('data-id');
    $removeID=$(this).attr('data-remove-id');


      $("#answer_div_"+$id).remove();
    
  });

  $(document).on("click","#close_question",function(){
    window.parent.$.colorbox.close();
  });
  $(document).on("click","#save_question",function(){
    $("#ajax_loader").show();
    $.ajax({
      url:'<?= $ADMIN_HOST ?>/ajax_add_custom_question.php',
      dataType:'JSON',
      data:$("#customQuestionForm").serialize(),
      type:'POST',
      success: function(res) {
        $('#ajax_loader').hide();
          if (res.status == 'success') {
            window.parent.$.colorbox.close();
          } else if (res.status == 'fail') {
              var is_error = true;
              $.each(res.errors, function (index, error) {
                  $('#error_' + index).html(error);
                  if (is_error) {
                      var offset = $('#error_' + index).offset();
                      if(typeof(offset) === "undefined"){
                          console.log("Not found : "+index);
                      }else{
                          var offsetTop = offset.top;
                          var totalScroll = offsetTop - 195;
                          $('body,html').animate({
                              scrollTop: totalScroll
                          }, 1200);
                          is_error = false;
                      }
                  }
              });
          }
          return false;
      }
    });
  });
  loadAnswerDiv = function(){
    $count=$("#customQuestionForm .answer_div").length;
    $number=$count+1;
      
    html = $('#answer_dynamic_div').html();
    $('#main_answer_div').append(html.replace(/~number~/g, '-'+$number));
    if($number==1){
      $("#remove_div_-"+$number).hide();
    }
    $("#answer_eligible_-"+$number).addClass('form-control');
    $("#answer_eligible_-"+$number).selectpicker({ 
        container: 'body', 
        style:'btn-select',
        noneSelectedText: '',
        dropupAuto:false,
      });
  }
</script>
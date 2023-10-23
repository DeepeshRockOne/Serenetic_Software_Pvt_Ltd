<form id="templateForm" action="ajax_manage_template.php" name="templateForm">
    <input type="hidden" name="templateId" value="<?=$templateId?>">
    <input type="hidden" name="action" value="<?=$action?>">

<div class="panel panel-default panel-block">
  <div class="panel-body ">
    <div class="theme-form">
      <h4 class="m-b-20">+ Template</h4>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <input type="text" class="form-control" name="title" value="<?=$title?>" id="title">
            <label>Name Template</label>
            <span class="error" id="error_title"></span>
          </div>
        </div>
         <div class="col-sm-6">
          <div class="form-group">
             <select class="form-control" name="company_id" id="companyId">
              <option></option>
              <?php 
                if(!empty($companyRes)){
                  foreach ($companyRes as $company) {
                ?>
                  <option value="<?=$company['id']?>" <?=($companyId==$company['id']) ? 'selected="selected"' : ''?>><?=$company['company_name']?></option>
                <?php
                  }
                }
              ?>
            </select>
            <label>Company</label>
            <span class="error" id="error_company_id"></span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-9">
          <textarea class="summernote" name="content" id="content"><?=$content?></textarea>
          <span class="error" id="error_content"></span>
          <div class="m-t-30">
            <a href="javascript:void(0);" class="btn btn-action" id="saveTemplateBtn">Save</a>
            <a href="javascript:void(0);" class="btn red-link" onclick="window.location.href='emailer_template.php'">Cancel</a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="editor_tag_wrap" style="max-width: 100%;">
            <div class="tag_head"><h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4></div>
            <div class="editor_tag_wrap_inner" style="max-height:350px;">
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[MemberName]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[MemberPortalLoginPage]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[EnrollingAgentDisplayName]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[EnrollingAgentDisplayEmail]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[EnrollingAgentDisplayPhone]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[AgentName]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[AgentPortalLoginPage]]</label>
                </div>
              </div>
            </div>
             <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[EmployerGroupName]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[EmployerGroupPortalLoginPage]]</label>
                </div>
              </div>
            </div>
            <div>
              <div class="phone-control-wrap">
                 <div class="phone-addon text-left w-30">
                  <span class="fa fa-info-circle text-blue fs18"></span>
                </div>
                <div class="phone-addon">
                  <label>[[msg_content]]</label>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
  initCKEditor("content",false,"335px");

  $(".editor_tag_wrap_inner").mCustomScrollbar({
      theme:"dark",
      scrollbarPosition: "outside"
  });

  // Save template Code Start
    $(document).off("click","#saveTemplateBtn");
    $(document).on("click","#saveTemplateBtn",function(e){
      saveTemplate();
    });


  });


  saveTemplate = function(){
    $("#content").val(CKEDITOR.instances.content.getData());
    var params = $('#templateForm').serialize();
    $(".error").html('').hide();
    $.ajax({
      url: $('#templateForm').attr('action'),
      type: 'POST',
      data: params,
      dataType : 'json',
      beforeSend:function(){
                  $("#ajax_loader").show();
                },
      success: function(res) {
        $('#ajax_loader').hide();
        if (res.status == 'success') {
          setTimeout(function(){ 
              window.location.href = 'emailer_template.php';
            },1000); 
          setNotifySuccess(res.msg);
        } else if (res.status == 'fail') {
          if(typeof res.msg !== 'undefined'){
           setNotifyError(res.msg);
          }
           var is_error = true;
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 150;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    });
  }
</script>
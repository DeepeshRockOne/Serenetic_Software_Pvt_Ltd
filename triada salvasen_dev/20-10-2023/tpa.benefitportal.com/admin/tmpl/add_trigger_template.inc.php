<?php include("notify.inc.php"); ?>  
<div class="tabbing-tab">
  <ul class="nav nav-tabs customtab"> 
    <?php
    $trigger = "active";
    include("br_broadcaster_tabs.inc.php");
    ?> 
  </ul>
</div>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div>
      <i class="icon-envelope"></i>
      <h1><span>Add Trigger Template</span></h1>
    </div>
  </div>
</div>
<div class="tabbing-tab">
  <ul class="nav nav-tabs customtab">
    <?php $trigger_template = 'active'; ?>
    <?php include_once('triggers_tabs.inc.php'); ?>  
  </ul>
</div>	
<div class="panel panel-default panel-block ">
  <div class="list-group">
    <div class="list-group-item">					
      <div class="panel panel-default ">
        <div class="row">
          <div class="panel-heading">
            <form method="post" name="template_frm" id="template_frm">
              <div class="form-group">
                <label class="col-lg-1" for="title">Title<em>*</em></label>
                <div class="col-lg-3">
                  <input type="text" value="<?php echo $title; ?>" class="form-control" name="title" id="title">
                  <ul class="parsley-error-list" id="err_title"><li class="required"></li></ul>
                </div>
                <label class="col-lg-1" for="Company">Company<em>*</em></label>
                <div class="col-lg-3">
                  <select id="company_id" name="company_id" class="form-control <?php echo isset($errors['company_id']) ? 'parsley-error' : '' ?>">
                        <option value="">-- Select company --</option>
                        <?php foreach ($company_res AS $key => $row) { ?>
                            <option value="<?= $row['id'] ?>" <?= $company_id == $row['id'] ? 'selected' : '' ?>><?= $row['company_name'] ?></option>
                        <?php } ?>
                    </select>
                      <ul class="parsley-error-list" id="err_company_id"><li class="required"></li></ul>
                </div>
                <div class="col-lg-4">
                  <label for="template_type" class="m-r-10">Type<em>*</em></label>
                  <label class="radio-inline">
                    <input type="radio" name="template_type" id="template_type_default" value="default"  <?php echo $type == 'default' ? 'checked' : ''; ?>>
                    Default
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="template_type" id="template_type_custom" value="custom"  <?php echo $type == 'custom' ? 'checked' : ''; ?>>
                    Custom
                  </label>
                      <ul class="parsley-error-list" id="err_template_type"><li class="required"></li></ul>
                </div>

                <div class="col-md-12 text-center m-t-20">
                  <textarea  name="template_data" id="template_data" style="display: none"></textarea>
                  <button type="button" name="save" id="save" class="btn btn-info">Save</button>
                  <input type="hidden" name="type" value="save">
                  <a class="btn btn-default" href="trigger_template.php">Cancel</a>
                </div>
              </div>
              <div class="clearfix"></div><br/>

              <div id="defaultContentDiv" style="<?= $type=='default' ? '' : 'display: none'; ?>">
                <div class="col-md-6 col-lg-4">
                  <div id="demo-accordion" class="panel-collapsetab panel-group">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a href="#demo-collapseOne" data-parent="#demo-accordion" data-toggle="collapse" class="accordion-toggle">
                            LOGO IMAGES
                          </a>
                        </h4>
                      </div>
                      <div class="panel-collapse collapse in" id="demo-collapseOne" style="height: auto;">
                        <div class="panel-body img_scroll">
                          <?php if (!empty($imgArray)) { ?>
                            <?php foreach ($imgArray as $key => $value) { ?>
                              <div class="trigger_logo <?php echo isset($trg_img_check[$value['id']]) ? 'active' : ''; ?>" data-entity-id="<?= $value['id']; ?>" id="trigger_logo_<?= $value['id']; ?>">
                                <img src="<?php echo $TRIGGER_IMAGE_WEB . $value['src']; ?>" id="trg_img_<?= $value['id']; ?>">
                                <input type="checkbox" name="trg_image_check[<?= $value['id'] ?>]" value="<?= $value['id']; ?>" id="trg_image_check_<?= $value['id']; ?>" <?php echo isset($trg_img_check[$value['id']]) ? 'checked' : ''; ?>  style="display: none;"/>
                              </div>
                            <?php } ?>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a href="#demo-collapseTwo" data-parent="#demo-accordion" data-toggle="collapse" class="accordion-toggle collapsed">
                            ADDRESS
                          </a>
                        </h4>
                      </div>
                      <div class="panel-collapse collapse" id="demo-collapseTwo" style="height: 0px;">
                        <div class="panel-body address_scroll">
                          <?php if (!empty($addressArray)) { ?>
                            <?php foreach ($addressArray as $key => $value) { ?>
                              <div class="trigger_address <?php echo isset($trg_address_check[$value['id']]) ? 'active' : ''; ?>" data-entity-id="<?= $value['id']; ?>" id="trigger_address_<?= $value['id']; ?>">
                                <input type="hidden" name="trigger_address" id="trigger_add_hidden_<?= $value['id']; ?>" value="<?= strip_tags($value['address']); ?>"/>
                                <?php echo html_entity_decode($value['address']); ?>
                                <input type="checkbox" name="trg_address_check[<?= $value['id'] ?>]" value="<?= $value['id']; ?>" id="trg_address_check_<?= $value['id']; ?>" <?php echo isset($trg_address_check[$value['id']]) ? 'checked' : ''; ?> style="display: none;"/>
                              </div>
                              <div class="clearfix"></div>
                            <?php } ?>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4 class="panel-title">
                          <a href="#demo-collapseThree" data-parent="#demo-accordion" data-toggle="collapse" class="accordion-toggle collapsed">
                            FOOTERS
                          </a>
                        </h4>
                      </div>
                      <div class="panel-collapse collapse" id="demo-collapseThree" style="height: 0px;">
                        <div class="panel-body footer_scroll">
                          <?php if (!empty($footerArray)) { ?>
                            <?php foreach ($footerArray as $key => $value) { ?>
                              <div class="trigger_footer <?php echo $value['id'] == $trg_footer_check ? 'active' : ''; ?>" data-toggle="popover" data-content="<?php echo $value['content']; ?>" data-entity-id="<?= $value['id']; ?>" id="trigger_footer_<?= $value['id']; ?>">
                                <input style="display: none" type="hidden" name="trigger_footer" id="trigger_foot_hidden_<?= $value['id']; ?>" value="<?= $value['content']; ?>"/>
                                <?php echo wrap_content(html_entity_decode($value['content']), 120) . "<br/><br/>"; ?>
                                <input type="radio" name="trg_footer_check" value="<?= $value['id']; ?>" id="trg_footer_check_<?= $value['id']; ?>" <?php echo $value['id'] == $trg_footer_check ? 'checked' : ''; ?> style="display: none;"/>
                              </div>
                              <div class="clearfix"></div>
                            <?php } ?>
                          <?php } ?>
                        </div>
                      </div>
                      <input type="hidden" name="selected_footer_id" id="selected_footer_id" value="">
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-8" style="background-color: #ffffff;  color: #000000;  font-family: Verdana, Arial, sans-serif;  font-size: 12px;  line-height: 1.4em;">
                	<div class="table-responsive">
                    <table width="700" cellspacing="0" cellpadding="20" border="0" align="center" style="border-top: 1px solid #cccccc;  border-left: 1px solid #cccccc;  border-right: 1px solid #cccccc;  border-bottom: 1px solid #cccccc;">
                      <tr>
                        <td style="font-size: 12px;  line-height: 1.4em;  border-bottom: 1px solid #cccccc; height: 160px">
                          <table width="100%"  cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #cccccc;  border-left: 1px solid #cccccc; border: none;">
                            <tr>
                              <td width="65%" style="font-size: 12px;  line-height: 1.4em; border: none; " id="trg_img_id_disp">
                                <?php if (!empty($trg_img_check)) { ?>
                                  <?php foreach ($trg_img_check as $val) { ?>
                                    <img src="<?php echo $TRIGGER_IMAGE_WEB . $imgArray[$val]['src']; ?>" id="trg_img_remove_<?= $val ?>"/>
                                  <?php } ?>
                                <?php } ?>
                                  <ul class="parsley-error-list" id="err_trg_image_check"><li class="required"></li></ul>
                              </td>  
                              <td width="35%" class="note" style="color: #888888;  font-size: 10px; border: none; " id="trg_address_id_disp">
                                <?php if (!empty($trg_address_check)) { ?>
                                  <?php foreach ($trg_address_check as $val) { ?>
                                    <div id="trg_address_remove_<?= $val; ?>"><?php echo html_entity_decode($addressArray[$val]['address']); ?></div>
                                  <?php } ?>
                                <?php } ?>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size: 12px;  line-height: 1.4em; border-bottom: 1px solid #cccccc;">
                          <h2 style="font-family: Trebuchet, Trebuchet MS, Verdana, Arial, sans-serif; align: center; font-size: 1.3em; height: 200px;  color: #DF521A;">Content comes here.</h2>
                        </td>  
                      </tr>
                      <tr>
                        <td class="note" style="font-size: 12px;  line-height: 1.4em; color: #888888;  font-size: 10px; height: 100px;" id="trg_footer_id_disp">
                          <?php if ($trg_footer_check > 0) { ?>
                            <div id="trg_footer_remove_<?= $trg_footer_check; ?>"> <?php echo html_entity_decode($footerArray[$trg_footer_check]['content']) ?></div>
                          <?php } ?>
                        </td>  
                      </tr>
                    </table>
  								</div>
                </div>
                <div id="content_data" style="display: none">
                  <table width="700" cellspacing="0" cellpadding="20" border="0" align="center" style="border-top: 1px solid #cccccc;  border-left: 1px solid #cccccc;  border-right: 1px solid #cccccc;  border-bottom: 1px solid #cccccc;">
                    <tr>
                      <td style="font-size: 12px;  line-height: 1.4em;  border-bottom: 1px solid #cccccc;">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #cccccc; border-left: 1px solid #cccccc; border: none;">
                          <tr>
                            <td width="65%" style="font-size: 12px;  line-height: 1.4em; border: none; " id="trg_img_id_hide">
                              <?php if (!empty($trg_img_check)) { ?>
                                <?php foreach ($trg_img_check as $val) { ?>
                                  [[trg_img_<?= $val; ?>]]
                                <?php } ?>
                              <?php } ?>
                            </td>  
                            <td width="35%" class="note" style="color: #888888;  font-size: 10px; border: none; " id="trg_address_id_hide">
                              <?php if (!empty($trg_address_check)) { ?>
                                <?php foreach ($trg_address_check as $val) { ?>
                                  [[trg_address_<?= $val; ?>]]
                                <?php } ?>
                              <?php } ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:0 10px; font-family:Verdana, Geneva, sans-serif; line-height: 1.4em; border-bottom: 1px solid #cccccc; ">
                        <br/> [[msg_content]] <br/>&nbsp;
                      </td>  
                    </tr>
                    <tr>
                      <td class="note" style="font-size: 12px;  line-height: 1.4em; color: #888888;  font-size: 10px;" id="trg_footer_id_hide">
                        <?php if ($trg_footer_check > 0) { ?>
                          [[trg_footer_<?= $trg_footer_check; ?>]]
                        <?php } ?>
                      </td>  
                    </tr>
                  </table>
                </div>
              </div>

              <div id="customContentDiv" style="<?= $type=='custom' ? '' : 'display: none'; ?>">
                <div class="col-md-12">  
                  <div class="form-group height_auto">
                    <textarea id="content" class="form-control ckeditor" name="content"><?php echo $content; ?></textarea>
                      <ul class="parsley-error-list" id="err_content"><li class="required"></li></ul>
                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>       
    </div>
  </div>
  <script language="javascript" type="text/javascript">
    function confirmAction() {
      var confirmed = confirm("Are you sure you want to delete this trigger category? \nNote: If you delete this category, related triggers will also automatically deleted.");
      return confirmed;
    }
    $(document).on("click",'input[name="template_type"]',function(){
      $val=$(this).val();
      if($val=="default"){
        $("#defaultContentDiv").show();
        $("#customContentDiv").hide();
      }else{
        $("#defaultContentDiv").hide();
        $("#customContentDiv").show();
      }
    });
    $(document).ready(function() {

      CKEDITOR.replace('content', {
        allowedContent: true,
        toolbar: [
          ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Link', 'Unlink', 'Format','Font', 'FontSize','TextColor', 'BGColor','Source','-','Maximize','MyImage' ],
        ]

      });
      
      $('.img_scroll').slimscroll({height: 400, width: '100%'});
      $('.address_scroll').slimscroll({height: 400, width: '100%'});
      $('.footer_scroll').slimscroll({height: 400, width: '100%'});

      $('.trigger_logo').on('click', function() {
        var id = $(this).attr('data-entity-id');
        if ($('#trg_image_check_' + id).is(':checked')) {
          $('#trigger_logo_' + id).removeClass('active');
          //$('#trg_image_check_' + id).removeAttr('checked', 'checked');
          $('#trg_image_check_' + id).prop('checked', false);
          $('#trg_img_remove_' + id).remove();
          var data = $('#trg_img_id_hide').html();
          var image_data = $.trim(data).replace('[[trg_img_' + id + ']]', '');
          $('#trg_img_id_hide').html(image_data);
        } else {
          $('#trigger_logo_' + id).addClass('active');
          //$('#trg_image_check_' + id).attr('checked', 'checked');
          $('#trg_image_check_' + id).prop('checked', true);
          var logo_src = $(this).find($('img')).attr('src');
          $('#trg_img_id_disp').append('<img src=' + logo_src + ' id="trg_img_remove_' + id + '">');
          $('#trg_img_id_hide').append('[[trg_img_' + id + ']] ');
        }
      });

      $('.trigger_address').on('click', function() {
        var id = $(this).attr('data-entity-id');
        if ($('#trg_address_check_' + id).is(':checked')) {
          $('#trigger_address_' + id).removeClass('active');
          //$('#trg_address_check_' + id).removeAttr('checked', 'checked');
          $('#trg_address_check_' + id).prop('checked',false);
          $('#trg_address_remove_' + id).remove();
          var data = $('#trg_address_id_hide').html();
          var address_data = $.trim(data).replace('[[trg_address_' + id + ']]', '');
          $('#trg_address_id_hide').html(address_data);
        } else {
          $('#trigger_address_' + id).addClass('active');
          //$('#trg_address_check_' + id).attr('checked', 'checked');
          $('#trg_address_check_' + id).prop('checked',true);
          var address = $('#trigger_add_hidden_' + id).val();
          $('#trg_address_id_disp').append('<div id="trg_address_remove_' + id + '" style="font-size: 10px;  line-height: 1.4em; padding: 2px 0 2px 0;">' + address + '</div>');
          $('#trg_address_id_hide').append('[[trg_address_' + id + ']] ');
        }
      });


      $('.trigger_footer').on('click', function() {
        var id = $(this).attr('data-entity-id');
        $('.trigger_footer.active').removeClass('active');
        $(this).addClass('active');
        $('#trg_footer_check_' + id).attr('checked', 'checked');
        $('#selected_footer_id').val(id);

        var footer = $('#trigger_foot_hidden_' + id).val();
        $('#trg_footer_id_disp').html('<div id="trg_footer_remove_' + id + '">' + footer + '</div>');
        $('#trg_footer_id_hide').html('[[trg_footer_' + id + ']] ');
      });

      $('.trigger_footer').popover({
        html: true,
        trigger: 'manual',
        container: $(this).attr('id'),
        placement: 'bottom',
        content: function() {
          $return = '<div class="hover-hovercard"></div>';
        }
      }).on("mouseenter", function() {
        var _this = this;
        $(this).popover("show");
        $(this).siblings(".popover").on("mouseleave", function() {
          $(_this).popover('hide');
        });
      }).on("mouseleave", function() {
        var _this = this;
        setTimeout(function() {
          if (!$(".popover:hover").length) {
            $(_this).popover("hide")
          }
        }, 100);
      });


      $(document).on("click",'#save',function(){
        //alert('operation');

        $(".parsley-error-list").html("");
        var content_data = $('#content_data').html();
        $("#content").val(CKEDITOR.instances.content.getData());
        $("#template_data").val(content_data);
        //$('#template_frm').submit();
        $.ajax({
          url: 'ajax_add_trigger_template.php',
          type: 'POST',
          data: $('#template_frm').serialize(),
          dataType: 'json',
          success: function (res) {
            if (res.status == 'success') {
              window.location=('trigger_template.php');
            } else {
              $.each(res.error,function(key,val){
                  $("#err_"+key).html(val);
              });
            }
          }
        });
      });

    });

  </script>

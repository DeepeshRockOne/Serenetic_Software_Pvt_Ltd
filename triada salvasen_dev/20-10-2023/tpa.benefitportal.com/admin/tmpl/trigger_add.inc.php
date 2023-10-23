<?php include_once 'notify.inc.php';?>
<div class="tabbing-tab">
  <ul class="nav nav-tabs customtab">
    <?php
    $trigger = "active";
    include "br_broadcaster_tabs.inc.php";
    ?>
  </ul>
</div>
<div class="tigger_add">
  <form name="frm_trigger" id="frm_trigger" method="post" class="form-horizontal">
    <div class="panel panel-default panel-block">
      <div class="list-group">
        <div class="list-group-item">
          <div class="form-group">
            <label for="fields" class="col-md-2">Category</label>
            <div class="col-lg-4">
              <select id="category" name="category" class="form-control select2 select2-offscreen <?php echo isset($errors['category']) ? 'parsley-error' : '' ?>">
                <option value="">-- Select Category --</option>
                <?php if(!empty($crs)){ ?>
                <?php foreach ($crs AS $key => $crow) {?>
                <option value="<?=$crow['id']?>" <?=$category == $crow['id'] ? 'selected' : ''?>><?=$crow['title'] . " (" . $crow['company_name'] . ")"?></option>
                <?php }?>
                <?php }?>
              </select>
              <?php if (isset($errors['category'])): ?>
              <ul class="parsley-error-list"><li class="required"><?php echo $errors['category'] ?></li></ul>
              <?php endif;?>
            </div>
          </div>
          <div class="form-group">
            <label for="fields" class="col-md-2">Company</label>
            <div class="col-lg-4">
              <select id="company_id" name="company_id" class="form-control <?php echo isset($errors['company_id']) ? 'parsley-error' : '' ?>">
                <option value="">-- Select company --</option>
                <?php if(!empty($company_res)){ ?>
                <?php foreach ($company_res AS $key => $row) {?>
                <option value="<?=$row['id']?>" <?=$company_id == $row['id'] ? 'selected' : ''?>><?=$row['company_name']?></option>
                <?php }?>
                <?php }?>
              </select>
              <?php if (isset($errors['company_id'])): ?>
              <ul class="parsley-error-list"><li class="required"><?php echo $errors['company_id'] ?></li></ul>
              <?php endif;?>
            </div>
          </div>
          <div class="form-group">
            <label for="fields" class="col-md-2">Template</label>
            <div class="col-md-4">
              <select id="category" name="template" class="form-control select2 <?php echo isset($errors['template']) ? 'parsley-error' : '' ?>">
                <option value="">-- Select Template --</option>
                <?php if(!empty($templatedata)){ ?>
                <?php foreach ($templatedata AS $key => $row) {?>
                <option value="<?=$row['id']?>" <?=($template_id == $row['id']) ? 'selected' : ''?>><?=$row['title']?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if (isset($errors['template'])): ?>
              <ul class="parsley-error-list"><li class="required"><?php echo $errors['template'] ?></li></ul>
              <?php endif;?>
            </div>
          </div>
          <div id="after_category" style="<?= ((!empty($errors) || $category)?"display:block;":"display:none;")
            ?>">
            <div class="form-group">
              <label for="fields" class="col-md-2">Title</label>
              <div class="col-lg-4">
                <input type="text" id="title" name="title" value="<?=$title?>" class="form-control <?php echo isset($errors['title']) ? 'parsley-error' : '' ?>"/>
                <?php if (isset($errors['title'])): ?>
                <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                <?php endif;?>
                
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-md-2">Description</label>
              <div class="col-lg-9">
                <textarea id="description" name="description" rows="5" class="form-control <?php echo isset($errors['description']) ? 'parsley-error' : '' ?>"><?=$description?></textarea>
                <?php if (isset($errors['description'])): ?>
                <ul class="parsley-error-list"><li class="required"><?php echo $errors['description'] ?></li></ul>
                <?php endif;?>
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-md-2">This communication will send</label>
              <div class="col-lg-4">
                <select id="type" name="type" class="form-control select2 select2-offscreen <?php echo isset($errors['type']) ? 'parsley-error' : '' ?>">
                  <option  value=""></option>
                  <option value="Email" <?=$type == 'Email' ? 'selected' : ''?>>Email</option>
                  <option value="SMS" <?=$type == 'SMS' ? 'selected' : ''?>>SMS</option>
                  <option value="Both" <?=$type == 'Both' ? 'selected' : ''?>>Both</option>
                </select>
                <?php if (isset($errors['type'])): ?>
                <ul class="parsley-error-list"><li class="required"><?php echo $errors['type'] ?></li></ul>
                <?php endif;?>
              </div>
            </div>
            <div class="smstp tp"  style="<?=($type == 'SMS' || $type == 'Both') ? 'display: block' : 'display:none';?>">
              <div class="smstp tp form-group">
                <label for="sms_content" class="col-md-2">SMS Content</label>
                <div class="col-lg-8">
                  <textarea id="sms_content" name="sms_content" rows="4" class="form-control <?php echo isset($errors['sms_content']) ? 'parsley-error' : '' ?>" maxlength="160"><?=$sms_content?></textarea> 
                  <span id="message1">160</span> Characters Remaining
                  <p class="help-block">Available tags: [[fname]], [[lname]], [[username]], [[email]], [[link]], [[invite_link]]</p>
                  <?php if (isset($errors['sms_content'])): ?>
                  <ul class="parsley-error-list"><li class="required"><?php echo $errors['sms_content'] ?></li></ul>
                  <?php endif;?>
                </div>
              </div>
            </div>
            <div class="emailtp tp"  style="<?=($type == 'Email' || $type == 'Both') ? 'display: block' : 'display:none';?>">
              <div class="emailtp tp form-group">
                <label for="email_subject" class="col-md-2">Email Subject</label>
                <div class="col-lg-4">
                  <input type="text" id="email_subject" name="email_subject" value="<?=$email_subject?>" class="form-control <?php echo isset($errors['email_subject']) ? 'parsley-error' : '' ?>"/>
                  <?php if (isset($errors['email_subject'])): ?>
                  <ul class="parsley-error-list"><li class="required"><?php echo $errors['email_subject'] ?></li></ul>
                  <?php endif;?>
                </div>
              </div>
            </div>
            <div class="emailtp tp" style="<?=($type == 'Email' || $type == 'Both') ? 'display: block' : 'display:none';?>">
              <div class="emailtp tp  form-group">
                <label for="email_content" class="col-md-2">Email Content</label>
                <div class="col-lg-10">
                  <textarea  id="email_content" name="email_content" class="ckeditor"><?=$email_content?></textarea>
                  <p class="help-block">Available tags: [[fname]], [[lname]], [[username]], [[email]], [[link]], [[invite_link]]</p>
                  <?php if (isset($errors['email_content'])): ?>
                  <ul class="parsley-error-list"><li class="required"><?php echo $errors['email_content'] ?></li></ul>
                  <?php endif;?>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-md-2">&nbsp;</label>
            <div class="col-lg-8">
              <button type="submit" name="save" id="save" class="btn btn-info">Save</button>
              <button type="button" name="cancel" id="cancel" class="btn btn-default" onclick="window.location = 'triggers.php';">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">

  $(document).ready(function() {

    CKEDITOR.replace('email_content', {
      allowedContent: true,
      toolbar: [
        ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Link', 'Unlink', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', '-', 'Maximize', 'MyImage'],
      ]

    });

    $('#type').change(function() {
      if ($(this).val().toLowerCase() == "both") {
        $('.tp').show();
      } else {
        var tp = $(this).val().toLowerCase() + 'tp';
        $('.tp').hide();
        $('.' + tp).show();
      }
    });

    $('#category').change(function() {
      $('#after_category').show();
    });

    $("#sms_content").keyup(function(e) {
      var chars = $(this).val().length;
      $("#message1").text(160 - chars);

      if (chars > 160 || chars <= 0) {
        $("#message1").addClass("minus");
        $(this).css("text-decoration", "line-through");
      } else {
        $("#message1").removeClass("minus");
        $(this).css("text-decoration", "");
        e.preventDefault();
      }
    });

  });
</script>

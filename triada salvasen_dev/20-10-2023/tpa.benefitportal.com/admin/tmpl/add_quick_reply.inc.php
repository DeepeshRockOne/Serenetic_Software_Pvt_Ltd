<?php include("notify.inc.php"); ?>
<?php /* <div class="panel panel-default panel-block panel-title-block">
  <div class="panel-heading">
    <div>
      <i class="fa fa-mail-reply"></i><h1><span>Add <?php echo $type; ?> Quick Reply</span></h1>
    </div>
  </div>
</div> */ ?>
<form role="form" method="post" class="uform" name="user_form"  id="user_form" enctype="multipart/form-data">
  <div class="panel panel-default panel-block">
    <div class="list-group">
      <div class="list-group-item">
        <div class="panel-body">					
          <div class="col-md-13">
            <div class="form-group">
              <label for="title" class="col-lg-2 col-sm-3">Title<em>*</em></label>
              <div class="col-sm-9">				
                <input type="text" id="title" name="title" class="form-control <?php echo isset($errors['title']) ? 'parsley-error' : '' ?>" value="<?php echo $title; ?>" tabindex="1"/>
                <?php if (isset($errors['title'])): ?>
                  <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                <?php endif; ?>
              </div>
            </div>
            <div class="form-group">
              <?php if ($_GET['type'] != 'Chat') { ?>
                <label class="col-lg-2 col-sm-3">Enter Message<em>*</em></label>
                <div class="col-sm-9">
                  <textarea class="span12 ckeditor" id="response" name="response"><?php echo $response; ?></textarea>
                  <?php if (isset($errors['response'])): ?>
                    <ul class="parsley-error-list">
                      <li class="required"><?php echo $errors['response'] ?></li>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php } else { ?>
                <label class="col-lg-2 col-sm-3">Enter Message<em>*</em></label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="response" name="response" cols="200" rows="5"><?php echo $response; ?></textarea>
                  <?php if (isset($errors['response'])): ?>
                    <ul class="parsley-error-list">
                      <li class="required"><?php echo $errors['response'] ?></li>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php } ?>
            </div>
            <div class="form-group">
              <label for="fname" class="col-lg-2 col-sm-3">&nbsp;</label>
              <div class="col-sm-9">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <button class="btn btn-info" type="submit" name="save">Save</button>	                
                <button class="btn btn-default" type="button" name="cancel" onClick="window.location = 'quick_reply.php?type=<?= $type; ?>'">Cancel</button>
              </div>
            </div>            
          </div>
        </div>
      </div>
    </div>
  </div>	
</form>

<script language="javascript" type="text/javascript">
<?php if ($_GET['type'] != 'Chat') { ?>
    CKEDITOR.replace('response', {
      allowedContent: true,
      toolbar: [
        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']},
        {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote']},
        {name: 'links', items: ['Link', 'Unlink']},
        {name: 'styles', items: ['Styles', 'Format']},
        {name: 'tools', items: ['Maximize']}
      ]

    });
<?php } ?>
</script>
<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div>
      <i class="fa fa-list-alt"></i>
      <h1><span>This Trigger image can't be delete because it is used in the following templates.</span></h1>
    </div>			
  </div>
  <div class="panel-body ">
    <div class="form-group">
      <table class="table table-striped table-bordered table-condensed  detail_table text-left">
        <thead>
          <tr>
            <th width="5%">No.</th>
            <th>Template Title</th>              
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($t_res)) { ?>
            <?php $count = 0; ?>
            <?php foreach ($t_res as $key => $val) { ?>     
              <?php $count++; ?>
              <tr>
                <td class="index"><?php echo $count; ?></td>
                <td class=""><a href="javascript:void(0)" onclick="window.parent.location.href = 'trigger_template.php?id=<?= $val['template_id']; ?>'"><?php echo $val['title']; ?></a></td>
              </tr>              
            <?php } ?>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <form method="post" action="" class="row">
      <div class="col-xs-8">	
        <div class="form-group">
          <label>Would you like to replace another image in place of this image?</label>
          <select class="form-control select" name="image_replace" onchange="change_image(this.value)">
            <option value=""></option>
            <?php if (!empty($img_res)) { ?>
              <?php foreach ($img_res as $res) { ?>
                <option value="<?= $res['src']; ?>"><?php echo $res['title']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
          <div class="clearfix"></div>
        </div>
      </div>
      <input type="hidden" name="image_id" value="<?= $image_id; ?>"/>
      <div class="col-xs-10" >
        <div class="form-group">
          <img src="" name="image" id="trigger_image" height="auto" style="display:none" class="img-thumbnail">
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      <div class="col-xs-12">
        <button class="btn btn-info" id="change" name="change" type="submit">Change</button>
        <button onclick="window.parent.$.colorbox.close();" class="btn btn-default" id="cancle" name="cancle" type="button">Cancel</button>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  function change_image(image_src) {
    $('#trigger_image').attr('src',  '<?= $TRIGGER_IMAGE_WEB; ?>' + image_src).show('slow');
  }
</script>
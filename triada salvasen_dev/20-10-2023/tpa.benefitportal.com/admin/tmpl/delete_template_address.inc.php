<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div>
      <i class="icon-list-alt"></i>
      <h1><span>This Trigger Address can't be delete because it is used in the following templates.</span></h1>
    </div>			
  </div>
  <div class="panel-body ">
    <div class="form-group">
      <table class="<?=$table_class?>">
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
          <label>Would you like to replace another address in place of this address?</label>
          <select class="form-control select" name="address_replace" onchange="change_address(this.value)">
            <option value=""></option>
            <?php if (!empty($img_res)) { ?>
              <?php foreach ($img_res as $res) { ?>
                <option value="<?= $res['id']; ?>"><?php echo $res['title']; ?></option>
              <?php } ?>
            <?php } ?>
          </select>
          <div class="clearfix"></div>
        </div>
      </div>
      <input type="hidden" name="address_id" value="<?= $address_id; ?>"/>
      <div class="col-xs-10" style="display:none" id="address_show">
        <p>
        </p>
      </div>
      <div class="clearfix">&nbsp;</div>
      <div class="col-xs-12">
        <button class="btn btn-primary" id="change" name="change" type="submit">Change</button>
        <button onclick="window.parent.$.colorbox.close();" class="btn btn-default" id="cancle" name="cancle" type="button">Cancel</button>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  function change_address(id) {
    $.ajax({
      url: 'ajax_get_address.php',
      type: 'post',
      data: {'address_id': id},
      dataType: 'json',
      success: function (res) {
        if (res.status == 'fail') {
          alert('Invalid operation');
        } else if (res.status == 'success') {
          $('#address_show').show();
          $('#address_show').children('p').html(res.address);
        }
      }
    });

  }
</script>
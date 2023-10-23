<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="mn">Number of Products - <span class="fw300"><?=count($resProducts)?></span></h4>
  </div>
  <div class="panel-body">
    <div class="table-responsive br-n">
      <table class="<?=$table_class?> table-action "> 
        <thead>
          <th>Product Name</th>
          <th>Product ID</th>
        </thead>
        <tbody>
        <?php if (count($resProducts) > 0) { ?>
          <?php foreach ($resProducts as $rows) { ?>
            <tr>
                <td><?=$rows['product_name']?></td>
                <td><a href="javascript:void(0);" class="text-action"><strong><?=$rows['product_code']?></strong></a></td>
            </tr>
        <?php }} ?>
        </tbody>
      </table>
    </div> 
    <div class="text-center">
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
    </div>
  </div>
</div>

<script type="text/javascript">
 $(document).ready(function() { 
   $('select.form-control').selectpicker({ 
    container: 'body', 
    style:'btn-select',
    noneSelectedText: '',
    dropupAuto:true
  });
  $('select.form-control').selectpicker('refresh');
  });
</script>
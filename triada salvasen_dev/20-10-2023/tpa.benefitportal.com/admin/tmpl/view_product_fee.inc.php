<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500"><?=$product_code ?> </strong> <span class="fw300"> - Price</span></p>
    </div>
  </div>
  <div class="panel-body">
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr class="data-head">
              <th>Plan</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($feePrice)) { ?>
              <?php foreach ($feePrice as $rows) { ?>
                <tr>
                  <td><?= $prdPlanTypeArray[$rows['plan_type']]['title']?></td>
                  <td>
                    <?php
                      $prd_price = '$'.$rows['price'];
                      if($rows['price_calculated_on'] == 'Percentage'){
                        $prd_price=(floor($rows['price']*100)/100).'%';
                      } 
                    ?>
                    <?=$prd_price?>
                    
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2">No Price found</td>
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
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">Pricing - <span class="fw300"><?=$prd_main['name']?></span> </h4>
    </div>
  </div>
  <div class="panel-body">
    <?php if($prd_main['pricing_model'] != 'VariableEnrollee') {?>
      <div class="table-responsive br-n">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
                <th>Coverage</th>
                  <th width="20%">Price</th>
                  <?php if(!empty($price_control)) { 
                      foreach($prdPricingQuestionRes as $control){ 
                        if(in_array($control['id'],$price_control)){?>
                          <th><?=$control['display_label']?></th>
                  <?php } } } ?>
              </tr>
          </thead>
          <tbody>
            <?php if(!empty($products) && count($products) > 0) {
                  foreach($products as $key => $product){
              ?>
              <tr>
                  <td><?=checkIsset($prdPlanTypeArray[$product['plan_type']]['title'])?></td>
                  <td><?=displayAmount($product['price'])?></td>
                  <?php if(!empty($price_control)) { 
                      foreach($prdPricingQuestionRes as $control){ 
                        if(in_array($control['id'],$price_control)){
                            $display_text = '-';

                            switch ($control['display_label']) {
                              case 'Age':
                                $display_text = $product['age_from'].' to '.$product['age_to'];
                                break;
                              case 'State':
                                $display_text = checkIsset($product['state'])!='' ? $product['state'] : '-' ;
                                break;
                              case 'Zip Code':
                                $display_text = checkIsset($product['zipcode'])!='' ? $product['zipcode'] : '-';
                                break;
                              case 'Gender':
                                $display_text = checkIsset($product['gender'])!='' ? $product['gender'] : '-';
                                break;
                              case 'Smoking Status':
                                $display_text = checkIsset($product['smoking_status'])!='' ? $product['smoking_status'] : '-';
                                break;
                              case 'Tobacco Status':
                                $display_text = checkIsset($product['tobacco_status'])!='' ? $product['tobacco_status'] : '-';
                                break;
                              case 'Height':
                                $display_text = $product['height_feet'].'Ft '.$product['height_inch'].'In';
                                break;
                              case 'Weight':
                                $display_text = $product['weight'];
                                break;
                              case 'Number Of Children':
                                $display_text = checkIsset($product['no_of_children'])!='' ? $product['no_of_children'] : '-';
                                break;
                              case 'Has Spouse':
                                $display_text = checkIsset($product['has_spouse'])!='' ? $product['has_spouse'] : '-';
                                break;
                              case 'Spouse Age':
                                $display_text = $product['spouse_age_from'].' to '.$product['spouse_age_to'];
                                break;
                              case 'Spouse Gender':
                                $display_text = checkIsset($product['spouse_gender'])!='' ? $product['spouse_gender'] : '-';
                                break;
                              case 'Spouse Smoking Status':
                                $display_text = checkIsset($product['spouse_smoking_status'])!='' ? $product['spouse_smoking_status'] : '-';
                                break;
                              case 'Spouse Tobacco Status':
                                $display_text = checkIsset($product['spouse_tobacco_status'])!='' ? $product['spouse_tobacco_status'] : '-';
                                break;
                              case 'Spouse Height':
                                $display_text = $product['spouse_height_feet'].'Ft '.$product['spouse_height_inch'].'In';
                                break;
                              case 'Spouse Weight':
                                $display_text = $product['spouse_weight'].'Ft '.$product['spouse_weight_type'].'In';
                                break;
                              case 'Benefit Amount':
                                $display_text = '$'.$product['benefit_amount'];
                                break;
                              default:
                                $display_text ='-';
                                break;
                            }

                          ?>
                          <td><?=$display_text?></td>
                  <?php } } } ?>
                </tr>
            <?php } }else{ ?>
              <tr>
                <td colspan="2">
                  No rows found!
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php }else{ ?>
      <div class="panel-title ">
        <h4 class="text-center"><span class="fw400">Pricing variable by enrollee</span> </h4>
      </div>
    <?php  } ?>
    <div class="text-center m-t-20"> 
    <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> </div>
  </div>
</div>
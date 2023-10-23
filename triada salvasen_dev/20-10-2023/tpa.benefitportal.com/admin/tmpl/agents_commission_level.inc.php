<div class="panel panel-default panel-block theme-form">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn fs18">Commission Per Level - <span class="fw300"><?=$agentCommRuleRow['name']?></span> </h4>
      <h4 class="mn fs14">Commission Rule: <span class="fw300"><?=checkIsset($agentCommRuleRow['rule_code'])?></span></h4>
    </div>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-sm-8">
      <div class="btn-group">
              <button type="button" class="btn btn-info btn-outlined dropdown-toggle" data-toggle="dropdown" id="plan-text" aria-expanded="false">
                <?php foreach($prdPlanRow as $prd){?>
                  <?=$prdPlanTypeArray[$prd['plan_type']]['title'];?>
                <?php break; }?>
                  <span class="caret ml5"></span>
              </button>
              <ul class="dropdown-menu" role="menu">
                  <?php foreach($prdPlanRow as $prd){?>
                  <li>
                      <a href="javascript:void(0)" class="select_plan" data-id="<?=$prd['id'];?>"><?=$prdPlanTypeArray[$prd['plan_type']]['title']?></a>
                  </li>
                  <?php }?>
              </ul>
            </div>
      </div>
    </div>
          <div class="table-responsive">

          <?php $cn=0; foreach($prdPlanRow as $prd){ $cn++; ?>
            <div class="text-blue m-b-10 m-t-10 tier_plan<?=$prd['id'];?>" data-id="<?=$prd['id'];?>" style="<?=$cn==1 ? '' : 'display:none' ?>">Commissionable Price: <?php echo displayAmount($original_price_arr[$prdPlanTypeArray[$prd['plan_type']]['title']]['commission_price'])?></div>
          <?php }?>

          <!-- <div class="text-blue m-b-10">Commissionable Price: <?php echo displayAmount($original_price_arr[$prdPlanTypeArray[$prd['plan_type']]['title']]['commission_price'])?></div> -->
          <table class="<?=$table_class?> text-center" id="table1">
              <thead>
                  <tr>
                      <th class="text-left">Level</th>
                      <?php $tmp=0; foreach($prdPlanRow as $prd){?>
                      <th class="tier_plan<?=$prd['id'];?>" style="<?=$tmp>0?"display:none":''?>">Commission %</th>
                      <th class="tier_plan<?=$prd['id'];?>" style="<?=$tmp>0?"display:none":''?>">Override %</th>
                      <th class="tier_plan<?=$prd['id'];?>" style="<?=$tmp>0?"display:none":''?>">Earned $</th>
                      <?php $tmp++;}?>
                      <th class="">Advance $</th>
                      <th class="">Agent</th>
                    </tr>
              </thead>
                <tbody>
                  <?php 
                  foreach($displayAgentLevelArr as $codedLevel => $data) {
                    $class= ($levelHeading == $codedLevel) ? 'text-red custom_class':'';
                  ?>
                  <tr>
                      <td class="text-left <?=$class?>"><?=$codedLevel?></td>
                      <?php $tmp=0; foreach($prdPlanRow as $prd){
                        
                       
                    $original_amount=$plan_amount=$amount=$next_commission=0;
                        ?>
                      
                      <td class="<?=$class?> tier_plan<?=$prd['id'];?>" style="<?=$tmp>0?"display:none":''?>">
                        <?php 

                          $original_amount = $data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['original_amount'];
                          if($prd['plan_type'] !=0 && isset($original_amount) && $original_amount > 0){
                            if($data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['amount_type'] == 'Amount'){
                              echo displayAmount($original_amount,2);
                            }else{                              
                              echo number_format($original_amount,2,".","").'%';
                            }
                          }else{
                            echo ' - ';
                          }?>
                      </td>
                      <td class="<?=$class?> tier_plan<?=$prd['id']?>" style="<?=$tmp>0?"display:none":''?>">
                        <?php
                        $plan_amount = $data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['amount'];
                        $override  = '';
                          if($prd['plan_type'] !=0 && isset($plan_amount) &&  $plan_amount > 0 ){
                            if($data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['amount_type']=='Percentage'){
                              echo number_format($plan_amount,2,".","").'%';
                              $override  = number_format($plan_amount,2,".","");
                            }else{
                              echo '-';
                            }
                          }else{
                            echo '-';
                          }
                          ?> 
                      </td>
                      <td class="<?=$class?> tier_plan<?=$prd['id']?>" style="<?=$tmp>0?"display:none":''?>">
                        <?php
                          if($prd['plan_type'] !=0 && isset($plan_amount) && $plan_amount > 0){
                            if($data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['amount_type']=='Percentage'){
                              $amount=($data['commission'][$prdPlanTypeArray[$prd['plan_type']]['title']]['price']*$override)/100;
                              echo '$'.number_format($amount,2,".","")."";
                            }else{

                              if($original_amount > 0){
                                echo '$'.number_format($plan_amount,2,".","");
                              }else{
                                echo '-';
                              }
                            }
                          }else{
                              echo '-';
                          }
                          ?> 
                      </td>
                      <td class="<?=$class?> tier_plan<?=$prd['id']?>" style="<?=$tmp>0?"display:none":''?>" > 
                        <?php 
                        $flg =  checkIsset($data['is_on']) == 'Y' ? true : false ;
                        $product_id = checkIsset($data['product_id']) != '' ? true : false ;
                        echo checkIsset($data['advance_month']) !='' && $amount!=0 && $flg && $product_id? $data['advance_month'] ." / ".displayAmount($amount*($data['advance_month']-1)) :'-';
                        ?>
                      </td>
                      <?php $tmp++;  } ?>
                      
                      <td class="<?=$class?>">
                        <?php 
                          if(checkIsset($data['rep_id'])!=''){
                            echo $data['fname'].' '.$data['lname'].'<br>'.$data['rep_id'];
                          }else{
                            echo"-";
                          }
                        ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
          </table>
          </div>
    <div class="text-center m-t-20"> 
    <!-- <a href="javascript:void(0);" class="btn btn-action" >Export</a>  -->
    <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> 
    </div>
  </div>
</div>
<script type="text/javascript">
  // $(function () {
  //     $("#table1 colgroup col.highlight_col").children("tr>td").addClass("class1");
  // });
  $(document).ready(function(){
    $(".select_plan").click(function(){
      $html = $(this).html();
      $html =$html + " <span class='caret ml5'></span>";
      $id=$(this).attr("data-id");
      $("#plan-text").html($html);
      $('[class*="tier_plan"]').hide();
      $(".tier_plan"+$id).show();
    });
    $(".custom_class").addClass("fw500");
  });
</script>
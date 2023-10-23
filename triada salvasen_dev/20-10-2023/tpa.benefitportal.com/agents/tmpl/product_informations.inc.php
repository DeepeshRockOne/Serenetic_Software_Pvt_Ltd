 <div class="container m-t-30">
    <div class="panel panel-default  panel-block">
       <div class="panel-body">
         <div class="clearfix tbl_filter">
            <div class="pull-left">
               <h4 class="m-t-0">Products Summary</h4>
            </div>
         </div>
          <div class="table-responsive">
             <table class="<?=$table_class?>">
                <thead>
                   <tr>
                      <th>Product Category</th>
                      <th>Product Name</th>
                      <th>Application Method</th>
                      <th class="text-center">Price</th>
                      <th>Commission</th>
                      <th class="text-center">Provider</th>
                      <th class="text-center">Product Info</th>
                   </tr>
                </thead>
                <tbody>
                   <?php if(count($resProducts) > 0){
                      foreach($resProducts as $product){
                      ?>
                   <tr>
                      <td><?=$product['prdCategory']?></td>
                      <td><?=$product['name']?></td>
                      <td><?=$product['enrollmentMethod']?></td>
                      <td class="text-center icons"><a href="javascript:void(0)" data-href="<?=$HOST?>/agents_pricing.php?product_id=<?=$product['product_id'];?>" class="product_pricing" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye"></i></a></td>
                      <td>
                            <?php
                            $comm_json = json_decode($product['commission_json'], true);
                            $commission_amt = '0.00';
                            if ($product['commission_on'] == 'Plan') {
                                $commission_arr = isset($comm_json[$product['min_plans']][$level]) ? $comm_json[$product['min_plans']][$level] : array("amount_type"=>"Percentage","amount"=>0);
                                $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] . '%' : displayAmount($commission_arr['amount']);
                            } else {
                                $commission_arr = isset($comm_json[$level]) ? $comm_json[$level] : array("amount_type"=>"Percentage","amount"=>0);
                                $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] . '%' : displayAmount($commission_arr['amount']);
                            }?>
                            <p class="text-red"><?= $commission_amt ?></p>
                      </td>
                      <td class="text-center">
                         <?php if($product['sp_id'] !=''){  ?>
                            <a href="<?=!empty($product['url']) ? (substr($product['url'],0,7)=="http://" || substr($product['url'],0,8)=="https://"?$product['url']:'//'.$product['url']) : 'javascript:void(0)';?>" target="_blank"><i class="fa fa-link fs18 text-blue"></i></a>

                         <?php } else { echo '-'; } ?>
                      </td>
                      <td class="text-center icons"><a href="javascript:void(0)" data-href="agent_product_detail.php?product_id=<?=md5($product['product_id'])?>&user_id=<?=md5($product['agent_id'])?>" class="group_product_detail" data-toggle="tooltip" data-placement="top" title="Details"><i class="fa fa-list"></i></a></td>
                   </tr>
                   <?php } }else{
                      echo "<tr><td colspan='6' class='text-center'>No product found!</td></tr>";
                   } ?>
                </tbody>
             </table>
          </div>
       </div>
    </div>
 </div>

<script type="text/javascript">
$(document).ready(function () {
//   $(".product_pricing").colorbox({iframe: true, width: '768px', height: '500px'});

  $('.product_pricing').on('click',function(e){
     var $href=$(this).attr('data-href');
      $.colorbox({
         href:$href,
         iframe: true, 
         width: '768px', 
         height: '500px'
      });
  });

  $('.group_product_detail').on('click',function(e){
     var $href=$(this).attr('data-href');
      $.colorbox({
         href:$href,
         iframe: true, 
         width: '980px', 
         height: '580px'
      });
  });

});
</script>
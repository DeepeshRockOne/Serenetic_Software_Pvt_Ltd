<div class="section-padding">
   <div class="container">
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
                        <th>Product Name</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Provider</th>
                        <th class="text-center">Product Info</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if(count($resProducts) > 0){
                        foreach($resProducts as $product){
                        ?>
                     <tr>
                        <td><?=$product['name']?></td>
                        <td class="text-center icons"><a href="javascript:void(0)" data-href="product_pricing.php?product_id=<?=md5($product['product_id'])?>" class="product_pricing" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye"></i></a></td>
                        <td class="text-center">
                           <?php if($product['sp_id'] !=''){  ?>
                              <a href="<?=!empty($product['url']) ? $product['url'] : 'javascript:void(0)';?>" target="_blank"><i class="fa fa-link fs18 text-blue"></i></a>
                           <?php }else echo '-'; ?>
                        </td>
                        <td class="text-center icons"><a href="javascript:void(0)" data-href="group_product_detail.php?product_id=<?=md5($product['product_id'])?>&user_id=<?=md5($product['agent_id'])?>" class="group_product_detail" data-toggle="tooltip" data-placement="top" title="Details"><i class="fa fa-list"></i></a></td>
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
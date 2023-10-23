 <div class="panel panel-default panel-block">
    <div class="panel-heading">
        <div class="panel-title">
        <h4 class="mn">Classes - <span class="fw300"><?=$coverage_period_name?></span></h4>
       </div>
    </div>
 <div class="panel-body">
      <h4  class="fs16 m-t-0 m-b-15">Product Offerings</h4>
      <table class="<?=$table_class?> br-n">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-center"># Classes Assigned</th>
            <th class="text-center"># Enrolled</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($resCheck)) { ?>
            <?php foreach ($resCheck as $key => $value) { ?>
              <tr> 
                <td><?= $value['name'] ?></td>
                <td class="text-center"><a href="javascript:void(0);" class="text-action"><strong><?= $value['total_class'] ?></strong></a>
                </td>
                <td class="text-center"><?= $value['total_member'] ?></td>
              </tr>
            <?php } ?>
          <?php }else{ ?>
            <tr><td colspan="4">No Record(s) Found</td></tr>
          <?php } ?>
        </tbody>
      </table>
      <div  id="popover_content_wrapper" style="display: none;">
       <table class="table">
           <tr>
               <td><strong>Executive</strong></td>
               <td>23</td>
           </tr>
           <tr>
               <td><strong>Full Time</strong></td>
               <td>20</td>
           </tr>
           <tr>
               <td><strong>Temporary</strong></td>
               <td>19</td>
           </tr>
       </table>
     </div>
      <div class="text-center m-t-20">
         <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>
 </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){

    $('.member_product_popover').popover({ 
    html : true,
    container: 'body',
    trigger: 'click',
    template: '<div class="popover"><div class="arrow"></div><div class="popover-content"></div></div>',
    placement: 'auto top',
    content: function() {
      return $('#popover_content_wrapper').html();
    }

  });
  });
</script>
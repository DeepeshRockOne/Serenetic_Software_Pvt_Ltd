<?php if($is_ajaxed) { ?>
  <div class="panel panel-default panel-block theme-form">
    <div class="panel-body">
    	<div class="table-responsive">
        	<table class="<?=$table_class?> text-center">
            	<thead>
                	<tr>
                    	<th class="text-left">ID/Added Date</th>
                        <th>Advance Months</th>
                        <th>Effective Date</th>
                        <th>Termination Date</th>
                        <th>Service Fee(s)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                  if($totalRes > 0){
                    foreach($fetchComm as $comm ) { ?>
                    <tr>
                    <?php
                      $added_date = getCustomDate($comm['added_date']);
                      $amount = $comm['price_calculated_on'] == 'Percentage' ? $comm['price'] ."%" : displayAmount($comm['price']);
                      $effective_date = getCustomDate($comm['effective_date']);
                      $termination_date = getCustomDate($comm['termination_date']);
                      if($comm['rule_type'] == 'Global'){
                        $commLink = "add_member_advance_global_rule.php?advRuleId=".md5($comm['advRuleId'])."&chargedTo=".$comm["charged_to"];
                      }else{
                        $commLink = "advance_commission_variation.php?advRuleId=".md5($comm['advRuleId'])."&chargedTo=".$comm["charged_to"];
                      }
                    ?>
                      <td class="text-left"><?=$added_date?></td>
                      <td ><?=$comm['advance_month']?> Months</td>
                      <td ><?=$effective_date?></td>
                      <td ><?=$termination_date?></td>

                    <?php if($comm['charged_to'] == 'Agents'){ ?>
                        <td class="text-center"><?=$comm['price_calculated_on'] == 'Percentage' ? $comm['price'] . '%' : '$' . $comm['price']?></td>
                    <?php }else{ 
                        if($comm['pricing_model'] == "FixedPrice"){
                    ?>
                        <td class="text-center"><?='$' . $comm['price']?></td>
                    <?php 
                      } else{ 
                        $selPricingRange = "SELECT pmt.price,pmc.min_total,pmc.max_total 
                                            FROM prd_matrix pmt 
                                            JOIN prd_matrix_criteria pmc ON(pmt.id=pmc.prd_matrix_id AND pmc.is_deleted='N')
                                            WHERE pmt.product_id = :advFeeId AND pmt.is_deleted='N'";

                        $resPricingRange = $pdo->select($selPricingRange,array(':advFeeId' => $comm['advFeeId']));
                    ?>
                      <td class="icons text-center">
                        <a href="javascript:void(0)" data-toggle="popover"
                        data-html="true"
                        data-placement="top"
                        data-content="
                        <table class='<?=$table_class?>'>
                          <thead>
                            <tr>
                              <th>Fee</th>
                              <th>From</th>
                              <th>To</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if($resPricingRange){ ?>
                            <?php foreach($resPricingRange as $range){ ?>
                            <tr>
                              <td>$<?=$range['price']?></td>
                              <td>$<?=$range['min_total']?></td>
                              <td>$<?=$range['max_total']?></td>
                            </tr>
                            <?php } ?>
                            <?php } ?>
                          </tbody>
                        </table>">
                        <i class="fa fa-eye "></i>
                        </a>
                      </td>
                    <?php } 
                        } 
                    ?>

                    <td class="icons">
                          <a data-toggle="tooltip" data-trigger="hover" data-placement="top" target="_blank" title="Edit" href="<?=$commLink?>" ><i class="fa fa-pencil-square-o"></i></a> 
                    </tr>
                  <?php } 
                }else{ ?>
                    <tr>
                      <td colspan="6">
                        No rows Found!
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                  <?php if($totalRes > 0 && !empty($fetchComm)) { ?>
                      <td colspan="6">
                      <?php echo $paginate->links_html; ?>
                      </td>
                  <?php } ?>
                  </tr>
                </tfoot>
            </table>
        </div>
    <div class="text-center m-t-20"> 
    <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> 
    </div>
  </div>
</div>
</div>
<?php } else { ?>
<div class="panel panel-default panel-block theme-form">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">Advances - <span class="fw300"><?=$agent_detail['name']?></span> <span class="fw300 red-link"><?=$agent_detail['rep_id']?></span> </h4>
    </div>
  </div>
  <form id="agent_product_frm_search" action="agents_adv_com.php" method="GET" class="sform">
    <input type="hidden" name="agent_id" id="agent_id" value="<?=!empty($agent_id) ? $agent_id : $_REQUEST['id']?>"/>
    <input type="hidden" name="is_ajaxed" id="adv_is_ajaxed" value=""/>
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
    <input type="hidden" name="type" id="type" value="<?=$type;?>"/>
    <input type="hidden" name="charged" id="charged" value="<?=$charged;?>"/>
    <div class="panel-body">
        <div class="col-sm-4">
            <select class="form-control sel_prd" name="product_id" id="chng_product">
              <?php foreach($agproduct as $prd) {?>
                <option value="<?=$prd['id']?>" <?= $prd['id']== $product_id ? 'selected="selected"' : '' ?>><?=$prd['name']?></option>
              <?php } ?>
            </select>
        </div>
    </div>
  </form>
</div>
<div id="agent_adv_ajax_data"></div>
<script type="text/javascript">
if(typeof($) !== 'undefined') {
   $(document).ready(function () {
    dropdown_pagination('agent_adv_ajax_data')
        adv_ajax_submit();
    });
}
$(document).off('change',"#chng_product");
$(document).on('change',"#chng_product",function(e){
  adv_ajax_submit();
}); 


$(document).off('click', '#agent_adv_ajax_data ul.pagination li a');
$(document).on('click', '#agent_adv_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#agent_adv_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_adv_ajax_data').html(res).show();
            $('[data-toggle="popover"]').popover();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
        }
    });
});

  function adv_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_adv_ajax_data').hide();
    $('#adv_is_ajaxed').val('1');
    var params = $('#agent_product_frm_search').serialize();
    $.ajax({
        url: $('#agent_product_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_adv_ajax_data').html(res).show();
            $('[data-toggle="popover"]').popover();
            $('[data-toggle="tooltip"]').tooltip();
            common_select();
        }
    });
    return false;
  }
</script>
<?php } ?>
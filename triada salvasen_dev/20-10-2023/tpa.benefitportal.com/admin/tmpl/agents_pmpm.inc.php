<?php if($is_ajaxed) { ?>
    <div class="table-responsive">
      <table class="<?=$table_class?> text-center" id="pmpm_table">
        <thead>
          <tr>
              <th class="text-left">ID/Added Date</th>
              <th >Agents</th>
              <th >Effective Date</th>
              <th >Termination Date</th>
              <th >PMPM Amount</th>
              <th >Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if($totalRes > 0 && !empty($fetchComm)) {
            foreach($fetchComm as $comm) { 

              $created_at = getCustomDate($comm['created_at']);
              $effective_date = getCustomDate($comm['effective_date']);
              $termination_date = getCustomDate($comm['termination_date']);
              $amount = '';
              if($comm['amount_calculated_on'] == 'Percentage'){
                $amount = $comm['amount'].'%';
              }else{
                $amount = '$'.$comm['amount'];
              }
          ?>
          <tr>
            <td class="text-left"><strong class="text-red"><?=$comm['display_id']?></strong><br /><?=$created_at?></td>
            <td ><a href="pmpm_fee_agent_popup.php?id=<?=$comm['rule_id']?>" class="text-red" target="_blank"><?=$comm['total_agents']?></td>
            <td><?=$effective_date?></td>
            <td><?=$termination_date?></td>
            <td><?=$amount?></td>
            <td class="icons"><a href="add_pmpm_commission.php?id=<?=$comm['id']?>" target="_blank" class="external_link"><i class="fa fa-edit" aria-hidden="true"></i></a></td>
          </tr>
          <?php } }else{ ?>
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
<?php }else{ ?>
<div class="panel panel-default panel-block theme-form">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">PMPMs - <span class="fw300"><?=$agent_detail['name']?></span> <span class="fw300 red-link"><?=$agent_detail['rep_id']?></span> </p>
    </div>
  </div>
  <div class="panel-body">
    <form id="agent_product_frm_search" action="agents_pmpm.php" method="GET">
    <input type="hidden" name="agent_id" id="agent_id" value="<?=!empty($agent_id) ? $agent_id : $_REQUEST['id']?>"/>
    <input type="hidden" name="is_ajaxed" id="pmpm_is_ajaxed" value=""/>
    <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
    <input type="hidden" name="ids" value="<?=$comm_ids['ids']?>" id="comm_ids">
    <input type="hidden" name="id" value="<?=$commid?>" id="commid">

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group m-t-7">
            <select class="form-control sel_prd" name="product_id" id="chng_product">
              <?php foreach($agproduct as $prd) { ?>
              <option value="<?=$prd['id']?>" <?= $prd['id'] == $product_id ? 'selected="selected"' : "" ?>><?=$prd['name']?></option>
            <?php } ?>
            </select>
          </div>
        </div>
      </div>

    </form>
    <div id="agent_pmpm_ajax_data"></div>
    <div class="text-center m-t-20"> 
      <!-- <a href="javascript:void(0);" class="btn btn-action" >Export</a>  -->
      <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a> 
    </div>
  </div>
</div>
<script type="text/javascript">
if(typeof($) !== 'undefined') {
   $(document).ready(function () {
    dropdown_pagination('agent_pmpm_ajax_data')
        pmpm_ajax_submit();
    });
}
$(document).off('change',"#chng_product");
$(document).on('change',"#chng_product",function(e){
  pmpm_ajax_submit();
}); 

$(document).off('click',".external_link");
$(document).on('click',".external_link",function(e){
  pmpm_ajax_submit();
});

$(document).off('click', '#agent_pmpm_ajax_data ul.pagination li a');
$(document).on('click', '#agent_pmpm_ajax_data ul.pagination li a', function (e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $('#agent_pmpm_ajax_data').hide();
    $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_pmpm_ajax_data').html(res).show();
            common_select();
        }
    });
});

  function pmpm_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_pmpm_ajax_data').hide();
    $('#pmpm_is_ajaxed').val('1');
    var params = $('#agent_product_frm_search').serialize();
    $.ajax({
        url: $('#agent_product_frm_search').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_pmpm_ajax_data').html(res).show();
            common_select();
        }
    });
    return false;
  }
</script>
<?php } ?>

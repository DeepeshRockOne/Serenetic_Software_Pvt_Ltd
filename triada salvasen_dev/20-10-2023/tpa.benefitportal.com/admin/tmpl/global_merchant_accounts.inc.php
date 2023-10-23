<?php if($is_global_ajaxed) { ?>
    <div class="clearfix m-b-15 tbl_filter">
      <div class="pull-left">
        <h4 class="m-t-7">Global Merchants</h4>
      </div>
      <div class="pull-right">
        <a href="add_merchant_processor.php?type=Global" class="btn btn-action">+ Global Processor</a>
      </div>
    </div>
    <div class="table-responsive">
      <table id="rearrange" class="<?=$table_class?> merchant_table">
        <thead>
          <tr>
            <th>Default</th>
            <th>Added Date</th>
            <th>Name</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Transactions</th>
            <th>Sales</th>
            <th>Refunds</th>
            <th>Chargebacks</th>
            <th>Data As Of</th>
            <th width="130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($fetch_rows) && count($fetch_rows) > 0) {
            if(!$is_ach_default || !$is_cc_default){ ?>
              <tr class="bg_dark_success">
                <?php if(!$is_ach_default) {?>
                    <td>ACH</td>
                    <td class="text-center" colspan="12">No ACH default is currently assigned. Edit an account below to assign it as the default processor.</td>
                <?php }  ?>
              </tr>
              <tr class="bg_dark_success">
                <?php if(!$is_cc_default){ ?>
                    <td>CC</td>
                    <td class="text-center" colspan="12"> No Credit Card default is currently assigned. Edit an account below to assign it as the default processor. </td>
                <?php } ?>
              </tr>
            <?php } ?>
            <?php $counter = 1;
            foreach ($fetch_rows as $key => $value) { 
              $class_value = '';
              $is_default = 'Standby';
              if(($value['is_default_for_ach'] == 'Y') && ($value['is_default_for_cc'] == 'Y')){
                $is_default = 'ACH & CC';
                $class_value = 'success';
              }else if($value['is_default_for_ach'] == 'Y'){
                  $is_default = 'ACH';
                  $class_value = 'success';
              }else if($value['is_default_for_cc'] == 'Y'){
                  $is_default = 'CC';
                  $class_value = 'success';
              }

              $payment_type = '-';
              if(($value['is_ach_accepted'] == 'Y') && ($value['is_cc_accepted'] == 'Y')){
                $payment_type = 'ACH & CC';
              }else if($value['is_ach_accepted'] == 'Y'){
                $payment_type ='ACH';
              }else if($value['is_cc_accepted'] == 'Y'){
                $payment_type ='CC';
              }
            ?>
            
           <tr class="<?=(!empty($class_value) && $class_value == 'success') ? "bg_light_success" : "" ?>">
                <td class="<?=(!empty($class_value) && $class_value == 'success') ? "text-success" : "" ?>">
                  <?=$is_default?>
                </td>
                <td><?=date("m/d/Y",strtotime($value['created_at']))?></td>
                <td><?=$value['name']?></td>
                <td><?=$payment_type?></td>
                <td>
                <div class="theme-form pr w-130">
                  <select class="processor_status form-control <?=!empty($value['status']) ? 'has-value' : '' ?>" id="processor_status_<?=md5($value['id'])?>" data-old_val="<?=$value['status']?>">
                    <option value="Active" <?=($value['status'] == 'Active') ? 'selected="selected"' : '' ?>>Active</option>
                    <option value="Inactive" <?=($value['status'] == 'Inactive') ? 'selected="selected"' : '' ?>>Inactive</option>
                    <option value="Closed" <?=($value['status'] == 'Closed') ? 'selected="selected"' : '' ?>>Closed</option>
                  </select>
                  <label>Select</label>
                </div>
                </td>
                <?php 
                $approved_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('New Order','Renewal Order') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $value['id'],":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));

                $chargeback_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('Chargeback') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $value['id'],":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));

                $refund_void_order_total = $pdo->selectOne("SELECT count(DISTINCT(order_id)) as total_count, sum(credit) as total_credit, sum(debit) as total_debit FROM transactions as t WHERE payment_master_id = :payment_master_id AND transaction_type IN ('Refund Order') AND Date(created_at) >= :from_date AND Date(created_at) <= :to_date", array(":payment_master_id" => $value['id'],":from_date" => date("Y-m-01"),":to_date" => date("Y-m-t")));

                $total_count = 0;
                $total_amount =  $total_approved = $total_chargeback = $total_refund_void  = 0.00;
                $order_date_time = 0;
                $approved_parcentage = $refund_void_percentage =$chargeback_percentage = 0;
                $lable_class = '';
                $total_lable_class = '';

                if(!empty($approved_order_total)){
                  $total_count = $total_count + $approved_order_total['total_count'];
                  $total_approved = $approved_order_total['total_credit'] - $approved_order_total['total_debit'];
                  $total_amount = $approved_order_total['total_credit'] - $approved_order_total['total_debit'];
                }
                if(!empty($chargeback_order_total)){
                  $total_chargeback = abs($chargeback_order_total['total_credit'] - $chargeback_order_total['total_debit']);
                }
                if(!empty($refund_void_order_total)){
                  $total_refund_void = abs($refund_void_order_total['total_credit'] - $refund_void_order_total['total_debit']);
                }
                if($total_count < 0){
                  $total_count = abs($total_count);
                  if($total_count < 0){
                    $total_lable_class = 'text-action';
                  }
                }
                if($total_amount < 0){
                  $total_amount = abs($total_amount);
                  if($total_amount < 0){
                    $lable_class = 'text-action';
                  }
                } 
                if(!empty($total_count)){
                  $order_date_time = $pdo->selectOne("SELECT t.created_at FROM transactions as t JOIN orders as o on(o.id = t.order_id) WHERE o.payment_master_id = :payment_master_id AND t.transaction_type IN ('New Order','Renewal Order','Refund Order','Payment Returned','Chargeback','Void Order') ORDER BY t.created_at DESC", array(":payment_master_id" => $value['id']));
                } 
                if(!empty($value['monthly_threshold_sale']) && ($value['monthly_threshold_sale'] > 0)){
                  if($total_approved > 0){
                    $approved_parcentage = ($total_approved * 100) / $value['monthly_threshold_sale'];
                  }
                  if($total_refund_void > 0){
                    $refund_void_percentage = ($total_refund_void * 100) / $value['monthly_threshold_sale'];
                  }
                  if($total_chargeback > 0){
                    $chargeback_percentage = ($total_chargeback * 100) / $value['monthly_threshold_sale'];
                  }
                }

                 ?>
                <td class=" <?=(!empty($class_value) && $class_value == 'success') ? "" : "fw500 text-action " ?>">
                  <strong>
                    <a href="javascript:void(0)" id="<?=md5($value['id'])?>" class="transaction_popup fw500 text-action ">
                      <span class="<?=$total_lable_class?>"><?=$total_count?></span>
                      <span>/</span>
                      <span class="<?=$lable_class?>"><?=displayAmount($total_amount,2) ?></span>
                    </a>
                  </strong>
                </td>
                <td class="<?=(!empty($approved_parcentage) && $approved_parcentage >= $value['sales_threshold_value'] && $value['is_sales_threshold'] == 'Y' ) ? "text-action" : "" ?>">
                  <a href="javascript:void(0)" id="<?=md5($value['id'])?>" class="transaction_popup fw500 text-action ">
                  <?=displaypercentage($approved_parcentage,2)?>
                  </a>
                </td>

                <td class="<?=(!empty($refund_void_percentage) && $refund_void_percentage >= $value['refund_threshold_value'] && $value['is_refund_threshold'] == 'Y') ? "text-action" : "" ?>">
                  <a href="javascript:void(0)" id="<?=md5($value['id'])?>" class="transaction_popup fw500 text-action ">
                  <?=displaypercentage($refund_void_percentage,2); ?>
                  </a>
                </td>

                <td class=" <?=(!empty($chargeback_percentage) && $chargeback_percentage >= $value['chargeback_threshold_value'] && $value['is_chargeback_threshold'] == 'Y' ) ? "text-action" : "" ?>">
                  <a href="javascript:void(0)" id="<?=md5($value['id'])?>" class="transaction_popup fw500 text-action ">
                  <?=displaypercentage($chargeback_percentage,2);?>
                  </a>
                </td>
                <td><?=!empty($order_date_time) ? $tz->getDate($order_date_time['created_at']) : '-'; ?></td>
                <td class="icons">
                  <a href="javascript:void(0)" class="view_assigned_agents" data-id="<?=md5($value['id'])?>"><i class="fa fa-user "></i></a>
                  <a href="javascript:void(0);" class="edit_global_product" id="edit_global_product_<?=md5($value['id'])?>"><i class="fa fa-edit "></i></a>
                  <?php if($class_value != 'success') { ?>
                    <a href="javascript:void(0);" class="global_delete_processor" id="global_delete_processor_<?=md5($value['id'])?>"  data-name="<?=$value['name']?>"><i class="fa fa-trash"></i></a>
                  <?php } ?>
                </td>
              </tr>
            <?php } 
          } else { ?>
            <tr>
              <td colspan="12">No record(s) found</td>
            </tr>
          <?php } ?>
        </tbody>
        <tfoot>
          <?php if ($total_rows > 0) {?>
            <tr>
              <td colspan="12"><?php echo $paginate->links_html; ?></td>
            </tr>
          <?php } ?>
        </tfoot>
      </table>
    </div>
<?php } else { ?>
    <form id="frm_search_global" action="global_merchant_accounts.php" method="GET" >
          <input type="hidden" name="processor_names" class="form-control" value="<?=checkIsset($processor_names)?>">
          <input type="hidden" name="status" class="form-control" value="<?=checkIsset($status)?>">
          <input type="hidden" name="agent_ids" class="form-control" value="<?=checkIsset($agent_ids)?>">
          <input type="hidden" name="processor_mid" class="form-control" value="<?=!empty($processor_mid) ? $processor_mid : ''?>">
           <input type="hidden" name="payment_method_type" value='<?=$payment_method_type?>'>
           <input type="hidden" name="type" value='<?=$type?>'>
           <input type="hidden" name="join_range" value='<?=$join_range?>'>
           <input type="hidden" name="fromdate" value='<?=$fromdate?>'>
           <input type="hidden" name="todate" value='<?=$todate?>'>
           <input type="hidden" name="added_date" value='<?=$added_date?>'>

          <input type="hidden" name="is_global_ajaxed" id="is_global_ajaxed" value="1" />
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
          <input type="hidden" name="sort_global_by" id="sort_by_column_global" value="<?=!empty($GlobalSortBy) ? $GlobalSortBy : '';?>" />
          <input type="hidden" name="sort_global_direction" id="sort_by_direction_global" value="<?=!empty($GlobalSortDirection) ? $GlobalSortDirection : '';?>" />
          <input type="hidden" name="export" id="export" value=""/>
    </form>
  <div id="ajax_global_loader" class="ajax_global_loader" style="display: none;">
    <div class="loader"></div>
  </div>
  <div id="ajax_global_data" class=""> </div>
  <script type="text/javascript">
    $(document).ready(function () {
   dropdown_pagination('ajax_global_data')

      ajax_submit_global();
      $(document).off('click', '#ajax_global_data tr.data-head a');
      $(document).on('click', '#ajax_global_data tr.data-head a', function(e) {
        e.preventDefault();
        $('#sort_by_column_global').val($(this).attr('data-column'));
        $('#sort_by_direction_global').val($(this).attr('data-direction'));
        ajax_global_data();
      });

      $(document).off('click', '#ajax_global_data ul.pagination li a');
      $(document).on('click', '#ajax_global_data ul.pagination li a', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_global_data').hide();
        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_global_data').html(res).show();
            common_select();
          }
        });
      });

      function ajax_submit_global() {
        $('#ajax_loader').show();
        $('#ajax_global_data').hide();
        $('#is_global_ajaxed').val('1');
        var params = $('#frm_search_global').serialize();
        $.ajax({
          url: $('#frm_search_global').attr('action'),
          type: 'GET',
          data: params,
          success: function(res) {
            $('#ajax_loader').hide();
            $('#ajax_global_data').html(res).show();
            common_select();
          }
        });
        return false;
      }
    });
  </script>
<?php } ?>
<script type="text/javascript">
  $(document).off("click","#merchant_processor");
  $(document).on("click","#merchant_processor",function(){
    parent.window.location = 'add_merchant_processor.php?type=Global';
  });

  $(document).off('change','.processor_status');
  $(document).on('change','.processor_status',function (e) {
    e.stopPropagation();
    var id = $(this).attr('id').replace('processor_status_', '');
    var processor_status = $(this).val();
    var is_status = 'Y';
    var old_val = $(this).attr('data-old_val');
    parent.swal({
      text: "Update Status: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function () {
      $.ajax({
        url: 'change_processor_status.php',
        method: 'GET',
        data: {id: id, status: processor_status, is_status : is_status},
        dataType: 'json',
        beforeSend :function(e){
            $("#ajax_loader").show();
        },
        success: function(res) {
            $("#ajax_loader").hide();
          if (res.status == "success") {
            parent.setNotifySuccess('Status Changed Successfully');
          } else {
            parent.setNotifyError('Something went wrong');
          }
          window.location.reload();
        }
      });
    }, function (dismiss) {
        $('#processor_status_'+id).val(old_val);
        $("#processor_status_"+id).selectpicker('render');
    });
  });

  $(document).off('click','.global_delete_processor');
  $(document).on('click','.global_delete_processor',function () {
    var id = $(this).attr('id').replace('global_delete_processor_', '');
    var is_status = 'N';
    var $name = $(this).attr('data-name');
    parent.swal({
      text: "<br>Delete Record: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function () {
      $.ajax({
        url: 'change_processor_status.php',
        method: 'GET',
        data: {id: id, is_status : is_status},
        dataType: 'json',
        beforeSend : function(e){
          $("#ajax_loader").show();
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == "success") {
            parent.setNotifySuccess('You have successfully removed '+$name);
          } else {
            parent.setNotifyError('Something went wrong');
          }
          window.location.reload();
        }
      });
    }, function (dismiss) {
      // window.location.reload();
    });
  });

  $(document).off("click",".edit_global_product");
  $(document).on("click",".edit_global_product",function(){
    $id = $(this).attr("id").replace('edit_global_product_','');
    parent.window.location.href = "add_merchant_processor.php?type=Global&id=" + $id;
  });
</script>
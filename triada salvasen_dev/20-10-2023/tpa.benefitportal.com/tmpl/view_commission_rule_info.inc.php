<div class="panel panel-default panel-main">
    <div class="panel-heading">
       <div class="panel-title"><h4 class="mn"> Global Commission rule</h4></div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="<?=$table_class?>">
            <thead>
            <tr>
                <th <?php echo $show_tr ? '' : 'style="display:none;"' ?>>Plan Name</th>
                <th>Products</th>
                <?php foreach($agentCodedRes as $a){
                    if(in_array($a['level'],$levels)) {
                    ?>
                    <th><?=$a['level_heading']?></th>
                    <?php
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($product_wise_commissions)) {
                foreach ($product_wise_commissions as $product_id => $plan_wise_commissions) {
                    foreach ($plan_wise_commissions as $plan_type_id => $plan_wise_commission) {
                            if($plan_wise_commission['commission_on'] == 'Plan') {
                        ?>
                        <tr class="plan_commission_row plan_commission_row_<?= $plan_type_id ?>" style="<?php echo $show_tr ? '' : 'display:none;' ?>" >
                            <td><?= $plan_wise_commission['plan_name'] ?></td>
                            <td>
                                <?= $plan_wise_commission['product_name']. ' ' .'(<strong>' . $plan_wise_commission['product_code'] . '</strong>)'   ?>
                            </td>
                            <?php
                            foreach ($plan_wise_commission['plan_commissions'] as $key => $plan_commissions) {
                                    if(in_array($key,$levels)){
                                ?>
                                <td>
                                    <?php if ($plan_commissions['amount_type'] == "Amount") {
                                        echo $plan_commissions['amount'] != '' ? "$" . $plan_commissions['amount'] : '$0';
                                    } else {
                                        echo $plan_commissions['amount'] != '' ? $plan_commissions['amount'] . '%' : '0%';
                                    } ?>
                                </td>
                                <?php
                                }
                            }
                            ?>
                        </tr>
                        <?php
                    }  else { ?>
                        <tr class="plan_commission_row plan_commission_row_<?= $plan_type_id ?>">
                            <td style="<?php echo $show_tr ? '' : 'display:none;' ?>" >-</td>
                            <td>
                                <?= $plan_wise_commission['product_name']. ' ' .'(<strong>' . $plan_wise_commission['product_code'] . '</strong>)'   ?>
                            </td>
                            <?php
                            foreach ($plan_wise_commission['plan_commissions'] as $key => $plan_commissions) {
                                if(in_array($key,$levels)){
                                ?>
                                <td>
                                    <?php if ($plan_commissions['amount_type'] == "Amount") {
                                        echo $plan_commissions['amount'] != '' ? "$" . $plan_commissions['amount'] : '$0';
                                    } else {
                                        echo $plan_commissions['amount'] != '' ? $plan_commissions['amount'] . '%' : '0%';
                                    } ?>
                                </td>
                                <?php
                                }
                            }
                            ?>
                        </tr>
                    <?php } }
                }
            } ?>
            </tbody>
        </table>
        </div>
        <div class="text-center m-t-30">
          <a href="javascript:void(0);" onclick="parent.$.colorbox.close();" class="btn red-link">Cancel</a>
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.select_plan', function () {
            $("#ajax_loader").show();
            var selected_plan_id = $(this).data('id');
            var selected_plan_title = $(this).data('title');
            if (selected_plan_id == "all") {
                $('.plan_commission_row').show();
            } else {
                $('.plan_commission_row').hide();
                $('.plan_commission_row_' + selected_plan_id).show();
            }
            $("#plan-text").html(selected_plan_title);
            setTimeout(function () {
                $("#ajax_loader").hide();
            }, 1000);
        });
    });
</script>
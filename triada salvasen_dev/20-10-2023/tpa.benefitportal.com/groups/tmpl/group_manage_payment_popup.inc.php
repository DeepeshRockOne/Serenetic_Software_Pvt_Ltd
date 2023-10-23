<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="mn">Manage Payment</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive payment-table">
            <table class="<?=$table_class?>">
                <thead>
                    <th>Company</th>
                    <th>Payment Method</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?=$group_setting_row['business_name']?>
                        </td>
                        <td>
                            <?php 
                                if($group_setting_row['payment_mode'] == "CC") {
                                    echo $group_setting_row['card_type']." *".$group_setting_row['last_cc_ach_no'];

                                } else if($group_setting_row['payment_mode'] == "ACH") {
                                    echo "ACH *".$group_setting_row['last_cc_ach_no'];

                                } else if($group_setting_row['payment_mode'] == "Check") {
                                    echo "Check";
                                } else {
                                    echo '-';
                                }
                                ?>
                        </td>
                        <td class="icons"><a href="auto_draft_setting.php?group_id=<?=md5($group_setting_row['customer_id']);?>" data-toggle="tooltip" title="View" class="btn_auto_draft_setting"><i class="fa fa-edit"></i></a></td>
                    </tr>                    
                    <?php
                        if($group_setting_row['invoice_broken_locations'] == "Y" && !empty($gc_res)) {
                            foreach ($gc_res as $key => $gc_row) {
                            ?>
                            <tr>
                                <td>
                                    <?=$gc_row['name']?>
                                </td>
                                <td>
                                    <?php 
                                            if($gc_row['payment_mode'] == "CC") {
                                                echo $gc_row['card_type']." *".$gc_row['last_cc_ach_no'];

                                            } else if($gc_row['payment_mode'] == "ACH") {
                                                echo "ACH *".$gc_row['last_cc_ach_no'];

                                            } else if($gc_row['payment_mode'] == "Check") {
                                                echo "Check";
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                </td>
                                <td class="icons"><a href="auto_draft_setting.php?group_id=<?=md5($gc_row['group_id']);?>&company_id=<?=md5($gc_row['company_id']);?>" data-toggle="tooltip" title="View" class="btn_auto_draft_setting"><i class="fa fa-edit"></i></a></td>
                            </tr>
                    <?php
                            }
                        }
                    
                    ?>
                </tbody>
            </table>
            <div class="text-center">
                <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    parent.$.colorbox.resize({
        height:350
    });
    $(document).on("click",".btn_auto_draft_setting",function(){
        var tmp_href = $(this).attr('href');
        console.log(tmp_href);
        parent.$.colorbox({href:tmp_href,iframe:true, width:"768px",height:"350px"});
    });
    
});
</script>
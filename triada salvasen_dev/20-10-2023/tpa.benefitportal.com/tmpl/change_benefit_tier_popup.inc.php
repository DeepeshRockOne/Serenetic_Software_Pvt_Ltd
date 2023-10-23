<div class="panel-collapse panel panel-popup" style="height: 350px;">
    <div class="panel-heading">
        <div class="panel-title"><?php 
        if($policy_change_reason == "policy_change") {
            echo "Edit Future Policy Change";

        } elseif($policy_change_reason == "benefit_amount_change") {
            echo "Edit Future Benefit Amount Change";

        } else {
            echo "Edit Future Coverage Update";
        } ?></div>
        
    </div>
    <div class="panel-body">
        <div class="theme-form">
            <form class="form_wrap" id="form_edit_future_coverage_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="ws_id" value="<?= $ws_id ?>">
                <input type="hidden" name="location" value="<?= $location ?>">
                <div class="col-xs-12">
                    <p class="fw500">Effective Date</p>
                    <div class="form-group">
                        <select class="form-control tier_change_date" name="tier_change_date" id="tier_change_date">
                            <option data-hidden="true"></option>
                            <?php
                                foreach ($date_selection_options as $key => $date_option) {
                                    ?>
                                    <option value="<?=$date_option['value']?>" <?=strtotime($tier_change_date) == strtotime($date_option['value'])?"selected":""?>><?=$date_option['text']?></option>
                                    <?php
                                }
                            ?>  
                        </select>
                        <label>Select</label>
                        <p class="error" id="error_tier_change_date"></p>
                    </div>
                </div>
                <div class="col-xs-12 m-t-10">
                    <label>
                        <input type="checkbox" id="cancel_benefit_tier_update" name="cancel_benefit_tier_update" value="Y">
                        <?php 
                        if($policy_change_reason == "policy_change") {
                            echo "Cancel future plan change and continue current plan";

                        } elseif($policy_change_reason == "benefit_amount_change") {
                            echo "Cancel future benefit amount change and continue current plan";

                        } else {
                            echo "Cancel future coverage update and continue current coverage";
                        } 
                        ?>
                    </label>
                </div>
                <div class="col-xs-12 text-center">
                    <br/>
                    <div class="form-group">
                        <button class="btn btn-icon btn-info" id="update" name="update" type="button">
                            Update
                        </button>
                        <a href="javascript:void(0);" class="btn btn-icon btn-default" name="cancel"
                           onclick="window.parent.$.colorbox.close()">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {       
        $(document).on('click', '#update', function (e) {
            $("#form_edit_future_coverage_update").submit();
        });

        $(document).on('submit', '#form_edit_future_coverage_update', function (e) {
            e.preventDefault();
            $("#ajax_loader").show();
            var Form_Data = $(this).serialize();
            $.ajax({
                url: 'change_benefit_tier_popup.php',
                type: 'POST',
                data: Form_Data,
                dataType: 'json',
                success: function (res) {
                    $("#ajax_loader").hide();
                    $('.error').html("");
                    if (res.status == 'success') {
                        window.parent.$.colorbox.close();
                    } else {
                        $.each(res.errors, function (index, error) {
                            $('#error_' + index).html(error).show();
                            var offset = $('#error_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 195;
                            $('body,html').animate({
                                scrollTop: totalScroll
                            }, 1200);
                            is_error = false;
                        });
                    }
                }
            });
        });
    });
</script>

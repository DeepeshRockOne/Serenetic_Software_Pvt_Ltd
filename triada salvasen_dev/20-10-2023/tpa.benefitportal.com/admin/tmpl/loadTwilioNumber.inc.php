<div class="table-responsive">
    <table class="<?=$table_class?> text-nowrap">
        <thead>
        <tr class="data-head">
            <th>Service</th>
            <th width="15%">SMS Number</th>
            <th class="text-center" >Is Active</th>
            <th>Sent SMS</th>
        </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
                    <?php 
                        $sqlCount = "SELECT count(id) as sent_sms_count FROM sms_log WHERE from_number=:from_number";
                        $resCount = $pdo->selectOne($sqlCount,array(":from_number"=>$rows['TwilioNumber']));

                        $sent_sms_count = 0;

                        if(!empty($resCount) && !empty($resCount['sent_sms_count'])){
                            $sent_sms_count = $resCount['sent_sms_count'];
                        }
                    ?>
                    <tr>
                        <td><?=$rows['service']?></td>
                        <td><?= format_telephone(str_replace($callingCode, $callingCodeReplace, $rows['TwilioNumber'])); ?></td>
                        <td class="text-center">
                            <input type="radio" name="isActive[]" id="is_active_number_<?= $rows['id'] ?>" data-id="<?= $rows['id'] ?>" value="Y" class="is_active_number" <?= $rows['is_active']=='Y' ? 'checked' : '' ?>>
                        </td>
                        <td><?= $sent_sms_count ?></td>
                    </tr>
                <?php }?>
            <?php } else {?>
                <tr>
                    <td colspan="4" class="text-center">No record(s) found</td>
                </tr>
            <?php } ?>
        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
</div>

<script type="text/javascript">
    $(document).off('click', '#twliioNumberDiv ul.pagination li a');
    $(document).on('click', '#twliioNumberDiv ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#twliioNumberDiv').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#twliioNumberDiv').html(res).show();
                $(".is_active_number").not('.js-switch').uniform();
                common_select();
            }
        });
    });

    $(document).off("change",".is_active_number");
    $(document).on("change",".is_active_number",function(){
        $id = $(this).attr('data-id');
        if($(this).is(":checked")){
            swal({
                text:'Activate Number: Are You sure?',
                showCancelButton:true,
                confirmButtonText:'Confirm',
            }).then(function () {
                $('#ajax_loader').show();
                $.ajax({
                    url: 'ajax_activateTwilioNumber.php',
                    data: {
                      id: $id,
                    },
                    method: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        $('#ajax_loader').hide();
                        if (res.status == "success") {
                            setNotifySuccess(res.msg);
                        } else {
                            setNotifyError(res.msg);
                        }
                    }
                });
            }, function (dismiss) {
                loadTwilioNumber();
            });   
        }
        
    });
</script>
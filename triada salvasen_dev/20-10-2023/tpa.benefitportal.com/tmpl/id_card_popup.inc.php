<div id="smarteapp_vue" class="panel panel-default idcard-panel">
    <div class="panel-heading">
        <?php if(isset($ws_row['member_name'])) { ?>
            <h4 class="mn">ID Card - <span class="fw300"><?=$ws_row['member_name']?></span></h4>
        <?php } else { ?>
            <h4 class="mn">ID Card</h4>
        <?php } ?>
    </div>
    <div class="panel-body">
        <div class="idcard-content">
        <?php 
            if(!empty($id_card_res)) {
                if(count($id_card_res) == 1) {
                    foreach ($id_card_res as $i_key => $id_card_row) {
                        echo $id_card_row['description'];
                    }
                } else {
                    ?>
                    <ul class="nav nav-tabs tabs  nav-noscroll customtab" role="tablist">
                    <?php
                    foreach ($id_card_res as $i_key => $id_card_row) {
                        ?>
                        <li role="presentation" class="<?=$i_key == 0?"active":""?>">
                            <a class="btn_card_tab" href="#id_card_tab_<?=$i_key?>" data-id_card_id="<?=$id_card_row['id']?>" data-toggle="tab" aria-expanded="false"><?=$id_card_row['name']?></a>
                        </li>
                        <?php
                    }
                    ?>
                    </ul>
                    <div class="tab-content m-t-20">
                    <?php
                    foreach ($id_card_res as $i_key => $id_card_row) {
                        ?>
                        <div role="tabpanel" class="tab-pane <?=$i_key == 0?"active":""?>" id="id_card_tab_<?=$i_key?>"><?=$id_card_row['description']?></div>                    
                        <?php
                    }
                    ?>
                    </div>
                    <?php
                }
            }
        ?>
        </div>
    </div>
    <?php if(!empty($id_card_row)) { ?>
        <div class="panel-footer theme-form p-l-15 p-r-15">
            <form action="ajax_send_id_card.php" role="form" method="post" class="theme-form " name="form_send_id_card" id="form_send_id_card" enctype="multipart/form-data">
                <input type="hidden" name="id_card_id" value="<?=$id_card_id;?>" id="id_card_id">
                <input type="hidden" name="ws_id" value="<?=$_GET['ws_id'];?>" id="ws_id">
                <input type="hidden" name="user_id" value="<?=isset($_GET['user_id'])?$_GET['user_id']:'';?>" id="user_id">
                <input type="hidden" name="user_type" value="<?=isset($_GET['user_type'])?$_GET['user_type']:'';?>" id="user_type">
                <div class="row">
                    <div class="col-md-8">
                            <div class="col-sm-4">
                                <div class="pr">
                                    <select class="form-control" name="sent_via" id="sent_via" v-model="sent_via">
                                        <option data-hidden="true"></option>
                                        <option value="email">Email</option>
                                        <option value="text">Text Message (SMS)</option>
                                        <option value="physical_card">Physical Card</option>
                                    </select>
                                    <p class="error" id="error_sent_via"></p>
                                </div>
                            </div>
                            <div class="col-sm-5" v-show="sent_via === 'email'">
                                <div class="pr">
                                    <input type="text" name="email_to" id="email_to" v-model="email_to" class="form-control">
                                    <p class="error" id="error_email_to"></p>
                                </div>
                            </div>
                            <div class="col-sm-5" v-show="sent_via === 'text'">
                                <div class="pr">
                                    <input type="text" name="sms_to" id="sms_to" v-model="sms_to" @keyup="sms_to = this.event.target.value;" class="form-control">
                                    <p class="error" id="error_sms_to"></p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="pr">
                                    <button type="button" id="btn_send_id_card" class="btn btn-info btn-block" :disabled="sent_via == '' || (sent_via == 'email' && email_to == '') || (sent_via == 'text' && sms_to == '')">Send</button>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="download_id_card.php?ws_id=<?=$_GET['ws_id'];?>&id_card_id=<?=$id_card_id;?>" target="_blank" class="btn red-link m-l-10 btn_download_id_card"><i class="fa fa-download"></i> Export</a>
                    </div>
                </div>
            </form>
        </div>
    <?php } ?>
</div>
<?php if(!empty($id_card_res)) { ?>
<script type="text/javascript">
     var app = new Vue({
        el: '#smarteapp_vue',
        data: {
            sent_via: '',
            email_to: "<?=$ws_row['email']?>",
            sms_to: "<?=format_telephone($ws_row['cell_phone'])?>"
        },
        methods: {},
        computed: {}
    });
    $(document).ready(function () {
        $("input#sms_to").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $('select.form-control').selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: true
        });
        $('select.form-control').selectpicker('refresh');

        $(document).off('click', '.btn_card_tab');
        $(document).on('click', '.btn_card_tab', function (e) {
            var id_card_id = $(this).data('id_card_id');
            $("#id_card_id").val(id_card_id);
            $(".btn_download_id_card").attr('href','download_id_card.php?ws_id=<?=$_GET['ws_id'];?>&id_card_id='+id_card_id);
        });

        $(document).off('click', '#btn_send_id_card');
        $(document).on('click', '#btn_send_id_card', function (e) {
            parent.disableButton($(this));
            $("#ajax_loader").show();
            $.ajax({
                url: $('#form_send_id_card').attr('action'),
                data: $('#form_send_id_card').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    parent.enableButton($("#btn_send_id_card"));
                    $("#ajax_loader").hide();
                    $("p.error").hide();

                    if (data.status == 'success') {
                        parent.setNotifySuccess(data.msg);
                        parent.$.colorbox.close();
                    } else if (data.status == "fail") {
                        parent.setNotifyError(data.msg);
                        parent.$.colorbox.close();
                    } else {
                        $(".error").hide();
                        var tmp_flage = true;
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();
                            if (tmp_flage == true && $("[name='" + key + "']").length > 0) {
                                tmp_flage = false;
                                $('html, body').animate({
                                    scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                                }, 1000);
                            }
                        });
                    }
                }
            });
        });
    });
</script>
<?php } ?>
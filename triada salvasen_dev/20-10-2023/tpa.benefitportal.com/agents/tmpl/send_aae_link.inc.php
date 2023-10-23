<div id="smarteapp_vue" class="panel panel-default panel-block generate_report_panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">
                <i class="fa fa-link"></i> &nbsp; <span class="fw300">Resend AAE Verification Link</span>
            </h4>
        </div>
    </div>
    <form action="ajax_send_aae_link.php" role="form" method="post" class="theme-form " name="form_send_aae_link" id="form_send_aae_link" enctype="multipart/form-data">
        <input type="hidden" name="customer_id" value="<?=md5($lq_row['customer_id']);?>">
        <input type="hidden" name="token" value="<?=$lq_row['token'];?>">
        <div class="panel-body">
            <div class="theme-form">
                <div id="member_submit_button">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control" name="sent_via" id="sent_via" v-model="sent_via">
                                    <option value=""></option>
                                    <option value="text">Text Message (SMS)</option>
                                    <option value="email">Email</option>
                                    <option value="Both">Email & Text Message (SMS)</option>
                                </select>
                                <label>Select Resend Method<em>*</em></label>
                                <p class="error" id="error_sent_via"></p>
                            </div>
                        </div>
                    </div>
                    <div id="show_send_option" class="clearfix" v-show="sent_via !== ''">
                        <div class="row">
                            <div id="email_tp" class="emailtp tp" v-show="sent_via === 'email' || sent_via === 'Both'">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" readonly class="form-control" size="35" value="<?= isset($lead_row['email']) ? $lead_row['email'] : '' ?>"/>
                                        <label>Email</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="email_subject" id="email_subject" class="form-control"
                                               size="35" value="<?= $email_subject ?>"/>
                                        <label>Subject</label>
                                        <p class="error" id="error_email_subject"></p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-12">
                                    <div class="form-group height_auto">
                                        <textarea id="email_content"
                                                  name="email_content"><?= $email_content ?></textarea>
                                        <span class="error textarea_error" id="error_email_content"></span>
                                        <div> Available tags : 
                                          <span class="text-light-gray">[[fname]], [[lname]], [[Email]], [[Phone]], [[Agent]], [[ParentAgent]], [[MemberID]], [[ActiveProducts]], [[link]]</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div id="sms_tp" class="smstp tp" v-show="sent_via === 'text' || sent_via === 'Both'">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" readonly  class="form-control" size="35"
                                               value="<?= isset($lead_row['cell_phone']) ? format_telephone($lead_row['cell_phone']) : '' ?>"/>
                                        <label>Phone</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group height_auto">
                                        <textarea id="sms_content" name="sms_content" rows="3" class="form-control"
                                                  maxlength="160"><?= $sms_content ?></textarea>
                                        <span class="error textarea_error" id="error_sms_content"></span>
                                        <div> Character limit : <span id="message1" class="text-light-gray">160</span>
                                            <br>Available tags: <span class="text-light-gray">[[fname]], [[lname]], [[link]]</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

    </form>
    <div class="m-b-30 p-b-10"></div>
    <div class="panel-footer text-center">
                            <button type="button" id="btn_send_aae_link" class="btn btn-action " :disabled="sent_via == ''">Send</button>
                            <a href="javascript:void(0)" class="btn red-link m-l-15" onclick="parent.$.colorbox.close();">Cancel</a>
                        </div>
</div>
<script type="text/javascript">
    var app = new Vue({
        el: '#smarteapp_vue',
        data: {
            sent_via: ''
        },
        methods: {},
        computed: {}
    });
    $(document).ready(function () {
        var chars = jQuery("#sms_content").val().length;
        jQuery("#message1").text(160 - chars);

        jQuery("#sms_content").keyup(function (e) {
            var chars = jQuery(this).val().length;
            jQuery("#message1").text(160 - chars);

            if (chars > 160 || chars <= 0) {
                jQuery("#message1").addClass("minus");
                jQuery(this).css("text-decoration", "line-through");
            } else {
                jQuery("#message1").removeClass("minus");
                jQuery(this).css("text-decoration", "");
                e.preventDefault();
            }
        });

        $('#email_content').summernote({
          toolbar: $SUMMERNOTE_TOOLBAR,
          disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
          focus: true, // set focus to editable area after initializing summernote
          height:125,
          callbacks: {
            onImageUpload: function(image) {
              editor = $(this);
              uploadImageContent(image[0], editor);
            },
            onMediaDelete : function(target) {
                deleteImage(target[0].src);
                target.remove();
            }
          }
        });

        $(document).off('click', '#btn_send_aae_link');
        $(document).on('click', '#btn_send_aae_link', function (e) {
            formHandler($("#form_send_aae_link"),
                function () {
                    $("#ajax_loader").show();
                },
                function (data) {
                    $("#ajax_loader").hide();
                    $("p.error").hide();
                    if (data.status == 'success') {
                        window.parent.location.href=window.parent.location.href;
                    } else if (data.status == "fail") {
                        window.parent.location.href=window.parent.location.href;
                    } else {
                        $(".error").hide();
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();
                            if ($("[name='" + key + "']").length > 0) {
                                $('html, body').animate({
                                    scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                                }, 1000);
                            }
                        });
                    }
                });
        });
    });
</script>
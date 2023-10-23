<?php include "notify.inc.php"; ?>
<div class="module_instruction m-b-30">
    <div class="container">
        <h4 class="m-t-0 m-b-15">Instructions</h4>
        <p class="mn">Fill in the form below with the recipients details. A generated link will be created to send to
            the recipient. <span class="text-action">The link that will be generated and sent off to this new agent is valid for 7 Days.</span>
        </p>
    </div>
</div>
<div id="smarteapp_vue" class="container theme-form m-b-40">
    <form action="" role="form" method="post" name="user_form" id="user_form" enctype="multipart/form-data">
        <input type="hidden" name="parent_agent_id" id="parent_agent_id" value="<?= checkIsset($agentId) ?>"/>
        <h4 class="m-b-20">+ Agent</h4>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" id="fname" name="fname" v-model="fname" value="<?=$fname?>" class="form-control"/>
                    <label for="fname">First Name<em>*</em></label>
                    <div id="fname_err" class="mid"><span></span></div>
                    <p class="error"><span id="err_fname"></span></p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group ">
                    <input type="text" id="lname" name="lname" v-model="lname" value="<?=$lname?>" class="form-control"/>
                    <label for="lname">Last Name<em>*</em></label>
                    <div id="lname_err" class="mid"><span></span></div>
                    <p class="error"><span id="err_lname"></span></p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" id="cell_phone" name="cell_phone" v-model="cell_phone"  value="<?=format_telephone($cell_phone)?>" class="form-control"/>
                    <label for="phone">Phone<em>*</em></label>
                    <div id="cell_phone_err" class="mid"><span></span></div>
                    <p class="error"><span id="err_cell_phone"></span></p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" id="email" name="email" v-model="email"  value="<?=$email?>" class="form-control no_space"/>
                    <label for="email">Email<em>*</em></label>
                    <div id="email_err" class="mid"><span></span></div>
                    <p class="error"><span id="err_email"></span></p>
                </div>
            </div>
            
            <div class="col-sm-6">
                <input type="hidden" name="profile_id" id="profile_id" value="<?= $profile_id ?>"/>
                <input type="hidden" name="allow_sell_prd" class="allow_sell_prd" value="Y" checked='checked'>
                <div class="form-group " id="product_to_sell">
                        <select name="products[]" id="products" multiple="multiple" class="se_multiple_select">
                        </select>
                        <label for="products">Assign Product(s)<em>*</em></label>
                        <div id="products_err" class="mid"><span></span></div>
                        <p class="error"><span id="err_products"></span></p>
                    </div>
            </div>
            
            <div class="col-sm-6">
                <div id="license_step_div">
                    <div class="form-group">
                        <select name="coded_level" class="" id="coded_level">
                        </select>
                        <label for="coded_level">Agent Level<em>*</em></label>
                        <div id="coded_level_err" class="mid"><span></span></div>
                        <p class="error"><span id="err_coded_level"></span></p>
                        <div class="clearfix m-t-5 pull-right"><a href='javascript:void(0)'
                                                                  data-href="<?=$HOST ?>/view_commission_rule_info.php?profile_id=<?= $profile_id ?>&agent_id=<?= $default_agent_row['id'] ?>"
                                                                  class="comm_popup red-link ">Commissions</a></div>
                    </div>
                </div>
                <input type="hidden" name="access_type" id="access_type" value="full_access"/>
            </div>
        </div>
        <hr class="m-t-15">
        <div class="m-b-20" id="send_contract">
          <p>Would you like  <!-- <a id="smartE-link" href="javascript:void(0);" class="red-link " tabindex="0" data-placement="top"  data-popover-content="#smartE-popover"><?= $DEFAULT_SITE_NAME ?></a> --> system to invite this agent or do this personally?</p>
            <div id="smartE-popover" class="hide">
                <p><strong class="fs16 text-blue"><i class="fa fa-info-circle"></i> &nbsp; Who is <?= $DEFAULT_SITE_NAME ?>?</strong></p>
                <p>Built into our platform, <?= $DEFAULT_SITE_NAME ?> is part of our artificial intelligence that delivers information
                    based on your unique business processes and customer data. Using those insights to automate
                    responses and actions, making you more productive, and your members even happier. Talk about
                    smart!<br> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" width="50px" align="right"/></p>
            </div>
            <p>
                <label class="mn">
                    <input type="radio" class="radio_button" name="send_contract_radio" value="yes">
                    System Invite </label>
            </p>
            <p class="mn">
                <label class="mn">
                    <input type="radio" class="radio_button" name="send_contract_radio" value="no">
                    Personal Invite </label>
            </p>
            <div id="send_contract_radio_err" class="mid"><span></span></div>
            <p class="error"><span id="err_send_contract_radio"></span></p>
        </div>
        <div id="smarte_div" class="send_contract_div" style="display: none;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <select class="form-control" id="select_type" name="select_type">
                            <option value="" disabled selected hidden></option>
                            <option value="email">Email</option>
                            <option value="text">Text Message (SMS)</option>
                            <option value="email_text">Email & Text Message (SMS)</option>
                        </select>
                        <label>Select Delivery Method<em>*</em></label>
                        <div id="select_type_err" class="mid"><span></span></div>
                        <p class="error"><span id="err_select_type"></span></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="emailtp tp"
                         style="display: <?php echo (!empty($select_type) && in_array('email', $select_type)) ? 'block' : 'none'; ?>">
                        <h4 class="m-t-0 m-b-20">Email</h4>
                        <div class="form-group ">
                            <input type="text" name="email_from" id="email_from" class="form-control no_space"
                                   value="<?= $email_from ?>">
                            <label>From</label>
                            <p class="error"><span id="err_email_from"></span></p>
                        </div>
                        <div class="form-group ">
                            <input type="text" name="email_subject" id="email_subject" value="<?= $email_subject ?>"
                                   class="form-control ">
                            <label>Subject</label>
                        </div>
                         <div class="m-b-20">
                        <textarea id="email_content" name="email_content"
                                  class="cust_summernote"><?= $email_content ?></textarea>
                        <div id="email_content_err" class="mid"><span></span></div>
                        <p class="error"><span id="err_email_content"></span></p>
                    </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="smstp tp m-b-20"
                         style="display:<?php echo (!empty($select_type) && in_array('text', $select_type)) ? 'block' : 'none'; ?>">
                        <h4 class="m-t-0 m-b-20">Text Message(SMS)</h4>
                        <!-- <div class="form-group">
                            <input type="text" name="" class="form-control">
                            <label>From Phone</label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="" class="form-control">
                            <label>To Phone</label>
                        </div> -->
                        <div class="m-b-25">
                            <textarea id="sms_content" name="sms_content" rows="17"
                                      class="form-control <?php echo isset($errors['sms_content']) ? 'parsley-error' : '' ?>"
                                      maxlength="160"><?= $sms_content ?></textarea>
                            <div class="m-t-5">Character Remaining : <span
                                        id="sms_content_count"><?php echo $sms_content != "" ? 160 - strlen($sms_content) : 160; ?></span>
                                <br>Messages over 160 characters will send in multiple SMS messages.
                            </div>
                            <div id="sms_content_err" class="mid"><span></span></div>
                            <p class="error"><span id="err_sms_content"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <hr class="m-t-0">
            <button class="btn btn-action" type="submit" name="save" id="save">Generate Invite</button>
            <button class="btn red-link" type="button" name="cancel" onClick="window.location = 'agent_listing.php'">
                Cancel
            </button>
        </div>
    </form>
</div>
<div class="modal fade" id="copy_alert">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="mn fs20"><i class="fa fa-link" aria-hidden="true"></i>&nbsp; <span
                            class="agent_name fw600"></span> - Agent Application Link</p>
            </div>
            <div class="modal-body">
                <label>Copy the link below to copy and share</label>
                <div class="row">
                    <div class="col-sm-10">
                        <div class="form-group">
                            <input type="text" class="form-control" id="copytext" readonly="readonly"
                                   data-clipboard-text="1111" tabindex="" placeholder="display link here" value=""/>
                            <textarea id="holdtext" style="display:none;"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <button class="btn btn-info" id="copyingg" data-clipboard-target="#copytext">COPY LINK
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="no-thanks btn red-link">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // var app = new Vue({
    //     el: '#smarteapp_vue',
    //     data: {
    //         lead_id: '<?=$lead_id?>',
    //         fname: '<?=$fname?>',
    //         lname: '<?=$lname?>',
    //         email: '<?=$email?>',
    //         cell_phone: '<?=format_telephone($cell_phone)?>',
    //         state: '<?=$state?>',
    //     },
    //     methods: {},
    //     computed: {}
    // });
    var clipboard = new Clipboard('#copyingg');
    $(document).ready(function () {
        checkEmail();
        $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        // $("#products").multipleSelect('refreshOptions',{});
        // auto_complete_opt_sponsor();
        // $("#parent_agent").autocomplete(auto_complete_opt_sponsor);

        $('#parent_agent').addClass('form-control');
        $('#parent_agent').selectpicker({
            container: 'body',
            style: 'btn-select',
            noneSelectedText: '',
            dropupAuto: false,
        });

        set_agent_products($("#parent_agent_id").val());
        $(document).off("change", "#parent_agent");
        $(document).on("change", "#parent_agent", function (e) {
            set_agent_products($(this).val());
            $("#parent_agent_id").val($(this).val());
            $(this).selectpicker();
        });
        $('#smartE-link').popover({
            container: 'body',
            html: true,
            trigger: 'hover',
            template: '<div class="popover smarte-popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
            content: function () {
                var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
                return clone;
            }
        }).click(function (e) {
            e.preventDefault();
        });

        $('.cust_summernote').summernote({
          toolbar: $SUMMERNOTE_TOOLBAR,
          disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
          focus: true, // set focus to editable area after initializing summernote
          height:350,
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


        var allow_to_sell = $(".allow_sell_prd").val();
        $('#send_contract_div').fadeIn("slow");
        if (allow_to_sell == 'Y') {
            $('#product_to_sell').show('slow');
        } else {
            $('#product_to_sell').hide('slow');
            $("#products").multipleSelect("uncheckAll");
        }


        // $(".comm_popup").colorbox({iframe: true, width: '850px', height: '500px'});
        $(document).off("click", ".comm_popup");
        $(document).on("click", ".comm_popup", function (e) {
            $.colorbox({iframe: true, href: $(this).attr('data-href'), width: '850px', height: '500px'});
        });

        $(".select-multiselect").multipleSelect({
            width: '100%',
            selectAll: false,
        });
    });

    $(document).off("click", ".no-thanks");
    $(document).on("click", ".no-thanks", function () {
        $('#copy_alert').modal('hide')
        location.href = "agent_listing.php"
    });

    $(document).off("click", "#copyingg");
    $(document).on("click", "#copyingg", function () {

        clipboard.on('success', function (e) {
            setNotifySuccess("Link Copied!");
            $('#copy_alert').modal('hide')
            location.href = "agent_listing.php"
        });
    });

    $(document).off("click", "#save");
    $(document).on("click", "#save", function (e) {
        e.preventDefault();
        $('.error span').html('');
        $('#ajax_loader').show();
        $.ajax({
            url: "<?= $AGENT_HOST ?>/ajax_invite_agent.php",
            data: $("#user_form").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                $('#ajax_loader').hide();

                if (res.status == 'success') {
                    var val = res.link;
                    var fname = res.fname;
                    var lname = res.lname;
                    if (res.invite_by == 'personal_invite') {
                        $('#copytext').val(val);
                        $('.agent_name').html(fname + " " + lname);
                        $('#copy_alert').modal('show')
                    } else {
                        window.location.href = "agent_listing.php";
                    }
                } else if (res.status == 'fail') {
                    var is_error = true;
                    $('.error span').html('');
                    $('.form-group').removeClass('has-error');
                    $.each(res.errors, function (index, value) {
                        $('#err_' + index).closest('.form-group').addClass('has-error');
                        $('#err_' + index).html(value).show();
                        if (is_error) {
                            var offset = $('#err_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 50;
                            $('body,html').animate({scrollTop: totalScroll}, 1200);
                            is_error = false;
                        }
                    });
                }
                return false;
            }
        });
    });

    $(document).off("click", ".access_type");
    $(document).on('click', '.access_type', function () {
        var access_val = $(this).val();
        if (access_val == 'limited') {
            $("#access_feature_div").show('slow');
        } else {
            $("#access_feature_div").hide('slow');
        }
    });

    $(document).off("click", ".radio_button");
    $(document).on('click', '.radio_button', function () {
        var val = $(this).val();
        if (val == 'yes') {
            $("#smarte_div").show();
            $.uniform.update();
        } else {
            $("#smarte_div").hide();
            $.uniform.update();
        }
    });

    $(document).off("change", "#select_type");
    $(document).on("change", "#select_type", function () {
        var check_val = $(this).val();
        if (check_val == 'text') {
            $(".emailtp").hide();
            $(".smstp").show();
        } else if (check_val == 'email') {
            $(".smstp").hide();
            $(".emailtp").show();
        } else if (check_val == 'email_text') {
            $(".smstp").show();
            $(".emailtp").show();
        }
    });

    $(document).off("keyup", "#sms_content");
    $(document).on('keyup', '#sms_content', function (e) {
        var chars = $("#sms_content").val().length;
        if (160 - chars <= 0) {
            $("#sms_content_count").parent("span").addClass("text-danger");
        } else {
            $("#sms_content_count").parent("span").removeClass("text-danger");
        }
        $("#sms_content_count").text(160 - chars);
    });

    $(document).off("blur", "#email_from");
    $(document).on('blur', '#email_from', function (e) {
        $val = $(this).val();
        if ($val == '') {
            $(this).val('<?= $email_from ?>');
        }
    });

    $(document).off("blur", "#email_subject");
    $(document).on('blur', '#email_subject', function (e) {
        $val = $(this).val();
        if ($val == '') {
            $(this).val('<?= $email_subject ?>');
        }
    });


    function set_agent_products(agent_id) {
        $('#ajax_loader').show();
        var level = 'agent_level';
        var profile_id = $("#profile_id").val();
        $.ajax({
            url: "<?=$HOST ?>/get_products_invite_agent.php",
            type: "POST",
            dataType: "json",
            data: {agent_id: agent_id, level: level},
            success: function (res) {
                $('#ajax_loader').hide();
                if (res.status == 'success') {
                    $("#coded_level").html("");
                    $("#err_products").html();
                    $("#products").html(res.products_drop_down_html);
                    $("#products").multipleSelect('refresh');
                    $("#coded_level").html(res.level_html);
                    $("#coded_level").addClass('form-control');
                    $('#coded_level').selectpicker({ 
                      container: 'body', 
                      style:'btn-select',
                      noneSelectedText: '',
                      dropupAuto:false,
                    });
                    $('#coded_level').selectpicker('refresh');
                    $('.comm_popup').attr('data-href', "<?=$HOST ?>/view_commission_rule_info.php?profile_id=" + profile_id + "&agent_id=" + agent_id);
                    fRefresh();
                } else {
                    $("#products").html("");
                    $("#coded_level").html("");
                    $("#products").multipleSelect('refresh');
                }
            }
        });
    }

    // var auto_complete_opt_sponsor = function(){
    //       autoFocus: true,
    //       source: function (request, response) {
    //         $.ajax({
    //             url: "ajax_search_parent_agent.php?action=agent_auto_complete",
    //             type: "POST",
    //             dataType: "json",
    //             data: {query: request.term},
    //             success: function (data) {
    //               response(data);
    //             }
    //         });
    //       },
    //       minLength: 0,
    //       select: function (event, ui) {
    //           var label = ui.item.label;
    //           var agent_id = ui.item.val;
    //           $("#parent_agent_id").val(agent_id);
    //           set_agent_products(agent_id);
    //       },
    //       change: function (event, ui) {
    //           if(!ui.item){
    //               $("#parent_agent").val("");
    //               $("#parent_agent_id").val('');
    //           }

    //       }
    //   };
</script> 

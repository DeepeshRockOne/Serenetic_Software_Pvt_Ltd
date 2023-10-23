<?php if(isset($_GET['get_access_level_data'])) { ?>
<table class="<?=$table_class?>">
    <thead>
        <tr>
            <th>Access Level Name</th>
            <th class="text-center">Assigned #</th>
            <th class="text-center">Default Access</th>
            <th>Added Date</th>
            <th width="130px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($res_acl)){ ?>
            <?php foreach($res_acl as $acl){?>
                <tr>
                    <td><?=$acl['name'];?></td>
                    <td class="text-center"><?=$total_ass[$acl['name']]?></td>
                    <td class="icons text-center"><a id="feature_access" href="javascript:void(0);" data-toggle="tooltip" data-href="create_new_admin_level.php?action=edit&id=<?=$acl['id'];?>" title="" ><i class="fa fa-wrench" aria-hidden="true"></i></a></td>
                    <td><?=date('m/d/Y',strtotime($acl['created_at']))?></td>
                    <td class="icons">
                        <a class="edit_new_level" href="create_new_admin_level.php?action=edit&id=<?=$acl['id'];?>" data-toggle="tooltip" title="Edit" ><i class="fa fa-edit"></i></a>
                        <?php if(!in_array($acl['name'],array('Executive','Development'))) { ?>
                        <a class="delete_access_level" data-id="<?=$acl['id'];?>" data-name="<?=$acl['name'];?>" href="javascript:void(0);" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<?php } else { ?>
    <div class="add_level_panelwrap">
        <div class="panel panel-default panel-block ">
            <div class="panel-body">
                <form action="" name="frm_global_security" id="frm_global_security">
                    <input type="hidden" name="ip_group_count" value="1" id="ip_group_count">
                    <input type="hidden" name="security_ajax" value="" id="security_ajax">
                    <input type="hidden" name="ip_display_counter" value="0" id="ip_display_counter">
                    <div class="add-level-heading">
                        <div class="clearfix m-b-15">
                            <div class="pull-left">
                                <p class="fs16 fw600 mn text-red">Global Admin Login Security</p>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:void(0);" class="btn btn-action save_global_security">Save</a>
                            </div>
                        </div>
                    </div>
                    <div class="row theme-form">
                        <div class="col-sm-8">
                            <div class="phone-control-wrap m-b-25">
                                <div class="phone-addon text-left">
                                    <strong>Two-Factor Authentication (2FA):</strong><br>
                                    Two-factor authentication is an extra layer of security on login designed to ensure that user is the only person who can access their account, even if someone knows their password.
                                </div>
                                <div class="phone-addon w-90">
                                    <div class="custom-switch">
                                        <label class="smart-switch">
                                            <input type="checkbox" class="js-switch" name="is_2fa" id="is_2fa" <?=checkIsset($globalSett['is_2fa'])=='Y' ? 'checked' : ''?> value="Y" />
                                            <div class="smart-slider round"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="phone-control-wrap">
                                <div class="phone-addon text-left">
                                    <strong>IP Address Restriction:</strong><br>
                                    IP restrictions allow user to specify which IP addresses have access to sign in to their account. We recommend using IP restrictions if user desires to access account when they are in office, mobile, etc.
                                </div>
                                <div class="phone-addon w-90">
                                    <div class="custom-switch">
                                        <label class="smart-switch">
                                            <input type="checkbox" class="js-switch" name="is_ip_restriction" id="is_ip_restriction" <?=checkIsset($globalSett['is_ip_restriction'])=='Y' ? 'checked' : ''?> value="Y" />
                                            <div class="smart-slider round"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $allowed_ip_res = array();
                            if(checkIsset($globalSett['is_ip_restriction']) == 'Y' && !empty($globalSett['allowed_ip'])) {
                                $allowed_ip_res = explode(',',$globalSett['allowed_ip']);
                            }
                        ?>
                        <div class="clearfix"></div>
                        <div class="ip_address_div m-t-25" style="<?=checkIsset($globalSett['is_ip_restriction'])=='Y' ? '' : 'display: none;'?>">
                            <div class="col-sm-6 col-sm-offset-2  m-b-25">
                                <div id="ip_address_row_div">
                                    <?php if(!empty($allowed_ip_res)) {
                                            foreach ($allowed_ip_res as $key => $allowed_ip) { ?>
                                    <div class="ip_address_row" id="ip_address_row_<?=$key?>" data-id="<?=$key?>">
                                        <div class="phone-control-wrap">
                                            <div class="phone-addon">
                                                <div class="form-group">
                                                    <input type="text" name="allowed_ip_res[<?=$key?>]" class="form-control ip_input" value="<?=$allowed_ip?>">
                                                    <label>IP Address</label>
                                                    <p class="error text-left"><span id="error_ip_address_<?=$key?>"></span></p>
                                                </div>
                                            </div>
                                            <?php if($key > 0) { ?>
                                                <div class="phone-addon">
                                                    <div class="form-group">
                                                        <a href="javascript:void(0);" class="text-light-gray fw700 remove_ip_address"  data-id="<?=$key?>">X</a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } } else { ?>
                                    <div class="ip_address_row" id="ip_address_row_0" data-id="0">
                                        <div class="form-group">
                                        <input type="text" name="allowed_ip_res[0]" class="form-control ip_input"  value="<?=checkIsset($allowed_ip[0])?>">
                                        <label>IP Address</label>
                                        <p class="error"><span id="error_ip_address_0"></span></p>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="add_ip_address_row text-right">
                                    <button id="add_ip_address" type="button" class="btn btn-action">+ IP Address</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clearfix m-b-15"></div>
                <hr />
                <div class="add-level-heading m-b-15 clearfix">
                    <div class="pull-left">
                        <p class="fs16 fw600 mn">Admin Levels</p>
                        <p class="mn">Create level options to assign to an admin. Each name, dashboard type, and available admin portal features may be customized per level.</p>
                    </div>
                    <div class="pull-right">
                    <a href="create_new_admin_level.php" class="btn btn-action create_new_level">+ Level</a>
                    </div>
                </div>
                <div class="table-responsive" id="access_level_data">
                </div>
                <div class="clearfix m-b-20 m-t-30">
                    <div class="pull-left">
                        <p class="fs16 fw600 mn">Admin Agreement</p>
                        <p class="mn">The terms an admin will agree to when signing up for an account.</p>
                    </div>
                    <div class="pull-right">
                        <a href="javascript:void(0);"  id="edit_terms" data-id="<?=$res_t['id']?>" data-type="Admin"></a>
                    </div>
                </div>
                <textarea rows="13" class="summernote" id="admin_terms" name="admin_terms"><?=$res_t['terms']?></textarea>
            </div>
        </div>
    </div>
    <div id="dynamic_ip_address_div" style="display: none">
        <div class="ip_address_row" id="ip_address_row_~number~" data-id="~number~">
            <div class="phone-control-wrap">
            <div class="phone-addon">
                <div class="form-group">
                    <input type="text" name="allowed_ip_res[~number~]" class="form-control ip_input" >
                    <label>IP Address</label>
                    <p class="error text-left"><span id="error_ip_address_~number~"></span></p>
                </div>
            </div>
            <div class="phone-addon">
                <div class="form-group">
                <a href="javascript:void(0);" class="text-light-gray remove_ip_address"  data-id="~number~">X</a>
            </div>
            </div>
        </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            get_access_level_data();
            initCKEditor("admin_terms",true,'350px');

            //$('.summernote').summernote('disable');
            $("#edit_terms").addClass('fa fa-edit fs18 edit_term');
            // $('.summernote').summernote({
            //   toolbar: $SUMMERNOTE_TOOLBAR,
            //   disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
            //   focus: true, // set focus to editable area after initializing summernote
            //   height:350,
            //   callbacks: {
            //     onImageUpload: function(image) {
            //       editor = $(this);
            //       uploadImageContent(image[0], editor);
            //     },
            //     onMediaDelete : function(target) {
            //         deleteImage(target[0].src);
            //         target.remove();
            //     }
            //   }
            // });
            // $('#admin_terms').summernote('disable');
            // var id_description = $("#admin_terms").attr('name');
            // CKEDITOR.config.readOnly = true;
            // CKEDITOR.instances['admin_terms'].setReadOnly(true);
            
        });
        
        $(document).off('click', '.create_new_level');
        $(document).on('click', '.create_new_level', function (e) {
            e.preventDefault();
            $.colorbox({
                href: $(this).attr('href'),
                iframe: true, 
                width: '500px', 
                height: '600px',
                onClosed: function() {
                    get_access_level_data();
                }
            });
        });

        $(document).off('click', '.edit_new_level');
        $(document).on('click', '.edit_new_level', function (e) {
            e.preventDefault();
            $.colorbox({
                href: $(this).attr('href'),
                iframe: true, 
                width: '500px', 
                height: '600px',
                onClosed: function() {
                    get_access_level_data();
                }
            });
        });

        $(document).off('click', '.delete_access_level');
        $(document).on('click', '.delete_access_level', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            $('#ajax_loader').show();
            $.ajax({
                url: 'delete_admin_access_level.php',
                data: {
                    id: id,
                    name: name
                },
                type: 'POST',
                success: function(res) {
                    $('#ajax_loader').hide();
                    if(res.status == "deleted") {
                        get_access_level_data();
                        setNotifySuccess('Access Level Deleted Successfully!');
                    
                    } else if(res.status == "need_reassign_admin") {
                        $.colorbox({
                            href: 'create_new_admin_level.php?action=delete&id='+id,
                            iframe: true, 
                            width: '500px', 
                            height: '600px',
                            onClosed: function() {
                                get_access_level_data();
                            }
                        });
                    }
                }
            });
        });

        $(document).off('click', '#feature_access');
        $(document).on('click', '#feature_access', function(e) {
            e.preventDefault();
            $href = $(this).attr("data-href");
            $.colorbox({
                href: $href,
                iframe: true,
                width: '500x',
                height: '600px',
                onClosed: function() {
                    get_access_level_data();
                }
            });
        });

        $(document).off('click', '#edit_terms');
        $(document).on('click', '#edit_terms', function(e) {
            if ($(this).hasClass('edit_term')) {
                // $('.summernote').summernote('enable');
                // CKEDITOR.config.readOnly = false;
                CKEDITOR.instances['admin_terms'].setReadOnly(false);
                $("#edit_terms").removeClass('edit_term pull-right fa fa-edit fs18 m-t-15');
                $("#edit_terms").addClass('pull-right btn btn-info save_term m-t-7').text('Save');
            } else {
                // $('.summernote').summernote('disable');
                CKEDITOR.instances['admin_terms'].setReadOnly(true);
                $("#edit_terms").removeClass('pull-right btn btn-info save_term').text('');;
                $("#edit_terms").addClass('pull-right fa fa-edit fs18 m-t-15 edit_term');
                $('#ajax_loader').show();
                var id = $(this).data('id');
                var type = $(this).data('type');
                var terms = CKEDITOR.instances['admin_terms'].getData();
                $.ajax({
                    url: 'ajax_update_terms.php',
                    data: {
                        id: id,
                        type: type,
                        terms: terms
                    },
                    type: 'POST',
                    success: function(res) {
                        $('#ajax_loader').hide();
                        if(res.status='success'){
                            setNotifySuccess(res.msg);
                            // $('.summernote').summernote('disable');
                            CKEDITOR.instances['admin_terms'].setReadOnly(true);
                        }else{
                            setNotifyError(res.msg);
                        }
                    }
                })
            }
        });
        function get_access_level_data()
        {
            $('#ajax_loader').show();
            $.ajax({
                url: 'add_access_level.php?get_access_level_data=1',
                data: {},
                type: 'POST',
                success: function(res) {
                    $('#ajax_loader').hide();
                    $("#access_level_data").html(res);
                    $('[data-toggle="tooltip"]').tooltip();
                }
            })
        }

        $(document).off('click', '.save_global_security');
        $(document).on('click', '.save_global_security', function(e){
            e.preventDefault();
            $('#ajax_loader').show();
            $("#security_ajax").val(1);
            $.ajax({
                url: 'add_access_level.php',
                data: $("#frm_global_security").serialize(),
                type: 'POST',
                success: function(res) {
                    $("p.error").hide();
                    $("#security_ajax").val("");
                    $('#ajax_loader').hide();
                    console.log(res.status);
                    if(res.status == 'success'){
                        setNotifySuccess("Global Admin Login Security Updated Successfully.");
                    }else if(res.status=='fail'){
                        $.each(res.errors, function(key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                        });
                    }else{
                        setNotifySuccess("No updates.");
                    }
                }
            });
        });

        $(document).off('click', '.remove_ip_address');
        $(document).on('click', '.remove_ip_address', function(e){
            e.preventDefault();
            $add_counter = parseInt($("#ip_group_count").val()) - 1;
            if($add_counter <= 10){
                $("#add_ip_address").show();
            }
            $("#ip_group_count").val($add_counter);
            $("#ip_address_row_"+$(this).attr('data-id')).remove();
        });

        $(document).off("change", "#is_ip_restriction");
        $(document).on("change", "#is_ip_restriction", function () {
            if($(this).is(":checked")) {
                $(".ip_address_div").show();
            } else {
                $(".ip_address_div").hide();
            }
        });

        $(document).off('click', '#add_ip_address');
        $(document).on('click', '#add_ip_address', function(e){
            e.preventDefault();
            $add_counter = parseInt($("#ip_group_count").val()) + 1;
            if($add_counter >= 10){
                $(this).hide();
            }
            $("#ip_group_count").val($add_counter);
            loadIPAddressDiv();
        });

        loadIPAddressDiv = function(){
            $count = $("#account_detail .ip_address_row").length;
            $ip_display_counter = parseInt($('#ip_display_counter').val());
            $number = $count+1;
            if($ip_display_counter > $count){
                $number = $ip_display_counter + 1;
            }
            $neg_number = $number * -1;
            html = $('#dynamic_ip_address_div').html();
            $('#ip_address_row_div').append(html.replace(/~number~/g, $neg_number));
            $("#ip_display_counter").val($number);
        }
    </script>
<?php } ?>   
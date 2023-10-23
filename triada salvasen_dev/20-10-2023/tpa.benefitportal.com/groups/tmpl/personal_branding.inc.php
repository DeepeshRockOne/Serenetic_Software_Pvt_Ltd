<div id="smarteapp_vue" class="panel panel-default ">
    <div class="panel-heading">
        <div class="panel-title">
            <h4 class="mn">Personal Branding - <span class="fw300"><?=!empty($_SESSION['groups']['public_name']) ? $_SESSION['groups']['public_name'] : $_SESSION['groups']['fname'].' '.$_SESSION['groups']['lname'];?></span></h4>
        </div>
    </div>
    <form action="personal_branding.php" role="form" method="post" class="theme-form " name="form_personal_branding" id="form_personal_branding" enctype="multipart/form-data">
        <input type="hidden" name="operation" value="personal_branding">
        <div class="panel-body">
            <p class="fw600 lato_font">Color Palette</p>
            <div class="row color_selection_label">
                <div class="col-xs-4">
                    <label for="default_color" class="<?=$tmp_theme_color == "skin-default" ? "active" : ""?>" >
                        <input <?=$tmp_theme_color == "skin-default" ? "checked=''" : ""?> value="skin-default" type="radio" name="theme_color" class="theme_color" id="default_color">
                        <div class="text-center">
                            <img src="<?=$GROUP_HOST?>/images/theme/default-color.png<?=$cache?>">
                        </div>
                        <span>Default</span>
                    </label>
                </div>
                <div class="col-xs-4">
                    <label for="dark_color" class="<?=$tmp_theme_color == "skin-dark" ? "active" : ""?>">
                        <input type="radio" <?=$tmp_theme_color == "skin-dark" ? "checked=''" : ""?> value="skin-dark" name="theme_color" class="theme_color" id="dark_color">
                        <div class="text-center">
                            <img src="<?=$GROUP_HOST?>/images/theme/dark-color.png<?=$cache?>">
                        </div>
                        <span>Dark</span>
                    </label>
                </div>
                <div class="col-xs-4">
                    <label for="light_color" class="<?=$tmp_theme_color == "skin-light" ? "active" : ""?>">
                        <input type="radio" <?=$tmp_theme_color == "skin-light" ? "checked=''" : ""?> value="skin-light" name="theme_color" class="theme_color" id="light_color">
                        <div class="text-center">
                            <img src="<?=$GROUP_HOST?>/images/theme/light-color.png<?=$cache?>">
                        </div>
                        <span>Light</span>
                    </label>
                </div>
            </div>
            <div class="theme-screenshot-wrap">
                <?php 
                    if($tmp_theme_color == "skin-dark"){
                        $theme_img = $GROUP_HOST . "/images/theme/dark-theme.png" . $cache;
                    }else if($tmp_theme_color == "skin-light"){
                        $theme_img = $GROUP_HOST . "/images/theme/light-theme.png" . $cache;
                    }else{
                        $theme_img = $GROUP_HOST . "/images/theme/default-theme.png" . $cache;
                    } 
                ?>
                <img src="<?= $theme_img ?>" name="theme-img" id="theme-img">
            </div>
            <div>
                <div class="text-center">
                    <button type="button" class="btn btn-info" id="btn_apply">Apply</button>
                </div>    
            </div>
            <p class="fw600 lato_font m-b-20">Logo</p>
            <p class="fw500 m-b-20">Click the box below to upload your customized branding logo.</p>
            <div class="agent_drop_div pro_drop_div m-b-20" id="enrollment_profile">
                <input type="hidden" id="contract_profile_image_size" name="profile_image[size]"
                       value=""/>
                <input type="hidden" id="contract_profile_image_name" name="profile_image[name]"
                       value=""/>
                <input type="hidden" id="contract_profile_image_type" name="profile_image[type]"
                       value=""/>
                <input type="hidden" id="contract_profile_image_tmp_name"
                       name="profile_image[tmp_name]" value=""/>
                <div class="dropzone profile-dropzone">
                    <div class="dropzone-previews">
                    </div>
                </div>
            </div>
            <div class="text-right pro_link_div m-t-15">
                <button type="button" class="btn btn-info btn_upload_logo">Upload</button>
                <?php if ($contract_business_image != "" && file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image)) { ?>
                    <a href="javascript:void(0);"  style="<?= $tmp_style; ?>" onclick="return delete_business_image();"
                   class="btn red-link">Remove</a>
                <?php } ?>
            </div>
        </div>
    </form>
    <div class="panel-footer text-center">
        <!-- <button type="button" class="btn btn-action btn_save">Save</button> -->
        <a href="javascript:void(0)" class="btn red-link m-l-15" onclick="parent.$.colorbox.close();">Close</a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#btn_apply').on('click',function(e){
            e.preventDefault();
            var theme = $("input[name='theme_color']:checked"). val();
            $.ajax({
                url: '<?=$GROUP_HOST?>/ajax_change_theme_color.php',
                data: {theme:theme},
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#ajax_loader').show();
                },
                success: function (res) {
                    $('#ajax_loader').hide();
                    if (res.status == 'fail') {
                        if (res.error != ""){
                            //alert(res.error);
                        }
                    } else {
                        setNotifySuccess(res.message);
                        parent.$.colorbox.close()
                        parent.window.location.reload();
                    }
                }
            });
        });
        
        $('input:radio[name=theme_color]').click(function(){
            $('label').removeClass('active');
            $(this).closest('label').addClass('active');
            var value = $(this).val();
            var image_name;
            if(value == 'skin-default'){
                image_name = "<?=$GROUP_HOST?>/images/theme/default-theme.png<?=$cache?>";
            } 
            if(value == 'skin-dark'){
                image_name = "<?=$GROUP_HOST?>/images/theme/dark-theme.png<?=$cache?>";
            }
            if(value == 'skin-light'){
                image_name = "<?=$GROUP_HOST?>/images/theme/light-theme.png<?=$cache?>";
            }  
             $('#theme-img').attr('src', image_name);
        });
        
        <?php
        $currentImage = '';
        if ($contract_business_image != "" && file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image)) {
            $currentImage = $GROUPS_BRAND_ICON_WEB . $contract_business_image;
        }
        ?>

        console.log('<?=$currentImage?>');
        $("#enrollment_profile .profile-dropzone").attr('style', 'background:url(<?php echo $currentImage;?>) no-repeat scroll center center /100% 100%;border-radius:0;height:100px;');
        
        // DROPZONE CODE START
        Dropzone.autoDiscover = false;
        var remaingContractDropzone = new Dropzone("#enrollment_profile .profile-dropzone", {
            // The configuration we've talked about above
            url: "#",
            autoProcessQueue: false,
            uploadMultiple: false,
            addRemoveLinks: false,
            parallelUploads: 1,
            thumbnailWidth: null,
            thumbnailHeight: null,
            maxFiles: 1,
            maxFilesize: 2,
            // createImageThumbnails:false,
            acceptedFiles: '.jpg, .gif, .png, .jpeg',
            //previewsContainer: "#imagePreviewEvent",
            dictDefaultMessage: '',
            dictInvalidFileType: 'Please upload .jpg, .gif,.png ,.jpeg files type only',
            dictFileTooBig: 'The maximum image size to upload is {{maxFilesize}} MiB and this image is {{filesize}} MiB Please resize your image and upload it again or try different image.',
            customErrorHandlingCode: 0,
            // The setting up of the dropzone

            init: function () {
                var remaingContractDropzone = this;
                this.on("addedfile", function (file) {
                    if (this.files.length > 1) {
                        this.files = this.files.slice(1, 2);
                    }

                    //ajax_loader(true);
                    $('#ajax_loader').show();

                    //set starting error none, if any error occuer then we setting up this code to 300 on error event
                    this.options.customErrorHandlingCode = 200;
                    contractcurrentDropzone1 = this;
                });

            }, thumbnail: function (file, dataUrl) {
                if (this.options.customErrorHandlingCode == 200) {
                    $('#contract_profile_image_name').val(file.name);
                    $('#contract_profile_image_size').val(file.size);
                    $('#contract_profile_image_original').val(dataUrl);
                    $('#contract_profile_image_type').val(file.type);
                    $('#contract_profile_image_editor').addClass('ready');

                    $('.cr-slider-wrap').addClass('range-overlay mb15');
                    $("#cropper_dropzone_type").val("profile");
                    $("#profile_image,#enrollment_profile .profile-dropzone").attr('style', 'background:url(' + dataUrl + ') no-repeat;background-size: 100% 100%;border-radius:0;height:100px;');

                    $("#profile_image .dz-message").text(this.options.dictDefaultChangeMessage);
                    //ajax_loader(false);
                    $('#ajax_loader').hide();
                    /* contractOpenCropModal(dataUrl); */
                    var $group_id = '<?=$group_id?>';
                    $.ajax({
                        url: '<?=$GROUP_HOST?>/ajax_update_rep_business_picture.php?id=' + $group_id + '&is_my_profile=Y&personal_branding=Y',
                        data: 'profile_picture=' + dataUrl,
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            $('#ajax_loader').show();
                        },
                        success: function (res) {
                            $('#ajax_loader').hide();
                            if (res.status == 'fail') {
                                if (res.error != ""){
                                    //alert(res.error);
                                }
                            } else {
                                //setNotifySuccess(res.message);
                                $('.pro_link_div').show();
                                $('.dz-remove').remove();
                                $('.dropzone-previews').empty();
                                if (res.url != "") {
                                    $(".sidebar-header").find("img").attr("src", res.url);
                                    $("#img_powered_by_logo").find("img").attr("src", res.url);
                                    $(".mw55").attr("src", res.url);
                                    parent.window.location.reload();
                                }
                            }
                        }
                    });
                    $('#enrollment_profile .dz-preview').remove();
                    $('#enrollment_profile .dz-details img').attr('src', $('#contract_profile_image_tmp_name').val());
                }
            },
            error: function (e, error_msg) {
                this.options.customErrorHandlingCode = 300;
                $("#profile_image .dz-message").text(this.options.dictDefaultMessage);
                if (error_msg.search('The maximum image size to upload is') != -1) {
                    error_type = "Oops, this image is too big!";
                } else {
                    error_type = "Oops, something went wrong!";
                }
                swal(error_type, error_msg);
                //ajax_loader(false);
                $('#ajax_loader').hide();
                remaingContractDropzone.removeAllFiles(true);
                $(".profile-dropzone .dz-preview.dz-file-preview").remove();
            },
            removedfile: function () {
                //when remove file then setting up error to default 200
                this.options.customErrorHandlingCode = 0;

                $('#contract_profile_image_name').val('');
                $('#contract_profile_image_size').val('');
                $('#contract_profile_image_tmp_name').val('');
                $('#contract_profile_image_type').val('');
                $('#pro_image').attr('src', "");
                $('#pro_image').cropper("destroy");
                var _ref;
            }
        });

        $(document).off('click','.btn_upload_logo');
        $(document).on('click','.btn_upload_logo', function (e) {
            $("#enrollment_profile .dz-clickable").click();
        });

        $(document).off('click', '.btn_save');
        $(document).on('click', '.btn_save', function (e) {
            $(".btn_save").prop('disabled',true);
            formHandler($("#form_personal_branding"),
            function () {
                $("#ajax_loader").show();
            },
            function (data) {
                $("#ajax_loader").hide();
                $(".btn_save").prop('disabled',false);
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
    function delete_business_image() {
        if (confirm('Are you sure you want to delete business logo?')) {
            $.ajax({
                url: '<?=$GROUP_HOST?>/delete_business_image.php?id=<?=$group_id?>&personal_branding=Y',
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $('#ajax_loader').show();
                },
                success: function (res) {
                    if (res.status == false) {
                        $('#ajax_loader').hide();
                        setNotifyError(res.message);
                        
                    } else {
                        parent.window.location.reload();
                        var default_business_image = '';
                        $("#enrollment_profile .profile-dropzone").attr('style', 'background:url(' + default_business_image + ') no-repeat scroll center center /100% 100%;height:100px;');
                        $(".pro_link_div").hide();
                        $('#enrollment_profile .dropzone-previews').empty();
                        //setNotifySuccess(res.message);
                    }
                }
            });
        }
    }
</script>
<form action="" name="personal_brand_frm" id="personal_brand_frm" method="POST">
    <input type="hidden" name="group_id" id="group_id" value="<?=$group_row['id']?>">
    <input type="hidden" name="group_id_" id="group_id_" value="<?=$group_row['_id']?>">
    
    <p class="agp_md_title">Branding and Vanity URL <i class="fa fa-info-circle text-info" aria-hidden="true"></i></p>
    <p class="fw500 m-b-20">
        This unique url allows your members to self enroll quickly and easily without having to login to your group portal. Please provide your custom vanity url.
    </p>
    <div class="theme-form">
     <div class="row">
       <div class="col-sm-12">
         <div class="form-group height_auto">
           <label class="mn label-input"><input type="checkbox" name="display_in_member" value="Y" <?= $display_in_member == 'Y' ? 'checked' : '' ?>>Check this box if you do not wish to display your name, email, and phone as a point of contact inside the member portal.</label>
         </div>
       </div>
       <div class="col-sm-6">
         <div class="form-group">
            <input name="admin_name" id="admin_name" type="text" class="form-control <?= !empty($public_name) ? 'has-value' : '' ?>"  value="<?= $public_name ?>">
            <label>Display Name<em>*</em></label>
            <p class="error" id="error_admin_name"></p>
         </div>
       </div>
       <div class="col-sm-6">
         <div class="form-group">
            <input id='admin_phone' name="admin_phone" type="text" class="form-control phone_mask <?= !empty($public_phone) ? 'has-value' : '' ?>"  value="<?= $public_phone ?>">
            <label>Display Phone<em>*</em></label>
            <p class="error" id="error_admin_phone"></p>
         </div>
       </div>
       <div class="col-sm-6">
         <div class="form-group">
            <input id='admin_emails' name="admin_email" type="text" class="form-control no_space <?= !empty($public_email) ? 'has-value' : '' ?>"  value="<?= $public_email ?>">
            <label>Display Email<em>*</em></label>
            <p class="error" id="error_admin_email"></p>
         </div>
       </div>
       <div class="col-sm-6">
         <div class="form-group">
           <div class="input-group">
            <span class="input-group-addon"><?= $DEFAULT_SITE_URL ?>/</span>
            <input  type="text" class="form-control <?= !empty($username) ? 'has-value' : '' ?>"  placeholder="<?= $DEFAULT_SITE_URL ?>/company001" name="username" id="username" class="form-control" value="<?= $username ?>" readonly>
            <div id="username_info" class="pswd_popup" style="display: none">
              <div class="pswd_popup_inner">
                 <h4>URL Requirements</h4>
                 <ul style="list-style:none; padding-left:10px;">
                   <li id="ulength" class="invalid"><em></em>Be between 4-20 characters</li>
                   <li id="alpha" class="invalid"><em></em>Contain no spaces or special characters</li>
                   <li id="unique" class="invalid"><em></em>Unique URL</li>
                 </ul>
                 <div class="btarrow"></div>
              </div>
            </div>
          </div>
          <p class="error" id="error_username"></p>
         </div>
       </div>
     </div>
    </div>
    <!-- <p class="agp_md_title">Personalized Website(s)</p>
     <div class="table-responsive m-b-20">
        <table class="<?=$table_class?>">
            <thead>
                <tr>
                    <th>Added Date</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th width="120px">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resWebsite)) { ?>
                    <?php foreach ($resWebsite as $key => $rows) { ?>
                    <tr>
                        <td><?=date('m/d/Y', strtotime($rows['created_at']));?></td>
                        <td><?=!empty($rows['page_name'])?$rows['page_name']:"N/A";?></td>
                        <td>
                            <?php if(!empty($rows['user_name'])){ ?>
                                <a href="<?=$GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$rows['user_name']?>" class="text-action fw500" target="_blank"><?=$GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$rows['user_name']?></a>
                            <?php } else { echo "N/A"; } ?>
                        </td>
                        <td>
                            <?php
                                if($rows['status'] == "Draft" || $rows['status'] == "") {
                                    echo "Draft";

                                } elseif($rows['status'] == "Active" || $rows['status'] == "Inactive") {
                                ?>
                                <select name="is_published" class="form-control is_published" id="website_status_<?=$rows['id'];?>">
                                    <option value="Active" <?= ($rows['status'] == 'Active') ? "selected='selected'" : ""     ?>>Published </option>
                                    <option value="Inactive" <?= ($rows['status'] == 'Inactive') ? "selected='selected'" : ""     ?>>Unpublished</option>
                                </select>
                                <?php
                                } else {
                                    echo $rows['status'];
                                }
                            ?>
                        </td>
                        <td class="icons">
                            <a href="<?=$GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$rows['user_name']?>" target="_blank"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="5" align="center">No record(s) found</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
     </div>
    <p class="agp_md_title">Branding Customization</p>
      <div class="form-group height_auto">
        <label class="mn"><input type="checkbox" name="is_branding" <?=$is_branding == 'Y' ? 'checked' : ''?> value="Y">
            Check this box to allow personal branding of group portal</label>
      </div>
    <p class="m-b-20 fw600">Logo</p>
    <p class="fw500 m-b-20">Click the box below to upload your customized branding logo.</p>
     <div class="row">
       <div class="col-sm-6">
         <div class="agent_drop_div pro_drop_div m-b-20" id="enrollment_profile">
            <input type="hidden" id="contract_profile_image_size" name="profile_image[size]" value="" />
            <input type="hidden" id="contract_profile_image_name" name="profile_image[name]" value="" />
            <input type="hidden" id="contract_profile_image_type" name="profile_image[type]" value="" />
            <input type="hidden" id="contract_profile_image_tmp_name" name="profile_image[tmp_name]" value="" />
              <div class="dropzone profile-dropzone">
                  <div class="dropzone-previews" >
                  </div>
              </div>
          </div> -->
          <!-- <?php
            $tmp_style = 'display: none;';
            if (file_exists($GROUPS_BRAND_ICON_DIR . $contract_business_image) && $contract_business_image != "") {
                $tmp_style = 'display: block;';
            } ?>
          <div class="text-right pro_link_div m-t-15" style="<?=$tmp_style;?>"> -->
              <!-- <a href="javascript:void(0);" class="btn btn-info">Upload</a> -->
              <!-- <a href="javascript:void(0);" onclick="return delete_brand_icon();" class="btn red-link">Remove</a>
          </div>
       </div>
     </div> -->
    <div class="clearfix text-center">
       <a href="javascript:void(0);" class="btn btn-action" id="personal_brand_link_save">Save</a>
    </div>
    <!-- <hr> -->
            
    
</form>
<script src="<?=$HOST?>/js/password_validation.js<?=$cache?>" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        checkEmail();
        $(".phone_mask").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $(".ein_mask").inputmask({"mask": "99-9999999",'showMaskOnHover': false});

        $("#exit_business_img").click(function () {
              $("#enrollment_profile .profile-dropzone").click();
           });

           $("#enrollment_profile .profile-dropzone").attr('style', 'background:url(<?php echo $currentImage;?>) no-repeat scroll center center /100% 100%;border-radius:0;height:100px;');
          // DROPZONE CODE START
             // Dropzone.autoDiscover = false;
             // var remaingContractDropzone = new Dropzone("#enrollment_profile .profile-dropzone", {
             //     // The configuration we've talked about above
             //     url: "#",
             //     autoProcessQueue: false,
             //     uploadMultiple: false,
             //     addRemoveLinks: false,
             //     parallelUploads: 1,
             //     thumbnailWidth: null,
             //     thumbnailHeight: null,
             //     maxFiles: 1,
             //     maxFilesize: 2,
             //     // createImageThumbnails:false,
             //     acceptedFiles: '.jpg, .gif, .png, .jpeg',
             //     //previewsContainer: "#imagePreviewEvent",
             //     dictDefaultMessage: '',
             //     dictInvalidFileType: 'Please upload .jpg, .gif,.png ,.jpeg files type only',
             //     dictFileTooBig: 'The maximum image size to upload is {{maxFilesize}} MiB and this image is {{filesize}} MiB Please resize your image and upload it again or try different image.',
             //     customErrorHandlingCode: 0,
             //     // The setting up of the dropzone

             //     init: function () {
             //         var remaingContractDropzone = this;
             //         this.on("addedfile", function (file) {
             //           if (this.files.length > 1) {
             //             this.files = this.files.slice(1, 2);
             //           }
             //             //ajax_loader(true);
             //             $('#ajax_loader').show();

             //             //set starting error none, if any error occuer then we setting up this code to 300 on error event
             //             this.options.customErrorHandlingCode = 200;
             //             contractcurrentDropzone1 = this;
             //         });

             //     }, thumbnail: function (file, dataUrl) {
             //         if (this.options.customErrorHandlingCode == 200) {
             //             $('#contract_profile_image_name').val(file.name);
             //             $('#contract_profile_image_size').val(file.size);
             //             $('#contract_profile_image_original').val(dataUrl);
             //             $('#contract_profile_image_type').val(file.type);
             //             $('#contract_profile_image_editor').addClass('ready');

             //             $('.cr-slider-wrap').addClass('range-overlay mb15');
             //             $("#cropper_dropzone_type").val("profile");
             //             $("#profile_image,#enrollment_profile .profile-dropzone").attr('style', 'background:url(' + dataUrl + ') no-repeat;background-size: 100% 100%;border-radius:0;height:100px;');

             //             $("#profile_image .dz-message").text(this.options.dictDefaultChangeMessage);
             //             //ajax_loader(false);
             //             $('#ajax_loader').hide();
             //             /* contractOpenCropModal(dataUrl); */
             //             $group_id = $("#group_id_").val();
             //             $.ajax({
             //                 url: '<?=$ADMIN_HOST?>/ajax_update_group_business_picture.php?id=' + $group_id + '',
             //                 data: 'profile_picture=' + dataUrl,
             //                 type: 'POST',
             //                 dataType: 'json',
             //                 beforeSend:function(){
             //                   $('#ajax_loader').show();
             //                 },
             //                 success: function (res) {
             //                   $('#ajax_loader').hide();
             //                     if (res.status == 'fail') {
             //                         if (res.error != "")
             //                             alert(res.error);
             //                     } else {
             //                         setNotifySuccess(res.message);
             //                         $('.pro_link_div').show();
             //                         $('.dz-remove').remove();
             //                         $('.dropzone-previews').empty();
             //                         if (res.url != "") {
             //                             $(".sidebar-header").find("img").attr("src", res.url);
             //                             $(".mw55").attr("src", res.url);
             //                         }
             //                     }
             //                 }
             //             });
             //             $('#enrollment_profile .dz-preview').remove();
             //             $('#enrollment_profile .dz-details img').attr('src', $('#contract_profile_image_tmp_name').val());
             //         }
             //     },
             //     error: function (e, error_msg) {
             //         this.options.customErrorHandlingCode = 300;
             //         $("#profile_image .dz-message").text(this.options.dictDefaultMessage);
             //         if (error_msg.search('The maximum image size to upload is') != -1){
             //             error_type = "Oops, this image is too big!";
             //         } else {
             //             error_type = "Oops, something went wrong!";
             //         }
             //         swal(error_type, error_msg);
             //                 //ajax_loader(false);
             //         $('#ajax_loader').hide();
             //         remaingContractDropzone.removeAllFiles(true);
             //         $(".profile-dropzone .dz-preview.dz-file-preview").remove();
             //     },
             //     removedfile: function () {
             //         //when remove file then setting up error to default 200
             //         this.options.customErrorHandlingCode = 0;

             //         $('#contract_profile_image_name').val('');
             //         $('#contract_profile_image_size').val('');
             //         $('#contract_profile_image_tmp_name').val('');
             //         $('#contract_profile_image_type').val('');
             //         //$('#main_profile_image').attr('src', "images/default_profile_image.png");
             //         $('#pro_image').attr('src', "");
             //         $('#pro_image').cropper("destroy");
             //         var _ref;
             //         // return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;

             //     }
             // });
          // DROPZONE CODE END
    });

    $(document).on('focusin click keyup', '#username', function() {
        $('#username_info').show();
        var user_name = $(this).val();
        var user_email = $('#email').val();
        var group_id = $("#group_id_").val();
        var pattern = new RegExp('^[0-9a-zA-Z]+$');
        if (user_name.match(pattern)) {
            $('#alpha').removeClass('invalid').addClass('valid');
        } else {
            $('#alpha').removeClass('valid').addClass('invalid');
        }
        if (user_name.length < 4 || user_name.length > 20) {
            $('#ulength').removeClass('valid').addClass('invalid');
        } else {
            $('#ulength').removeClass('invalid').addClass('valid');
        }
        if (user_name.match(pattern) && user_name.length > 3 && user_name.length < 21) {
            $.ajax({
                url: "<?= $ADMIN_HOST ?>/check_already_username.php",
                data: {
                    username: user_name,
                    email: user_email,
                    group_id: group_id
                },
                dataType: 'json',
                type: 'post',
                success: function(res) {
                    if (!res) {
                        $('#unique').removeClass('valid').addClass('invalid');
                    } else if (res) {
                        $('#unique').removeClass('invalid').addClass('valid');
                    }
                }
            });
        } else {
            $('#unique').removeClass('valid').addClass('invalid');
        }
    }).on('blur', '#username', function() {
        id = $(this).attr('id');
        $('#username_info').hide();
        if ($.trim($(this).val()) == "") {
            $('#error_' + id).html('Web Alias is required');
            $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
            $("#" + id + '_err').addClass('wrongmark_red');
            return false;
        } else {
            if ($(this).val().length < 4) {
                $('#error_' + id).html('Minimum 4 chracter(s) required');
                $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
                $("#" + id + '_err').addClass('wrongmark_red');
                return false;
            } else if ($(this).val().length > 20) {
                $('#error_' + id).html('Maximum 20 chracter(s) allow');
                $("#" + id + '_err').removeClass('rightmark wrongmark wrongmark_red');
                $("#" + id + '_err').addClass('wrongmark_red');
                return false;
            } else {
                $('#error_' + id).html('');
                //checkUsername($(this).val(), $('#email').val(), id);
                $("#pws_username_url").html($(this).val());
            }
        }
    });
    function delete_brand_icon() {
       if (confirm('Are you sure you want to delete logo?')) {
            $group_id = $("#group_id_").val();
           $.ajax({
               url: '<?= $ADMIN_HOST ?>/ajax_delete_brand_icon.php?id='+$group_id,
               type: 'POST',
               dataType: 'json',
               beforeSend: function () {
                 $('#ajax_loader').show();
               },
               success: function (res) {
                 $('#ajax_loader').hide();
                   if (res.status == false) {
                       setNotifyError(res.message);
                   } else {
                       var default_business_image = '';
                       $("#enrollment_profile .profile-dropzone").attr('style', 'background:url('+default_business_image+') no-repeat scroll center center /100% 100%;height:100px;');
                       $(".pro_link_div").hide();
                       $('#enrollment_profile .dropzone-previews').empty();
                       setNotifySuccess(res.message);
                   }
               }
           });
       }
   }
    $(document).off('change', '.is_published');
    $(document).on("change", ".is_published", function(e) {
        e.stopPropagation();
        $group_id = $("#group_id_").val();
        var id = $(this).attr('id').replace('website_status_', '');
        var publish_status = $(this).val();
        swal({
            text: "Change Published Status: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function() {
            $.ajax({
                url: 'ajax_group_personalize_website_status.php',
                data: {
                    id: id,
                    status: publish_status,
                    operation:'change_publish_status',
                    group_id : $group_id,
                },
                method: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status == "success") {
                        setNotifySuccess(res.msg);
                    }else{
                        setNotifyError(res.msg);
                    }
                }
            });
        }, function(dismiss) {
        });
    });
    
    $(document).off('click','#personal_brand_link_save');
    $(document).on('click','#personal_brand_link_save',function(e){
        $("#ajax_loader").show();
        $(".error").html('');
        $.ajax({
            url:'ajax_update_group_personal_brand_link.php',
            dataType:'JSON',
            data:$("#personal_brand_frm").serialize(),
            type:'POST',
            success:function(data){
                $("#ajax_loader").hide();
                if (data.status == 'success') {
                    setNotifySuccess("Group detail updated successfully!");
                } else if (data.status == "fail") {
                    setNotifyError("Oops... Something went wrong please try again later");
                } else {
                    $.each(data.errors, function(key, value) {
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
            }
        });
    });
</script>
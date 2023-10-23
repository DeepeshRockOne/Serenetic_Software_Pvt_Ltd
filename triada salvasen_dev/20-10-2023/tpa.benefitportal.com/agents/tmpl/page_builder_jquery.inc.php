<script type="text/javascript">
    isValidEmailAddress = function (emailAddress) {
        return /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i.test(emailAddress);
    };
    getOnlyNumber = function ($str) {
        if (typeof ($str) === "undefined") {
            return "";
        }
        var num = $str.match(/[\d\.]+/g);
        if (num != null) {
            $mergeNumber = "";
            for (i = 0; i < num.length; i++) {
                $mergeNumber += num[i];
            }
            return $mergeNumber;
        } else {
            return "";
        }
    };
    isValidURL = function (url) {
        return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    };
    removeErrorById = function ($element) {
        $("#error_" + $element).empty();
        allValidation();
    };
    
    trashQuestion = function (action, title, text, btnText) {
       swal({
            text: "<br/>Once you delete this image it will be gone for good!",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(action, function (dismiss) {

        });
    };
    /* preview switch toggle js start */
    resizeIframe = function ($height) {
        $("#prd_preview_iframe")[0].style.height = $height + 'px';
        $("#ajax_loader").hide();
        setupHeight();
    };

    refreshPreview = function () {
        $("#prd_preview_iframe").attr("src", "<?=$HOST?>/prd_preview.php?page_builder_id=<?=md5($page_builder_id)?>").show();
    };
    previewProcessed = function () {
        $(".center_device").show();
        $("#data_action").val('preview');
        formHandler($("#page_builder"), function () {
            $(".custom_loader").show();
        }, function (data) {
            $(".custom_loader").hide();
            refreshPreview();
        });
    };

    trigger("#prd_preview_btn", function ($this) {
        if ($this.prop("checked")) {
            previewProcessed();
        } else {
            $(".center_device").hide();
            $("#wrapper > .navbar.navbar-default, .sidebar, .sttabs > nav, #page-wrapper .row.bg-title, footer.footer, .org_updated_main").show();
            $("body").removeClass("prd_preview");
            $("#page-wrapper").removeClass("mn");
            $("#page-wrapper, .content-current, #page-wrapper > .container-fluid").removeClass("pn");
            $("#prd_preview_iframe").hide();
        }
    }, "change");


    // Start: Switchery
    /*$('.js-switch').each(function() {
      new Switchery($(this)[0], $(this).data());
    });*/
    // End: Switchery
    /* preview switch toggle js end */

    $(document).on('keyup', '#header_content', function (e) {
        var chars = $("#header_content").val().length;
        if (<?=HEADER_CONTENT_LIMIT?> -chars <= 0) {
            $("#header_content_count").parent("span").addClass("text-danger");
        } else {
            $("#header_content_count").parent("span").removeClass("text-danger");
        }
        $("#header_content_count").text(<?=HEADER_CONTENT_LIMIT?> -chars);
    });

    /*$(document).on('keyup', '#lead_content', function(e) {
      var chars = $("#lead_content").val().length;
      if (100 - chars <= 0) {
        $("#lead_content_count").parent("span").addClass("text-danger");
      } else {
        $("#lead_content_count").parent("span").removeClass("text-danger");
      }
      $("#lead_content_count").text(100 - chars);
    });*/

    $(document).on('keyup', '#header_subcontent', function (e) {
        var chars = $("#header_subcontent").val().length;
        if (<?=SUBHEADER_CONTENT_LIMIT?> -chars <= 0) {
            $("#header_subcontent_count").parent("span").addClass("text-danger");
        } else {
            $("#header_subcontent_count").parent("span").removeClass("text-danger");
        }
        $("#header_subcontent_count").text(<?=SUBHEADER_CONTENT_LIMIT?> -chars);
    });


    $(function () {
        $("#contact_us_phone_number").mask("(999) 999-9999");

        /*$("#header_content,#header_subcontent").summernote({
          width: "100%",
          height: '125px',
          toolbar: [
            ["style", ["style"]],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["fontname", ["fontname"]],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
          ],
          callbacks: {
            onKeyup: function(e) {
              $attrName=$(this).attr("name");
              $attrName!=""&&removeErrorById($attrName);
            }
          }
        });*/
        trigger("#header_content,#header_subcontent", function ($this, e) {
            $attrName = $this.attr("name");
            $attrName != "" && removeErrorById($attrName);
        }, "change");
        $("#product_types").multipleSelect({
            width: "100%",
            placeholder: "Select Product Type",
            onCheckAll: function () {
                $.uniform.update();
                previewProcessed();
            },
            onUncheckAll: function () {
                $.uniform.update();
                previewProcessed();
            },
            onOptgroupClick: function (view) {
                $.uniform.update();
                previewProcessed();
            },
            onClick: function (view) {
                $.uniform.update();
                previewProcessed();
            }
        });

        $(document).off('click','.btn_cancel');
        $(document).on('click','.btn_cancel',function(){
            swal({
                text: "Cancel Changes: Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Confirm"
            }).then(function(){
                window.location.href = 'manage_website.php';
            }, function (dismiss) {
                
            });
        });

        $("#category_ids").multipleSelect({
            selectAll: false,
            filter: true,
            onClick: function (view) {
                if(view.selected) {
                    $(".category_section_"+view.value).show();
                    $(".category_section_"+view.value +" input[type='checkbox']").each(function(index,element){
                        $(this).prop('checked',true);
                    });
                } else {
                    $(".category_section_"+view.value).hide();
                    $(".category_section_"+view.value +" input[type='checkbox']").each(function(index,element){
                        $(this).prop('checked',false);
                    });
                }
                $(".category_section_"+view.value +" input[type='checkbox']").uniform();
                previewProcessed();
            }
        });

        $(document).off('click','.multiple_selection_choice_remove');
        $(document).on('click','.multiple_selection_choice_remove',function(){
            var category_id = $(this).attr('data-title');
            $(".category_section_"+category_id).hide();
            $(".category_section_"+category_id +" input[type='checkbox']").each(function(index,element){
                $(this).prop('checked',false);
            });
            $(".category_section_"+category_id +" input[type='checkbox']").uniform();
            previewProcessed();
        });

        trigger(".coverCropAction", function () {
            if ($("#cover_image_tmp_name").val() == "") {
                $("#error_cover_image").html("Please select background image.");
            } else {
                $("#dropzone_type").val("cover");
                openPageCropModal($('#cover_image_tmp_name').val());
            }
        });
        trigger(".coverTrashAction", function () {
            if ($("#cover_image_tmp_name").val() == "") {
                $("#error_cover_image").html("Please select background image.");
            } else {
                trashQuestion(function () {
                    coverDropzone.removeAllFiles();
                });
            }
        });
        //dropzone for cover images start
        Dropzone.autoDiscover = false;

        var maxImageWidth = 850, maxImageHeight = 148;
        coverDropzone = new Dropzone("#custom_bg_url", {
            // The configuration we've talked about above
            url: "#",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: false,
            parallelUploads: 1,
            thumbnailWidth: null,
            thumbnailHeight: null,
            maxFiles: 2,
            maxFilesize: 4,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            dictDefaultMessage: '+',
            dictInvalidFileType: 'Please upload .jpg, .gif,.png ,.jpeg files type only',
            dictFileTooBig: 'The maximum image size to upload is {{maxFilesize}} MiB and this image is {{filesize}} MiB Please resize your image and upload it again or try different image.',
            customErrorHandlingCode: 0,
            uploadProgress: function (progress) {
                document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
            },
            // The setting up of the dropzone
            init: function () {
                this.on("addedfile", function (file) {
                    if (this.files.length > 1) {
                        this.files = this.files.slice(1, 2);
                    }
                    this.options.customErrorHandlingCode = 200;
                    file.previewElement.addEventListener("click", function () {
                        $("#dropzone_type").val("cover");
                        openPageCropModal($('#cover_image_tmp_name').val());
                    });
                });
            },
            thumbnail: function (file, dataUrl) {
                if (!isset(file.mock)) {
                    var data = getPngDimensions(dataUrl);
                    if (data.width <= 400 || data.height <= 150) {
                        this.options.customErrorHandlingCode = 300;
                        swal({
                            title: 'Check Image Size',
                            text: "Minimum required image is 400pxs wide by 150pxs tall, please upload a photo with larger dimensions",
                            type: 'error',
                            confirmButtonText: 'OK'
                        });

                        coverDropzone.removeAllFiles();
                        $('#custom_bg_url').attr('style', '');
                        $('#custom_bg_url .dz-message').show();
                        addPanelErrorByInnerId('#error_cover_image')
                    } else {
                        this.options.customErrorHandlingCode == 200;
                    }
                }
                removeErrorById("cover_image");
                if (this.options.customErrorHandlingCode == 200) {
                    if (!isset(file.mock)) {
                        uos(dataUrl, function (data) {
                            $('#custom_bg_url .dz-filename').hide();
                            $('#cover_image_name').val(file.name);
                            $('#cover_image_size').val(file.size);
                            $('#cover_image_tmp_name').val(dataUrl);
                            $('#cover_image_type').val(file.type);
                            $('#cover_image_editor').addClass('ready');
                            $("#dropzone_type").val("cover");
                            $("#cover_image_mock").val("N");
                            openPageCropModal(data.imagePath);
                            allValidation();
                        });
                    }
                }
            },
            error: function (e, error_msg) {
                this.options.customErrorHandlingCode = 300;
                if (error_msg.search('The maximum image size to upload is') != -1) {
                    error_type = "Oops, this image is too big!";
                } else {
                    error_type = "Oops, something went wrong!";
                }
                swal(error_type, error_msg);
                coverDropzone.removeAllFiles();
                $("#is_back_ground_image").val('N');
                $("#background_action_image").val('');
            },
            removedfile: function (file) {
                this.options.customErrorHandlingCode = 0;
                $('#custom_bg_url .dz-message').show();
                $('#cover_image_name').val('');
                $('#cover_image_size').val('');
                $('#cover_image_tmp_name').val('');
                $('#cover_image_type').val('');
                $('#custom_bg_url').attr('style', '');
                allValidation();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }
        });
        // dropzone for cover images end

        trigger(".coverChangeAction", function ($this, e) {
            $("#custom_bg_url").trigger("click");
        });

        //Save Form Code Start
        $tmp_image = "";
        trigger("#page_builder", function ($this, e) {
            
            $("#cover_image_size").val("");
            $("#cover_image_name").val("");
            $("#cover_image_type").val("");
            $("#cover_image_tmp_name").val("");
            $("#cover_image_mock").val("");
            e.preventDefault();
            $("#step").val($(".data_tab li.active").index() + 1);
            formHandler($("#page_builder"), function () {
            }, function (data) {
                $(".error").html("");
                refreshPreview();
                if ($tmp_image != "") {
                    $("#cover_image_tmp_name").val($tmp_image);
                    $tmp_image = "";
                }
                removeLoader($(".update_page_builder,.next_page_builder"));
                

                ajax_loader(false);
                $("#ajax_loader").hide();
                if (data.status == 'fail') {
                    if (data.div_step_error.length) {
                        if (!$('#' + data.div_step_error).is(":visible")) {
                            $("[href='#" + data.div_step_error + "']").click();
                        }
                    }

                    var firstError = true;
                    $.each(data.errors, function (index, error) {
                        $(".error_" + index).html(error).show();
                        if (firstError && $('.error_' + index).length > 0) {
                            firstError = false;
                            $('html, body').animate({
                                scrollTop: $('.error_' + index).offset().top - 100
                            }, 1000);
                        }
                    });
                } else if (data.status == 'error') {
                    error(data.msg);
                } else {
                    if (isset(data.logo)) {
                        $(".logo_preview").show().attr("href", data.logo);
                        $("#preview_exists").val("Y");
                    }
                    if (data.data_action == "draft") {
                        swal({
                            text: '<br/>' + data.msg,
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then(function () {
                            window.location.href = 'manage_website.php';
                        }, function (dismiss) {
                            window.location.href = 'manage_website.php';
                        });
                    } else if (data.data_action == "next") {
                        $(".data_tab li.active").addClass('completed');
                        $(".data_tab li.active").next().find("a").trigger("click");
                        $('html, body').animate({
                            scrollTop: $('.data_tab').offset().top - 100
                        }, 1000);
                    } else {
                        $(".error").empty();
                        swal({
                            text: '<br/>' + data.msg,
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then(function () {
                            window.location.href = 'manage_website.php';
                        }, function (dismiss) {
                            window.location.href = 'manage_website.php';
                        });
                    }
                }
                checkTab();
            });
            return false;
        }, "submit");

        checkTab = function () {
            var active_href = $(".data_tab li.active a").attr("href");
            if (active_href == "#settings_panel") {
                $(".update_page_builder").show();
                $(".next_page_builder").hide();
            } else {
                $(".update_page_builder").hide();
                $(".next_page_builder").show();
            }

            if (active_href == "#top_fold_panel") {
                $(".background_image_section").show();
            } else {
                $(".background_image_section").hide();
            }
            $("#current_step").html(($(".data_tab li.active").index() + 1));
        };

        $('.data_tab a').on('shown.bs.tab', function (event) {
            checkTab();
        });

        uploadImage = function () {
            formHandler($("#page_builder_file_upload"), function () {
            }, function (data) {
                $(".error").html("");
                refreshPreview();
                removeLoader($(".update_page_builder"));

                $("#ajax_loader").hide();
                if (data.status == 'success') {
                    destroySlider();
                    $html = '<div class="item" data-id="' + data.image_id + '"><input type="radio" name="pb_img" id="pb_img_' + data.image_id + '" class="js-switch" value="' + data.image_id + '" /><label for="pb_img_' + data.image_id + '" style="background-image: url(<?=$PAGE_COVER_WEB?>/' + data.cover_image + ');"></label><a href="javascript:void(0);" class="pb_img_delete" data-container="body" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></a></div>';
                    $("#upload_img_slider").prepend($html);
                    initSlider();
                    $("#pb_img_" + data.image_id).trigger("click");
                    previewProcessed();
                } else {
                    $(".circle-tab").find(".valid_tab i").removeClass("fa-times").addClass("fa-check");
                    if (data.div_step_error.length) {
                        if (!$('#' + data.div_step_error).is(":visible"))
                            $("[href='#" + data.div_step_error + "']").click();
                    }

                    var firstError = true;
                    $.each(data.errors, function (index, error) {
                        $(".error_" + index).html(error).show();
                        if (firstError && $('.error_' + index).length > 0) {
                            firstError = false;
                            $('html, body').animate({
                                scrollTop: $('.error_' + index).offset().top - 100
                            }, 1000);
                        }
                    });
                }
            });
        };
        trigger("[name='pb_img']", function () {
            previewProcessed();
        });
        //save and publish
        trigger(".update_page_builder", function ($this, e) {
            addLoader($(".update_page_builder"));
            $("#ajax_loader").show();
            savePageBuilder("update");
        });
        //save and next
        trigger(".next_page_builder", function ($this, e) {
            addLoader($(".next_page_builder"));
            $("#ajax_loader").show();
            savePageBuilder("next");
        });

        //save as draft
        trigger(".update_page_draft", function () {
            addLoader($(".update_page_draft"));
            $("#ajax_loader").show();
            savePageBuilder("draft");
        });
        //Save Form Code End
    });


    savePageBuilder = function ($t) {
        $("#data_action").val($t);
        $('#page_builder').submit();
    };

    //Add Group Contact Code Start

    error = function (msg) {
        swal(
            'Oops..',
            msg,
            'error'
        );
    };
    success = function (msg) {
        swal(
            'Congratulations!',
            msg,
            'success'
        );
    };

    //Cropper Code Start
    function openPageCropModal(dataUrl) {
        $("#page-crop-modal").modal("show").on('shown.bs.modal', function () {
            $("#page-crop-modal").attr("data-modalType", $("#dropzone_type").val());
            $("#cropper_image #page_image").attr('src', dataUrl);
            setTimeout(initPageCropper, 600);
            if ($("#dropzone_type").val() == 'cover') {
                $(".modal-title").html('Background Image');
            }            
        }).on('hidden.bs.modal', function () {
            $(".modal-title").html('');
            $coverImage.cropper("destroy");

        });
    }

    function initPageCropper() {
        $coverImage = $('#cropper_image #page_image');
        $coverImage.cropper({
            viewMode: 1,
            dragMode: 'move',
            aspectRatio: 16 / 9,
            restore: false,
            guides: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            zoomOnWheel: true,
            zoomOnTouch: true,
        });

        // Buttons
        if (!$.isFunction(document.createElement('canvas').getContext)) {
            $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
        }

        if (typeof document.createElement('cropper').style.transition === 'undefined') {
            $('button[data-method="rotate"]').prop('disabled', true);
            $('button[data-method="scale"]').prop('disabled', true);
        }
    }

    // Methods
    flipPosX = 1;
    flipPosY = 1;
    trigger("#scaleX", function ($this, e) {
        if (flipPosX == 1) {
            flipPosX = -1;
        } else if (flipPosX == -1) {
            flipPosX = 1;
        }
        $coverImage.cropper('scaleX', flipPosX);
    });
    trigger("#scaleY", function ($this, e) {
        if (flipPosY == 1) {
            flipPosY = -1;
        } else if (flipPosY == -1) {
            flipPosY = 1;
        }
        $coverImage.cropper('scaleY', flipPosY);
    });

    trigger(".page-cropper-button [data-method]", function ($this, e) {
        var data = $this.data();
        var $target;
        var result;
        if ($this.prop('disabled') || $this.hasClass('disabled')) {
            return;
        }
        if ($coverImage.data('cropper') && data.method) {
            data = $.extend({}, data); // Clone a new one

            if (typeof data.target !== 'undefined') {
                $target = $(data.target);

                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }

            if (data.method === 'rotate') {
                $coverImage.cropper('clear');
            }

            result = $coverImage.cropper((data.method == "saveCropperImage" ? "getCroppedCanvas" : data.method), data.option, data.secondOption);

            if (data.method === 'rotate') {
                $coverImage.cropper('crop');
            }

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    $(this).data('option', -data.option);
                    break;
                case 'saveCropperImage':
                    if (result) {
                        var drop_type = $("#dropzone_type").val();
                        if (drop_type == "cover") {
                            $("#is_back_ground_image").val('Y');
                            $("#background_action_image").val('');
                            try {
                                $('#cover_image_tmp_name').val(result.toDataURL('image/jpeg'));
                                allValidation();
                                uploadImage();
                                coverDropzone.removeAllFiles()
                            } catch (e) {
                            }
                        }
                    }
                    break;
                case 'getCroppedCanvas':
                    if (result) {
                        $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
                    }
                    break;
            }

            if ($.isPlainObject(result) && $target) {
                try {
                    $target.val(JSON.stringify(result));
                } catch (e) {
                    console.log(e.message);
                }
            }
        }
    });
    trigger("[name='enroll_online']", function ($this, e) {
        if ($this.val() == 'No') {
            $(".call_in_enrollment").fadeIn("slow");
        } else {
            $(".call_in_enrollment").fadeOut("slow");
        }
        previewProcessed();
    }, "change");
    trigger("[name='social_accounts']", function ($this, e) {
        if ($this.val() == 'Yes') {
            $(".social_accounts_links").fadeIn("slow");
        } else {
            $(".social_accounts_links").fadeOut("slow");
        }
        previewProcessed();
    }, "change");

    //Cropper Code End
    $(document).on('focusin click keyup', '#user_name', function () {
        $('#user_name_info').show();
        var user_name = $(this).val();
        var user_email = $('#emails').val();
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
                url: $AGENT_HOST + "/check_already_username.php",
                data: {
                    username: user_name,
                    email: user_email,
                    agent_id: 0,
                    page_builder_id: $("form#page_builder #page_builder_id").val()
                },
                dataType: 'json',
                type: 'post',
                success: function (res) {
                    if (!res) {
                        $('#unique').removeClass('valid').addClass('invalid');
                    } else if (res) {
                        $('#unique').removeClass('invalid').addClass('valid');
                    }
                    allValidation();
                }
            });
        } else {
            $('#unique').removeClass('valid').addClass('invalid');
        }
        allValidation();
    }).on('blur', '#user_name', function () {
        id = $(this).attr('id');
        $('#user_name_info').hide();
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

    $('#logo').checkFileType({
        allowedExtensions: allowed_extesion,
        success: function () {
            $("#preview_exists").val("Y");
            allValidation();
            imagePreview("#logo", "#preview_logo", 50);
            previewProcessed();
        },
        error: function () {
            $el = $('#logo');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
            setNotifyError('Invalid Image! Please select valid image');
        }
    });

    addPanelSuccessByInnerId = function ($element) {
        $tabHrefName = $($element).parents(".tab-pane").attr("id");
        $("[href='#" + $tabHrefName + "']").find(".valid_tab i").removeClass("fa-times").addClass("fa-check")
    };
    addPanelErrorByInnerId = function ($element) {
        $tabHrefName = $($element).parents(".tab-pane").attr("id");
        $("[href='#" + $tabHrefName + "']").find(".valid_tab i").removeClass("fa-check").addClass("fa-times")
    };
    validTab = function ($tabHrefName) {
        $("[href='" + $tabHrefName + "']").find(".valid_tab i").removeClass("fa-times").addClass("fa-check")
    };
    inValidTab = function ($tabHrefName) {
        $("[href='" + $tabHrefName + "']").find(".valid_tab i").removeClass("fa-check").addClass("fa-times");
    };
    //live validation start
    topFoldValidationCheck = function () {
        $error = false;
        if ($("#cover_image_tmp_name").val() == "") {
            $error = true;
        }
        
        if ($('#header_content').val().trim() == "") {
            $error = true;
        }
        
        if ($('#header_subcontent').val().trim() == "") {
            $error = true;
        }
        if ($("#preview_exists").val() == "N") {
            $error = true;
        }
        if ($error) {
            inValidTab("#top_fold_panel");
        } else {
            validTab("#top_fold_panel");
        }
    };
    productsValidationCheck = function () {
        $error = false;
        if ($("#product_types").val() == "" || $("#product_types").val() == null) {
            $error = true;
        }
        if ($("#future_product").val() == "") {
            
        }
        if ($error) {
            inValidTab("#products_panel");
        } else {
            validTab("#products_panel");
        }
    };
    contactUsValidationCheck = function () {
        $error = false;
        if ($("#entity_name").val() == "") {
            $error = true;
        }
        if ($("[name='social_accounts']:checked").length == 0) {
            $error = true;
        }
        if ($("[name='social_accounts']:checked").val() == "Yes") {
            $facebook_link = $("#facebook_link").val();
            $twitter_link = $("#twitter_link").val();
            $linkedin_link = $("#linkedin_link").val();
            if ($facebook_link == "" && $twitter_link == "" && $linkedin_link == "") {
                $error = true;
            }
            if ($facebook_link != "") {
                if (!isValidURL($facebook_link) || 1) {
                    $error = true;
                }
            }
            if ($twitter_link != "") {
                if (!isValidURL($twitter_link) || 1) {
                    $error = true;
                }
            }
            if ($linkedin_link != "") {
                if (!isValidURL($linkedin_link) || 1) {
                    $error = true;
                }
            }
        }
        if (!isValidEmailAddress($("#email").val())) {
            $error = true;
        }
        if ($('#address').val() == "") {
            $error = true;
        } else {
            
        }
        if ($("#phone_number").val() == "") {
            $error = true;
        } else {
            if (getOnlyNumber($("#phone_number").val()).toString().length < 10) {
                $error = true;
            }
        }
        if ($error) {
            inValidTab("#contact_us_panel");
        } else {
            validTab("#contact_us_panel");
        }
    };
    settingsValidationCheck = function () {
        $error = false;
        if ($("#page_name").val().trim() == "") {
            $error = true;
        }
        if ($('#user_name').val().trim() == "") {
            $error = true;
        } else {
            if ($("#user_name_info ul li.invalid").length > 0) {
                $error = true;
            }
        }
        if ($("[name='enroll_online']:checked").length == 0) {
            $error = true;
        }
        if ($("[name='enroll_online']:checked").val() == "No") {
            if ($("#enroll_phone_number").val() == "") {
                $error = true;
            } else {
                if (getOnlyNumber($("#enroll_phone_number").val()).toString().length < 10) {
                    $error = true;
                }
            }
        }

        if ($error) {
            inValidTab("#settings_panel");
        } else {
            validTab("#settings_panel");
        }
    };
    allValidation = function () {
        topFoldValidationCheck();
        contactUsValidationCheck();
        productsValidationCheck();
        settingsValidationCheck();
    };
    trigger(".pb_img_delete", function ($this) {
        $image_id = $this.parents(".item").attr("data-id");
        trashQuestion(function () {
            handler("<?=$AGENT_HOST?>/ajax_page_edit_image.php", "action=delete&page_builder_id=<?=$page_builder_id?>&image_id=" + $image_id, function () {
                $("#ajax_loader").show();
            }, function (data) {
                $("#ajax_loader").hide();
                if (data.status == "success") {
                    destroySlider();
                    refreshPreview();
                    $(".item[data-id='" + $image_id + "']").remove();
                    initSlider();
                } else {
                    swal("Image Not Uploaded", "Sorry! Somthings Went wrong.");
                }
            });
        });
    });
    trigger("#enroll_phone_number,#phone_number", function ($this, e) {
        //this validation added caused if we have applied msking any where and not add all masking input date then masking will empty that textbox at that time we have validate this
        allValidation();
    }, "blur");
    trigger("input,textarea,select", function ($this) {
        $idd = $this.attr("id");
        removeErrorById($idd);
    }, "change keyup");
    $text = "";
    trigger("input[type='text'],textarea", function ($this) {
        $text = $this.val();
    }, "focus");
    trigger("input[type='text'],textarea", function ($this) {
        $text != $this.val() && previewProcessed();
    }, "blur");
    /*trigger('#contact_us_panel,#top_fold_panel',function(){
      $("textarea.resizable").autoHeight();
    },"shown.bs.collapse");*/

    $(function () {
        allValidation();
        $("#ajax_loader").hide();
    });

    // $(document).ready( function () {
    //   $('#prd_preview_iframe').load( function () {
    // });
    // });

    //live validation end

    $('.circle-tab a').on('shown.bs.tab', function (event) {
        //not required to edit this code, its generalized code
        $currentId = $(this).attr("href");
        $percentage = $(this).attr("data-pecentage");
        $("[href='" + $currentId + "']").parent("li").prevAll().addClass("complete");
        $("[href='" + $currentId + "']").parent("li").nextAll().removeClass("complete");
        $(".progress-bar").css({"width": $percentage});
        tabOpenActions($(this).attr("href"), $(this));
    });
    $(function () {
        $ref = $(".circle-tab li");
        $liSize = $ref.length;
        if ($ref.length > 0) {
            $ref.css("width", (parseInt(100 / $liSize) - 1) + "%");
        }
    });

    tabOpenActions = function ($tabName, $this) {
        /*if($tabName=="#thirdstep"){
          // alert($this.attr("data-contract"));
          if($this.attr("data-contract")=="Pending Contract"){
            signaturePadInit();
          }
        }*/
    };
    win = $("#prd_preview_iframe")[0].contentWindow;

    $("#xs-mobile").click(function () {
        $('#prd_preview_iframe').attr('width', '380px');
        $prevActiveId = $("ul.pb_prv_icon>li.active>a").attr("id");
        $(this).parent("li").siblings().removeClass("active");
        $(this).parent("li").addClass("active");
        win.postMessage("resize_frame", "<?=$HOST?>");
        $('#prd_preview_iframe').removeClass("bigscreen")
        responsiveIframe();
    });

    $("#xs-desktop").click(function () {
        $('#prd_preview_iframe').attr('width', '1024');

        $prevActiveId = $("ul.pb_prv_icon>li.active>a").attr("id");
        $(this).parent("li").siblings().removeClass("active");
        $(this).parent("li").addClass("active");
        win.postMessage("resize_frame", "<?=$HOST?>");
        $('#prd_preview_iframe').addClass("bigscreen")
        responsiveIframe();
    });

    $("#xs-tablet").click(function () {
        $("#prd_preview_iframe").attr('width', '768px');
        $prevActiveId = $("ul.pb_prv_icon>li.active>a").attr("id");
        $(this).parent("li").siblings().removeClass("active");
        $(this).parent("li").addClass("active");
        win.postMessage("resize_frame", "<?=$HOST?>");
        $('#prd_preview_iframe').removeClass("bigscreen")
        responsiveIframe();

    });
    responsiveIframe = function () {
        $(".iframe_sm_responsive").css({"width": $("#prd_preview_iframe")[0].getBoundingClientRect().width + 2});
    };
    setupHeight = function () {
        $(".iframe_sm_responsive").css({"height": $("#prd_preview_iframe")[0].getBoundingClientRect().height});
    };

    destroySlider = function () {
        $('#upload_img_slider').owlCarousel('destroy');
    };
    initSlider = function () {
        $('#upload_img_slider').owlCarousel({
            nav: true,
            items: 3,
            dots: false,
            navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: 4,
                },
                1400: {
                    items: 8,
                },
                1600: {
                    items: 9,
                }
            }
        });
    };
    /*img slider start*/
    $(document).ready(function () {
        checkEmail();
        initSlider();
        responsiveIframe();
        previewProcessed();
        if ($(window).width() >= 768) {
            $('.page-builder-footer').scrollFix({
                side: 'bottom'
            });
        }
    });
    /*img slider start*/

    /*=== full screen view start =====*/
    $("#full-scrren-view a").click(function () {
        $("body").addClass("full_preview");
        $("#screen_prv_scection").addClass("active");
    });
    $("#actual-screen-view a").click(function () {
        $("body").removeClass("full_preview");
        $("#screen_prv_scection").removeClass("active");
        $('#prd_preview_iframe').removeClass("bigscreen")
        responsiveIframe();
    });
    /*=== full screen view End =====*/
    $(window).on("resize", function () {
        if ($(window).width() > 1024) {
            $('#prd_preview_iframe').addClass("bigscreen")
        }
    });


    $(function () {
        if ($("[name='pb_img']:checked").length == 0) {
            $("#pb_img_-2").trigger("click");
            previewProcessed();
        }
    })

    $(window).on('resize', function () {
        var win = $(this);
        if (win.width() < 901) {
            $("#full-scrren-view a").click(function () {
                $('html, body').animate({scrollTop: $("#actual-screen-view").offset().top - 40}, 300);
            });
            $("#actual-screen-view a").click(function () {
                $('html, body').animate({scrollTop: $("#full-scrren-view").offset().top - 100}, 300);
            });
        }
    });
</script>
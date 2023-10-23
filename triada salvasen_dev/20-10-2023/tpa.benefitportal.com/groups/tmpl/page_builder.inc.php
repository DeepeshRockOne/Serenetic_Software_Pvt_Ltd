<style type="text/css">
    .modal[data-modaltype="profile"] .img-container { overflow: hidden; }
    .modal-footer .dropzon-options .flip_option { display: inline-block; vertical-align: middle; }
    .modal-footer .dropzon-options label { margin: 1px 10px 0 0; display: inline-block; vertical-align: top; }
    .modal-footer .dropzon-options .form-group.height_auto { margin-top: 4px; margin-right: 15px; margin-bottom: 0; }
    .dropzon-options .dropzon-controls { text-align: center; border-top: 1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2; padding: 20px 0; }
    .dropzon-options .flip_option { margin: 0; padding: 0; }
    .dropzon-options .flip_option li { display: inline-block; margin-right: 5px; padding-right: 10px; border-right: 1px solid #ccced0; }
    .dropzon-options .flip_option li a { font-size: 20px; line-height: 20px; }
    .dropzon-options .flip_option li:last-child { margin-left: 0; padding-right: 0; border-right: none; }
    #page-crop-modal .modal-header, #page-crop-modal .modal-footer  { border:0;}
    #cropper_image .img-container { max-height: 300px !important; min-height: 300px !important; overflow:hidden; }
</style>
<div class="agent_enroll_head">
    <div class="container">
        <h4>Instructions</h4>
        <p class=" mn">Build out your website below. Choose a background image to get started.</p>
    </div>
</div>
<div class="container m-t-30">
    <form id="page_builder_file_upload" enctype="multipart/form-data" action="<?= $GROUP_HOST ?>/ajax_page_edit_image.php">
        <input type="hidden" id="cover_image_size" name="cover_image[size]" value=""/>
        <input type="hidden" id="cover_image_name" name="cover_image[name]" value=""/>
        <input type="hidden" id="cover_image_type" name="cover_image[type]" value=""/>
        <input type="hidden" id="cover_image_tmp_name" name="cover_image[tmp_name]" value=""/>
        <input type="hidden" id="cover_image_mock" name="cover_image[mock]" value=""/>
        <input type="hidden" value="<?= $page_builder_id ?>" name="page_builder_id" />
    </form>
    <div class="panel-builder">
        <!-- tabs start -->
        <div class="thin-steps-numbered-bg m-t-0">
            <ul class="nav nav-tabs nav-justified nav-noscroll data_tab">
                <li class="active" data-tab="contact_tab">
                    <a data-toggle="tab" href="#top_fold_panel" aria-expanded="true">
                        Top Fold
                    </a>
                </li>
                <li data-tab="enroll_tab" class="">
                    <a data-toggle="tab" href="#products_panel" aria-expanded="false">
                        Products
                    </a>
                </li>
                <li data-tab="setting_tab" class="">
                    <a data-toggle="tab" href="#settings_panel" aria-expanded="false">
                        Settings
                    </a>
                </li>
            </ul>
        </div>
        <!-- tabs End -->
        <form id="page_builder" method="POST" class="event_form" enctype="multipart/form-data" action="<?= $GROUP_HOST ?>/ajax_page_edit.php">
        <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>">
        <div class="row panel-bcontent">
            <div class="col-sm-12 background_image_section">
                <h4 class="pb-title m-t-30">Background Image</h4>
                <div class="pd_up_image clearfix">
                    <p class="m-b-25">Choose a background image or upload your own.*</p>
                    <div class="new_theme_dropzone">
                        <div class="custom_drag_control">
                            <span class="btn btn-action">Upload Image</span>
                            <input type="text" class="gui-file" id="custom_bg_url" name="" multiple="">
                            <input type="text" class="gui-input" placeholder="Choose File">
                        </div>
                    </div>
                    
                    <!--
                    <div id="custom_bg_url" class=" new_theme_dropzone mbn remove-size"></div>  
                    <div class="new_theme_dropzone" id="custom_bg_url">
                        <div class="custom_drag_control">
                            <span class="btn btn-action">Upload Image</span>
                            <input type="file" class="gui-file" id="" name="">
                            <input type="text" class="gui-input" placeholder="Choose File">
                        </div>
                    </div> -->
                    <div class="pb-pre_imgs">
                        <div class="owl-carousel" id="upload_img_slider">
                            <?php
                            foreach ($pb_image_res as $pb_image_row) {
                                if (!empty($pb_image_row["image_name"]) && file_exists($PAGE_COVER_DIR . DIRECTORY_SEPARATOR . $pb_image_row["image_name"])) {
                                    ?>
                                    <div class="item" data-id="<?= $pb_image_row["id"] ?>">
                                        <input type="radio" name="pb_img" id="pb_img_<?= $pb_image_row["id"] ?>"
                                               class="js-switch"
                                               value="<?= $pb_image_row["id"] ?>" <?= $pb_image_row["id"] == $cover_image ? 'checked' : '' ?> />
                                        <label for="pb_img_<?= $pb_image_row['id'] ?>"
                                               style="background-image: url('<?= $PAGE_COVER_WEB . "/" . $pb_image_row["image_name"] ?>');">
                                        </label>
                                        <?php if ($pb_image_row["page_builder_id"] != 0) { ?>
                                            <a href="javascript:void(0);" class="pb_img_delete" data-container="body"
                                               data-toggle="tooltip" data-placement="top" title="Delete"><i
                                                        class="fa fa-trash-o"></i></a>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <p class="error dropzone_error error_cover_image"></p>
                </div>
                <div class="clearfix">
                    <hr class="m-b-0 m-t-30">
                </div>
            </div>
            <div class="col-sm-6 p-t-30 ">
                    <input type="hidden" value="update" name="data_action" id="data_action"/>
                    <input type="hidden" value="<?= $page_builder_id ?>" name="page_builder_id" id="page_builder_id"/>
                    <input type="hidden" id="is_back_ground_image" name="is_back_ground_image"
                           value="<?= ($cover_image != "" && file_exists($PAGE_COVER_DIR . $cover_image)) ? "Y" : "N" ?>">
                    <input type="hidden" id="background_action_image" name="background_action_image">
                    <input type="hidden" id="step" name="step" value="1">
                    <div class="panel-group" id="accordion">
                        <div class="tab-content mn">
                            <div id="top_fold_panel" class="tab-pane fade active in">
                                <div class="panel-body pn">
                                    <div class="org_panel_text theme-form">
                                        <div class="cdrop_wrapper m-b-25">
                                            <h4 class="pb-title">Logo</h4>
                                            <div class="custom_drag_control">
                                                <span class="btn btn-action">Upload Image</span>
                                                <input type="file" class="gui-file" id="logo" name="logo">
                                                <input type="text" class="gui-input" placeholder="Choose File">
                                                <p class="error error_logo"></p>
                                            </div>
                                            <div class="m-t-10">
                                                <?php if ($logo != "" && file_exists($PAGE_LOGO_DIR . $logo)) { ?>
                                                    <img src="<?= $PAGE_LOGO_WEB . $logo ?>" id="preview_logo"
                                                         class="img-thumbnail" style="height:50px"/>
                                                    <input type="hidden" name="preview_exists" id="preview_exists"
                                                           value='Y'/><!-- for validation only -->
                                                <?php } else {
                                                    ?>
                                                    <img style="display: none" id="preview_logo" class="img-thumbnail"/>
                                                    <input type="hidden" name="preview_exists" id="preview_exists"
                                                           value='N'/><!-- for validation only -->
                                                    <?php
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="m-b-25">
                                            <h4 class="pb-title">Header</h4>
                                            <textarea id="header_content" name="header_content"
                                                      placeholder="Top Heading (ie. Let us help you find the plan you deserve!)"
                                                      class="form-control" rows="1"
                                                      maxlength="<?= HEADER_CONTENT_LIMIT ?>"
                                                      placeholder=""><?= substr($header_content, 0, HEADER_CONTENT_LIMIT) ?></textarea>
                                            <p class="error error_header_content"></p>
                                        </div>
                                        <div class="m-b-25">
                                            <h4 class="pb-title">Content Section</h4>
                                            <textarea name="header_subcontent" id="header_subcontent"
                                                          placeholder="Sub Content (ie. Let us help you find the plan you deserve!)"
                                                          rows="4" class="form-control ckeditor"
                                                          ><?= $header_subcontent ?></textarea>
                                            <p class="error error_header_subcontent"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="products_panel" class="tab-pane fade prd_selctions">
                                <div class="panel-body pn">
                                    <h4 class="pb-title">Product Section</h4>
                                    <div class="org_panel_text mb40 theme-form">
                                        <div class="form-group ">
                                                <select name="category_ids[]" id="category_ids" class="se_multiple_select" multiple="multiple" data-live>
                                                    <?php 
                                                        foreach ($prd_category_res as $key => $prd_category_row) {
                                                    ?>
                                                    <option value="<?=$prd_category_row['category_id']?>" <?=in_array($prd_category_row['category_id'],$category_ids)?"selected":""?>><?=$prd_category_row['category_name']?></option>
                                                    <?php
                                                        } 
                                                    ?>
                                                </select>
                                                <label class="label-wrap">Select Product Categories That You Would Like Displayed</label>
                                                <p class="error error_category_ids"></p>
                                        </div>
                                        <?php 
                                            foreach ($prd_category_res as $key => $prd_category_row) {
                                                ?>
                                                <div class="m-t-10 category_section category_section_<?=$prd_category_row['category_id']?>" <?=in_array($prd_category_row['category_id'],$category_ids)?"":'style="display: none;"'?>>
                                                    <h4 class="pb-title m-b-10"><?=$prd_category_row['category_name']?></h4>
                                                    <div class="table-responsive">
                                                        <div class="prd_builder_table">
                                                            <div class="table-responsive">
                                                                <table class="<?= $table_class ?>">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Product</th>
                                                                        <th width="30%">Pricing</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        foreach ($prd_category_row['prd_res'] as $prd_row) {
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <label class="mn">
                                                                                    <input type="checkbox" 
                                                                                    name="product_ids[]" 
                                                                                    value="<?=$prd_row['product_id']?>" 
                                                                                    <?=in_array($prd_row['product_id'],$product_ids)?"checked":""?>> 
                                                                                    <?=$prd_row['product_name']?>
                                                                                </label>
                                                                            </td>
                                                                            <td>Starting at <?=displayAmount($prd_row['price']);?></td>
                                                                        </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        ?>
                                        <p class="error error_product_ids"></p>
                                    </div>
                                </div>
                            </div>
                            <div id="settings_panel" class="tab-pane fade">
                                <div class="panel-body pn">
                                    <h4 class="pb-title">Settings</h4>
                                    <div class="org_panel_text theme-form">
                                        <p>Select classes who will use this site to enroll*</p>
                                        <div class="form-group">
                                                <select name="class_ids[]" id="class_ids" class="se_multiple_select" multiple="multiple" data-live>
                                                    <?php 
                                                        foreach ($resClass as $key => $classes) {
                                                    ?>
                                                    <option value="<?=$classes['id']?>" <?=in_array($classes['id'],$class_ids)?"selected":""?>><?=$classes['class_name']?></option>
                                                    <?php
                                                        } 
                                                    ?>
                                                </select>
                                                <label>Assign classes to Enroll</label>
                                                <p class="error error_class_ids"></p>
                                        </div>
                                        <p>Name Site*</p>
                                        <div class="form-group">
                                            <input type="text" name="page_name" id="page_name" class="form-control"
                                                   required="" value="<?= $is_clone == 'Y' ? '' : $page_name ?>"/>
                                            <label>Site Name</label>
                                            <p class="error error_page_name"></p>
                                        </div>
                                        <p>Email(s) you'd like any quote request to be sent to*</p>
                                        <div class="form-group">
                                            <input type="text" name="contact_us_emails" id="contact_us_emails" class="form-control no_space" value="<?=$contact_us_emails?>" required="">
                                            <label>Enter Email(s) - Seperate by semicolon (;)</label>
                                            <p class="error error_contact_us_emails"></p>
                                        </div>
                                        <p>Set Website URL*</p>
                                        <div class="form-group ">
                                            <div class="input-group">
                                                <span class="input-group-addon"><?= $DEFAULT_SITE_URL ?>/</span>
                                                <input class="tblur form-control" value="<?= $is_clone == 'Y' ? '' : $user_name ?>"
                                                       id="user_name" name="user_name" data-error="Username is required"
                                                       required="" type="text" placeholder="Web Alias">
                                                <div id="user_name_info" class="pswd_popup" style="display: none">
                                                    <div class="pswd_popup_inner">
                                                        <h4>URL Requirements</h4>
                                                        <ul style="list-style:none; padding-left:10px;">
                                                            <li id="ulength"
                                                                class="<?= (empty($user_name) ? 'invalid' : 'valid') ?>">
                                                                <em></em>Be between 4-20 characters
                                                            </li>
                                                            <li id="alpha"
                                                                class="<?= (empty($user_name) ? 'invalid' : 'valid') ?>">
                                                                <em></em>Contain no spaces or special characters
                                                            </li>
                                                            <li id="unique"
                                                                class="<?= (empty($user_name) ? 'invalid' : 'valid') ?>">
                                                                <em></em>Unique URL
                                                            </li>
                                                        </ul>
                                                        <div class="btarrow"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="error error_user_name"></p>
                                        </div>
                                        
                                        <p>Conatact Us - Phone # for assistance*</p>
                                        <div class="form-group">
                                            <input type="text" name="contact_us_phone_number" id="contact_us_phone_number" value="<?=$contact_us_phone_number?>" class="form-control">
                                            <label>Phone # (555) 555-5555</label>
                                            <p class="error error_contact_us_phone_number"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="text-center col-sm-12 mt20 mb20">
                            <button class="next_page_builder btn btn-info " type="button">Next</button>
                            <button class="update_page_draft btn btn-info" style="display: none" type="button">Save
                                Draft
                            </button>
                            <button class="update_page_builder btn btn-action" style="display: none" type="button">
                                Publish Page
                            </button>
                        </div> -->
                    </div>
                
            </div>
            <div class="col-sm-6 p-t-30" id="screen_prv_scection">
                <div class="panel-previwe">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="pb-title">Preview</h4>
                        </div>
                        <div class="pull-right">
                            <ul class="pb_prv_icon">
                                <li>
                                    <a href="javascript:void(0)" id="xs-mobile" data-toggle="tooltip"
                                       data-placement="top" title="Mobile View"><i class="ic ic-mobile"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" id="xs-tablet" data-toggle="tooltip"
                                       data-placement="top" title="Tablet View"><i class="ic ic-tablet"></i></a>
                                </li>
                                <li class="active">
                                    <a href="javascript:void(0)" id="xs-desktop" data-toggle="tooltip"
                                       data-placement="top" title="Desktop View"><i class="ic ic-pc"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="pb-preview">
                        <div class="iframe_sm_responsive">
                            <div class="custom_loader"></div>
                            <iframe src="<?=$HOST?>/prd_preview.php?page_builder_id=<?=md5($page_builder_id)?>" id="prd_preview_iframe" style="" frameborder="0" width="1024px"
                                    scrolling="no" align="middle"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div class="page-builder-footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="m-b-0 m-t-7 text-light-gray"><span id="current_step">1</span> of 3 Steps </p>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-right">
                            <button type="button" class="btn btn-info update_page_draft">Save Draft</button>
                            <button type="button" class="btn btn-action next_page_builder">Next</button>
                            <button type="button" class="btn btn-action update_page_builder" style="display: none">Publish Page</button>
                            <button type="button" class="btn red-link btn_cancel">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
     </div>
    </div>
</div>
<!-- Modal -->
<div id="page-crop-modal" class="modal modal_theme fade bd-example-modal-lg" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">X</button>
                <h4 class="modal-title fw300"></h4>
            </div>
            <div class="modal-body clearfix">
                <div id="cropper_image" class="clearfix dropzon-options text-center">
                    <div class="img-container">
                        <img id="page_image">
                    </div>
                </div>
            </div>
            <div class="modal-footer ">
                <div class="page-cropper-button">
                    <div class="page-cropper-button dropzon-options">
                        <div class="dropzon-controls form-inline">
                            <div class="form-group  mr15">
                                <label>Zoom</label>
                                <ul class="flip_option mtn">
                                    <li><a href="javascript:void(0);" data-method="zoom" data-option="0.1"
                                           title="Zoom In"><i class="fa fa-search-plus"></i></a></li>
                                    <li><a href="javascript:void(0);" data-method="zoom" data-option="-0.1"
                                           title="Zoom Out"><i class="fa fa-search-minus"></i></a></li>
                                </ul>
                            </div>
                            <div class="form-group  mr15">
                                <label>Rotate</label>
                                <ul class="flip_option mtn">
                                    <li><a href="javascript:void(0);" data-method="rotate" data-option="-45"
                                           title="Rotate Left"><i class="fa fa-undo"></i></a></li>
                                    <li><a href="javascript:void(0);" data-method="rotate" data-option="45"
                                           title="Rotate Right"><i class="fa fa-repeat"></i></a></li>
                                </ul>
                            </div>
                            <div class="form-group  mr15">
                                <label>Flip</label>
                                <ul class="flip_option mtn">
                                    <li><a href="javascript:void(0);" id="scaleX" title="Flip Horizontal"><img
                                                    src="<?= $GROUP_HOST ?>/images/flip-h.svg" width="25px"/></a></li>
                                    <li><a href="javascript:void(0);" id="scaleY" title="Flip Vertical"><img
                                                    src="<?= $GROUP_HOST ?>/images/flip-v.svg" width="25px"/></a></li>
                                </ul>
                            </div>
                            <div class="form-group  mr15">
                                <label>Position</label>
                                <ul class="flip_option mtn">
                                    <li><a href="javascript:void(0);" data-method="move" data-option="-10"
                                           data-second-option="0" title="Move Left"><i class="fa fa-arrow-left"></i></a>
                                    </li>
                                    <li><a href="javascript:void(0);" data-method="move" data-option="10"
                                           data-second-option="0" title="Move Right"><i
                                                    class="fa fa-arrow-right"></i></a></li>
                                    <li><a href="javascript:void(0);" data-method="move" data-option="0"
                                           data-second-option="-10" title="Move Up"><i class="fa fa-arrow-up"></i></a>
                                    </li>
                                    <li><a href="javascript:void(0);" data-method="move" data-option="0"
                                           data-second-option="10" title="Move Down"><i
                                                    class="fa fa-arrow-down"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="dz-action-div text-center m-t-15">
                            <input type="hidden" name="dropzone_type" id="dropzone_type">
                            <button type="button" class="btn btn-info" data-dismiss="modal"
                                    data-method="saveCropperImage">Save Image
                            </button>
                            <button type="button" class="btn btn-default mn ml15" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once "page_builder_jquery.inc.php"; ?>
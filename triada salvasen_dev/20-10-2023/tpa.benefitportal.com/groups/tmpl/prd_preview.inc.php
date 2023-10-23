<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <link rel="shortcut icon" href="<?= $HOST ?>/images/favicon.ico?<?= $cache ?>">
    <title><?= $SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $AGENT_HOST ?>/css/prd_preview.css<?= $cache ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/thirdparty/colorbox/colorbox.css"/>
    <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.css<?=$cache?>">

    <script type="text/javascript" src="<?= $HOST ?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= $HOST ?>/js/bootstrap.min.js"></script>
    <script src="<?= $HOST ?>/thirdparty/colorbox/jquery.colorbox.js"></script>
    <script src="<?= $HOST ?>/thirdparty/jquery_custom.js<?= $cache ?>"></script>
    <script type="text/javascript" src="<?= $AGENT_HOST ?>/js/prd_view_theme.js<?= $cache ?>"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/promise.min.js"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/masked_inputs/jquery.inputmask.bundle.js"></script>

    <style type="text/css">
        .text-action {
            color: #bd4360 !important;
        }
        a {
            color: #465b74;
        }
        a:hover, a:focus {
            text-decoration: none;
            color: #465b74;
        }
        .error, .required_error {
            padding: 0px !important;
            color: #FF0000!important;
            font-size: 12px!important;
            margin: 0px!important;
        }
    </style>
    <!-- JS includes -->
    <script type="text/javascript">
        resizeFrame = function () {
            if(typeof(parent.resizeIframe) !== "undefined") {
                parent.resizeIframe($("body").outerHeight());
            }            
        };

        $(function () {
            resizeFrame();
        });

        window.addEventListener("message", function (e) {
            setTimeout(function () {
                resizeFrame();
            }, 1000);
            setTimeout(function () {
                resizeFrame();
            }, 2000);
            if (e.data == "resize_frame") {
                resizeFrame();
            }
            if (e.data == "resize_frame") {
                resizeFrame();
            }
            if (e.data == "resize_frame") {
                resizeFrame();
            }
        }, false);
    </script>
    <style>
        <?php if (!empty($pb_row['cover_image']) && file_exists($PAGE_COVER_DIR . $pb_row['cover_image'])) { ?>
        .main_top_rail:after {
            background: url(<?=$PAGE_COVER_WEB . $pb_row['cover_image']?>) no-repeat center;
            background-size: cover;
        }

        <?php } ?>
    </style>
</head>
<body class="prd_preViewing">
<div class="header">
    <div class="container-fluid">
        <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php';?>

        <div class="row">
            <div class="col-sm-6 col-xs-4">
                <div class="logos">
                    <a href="javascript:void(0);" class="logo-wrap">
                        <?php if ($pb_row['logo'] != "" && file_exists($PAGE_LOGO_DIR . $pb_row['logo'])) { ?>
                        <img src="<?= $PAGE_LOGO_WEB . $pb_row['logo'] ?>" height="30px" alt="">
                        <?php } else { ?>
                        <img src="<?= $HOST ?>/images/logo.svg" height="30px" alt="">
                        <?php } ?>
                    </a>
                </div>
            </div>
            <div class="col-sm-6 col-xs-8">
                <div class="request_demo_wrap">
                    <div class="dropdown pull-right ">
                        <a href="javascript:void" class="dropdown-toggle" type="button" data-toggle="dropdown">
                            CONTACT US
                            &nbsp;
                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <p class="fs12 text-white">Fill Out the form below to get started or call <?=format_telephone($pb_row['contact_us_phone_number']);?> for any
                                assistance</p>
                            <div class="request_form">
                                <form action="<?= $HOST ?>/request_quote.php" role="form" method="post" class="theme-form " name="form_request_quote" id="form_request_quote">
                                    <input type="hidden" name="id" value="<?=md5($pb_row['id'])?>">
                                    <div class="form-group">
                                    <input class="form-control" name="full_name" id="full_name" type="text" placeholder="Full Name" required="">
                                    <p class="error error_full_name" style="display: none;"></p>
                                    </div>
                                    <div class="form-group">
                                    <input class="form-control" name="email" id="email" type="text" placeholder="Email" required="">
                                    <p class="error error_email" style="display: none;"></p>
                                    </div>
                                    <div class="form-group">
                                    <input class="form-control" name="phone" id="phone" type="text" placeholder="Phone" required="">
                                    <p class="error error_phone" style="display: none;"></p>
                                    </div>
                                    <div class="form-group">
                                    <textarea class="form-control mb15" name="comment" id="comment" rows="3" placeholder="Comment…"></textarea>
                                    <p class="error error_comment" style="display: none;"></p>
                                    </div>
                                    <div class="form-group text-center mn">
                                        <button type="submit" id="btn_request_quote" class="btn btn-white-o">Submit <i class="fa fa-spinner fa-spin form_loader" style="font-size:24px;display: none;"></i></button>
                                    </div>
                                </form>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="main_top_rail">
    <div class="container">
        <div class="center_heading">
            <h1><?= $pb_row["header_content"] ?></h1>
            <p class="h_sub_content"><?= $pb_row["header_subcontent"] ?></p>
        </div>
    </div>
</section>
<div class="bottom_part_gray">
    <section class="product_wrap">
        <div class="container">
            <div class="clearfix mb25">
                <div class="pull-left">
                    <h1 class="mn fw300">Products</h1>
                </div>
                <div class="pull-right">
                    <a href="<?=$website_enroll_link?>" class="btn btn-action">Get Plan</a>
                </div>
            </div>
            <div class="row">
                <?php if(!empty($prd_category_res)) { ?>
                    <?php foreach ($prd_category_res as $key => $prd_category_row) { ?>
                        <div class="col-sm-4">
                            <div class="plan_box">
                                <h5><?=$prd_category_row['category_name']?></h5>
                                <?php 
                                    $category_image = $HOST.'/images/prd_preview/thumbnail/hugging_girl_in_wheelchair.jpg';
                                    if(file_exists($CATEGORY_IMAGE_DIR . $prd_category_row['category_image'])) {
                                        $category_image = $CATEGORY_IMAGE_WEB . $prd_category_row['category_image'];
                                    }
                                ?>
                                <div class="plan_thumb" style="background-image: url(<?=$category_image?>);">
                                </div>
                                <div class="plan_info">
                                    <p class="mn"><?=$prd_category_row['short_description']?></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php  } else { ?>
                <?php  } ?>                
            </div>
        </div>
    </section>
    <section class="get_coverage">
        <div class="container">
            <div class="get_coverage_content">
                <a href="<?=$website_enroll_link?>" class="btn btn-action">Get Plan</a>
            </div>
        </div>
    </section>
</div>
<div class="smarte_footer">
    <div class="container">
        <div class="footer_help">
            <div class="pull-left">
                <h4 class="text-action m-t-0">NEED HELP?</h4>
                <?php if($spon_row['display_in_member'] == "N") { ?>
                    <p class="m-b-20"><?=$spon_row['public_name'];?>  &nbsp; |  &nbsp;  <a href="<?='tel:+1'.$spon_row['public_phone']?>"><?=format_telephone($spon_row['public_phone']);?></a> &nbsp; | &nbsp; <a href="mailto:<?=$spon_row['public_email'];?>"><?=$spon_row['public_email'];?></a> </p>
                <?php } else { ?>
                    <p class="m-b-20">Member Services  &nbsp; |  &nbsp;  <a href="<?='tel:+1'.$member_services_cell_phone?>"><?=format_telephone($member_services_cell_phone);?></a> &nbsp; | &nbsp; <a href="mailto:<?=$member_services_email;?>"><?=$member_services_email;?></a> </p>
                <?php } ?>
            </div>
            <div class="pull-right">
                <img src="<?= $POWERED_BY_LOGO ?>" height="43px">
            </div>
        </div>
    </div>
    <div class="bottom_footer">
        <div class="container">
            <?php /*<a href="javascript:void(0);" class="btn btn-white-o">Live Chat</a>*/ ?>
            <ul>
                <?php /*<li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Legal</a></li>
                <li><a href="#">FAQ</a></li>*/ ?>
                <li class="" style="padding: 10px;"><?= $DEFAULT_SITE_NAME ?> &copy; <?php echo date('Y')?></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});

        $(document).off("submit","#form_request_quote");
        $(document).on("submit","#form_request_quote", function(e){
            e.preventDefault();
            $('#btn_request_quote').prop('disabled',true);
            $(".error").hide();
            $.ajax({
                url: $("#form_request_quote").attr('action'),
                type: 'POST',
                data: $("#form_request_quote").serialize(),
                dataType : 'json',
                beforeSend:function(){
                    $("#ajax_loader").show();
                    $('.form_loader').show();
                },
                success: function(res) {
                    $('#btn_request_quote').prop('disabled',false);
                    $('.form_loader').hide();
                    $('#ajax_loader').hide();
                    if(res.status == 'success'){
                        $("#full_name").val('');
                        $("#email").val('');
                        $("input#phone").val('');
                        $("#comment").val('');

                        swal({
                            text: '<br/>' + res.msg,
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Close'
                        }).then(function () {
                            window.location.reload();
                        }, function (dismiss) {
                            
                        });
                    } else if(res.status == 'page_not_found'){
                        window.location.reload();
                    } else {
                        $.each(res.errors, function(key, value) {
                            $('.error_' + key).html(value).show();
                        });
                    }
                }
            });
        });
    });
</script>
</body>
</html>
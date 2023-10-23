<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title><?= $SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $AGENT_HOST ?>/css/prd_preview.css<?= $cache ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/thirdparty/colorbox/colorbox.css"/>
    <script type="text/javascript" src="<?= $HOST ?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= $HOST ?>/js/bootstrap.min.js"></script>
    <script src="<?= $HOST ?>/thirdparty/colorbox/jquery.colorbox.js"></script>
    <script src="<?= $HOST ?>/thirdparty/jquery_custom.js<?= $cache ?>"></script>
    <script type="text/javascript" src="<?= $AGENT_HOST ?>/js/prd_view_theme.js<?= $cache ?>"></script>
    <!-- JS includes -->
    <script type="text/javascript">
        resizeFrame = function () {
            parent.resizeIframe($("body").outerHeight());
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
        .main_top_rail {
            background: url(<?=$PAGE_COVER_WEB . $pb_row['cover_image']?>) no-repeat center;
            background-size: cover;
        }

        <?php } ?>
    </style>
</head>
<body class="prd_preViewing">
<div class="header">
    <div class="container-fluid">
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
                            <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;
                            REQUEST A QUOTE&nbsp;
                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <p class="fs12 text-white">Fill Out the form below to get started or call (555) 555-5555 for any
                                assistance</p>
                            <div class="request_form">
                                <input class="form-control" type="text" placeholder="Full Name">
                                <input class="form-control" type="text" placeholder="Email">
                                <input class="form-control" type="text" placeholder="Phone">
                                <textarea class="form-control mb15" rows="3" placeholder="Comment…"></textarea>
                                <div class="text-center">
                                    <a href="javascript:void(0);" class="btn btn-white-o">Submit</a>
                                </div>
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
                    <a href="javascript:void(0);" class="btn btn-action">Get Plan</a>
                </div>
            </div>
            <div class="row">
                <?php if(!empty($prd_category_res)) { ?>
                    <?php foreach ($prd_category_res as $key => $prd_category_row) { ?>
                        <div class="col-sm-4">
                            <div class="plan_box">
                                <h5><?=$prd_category_row['category_name']?></h5>
                                <?php 
                                    $category_image = $AGENT_HOST.'/images/prd_preview/thumbnail/hugging_girl_in_wheelchair.jpg';
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
                <a href="javascript:void(0);" class="btn btn-action">Get Plan</a>
            </div>
        </div>
    </section>
</div>
<div class="smarte_footer">
    <div class="container">
        <div class="footer_help">
            <div class="pull-left">
                <h4 class="text-action m-t-0">NEED HELP?</h4>
                <p class="m-b-20"></p>
            </div>
            <div class="pull-right">
                <img src="<?= $POWERED_BY_LOGO ?>" height="43px">
            </div>
        </div>
    </div>
    <div class="bottom_footer">
        <div class="container">
            <a href="javascript:void(0);" class="btn btn-white-o">Live Chat</a>
            <ul>
                <li><a href="#">Privacy Plan</a></li>
                <li><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Legal</a></li>
                <li><a href="#">FAQ</a></li>
                <li><?= $DEFAULT_SITE_NAME ?> &copy; <?php echo date('Y')?></li>
            </ul>
        </div>
    </div>
</div>
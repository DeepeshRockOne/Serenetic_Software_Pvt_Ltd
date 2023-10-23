<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <link rel="shortcut icon" href="<?= $HOST ?>/images/favicon.ico?<?= $cache ?>">
    <title><?= $SITE_NAME; ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $GROUP_HOST ?>/css/prd_preview.css<?= $cache ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= $HOST ?>/thirdparty/colorbox/colorbox.css"/>
    <link rel="stylesheet" type="text/css" href="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.css<?=$cache?>">

    <script type="text/javascript" src="<?= $HOST ?>/js/jquery.min.js"></script>
    <script type="text/javascript">
        $HOST='<?= $HOST ?>';
    </script>
    <script type="text/javascript" src="<?= $HOST ?>/js/bootstrap.min.js"></script>
    <script src="<?= $HOST ?>/thirdparty/colorbox/jquery.colorbox.js"></script>
    <script src="<?= $HOST ?>/thirdparty/jquery_custom.js<?= $cache ?>"></script>
    <script type="text/javascript" src="<?= $AGENT_HOST ?>/js/prd_view_theme.js<?= $cache ?>"></script>
    <script type="text/javascript" src="<?=$HOST?>/js/notification.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/sweetalert2/promise.min.js"></script>
    <script type="text/javascript" src="<?=$HOST?>/thirdparty/masked_inputs/jquery.inputmask.bundle.js"></script>
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
        .error{color: red;}
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
    <?php include_once dirname(__DIR__) . '/tmpl/notify.inc.php';?>
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
                        <?php /*
                        <a href="<?=$HOST?>/group_enroll/<?=$userName?>" class="btn btn-action">Begin Enrollment</a>
                        */?>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="main_top_rail">
        <div class="top_rail_tagline fs24  fw700 text-white">
            <?= $pb_row["header_content"] ?>
        </div>
    </section>

    <section class="enroll_intro">
        <div class="container">
            <p class="h_sub_content"><?= html_entity_decode($pb_row["header_subcontent"]) ?></p>
        </div>
    </section>

    <section class="plan_area">
        <div class="text-center">
            <h3 class="fs24 mt0 mb25 fw700">Products</h3>
        </div>
        <div class="container">
            <div class="row">
                <?php if(!empty($prd_category_res)) { ?>
                    <?php foreach ($prd_category_res as $key => $prd_category_row) { ?>
                        <div class="col-sm-4">
                            <div class="single-plan-block">
                                <div class="single-plan-head clearfix">
                                    <h4 class="fs18 fw700 "><?=$prd_category_row['category_name']?></h4>
                                    <a href="" class="btn btn-action plan_view" data-category_id="<?=md5($prd_category_row['category_id'])?>">View</a>
                                </div>
                                <p class="mt15"><?=$prd_category_row['short_description']?></p>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="strat_enroll">
        <div class="container">
            <div class="text-center strat_enroll_action">
                <a href="javascript:void(0)" data-href="begin_enrollment.php?username=<?=$userName?>&sponsorId=<?=md5($sponsor_id)?>" class="btn btn-action begin_enrollment">Begin Enrollment</a>
            </div>
        </div>
    </section>
    <?php include_once 'layout/bablic.inc.php'; ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false}); 
        });

        $(document).off("submit","#form_request_quote");
        $(document).on("submit","#form_request_quote", function(e){
            e.preventDefault();
            $('#btn_request_quote').prop('disabled',true);
            $(".error").hide();
            $.ajax({
                url: '<?=$HOST?>/ajax_api_call.php',
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
                    if(res.status == 'Success'){
                        $("#full_name").val('');
                        $("#email").val('');
                        $("input#phone").val('');
                        $("#comment").val('');

                        swal({
                            text: '<br/>' + res.message,
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Close'
                        }).then(function () {
                            window.location.reload();
                        }, function (dismiss) {
                            
                        });
                    } else if(res.data.userName != '' && typeof res.data.userName !== 'undefined'){
                        window.location.reload();
                    } else {
                        $.each(res.data, function(key, value) {
                            $('.error_' + key).html(value).show();
                        });
                    }
                }
            });
        });

        $(document).off('click', '.plan_view');
        $(document).on('click', '.plan_view', function(e) {
          e.preventDefault();
          var productIDs = '<?=$product_ids?>';
          var agentID = '<?=$pb_row['agent_id']?>';
          var categoryID = $(this).data('category_id');
          var url ="prd_plan_view.php?categoryID="+categoryID+"&agentID="+agentID+"&productIDs="+productIDs;
          $.colorbox({
            href: url,
            iframe: true,
            width: '1064px',
            height: '600px',
          });
        });

        $(document).off('click', '.begin_enrollment');
        $(document).on('click', '.begin_enrollment', function(e) {
          e.preventDefault();
          $.colorbox({
            href: $(this).attr('data-href'),
            iframe: true,
            width: '580px',
            height: '200px',
          });
        });
    </script>
</body>
</html>
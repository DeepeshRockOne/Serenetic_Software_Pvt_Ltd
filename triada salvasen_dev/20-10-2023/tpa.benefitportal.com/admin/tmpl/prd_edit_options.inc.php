<div class="clearfix">
  <?php if(!empty($managePrdArr)){ ?>
    <?php foreach ($managePrdArr as $key => $val) { ?>
      <div class=" lhs_content">
        <div class="white-box">
        <div class="clearfix m-b-15 tbl_filter">
          <div class="pull-left">
            <h4 class="m-t-7"><?= $val['title'] ?></h4>
          </div>
          <div class="pull-right">
            <div class="note_search_wrap auto_size " id="<?= $val['id'] ?>_search_div" style="display: none;">
              <div class="phone-control-wrap">
                <div class="phone-addon">
                  <a href="javascript:void(0);" class="search_close_btn text-light-gray " data-close="<?= $val['id'] ?>" >X</a>
                </div>
                <div class="phone-addon">
                  <input type="text" name="<?= $val['id'] ?>" id="<?= $val['id'] ?>" class="form-control" value=""  placeholder="<?= $val['add_name'] ?>">
                </div>
                <div class="phone-addon w-80">
                  <a href="javascript:void(0);" class="btn btn-info search_button" data-search="<?= $val['id'] ?>" ><i class="fa fa-search"></i> Search</a>
                </div>
              </div>
            </div>
            <a href="javascript:void(0);" class="search_btn searchBox" id="search_<?= $val['id'] ?>" data-tab="<?= $val['id'] ?>" ><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0);" class="btn btn-action add_button m-l-5" data-add="<?= $val['id'] ?>" style="display:inline-block;" >+ <?= $val['add_name'] ?></a>
            <button type="button" data-type="<?= $val['id'] ?>" id="exportReport" class="btn red-link"> <i class="fa fa-download"></i> Export </button>
          </div>
          </div>
          <?php if($val['title'] == 'CONNECTED PRODUCTS'){ ?>
            <p class="mn">Use the feature below to set the display order and upgrade/downgrade options for related products.</p>
            <hr />
          <?php } ?>
          <div id="<?= $val['id'] ?>_div"></div>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
</div>
<div id="prd_conn_ajax_loader" class="ajex_loader fixed" style="display: none;"></div>
<script type="text/javascript">

$(document).ready(function() {
  dropdown_pagination('company_offering_products_div','product_categories_div','sub_products_div');
})
    
  $(document).off("click", ".searchBox");
  $(document).on("click", ".searchBox", function(e) {
    e.preventDefault();
    var tabs = $(this).attr('data-tab');
    $(this).hide();
    $("#" + tabs + "_search_div").css('display', 'inline-block');
    $("#" + tabs + "_search_div").show();
  });

  $(document).off("click", ".search_close_btn");
  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    var tabs = $(this).attr('data-close');
    $("#" + tabs + "_search_div").hide();
    $("#search_" + tabs).show();
    $('#' + tabs).val('');

    if (tabs == 'company_offering_products') {
      company_offering_products(search_val = '');
    } else if (tabs == 'product_categories') {
      product_categories(search_val = '');
    } else if (tabs == 'sub_products') {
      sub_products(search_val = '');
    }else if (tabs == 'connected_products') {
      connected_products(search_val = '');
    }

  });

  $(document).off("click", ".search_button");
  $(document).on("click", ".search_button", function(e) {
    e.preventDefault();
    var search = $(this).attr('data-search');
    var search_val = $('#' + search).val();

    if (search == 'company_offering_products') {
      company_offering_products(search_val);
    } else if (search == 'product_categories') {
      product_categories(search_val);
    } else if (search == 'sub_products') {
      sub_products(search_val);
    }else if (search == 'connected_products') {
      connected_products(search_val);
    }
  });


  $(document).off("click", ".add_button");
  $(document).on("click", ".add_button", function(e) {
    e.preventDefault();
    var tabs = $(this).attr('data-add');

    if (tabs == 'company_offering_products') {
      $link = 'company_offering_products_edit.php';
      $.colorbox({
        href: $link,
        iframe: true,
        width: '600px',
        height: '250px',
        onClosed: function() {
          company_offering_products();
        }
      });
    } else if (tabs == 'product_categories') {
      $link = 'product_categories_edit.php';
      $.colorbox({
        href: $link,
        iframe: true,
        width: '600px',
        height: '700px',
        onClosed: function() {
          product_categories();
        }
      });
    } else if (tabs == 'sub_products') {
      $link = 'sub_product_edit.php';
      $.colorbox({
        href: $link,
        iframe: true,
        width: '500px',
        height: '430px',
        onClosed: function() {
          sub_products();
        }
      });
    } else if (tabs == 'connected_products') {
      addConnectionDiv();
    }

  });


  $(document).ready(function() {
    var company_offering_products_BoxHeight = '';
    var product_categories_BoxHeight = '';

    company_offering_products();
    product_categories();
    sub_products();
    connected_products();

  });

  $(document).off('click', '#exportReport');
  $(document).on('click', '#exportReport', function (e) {
      e.stopPropagation();
      var reportType = $(this).attr('data-type');
      var serachVal = $("#"+reportType).val();
      confirm_export_data(function() {
          $('#ajax_loader').show();
          $.ajax({
              url: 'prd_edit_options.php',
              type: 'GET',
              data: {
                export_val:1,
                serachVal:serachVal,
                reportType: reportType
              },
              dataType: 'json',
              success: function(res) {
                  $('#ajax_loader').hide();
                  if(res.status == "success") {
                      confirm_view_export_request();
                  } else {
                      setNotifyError(res.message);
                  }
              }
          });
      });
  });

  company_offering_products = function(search_val) {
    $('#ajax_loader').show();
    $('#company_offering_products_div').hide();
    $.ajax({
      url: 'company_offering_products.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_val: search_val
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#company_offering_products_div').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }

  product_categories = function(search_val) {
    $('#ajax_loader').show();
    $('#product_categories_div').hide();

    $.ajax({
      url: 'product_categories.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_val: search_val
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#product_categories_div').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }

  sub_products = function(search_val) {
    $('#ajax_loader').show();
    $('#sub_products_div').hide();

    $.ajax({
      url: 'sub_products.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_val: search_val
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#sub_products_div').html(res).show();
        common_select();
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
  }

  connected_products = function(search_val) {
    $('#prd_conn_ajax_loader').show();
    $('#connected_products_div').hide();
    
    $.ajax({
      url: 'connected_products.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_val: search_val
      },
      success: function(res) {
        $('#prd_conn_ajax_loader').hide();
        $('#connected_products_div').html(res).show();
        common_select();
        $('.is_allow_downgrade_life_event, .is_allow_upgrade_life_event').uniform();
      }
    });
  }

</script> 

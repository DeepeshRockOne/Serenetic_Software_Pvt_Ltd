var HOSTV = window.location.protocol + "//" + window.location.hostname + "/admin/";
var go_to_next = true;
$(document).ready(function() {

  $('.tblur').on('keyup change', function(e) {
    if (e.keyCode == 9 || e.keyCode == 16) {
      return false;
    }
    var content_id = $(this).attr('id') + '_err';
    var field = 'error_' + $(this).attr('name');
    if ($.trim($(this).val()) == "") {
      $("#" + content_id).removeClass('wrongmark_red rightmark');
      $("#" + content_id).addClass('wrongmark');
    } else {
      $("#" + content_id).removeClass('wrongmark_red wrongmark');
      $("#" + content_id).addClass('rightmark');
      $("#" + field).html('');
    }
  });

  $('.tblur').blur(function(e) {
    var content_id = $(this).attr('id') + '_err';
    var field = 'error_' + $(this).attr('name');
    var error = $(this).attr('data-error');
    var id = $(this).attr('id');
    if ($.trim($(this).val()) == "") {
      $("#" + content_id).removeClass('wrongmark rightmark');
      $("#" + content_id).addClass('wrongmark_red');
      $("#" + field).html(error);
    } else {
      $("#" + content_id).removeClass('wrongmark_red wrongmark');
      $("#" + content_id).addClass('rightmark');
      $("#" + field).html('');
    }
  });

  $(document).off('keyup', '.valid_phone1');
  $(document).on('keyup', '.valid_phone1', function(e) {
    var value = $(this).val();
    var id = $(this).attr('id');
    var res = id.split("_");

    var content_id = "phone1_err";
    var field = 'error_phone1';
    $(id).autotab_magic().autotab_filter('numeric');

    if (e.keyCode == 9) {
      return false;
    }
    $('#phone2,#phone3,#' + id).autotab_magic().autotab_filter('numeric');
    if (value.length < 3 || $('#phone2').val().length < 3 || $('#phone3').val().length < 4) {
      $("#" + content_id).addClass('wrongmark');
      $("#" + content_id).removeClass('rightmark wrongmark_red');
    } else {
      $("#" + content_id).addClass('rightmark');
      $("#" + content_id).removeClass('wrongmark_red wrongmark');
      $('#' + field).html('').hide();
    }
  });

  $(document).off('keyup', '.valid_phone2');
  $(document).on('keyup', '.valid_phone2', function(e) {

    var value = $(this).val();
    var id = $(this).attr('id');
    var res = id.split("_");

    var content_id = "phone1_err";
    var field = 'error_phone1';
    $('#phone1,#phone3,#' + id).autotab_magic().autotab_filter('numeric');
    if (e.keyCode == 9) {
      return false;
    }
    if (value.length < 3 || $('#phone1').val().length < 3 || $('#phone3').val().length < 4) {

      $("#" + content_id).addClass('wrongmark');
      $("#" + content_id).removeClass('rightmark wrongmark_red');
    } else {
      $("#" + content_id).addClass('rightmark');
      $("#" + content_id).removeClass('wrongmark_red wrongmark');
      $('#' + field).html('').hide();
    }
  });

  $(document).off('keyup', '.valid_phone3');
  $(document).on('keyup', '.valid_phone3', function(e) {

    var value = $(this).val();
    var id = $(this).attr('id');
    var res = id.split("_");

    var content_id = "phone1_err";
    var field = 'error_phone1';
    $('#phone1,#phone2,#' + id).autotab_magic().autotab_filter('numeric');

    if (e.keyCode == 9) {
      return false;
    }
    if (value.length < 4 || $('#phone1').val().length < 3 || $('#phone2').val().length < 3) {
      $("#" + content_id).addClass('wrongmark');
      $("#" + content_id).removeClass('rightmark wrongmark_red');
    } else {
      $("#" + content_id).addClass('rightmark');
      $("#" + content_id).removeClass('wrongmark_red wrongmark');
      $('#' + field).html('').hide();
    }
  });

  $(document).off('blur', '.valid_phone1, .valid_phone2 ,.valid_phone3');
  $(document).on('blur', '.valid_phone1 ,.valid_phone2 ,.valid_phone3', function(e) {
    var id = $(this).attr('id');
    var res = id.split("_");
  });

  $('#terms').click(function() {
    $('#form').show();
  });

  $('#agree_btn').click(function() {
    $('#form').hide();
    $('.mfp-bg').css("background", "none");
    $('#checkbox_signup').attr("checked", true);
  })

  $('#password').on("keypress", function() {
    $("#pswd_info").show();
  });

  $("#r_signup").click(function(event) {
    $("#ajax_loader").show();
    $.ajax({
      url: 'ajax_signup_validation.php',
      data: $('#sign_up_form').serialize(),
      type: 'POST',
      success: function(res) {
        $('.error span').html('');
        if (res.status == 'fail') {
          $("#ajax_loader").hide();
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              var offsetTop = offset.top;
              var totalScroll = offsetTop - 195;
              $('body,html').animate({
                scrollTop: totalScroll
              }, 1200);
              is_error = false;
            }
          });
        } else if (res.status == 'success') {
          $('.error span').html('');
          $("#ajax_loader").hide();
          $("#bap1").show();
          $.colorbox({
            inline: true,
            width: "400px",
            height: "225px",
            overlayClose: false,
            closeButton: false,
            href: "#bap1",
            right: '26%'
          });
        }
      }
    });
  });

});

function check_email(obj, e, is_red) {
  var pattern = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i
  var cnt_id = obj.id + '_err';
  var error_cont = 'err_' + obj.id;
  var is_red = is_red || false;
  var email = $(obj).val();
  if (e.keyCode == 9) {
    return false;
  }
  $("#" + cnt_id).html('').hide();
  if ($.trim(email) == "") {
    $("#" + cnt_id).removeClass('rightmark wrongmark wrongmark_red');
    $("#" + cnt_id).addClass('wrongmark' + (is_red ? '_red' : ''));
    if (is_red) {
      $("#" + cnt_id).html("Email is required").show();
      $("#" + obj.id).parent().addClass('field_error');
    }
    return false;
  } else if (!pattern.test(email)) {
    $("#" + cnt_id).removeClass('rightmark wrongmark wrongmark_red');
    $("#" + cnt_id).addClass('wrongmark' + (is_red ? '_red' : ''));
    if (is_red) {
      $("#" + cnt_id).html("Valid email is required").show();
      $('#r_signup').click(function() {
        $("#" + cnt_id).html("Valid email is required").hide();
      });
    }
    return false;
  } else {
    $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
    $("#" + cnt_id).addClass('rightmark');
    $("#" + cnt_id).html('').hide();
    $.ajax({
      url: HOSTV + 'check_email.php',
      data: {
        email: email
      },
      dataType: 'json',
      type: 'post',
      success: function(res) {
        if (res.status == "fail") {
          $("#" + cnt_id).removeClass('rightmark wrongmark wrongmark_red');
          $("#" + cnt_id).addClass('wrongmark' + (is_red ? '_red' : ''));
          if (is_red) {
            $("#" + cnt_id).html(res.errors.email).show();
          }
        } else if (res.status == "success") {
          $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
          $("#" + cnt_id).addClass('rightmark');
          $("#" + cnt_id).html('').hide();
        }
      }
    });
  }
}


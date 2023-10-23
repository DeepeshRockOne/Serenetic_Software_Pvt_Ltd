$(document).ready(function () {
    $('#password').on('focusin click', function () {
        var upperCase = new RegExp('[A-Z]');
        var lowerCase = new RegExp('[a-z]');
        var numbers = new RegExp('[0-9]');
        var special_char1 = new RegExp(/[\\\'?/$*+,"]/g);


        if ($(this).parent().hasClass('field_error')) {
            $(this).parent().addClass('focus');
        }
        $('#pswd_info').show();
        var pswd = $(this).val();
        if (pswd != "") {
            if (pswd.length < 8 || pswd.length > 20) {
                $('#pwdLength').removeClass('valid').addClass('invalid');
            } else {
                $('#pwdLength').removeClass('invalid').addClass('valid');
            }

            
            if (pswd.match(upperCase)) {
                $('#pwdUpperCase').removeClass('invalid').addClass('valid');
            } else {
                $('#pwdUpperCase').removeClass('valid').addClass('invalid');
            }

            if (pswd.match(lowerCase)) {
                $('#pwdLowerCase').removeClass('invalid').addClass('valid');
            } else {
                $('#pwdLowerCase').removeClass('valid').addClass('invalid');
            }

            if (pswd.match(special_char1)) {
                $('#special_char').removeClass('invalid').addClass('valid');
            } else {
                $('#special_char').removeClass('valid').addClass('invalid');
            }

            //validate number
            if (pswd.match(numbers)) {
                $('#pwdNumber').removeClass('invalid').addClass('valid');
            } else {
                $('#pwdNumber').removeClass('valid').addClass('invalid');
            }
        }
    });

});
function check_password_Keyup(pwd_obj, cnt_id, err_field, e) {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');

    if (e.keyCode == 9) {
        return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');

    var special_char1 = new RegExp(/[\\\'?/$*+,"]/g);
    var pswd = $(pwd_obj).val();
    
    if ($(pwd_obj).val().length > 5 && $(pwd_obj).val().length < 21) {
        if (($(pwd_obj).val().match(lowerCase) && $(pwd_obj).val().match(upperCase)) && $(pwd_obj).val().match(numbers)) {
            $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
            $("#" + cnt_id).addClass('rightmark');
            $("#" + err_field).hide();
            $(pwd_obj).parent().removeClass('field_error');
            $("#" + err_field).html('');
        }
        else {
            $("#" + cnt_id).removeClass('wrongmark_red rightmark');
            $("#" + cnt_id).addClass('wrongmark');
        }
    }
    else {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
    }
    if (pswd.length < 8 || pswd.length > 20) {
        $('#pwdLength').removeClass('valid').addClass('invalid');
    } else {
        $('#pwdLength').removeClass('invalid').addClass('valid');
    }
    //validate capital and letter
    if (pswd.match(upperCase)) {
        $('#pwdUpperCase').removeClass('invalid').addClass('valid');
    } else {
        $('#pwdUpperCase').removeClass('valid').addClass('invalid');
    }

    if (pswd.match(lowerCase)) {
        $('#pwdLowerCase').removeClass('invalid').addClass('valid');
    } else {
        $('#pwdLowerCase').removeClass('valid').addClass('invalid');
    }
    //validate number
    if (pswd.match(numbers)) {
        $('#pwdNumber').removeClass('invalid').addClass('valid');
    } else {
        $('#pwdNumber').removeClass('valid').addClass('invalid');
    }    

    if (pswd.match(special_char1)) {
        $('#special_char').removeClass('invalid').addClass('valid');
    } else {
        $('#special_char').removeClass('valid').addClass('invalid');

    }
}
/**
 *
 * @param pwd_obj
 * @param cnt_id
 * @param err_field
 * @param e
 * @param allow_special_char
 * @returns {boolean}
 */
function check_password(pwd_obj, cnt_id, err_field, e,allow_special_char) {

    if(typeof(allow_special_char) === 'undefined') {
        allow_special_char = true;
    }

    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    $(pwd_obj).parent().removeClass('focus');
    $('#pswd_info').hide();
    if (e.keyCode == 9) {
        return false;
    }
    if ($.trim($(pwd_obj).val()) == '') {
        $("#" + cnt_id).removeClass('wrongmark rightmark');
        $("#" + cnt_id).addClass('wrongmark_red');
        $("#" + err_field).show();
        $(pwd_obj).parent().addClass('field_error');
        $("#" + err_field).html('Password is required');
        return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    //<>@!#$%^&*()_+[]{}?:;|'\"\\,./~`-=
    /*var special_char = new RegExp(/[\\\'?/$*+,"]/g);*/
    var special_char = new RegExp(/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\"|\;|\-|\:|\s/g);
    if ($(pwd_obj).val().length > 5 && $(pwd_obj).val().length < 21) {

        if (allow_special_char == false && $(pwd_obj).val().match(special_char)){

            $("#" + cnt_id).removeClass('wrongmark_red rightmark');
            $("#" + cnt_id).addClass('wrongmark');
            $("#" + err_field).show();
            $(pwd_obj).parent().addClass('field_error');
            $("#" + err_field).html('Special character not allowed');

        } else if (($(pwd_obj).val().match(lowerCase) && $(pwd_obj).val().match(upperCase)) && $(pwd_obj).val().match(numbers)) {
            $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
            $("#" + cnt_id).addClass('rightmark');
            $("#" + err_field).hide();
            $("#" + err_field).html('');
        }
        else {
            $("#" + cnt_id).removeClass('wrongmark_red rightmark');
            $("#" + cnt_id).addClass('wrongmark');
            $("#" + err_field).show();
            $(pwd_obj).parent().addClass('field_error');
            $("#" + err_field).html('Password not valid');

        }
    }
    else {
        $("#" + cnt_id).removeClass('wrongmark rightmark');
        $("#" + cnt_id).addClass('wrongmark_red');
        $("#" + err_field).show();
        $(pwd_obj).parent().addClass('field_error');
        $("#" + err_field).html('Passwords must be 8-20 characters');
    }
}

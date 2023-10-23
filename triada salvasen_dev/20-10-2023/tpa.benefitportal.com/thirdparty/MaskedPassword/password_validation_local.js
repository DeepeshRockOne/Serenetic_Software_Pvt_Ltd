	$(document).ready(function() {	
		$('#password').on('focusin click', function() {      
      var upperCase = new RegExp('[A-Z]');
      var lowerCase = new RegExp('[a-z]');
      var numbers = new RegExp('[0-9]');
      if($(this).parent().hasClass('field_error')){
        $(this).parent().addClass('focus');
      }
      $('#pswd_info').show();
			var pswd = $(this).val();
			if(pswd != "")
			{							
				//validate password length
				if ( pswd.length < 6 || pswd.length > 12 ) {
					$('#length').removeClass('valid').addClass('invalid');
				} else {
					$('#length').removeClass('invalid').addClass('valid');
				}
				
        //validate capital and letter
        if (pswd.match(lowerCase) && pswd.match(upperCase)) {
          $('#capital').removeClass('invalid').addClass('valid');
        } else {
          $('#capital').removeClass('valid').addClass('invalid');
        }
				
				//validate number
				if ( pswd.match(numbers) ) {
					$('#number').removeClass('invalid').addClass('valid');
				} else {
					$('#number').removeClass('valid').addClass('invalid');
				}
      }
    });

    $('#safe_code').on('focusin click', function() {            
      var numbers = new RegExp('[0-9]');
      if($(this).parent().hasClass('field_error')){
        $(this).parent().addClass('focus');
      }
      $('#safe_code_info').show();
      var pswd = $(this).val();
      if(pswd != "")
      {             
        //validate password length
        if ( pswd.length < 4 || pswd.length > 6 ) {
          $('#clength').removeClass('valid').addClass('invalid');
        } else {
          $('#clength').removeClass('invalid').addClass('valid');
        }
                        
        //validate number
        if ( pswd.match(numbers) ) {
          $('#cnumber').removeClass('invalid').addClass('valid');
        } else {
          $('#cnumber').removeClass('valid').addClass('invalid');
        }
      }
    });
	});
  function check_password_Keyup(pwd_obj, cnt_id, err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');

    if (e.keyCode == 9) {
      return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    var pswd = $(pwd_obj).val();
    if ($(pwd_obj).val().length > 5 && $(pwd_obj).val().length < 13)
    {
      if (($(pwd_obj).val().match(lowerCase) && $(pwd_obj).val().match(upperCase)) && $(pwd_obj).val().match(numbers))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();
        $(pwd_obj).parent().removeClass('field_error');
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark_red rightmark');
      $("#" + cnt_id).addClass('wrongmark');
    }
    if ( pswd.length < 6 || pswd.length > 12) {
      $('#length').removeClass('valid').addClass('invalid');
    } else {
      $('#length').removeClass('invalid').addClass('valid');
    }    		
    //validate capital and letter
    if (pswd.match(lowerCase) && pswd.match(upperCase)) {
      $('#capital').removeClass('invalid').addClass('valid');
    } else {
      $('#capital').removeClass('valid').addClass('invalid');
    }
    //validate number
    if ( pswd.match(numbers) ) {
      $('#number').removeClass('invalid').addClass('valid');
    } else {
      $('#number').removeClass('valid').addClass('invalid');
    }
  }
  function check_password(pwd_obj, cnt_id,err_field, e)
  {
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
    if ($(pwd_obj).val().length > 5 && $(pwd_obj).val().length < 13)
    {
      if (($(pwd_obj).val().match(lowerCase) && $(pwd_obj).val().match(upperCase)) && $(pwd_obj).val().match(numbers))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();        
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
        $("#" + err_field).show();
        $(pwd_obj).parent().addClass('field_error');
        $("#" + err_field).html('Password not valid');

      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Passwords must be 6-12 characters');
    }
  }

  function con_password_Keyup(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    var step = cont_id.replace('_' + field, '');

    if (e.keyCode == 9) {
      return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    var new_pass = $('#password').val();
    
    if ($(pwd_obj).val() != new_pass)
    {
      $("#" + cnt_id).removeClass('wrongmark_red rightmark');
      $("#" + cnt_id).addClass('wrongmark');
    }
    else
    {
      $("#" + cnt_id).addClass('rightmark');
      $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
      $("#" + err_field).hide();
      $(pwd_obj).parent().removeClass('field_error');
      $("#" + err_field).html('');
    }  
  }
  function con_password(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    var step = cont_id.replace('_' + field, '');
    $(pwd_obj).parent().removeClass('focus');
    if (e.keyCode == 9) {
      return false;
    }
    if ($.trim($(pwd_obj).val()) == '') {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Confirm Password is required');
      return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    var new_pass = $('#password').val();
    //console.log($(pwd_obj).val());
    if ($(pwd_obj).val() != new_pass)
    {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Both Password must be same');
      //console.log("sdf");
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
      $("#" + cnt_id).addClass('rightmark');
      $("#" + err_field).hide();
      $("#" + err_field).html('');
    }   
  }

  //Safe keyCode
  function check_safe_code_Keyup(pwd_obj, cnt_id, err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');

    if (e.keyCode == 9) {
      return false;
    }    
    var numbers = new RegExp('[0-9]');
    var pswd = $(pwd_obj).val();
    if ($(pwd_obj).val().length >= 4 && $(pwd_obj).val().length <= 6)
    {
      if ($(pwd_obj).val().match(numbers))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();
        $(pwd_obj).parent().removeClass('field_error');
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark_red rightmark');
      $("#" + cnt_id).addClass('wrongmark');
    }
    if ( pswd.length < 4 || pswd.length > 6) {
      $('#clength').removeClass('valid').addClass('invalid');
    } else {
      $('#clength').removeClass('invalid').addClass('valid');
    }           
    //validate number
    if ( pswd.match(numbers) ) {
      $('#cnumber').removeClass('invalid').addClass('valid');
    } else {
      $('#cnumber').removeClass('valid').addClass('invalid');
    }
  }
  function check_safe_code(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    $(pwd_obj).parent().removeClass('focus');
    $('#safe_code_info').hide();
    if (e.keyCode == 9) {
      return false;
    }
    if ($.trim($(pwd_obj).val()) == '') {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Safe Code is required');
      return false;
    }    
    var numbers = new RegExp('[0-9]');
    if ($(pwd_obj).val().length >= 4 && $(pwd_obj).val().length <= 6)
    {
      if ($(pwd_obj).val().match(numbers))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();        
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
        $("#" + err_field).show();
        $(pwd_obj).parent().addClass('field_error');
        $("#" + err_field).html('Safe Code is not valid');

      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Safe Code must be 4-6 digits');
    }
  }

  function con_safe_code_Keyup(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    var step = cont_id.replace('_' + field, '');

    if (e.keyCode == 9) {
      return false;
    }    
    var numbers = new RegExp('[0-9]');
    var new_pass = $('#safe_code').val();
    
    if ($(pwd_obj).val() != new_pass)
    {
      $("#" + cnt_id).removeClass('wrongmark_red rightmark');
      $("#" + cnt_id).addClass('wrongmark');
    }
    else
    {
      $("#" + cnt_id).addClass('rightmark');
      $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
      $("#" + err_field).hide();
      $(pwd_obj).parent().removeClass('field_error');
      $("#" + err_field).html('');
    }  
  }
  function con_safe_code(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    var step = cont_id.replace('_' + field, '');
    $(pwd_obj).parent().removeClass('focus');
    if (e.keyCode == 9) {
      return false;
    }
    if ($.trim($(pwd_obj).val()) == '') {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Confirm Safe Code is required');
      return false;
    }    
    var numbers = new RegExp('[0-9]');
    var new_pass = $('#safe_code').val();
    
    if ($(pwd_obj).val() != new_pass)
    {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Both Safe Code must be same');
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
      $("#" + cnt_id).addClass('rightmark');
      $("#" + err_field).hide();
      $("#" + err_field).html('');
    }   
  }  
  // Rep id validation code
  function check_repid_Keyup(pwd_obj, cnt_id, err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');

    if (e.keyCode == 9) {
      return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    var repid = $(pwd_obj).val();
    if (repid.length == 7)
    {     
        var number_character=repid.slice(1,$(pwd_obj).val().length)
        // if (($(pwd_obj).val().match(lowerCase) && $(pwd_obj).val().match(upperCase)) && $(pwd_obj).val().match(numbers))
      if($.isNumeric(number_character) && (repid.charAt(0)=='R' || repid.charAt(0)=='r'))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();
        $(pwd_obj).parent().removeClass('field_error');
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark_red rightmark');
      $("#" + cnt_id).addClass('wrongmark');
    }
    if ( repid.length == 7 ) {  
      $('#r_length').removeClass('invalid').addClass('valid');
    } else {
      
       $('#r_length').removeClass('valid').addClass('invalid');
    }    		
    //validate capital and letter
    if (repid.charAt(0)=='R' || repid.charAt(0)=='r') {
      $('#r_capital').removeClass('invalid').addClass('valid');
    } else {
      $('#r_capital').removeClass('valid').addClass('invalid');
    }
    //validate other digits
     var number_character=repid.slice(1,$(pwd_obj).val().length)
     if($.isNumeric(number_character) && number_character.length == 6)
     {
        $('#r_number').removeClass('invalid').addClass('valid');
     }
     else
     {
        $('#r_number').removeClass('valid').addClass('invalid');
     }
  }
  function check_repid(pwd_obj, cnt_id,err_field, e)
  {
    var cont_id = pwd_obj.id;
    var field = $(pwd_obj).attr('name');
    $(pwd_obj).parent().removeClass('focus');
    $('#rep_info').hide();
    if (e.keyCode == 9) {
      return false;
    }
    if ($.trim($(pwd_obj).val()) == '') {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Invalid Rep ID');
      return false;
    }
    var upperCase = new RegExp('[A-Z]');
    var lowerCase = new RegExp('[a-z]');
    var numbers = new RegExp('[0-9]');
    var repid = $(pwd_obj).val();
    if (repid.length == 7)
    {
        var number_character=repid.slice(1,$(pwd_obj).val().length)
      if ($.isNumeric(number_character) && (repid.charAt(0)=='R' || repid.charAt(0)=='r'))
      {
        $("#" + cnt_id).removeClass('wrongmark_red wrongmark');
        $("#" + cnt_id).addClass('rightmark');
        $("#" + err_field).hide();        
        $("#" + err_field).html('');
      }
      else
      {
        $("#" + cnt_id).removeClass('wrongmark_red rightmark');
        $("#" + cnt_id).addClass('wrongmark');
        $("#" + err_field).show();
        $(pwd_obj).parent().addClass('field_error');
        $("#" + err_field).html('Invalid Rep ID');

      }
    }
    else
    {
      $("#" + cnt_id).removeClass('wrongmark rightmark');
      $("#" + cnt_id).addClass('wrongmark_red');
      $("#" + err_field).show();
      $(pwd_obj).parent().addClass('field_error');
      $("#" + err_field).html('Invalid Rep ID');
    }
  }
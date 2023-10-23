var copy_search_form = false;
$(document).ready(function() {

  $('.notify').click(function() {
    $(this).fadeOut('slow', function() {
      $(this).remove();
    });
  });

  $('.addticketiframe').click(function() {
    $.colorbox({
      iframe: true,
      href: 'add_ticket.php',
      width: '60%',
      height: '80%'
    });
  });

  $(document).off('change', '[id="custom_date"]');
  $(document).on('change', '[id="custom_date"]', function() {
    $(this).closest('form').find('#date_range [name="fromdate"], #date_range [name="todate"]').val('');
    if ($.trim($(this).val()) == "DateRange") {
      $(this).closest('form').find("#date_range").fadeIn('slow');
    } else {
      $(this).closest('form').find("#date_range").fadeOut('slow');
    }
  });

  /*$(window).scroll(function() {
   var curscroll = $(window).scrollTop();
   var windowHeight = $(window).height();
   var documentHeight = $(document).height();
   var diffHeight = documentHeight - windowHeight;
   if (curscroll >= 230) {
   if (!$('#pos_fix_search_form').html()) {
   $('#section_cont').before('<div id="pos_fix_search_form" class="search_sticky"><div id="pos_fix_search_inner" class="serach_sticky_inner">' + $('#search_quick_summary').html() + $('#top_paginate_cont').html() + '</div></div>');
   $('#search_quick_summary').hide();
   $('#top_paginate_cont').hide();
   } else {
   $('#pos_fix_search_form').show();
   $('#search_quick_summary').hide();
   $('#top_paginate_cont').hide();
   }
   if (!copy_search_form) {
   copy_search_form = true;
   count_filter = 0;
   $('#pos_fix_search_form #search_cont form').html($('#search_quick_summary #search_cont form').html());
   $('#search_quick_summary input, #search_quick_summary  select').each(function(key, cnt) {
   $('#pos_fix_search_form [name="' + cnt.name + '"]').val(cnt.value);
   });
   }
   } else {
   if (diffHeight != curscroll) {
   $('#pos_fix_search_form').hide();
   $('#search_quick_summary').slideDown('slow');
   $('#top_paginate_cont').slideDown('slow');
   if (copy_search_form) {
   copy_search_form = false;
   $('#search_quick_summary #search_cont form').html($('#pos_fix_search_form #search_cont form').html());
   $('#pos_fix_search_form input, #pos_fix_search_form select').each(function(key, cnt) {
   $('#search_quick_summary  [name="' + cnt.name + '"]').val(cnt.value);
   });
   }
   }
   }
   });*/

  $(document).on('click', "#viewsummary_btn", function() {
    $('#search_cont').slideUp('slow', function() {
      $('#summary_cont').slideDown('slow');
    });
  });

  $(document).on('click', "#modify_search_btn, #filter_count", function() {
    $('#summary_cont').slideUp('slow', function() {
      $('#search_cont').slideDown('slow');
    });
  });

  $("#gsearch").on("click change keyup", function() {
    if ($.trim($(this).val()).length > 0)
      $('#gsearch_area').slideDown('slow');
    else
      $('#gsearch_area').slideUp('slow');
  }).blur(function() {
    $('#gsearch_area').slideUp('slow');
  });
  $("#gsearch_area li").click(function() {
    var search_type = $(this).attr('data-value');
    $('#gsearch_area_type').val(search_type);
    $('#global_search_form').submit();
  });

});

function copy_search_to_top() {
  search_pagin = $('#search_quick_summary').html();
  if ($('#top_paginate_cont').html()) {
    search_pagin += $('#top_paginate_cont').html();
  }
  $('#section_cont').before('<div id="pos_fix_search_form" class="search_sticky"><div id="pos_fix_search_inner" class="serach_sticky_inner">' + search_pagin + '</div></div>');
  $('section.dashheading').addClass('headerfix');
  $('#section_cont').addClass('sectioncontfix');
  $('#search_quick_summary').hide();
  $('#top_paginate_cont').hide();
  $('#page_title_cont').hide();
}

function deleteconfirm(msg, url) {
  var confirmed = confirm(msg);
  if (confirmed) {
    window.location = url;
    return true;
  }
  return confirmed;
}

function isValidUKPostcode(toCheck) {
  // http://www.braemoor.co.uk/software/postcodes.shtml
  // Permitted letters depend upon their position in the postcode.
  var alpha1 = "[abcdefghijklmnoprstuwyz]"; // Character 1
  var alpha2 = "[abcdefghklmnopqrstuvwxy]"; // Character 2
  var alpha3 = "[abcdefghjkpmnrstuvwxy]"; // Character 3
  var alpha4 = "[abehmnprvwxy]"; // Character 4
  var alpha5 = "[abdefghjlnpqrstuwxyz]"; // Character 5
  var BFPOa5 = "[abdefghjlnpqrst]"; // BFPO alpha5
  var BFPOa6 = "[abdefghjlnpqrstuwzyz]"; // BFPO alpha6

  // Array holds the regular expressions for the valid postcodes
  var pcexp = new Array();

  // BFPO postcodes
  pcexp.push(new RegExp("^(bf1)(\\s*)([0-6]{1}" + BFPOa5 + "{1}" + BFPOa6 + "{1})$", "i"));

  // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA
  pcexp.push(new RegExp("^(" + alpha1 + "{1}" + alpha2 + "?[0-9]{1,2})(\\s*)([0-9]{1}" + alpha5 + "{2})$", "i"));

  // Expression for postcodes: ANA NAA
  pcexp.push(new RegExp("^(" + alpha1 + "{1}[0-9]{1}" + alpha3 + "{1})(\\s*)([0-9]{1}" + alpha5 + "{2})$", "i"));

  // Expression for postcodes: AANA  NAA
  pcexp.push(new RegExp("^(" + alpha1 + "{1}" + alpha2 + "{1}" + "?[0-9]{1}" + alpha4 + "{1})(\\s*)([0-9]{1}" + alpha5 + "{2})$", "i"));

  // Exception for the special postcode GIR 0AA
  pcexp.push(/^(GIR)(\s*)(0AA)$/i);

  // Standard BFPO numbers
  pcexp.push(/^(bfpo)(\s*)([0-9]{1,4})$/i);

  // c/o BFPO numbers
  pcexp.push(/^(bfpo)(\s*)(c\/o\s*[0-9]{1,3})$/i);

  // Overseas Territories
  pcexp.push(/^([A-Z]{4})(\s*)(1ZZ)$/i);

  // Anguilla
  pcexp.push(/^(ai-2640)$/i);

  // Load up the string to check
  var postCode = toCheck;

  // Assume we're not going to find a valid postcode
  var valid = false;

  // Check the string against the types of post codes
  for (var i = 0; i < pcexp.length; i++) {

    if (pcexp[i].test(postCode)) {

      // The post code is valid - split the post code into component parts
      pcexp[i].exec(postCode);

      // Copy it back into the original string, converting it to uppercase and inserting a space 
      // between the inward and outward codes
      postCode = RegExp.$1.toUpperCase() + " " + RegExp.$3.toUpperCase();

      // If it is a BFPO c/o type postcode, tidy up the "c/o" part
      postCode = postCode.replace(/C\/O\s*/, "c/o ");

      // If it is the Anguilla overseas territory postcode, we need to treat it specially
      if (toCheck.toUpperCase() == 'AI-2640') {
        postCode = 'AI-2640'
      };

      // Load new postcode back into the form element
      valid = true;

      // Remember that we have found that the code is valid and break from loop
      break;
    }
  }

  // Return with either the reformatted valid postcode or the original invalid postcode
  if (valid) {
    return postCode;
  } else
    return false;
}
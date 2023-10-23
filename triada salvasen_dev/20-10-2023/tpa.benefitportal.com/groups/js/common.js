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

function deleteconfirm(msg, url)
{
  var confirmed = confirm(msg);
  if (confirmed) {
    window.location = url;
    return true;
  }
  return confirmed;
}

function ajax_loader(view) {
if (view)
  $('#ajax_loader').show();
else
  $('#ajax_loader').hide();
}
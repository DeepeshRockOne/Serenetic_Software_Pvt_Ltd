<script type="text/javascript">
  // Agent level change
  $(document).off('change', '#agent_level');
  $(document).on('change', '#agent_level', function(e) {
    e.stopPropagation();
    var id = '<?=$_GET['id']?>';
    var level = $(this).val();
    var level_id = $("#agent_level option:selected").attr('data-id');
    var old_level_id = $(this).attr('data-old_lvl_id');
    var old_val = $(this).attr('data-old_value');
    // if(level_id !== old_level_id){
      swal({
        text: "Change Level: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
      }).then(function() {
        window.location = 'ajax_agent_level_change.php?agent_id=' + id + '&level_id=' + level_id+"&level="+level;
      }, function(dismiss) {
          $("#agent_level").val(old_val);
          $("#agent_level").selectpicker('render');
      });
    // }
  });
$(document).off('click',".agent_tree_popup");
$(document).on('click',".agent_tree_popup",function(e){
    $href = $(this).attr('data-href');
    $.colorbox({
        iframe:true,
        href:$href,
        width: '900px',
        height: '650px'
    });
});
  //agent status change
  $(document).off('click', '.btn_licensed_approval');
  $(document).on("click", ".btn_licensed_approval", function(e) {
      $.colorbox({
          href: "agent_review_license.php?id=<?=$agent_id?>",
          iframe: true,
          width: '700px',
          height: '500px'
      });
  });

  $(document).off('click', '.member_status');
  $(document).on("click", ".member_status", function(e) {
      e.stopPropagation();
      var id = '<?=$_GET['id']?>';
      var member_status = $(this).attr('data-status');
      var old_status = "<?=$row['status']?>";
      var $txt = '';
      if(member_status === 'Active'){
          $txt = 'Contracted Status: Contracted status allows agent to login to account, continues payment of renewal commissions, and allows new applications.';
      }else if(member_status === 'Suspended'){
          $txt = 'Suspended Status: Suspended status allows agent to login to account, continues payment of renewal commissions, but stops new applications.'
      }else if(member_status === 'Terminated'){
          $txt = 'Terminated Status: Terminated status blocks agent access to login to account, stops payment of renewal commissions, and stops new applications.';
      }
      
      swal({
          title: "<h4>Agent Status Change to <span class='text-blue'>"+member_status+"</span>: Are you sure?</h4>",
          html:'<p class="fs14 m-b-15">'+$txt+'</p><div class="text-center fs14"><div class="d-inline  text-left"><label class="m-b-10 "><input name="downline" type="checkbox" value="downline" id="downline" class="js-switch" autofocus> <span class="p-l-10"> Apply to downline agents? </span> </label><div class="clearfix"></div>' + '<label class="m-b-0" ><input name="loa" type="checkbox" value="loa" id="loa" class="js-switch"> <span class="p-l-10"> Apply to LOA agents?</span></label></div></div>',
          showCancelButton : true,
          confirmButtonText: "Confirm",
          cancelButtonText: "Cancel",
      }).then(function(e1) {
        if(e1){
          var $downline = '';
          if($("#downline").is(":checked")){
              var $downline = $("#downline").val();
          }
          var $loa = '';
          if($("#loa").is(":checked")){
              $loa = $("#loa").val();
          }
          if (member_status == 'Terminated' || member_status == 'Suspended') {
              $.colorbox({
                  iframe: true,
                  href: "<?=$ADMIN_HOST?>/reason_change_agent_status.php?id=" + id + "&status=" + member_status + "&downline="+$downline+"&loa="+$loa+"&old_status="+old_status+"&from=detail_page",
                  width: '600px',
                  height: '260px',
                  trapFocus: false,
                  closeButton: false,
                  overlayClose: false,
                  escKey: false
              });
          } else {
              $.ajax({
                  url: 'ajax_change_agent_status.php',
                  data: {
                      id: id,
                      downline:$downline,
                      loa:$loa,
                      status: member_status
                  },
                  method: 'POST',
                  dataType: 'json',
                  success: function(res) {
                      if (res.status == "success") {
                          setNotifySuccess(res.msg);
                          location.reload();
                      }else{
                          setNotifyError(res.msg);
                      }
                  }
              });
          }
        }
      }, function(dismiss) {
      })
  });

  $(document).off('click','#show_reason');
  $(document).on('click','#show_reason',function(e){
    $("#profile_table").hide();
    $(".reason_info").show();
    $("#close_reason").show();
  });

  $(document).off('click','#close_reason');
  $(document).on('click','#close_reason',function(e){
    $(this).hide();
    $("#profile_table").show();
    $(".reason_info").hide();
  });

  /*scroll div function start */
  function scrollToDiv(element, navheight,url,ajax_div,scroll_to) {
  var str = $("#"+ajax_div).html().trim();
    if(str === '' && url!==''){
    ajax_get_agent_data(url,ajax_div);
      setTimeout(function(){
        if(scroll_to === 'agp_attributes' || scroll_to === 'agp_brand_links'){
          scrollToDiv(element, navheight,url,scroll_to);
          if(scroll_to === 'agp_attributes')
            $("#data_agp_attributes").click();
          else if( scroll_to === 'agp_brand_links')
          $("#data_agp_brand_links").click();
          return false;
        }
      }, 1500);
    }
    if ($(element).length) {
      var offset = element.offset();
      var offsetTop = offset.top;
      var totalScroll = offsetTop - navheight;
      if ($(window).width() >= 1171) {
        var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 42
      } else {
        var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 42
      }
      $('body,html').animate({
        scrollTop: totalScroll
      }, 1200);
    }
  }
  $(document).on('click','#click_to_show',function(){
    if($("#ad_password").attr('type') === 'password')
    {
      $("#password_popup").show();
    }else{
      $("#ad_password").attr('type','password');
      $("#ad_password").val('<?=base64_encode($password)?>');
    }
  });

  $(document).on('click','#show_password',function(){
    if($("#showing_pass").val() === '5401')
    {
      $("#ajax_loader").show();
      $("#showing_pass").val("");
      $("#password_popup").hide();
      var id = '<?=$_GET['id']?>';
      $.ajax({
        url:'agent_detail_v1.php',
        method : 'POST',
        data : {id:id,show_pass:"show_pass"},
        success:function(){
          $("#ajax_loader").hide();
          $("#ad_password").attr('type','text');
          $("#ad_password").val('<?=$password?>');
        }
      });
    }else{
        $("#password_popup").hide();
      }
  });

      var not_win = '';
      $(document).off('click',".account_note_popup_new");
      $(document).on('click',".account_note_popup_new",function(){
        $href = $(this).attr('data-href');
        var not_win = window.open($href, "_blank", "width=500,height=600");
        if(not_win.closed) {  
          alert('closed');  
        } 
      });
    $(document).ready(function() {
      $('#srh_btn_interaction').click(function(e){
        e.preventDefault(); //to prevent standard click event
        $(this).hide();
        $("#srh_close_btn_interaction").show();
        $("#search_interaction").slideDown();    
        $('.activity_wrap').addClass('interaction_filter_active');
        $('.activity_wrap').mCustomScrollbar("update");		
      });
      $('#srh_btn_note').click(function(e){
        e.preventDefault(); //to prevent standard click event
        $(this).hide();
        $("#srh_close_btn_note").show();
        $("#search_note").slideDown();
        $('.activity_wrap').addClass('interaction_filter_active');
        $('.activity_wrap').mCustomScrollbar("update");		
      });
      $('#srh_close_btn_interaction').click(function(e){
        e.preventDefault(); //to prevent standard click event
         $("#search_interaction").slideUp();
         $("#srh_close_btn_interaction").hide();
         $("#srh_btn_interaction").show();
         $('.activity_wrap').removeClass('interaction_filter_active');
         $('.activity_wrap').mCustomScrollbar("update");
         var id = '<?=$_GET['id']?>';
         interactionUpdate(id,'interaction','agent_detail_v1.php');
      });
      $('#srh_close_btn_note').click(function(e){
        e.preventDefault(); //to prevent standard click event
       $("#search_note").slideUp();
       $("#srh_close_btn_note").hide();
       $("#srh_btn_note").show();
       $('.activity_wrap').removeClass('interaction_filter_active');
       $('.activity_wrap').mCustomScrollbar("update");
         var id = '<?=$_GET['id']?>';
         interactionUpdate(id,'notes','agent_detail_v1.php');
      });

      $("#note_search_keyword").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".activity_wrap_note div.media").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });

      $("#interaction_search_keyword").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".activity_wrap_interaction div.media").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });

    $(document).on('click','.interactions_note',function(e){
        var $href = $(this).attr('data-href');
        $.colorbox({
          href:$href,
          iframe: true,
          width: '800px',
          height: '500px'
          });
    });
    
    $(".activity_wrap").mCustomScrollbar({
        theme:"dark"
    });
    
      $('#dt_range').css({ display: 'none' });
    $('#search_activty').css({ display: 'none' });
    
      $('#acc_his_custom_date').change(function(){
        if($('#acc_his_custom_date').val() == 'Range') {
          $('#dt_range').css({ display: 'inline-block' });
          $("#from_date").show();
          $(".to_date").show();
        } else if($('#acc_his_custom_date').val() == ''){
          $('#dt_range').css({ display: 'none' });
        }else{
          $("#from_date").hide();
          $('#dt_range').css({ display: 'inline-block' });
          $(".to_date").hide();
        }
      });
    });

  function refreshCurrencyFormatter(){
    $("#e_o_amount").formatCurrency({
      colorize: true,
      negativeFormat: '-%s%n',
      roundToDecimalPlace: 0
    });
  }

  function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
        }
        return true;
    }

  // $('#e_o_amount').blur
  $(document).on('keyup','#e_o_amount',function(e) {
          var e = window.event || e;
          var keyUnicode = e.charCode || e.keyCode;
          if (e !== undefined) {
            switch (keyUnicode) {
              case 16:
                break; // Shift
              case 17:
                break; // Ctrl
              case 18:
                break; // Alt
              case 27:
                this.value = '';
                break; // Esc: clear entry
              case 35:
                break; // End
              case 36:
                break; // Home
              case 37:
                break; // cursor left
              case 38:
                break; // cursor up
              case 39:
                break; // cursor right
              case 40:
                break; // cursor down
              case 78:
                break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
              case 110:
                break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
              case 190:
                break; // .
              default:
                $(this).formatCurrency({
                  colorize: true,
                  negativeFormat: '-%s%n',
                  roundToDecimalPlace: -1,
                  eventOnDecimalsEntered: true
                });
            }
          }
        })
        /*.bind('decimalsEntered', function(e, cents) {
          if (String(cents).length > 2) {
            var errorMsg = 'Please do not enter any cents (0.' + cents + ')';
            $('#formatWhileTypingAndWarnOnDecimalsEnteredNotification2').html(errorMsg);
            log('Event on decimals entered: ' + errorMsg);
          }
        })*/;

  function refreshControl(id_class)
  {
    $(id_class).addClass('form-control');
    $(id_class).selectpicker({ 
        container: 'body', 
        style:'btn-select',
        noneSelectedText: '',
        dropupAuto:false,
    });
  }

  function delete_interaction(interaction_id) {
    var id = '<?=$_GET['id']?>';
    var url = "";
    url = "agent_detail_v1.php";
    swal({
      text: "Delete Interaction: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      cancelButtonText: "Cancel",
    }).then(function () {
      $.ajax({
        url: 'ajax_interaction_add.php',
        data: {
          type : "delete",
          interaction_detail_id: interaction_id,
          agent_id : '<?=$_GET['id']?>'
        },
        dataType: 'json',
        type: 'post',
        success: function (res) {
          if (res.status == "success") {
            // window.location = url + '?id=' + id;
            interactionUpdate(id,'interaction','agent_detail_v1.php');
            setNotifySuccess('Interaction deleted successfully.');
          }
        }
      });
    }, function (dismiss) {

    });
  }

  $(document).off("click",".interaction_class");
  $(document).on("click",".interaction_class",function(e){
    $(".note_div").hide();
    $(".interaction_div").show();
  });

  $(document).off("click",".note_class");
  $(document).on("click",".note_class",function(e){
    $(".interaction_div").hide();
    $(".note_div").show();
  });

  $(document).off("click",".agent_license_class");
  $(document).on("click",".agent_license_class",function(e){
    $(".interaction_div").hide();
    $(".note_div").hide();
  });

  function edit_note_agent(note_id, t) {
    var user_type = $("#edit_note_id").attr("data-value");
    var show = "";
    if(t === 'view')
    {
      show = "show";
    }
    var customer_id = '<?=$_GET['id']?>';
    url = "agent_detail_v1.php";
    if (user_type == 'View' || user_type == 'Agent') {
      $.colorbox({
        iframe: true,
        width: '800px',
        height: '400px',
        href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
      });
    } else {
      window.location.href = url + "?id=" + '<?=$_GET['id']?>' +"&note_id=" + note_id;
    }
  }

  function delete_note(note_id, activity_feed_id) {
    var id = '<?=$_REQUEST['id']?>';
    var url = "";
    url = "agent_detail_v1.php";
    swal({
      text: "Delete Note: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
    }).then(function () {
      $.ajax({
        url: 'ajax_general_note_delete.php',
        data: {
          note_id: note_id,
          activity_feed_id: activity_feed_id,
          usertype:'Agent',
          user_id :id,
        },
        dataType: 'json',
        type: 'post',
        success: function (res) {
          if (res.status == "success") {
            // window.location = url + '?id=' + id;
            interactionUpdate(id,'notes','agent_detail_v1.php');
            setNotifySuccess('Note deleted successfully.');
          }
        }
      });
    }, function (dismiss) {

    });
  }
  $(document).off('click','#search_btn_note');
  $(document).on('click','#search_btn_note',function(){
    $("#ajax_loader").show();
    var note_search_keyword = $("#note_search_keyword").val();
    var id = '<?=$_GET['id']?>';
    if(note_search_keyword!==''){
    $.ajax({
      url:'agent_detail_v1.php?id='+id,
      data:{note_search_keyword:note_search_keyword,id:id},
      method:'post',
      dataType: 'html',
      success:function(res){
        $("#ajax_loader").hide();
        $("#note_tab").html(res);
        $(".activity_wrap").mCustomScrollbar({
        theme:"dark"
        });
      }
    });
    }else{
      alert("Please Enter Search Keyword(s)");
      $("#ajax_loader").hide();
    }
  });

  $(document).off('click','#search_btn_interaction');
  $(document).on('click','#search_btn_interaction',function(){
    $("#ajax_loader").show();
    var interaction_search_keyword = $("#interaction_search_keyword").val();
    var id = '<?=$_GET['id']?>';
    if(interaction_search_keyword!==''){
    $.ajax({
      url:'agent_detail_v1.php?id='+id,
      data:{interaction_search_keyword:interaction_search_keyword,id:id},
      method:'post',
      dataType: 'html',
      success:function(res){
        $("#ajax_loader").hide();
        $("#interactions_tab").html(res);
        $(".activity_wrap").mCustomScrollbar({
        theme:"dark"
        });
      }
    });
    }else{
      alert("Please Enter Search Keyword(s)");
      $("#ajax_loader").hide();
    }
  });

  $(document).off('click',"#advances_history");
  $(document).on('click',"#advances_history",function(e){
    $.colorbox({
      inline:true,
      href:"#advance_history_div",
      width: "525px",
      height: "300px",
      fixed:true,
      closeButton: true,
    });
  });

  $(document).off('change','#advances');
  $(document).on('change','#advances',function(e){
      if($(this).is(":checked") === true){
        change_advance_commission_settings('Y');
      }else{
        change_advance_commission_settings('N');
      }
  });

  function change_advance_commission_settings($is_on) {
    var id = <?=$row['_id']?>;
    swal({
      text: "Change Advance Commission: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      cancelButtonText: "Cancel",
    }).then(function () {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_agent_commission_setting.php',
        data: {
          type:'advance',
          agent_id: id,
          is_on:$is_on,
        },
        dataType: 'json',
        type: 'post',
        success: function (res) {
          if (res.status == "success") {
            window.location = "agent_detail_v1.php" + '?id=<?=$row['id']?>';
          }
        }
      });
    }, function (dismiss) {
      if($is_on == 'Y'){
        $("#advances").prop("checked",false);
      }else{
        $("#advances").prop("checked",true);
      }
    });
  }

  $(document).off('click',"#graded_history");
  $(document).on('click',"#graded_history",function(e){
    $.colorbox({
      inline:true,
      href:"#graded_history_div",
      width: "525px",
      height: "300px",
      fixed:true,
      closeButton: true,
    });
  });

  $(document).off('change','#graded');
  $(document).on('change','#graded',function(e){
      if($(this).is(":checked") === true){
        change_graded_commission_settings('Y');
      }else{
        change_graded_commission_settings('N');
      }
  });

  function change_graded_commission_settings($is_on) {
    var id = <?=$row['_id']?>;
    swal({
      text: "Change Graded Commission: Are you sure?",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      cancelButtonText: "Cancel",
    }).then(function () {
      $("#ajax_loader").show();
      $.ajax({
        url: 'ajax_agent_commission_setting.php',
        data: {
          type:'graded',
          agent_id: id,
          is_on:$is_on,
        },
        dataType: 'json',
        type: 'post',
        success: function (res) {
          if (res.status == "success") {
            window.location = "agent_detail_v1.php" + '?id=<?=$row['id']?>';
          }
        }
      });
    }, function (dismiss) {
      if($is_on == 'Y'){
        $("#graded").prop("checked",false);
      }else{
        $("#graded").prop("checked",true);
      }
    });
  }

  ajax_get_agent_data = function(url,ajax_div){
    $.ajax({
      url : url,
      type : 'POST',
      data:{
        id:'<?=$_GET['id']?>'
      },
      beforeSend :function(e){
        $("#ajax_loader").show();
      },
      success : function(res){
        $("#ajax_loader").hide();
        $("#"+ajax_div).html(res);
        $('#is_branding, #display_in_member, #special_display').uniform();
        autoResizeNav();
        if(ajax_div === 'agent_account_detail_div'){
            $("#account_detail :input").each(function(e){
              if($(this).val() !== ''){
                $(this).addClass('has-value');
              }
            })
            // common_select();
        }
      }
    });
  }
  
function initAutocomplete() {              

var input = document.getElementById('address');
var options = {
    types: ['geocode'],
    componentRestrictions: {country: 'us'}
};

autocomplete = new google.maps.places.Autocomplete(input, options);

autocomplete.setFields(['address_component']);

autocomplete.addListener('place_changed', fillInAddress);
}

function initAutocompleteAgency() {              

var input = document.getElementById('business_address');
var options = {
types: ['geocode'],
componentRestrictions: {country: 'us'}
};

autocomplete_Agency = new google.maps.places.Autocomplete(input, options);

autocomplete_Agency.setFields(['address_component']);

autocomplete_Agency.addListener('place_changed', fillInAddressAgency);
}

//google map api for address start
function fillInAddress() {
// $("#is_valid_address").val('N');
var place = autocomplete.getPlace();
var address = "";
var zip = "";
var city = "";
var state = "";
console.log(place);
/* var defaultZip = $("#account_detail #zip").val(); */
$(".error").html('');
for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if(addressType == "street_number"){
    var val = place.address_components[i]["short_name"];
        address = address + " "+ val;
    }else if(addressType=="route"){
    var val = place.address_components[i]["long_name"];
    address = address + " "+ val;
    }else if(addressType=="postal_code"){
    zip = place.address_components[i]["short_name"];
    }else if(addressType=="locality"){
    city = place.address_components[i]["short_name"];
    }else if(addressType == "administrative_area_level_1"){
    state = place.address_components[i]["long_name"];
    }
}
    $("#account_detail #zipcode").val(zip);
    $("#account_detail #address").val(address);
    $("#account_detail #city").val(city);
    $("#account_detail #state").val(state).change();
  /* $("#is_valid_address").val('Y'); */
}

function fillInAddressAgency() {
// $("#is_valid_address").val('N');
var place = autocomplete_Agency.getPlace();
var address = "";
var zip = "";
var city = "";
var state = "";
console.log(place);
/* var defaultZip = $("#account_detail #business_zipcode").val(); */
$(".error").html('');
for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if(addressType == "street_number"){
    var val = place.address_components[i]["short_name"];
        address = address + " "+ val;
    }else if(addressType=="route"){
    var val = place.address_components[i]["long_name"];
    address = address + " "+ val;
    }else if(addressType=="postal_code"){
    zip = place.address_components[i]["short_name"];
    }else if(addressType=="locality"){
    city = place.address_components[i]["short_name"];
    }else if(addressType == "administrative_area_level_1"){
    state = place.address_components[i]["long_name"];
    }
}
    $("#account_detail #business_zipcode").val(zip);
    $("#account_detail #business_address").val(address);
    $("#account_detail #business_city").val(city);
    $("#account_detail #business_state").val(state).change();
    /* $("#is_valid_address").val('Y'); */

}
//google map api for address end

$(window).on('resize load', function(){
   if ($(window).width() <= 1170) {
      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
      autoResizeNav();
   }
});

function autoResizeNav(){
   if ($('.nav-tabs:not(.nav-noscroll)').length){
      ;(function() {
        'use strict';
         $(activate);
         function activate() {
         $('.nav-tabs:not(.nav-noscroll)')
           .scrollingTabs({
               scrollToTabEdge: true,
               enableSwiping: true  
            })
        }
      }());
   }
}
</script>
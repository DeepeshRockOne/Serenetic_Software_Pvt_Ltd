<div class="panel panel-default panel-block advance_info_div">
   <div class="panel-body">
      <div class="phone-control-wrap ">
         <div class="phone-addon w-90 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="85px">
         </div>
         <div class="phone-addon text-left">
            <p>Under a list-bill arrangement, a single bill is generated for each group members personal health plan and paid by the group. Below are the open and closed list bills to date with management options to assist.   To make an adjustment to an open list bill, click on the button below. </p>
            <a href="listbill_adjustment.php" class="btn btn-action">+ Adjustment</a>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <h4 class="m-t-0 ">Open List Bills</h4>
      <div class="clearfix m-b-15 tbl_filter">
         <div class="pull-left">
            <div class="form-inline">
               <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
               </div>
               <div class="form-group mn">
                  <select id="sdfsdf" data-search="open_list_bills" class="form-control records_per_page" >
                     <option value="" data-hidden="true"></option>
                     <option value="10">10</option>
                     <option value="25" selected="selected">25</option>
                     <option value="50">50</option>
                     <option value="100">100</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="open_list_bills_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray" data-close="open_list_bills">X</a>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="list_bill_id_open_list_bills" value="" >
                        <label>List Bill ID</label>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="group_id_open_list_bills" value="" >
                        <label>Group ID</label>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <select class="form-control" id="status_open_list_bills">
                           <option></option>
                           <option value="open">Open</option>
                        </select>
                        <label>Status</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button" data-search="open_list_bills">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn v-align-top" id="search_open_list_bills" data-tab="open_list_bills"><i class="fa fa-search fa-lg text-blue"></i></a>
            <?php if($SITE_ENV !='Live' || ($SITE_ENV =='Live' && in_array($admin_display_id,array('AD6538')))){ ?>
               <a href="generate_manually_listbill.php" class="btn btn-action v-align-top" id="generate_manually_listbill">Generate List Bill</a>
            <?php } ?>
            <a class="btn btn-default m-r-5 v-align-top" href="manage_listbills.php"> Manage List Bills</a>
            <?php if($SITE_ENV != 'Live'){?>
            <div class="d-inline">
               <div class="phone-control-wrap theme-form">
                  <div class=" phone-addon w-160">
                     <div class="form-group  mn">
                        <div class="input-group"> 
                           <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                           <div class="pr">
                              <input type="text" name="paylbDate" id="paylbDate" value="" class="form-control date_picker" />
                              <label>Date</label>
                           </div>
                        </div>
                        <p class="error" id="error_paylbDate"></p>
                     </div>
                  </div>
                  <div class="phone-addon v-align-top">
                     <a class="btn btn-default m-r-5" id ="payListBill" href="javascript:void(0);"> Pay List Bill</a>
                  </div>
               </div>
            </div>
            <?php }?>
         </div>
      </div>
      <div id="open_list_bills_div">
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <h4 class="m-t-0 ">Closed List Bills</h4>
      <div class="clearfix m-b-15 tbl_filter">
         <div class="pull-left">
            <div class="form-inline">
               <div class="form-group mn">
                  <label for="user_type">Records Per Page </label>
               </div>
               <div class="form-group mn">
                  <select id="sdfsdf" data-search="close_list_bills" class="form-control records_per_page" >
                     <option value="" data-hidden="true"></option>
                     <option value="10">10</option>
                     <option value="25" selected="selected">25</option>
                     <option value="50">50</option>
                     <option value="100">100</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="pull-right">
            <div class="note_search_wrap auto_size" id="close_list_bills_search_div" style="display: none; max-width: 100%;">
               <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray" data-close="close_list_bills">X</a>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="list_bill_id_close_list_bills" value="" >
                        <label>List Bill ID</label>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <input type="text"  class="form-control" id="group_id_close_list_bills" value="" >
                        <label>Group ID</label>
                     </div>
                  </div>
                  <div class="phone-addon w-130">
                     <div class="form-group height_auto mn">
                        <select class="form-control" id="status_close_list_bills">
                           <option></option>
                           <option value="paid">Paid</option>
                           <option value="void">Void</option>
                           <option value="Cancelled">Cancelled</option>
                        </select>
                        <label>Status</label>
                     </div>
                  </div>
                  <div class="phone-addon w-80">
                     <div class="form-group height_auto mn">
                        <a href="javascript:void(0);" class="btn btn-info search_button" data-search="close_list_bills">Search</a>
                     </div>
                  </div>
               </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="search_close_list_bills" data-tab="close_list_bills"><i class="fa fa-search fa-lg text-blue"></i></a>
         </div>
      </div>
      <div id="close_list_bills_div">
      </div>
   </div>
</div>
<script type="text/javascript">

$(document).on('change', 'select.listbill_action', function (e) {
  e.preventDefault();
  $id = $(this).find(':selected').attr('data-id');
  $val = $(this).val();
  if ($val == 'void_select') {
      $list_bill_no = $(this).find(':selected').attr('data-list-bill-no');
      $group_name = $(this).find(':selected').attr('data-group-name');
      swal({
         text: "<br>Void List Bill#- <b>"+$list_bill_no+"</b> ("+$group_name+"?): Are you sure?",
         showCancelButton: true,
         confirmButtonText: "Confirm",
       }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                  url: '<?=$HOST?>/ajax_void_list_bill.php',
                  data: {id: $id},
                  dataType: 'JSON',
                  type: 'POST',
                  success: function (res) {
                     $("#ajax_loader").show();
                     setNotifySuccess(res.msg);
                     open_list_bills();
                     close_list_bills();
                  }
            });
       }, function (dismiss) {
           
       });
  }else if($val == "View" || $val=="Amend" || $val=="Receipt" || $val == "download"){
      $href = $(this).find(':selected').attr('data-href');
      window.open($href, "_blank");
  }else if($val == "Pay"){
      $href = $(this).find(':selected').attr('data-href');
      $.colorbox({
         iframe: true,
         width: '800px',
         height: '500px',
         href: $href
      });
  }
  $(this).val('');
  $(".listbill_action").selectpicker('refresh');
});

$(document).ready(function(){
   open_list_bills();
   close_list_bills();
   dropdown_pagination('close_list_bills_div','open_list_bills_div')

});

//******************** General Search Code Start  **********************
   $(document).off("click", ".search_btn");
   $(document).on("click", ".search_btn", function(e) {
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

      if (tabs == 'open_list_bills') {
        open_list_bills('','','','');
      }else if (tabs == 'close_list_bills') {
        close_list_bills('','','','');
      }
   });

   $(document).off("click", ".search_button");
   $(document).on("click", ".search_button", function(e) {
       e.preventDefault();
       var search = $(this).attr('data-search');
       var search_val = $('#' + search).val();
       var search_list_bill_id = $("#list_bill_id_"+search).val();
       var search_group_id = $("#group_id_"+search).val();
       var search_status = $("#status_"+search).val();
      
      
       if (search == 'open_list_bills') {
         open_list_bills(search_list_bill_id,search_group_id,search_status);
       }else if (search == 'close_list_bills') {
         close_list_bills(search_list_bill_id,search_group_id,search_status);
       } 
   });

   $(document).off("change", ".records_per_page");
   $(document).on("change", ".records_per_page", function(e) {
       e.preventDefault();
       var search = $(this).attr('data-search');
       var records_per_page = $(this).val();
      
       if (search == 'open_list_bills') {
         open_list_bills('','','',records_per_page);
       }else if (search == 'close_list_bills') {
         close_list_bills('','','',records_per_page);
       } 
   });
//******************** General Search Code End    **********************

open_list_bills = function(search_list_bill_id,search_group_id,search_status,records_per_page) {
    $('#ajax_loader').show();
    $('#open_list_bills_div').hide();
    $.ajax({
      url: 'open_list_bills.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_list_bill_id: search_list_bill_id,
        search_group_id: search_group_id,
        search_status: search_status,
        pages: records_per_page,
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#open_list_bills_div').html(res).show();
        common_select();
      }
    });
}

close_list_bills = function(search_list_bill_id,search_group_id,search_status,records_per_page) {
    $('#ajax_loader').show();
    $('#close_list_bills_div').hide();
    $.ajax({
      url: 'close_list_bills.php',
      type: 'GET',
      data: {
        is_ajaxed: 1,
        search_list_bill_id: search_list_bill_id,
        search_group_id: search_group_id,
        search_status: search_status,
        pages: records_per_page,
      },
      success: function(res) {
        $('#ajax_loader').hide();
        $('#close_list_bills_div').html(res).show();
        common_select();
      }
    });
}
$(".date_picker").datepicker({
    changeDay: true,
    changeMonth: true,
    changeYear: true
});
$(document).on("click", "#generate_manually_listbill", function(e) {
    e.preventDefault();
    $.colorbox({
        href:'generate_manually_listbill.php',
          iframe:true,
          width:"470px;",
          height:"385px;"
      });
});

$(document).off('click','#payListBill');
$(document).on('click','#payListBill',function(e){
   e.preventDefault();
   var date = $('#paylbDate').val(); 
   $.ajax({
    url: 'test_auto_pay_list_bill.php?date='+date,
    type: 'GET',
    success: function(res) {
      if(res.status == 'fail'){
         $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
         });
      }else if(res.status == 'success'){
         window.location.reload();
      }
    }
  });
  return false;
});
</script>
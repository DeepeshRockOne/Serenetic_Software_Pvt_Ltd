<div class="panel panel-default panel-block">
  <div class="panel-body">
    <form class="theme-form" id="sms_unsubscribes_form" action="sms_unsubscribes.php">
        <input type="hidden" id="sms_ajaxed" name="sms_ajaxed" value="">
        <h4 class="m-t-7">Text Message (SMS) Unsubscribes </h4>
         <div class="clearfix tbl_filter">
         <div class="pull-left">
    <?php if ($total_rows > 0) {?>
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group height_auto mn">
                    <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group height_auto mn">
                    <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);sms_unsubscribes_submit();">
                        <option value="10" <?php echo isset($_GET['pages']) && $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo (isset($_GET['pages']) && $_GET['pages'] == 25) || (isset($_GET['pages']) && $_GET['pages'] == "") ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo isset($_GET['pages']) && $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo isset($_GET['pages']) && $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
            </div>
              <?php } ?>
        </div>
    <div class="pull-right">
          <div class="m-b-15">
            <div class="note_search_wrap auto_size" id="sms_search_div" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                  <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                  </div>
                </div>
                <div class="phone-addon w-200">
                  <div class="form-group height_auto mn">
                  <input type="text" class="form-control" name="phone" id="phone" maxlength="10" onkeypress="return isNumberKey(event)" value="<?=!empty($phone) ? $phone : ''?>">
                  <label>Phone Number</label>
                  </div>
                </div>
                 <div class="phone-addon w-200">
                   <div class="form-group height_auto mn">
                    <input type="text" name="phone_added_date" id="phone_added_date" value="<?=!empty($phone_added_date) ? $phone_added_date : ''?>" class="form-control phone_date_picker" />
                    <label>Added Date</label>
                  </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                  <a href="javascript:void(0);" class="btn btn-info search_button" onclick="sms_unsubscribes_submit();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="sms_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0);" id="unsubscribe_phone_btn" class="btn btn-action disabled m-l-5" style="display:inline-block;" > Remove</a>
          </div>
        </div>
   </div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th width="42%">Added Date</th>
            <th>Phone </th>
            <th width="100px">
              <div class="checkbox checkbox-custom mn">
                <input type="checkbox" class="js-switch" id='remove_all_phone'/>
                <label for="">Select</label>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
            <?php if ($total_rows > 0) {
                foreach ($fetch_rows as $rows) { ?>
          <tr>
            <td><?=date("m/d/Y",strtotime($rows['added_date']))?></td>
            <td> <?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $rows['phone']) ?>
            <td>
              <div class="checkbox checkbox-custom">
                <input type="checkbox" name="remove_phone[]" class="js-switch remove_phone" value="<?=$rows['id']?>"/>
                <label for=""></label>
              </div>
            </td>
          </tr>
           <?php }
            } else { ?>
                <tr>
                    <td colspan="3" class="text-center">0 Records found</td>
                </tr>
            <?php } ?>

        </tbody>
        <?php 
        if ($total_rows > 0) {?>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?php echo $paginate->links_html; ?>
                    </td>
                </tr>
            </tfoot>
        <?php }?>
    </table>
  </div>
      </form>
</div>
</div>


<script type="text/javascript">
    $(document).ready(function(){

       $(".phone_date_picker").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true
        });

        $(document).off("click", "#sms_search_btn");
        $(document).on("click", "#sms_search_btn", function(e) {
          e.preventDefault();
          $(this).hide();
          $("#sms_search_div").css('display', 'inline-block');
        });

        $(document).off("click", ".search_close_btn");
        $(document).on("click", ".search_close_btn", function(e) {
          e.preventDefault();
          $("#sms_search_div").hide();
          $("#sms_search_btn").show();

        });

        // phone listing pagination
        $(document).off('click', '#sms_unsubscribes_div ul.pagination li a');
        $(document).on('click', '#sms_unsubscribes_div ul.pagination li a', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $('#sms_unsubscribes_div').hide();
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#sms_unsubscribes_div').html(res).show();
                       common_select();
                }
            });
        });

        // select phone to remove
        $(document).off('click', '#remove_all_phone');
        $(document).on('click', '#remove_all_phone', function (e) {
            if($(this).is(':checked')){
                $(".remove_phone").prop("checked",true);
            }else{
                $(".remove_phone").prop("checked",false);
            }
            if ($('input.remove_phone[type=checkbox]:checked').length > 0){
              $('#unsubscribe_phone_btn').removeClass('disabled');
            }else{
              $('#unsubscribe_phone_btn').addClass('disabled');
            }
        });

        $('.remove_phone').on('click', function() {
          if ($('input.remove_phone[type=checkbox]:checked').length > 0){
            $('#unsubscribe_phone_btn').removeClass('disabled');
          }else{
            $('#unsubscribe_phone_btn').addClass('disabled');
          }
        });

        // remove from unsubscribe SMS list
        $(document).off("click","#unsubscribe_phone_btn");
        $(document).on("click","#unsubscribe_phone_btn",function(){
            var sysNumber = "<?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $sysNumber);?>";
            $formId=$("#sms_unsubscribes_form");
            swal({
                text: '<br/>Remove Phone: User must text "START" to <span class="text-nowrap">' + sysNumber + ' </span> to be removed.',
                showCancelButton: false,
                confirmButtonText: 'Close'
            }); 
        });

    });

    sms_unsubscribes_submit = function() {
        $('#ajax_loader').show();
        $('#sms_ajaxed').val('1');
        var params = $('#sms_unsubscribes_form').serialize();
        $.ajax({
            url: $('#sms_unsubscribes_form').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#sms_unsubscribes_div').html(res).show();
                common_select();
                if($("#phone").val() != '' || $("#phone_added_date").val() != ''){
                  $("#sms_search_div").show().css('display', 'inline-block');
                  $("#sms_search_btn").hide();
                }
                fRefresh();
            }
        });
        return false;
    }

    isNumberKey = function(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)){
          return false;
      }
      return true;
    }
</script>
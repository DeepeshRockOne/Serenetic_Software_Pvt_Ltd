<div class="panel panel-default panel-block">
  <div class="panel-body">
    <form class="theme-form" id="email_unsubscribes_form" action="email_unsubscribes.php">
        <input type="hidden" id="is_ajaxed" name="is_ajaxed" value="">
        <div class="clearfix tbl_filter">
        <h4 class="m-t-7">Email Unsubscribes </h4>
         <div class="pull-left">
            <?php if ($total_rows > 0) {?>
            <div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
                <div class="form-group height_auto mn">
                    <label for="user_type">Records Per Page </label>
                </div>
                <div class="form-group height_auto mn">
                    <select size="1" id="pages" name="pages" class="form-control select2 placeholder" onchange="$('#per_pages').val(this.value);$('#nav_page').val(1);email_unsubscribes_submit();">
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
            <div class="note_search_wrap auto_size" id="email_search_div" style="display: none; max-width: 100%;">
              <div class="phone-control-wrap theme-form">
                <div class="phone-addon">
                  <div class="form-group height_auto mn">
                  <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                  </div>
                </div>
                <div class="phone-addon w-200">
                  <div class="form-group height_auto mn">
                  <input type="text" class="form-control" name="email" id="email" value="<?=!empty($email) ? $email : ''?>">
                  <label>Email Address</label>
                  </div>
                </div>
                 <div class="phone-addon w-200">
                   <div class="form-group height_auto mn">
                    <input type="text" name="email_added_date" id="email_added_date" value="<?=!empty($email_added_date) ? $email_added_date : ''?>" class="form-control email_date_picker" />
                    <label>Added Date</label>
                  </div>
                </div>
                <div class="phone-addon w-80">
                  <div class="form-group height_auto mn">
                  <a href="javascript:void(0);" class="btn btn-info search_button" onclick="email_unsubscribes_submit();">Search</a>
                  </div>
                </div>
              </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="email_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0);" id="unsubscribe_email_btn" class="btn btn-action disabled m-l-5" style="display:inline-block;" > Remove</a>
          </div>
        </div>
    </div>
    <div class="table-responsive">
      <table class="<?=$table_class?>">
        <thead>
          <tr>
            <th>Added Date</th>
            <th>Email Address </th>
            <th width="100px">
              <div class="checkbox checkbox-custom mn">
                <input type="checkbox" class="js-switch" id='remove_all_email'/>
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
            <td><?=$rows['email']?></td>
            <td>
              <div class="checkbox checkbox-custom">
                <input type="checkbox" name="remove_email[]" class="js-switch remove_email" value="<?=$rows['id']?>"/>
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

       $(".email_date_picker").datepicker({
          changeDay: true,
          changeMonth: true,
          changeYear: true
        });

        $(document).off("click", "#email_btn");
        $(document).on("click", "#email_btn", function(e) {
          e.preventDefault();
          $(this).hide();
          $("#email_search_div").css('display', 'inline-block');
        });

        $(document).off("click", ".search_close_btn");
        $(document).on("click", ".search_close_btn", function(e) {
          e.preventDefault();
          $("#email_search_div").hide();
          $("#email_btn").show();

        });

        // email listing pagination
        $(document).off('click', '#email_unsubscribes_div ul.pagination li a');
        $(document).on('click', '#email_unsubscribes_div ul.pagination li a', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $('#email_unsubscribes_div').hide();
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#email_unsubscribes_div').html(res).show();
                       common_select();
                }
            });
        });

        // select emails to remove
        $(document).off('click', '#remove_all_email');
        $(document).on('click', '#remove_all_email', function (e) {
            if($(this).is(':checked')){
                $(".remove_email").prop("checked",true);
            }else{
                $(".remove_email").prop("checked",false);
            }
            if ($('input.remove_email[type=checkbox]:checked').length > 0){
              $('#unsubscribe_email_btn').removeClass('disabled');
            }else{
              $('#unsubscribe_email_btn').addClass('disabled');
            }
        });

        $('.remove_email').on('click', function() {
          if ($('input.remove_email[type=checkbox]:checked').length > 0){
            $('#unsubscribe_email_btn').removeClass('disabled');
          }else{
            $('#unsubscribe_email_btn').addClass('disabled');
          }
        });

        // remove from unsubscribe email list
        $(document).off("click","#unsubscribe_email_btn");
        $(document).on("click","#unsubscribe_email_btn",function(){
            $formId=$("#email_unsubscribes_form");

            swal({
                text: 'Remove Email: Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
            }).then(function () {
                $.ajax({
                    url:'ajax_email_unsubscribes.php',
                    dataType:'JSON',
                    type:'POST',
                    data: $formId.serialize(),
                    beforeSend:function(){
                      $("#ajax_loader").show();
                    },
                    success:function(res){
                      if(res.status=='success'){
                          window.parent.setNotifySuccess("Email removed from unsubscribed successfully");
                           email_unsubscribes();
                      }else{    

                          window.parent.setNotifyError("Please select email");
                      }
                      $("#ajax_loader").hide();
                    }
                });
            }, function (dismiss) {
            });
        });

    });

    email_unsubscribes_submit = function() {
        $('#ajax_loader').show();
        $('#is_ajaxed').val('1');
        var params = $('#email_unsubscribes_form').serialize();
        $.ajax({
            url: $('#email_unsubscribes_form').attr('action'),
            type: 'GET',
            data: params,
            success: function(res) {
                $('#ajax_loader').hide();
                $('#email_unsubscribes_div').html(res).show();
                common_select();
                if($("#email").val() != '' || $("#email_added_date").val() != ''){
                  $("#email_search_div").show().css('display', 'inline-block');
                  $("#email_btn").hide();
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
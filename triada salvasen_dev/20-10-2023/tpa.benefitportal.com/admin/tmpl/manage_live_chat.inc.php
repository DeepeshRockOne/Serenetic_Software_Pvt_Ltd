
<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="clearfix tbl_filter">
    <div class="pull-left ">
        <h4 class="m-t-7">Category</h4>
    </div>
    <div class="pull-right">
      <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="dep_search_div" style="display: none; max-width: 100%;">
          <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
              <div class="form-group height_auto mn">
              <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="dep_search_close_btn">X</a>
              </div>
            </div>
            <div class="phone-addon w-300">
              <div class="form-group height_auto mn">
                <input type="text" id="dep_search_input" class="form-control">
                <label>Search</label>
              </div>
            </div>
            <div class="phone-addon w-80">
              <div class="form-group height_auto mn">
              <a href="javascript:void(0);" class="btn btn-info submit_dep_search_btn">Search</a>
              </div>
            </div>
          </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" id="dep_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
        <a href="live_chat_department.php" class="btn btn-action m-l-5 live_chat_department" style="display:inline-block;" >+ Category</a>
      </div>
    </div>
   </div>
    <div class="table-responsive">
      <table class="<?=$table_class?> dep_table">
        <thead>
          <tr>
            <th>Name</th>
            <th width="100px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
              if(!empty($lc_department_res)) {
                  foreach ($lc_department_res as $dep_row) {
                    ?>
                  <tr>
                      <td>
                          <?=$dep_row['name'];?>
                      </td>
                      <td class="icons ">
                          <a href="live_chat_department.php?department_id=<?=$dep_row['id']?>" class="live_chat_department" data-toggle="tooltip" data-trigger="hover" data-title="Edit"><i class="fa fa-edit"></i></a>
                          <a href="javascript:void(0);" class="btn_delete_dep" data-department_id="<?=$dep_row['id']?>"
                             data-toggle="tooltip" data-trigger="hover" title="Delete"><i class="fa fa-trash"></i></a>
                      </td>
                  </tr>
              <?php
                    }
              } else {?>
              <tr>
                  <td class="text" colspan="2">No record(s) found</td>
              </tr>
              <?php
            }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<div class="panel panel-default panel-block">
  <div class="panel-body">
    <div class="clearfix tbl_filter">
        <div class="pull-left">
            <h4 class="m-t-7">Quick Reply</h4>
        </div>
    <div class="pull-right">
      <div class="m-b-15">
        <div class="note_search_wrap auto_size" id="label_search_div" style="display: none; max-width: 100%;">
          <div class="phone-control-wrap theme-form">
            <div class="phone-addon">
              <div class="form-group height_auto mn">
              <a href="javascript:void(0);" class="search_close_btn text-light-gray" id="label_search_close_btn">X</a>
              </div>
            </div>
            <div class="phone-addon w-300">
              <div class="form-group height_auto mn">
                <input type="text" id="label_search_input" class="form-control">
                <label>Search</label>
              </div>
            </div>
            <div class="phone-addon w-80">
              <div class="form-group height_auto mn">
              <a href="javascript:void(0);" class="btn btn-info submit_label_search_btn">Search</a>
              </div>
            </div>
          </div>
        </div>
        <a href="javascript:void(0);" class="search_btn" id="label_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
        <a href="add_quick_reply_chat.php" class="btn btn-action m-l-5 add_quick_reply_chat" style="display:inline-block;" >+ Quick Reply</a>
      </div>
    </div>
    </div>
    <div class="table-responsive">
      <table class="<?=$table_class?> reply_table">
        <thead>
          <tr>
            <th width="300px">Name</th>
            <th>Text</th>
            <th width="100px">Actions</th>
          </tr>
        </thead>
        <tbody>
            <?php
                if(!empty($saved_replies_res)) {
                    foreach ($saved_replies_res as $reply_key => $saved_replies_row) {
                      ?>
                    <tr>
                        <td>
                            <?=$saved_replies_row['reply-name'];?>
                        </td>
                        <td>
                            <?=$saved_replies_row['reply-text'];?>
                        </td>
                        <td class="icons ">
                            <a href="add_quick_reply_chat.php?reply_key=<?=$reply_key?>" class="edit_quick_reply_chat" data-toggle="tooltip" data-trigger="hover" data-title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0);" class="btn_delete_quick_reply" data-reply_key="<?=$reply_key?>"
                               data-toggle="tooltip" data-trigger="hover" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php
                      }
                } else {?>
                <tr>
                    <td class="text" colspan="3">No record(s) found</td>
                </tr>
                <?php
              }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(document).off("click", "#label_search_btn");
    $(document).on("click", "#label_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#label_search_div").css('display', 'inline-block');
    });

    $(document).off("click", "#label_search_close_btn");
    $(document).on("click", "#label_search_close_btn", function(e) {
        e.preventDefault();
        $("#label_search_div").hide();
        $("#label_search_btn").show();

        $("#label_search_input").val('');
        var value = $("#label_search_input").val();
        $("table.reply_table tbody").each(function(index,element){
            $(this).find("tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    $(document).off("click", ".submit_label_search_btn");
    $(document).on("click", ".submit_label_search_btn", function(e) {
        //alert('search');
        var value = $("#label_search_input").val().toLowerCase();
        $("table.reply_table tbody").each(function(index,element){
            $(this).find("tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });            
    });

    $(document).off("click", "#dep_search_btn");
    $(document).on("click", "#dep_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#dep_search_div").css('display', 'inline-block');
    });

    $(document).off("click", "#dep_search_close_btn");
    $(document).on("click", "#dep_search_close_btn", function(e) {
        e.preventDefault();
        $("#dep_search_div").hide();
        $("#dep_search_btn").show();

        $("#dep_search_input").val('');
        var value = $("#dep_search_input").val();
        $("table.dep_table tbody").each(function(index,element){
            $(this).find("tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });

    $(document).off("click", ".submit_dep_search_btn");
    $(document).on("click", ".submit_dep_search_btn", function(e) {
        //alert('search');
        var value = $("#dep_search_input").val().toLowerCase();
        $("table.dep_table tbody").each(function(index,element){
            $(this).find("tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });            
    });

    $(".live_chat_department").colorbox({iframe: true, width: '550px', height: '215px'});
    $(".edit_live_chat_category").colorbox({iframe: true, width: '550px', height: '215px'});
    $(".add_quick_reply_chat").colorbox({iframe: true, width: '550px', height: '460px'});
    $(".edit_quick_reply_chat").colorbox({iframe: true, width: '610px', height: '550px'});
    $(".quick_reply_content").colorbox({iframe: true, width: '550px', height: '350px'});

    $(document).on("click", "#name_search_btn", function(e) {
        e.preventDefault();
        $(this).hide();
        $("#name_search_div").css('display', 'inline-block');
    });
    
    $(document).on("click", "#name_search_close_btn", function(e) {
        e.preventDefault();
        $("#name_search_div").hide();
        $("#name_search_btn").show();
    });

    $(document).off('click', '.btn_delete_quick_reply');
    $(document).on("click", ".btn_delete_quick_reply", function (e) {
        var reply_key = $(this).attr('data-reply_key');
        swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url: 'manage_live_chat.php',
                data: {
                    reply_key: reply_key,
                    action: 'delete_reply',
                },
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    window.location.reload();
                }
            });
        }, function (dismiss) {

        });
    });

    $(document).off('click', '.btn_delete_dep');
    $(document).on("click", ".btn_delete_dep", function (e) {
        var department_id = $(this).attr('data-department_id');
        swal({
            text: "Delete Record: Are you sure?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }).then(function () {
            $("#ajax_loader").show();
            $.ajax({
                url: 'manage_live_chat.php',
                data: {
                    department_id: department_id,
                    action: 'delete_department',
                },
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    window.location.reload();
                }
            });
        }, function (dismiss) {

        });
    });
});
</script>

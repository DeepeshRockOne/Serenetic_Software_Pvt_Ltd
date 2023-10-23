<!--  updated ui static html start  -->
<!-- <div class="container m-t-30">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="clearfix ">
        <div class="pull-left m-b-10">
          <h4 class="m-t-7">Personalized Website(s)</h4>
        </div>
        <div class="pull-right">
          <a href="page_builder.php" class="btn btn-action">+ Website</a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th>Added Date</th>
              <th>Name</th>
              <th>URL</th>
              <th>Status</th>
              <th width="130px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>01/01/2020</td>
              <td>N/A</td>
              <td>N/A</td>
              <td>Draft</td>
              <td class="icons">
                <a href="javascript:void(0);"><i class="fa fa-eye"></i></a>
                <a href="javascript:void(0);"><i class="fa fa-edit"></i></a>
                <a href="javascript:void(0);"><i class="fa fa-trash"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div> -->
<!--  updated ui static html end  -->
<style type="text/css">
  .disabled{pointer-events: none;}
  .disabled a{color: grey !important;}
</style>
<script type="text/javascript">
    $(function() {
      $(document).on("change", '.is_published', function() {
        var id = $(this).attr('data-id');
        var publish_status = $(this).val();
        swal({
          text: "Published Status: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
          showCloseButton: true
        }).then(function() {
          $.ajax({
            url: 'change_personalized_published_status.php',
            data: {
              id: id,
              status: publish_status
            },
            method: 'POST',
            dataType: 'json',
            success: function(res) {
              if (res.status == "success") {
                window.location.reload();
              }
            }
          });
        });
      });
    });
</script>

<?php if ($is_ajaxed) { ?>
<div class="table-responsive">
  <table class="<?=$table_class?>">
    <thead>
      <tr class="data-head">
        <th width="120px"><a href="javascript:void(0);" data-column="pb.created_at" data-direction="<?php echo $SortBy == 'pb.created_at' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Date Created</a></th>
        <th><a href="javascript:void(0);" data-column="pb.page_name" data-direction="<?php echo $SortBy == 'pb.page_name' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Name</a></th>
        <th width="150px"><a href="javascript:void(0);" data-column="pb.status" data-direction="<?php echo $SortBy == 'pb.status' ? ($currSortDirection == 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>">Published</a> </th>
        <th width="150px">Options</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($total_rows > 0) {
        foreach ($fetch_rows as $rows) { ?>
          <tr>
          <td><?php echo date('m/d/Y', strtotime($rows['created_at'])); ?></td>
          <td><?= ($rows['page_name']!="") ? $rows['page_name'] : 'N/A' ?></td>
          
          <td>
            <?php if($rows['status']=="Draft" || $rows['status']==""){ 
                 echo "Draft";
            }else{ ?>
              <select name="is_published" class="form-control is_published input-sm" id="is_published_<?=$rows['id'];?>" data-id="<?=$rows['id'];?>">
                <option value="Active" <?= ($rows['status'] == 'Active') ? "selected='selected'" : ""     ?>>Yes </option>
                <option value="Inactive" <?= ($rows['status'] == 'Inactive') ? "selected='selected'" : ""     ?>>No </option>
              </select>
            <?php }?>
        </td>
        <td class="icons no-wr">
          <div class="text-left">
            <?php if($rows['status']!='Active'){ ?>
            <a href="javascript:void(0)" data-id="<?= $rows['id'] ?>" class="view_preview" data-toggle="tooltip" title="Preview Page"><i class="fa fa-eye"></i></a>
            <?php } else { ?>
            <a href="<?=$PRODUCTS212?>/<?= $rows['user_name']?>" target="_blank" data-id="<?= $rows['id'] ?>" class="view_page" data-toggle="tooltip" title="View Page"><i class="fa fa-file-text-o"></i></a>
            <?php } ?>
            <a href="page_builder.php?id=<?= $rows['id'] ?>" data-id="<?= $rows['id'] ?>" class="update_page" data-toggle="tooltip" title="Update Page"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" data-id="<?= $rows['id'] ?>" class="delete_page" data-toggle="tooltip" title="Delete Page"><i class="fa fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php }?>
      <?php } else {?>
      <tr>
        <td colspan="4" class="text-center">No record(s) found</td>
      </tr>
      <?php }?>
    </tbody>
    <?php if ($total_rows > 0) {?>
    <tfoot>
      <tr>
        <td colspan="9"><?php echo $paginate->links_html; ?></td>
      </tr>
    </tfoot>
    <?php }?>
  </table>
</div>
<?php } else { ?>
<?php include_once 'notify.inc.php';?>
<div class="main-content">
  <div class="panel panel-default panel-block">
    <form id="frm_search" action="personalized_sales_page.php" method="GET" class="sform">
          <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
          <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>"/>
          <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>"/>
          <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>"/>
    </form>
  </div>
  <div class="m-b-30 m-t-30"><h2 class="text-center">Personalized Website(s)</h2></div>
  <div class="row">
    <div class="col-lg-8 col-lg-offset-2">
      <div class="panel panel-default panel-block ">
        <div class="panel-body">
          <div id="ajax_loader" class="ajex_loader" style="display: none;">
            <div class="loader"></div>
          </div>
          <div class="text-center m-b-30">
              <a href="page_builder.php" class="btn btn-info btn-action btn-xs-small">+ Website</a>
          </div>
          
          <div id="ajax_data" class=""></div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="prv_bborder" style="display: none;"> 
 <div class="prv_mainswitch">
      <!-- <label class="m-r-10">Preview Mode</label>
      <span class="pr text-left">
      <input name="prd_preview_btn" id="prd_preview_btn" class="js-switch" data-color="#99d683" data-secondary-color="#f96262" data-size="small" style="display: none;" data-switchery="true" type="checkbox">
      </span> -->
      <button class="btn btn-action edit_builder_button">Edit</button>
  </div>
  <div class="center_device" style="display:none">
    <ul>
      <li><a href="javascript:void(0)" id="xs-mobile"><i class="fa fa-mobile"></i></a></li>
      <li class="active"><a href="javascript:void(0)" id="xs-tablet"><i class="fa fa-tablet"></i></a></li>
      <li><a href="javascript:void(0)" id="xs-desktop"><i class="fa fa-television"></i></a></li>
    </ul>
  </div>
  <div class="back_btn_wrapper">
    <button class="btn btn-action stop_preview">Back</button>
  </div>
</div>
<div class="iframe_sm_responsive">
	<iframe id="prd_preview_iframe" style="display:none;" frameborder="0" width="768px" scrolling="no"></iframe>
</div>
<script type="text/javascript">
  trigger(".view_preview",function($this,e){
      /*if(!$("#prd_preview_btn").prop('checked')){
        $("#prd_preview_btn").trigger("click");
      }*/
      $page_builder_id = $this.attr("data-id");
      $(".edit_builder_button").attr("data-id",$page_builder_id);
      $("#ajax_loader").show();
      $(".center_device,.prv_bborder").show();
      $("#data_action").val('preview');
      //preview code start
      $("#wrapper > .navbar.navbar-default, .sidebar, .sttabs > nav, #page-wrapper .row.bg-title,  footer.footer, .main-content,.preli_commissions").hide();
     
      $("body").addClass("prd_preview");
      $("#page-wrapper").addClass("mn");
      $("#page-wrapper, .content-current, #page-wrapper > .container-fluid").addClass("pn");
      $("#prd_preview_iframe").attr("src", "<?=$HOST?>/prd_preview.php?page_builder_id=" + $page_builder_id).show();
      //preview code end
    });
    trigger(".stop_preview",function($this,e){
     endPreview();
    });
    trigger(".edit_builder_button",function($this,e){
      window.location.href='<?=$AGENT_HOST?>/page_builder.php?id='+$this.attr("data-id");
    });
    endPreview=function(){
      $(".center_device,.prv_bborder").hide();
      $("#wrapper > .navbar.navbar-default, .sidebar, .sttabs > nav, #page-wrapper .row.bg-title, footer.footer, .main-content,.preli_commissions").show();
      $("body").removeClass("prd_preview");
      $("#page-wrapper").removeClass("mn");
      $("#page-wrapper, .content-current, #page-wrapper > .container-fluid").removeClass("pn");
      $("#prd_preview_iframe").hide();
    }
    resizeIframe = function($height) {
      $("#prd_preview_iframe")[0].style.height = $height + 'px';
      $("#ajax_loader").hide();
    };
    /*trigger("#prd_preview_btn", function($this) {
      if (!$this.prop("checked")) {
        endPreview();
      }
    },"change");*/
    /*$(function(){
       // Start: Switchery
      $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
      });
      // End: Switchery
    });*/
    win = $("#prd_preview_iframe")[0].contentWindow;

    $("#xs-mobile").click(function(){
      $('#prd_preview_iframe').attr('width', '380px');
      $prevActiveId = $("div.center_device>ul>li.active>a").attr("id");
      $(this).parent("li").siblings().removeClass("active");
      $(this).parent("li").addClass("active");
      win.postMessage("resize_frame", "<?=$HOST?>");
    });

    $("#xs-desktop").click(function(){
      $('#prd_preview_iframe').attr('width', '100%');
	  
	  if ($(window).width() < 1025) {
		$('#prd_preview_iframe').attr('width', '1024px');
	  }
      
	  $prevActiveId = $("div.center_device>ul>li.active>a").attr("id");
      $(this).parent("li").siblings().removeClass("active");
      $(this).parent("li").addClass("active");
      win.postMessage("resize_frame", "<?=$HOST?>");
    });

    $("#xs-tablet").click(function(){
      $("#prd_preview_iframe").attr('width', '768px');
      $prevActiveId = $("div.center_device>ul>li.active>a").attr("id");
      $(this).parent("li").siblings().removeClass("active");
      $(this).parent("li").addClass("active");
      win.postMessage("resize_frame", "<?=$HOST?>");
    });

</script>
<script type="text/javascript">
  $(document).ready(function () {
    ajax_submit();

    $(document).off('click', '#ajax_data tr.data-head a');
    $(document).on('click', '#ajax_data tr.data-head a', function (e) {
        e.preventDefault();
        $('#sort_by_column').val($(this).attr('data-column'));
        $('#sort_by_direction').val($(this).attr('data-direction'));
        ajax_submit();
    });

    $(document).off('click', '#ajax_data ul.pagination li a');
    $(document).on('click', '#ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#ajax_data').html(res).show();
            }
        });
    });
  });
  function ajax_submit() {
      $('#ajax_loader').show();
      $('#ajax_data').hide();
      $('#is_ajaxed').val('1');
      var params = $('#frm_search').serialize();
      $.ajax({
          url: $('#frm_search').attr('action'),
          type: 'GET',
          data: params,
          success: function (res) {
              $('#ajax_loader').hide();
              $('#ajax_data').html(res).show();
          }
      });
      return false;
  } 
  $(document).on("click",".delete_page",function(){
    $id=$(this).attr('data-id');

    swal({
        text: "Delete Page: Are you sure?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
        showCloseButton: true
    }).then(function () {
        $.ajax({
            url: 'ajax_personalized_published_delete.php',
            data: {id: $id},
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.status == "success") {
                   window.location.reload();

                }
            }
        });
    });
  });
</script>
<?php }?>

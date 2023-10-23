<?php include_once('notify.inc.php'); ?>  
<div class="panel panel-default panel-block panel-title-block">
  <div class="panel-heading">
    <div>
      <h1 style="line-height:36px;">
        <i class="fa fa-mail-reply"></i>
        <span><?= $_GET['type'] ?> Quick Reply (<?= $total ?>)</span>
      </h1> 
      <a class="btn btn-action pull-right" href="add_quick_reply.php?type=<?= $_GET['type'] ?>"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add <?php echo $type; ?> Quick Reply</a>

    </div>

  </div>
  <!-- <section class="extended"> -->
    <!-- <script>
      if ($.cookie('protonSidebar') == 'retracted') {
        $('.sidebar').removeClass('extended').addClass('retracted');
        $('.wrapper').removeClass('retracted').addClass('extended');
      }
      if ($.cookie('protonSidebar') == 'extended') {
        $('.wrapper').removeClass('extended').addClass('retracted');
        $('.sidebar').removeClass('retracted').addClass('extended');
      }
    </script> -->
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="title m-b-20" style="font-size:16px; color: #999999;">
          <i class="fa fa-search"></i>
          <span>
            ADVANCE SEARCH
          </span>
        </div>
        <form action="" method="GET" class="sform">
          <fieldset>
            <label>Title:</label><input type="text" class="form-control" name="title" value="<?php echo $title; ?>">
          </fieldset>

          <fieldset>
            <label>Quick Date:</label>
            <select class="form-control select2 placeholder select2-offscreen" name="custom_date" id="custom_date">              
              <option value="">&nbsp;</option>
              <option value="Today" <?php
              if ($custom_date == "Today") {
                echo 'selected';
              }
              ?>>Today</option>
              <option value="Yesterday" <?php
              if ($custom_date == "Yesterday") {
                echo 'selected';
              }
              ?>>Yesterday</option>
              <option value="Last7Days" <?php
              if ($custom_date == "Last7Days") {
                echo 'selected';
              }
              ?>>Last 7 Days</option>
              <option value="ThisMonth" <?php
              if ($custom_date == "ThisMonth") {
                echo 'selected';
              }
              ?>>This Month</option>
              <option value="LastMonth" <?php
              if ($custom_date == "LastMonth") {
                echo 'selected';
              }
              ?>>Last Month</option>
              <option value="ThisYear" <?php
              if ($custom_date == "ThisYear") {
                echo 'selected';
              }
              ?>>This Year</option>
            </select>
          </fieldset>         
          <fieldset>
            <label>From date:</label><input type="text" name="fromdate" id="fromdate" value="<?php echo $fromdate ?>" class="datetimepicker-range form-control">
          </fieldset>
          <fieldset>
            <label>To date:</label><input type="text" name="todate" id="todate" value="<?php echo $todate ?>" class="datetimepicker-range form-control">
          </fieldset>

          <fieldset style="margin-top: 25px;">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <button type="submit" class="btn btn-info" name="search" id="search">
              <i class="fa fa-search"></i> Search
            </button>
            <button type="button" class="btn btn-info" name="viewall" id="viewall" onClick="window.location = 'quick_reply.php?type=<?= $_GET['type'] ?>'">
              <i class="fa fa-search-plus"></i> View All
            </button>            
          </fieldset>
          <input type="hidden" name="pages" id="pages" value="<?= $per_page; ?>" />
        </form>
      </div>                            
    </div>
    <div class="adsearch">
      <span class="sidebar-handle"></span>
    </div>
  <!-- </section> -->
</div>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div class="list-group-item">
      <?php if ($total_rows > 0) { ?>
        <div class="ie7_pagination col-md-12 row ">
          <div class="col-md-7">
            <?php echo ($total_rows > 0 ? $paginate->links_html : ''); ?>
          </div>
          <div class="form-inline  text-right row pull-right" id="DataTables_Table_0_length">
            <div class="form-group"  >
              <label for="user_type"	>Records Per Page</label>
            </div>
            <div class="form-group">
              <select size="1" id="pages" name="pages" onchange="javascript:location.href = '?pages=' + this.value + '&<?php echo "type=$type&title=$title&fromdate=$fromdate&todate=$todate&custom_date=$custom_date"; ?>';" class="form-control select2 placeholder">
                <option value="10" <?php echo $_GET['pages'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="25" <?php echo $_GET['pages'] == 25 || $_GET['pages'] == "" ? 'selected' : ''; ?>>25</option>
                <option value="50" <?php echo $_GET['pages'] == 50 ? 'selected' : ''; ?>>50</option>
                <option value="100" <?php echo $_GET['pages'] == 100 ? 'selected' : ''; ?>>100</option>
              </select>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      <?php } ?> 
      <div class="clearfix"></div> 
      <div class="table-responsive">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="<?=$table_class?>">
          <thead class="">
            <tr>
              <th>Name</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>          
          <tbody>
            <?php
            if ($total_rows > 0) {
              foreach ($fetch_rows as $rows) {
                ?>
                <tr>
                  <td><?php echo $rows['title']; ?></td>
                  <td><?php echo date($DATE_FORMAT, strtotime($rows['created_at'])); ?></td>

                  <td class="icons">	
                    <a href="edit_quick_reply.php?id=<?php echo $rows['id']; ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>                      
                  </td>

                </tr>
              <?php } ?>
            <tfoot>
              <tr>
                <td colspan="4">
                  <?php echo $paginate->links_html; ?>
                </td>
              </tr>
            </tfoot>
          <?php } else { ?>
            <tr>
              <td colspan="4" class="text-center">
                No Record(s)
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
      $(document).ready(function () {
    $( "#fromdate" ).datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true 
    });

  });

    $(document).ready(function () {
    $( "#todate" ).datepicker({
      changeDay: true,
      changeMonth: true,
      changeYear: true 
    });
  });
</script>
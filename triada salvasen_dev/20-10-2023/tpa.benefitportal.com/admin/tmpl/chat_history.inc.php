<?php include_once('notify.inc.php'); ?> 
<div class="panel panel-default panel-block panel-title-block">
  <div class="panel-heading">
    <div>
      <h1>
        <i class="icon-time"></i>
        <span>Chat History</span>
      </h1>             
    </div>
  </div>
  <!-- <section class="sidebar animated fadeInLeft extended">
    <script>
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
        <div class="title  m-b-20" style="font-size:16px; color: #999999;"">
          <i class="icon-search"></i>
          <span>
            ADVANCE SEARCH
          </span>
        </div>
        <form autocomplete="off" method="GET" action="" name="frmseach" class="sform">      
          <fieldset>
            <label>Chat ID:</label>
            <input type="text" name="chat_id" id="chat_id" value="<?= $chat_id ?>" class="form-control" />
          </fieldset>          
          <fieldset>
            <label>Name: </label>
            <input type="text" name="name" id="name" value="<?= $name ?>" class="form-control" />
          </fieldset>
          <fieldset>
            <label>IP Address:</label>
            <input type="text" name="ip_address" id="ip_address" value="<?= $ip_address ?>" class="form-control" />
          </fieldset>
          <fieldset class="demo cselect">
            <label>Operator:</label>
            <select id="operator" name="operator" class=" form-control select2 placeholder">
              <option value="">&nbsp;</option>
              <?php foreach ($operator_data as $op_data) { ?>
                <option value="<?php echo $op_data['operatorid']; ?>" <?php if ($op_data['operatorid'] == $operator) { ?>selected="selected" <?php } ?>><?php echo $op_data['vclocalename']; ?></option>
              <?php } ?>
            </select> 
          </fieldset>
          <fieldset class="demo cselect">
            <label>Quick Date Search:</label>
            <select id="custom_date" name="custom_date" class=" form-control select2 placeholder">
              <option value="">&nbsp;</option>
              <option value="Today">Today</option>
              <option value="Yesterday">Yesterday</option>
              <option value="Last7Days">Last 7 Days</option>
              <option value="ThisMonth">This Month</option>
              <option value="LastMonth">Last Month</option>
              <option value="ThisYear">This Year</option>
            </select>
          </fieldset>
          <fieldset>
            <label>From Date:</label>
            <div class="datepick">
              <input type="text" value="<?= $fromdate; ?>" id="fromdate" name="fromdate" class="datetimepicker-range form-control"  style="padding-left:100px"/>
            </div>
          </fieldset>
          <fieldset>
            <label>To Date:</label>
            <div class="datepick">
              <input type="text" value="<?= $todate; ?>" id="todate" name="todate"  class="datetimepicker-range form-control" style="padding-left:100px" />
            </div>
          </fieldset>            
          <fieldset style="margin-top: 25px;">
            <button class="btn btn-info" id="search" name="search" value="Search" type="submit"><i class="icon-search"></i> Search</button>
            <button class="btn btn-info" id="all" name="all" value="all" type="button" onclick='window.location = "chat_history.php";
                return false;'><i class="icon-zoom-in"></i> View All</button>
          </fieldset> 
          <fieldset>&nbsp;</fieldset>
        </form>
      </div>                            
    </div>
    <div class="adsearch"><span class="sidebar-handle"></span></div>
  </section> 
</div>
<div class="panel panel-default panel-block">
  <div class="list-group">
    <div class="list-group-item">
      <?php if ($total_rows > 0) { ?>
        <div class="ie7_pagination col-md-12 row ">
          <div class="col-md-8">
            <?php echo ($total_rows > 0 ? $paginate->links_html : ''); ?>
          </div>
          <div class="form-inline  text-right row pull-right" id="DataTables_Table_0_length">
            <div class="form-group"  >
              <label for="user_type"	>Records Per Page</label>
            </div>
            <div class="form-group">
              <select size="1" id="pages" name="pages" onchange="javascript:location.href = '?pages=' + this.value + '&<?php echo "name=$name&operator=$operator&ip_address=$ip_address&fromdate=$fromdate&todate=$todate&custom_date=$custom_date"; ?>';" class="form-control select2 placeholder">
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
          <thead class="center">
            <tr>
              <th>Chat ID</th>
              <th>Date</th>              
              <th>Name</th>
              <th>IP Address</th>
              <th>Operator</th>
              <th>Total Time</th>
              <th>Details</th>
            </tr>
          </thead>          
          <tbody>
            <?php if ($total_rows > 0) { ?> 
              <?php foreach ($fetch_rows as $row) { ?>
                <tr>
                  <td><?php echo $row['display_id']; ?></td>
                  <td><?php echo retrieveDate($row['dtmcreated']); ?></td>                  
                  <td>
                    <?php
                    //$detail_page_link = "customer_detail.php?id=" . $row['customer_id'];
                    if ($row['customer_id'] == 0) {
                      echo $row['userName'];
                    } else {
                      ?>
                      <!-- <a href="<?php //$detail_page_link ?>javascript:void(0);"><?php echo $row['userName']; ?></a> -->
                      <?php echo $row['userName']; ?>
                      <?php
                    }
                    ?> 
                  </td>
                  <td><?php echo $row['remote']; ?></td>
                  <td><?php echo $operator_data[$row['agentId']]['vclocalename']; ?></td>
                  <td>
                    <?php
                    $interval = date_diff(date_create($row['dtmcreated']), date_create($row['dtmmodified']));
                    $total_time = $interval->format('%H') . ":" . str_pad($interval->format('%i'), 2, "0", STR_PAD_LEFT) . ":" . str_pad($interval->format('%s'), 2, "0", STR_PAD_LEFT);
                    echo $total_time;
                    ?>
                  </td>
                  <td class="icons">
                    <!-- <a href="<?php echo $HOST ?>/webim/operator/agent.php?thread=<?php echo $row['threadid'] ?>&viewonly=true" class="chat_popup" ><i class="fa fa-comments"></i></a> -->

                    <a href="user_chat_history_popup.php?thread=<?php echo $row['threadid'] ?>&viewonly=true" class="chat_popup" ><i class="fa fa-comments"></i></a>


                  </td>
                </tr>
              <?php } ?>
            <!-- <tfoot>
              <tr>
                <td colspan="8">
                  <?php echo $paginate->links_html; ?>
                </td>
              </tr>
            </tfoot> -->
          <?php } else { ?>
            <tr>
              <td colspan="8">
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
  $(document).ready(function() {
    $(".chat_popup").click(function(e) {
      e.preventDefault();
      $.colorbox({className: 'col_responsive', iframe: true, fixed: true, href: this.href, width: '740px', height: '480px'});
    });
    // $(".chat_popup").colorbox({iframe: true, width: '60%', height: '60%'});
    $('#fromdate').change(function() {
      if ($(this).val().trim() != "") {
        $('#todate').focus();
      }
    });
  });
</script>
<?/*
//onclick="this.newWindow = window.open(\'<?php echo $HOST ?>/webim/operator/agent.php?thread=<?php echo $row['threadid'] ?>&viewonly=true\', \'ImCenter<?php echo $row['threadid'] ?>\', \'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;"
<div class="panel panel-default panel-block">
<div class="list-group">
<div class="list-group-item">
<iframe src="<?php echo $HOST; ?>/webim/operator/users.php" height="500px" width="100%" scrollbar="no" frameborder="0"></iframe>
</div>
</div>  
</div>
*/?>
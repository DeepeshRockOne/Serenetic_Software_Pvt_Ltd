<?php 
  if($is_ajaxed){
?>
  <table class="<?=$table_class?>">
    <thead>
      <tr class="data-head">
          <th>ID/Added Date</th>
          <th>ID/Agent Name</th>
          <th>Charged</th>
          <th class="text-center" >Products #</th>
          <th width="15%">Status</th>
          <th width="130px">Actions</th>
      </tr>
    </thead>
    <tbody>
          <?php if($total_rows > 0){ ?>
            <?php foreach ($fetch_rows as $key => $row) { ?>
                <tr>
                  <td>
                    <a href="advance_commission_variation.php?advRuleId=<?=$row['id']?>&chargedTo=<?=$row['charged_to']?>"><span class="text-red"><strong><?=$row['display_id']?></strong></span></a>
                    <br/>
                    <?=date('m/d/Y',strtotime($row['created_at']))?>
                  </td>
                  <td>
                      <span class="text-red"><strong><?=$row['agentId']?></strong></span>
                      <br/>
                      <?=$row['agentName']?>
                  </td>
                  <td><?=$row['charged_to']?></td>
                  <td class="text-center">
                    <a href="advance_commission_product.php?advRuleId=<?=$row['id']?>" class="advPrdPopup text-red"><strong><?=$row['total_products']?></strong></a>
                  </td>
                  <td>
                    <div class="theme-form pr w-130">
                    <select class="form-control has-value" name="ruleStatus" id="updVariationStatus<?=$key?>" data-old_status="<?=$row['status']?>" data-ruleType="Variation" data-chargedTo="<?=$row['charged_to']?>" onchange="updStatusCommRule('<?=$row['id']?>','updVariationStatus<?=$key?>')">
                        <option value="Active" <?=$row['status'] == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
                        <option value="Inactive" <?=$row['status'] == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
                      </select>
                      <label>Select</label>
                    </div>
                  </td>
                  <td class="icons">
                    <a href="advance_commission_variation.php?advRuleId=<?=$row['id']?>&chargedTo=<?=$row['charged_to']?>&is_clone=Y" class="m-r-5" data-toggle="tooltip" data-trigger="hover" title="Duplicate"><i class="fa fa-clone"></i></a>
                    <a href="advance_commission_variation.php?advRuleId=<?=$row['id']?>&chargedTo=<?=$row['charged_to']?>" class="m-r-5"><i class="fa fa-pencil-square-o"></i></a> 
                    <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Delete" href="javascript:void(0);" class="m-r-5" onclick="delAdvCommRule('<?=$row['id']?>')"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
            <?php } ?>
          <?php }else{ ?>
              <tr><td colspan="6" class="text-center">No Records Found</td></tr>
          <?php } ?>
    </tbody>
    <?php if ($total_rows > 0) { ?>
      <tfoot>
        <tr>
          <td colspan="6">
            <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
    <?php }?>
  </table>
<?php }else{ ?>

  <div class="panel panel-default panel-block advance_info_div">
    <div class="panel-body">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-130">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="145px">
        </div>
        <div class="phone-addon text-left"> <p class="fs14">There are two fee options inside <?= $DEFAULT_SITE_NAME ?> to manage commission advances paid to agents. Your business rules will decide how these advance fees are managed, but your options are:</p>
        <div class="row">
          <div class="col-sm-6">
            <div class="info_box">
              <p class="m-t-0 fw600">Advances with Service Fees applied to <span class="text-red">Agents</span></p>
              <p class="fs14 mn">A service fee is applied to the advanced agent based on setup rules. The service fee may be setup as a fixed or variable amount.</p>
            </div>
          </div>
          <div class="visible-xs m-b-15"></div>
          <div class="col-sm-6">
            <div class="info_box">
              <p class="m-t-0 fw600">Advances with Service Fees applied to <span class="text-red">Members</span></p>
              <p class="fs14 mn">A service fee is applied to member orders based on order total excluding fees. The service fee may be setup as a fixed or variable amount. </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix">
      <div class="pull-left">
        <h4 class="m-t-7">Global Advance</h4>
      </div>
      <div class="pull-right">
        <div class="m-b-15">
          <div class="dropdown"> <a href="javascript:void(0)" class="fs14 btn btn-action" data-toggle="dropdown">+ Advance Commission</a>
          <ul class="dropdown-menu blue-border" style="width: 100%">
            <li><a href="add_agent_advance_global_rule.php" <?=in_array("Agents", $globalChargedArr) ? 'style="pointer-events: none;cursor: default;opacity:0.6;"' : ""?>>Charged to Agents</a></li>
            <li><a href="add_member_advance_global_rule.php" <?=in_array("Members", $globalChargedArr) ? 'style="pointer-events: none;cursor: default;opacity:0.6;"' : ""?>>Charged to Members</a></li>
          </ul>
          </div>
        </div>
      </div>
    </div>
      <div class="table-responsive">
        <div class="theme-form">
          <table class="<?=$table_class?>">
            <thead>
              <tr class="data-head">
                <th>ID/Added Date</th>
                <th>Charged</th>
                <th class="text-center">Products #</th>
                <th width="15%">Status</th>
                <th  width="130px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($globalAdvRes)){ ?>
              <?php foreach ($globalAdvRes as $key => $row) { ?>
              <tr>
                <?php if($row['charged_to'] == 'Members'){ ?>
                <td>
                  <a href="add_member_advance_global_rule.php?advRuleId=<?=$row['id']?>"><span class="text-red"><strong><?=$row['display_id']?></strong></span></a>
                  <br/>
                  <?=date('m/d/Y',strtotime($row['created_at']))?>
                </td>
                <?php }else{ ?>
                <td>
                  <a href="add_agent_advance_global_rule.php?advRuleId=<?=$row['id']?>"><span class="text-red"><strong><?=$row['display_id']?></strong></span></a>
                  <br/>
                  <?=date('m/d/Y',strtotime($row['created_at']))?>
                </td>
                <?php } ?>
              
                <td><?=$row['charged_to']?></td>
                <td class="text-center">
                  <a href="advance_commission_product.php?advRuleId=<?=$row['id']?>" class="advPrdPopup text-red"><strong><?=$row['total_products']?></strong></a>
                </td>
                <td>
                  <div class="theme-form pr w-130">
                  <select class="form-control has-row" id="updGlobalStatus<?=$key?>" data-ruleType="Global" data-old_status="<?=$row['status']?>" data-chargedTo="<?=$row['charged_to']?>" onchange="updStatusCommRule('<?=$row['id']?>','updGlobalStatus<?=$key?>')">
                      <option value="Active" <?=$row['status'] == 'Active' ? "selected = 'selected'" : ""?>>Active</option>
                      <option value="Inactive" <?=$row['status'] == 'Inactive' ? "selected = 'selected'" : ""?>>Inactive</option>
                    </select>
                    <label>Select</label>
                  </div>
                </td>
                <td class="icons ">
                  <?php if($row['charged_to'] == 'Members'){ ?>
                    <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" href="add_member_advance_global_rule.php?advRuleId=<?=$row['id']?>" ><i class="fa fa-pencil-square-o"></i></a>
                  <?php }else{ ?>
                  <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" href="add_agent_advance_global_rule.php?advRuleId=<?=$row['id']?>" ><i class="fa fa-pencil-square-o"></i></a>
                  <?php } ?>
                  <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Delete" href="javascript:void(0);" onclick="delAdvCommRule('<?=$row['id']?>')"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
              <?php } ?>
              <?php }else{ ?>
              <tr><td colspan="6" class="text-center">No Records Found</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-block">
    <div class="panel-body">
      <div class="clearfix tbl_filter">
        <div class="pull-left">
          <h4 class="m-t-7">Variation Advance</h4>
        </div>
        <div class=" pull-right">
          <form id="frm_search" action="" method="GET">
              <div class="m-b-15">
                <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
                  <div class="phone-control-wrap theme-form">
                    <div class="phone-addon">
                      <div class="form-group height_auto mn">
                      <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                      </div>
                    </div>
                    <div class="phone-addon w-200">
                      <div class="form-group height_auto mn">
                        <input type="text" name="display_id" id="display_id" class="form-control">
                        <label>Advance ID</label>
                      </div>
                    </div>
                    <div class="phone-addon w-200">
                      <div class="form-group height_auto mn">
                        <input type="text" name="agent_id" id="agent_id" class="form-control">
                        <label>Agent ID</label>
                      </div>
                    </div>
                     <div class="phone-addon w-200">
                      <div class="form-group height_auto mn">
                        <select class="form-control" name="status" id="status">
                          <option></option>
                          <option>Active</option>
                          <option>Inactive</option>
                        </select>
                        <label>Status</label>
                      </div>
                    </div>
                    <div class="phone-addon w-80">
                      <div class="form-group height_auto mn">
                      <a href="javascript:void(0);" class="btn btn-info search_button" id="search_button">Search</a>
                      </div>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
                <input type="hidden" name="pages" id="per_pages" value="<?=$per_page;?>" />
                <input type="hidden" name="sort_by" id="sort_by_column" value="<?=$SortBy;?>" />
                <input type="hidden" name="sort_direction" id="sort_by_direction" value="<?=$SortDirection;?>" />
              
                <a href="javascript:void(0);" class="search_btn " ><i class="fa fa-search fa-lg text-blue"></i></a>
                   <div class="dropdown m-l-5 d-inline"> <a href="javascript:void(0)" class="fs14 btn btn-action" data-toggle="dropdown">+ Variation Advance</a>
                    <ul class="dropdown-menu blue-border" style="width: 100%">
                      <li><a href="advance_commission_variation.php?chargedTo=Agents">Charged to Agents</a></li>
                      <li><a href="advance_commission_variation.php?chargedTo=Members">Charged to Members</a></li>
                    </ul>
                  </div>
              </div>
          </form>
        </div>
     </div>
      <div class="table-responsive" id="variation_list"></div>
    </div>
  </div>

<script type="text/javascript">
  $(document).ready(function() {
    dropdown_pagination('variation_list')
    ajax_submit();
    $('.advPrdPopup').colorbox({
      iframe: true,
      height: "350px",
      width: "800px"
    });

    $(document).on("click", ".search_btn", function(e) {
      e.preventDefault();
      $(this).hide();
      $("#search_div").css('display', 'inline-block');
    });
    $(document).on("click", "#search_button", function(e) {
      ajax_submit();
    });
    $(document).on("click", ".search_close_btn", function(e) {
      e.preventDefault();
      $("#search_div").hide();
      $(".search_btn").show();
      $("#display_id").val("");
      $("#agent_id").val("");
      $('#status option:first').prop('selected', true);
      window.location.reload();

    });

    $(document).off('click', '#variation_list ul.pagination li a');
    $(document).on('click', '#variation_list ul.pagination li a', function(e) {
      e.preventDefault();
      $('#ajax_loader').show();
      $('#variation_list').hide();
      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        success: function(res) {
          $('#ajax_loader').hide();
          $('#variation_list').html(res).show();
          common_select();
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
      success: function(res) {
        $('#ajax_loader').hide();
        $('#variation_list').html(res).show();
         $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        common_select();
        $(".advPrdPopup").colorbox({
          iframe: true,
          width: '800px',
          height: '600px'
        });
      }
    });
    return false;
  }

  delAdvCommRule = function(rule_id) {
    swal({
      text: '<br>Delete Record: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      $("#ajax_loader").show();
      $.ajax({
        url: "advances_commission.php",
        dataType: 'JSON',
        type: 'GET',
        data: {
          rule_id: rule_id,
          delete: 'Y'
        },
        success: function(res) {
          $("#ajax_loader").hide();
          if (res.status == 'success') {
            setNotifySuccess(res.message);
            window.location.reload();
          }
        }
      });
    }, function(dismiss) {
      window.location.reload();
    })
  }
  updStatusCommRule = function(rule_id,selId) {
    var old_val = $('#'+selId).attr('data-old_status');
    var rule_status = $('#'+selId+' option:selected').val();
    var chargedTo = $('#'+selId).attr("data-chargedTo");
    var ruleType = $('#'+selId).attr("data-ruleType");
    
    swal({
      text: '<br>Change Status: Are you sure?',
      showCancelButton: true,
      confirmButtonText: 'Confirm',
      cancelButtonText: 'Cancel',
    }).then(function() {
      window.location = 'advances_commission.php?rule_id=' + rule_id + '&rule_status=' + rule_status + '&chargedTo=' + chargedTo + '&ruleType=' + ruleType;
    }, function(dismiss) {
      $('#'+selId).val(old_val);
      $('#'+selId).selectpicker('render');
    });
  }
</script>
<?php } ?>
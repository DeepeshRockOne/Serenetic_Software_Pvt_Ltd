<div class="panel panel-block panel-default">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn">Assign Agent<span class="text-lowercase">(s)</span> - <span class="fw300"><?=$resPrd['prdName'].' ('.$resPrd['prdCode'].')'?></span></h4>
    </div>
  </div>
  <div class="panel-body">
    <div class="theme-form">
      <form name="agentPrdRule" id="agentPrdRule" action="ajax_agent_product_rule.php">
        <input type="hidden" name="productId" value="<?=$productId?>">
        <input type="hidden" name="action" id="action" value="">
      <!-- assigned rule to agent code start -->
        <div class="form-group">
          <select name="commission_rule_id" class=" form-control" id="commissionRuleId">
            <option value=""></option>
            <?php if(!empty($ruleRows)){
              foreach ($ruleRows as $rule) { 
                $ruleType = ($rule["parent_rule_id"] == 0 ? " (Global)" : "") ?> 
                <option value="<?=$rule['id']?>"><?=$rule["rule_code"].' ('.$rule['prdName'].' - '.$rule["prdCode"].') '.$ruleType?></option>
              <?php }
            } ?>
          </select>
          <label>Select Commission Rule</label>
          <span class="error" id="error_commission_rule_id"></span>
        </div>

        <div class="form-group">
          <select class="form-control" name="product_status" id="productStatus">
            <option></option>
            <option value="Contracted">Active</option>
            <option value="Pending Approval">Pending</option>
          </select>
          <label>Product Status</label>
          <span class="error" id="error_product_status"></span>
        </div>

        <div class="m-b-25">
          <p>Would you like to assign product to</p>
          <label class="m-t-0 m-b-10">
            <input type="radio" class="assignTo" name="assign_to" value="all_agents">All Agent(s)
          </label><br>
          <label class="mn">
            <input type="radio" class="assignTo" name="assign_to" value="specific_agents">Specific Agent(s)
          </label><br>
          <span class="error" id="error_assign_to"></span>
        </div>

        <div id="selectAgents" style="display: none;">
          <div class="phone-control-wrap">
              <div class="phone-addon text-left">
                <div class="form-group ">
                  <select class="se_multiple_select"  id="assignAgent" name="agents[]" multiple="multiple">
                    <?php 
                    if(count($agentRows) > 0){ 
                      foreach($agentRows as $agent){
                    ?>
                      <option value="<?php echo $agent["id"]; ?>" data-agentId="<?=$agent['agentDispId']?>" data-agentName="<?=$agent['agentName']?>">
                        <?php echo $agent['agentDispId'].' - '.$agent['agentName']; ?>
                      </option>
                    <?php
                      }
                    }
                    ?>
                </select>
                <label>Select Agent(s)</label>
                <span class="error" id="error_agents"></span>
                </div>
              </div>
              <div class="phone-addon w-70">
                <div class="form-group ">
                  <a href="javascript:void(0);" class="btn btn-info btn-block" id="addAgentBtn">Add</a>
                </div>
              </div>
          </div>
        </div>

        <div id="agentDownline" style="display: none;">
          <div class="table-responsive m-b-10 br-n">
            <table class="<?=$table_class?>">
              <thead>
                <tr class="bg_dark_black">
                  <th>ID</th>
                  <th>Name</th>
                  <th class="text-center">Include Full Downline?</th>
                  <th class="text-center">Include LOA Only?</th>
                </tr>
              </thead>
              <tbody id="selAgentTbl">
              </tbody>
            </table>
          </div>
        </div>
      <!-- assigned rule to agent code ends -->

      <!-- display contracted agents code start -->
      <div id="assignedAgentDiv">

        <div class="row">
          <div class="col-xs-6">
            <h5 class="m-t-10 m-b-0 fs16">
            Assigned Agents
            </h5>
          </div>
          <!-- search div code start -->
          <div class="col-xs-6">
            <div class="m-b-15 text-right">
                <div class="note_search_wrap auto_size" id="search_div" style="display: none; max-width: 100%;">
                <div class="phone-control-wrap theme-form">
                  <div class="phone-addon">
                     <div class="form-group  mn">
                        <a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
                     </div>
                  </div>
                  <div class="phone-addon">
                     <div class="form-group  mn">
                        <input type="text" class="form-control" id="agentSearch" name="agent" placeholder="ID/Name">
                     </div>
                  </div>
                </div>
             </div>
             <a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
            </div>
          </div>
          <!-- search div code ends -->
        </div>

        <!-- update agent products status div code start -->
        <div class="note_search_wrap m-b-15" id="updAgentStatusDiv" style="display: none; max-width: 100%;">
          <div class="row theme-form">
            <div class="col-xs-8">
                 <div class="form-group">
                  <select class="form-control" name="agentUpdStatus" id="agentUpdStatus">
                    <option></option>
                    <option value="Contracted">Active</option>
                    <option value="Pending Approval">Pending</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Extinct">Extinct</option>
                  </select>
                  <label>Product Status</label>
              </div>
            </div>
            <div class="col-xs-4 text-left">
              <div class="form-group  mn">
                <a href="javascript:void(0);" class="btn btn-info" id="agentPrdStatusBtn">Confirm</a>
                <a href="javascript:void(0);" class="btn red-link" id="closeStatusDivBtn">Cancel</a>
              </div>
            </div>
          </div>
        </div>
         <!-- update agent products status div code ends -->

        <!-- contracted agent listing start-->
        <table data-toggle="table" data-height="175" data-mobile-responsive="true" class="<?=$table_class?>">
          <thead>
            <tr>
              <th>
                <div class="checkbox checkbox-custom mn">
                     <input type="checkbox" id="allAgentCheck" class="js-switch" data-toggle="tooltip" title="Select All"/>
                     <label for="allAgentCheck"></label>
                </div>
              </th>
              <th>ID</th>
              <th>Name</th>
              <th>Product Status</th>
            </tr>
          </thead>
          <tbody id="tblData">
            <?php 
              if(!empty($assignedAgentRows)){
                foreach ($assignedAgentRows as $agents) {
            ?>

              <tr>
                <td>
                  <div class="checkbox checkbox-custom mn">
                       <input type="checkbox" name="updAgents[]" id="updAgents_<?=$agents['id']?>" class="agentCheck js-switch" value="<?=$agents['id']?>" />
                       <label for="updAgents_<?=$agents['id']?>"></label>
                  </div>
                </td>
                <td><a href="javascript:void(0);" class="fw500 text-action"><?=$agents['agentDispId']?></a></td>
                <td><?=$agents['agentName']?></td>
                <td>
                  <?php 
                    $prdStatus = '';
                    if($agents['productStatus'] == "Contracted"){
                      $prdStatus = "Active";
                    }else if($agents['productStatus'] == "Pending Approval"){
                      $prdStatus = "Pending";
                    }else{
                      $prdStatus = $agents['productStatus'];
                    }
                    echo $prdStatus;
                  ?>
                </td>
              </tr>
            
            <?php
                }
              }
            ?>
          </tbody>
        </table>
        <!-- contracted agent listing ends-->
      </div>
      <!-- display contracted agents code ends -->
    </div>
    <div class="text-center m-t-30">
      <a href="javascript:void(0)" class="btn btn-action" id="saveBtn">Save</a>
      <a href="javascript:void(0)"  onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {

    // assign agent product code start
    $("#assignAgent").multipleSelect({
          selectAll: false,
          onClick: function(e) {
            if(!e.selected){
                $("#downline_agent_"+e.value).remove();
            }
          },
          onTagRemove:function(e){
            $("#downline_agent_"+e.value).remove();
            if($("#selAgentTbl").length != 1){
                $("#agentDownline").hide();
            }
          }
    });
    $(document).on("click", ".assignTo", function(e) {
      $assignTo =  $("input[name='assign_to']:checked").val();
        if($assignTo == "all_agents"){   
          $("#selectAgents").hide();
          $("#agentDownline").hide();
        }else if($assignTo == "specific_agents"){
          $("#selectAgents").show();
        }
    });
    $(document).on("click", "#addAgentBtn", function(e) {
      e.preventDefault();

       $.each($("#assignAgent option:selected"),function(){
        $agentDispId = $(this).attr("data-agentId");
        $agentName = $(this).attr("data-agentName");
        $agentId = $(this).val();

         if($("#downline_agent_"+$agentId).length != 1){
          $("#selAgentTbl").append('<tr id="downline_agent_'+$agentId+'"><td class="fw500 text-action">'+$agentDispId+'</td><td>'+$agentName+'</td><td class="text-center"><input type="checkbox" name="full_downline['+$agentId+']" id="downline_'+$agentId+'" data-id="'+$agentId+'" class="js-switch full_downline_chk" value="Y"></td><td class="text-center"><input type="checkbox" name="loa_only['+$agentId+']" id="loa_'+$agentId+'" data-id="'+$agentId+'" class="js-switch loa_only_chk" value="Y"></td></tr>');
         }
         $("#agentDownline").show();
       });   
    });
    $(document).on("click","#saveBtn",function(){
      agentProductRule();    
    });

    // assigned product status table code start
    $("#agentSearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#tblData tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    $(document).off("click", ".search_btn");
    $(document).on("click", ".search_btn", function(e) {
     e.preventDefault();
     $(this).hide();
     $("#search_div").css('display', 'inline-block');
    });

    $(document).off("click", ".search_close_btn");
    $(document).on("click", ".search_close_btn", function(e) {
     e.preventDefault();
     $("#search_div").hide();
     $(".search_btn").show();
    });
    $(document).on("click","#allAgentCheck",function(){
      if($('#allAgentCheck').is(':checked')) {
        $('.agentCheck').prop('checked',true);
        if($('.agentCheck').length > 0)
          {
            $("#updAgentStatusDiv").show();
          }
      }else {
        $('.agentCheck').prop('checked', false);
        $("#updAgentStatusDiv").hide();
      }
    });

    $(document).on("click",".agentCheck",function(){
       if($('input.agentCheck[type=checkbox]:checked').length > 0){
          $("#updAgentStatusDiv").show();
       }else{
          $("#updAgentStatusDiv").hide();
       }
    });

    $(document).on("click",".full_downline_chk",function(){
      $agent_id = $(this).data('id');
      if($('#loa_'+$agent_id+':checked').length > 0){
        $('#loa_' + $agent_id).prop('checked', false);
      }
    });

    $(document).on("click",".loa_only_chk",function(){
      $agent_id = $(this).data('id');
      if($('#downline_'+$agent_id+':checked').length > 0){
        $('#downline_' + $agent_id).prop('checked', false);
      }
    });

    $(document).on("click", "#closeStatusDivBtn", function(e) {
      e.preventDefault();
      $("#updAgentStatusDiv").hide();
    });

    $(document).on("click","#agentPrdStatusBtn",function(){
      $agentUpdStatus = $("#agentUpdStatus").val();
      if($agentUpdStatus != ''){
        $("#action").val("updStatus");
        var params = $('#agentPrdRule').serialize();
        $.ajax({
            url: 'add_assign_agents.php',
            type: 'GET',
            data: params,
            dataType : 'json',
            success: function(res) {
              if(res.status == 'success'){
                setTimeout(function(){ 
                  parent.$.colorbox.close();
                },1000); 
                parent.setNotifySuccess(res.msg);
              }else{
                parent.setNotifyError(res.msg);
              }

            }
        });
      }
    });
});

  agentProductRule = function(){
    var params = $('#agentPrdRule').serialize();
    $(".error").html('').hide();
    $.ajax({
      url: $('#agentPrdRule').attr('action'),
      type: 'POST',
      data: params,
      dataType : 'json',
      beforeSend:function(){
                  $("#ajax_loader").show();
                },
      success: function(res) {
        $('#ajax_loader').hide();
        if (res.status == 'success') {
          setTimeout(function(){ 
            window.location.reload();
          },1000); 
          parent.setNotifySuccess(res.msg);
        } else if (res.status == 'fail') {
           var is_error = true;
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
            if(is_error){
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 150;
                $('body,html').animate({scrollTop: totalScroll}, 1200);
                is_error = false;
            }
          });
        }
      }
    });
  }

</script>
<style type="text/css">    
    .controlDiv { position:absolute; z-index:50; top:10px; width: 88px;   height: 88px; right:10px; background:transparent; background-size:100%; padding:4px 3px; }
    .controlDiv table { width:100%; margin:0px; }
    .controlDiv table td { text-align:center; vertical-align:middle; }
    .controlDiv table td a { display:inline-block; }
    .controlDiv table td a.uparrow { background:url(images/up-arrow.png) no-repeat; width:27px; height:14px; cursor:pointer; }
    .controlDiv table td a.uparrow:hover { background:url(images/up-arrow-hover.png) no-repeat; }
    .controlDiv table td a.leftarrow { background:url(images/left-arrow.png) no-repeat; width:16px; height:26px; cursor:pointer; }
    .controlDiv table td a.leftarrow:hover { background:url(images/left-arrow-hover.png) no-repeat; }
    .controlDiv table td a.rightarrow { background:url(images/right-arrow.png) no-repeat; width:16px; height:26px; cursor:pointer; }
    .controlDiv table td a.rightarrow:hover { background:url(images/right-arrow-hover.png) no-repeat; }
    .controlDiv table td a.downarrow { background:url(images/down-arrow.png) no-repeat; width:27px; height:14px; cursor:pointer; }
    .controlDiv table td a.downarrow:hover { background:url(images/down-arrow-hover.png) no-repeat; }

    .rightzoom { display:block;  display:inline-block; position:absolute; z-index:10; margin-top:12px;   margin-left: 12px; }
    .rightzoom ul { padding:2px 2px 5px; margin:0; border-radius: 5px; background-color:#DCDCDC; float:left; width:40px;   box-shadow: 1px 1px 2px 0 rgba(0, 0, 0, 0.2);  border: 1px solid #c4c4c4; }
    .rightzoom ul li{ display:block;  list-style:none; margin:3px; float:left; }
    .rightzoom ul li:first-child{ border-bottom:1px solid #929292;   padding-bottom: 5px;}
    .rightzoom ul li a{ color:#4a4a49;  padding:5px; float:left;   border-radius: 2px; font-size: 19px;
                        padding: 2px 5px; }
    .rightzoom ul li a:hover {color:#157bfb; }
</style>
<div id="load_data">
    <div class="panel panel-default panel-block">
        <div class="panel-heading">
            <div class="panel-title ">
              <h4 class="mn">Tree -  <span class="fw300"><?=$resMember['memberName']?></span> </h4>
            </div>
        </div>
        <div class="panel-body br-b">
            <form id="updAgentForm" action="" method="post" class="theme-form">
                <input type="hidden" name="agent_id" value="<?=$resMember['parent_id']?>" />
                <input type="hidden" name="customer_id" value="<?=$member_id?>" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="disp_agent_id" id="currentAgent" data-name="<?=$resMember['sponsor_name']?>" value="<?= $resMember['sponsor_name'] ." (".$resMember['s_rep_id'] ?>)" readonly>
                                    <label>Current Parent Agent</label>
                                </div>
                            </div>
                            <div class="phone-addon w-70 v-align-top">
                                <button class="btn red-link" type="button" id="changeAgentBtn">Change</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="changeAgentDiv" style="display:none">
                    <div class="col-sm-6">
                        <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                                <div class="form-group height_auto m-b-15">
                                <select id="parentAgentSel" name="new_agent_id" data-live-search="true">
                                    <option data-hidden="true"></option>
                                    <?php if(!empty($newAgent)){
                                            foreach($newAgent as $agent){ 
                                    ?>
                                        <option value="<?=$agent['agentId']?>" data-name="<?=$agent['agentName']?>" ><?=$agent['agentDispId']?> - <?=$agent['agentName']?></option>
                                    <?php } }?>
                                </select>
                                <label>New Parent Agent</label>
                                <span class="error err_new_agent_id"></span>
                                 </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-right" id="updateBtnDiv" style="display:none">
                        <!-- <div>Click submit to make this. change immediately.</div> -->
                        <button class="btn btn-action" type="button" id="updateBtn">Submit</button>
                    </div>
                </div>
                <div id="agentUpdDiv" style="display:none">
                    <strong><span id="currentAgentName">Old Name &nbsp;</span></strong>
                    <i class="fa fa-arrow-circle-o-right fa-lg text-green"></i>
                    <strong><span id="newAgentName"> &nbsp;New Name &nbsp;</span></strong>
                </div>
            </form>
        </div>

        <div class="panel-body text-right">
              <div class="spacetree_action">
                  <a href="javascript:void(0);" class="btn sp_refresh" id="refresh"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                  <a href="member_tree_popup.php?member_id=<?=$_GET['member_id']?>" target="_blank" class="btn sp_link"><i class="fa fa-external-link" aria-hidden="true"></i></a>
              </div>
        </div>
        <div class="panel-body pn">
          <form id="sponsor_search_form" method="POST" action="sponsor_tree.php">
          <input type="hidden" name="is_up_root_check" id="is_up_root_check" value="N">    
          <input type="hidden" name="node_id" id="node_id" value="<?= $node_id ?>">    
          </form>
            <div id="tree-simple"> </div>
        </div>
    </div>
</div>
<script language="javascript">
config = {
    container: "#tree-simple"
};
parent_node = <?=json_encode($sponsorparent)?> ;
<?php foreach($sponsorArr as  $key => $arr){ ?>
    <?=$key?> = {parent:<?=$arr['parent']?>,"innerHTML" : <?= json_encode($arr['innerHTML']) ?>,"HTMLclass":'<?=$arr['HTMLclass']?>' };
<?php  } ?>
simple_chart_config = [
    config, parent_node,<?php foreach($sponsorArr as  $key => $arr){
         ?>
         <?=$key?>,
    <?php } ?>
];
var chart = new Treant(simple_chart_config, function() {  }, $ );
    
    $(document).ready(function () {
        $(".ui-helper-hidden-accessible").remove();
        $('#refresh').click(function () {
            window.location="member_tree_popup.php?member_id=<?=$_GET['member_id']?>";
        });

        // parentAgent update code start

        $('#parentAgentSel').addClass('form-control');
        $('#parentAgentSel').selectpicker({ 
            container: 'body', 
            style:'btn-select',
            noneSelectedText: '',
            dropupAuto:false,
             filter: true            
        });

        $(document).off("click","#changeAgentBtn")
        $(document).on("click","#changeAgentBtn",function(){
            $btnText = $(this).text();
            if($btnText == 'Change'){
                $(this).text("Cancel");
                $("#changeAgentDiv").show();
            }else{
                $(this).text("Change");
                $("#changeAgentDiv").hide();
                $("#agentUpdDiv").hide();
            }
        });

        $(document).off("change","#parentAgentSel");
        $(document).on("change","#parentAgentSel",function(e){
            $newAgent = $("#parentAgentSel option:selected").val();
            if($newAgent != ''){
                $("#updateBtnDiv").show();
                $("#agentUpdDiv").show();
                $("#currentAgentName").text($("#currentAgent").attr('data-name'));
                $("#newAgentName").text(" "+$("#parentAgentSel option:selected").attr('data-name'));
                $(this).selectpicker();
            }else{
                $("#updateBtnDiv").hide();
                $("#agentUpdDiv").hide();
            }
        });

        $(document).off("click","#updateBtn");
        $(document).on("click","#updateBtn",function(e){
            e.preventDefault();
            parent.disableButton($(this));
            $('#ajax_loader').show();
            $.ajax({
                  url:"ajax_change_members_parent_agent.php",
                  data: $("#updAgentForm").serialize(),
                  method: 'POST',
                  dataType: 'json',
                  success: function(res) {
                    $('#ajax_loader').hide();       
                    parent.enableButton($("#updateBtn"));      
                    if (res.status == 'success'){
                        setTimeout(function(){ 
                          window.location.reload();
                        },1000); 
                        parent.setNotifySuccess('Parent Agent Updated Successfully');
                    }else if (res.status == 'fail'){
                      var is_error = true;
                      $('.error span').html('');
                      $.each(res.errors, function (index, value) {
                        $('#err_' + index).html(value).show();
                        if(is_error){
                          var offset = $('#err_' + index).offset();
                          var offsetTop = offset.top;
                          var totalScroll = offsetTop - 50;
                          $('body,html').animate({scrollTop: totalScroll}, 1200);
                          is_error = false;
                        }
                      });
                    }
                    return false;
                  }
            });
        });
       
    });
</script>
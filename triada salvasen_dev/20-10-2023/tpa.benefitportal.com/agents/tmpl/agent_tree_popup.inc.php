<div id="load_data">
    <div class="panel panel-default panel-block">
        <div class="panel-heading">
            <div class="panel-title ">
              <h4 class="mn">Tree -  <span class="fw300"><?=$resAgent['agentName']?></span> </h4>
            </div>
        </div>
        <div class="panel-body br-b">
            <form id="updAgentForm" action="ajax_change_agent.php" method="post" class="theme-form">
                <input type="hidden" name="agent_id" value="<?=$resAgent['id']?>" />
                <div class="row">
                    <div class="col-sm-6">
                        <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="disp_agent_id" id="currentAgent" data-name="<?=$resAgent['sponsor_name']?>" value="<?= $resAgent['sponsor_name'] ." (".$resAgent['s_rep_id'] ?>)" readonly>
                                    <label>Current Parent Agent</label>
                                </div>
                            </div>
                            <?php /*
                            <div class="phone-addon w-70 v-align-top">
                                <!-- <button class="btn red-link" type="button" id="changeAgentBtn">Change</button> -->
                            </div>
                            */?>
                        </div>
                    </div>
                </div>
                <?php /*
                <div class="row" id="changeAgentDiv" style="display:none">
                    <div class="col-sm-6">
                        <div class="phone-control-wrap">
                            <div class="phone-addon text-left">
                                <div class="form-group height_auto m-b-15">
                                <select id="parentAgentSel" name="new_agent_id">
                                    <option></option>
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
                */ ?>
            </form>
        </div>

        <div class="panel-body text-right">
              <div class="spacetree_action">
                  <a href="javascript:void(0);" class="btn sp_refresh" id="refresh"><i class="fa fa-repeat" aria-hidden="true"></i></a>
                  <a href="agent_tree_popup.php?agent_id=<?=$_GET['agent_id']?>" target="_blank" class="btn sp_link"><i class="fa fa-external-link" aria-hidden="true"></i></a>
              </div>
        </div>
        <div class="panel-body pn">
          <form id="sponsor_search_form" method="POST" action="sponsor_tree.php">
          <input type="hidden" name="is_up_root_check" id="is_up_root_check" value="N">    
          <input type="hidden" name="node_id" id="node_id" value="<?= $node_id ?>">    
          </form>
            <div id="infovis" style="width: 100%; position:relative;" >
            </div>
        </div>
        <div class="p-t-20  text-gray text-center">
                <i class="fa fa-arrows fs20" aria-hidden="true"></i><br>
                <span class="fw400 fs18">Click and hold to drag around</span>
        </div>        
    </div>
</div>


<script language="javascript">
    // agent tree code start
    var sc = 1;
    var st;
    function init() {
        var json = [<?= $jsonData ?>];
        $jit.ST.Plot.NodeTypes.implement({
            'nodeline': {
                'render': function (node, canvas, animating) {
                    if (animating === 'expand' || animating === 'contract') {
                        var pos = node.pos.getc(true), nconfig = this.node, data = node.data;
                        var width = nconfig.width, height = nconfig.height;
                        var algnPos = this.getAlignedPos(pos, width, height);
                        var ctx = canvas.getCtx(), ort = this.config.orientation;
                        ctx.beginPath();
                        if (ort == 'left' || ort == 'right') {
                            ctx.moveTo(algnPos.x, algnPos.y + height / 2);
                            ctx.lineTo(algnPos.x + width, algnPos.y + height / 2);
                        } else {
                            ctx.moveTo(algnPos.x + width / 2, algnPos.y);
                            ctx.lineTo(algnPos.x + width / 2, algnPos.y + height);
                        }
                        ctx.stroke();
                    }
                }
            }
        });
        $jit.ST.Plot.EdgeTypes.implement({
            'angle': {
                'render': function (adj, canvas) {
                    var orn = this.getOrientation(adj),
                            nodeFrom = adj.nodeFrom,
                            nodeTo = adj.nodeTo,
                            rel = nodeFrom._depth < nodeTo._depth,
                            from = this.viz.geom.getEdge(rel ? nodeFrom : nodeTo, 'begin', orn),
                            to = this.viz.geom.getEdge(rel ? nodeTo : nodeFrom, 'end', orn),
                            begin = this.viz.geom.getEdge(nodeFrom, 'begin', orn),
                            end = this.viz.geom.getEdge(nodeTo, 'end', orn),
                            ctx = canvas.getCtx();
                    ctx.beginPath();
                    ctx.moveTo(from.x, from.y);
                    ctx.lineTo(from.x, (from.y + to.y) / 2);
                    ctx.lineTo(to.x, (from.y + to.y) / 2);
                    ctx.lineTo(to.x, to.y);
                    ctx.stroke();
                },
            }
        });
        st = new $jit.ST({
            injectInto: 'infovis',
            duration: 0,
            transition: $jit.Trans.Quart.easeIn,
            levelDistance: 35,
            levelsToShow: 1,
            orientation: "top",
            Navigation: {enable: true, panning: true, zooming: 0},
            Node: {height: 70, width: 125, type: 'nodeline', color: '#828282', lineWidth: 1, align: "center", overridable: true},
            Edge: {type: 'angle', lineWidth: 1, color: '#828282', overridable: true},
            Margin: {top: 0, left: 0, right: 0, bottom: 0},
            Tips: {
                enable: false,
                type: 'native',
                offsetX: 20,
                offsetY: 20,
                onShow: function (tip, node) {
                    var tip_val = '<table>';
                    var is_exist = false;
                    for (var k in node.data) {
                        if (k.indexOf('$') != 0) {
                            if (node.data[k] !== null && node.data[k].length > 0 && k !== "user_id") {
                                is_exist = true;
                                tip_val += "<tr><td>" + (k.replace('_', " ")) + '&nbsp;</td><td><b>' + node.data[k] + '</b></td></tr>';
                            }
                        }
                    }
                    tip_val += "</table>";
                    if (is_exist == true) {
                        tip.innerHTML = tip_val;
                    } else {
                        tip.innerHTML = "Open";
                    }
                }
            },
            request: function (nodeId, level, onComplete) {
              console.log(document.getElementById(nodeId));
                var className_id = document.getElementById(nodeId).className.replace("st_label ", "");
                if (className_id) {
                    jQuery.get('ajax_sponsor_tree.php', {userId: className_id}, function (data) {
                        try {
                            if (data.length > 1) {
                                var json2 = eval("(" + data + ")");
                                var ans = {"id": nodeId, 'children': json2.childData};
                                onComplete.onComplete(nodeId, ans);
                            } else {
                                var ans = {"id": nodeId, 'children': []};
                                onComplete.onComplete(nodeId, ans);
                            }
                        } catch (err) {
                        }
                    });
                } else {
                    var ans = {"id": nodeId, 'children': []};
                    onComplete.onComplete(nodeId, ans);
                }
            },
            onBeforeCompute: function (node) {},
            onAfterCompute: function () {},
            onCreateLabel: function (label, node) {
               
                label.id = node.id;
                label.innerHTML = node.name;
                label.className = "st_label " + node.data.user_id;

                $(label).css("-moz-transform", "scale(" + sc + "," + sc + ")")
                $(label).css("-webkit-transform", "scale(" + sc + "," + sc + ")")
                $(label).css("-ms-transform", "scale(" + sc + "," + sc + ")")
                $(label).css("-o-transform", "scale(" + sc + "," + sc + ")")
                label.onclick = function () {
                    $("#node_id").val(node.id);
                    st.onClick(node.id);
                    
                };
                var style = label.style;
                style.width = 125 + 'px';
                style.height = 70 + 'px';
                style.cursor = 'pointer';
                style.color = '#6a6c72';
                style.fontSize = '1.0em';
                style.textAlign = 'center';
                style.textDecoration = 'none';
                style.paddingTop = '0px';

            },
            onBeforePlotNode: function (node) {
                $('.member_popup').colorbox({iframe: true, width: '500px', height: '393px'});
                if (node.selected) {
                    node.data.$color = "#6a6c72";
                } else {
                    delete node.data.$color;
                }
            },
            onBeforePlotLine: function (adj) {
                if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                    adj.data.$color = "#6a6c72";
                    adj.data.$lineWidth = 1;
                } else {
                    delete adj.data.$color;
                    delete adj.data.$lineWidth;
                }
            }
        });
        st.loadJSON(json);
        st.compute();
        st.canvas.translate(0, -220);
        st.onClick(st.root);
    }
    function res(msht, zoomtype) {
        if (sc * msht > 0.1 && sc * msht < 2.25) {
          sc = sc * msht;
          var canv = st.canvas;
          canv.scale(msht, msht);
          $(".st_label").each(function() {
            $(this).css("-moz-transform", "scale(" + sc + "," + sc + ")")
            $(this).css("-webkit-transform", "scale(" + sc + "," + sc + ")")
            $(this).css("-ms-transform", "scale(" + sc + "," + sc + ")")
            $(this).css("-o-transform", "scale(" + sc + "," + sc + ")")
          });
          if (zoomtype == "in")
            st.canvas.translate(0, +50);
          else
            st.canvas.translate(0, -50);
        }
    }
    
    $(document).ready(function () {
        init();

        $(".ui-helper-hidden-accessible").remove();
        $('#refresh').click(function () {
            window.location="agent_tree_popup.php?agent_id=<?=$_GET['agent_id']?>";
        });

        // parentAgent update code start

        $('#parentAgentSel').addClass('form-control');
        $('#parentAgentSel').selectpicker({ 
            container: 'body', 
            style:'btn-select',
            noneSelectedText: '',
            dropupAuto:false,
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
            $('#ajax_loader').show();
            $.ajax({
                  url:"ajax_change_agent.php",
                  data: $("#updAgentForm").serialize(),
                  method: 'POST',
                  dataType: 'json',
                  success: function(res) {
                    $('#ajax_loader').hide();             
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
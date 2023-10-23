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
<script language="javascript">
    var sc = 1;
    var st;
    function init() {
        var json = [<?=$jsonData?>];
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
            duration: 800,
            transition: $jit.Trans.Quart.easeIn,
            levelDistance: 40,
            levelsToShow: 1,
            orientation: "top",
            Navigation: {enable: true, panning: true, zooming: 0},
            Node: {height: 80, width: 125, type: 'nodeline', color: '#828282', lineWidth: 1, align: "center", overridable: true},
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
                    jQuery.get('ajax_agent_tree.php', {userId: className_id}, function (data) {
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
                style.height = 80 + 'px';
                style.cursor = 'pointer';
                style.color = '#333';
                style.fontSize = '1.0em';
                style.textAlign = 'center';
                style.textDecoration = 'none';
                style.paddingTop = '0px';

            },
            onBeforePlotNode: function (node) {
                $('.member_popup').colorbox({iframe: true, href: $(this).attr('data-href'),width: '800px', height: '500px'});
                if (node.selected) {
                    node.data.$color = "#333";
                } else {
                    delete node.data.$color;
                }
            },
            onBeforePlotLine: function (adj) {
                if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                    adj.data.$color = "#333";
                    adj.data.$lineWidth = 3;
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
    function moveDirection(direction) {
        switch (direction) {
            case 'up':
                st.canvas.translate(0, +25);
                break;
            case 'down':
                st.canvas.translate(0, -25);
                break;
            case 'right':
                st.canvas.translate(-25, 0);
                break;
            case 'left':
                st.canvas.translate(+25, 0);
                break;
        }
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

        $('#refresh').click(function () {
            //st.onClick(st.root);
            $get_user='<?=$_GET['sponsor_id']?>'
            if($get_user!=''){
              window.location="agent_tree.php?sponsor_id="+$get_user;
            }else{
              window.location="agent_tree.php";
            }
        });

    });
    $(document).on("click","#up_root",function(){
      $("#is_up_root_check").val('Y');
      $node_id=$("#node_id").val();


      $up_node_id='';

      if($node_id!=<?=$sponsor_id?>)
        $("#agent_search_form").submit();
    });

</script>
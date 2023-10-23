<div class="clearfix">
    <h4 class="m-t-7 pull-left">Resources</h4>
</div>
<div class="row" id="agent_div">
    <?php if(!empty($agentPortal)) {
        $class = '';
        $exitstArr = array();
        $currentModule = '';
        $i=1;
        $se_class = 0;
        foreach($agentPortal as $agent){            
            if($i == 2 || ($i - $se_class == 3)  ){
                $class = 'danger';
                $se_class = $i;
            }else if($i%3 == 0){
                $class = 'primary';
            }else{
                $class = 'info';
            }
                if(empty($exitstArr) || !in_array($agent['module_name'],$exitstArr)){ 
                    $i++;  
            ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="resources_box">
                            <div class="panel panel-default <?= $class ?>">
                            <div class="panel-heading"><?=ucfirst($agent['module_name'])?></div>
                            <div class="panel-body">
                            <ul>
                <?php 
                    $currentModule = $agent['module_name'];
                    foreach($agentPortal as $val){ 
                        if($val['module_name'] == $currentModule){ ?> 
                            <li><a href="javascript:void(0);" data-href="view_resources.php?id=<?=md5($val['id'])?>" class="view_resources"><?=$val['resource_name']?></a></li>        
                <?php array_push($exitstArr,$agent['module_name']); } } ?>
                            </ul>
                            </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>        
        <?php  } }else{
        echo "<div class='col-sm-6 col-md-4'>No record Found!</div>";
    } ?>
</div>
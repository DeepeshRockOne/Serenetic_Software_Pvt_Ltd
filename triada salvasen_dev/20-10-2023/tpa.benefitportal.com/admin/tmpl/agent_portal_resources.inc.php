<div class="clearfix">
    <h4 class="m-t-7 pull-left">Agent Portal Resources</h4>
    <div class="pull-right">
        <div class="m-b-15">
            <div class="note_search_wrap auto_size" id="agent_search_div" style="display: none; max-width: 100%;">
                <div class="phone-control-wrap theme-form">
                    <div class="phone-addon">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="agent_search_close_btn text-light-gray" id="agent_search_close_btn">X</a>
                        </div>
                    </div>
                    <div class="phone-addon w-300">
                        <div class="form-group height_auto mn ">
                            <select class="form-control" name="agent_search" id="agent_search">
                            <option value="" hidden selected></option>
                            <option value="dashboard">Dashboard</option>
                                <?php foreach($agentresModule as $key => $module){  ?>
                                    <option value="<?= $module['title'] ?>"><?= $module['title'] ?></option>
                                <?php } ?>
                            </select>
                            <label>Search</label>
                        </div>
                    </div>
                    <div class="phone-addon w-80">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="btn btn-info agent_search_button" onclick="searchResources('agent','agent_div','')">Search</a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="agent_search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0)" data-href="add_resources.php?type=agent" class="btn btn-action m-l-5 add_resources">+ Resource</a>
        </div>
    </div>
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
    <!-- <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading">Dashboard</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);" data-href="view_resources.php" class="view_resources">Left Menu</a></li>
                    <li><a href="javascript:void(0);">Top Header / Icons / Global Search</a></li>
                    <li><a href="javascript:void(0);">Custom Branding</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">Enroll</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Agent</a></li>
                    <li><a href="javascript:void(0);">Member</a></li>
                    <li><a href="javascript:void(0);">Employer Group</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Pending AAE</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">AAE Post Payments</a></li>
                    <li><a href="javascript:void(0);">AAE Member Enrollments</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading">Book of Business</div>
            <div class="panel-body">
                        <ul>
                        <li><a href="javascript:void(0);">Agents</a></li>
                        <li><a href="javascript:void(0);">Members</a></li>
                        <li><a href="javascript:void(0);">Leads</a></li>
                        <li><a href="javascript:void(0);">Employer Groups</a></li>
                        <li><a href="javascript:void(0);">Products</a></li>
                        </ul>
                </div>
            </div>
            </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">Billing</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Orders</a></li>
                    <li><a href="javascript:void(0);">Transactions</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Commissions</div>
            <div class="panel-body">
                        <ul>
                        <li><a href="javascript:void(0);">Weekly</a></li>
                        <li><a href="javascript:void(0);">Monthly</a></li>
                        <li><a href="javascript:void(0);">Ledger</a></li>
                        </ul>
                    </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading">Communications</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Email</a></li>
                    <li><a href="javascript:void(0);">SMS Text Message</a></li>
                    <li><a href="javascript:void(0);">Communications Queue</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">Reporting</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Quick Sales</a></li>
                    <li><a href="javascript:void(0);">Production</a></li>
                    <li><a href="javascript:void(0);">Book of Business</a></li>
                    <li><a href="javascript:void(0);">Email Snapshots</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Resources</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Training Manuals</a></li>
                    <li><a href="javascript:void(0);">API Integrations</a></li>
                    <li><a href="javascript:void(0);">Support</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div> -->
</div>
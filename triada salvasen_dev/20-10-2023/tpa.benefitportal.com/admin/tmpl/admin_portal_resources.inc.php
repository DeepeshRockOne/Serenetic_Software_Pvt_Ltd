<div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Admin Portal Resources</h4>
    </div>
    <div class="pull-right">
        <div class="m-b-15">
            <div class="note_search_wrap auto_size" id="admin_search_div" style="display: none; max-width: 100%;">
                <div class="phone-control-wrap theme-form">
                    <div class="phone-addon">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="admin_search_close_btn text-light-gray" id="admin_search_close_btn">X</a>
                        </div>
                    </div>
                    <div class="phone-addon w-300">
                        <div class="form-group height_auto mn ">
                            <select class="form-control" name="admin_search" id="admin_search">
                            <option value="" hidden selected></option>
                            <option value="dashboard">Dashboard</option>
                                <?php foreach($adminresModule as $key => $module){  ?>
                                    <option value="<?= $module['title'] ?>"><?= $module['title'] ?></option>
                                <?php } ?>
                            </select>
                            <label>Search</label>
                        </div>
                    </div>
                    <div class="phone-addon w-80">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="btn btn-info admin_search_button" onclick="searchResources('admin','admin_div','')">Search</a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="admin_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0)" data-href="add_resources.php?type=admin" class="btn btn-action m-l-5 add_resources" style="display:inline-block;" >+ Resource</a>
        </div>
    </div>
</div>
<div class="row" id="admin_div">
    <?php if(!empty($adminPortal)) {
        $class = '';
        $exitstArr = array();
        $currentModule = '';
        $i=1;
        $se_class = 0;
        foreach($adminPortal as $admin){            
            if($i == 2 || ($i - $se_class == 3)  ){
                $class = 'danger';
                $se_class = $i;
            }else if($i%3 == 0){
                $class = 'primary';
            }else{
                $class = 'info';
            }
                if(empty($exitstArr) || !in_array($admin['module_name'],$exitstArr)){ 
                    $i++;  
            ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="resources_box">
                            <div class="panel panel-default <?= $class ?>">
                            <div class="panel-heading"><?=ucfirst($admin['module_name'])?></div>
                            <div class="panel-body">
                            <ul>
                <?php 
                    $currentModule = $admin['module_name'];
                    foreach($adminPortal as $val){ 
                        if($val['module_name'] == $currentModule){ ?> 
                            <li><a href="javascript:void(0);" data-href="view_resources.php?id=<?=md5($val['id'])?>" class="view_resources"><?=$val['resource_name']?></a></li>        
                <?php array_push($exitstArr,$admin['module_name']); } } ?>
                            </ul>
                            </div>
                            </div>
                        </div>
                    </div>
            <?php } ?>        
        <?php  } }else{
        echo "<div class='col-sm-6 col-md-4'>No record Found!</div>";
    } ?>
    <!-- <div class="col-sm-6 col-md-4">
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
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">User Groups</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Admins</a></li>
                    <li><a href="javascript:void(0);">Agents</a></li>
                    <li><a href="javascript:void(0);">Employer Groups</a></li>
                    <li><a href="javascript:void(0);">Leads</a></li>
                    <li><a href="javascript:void(0);">Members</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Communications</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Email Broadcaster</a></li>
                    <li><a href="javascript:void(0);">SMS Text Message Broadcaster</a></li>
                    <li><a href="javascript:void(0);">Unsubscribes</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading">Products</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul>
                        <li><a href="javascript:void(0);">Builder</a></li>
                        <li><a href="javascript:void(0);">Carriers</a></li>
                        <li><a href="javascript:void(0);">Memberships</a></li>
                        <li><a href="javascript:void(0);">Providers</a></li>
                        <li><a href="javascript:void(0);">Resources</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <ul>
                        <li><a href="javascript:void(0);">Vendors</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">COMMISSIONS</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Builder</a></li>
                    <li><a href="javascript:void(0);">Advances</a></li>
                    <li><a href="javascript:void(0);">PMPMs</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Payment</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul>
                        <li><a href="javascript:void(0);">Billing Files</a></li>
                        <li><a href="javascript:void(0);">Orders</a></li>
                        <li><a href="javascript:void(0);">Transactions</a></li>
                        <li><a href="javascript:void(0);">Reversals</a></li>
                        <li><a href="javascript:void(0);">Subscriptions</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <ul>
                        <li><a href="javascript:void(0);">Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading">REPORTING</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Custom Reports</a></li>
                    <li><a href="javascript:void(0);">Set Reports</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">Eligibility</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Generator</a></li>
                    <li><a href="javascript:void(0);">History</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">Fulfillment</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Generator</a></li>
                    <li><a href="javascript:void(0);">History</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="resources_box">
            <div class="panel panel-default info">
            <div class="panel-heading"><?= $DEFAULT_SITE_NAME ?></div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Resources</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div> -->
</div>
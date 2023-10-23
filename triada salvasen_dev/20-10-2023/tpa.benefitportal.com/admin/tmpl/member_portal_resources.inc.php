<div class="clearfix tbl_filter">
    <div class="pull-left">
        <h4 class="m-t-7">Member Portal Resources</h4>
    </div>
    <div class="pull-right">
        <div class="m-b-15">
            <div class="note_search_wrap auto_size" id="member_search_div" style="display: none; max-width: 100%;">
                <div class="phone-control-wrap theme-form">
                    <div class="phone-addon">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="member_search_close_btn text-light-gray" id="member_search_close_btn">X</a>
                        </div>
                    </div>
                    <div class="phone-addon w-300">
                        <div class="form-group height_auto mn ">
                            <select class="form-control" name="member_search" id="member_search">
                            <option value="" hidden selected></option>
                            <option value="dashboard">Dashboard</option>
                                <?php foreach($meberresModule as $key => $module){  ?>
                                    <option value="<?= $key ?>"><?= $module ?></option>
                                <?php } ?>
                            </select>
                            <label>Search</label>
                        </div>
                    </div>
                    <div class="phone-addon w-80">
                        <div class="form-group height_auto mn">
                            <a href="javascript:void(0);" class="btn btn-info member_search_button" onclick="searchResources('member','member_div','')">Search</a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0);" class="search_btn" id="member_search_btn"><i class="fa fa-search fa-lg text-blue"></i></a>
            <a href="javascript:void(0)" data-href="add_resources.php?type=member" class="btn btn-action m-l-5 add_resources">+ Resource</a>
        </div>
    </div>
</div>
<div class="row" id="member_div">
    <?php if(!empty($memberPortal)) {
        $class = '';
        $exitstArr = array();
        $currentModule = '';
        $i=1;
        $se_class = 0;
        foreach($memberPortal as $member){            
            if($i == 2 || ($i - $se_class == 3)  ){
                $class = 'danger';
                $se_class = $i;
            }else if($i%3 == 0){
                $class = 'primary';
            }else{
                $class = 'info';
            }
                if(empty($exitstArr) || !in_array($member['module_name'],$exitstArr)){ 
                    $i++;  
            ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="resources_box">
                            <div class="panel panel-default <?= $class ?>">
                            <div class="panel-heading"><?=ucfirst($member['module_name'])?></div>
                            <div class="panel-body">
                            <ul>
                <?php 
                    $currentModule = $member['module_name'];
                    foreach($memberPortal as $val){ 
                        if($val['module_name'] == $currentModule){ ?> 
                            <li><a href="javascript:void(0);" data-href="view_resources.php?id=<?=md5($val['id'])?>" class="view_resources"><?=$val['resource_name']?></a></li>        
                <?php array_push($exitstArr,$member['module_name']); } } ?>
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
                    <li><a href="javascript:void(0);" data-href="view_resources.php" class="view_resources">Top Header / Icons / Global Search</a></li>
                    <li><a href="javascript:void(0);">Product Details</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default danger">
            <div class="panel-heading">+ Product</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Adding a Product</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="resources_box">
            <div class="panel panel-default primary">
            <div class="panel-heading">My Account</div>
            <div class="panel-body">
                <ul>
                    <li><a href="javascript:void(0);">Primary Policy Holder</a></li>
                    <li><a href="javascript:void(0);">Dependents</a></li>
                    <li><a href="javascript:void(0);">Orders</a></li>
                    <li><a href="javascript:void(0);">Account</a></li>
                    <li><a href="javascript:void(0);">Contact Us</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div> -->
</div>
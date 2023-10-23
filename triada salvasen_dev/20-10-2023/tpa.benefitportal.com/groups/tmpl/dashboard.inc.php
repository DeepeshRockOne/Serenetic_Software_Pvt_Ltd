<div class="group_dashboard">
    <div class="container">
        <?php if($display_group_welcome == 'Y') { ?>
            <div class="text-center m-b-30" id="display_group_welcome_div">
                <h1>Hello <span class="fw300"> <?= $group_name ?>!</span></h1>
                <h4>Welcome to your group portal!</h4>
                <a href="javascript:void(0);" data-id="<?= $group_id ?>" id="display_group_welcome" class="remove-group">X</a>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-default panel-block group-dash-table">
                 	<div class="panel-heading">
                    	<span class="fs16 text-uppercase pull-left">Application Websites</span>
                        <a href="<?= $GROUP_HOST ?>/manage_website.php" class="fs16 pull-right red-link">Manage</a>
                   	</div>
                    <div class="panel-body">
                        <div class="dashboard_enroll_weblist" style="max-height: 115px;">
                    	<div class="table-responsive">
                        <table class="<?=$table_class?>" >
                            <tr>
                                <td>Enrollee ID Website</td>
                                <td class="icons" width="80px"> 
                                    <a href="<?=$HOST.'/quote/'.$_SESSION['groups']['user_name']?>" target="_blank"><i class="fa fa-eye"></i></a> 
                                    <a href="javascript:void(0);" data-id="0" title="Share" data-toggle="tooltip" class="share_website">
                                        <i class="fa fa-external-link"></i>
                                    </a> 
                                </td> 
                            </tr>
                            <?php if(!empty($website_res)) { ?>
                                <?php foreach ($website_res as $website_key => $website_data) { ?>
                                    <tr>
                                        <td><?= $website_data['page_name'] ?></td>
                                        <td class="icons" width="80px"> 
                                            <a href="<?=$GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$website_data['user_name']?>" target="_blank"><i class="fa fa-eye"></i></a> 
                                            <a href="javascript:void(0);" data-id="<?= $website_data['id'] ?>" title="Share" data-toggle="tooltip" class="share_website">
                                                <i class="fa fa-external-link"></i>
                                            </a> 
                                        </td> 
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                        </div>
                    </div>
                   	</div>
                </div>
                <div class="panel panel-default panel-block mn group-dash-table">
                 	<div class="panel-heading">
                    	<span class="fs16 text-uppercase pull-left">Billing <?= $billing_type=='list_bill' ? ' - List Bill' : '' ?></span>
                        <a href="<?= $billing_type=='list_bill' ? 'group_billing.php' : 'javascript:void(0)' ?>" class="fs16 pull-right red-link">Details</a>
                   	</div>
                    <div class="panel-body">
                        <?php if($billing_type=='list_bill') { ?>
                        	<ul class="list-group list-cust-bar">
                              <li class="list-group-item cyan-bg">Current Payment <strong class="pull-right"><?= strip_tags(displayAmount2($current_amount)) ?></strong></li>
                              <li class="list-group-item">Due Date <strong class="pull-right"><?= $due_date ?></strong></li>
                            </ul>

                        	<!-- <p class="text-gray fs12 text-right">Auto Payment: 02/01/2020</p> -->
                        <?php }else if($billing_type == "individual") { ?>
                            <span class="text-center"> Members individually billed</span>

                        <?php }else if($billing_type == "TPA"){ ?>
                            <button class="btn btn-action">TPA Managed Billing</button>
                        <?php } ?>
                   </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-default panel-block mn">
                    <div class="panel-body">
                        <div class="clearfix m-b-15">
                            <div class="pull-left">
                                <p class="fs16 text-uppercase fw300 mn"><strong>Enrollees</strong> - Pending Application</p>
                            </div>
                            <div class="pull-right p-t-5">
                                <a href="<?= $GROUP_HOST ?>/group_add_csv_enrollee.php" class="red-link fw600">+ Enrollee</a>
                            </div>
                    </div>
                            <table class="<?=$table_class?>" data-toggle="table" data-height="320" data-mobile-responsive="true" >
                                <thead>
                                    <tr>
                                        <th>Added Date</th>
                                        <th>Plan Period Name</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th width="100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($enrollee_res)) { ?>
                                        <?php foreach ($enrollee_res as $enrollee_key => $enrollee_data) { ?>
                                            <tr>
                                                <td><?= date('m/d/Y',strtotime($enrollee_data['created_at'])) ?></td>
                                                <td><?= $enrollee_data['coverage_period_name'] ?></td>
                                                <td><a href="<?= $GROUP_HOST ?>/lead_details.php?id=<?= $enrollee_data['secured_id'] ?>" class="text-red" target="_blank"><?= $enrollee_data['lead_id'] ?></a></td>
                                                <td><?= $enrollee_data['fname'] .' '. $enrollee_data['lname'] ?></td>
                                                <td class="icons"> 
                                                    <a href="<?= $GROUP_HOST ?>/lead_details.php?id=<?= $enrollee_data['secured_id'] ?>" class="text-red" target="_blank" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></a> 
                                                    <a href="<?= $GROUP_HOST ?>/add_email_broadcast.php?enrollee=<?= $enrollee_data['secured_id'] ?>" target="_blank" data-toggle="tooltip" title="Share"><i class="fa fa-external-link"></i></a> 
                                                </td>            
                                            </tr>
                                        <?php } ?>
                                    <?php }else{?>
                                    <?php } ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="coverage_sec">
	<div class="container">            
        <div class="row theme-form">
        	<div class="col-sm-6 col-md-5 col-xs-12">
                 <h4 class="m-b-30">Plan Periods</h4>
        	     <p class="m-b-15">Select a plan period below to view application information by class.</p>
            	<div class="form-group">
                	<select class="form-control" name="coverage_period" id="coverage_period">
                    	<option selected="selected" ></option>
                        <?php if(!empty($resCoverage)){ ?>
                            <?php foreach ($resCoverage as $coverage_key => $coverage_data) { ?>
                                <option value="<?= $coverage_data['id'] ?>" <?= !empty($default_coverage) && $default_coverage == $coverage_data['id'] ? 'selected' : '' ?>><?= $coverage_data['coverage_period_name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <label>Plan Period Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-xs-12 pull-right" id="active_member_policy_count_div" style="display: none">
            	<div class="sum_activity_wrap">
   
                    <div class="sum_activity_box danger"> 
                      <a href="javascript:void(0)" data-href="active_members_popup.php" data-coverage-id="" id="active_member">Total Active Members <span class="sum_activity_addon" data-toggle="tooltip" data-placement="top" title="" data-original-title="View" id="active_member_html" ></span> 
                      </a> 
                    </div>
                    <div class="sum_activity_box info"> 
                      <a href="javascript:void(0)" data-href="active_policies_popup.php" data-coverage-id="" id="active_policy"> Total Active Policies <span class="sum_activity_addon" data-toggle="tooltip" data-placement="top" title="" data-original-title="View" id="active_policy_html" ></span> 
                      </a> 
                    </div>
                </div>
            </div>
        </div>
        <div id="coverage_period_detail_div" style="display: none">
        </div>
        
    </div>
</div>



<script type="text/javascript">
    $(function(){
        var selected_coverage_period = getCookie('selected_coverage_period');
        if (selected_coverage_period) {
            /* if cookie key 'select' is set change the '#category' selected value 
            and trigger the change() event.*/
            $('#coverage_period').val(selected_coverage_period).addClass('has-value');
            load_coverage_table(selected_coverage_period);
        }     
    });
    $(document).ready(function(){
        $default_coverage = '<?= $default_coverage ?>';

        if($default_coverage > 0){
            load_coverage_table($default_coverage);
        }
    });
    
    $(document).off('click',"#display_group_welcome");
    $(document).on('click',"#display_group_welcome",function(){
        $id = $(this).attr('data-id');
        $("#ajax_loader").show();
        $.ajax({
            url:'<?= $GROUP_HOST ?>/ajax_display_group_welcome.php',
            dataType:'JSON',
            data:{id:$id,display:'N'},
            type:'POST',
            success:function(res){
                $("#ajax_loader").hide();
                if(res.status=='success'){
                    $("#display_group_welcome_div").hide();
                }
            }
        });
    });

    $(document).off('click',".share_website");
    $(document).on('click',".share_website",function(){
        $id = $(this).attr('data-id');
        $.colorbox({
            href:'share_website_link.php?id='+$id,
            iframe:true,
            height:'380px',
            width:'500px'
        });
    });

    $(document).off('change',"#coverage_period");
    $(document).on('change',"#coverage_period",function(e){
        e.stopPropagation();
        $id= $(this).val();
        load_coverage_table($id);
        setCookie('selected_coverage_period', $id);
    });

    load_coverage_table = function($id){
        $group_id = '<?= $group_id ?>';
        if($id > 0){
            $("#ajax_loader").show();
            $.ajax({
                url:'<?= $GROUP_HOST ?>/ajax_load_covereage_detail.php',
                dataType:'JSON',
                data:{id:$id,group_id:$group_id},
                type:'POST',
                success:function(res){
                    $("#ajax_loader").hide();
                    if(res.status=='success'){
                        $("#active_member_html").html(res.active_member);
                        $("#active_member").attr('data-coverage-id',$id);
                        
                        $("#active_policy_html").html(res.active_policy);
                        $("#active_policy").attr('data-coverage-id',$id);

                        $("#active_member_policy_count_div").show();
                        $("#coverage_period_detail_div").html(res.html).show();
                    }
                }
            });
        }else{
            $("#active_member_policy_count_div").hide();
            $("#coverage_period_detail_div").html('').hide();
        }
    }

    $(document).off('click',".coverage_popup");
    $(document).on('click',".coverage_popup",function(){
        $href = $(this).attr('data-href');
        $.colorbox({
            href:$href,
            iframe:true,
            height:'600px',
            width:'800px'
        });
    });

    $(document).off('click',"#active_policy");
    $(document).on('click',"#active_policy",function(){
        $href = $(this).attr('data-href');
        $id = $(this).attr('data-coverage-id');
        $group_id = '<?= $group_id ?>';
        $.colorbox({
            href:$href+'?group_id='+$group_id+'&id='+$id,
            iframe:true,
            height:'600px',
            width:'500px'
        });
    });

    $(document).off('click',"#active_member");
    $(document).on('click',"#active_member",function(){
        $href = $(this).attr('data-href');
        $id = $(this).attr('data-coverage-id');
        $group_id = '<?= $group_id ?>';
        $.colorbox({
            href:$href+'?group_id='+$group_id+'&id='+$id,
            iframe:true,
            height:'600px',
            width:'500px'
        });
    });
    $(document).ready(function () {
        $('.dashboard_enroll_weblist').slimscroll({height: '100%', width: '100%'});
    });

    //Cookie functions for setting and retrieving, the duration I set is 24hrs only.
    function setCookie(key, value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (60 * 60 * 1000));
        document.cookie = key+'='+value+';expires='+expires.toUTCString();
    }

    function getCookie(key) {
        var key = document.cookie.match('(^|;) ?'+key+'=([^;]*)(;|$)');
        return key ? key[2] : null;
    }
</script>
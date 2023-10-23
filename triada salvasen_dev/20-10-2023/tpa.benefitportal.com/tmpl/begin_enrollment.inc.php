<style type="text/css">
    body.iframe, .panel-body{background-color: rgba(32,46,90,0.2) !important;}
</style>
<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">Begin Enrollment</h4>
    </div>
    <div class="panel-body">
        <div id="enrollmentOpen">
            <h5 class="m-t-0 m-b-15">Enter Enrollee ID provided from your group administrator below.</h5>
                <form  role="form" method="post" class="theme-form " name="form_request_enrollment_id" id="form_request_enrollment_id">
                    <input type="hidden" name="api_key" value="checkEnrollmentID">
                    <input type="hidden" name="sponsorId" value="<?=$sponsorId?>">
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <div class="clearfix">
                                <input type="text" class="form-control" placeholder="Enter Enrollment ID" name="enrollmentID" id="enrollmentID">
                            </div>
                        </div>
                        <div class="phone-addon w-110">
                            <!-- <a href="javascript:void(0);" class="btn btn-action enrollmentIDSubmit" data-toggle="tooltip" data-container="body" title="Submit" data-placement="bottom">Submit <i class="fa fa-spinner fa-spin form_loader" style="font-size:24px;display: none;"></i></a> -->
                            <button type="submit" id="enrollmentIDSubmit" class="btn btn-action btn-block">Submit <i class="fa fa-spinner fa-spin form_loader fs18" style="display: none;"></i></button>
                        </div>
                    </div>
                    <p class="error error_enrollmentID" style="display: none;"></p>
                </form>
        </div>
        <div id="enrollmentClose" style="display:none;">
            <p class="text-center mn p-t-20">Sorry, it looks like your open enrollment is closed.</p>
            <p class="text-center">Contact your HR if you think this is incorrect.</p>
        </div>
        <?php /*
        <div class="panel-body">
            <div class="text-center">
                <div class="clearfix m-b-15">
                    <a href="javascript:void(0);" class="btn red-link">Don’t have an Enrollee ID?</a>
                </div>
                <div class="clearfix">
                    <a href="<?=$HOST?>/group_enroll/<?=$username?>" target="_blank" class="btn btn-action-o" data-toggle="tooltip" data-container="body" title="+ New Member">+ New Member</a>
                </div>
            </div>
        </div>
        */ ?>
    </div>
</div>
<script type="text/javascript">
    $(document).off('click','#enrollmentIDSubmit');
    $(document).on('click','#enrollmentIDSubmit',function(e){
        var host = "<?=$HOST?>";
        var pageBuilderId = "<?=$username?>";
        $this = $(this);
        e.preventDefault();
        $(".error").hide();
        $.ajax({
            url: '<?=$HOST?>/ajax_api_call.php',
            type: 'POST',
            data: $("#form_request_enrollment_id").serialize(),
            dataType : 'json',
            beforeSend:function(){
                $this.prop("disabled", true);
                $this.append("<i class='fa fa-spin fa-spinner quote_loader'></i>")
            },
            success: function(res) {
                $this.html("Submit");
                $this.prop("disabled", false);
                $('.form_loader').hide();
                if(res.status == 'Success'){
                    $(".error").val('');
                    if(res.data['beginenrollment'] == 'openenrollment'){
                        window.parent.location = host+"/group_enroll/"+pageBuilderId+"/"+$("#enrollmentID").val();
                    }else if(res.data['beginenrollment'] == 'closeenrollment'){
                        $("#enrollmentOpen").hide();
                        $("#enrollmentClose").show();
                    }
                } else {
                    $.each(res.data, function(key, value) {
                        $('.error_' + key).html(value).show();
                    });
                }
            }
        });
    });
</script>
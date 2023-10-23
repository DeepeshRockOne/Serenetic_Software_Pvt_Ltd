<div class="row">
   <div class="col-md-4">
     <div class="panel panel-default profile-info admin <?=$status_class?>">
        <div class="panel-header ">
            <div class="media">
                <div class="media-body">              
                <h4><?php echo $fname . " " . $lname ?> - <small><?php echo $display_id ?></small></h4>
                </div>
                <div class="media-right">
                    <div class="dropdown">
                      <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" class="btn btn-white text-black" aria-expanded="false">
					  <?=$admin_status?> &nbsp;<i class="fa fa-sort"></i>                             
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
                       <li><a href="javascript:void(0);" class="member_status" data-status="Active" data-href="admins.php?member_status_c=Active&admin_id=<?=$_GET['id']?>&old_status=<?=$admin_status?>">Active</a></li>
                       <li><a href="javascript:void(0);" class="member_status"  data-status="Inactive" data-href="admins.php?member_status_c=Inactive&admin_id=<?=$_GET['id']?>&old_status=<?=$admin_status?>">Inactive</a></li>
                      </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">	
                <table width="100%">
              <tbody>
              	<tr>
              		<td>Level:</td>
              		<td>
              			<select class="form-control max-w175" name="admin_level" id="admin_level" disabled="disabled">
              				<?php if($res_acl){ ?>
              					<?php foreach ($res_acl as $key => $value) { ?>
									<option value="<?=$value['name']?>" <?=$type == $value['name'] ? "selected='selected'" : ""?>><?=$value['name']?></option>				
           						<?php } ?>
              				<?php } ?>
              			</select>

              		</td>
              	</tr>
                <tr>
                    <td width="90px">Email:</td>
                    <td><?php echo $email ?></td>
                </tr>
                 <?php if ($row['phone'] > 0) { ?>
                <tr>
                    <td>Phone:</td>
                    <td><?php echo $mobile_num; ?></td>
                </tr>
              <?php } ?>
                <tr>
                    <td>Password:</td>
                    <td>
                    <div class="password_unlock">
                    <div style="display:none" id="password_popup">
						<div class="phone-control-wrap">
							<div class="phone-addon"><input type="password" class="form-control" name="" id="showing_pass"></div>
							<div class="phone-addon w-65"><button class="btn btn-info" id="show_password">Unlock</button></div>
							</div>
                            </div>
                            <div>
						<input type="password" value="<?=base64_encode($password)?>" name="ad_password" id="ad_password" disabled="disabled" class="dot_password" size="12" maxlength="12">
						<a href="javascript:void(0);" id="click_to_show"><i class="fa fa-eye fa-lg"></i></a>
                        </div>
                     </div>
                     
                     </td>
                </tr>
                <!-- <tr>
                	<td>Access Level:</td>
                	<td>
                		<a href="admins_update_feature.php?id=<?php echo md5($row['id'])?>" class="access_popup" data-toggle="tooltip" title="Update Access Levels"><i class="fa fa-wrench"></i></a>
                	</td>
                </tr> -->
              </tbody>
            </table>
            </div>
            <!-- <div class="right_btn">
                <a href="#" class="btn btn-primary-o">Edit</a>
            </div> -->
        </div>
     </div>
   </div>

   <div class="col-md-8">
      <div class="panel panel-default admin_intrection_wrap">
	  	<div class="ajex_loader" id="intrection_loader" style="display: none;">
			<div class="loader"></div>
		</div>
         <div class="panel-body">
         	 <div class="clearfix ">
              <ul class="nav nav-tabs tabs customtab pull-left nav-noscroll" role="tablist">
                <li role="presentation" class="active"><a href="#note_tab" aria-controls="note_tab" role="tab" data-toggle="tab">Notes</a></li>
                <?php /*?><li role="presentation"><a href="#communications_tab" aria-controls="communications_tab" role="tab" data-toggle="tab">Communications</a></li><?php */?>
              </ul>
              <div class="text-right">
                <a href="#" class="search_btn  " id="srh_btn_note"><i class="fa fa-search fa-lg text-blue"></i></a> 
                <a href="#" class="search_btn search_close_btn" id="srh_close_btn_note" style="display:none;" ><i class="text-light-gray ">X</i></a>
                <a data-href="account_note.php?id=<?=$_GET['id']?>&type=Admin" class="btn btn-action account_note_popup_new m-l-5"><strong>+ Note</strong></a>
                <div class="clearfix"></div>
                <div class="note_search_wrap" id="search_div" style="display:none;">
                <div class="phone-control-wrap">
                <div class="phone-addon">
                <input type="text" class="form-control" id="note_search_keayword" placeholder="Search Keyword(s)" />
                </div>
                <div class="phone-addon w-80">
                <button href="javascript:void(0);" class="btn btn-action-o" id="search_note" >Search</button>
                </div>
                </div>
                </div>
              </div>
             </div>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active pn" id="note_tab">
                   <div class="activity_wrap p-t-0 ">
				   <?php if(count($res_notes) > 0){ ?>
				   <?php foreach($res_notes as $note) { ?>
						<div class="media">
							<div class="media-body fs14 br-n">
								<p class="text-light-gray mn"><?php echo //convert_to_user_date($note['cdate'],'m/d/Y g:i A',$_SESSION['admin']['timezone']);
								$tz->getDate($note['cdate']); ?></p>
								<p class="mn"><?=custom_charecter($note['description'],200,$note['name'],$note['display_id'],$note['admin_id'])?></p>
							</div>
							<div class="media-right text-nowrap">
								<?php 
									if($_SESSION['admin']['id'] == $note['admin_id']){
										if($note['difference'] < 24){
									?>
								<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_admin(<?=$note['id']?>,'')" data-value="Admin"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
								<?php } ?>
								<a href="javascript:void(0);" class="" id="delete_note_id" data-original-title="Delete" onclick="delete_note(<?=$note['id']?>,<?=$note['ac_id']?>)" data-value="Admin"><i class="fa fa-trash fa-lg"></i></a> &nbsp;
								<?php } ?>
								<a href="javascript:void(0);" class="" id="edit_note_id" data-original-title="Edit" onclick="edit_note_admin(<?=$note['id']?>,'view')" data-value="Admin"><i class="fa fa-eye fa-lg"></i></a>
								<!-- <a href="#" data-id="<?=$note['id']?>"><i class="fa fa-edit fa-lg"></i></a> &nbsp;
								<a href="#" data-id="<?=$note['id']?>"><i class="fa fa-eye fa-lg"></i></a> -->
							</div>
						</div>
				   <?php }}else{ ?>
					<p class="text-center mn"> Add first note by clicking the ‘+ Note’ button above. </p>
				   <?php } ?>
                   </div>                       
                </div>
              </div>
           </div>
      </div>       	
   </div>
</div>

<div class="panel panel-default">
        <div class="panel-body">
            <div>
              <!-- Nav tabs -->
              <ul class="nav nav-tabs tabs customtab fixed_tab_top" role="tablist">
                <li role="presentation" class="active"><a href="#account_tab"  data-toggle="tab"  onclick="scrollToDiv($('#account_tab'), 0,'','account_tab');" >Account</a></li>
                <li role="presentation"><a href="#attribute_tab"  data-toggle="tab"  onclick="scrollToDiv($('#attribute_tab'), 0,'','attribute_tab');" >Attributes</a></li>
                <li role="presentation"><a href="#activity_history_tab"  data-toggle="tab" onclick="scrollToDiv($('#activity_history_tab'), 0,'tmpl/activity_feed_admin.inc.php','activity_history_tab');" >Activity History</a></li>
              </ul>
              <!-- Tab panes -->
              <div class="m-t-20">
                <div role="tabpanel" class="tab-pane active" id="account_tab">
                   	<p class="agp_md_title">Account</p>
				   	<form id="frm_profile" name="frm_profile" method="POST" action="<?php echo "update_admin_profile.php?id=" . $_GET['id']?>" class="leadform" enctype="multipart/form-data" autocomplete="off">
						<input type="hidden" name="ip_group_count" value="1" id="ip_group_count">
						<input type="hidden" name="ip_display_counter" value="0" id="ip_display_counter">
						<input type="text" name="fake_email" id="fake_email" value="" style="left: -9999px;position: absolute">    
						<input type="password" name="fake_password" id="fake_password" value="" style="left: -9999px; position: absolute">
						<div class="row theme-form">
							<div class="col-sm-6">	
								<div class="form-group">
									<input class="form-control profile_input" type="text" id="fname" name="fname" value="<?= $fname ?>" />
									<label for="fname">First Name </label>
									<div id="fname_err" class="mid"><span></span></div>
									<p class="error"><span id="error_fname"></span></p>
								</div>
							</div>
							<div class="col-sm-6">	
								<div class="form-group">
									<input class="form-control profile_input" type="text" id="lname" name="lname" value="<?= $lname ?>" />
									<label for="lname">Last Name </label>
									<div id="lname_err" class="mid"><span></span></div>
									<p class="error"><span id="error_lname"></span></p>
								</div>
							</div>
							<div class="col-sm-6">	
								<div class="form-group resetBtnfield">
									<input class="form-control no_space" type="text" id="email" name="email" value="<?= $email ?>" />
									<label for="email">Email </label>
									<div id="email_err" class="mid"><span></span></div>
									<p class="error"><span id="error_email"></span></p>
								</div>
							</div>
							<div class="col-sm-6">	
								<div class="form-group">
									<div class="phone-control-wrap">
										<div class="phone-addon">
											<input type="text" name="mobile_number" value="<?=$phone?>" class="form-control valid_phone"  id="mobile_number">
											<label for="mobile_number">Phone </label>
										</div>
									</div>
									<div id="mobile_number_err" class="mid"><span></span></div>
									<p class="error"><span id="error_mobile_number"></span></p>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<p class="agp_md_title">Login Security</p>
						<div class="row theme-form">
							<div class="col-sm-6">
								<div class="form-group">
									<input type="password" id="password" name="password" class="form-control"  value="" data-error="Password is required" maxlength="12" onblur="check_password(this, 'password_err', 'err_password', event, 'input_validation');" onkeyup="check_password_Keyup(this, 'password_err', 'err_password', event, 'input_validation');">
									<label for="password">Password </label>
									<div id="password_err" class="mid"><span></span></div>
									<p class="error"><span id="error_password"></span></p>
									<div id="pswd_info" class="pswd_popup" style="display: none">
										<div class="pswd_popup_inner">
											<h4>Password Requirements</h4>
											<ul>
												<li id="pwdLength" class="invalid"><em></em>Minimum 8 characters</li>
												<li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter</li>
												<li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowecase letter</li>
												<li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
											</ul>
											<div class="btarrow"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<input type="password" id="c_password" name="c_password" class="form-control"  maxlength="20">
									<label>Confirm Password</label>
									<div id="c_password_err" class="mid"><span></span></div>
									<p class="error"><span id="error_c_password"></span></p>
								</div>
							</div>
							<?php echo generate2FactorAuthenticationUI($row);/*<div class="col-sm-6">
								<div class="phone-control-wrap m-b-25">
									<div class="phone-addon text-left">
										<strong>Two-Factor Authentication (2FA):</strong><br>
										Two-factor authentication is an extra layer of security on login designed to ensure that user is the only person who can access their account, even if someone knows their password.
									</div>
									<div class="phone-addon w-90">
										<div class="custom-switch">
											<label class="smart-switch">
												<input type="checkbox" class="js-switch" name="is_2fa" id="is_2fa" <?=$row['is_2fa']=='Y' ? 'checked' : ''?> value="Y" />
												<div class="smart-slider round"></div>
											</label>
										</div>
									</div>
								</div>
								<div class="2fa_div m-t-25 user_authentication" style="<?=$row['is_2fa']=='Y' ? '' : 'display: none;'?>">
									<div class="phone-control-wrap">
										<div class="phone-addon text-left w-160 p-t-7">
											<input type="radio" name="send_via" id="send_via_email" value="email" <?=$row['send_otp_via']=='email' ? 'checked' : ''?>>Via Email
										</div>
										<div class="phone-addon">
											<div class="form-group">
												<input type="text" name="via_email" value="<?=$row['via_email']?>" class="form-control valid_phone"  id="via_email">
												<label for="via_email">Email Address </label>
												<div id="via_email_err" class="mid"><span></span></div>
												<p class="error text-left"><span id="error_via_email"></span></p>
											</div>
										</div>
									</div>
									<div class="phone-control-wrap">
										<div class="phone-addon text-left w-160 p-t-7">
											<input type="radio" name="send_via" id="send_via_mobile" value="sms" <?=$row['send_otp_via']=='sms' ? 'checked' : ''?>>Via Text Message
										</div>
										<div class="phone-addon">
											<div class="form-group">
												<input type="text" name="via_mobile" value="<?=$row['via_sms']?>" class="form-control valid_phone"  id="via_mobile">
												<label for="via_mobile">Phone Number </label>
												<div id="via_mobile_err" class="mid"><span></span></div>
												<p class="error text-left"><span id="error_via_mobile"></span></p>
											</div>
										</div>
									</div>
									<p class="error"><span id="error_send_via"></span></p>
								</div>
								<div class="phone-control-wrap">
									<div class="phone-addon text-left">
										<strong>IP Address Restriction:</strong><br>
										IP restrictions allow user to specify which IP addresses have access to sign in to their account. We recommend using IP restrictions if user desires to access account when they are in office, mobile, etc.
									</div>
									<div class="phone-addon w-90">
										<div class="custom-switch">
											<label class="smart-switch">
												<input type="checkbox" class="js-switch" name="is_ip_restriction" id="is_ip_restriction" <?=$row['is_ip_restriction']=='Y' ? 'checked' : ''?> value="Y" />
												<div class="smart-slider round"></div>
											</label>
										</div>
									</div>
								</div>
							</div>
							<?php 
								$allowed_ip_res = array();
								if($row['is_ip_restriction'] == 'Y' && !empty($row['allowed_ip'])) {
									$allowed_ip_res = explode(',',$row['allowed_ip']);
								}
							?>
							<div class="clearfix"></div>
							<div class="ip_address_div m-t-25" style="<?=$row['is_ip_restriction']=='Y' ? '' : 'display: none;'?>">
								<div class="col-sm-5 col-sm-offset-1  m-b-25">
									<div id="ip_address_row_div">
										<?php if(!empty($allowed_ip_res)) {
												foreach ($allowed_ip_res as $key => $allowed_ip) { ?>
										<div class="ip_address_row" id="ip_address_row_<?=$key?>" data-id="<?=$key?>">
											<div class="phone-control-wrap">
												<div class="phone-addon">
													<div class="form-group">
														<input type="text" name="allowed_ip_res[<?=$key?>]" class="form-control ip_input" value="<?=$allowed_ip?>">
														<label>IP Address</label>
														<p class="error text-left"><span id="error_ip_address_<?=$key?>"></span></p>
													</div>
												</div>
												<?php if($key > 0) { ?>
													<div class="phone-addon">
														<div class="form-group">
															<a href="javascript:void(0);" class="text-light-gray fw700 remove_ip_address"  data-id="<?=$key?>">X</a>
														</div>
													</div>
												<?php } ?>
											</div>
										</div>
										<?php } } else { ?>
										<div class="ip_address_row" id="ip_address_row_0" data-id="0">
											<div class="form-group">
											<input type="text" name="allowed_ip_res[0]" class="form-control ip_input"  value="<?=checkIsset($allowed_ip[0])?>">
											<label>IP Address</label>
											<p class="error"><span id="error_ip_address_0"></span></p>
											</div>
										</div>
										<?php } ?>
									</div>
									<div class="clearfix"></div>
									<div class="add_ip_address_row text-right">
										<button id="add_ip_address" type="button" class="btn btn-action">+ IP Address</button>
									</div>
								</div>
							</div>
							<div class="clearfix"></div> */?>

						</div>
						<div class="clearfix"></div>
						<div class="clearfix text-center m-t-20" id="btns_submit_cancel">
							<input type="submit" tabindex="13" name="submit" class="btn btn-action" value="Save"/>
						</div>
						<div class="clearfix"></div>
					</form> 
                </div>
                <div role="tabpanel" class="tab-pane" id="attribute_tab">
                	<hr />
                   <p class="agp_md_title">Attributes</p>               
                   <div class="clearfix attributes_btn m-b-20">
				      <!--   <a href="admins_update_feature.php?id=<?php echo md5($row['id'])?>" class="btn btn-info access_popup">Access</a> --> 
				      	<?php if(!empty($row) && $row['status'] == 'Active'){ ?>
				        	<a href="<?=$HOST?>/downloads3bucketfile.php?file_path=<?=urlencode($ADMIN_AGREEMENT_CONTRACT_FILE_PATH)?>&file_name=<?=urlencode($row['admin_contract_file'])?>&user_id=<?=md5($row['id'])?>&location=admin_profile_details" class="btn btn-action btn-outline">Admin Agreement</a>
				    	<?php } ?>
			        </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="activity_history_tab">
                   	
                </div>
              </div>
            </div>
        </div>
    </div>
   

<?=generateIPAddressUI()?>
<script type="text/javascript">
/*scroll div function start */
	function scrollToDiv(element, navheight,url,ajax_div) {
		var str = $("#"+ajax_div).html().trim();
		if(str === '' && url!==''){
		    ajax_get_admin_data(url,ajax_div,'');
		}
		if ($(element).length) {
			var offset = element.offset();
			var offsetTop = offset.top;
			var totalScroll = offsetTop - navheight;
			if ($(window).width() >= 1171) {
				var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 42
			} else {
				var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 42
			}
			$('body,html').animate({
				scrollTop: totalScroll
			}, 1200);
		}
	}
	
	ajax_get_admin_data = function(url,ajax_div,newid){
      	var id = '<?=$_GET['id']?>';
      	if(newid !== '' && newid !== undefined){
         	id = newid;
      	}
	    $.ajax({
	      	url : url,
	      	type : 'POST',
	      	data:{
	        	id:id
	      	},
	      	beforeSend :function(e){
	        	$("#ajax_loader").show();
	      	},
	      	success : function(res){
	        	$("#ajax_loader").hide();
	        	$("#"+ajax_div).html(res);
	        	fRefresh();
	      	}
	    });
  	}

$(document).off('click', '.access_popup');
$(document).on('click', '.access_popup', function(e) {
  e.preventDefault();
  $.colorbox({
    href: $(this).attr('href'),
    iframe: true,
    width: '500px',
    height: '600px',
    // onClosed: function() {
    //   ajax_submit();
    // }
  });
});
$(document).on('change','#admin_level',function(){
	if($(this).val() != ""){
		$("#ajax_loader").show();
		$level = $(this).val();
		$admin_id = '<?=$_GET['id']?>';
		$.ajax({
			url:'admin_profile.php',
			data:{level:$level,id:$admin_id},
			method:'post',
			dataType: 'json',
			success:function(res){
				$("#ajax_loader").hide();
				setNotifySuccess("Level Changed Successfully");
			}
		});
	}else{
		alert("Please Select Level");
	}
});


$(document).on('click','#search_note',function(){
	$("#ajax_loader").show();
	// var date_from = $("#note_fromdate").val();
	// var date_to = $("#note_todate").val();
	var note_search_keayword = $("#note_search_keayword").val();
	var id = '<?=$_GET['id']?>';
	if(note_search_keayword!==''){
	$.ajax({
		url:'admin_profile.php',
		data:{note_search_keayword:note_search_keayword,id:id},
		method:'post',
		dataType: 'html',
		success:function(res){
			$("#ajax_loader").hide();
			$("#note_tab").html(res);
			$(".activity_wrap").mCustomScrollbar({
			theme:"dark"
			});
		}
	});
	}else{
		alert("Enter Serach Notes, Keywords, Timestamp, etc.");
		$("#ajax_loader").hide();
		// return false;
	}
});

$(document).ready(function() {
	checkEmail();
  if ($(window).width() >= 1199) {
      $(window).scroll(function() {
      if ($(this).scrollTop() > 455) {
         $('.fixed_tab_top').addClass('fixed');
      } else {
         $('.fixed_tab_top').removeClass('fixed');
      }
   });
   }
	$("#note_search_keayword").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$(".activity_wrap div.media").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});

	$('#srh_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $(this).hide();
            $("#srh_close_btn_note").show();
            $("#search_div").slideDown();
            $('.activity_wrap').addClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
        });
        $('#srh_close_btn_note').click(function (e) {
            e.preventDefault(); //to prevent standard click event
            $("#search_div").slideUp();
            $("#srh_close_btn_note").hide();
            $("#srh_btn_note").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
			$('.activity_wrap').mCustomScrollbar("update");
			$("#note_search_keyword").val('');
			var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'notes','admin_profile.php');
        });
	$('#note_custom_date').change(function(){
	if($('#note_custom_date').val() == 'Range') {
		$('#dt_range_note').css({  display: 'inline-block' });
		$("#note_from_date").show();
		$(".note_to_date").show();
	} else if($('#note_custom_date').val() == ''){
		$('#dt_range_note').css({ display: 'none' });
	}else{
		$("#note_from_date").hide();
		$('#dt_range_note').css({ display: 'inline-block' });
		$(".note_to_date").hide();
	}
	});
	
	$(".activity_wrap").mCustomScrollbar({
	theme:"dark"
	});
	var not_win = '';
	$(".account_note_popup_new").on('click',function(){
		$href = $(this).attr('data-href');
		var not_win = window.open($href, "_blank", "width=700px,height=600px");
		if(not_win.closed) {  
			alert('closed');  
		} 
	});
});

$(document).on('click','#click_to_show',function(){
	if($("#ad_password").attr('type') === 'password')
	{
		$("#password_popup").show();
	}else{
		$("#ad_password").attr('type','password');
		$("#ad_password").val('<?=base64_encode($password)?>');
	}
});

$(document).on('click','#show_password',function(){
	if($("#showing_pass").val() === '5401')
	{
		$("#ajax_loader").show();
		$("#showing_pass").val("");
		$("#password_popup").hide();
		var id = '<?=$_GET['id']?>';
		$.ajax({
			url:'admin_profile.php',
			method : 'POST',
			data : {id:id,show_pass:"show_pass"},
			success:function(){
				$("#ajax_loader").hide();
				$("#ad_password").attr('type','text');
				$("#ad_password").val('<?=$password?>');
			}
		});
		
	}else{
			$("#password_popup").hide();
		}
});

$(document).on('click','.member_status',function () {
		var $href = $(this).data('href');
		swal({
			//title: "Are you sure ",
			text: "Change Status: Are you sure?",
			//type: "warning",
			showCancelButton: true,
			//confirmButtonColor: "#DD6B55",
			confirmButtonText: "Confirm",
			//showCloseButton: true
		}).then(function () {
			window.location = $href;
		});
	});	

! function($) {
	"use strict";
	var SweetAlert = function() {};
	//examples
	SweetAlert.prototype.init = function() {
			$('.status_s').change(function() {
				var id = $(this).attr('id').replace('status_s_', '');
				var status_s = $(this).val();
				
				$.ajax({
					url: 'ajax_update_profile_status.php',
					data: {
						id: id,
						status: status_s
					},
					method: 'POST',
					dataType: 'json',
					success: function(res) {
						if (res.status == 'success') {
						} else {
						}
					}
				});
			});
			// });
		},
		$.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
}(window.jQuery),
//initializing
function($) {
	"use strict";
	$.SweetAlert.init()
}(window.jQuery);

$(document).ready(function() {
	$("#mobile_number,#via_mobile").mask("(999) 999-9999");
	$(".popup").colorbox({
		className: 'col_responsive',
		href: this.href,
		iframe: true,
		width: '35%',
		height: '460px'
	});

	/************ Validation Start *************************/
	//validations
	$('#fname').blur(function() {
		if ($(this).val().trim() == "") {
			$('#error_fname').html('First name is required');
			$(this).focus();
			return false;
		} else {
			$('#error_fname').html('');
		}
	});
	$('#lname').blur(function() {
		if ($(this).val().trim() == "") {
			$('#error_lname').html('Last name is required');
			$(this).focus();
			return false;
		} else {
			$('#error_lname').html('');
		}
	});
	$('#email').blur(function() {
		if ($(this).val().trim() == "") {
			$('#error_email').html('Email is required');
			$(this).focus();
			return false;
		} else {
			$('#error_email').html('');
		}
	});
	// $('#password').blur(function() {
	// 	if ($(this).val().trim() == "") {
	// 		$('#error_password').html('Password is required');
	// 		$(this).focus();
	// 		return false;
	// 	} else {
	// 		$('#error_password').html('');
	// 	}
	// });
	$('#mobile_number').blur(function() {
		var mVal = $('#mobile_number').val();
		if (mVal.trim() == "(___) ___-____") {
			$('#error_mobile_number').html('Mobile number is required');
			$(this).focus();
			return false;
		}else {
			$('#error_mobile_number').html('');
		}
	});

	$('#admin_status').on("blur", function(e) {
		if ($(this).val().trim() == "") {
			$('#error_status').html('Status is required');
			$(this).focus();
			return false;
		} else {
			$('#error_status').html('');
		}
	});
/************** Validation End *************************/
// Submit admin profile and validate it
$('#frm_profile').submit(function(e) {
	e.preventDefault();
	$("#ajax_loader").show();
	$(".error").hide();
	$.ajax({
		url: $(this).attr('action'),
		data: $(this).serialize(),
		method: 'POST',
		dataType: 'json',
		success: function(res) {
			$("#ajax_loader").hide();
			if (res.status == 'success') {
				$('.required').html('');
				$('#error_password').hide();
				$('#error_password_chk').hide();
				setNotifySuccess("Admin Profile updated Successfully");
				window.location.reload();
				return false;
				//alert(res.msg);
			} else if (res.status == 'fail') {
				$.each(res.errors, function(key, value) {
					$('#error_' + key).parent("p.error").show();
					$('#error_' + key).html(value).show();
				});
			}
			return false;
		}
	});
});
});
var total_groups = 0;
var loading = false;
var track_load = 0;
</script>
<script type="text/javascript">
function edit_note_admin(note_id, t) {
	// var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	var url = "";
	var user_type = $("#edit_note_id").attr("data-value");
	var show = "";
	if(t === 'view')
	{
		show = "show";
	}
	var customer_id = '<?=$_GET['id']?>';
	if (user_type == 'Admin') {
		url = 'admin_profile.php' 
	} else {
		url = "admin_profile.php";
	}
	if (user_type == 'View' || user_type == 'Admin') {
		$.colorbox({
			iframe: true,
			width: '700px',
			height: '365px',
			href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
		});
	} else {
		window.location.href = url + "?id=" + '<?=$_REQUEST['id']?>' +"&note_id=" + note_id;
	}
}
function delete_note(note_id, activity_feed_id) {
	var id = '<?=$_REQUEST['id']?>';
	var user_type = $("#delete_note_id").attr("data-value");
	var url = "";
	if (user_type == 'Admin') {
		url = 'admin_profile.php' 
	} else {
		url = "admin_profile.php";
	}
	swal({
		text: "Delete Note: Are you sure?",
		showCancelButton: true,
		confirmButtonText: "Confirm",
	}).then(function () {
		$.ajax({
			url: 'ajax_general_note_delete.php',
			data: {
				note_id: note_id,
				activity_feed_id: activity_feed_id,
				usertype:'Admin',
        		user_id :id,
			},
			dataType: 'json',
			type: 'post',
			success: function (res) {
				if (res.status == "success") {
					// window.location = url + '?id=' + id;
					interactionUpdate(id,'notes','admin_profile.php');
					setNotifySuccess('Note deleted successfully.');
				}
			}
		});
	}, function (dismiss) {

	});
}
// function email_resend(trigger_id, email,type='email') {
// 	swal({
// 		//title: "Are you sure?",
// 		text: "Are you sure you want to resend "+ type +"?",
// 		//type: "warning",
// 		showCancelButton: true,
// 		//confirmButtonColor: "#DD6B55",
// 		confirmButtonText: "Yes",
// 		cancelButtonText: "Cancel",
// 		//closeOnConfirm: false,
// 		//closeOnCancel: false
// 	}).then(function () {
// 		$.ajax({
// 			url: 'ajax_trigger_resend.php',
// 			data: {
// 				trigger_id: trigger_id,
// 				email: email,
// 				type:type
// 			},
// 			dataType: 'json',
// 			type: 'post',
// 			success: function (res) {
// 				if (res.status == "success") {
// 					swal("Good job!", type + " send successfully!", "success");
// 				}
// 			}
// 		});
// 	}, function (dismiss) {

// 	});
// }

$(function() {
     $('.admin_intrection_wrap').matchHeight({
         target: $('.profile-info')
     });
});
$(window).on('resize load', function(){
   if ($(window).width() <= 1170) {
      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
      autoResizeNav();
   }
});

function autoResizeNav(){
   if ($('.nav-tabs:not(.nav-noscroll)').length){
      ;(function() {
        'use strict';
         $(activate);
         function activate() {
         $('.nav-tabs:not(.nav-noscroll)')
           .scrollingTabs({
               scrollToTabEdge: true,
               enableSwiping: true  
            })
        }
      }());
   }
}

<?=generate2FactorAuthenticationJS()?>
</script>
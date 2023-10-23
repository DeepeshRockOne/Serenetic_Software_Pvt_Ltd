<script type="text/javascript">
	//*********** General Code Start **************
	//*********** General Code End   **************

	$(document).ready(function(){
		$(".activity_wrap").mCustomScrollbar({
		    	theme:"dark"
		});
		//***********  Right Box Code Start **************
			$(".send_communication").hide();
		//***********  Right Box Code End   **************

		//***********  Tabs Code Start **************
			
		//***********  Tabs Code End   **************

		//***********  Code Start **************
		//***********  Code End   **************
		$("#interaction_search_keyword").on("keyup", function() {
	        var value = $(this).val().toLowerCase();
	        $(".activity_wrap_interaction div.media").filter(function() {
	        	$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	        });
	    });
	    $("#note_search_keyword").on("keyup", function() {
	        var value = $(this).val().toLowerCase();
	        $(".activity_wrap_note div.media").filter(function() {
	        	$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	        });
	    });
	});

	$(document).off('click',".agent_tree_popup");
	$(document).on('click',".agent_tree_popup",function(e){
	    $href = $(this).attr('data-href');
	    $.colorbox({
	        iframe:true,
	        href:$href,
	        width: '900px',
	        height: '650px'
	    });
	});

	//*********** Left Box Code Start **************
		$(document).off('click','#show_reason');
	  	$(document).on('click','#show_reason',function(e){
	    	$("#profile_table").hide();
	    	$(".reason_info").show();
	    	$("#close_reason").show();
	  	});

	  	$(document).off('click','#close_reason');
	  	$(document).on('click','#close_reason',function(e){
		    $(this).hide();
		    $("#profile_table").show();
		    $(".reason_info").hide();
	  	});

		$(document).off("click", ".group_status");
		$(document).on("click", ".group_status", function(e) {
			e.stopPropagation();
			var id = '<?=$_GET['id']?>';
			var new_status = $(this).attr('data-status');
			var old_status = "<?=$row['status']?>";

			$colorboxHeight = "240px";
			if(new_status === 'Active'){
				$colorboxHeight = "240px";
			}else if(new_status === 'Suspended'){
				$colorboxHeight = "330px";
			}else if(new_status === 'Terminated'){
				$colorboxHeight = "480px";
			}

			$href = "change_group_status.php?group_id="+id+"&new_status="+new_status+"&old_status="+old_status+"&from=detail_page";
			$.colorbox({
				iframe:true,
				width: "500px",
				height: $colorboxHeight,
				closeButton: false,
				href: $href,
				overlayClose: false,
				escKey: false,
				onClosed : function(){
				}
			});

			return false;
			/*
		      var $txt = '';
		      if(member_status === 'Active'){
		          $txt = 'Contracted Status: Contracted status allows group to login to account, continues payment of renewal commissions, and allows new enrollments.';
		      }else if(member_status === 'Suspended'){
		          $txt = 'Suspended Status: Suspended status allows group to login to account, continues payment of renewal commissions, but stops new enrollments.'
		      }else if(member_status === 'Terminated'){
		          $txt = 'Terminated Status: Terminated status blocks group access to login to account, stops payment of renewal commissions, and stops new enrollments. <p class="fs14 m-b-15">Additionally setting a termination status will also  terminate all policies of account as of selection below :</p> <div class="text-center"><a href="" class="btn btn-action-o">Set Termination Date</a></div>';
		      }
		      swal({
		          title: "<h4>Are you sure you want to change the status for this Group to <span class='text-blue'>"+member_status+"</span>?</h4>",
		          html:'<p class="fs14">'+$txt+'</p>',
		          showCancelButton : true,
		          confirmButtonColor : '#bd4360',
		          confirmButtonText: "Confirm",
		          cancelButtonText: "Cancel",
		      }).then(function() {
		          if (member_status == 'Terminated' || member_status == 'Suspended') {
		              $.colorbox({
		                  iframe: true,
		                  href: "<?=$ADMIN_HOST?>/reason_change_group_status.php?id=" + id + "&status=" + member_status+"&old_status="+old_status+"&from=detail_page",
		                  width: '600px',
		                  height: '260px',
		                  trapFocus: false,
		                  closeButton: false,
		                  overlayClose: false,
		                  escKey: false
		              });
		          } else {
		              $.ajax({
		                  url: 'change_group_status.php',
		                  data: {
		                      id: id,
		                      status: group_status
		                  },
		                  method: 'POST',
		                  dataType: 'json',
		                  success: function(res) {
		                      if (res.status == "success") {
		                          setNotifySuccess(res.msg);
		                          location.reload();
		                      } else {
		                          setNotifyError(res.msg);
		                      }
		                  }
		              });
		          }
		      }, function(dismiss) {
		      })
			*/
	  	});

	  	$(document).off('click',"#click_to_show");
	  	$(document).on('click','#click_to_show',function(){
		    if($("#ad_password").attr('type') === 'password')
		    {
		      $("#password_popup").show();
		    }else{
		      $("#ad_password").attr('type','password');
		      $("#ad_password").val('<?=base64_encode($password)?>');
		    }
	  	});
	  	
	  	$(document).off('click',"#show_password");
		$(document).on('click','#show_password',function(){
		    if($("#showing_pass").val() === '5401'){
		     	$("#ajax_loader").show();
		      	$("#showing_pass").val("");
		      	$("#password_popup").hide();
		      	var id = '<?=$_GET['id']?>';
		      	$.ajax({
		        	url:'groups_details.php',
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
	//*********** Left Box Code End   **************
	
	//*********** Right Box Code Start **************
		$(document).off("click",".interaction_class");
		$(document).on("click",".interaction_class",function(e){
		    $(".note_div").hide();
		    $(".interaction_div").show();
	  	});

	  	$(document).off("click",".note_class");
	  	$(document).on("click",".note_class",function(e){
		    $(".interaction_div").hide();
		    $(".note_div").show();
	  	});

	  	$(document).off("click",".communication_class");
	   	$(document).on("click",".communication_class",function(e){
	      $(".note_div").hide();
	      $(".interaction_div").hide();
	   	});

	   	$('#srh_btn_interaction').click(function(e){
        	e.preventDefault(); //to prevent standard click event
         	$(this).hide();
         	$("#srh_close_btn_interaction").show();
         	$("#search_interaction").slideDown();   
         	$('.activity_wrap').addClass('interaction_filter_active');
         	$('.activity_wrap').mCustomScrollbar("update"); 
      	});
      	$('#srh_close_btn_interaction').click(function(e){
            e.preventDefault(); //to prevent standard click event
            $(this).hide();
            $("#search_interaction").slideUp();
            $("#srh_btn_interaction").show();
            $("#srh_close_btn_interaction").hide();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
            $("#interaction_search_keyword").val('');
            var id = '<?=$_GET['id']?>';
			interactionUpdate(id,'interaction','groups_details.php');
      	});
      	$(document).off('click','#search_btn_interaction');
	   	$(document).on('click','#search_btn_interaction',function(){
	      	$("#ajax_loader").show();
	      	var interaction_search_keyword = $("#interaction_search_keyword").val();
	      	var id = '<?=$_GET['id']?>';
	      	if(interaction_search_keyword!==''){
	      		$.ajax({
	      			url:'groups_details.php?id='+id,
	      			data:{interaction_search_keyword:interaction_search_keyword,id:id},
	      			method:'post',
	      			dataType: 'html',
	      			success:function(res){
	         			$("#ajax_loader").hide();
	         			$("#interactions_tab").html(res);
	         			$(".activity_wrap").mCustomScrollbar({
	         				theme:"dark"
	         			});
	      			}
	      		});
	      	}else{
	      		alert("Please Enter Search Keyword(s)");
	      		$("#ajax_loader").hide();
	      	}
	   	});
      	$('#srh_btn_note').click(function(e){
            e.preventDefault(); //to prevent standard click event
            $(this).hide();
            $("#srh_close_btn_note").show();
            $("#search_note").slideDown();
            $('.activity_wrap').addClass('interaction_filter_active');
         	$('.activity_wrap').mCustomScrollbar("update");			
     	});
     	$('#srh_close_btn_note').click(function(e){
            e.preventDefault(); //to prevent standard click event
            $("#search_note").slideUp();
            $("#srh_close_btn_note").hide();
            $("#srh_btn_note").show();
            $('.activity_wrap').removeClass('interaction_filter_active');
            $('.activity_wrap').mCustomScrollbar("update");
            $("#note_search_keyword").val('');
            var id = '<?=$_GET['id']?>';
            interactionUpdate(id,'notes','groups_details.php');
     	});

      	$(document).off('click',".account_note_popup_new");
      	$(document).on('click',".account_note_popup_new",function(e){
			e.preventDefault();
        	$href = $(this).attr('data-href');
        	var not_win = window.open($href, "_blank", "width=500,height=600");
      	});

      	$(document).off('click',".interactions_note");
      	$(document).on('click','.interactions_note',function(e){
        	var $href = $(this).attr('data-href');
        	$.colorbox({
          		href:$href,
          		iframe: true,
          		width: '800px',
          		height: '500px',
          		className: 'white-close'
          });
    	});

    	function delete_interaction(interaction_id) {
		    var id = '<?=$_GET['id']?>';
		    var url = "";
		    url = "groups_details.php";
		    swal({
		      text: "Delete Interaction: Are you sure?",
		      showCancelButton: true,
		      confirmButtonText: "Confirm",
		      cancelButtonText: "Cancel",
		    }).then(function () {
		      $.ajax({
		        url: 'ajax_group_interaction_add.php',
		        data: {
		          type : "delete",
		          interaction_detail_id: interaction_id,
		          group_id : '<?=$_GET['id']?>'
		        },
		        dataType: 'json',
		        type: 'post',
		        success: function (res) {
		          if (res.status == "success") {
					// window.location = url + '?id=' + id;
					interactionUpdate(id,'interaction','groups_details.php');
		            setNotifySuccess('Interaction deleted successfully.');
		          }
		        }
		      });
		    }, function (dismiss) {

		    });
	  	}	

	  	function edit_note_agent(note_id, t) {
		    var user_type = $("#edit_note_id").attr("data-value");
		    var show = "";
		    if(t === 'view')
		    {
		      show = "show";
		    }
		    var customer_id = '<?=$_GET['id']?>';
		    url = "groups_details.php";
		    if (user_type == 'View' || user_type == 'Group') {
		      $.colorbox({
		        iframe: true,
		        width: '800px',
		        height: '400px',
		        href: "account_note.php?id=" + customer_id + "&note_id=" + note_id + "&type=" + user_type +"&show="+show
		      });
		    } else {
		      window.location.href = url + "?id=" + '<?=$_GET['id']?>' +"&note_id=" + note_id;
		    }
	  	}

  		function delete_note(note_id, activity_feed_id) {
		    var id = '<?=$_REQUEST['id']?>';
		    var url = "";
		    url = "groups_details.php";
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
		          usertype:'Group',
		          user_id :id,
		        },
		        dataType: 'json',
		        type: 'post',
		        success: function (res) {
		          if (res.status == "success") {
					// window.location = url + '?id=' + id;
					interactionUpdate(id,'notes','groups_details.php');
		            setNotifySuccess('Note deleted successfully.');
		          }
		        }
		      });
		    }, function (dismiss) {

		    });
	  	}

  		$(document).off('click','#search_btn_note');
	  	$(document).on('click','#search_btn_note',function(){
	    	$("#ajax_loader").show();
		    var note_search_keyword = $("#note_search_keyword").val();
		    var id = '<?=$_GET['id']?>';
		    if(note_search_keyword!==''){
		    $.ajax({
		      url:'groups_details.php?id='+id,
		      data:{note_search_keyword:note_search_keyword,id:id},
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
		      alert("Please Enter Search Keyword(s)");
		      $("#ajax_loader").hide();
		    }
	  	});

	  	$(document).off('click','.group_send_trigger');
   		$(document).on('click','.group_send_trigger',function(e){
	      	e.preventDefault();
    	  	var $name = $(this).attr('data-name');
	      	var $id = $(this).attr('data-id');
	      	var group_id = '<?=$_GET['id']?>';
	      	var $href = 'group_send_trigger.php?group_id='+group_id+'&id='+$id+'&name='+$name;
	      	$.colorbox({
		         iframe: true,
		         href:$href,
		         width: '1024px', 
		         height: '767px'
	      	});
   		});
   		function changeCommunication(element){
	      var $value = element.val();
	      var $type = element.find(':selected').attr('data-type');
	      var $name = element.find(':selected').text();
	      $(".group_send_trigger").show();
	      $(".send_communication").attr('data-id',$value);
	      $(".send_communication").attr('data-name',$name);

	      if($type === 'SMS'){
	         $("#send_sms").show();
	         $("#send_email").hide();
	      }else if($type === 'Email'){
	         $("#send_email").show();
	         $("#send_sms").hide();
	      }else if($type =='Both' && $type !== undefined){
	         $(".send_communication").show();
	      }else{
	         $(".send_communication").hide();
	         $(".send_communication").removeAttr('data-id');
	         $(".send_communication").removeAttr('data-name');
	      }
	   }
	   function sendEmailSMS(type){
	      var  $id = $('.send_communication').attr('data-id');
	      $.ajax({
	         url:"ajax_group_send_email_sms.php",
	         data : {
	            is_direct : 1,
	            sent_via : type,
	            trigger_id : $id,
	            customer_id :'<?=$_GET['id']?>',
	         },
	         dataType : 'json',
	         type:'post',
	         beforeSend : function(e){
	            $("#ajax_loader").show();
	         },
	         success :function(res){
	            $("#ajax_loader").hide();
	            console.log(res);

	            if(res.status =='success'){
	               setNotifySuccess(res.msg);
	            }else if(res.status == 'fail'){
	               setNotifyError(res.msg);
	            }
	         }
	      });
	   }
	//*********** Right Box Code End   **************
	
	//*********** Tabs Code Start **************
		function scrollToDiv(element, navheight,url,ajax_div,scroll_to) {
		  var str = $("#"+ajax_div).html().trim();
		    if(str === '' && url!==''){
		    ajax_get_group_data(url,ajax_div);
		      setTimeout(function(){
		        if(scroll_to === 'gp_attributes'){
		          scrollToDiv(element, navheight,url,scroll_to);
		          if(scroll_to === 'gp_attributes')
		            $("#data_gp_attributes").click();
		          return false;
		        }
		      }, 1500);
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
	  	ajax_get_group_data = function(url,ajax_div){
		    $.ajax({
		      url : url,
		      type : 'POST',
		      data:{
		        id:'<?=$_GET['id']?>'
		      },
		      beforeSend :function(e){
		        $("#ajax_loader").show();
		      },
		      success : function(res){
		        $("#ajax_loader").hide();
		        $("#"+ajax_div).html(res);
		        fRefresh();
		        common_select();
		        $('#is_branding, #display_in_member').uniform();
		      }
		    });
		}
		function refreshControl(id_class)
		  {
		    $(id_class).addClass('form-control');
		    $(id_class).selectpicker({ 
		        container: 'body', 
		        style:'btn-select',
		        noneSelectedText: '',
		        dropupAuto:false,
		    });
		    //$("input[type='checkbox']").uniform();
		 }
	//*********** Tabs Code End   **************
</script>
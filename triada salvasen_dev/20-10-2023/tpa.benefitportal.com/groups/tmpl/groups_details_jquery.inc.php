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

	  	$(document).off('click', '.group_status');
	  	$(document).on("click", ".group_status", function(e) {
		      e.stopPropagation();
		      var id = '<?=$group_id?>';
		      var group_status = $(this).attr('data-status');
		      var $txt = '';
		      if(member_status === 'Active'){
		          $txt = 'Contracted Status: Contracted status allows group to login to account, continues payment of renewal commissions, and allows new enrollments.';
		      }else if(member_status === 'Suspended'){
		          $txt = 'Suspended Status: Suspended status allows group to login to account, continues payment of renewal commissions, but stops new enrollments.'
		      }else if(member_status === 'Terminated'){
		          $txt = 'Terminated Status: Terminated status blocks group access to login to account, stops payment of renewal commissions, and stops new enrollments. <p class="fs14 m-b-15">Additionally setting a termination status will also  terminate all policies of account as of selection below :</p> <div class="text-center"><a href="" class="btn btn-action-o">Set Termination Date</a></div>';
		      }
		      swal({
		          text: "Change Status: Are you sure?",
		          showCancelButton: true,
		          confirmButtonText: "Confirm",
		      }).then(function() {
		          if (group_status == 'Terminated' || group_status == 'Suspended') {
		              $.colorbox({
		                  iframe: true,
		                  href: "<?=$ADMIN_HOST?>/reason_change_group_status.php?id=" + id + "&status=" + group_status,
		                  width: '600px',
		                  height: '260px',
		                  trapFocus: false,
		                  closeButton: false,
		                  overlayClose: false,
		                  escKey: false,
		                  onClosed: function() {
		                      $.ajax({
		                          url: "reason_change_group_status.php",
		                          type: 'POST',
		                          dataType: 'json',
		                          data: {
		                              customer_id: id,
		                              action: 'OldStatus'
		                          },
		                          success: function(data) {
		                              if (data.status == 'success') {
		                                  $status = data.group_status;
		                                  $('.group_status [value=' + $status + ']').attr('selected', 'true');
		                              }
		                          }
		                      });
		                  }
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
		                      }else{
		                          setNotifyError(res.msg);
		                      }
		                  }
		              });
		          }
		      }, function(dismiss) {
		      })
	  	});
	  	
	  	$(document).off('click','.group_send_trigger');
   		$(document).on('click','.group_send_trigger',function(e){
	      	e.preventDefault();
    	  	var $name = $(this).attr('data-name');
	      	var $id = $(this).attr('data-id');
	      	var group_id = '<?=$group_id?>';
	      	var $href = 'group_send_trigger.php?group_id='+group_id+'&id='+$id+'&name='+$name;
	      	$.colorbox({
		         iframe: true,
		         href:$href,
		         width: '1024px', 
		         height: '767px'
	      	});
   		});
   		
	   function sendEmailSMS(type){
	      var  $id = $('.send_communication').attr('data-id');
	      $.ajax({
	         url:"ajax_group_send_email_sms.php",
	         data : {
	            is_direct : 1,
	            sent_via : type,
	            trigger_id : $id,
	            customer_id :'<?=$group_id?>',
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
		        id:'<?=$group_id?>'
		      },
		      beforeSend :function(e){
		        $("#ajax_loader").show();
		      },
		      success : function(res){
		        $("#ajax_loader").hide();
		        $("#"+ajax_div).html(res);
		        fRefresh();
		        common_select();
		        $("input[type='checkbox']").uniform();
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
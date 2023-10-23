<script type="text/javascript">
	//*************** General Code Start ***************************
		$contribution_option_change = '<?= $display_contribution ?>';
		$(document).on("change","#class_list",function(){
	      $val = $(this).val();

	      if($val!=''){
	         $class_id = $("#class_list").find(':selected').attr('data-class_id');
	         $class_name = $("#class_list").find(':selected').attr('data-class_name');
	         $pay_period = $("#class_list").find(':selected').attr('data-pay_period');
	         $existing_member = $("#class_list").find(':selected').attr('data-existing_member');
	         $new_member = $("#class_list").find(':selected').attr('data-new_member');
	         $renewed_member = $("#class_list").find(':selected').attr('data-renewed_member');
	         
	         $("#class_id").val($val);
	         $("#class_list_detail").show();
	         $("#offering_form_div").show();
	         $("#offering_form_foorter_div").hide();

	         if($new_member != "Immediately"){
	            $new_member = "After "+$new_member+" days";
	         }
	         if($existing_member != "Immediately"){
	            $existing_member = "After "+$existing_member+" days";
	         }
	         if($renewed_member != "Immediately"){
	            $renewed_member = "After "+$renewed_member+" days";
	         }

	         $("#cl_class_name").html($class_name);
	         $("#cl_existing_member").html($existing_member);
	         $("#cl_new_member").html($new_member);
	         $("#cl_renewed_member").html($renewed_member);
	         $("#cl_pay_period").html($pay_period);
	         $("#cl_link").attr('href','group_add_class.php?class='+$class_id);
	      }else{
	         $("#class_id").val(0);
	         $("#class_list_detail").hide();
	         $("#offering_form_div").hide();
	         $("#offering_form_foorter_div").show();
	      }
   		});

   		$(document).on("click",".datePickerIcon",function(){
		  $id=$(this).attr('data-applyon');
		  $("#"+$id).datepicker('show');
		  $("#"+$id).trigger("blur");
		});

		$(".dates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

		$(document).on('shown.bs.tab', '.offering_tabs', function (e) {
            $id= $(this).attr('id');
            $step = $(this).attr('data-step');
            $("#step_counter").html($step);
            if($id=="a_contributions_tab"){
                if($contribution_option_change){
                    contribution_option();
                }  
            }            
        });
   	//*************** General Code End ***************************

   	$(document).ready(function() {
       $("#products").multipleSelect({
       	width:"100%",
       	onClick : function(){
       		$contribution_option_change = true;
       	},	
       	onCheckAll : function(){
       		$contribution_option_change = true;
       	},
       	onUncheckAll : function(){
       		$contribution_option_change = true;
       	}
       });

       $('#allowed_range').selectpicker({
	      container: 'body',
	      style: 'btn-select',
	      noneSelectedText: '',
	      dropupAuto: true
	    });
	    $('#allowed_range').selectpicker('refresh');
       	//******************** Form Submit tab Code start *******************************
         	$('#offering_form').ajaxForm({
	             beforeSend: function () {
	                 $("#ajax_loader").show();
	             },
	             dataType: 'json',
	             success: function (res) {                
	                 $("#ajax_loader").hide();
	                 
	                 if(res.status=="success"){
	                     if(res.submit_type=="continue"){

	                        $(".data_tab li.active").addClass("completed");
	                        $(".data_tab li.active").next().find("a").trigger("click");
	                        $(".data_tab li.active").removeClass("disabled");
	                        $('html, body').animate({
	                           scrollTop: $('.data_tab').offset().top-100
	                        }, 1000);
	                     }else if(res.submit_type=="next_panel"){
	                     	$next_back_product = $("#next_back_product").val();
	                     	$next_id = $("#next_product_"+$next_back_product).parents('.product_panel').parent().next().find('.product_panel').attr('id');
				      		$current_id = $("#next_product_"+$next_back_product).parents('.product_panel').attr('id');

				      		$("#"+$next_id).collapse('show');
				      		$("#"+$current_id).collapse('hide');
	                     }
	                 }else if (res.status == 'offering_added') {
	                     window.parent.$.colorbox.close();
	                 } else if (res.status == 'fail') {
	                    if (res.div_step_error) {
	                       if (!$('#' + res.div_step_error).is(":visible"))
	                         $("[href='#" + res.div_step_error + "']").click();
	                    }
	                     var is_error = true;
	                     $.each(res.errors, function (index, error) {
	                         $('#error_' + index).html(error).show();
	                         if (is_error) {
	                             var offset = $('#error_' + index).offset();
	                             if(typeof(offset) === "undefined"){
	                                 console.log("Not found : "+index);
	                             }else{
	                                 scrollToElement($('#error_' + index));
	                                 is_error = false;
	                             }
	                         }
	                     });

	                     if(res.panel_error){
	                     	$.each(res.panel_error,function($k,$v){
	                     		$("#panel_id_"+$v).collapse("show");
	                     		$("#main_panel_id_"+$v).removeClass("completed");
	                     	});
	                     }
	                     if(res.success_panel){
	                     	$.each(res.success_panel,function($k,$v){
	                     		$("#panel_id_"+$v).collapse("hide");
	                     		$("#main_panel_id_"+$v).addClass("completed");
	                     	});
	                     }


	                 } 
	             },
	             error: function () {
	                 alert('Due to some technical error file couldn\'t uploaded.');
	             }
         	});
    	//******************** Form Submit tab Code end   *******************************
   	});
   
   	


   	//******************** Button Click Code start *******************************
      $(document).off("click",".next_tab_button");
      $(document).on("click",".next_tab_button",function(){
         $step=$(this).attr('data-step');
         $("#dataStep").val($step);
         $("#submit_type").val('continue');
         $("#action").val('continue_application');
         $('.error ').html('');
         
         $("#offering_form").submit();
      });

      $(document).off("click",".cancel_tab_button");
      $(document).on("click",".cancel_tab_button",function(){
        window.parent.$.colorbox.close();
      });
   	//******************** Button Click Code end   *******************************

   	//******************** step1 Code Start *******************************
   		
   		$(document).on("change","input[name=allow_future_effective_date]",function(){
	         $val=$(this).val();
	         $("#allow_future_effective_date_div").hide();
	         if($val=='Y'){
	            $("#allow_future_effective_date_div").show();
	         }
	         $('#allowed_range').selectpicker({
		      container: 'body',
		      style: 'btn-select',
		      noneSelectedText: '',
		      dropupAuto: true
		    });
		    $('#allowed_range').selectpicker('refresh');
      	});
   	//******************** step1 Code End   *******************************

   	//******************** step2 Code Start *******************************
   		$(document).on("change","input[name=is_contribution]",function(){
	        $val=$(this).val();
	        $("#is_contribution_div").hide();
	        if($val=='Y'){
	            $("#is_contribution_div").show();
	        }
	        $contribution_option_change = true;
      	});
   	//******************** step2 Code End   *******************************

   	//******************** step3 Code Start *******************************
   		$(document).off("click",".back_product");
      	$(document).on("click",".back_product",function(){
      		$id = $(this).attr('data-id');
      		$("#next_back_product").val($id);
      		
      		$pre_id = $(this).parents('.product_panel').parent().prev().find('.product_panel').attr('id');
      		$current_id = $(this).parents('.product_panel').attr('id');
      		
      		$("#"+$pre_id).collapse('show');
      		$("#"+$current_id).collapse('hide');
      	});

      	$(document).off("click",".next_product");
      	$(document).on("click",".next_product",function(){
      		$id = $(this).attr('data-id');
      		$("#next_back_product").val($id);

      		$("#dataStep").val(3);
	        $("#submit_type").val('next_panel');
	        $("#action").val('next_panel');
	        $('.error ').html('');
	         
	        $("#offering_form").submit();
      	});
		   	
		
   		contribution_option = function(){
   			$("#ajax_loader").show();
            $("#contribution_option_div").html('');
            $.ajax({
                url: '<?=$GROUP_HOST?>/ajax_get_contribution_option.php',
                data: $("#offering_form").serialize(),
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    $("#ajax_loader").hide();
                    $contribution_option_change = false;   
                    if(res.status=="success"){
                        $("#contribution_option_div").html(res.html);
                        $("input[type='radio']").not('.js-switch').uniform();
                        $('.product_panel').on('hidden.bs.collapse', function () {
				   			$id=$(this).attr('data-id');
				   			$("#main_panel_id_"+$id).removeClass('cyan-panel');
						}).on('shown.bs.collapse', function () {
							$id=$(this).attr('data-id');
							$("#main_panel_id_"+$id).addClass('cyan-panel');
						});
						if(res.added_id){
							$.each(res.added_id,function($k,$v){
								updateContributionAmount($v,'','ALL');
							});
						}
                    }                
                }
            });
   		}
   		$(document).off("change",".contribution_type");
      	$(document).on("change",".contribution_type",function(){
      		$val = $(this).val();
      		$id = $(this).attr('data-id');
      		$(".Percentage_"+$id).hide();
			$(".Amount_"+$id).hide();
      		if($val=="Amount"){
      			$(".Amount_"+$id).show();
      			$(".contribution_value_"+$id).attr('maxlength',8);
      		}else{
      			$(".Percentage_"+$id).show();
      			$(".contribution_value_"+$id).attr('maxlength',3);
      		}
      		updateContributionAmount($id,'','ALL');
      	});

      	$(document).off("keyup",".contribution_value");
      	$(document).on("keyup",".contribution_value",function(){
      		$id = $(this).attr('data-id');
      		$matrix_id = $(this).attr('data-matrix-id');

      		updateContributionAmount($id,$matrix_id,'Single');
      	});



      	isNumberOnly = function(evt) {
	      evt = (evt) ? evt : window.event;
	      var charCode = (evt.which) ? evt.which : evt.keyCode;
	      if (charCode != 8 && charCode != 46 && charCode != 47 && charCode != 0 && (charCode < 48 || charCode > 57)) {
	          return false;
	      }
	      return true;
	    }

	    updateContributionAmount = function($id,$matrix_id,$type){
	    	if($type=="Single"){
	    		$product_price = parseFloat($("#contribution_value_"+$id+"_"+$matrix_id).attr('data-price'));
	    		$val = $("#contribution_value_"+$id+"_"+$matrix_id).val();
	    		$contribution_type = $("input[name='contribution_type["+$id+"]']:checked").val();

	    		if($val == ""){
	      			$val = 0;
	      			//$("#contribution_value_"+$id+"_"+$matrix_id).val($val);
	      		}
	    		$val = parseFloat($val);
	    		
	    		if($contribution_type=="Amount"){
	    			if($val > $product_price){
	    				$group_cost=parseFloat($product_price).toFixed(2);
	    				$member_cost=parseFloat(0).toFixed(2);
	    				$("#contribution_value_"+$id+"_"+$matrix_id).val($product_price);
	    			}else{
	    				$group_cost=parseFloat($val).toFixed(2);
	    				$member_cost=parseFloat($product_price - $group_cost).toFixed(2);
	    			}
	    		}else{
					if($val > 100){
	    				$group_cost=parseFloat($product_price).toFixed(2);
	    				$member_cost=parseFloat(0).toFixed(2);
	    				$("#contribution_value_"+$id+"_"+$matrix_id).val(100);
	    			}else{
	    				$group_cost=parseFloat($product_price * $val / 100).toFixed(2);
	    				$member_cost=parseFloat($product_price - $group_cost).toFixed(2);
	    			}
	    		}
	    		$total_cost = parseFloat(parseFloat($group_cost) + parseFloat($member_cost)).toFixed(2);
	    		$("#group_cost_"+$id+"_"+$matrix_id).html($group_cost);
				$("#member_cost_"+$id+"_"+$matrix_id).html($member_cost);
				$("#total_cost_"+$id+"_"+$matrix_id).html($total_cost);
	    	}else{
	    		$(".contribution_value_"+$id).each(function(){
	    			$id = $(this).attr('data-id');
		      		$matrix_id = $(this).attr('data-matrix-id');

		      		updateContributionAmount($id,$matrix_id,'Single');
	    		});
	    	}

	    }
   	//******************** step3 Code End   *******************************

   	scrollToElement = function(e) {
       add_scroll = 0;
       element_id = $(e).attr('id');
       if("both_button" == element_id)
           add_scroll = 50;
       var offset = $(e).offset();
       var offsetTop = offset.top;
       var totalScroll = offsetTop - 200 + add_scroll;
       $('body,html').animate({
           scrollTop: totalScroll
       }, 1200);
   	}


</script>
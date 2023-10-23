<iframe id="tmp_content_iframe" style="display: none;"></iframe>
<script type="text/javascript">
	var $xhr = '';
	var $changeFromTags = false;
	$(document).ready(function(){
		change_prd_fee_label();
		$(window).on('resize load', function(){
		   if ($(window).width() <= 500) {
		      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
		      autoResizeNav();
		   }
		});
		$(".hrm_payment_input").priceFormat({
		    prefix: '',
            suffix: '',
            centsSeparator: '.',
            thousandsSeparator: ',',
            centsLimit: 2,
		});

		//********** General Code Start ***************** 
			window.onbeforeunload = function () {
		      return 'Are you sure you want to leavsse?';
		    };
			$(document).off("click", ".popup_lg");		
			$(document).on("click",".popup_lg",function(e){
				e.preventDefault();
				$href=$(this).attr('href');
				$type=$(this).attr('data-type');
				$product=$("#product_id").val();
				$.colorbox({
					href: $href+"?type="+$type+"&product="+$product,
					iframe: true, 
					width: '900px',
					height: '580px',
				});
			});
			// initCKEditor("termsConditionData");
			// $('#termsConditionData').summernote({
			// 		    toolbar: $SUMMERNOTE_TOOLBAR,
  	// 					disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
			// 		    buttons: {
			// 		        AddCheckbox: addCheckbox
			// 		    },
			// 		    focus: true, // set focus to editable area after initializing summernote
			// 		    height:350,
			// 		    callbacks: {
			// 			    onImageUpload: function(image) {
			// 			      editor = $(this);
			// 			      uploadImageContent(image[0], editor);
			// 			    },
			// 			    onMediaDelete : function(target) {
			// 			        deleteImage(target[0].src);
			// 			        target.remove();
			// 			    }
			// 			  }
			// 		});
					// function addCheckbox(context) {
					//     var ui = $.summernote.ui;
					//     var button = ui.button({
					//         contents: '<i class="fa fa-check-square-o fs14"/>',
					//         tooltip: 'Insert a Checkbox',
					//         click: function() {
					//             context.invoke('editor.pasteHTML', '<span contentEditable="false"><input type="checkbox" class="note-editor-checkbox" checked="checked" /></span>&nbsp;');

					//         }
					//     });
					//     return button.render();
					// }
			// loadSummerNote('');
			// initCKEditor('enrollmentPage');
			// initCKEditor('agent_portal');
			// initCKEditor('limitations_exclusions');
			$('.summernoteClass').each(function(i, obj) {
				initCKEditor($(obj).attr('id'));
			});

			<?php if($initialBuild) { ?>
				loadDepartmentlDiv();
			<?php }else {?>
				arrangeDepartmentAddButton();
			<?php } ?>
		     $(window).load(function(){
			    $(".portal_prdinfo_scroll").mCustomScrollbar({
			      axis: "y",
			      theme:"dark-thick",
			      scrollbarPosition: "outside",
			    });
			  });

		    $(document).off("click", ".popup_sm");		
			$(document).on("click",".popup_sm",function(e){
				e.preventDefault();
				$.colorbox({
					href: $(this).attr('href'),
					iframe: true, 
					width: '600px',
					height: '400px',
				});
			});

			// loadContent();
			loadCustomQuestions();
			var not_win = '';
			    $(".product_smart_tag_popup").on('click',function(){
			    $href = $(this).attr('data-href');
			    var not_win = window.open($href, "myWindow", "width=768,height=600");
			    if(not_win.closed) {  
			      alert('closed');  
			    } 
			  });
		//********** General Code end ***************** 
		
		//********** prd_infomartion Code Start *****************
		//********** prd_infomartion Code end *****************
		
		//********** prd_rules Code Start *****************
			$("#membership_ids").multipleSelect({selectAll: false});
			$("#zipcode_allow_only_state").multipleSelect({selectAll: false,
				onClick:function(e){
					$text = e.text;
					$stateName = e.value;
					$stateName = $stateName.replaceAll(" ", "_");
					if(e.selected){
						html = $('#available_specific_zipcode_dynamic_div').html();
						html = html.replace(/~state_full_name~/g, $text);
						html = html.replace(/~state_name~/g, $stateName);
						html = html.replace(/~state_names~/g, $stateName.replaceAll("_", " "));
			            $('#available_specific_zipcode').append(html);

			            $("#available_state_zipcode_"+$stateName).tagsinput('items');
					}else{
						$("#available_specific_zipcode_div_"+$stateName).remove();
					}
				},
				onTagRemove:function(e){
					$stateName = e.value;
					$stateName.replaceAll(" ", "_");
					$("#available_specific_zipcode_div_"+$stateName).remove();
				}
			});
			
			$(".available_no_sale_state").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

			$specificZipCodeArr = JSON.parse('<?= json_encode($specificZipCodeArr) ?>');
			if($specificZipCodeArr){
				$.each($specificZipCodeArr,function($k,$v){
					$changeFromTags = true;
					$stateName = $k.replaceAll(" ", "_");
					$("#available_state_zipcode_"+$stateName).tagsinput('items');
				});
			}
			
			

			$("#sub_product").multipleSelect({selectAll: false});
			$("#excludeProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
					$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					
				}
			});
			$("#autoAssignProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);

					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					
				}
			});
			$("#requiredProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
						$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
								$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
					$("#packagedProduct [value='"+$productName+"']").prop('disabled',false);
					
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					
				}
			});
			$("#packagedProduct").multipleSelect({
				selectAll: false,
				onClick:function(e){
					$text = e.text;
					$productName = e.value;
					if(e.selected){
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
					}else{
						$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
						$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
						$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					}
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
				},
				onOptgroupClick:function(e){
					$childRecords=e.children;

					$.each($childRecords,function($k,$v){
						$productName = $v.value;
						if(!$v.disabled){
							if(e.selected){
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',true);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',true);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',true);
							}else{
								$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
								$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
								$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
							}
						}
						
					});
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
				},
				onTagRemove:function(e){
					$productName = e.value;
					$("#excludeProduct [value='"+$productName+"']").prop('disabled',false);
					$("#autoAssignProduct [value='"+$productName+"']").prop('disabled',false);
					$("#requiredProduct [value='"+$productName+"']").prop('disabled',false);
					
					$("#excludeProduct").multipleSelect('refresh');
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					
				}
			});
		//********** prd_rules Code end *****************
		
		//********** prd_enrollment Code Start *****************
			$("#license_type").multipleSelect({selectAll: false});
		//********** prd_enrollment Code end ***************** 
		
		//********** prd_pricing Code Start ***************** 
			spouseControl();

			formatPricing();

			pricingMatrixIframe();
			pricingMatrixIframeEnrollee();

			productFeeIframe();

			$(".pricingDates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

			$pricingMatrixCount = 0;

			$progressTimerResponse = '';

			$pricing_modelJS= '<?= $pricing_model ?>';

			if($pricing_modelJS=='FixedPrice'){
				if($("#product_management .inner_pricing_div").last().find('.pricingTerminationDate').is('[readonly]')){
					$thisID = $("#product_management .inner_pricing_div").last().find('.pricingTerminationDate').attr('data-id');
					$thisTerm = $("#product_management .inner_pricing_div").last().find('.pricingTerminationDate').val();
					addPricingFixedDiv($thisTerm);
				}
			}
		//********** prd_pricing Code end *****************
		
		//********** form button handling Code Start ***************** 
		//********** form button handling Code end *****************

		//******** Form Submit Code start *******************
			$(document).on('shown.bs.tab', '.btn_step_heading', function (e) {
				if($(this).attr('data-step') == "2") {
					$("#autoAssignProduct").multipleSelect('refresh');
					$("#excludeProduct").multipleSelect('refresh');
					$("#requiredProduct").multipleSelect('refresh');
					$("#packagedProduct").multipleSelect('refresh');
					$('#direct_product').selectpicker('refresh');
				}
        	});

			$('#product_management').ajaxForm({
				beforeSend: function(xhr) {
					$xhr=xhr;
				},
				beforeSubmit:function(arr, $form, options){
	                
	            },
	            type: 'POST',
	            dataType: 'json',
	            success: function (res) {
	            	$("#ajax_loader").hide();
	            	$('.error').html('');

	            	if(typeof(res.step) !== "undefined" && res.step == "1") {
						setTimeout(function(){
							$("#autoAssignProduct").multipleSelect('refresh');
							$("#excludeProduct").multipleSelect('refresh');
							$("#requiredProduct").multipleSelect('refresh');
							$("#packagedProduct").multipleSelect('refresh');
						},2000);
					}

	            	if(res.formType=="CSV"){
	            		$val = $("input[name='pricing_model']:checked"). val();
					    if($val == "VariableEnrollee"){
					    	$("#CSVErrorList_Enrollee").html('');
					    	$("#csvError_Enrollee").hide();
					    	$("#stored_file_name_Enrollee").val(res.stored_file_name);
					    }else{
					    	$("#CSVErrorList").html('');
					    	$("#csvError").hide();
					    	$("#stored_file_name").val(res.stored_file_name);
					    }
	            		
	            		if (res.csv_data) {
	            			if($val == "VariableEnrollee"){
					        	$("#inline_content_Enrollee").show('slow');
					        	$("#is_csv_uploaded_Enrollee").val('Y');
	            			}else{
					        	$("#inline_content").show('slow');
					        	 $("#is_csv_uploaded").val('Y');
	            			}
					        
					        $.each(res.csv_data, function(key, val) {
					          $('.select_field').append('<option value="' + val + '">' + val + '</option>');
					        });
					        $(".select_field").addClass('form-control');
					        $('.select_field').selectpicker({
					        	container: 'body', 
						        style:'btn-select',
						        noneSelectedText: '',
						        dropupAuto:false,
					        });					        
				      	}
				      	if (res.status == 'fail') {
				      		if(res.saveCSVAs=="uploadCSV"){
					        	var is_error = true;
					        }else{
					        	$progressTimerResponse = 'error';
					        	if($val == "VariableEnrollee"){
					        		$("#csvError_Enrollee").show();
					        		$(".loading-progress_Enrollee").progressTimer('error');
					        	}else{
					        		$("#csvError").show();
				      				$(".loading-progress").progressTimer('error');
					        	}
					        	$CSVErrorList = [];
					        }
		                    $.each(res.errors, function (index, error) {
		                    	if(res.saveCSVAs=="uploadCSV"){
		                    		$('#error_' + index).html(error);
			                        if (is_error) {
			                            var offset = $('#error_' + index).offset();
			                            if(typeof(offset) === "undefined"){
			                                console.log("Not found : "+index);
			                            }else{
			                                var offsetTop = offset.top;
			                                var totalScroll = offsetTop - 195;
			                                $('body,html').animate({
			                                    scrollTop: totalScroll
			                                }, 1200);
			                                is_error = false;
			                            }
			                        }
		                    	}else{
		                    		$CSVErrorList.push(error);
		                    	}
		                    });
		                    if(res.saveCSVAs!="uploadCSV"){
		                    	if($val == "VariableEnrollee"){
		                    		$("#CSVErrorList_Enrollee").html("<li>"+$CSVErrorList.join("</li><li>"));
		                    	}else{
		                    		$("#CSVErrorList").html("<li>"+$CSVErrorList.join("</li><li>"));
		                    	}
		                	}
				      	} else if (res.status == 'success') {
				      		if($val == "VariableEnrollee"){
				      			$("#inline_content_Enrollee").hide('slow');
				      		}else{
					      		$("#inline_content").hide('slow');
				      		}
					      	$("#pricingMatrixKey").val(res.pricingMatrixKey);
					      	$val = $("input[name='pricing_model']:checked"). val();
					      	if($val == "VariableEnrollee"){
					      		$('#csv_file_Enrollee').val('');
					      		$("#cancelPricingFixedEnrollee").trigger('click');
					      		pricingMatrixIframeEnrollee();
					      		$("#stored_file_name_Enrollee").val('');
					      		$progressTimerResponse = "complete";
					        	$(".loading-progress_Enrollee").progressTimer('complete');
					      	}else{
					      		$('#csv_file').val('');
					      		$("#cancelPricingFixed").trigger('click');
					      		pricingMatrixIframe();
					      		$("#stored_file_name").val('');
					      		$progressTimerResponse = "complete";
					        	$(".loading-progress").progressTimer('complete');
					      	}
					        setNotifySuccess(res.msg);

					        
				      	}
	            	}else{
	            		if(res.status=="success"){
	            			$("#productFees").val(res.productFees);
	            			$("#pricingMatrixKey").val(res.pricingMatrixKey);
	            			pricingMatrixIframe();
							productFeeIframe();
							pricingMatrixIframeEnrollee();

		            		$("#product_id").val(res.product_id);
		            		$("#parent_product_id").val(res.parent_product_id);
		            		$("#manage_product_id").val(res.manage_product_id);
		            		$("#is_clone").val(res.is_clone);

		            		if(res.new_company_id){
		            			$("#company_id").append($("<option selected></option>").attr("value",res.new_company_id).text(res.new_company_name)); 
		            			$("#new_company_div").hide();
		            			$("#company_id").selectpicker('refresh');
		            		}

		            		/*if(res.new_category_id){
		            			$("#category_id").append($("<option selected></option>").attr("value",res.new_category_id).text(res.new_category_title)); 
		            			$("#new_category_div").hide();
		            			$("#category_id").selectpicker('refresh');
		            		}*/
			            	if(res.prdPlanCodeArray){
		            			$.each(res.prdPlanCodeArray,function(key,value){
		            				$("#product_plan_code_div_"+key).attr("data-counter",value);
		            				$("#product_plan_code_div_"+key).attr("id","product_plan_code_div_"+value);
		            				
		            				$("#product_plan_code_"+key).attr("name","product_plan_code["+value+"]");
		            				$("#product_plan_code_"+key).attr("id","product_plan_code_"+value);
		            				$("#error_product_plan_code_"+key).attr("id","error_product_plan_code_"+value);

		            				$("#product_plan_code_display_number_"+key).attr("id","product_plan_code_display_number_"+value);
		            				
		            				$("#remove_product_plan_code_"+key).attr("data-id",value);
		            				$("#remove_product_plan_code_"+key).attr("data-removeId",value);
		            				$("#remove_product_plan_code_"+key).attr("id","remove_product_plan_code_"+value);
		            				
		            			});
	            			}
	            			if(res.departmentArray){
		            			$.each(res.departmentArray,function(key,value){
		            				$("#department_div_"+key).attr("id","department_div_"+value);
		            				
		            				$("#removeDepartment_"+key).attr("data-id",value);
		            				$("#removeDepartment_"+key).attr("data-removeId",value);
		            				$("#removeDepartment_"+key).attr("id","removeDepartment_"+value);
		            				
		            				$("#department_name_"+key).attr("name","department_name["+value+"]");
		            				$("#department_name_"+key).attr("id","department_name_"+value);
		            				$("#error_department_name_"+key).attr("id","error_department_name_"+value);

		            				// $("#department_desc_"+key).summernote('destroy')
		            				
		            				$("#department_desc_"+key).attr("name","department_desc["+value+"]");
		            				$("#department_desc_"+key).attr("id","department_desc_"+value);
		            				$("#error_department_desc_"+key).attr("id","error_department_desc_"+value);
		            				$name_val = $("#department_desc_"+value).attr('name');

		            				// loadSummerNote("department_desc_"+value);
		            				for (instance in CKEDITOR.instances) {
								        CKEDITOR.instances[instance].updateElement();
								    }
            						arrangeDepartmentAddButton();
		            			});
		            		}
		            		if(res.prdNoSaleStateArray){
		            			$.each(res.prdNoSaleStateArray,function($stateId,$stateRow){
		            				$.each($stateRow,function(key,value){
			            				
			            				$("#available_no_sale_state_main_"+key).attr("id","available_no_sale_state_main_"+value);
			            				
			            				$("#available_no_sale_state_"+$stateId+"_"+key).attr("id","available_no_sale_state_"+$stateId+"_"+value);
			            				$("#state_name_"+$stateId+"_"+key).attr("id","state_name_"+$stateId+"_"+value);
			            				
			            				$("#apply_on_available_no_sale_state_"+$stateId+"_"+key+"_effective_date").attr("data-applyon","apply_on_available_no_sale_state_"+$stateId+"_"+value+"_effective_date");
			            				$("#apply_on_available_no_sale_state_"+$stateId+"_"+key+"_effective_date").attr("id","apply_on_available_no_sale_state_"+$stateId+"_"+value+"_effective_date");

			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_effective_date").attr("name","available_no_sale_state["+$stateId+"]["+value+"][effective_date]");
			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_effective_date").attr("data-id",value);
			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_effective_date").attr("id","available_no_sale_state_"+$stateId+"_"+value+"_effective_date");
			            				
			            				$("#error_available_no_sale_state_effective_date_"+$stateId+"_"+key).attr("id","error_available_no_sale_state_effective_date_"+$stateId+"_"+value);
			            				
			            				$("#apply_on_available_no_sale_state_"+$stateId+"_"+key+"_termination_date").attr("data-applyon","apply_on_available_no_sale_state_"+$stateId+"_"+value+"_termination_date");
			            				$("#apply_on_available_no_sale_state_"+$stateId+"_"+key+"_termination_date").attr("id","apply_on_available_no_sale_state_"+$stateId+"_"+value+"_termination_date");

			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_termination_date").attr("name","available_no_sale_state["+$stateId+"]["+value+"][termination_date]");
			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_termination_date").attr("data-id",value);
			            				$("#available_no_sale_state_"+$stateId+"_"+key+"_termination_date").attr("id","available_no_sale_state_"+$stateId+"_"+value+"_termination_date");
			            				$("#error_available_no_sale_state_termination_date_"+$stateId+"_"+key).attr("id","error_available_no_sale_state_termination_date_"+$stateId+"_"+value);
			            			});
		            			});
		            			
		            		}
		            		if(res.prdFixedPriceRes){
		            			$.each(res.prdFixedPriceRes,function(key,$matrixRow){
		            				$.each($matrixRow,function(plan_type,value){
		            					$("#inner_pricing_div_"+key).attr("data-id",value);
		            					$("#inner_pricing_div_"+key).attr("id","inner_pricing_div_"+value);
		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][Retail]']").attr('data-id',value);
		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][Retail]']").attr('name',"pricing_fixed_price["+value+"]["+plan_type+"][Retail]");

		            					$("#error_pricing_fixed_price_"+key+"_"+plan_type).attr('id','error_pricing_fixed_price_'+value+'_'+plan_type);

		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][NonCommissionable]']").attr('data-id',value);
		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][NonCommissionable]']").attr('name',"pricing_fixed_price["+value+"]["+plan_type+"][NonCommissionable]");

		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][Commissionable]']").attr('data-id',value);
		            					$("input[name='pricing_fixed_price["+key+"]["+plan_type+"][Commissionable]']").attr('name',"pricing_fixed_price["+value+"]["+plan_type+"][Commissionable]");

		            					$("#apply_on_pricing_effective_date_"+key).attr("data-applyon","pricing_effective_date_"+value);
			            				$("#apply_on_pricing_effective_date_"+key).attr("id","apply_on_pricing_effective_date_"+value);
			            				

			            				$("#pricing_effective_date_"+key).attr("name","pricing_effective_date["+value+"]");
			            				$("#pricing_effective_date_"+key).attr("data-id",value);
			            				$("#pricing_effective_date_"+key).attr('id','pricing_effective_date_'+value);

			            				$("#error_pricing_effective_date_"+key).attr('id','error_pricing_effective_date_'+value);
			            				

		            					$("#apply_on_pricing_termination_date_"+key).attr("data-applyon","pricing_termination_date_"+value);
			            				$("#apply_on_pricing_termination_date_"+key).attr("id","apply_on_pricing_termination_date_"+value);

			            				$("#pricing_termination_date_"+key).attr("name","pricing_termination_date["+value+"]");
			            				$("#pricing_termination_date_"+key).attr("data-id",value);
			            				$("#pricing_termination_date_"+key).attr('id','pricing_termination_date_'+value);
			            				$("#error_pricing_termination_date_"+key).attr('id','error_pricing_termination_date_'+value);

			            				$("#terminationDateClear_"+key).attr('id','terminationDateClear_'+value);
			            				
			            				$("#clearPriceDivTmp_"+key).attr('data-id',value);
			            				$("#clearPriceDivTmp_"+key).attr('id','clearPriceDivTmp_'+value);

			            				$("#pricing_setting_divTmp_"+key).attr('data-id',value);
			            				$("#pricing_setting_divTmp_"+key).attr('id','pricing_setting_divTmp_'+value);

			            				$("input[name='newPricingOnRenewals["+key+"]']").attr('name',"newPricingOnRenewals["+value+"]");
			            				$("#error_newPricingOnRenewals_"+key).attr('id','error_newPricingOnRenewals_'+value);
			            				
			            				
		            				});
		            			});
		            		}
		            		
		            		if(res.prdMaxAgeArr){
		            			$.each(res.prdMaxAgeArr,function(member_type,$MaxAgeArr){
	            					$.each($MaxAgeArr,function(key,value){
	            						$(".add_control_"+member_type+"_"+key).selectpicker('destroy');
	            						

	            						$("#autoTermMemberSettingInner_div_"+member_type+"_"+key).attr('id','autoTermMemberSettingInner_div_'+member_type+'_'+value);
	            						
	            						$("#removeAutoTermMemberSettingInner_"+member_type+"_"+key).attr('data-id',+value);
	            						$("#removeAutoTermMemberSettingInner_"+member_type+"_"+key).attr('id','removeAutoTermMemberSettingInner_'+member_type+'_'+value);


	            						
            							$("[name='autoTermMemberSettingWithin["+key+"]["+member_type+"]']").removeClass('add_control_'+member_type+'_'+key);
		            					$("[name='autoTermMemberSettingWithin["+key+"]["+member_type+"]']").addClass('add_control_'+member_type+'_'+value);
		            					$("[name='autoTermMemberSettingWithin["+key+"]["+member_type+"]']").attr('name',"autoTermMemberSettingWithin["+value+"]["+member_type+"]");
		            					$("#error_autoTermMemberSettingWithin_"+member_type+"_"+key).attr('id','error_autoTermMemberSettingWithin_'+member_type+'_'+value);

		            					$("[name='autoTermMemberSettingWithinType["+key+"]["+member_type+"]']").removeClass('add_control_'+member_type+'_'+key);
		            					$("[name='autoTermMemberSettingWithinType["+key+"]["+member_type+"]']").addClass('add_control_'+member_type+'_'+value);
		            					$("[name='autoTermMemberSettingWithinType["+key+"]["+member_type+"]']").attr('name',"autoTermMemberSettingWithinType["+value+"]["+member_type+"]");
		            					$("#error_autoTermMemberSettingWithinType_"+member_type+"_"+key).attr('id','error_autoTermMemberSettingWithinType_'+member_type+'_'+value);

		            					$("[name='autoTermMemberSettingRange["+key+"]["+member_type+"]']").removeClass('add_control_'+member_type+'_'+key);
		            					$("[name='autoTermMemberSettingRange["+key+"]["+member_type+"]']").addClass('add_control_'+member_type+'_'+value);
		            					$("[name='autoTermMemberSettingRange["+key+"]["+member_type+"]']").attr('name',"autoTermMemberSettingRange["+value+"]["+member_type+"]");
		            					$("#error_autoTermMemberSettingRange_"+member_type+"_"+key).attr('id','error_autoTermMemberSettingRange_'+member_type+'_'+value);

		            					
		            					
		            					$("[name='autoTermMemberSettingWithinTrigger["+key+"]["+member_type+"]']").attr('name',"autoTermMemberSettingWithinTrigger["+value+"]["+member_type+"]");
		            					$("#error_autoTermMemberSettingWithinTrigger_"+member_type+"_"+key).attr('id','error_autoTermMemberSettingWithinTrigger_'+member_type+'_'+value);

		            					$(".add_control_"+member_type+"_"+value).selectpicker({ 
									        container: 'body', 
									        style:'btn-select',
									        noneSelectedText: '',
									        dropupAuto:false,
								      	});
		            					
		            				});
		            			});
		            		}
		            		if(res.riderInformationArr){
		            			$.each(res.riderInformationArr,function($riderId,$riderRow){
		            				$.each($riderRow,function(key,value){
			            				
			            				$("#rider_div_"+$riderId+"_"+key).attr("data-id",value);
			            				$("#rider_div_"+$riderId+"_"+key).attr("id","rider_div_"+value+"_"+key);
			            				
			            				$("#remove_rider_div_"+$riderId+"_"+key).attr("id","remove_rider_div_"+value+"_"+key);
			            				$("#remove_rider_"+$riderId+"_"+key).attr("data-id",value);
			            				$("#remove_rider_"+$riderId+"_"+key).attr("id","remove_rider_"+value+"_"+key);			            				
			            				$("#riderProduct_"+$riderId+"_"+key).attr("name","riderProduct["+value+"]["+key+"]");
			            				$("#riderProduct_"+$riderId+"_"+key).attr("id","riderProduct_"+value+"_"+key);
			            				
			            				$("#error_riderProduct_"+$riderId+"_"+key).attr("id","error_riderProduct_"+value+"_"+key);
			            				
			            				$("input[name='riderRate["+$riderId+"]["+key+"]']").attr('name',"riderRate["+value+"]["+key+"]");
			            				$("#error_riderRate_"+$riderId+"_"+key).attr("id","error_riderRate_"+value+"_"+key);			            				
			            			});
		            			});
		            			
		            		}
		            		if(res.submit_type=="continue"){
		            			$(".data_tab li.active").next().find("a").trigger("click");
		            			$(".data_tab li.active").removeClass("disabled");
								$('html, body').animate({
								  scrollTop: $('.data_tab').offset().top-100
								}, 1000);

	            			}else if(res.submit_type=="exit"){
		            			window.onbeforeunload = null;
		            			if(res.dataStep == 4){
									notifySaveExit();
		            			}else{
									window.location.href="manage_product.php";
		            			}
	            			}
		            	} else if (res.status == 'fail') {
		            		$timeOut=0;
		                    if (res.div_step_error.length) {
					          if (!$('#' + res.div_step_error).is(":visible")){
					            $("[href='#" + res.div_step_error + "']").click();
					            $timeOut=1000;
					          }
					        }

					        
					        setTimeout(function(){  
			                    var is_error = true;
			                    $.each(res.errors, function (index, error) {
			                        $('#error_' + index).html(error);
			                        if (is_error) {
			                            var offset = $('#error_' + index).offset();
			                            if(typeof(offset) === "undefined"){
			                                console.log("Not found : "+index);
			                            }else{
			                                var offsetTop = offset.top;
			                                var totalScroll = offsetTop - 195;
			                                
			                                $('body,html').animate({
			                                    scrollTop: totalScroll
			                                }, 1200);
			                                is_error = false;
			                            }
			                        }
			                    });
			                }, $timeOut);

	                	}
	            	}
	            	
	            }
	        });
		//******** Form Submit Code end *******************

		// ***** Remove Direct Plan Option Code Start *****
		$prd_type_selected = $("#product_type").find(":selected").val();
		if($prd_type_selected != '' && $prd_type_selected != undefined && $prd_type_selected == 'Group Enrollment'){
				$("#direct_product option[value='Next Day']").hide();
				$("#direct_product option[value='Select Day Of Month']").hide();
		}
		// ***** Remove Direct Plan Option Code End *****
	});
	//********** form button handling Code Start ***************** 
		$(document).on('click','.btn_cancel',function(){
			window.location.href="<?= $ADMIN_HOST ?>/manage_product.php";
			
		});
		$(document).on('click','.btn_next',function(){
			$('.error').html('');
			for (instance in CKEDITOR.instances) {        
		        $("#tmp_content_iframe").contents().find("body").html(CKEDITOR.instances[instance].getData());
				if($("#tmp_content_iframe").contents().find('script').length > 0 || $("#tmp_content_iframe").contents().find('link').length > 0) {
					swal({
					   title:"Error",
					   text: "HTML contains JavaScript/CSS and cannot be saved. Please remove any JavaScript/CSS and try again.",
					   confirmButtonText: "Ok",
					}).then(function () {
						$('#error_' + instance).html('Please remove external JavaScript/CSS from HTML');
						var offset = $('#error_' + instance).offset();
						var offsetTop = offset.top;
	                    var totalScroll = offsetTop - 195;                    
	                    $('body,html').animate({
	                        scrollTop: totalScroll
	                    }, 1200);
					});
					return false;
				}
		    }

			$dataStep=$(".data_tab li.active a").attr('data-step');
        	$("#dataStep").val($dataStep);

			$("#submit_type").val('continue');
			
			$ajaxUrl = 'ajax_prd_add.php';
        	$("#product_management").attr('action', $ajaxUrl);
        	$("#ajax_loader").show();
        	for (instance in CKEDITOR.instances) {
		        CKEDITOR.instances[instance].updateElement();
		    }
			$("#product_management").submit();
			
		});
		$(document).on('click','.btn_save_exit',function(){
			$('.error').html('');
			for (instance in CKEDITOR.instances) {        
		        $("#tmp_content_iframe").contents().find("body").html(CKEDITOR.instances[instance].getData());
				if($("#tmp_content_iframe").contents().find('script').length > 0 || $("#tmp_content_iframe").contents().find('link').length > 0) {
					swal({
					   title:"Error",
					   text: "HTML contains JavaScript/CSS and cannot be saved. Please remove any JavaScript/CSS and try again.",
					   confirmButtonText: "Ok",
					}).then(function () {
						$('#error_' + instance).html('Please remove external JavaScript/CSS from HTML');
						var offset = $('#error_' + instance).offset();
						var offsetTop = offset.top;
	                    var totalScroll = offsetTop - 195;                    
	                    $('body,html').animate({
	                        scrollTop: totalScroll
	                    }, 1200);
					});
					return false;
				}
		    }
		    
	    	$dataStep=$(".data_tab li.active a").attr('data-step');
	        $("#dataStep").val($dataStep);

			$("#submit_type").val('exit');
			
			$ajaxUrl = 'ajax_prd_add.php';
        	$("#product_management").attr('action', $ajaxUrl);
        	$("#ajax_loader").show();
        	for (instance in CKEDITOR.instances) {
		        CKEDITOR.instances[instance].updateElement();
		    }
			$("#product_management").submit();

		});
	//********** form button handling Code End   ***************** 

	//********** prd_infomartion Code Start ***************** 
		$(document).on("change","#main_product_type",function(){
			$val=$(this).val();
			if($val == "Core Product"){
				$("#core_product_info_div").show();
			}else{
				$("#core_product_info_div").hide();
			}
		});

		$(document).on("change","#product_type",function(){
			$val=$(this).val();
			$direct_plan_selected = $("#direct_product").find(":selected").val();
			change_prd_fee_label();
			$("#voiceVerificationDIV").show();
			$("#AgentRequirementsDiv").show();
			$("#deduction_div_main").hide();
			$("#gap_plus_div_main").hide();
			if($val == "Group Enrollment"){
				$("#voiceVerificationDIV").hide();
				$("#AgentRequirementsDiv").hide();
				$("#deduction_div_main").show();
				$("#gap_plus_div_main").show();
				$("#groupEnrollmentPrd").val('Y');

				$("#direct_product option[value='Next Day']").hide();
				$("#direct_product option[value='Select Day Of Month']").hide();
			}else{
				$("#groupEnrollmentPrd").val('N');
				$("#direct_product option[value='Next Day']").show();
				$("#direct_product option[value='Select Day Of Month']").show();
			}

			if($direct_plan_selected != '' && $direct_plan_selected != undefined && $val == "Group Enrollment"){
				$("#day_of_month_div").hide();
				$("#day_of_sold_month_div").hide();
				if($direct_plan_selected == 'Next Day' || $direct_plan_selected == 'Select Day Of Month'){
					$("#direct_product option[value='"+$direct_plan_selected+"']").removeAttr('selected');
				}
				if($direct_plan_selected=="First Of Month"){
					$("#day_of_sold_month_div").show();
				}
			}
			$('#direct_product').selectpicker('refresh');
			fRefresh();
			productFeeIframe();
		});
		$(document).on("change","#company_id",function(){
			$val=$(this).val();
			$("#new_company_div").hide();
			if($val=="new_company"){
				$("#new_company_div").show();
			}
		});
		$(document).on("click","#add_new_company",function(){
			$company_name=$("#company_name").val();
			$("#ajax_loader").show();
			$.ajax({
				url:'<?= $ADMIN_HOST ?>/ajax_company_offering_products_edit.php',
				dataType:'JSON',
				data:{company_name:$company_name},
				type:'POST',
				success:function(res){
					$("#ajax_loader").hide();
					if(res.status=="success"){
						$("#company_id").append($("<option selected></option>").attr("value",res.new_company_id).text(res.new_company_name)); 
						$('#company_id').selectpicker('refresh');
						$("#new_company_div").hide();
					}else{
						var is_error = true;
	                    $.each(res.errors, function (index, error) {
	                        $('#error_' + index).html(error);
	                        if (is_error) {
	                            var offset = $('#error_' + index).offset();
	                            if(typeof(offset) === "undefined"){
	                                console.log("Not found : "+index);
	                            }else{
	                                var offsetTop = offset.top;
	                                var totalScroll = offsetTop - 195;
	                                $('body,html').animate({
	                                    scrollTop: totalScroll
	                                }, 1200);
	                                is_error = false;
	                            }
	                        }
	                    });
					}
				}
			});
		});

		/*$(document).on("change","#category_id",function(){
			$val=$(this).val();
			$("#new_category_div").hide();
			if($val=="new_category"){
				$("#new_category_div").show();
			}
		});
		$(document).on("click","#add_new_category",function(){
			$category_name=$("#category_name").val();
			$("#ajax_loader").show();
			$.ajax({
				url:'<?= $ADMIN_HOST ?>/ajax_product_categories_edit.php',
				dataType:'JSON',
				data:{category_name:$category_name},
				type:'POST',
				success:function(res){
					$("#ajax_loader").hide();
					if(res.status=="success"){
						$("#category_id").append($("<option selected></option>").attr("value",res.new_category_id).text(res.new_category_title)); 
						$('#category_id').selectpicker('refresh');
						$("#new_category_div").hide();
					}else{
						var is_error = true;
	                    $.each(res.errors, function (index, error) {
	                        $('#error_' + index).html(error);
	                        if (is_error) {
	                            var offset = $('#error_' + index).offset();
	                            if(typeof(offset) === "undefined"){
	                                console.log("Not found : "+index);
	                            }else{
	                                var offsetTop = offset.top;
	                                var totalScroll = offsetTop - 195;
	                                $('body,html').animate({
	                                    scrollTop: totalScroll
	                                }, 1200);
	                                is_error = false;
	                            }
	                        }
	                    });
					}
				}
			});
		});*/

		$(document).on("click","#add_product_plan_code",function(){
			$count=$("#product_management .product_plan_code").length - 1;
			$number=$count+1;
			$display_plan_code_counter = $number;
			html = $('#product_plan_code_dynamic_div').html();
			html = html.replace(/~plan_code_counter~/g,"-"+$number)
			html = html.replace(/~display_plan_code_counter~/g,$display_plan_code_counter)
            $('#product_plan_code_div').append(html);
		});
		$(document).on("click",".remove_product_plan_code",function(){
			$id=$(this).attr("data-id");
			$removeId=$(this).attr("data-removeId");
			$removed_display_number = parseInt($("#product_plan_code_display_number_"+$removeId).attr('data-display_number'));
			
			$("#product_plan_code_div_"+$removeId).remove();
			

            $('#product_management .product_plan_code_display_number').each(function(){
                $display_number = parseInt($(this).attr('data-display_number'));

                if($display_number > $removed_display_number){
                    $display_number = $display_number - 1;
                    $(this).attr('data-display_number',$display_number);
                    $(this).html($display_number);
                }
            });
		});

		$(document).on("click","#enrollmentPageExample",function(){
			// $("#enrollmentPageBody").html($("#enrollmentPage").val());
			$("#enrollmentPageBody").html(CKEDITOR.instances.enrollmentPage.getData());
			$.colorbox({
				inline: true , 
				href: '#enrollmentPageColorbox',
				width: '70%', 
				height: '80%',
			});
		});

		$(document).on("click","#add_department_div",function(){
			loadDepartmentlDiv();
		});

		$(document).on("click",".removeDepartment",function(){
			$id=$(this).attr("data-id");
			$removeId=$(this).attr("data-removeId");
			
			$("#department_div_"+$removeId).remove();
			
			arrangeDepartmentAddButton();
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
		
		$(document).on("click","#memberPortalPreview",function(){
			$initial=0;
			activeTab='';
			$("#memberPortalTitle").html('');
			$("#memberPortalContent").html('');
			$is_blank_desc = true;
			$("#product_management .department_div").each(function(){
				$id=$(this).attr('id');
				$count=$id.replace("department_div_", "");
				$title=$("#department_name_"+$count).val();
				// for (instance in CKEDITOR.instances) {
			 //        CKEDITOR.instances[instance].updateElement();
			 //        console.log(instance);
			 //    }
			 	for (instance in CKEDITOR.instances) {
			        CKEDITOR.instances[instance].updateElement();
			    }
			 	
				$desc_id='department_desc_'+$count;
				$desc=CKEDITOR.instances[$desc_id].getData();
				// $desc="";
				if($desc!=''){
					$is_blank_desc = false;
				}
				if($initial==0){
					$liHtml='<li role="presentation" class="active"><a href="#department_name_tab_'+$count+'" aria-controls="department_name_'+$count+'" role="tab" data-toggle="tab">'+$title+'</a></li>';
					$contentHtml='<div role="tabpanel" class="tab-pane active" id="department_name_tab_'+$count+'">'+$desc+'</div>';
					activeTab = 'department_name_'+$count;
					$initial++;
				}else{
					$liHtml='<li role="presentation"><a href="#department_name_tab_'+$count+'" aria-controls="department_name_'+$count+'" role="tab" data-toggle="tab">'+$title+'</a></li>';
					$contentHtml='<div role="tabpanel" class="tab-pane" id="department_name_tab_'+$count+'">'+$desc+'</div>';
				}

				$("#memberPortalTitle").append($liHtml);
				$("#memberPortalContent").append($contentHtml);
			});
			if($is_blank_desc){
				swal({   
		            text: "Error: No Description Added For Preview",   
		            }).then(function(){   

		        });
			}else{
				// autoResizeNav();
				$.colorbox({
					inline: true , 
					href: '#memberPortalColorbox', 
					width: '70%',
					height: '80%',
					onComplete:function(){
						autoResizeNav();
				    }
				});
			}
		});

		$(document).on("click","#agentPortalPreview",function(){
			// $("#agentEnrollmentInformationBody").html($("#agent_portal").val());
			$("#agentEnrollmentInformationBody").html(CKEDITOR.instances.agent_portal.getData());

			$("#agentInfoEffectiveHeading").hide();
			$("#agentInfoAvailableStateHeading").hide();
			$("#agentInfoProductRequiredHeading").hide();
			$("#agentInfoProductExcludedHeading").hide();

			$("#agentInfoProductBody").html($("#product_name").val());

			if($("input[name='agentInfoProductBox[]'][value='Effective Date']").is(":checked")){
				$("#agentInfoEffectiveHeading").show();
				$("#agentInfoEffectiveBody").html($("#direct_product :selected").text());

				if($("#direct_product :selected").val()=="Select Day Of Month"){
					$effectiveDayText = $("#effective_day :selected").text();
					$effectiveDayText = $effectiveDayText.replace(" of the month", "");
					$effectiveDayText2 = $("#effective_day2 :selected").text();
					$("#agentInfoEffectiveBody").html($effectiveDayText+", "+$effectiveDayText2);
				}
			}
			if($("input[name='agentInfoProductBox[]'][value='Available State']").is(":checked")){
				$("#agentInfoAvailableStateHeading").show();

				$availableState=[];
				$('input[name="available_state[]"]:checked').each(function() {
					$availableState.push($(this).attr('data-short-name'))
				});
				$("#agentInfoAvailableStateBody").html($availableState.join(", "));
			}
			if($("input[name='agentInfoProductBox[]'][value='Product Required']").is(":checked")){
				$("#agentInfoProductRequiredHeading").show();

				$requiredProduct=[];
				$("#requiredProduct :selected").each(function() {
					$requiredProduct.push($(this).text());
				});

				$("#agentInfoProductRequiredBody").html($requiredProduct.join(", "));
			}
			if($("input[name='agentInfoProductBox[]'][value='Product Excluded']").is(":checked")){
				$("#agentInfoProductExcludedHeading").show();

				$excludedProduct=[];
				$("#excludeProduct :selected").each(function() {
					$excludedProduct.push($(this).text());
				});
				$("#agentInfoProductExcludedBody").html($excludedProduct.join(", "));
			}
			$.colorbox({
				inline: true , 
				href: '#agentEnrollmentInformationColorbox',
				width: '70%', 
				height: '80%',
			});
		});

		$(document).on("click","#limitationExclusionPreview",function(){
			// $("#limitationAndExclusionBody").html($("#limitations_exclusions").val());
			$("#limitationAndExclusionBody").html(CKEDITOR.instances.limitations_exclusions.getData());
			$.colorbox({
				inline: true , 
				href: '#limitationAndExclusionColorbox',
				width: '70%', 
				height: '80%',
			});
		});

		$(document).on("click","input[name=is_life_insurance_product]",function(){
			$val=$(this).val();
			$("#life_insurance_div").hide();
			if($val=="Y"){
				$("#life_insurance_div").show();
			}
		});

		$(document).on("click","input[name=is_short_term_disablity_product]",function(){
			$val=$(this).val();
			$("#short_term_disablity_div").hide();
			if($val=="Y"){
				$("#short_term_disablity_div").show();
			}
		});

		$(document).off("click","input[name=is_gap_plus_product]");
		$(document).on("click","input[name=is_gap_plus_product]",function(){
			$val=$(this).val();
			if($val=="Y"){
				$("#gap_plus_div").show();
			} else {
				$("#gap_plus_div").hide();
			}
		});

		$(document).off("click","input[name=is_benefit_amount_limit]");
		$(document).on("click","input[name=is_benefit_amount_limit]",function(){
			$val=$(this).val();
			if($val=="Y"){
				$("#benefit_amount_limit_div").show();
			} else {
				$("#benefit_amount_limit_div").hide();
			}
		});

		$(document).off("click","input[name=is_set_default_out_of_pocket_maximum]");
		$(document).on("click","input[name=is_set_default_out_of_pocket_maximum]",function(){
			$val=$(this).val();
			if($val=="Y"){
				$("#default_out_of_pocket_maximum_div").show();
			} else {
				$("#default_out_of_pocket_maximum_div").hide();
			}
		});

		$(document).off("click","input[name=gap_home_savings_recommend_text]");
		$(document).on("click","input[name=gap_home_savings_recommend_text]",function(){
			$val=$(this).val();
			if($val=="custom_recommendation"){
				$("#gap_home_savings_recommend_text_div").show();
			} else {
				$("#gap_home_savings_recommend_text_div").hide();
			}
		});

		$(document).off("click",".guarantee_issue_amount_type");
		$(document).on("click",".guarantee_issue_amount_type",function(){
			$val=$(this).val();
			
			if($(this).is(":checked")) {
				if($val=="Primary"){
					$("#primary_issue_amount_div").show();
				}
				if($val=="Spouse"){
					$("#spouse_issue_amount_div").show();
					$("#is_spouse_issue_amount_larger_div").show();
				}
				if($val=="Child"){
					$("#child_issue_amount_div").show();
				}
				
			}else{
				if($val=="Primary"){
					$("#primary_issue_amount_div").hide();
				}
				if($val=="Spouse"){
					$("#spouse_issue_amount_div").hide();
					$("#is_spouse_issue_amount_larger_div").hide();
				}
				if($val=="Child"){
					$("#child_issue_amount_div").hide();
				}
			}
		});
	//********** prd_infomartion Code end *****************
	
	//********** prd_rules Code Start ***************** 
		$(document).on("change","#direct_product",function(){
			$val=$(this).val();
			$("#day_of_month_div").hide();
			$("#day_of_sold_month_div").hide();

			if($val=="Select Day Of Month"){
				$("#day_of_month_div").show();
				$("#day_of_sold_month_div").show();
			}
			if($val=="First Of Month"){
				$("#day_of_sold_month_div").show();
			}			
		});

		$(document).on("click","input[name=is_membership_require]",function(){
			$val=$(this).val();
			$("#membership_required_div").hide();
			if($val=="Y"){
				$("#membership_required_div").show();
			}
		});

		

		var statesArrInc=[];

		<?php if(!empty($resAvailableNoSaleState)) { ?>
          <?php foreach ($resAvailableNoSaleState as $keyr => $valuer) { ?>
            <?php if(!empty($valuer)) { ?>
				statesArrInc[<?php echo $keyr; ?>]=<?php echo count($valuer); ?>-1;
			<?php } ?>
		  <?php } ?>
		<?php } ?>

		$(document).on("click",".available_state",function(){
			$selectedState=$(this).val();
			$state_id=$(this).attr('data-state-id');
			
			if(!$(this).is(":checked")) {
        		$("#zipcode_allow_only_state option[value='"+$selectedState+"']").remove();
        		$('#zipcode_allow_only_state').multipleSelect('refresh');
        		$selectedStateInput = $selectedState.replaceAll(" ", "_");
        		$("#available_specific_zipcode_div_"+$selectedStateInput).remove();

        		if($("#availableCheckAll").is(":checked")){
        			$("#availableCheckAll").prop('checked',false);
        			$.uniform.update();
        		}
        		$("#available_no_sale_state_div").show();
        		$("#available_no_sale_state_main_"+$state_id).show();
				loadInitialAvailableState($(this));
				
        	}else{
        		$("#ajax_loader").show();
            	$.ajax({
	            	url:'<?= $ADMIN_HOST ?>/ajax_load_available_state.php',
	            	dataType:'JSON',
	            	data:{state:$selectedState,check_state:'Y'},
	            	type:'POST',
	            	success:function(res){
	            		$("#ajax_loader").hide();
	            		$("#zipcode_allow_only_state").append(res.optionsHtml);
	            		$('#zipcode_allow_only_state').multipleSelect('refresh');
	            		
						$("#available_no_sale_state_main_"+$state_id).hide();

						
						if(!$("#available_no_sale_state_main_div .available_no_sale_state_inner").is(":visible")){
			        		$("#available_no_sale_state_div").hide();
			        		$(".available_no_sale_state_main").hide();
			        		$("#availableCheckAll").prop('checked',true);
			        		$.uniform.update();
			        	}
			        	
	            	}
            	});
        	}
		});
		$(document).on("click","#availableCheckAll",function(){
			
			$(".available_state").prop("checked",this.checked);
			$.uniform.update();

			if($(this).is(":checked")){
				$("#ajax_loader").show();
				$("#available_no_sale_state_div").hide();
				$(".available_no_sale_state_main").hide();
				$.ajax({
	            	url:'<?= $ADMIN_HOST ?>/ajax_load_available_state.php',
	            	dataType:'JSON',
	            	type:'POST',
	            	success:function(res){
	            		$("#ajax_loader").hide();
	            		$("#zipcode_allow_only_state").html(res.optionsHtml);
	            		$('#zipcode_allow_only_state').multipleSelect('refresh');
	            		
	            	}
	            });
			}else{
				$("#zipcode_allow_only_state").html('');
				$('#zipcode_allow_only_state').multipleSelect('refresh');
				$("#available_specific_zipcode").html('');
	    		
	    		$("#available_no_sale_state_div").show();
	    		$(".available_no_sale_state_main").show();
	    		
	    		$("#ajax_loader").show();
				$('.available_state').each(function(){
					if(!$(this).is(":checked")){
						loadInitialAvailableState($(this));
					}
				});
				
				$("#ajax_loader").hide();
			}
			
		});

		$(document).on("blur",".availableEffectiveDate",function(){
			$id = $(this).attr('data-id');
			$state_id = $(this).attr('data-state-id');
			$effectiveDate=$(this).val();

			if($(this).hasClass('checkTermed') && new Date($effectiveDate) < new Date('<?= $stateUncheckDate ?>')){
				$(this).val('');
			}
		});
		$(document).on("blur",".availableTerminationDate",function(){
			$id = $(this).attr('data-id');
			$state_id = $(this).attr('data-state-id');
			$terminationDate=$(this).val();
			$effectiveDate=$("#available_no_sale_state_"+$state_id+"_"+$id+"_effective_date").val();

			if($(this).hasClass('checkTermed') && (new Date($terminationDate) < new Date('<?= $stateUncheckDate ?>') || new Date($terminationDate) < new Date($effectiveDate))){
				$(this).val('');
			}
		});

		$(document).on("click","input[name=is_specific_zipcode]",function(){
			$val=$(this).val();

			$("#available_only_zipcode_list_div").hide();
			if($val=="Y"){
				$("#available_only_zipcode_list_div").show();
			}
		});

		$(document).on("click",".coverage_options",function(){
			$val=$(this).val();
			$text=$(this).attr('data-text');
			$order=$(this).attr('data-order');
			
			if($(this).is(":checked")) {
				$(this).parent().addClass('active');
				
				if($val=="4"){
					$("#family_plan_rule_div").show();
				}
				addPricingFixed($val);
				addPricingOption($val);
			}else{
				$(this).parent().removeClass('active');
				
				if($val=="4"){
					$("#family_plan_rule_div").hide();
				}
				removePricingFixed($val);
				removePricingOption($val);
				
			}
		});

		$(document).on("change","input[name=term_automatically]",function(){
			$val=$(this).val();
			$("#term_automatically_after_div").hide();
			if($val=='Y'){
				$("#term_automatically_after_div").show();
			}
		});
		$(document).on("change","#term_automatically_within_type",function(){
			$val=$(this).val();

			if($val=="Weeks"){
				$end_range=52;
			}else if($val=="Months"){
				$end_range=24;
			}else if($val=="Years"){
				$end_range=10;
			}else if($val=="Coverage Period"){
				$end_range=24;
			}else{
				$end_range=365;
			}
			$("#term_automatically_within").html('');
			for($i=0;$i<=$end_range;$i++){
				$option_html='<option value="'+$i+'">'+$i+'</option>';
				$("#term_automatically_within").append($option_html);
			}
			$('#term_automatically_within').selectpicker('refresh');
		});

		$(document).on("change","#reinstate_option",function(){
			$val=$(this).val();

			$("#reinstate_within_div").hide();
			if($val=="Available Within Specific Time Frame"){
				$("#reinstate_within_div").show();
			}
		});
		$(document).on("change","#reinstate_within_type",function(){
			$val=$(this).val();

			if($val=="Weeks"){
				$end_range=52;
			}else if($val=="Months"){
				$end_range=24;
			}else if($val=="Years"){
				$end_range=10;
			}else{
				$end_range=365;
			}
			$("#reinstate_within").html('');
			for($i=0;$i<=$end_range;$i++){
				$option_html='<option value="'+$i+'">'+$i+'</option>';
				$("#reinstate_within").append($option_html);
			}

			$('#reinstate_within').selectpicker('refresh');
		});

		$(document).on("change","#reenroll_options",function(){
			$val=$(this).val();

			$("#reenroll_within_div").hide();
			if($val=="Available After Specific Time Frame"){
				$("#reenroll_within_div").show();
			}
		});
		$(document).on("change","#reenroll_within_type",function(){
			$val=$(this).val();

			if($val=="Weeks"){
				$end_range=52;
			}else if($val=="Months"){
				$end_range=24;
			}else if($val=="Years"){
				$end_range=10;
			}else{
				$end_range=365;
			}
			$("#reenroll_within").html('');
			for($i=0;$i<=$end_range;$i++){
				$option_html='<option value="'+$i+'">'+$i+'</option>';
				$("#reenroll_within").append($option_html);
			}
			$('#reenroll_within').selectpicker('refresh');
		});
	//********** prd_rules Code end ***************** 
	
	//********** prd_enrollment Code Start ***************** 
		$(document).on("click",".member_details_asked",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".member_details_required",function(){
			$dataQueID= $(this).attr('data-que-id');
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
			}
			if($(this).is(":checked")) {
				$("#memberQuestion_"+$dataQueID+"_asked").prop('checked',true);
			}
			$.uniform.update();
		});

		$(document).on("click",".spouse_details_asked",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".spouse_details_required",function(){
			$dataQueID= $(this).attr('data-que-id');
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
			}
			if($(this).is(":checked")) {
				$("#spouseQuestion_"+$dataQueID+"_asked").prop('checked',true);
			}
			$.uniform.update();
		});

		$(document).on("click",".dependent_details_asked",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".dependent_details_required",function(){
			$dataQueID= $(this).attr('data-que-id');
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
			}
			if($(this).is(":checked")) {
				$("#childQuestion_"+$dataQueID+"_asked").prop('checked',true);
			}
			$.uniform.update();
		});

		$(document).on("click",".memberCustom_details_required",function(){
			$id=$(this).attr('id');
			$dataQueID= $(this).attr('data-que-id');
			if($(this).is(":checked")) {
				$("#memberCustomQuestion_"+$dataQueID+"_asked").prop('checked',true);
				$.uniform.update();
			}

		});
		$(document).on("click",".spouseCustom_details_required",function(){
			$id=$(this).attr('id');
			$dataQueID= $(this).attr('data-que-id');
			if($(this).is(":checked")) {
				$("#spouseCustomQuestion_"+$dataQueID+"_asked").prop('checked',true);
				$.uniform.update();
			}
		});
		$(document).on("click",".dependentCustom_details_required",function(){
			$id=$(this).attr('id');
			$dataQueID= $(this).attr('data-que-id');
			if($(this).is(":checked")) {
				$("#childCustomQuestion_"+$dataQueID+"_asked").prop('checked',true);
				$.uniform.update();
			}
		});
		
		
		

		
		$(document).on("click",".view_custom_question_answers",function(e){
			e.preventDefault();
			$id=$(this).attr('data-id');
			$.colorbox({
				href:'<?= $ADMIN_HOST ?>/view_custom_question_answers.php?id='+$id,
		        iframe:true,
		        height:"550px",
		        width:"768px",
		  	});
		});

		$(document).on("click",".add_custom_question",function(e){
			e.preventDefault();
			$id=$(this).attr('data-id');
			$.colorbox({
				href:'<?= $ADMIN_HOST ?>/add_custom_question.php?id='+$id,
		        iframe:true,
		        height:"470px",
		        width:"768px",
		        escKey: false,
    			overlayClose: false,
		        onClosed:function(){
		        	loadCustomQuestions();
		        }
		  	});
		});

		$(document).on("click",".delete_custom_question",function(e){
			e.preventDefault();
			$id=$(this).attr('data-id');
			swal({
		        text: "Delete Question: Are you sure?",
		        showCancelButton: true,
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		        $.ajax({
		          url: '<?= $ADMIN_HOST ?>/ajax_delete_custom_question.php',
		          data: {question:$id},
		          dataType:'JSON',
		          method: 'POST',
		          success: function(data) {
		            loadCustomQuestions();
		          }
		        });
	      	});
		});
		
		$(document).on("change","input[name=is_beneficiary_required]",function(){
			$val=$(this).val();
			$("#beneficiary_details_div").hide();
			if($val=='Y'){
				$("#beneficiary_details_div").show();

				$(".principal_beneficiary_asked").prop('checked',true);
				$(".principal_beneficiary_required").prop('checked',true);
				$(".contingent_beneficiary_asked").prop('checked',true);
				$(".contingent_beneficiary_required").prop('checked',true);

				$.uniform.update();
			}
		});
		$(document).on("click",".principal_beneficiary_asked",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".principal_beneficiary_required",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".contingent_beneficiary_asked",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});
		$(document).on("click",".contingent_beneficiary_required",function(){
			if($(this).hasClass("disableCheckbox")){
				if($(this).is(":checked")) {
					$(this).prop("checked",false);
				}else{
					$(this).prop("checked",true);
				}
				$.uniform.update();
			}
		});

		$(document).on("click","#joinderAgreementRequire",function(){
			if($(this).is(":checked")) {
				$("#joinderAgreementDiv").show();
			}else{
				$("#joinderAgreementDiv").hide();
			}
		});

		

		$(document).on("change","input[name=is_license_require]",function(){
			$val=$(this).val();
			$("#license_require_div").hide();
			if($val=='Y'){
				$("#license_require_div").show();
			}
		});

		$(document).on("change","input[name=license_rule]",function(){
			$val=$(this).val();
			$("#specific_license_div").hide();
			$("#appointed_license_div").hide();
			if($val=='Licensed in Specific States Only'){
				$("#specific_license_div").show();
			}else if($val=='Licensed and Appointed'){
				$("#appointed_license_div").show();
			}
		});

		$(document).on("click",".specificState",function(){
			
			if(!$(this).is(":checked")) {
        		if($("#specificStateCheckAll").is(":checked")){
        			$("#specificStateCheckAll").prop('checked',false);
        			$.uniform.update();
        		}
        	}else{
        		if ($('.specificState:checked').length == $('.specificState').length) {
			     	$("#specificStateCheckAll").prop('checked',true);
        			$.uniform.update();  
			    }
        	}
		});
		$(document).on("click","#specificStateCheckAll",function(){
			$(".specificState").prop("checked",this.checked);
			$.uniform.update();
		});

		$(document).on("click",".preSaleState",function(){
			
			if(!$(this).is(":checked")) {
        		if($("#preSaleCheckAll").is(":checked")){
        			$("#preSaleCheckAll").prop('checked',false);
        			$.uniform.update();
        		}
        	}else{
        		if ($('.preSaleState:checked').length == $('.preSaleState').length) {
			     	$("#preSaleCheckAll").prop('checked',true);
        			$.uniform.update();  
			    }
			    if($("#justInTimeSaleState_"+$(this).val()).is(":checked")){
			    	$("#justInTimeSaleState_"+$(this).val()).prop("checked",false);
			    	$.uniform.update();  
			    }
        	}
		});
		$(document).on("click","#preSaleCheckAll",function(){
			$(".preSaleState").prop("checked",this.checked);
			$.uniform.update();
			if($(this).is(":checked")) {
				if($("#justInTimeSaleCheckAll").is(":checked")){
					$("#justInTimeSaleCheckAll").trigger("click");
				}else{
					$(".justInTimeSaleState").prop("checked",false);
					$.uniform.update();
				}
			}

		});

		$(document).on("click",".justInTimeSaleState",function(){
			
			if(!$(this).is(":checked")) {
        		if($("#justInTimeSaleCheckAll").is(":checked")){
        			$("#justInTimeSaleCheckAll").prop('checked',false);
        			$.uniform.update();
        		}
        	}else{
        		if ($('.justInTimeSaleState:checked').length == $('.justInTimeSaleState').length) {
			     	$("#justInTimeSaleCheckAll").prop('checked',true);
        			$.uniform.update();  
			    }
			    if($("#preSaleState_"+$(this).val()).is(":checked")){
			    	$("#preSaleState_"+$(this).val()).prop("checked",false);
			    	$.uniform.update();  
			    }
        	}
		});
		$(document).on("click","#justInTimeSaleCheckAll",function(){
			$(".justInTimeSaleState").prop("checked",this.checked);
			$.uniform.update();
			if($(this).is(":checked")) {
				if($("#preSaleCheckAll").is(":checked")){
					$("#preSaleCheckAll").trigger("click");
				}else{
					$(".preSaleState").prop("checked",false);
					$.uniform.update();
				}
			}
		});
		
		

		$(document).on("change","input[name=is_primary_age_restrictions]",function(){
			$val=$(this).val();
			$("#primary_age_restrictions_div").hide();
			if($val=='Y'){
				$("#primary_age_restrictions_div").show();
			}
			isAgeRestriction();
		});
		$(document).on("change","input[name=is_spouse_age_restrictions]",function(){
			$val=$(this).val();
			$("#spouse_age_restrictions_div").hide();
			if($val=='Y'){
				$("#spouse_age_restrictions_div").show();
			}
			isAgeRestriction();
		});
		$(document).on("change","input[name=is_children_age_restrictions]",function(){
			$val=$(this).val();
			$("#children_age_restrictions_div").hide();
			if($val=='Y'){
				$("#children_age_restrictions_div").show();
			}
			isAgeRestriction();
		});

		$(document).on("change","input[name=maxAgeAutoTermed]",function(){
			$val=$(this).val();
			$("#maxAgeAutoTermedDiv").hide();
			if($val=='Y'){
				$("#maxAgeAutoTermedDiv").show();
			}
		});

		$(document).on("click","#autoTermedMemberTypeAll",function(){
			$(".autoTermedMemberType").prop("checked",this.checked);
			$.uniform.update();
			$(".autoTermedMemberType").each(function(){
				autoMemberTypeSetting($(this));
			});
		});
		$(document).on("click",".autoTermedMemberType",function(){
			autoMemberTypeSetting($(this));
		});

		$(document).on("click",".addTrigger",function(){
			$title=$(this).attr('data-title');
			loadAutoTermedMemberSettingOptions($title);
		});


		$(document).on("click",".removeAutoTermMemberSettingInner",function(){
			$id=$(this).attr('data-id');
			$title=$(this).attr('data-title');
			
			$(".add_control_"+$title+"_"+$id).selectpicker('destroy');
			$("#autoTermMemberSettingInner_div_"+$title+"_"+$id).remove();
		});

		$(document).on("change","input[name=allowedBeyoundAge]",function(){
			$val=$(this).val();
			$("#allowedBeyoundAgedDiv").hide();
			if($val=='Y'){
				$("#allowedBeyoundAgedDiv").show();
			}
		});
		$(document).on("click","#allowedBeyoundAgeTypeAll",function(){
			$(".allowedBeyoundAge").prop("checked",this.checked);
			$.uniform.update();
		});
		$(document).on("click",".allowedBeyoundAge",function(){
			$val=$(this).val();

			if($(this).is(":checked")) {
				if ($('.allowedBeyoundAge:checked').length == $('.allowedBeyoundAge').length) {
			     	$("#allowedBeyoundAgeTypeAll").prop('checked',true);
        			$.uniform.update();  
			    }
			}else{
			    if($("#allowedBeyoundAgeTypeAll").is(":checked")){
        			$("#allowedBeyoundAgeTypeAll").prop('checked',false);
        			$.uniform.update();
        		}
			}
		});
	//********** prd_enrollment Code end *****************
	
	//********** prd_pricing Code Start *****************
		$(document).on("change","input[name=member_payment]",function(){
			$val=$(this).val();
			$("#member_payment_div").hide();
			if($val=='Recurring'){
				$("#member_payment_div").show();
			}
		});
		
		$(document).on("click","input[name=pricing_model]",function(){
			$val=$(this).val();
			$("#variable_price_div").hide();
			$("#fixed_price_div").hide();
			$("#variable_enrollee_div").hide();

			
			if($val=="FixedPrice"){
				$("#fixed_price_div").show();
				$(".coverage_options:checked").each(function(){
					$val=$(this).val();
					addPricingFixed($val);
				});
				addPricingFixedSettingDiv();
			}else if($val=="VariableEnrollee"){
				$("#variable_enrollee_div").show();
				$("#btn_set_pricing_matrix_Enrollee").show();
				$("#pricing_matrix_pricing_main_div_Enrollee").html('');
				$("#create_pricing_matrix_div_Enrollee").hide();
				$("#matrixID").val('');
				$("#keyID").val('');
				//$("#pricingMatrixKey").val('');
				pricingMatrixIframeEnrollee();
				
			}else{
				$("#variable_price_div").show();
				$("#btn_set_pricing_matrix").show();
				$("#pricing_matrix_pricing_main_div").html('');
				$("#create_pricing_matrix_div").hide();
				$("#matrixID").val('');
				$("#keyID").val('');
				//$("#pricingMatrixKey").val('');
				pricingMatrixIframe();
			}
		});

		$(document).on("blur",".pricingTerminationDate",function(){
			$id = $(this).attr('data-id');
			$terminationDate=$(this).val();
			$effectiveDate=$("#pricing_effective_date_"+$id).val();

			$lastID = $("#product_management .pricingEffectiveDate").last().attr('data-id');
			
			if(new Date($terminationDate) >= new Date('<?= $productActiveEffectiveDate ?>') && new Date($terminationDate) > new Date($effectiveDate)){
				if($id==$lastID){
					if($(".coverage_options:checked").length > 0){
						$("#terminationDateClear_"+$id).show();
						addPricingFixedDiv($terminationDate);
					}
				}else{
					$newEffectiveDate = add1DayToDate($terminationDate);
					$("#inner_pricing_div_"+$id).next('.inner_pricing_div').find('.pricingEffectiveDate').val($newEffectiveDate);
				}
			}else{
				if($(this).hasClass('checkTermed')){
					$(this).val('');
				}
			}
			
		});

		$(document).on("change","#height_by",function(){
			$val= $(this).val();
			$(".height_range_div").hide();
			if($val=="Range"){
				$(".height_range_div").show();
			}
		});
		$(document).on("change","#weight_by",function(){
			$val= $(this).val();
			$(".weight_range_div").hide();
			if($val=="Range"){
				$(".weight_range_div").show();
			}
		});
		$(document).on("change","#no_of_children_by",function(){
			$val= $(this).val();
			$(".no_of_children_by_range_div").hide();
			if($val=="Range"){
				$(".no_of_children_by_range_div").show();
			}
		});
		$(document).on("change","#height_by_Enrollee",function(){
			$val= $(this).val();
			$(".height_range_divEnrollee").hide();
			if($val=="Range"){
				$(".height_range_divEnrollee").show();
			}
		});
		$(document).on("change","#weight_by_Enrollee",function(){
			$val= $(this).val();
			$(".weight_range_divEnrollee").hide();
			if($val=="Range"){
				$(".weight_range_divEnrollee").show();
			}
		});
		$(document).on("change","#no_of_children_by_Enrollee",function(){
			$val= $(this).val();
			$(".no_of_children_by_range_divEnrollee").hide();
			if($val=="Range"){
				$(".no_of_children_by_range_divEnrollee").show();
			}
		});

		$(document).on("click",".clearPricingDiv",function(e){
			e.preventDefault();
			$dataId=$(this).attr('data-id');
			$id = parseInt($dataId);
			
			$("#pricing_termination_date_"+$dataId).val('');
			$("#terminationDateClear_"+$dataId).hide();
			
			$('#product_management .inner_pricing_div').each(function(){
                $display_number = parseInt($(this).attr('data-id'));
                
                if($id<0){
                	if($id > $display_number){
                		$("#inner_pricing_div_"+$(this).attr('data-id')).remove();
                	}
                }else{
                	if($display_number<0){
	                	if($id > $display_number){
	                		$("#inner_pricing_div_"+$(this).attr('data-id')).remove();
	                	}
	                }else{
	                	if($display_number > $id){
	                		$("#inner_pricing_div_"+$(this).attr('data-id')).remove();
	                	}
	                }
                }
                
                
                
                
            });
			addPricingFixedSettingDiv();
		});

		$(document).on("change",".price_control_matrix",function(){
			$price_control = $(this).val();
			spouseControl();

			if($(this).is(":checked")){
				$("."+$price_control+"PriceDiv").show();
				$("."+$price_control+"PriceRow").show();
			}else{
				$("."+$price_control+"PriceDiv").hide();
				$("."+$price_control+"PriceRow").hide();
			}
		});
		$(document).on("change","#enrolleeMatrix",function(){
			$val = $(this).val();
			$allowPricingUpdate = '<?= $allowPricingUpdate ?>';
			if(!$allowPricingUpdate){
				$val = $("#allow_enrolleeMatrix").val();
			}
			$("input[name='price_control_enrollee["+$val+"][]']").each(function(){
				$price_control = $(this).val();
				if($val=="Spouse" || $val == "Child"){
					$("#manual_matrix_Enrollee .9PriceDivEnrollee").hide();
					$("#manual_matrix_Enrollee .10PriceDivEnrollee").hide();
				}

				if($(this).is(":checked")){
					$("#manual_matrix_Enrollee ."+$price_control+"PriceDivEnrollee").show();
				}else{
					$("#manual_matrix_Enrollee ."+$price_control+"PriceDivEnrollee").hide();
				}
			});
		});
		$(document).on("change",".price_control_matrix_enrollee",function(){
			$price_control = $(this).val();
			$title=$(this).attr('data-label');
			spouseControl();
			$enrolleeType=$(this).attr('data-enrollee-type');

			if($(this).is(":checked")){
				$("."+$price_control+"PriceDivEnrollee").show();
				$("."+$price_control+"PriceRowEnrollee").show();

				

				

				if($price_control==1){
					$("#rate_change_after_trigger_div").show();
					
				}
			}else{
				if(!$("input[name='price_control_enrollee[Primary][]'][value='"+$price_control+"']").is(":checked") && !$("input[name='price_control_enrollee[Spouse][]'][value='"+$price_control+"']").is(":checked") && !$("input[name='price_control_enrollee[Child][]'][value='"+$price_control+"']").is(":checked")){
					$("."+$price_control+"PriceDivEnrollee").hide();
					$("."+$price_control+"PriceRowEnrollee").hide();


					if($price_control==1){
						$("#rate_change_after_trigger_div").hide();
					}
				}

			}
			$("#enrolleeMatrix").trigger('change');
		});

		$(document).on('click', '#btn_set_pricing_matrix', function() {
			// $("#manual_matrixLI").trigger('click');
			$("#pricingTypeTab").show();
			$("#create_pricing_matrix_div").show();
			$(this).hide();
			pricingDataDisabled(false,0,false);
			clearPricing();
			$number = addPricingMatrix(0,'');
			$("#pricing_matrix_setting_div_"+$number).hide();
			if ($(window).width() <= 500) {
		      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
		      autoResizeNav();
	   		}	

		});
		$(document).on('click', '#btn_set_pricing_matrix_Enrollee', function() {
			// $("#manual_matrixLI_Enrollee").trigger('click');
			$("#pricingTypeTabEnrollee").show();
			$("#create_pricing_matrix_div_Enrollee").show();
			$(this).hide();
			pricingDataDisabled_Enrollee(false,0,false);
			clearPricing();
			$number = addPricingMatrixEnrollee(0,'');
			$("#pricing_matrix_setting_div_Enrollee_"+$number).hide();
			if ($(window).width() <= 500) {
		      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
		      autoResizeNav();
	   		}

		});
		$(document).on('click', '#cancelPricingFixed,#cancelPricingMatrix', function() {
			$("#create_pricing_matrix_div").hide();
			$("#matrixID").val('');
			$("#keyID").val('');
			$("#btn_set_pricing_matrix").show();
			$("#pricing_matrix_pricing_main_div").html('');
			if ($(window).width() <= 500) {
		      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
		      autoResizeNav();
	   		}
			
			$(".after_export").hide();
	        $(".before_export").show();
	        $(".while_exportBTN").hide();
	        $(".while_export").hide();
	       
	        $("#inline_content").hide();
	        $('#csv_file').val('');

	        $(".height_range_div").hide();
	        $(".weight_range_div").hide();
	        $(".no_of_children_by_range_div").hide();
		});
		$(document).on('click', '#cancelPricingFixedEnrollee,#cancelPricingMatrixEnrollee', function() {
			$("#create_pricing_matrix_div_Enrollee").hide();
			$("#matrixID").val('');
			$("#keyID").val('');
			$("#btn_set_pricing_matrix_Enrollee").show();
			$("#pricing_matrix_pricing_main_div_Enrollee").html('');
			if ($(window).width() <= 500) {
		      $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
		      autoResizeNav();
	   		}
			
			$(".after_exportEnrollee").hide();
	        $(".before_exportEnrollee").show();
	        $(".while_exportBTNEnrollee").hide();
	        $(".while_exportEnrollee").hide();
	       
	        $("#inline_content_Enrollee").hide();
	        $('#csv_file_Enrollee').val('');

	        $(".height_range_divEnrollee").hide();
	        $(".weight_range_divEnrollee").hide();
	        $(".no_of_children_by_range_divEnrollee").hide();
		});
		
		$(document).on('click', '#clearPricingMatrix', function() {
			clearPricing();
		});

		$(document).on("blur",".pricingMatrixTerminationDate",function(){
			$id = $(this).attr('data-id');
			$terminationDate=$(this).val();
			$effectiveDate=$("#pricing_matrix_effective_date_"+$id).val();
			$matrixID = $("#matrixID").val();

			$lastID = $("#product_management .pricingMatrixEffectiveDate").last().attr('data-id');
			
			if(new Date($terminationDate) >= new Date('<?= $productActiveEffectiveDate ?>') && new Date($terminationDate) > new Date($effectiveDate)){
				if($id==$lastID){
						//if($matrixID==""){
							$("#terminationDateMatrixClear_"+$id).show();
							addPricingMatrix(0,'add');
						//}
				}else{
					$newEffectiveDate = add1DayToDate($terminationDate);
					$("#inner_pricing_matrix_div_"+$id).next('.inner_pricing_matrix_div').find('.pricingMatrixEffectiveDate').val($newEffectiveDate);
				}
			}else{
				if($(this).hasClass('checkTermed')){
					$(this).val('');
				}
			}
			
		});

		$(document).on("click",".clearMarixPricingDiv",function(e){
			e.preventDefault();
			$dataId=$(this).attr('data-id');
			$id = parseInt($dataId);
			
			$("#pricing_matrix_termination_date_"+$dataId).val('');
			$("#terminationDateMatrixClear_"+$dataId).hide();
			
			$('#product_management .inner_pricing_matrix_div').each(function(){
                $display_number = parseInt($(this).attr('data-id'));
                
                if($id<0){
                	if($id > $display_number){
                		$("#inner_pricing_matrix_div_"+$(this).attr('data-id')).remove();
                	}
                }else{
                	if($display_number<0){
	                	if($id > $display_number){
	                		$("#inner_pricing_matrix_div_"+$(this).attr('data-id')).remove();
	                	}
	                }else{
	                	if($display_number > $id){
	                		$("#inner_pricing_matrix_div_"+$(this).attr('data-id')).remove();
	                	}
	                }
                }
            });
			
			$("#product_management .pricing_matrix_setting_div").hide();
			$number=$("#product_management .inner_pricing_matrix_div").length;
			$("#product_management .newPricingMatrixOnRenewals").prop('checked', false);
			$("#product_management .newPricingMatrixOnRenewals").not('.js-switch').uniform();
			if($number > 1){
				$("#pricing_matrix_setting_div_"+$dataId).show();
			}
		});

		$(document).on("blur",".caculatePricing",function(){
			
			$val = $("input[name='pricing_model']:checked"). val();
			$number=$(this).attr('data-id');
			if($val == "FixedPrice"){
				$tier_id=$(this).attr('data-tier-id');

      			$Retail = $("input[name='pricing_fixed_price["+$number+"]["+$tier_id+"][Retail]']").val();
      			$NonCommissionable = $("input[name='pricing_fixed_price["+$number+"]["+$tier_id+"][NonCommissionable]']").val();
			}else if($val == "VariableEnrollee"){
				$Retail = $("input[name='pricing_matrix_price_Enrollee["+$number+"][Retail]']").val();
      			$NonCommissionable = $("input[name='pricing_matrix_price_Enrollee["+$number+"][NonCommissionable]']").val();
			}else{
				$Retail = $("input[name='pricing_matrix_price["+$number+"][Retail]']").val();
      			$NonCommissionable = $("input[name='pricing_matrix_price["+$number+"][NonCommissionable]']").val();
			}

	      	$NonCommissionable = $NonCommissionable.replace(",", "");
	      	$Retail = $Retail.replace(",", "");

	      	$NonCommissionable = parseFloat($NonCommissionable);
	      	$Retail = parseFloat($Retail);
	      	$Commissionable=($Retail - $NonCommissionable).toFixed(2);

	      	if($Commissionable<0){
	        	$Commissionable = '0.00';
	      	}

		    if($val == "FixedPrice"){
	      		$("input[name='pricing_fixed_price["+$number+"]["+$tier_id+"][Commissionable]']").val($Commissionable);
	      	}else if($val == "VariableEnrollee"){
	      		$("input[name='pricing_matrix_price_Enrollee["+$number+"][Commissionable]']").val($Commissionable);
	      	}else{
	      		$("input[name='pricing_matrix_price["+$number+"][Commissionable]']").val($Commissionable);
	      	}
	      	formatPricing();
		});

		$(document).on("blur",".pricingMatrixTerminationDate_Enrollee",function(){
			$id = $(this).attr('data-id');
			$terminationDate=$(this).val();
			$effectiveDate=$("#pricing_matrix_effective_date_Enrollee_"+$id).val();
			$matrixID = $("#matrixID").val();

			$lastID = $("#product_management .pricingMatrixEffectiveDate_Enrollee").last().attr('data-id');
			if(new Date($terminationDate) >= new Date('<?= $productActiveEffectiveDate ?>') && new Date($terminationDate) > new Date($effectiveDate)){
				if($id==$lastID){
						//if($matrixID==""){
							$("#terminationDateMatrixClear_Enrollee_"+$id).show();
							addPricingMatrixEnrollee(0,'add');
						//}
				}else{
					$newEffectiveDate = add1DayToDate($terminationDate);
					$("#inner_pricing_matrix_div_Enrollee_"+$id).next('.inner_pricing_matrix_div_Enrollee').find('.pricingMatrixEffectiveDate_Enrollee').val($newEffectiveDate);
				}
			}else{
				if($(this).hasClass('checkTermed')){
					$(this).val('');
				}
			}
			
		});

		$(document).on("click",".clearMarixPricingDiv_Enrollee",function(e){
			e.preventDefault();
			$dataId=$(this).attr('data-id');
			$id = parseInt($dataId);
			
			$("#pricing_matrix_termination_date_Enrollee_"+$dataId).val('');
			$("#terminationDateMatrixClear_Enrollee_"+$dataId).hide();
			
			$('#product_management .inner_pricing_matrix_div_Enrollee').each(function(){
                $display_number = parseInt($(this).attr('data-id'));
                
                if($id<0){
                	if($id > $display_number){
                		$("#inner_pricing_matrix_div_Enrollee_"+$(this).attr('data-id')).remove();
                	}
                }else{
                	if($display_number<0){
	                	if($id > $display_number){
	                		$("#inner_pricing_matrix_div_Enrollee_"+$(this).attr('data-id')).remove();
	                	}
	                }else{
	                	if($display_number > $id){
	                		$("#inner_pricing_matrix_div_Enrollee_"+$(this).attr('data-id')).remove();
	                	}
	                }
                }
            });
			
			$("#product_management .pricing_matrix_setting_div_Enrollee").hide();
			$number=$("#product_management .inner_pricing_matrix_div_Enrollee").length;
			$("#product_management .newPricingMatrixOnRenewals_Enrollee").prop('checked', false);
			$("#product_management .newPricingMatrixOnRenewals_Enrollee").not('.js-switch').uniform();
			if($number > 1){
				$("#pricing_matrix_setting_div_Enrollee_"+$dataId).show();
			}
		});

		$(document).off('click', '#addPricingMatrix');
      	$(document).on('click', '#addPricingMatrix', function(e) {
      		$('.error').html('');
      		$("ajax_loader").show();
      		$.ajax({
      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_add.php',
      			dataType:'JSON',
      			data:$("#product_management").serialize(),
      			type:'POST',
      			success:function(res){
      				$("ajax_loader").hide();
      				if (res.status == 'success') {
		              setNotifySuccess(res.msg);
		              $("#pricingMatrixKey").val(res.pricingMatrixKey);
		              $("#cancelPricingFixed").trigger('click');
		              pricingMatrixIframe();
		            } else if (res.status == 'fail') {
		              var is_error = true;
		              $.each(res.errors, function (index, error) {
		                  $('#error_' + index).html(error);
		                  if (is_error) {
		                      var offset = $('#error_' + index).offset();
		                      if(typeof(offset) === "undefined"){
		                          console.log("Not found : "+index);
		                      }else{
		                          var offsetTop = offset.top;
		                          var totalScroll = offsetTop - 195;
		                          $('body,html').animate({
		                              scrollTop: totalScroll
		                          }, 1200);
		                          is_error = false;
		                      }
		                  }
		              });
		            }
		            return false;
      			}
      		});
      	});

      	$(document).off('click', '#addPricingMatrixEnrollee');
      	$(document).on('click', '#addPricingMatrixEnrollee', function(e) {
      		$('.error').html('');
      		$("ajax_loader").show();
      		$.ajax({
      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_add_Enrollee.php',
      			dataType:'JSON',
      			data:$("#product_management").serialize(),
      			type:'POST',
      			success:function(res){
      				$("ajax_loader").hide();
      				if (res.status == 'success') {
      				  clearPricing();
		              setNotifySuccess(res.msg);
		              $("#pricingMatrixKey").val(res.pricingMatrixKey);
		              $("#cancelPricingFixedEnrollee").trigger('click');
		              pricingMatrixIframeEnrollee();

		            } else if (res.status == 'fail') {
		              var is_error = true;
		              $.each(res.errors, function (index, error) {
		                  $('#error_' + index).html(error);
		                  if (is_error) {
		                      var offset = $('#error_' + index).offset();
		                      if(typeof(offset) === "undefined"){
		                          console.log("Not found : "+index);
		                      }else{
		                          var offsetTop = offset.top;
		                          var totalScroll = offsetTop - 195;
		                          $('body,html').animate({
		                              scrollTop: totalScroll
		                          }, 1200);
		                          is_error = false;
		                      }
		                  }
		              });
		            }
		            return false;
      			}
      		});
      	});

      	
      	$(document).off('click', '.matrixPricingEdit,.matrixPricingClone');
      	$(document).on('click', '.matrixPricingEdit,.matrixPricingClone', function(e) {
			$id=$(this).attr('data-row-id');
			$arr=jQuery.parseJSON($("#pricingMatrixKey").val());
			$innerArrMatrix = $arr[$id];

			$clickType=$(this).attr('data-click-type');

			if($clickType=="Clone"){
				$id=$id+Math.floor((Math.random() * 100) + 1);
				$("#matrixID").val('');
				$("#matrixGroup").val('');
			}else{
				$("#matrixID").val($id);
				$("#matrixGroup").val($id);
			}
			$("#btn_set_pricing_matrix").hide();
			$("#create_pricing_matrix_div").show();
			$("#manual_matrixLI").trigger('click');
			$("#pricingTypeTab").hide();
			$("#pricing_matrix_pricing_main_div").html('');

			$(".height_range_div").hide();
	        $(".weight_range_div").hide();
	        $(".no_of_children_by_range_div").hide();

	        

	        $.each($innerArrMatrix,function($k,$v){
				$innerArr = $v;
				if($clickType=="Clone"){
					$k=$k+Math.floor((Math.random() * 100) + 1);
				}
				addPricingMatrix($k,'');
			
				$("#matrixPlanType").val($innerArr['matrixPlanType']);
				$("#allow_matrixPlanType").val($innerArr['matrixPlanType']);
				$("#age_from").val($innerArr[1]['age_from']);
				$("#allow_age_from").val($innerArr[1]['age_from']);
				$("#age_to").val($innerArr[1]['age_to']);
				$("#allow_age_to").val($innerArr[1]['age_to']);
				$("#state").val($innerArr[2]['matrix_value']);
				$("#allow_state").val($innerArr[2]['matrix_value']);
				$("#zip").val($innerArr[3]['matrix_value']);
				$("#allow_zip").val($innerArr[3]['matrix_value']);
				$("#gender").val($innerArr[4]['matrix_value']);
				$("#allow_gender").val($innerArr[4]['matrix_value']);
				$("#smoking_status").val($innerArr[5]['matrix_value']);
				$("#allow_smoking_status").val($innerArr[5]['matrix_value']);
				$("#tobacco_status").val($innerArr[6]['matrix_value']);
				$("#allow_tobacco_status").val($innerArr[6]['matrix_value']);
				$("#height_by").val($innerArr[7]['height_by']);
				$("#allow_height_by").val($innerArr[7]['height_by']);
				$("#height_feet").val($innerArr[7]['height_feet']);
				$("#allow_height_feet").val($innerArr[7]['height_feet']);
				$("#height_inch").val($innerArr[7]['height_inch']);
				$("#allow_height_inch").val($innerArr[7]['height_inch']);
				
				if($innerArr[7]['height_by']=="Range"){
					$(".height_range_div").show();
					$("#height_feet_to").val($innerArr[7]['height_feet_to']);
					$("#allow_height_feet_to").val($innerArr[7]['height_feet_to']);
					$("#height_inch_to").val($innerArr[7]['height_inch_to']);
					$("#allow_height_inch_to").val($innerArr[7]['height_inch_to']);
				}
				$("#weight_by").val($innerArr[8]['weight_by']);
				$("#allow_weight_by").val($innerArr[8]['weight_by']);
				$("#weight").val($innerArr[8]['weight']);
				$("#allow_weight").val($innerArr[8]['weight']);
				if($innerArr[8]['weight_by']=="Range"){
					 $(".weight_range_div").show();
					 $("#weight_to").val($innerArr[8]['weight_to']);
					 $("#allow_weight_to").val($innerArr[8]['weight_to']);
				}
				
				$("#no_of_children_by").val($innerArr[9]['no_of_children_by']);
				$("#allow_no_of_children_by").val($innerArr[9]['no_of_children_by']);
				$("#no_of_children").val($innerArr[9]['no_of_children']);
				$("#allow_no_of_children").val($innerArr[9]['no_of_children']);
				if($innerArr[9]['no_of_children_by']=="Range"){
					$(".no_of_children_by_range_div").show();
					$("#no_of_children_to").val($innerArr[9]['no_of_children_to']);
					$("#allow_no_of_children_to").val($innerArr[9]['no_of_children_to']);
				}
				
				$("#has_spouse").val($innerArr[10]['matrix_value']);
				$("#allow_has_spouse").val($innerArr[10]['matrix_value']);
				$("#spouse_age_from").val($innerArr[11]['spouse_age_from']);
				$("#allow_spouse_age_from").val($innerArr[11]['spouse_age_from']);
				$("#spouse_age_to").val($innerArr[11]['spouse_age_to']);
				$("#allow_spouse_age_to").val($innerArr[11]['spouse_age_to']);
				$("#spouse_gender").val($innerArr[12]['matrix_value']);
				$("#allow_spouse_gender").val($innerArr[12]['matrix_value']);
				$("#spouse_smoking_status").val($innerArr[13]['matrix_value']);
				$("#allow_spouse_smoking_status").val($innerArr[13]['matrix_value']);
				$("#spouse_tobacco_status").val($innerArr[14]['matrix_value']);
				$("#allow_spouse_tobacco_status").val($innerArr[14]['matrix_value']);
				$("#spouse_height_feet").val($innerArr[15]['spouse_height_feet']);
				$("#allow_spouse_height_feet").val($innerArr[15]['spouse_height_feet']);
				$("#spouse_height_inch").val($innerArr[15]['spouse_height_inch']);
				$("#allow_spouse_height_inch").val($innerArr[15]['spouse_height_inch']);
				$("#spouse_weight").val($innerArr[16]['spouse_weight']);
				$("#allow_spouse_weight").val($innerArr[16]['spouse_weight']);
				$("#spouse_weight_type").val($innerArr[16]['spouse_weight_type']);
				$("#allow_spouse_weight_type").val($innerArr[16]['spouse_weight_type']);
				$("#benefit_amount").val($innerArr[17]['matrix_value']);
				$("#allow_benefit_amount").val($innerArr[17]['matrix_value']);
				$("#in_patient_benefit").val($innerArr[18]['matrix_value']);
				$("#allow_in_patient_benefit").val($innerArr[18]['matrix_value']);
				$("#out_patient_benefit").val($innerArr[19]['matrix_value']);
				$("#allow_out_patient_benefit").val($innerArr[19]['matrix_value']);
				$("#monthly_income").val($innerArr[20]['matrix_value']);
				$("#allow_monthly_income").val($innerArr[20]['matrix_value']);
				// $("#benefit_percentage").val($innerArr[21]['matrix_value']);
				// $("#allow_benefit_percentage").val($innerArr[21]['matrix_value']);
				
				$("#pricing_matrix_price_"+$k+"_Retail").val($innerArr['RetailPrice']);
				$("#pricing_matrix_price_"+$k+"_NonCommissionable").val($innerArr['NonCommissionablePrice']);
				$("#pricing_matrix_price_"+$k+"_Commissionable").val($innerArr['CommissionablePrice']);
				$("#pricing_matrix_effective_date_"+$k).val($innerArr['pricing_matrix_effective_date']);
				$("#pricing_matrix_termination_date_"+$k).val($innerArr['pricing_matrix_termination_date']);

				if($innerArr['newPricingMatrixOnRenewals']==''){
					$("#pricing_matrix_setting_div_"+$k).hide();
				}else{
					$("input[name='newPricingMatrixOnRenewals["+$k+"]'][value='"+$innerArr['newPricingMatrixOnRenewals']+"']").prop('checked', true);
				
					$("input[name='newPricingMatrixOnRenewals["+$k+"]']").not('.js-switch').uniform();
				}
				$allowPricingUpdate = $("#allowPricingUpdate").val();
				if(!$allowPricingUpdate || (new Date($innerArr['pricing_matrix_effective_date']) < new Date('<?= $stateUncheckDate ?>'))){
					$pricingReadonly = false;
					if(new Date($innerArr['pricing_matrix_effective_date']) < new Date('<?= $stateUncheckDate ?>')){
						$pricingReadonly = true;
					}
					pricingDataDisabled(true,$k,$pricingReadonly);
					$("#pricingDataDisabled").val('Y');
				}else{
					pricingDataDisabled(false,$k,false);
					$("#pricingDataDisabled").val('N');
				}
			});
			$('.clearPricing').selectpicker('refresh');
			fRefresh();
      	});

      	
      	$(document).off('click', '.matrixPricingDelete');
      	$(document).on('click', '.matrixPricingDelete', function(e) {
      		$id=$(this).attr('data-row-id');
      		swal({
		        text: "Delete Record: Are you sure?",
		        showCancelButton: true,
		        confirmButtonColor: "#bd4360",
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		      	$("#matrixID").val($id);
		        $("ajax_loader").show();
	      		$.ajax({
	      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_delete.php',
	      			dataType:'JSON',
	      			data:$("#product_management").serialize(),
	      			type:'POST',
	      			success:function(res){
	      				$("ajax_loader").hide();
	      				$("#matrixID").val('');
	      				if (res.status == 'success') {
			              setNotifySuccess(res.msg);
			              $("#pricingMatrixKey").val(res.pricingMatrixKey);
			              pricingMatrixIframe();
			            } 
	      			}
	      		});
	      	});
      	});

      	$(document).off("click",".deletePricingMatrixBox");
        $(document).on("click",".deletePricingMatrixBox",function(){
        	if ($('input[name="pricingMatrixRowDel[]"]:checked').length > 0){
	             $("#deleteMatrixBtnDiv").show();
	          }else{
	            $("#deleteMatrixBtnDiv").hide();
	          }
        });

      	$(document).off('click', '#deleteMatrixBtn');
      	$(document).on('click', '#deleteMatrixBtn', function(e) {
      		swal({
		        text: "Delete Record: Are you sure?",
		        showCancelButton: true,
		        confirmButtonColor: "#bd4360",
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		        $("ajax_loader").show();
	      		$.ajax({
	      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_delete.php',
	      			dataType:'JSON',
	      			data:$("#product_management").serialize(),
	      			type:'POST',
	      			success:function(res){
	      				$("ajax_loader").hide();
	      				$("#matrixID").val('');
	      				if (res.status == 'success') {
			              setNotifySuccess(res.msg);
			              $("#pricingMatrixKey").val(res.pricingMatrixKey);
			              pricingMatrixIframe();
			            } 
	      			}
	      		});
	      	});
      	});



      	$(document).off('click', '.matrixPricingCloneEnrollee,.matrixPricingEditEnrollee');
      	$(document).on('click', '.matrixPricingCloneEnrollee,.matrixPricingEditEnrollee', function(e) {
			$id=$(this).attr('data-row-id');
			$arr=jQuery.parseJSON($("#pricingMatrixKey").val());
			$innerArrMatrix = $arr[$id];
			
			$clickType=$(this).attr('data-click-type');

			if($clickType=="Clone"){
				$id=$id+Math.floor((Math.random() * 100) + 1);
				$("#matrixID").val('');
				$("#matrixGroupEnrollee").val('');
			}else{
				$("#matrixID").val($id);
				$("#matrixGroupEnrollee").val($id);
			}
			
			$("#btn_set_pricing_matrix_Enrollee").hide();
			$("#create_pricing_matrix_div_Enrollee").show();
			// $("#manual_matrixLI_Enrollee").trigger('click');
			$("#pricingTypeTabEnrollee").hide();
			$("#pricing_matrix_pricing_main_div_Enrollee").html('');

			$(".height_range_divEnrollee").hide();
	        $(".weight_range_divEnrollee").hide();
	        $(".no_of_children_by_range_divEnrollee").hide();
			
			

			$.each($innerArrMatrix,function($k,$v){
				$innerArr = $v;
				if($clickType=="Clone"){
					$k=$k+Math.floor((Math.random() * 100) + 1);
				}
				addPricingMatrixEnrollee($k,'');

				$("#enrolleeMatrix").val($innerArr['enrolleeMatrix']);
				$("#allow_enrolleeMatrix").val($innerArr['enrolleeMatrix']);
				$("#enrolleeMatrix").trigger("change");
				
				$("#age_from_Enrollee").val($innerArr[1]['age_from']);
				$("#allow_age_from_Enrollee").val($innerArr[1]['age_from']);
				$("#age_to_Enrollee").val($innerArr[1]['age_to']);
				$("#allow_age_to_Enrollee").val($innerArr[1]['age_to']);
				$("#state_Enrollee").val($innerArr[2]['matrix_value']);
				$("#allow_state_Enrollee").val($innerArr[2]['matrix_value']);
				$("#zip_Enrollee").val($innerArr[3]['matrix_value']);
				$("#allow_zip_Enrollee").val($innerArr[3]['matrix_value']);
				$("#gender_Enrollee").val($innerArr[4]['matrix_value']);
				$("#allow_gender_Enrollee").val($innerArr[4]['matrix_value']);
				$("#smoking_status_Enrollee").val($innerArr[5]['matrix_value']);
				$("#allow_smoking_status_Enrollee").val($innerArr[5]['matrix_value']);
				$("#tobacco_status_Enrollee").val($innerArr[6]['matrix_value']);
				$("#allow_tobacco_status_Enrollee").val($innerArr[6]['matrix_value']);
				$("#height_by_Enrollee").val($innerArr[7]['height_by']);
				$("#allow_height_by_Enrollee").val($innerArr[7]['height_by']);
				$("#height_feet_Enrollee").val($innerArr[7]['height_feet']);
				$("#allow_height_feet_Enrollee").val($innerArr[7]['height_feet']);
				$("#height_inch_Enrollee").val($innerArr[7]['height_inch']);
				$("#allow_height_inch_Enrollee").val($innerArr[7]['height_inch']);
				
				if($innerArr[7]['height_by']=="Range"){
					$(".height_range_divEnrollee").show();
					$("#height_feet_to_Enrollee").val($innerArr[7]['height_feet_to']);
					$("#allow_height_feet_to_Enrollee").val($innerArr[7]['height_feet_to']);
					$("#height_inch_to_Enrollee").val($innerArr[7]['height_inch_to']);
					$("#allow_height_inch_to_Enrollee").val($innerArr[7]['height_inch_to']);
				}
				$("#weight_by_Enrollee").val($innerArr[8]['weight_by']);
				$("#allow_weight_by_Enrollee").val($innerArr[8]['weight_by']);
				$("#weight_Enrollee").val($innerArr[8]['weight']);
				$("#allow_weight_Enrollee").val($innerArr[8]['weight']);
				if($innerArr[8]['weight_by']=="Range"){
					 $(".weight_range_divEnrollee").show();
					 $("#weight_to_Enrollee").val($innerArr[8]['weight_to']);
					 $("#allow_weight_to_Enrollee").val($innerArr[8]['weight_to']);
				}
				
				$("#no_of_children_by_Enrollee").val($innerArr[9]['no_of_children_by']);
				$("#allow_no_of_children_by_Enrollee").val($innerArr[9]['no_of_children_by']);
				$("#no_of_children_Enrollee").val($innerArr[9]['no_of_children']);
				$("#allow_no_of_children_Enrollee").val($innerArr[9]['no_of_children']);
				if($innerArr[9]['no_of_children_by']=="Range"){
					$(".no_of_children_by_range_divEnrollee").show();
					$("#no_of_children_to_Enrollee").val($innerArr[9]['no_of_children_to']);
					$("#allow_no_of_children_to_Enrollee").val($innerArr[9]['no_of_children_to']);
				}
				
				$("#has_spouse_Enrollee").val($innerArr[10]['matrix_value']);
				$("#allow_has_spouse_Enrollee").val($innerArr[10]['matrix_value']);
				$("#spouse_age_from_Enrollee").val($innerArr[11]['spouse_age_from']);
				$("#allow_spouse_age_from_Enrollee").val($innerArr[11]['spouse_age_from']);
				$("#spouse_age_to_Enrollee").val($innerArr[11]['spouse_age_to']);
				$("#allow_spouse_age_to_Enrollee").val($innerArr[11]['spouse_age_to']);
				$("#spouse_gender_Enrollee").val($innerArr[12]['matrix_value']);
				$("#allow_spouse_gender_Enrollee").val($innerArr[12]['matrix_value']);
				$("#spouse_smoking_status_Enrollee").val($innerArr[13]['matrix_value']);
				$("#allow_spouse_smoking_status_Enrollee").val($innerArr[13]['matrix_value']);
				$("#spouse_tobacco_status_Enrollee").val($innerArr[14]['matrix_value']);
				$("#allow_spouse_tobacco_status_Enrollee").val($innerArr[14]['matrix_value']);
				$("#spouse_height_feet_Enrollee").val($innerArr[15]['spouse_height_feet']);
				$("#allow_spouse_height_feet_Enrollee").val($innerArr[15]['spouse_height_feet']);
				$("#spouse_height_inch_Enrollee").val($innerArr[15]['spouse_height_inch']);
				$("#allow_spouse_height_inch_Enrollee").val($innerArr[15]['spouse_height_inch']);
				$("#spouse_weight_Enrollee").val($innerArr[16]['spouse_weight']);
				$("#allow_spouse_weight_Enrollee").val($innerArr[16]['spouse_weight']);
				$("#spouse_weight_type_Enrollee").val($innerArr[16]['spouse_weight_type']);
				$("#allow_spouse_weight_type_Enrollee").val($innerArr[16]['spouse_weight_type']);
				$("#benefit_amount_Enrollee").val($innerArr[17]['matrix_value']);
				$("#allow_benefit_amount_Enrollee").val($innerArr[17]['matrix_value']);
				$("#in_patient_benefit_Enrollee").val($innerArr[18]['matrix_value']);
				$("#allow_in_patient_benefit_Enrollee").val($innerArr[18]['matrix_value']);
				$("#out_patient_benefit_Enrollee").val($innerArr[19]['matrix_value']);
				$("#allow_out_patient_benefit_Enrollee").val($innerArr[19]['matrix_value']);
				$("#monthly_income_Enrollee").val($innerArr[20]['matrix_value']);
				$("#allow_monthly_income_Enrollee").val($innerArr[20]['matrix_value']);
				// $("#benefit_percentage_Enrollee").val($innerArr[21]['matrix_value']);
				// $("#allow_benefit_percentage_Enrollee").val($innerArr[21]['matrix_value']);
				
				$("#pricing_matrix_price_Enrollee_"+$k+"_Retail").val($innerArr['RetailPrice']);
				$("#pricing_matrix_price_Enrollee_"+$k+"_NonCommissionable").val($innerArr['NonCommissionablePrice']);
				$("#pricing_matrix_price_Enrollee_"+$k+"_Commissionable").val($innerArr['CommissionablePrice']);
				$("#pricing_matrix_effective_date_Enrollee_"+$k).val($innerArr['pricing_matrix_effective_date']);
				$("#pricing_matrix_termination_date_Enrollee_"+$k).val($innerArr['pricing_matrix_termination_date']);

				if($innerArr['newPricingMatrixOnRenewals']==''){
					$("#pricing_matrix_setting_div_Enrollee_"+$k).hide();
				}else{
					$("input[name='newPricingMatrixOnRenewals_Enrollee["+$k+"]'][value='"+$innerArr['newPricingMatrixOnRenewals']+"']").prop('checked', true);
				
					$("input[name='newPricingMatrixOnRenewals_Enrollee["+$k+"]']").not('.js-switch').uniform();
				}
				$allowPricingUpdate = $("#allowPricingUpdate").val();

				if(!$allowPricingUpdate || (new Date($innerArr['pricing_matrix_effective_date']) < new Date('<?= $stateUncheckDate ?>'))){
					$pricingReadonly = false;
					if(new Date($innerArr['pricing_matrix_effective_date']) < new Date('<?= $stateUncheckDate ?>')){
						$pricingReadonly = true;
					}
					pricingDataDisabled_Enrollee(true,$k,$pricingReadonly);
					$("#pricingDataDisabled_Enrollee").val('Y');
				}else{
					pricingDataDisabled_Enrollee(false,$id,false);
					$("#pricingDataDisabled_Enrollee").val('N');
				}
			});
			$('.clearPricing').selectpicker('refresh');
			fRefresh();
      	});

      	
      	$(document).off('click', '.matrixPricingDeleteEnrollee');
      	$(document).on('click', '.matrixPricingDeleteEnrollee', function(e) {
      		$id=$(this).attr('data-row-id');
      		swal({
		        text: "Delete Record: Are you sure?",
		        showCancelButton: true,
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		      	$("#matrixID").val($id);
		        $("ajax_loader").show();
	      		$.ajax({
	      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_delete.php',
	      			dataType:'JSON',
	      			data:$("#product_management").serialize(),
	      			type:'POST',
	      			success:function(res){
	      				$("ajax_loader").hide();
	      				$("#matrixID").val('');
	      				if (res.status == 'success') {
			              setNotifySuccess(res.msg);
			              $("#pricingMatrixKey").val(res.pricingMatrixKey);
			              pricingMatrixIframeEnrollee();
			            } 
	      			}
	      		});
	      	});
      	});

      	$(document).off("click",".deleteEnroleePricingMatrixBox");
        $(document).on("click",".deleteEnroleePricingMatrixBox",function(){
        	if ($('input[name="pricingMatrixRowDel[]"]:checked').length > 0){
	             $("#deleteEnroleeMatrixBtnDiv").show();
	          }else{
	            $("#deleteEnroleeMatrixBtnDiv").hide();
	          }
        });

      	$(document).off('click', '#deleteEnroleeMatrixBtn');
      	$(document).on('click', '#deleteEnroleeMatrixBtn', function(e) {
      		swal({
		        text: "Delete Record: Are you sure?",
		        showCancelButton: true,
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		        $("ajax_loader").show();
	      		$.ajax({
	      			url:'<?= $ADMIN_HOST ?>/ajax_prd_pricing_matrix_delete.php',
	      			dataType:'JSON',
	      			data:$("#product_management").serialize(),
	      			type:'POST',
	      			success:function(res){
	      				$("ajax_loader").hide();
	      				if (res.status == 'success') {
			              setNotifySuccess(res.msg);
			              $("#pricingMatrixKey").val(res.pricingMatrixKey);
			              pricingMatrixIframeEnrollee();
			            } 
	      			}
	      		});
	      	});
      	});




      	$(document).off('change', '#csv_file');
      	$(document).on('change', '#csv_file',function(e){
	      var filename = $('#csv_file').val();
	      if (filename != '') {
	      	$(".select_field").removeClass('form-control');
	        $('.select_field').selectpicker('destroy');
	        $('.select_field').html('<option value=" "></option>');
	        $("#is_csv_uploaded").val('N');
	        
	        $(".after_export").hide();
	        $(".before_export").show();
	        $(".while_exportBTN").hide();
	        $(".while_export").hide();
	        $("#inline_content").hide();

	        $(".error").html('');
		      
	        $("#saveCSVAs").val('uploadCSV');
	        uploadCSV();
		    
	      }
	    });

	    $(document).off('change', '#csv_file_Enrollee');
      	$(document).on('change', '#csv_file_Enrollee',function(e){
	      var filename = $('#csv_file_Enrollee').val();
	      if (filename != '') {
	      	$(".select_field").removeClass('form-control');
	        $('.select_field').selectpicker('destroy');
	        $('.select_field').html('<option value=" "></option>');
	        $("#is_csv_uploaded_Enrollee").val('N');
	        
	        $(".after_export").hide();
	        $(".before_export").show();
	        $(".while_exportBTN").hide();
	        $(".while_export").hide();
	        $("#inline_content_Enrollee").hide();

	        $(".error").html('');
		      
	        $("#saveCSVAs_Enrollee").val('uploadCSV');
	        uploadCSVEnrollee();
		    
	      }
	    });

	  	$(document).on('click', '#ImportCSV', function(e) {
		    $(".error").html('');
		    $("#saveCSVAs").val('');

		    $ajaxUrl = 'ajax_prd_pricing_import.php';
        	$("#product_management").attr('action', $ajaxUrl);

	    	$(".before_export").hide();
	    	$(".while_export").show();
	    	$(".while_exportBTN").show();
	    	$progressTimerResponse = '';
	    	$(".loading-progress").progressTimer({
		        timeLimit: 100,
		        onFinish: function (e) {
		        	if($progressTimerResponse=="error"){
		        		$(".while_export").show();
		        	}else{
		            	$(".while_export").hide();
		        	}
		        	$(".while_exportBTN").hide();
		            $(".before_export").show();
		            $(".after_export").show();
		        }
		    });
        	$("#product_management").submit();
	  	});
	  	$(document).on('click', '#ImportCSV_Enrollee', function(e) {
		    $(".error").html('');
		    $("#saveCSVAs_Enrollee").val('');

		    $ajaxUrl = 'ajax_prd_pricing_import_Enrollee.php';
        	$("#product_management").attr('action', $ajaxUrl);

	    	$(".before_exportEnrollee").hide();
	    	$(".while_exportEnrollee").show();
	    	$(".while_exportBTNEnrollee").show();
	    	$progressTimerResponse = '';
	    	$(".loading-progress_Enrollee").progressTimer({
		        timeLimit: 100,
		        onFinish: function (e) {
		        	if($progressTimerResponse=="error"){
		        		$(".while_exportEnrollee").show();
		        	}else{
		            	$(".while_exportEnrollee").hide();
		        	}
		        	$(".while_exportBTNEnrollee").hide();
		            $(".before_exportEnrollee").show();
		            $(".after_exportEnrollee").show();
		        }
		    });
        	$("#product_management").submit();
	  	});

		$(document).off('click', '#CancelCSV');
		$(document).on('click', '#CancelCSV', function(e) {
			$("#csvError").show();
			$(".loading-progress").progressTimer('error');
			$("#CSVErrorList").html('<li>Import Canceled</li>');
			$xhr.abort();
			
		});

		$(document).off('click', '#CancelCSV_Enrollee');
		$(document).on('click', '#CancelCSV_Enrollee', function(e) {
			$("#csvError_Enrollee").show();
			$(".loading-progress_Enrollee").progressTimer('error');
			$("#CSVErrorList_Enrollee").html('<li>Import Canceled</li>');
			$xhr.abort();
			
		});


		$(document).on("change","input[name=childRateCalculateType]",function(){
			$val=$(this).val();
			$("#singleRateBaseChildDiv").hide();
			if($val=='Single Rate based on Eldest Child'){
				$("#singleRateBaseChildDiv").show();
			}
		});

		

		

		

		$(document).on("change","input[name=rider_for_enrollee]",function(){
			$val=$(this).val();
			$("#rider_for_enrollee_div").hide();
			if($val=='Y'){
				$("#rider_for_enrollee_div").show();
				$('#primary_rider_div').html('');
				$('#spouse_rider_div').html('');
				$('#child_rider_div').html('');
				addRider('all');
			}
		});

		
		

		

		$(document).off('click', '.add_product_fee');
      	$(document).on('click', '.add_product_fee', function(e) {
	        e.preventDefault();
	        $.colorbox({
	          href: $(this).attr('href'),
	          iframe: true,
	          width: '855px',
	          height: '600px'
	        })
	  	});

	  	
		

		$(document).off('change', '.fee_status');
		$(document).on('change', '.fee_status', function(e) {
			e.stopPropagation();
			$fee_id = $(this).attr('data-row-id');
			$old_status = $(this).attr('data-old_status');
			$new_status = $(this).val();
			$id= $(this).attr('id');
			
			swal({
		        text: "Change Fee Status: Are you sure?",
		        showCancelButton: true,
		        confirmButtonText: "Confirm",
	      	}).then(function() {
                  $.ajax({
                    url: 'ajax_change_product_fee_status.php',
                    data: {id:$fee_id,status:$new_status},
                    type: 'POST',
                    dataType:'JSON',
                    success: function(res) {
                      if (res.status == "Success") {
                        setNotifySuccess(res.message);
                        $("#"+$id).attr('data-old_status',$new_status);
                      }else{
                       document.getElementById('fee_status_' + $fee_id).value = $old_status;
                       return false;
                      }
                    }
                  });
                  return false;
          	} , function(dismiss){
                document.getElementById('fee_status_' + $fee_id).value = $old_status;
                return false;
          	});
		});
		$(document).off('click', '.productFeePrice');
		$(document).on('click', '.productFeePrice', function(e) {
			$fee_id = $(this).attr('data-row-id');
			
			$obj = jQuery.parseJSON($("#productFees").val());
			$feeObj=$obj[$fee_id];
			$feeJson = JSON.stringify($feeObj);

			
			$href="<?= $ADMIN_HOST ?>/view_product_fee.php?fee_id="+$fee_id+"&data="+$feeJson;
			$.colorbox({
				href: $href,
				iframe: true, 
				width: '700px',
				height: '480px',
			});
		});
		$(document).off('click', '.productFeeClone');
		$(document).on('click', '.productFeeClone', function(e) {
			$fee_id = $(this).attr('data-row-id');
			
			$obj = jQuery.parseJSON($("#productFees").val());
			$feeObj=$obj[$fee_id];
			$feeJson = JSON.stringify($feeObj);
			var groupEnrollmentPrd = $("#groupEnrollmentPrd").val();
			
			$href="<?= $ADMIN_HOST ?>/add_product_fee.php?fee_id="+$fee_id+"&is_clone=Y&data="+$feeJson+"&groupEnrollmentPrd="+groupEnrollmentPrd;
			$.colorbox({
				href: $href,
				iframe: true, 
				width: '900px',
				height: '580px',
			});
		});
		$(document).off('click', '.productFeeEdit');
		$(document).on('click', '.productFeeEdit', function(e) {
			$fee_id = $(this).attr('data-row-id');
			var groupEnrollmentPrd = $("#groupEnrollmentPrd").val();
			$obj = jQuery.parseJSON($("#productFees").val());
			$feeObj=$obj[$fee_id];
			$feeJson = JSON.stringify($feeObj);

			$href="<?= $ADMIN_HOST ?>/add_product_fee.php?fee_id="+$fee_id+"&data="+$feeJson+"&groupEnrollmentPrd="+groupEnrollmentPrd;
			$.colorbox({
				href: $href,
				iframe: true, 
				width: '900px',
				height: '580px',
			});
		});
		$(document).off('click', '.productFeeDelete');
		$(document).on('click', '.productFeeDelete', function(e) {
			$id=$(this).attr('data-row-id');
      		swal({
		        text: "Delete Product Fee: Are you sure?",
		        showCancelButton: true,
		        confirmButtonText: "Confirm",
	      	}).then(function() {
		        $("ajax_loader").show();
	      		$.ajax({
	      			url:'<?= $ADMIN_HOST ?>/ajax_delete_product_fee.php?id='+$id,
	      			dataType:'JSON',
	      			data:$("#product_management").serialize(),
	      			type:'POST',
	      			success:function(res){
	      				$("ajax_loader").hide();
	      				if (res.status == 'success') {
			              setNotifySuccess(res.msg);
			              $("#productFees").val(res.productFees);
			              productFeeIframe();
			            } 
	      			}
	      		});
	      	});
		});
		$(document).off('click', '.productFeeAdd');
		$(document).on('click', '.productFeeAdd', function(e) {
			var groupEnrollmentPrd = $("#groupEnrollmentPrd").val();
			$href="<?= $ADMIN_HOST ?>/add_product_fee.php?groupEnrollmentPrd="+groupEnrollmentPrd;
			$.colorbox({
				href: $href,
				iframe: true, 
				width: '900px',
				height: '580px',
			});
		});

		//********** Variable by enrollee Code start ***************** 
			$(document).on("click","#enrolleeTypeAll",function(){
				$(".enrolleeType").prop("checked",this.checked);
				$.uniform.update();
				$(".enrolleeType").each(function(){
					enrolleeType($(this));
				});
			});
			$(document).on("click",".enrolleeType",function(){
				enrolleeType($(this));
			});
		//********** Variable by enrollee Code end  ***************** 

		$(document).on('click', '.add_rider', function() {
			$type=$(this).attr('data-riderType');

			addRider($type);

		});
		$(document).on('click', '.remove_rider', function() {
			$id=$(this).attr('data-id');
			$type=$(this).attr('data-type');

			$("#rider_div_"+$id+"_"+$type).remove();

		});
	//********** prd_pricing Code end ***************** 
	

	//******** Functions Code Start *******************

		$('.summernoteClass').each(function(i, obj) {
			initCKEditor($(obj).attr('id'));
		});
		/*loadSummerNote = function($id){
			if($id==''){
				$id='.summernoteClass';
			}else{
				$id='#'+$id;
			}
			$($id).summernote({
		    	toolbar: $SUMMERNOTE_TOOLBAR,
  				disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
		      	focus: true, // set focus to editable area after initializing summernote
		      	height:350,
		        disableResizeEditor: true,
		      	callbacks: {
	                onChange: function() {
						$matchOn = $(this).attr('data-match-on');
						matchGlobal($matchOn);
                	},
                	 onImageUpload: function(image) {
				      editor = $(this);
				      uploadImageContent(image[0], editor);
				    },
				    onMediaDelete : function(target) {
				        deleteImage(target[0].src);
				        target.remove();
				    }
              	}
		    });
		}*/
		loadDepartmentlDiv = function(){
			$count=$("#product_management .department_div").length;
			$number=$count+1;
			
			$display_number = $number;
			$number="-"+$number;
			
			if($count % 2 == 0 && $count >= 2){
				html = '<div class="clearfix"></div>';
            	$('#department_div').append(html);
			}
			html = $('#department_dynamic_div').html();
			
			html=html.replace(/~number~/g, $number);
			html=html.replace(/~display_number~/g, $display_number);
            $('#department_div').append(html);

            if($display_number == 1){
            	$("#removeDepartment_"+$number).hide();
            }

            $name_val = $("#department_desc_"+$number).attr('name');
            // loadSummerNote("department_desc_"+$number);
            initCKEditor("department_desc_"+$number);
            arrangeDepartmentAddButton();
		}

		arrangeDepartmentAddButton = function(){
			$("#product_management .departmentAddButton").html('');

			$("#product_management .departmentAddButton").last().html($("#addButtonDiv").html());
		}

		loadInitialAvailableState = function($this){
			$state_id=$this.attr('data-state-id');
			
			if($("#available_no_sale_state_main_"+$state_id).length == 0){
				no_state_html = $('#available_no_sale_state_dynamic_div').html();
				no_state_html = no_state_html.replace(/~state_id~/g, $state_id);
				$('#available_no_sale_state_main_div').append(no_state_html);

				loadAvailableState($this,'initial');
			}else{
				$("#available_no_sale_state_main_"+$state_id).show();

				$effectiveDate=$(".availableEffectiveDate"+$state_id).last().val();
				$terminationDate=$(".availableTerminationDate"+$state_id).last().val();
				if(new Date($terminationDate) < new Date('<?= $stateUncheckDate ?>') || new Date($terminationDate) < new Date($effectiveDate)){
					loadAvailableState($this,'termed');
				}
			}
			$("#no_sale_state_title").show();
		}

		loadAvailableState = function($this,$type){
			$state_short_name=$this.attr('data-short-name');
			$state_full_name=$this.attr('data-name');
			$state_id=$this.attr('data-state-id');
			$uncheckDate='<?= $stateUncheckDate ?>';
			
			if ($state_id in statesArrInc){
				statesArrInc[$state_id]=statesArrInc[$state_id]+1;
			}else{
				statesArrInc[$state_id]=1;
			}
			$id = "-"+statesArrInc[$state_id];

			no_state_html = $('#available_no_sale_state_dynamic_additional_div').html();
			no_state_html = no_state_html.replace(/~state_short_name~/g, $state_short_name);
			no_state_html = no_state_html.replace(/~state_full_name~/g, $state_full_name);
			no_state_html = no_state_html.replace(/~state_id~/g, $state_id);
			no_state_html = no_state_html.replace(/~id~/g, $id);
			
			$('#available_no_sale_state_main_'+$state_id).append(no_state_html).show();
			
			$( "input[name='available_no_sale_state["+$state_id+"]["+$id+"][effective_date]']" ).val($uncheckDate);
			$(".available_no_sale_state").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
			
			

			if($type=="termed"){
				$("#state_name_"+$state_id+"_"+$id).html('');
			}
			fRefresh();
		}

		loadCustomQuestions = function(){
			$("#ajax_loader").show();
			$.ajax({
		      url:'<?= $ADMIN_HOST ?>/ajax_load_custom_question.php',
		      dataType:'JSON',
		      data:$("#product_management").serialize(),
		      type:'POST',
		      success: function(res) {
		        $('#ajax_loader').hide();
	          	$("#customQuestionData").html(res.html);
	          	$(".customQuestion").not('.js-switch').uniform();
		      }
		    });
			
		}

		// loadContent = function(){
		// 	$("#ajax_loader").show();
		// 	$product_id = $("#manage_product_id").val();
		// 	$.ajax({
		//       url:'<?= $ADMIN_HOST ?>/ajax_load_content.php',
		//       dataType:'JSON',
		//       data:{product_id : $product_id},
		//       type:'POST',
		//       success: function(res) {
		//         $('#ajax_loader').hide();
		//         if(res.status == 'success'){
		//         	$.each(res.data, function (index, val) {
		//         		$("#" + index).html(val);
		//         		console.log("val : " + val);
		//         		CKEDITOR.instances[index].setData(val);
		//         	});
		//         	for (instance in CKEDITOR.instances) {
		// 		        CKEDITOR.instances[instance].updateElement();
		// 		    }
		//         }
	 //          	// $("#customQuestionData").html(res.html);
	 //          	// $(".customQuestion").not('.js-switch').uniform();
		//       }
		//     });
			
		// }

		autoMemberTypeSetting = function($this){
			$val=$this.val();

			if($this.is(":checked")) {
				if ($('.autoTermedMemberType:checked').length == $('.autoTermedMemberType').length) {
			     	$("#autoTermedMemberTypeAll").prop('checked',true);
        			$.uniform.update();  
			    }
			    loadAutoTermedMemberSetting($val);
			}else{
			    if($("#autoTermedMemberTypeAll").is(":checked")){
        			$("#autoTermedMemberTypeAll").prop('checked',false);
        			$.uniform.update();
        		}
        		$("#autoTermedInnerMainDiv"+$val).hide();
				
			}
		}
		loadAutoTermedMemberSetting = function($title){

			if($("#autoTermedInnerMainDiv"+$title).length == 0){
				settingHtml = $('#autoTermedMemberSettingDynamicDiv').html();
				settingHtml = settingHtml.replace(/~title~/g, $title);
				$('#autoTermedMemberSettingMainDiv').append(settingHtml);
				loadAutoTermedMemberSettingOptions($title);
			}else{
				$("#autoTermedInnerMainDiv"+$title).show();
			}
		}

		loadAutoTermedMemberSettingOptions = function($title){
			$count=$("#autoTermedInnerDiv"+$title+" .autoTermMemberSettingInner").length;
			$number=$count+1;

			settingHtml = $('#autoTermedMemberSettingInnerDynamicDiv').html();
			settingHtml = settingHtml.replace(/~title~/g, $title);
			settingHtml = settingHtml.replace(/~number~/g, '-'+$number);
			$('#autoTermedInnerDiv'+$title).append(settingHtml);

			$(".add_control_"+$title+"_-"+$number).addClass('form-control');
			$(".add_control_"+$title+"_-"+$number).selectpicker({ 
		        container: 'body', 
		        style:'btn-select',
		        noneSelectedText: '',
		        dropupAuto:false,
	      	});
		}

		isAgeRestriction = function(){
			$primary =$("input[name='is_primary_age_restrictions']:checked").val();
			$spouse =$("input[name='is_spouse_age_restrictions']:checked").val();
			$child =$("input[name='is_children_age_restrictions']:checked").val();
			
			if($primary=='Y' || $spouse=='Y' || $child=='Y'){
				$("#ageRestrictionDiv").show();
			}

			if($primary=='N' && $spouse=='N' && $child=='N'){
				$("#ageRestrictionDiv").hide();
			}

		}

		addPricingFixed = function($id,$type){
			if($("#product_management .priceTier_"+$id).length == 0 || $type=='add'){
				$count=$("#product_management .inner_pricing_div").length;
				$number=$count+1;
				$newEffectiveDate = '<?= $productActiveEffectiveDate ?>';
				
				if($number>1){
					$lastTerminationDate = $("#product_management .pricingTerminationDate").last().val();
					$newEffectiveDate = add1DayToDate($lastTerminationDate);
				}


				settingHtml = $('#pricing_dynamic_div').html();
				settingHtml = settingHtml.replace(/~number~/g,"-"+$number);
				$('#benefit_tier_pricing_main_div').append(settingHtml);
				$("#product_management .priceTier_"+$id).show();

				$(".pricingDates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
				$("#pricing_effective_date_-"+$number).val($newEffectiveDate);

				if($type == 'add'){
					$("#pricing_effective_date_-"+$number).attr('readonly',true);
				}
				

				formatPricing();
				


			}else{
				$("#product_management .priceTier_"+$id).show();
			}			
		}
		removePricingFixed = function($id){
			$("#product_management .priceTier_"+$id).hide();
		}

		addPricingFixedDiv = function($terminationDate){
			$type='add';
			$(".coverage_options:checked").each(function(){
				$val=$(this).val();
				addPricingFixed($val,$type);
				if($type=='add'){
					$type='';
				}
			});
			addPricingFixedSettingDiv();
		}

		addPricingFixedSettingDiv = function(){
			$number=$("#product_management .inner_pricing_div").length;
			
			if($number > 1){
				$("#product_management .pricing_setting_div").html('');
				$number = $("#product_management .pricing_setting_div").last().attr('data-id');
				
				settingHtml = $('#pricing_setting_dynamic_div').html();
				settingHtml = settingHtml.replace(/~number~/g,$number);
				$("#product_management .pricing_setting_div").last().html(settingHtml);
				$(".newPricingOnRenewals").not('.js-switch').uniform();
			}
		}

		add1DayToDate = function($date){
			date = new Date($date);
			date.setDate(date.getDate() + 1);
			return ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + 
						((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + 
						date.getFullYear();
		}

		spouseControl = function(){
			if($('.HasSpouse').is(":checked")){
				$('.spouseControl').prop('disabled',false);
			}else{
				$('.spouseControl').prop('checked',false);
				$('.spouseControl').prop('disabled',true);
				$(".spouseControlPriceDiv").hide();
			}
			$.uniform.update();
		}

		addPricingOption = function($val){
			$("#matrixPlanType option[value=" + $val + "]").prop('disabled',false);
			$("#matrixPlanType").selectpicker('refresh');
		}
		removePricingOption = function($val){
			$("#matrixPlanType").val('');
			$("#matrixPlanType option[value=" + $val + "]").prop('disabled',true);
			$("#matrixPlanType").selectpicker('refresh');
		}

		addEnrolleeOption = function($val){
			$("#enrolleeMatrix option[value=" + $val + "]").prop('disabled',false);
			$("#enrolleeMatrix").selectpicker('refresh');
		}
		removeEnrolleeOption = function($val){
			$("#enrolleeMatrix").val('');
			$("#enrolleeMatrix option[value=" + $val + "]").prop('disabled',true);
			$("input[name='price_control_enrollee["+$val+"][]']").each(function(){
				$(this).prop('checked',false);
				$.uniform.update();
				$(this).trigger('change');
			});
			$("#enrolleeMatrix").selectpicker('refresh');
		}


		addPricingMatrix = function($key,$type){
			$pricingTitle = '';
			if($key!=0){
				$number = $key;
				$pricingTitleCount = $("#product_management .inner_pricing_matrix_div").length;
				
				if($pricingTitleCount > 0){
					$pricingTitle = $pricingTitleCount + 1;
				}
			}else{
				$pricingMatrixCount=$pricingMatrixCount + 1;
				$number = "-"+$pricingMatrixCount;
				$pricingTitleCount = $("#product_management .inner_pricing_matrix_div").length;
				
				if($pricingTitleCount > 0){
					$pricingTitle = $pricingTitleCount + 1;
				}
				
			}
			$newEffectiveDate = '<?= $productActiveEffectiveDate ?>';
			$lastTerminationDate = $("#product_management .pricingMatrixTerminationDate").last().val();
			
			if($lastTerminationDate!== undefined && $lastTerminationDate!=''){
				$newEffectiveDate = add1DayToDate($lastTerminationDate);
			}
			$matrixGroup = $("#matrixGroup").val();
			if($matrixGroup == ''){
				$("#matrixGroup").val($number);
				$matrixGroup = $("#matrixGroup").val();
			}
			settingHtml = $('#pricing_matrix_dynamic_div').html();
			settingHtml = settingHtml.replace(/~number~/g,$number);
			settingHtml = settingHtml.replace(/~matrixGroup~/g,$matrixGroup);
			settingHtml = settingHtml.replace(/~pricingTitle~/g,$pricingTitle);
			$('#pricing_matrix_pricing_main_div').append(settingHtml);

			$(".pricingMatrixDates").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
			$("#pricing_matrix_effective_date_"+$number).val($newEffectiveDate);

			if($type=='add'){
				$("#pricing_matrix_effective_date_"+$number).attr('readonly',true);
			}

			
			formatPricing();

			$("#product_management .pricing_matrix_setting_div").hide();
			$("#product_management .newPricingMatrixOnRenewals").prop('checked', false);
			
			settingHtml = $('#pricing_matrix_setting_dynamic_div').html();
			settingHtml = settingHtml.replace(/~number~/g,$number);
			$("#pricing_matrix_setting_div_"+$number).html(settingHtml).show();
			$("#product_management .newPricingMatrixOnRenewals").not('.js-switch').uniform();
			
			return $number;
		}

		addPricingMatrixEnrollee = function($key,$type){
			$pricingTitle = '';
			
			if($key!=0){
				$number = $key;
				$pricingTitleCount = $("#product_management .inner_pricing_matrix_div_Enrollee").length;
				
				if($pricingTitleCount > 0){
					$pricingTitle = $pricingTitleCount + 1;
				}
				
			}else{
				$pricingMatrixCount=$pricingMatrixCount + 1;
				$number = "-"+$pricingMatrixCount;
				$pricingTitleCount = $("#product_management .inner_pricing_matrix_div_Enrollee").length;
				
				if($pricingTitleCount > 0){
					$pricingTitle = $pricingTitleCount + 1;
				}
				
			}
			
			$newEffectiveDate = '<?= $productActiveEffectiveDate ?>';
			$lastTerminationDate = $("#product_management .pricingMatrixTerminationDate_Enrollee").last().val();
			if($lastTerminationDate!== undefined && $lastTerminationDate!=''){
				$newEffectiveDate = add1DayToDate($lastTerminationDate);
			}
			$matrixGroupEnrollee = $("#matrixGroupEnrollee").val();

			if($matrixGroupEnrollee == ''){
				$("#matrixGroupEnrollee").val($number);
				$matrixGroupEnrollee = $("#matrixGroupEnrollee").val();
			}
			settingHtml = $('#pricing_matrix_dynamic_div_Enrollee').html();
			settingHtml = settingHtml.replace(/~number~/g,$number);
			settingHtml = settingHtml.replace(/~pricingTitle~/g,$pricingTitle);
			settingHtml = settingHtml.replace(/~matrixGroup_Enrollee~/g,$matrixGroupEnrollee);
			$('#pricing_matrix_pricing_main_div_Enrollee').append(settingHtml);

			$(".pricingMatrixDates_Enrollee").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
			$("#pricing_matrix_effective_date_Enrollee_"+$number).val($newEffectiveDate);

			if($type=='add'){
				$("#pricing_matrix_effective_date_Enrollee_"+$number).attr('readonly',true);
			}

			
			formatPricing();

			$("#product_management .pricing_matrix_setting_div_Enrollee").hide();
			$("#product_management .newPricingMatrixOnRenewals_Enrollee").prop('checked', false);
			
			settingHtml = $('#pricing_matrix_setting_dynamic_div_Enrollee').html();
			settingHtml = settingHtml.replace(/~number~/g,$number);
			$("#pricing_matrix_setting_div_Enrollee_"+$number).html(settingHtml).show();
			$("#product_management .newPricingMatrixOnRenewals_Enrollee").not('.js-switch').uniform();
			
			return $number;
		}
		
		formatPricing = function(){
			$("#product_management .formatPricing").priceFormat({
			    prefix: '',
	            suffix: '',
	            centsSeparator: '.',
	            thousandsSeparator: ',',
	            limit: false,
	            centsLimit: 2,
			});

			$("#product_management #percentage_of_salary").priceFormat({
			    prefix: '',
	            suffix: '',
	            centsSeparator: '.',
	            thousandsSeparator: ',',
	            limit: 5,
	            centsLimit: 2,
			});
		}

		pricingMatrixIframe = function(){
			$('#ajax_loader').show();
		    $('#pricingMatrixIframeDiv').hide();
		    $.ajax({
		      url: '<?= $ADMIN_HOST ?>/prd_pricing_matrix_list.php',
		      type: 'POST',
		      data: $("#product_management").serialize(),
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#pricingMatrixIframeDiv').html(res).show();
		        $('[data-toggle="tooltip"]').tooltip();
		        $('.deletePricingMatrixBox').uniform();
		      }
		    });
		}
		pricingMatrixIframeEnrollee = function(){
			$('#ajax_loader').show();
		    $('#pricingMatrixIframeDivEnrollee').hide();
		    $.ajax({
		      url: '<?= $ADMIN_HOST ?>/prd_pricing_matrix_list_enrollee.php',
		      type: 'POST',
		      data: $("#product_management").serialize(),
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#pricingMatrixIframeDivEnrollee').html(res).show();
		        $('[data-toggle="tooltip"]').tooltip();
		        $('.deleteEnroleePricingMatrixBox').uniform();
		      }
		    });
		}
		
		clearPricing = function(){
			$(".clearPricing").val('');
			$('.clearPricing').selectpicker('refresh');
		}

		uploadCSV = function(){
	    	$ajaxUrl = 'ajax_prd_pricing_import.php';
        	$("#product_management").attr('action', $ajaxUrl);
        	$("#ajax_loader").show();
        	$("#product_management").submit();
	    }

	    uploadCSVEnrollee = function(){
	    	$ajaxUrl = 'ajax_prd_pricing_import_Enrollee.php';
        	$("#product_management").attr('action', $ajaxUrl);
        	$("#ajax_loader").show();
        	$("#product_management").submit();
	    }

	    productFeeIframe = function(){
			$('#ajax_loader').show();
		    $('#productFeeIframeDiv').hide();
		    $.ajax({
		      url: '<?= $ADMIN_HOST ?>/prd_product_fee_list.php',
		      type: 'POST',
		      data: $("#product_management").serialize(),
		      success: function(res) {
		        $('#ajax_loader').hide();
		        $('#productFeeIframeDiv').html(res).show();
		        common_select();
		        $('[data-toggle="tooltip"]').tooltip();
		      }
		    });
		}

		notifySaveExit = function(){
	  		$.colorbox({
		      href: '<?= $ADMIN_HOST ?>/notify_save_exit.php?id='+$("#product_id").val(),
		      iframe: true,
		      width: '1024px',
		      height: '700px',
		      onClosed:function(){
		      	window.onbeforeunload = null;
				window.location.href="<?= $ADMIN_HOST ?>/manage_product.php";
		      }

		    });
	  	}

	  	matchGlobal = function($matchOn){
	  		$checkBoxId="matchGlobal_"+$matchOn;
	  		
			if($("#"+$checkBoxId).is(":checked")){
				$("#"+$checkBoxId).prop("checked",false);
				$.uniform.update();
			}
	  	}

	  	enrolleeType = function($this){
			$val=$this.val();

			if($this.is(":checked")) {
				if ($('.enrolleeType:checked').length == $('.enrolleeType').length) {
			     	$("#enrolleeTypeAll").prop('checked',true);
        			$.uniform.update();  
			    }
			    if($("#enrolleeTypeInnerMainDiv"+$val).length > 0){
			    	$("#enrolleeTypeInnerMainDiv"+$val).show();
			    }
			    if($val=="Child"){
			    	$("#child_added_div").show();
			    }
			   
			    addEnrolleeOption($val);
			}else{
			    if($("#enrolleeTypeAll").is(":checked")){
        			$("#enrolleeTypeAll").prop('checked',false);
        			$.uniform.update();
        		}
        		$("#enrolleeTypeInnerMainDiv"+$val).hide();

        		if($val=="Child"){
			    	$("#child_added_div").hide();
			    }
				removeEnrolleeOption($val);
			}
		}

		

		addRider = function($type){			
			if($type=="Primary" || $type=="all"){
				$count=$("#product_management .rider_div").length;
				$dispNumber=$count+1;
				$number="-"+$dispNumber;
				primaryHtml = $('#rider_dynamic_div').html();
				primaryHtml=primaryHtml.replace(/~number~/g, $number);
				primaryHtml=primaryHtml.replace(/~rider_type~/g, "Primary");
            	$('#primary_rider_div').append(primaryHtml);

            	$("#riderProduct_"+$number+"_Primary").addClass("form-control");
            	$("#riderProduct_"+$number+"_Primary").selectpicker({
		        	container: 'body', 
			        style:'btn-select',
			        noneSelectedText: '',
			        dropupAuto:false,
		        });	
		        if($dispNumber > 1){
		        	$("#remove_rider_div_"+$number+"_Primary").show();
		        }
		        $("input[name='riderRate["+$number+"][Primary]']").not('.js-switch').uniform();
			}
			if($type == "Spouse" || $type=="all"){
				$count=$("#product_management .rider_div").length;
				$dispNumber=$count+1;
				$number="-"+$dispNumber;
				spouseHtml = $('#rider_dynamic_div').html();
				spouseHtml=spouseHtml.replace(/~number~/g, $number);
				spouseHtml=spouseHtml.replace(/~rider_type~/g, "Spouse");
            	$('#spouse_rider_div').append(spouseHtml);
            	$("#riderProduct_"+$number+"_Spouse").addClass("form-control");
            	$("#riderProduct_"+$number+"_Spouse").selectpicker({
		        	container: 'body', 
			        style:'btn-select',
			        noneSelectedText: '',
			        dropupAuto:false,
		        });
		        if($dispNumber > 2){
		        	$("#remove_rider_div_"+$number+"_Spouse").show();
		        }
		        $("input[name='riderRate["+$number+"][Spouse]']").not('.js-switch').uniform();
			} 
			if($type == "Child" || $type=="all"){
				$count=$("#product_management .rider_div").length;
				$dispNumber=$count+1;
				$number="-"+$dispNumber;
				childHtml = $('#rider_dynamic_div').html();
				childHtml=childHtml.replace(/~number~/g, $number);
				childHtml=childHtml.replace(/~rider_type~/g, "Child");
            	$('#child_rider_div').append(childHtml);
            	$("#riderProduct_"+$number+"_Child").addClass("form-control");
            	$("#riderProduct_"+$number+"_Child").selectpicker({
		        	container: 'body', 
			        style:'btn-select',
			        noneSelectedText: '',
			        dropupAuto:false,
		        });
		        if($dispNumber > 3){
		        	$("#remove_rider_div_"+$number+"_Child").show();
		        }
		        $("input[name='riderRate["+$number+"][Child]']").not('.js-switch').uniform();
		        
			}
			
			fRefresh();

		}
		
	//******** Functions Code end *******************	
	

	//******** Match Global Code Start *******************	
		$(document).on("change",".matchGlobal",function(e){
			e.stopPropagation();
			$matchOn = $(this).attr('data-match-on');
			if(!$changeFromTags){
				matchGlobal($matchOn);
			}else{
				$changeFromTags = false;
			}
		});
		$(document).on("click",".matchGlobalBtn",function(e){
			e.stopPropagation();
			$matchOn = $(this).attr('data-match-on');
			matchGlobal($matchOn);
		});
	//******** Match Global Code end   *******************	

	$(document).on("click",".datePickerIcon",function(){
	    $id=$(this).attr('data-applyon');
	    $("#"+$id).datepicker('show');
	    $("#"+$id).trigger("blur");
	});

	pricingDataDisabled_Enrollee = function($status,$id,$pricingReadonly){
		$("#enrolleeMatrix").prop('disabled',$status);
		$("#age_from_Enrollee").prop('disabled',$status);
		$("#age_to_Enrollee").prop('disabled',$status);
		$("#state_Enrollee").prop('disabled',$status);
		$("#zip_Enrollee").prop('disabled',$status);
		$("#gender_Enrollee").prop('disabled',$status);
		$("#smoking_status_Enrollee").prop('disabled',$status);
		$("#tobacco_status_Enrollee").prop('disabled',$status);
		$("#height_by_Enrollee").prop('disabled',$status);
		$("#height_feet_Enrollee").prop('disabled',$status);
		$("#height_inch_Enrollee").prop('disabled',$status);
		$("#weight_by_Enrollee").prop('disabled',$status);
		$("#weight_Enrollee").prop('disabled',$status);
		$("#no_of_children_by_Enrollee").prop('disabled',$status);
		$("#no_of_children_Enrollee").prop('disabled',$status);
		$("#has_spouse_Enrollee").prop('disabled',$status);
		$("#spouse_age_from_Enrollee").prop('disabled',$status);
		$("#spouse_age_to_Enrollee").prop('disabled',$status);
		$("#spouse_gender_Enrollee").prop('disabled',$status);
		$("#spouse_smoking_status_Enrollee").prop('disabled',$status);
		$("#spouse_tobacco_status_Enrollee").prop('disabled',$status);
		$("#spouse_height_feet_Enrollee").prop('disabled',$status);
		$("#spouse_height_inch_Enrollee").prop('disabled',$status);
		$("#spouse_weight_Enrollee").prop('disabled',$status);
		$("#spouse_weight_type_Enrollee").prop('disabled',$status);
		$("#benefit_amount_Enrollee").prop('disabled',$status);
		$("#in_patient_benefit_Enrollee").prop('disabled',$status);
		$("#out_patient_benefit_Enrollee").prop('disabled',$status);
		$("#monthly_income_Enrollee").prop('disabled',$status);
		// $("#benefit_percentage_Enrollee").prop('disabled',$status);

		if($id!=0){
			$("#pricing_matrix_price_Enrollee_"+$id+"_Retail").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_price_Enrollee_"+$id+"_NonCommissionable").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_price_Enrollee_"+$id+"_Commissionable").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_effective_date_Enrollee_"+$id).attr('readonly',$pricingReadonly);

		}
	}

	pricingDataDisabled = function($status,$id,$pricingReadonly){
		$("#matrixPlanType").prop('disabled',$status);
		$("#age_from").prop('disabled',$status);
		$("#age_to").prop('disabled',$status);
		$("#state").prop('disabled',$status);
		$("#zip").prop('disabled',$status);
		$("#gender").prop('disabled',$status);
		$("#smoking_status").prop('disabled',$status);
		$("#tobacco_status").prop('disabled',$status);
		$("#height_by").prop('disabled',$status);
		$("#height_feet").prop('disabled',$status);
		$("#height_inch").prop('disabled',$status);
		$("#height_feet_to").prop('disabled',$status);
		$("#height_inch_to").prop('disabled',$status);
		$("#weight_by").prop('disabled',$status);
		$("#weight").prop('disabled',$status);
		$("#weight_to").prop('disabled',$status);
		$("#no_of_children_by").prop('disabled',$status);
		$("#no_of_children").prop('disabled',$status);
		$("#no_of_children_to").prop('disabled',$status);
		$("#has_spouse").prop('disabled',$status);
		$("#spouse_age_from").prop('disabled',$status);
		$("#spouse_age_to").prop('disabled',$status);
		$("#spouse_gender").prop('disabled',$status);
		$("#spouse_smoking_status").prop('disabled',$status);
		$("#spouse_tobacco_status").prop('disabled',$status);
		$("#spouse_height_feet").prop('disabled',$status);
		$("#spouse_height_inch").prop('disabled',$status);
		$("#spouse_weight").prop('disabled',$status);
		$("#spouse_weight_type").prop('disabled',$status);
		$("#benefit_amount").prop('disabled',$status);
		$("#in_patient_benefit").prop('disabled',$status);
		$("#out_patient_benefit").prop('disabled',$status);
		$("#monthly_income").prop('disabled',$status);
		// $("#benefit_percentage").prop('disabled',$status);

		if($id!=0){
			$("#pricing_matrix_price_"+$id+"_Retail").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_price_"+$id+"_NonCommissionable").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_price_"+$id+"_Commissionable").attr('readonly',$pricingReadonly);
			$("#pricing_matrix_effective_date_"+$id).attr('readonly',$pricingReadonly);
		}
	}
	function setPercentage ($this){
		if ($this.val() > 100){
			$this.val('100');
		}
	}

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
	function change_prd_fee_label()
	{
		var product_type = $("#product_type").val();
		if(product_type == "Group Enrollment") {
			var prd_fee_label = "Admin Fee";
		} else {
			var prd_fee_label = "Product Fee";
		}
		$(".prd_fee_label").text(prd_fee_label);
	}

</script>
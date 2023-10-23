<script type="text/javascript">
    var display = false;
    var $answerCount=1;
    var $answerTabIds=[];
    $groupIds = '<?= $groupIds ?>';
    var currentMultipleSelect = '';
    data = {
        groupId:$groupIds,
    };

    $(document).off('change','#paginateLinkDiv .api_pagination_select');
    $(document).on('change','#paginateLinkDiv .api_pagination_select',function(e){
        e.stopPropagation();
        data.page = $(this).find('option:selected').attr('data-page');
        data.from = 'select';
        create_bundle_ajax_submit(data);
    });

    $(document).off('click', '#paginateLinkDiv ul.pagination li a');
    $(document).on('click', '#paginateLinkDiv ul.pagination li a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(data.from == '' || data.from == undefined){
            data.page = $(this).attr('data-page');
            create_bundle_ajax_submit(data);
        }
        
    });
$(document).ready(function(){

$pageEdit = false;

    if($groupIds != ''){
        create_bundle_ajax_submit(data);
    }

    $('.date_picker').datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
    $('#bundle_recommendation .display_bundle').multipleSelect({});
    // $("#selectedGroup").addClass('form-control');
    // $(".bundle_wrap").show();
    $(".moveable").sortable({
            axis: 'y',
            //helper: fixHelper,
            cursor: 'move',
            items: '.category_block',
            placeholder: '.category_block',
            handle: '.moveable_controller',
            update: function(event,ui){
                $assignBndlId=$(this).attr('data-id');
                updateBundleOrder($assignBndlId);
            }
    });
    // $(".comparison_wrap").show();
    // $(".comparison_toggle").show();
});
// get group product by id

$(document).off('change', '#selectedGroup');
$(document).on("change", "#selectedGroup", function(e) {
    e.stopPropagation();
     var group_id = $(this).val();
    getGroupProduct(group_id);
});
getGroupProduct = (group_id) => {
    $("#ajax_loader").show();
    $.ajax({
        url: '<?=$ADMIN_HOST?>' + '/add_bundle_recommendation.php',
        data:{groupId:group_id,api_key:'getGroupProduct'},
        dataType: 'json',
        type: 'POST',
        success: function(res) {
            $("#groupProducts").html(res.option_html);
            $("#bundleTableBody .bundle_product").html(res.option_html);
            $("#bundleTableBody .bundle_product").multipleSelect('refresh');
            $("#ajax_loader").hide();
        }
    });

}
// Add dynamic bundle
addBundle = () => {
    $(".saveBundleButton").show();
    index = parseInt($(".bundlePortion").length);
    $display_counter = parseInt($('#bundle_table_counter').val());
    $number=index+1;
    if($display_counter > index){
        $number = $display_counter + 1;
    }
    $negativeNumber = ($number * -1);
    html = $("#bundle_template").html();
    html=html.replace(/~i~/g, $negativeNumber);
    $('#bundleTableBody').append(html);
    $("#bundle_product_"+$negativeNumber).addClass("se_multiple_select");
    $("#bundleID").val($negativeNumber);

    $("#bundle_table_counter").val($number);

    $.each($('#bundleTableBody .bundlePortion'),function(i){
        $id = $(this).attr('data-id');
        var totalBundle = parseInt($id) > 0 ? (i+1) : parseInt($("#totalBundleForGroup").val()) + 1;
        $("#dynamicId_"+$id).text(totalBundle);
    });

    $('#cancelCreateBundleButton').attr('data-id',$negativeNumber);
    $options = $("#groupProducts").html();
    $("#inputBundleTr"+$negativeNumber).show();
    $("#bundle_product_"+$negativeNumber).html($options);
    currentMultipleSelect = $("#bundle_product_"+$negativeNumber).multipleSelect({selectAll:false});
    var date = new Date();
    date.setDate(date.getDate());
    $("#bundle_effective_date_"+$negativeNumber).datepicker({
        startDate: date,
        changeDay: true,
        changeMonth: true,
        changeYear: true,
    });
    $("#bundle_termination_date_"+$negativeNumber).datepicker({
        startDate: date,
        changeDay: true,
        changeMonth: true,
        changeYear: true,
    });
    $("#addBundleButton").hide();
    $(".tooltip"+$negativeNumber).tooltip();

    $('.table-responsive').scroll(function(e){
        e.stopPropagation();
        $('select#bundle_product_'+$negativeNumber).multipleSelect('close');
    });
    
    disableFirstStepButton(false,'disableNext');
};

$(document).off('click', '#removeId');
$(document).on("click", "#removeId", function(e) {
    e.stopPropagation();
     var group_id = $(this).val();
    getGroupProduct(group_id);
});

//edit bundle
$(document).off('click', '.edit_Bundle');
$(document).on("click", ".edit_Bundle", function(e) {
    e.stopPropagation();
    if(currentMultipleSelect !== ''){
        currentMultipleSelect.multipleSelect('close');
        currentMultipleSelect = '';
    }
    editID = $(this).attr('data-id');
    $('.table-responsive').scroll(function(e){
        e.stopPropagation();
        $('select#bundle_product_'+editID).multipleSelect('close');
    });
    editBundle(editID);
});

//remove bundle
$(document).off('click', '.remove_Bundle');
$(document).on("click", ".remove_Bundle", function(e) {
    e.stopPropagation();
    selected_value = $('select.selectedGroup').children("option:selected").val();
    removeID=$(this).attr('data-id');
    $('.error').hide();
    $("#ajax_loader").show();
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        method :'POST',
        data :{bundle_id:removeID,group :selected_value, api_key :'checkAssignBundle'},
        dataType : 'JSON',
        success :function(res){
           var data = res.data;
           if(data['QuestionAssign'] == 0){
                var top_recommended = $('input[name="top_recommended"]:checked').val();
                if(data['BundleRowInformation']){
                    $('#error_checkBundle_'+removeID).html('This bundle is selected as a top recommanded bundle.please remove it first.');
                    $('#error_checkBundle_'+removeID).show();
                    setTimeout(function() { $('#error_checkBundle_'+removeID).fadeOut(1000); }, 5000);
                    $("#ajax_loader").hide();
                    if(top_recommended != data['BundleRowInformation']['top_recommanded']){
                        removeBundle(removeID,selected_value,top_recommended);
                        $('#error_checkBundle_'+removeID).hide();
                    }
                }else if(data['BundleRowDetails']){
                    removeBundle(removeID,selected_value,removeID);
                    $("#ajax_loader").hide();
                }
           }else{
            $('#error_checkBundle_'+removeID).html('This bundle is assign to questions. please remove it first.');
            $('#error_checkBundle_'+removeID).show();
            setTimeout(function() { $('#error_checkBundle_'+removeID).fadeOut(1000); }, 6000);
            $("#ajax_loader").hide();
            
            }
        }
    });
    
});


removeBundle = (removeID,selected_value,top_recommended) => {
    swal({
           text: 'Delete Bundle: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
        }).then(function() {
             $("#ajax_loader").show();
            $.ajax({
                url: '<?=$HOST?>' + '/ajax_api_call.php',
                data: {id: removeID,assign_group: selected_value,TopRecommanded : top_recommended, api_key:'deleteBundle'},
                method: 'POST',
                dataType: 'JSON',
                success : function(res){
                    // console.log(res);
                    if (res.status == 'Success') {
                        $("#inputBundleTr"+removeID).remove();
                        $("#textBundleTr"+removeID).remove();
                        $('th[data-id='+removeID+']').remove();
                        $('td[data-column-id='+removeID+']').remove();
                         alloweDisabled(res.data , selected_value);
                        $.each($('#bundleTableBody .bundlePortion'),function(i){
                            $id = $(this).attr('data-id');
                            $("#dynamicId_"+$id).text(i+1);
                        });
                    }
                    // if(res.data != 5 && res.data < 5){
                    //    $("#addBundleButton").show();
                    // }else{
                    //     $("#addBundleButton").hide();
                    //  }
                     $("#ajax_loader").hide();
                      location.reload();
                }
            });

            // if(res.data!= 5 && res.data< 5){
                $("#addBundleButton").show();
            // }else{
            //     $("#addBundleButton").hide();
            // }
        });
    disableFirstStepButton(true,'enableNext');
}
//Edit Bundle
editBundle = (editId) => {
    var bundleID = $("#bundleID").val();
    if(bundleID < 0){
        $("#textBundleTr"+bundleID).remove();
        $("#inputBundleTr"+bundleID).remove();
    }
    $("#editBundleID").val(editId);
    $("#bundleID").val(editId);
    $("#bundleTableBody .inputBundleTr").hide();    
    $("#bundleTableBody .textBundleTr").show();    
    $("#textBundleTr"+editId).hide();
    $("#inputBundleTr"+editId).show();
    $("#removeId"+editId).hide();
    $(".saveBundleButton").show();
    $("#addBundleButton").hide();
    $("#bundle_product_"+editId).addClass("se_multiple_select");
    fRefresh();
    currentMultipleSelect = $("#bundle_product_"+editId).multipleSelect('refresh');
    disableFirstStepButton(false,'disableNext');
}

//Save Bundle
saveBundle = ($step) => {
    $("#ajax_loader").show();
    var bundleID = $("#bundleID").val();
    var editBundleID = $("#editBundleID").val();
    var errorClassAdd  = editBundleID !=0 ? editBundleID : bundleID ;
    var formData = $("#bundle_recommendation").serialize();
    formData = formData+"&api_key=saveBundleRecommendation&step="+$step;
    $('.error').html('');
    $(".error.error_bundle_"+errorClassAdd).parent().removeClass('bundle_error_space');
    $("#dynamicId_"+errorClassAdd).removeClass('bundle_error_space');
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        data:formData,
        dataType: 'json',
        type: 'POST',
        success: function(res) {
            $("#ajax_loader").hide();
            var is_error = false;
            if(res.status == 'Success'){
                if($step == '1'){
                    var values =  res.data;
                    $("#totalBundleForGroup").val(values['bundle_count']);
                    if(editBundleID !=0 ){
                        $("#editBundleID").val('0');
                    }
                    //Replace New id in inputBundleTr
                    $("#inputBundleTr"+bundleID).attr("id","inputBundleTr"+values['id']); //replace row id to record id
                    $('#bundle_label_'+bundleID).attr("id","bundle_label_"+values['id']).attr('name','bundle_label['+values['id']+']');
                    $('#error_bundle_label_'+bundleID).attr("id","error_bundle_label_"+values['id']);

                    $("#bundle_product_"+bundleID).attr("id","bundle_product_"+values['id']).attr('name','bundle_product['+values['id']+'][]').attr('data-id',values['id']);
                    $("#bundle_product_"+values['id']).multipleSelect('refresh');
                    $('#error_bundle_product_'+bundleID).attr("id","error_bundle_product_"+values['id']);
                    $('#error_checkBundle_'+bundleID).attr("id","error_checkBundle_"+values['id']);
                    $('#error_bundle_'+bundleID).attr("id","error_bundle_"+values['id']);

                    $("#bundle_effective_date_"+bundleID).attr("id","bundle_effective_date_"+values['id']).attr('name','bundle_effective_date['+values['id']+']').attr('data-change_text','bundle_effective_date_text'+values['id']+'');
                    $('#error_bundle_effective_date_'+bundleID).attr("id","error_bundle_effective_date_"+values['id']);

                    $("#bundle_termination_date_"+bundleID).attr("id","bundle_termination_date_"+values['id']).attr('name','bundle_termination_date['+values['id']+']').attr('data-change_text','bundle_termination_date_text'+values['id']+'');
                    $('#error_bundle_termination_date_'+bundleID).attr("id","error_bundle_termination_date_"+values['id']);

                    $("#recommendation_reason_"+bundleID).attr("id","recommendation_reason_"+values['id']).attr('name','recommendation_reason['+values['id']+']');
                    $('#error_recommendation_reason_'+bundleID).attr("id","error_recommendation_reason_"+values['id']);
                    $('#editBundleLabelTr'+bundleID).attr('id','editBundleLabelTr'+values['id']).attr('data-id',values['id'])
                    $("#inputBundleTr"+values['id']).hide();

                    //put input text in td text
                    var product_ids = values['product_ids'].split(",").length;  //count selected product
                    var effective_date = moment(values['effective_date']).format("MM/DD/YYYY"); //change effective date formate
                    var termination_date = values['termination_date'] ? moment(values['termination_date']).format("MM/DD/YYYY") : ' ';
                    $("#bundle_label_text"+bundleID).text(values['bundle_label']);  //put data on input field
                    $("#bundle_product_text"+bundleID).text(product_ids);
                    $("#bundle_recommendation_reason_text"+bundleID).text(values['recommendation_reason']);
                    $("#bundle_effective_date_text"+bundleID).text(effective_date);
                    $("#bundle_termination_date_text"+bundleID).text(termination_date);
                    /* */

                    //Replace New id in textBundleTr
                    $("#textBundleTr"+bundleID).attr("id","textBundleTr"+values['id']);
                    $("#bundle_label_text"+bundleID).attr("id","bundle_label_text"+values['id']);
                    $("#bundle_product_text"+bundleID).attr("id","bundle_product_text"+values['id']);
                    $("#bundle_effective_date_text"+bundleID).attr("id","bundle_effective_date_text"+values['id']);
                    $("#bundle_termination_date_text"+bundleID).attr("id","bundle_termination_date_text"+values['id']);
                    $("#bundle_recommendation_reason_text"+bundleID).attr("id","bundle_recommendation_reason_text"+values['id']);
                    $("#edit_Bundle_"+bundleID).attr("id","edit_Bundle_"+values['id']).attr('data-id',values['id']);
                    $("#remove_Bundle_"+bundleID).attr("id","remove_Bundle_"+values['id']).attr('data-id',values['id']);

                    $("#textBundleTr"+values['id']).show();

                    $("#cancelCreateBundleButton").attr('data-id',values['id']);
                    $(".saveBundleButton").hide();
                    if(values['bundle_count'] == "5" || values['bundle_count'] > "5"){
                        disableFirstStepButton(true,'enableNext');
                        // $("#addBundleButton").hide();
                    }
                    else{
                        disableFirstStepButton(true,'enableNext');
                    }
                    alloweDisabled(values['bundle_count'],values['group_id']);
                    addComparisionTab(values['id'],values['bundle_label'],editBundleID);
                    if($groupIds != ''){
                        create_bundle_ajax_submit(data);
                    }

                    if(editBundleID == 0){    
                        $('#compare_rows_label_div .compare_label_inputs').each(function(){
                            $temp_camp_id = $(this).attr('data-id');
                            $("#editcomparisonRowID").val($temp_camp_id);
                            saveComparisionRow($temp_camp_id);
                        });
                    }
                }
            }else if(res.status == 'Error'){
              is_error = true;
              var errors = res.data;
                is_error = true;
               $.each(errors,function(i,v){
                
                var label = i.replace(/\./,'_');
                $("#error_"+label).text(v[0]).show();
              });

              $(".error_bundle_"+errorClassAdd).each(function(){
                    if($(this).html() == ""){
                        $(this).parent().addClass('bundle_error_space');
                        $("#dynamicId_"+errorClassAdd).addClass('bundle_error_space');
                    }
              });
            }

        }
    });
}

/*** Questions add ***/
addQuestion = () => {
    $(".saveQuestionButton").show();
    index = parseInt($(".questionPortion").length);
    $display_counter = parseInt($('#question_table_counter').val());
    $number=index+1;
    if($display_counter > index){
        $number = $display_counter + 1;
    }
    $negativeNumber = ($number * -1);
    $lastLabelID = $("#questionTableBody .textQuestionTr:last").attr('data-LabelID');
    if($lastLabelID == undefined){
        $lastLabelID = 0;
    }
    $lastLabelID = parseInt($lastLabelID) + 1;
    
    htmlDiv = $("#quetion_template").html();
    html=htmlDiv.replace(/~i~/g, $negativeNumber);
    $('#editBundleQuestions').html(html);

    $("#question_id").val($negativeNumber);
    $("#inputQuetionsTr"+$negativeNumber).attr('data-LabelID',$lastLabelID);
    $("#dynamicQuestionId_"+$negativeNumber).html($lastLabelID);

    $("#question_table_counter").val($number);
    $("#control_type_"+$negativeNumber).addClass('form-control');
    $('#cancelCreateQuestionButton').attr('data-id',$negativeNumber);
    $options = $("#groupProducts").html();
    $("#inputQuetionsTr"+$negativeNumber).show();
    $("#QuestionID").val($negativeNumber);

    $("#addQuestionButton").hide();
    $(".tooltip"+$negativeNumber).tooltip();
    $("#answer_div").hide();
    disableSecondStepButton(false,'disableNext');
    common_select();

}


$(document).off('click', '.add_answer_btn');
$(document).on("click", ".add_answer_btn", function(e) {
    $questionDivId=$(this).attr('data-id');
    addBundleAnswerDiv($questionDivId);

});
$(document).off('click',"#cancel_recommendation");
$(document).on('click',"#cancel_recommendation",function(e){
    window.location.href="manage_groups.php";
});

$(document).off('click',"#cancel_recommandation_btn");
$(document).on('click',"#cancel_recommandation_btn",function(e){
    window.location.href="manage_groups.php";
});


$(document).off('click', '.view_bundle_table');
$(document).on('click', '.view_bundle_table', function(e) {
 e.preventDefault();
 $.colorbox({
   href: $(this).attr('href'),
   iframe: true,
   width: '800px',
   height: '500px'
 })
});

$(document).off('click',"#save_recommendation");
$(document).on('click',"#save_recommendation",function(e){
    var BundleTabId = $("#bundleID").val();
    $('.error').html('');
    if(!BundleTabId){
        $('#error_display_bundle_'+BundleTabId).html('Please Select Bundle');
    }
    // var is_check = $(".inputBundleTr").attr('') 
    var skipCompairions = $("#skipCompairions").val();
    $editRowComapreId = $("#editcomparisonRowID").val();
    var editskipCompairionsId = $("#editskipCompairionsId").val();
    var top_recommended = $('input[name="top_recommended"]:checked').val();
    var BundleInformationdata = $("#bundle_recommendation").serializeArray();
    BundleInformationdata.push({'name':'api_key','value':'saveBundleInformation'});
    BundleInformationdata.push({'name':'skipCompairions','value':skipCompairions});
    BundleInformationdata.push({'name':'top_recommended','value':top_recommended});
    BundleInformationdata.push({'name':'editskipCompairionsId','value':editskipCompairionsId});
    BundleInformationdata.push({'name':'editRowID','value':$editRowComapreId});
    $("#ajax_loader").show();
    $('.error').hide();
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        data :BundleInformationdata,
        method : 'POST',
        dataType :'JSON',
        success : function(res){
            if(res.status == 'Success'){
                 $data = res.data;
                 $compareIds = $('#comparisonRowID').val();
                $("#editskipCompairionsId").val($data['bundle_infomation_id']);                
                if(editcomparisonRowID !=0 ){
                    $("#editcomparisonRowID").val('0');
                }
                if($data['isExits'] == "Y"){
                     saveComparionDetails($data);
                }
                $("#ajax_loader").hide();
                window.location.href="manage_groups.php";
            }
            else if(res.status == 'Error'){

                is_error = true;
                var is_error = true;
                var errors = res.data;
                if(!errors['selectedGroup']){
                    CompareBundleErrorDisplay(errors);
                }
                else{
                    $("#error_selectedBundle").text(errors['selectedGroup']).show();
                    if(is_error){
                        var offset = $("#error_selectedBundle").offset();
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
                }
            }
            $("#ajax_loader").hide();
        }
    });
});
$(document).off('change',"select.questionType");
$(document).on('change', "select.questionType", function(event) {
        event.stopPropagation();
        
        $val = $(this).val();
        $questionDivId=$(this).attr('data-id');
        $("#inputQuetionsTr"+$questionDivId+" #main_answer_div").html('');
        if($val=="select_multiple" || $val=="select" || $val == "radio" || $val == 'checkbox'){
            $("#inputQuetionsTr"+$questionDivId+" #answer_parent_div").show();
           
           if($val == 'radio'){
                addBundleAnswerDiv($questionDivId,2);
                $('#inputQuetionsTr'+$questionDivId+' .add_answer_btn').hide();
           }else{
                addBundleAnswerDiv($questionDivId);
            $('#inputQuetionsTr'+$questionDivId+' .add_answer_btn').show();
           }
        }else{
            $('#inputQuetionsTr'+$questionDivId+' .add_answer_btn').hide();
        }
});

$(document).off('change',"select.display_bundle");
$(document).on('change',"select.display_bundle",function(e){
    e.stopPropagation();
    $('.error').html('');
    select=$(this);
    $assignBndlId=select.attr('data-id');

    selectedBndl=[];
    if(select.val() != null)
    {
        selectedBndl = Object.values($(this).val());
    }

    var existingBundle = [];
    $("#bundlePriority_"+$assignBndlId+" .bundlePriority").each(function () {
        existingBundle.push( $(this).attr("data-id"));
    });

    var addingBundles = selectedBndl.filter(x => existingBundle.indexOf(x) === -1);        
    var deletedBundles =  existingBundle.filter(x => selectedBndl.indexOf(x) === -1);
    if(addingBundles.length > 0)
    {
        addPrioritybundle(addingBundles,$assignBndlId);
    }

    if(deletedBundles.length>0){
        $.each(deletedBundles, function(key, val) {
         $('#addAnwserTab_'+$assignBndlId+' #display_bundle_div_'+val).remove(); //1
         updateBundleOrder($assignBndlId);
        });
    }

});

addPrioritybundle = (addingBundles,$assignBndlId) => {
        $.each(addingBundles, function(key, val) {
            $priorityCount = parseInt($("#addAnwserTab_"+$assignBndlId+" .bundlePriority").length);
            $number=$priorityCount+1;
            // $bundleNegativeNumber = ($number * -1);

            text=select.find('option[value='+val+']').text();
            html = $("#setPriorityDiv").html();
            html = html.replace(/~category_block~/g,'category_block');
            html = html.replace(/~bundleCount~/g,$number);
            html = html.replace(/~AnsweID~/g,$assignBndlId);
            html = html.replace(/~OrderValue~/g,$number);
            html=html.replace(/~i~/g, val);
            html = html.replace(/~bundleText~/g,text);
            $('#bundlePriority_'+$assignBndlId).append(html);
            $(".moveable").sortable({
            axis: 'y',
            //helper: fixHelper,
            cursor: 'move',
            items: '.category_block',
            placeholder: '.category_block',
            handle: '.moveable_controller',
            update: function(event,ui){
                $assignBndlId=$(this).attr('data-id');
                updateBundleOrder($assignBndlId);
            }
            });
            $(".tooltip"+val).tooltip();
        });
}

addBundleAnswerDiv = ($questionDivId,$answerRowCount=1,cloneCallBack=function(){},$callBackParam=0) =>{
    $answerArr = [];
    for($i=1;$i<=$answerRowCount;$i++){
        $anwerCount = $("#main_answer_div .answer_div").length;
        html = $("#answer_dynamic_div").html();
        $number=$answerCount+1;

        if($anwerCount==0){                            
            html = html.replace(/~icon_display~/g,'display:none');
        }else if($anwerCount==1 && $answerRowCount ==2){
            html = html.replace(/~icon_display~/g,'display:none');
            html = html.replace(/~header_display~/g,'display:none');   
        }else{
            html = html.replace(/~header_display~/g,'display:none');
        }
        html=html.replace(/~j~/g,$questionDivId);
        html=html.replace(/~i~/g, '-'+$number);
        $("#inputQuetionsTr"+$questionDivId+" #main_answer_div").append(html);
        $("#display_bundle_"+'-'+$number).addClass("se_multiple_select");
        $("#display_bundle_"+'-'+$number).multipleSelect({});
        $(".tooltip"+'-'+$number).tooltip();
        $answerArr.push("-"+$number);
        ++$answerCount;
    }
    assginBundle($answerArr,cloneCallBack,$callBackParam);
}

assginBundle = function($answerdivID,cloneCallBack=function(){},$callBackParam=0){
    var GroupIds = $("#selectedGroup").val();
    var option_html='';
    $("#ajax_loader").show();
    $.ajax({
        url:'<?=$HOST?>' + '/ajax_api_call.php',
        data : {api_key:'getBundle', group_id:GroupIds}, 
        method :'POST',
        datatype : 'JSON',
        success : function(res){
            $data = res.data;
            $.each($data,function(key,value){
                option_html+= '<option value='+value['id']+'>'+value['bundle_label']+'</option>';
            });

            $.each($answerdivID,function($k,$v){
                $("#display_bundle_"+$v).append(option_html);
                $("#display_bundle_"+$v).multipleSelect('refresh');
            })
            
            $(".tooltip"+$answerdivID).tooltip();
            cloneCallBack($callBackParam);
            $("#ajax_loader").hide();
        }
    });
}
updateBundleOrder = ($answerdivID) => {
        var bundle_order = 1;
        $("#addAnwserTab_"+$answerdivID+" div.category_block").each(function(index,ele){
            $(this).find('div.bundleCountDiv').html(bundle_order);
            $(this).attr('data-display-number',bundle_order);
           $(this).find('input.bundle_order').val(bundle_order);
            bundle_order++;
        });
}

/*** save Question ***/

saveQuestions = ($step) => {
    $("#ajax_loader").show();

    var QuestionID = $("#QuestionID").val();
    var editQuestionID = $("#editQuestionID").val();
    var formDatails = $("#bundle_recommendation").serializeArray();
    formDatails.push({'name':'api_key','value':'saveBundleQuestions'});
    formDatails.push({'name':'editQuestionId','value':editQuestionID});
    formDatails.push({'name':'step','value':$step});
    var bundleOrderArray=[];
    $('.error').hide();
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        data:formDatails,
        dataType: 'json',
        type: 'POST',
        success: function(res) {
            $("#ajax_loader").hide();
            $data = res.data;
            if(res.status == 'Success'){
                if($step=='2'){
                    

                    var saveQuestionIds = $data['QuestionDetails'][0]['question_id'];
                    var saveEncryptedQuestionIds = $data['QuestionDetails'][0]['encrypted_question_id'];
                    var saveQuestions = $data['QuestionDetails'][0]['questions'];
                    var saveQuestionType = $data['QuestionDetails'][0]['control_type'];
                    var saveQuestionIds = $data['QuestionDetails'][0]['question_id'];
                    var totalBundle = $data['QuestionDetails'][0]['total_assign_bundle'];

                    if(editQuestionID < 0 || editQuestionID == 0){
                        $lastLabelID = $("#questionTableBody .textQuestionTr:last").attr('data-LabelID');
                        if($lastLabelID == undefined){
                            $lastLabelID = 0;
                        }
                        $labelID = parseInt($lastLabelID) + 1;
                        html = $("#quetion_template_tr").html();
                        html=html.replace(/~i~/g, QuestionID);
                        $('#questionTableBody').append(html);
                        $(".tooltip"+QuestionID).tooltip();
                    }
                    // if(editQuestionID !=0 ){
                    //     $("#editQuestionID").val('0');
                    //      QuestionID = editQuestionID;
                    // }
                    $("#inputQuetionsTr"+QuestionID).remove();
                    $("#question_label_text"+QuestionID).attr("id","question_label_text"+saveQuestionIds).text(saveQuestions);
                    $("#question_answer_type_text"+QuestionID).attr("id","question_answer_type_text"+saveQuestionIds).text(saveQuestionType);
                    $("#textQuestionTr"+QuestionID).attr("id","textQuestionTr"+saveQuestionIds);

                    $("#textQuestionTr"+saveQuestionIds).attr('data-LabelID',$labelID);
                    if(saveQuestionType == 'radio'){
                        $("#question_answer_type_text"+saveQuestionIds).text('Yes/No');
                    }
                    else if(saveQuestionType == 'select'){
                        $("#question_answer_type_text"+saveQuestionIds).text('Multiple Choice (1 option)');
                    }
                    else if(saveQuestionType == 'select_multiple'){
                        $("#question_answer_type_text"+saveQuestionIds).text('Multiple Choice (1+ option)');
                    }
                    else if(saveQuestionType == 'checkbox'){
                        $("#question_answer_type_text"+saveQuestionIds).text('Check Answer');
                    }
                    $("#question_assigned_bundle_text"+QuestionID).attr("id","question_assigned_bundle_text"+saveQuestionIds).text(totalBundle);;
                    $("#view_bundle_table_"+QuestionID).attr('href',"view_bundle_question_answer_details.php?id="+saveEncryptedQuestionIds).attr('id','view_bundle_table_'+saveQuestionIds);
                    $("#question_clone_"+QuestionID).attr('data-id',saveQuestionIds).attr("id","question_clone_"+saveQuestionIds);
                    $("#question_edit_"+QuestionID).attr('data-id',saveQuestionIds).attr("id","question_edit_"+saveQuestionIds);
                    $("#question_remove_"+QuestionID).attr('data-id',saveQuestionIds).attr("id","question_remove_"+saveQuestionIds);
                    $("#textQuestionTr"+saveQuestionIds).show();
                    $(".QuestionHeaderTr").show();
                    $("#addQuestionButton").show();
                    $(".saveQuestionButton").hide();
                    // $(".cancelCreateQuestionButton").hide();
                    $("#QuestionID").val(0);
                    $("#editQuestionID").val(0);
                   disableSecondStepButton(true,'enableNext');    
                }
            }
            else if(res.status == 'Error'){
                is_error = true;
                var errors = res.data;
                $.each(errors,function(i,v){
                    label = i.replace(/\./,'_');
                    $("#error_"+label).text(v[0]).show();
                    if(is_error){
                        var offset = $('#error_' + label).offset();
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
            $("#ajax_loader").hide();
        }
    });
}

/*** /save  Question ***/


/*** edit Question ***/
$(document).off('click',".EditQuestion,.CloneQuestion");
$(document).on('click',".EditQuestion,.CloneQuestion",function(e){
    var selected_value = $('select.selectedGroup').children("option:selected").val();
    var id = $(this).attr('data-id');
    var QuestionId = $(this).attr('id');
    $labelID = $("#textQuestionTr"+id).attr('data-LabelID');

    var is_clone = '';
    if(QuestionId == "question_edit_"+id){
        is_clone = "N";
    }else{
        is_clone = "Y";
        //$lastLabelID = $("#questionTableBody .textQuestionTr:last").attr('data-LabelID');
        //$labelID = parseInt($lastLabelID) + 1;
    }
    $("#ajax_loader").show();
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        data:{id:id,group_id:selected_value,is_clone : is_clone ,api_key:"getEditBundleQuestion"},
        dataType: 'json',
        type: 'POST',
        success: function(res) {
            if (res.status=='Success') {
                $data = res.data;
                var QuestionIds = $data['QuestionDetails'][0]['question_id'];
                var QuestionType = $data['QuestionDetails'][0]['control_type'];
                $("#QuestionID").val(QuestionIds);
                $("#editQuestionID").val(QuestionIds); 
                $("#cancelCreateQuestionButton").attr('data-id',QuestionIds);
                var  htmlDiv = $("#quetion_template").html();
                // $existingAnswerCount=$("#inputQuetionsTr"+$questionDivId+" .answer_div").length;
                var QuestionCount = $(".textQuestionTr").length;
                
                if($data['QuestionDetails']){
                    $arr = $data['QuestionDetails'];
                    $.each($arr,function(k,val){
                        var html = htmlDiv.replace(/~i~/g,val['question_id']);
                        $("#editBundleQuestions").html(html);
                        $("#control_type_"+val['question_id']).addClass('form-control');
                        $("#control_type_"+val['question_id']+" option[value ='"+val['control_type']+"']").attr("selected", "selected");
                            
                        $("#control_type_"+val['question_id']).selectpicker({
                            container: 'body', 
                            style:'btn-select',
                            noneSelectedText: '',
                            dropupAuto:false,
                        });
                        $("#ask_question_"+val['question_id']).attr('value',val['questions']);
                        $("#dynamicQuestionId_"+val['question_id']).text($labelID);
                        $(".tooltip"+val['question_id']).tooltip();
                    });

                }
                if($data['AnswerDetails']){
                    $arr = $data['AnswerDetails'];
                    $.each($arr,function(i,v){
                        var existingAnswerCount=$("#inputQuetionsTr"+QuestionIds+" .answer_div").length;
                        // var existingAnswerCount = $("#main_answer_div .answer_div").length;
                        html = $("#answer_dynamic_div").html();
                        html=html.replace(/~j~/g,QuestionIds);
                        html = html.replace(/~i~/g,v['answer_id']);
                        if(existingAnswerCount==0){                            
                            html = html.replace(/~icon_display~/g,'display:none');
                        }else if(existingAnswerCount==1 && QuestionType =='radio'){
                            html = html.replace(/~icon_display~/g,'display:none');
                            html = html.replace(/~header_display~/g,'display:none');   
                        }else{
                            html = html.replace(/~header_display~/g,'display:none');
                        }
                        $("#inputQuetionsTr"+QuestionIds+" #main_answer_div").append(html);
                        $("#inputQuetionsTr"+QuestionIds+" #answer_parent_div").show();
                        $("#question_answer_"+v['answer_id']).attr('value',v['answer']);
                        $("#display_bundle_"+v['answer_id']).addClass("se_multiple_select");
                        $("#display_bundle_"+v['answer_id']).multipleSelect({});

                        if(QuestionType == "radio"){
                            $('#inputQuetionsTr'+QuestionIds+' .add_answer_btn').hide();
                        }else{
                            $('#inputQuetionsTr'+QuestionIds+' .add_answer_btn').show();
                        }
                    });
                }
                if($data['BundleDetails']){
                    $arr = $data['BundleDetails'];
                    $arrAns = $data['AnswerDetails'];
                    $arrBundleDiv = $data['BundleOrderDetails'];

                    var option_html='';
                    $.each($arr,function(i,bundleval){
                        option_html += '<option value='+bundleval['bundleId']+'>'+bundleval['bundle_label']+'</option>';  
                    });
                    $.each($arrAns,function(k,v){
                        var bundleVal = "["+v['selected_bundle']+"]"; 
                        $("#display_bundle_"+v['answer_id']).append(option_html);
                        $("#display_bundle_"+v['answer_id']).multipleSelect('refresh');
                        $("#display_bundle_"+v['answer_id']).multipleSelect('setSelects',bundleVal);
                        $("#bundlePriority_"+v['answer_id']).html('');
                        $("#editPriority_"+v['answer_id']).show();
                        $("#set_priority_"+v['answer_id']).hide();
                        $(".tooltip"+v['answer_id']).tooltip();
                        var j=1;
                        $.each($arrBundleDiv[v['answer_id']],function(key,val){
                            html = $("#setPriorityDiv").html();
                            html = html.replace(/~category_block~/g,'category_block');
                            html = html.replace(/~AnsweID~/g,v['answer_id']);
                            html = html.replace(/~bundleCount~/g,j);
                            html=html.replace(/~i~/g, val['bundle_id']);
                            html = html.replace(/~bundleText~/g,val['bundle_label']);
                            html = html.replace(/~OrderValue~/g,val['order_by']);
                            $('#bundlePriority_'+v['answer_id']).append(html);
                            $(".tooltip"+val['bundle_id']).tooltip();
                            $(".moveable").sortable({
                            axis: 'y',
                            //helper: fixHelper,
                            cursor: 'move',
                            items: '.category_block',
                            placeholder: '.category_block',
                            handle: '.moveable_controller',
                            update: function(event,ui){
                                $assignBndlId=$(this).attr('data-id');
                                updateBundleOrder($assignBndlId);
                            }
                            });

                            j++;
                        });
                    });                         
                }
                fRefresh();
            }
            $("#ajax_loader").hide();
        }

    });
    disableSecondStepButton(false,'disableNext');
});

/*** /edit Question ***/

/*** delete Question ***/
$(document).off('click',".QuestionRemove");
$(document).on('click',".QuestionRemove",function(e){
    e.stopPropagation();
     var id = $(this).attr('id').replace(/question_remove_/g,'');
     
     swal({
           text: 'Delete Question: Are you sure?',
           showCancelButton: true,
           confirmButtonText: 'Confirm',
        }).then(function() {
        $("#ajax_loader").show();
        $.ajax({
            url :'<?=$HOST?>' + '/ajax_api_call.php',
            data: {id:id , api_key:'deleteBundleQuestion'},
            method: 'POST',
            dataType: 'JSON',
            success: function(res) {
                if (res.status == 'Success') {
                    $deleteLabelID = $("#textQuestionTr"+id).attr('data-LabelID');
                    $("#questionTableBody .textQuestionTr").each(function(){
                        $labelID = $(this).attr('data-LabelID');

                        if($deleteLabelID < $labelID){
                            $newLabelID = parseInt($labelID) - 1;
                            $(this).attr('data-LabelID',$newLabelID);
                        }
                    });
                    $("#textQuestionTr"+id).remove();
                    $("#inputQuetionsTr"+id).remove();
                    $(".tooltip"+id).tooltip();
                    // $('[data-toggle="tooltip"]').tooltip();
                    $("#ajax_loader").hide();
                }
                
            }
           
        });
            // $("#textQuestionTr").remove();
            }, function(dismiss){
        }); 
});

/*** /delete Question ***/

$(document).off('click',".savePriority");
$(document).on('click',".savePriority",function(e){
    $answerTabId=$(this).attr('data-id');
    $priorityBundlCount = $("#addAnwserTab_"+$answerTabId+" .bundlePriority").length;
    $('#error_display_bundle_'+$answerTabId).hide().html('');

    if($priorityBundlCount>0){
        priorityHandle($answerTabId,'save');
    }else{
        $('#error_display_bundle_'+$answerTabId).html('Please Select Bundle').show();
    }
});

$(document).off('click',".editPriority");
$(document).on('click',".editPriority",function(e){
    $answerdivID = $(this).attr('data-id');
    priorityHandle($answerdivID,'edit');
});

priorityHandle = ($answerdivID,$operation) => {
    if($operation=='save'){
        $('#addAnwserTab_'+$answerdivID+' .set_priority').hide();
        $('#addAnwserTab_'+$answerdivID+' #moveable_div').hide();
        $('#addAnwserTab_'+$answerdivID+' .bundles_priority_div').hide();   
        $('#addAnwserTab_'+$answerdivID+' .editPriority').show();
    }else if($operation =='edit'){
        $('#addAnwserTab_'+$answerdivID+' #moveable_div').show();
        $('#addAnwserTab_'+$answerdivID+' .bundles_priority_div').show();   
        $('#addAnwserTab_'+$answerdivID+' .editPriority').hide();
    }
}


$(document).off('click' , '.set_priority');
$(document).on('click' , '.set_priority',function(e){
    $answerTabId=$(this).attr('data-id');
    $priorityBundlCount = $("#addAnwserTab_"+$answerTabId+" .bundlePriority").length;
    $('#error_display_bundle_'+$answerTabId).hide().html('');

    if($priorityBundlCount>0){
        $(this).attr('data-enabled','1');
        $(this).hide();
        $('#addAnwserTab_'+$answerTabId+' #moveable_div').show();
        $('#addAnwserTab_'+$answerTabId+' .bundles_priority_div').show();
        $("#addAnwserTab_"+$answerTabId+" .save_answer_div").show();            
    }else{
        $('#error_display_bundle_'+$answerTabId).html('Please Select Bundle').show();
    }
});

$(document).off('click' , '.delete_answer_btn');
$(document).on('click' , '.delete_answer_btn',function(e){
    $answerTabId=$(this).attr('data-id');
    $('#addAnwserTab_'+$answerTabId).remove();
});

cancelQuestion = () => {
    var removeId = parseInt($("#cancelCreateQuestionButton").attr('data-id'));
    var editId = $("#editQuestionID").val();
    $(".saveQuestionButton").hide();
    if(editId == 0 || editId < 0){
        $("#textQuestionTr"+removeId).remove();
        $("#inputQuetionsTr"+removeId).remove();
    }
    else{
        $("#textQuestionTr"+editId).show();
        $("#inputQuetionsTr"+editId).hide();
    }
    $("#addQuestionButton").show();
    $("#editQuestionID").val('0');
    $("#QuestionID").val('0');
    disableSecondStepButton(true,'enableNext');
}
/*** /Questions add ***/
//group assign disble excepted selected value
alloweDisabled = ($bundleCount,$assignGrop) =>{
    if($bundleCount > 0){
        $('select.selectedGroup option').each(function() {
            option_val = $(this).val();
                if($assignGrop != option_val){
                  $(this).prop('disabled', true);
                 $('.selectedGroup').selectpicker('refresh');
                }
        });
        $(".BundleheaderTr").show();
    }
    else{
        $('select.selectedGroup option').each(function() {
                $(this).prop('disabled', false);
                $('.selectedGroup').selectpicker('refresh');
        });
        $(".BundleheaderTr").hide();
    }
}
$(document).off('click',"#cancelCreateBundleButton");
$(document).on('click',"#cancelCreateBundleButton",function(){
    var bundleID = $("#bundleID").val();
    var editBundleID = $("#editBundleID").val();
    cancelBundle(bundleID,editBundleID);
});

//Cancel Bundle
cancelBundle = (bundleID,editBundleID) => {
    $(".saveBundleButton").hide();
    var bundleCount = parseInt($("#bundleTableBody .textBundleTr").length);
    if(bundleID < 0){
        $("#textBundleTr"+bundleID).remove();
        $("#inputBundleTr"+bundleID).remove();
    }
    else{
        $("#textBundleTr"+editBundleID).show();
        $("#inputBundleTr"+editBundleID).hide();
    }

    // if(bundleCount != 5 && bundleCount < 5){
            $("#addBundleButton").show();
    // }
    $("#editBundleID").val('0');
    $bundleTRCount = $("#bundleTableBody tr").length;

    if($bundleTRCount > 0){
        disableFirstStepButton(true,'enableNext');
    }else{
        disableFirstStepButton(true,'disableNext');
    }
}
//Disable goto second step button
disableFirstStepButton = ($showBundle,$nextButton) =>{
    var bundleCount = parseInt($("#bundleTableBody .textBundleTr").length);
    if($showBundle){
        // if(bundleCount != 5 && bundleCount < 5){
            $("#addBundleButton").show();
        // }
        $(".saveBundleButton").hide();
    }else{
        // $("#addBundleButton").hide();
        $(".saveBundleButton").show();
    }

    if($nextButton == "disableNext"){
        $("#go_to_second_step").addClass('disabled');
        $("#go_to_second_step").prop('disabled',true);
    }else{
        $("#go_to_second_step").prop('disabled',false);
        $("#go_to_second_step").removeClass('disabled');
            
    }

    $bundleLength = parseInt($(".bundlePortion").length);
    if($bundleLength == 0){
        $("#addBundleButton").show();
        $("#go_to_second_step").prop('disabled',true);
        $("#go_to_second_step").addClass('disabled');
    }
}

//Disable goto Third step button
disableSecondStepButton = ($showBundle,$nextButton) =>{
    if($showBundle){
        $(".saveQuestionButton").hide();
        $("#addQuestionButton").show();
    }else{
        $(".saveQuestionButton").show();
        $("#addQuestionButton").hide();
    }

    if($nextButton == "disableNext"){
        $("#go_to_third_step").addClass('disabled');
        $("#go_to_third_step").prop('disabled',true);   
    }else{
        $("#go_to_third_step").removeAttr('disabled');
        $("#go_to_third_step").removeClass('disabled');         
    }

}

//Change hidden tr label text
changeText = ($this) => {
    $element = $this.attr('data-change_text');
    $("#"+$element).text($this.val());
}

// go to step 2
goto_step = ($step) => {
var SkipComparison = '<?=$SkipComparison?>';
    if($step == 2){
        $("#edit_bundle_step_1").show();
        $("#step_1_footer").hide();
        // $(".editBundleLabelTr").hide();
        $("#step_1_hr").show();
        $(".bundle_wrap").show();
    }else if($step == 3){
        $("#edit_bundle_step_2").show();
        $("#save_recommendation").show();
        $("#cancel_recommendation").show();
        $("#step_2_footer").hide();
        $(".editQuestionIcons").hide();
        $("#step_2_hr").show();
        $('.comparison_toggle').show();
        $(".comparison_wrap").show();
        if(SkipComparison == 'Y'){
            $("#skip_compare_btn").show();
            $("#add_compare_btn").hide();
        }
    }
}

edit_bundle_step = ($step) => {
    var bundleCount = parseInt($("#bundleTableBody .textBundleTr").length);
    if($step == 1){
        $("#edit_bundle_step_1").hide();
        $("#step_1_footer").show();
        
        // if(bundleCount != 5 && bundleCount < 5){
            $("#addBundleButton").show();
        // }

        $(".editBundleLabelTr").show();
        disableFirstStepButton(true,'enableNext');
    }else if($step == 2){
        $("#edit_bundle_step_2").hide();
        $("#step_2_footer").show();
        $("#addQuestionButton").show();
        $(".editQuestionIcons").show();
        disableSecondStepButton(true,'enableNext');
    }
}

//comparion handle 

//add comparision row

addComparisionTab = ($bundlId,$bundltext,$editBundleID) => {
    if($editBundleID == '0'){
        html = $(".compareRecommendDynamicTh").html();
        html = html.replace(/~i~/g,$bundlId);
        // html = html.replace(/~j~/g,$bundlId);
        html = html.replace(/~text~/g,$bundltext);
        $('#comparision_recommended_div').append(html);
        $('#top_recommended_'+$bundlId).attr('type','radio').uniform();

        html=$('.compareBundleHeadingDynamicTh').html();
        html = html.replace(/~i~/g,$bundlId);
        html = html.replace(/~text~/g,$bundltext);
        $('#comparision_bundles_div').append(html);
        existingCompareRowCount=$('#comparison_row_counter').val();
        if(existingCompareRowCount>0){
            $('.compare_label_inputs').each(function(e){
                    $rowId=$(this).attr('data-id');
                    addComparisionTdColumn($rowId,$bundlId);            
            })
        }
    }
    else{
        $("#comparision_bundle_"+$bundlId).text($bundltext);
    }
}

$(document).off('change','.top_recommended_bundle');
$(document).on('change','.top_recommended_bundle',function(e){
    var top_recommended = $('input[name="top_recommended"]:checked').val();
    $('.comparision_bundle').removeClass('bundle-selected');
    $("#comparision_bundle_"+top_recommended).addClass('bundle-selected');
});


$(document).off('click', '#add_row');
$(document).on('click', '#add_row', function(e) {
    /*consider bundleID as column id for each row
      matrix example (if have 3 three bundle selected and 2 row)
    {
        rowid  colId (Bundle A)    colId (Bundle B)  colId (Bundle C)
        -1      12                 13                 14
        -2      12                 13                 14
    }*/

    e.stopPropagation();
    $count = parseInt($('#comparison_row_counter').val());
    $number=$count+1;
    $negativeNumber = ($number * -1);
    $('#comparisonRowID').val($negativeNumber);
    addLableSet($negativeNumber);
    addRowTrSet($negativeNumber);

    $('#comparision_bundles_div .comparision_bundle').each(function(e){
        $bundlId=$(this).attr('data-id');
        addComparisionTdColumn($negativeNumber,$bundlId);       
    });
    toggleThirdStepButton(true);
    $('#comparison_row_counter').val($number);
    skipComapreDiv();
});

$(document).off('click', '#save_row');
$(document).on('click', '#save_row', function(e) {
    e.stopPropagation();
    $rowId = parseInt($('#comparisonRowID').val());

    saveComparisionRow($rowId);
});

$(document).off('click', '#cancel_row');
$(document).on('click', '#cancel_row', function(e) {
    e.stopPropagation();
    $rowId = parseInt($('#comparisonRowID').val());
    cancelRowCompare($rowId);
   skipComapreDiv();
});

$(document).off('click', '.edit_compare_row');
$(document).on('click', '.edit_compare_row', function(e) {
    e.stopPropagation();
    $rowId = $(this).attr('data-id');
    var compareId = $("#comparisonRowID").val();
    if(compareId < 0){
        $('#compare_label_inputs_'+compareId).remove();
        $('#compare_label_text_'+compareId).remove();
        $('#compare_bundle_inputs_'+compareId).remove();
        $('#compare_bundle_texts_'+compareId).remove();
    }
    $('#comparisonRowID').val($rowId);
    $('#editcomparisonRowID').val($rowId);
    $('#compare_rows_label_div .compare_label_inputs').hide();
    $('#comparision_table_body .compare_bundle_inputs').hide();
    $('#compare_rows_label_div .compare_label_text').show();
    $('#comparision_table_body .compare_bundle_texts').show();
    $(".error").hide();
    toggleComparisionRowTr($rowId,true);    
    //toggleInputTextTr
    toggleThirdStepButton(true);

});

$(document).off('click', '.del_compare_row');
$(document).on('click', '.del_compare_row', function(e) {
    e.stopPropagation();
    $rowId = $(this).attr('data-id');  
    var EditRowID = $("#editcomparisonRowID").val();
    removeComparisonRow($rowId);
});

skipComapreDiv = () =>  {
    var rowCount = $("#compare_rows_label_div .compare_label_inputs").length;
    if(rowCount == 0) {
        $("#skip_compare_btn").show();
        // $('#add_compare_btn').hide();

    }else {
        $("#skip_compare_btn").hide();
        // $('#add_compare_btn').show();

    }
   
}
cancelRowCompare = ($rowId) =>{
    if($rowId < 0){
        $('#compare_label_inputs_'+$rowId).remove();
        $('#compare_label_text_'+$rowId).remove();
        $('#compare_bundle_inputs_'+$rowId).remove();
        $('#compare_bundle_texts_'+$rowId).remove();
    }else{
        $('#compare_label_inputs_'+$rowId).hide();
        $('#compare_label_text_'+$rowId).show();
        $('#compare_bundle_inputs_'+$rowId).hide();
        $('#compare_bundle_texts_'+$rowId).show();
    }
    $(".error").hide();
    $(".compare_label_text div.dropdown").removeClass('open');
    $('#editcomparisonRowID').val('0');
    $('#comparisonRowID').val('0');
    
    toggleThirdStepButton(false);
}
addLableSet = ($rowId) => {
    html = $(".compareLableSetDynamicDiv").html();
    html = html.replace(/~i~/g,$rowId);
    $('#compare_rows_label_div').append(html);
}

addRowTrSet = ($rowId) => {
    html=$(".compareBundleDynamicTrSet").html();
    html = html.replace(/~i~/g,$rowId);
    $('#comparision_table_body').append(html);
}

addComparisionTdColumn = ($rowId,$bundlId) => {
    html = $(".compareBundleInputDynamicTd").html();
    html = html.replace(/~i~/g,$rowId);
    html = html.replace(/~j~/g,$bundlId);
    $('#compare_bundle_inputs_'+$rowId).append(html);

    html=$(".compareBundleTextDynamicTd").html();
    html = html.replace(/~i~/g,$rowId);
    html = html.replace(/~j~/g,$bundlId);
    $('#compare_bundle_texts_'+$rowId).append(html);
}

saveComparisionRow = ($rowId) => {

    //get all row column inputs and set in respective column text

    $editRowComapreId = $("#editcomparisonRowID").val();
    var skipCompairions = $("#skipCompairions").val();
    var editskipCompairionsId = $("#editskipCompairionsId").val();
    var formData = $("#bundle_recommendation").serialize();
    formData = formData+"&api_key=saveBundleComparison&editRowID="+$editRowComapreId+"&editskipCompairionsId="+editskipCompairionsId+"&skipCompairions"+skipCompairions;
    $('.error').hide();
    var status = "";
    $("#ajax_loader").show();
    $.ajax({
        url:'<?=$HOST?>' + '/ajax_api_call.php',
        data:formData,
        method:'POST',
        dataType:'JSON',
        success :function(res){
            $data = res.data;
            status = res.status;
            var status = "";
            if(res.status == "Success"){
                 $("#editskipCompairionsId").val($data['saveInforId']);
                saveComparionDetails($data);
            }
            else if(res.status == 'Error'){
            $("#editskipCompairionsId").val(editskipCompairionsId);
              is_error = true;
              var errors = res.data;
                CompareBundleErrorDisplay(errors);
                toggleThirdStepButton(true);
                toggleComparisionRowTr($rowId,true);
            }
            $("#ajax_loader").hide();
            $(".compare_label_text div.dropdown").removeClass('open');

        }
    });
}
saveComparionDetails = ($data)=>{
    $rowId = parseInt($('#comparisonRowID').val());
        $("#compare_label_inputs_"+$rowId).attr('data-id',$data['id']).attr('id','compare_label_inputs_'+$data['id']);
        $("#edit_compare_row_"+$rowId).attr('data-id',$data['id']).attr('id','edit_compare_row_'+$data['id']);
        $("#del_compare_row_"+$rowId).attr('data-id',$data['id']).attr('id','del_compare_row_'+$data['id']);
        $("#comparision_bundle_"+$rowId).attr('data-id',$data['id']).attr('id','comparision_bundle_'+$data['id']);
        $('#compare_label_'+$rowId).text($data['comparison_lable']).attr('id','compare_label_'+$data['id']);
        arr = $.parseJSON($data['bundle_comparison_lable']);
        $.each(arr,function(i,v){
            $("#bundle_recom_input_"+$rowId+"_"+i).attr('id','bundle_recom_input_'+$data['id']+'_'+i).attr('name','compare_bundle['+i+']['+$data['id']+']');
            var val = v ? v :' ';
            $('#compare_bundle_texts_'+$rowId).find('td[data-column-id='+i+']').text(val);
        });

        $("#compare_row_label_"+$rowId).attr('name','compare_row_label['+$data['id']+']').attr('id','compare_row_label_'+$data['id']);
        $("#compare_label_text_"+$rowId).attr('id','compare_label_text_'+$data['id']);
        $("#compare_bundle_inputs_"+$rowId).attr('id','compare_bundle_inputs_'+$data['id']);
        $("#compare_bundle_texts_"+$rowId).attr('id','compare_bundle_texts_'+$data['id']);

        $("#top_recommended_"+$rowId).attr('id','top_recommended_'+$data['id']);
        $('#comparisonRowID').val('0');
        toggleThirdStepButton(false);
        toggleComparisionRowTr($data['id'],false);
}
CompareBundleErrorDisplay = (errors) =>{
     var is_error = true;
     $.each(errors,function(i,v){
        var label = i.replace(/\i/,'_');
        if(label == "top_recommended"){
            $("#error_top_recommended").text(v[0]).show();
        }
        else if(i == "error_bundle_compare_row") {
            $("#error_bundle_compare_row").text(v[0]).show();
        }else{
            $("#error_compare_row_label").text(v[0]).show();
        }   
      });
}
removeComparisonRow = ($rowId) => {
    //remove row
    $("#ajax_loader").show();
    $.ajax({
        url: '<?=$HOST?>' + '/ajax_api_call.php',
        data :{id:$rowId,api_key:'deleteBundleComaprison'},
        method : 'POST',
        dataType:'JSON',
        success:function(res){
            $('#compare_label_inputs_'+$rowId).remove();
            $('#compare_label_text_'+$rowId).remove();
            $('#compare_bundle_inputs_'+$rowId).remove();
            $('#compare_bundle_texts_'+$rowId).remove();
            skipComapreDiv();
            $("#ajax_loader").hide();
        }
    });
    

}

// removeComparisonTab = ($bundlId) => {
//     $('th[data-id='+$bundlId+']').remove();
//     $('td[data-column-id='+$bundlId+']').remove();
// }

toggleComparisionRowTr = ($rowId,$enableInputs) => {
    if($enableInputs){
        $('#compare_label_inputs_'+$rowId).show();
        $('#compare_label_text_'+$rowId).hide();
        $('#compare_bundle_inputs_'+$rowId).show();
        $('#compare_bundle_texts_'+$rowId).hide();
    }else{
        $('#compare_label_inputs_'+$rowId).hide();
        $('#compare_label_text_'+$rowId).show();
        $('#compare_bundle_inputs_'+$rowId).hide();
        $('#compare_bundle_texts_'+$rowId).show();
    }
}

//Disable goto Third step button
toggleThirdStepButton = ($enableSave) =>{
    if($enableSave){
        $("#add_row").hide();
        $(".row_btn").show();
    }else{
        $("#add_row").show();
        $(".row_btn").hide();
    }
}
skipCompairions = () => {
    $('#add_compare_btn').show();
    $('.comparison_wrap').hide();
    $('#compare_rows_label_div .bundle_label_set').remove();
    $('#compare_rows_label_div .compare_label_text').remove();
    $('#comparision_table_body .compare_bundle_inputs').remove();
    $('#comparision_table_body .compare_bundle_texts').remove();
    $('#skipCompairions').val('Y');
}

function create_bundle_ajax_submit(data) {
    $('#ajax_loader').show();
    $("#step_1_footer").hide();
    $(".editBundleLabelTr").hide();
    if(currentMultipleSelect !== ''){
        currentMultipleSelect.multipleSelect('close');
        currentMultipleSelect = '';
    }
    $.ajax({
        url: 'create_bundle.php',
        data: data,
        method:'post',
        dataType: 'json',
        success: function(res) {
            $("#bundleTableBody").html(res.htmlData);
            $("#paginateLinkDiv").html(res.paginateLink);
            $("#totalBundleForGroup").val(res.totalBundleForGroup);
            $('#ajax_loader').hide();
            common_select();
            data.from = '';
            $(".editBundleLabelTr").hide();
            $("#edit_bundle_step_1").show();
        }
    });
}
$(document).off('click', '#skip_compare_btn');
$(document).on('click', '#skip_compare_btn', function(e) {
    e.stopPropagation();
    $(this).hide();
    skipCompairions();
});

$(document).off('click', '#add_compare_btn');
$(document).on('click', '#add_compare_btn', function(e) {
    e.stopPropagation();
    $(this).hide();
    $('.comparison_wrap').show();
    $('#skip_compare_btn').show();
    $('#skipCompairions').val('N');
});

</script>
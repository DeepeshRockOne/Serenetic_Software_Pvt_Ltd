<style>
@media (min-width: 992px){
   #cke_certificate_mem_description {  margin-top:-85px;margin-bottom: 20px;}   
}
</style>
<?php include "notify.inc.php";?>
<div class="panel panel-default panel-space">
   <form action="" role="form" method="post"  name="resources_form" id="resources_form" enctype="multipart/form-data">
      <div class="panel-heading">
         <div class="panel-title">
            <h4 class=" mn">+ Resource</h4>
         </div>
      </div>
      <div class="panel-body theme-form">
         <input type="hidden" name="group_count" value="1" id="group_count">
         <input type="hidden" name="is_clone" id="is_clone" value="<?=$is_clone?>"/>
         <input type="hidden" name="display_counter" value="0" id="display_counter">
         <input type="hidden" name="resId" value="<?=checkIsset($resource_res['id'])?>" id="display_counter">
         <div class="row">
            <div class="col-md-4 col-sm-6">
               <div class="form-group ">
                  <input type="text" class="form-control" name="resource_name" id="resource_name" value="<?=checkIsset($resource_name)?>">
                  <label>Resource Name*</label>
                  <span class="error error_preview" id="error_resource_name"></span>
               </div>
            </div>
            <div class="col-md-4 col-sm-6">
               <div class="form-group ">
                  <input type="text" class="form-control" name="resource_id" maxlength='7' id="resource_id" value="<?=checkIsset($display_id)?>">
                  <label>Resource ID (Must Be Unique)*</label>
                  <span class="error error_preview" id="error_resource_id"></span>
               </div>
            </div>
            <div class="col-md-4 col-sm-6">
               <div class="form-group">
                     <select class="src_products se_multiple_select" multiple="multiple" name="src_products[]" id="src_products">
                        <?php foreach ($product_res as $key=>$company) { ?>
                        <optgroup label='<?= $key ?>'>
                           <?php foreach ($company as $pkey =>$row) { ?>
                              <?php 
                              $has_value = '';
                              if(!empty($sel_prd_arr) && in_array($row['id'], $sel_prd_arr)){
                                $has_value = 'selected="selected"';
                              }
                              ?>
                           <option value="<?= $row['id'] ?>" <?=$has_value?>><?= $row['name'] .' ('.$row['product_code'].')' ?></option>
                           <?php } ?>
                        </optgroup>
                        <?php } ?>
                     </select>
                     <label>Product(s)*</label>
                     <span class="error error_preview" id="error_src_products"></span>
               </div>
            </div>
            <div class="col-md-4 col-sm-6">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                     <div class="pr">
                        <input id="effective_date" type="text" class="form-control" name="effective_date" value="<?=!empty($resource_res['effective_date']) ? date('m/d/Y',strtotime($resource_res['effective_date'])) : '' ?>" placeholder="">
                        <label class="label-wrap">Effective Date(MM/DD/YYYY)</label>
                     </div>
                  </div>
                  <span class="error error_preview" id="error_effective_date"></span>
               </div>
            </div>
            <div class="col-md-4 col-sm-6">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                     <div class="pr">
                        <input id="termination_date" type="text" class="form-control" name="termination_date" value="<?=!empty($resource_res['termination_date']) && $resource_res['termination_date']!='0000-00-00' ? date('m/d/Y',strtotime($resource_res['termination_date'])) : '' ?>" placeholder="">
                        <label class="label-wrap">Termination Date(MM/DD/YYYY)</label>
                     </div>
                  </div>
                  <span class="error error_preview" id="error_termination_date"></span>
               </div>
            </div>
            <div class="col-md-4 col-sm-6">
               <div class="form-group ">
                  <select class="form-control" id="resources_type" name="resources_type" class="resources_type">
                     <option></option>
                     <?php if(checkIsset($resource_res['type']) !== ''){?>
                     <option value="Certificate" <?=checkIsset($resource_res['type'])=='Certificate' ? 'selected' : 'disabled'?>>Certificate</option>
                     <option value="Collateral" <?=checkIsset($resource_res['type'])=='Collateral' ? 'selected' : 'disabled'?>>Collateral</option>
                     <option value="id_card" <?=checkIsset($resource_res['type'])=='id_card' ? 'selected' : 'disabled'?>>ID Card</option>
                     <?php }else{?>
                     <option value="Certificate">Certificate</option>
                     <option value="Collateral">Collateral</option>
                     <option value="id_card">ID Card</option>
                     <?php } ?>
                  </select>
                  <label>Resource Type</label>
                  <span class="error error_preview" id="error_resources_type"></span>
               </div>
            </div>
         </div>
         <!-- resource edit start -->
         <?php
            if(!empty($sub_resource_res) && count($sub_resource_res) > 0) { // resources start
                $counter = 0;
              foreach ($sub_resource_res as $key => $value) { //foreach start
                $sel_satates = explode(",",$value['state_id']);
                $counter++;
                $selected_y = '';
                $selected_n = 'checked';
                if(!empty($sel_satates) && count($sel_satates) != 0 && $sel_satates[0] != ''){ 
                  $selected_y = 'checked';
                  $selected_n = '';
                }
            ?>
         <?php  if($value['type'] == 'Collateral' || $value['type'] == 'Certificate'){ ?>
         <?php if($counter == 1) { ?>  
         <div id="collateral_code">
            <div class="clearfix"></div>
            <p class="fs16 m-t-20"><strong class="fw500" id="coll_certi_id"><?=$value['type']?> Information</strong></p>
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <select  class="form-control coll_user_type" name="coll_user_type" id="coll_user_type">
                        <option value="Agent" <?=$value['user_group'] == 'Agent' ? 'selected="selected"' : '' ;?>>Agent</option>
                        <option value="Member" <?=$value['user_group'] == 'Member' ? 'selected="selected"' : '' ;?>>Member</option>
                        <option value="Group" <?=$value['user_group'] == 'Group' ? 'selected="selected"' : '' ;?>>Group</option>
                     </select>
                     <label>User Group </label>
                     <span class="error error_preview" id="error_coll_user_type"></span>
                  </div>
               </div>
            </div>
            <div class="coll_div_hidden">
               <div id="main_div_col">
                  <div id="dynamic_coll_div_">
                     <!-- dynamic_coll_tree_div start  -->
                     <?php } ?>
                     <div id="innerCollDiv_<?=$value['group_id']?>" class="inner_coll_div" data-id="<?=$value['group_id']?>">
                        <input type="hidden" name="dyncollFields[<?=$value['group_id']?>]">
                        <div class="row">
                           <div class="col-md-4 pr">
                              <div class="pull-left">
                                 <p class="fs16 m-b-15"><strong class="fw500" id="idcollateral<?=$value['group_id']?>"><?=$value['type']?> <?=$counter?></strong></p>
                              </div>
                               <div class="clearfix"></div>
                              <?php if($counter > 1) { ?>
                              <div class="pull-right">
                                 <a class="fs16 text-light-gray remove_state_group" href="javascript:void(0);" data-id="<?=$value['group_id']?>" id="remove_state_group_<?=$value['group_id']?>">X</a>
                              </div>
                              <?php } ?>
                              <div class="m-b-20">
                                 <label >Does this <?=$value['type']?> differ by state?</label>
                                 <div class="clearfix"></div>
                                 <label><input type="radio" name="opt_coll[<?=$value['group_id']?>]" class="option_coll option_coll_<?=$value['group_id']?>" value="yes" <?=$selected_y?> data-id="<?=$value['group_id']?>">Yes</label>
                                 <br />
                                 <label><input type="radio" name="opt_coll[<?=$value['group_id']?>]" class="option_coll option_coll_<?=$value['group_id']?>" value="no" <?=$selected_n?> data-id="<?=$value['group_id']?>">No</label>
                                 <br>
                                 <span class="error error_preview" id="error_opt_coll<?=$value['group_id']?>"></span>
                              </div>
                              <div class="coll_div_hidden">
                                 <div id="main_state_div_col_<?=$value['group_id']?>">
                                    <?php if($selected_y == 'checked'){ ?>
                                    <span class="error error_preview" id="error_states"></span>
                                    <div id="innerStateDiv_<?=$value['group_id']?>" class="inner_state_div" data-id="<?=$value['group_id']?>">
                                       <div class="clearfix"></div>
                                       <!-- <hr class="m-t-n " /> -->
                                          <!-- <div class="add_url_treewrap"> -->
                                          <div class="form-group">
                                             <!-- <input type="hidden" name="dynamicFields[<?=$value['group_id']?>]"> -->
                                             <select name= "states[<?=$value['group_id']?>][]" class="se_multiple_select state_select" multiple="multiple" id="stateSelect_<?=$value['group_id']?>" data-id="<?=$value['group_id']?>" >
                                                <?php foreach($allStateRes as $states){
                                                   $has_value = '';
                                                   if(!empty($sel_satates) && in_array($states['id'], $sel_satates)){
                                                     $has_value = 'selected="selected"';
                                                   }
                                                   ?>
                                                <option value="<?=$states['id']?>" <?=$has_value?> ><?=$states['name']?></option>
                                                <?php } ?>
                                             </select>
                                             <label>Select State(s)</label>
                                             <span class="error error_preview" id="error_states_<?=$value['group_id']?>"></span>
                                          </div>
                                          <!-- </div> -->
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <select class="coll_type form-control add_control_<?=$value['group_id']?>" name="coll_type[<?=$value['group_id']?>]" data-old_val="<?=$value['coll_type']?>" id="coll_type_<?=$value['group_id']?>"  data-id="<?=$value['group_id']?>" >
                                    <option value="pdf" <?=$value['coll_type'] == 'pdf' ? 'selected' : ''?>>PDF</option>
                                    <option value="video" <?=$value['coll_type'] == 'video' ? 'selected' : ''?>>Video</option>
                                    <option value="link" <?=$value['coll_type'] == 'link' ? 'selected' : ''?>>Link</option>
                                    <?php if($resource_res['type'] == 'Certificate'){ ?>
                                    <option value="html" <?=$value['coll_type'] == 'html' ? 'selected' : ''?> <?php if($value['user_group'] != 'Member'){ ?> disabled style="display: none;" <?php } ?>>HTML</option>
                                    <?php } ?>
                                 </select>
                                 <label for="coll_type[<?=$value['group_id']?>][]"><?=$value['type']?> Type</label>
                                 <span class="error error_preview" id="error_coll_type<?=$value['group_id']?>"></span>
                              </div>
                              <div class="m-b-25" id="coll_type_control_<?=$value['group_id']?>"  >
                                 <div class="phone-control-wrap">
                                    <div class="phone-addon  text-left file_types_pdf_<?=$value['group_id']?>" <?=in_array($value['coll_type'],array('pdf','video')) && !in_array($value['video_type'],array('Youtube','Wistia')) ? '' : 'style="display:none"'; ?> data-id="<?=$value['group_id']?>">
                                       <a class="add_url_close fs16 text-light-gray remove_video" href="javascript:void(0);" data-id="<?=$value['group_id']?>" id="remove_video_<?=$value['group_id']?>" style="display: none;">X</a>
                                       <div class="custom_drag_control"> <span class="btn btn-action" id="coll_type_control_text_<?=$value['group_id']?>" style="border-radius:0px;"><?=$value['coll_type'] == 'video' ? 'Upload Video' : 'Upload Pdf'?></span>
                                          <input type="hidden" name="file_uploaded[<?=$value['group_id']?>]" value="true" id="file_uploaded<?=$value['group_id']?>" />
                                          <input type="hidden" name="file_uploaded_url[<?=$value['group_id']?>]" value="<?=$value['coll_doc_url']?>" id="file_uploaded_url<?=$value['group_id']?>" />
                                          <input type="file" name="coll_attachements[<?=$value['group_id']?>]"  data-value="<?=$value['coll_doc_url']?>" id="coll_attachements<?=$value['group_id']?>" class="gui-file coll_attachements" data-id="<?=$value['group_id']?>">
                                          <input type="text" class="gui-input" id="coll_gui<?=$value['group_id']?>" placeholder="<?=$value['coll_doc_url']?>">
                                          <span class="error error_preview" id="error_coll_attachements<?=$value['group_id']?>"></span>
                                       </div>
                                    </div>
                                    <?php
                                       $path = $HOST.'\\uploads\\collateral_document\\'.$value['coll_type'].'\\'.$value['coll_doc_url'];
                                       ?>
                                    <div class="phone-control-wrap add_url_treewrap text-left file_types_video_<?=$value['group_id']?>" <?=in_array($value['coll_type'],array('pdf','video')) && in_array($value['video_type'],array('Youtube','Wistia')) ? '' : 'style="display:none"'?>>
                                       <div class="phone-addon ">
                                          <div class="form-group text-left">
                                             <input type="hidden" name="video_type[<?=$value['group_id']?>]" id="video_type<?=$value['group_id']?>" value="<?=$value['video_type']?>">
                                             <input type="text" class="form-control" data-id="<?=$value['group_id']?>" name="url_videos[<?=$value['group_id']?>]" onKeyPress="return ValidateAlpha(event);" value="<?=in_array($value['video_type'],array('Youtube','Wistia')) ? $value['coll_doc_url'] : ''?>" /><label>Embed code</label>
                                             <span class="error error_preview" id="error_url_videos<?=$value['group_id']?>"></span>
                                          </div>
                                       </div>
                                       <div class="phone-addon w-130 v-align-top p-l-30">
                                          <label class="label label-red br-left upload_video" id="upload_video_<?=$value['group_id']?>" data-id="<?=$value['group_id']?>">View Video</label>
                                       </div>
                                    </div>
                                    <div class="phone-addon text-left file_types_link_<?=$value['group_id']?> pn" <?=$value['coll_type'] == 'link' ? '' : 'style="display:none"'?>>
                                       <input type="text" name="link_url[<?=$value['group_id']?>]" value="<?=$value['video_type'] == '' ? $value['coll_doc_url'] : ''?>" class="gui-input form-control" placeholder="URL (www.url.com)">
                                       <span class="error error_preview" id="error_link_url<?=$value['group_id']?>"></span>
                                    </div>
                                    <div class="phone-addon remove_pdf w-90 pr" id="remove_pdf<?=$value['group_id']?>" <?=$value['coll_doc_url'] !== '' && $value['coll_type'] == 'pdf' ? '' : 'style="display:none;"'?>> 
                                       <a href="<?=$path?>" download class="addon_close text-blue m-r-5" id="uploaded_id<?=$value['group_id']?>" ><i class="fa fa-download" aria-hidden="true"></i></a><a class="addon_close pdf_remove fs14 text-light-gray"  href="javascript:void(0);" data-id="<?=$value['group_id']?>" id="pdf_remove<?=$value['group_id']?>">X</a> 
                                    </div>
                                 </div>
                                 <div class="clearfix" id="video_code_type_div_<?=$value['group_id']?>" style="<?=$value['coll_type'] == 'video' ? '' : 'display:none'?>">
                                    <!-- <input type="checkbox" class="video_code_type" name="video_code_type[<?=$value['group_id']?>]" <?=$value['video_type'] == 'Youtube' ? 'checked="checked"' : '';?> id="video_code_type_<?=$value['group_id']?>"><label for="video_code_type_[<?=$value['group_id']?>]">Youtube (Default is Wistia)</label> -->
                                    <p >Embed Video Type</p>
                                    <label><input type="radio" name="video_code_type[<?=$value['group_id']?>]" class="video_code_type" value="Youtube" <?=$value['video_type'] == 'Youtube' ? 'checked' : '';?> data-id="<?=$value['group_id']?>">Youtube Video</label>
                                    <br>
                                    <label class="mn"><input type="radio" name="video_code_type[<?=$value['group_id']?>]" class="video_code_type" value="Wistia" <?=$value['video_type'] == 'Wistia' ? 'checked' : '';?> data-id="<?=$value['group_id']?>">Wistia Video</label>
                                    <div class="clearfix"></div>
                                    <span class="error error_preview" id="error_opt_coll<?=$value['group_id']?>"></span>
                                 </div>
                              </div>
                              <div class="m-b-25">
                                 <textarea class="form-control" name="col_description[<?=$value['group_id']?>]" placeholder="Description" rows="3"><?=$value['description']?></textarea>
                                 <span class="error error_preview" id="error_col_description<?=$value['group_id']?>"></span>
                              </div>
                           </div>
                           <?php if($value['coll_type'] == 'html'){ $cert_type_name = 'html' ?>
                              <div class="col-md-8" id="res_editor_<?=$value['group_id']?>">
                                 <textarea rows="13" class="certificate_mem_description" name="certificate_mem_description[<?=$value['group_id']?>]" id="certificate_mem_description_<?=$value['group_id']?>" data-id="<?=$value['group_id']?>" placeholder="Add">
                                 <?=$value['member_description']?>
                                 </textarea>
                                 <span class="error error_preview" id="error_certificate_mem_description_<?=$value['group_id']?>"></span>
                              </div>
                           <?php }else{ ?>
                              <div class="col-md-8" id="res_editor_<?=$value['group_id']?>" style="display: none;">
                                 <textarea rows="13" class="certificate_mem_description" name="certificate_mem_description[<?=$value['group_id']?>]" id="certificate_mem_description_<?=$value['group_id']?>" data-id="<?=$value['group_id']?>" placeholder="Add">
                                 </textarea>
                                 <span class="error error_preview" id="error_certificate_mem_description_<?=$value['group_id']?>"></span>
                              </div>
                           <?php } ?>
                        </div>
                        <hr class="m-t-0">
                     </div>
                     <?php if(count($sub_resource_res) == $counter) { ?>  
                  </div>
                  <!-- dynamic_coll_tree_div end  -->
               </div>
               <div class="m-b-20 add_collateral_div row">
                  <div class="col-sm-6">
                     <a href="javascript:void(0);" class="btn btn-action" id="add_collateral" <?php //if($counter >= 10) echo 'style="display:none"';?> >+ <?=$value['type']?></a>
                  </div>
                  <div class="col-sm-6 text-right smart_tag_popup_div" <?php if($cert_type_name !== 'html') { ?> style="display: none;" <?php } ?> >
                     <a data-href="smart_tag_popup.php?user_type=member&editortype=html_res_tag" class="btn btn-info btn-outline smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
                  </div>
               </div>
            </div>
            <hr class="m-t-0">
            <?php /*if($value['type'] != 'Certificate'){?>
            <div class="assign-collateral pull-left">
               <p>Assign-collateral</p>
               <p><strong>This collateral will display in member portal on product  details with tab named Collateral</strong></p>
            </div>
            <?php }*/ ?>
         </div>
         <?php } ?>
         <!-- Certificate code  -->
         <?php }elseif($value['type'] == 'id_card'){ ?>
         <!-- ID card code  -->
         <div id="id_card">
            <p class="fs16"><strong class="fw500">ID Card</strong></p>
            <div class="col-sm-10 pn">
               <textarea rows="13" class="" name="card_descrition" id="card_description" placeholder="Add">
                  <?=$value['description']?>
               </textarea>
               <span class="error error_preview" id="error_card_descrition"></span>
               <div class="clearfix m-b-30 m-t-30">
                  <a href="javascript:void(0);" data-href="tmpl/view_id_card.inc.php" class="btn btn-action" id="preview_id_card">Preview ID Card</a>
               </div>
            </div>
            <div class="col-sm-2" id="editor_tag_wrap">
               <div class="required-tab text-center ">
                  <div class="editor_tag_wrap" >
                     <div class="tag_head">
                        <h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4>
                     </div>
                     <div class="editor_tag_wrap_inner" style="max-height: 452px;">
                        <div><label>[[MemberName]]</label></div>
                        <div><label>[[MemberID]]</label></div>
                        <div><label>[[BenefitTier]]</label></div>
                        <div><label>[[EffectiveDate]]</label></div>
                        <div><label>[[DependentName]]</label></div>
                        <div><label>[[GroupCode]]</label></div>
                        <div><label>[[PlanCode1]]</label></div>
                        <div><label>[[PlanCode2]]</label></div>
                        <div><label>[[PlanCode3]]</label></div>
                        <div><label>[[PlanCode4]]</label></div>
                        <div><label>[[PlanCode5]]</label></div>
                        <div><label>[[PlanCode6]]</label></div>
                        <div><label>[[PlanCode7]]</label></div>
                        <div><label>[[PlanCode8]]</label></div>
                        <div><label>[[PlanCode9]]</label></div>
                        <div><label>[[PlanCode10]]</label></div>
                        <div><label>[[PlanCode11]]</label></div>
                        <div><label>[[PlanCode12]]</label></div>
                        <div><label>[[PlanCode13]]</label></div>
                        <div><label>[[PlanCode14]]</label></div>
                        <div><label>[[PlanCode15]]</label></div>
                        <div><label>[[PlanCode16]]</label></div>
                        <div><label>[[PlanCode17]]</label></div>
                        <div><label>[[PlanCode18]]</label></div>
                        <div><label>[[PlanCode19]]</label></div>
                        <div><label>[[PlanCode20]]</label></div>
                        <div><label>[[ProductName]]</label></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="clearfix">
            </div>
         </div>
         <hr class="m-t-0">
         <!-- ID card code  -->
         <?php } ?>
         <?php
            } //foreach end
            }else{ // resources end
            ?>
         <!-- resource edit end -->
         <!-- Collateral code start -->
         <div id="collateral_code" style="display:none">
            <div class="clearfix"></div>
            <p class="fs16 m-t-20"><strong class="fw500" id="coll_certi_id">Collateral Information</strong></p>
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group ">
                     <select  class="form-control coll_user_type" name="coll_user_type" id="coll_user_type">
                        <option hidden selected="selected"></option>
                        <option value="Agent">Agent</option>
                        <option value="Member">Member</option>
                        <option value="Group">Group</option>
                     </select>
                     <label>User Group </label>
                     <span class="error error_preview" id="error_coll_user_type"></span>
                  </div>
               </div>
               
            </div>
            <div class="coll_div_hidden">
               <div id="main_div_col">
               </div>
               <div class="m-b-20 add_collateral_div row" style="display:none">
                  <div class="col-sm-6">
                     <a href="javascript:void(0);" class="btn btn-action" id="add_collateral">+ Collateral</a>
                  </div>
               <div class="col-sm-6 text-right smart_tag_popup_div" style="display: none;">
                  <a data-href="smart_tag_popup.php?user_type=member&editortype=html_res_tag" class="btn btn-info btn-outline smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
               </div>
               </div>
            </div>
         </div>
         <!-- Collateral code end -->
         <!-- ID Card section start --> 
         <div id="id_card" style="display:none">
            <p class="fs16"><strong class="fw500">ID Card</strong></p>
            <div class="row">
            <div class="col-sm-9">
               <textarea rows="13" class="" name="card_descrition" id="card_description" placeholder="Add HTML...">
                      <?=$default_id_card_template?>
              </textarea>
               <span class="error error_preview" id="error_card_descrition"></span>
               <div class="clearfix m-b-30 m-t-30">
                  <a href="javascript:void(0);" data-href="tmpl/view_id_card.inc.php" class="btn btn-action" id="preview_id_card">Preview ID Card</a>
               </div>
            </div>
            <div class="col-sm-3" id="editor_tag_wrap">
               <div class="text-center ">
                  <div class="editor_tag_wrap">
                     <div class="tag_head">
                        <h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4>
                     </div>
                     <div class="editor_tag_wrap_inner" style="max-height:350px;">
                        <div><label>[[MemberName]]</label></div>
                        <div><label>[[MemberID]]</label></div>
                        <div><label>[[BenefitTier]]</label></div>
                        <div><label>[[EffectiveDate]]</label></div>
                        <div><label>[[DependentName]]</label></div>
                        <div><label>[[GroupCode]]</label></div>
                        <div><label>[[PlanCode1]]</label></div>
                        <div><label>[[PlanCode2]]</label></div>
                        <div><label>[[PlanCode3]]</label></div>
                        <div><label>[[PlanCode4]]</label></div>
                        <div><label>[[PlanCode5]]</label></div>
                        <div><label>[[PlanCode6]]</label></div>
                        <div><label>[[PlanCode7]]</label></div>
                        <div><label>[[PlanCode8]]</label></div>
                        <div><label>[[PlanCode9]]</label></div>
                        <div><label>[[PlanCode10]]</label></div>
                        <div><label>[[PlanCode11]]</label></div>
                        <div><label>[[PlanCode12]]</label></div>
                        <div><label>[[PlanCode13]]</label></div>
                        <div><label>[[PlanCode14]]</label></div>
                        <div><label>[[PlanCode15]]</label></div>
                        <div><label>[[PlanCode16]]</label></div>
                        <div><label>[[PlanCode17]]</label></div>
                        <div><label>[[PlanCode18]]</label></div>
                        <div><label>[[PlanCode19]]</label></div>
                        <div><label>[[PlanCode20]]</label></div>
                        <div><label>[[ProductName]]</label></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
            <div class="clearfix"></div>
         </div>
         <!-- ID Card section end -->
         <hr / class="m-t-0">
         <?php } ?>
         <div class="clearfix">
            <!-- <div class="assign-collateral pull-left" style="display:none">
               <p>Assign-collateral</p>
               <p><strong>This collateral will display in member portal on product  details with tab named Collateral</strong></p>
               </div> -->
            <!-- static fields end -->
            <div class="step_btn_wrap pull-right    resources_button">
               <input type="button" name="btn_save" id="btn_save" class="btn btn-action" value="Save Resource">
               <input type="button" name="btn_cacel" id="" class="btn red-link" value="Cancel" onclick="window.location='products_resource.php'">
            </div>
         </div>
         <input type="hidden" name="ajax_file" id="ajax_file" value="">
         <input type="hidden" name="ajax_file_id" id="ajax_file_id" value="">
         <input type="hidden" name="tmp_filename" id="tmp_filename" value="">
   </form>
   </div>
   <div id="dynamic_coll_div" style="display: none;">
      <!-- dynamic_coll_tree_div start  -->
      <!-- <span class="error error_preview" id="error_col"></span> -->
      <div id="innerCollDiv_~number~" class="inner_coll_div" data-id="~number~">
         <input type="hidden" name="dyncollFields[~number~]">
         
         <!-- <p class="fs16 m-b-15"><strong class="fw500" id="idcollateral~number~">Collateral </strong></p> -->
        
         <div class="row">
            <div class="col-md-4">
               <div class="pull-left">
                  <p class="fs16 m-b-15"><strong class="fw500" id="idcollateral~number~">Collateral </strong></p>
               </div>
               <div class="pull-right">
                  <a class="fs16 text-light-gray remove_state_group" href="javascript:void(0);" data-id="~number~" id="remove_state_group_~number~" style="display:none;">X</a>
               </div>
               <div class="clearfix"></div>
               <div class="m-b-20">
                  <label id="idcolltext~number~">Does this Collateral differ by state?</label>
                  <div class="clearfix"></div>
                  <label><input type="radio" name="opt_coll[~number~]" class="option_coll option_coll_~number~" value="yes" data-id="~number~">Yes</label>
                  <br />
                  <label><input type="radio" name="opt_coll[~number~]" class="option_coll option_coll_~number~" value="no" data-id="~number~">No</label>
                  <br>
                  <span class="error error_preview" id="error_opt_coll~number~"></span>
               </div>
               <div class="coll_div_hidden">
                  <div id="main_state_div_col_~number~">
                  </div>
               </div>
               <div class="form-group">
                  <select class="coll_type add_control_~number~" name="coll_type[~number~]" id="coll_type_~number~"  data-id="~number~" >
                     <option selected="selected" ></option>
                     <option value="pdf">PDF</option>
                     <option value="video">Video</option>
                     <option value="link">Link</option>
                     <option value="html">HTML</option>
                  </select>
                  <label for="coll_type[~number~][]" id="lbl_coll_type_~number~" class="lbl_coll_type">Collateral Type</label>
                  <span class="error error_preview" id="error_coll_type~number~"></span>
               </div>
               <div class="m-b-25 " id="coll_type_control_~number~" style="display:none">
                  <div class="phone-control-wrap">
                     <div class="phone-addon  text-left file_types_pdf_~number~" data-id="~number~">
                        <a class="add_url_close fs16 text-light-gray remove_video" href="javascript:void(0);" data-id="~number~" id="remove_video_~number~" style="display: none;">X</a>
                        <div class="custom_drag_control">
                           <span class="btn btn-action" id="coll_type_control_text_~number~" style="border-radius:0px;">Upload Pdf</span>
                           <input type="hidden" name="file_uploaded[~number~]" value="false" id="file_uploaded~number~" />
                           <input type="file" name="coll_attachements[~number~]" id="coll_attachements~number~" class="gui-file coll_attachements" data-id="~number~">
                           <input type="text" class="gui-input" id="coll_gui~number~" placeholder="Attatch Document">
                           <span class="error error_preview" id="error_coll_attachements~number~"></span>
                           <!-- <input type="text" class="gui-input" placeholder="URL (WWW.url.com)"> -->
                        </div>
                     </div>
                     <div class="phone-control-wrap   add_url_treewrap text-left file_types_video_~number~" style="display:none">
                        <div class="phone-addon ">
                           <div class="form-group text-left">
                              <input type="hidden" name="video_type[~number~]" id="video_type~number~" value="url">
                              <input type="text" class="form-control" data-id="~number~" name="url_videos[~number~]" /><label>Embed code</label>
                              <span class="error error_preview" id="error_url_videos~number~"></span>
                           </div>
                        </div>
                        <div class="phone-addon w-130 v-align-top p-l-30">
                           <label class="label label-red br-left upload_video" id="upload_video_~number~" data-id="~number~">View Video</label>
                        </div>
                     </div>
                     <div class="m-b-25" id="video_code_type_div_~number~" style="display:none">
                        <p >Embed Video Type</p>
                        <div class="clearfix"></div>
                        <label><input type="radio" name="video_code_type[~number~]" class="video_code_type" value="Youtube"  data-id="~number~>">Youtube Video</label>
                        <br>
                        <label class="mn"><input type="radio" name="video_code_type[~number~]" class="video_code_type" value="Wistia" data-id="~number~">Wistia Video</label>
                        <div class="clearfix"></div>
                        <span class="error error_preview" id="error_video_code_type~number~"></span>
                     </div>
                     <!-- <p>Enter Youtube or wistia embed code</p> -->
                     <!-- </div> -->
                     <div class="phone-addon text-left file_types_link_~number~ pn" style="display:none">
                        <input type="text" name="link_url[~number~]" class="gui-input form-control" placeholder="URL (www.url.com)">
                        <span class="error error_preview" id="error_link_url~number~"></span>
                     </div>
                     <!-- </div> -->
                     <div class="phone-addon remove_pdf w-90 pr" id="remove_pdf~number~" style="display:none;">
                        <a href="javascript:void(0);" download class="addon_close text-blue m-r-5" id="uploaded_id~number~" ><i class="fa fa-download " aria-hidden="true"></i></a>
                        <a class="addon_close pdf_remove fs14 text-light-gray"  href="javascript:void(0);" data-id="~number~" id="pdf_remove~number~">X</a>
                     </div>
                  </div>
               </div>
               <div class="m-b-25">
                  <textarea class="form-control" name="col_description[~number~]" placeholder="Description" rows="3"></textarea>
                  <span class="error error_preview" id="error_col_description~number~"></span>
               </div>
            </div>
            <div class="col-md-8 res_mem_editor" id="res_editor_~number~" style="display:none;">
               <textarea rows="13" class="m-t-20 certificate_mem_description" name="certificate_mem_description[~number~]" id="certificate_mem_description_~number~" data-id="~number~" placeholder="Add">
               </textarea>
               <span class="error error_preview" id="error_certificate_mem_description_~number~"></span>
            </div>
         </div>
         <hr class="m-t-0">
      </div>
      <!-- dynamic_coll_tree_div end  -->
   </div>
   <div id="dynamic_state_div" style="display: none">
      <!--dynamic_state_div -->
      <span class="error error_preview" id="error_states"></span>
      <div id="innerStateDiv_~number~" class="inner_state_div" data-id="~number~">
         <div class="clearfix"></div>
            <!-- <hr class="m-t-n " /> -->
               <!-- <div class="add_url_treewrap"> -->
               <div class="form-group ">
               <select name= "states[~number~][]" class="state_select" multiple="multiple" id="stateSelect_~number~" data-id="~number~" >
                     <!-- <option value="111111">Select All</option> -->
                     <?php foreach($allStateRes as $states){?>
                     <option value="<?=$states['id']?>"><?=$states['name']?></option>
                     <?php } ?>
                  </select>
                  <label for="states[~number~][]">Select State(s)</label>
                  <span class="error error_preview" id="error_states_~number~"></span>
               </div>
      </div>
   </div>
   <!--dynamic_state_div -->
   <!-- <div class="dynamic_tree_div"> -->
   <div id="dynamic_tree_div" style="display: none;">
      <div id="inner_tree_div_~number~" class="inner_tree_div" data-stateID="" data-id="~number~">
         <div class="single_row">
            <div class="phone-control-wrap">
               <!-- <div class="phone-addon w-130">
                  <label class="label label-blue topClass_~number~ br-bottom">~stateName~</label>
                  </div> -->
               <div class="rightClass_~number~"></div>
            </div>
         </div>
      </div>
   </div>
   <div id="dynamic_url_div" style="display:none">
      <div data-id="~number~">
         <div class="phone-addon w-130">
            <label class="label label-red br-right">Upload Document</label>
         </div>
         <div class="phone-addon">
            <div class="form-group  mn text-left">
               <input type="text" name="url_states[~number~]" class="form-control" data-id="~number~" /><label>URL (www.url.com)</label>
               <span class="error error_preview" id="error_url_states~number~"></span>
            </div>
         </div>
      </div>
   </div>
   <div style="display:none">
      <div id="examplePageColorbox">
         <div class="panel panel-default panel-block panel-shadowless mn">
            <div class="panel-body" id="examplePageBody"></div>
         </div>
      </div>
   </div>
   <div style="display:none">
      <div id="view_video">
         <div class="panel panel-default panel-block panel-shadowless mn">
            <div class="panel-heading br-b">
               <div class="panel-title">
                  <p class="fs18 mn">&nbsp;</p>
               </div>
            </div>
            <div id="viewPageBody" style="line-height: 0px;">
            </div>
            <div  id="copyPageBodyWistia" style="display:none">
               <script src="//fast.wistia.com/embed/medias/~code~.jsonp" async></script>
               <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
               <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
               <div class="wistia_embed wistia_async_~code~ seo=false videoFoam=true" style="height:100%;width:100%">&nbsp;</div></div></div>
            </div>
            <div id="copyPageBodyTube" style="display:none" >
               <iframe  width="100%" height="515" src="https://www.youtube.com/embed/~code~" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
         </div>
      </div>
   </div>
   <div id="file_progress" style="display:none;">
      <div class="progress">
         <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            0%
         </div>
      </div>
   </div>
</div>
<iframe id="tmp_content_iframe" style="display: none;"></iframe>
<script type="text/javascript">
   $is_ckeditor_member = $is_ckeditor = $is_html_editor = false;
   $(document).ready(function() {
   $(".editor_tag_wrap_inner").mCustomScrollbar({
      theme:"dark",
      scrollbarPosition: "outside"
   });
   <?php
      if((!empty($resource_res) && $resource_res['type'] == 'id_card') || empty($_GET['resource_id'])){ ?>
         initCKEditor("card_description");
         $is_ckeditor = true;
   <?php 
      }
   ?>
   var temp_resources_type = $('#resources_type').val();
   var temp_coll_user_type = $('#coll_user_type').val();
   <?php if((!empty($resource_res) && $resource_res['type'] == 'Certificate') && $resource_res['user_group'] == 'Member'){
         ?>
         var temp_resources_type = $('#resources_type').val();
         var temp_coll_user_type = $('#coll_user_type').val();   
         if(temp_resources_type == 'Certificate' && temp_coll_user_type == 'Member'){
            $('.certificate_mem_description').each(function(index){
               var dataId = $(this).attr('data-id');
               if(dataId !== '~number~'){
                  var tempId = $(this).attr('data-id');
                  initCKEditor("certificate_mem_description_"+tempId,false,'200px');
               }
            });
            $is_ckeditor_member = $is_html_editor = true;
         }   
   <?php } ?>

   $('#resources_form').ajaxForm({
      xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                  var percentComplete = (evt.loaded / evt.total) * 100;
                  $val = $("#ajax_file").val();
                  if($val !== '')
                    $("#file_progress").show();
                  else 
                    $("#ajax_loader").show();
                  $(".progress-bar").css({"width":percentComplete+"%"}).text(Math.round(percentComplete)+"%");
              }
        },false);
        return xhr;
      },
      beforeSend: function(xhr) {
          $xhr=xhr;
         if($is_ckeditor){
            $("#card_description").val(CKEDITOR.instances.card_description.getData());
         }
      },
      beforeSubmit:function(arr, $form, options){
      },
      url:"<?= $ADMIN_HOST ?>/ajax_add_resources.php",
      cache: false,
      contentType: false,
      processData: false,
      // data: form_data,
      method: 'POST',
      dataType: 'json',
      success: function(res) {
        if (res.status == 'success') {
          $(".error").html('').hide();
          if(res.file_name !== ''){
            $("#uploaded_id"+$("#ajax_file_id").val()).attr('href','<?=$HOST.'/uploads/collateral_document/tmp_file/'?>'+res.file_name);
            $("#ajax_file_id").val("");
            $("#tmp_filename").val(res.file_name);
            $("#remove_pdf"+$("#ajax_file_id").val()).show();
            if(res.message === "form_submited"){
                if(res.form === "form_added")
                  setNotifySuccess("Resources Added Successfully!");
                else if(res.form === "form_updated")
                  setNotifySuccess("Resources updated Successfully!");
              setTimeout(function(){ 
                window.location.href = 'products_resource.php';
              }, 1000);  
            }
          }
          $("#file_progress").hide();
          $("#ajax_loader").hide();
          $("#ajax_file").val("");
        } else if (res.status == 'fail') {
          $("#ajax_loader").hide();
          $("#file_progress").hide();
          $("#remove_pdf"+$("#ajax_file_id").val()).hide();
          $.each(res.errors, function (index, error) {
            $('#error_' + index).html(error).show();
          });
        }
      }
    });
   $(".smart_tag_popup").on('click',function(){
      $href = $(this).attr('data-href');
      var not_win = window.open($href, "myWindow", "width=768,height=600");
      if(not_win.closed) {  
        alert('closed');  
      } 
    });
    <?php if(!empty($resource_res)) {?>
    // $(".state_select").multipleSelect('refresh');
   
    var sel_state = [];
    var $ids = [];
    $(".state_select").each(function(e){
      if($(this).val() !== null && $(this).attr('data-id')!=='~number~')
       {
            $.merge(sel_state,$(this).val());
            $.merge($ids,$(this).attr('data-id'));
        }
    });
   
    if(sel_state !== ''){
          for(i=0;i<$ids.length;i++){
            if($("#certificate_code").hasClass('certificate_class')){
              for(j=0;j<sel_state.length;j++){
                $selected = $("#stateSelect_"+$ids[i]+" option[value='"+sel_state[j]+"']").is(":selected");
                if($selected === true)
                  {
                    $("#stateSelect_"+$ids[i]+" option[value='"+sel_state[j]+"']").prop("disabled",false); 
                  }else{
                    $("#stateSelect_"+$ids[i]+" option[value='"+sel_state[j]+"']").prop("disabled",true); 
                  }
              }
            }
              $("#resources_form .state_select").multipleSelect('refresh');
          }
        }
      <?php } ?>
    // $('.add_fee_window').colorbox({iframe: true, width: '800x', height: '660px'});
    $("#effective_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
      //   startDate:new Date()
    }).on("changeDate", function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $('#termination_date').datepicker('setStartDate', minDate);
    });
    
    $("#termination_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true
    });
   //  $('.summernote').summernote({
   //   toolbar: $SUMMERNOTE_TOOLBAR,
   //   disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
   //   focus: true, // set focus to editable area after initializing summernote
   //   height:480,
   //   callbacks: {
   //     onImageUpload: function(image) {
   //       editor = $(this);
   //       uploadImageContent(image[0], editor);
   //     },
   //     onMediaDelete : function(target) {
   //         deleteImage(target[0].src);
   //         target.remove();
   //     }
   //   }
   // });
   
   $('#src_products').multipleSelect({
      width: '100%'
    });
   });
   //preview id card start
   $(document).off("click","#preview_id_card");
   $(document).on("click","#preview_id_card",function(e){
      if($is_ckeditor){
         $("#card_description").val(CKEDITOR.instances.card_description.getData());
      }
    // $("#card_description").val(CKEDITOR.instances.card_description.getData());  
    $("#examplePageBody").html($("#card_description").val());
    $.colorbox({
      inline: true , 
      href: '#examplePageColorbox',
      width: '900px', 
      height: '550px',
    });
   });
   //preview id card end
   // submit form start
   $(document).off("click", "#btn_save");
   $(document).on("click", "#btn_save", function(e){
      $(".error").html('').hide();
      if($is_ckeditor){
         $("#card_description").val(CKEDITOR.instances.card_description.getData());
         $("#tmp_content_iframe").contents().find("body").html(CKEDITOR.instances.card_description.getData());
         if($("#tmp_content_iframe").contents().find('script').length > 0 || $("#tmp_content_iframe").contents().find('link').length > 0) {
            swal({
               title:"Error",
               text: "HTML contains JavaScript/CSS and cannot be saved. Please remove any JavaScript/CSS and try again.",
               confirmButtonText: "Ok",
            }).then(function () {

            });
            return false;
         }
      }
      if($is_html_editor){
         $is_exit = false;
         $('.certificate_mem_description').each(function(index){
            var dataId = $(this).attr('data-id');
            if(dataId !== '~number~' && $("#coll_type_"+dataId).val() == 'html'){
               console.log(dataId);
               var tempId = $(this).attr('id');
               $(this).val(CKEDITOR.instances[tempId].getData());
               if($(this).val().indexOf('<script') != -1 || $(this).val().indexOf('<link') != -1 ){
                  swal({
                     title:"Error",
                     text: "HTML contains JavaScript/CSS and cannot be saved. Please remove any JavaScript/CSS and try again.",
                     confirmButtonText: "Ok",
                  }).then(function () {
                  });
                  $is_exit = true;
                  return false;
               }
            }
         });
         if($is_exit){
            return false;
         }
      }
      $("#resources_form").submit();
   });
   
   // submit form end
   
   //resource type start
   $(document).off("change","#resources_type");
   $(document).on("change","#resources_type",function(e){
      e.preventDefault();
      var val = $(this).val();
      $("#display_counter").val(0);
      // $(".assign-collateral").hide();
      $("#coll_user_type").val("").selectpicker('refresh');
    if(val === "Certificate"){
      $("#coll_certi_id").text("Certificate Information");
      $("#add_collateral").text("+ Certificate");
      $(".lbl_coll_type").text("Certificate Type");
        $("#main_div_col").html("");
        $("#main_state_div").html("");
        $("#id_card").hide();
        $("#collateral_code").show();
        $(".coll_type option[value='html']").show();
        // $(".assign-collateral").show();
    }else if(val === 'Collateral'){
      $("#coll_certi_id").text("Collateral Information");
      $("#add_collateral").text("+ Collateral");
      $(".lbl_coll_type").text("Collateral Type");
      $("#main_div_col").html("");
      $("#main_state_div").html("");
      $("#id_card").hide();
      $("#collateral_code").show();
      $(".coll_type option[value='html']").hide();
      // $(".assign-collateral").show();
    }else if( val ===  'id_card'){
      
      $("#id_card").show();
      $("#collateral_code").hide();
      $("#certificate_code").hide();
      $("#certificate_main").html("");
      $("#main_div_col").html("");
      // initCKEditor();
    }
   });
   //resource type end

   //certificate code start
   $(document).off('change', '.opt_certificate');
    $(document).on('change', '.opt_certificate', function(e){
      var val = $(this).val();
      var id = $(this).data('id');
      if(val === "yes"){
        var sel_state = [];
        $(".state_select").each(function(){
          if($(this).val() !== null)
            $.merge(sel_state,$(this).val());
        });
        loadStateDiv("main_state_div_"+id,'inner_state_div',id,sel_state);
        $(".state_div_hidden").show();
      }else if(val === "no"){
        $val = $("#stateSelect_"+$(this).attr('data-id')).val();
        if($val!=='' && $val!==null && $val !== undefined){
          for(i=0;i<=$val.length;i++)
          {
            $('.state_select').each(function(e){
              $("#"+$(this).attr("id")+" option[value='"+$val[i]+"']").prop("disabled",false); 
              $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
            });
          }
        }
        $("#main_state_div_"+id).html("");
      }
   });
   
   $(document).off('change', '.option_coll');
   $(document).on('change', '.option_coll', function(e){
    var id = $(this).data('id');
    var val = $(this).val();
    if(val === "yes"){
      loadStateDiv("main_state_div_col_"+id,'inner_coll_div',id);      
      $(".state_div_hidden").show();
    }else if(val === "no"){
      $(".state_div_hidden").hide();
      $("#main_state_div_col_"+id).html("");
      // $("#add_state_group").hide();
    }
   });
   //certificate code start
   
   //add remove state group start
   // $(document).off('click', '#add_state_group');
   //   $(document).on('click', '#add_state_group', function(e){
   //     // e.preventDefault();
   //     $count = $("#resources_form .inner_certi_div").length;
   //     $number=$count+1;
   //     alert($number);
   //     loadCertidiv('',$number);
   //     if($number > 1){
   //       $("#remove_state_group_-"+$number).show();
   //     }
   // });
   $(document).off('click', '.remove_state_group');
   $(document).on('click', '.remove_state_group', function(e){
    // e.preventDefault();
    $val = $("#stateSelect_"+$(this).attr('data-id')).val();
    if($val!=='' && $val!==null && $val !== undefined){
      for(i=0;i<=$val.length;i++)
      {
        $('.state_select').each(function(e){
          $("#"+$(this).attr("id")+" option[value='"+$val[i]+"']").prop("disabled",false); 
          $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
        });
      }
    }
    // $("#innerCertiDiv"+$(this).attr('data-id')).remove();
    $("#innerCollDiv_"+$(this).attr('data-id')).remove();
    $count = $(".inner_coll_div").length;
    if($count < 11){
      $("#add_collateral").show();
    }
   });
   //add remove state group end
   
   //collateral type start
   $(document).off("change",".coll_type");
   $(document).on("change",".coll_type",function(e){
    e.stopPropagation();
    var $id = $(this).data('id');
    var val = $(this).val();
    var old_val = $(this).attr('data-old_val');
    $("#coll_type_control_"+$id).show();
    $("#coll_type_control_text_"+$id).show();
    $("#video_type"+$id).val('url');
    if($("#file_uploaded"+$id).val() == 'true'){
        swal({
        text: '<br>Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
      }).then(function() {
        $("#coll_attachements"+$id).val("");
        $("#coll_gui"+$id).attr("placeholder","");
        $("#remove_pdf"+$id).hide();
        $("input[name='url_videos["+$id+"]']").val("");
        $("#file_uploaded"+$id).val("false");
        $("#ajax_loader").show();
        change_coll_type(val,$id);
        ajax_remove_file($id,$("#tmp_filename").val(),$("#coll_type_"+$id).val());
      }, function(dismiss) {
         $("#coll_type_"+$id).val(old_val);
         $("#coll_type_"+$id).selectpicker('refresh');
      });
    }else{
      $(this).attr('data-old_val',val);
      $("#video_type"+$id).val('url');
        change_coll_type(val,$id);
    }
    $('.coll_type').each(function(e){
           var certi_type = $(this).val();
           if(certi_type === "html"){
            $(".smart_tag_popup_div").show();
           }
      });
   });
   
   $(document).off("change","#coll_user_type");
   $(document).on("change","#coll_user_type",function(e){
    e.stopPropagation();
    $(".add_collateral_div").show();
    // $(".assign-collateral").show();
    $count = $(".inner_coll_div").length;
    if($count === 1){
      loadCollDiv();
    }
    var res_type = $('#resources_type').val();
    var user_grp = $('#coll_user_type').val();
       
    if(res_type === 'Certificate' && user_grp === 'Member'){
        $(".coll_type option[value='html']").prop("disabled",false);
        $(".coll_type option[value='html']").show();
        $(".coll_type").selectpicker('refresh');
        $(".coll_type").val('').selectpicker('destroy');
     }else if(res_type === 'Certificate'){
         $('.certificate_mem_description').each(function(index){
            var dataId = $(this).attr('data-id');
            console.log($("#coll_type_"+dataId).val());
            if(dataId !== '~number~' && $("#coll_type_"+dataId).val() == 'html'){
               $("#res_editor_"+dataId).hide();
            }
         });
         $(".coll_type option[value='html']").prop("disabled",true);
         $(".coll_type option[value='html']").hide();
         $(".coll_type").val('').selectpicker('destroy');
     }else{
      $('.certificate_mem_description').each(function(index){
            var dataId = $(this).attr('data-id');
            if(dataId !== '~number~' && $("#coll_type_"+dataId).val() == 'html'){
               $("#res_editor_"+dataId).hide();
               console.log(dataId);
            }
      });
     }
     $('select.coll_type').each(function(index,val){
            var id = $(this).attr('data-id');
            $('#coll_type_control_'+id).hide();
            $("input[name='url_videos["+id+"]']").val("");
            $("input[name='link_url["+id+"]']").val("");
            $("input[name='coll_attachements["+id+"]']").val("");
            $("textarea[name='col_description["+id+"]']").html("");
            $("#coll_gui"+id).attr("placeholder","");
            $("input[name='video_code_type["+id+"]']").not('.js-switch').uniform();
            $("#file_uploaded"+id).val("false");
    });
     common_select();
   });
   
   $(document).off("click","#add_collateral");
   $(document).on("click","#add_collateral",function(e){
    e.preventDefault();
    $count = $(".inner_coll_div").length;
    if($count > 1){
      loadCollDiv();
    }
   //  if($count==10){
   //    $(this).hide();
   //  }
    if($count > 1){
        $("#remove_state_group_-"+$count).show();
      }
      var res_type = $('#resources_type').val();
      var user_grp = $('#coll_user_type').val();
      if(res_type === 'Certificate' && user_grp === 'Member'){
         $(".coll_type option[value='html']").prop("disabled",false);
         $(".coll_type option[value='html']").show();
      }else{
         $(".coll_type option[value='html']").prop("disabled",true);
         $(".coll_type option[value='html']").hide();
      }
      $is_ckeditor_member = false;
   });
   
   $(document).off('click',".upload_video");
   $(document).on('click',".upload_video",function(e){
    console.clear();
    var $id = $(this).data('id');
    $("#viewPageBody").html("");
    var str ='';
    $("#copyPageBodyWistia").hide();
    $("#copyPageBodyTube").hide();
    if($("input[name='video_code_type["+$id+"]']:checked").val() == 'Youtube'){
      str = $("#copyPageBodyTube").html();
    }else{
      str = $("#copyPageBodyWistia").html();
      $("#copyPageBodyWistia").hide();
      $(".wistia_embed").children("iframe").remove();
    }
    str = str.replace(/~code~/g,$("input[name='url_videos["+$id+"]']").val());
    $("#viewPageBody").html(str);
    $.colorbox({
      inline: true ,
      href: '#view_video',
      width: '900px', 
      height: '600px',
      onClosed : function(){
        console.clear();
      }
    });
   });
   
   // $(document).off('click',".remove_video");
   // $(document).on('click',".remove_video",function(e){
   //   var $id = $(this).data('id');
   //   alert($id);
   //   $("#video_type"+$id).val('url');
   //   $("#file_uploaded"+$id).val("false");
   //   $(".file_types_pdf_"+$id).hide();
   //   $(".file_types_video_"+$id).show();
   //   $("#coll_attachements"+$id).val("");
   //   $("#coll_gui"+$id).attr("placeholder","");
   // });
   
   $(document).off("change",".coll_attachements");
   $(document).on("change",".coll_attachements",function(e){
    $count = $(this).data('id');
    if($(this).val() !== '' && $("#coll_type_"+$count).val() === 'pdf' || $("#coll_type_"+$count).val() === 'video'){
      $("#remove_pdf"+$count).show();
      $file = $("#coll_attachements"+$count).val();
      $("#ajax_file").val("file");
      $("#ajax_file_id").val($count);
      $("#file_uploaded"+$count).val("true");
      $("#resources_form").submit();    
    }else{
      $("#remove_pdf"+$count).hide();
      $("#file_uploaded"+$count).val("false");
    }
   });
   
   $(document).off("click",".pdf_remove");
   $(document).on("click",".pdf_remove",function(){
    $count = $(this).data('id');
       
    // var id = $(this).attr('data-id');
    swal({
        text: '<br>Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function() {
      $("#file_uploaded"+$count).val("false");
      $("#coll_attachements"+$count).val("");      
      $("#remove_pdf"+$count).hide();
      $("#ajax_loader").show();
      var fname = $("#coll_gui"+$count).attr("placeholder");
      var filetype = $("#coll_type_"+$count).val();
      ajax_remove_file($count,fname,filetype);
      $("#coll_gui"+$count).attr("placeholder","");
    }, function(dismiss) {
   
    });
   });
   //collateral type end
   
   //load dynamic collateral start
   loadCollDiv = function(div='',$type){
      $count = $(".inner_coll_div").length;
      $number=$count;
      $neg_number = $number * -1;
     
      html = $('#dynamic_coll_div').html();
      $('#main_div_col').append(html.replace(/~number~/g, $neg_number));
      $("input[type='radio']").uniform();
      $('.add_control_'+$neg_number).addClass('form-control');
      if($("#coll_user_type").val() !== 'Certificate'){
         $('.add_control_'+$neg_number).selectpicker({ 
          container: 'body', 
          style:'btn-select',
          noneSelectedText: '',
          dropupAuto:false,
         });
      }      
        // $(".remove_state_group").hide();
        $("#idcollateral"+$neg_number).text($("#resources_type").val()+" "+$number);
        $("#idcolltext"+$neg_number).text("Does this "+$("#resources_type").val()+" differ by state?");
        $("#lbl_coll_type_"+$neg_number).text($("#resources_type").val()+" Type");
    }
   //load dynamic collateral end
   
   //load dynamic certificate start
   loadCertidiv = function(div='',$type){
      // $count = $(".inner_certi_div").length;
      $number=$type;
      $neg_number = $number * -1;
      html = $('#dynaminc_certificate').html();
      $('#certificate_main').append(html.replace(/~number~/g, $neg_number));
      $("input[type='radio']").uniform();
      $('.add_control_'+$neg_number).addClass('form-control');
        $('.add_control_'+$neg_number).selectpicker({ 
          container: 'body', 
          style:'btn-select',
          noneSelectedText: '',
          dropupAuto:false,
        });
        // $(".remove_state_group").hide();
        $("#idcertificate"+$neg_number).text("Certificate Information "+$number);
    }
   //load dynamic certificate end
   
   // load dynamic state start
   loadStateDiv = function(div='',c_class='',id='',arr=''){
      $neg_number=$number=$count = id;
      html = $('#dynamic_state_div').html();
      $('#'+div).append(html.replace(/~number~/g, $neg_number));
        if(arr !== ''){
            for(i=0;i<arr.length;i++){
              $("#stateSelect_"+$neg_number+" option[value='"+arr[i]+"']").attr("disabled",true);
            }
        }
        $("#resources_form #stateSelect_"+$neg_number).addClass('se_multiple_select');
        $("#resources_form #stateSelect_"+$neg_number).multipleSelect('refresh');
   
      $('#stateSelect_'+$neg_number).multipleSelect('refresh').on('select2:select', function (e) {
        e.stopPropagation();
        $dataID=$(this).attr('data-id');
        $stateName=e.params.data.text;
        $stateID = e.params.data.id;
        var $id = $(this).attr('id');
        if($dataID > 0)
          {
            $str_type ='old';
          }else{
            $str_type ='new';
          }
      //   if($stateID === '111111'){
      //   $('#resources_form #'+$id+' > option').not(":disabled").prop("selected",true);
      //   $sd_val = $("#"+$id).val();
      //     $count = 0;
      //     $('.state_select').each(function(e){
      //     $count++;
      //     for(i=0;i<$sd_val.length;i++){
           
      //       $("#"+$(this).attr("id")+" option[value='"+$sd_val[i]+"']").prop("disabled",true); 
      //       $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
      //       $("#"+$id+" option[value='"+$sd_val[i]+"']").prop("disabled",false);
      //       $("#resources_form #"+$id).multipleSelect('refresh');
      //     }
      //     });
      //   return false;
      // }
        
        $id = $(this).attr('id');
        if($("#certificate_code").hasClass('certificate_class')){
          $('.state_select').each(function(e){
            $("#"+$(this).attr("id")+" option[value='"+$stateID+"']").prop("disabled",true); 
            $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
          });
        }
   
        $("#"+$id+" option[value='"+$stateID+"']").prop("disabled",false);
        $("#resources_form #"+$id).multipleSelect('refresh');
        
      }).on('select2:unselect',function(e){
        e.stopPropagation();
   
        $id = $(this).attr('id');
        $did = $(this).attr('data-id');
        var $sel_val = e.params.data.id;
        // if($sel_val === '111111'){
        //   $val = $("#stateSelect_"+$(this).attr('data-id')).val();
        //   $('#resources_form #'+$id+' > option').prop("selected",false);
        //   if($val!=='' && $val!==null){
        //     for(i=0;i<=$val.length;i++)
        //     {
        //       $('.state_select').each(function(e){
        //         $("#"+$(this).attr("id")+" option[value='"+$val[i]+"']").prop("disabled",false); 
        //         $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
        //       });
        //     }
        //   }
        //   $("#inner_tree_div_"+$did).remove();
        //   return false;
        // }
        
        $id=$(this).attr('data-id');
        $stateID = e.params.data.id;
        $stateName=e.params.data.text;
   
        if($("#certificate_code").hasClass('certificate_class')){
          $('.state_select').each(function(e){
            $("#"+$(this).attr("id")+" option[value='"+$stateID+"']").prop("disabled",false); 
            $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
          });
        }
        if($(this).val() === null || $(this).val() === '')
        $("#inner_tree_div_"+$id).remove();
      });      
    }
   
   $(document).on('select2:select','.state_select',function(e){
      e.stopPropagation();
      $id = $(this).attr('id');
      var $sel_val = e.params.data.id;
      // if($sel_val === '111111'){
      //   $('#resources_form #'+$id+' > option').not(":disabled").prop("selected",true);
      //   $sd_val = $("#"+$id).val();
      //     $count = 0;
      //     if($("#certificate_code").hasClass('certificate_class')){
      //     $('.state_select').each(function(e){
      //     $count++;
      //     for(i=0;i<$sd_val.length;i++){
           
      //       $("#"+$(this).attr("id")+" option[value='"+$sd_val[i]+"']").prop("disabled",true); 
      //       $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
      //       $("#"+$id+" option[value='"+$sd_val[i]+"']").prop("disabled",false);
      //       $("#resources_form #"+$id).multipleSelect('refresh');
      //     }
      //     });}
      //   return false;
      // }
      $dataID=$(this).attr('data-id');
      $stateName=e.params.data.text;
      $stateID = e.params.data.id;
      if($("#certificate_code").hasClass('certificate_class')){
        $('.state_select').each(function(e){
          $("#"+$(this).attr("id")+" option[value='"+$stateID+"']").prop("disabled",true); 
          $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
        });
      }
      $("#"+$id+" option[value='"+$stateID+"']").prop("disabled",false);
      $("#resources_form #"+$id).multipleSelect('refresh');
   
      if($(this).val().length > 0)
        {
          $("#inner_tree_div_"+$dataID).show();
        }
   }).on('select2:unselect','.state_select',function(e){
        e.stopPropagation();
        $id = $(this).attr('id');
        var $sel_val = e.params.data.id;
        // if($sel_val === '111111'){
        //   $val = $("#stateSelect_"+$(this).attr('data-id')).val();
        //   $('#resources_form #'+$id+' > option').prop("selected",false);          
        //   if($("#certificate_code").hasClass('certificate_class')){
        //   if($val!=='' && $val!==null){
        //     for(i=0;i<=$val.length;i++)
        //     {
        //       $('.state_select').each(function(e){
        //         $("#"+$(this).attr("id")+" option[value='"+$val[i]+"']").prop("disabled",false); 
        //         $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
        //       });
        //     }
        //   }
        //   }
        //   $("#inner_tree_div_"+$id).hide();
        //   return false;
        // }
        $id=$(this).attr('data-id');
        $stateID = e.params.data.id;
        $stateName=e.params.data.text;
        if($("#certificate_code").hasClass('certificate_class')){
          $('.state_select').each(function(e){
            $("#"+$(this).attr("id")+" option[value='"+$stateID+"']").prop("disabled",false); 
            $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
          });
        }
        $("#resources_form #"+$(this).attr("id")).multipleSelect('refresh');
        if($(this).val()=== null)
        {
          $("#inner_tree_div_"+$id).hide();
        }
    });
   function ajax_remove_file(id,file_name,file_type){
   $.ajax({
            url: 'ajax_add_resources.php',
            dataType: 'JSON',
            type: 'POST',
            data: {id:id,remove_file:"remove",file_name: file_name,file_type:file_type},
            success: function(res) {
                $("#ajax_loader").hide();
                if (res.status == "success") {
                    $("#tmp_filename").val("");
                    setNotifySuccess(res.message);
                } else {
                    // setNotifyError(res.message);
                }
            }
        });
   }
   function change_coll_type(val,$id){
        if(val === 'video'){
          $("input[name='video_code_type["+$id+"]']").prop('checked',true).uniform();
          $("#video_code_type_div_"+$id).show();
          $(".file_types_video_"+$id).show();
          $(".file_types_link_"+$id).hide();
          $(".file_types_pdf_"+$id).hide();
          $("#res_editor_"+$id).hide();
          $(".smart_tag_popup_div").hide();
        }else if(val === 'pdf'){
          $("#video_code_type_div_"+$id).hide();
          $("#coll_type_control_text_"+$id).text("Upload PDF");
          $(".file_types_pdf_"+$id).show();
          $(".file_types_video_"+$id).hide();
          $(".file_types_link_"+$id).hide();
          $("#remove_video_"+$id).hide();
          $("#res_editor_"+$id).hide();
          $(".smart_tag_popup_div").hide();
        }else if(val === 'link'){
          $("#video_code_type_div_"+$id).hide();
          $(".file_types_link_"+$id).show();
          $(".file_types_pdf_"+$id).hide();
          $(".file_types_video_"+$id).hide();
          $("#res_editor_"+$id).hide();
          $(".smart_tag_popup_div").hide();
        }else if(val === 'html'){
          $("#video_code_type_div_"+$id).hide();
          $(".file_types_link_"+$id).hide();
          $(".file_types_pdf_"+$id).hide();
          $(".file_types_video_"+$id).hide();
          $("#remove_video_"+$id).hide();
          $("#remove_pdf"+$id).hide();
          $("#res_editor_"+$id).show();
          $(".smart_tag_popup_div").show();
          $is_html_editor = true;  
         if($is_ckeditor_member){
           for (instance in CKEDITOR.instances) {
			     CKEDITOR.instances[instance].updateElement();
			   }
         }else{
            initCKEditor("certificate_mem_description_"+$id,false,'200px');
            $is_ckeditor_member = true;
         }
             
        }
   }
   function ValidateAlpha(evt)
   {
    var keyCode = (evt.which) ? evt.which : evt.keyCode
    if ((keyCode < 48 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)      
      return false;
        return true;
   }
</script> 

<div class="clearfix tbl_filter m-b-10">
    <div class="pull-left">
        <h4 class="">+ Bundle Recommendation</h4>
        <p>Create bundle(s) below to get started.</p>
    </div>
    <div class="pull-right">
        <a href="javascript:void(0);" id = "cancel_recommandation_btn" class="btn red-link red-link">Cancel</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <select  name="selectedGroup" id="selectedGroup"  class="form-control selectedGroup">  
                <option data-hidden="true"></option>
                <?php if (count($GetGroupDetails) > 0) { ?>
                    <?php foreach ($GetGroupDetails as $group) {  ?>
                        <option value="<?=$group['id']?>" <?= !empty($groupId) &&  $groupId == $group['id'] ? 'selected' : ''?>><?=$group['fname']?> - <?=$group['lname']?>(<?=$group['rep_id']?>)</option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label>Assign Group</label>
            <p class="error" id="error_selectedBundle"></p>
        </div>
    </div>
</div>
<div class="bundle_wrap m-b-20">
    <div class="create_bundle">
        <div class="bundle_head bg_dark_primary p-10">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="m-t-7 m-b-0 text-white">Step 1 - Create Bundle(s)</h4>
                </div>
                <div class="col-sm-6" >
                    <div class="text-right">
                        <a href="javascript:void(0);" id="edit_bundle_step_1" onclick="edit_bundle_step(1)" style="<?=!empty($data)? ' ' : 'display: none';?>" class="btn btn-default">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bundle_body">
            <div class="table-responsive">
                <table class="<?=$table_class?>" id="bundleTable">
                    <thead>
                        <?php if(empty($data)){ ?>
                            <tr class="BundleheaderTr" style="display: none">
                        <?php }else {?>
                            <tr class="BundleheaderTr">
                        <?php } ?>
                            <th>Bundle Label</th>
                            <th>Products</th>
                            <th>Effective Date</th>
                            <th>Termination Date</th>
                            <th>Recommendation Reason Text</th>
                            <th width="100px" class="editBundleLabelTr"></th>
                        </tr>
                    </thead>
                    <tbody id="bundleTableBody">
                        <div id="bundleTableInputTr">
                        </div>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bundle_footer p-15 b-t" id="step_1_footer" style="<?=empty($data) ? '' :'display: none';?>">
          <div class="row">
             <div class="col-sm-6">
               <button class="btn btn-info" data-toggle="tooltip" title="+ Bundle"  type="button" id="addBundleButton" onclick="addBundle()" >+ Bundle</button>
                <a href="javascript:void(0)" class="btn btn-info saveBundleButton" data-toggle="tooltip" title="save" style="display:none" onclick="saveBundle(1)" data-container="body">save</a>
                <a href="javascript:void(0)" class="btn blue-link saveBundleButton" id="cancelCreateBundleButton" data-id="" data-toggle="tooltip" title="cancel" style="display:none" data-container="body">cancel</a>
             </div>
             <div class="col-sm-6">
                <div class="text-right">
                   <button type="button" class="btn btn-info <?=$BundleCount == '5' || $BundleCount >'5'  ? ' ':'disabled'; ?>" onclick="goto_step(2)" id="go_to_second_step" data-toggle="tooltip" title="Next Step" data-container="body"  <?=$BundleCount == '5' || $BundleCount >'5'  ? ' ':'disabled'; ?> >Next Step</button>
                </div>
             </div>
          </div>
       </div>
    </div>
    <div clss="row">
        <div class="col-sm-12 m-t-15" id="paginateLinkDiv">
        </div>
    </div>
</div>
<input type="hidden" name="groupId" id="groupId" value="<?= !empty($groupId) ? md5($groupId) : '' ?>">
<input type="hidden" name="bundle_table_counter" id="bundle_table_counter" value="0">
<div class="clearfix"></div>

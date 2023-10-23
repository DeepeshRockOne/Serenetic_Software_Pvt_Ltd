<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$ajax_license = checkIsset($_POST['ajax_license']);

if (isset($_SESSION["agents"]['rep_id']) && $ajax_license) {

    $selADoc = "SELECT id,selling_licensed_state,license_active_date,license_num,license_exp_date,license_not_expire,license_type,license_auth FROM agent_license WHERE agent_id=:agent_id AND is_deleted='N'";
    $whrADoc = array(":agent_id" => $_SESSION["agents"]['id']);
    $resADoc = $pdo->select($selADoc, $whrADoc);

    if(!empty($resADoc)){
        $selectedState = array();
        foreach ($resADoc as $st) {
            $selectedState[]=$st['selling_licensed_state'];
        }
    }

     if(!empty($resADoc)){ 
        foreach($resADoc as $rkey => $doc){
          $resADocStatesArray=$doc['selling_licensed_state']!=""?explode(',',$doc['selling_licensed_state']):array();
          ob_start();
          ?>
          <div class="license_portion license_tempmdsm  m-t-25 div_license_<?=$rkey?>" >
            <div class="row seven-cols">
            <input type="hidden" name='hdn_license[<?=$rkey?>]' value="<?=$doc['id']?>" id='hdn_license_<?=$rkey?>'>
            <div class="col-md-1">
              <div class="form-group ">
                <select name="license_state[<?=$rkey?>]" id="license_state_<?=$rkey?>"  class="license_state select_class_<?=$rkey?> form-control" >
                  <option value=""></option>
                  <?php if ($allStateRes) {?>
                    <?php foreach ($allStateRes as $state) {
                      $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());?>
                    <option <?=in_array($state["name"],$resADocStatesArray) ? 'selected' : ''?> value="<?=$state["name"];?>" <?=in_array($state,$hide_states)?'disabled':'' ?>><?php echo $state['name']; ?></option>
                    <?php }?>
                  <?php }?>
                </select>
                <label>License State<em>*</em></label>
                <p id="error_license_state_<?=$rkey?>" class="error error_license_state"></p>
              </div>
            </div>
              <div class="col-md-1">
                <div class="form-group ">
                  <input name="license_number[<?=$rkey?>]" id="license_number_<?=$rkey?>" type="text" class="form-control license_number"   value="<?=$doc["license_num"]?>">
                  <label>License Number<em>*</em></label>
                  <p id="error_license_number_<?=$rkey?>" class="error error_license_number"></p>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group ">
                    <input type="text" name="license_active_date[<?=$rkey?>]" value="<?=(isset($doc["license_active_date"]) && $doc["license_active_date"] != "" && $doc["license_active_date"] != "0000-00-00"  && strtotime($doc["license_active_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_active_date"])) : ""?>" id="license_active_date_<?=$rkey?>" class="form-control license_active" />
                      <label>License Active Date<em>*</em></label>
                    <p id="error_license_active_date_<?=$rkey?>" class="error error_license_active_date"></p>
                  </div>
              </div>
              <div class="col-md-1">
                <div class="form-group mn " id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
                  <input name="license_expiry[<?=$rkey?>]" id="license_expiry_<?=$rkey?>" type="text" class="form-control license_expiry"  value="<?=(isset($doc["license_exp_date"]) && $doc["license_exp_date"] != "" && $doc["license_exp_date"] != "0000-00-00"  && strtotime($doc["license_exp_date"]) > 0) ? date("m/d/Y", strtotime($doc["license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?>>
                  <label>License Expiration<em>*</em></label>
                  <p id="error_license_expiry_<?=$rkey?>" class="error error_license_expiry"></p>
                   <div class="clearfix m-t-5">
                    <label for="license_not_expire[<?=$rkey?>]" class="text-red fs12 mn" >
                      <input type="checkbox" name="license_not_expire[<?=$rkey?>]" id="license_not_expire_<?=$rkey?>" value="Y" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="license_not_expire" data-id="<?=$rkey?>">
                      License does not expire</label>
                </div>
                </div>
              </div>
             <div class="col-md-1">
              <div class="form-group ">
                  <select class="form-control" name="license_type[<?=$rkey?>]" id="license_type_<?=$rkey?>">
                      <option value="" disabled selected hidden> </option>
                        <option value="Business" <?=$doc["license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                        <option value="Personal" <?=$doc["license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
                    </select>
                    <label>License Type<em>*</em></label>
                    <p id="error_license_type_<?=$rkey?>" class="error error_license_type"></p>
                </div>
            </div>
            <div class="col-md-1">
            <div class="form-group ">
                <select class="form-control" name="licsense_authority[<?=$rkey?>]" id="licsense_authority_<?=$rkey?>">
                    <option value="" disabled selected hidden> </option>
                      <option value="Health" <?=$doc["license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                      <option value="Life" <?=$doc["license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                      <option value="general_lines" <?=$doc["license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
                  </select>
                  <label>License of Authority<em>*</em></label>
                  <p id="error_licsense_authority_<?=$rkey?>" class="error error_licsense_authority"></p>
              </div>
          </div>
          <div class="col-md-1">
            <div class="form-group ">
              <!--<a href="javascript:void(0)" class="edit_license btn red-link" id="edit_license_<?=$rkey?>" data-id="<?=$rkey?>" > Edit </a>
              <div id="hidden_btn_<?=$rkey?>" style="display:none">
                <button type="button" class="btn btn-primary ajax_add_license" data-id="<?=$rkey?>">Save</button> -->
                <?php //if($rkey!=0){?>
                  <a href="javascript:void(0)" class="remove_license btn red-link" data-id="<?=$rkey?>" > Delete </a>
                  <?php //} ?>
              <!--</div>-->
              
            </div>
          </div>
          
              <?php /*if($rkey!=0){?>
                <div class="col-md-1">
                  <div class="form-group">
                    <a href="javascript:void(0)" class="remove_license btn red-link"> Delete </a>
                  </div>
                  </div>
                <?php }*/ ?>
              
            </div>
          </div>
  <?php     } }
}
$html = ob_get_contents();
ob_get_clean();
if($html !=''){
  echo $html;
}else{
  redirect('agent_contract_remaining.php');
}
exit;
?>
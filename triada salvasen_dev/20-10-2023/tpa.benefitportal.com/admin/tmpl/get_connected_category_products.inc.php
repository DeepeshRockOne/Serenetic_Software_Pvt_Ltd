    <?php if(!empty($connectionArr)) { ?>
        <?php foreach ($connectionArr as $connKey => $connValue) { ?>
            <?php 
                $conn_row = $connValue[0];
                $keyDiff = explode("_", $connKey);
                $connection_id = $keyDiff[0];
                $category_id = $keyDiff[1];
            ?>
            <div class="connection_div" id="connection_div_<?= $connection_id ?>" data-id="<?= $connection_id ?>">
                <form id="frm_connection_<?= $connection_id ?>" method="post">
                    <input type="hidden" name="connection_id[<?= $connection_id ?>]" id="connection_id_<?= $connection_id ?>" value="<?= $connection_id ?>">
                    <div class="row">
                        <div class="col-md-offset-1">
                            <div class="col-md-6 ">
                              <div class="m-b-25 clearfix">
                                <h3 class="font-light mn fs22 pull-left">+ Connection </h3>
                                <a href="javascript:void(0);" class="pull-right m-t-10 red-link remove_connection_div" data-id="<?= $connection_id ?>" id="remove_connection_div_<?= $connection_id ?>">Remove</a>    
                            </div>
                          </div>
                          <div class="clearfix"></div>
                            <div class="theme-form">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <select class="form-control add_control_<?= $connection_id ?> connection_category has-value" id="connection_category_<?= $connection_id ?>" name="connection_category[<?= $connection_id ?>]" data-id="<?= $connection_id ?>">
                                            <option value="" selected="selected" hidden></option>
                                            <?php if(!empty($resCategory)) { ?>
                                                <?php foreach ($resCategory as $catKey => $catValue) { ?>
                                                    <option value="<?= $catValue['id'] ?>" <?= $catValue['id'] == $category_id ? 'selected' : '' ?>><?= $catValue['title'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <label>Select Product Category</label>
                                        <p class="error" id="error_connection_category_<?= $connection_id ?>"></p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="clearfix m-l-30">
                                   <p class="m-b-5"><strong>Upgrade Plan Rules:</strong></p>
                                   <p> Option after a Plan has become effective, if the member can upgrade the Plan.</p>
                                   <div class="row">
                                     <div class="col-sm-6" >
                                        <div class="form-group">
                                            <select class="form-control upgrade_option" name="upgrade_option[<?=$connection_id?>]" id="upgrade_option_<?= $connection_id ?>" data-id="<?= $connection_id ?>">
                                                <option value=""></option>
                                                <option value="Available Without Restrictions" <?= isset($conn_row['upgrade_option']) && $conn_row['upgrade_option'] =="Available Without Restrictions" ? 'selected' : '' ?>>Available Without Restrictions</option>
                                                <option value="Available Within Specific Time Frame" <?= isset($conn_row['upgrade_option']) && $conn_row['upgrade_option'] =="Available Within Specific Time Frame" ? 'selected' : '' ?>>Available Within Specific Time Frame</option>
                                            </select>
                                            <label for="upgrade_option_<?= $connection_id ?>">Select Upgrade Option</label>
                                            <p class="error" id="error_upgrade_option_<?= $connection_id ?>"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="form-inline">
                                            <div class="form-group upgrade_within_div_<?= $connection_id ?>" <?= isset($conn_row['upgrade_option']) && $conn_row['upgrade_option'] =="Available Within Specific Time Frame" ? '' : 'style="display: none;"' ?>>
                                                <select name="upgrade_within[<?= $connection_id ?>]" id="upgrade_within_<?= $connection_id ?>" class="form-control input-sm">
                                                  <?php
                                                  if(isset($conn_row['upgrade_within_type']) && $conn_row['upgrade_within_type']=="Weeks"){
                                                  $end_range = 52;
                                                  }else if(isset($conn_row['upgrade_within_type']) && $conn_row['upgrade_within_type']=="Months"){
                                                  $end_range = 24;
                                                  }else if(isset($conn_row['upgrade_within_type']) && $conn_row['upgrade_within_type']=="Years"){
                                                  $end_range = 10;
                                                  }else{
                                                  $end_range = 365;
                                                  }
                                                  ?>
                                                  <?php for($i=0;$i<=$end_range;$i++){ ?>
                                                  <option value="<?= $i ?>" <?= isset($conn_row['upgrade_within']) && $i==$conn_row['upgrade_within'] ? 'selected' :'' ?>><?= $i ?></option>
                                                  <?php } ?>
                                                </select>
                                                <select name="upgrade_within_type[<?= $connection_id ?>]" id="upgrade_within_type_<?= $connection_id ?>" class="form-control input-sm upgrade_within_type" data-id="<?= $connection_id ?>">
                                                  <option value="Days" <?= isset($conn_row['upgrade_within_type']) && 'Days'==$conn_row['upgrade_within_type'] ? 'selected' :'' ?>>Days</option>
                                                  <option value="Weeks" <?= isset($conn_row['upgrade_within_type']) && 'Weeks'==$conn_row['upgrade_within_type'] ? 'selected' :'' ?>>Weeks</option>
                                                  <option value="Months" <?= isset($conn_row['upgrade_within_type']) && 'Months'==$conn_row['upgrade_within_type'] ? 'selected' :'' ?>>Months</option>
                                                  <option value="Years" <?= isset($conn_row['upgrade_within_type']) && 'Years'==$conn_row['upgrade_within_type'] ? 'selected' :'' ?>>Years</option>
                                                </select>
                                                <p class="error" id="error_upgrade_within_<?= $connection_id ?>"></p>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                  <p class="m-b-5"><strong>Life Event Rules:</strong></p>
                                  <div><label class="label-input" for="is_allow_upgrade_life_event_<?= $connection_id ?>"><input type="checkbox" name="is_allow_upgrade_life_event[<?= $connection_id ?>]" id="is_allow_upgrade_life_event_<?= $connection_id ?>" class="is_allow_upgrade_life_event" data-id="<?= $connection_id ?>" value="Y" <?= isset($conn_row['is_allow_upgrade_life_event']) && 'Y'==$conn_row['is_allow_upgrade_life_event'] ? 'checked' :'' ?>>  Allow upgrade if primary Plan holder has specific life event?</label></div>
                                  <div class="row">
                                            <?php 
                                    $upgrade_life_event_options = array();
                                    if(isset($conn_row['is_allow_upgrade_life_event']) && 'Y' == $conn_row['is_allow_upgrade_life_event']) {
                                        $upgrade_life_event_options = json_decode($conn_row['upgrade_life_event_options'],true);
                                        if(empty($upgrade_life_event_options)) {
                                          $upgrade_life_event_options = array();
                                        }
                                    }
                                    ?>
                                    <div class="col-sm-6 upgrade_life_event_options_div_<?= $connection_id ?>" <?= isset($conn_row['is_allow_upgrade_life_event']) && 'Y'==$conn_row['is_allow_upgrade_life_event'] ? '' :'style="display: none;"' ?>>
                                        <p>Select all that apply: </p>
                                        <div class="form-group">
                                            <select name="upgrade_life_event_options[<?= $connection_id ?>][]" class="se_multiple_select upgrade_life_event_options" id="upgrade_life_event_options_<?= $connection_id ?>" multiple="multiple">
                                                <?php 
                                                  if(!empty($LifeEvents)) {
                                                    foreach ($LifeEvents as $LifeEventKey => $LifeEventLabel) {
                                                      ?>
                                                      <option value="<?=$LifeEventKey?>" <?=in_array($LifeEventKey,$upgrade_life_event_options)?'selected':''?>><?=$LifeEventLabel?></option>
                                                      <?php
                                                    }
                                                  }
                                                ?>
                                            </select>
                                            <label for="upgrade_life_event_options_<?= $connection_id ?>">Life Event</label>
                                            <p class="error" id="error_upgrade_life_event_options_<?= $connection_id ?>"></p>
                                        </div>
                                    </div>
                                  </div>

                                    <p class="m-b-5"><strong>Downgrade Plan Rules:</strong> </p> <p>Option after a Plan has become effective, if the member can downgrade the Plan.</p>
                                    <div class="row">
                                         <div class="col-sm-6" >
                                        <div class="form-group">
                                            <select class="form-control downgrade_option" name="downgrade_option[<?=$connection_id?>]" id="downgrade_option_<?= $connection_id ?>" class="downgrade_option" data-id="<?= $connection_id ?>">
                                                <option value=""></option>
                                                <option value="Available Without Restrictions" <?= isset($conn_row['downgrade_option']) && $conn_row['downgrade_option'] =="Available Without Restrictions" ? 'selected' : '' ?>>Available Without Restrictions</option>
                                                <option value="Available Within Specific Time Frame" <?= isset($conn_row['downgrade_option']) && $conn_row['downgrade_option'] =="Available Within Specific Time Frame" ? 'selected' : '' ?>>Available Within Specific Time Frame</option>
                                            </select>
                                            <label for="downgrade_option_<?= $connection_id ?>">Select Downgrade Option</label>
                                            <p class="error" id="error_downgrade_option_<?= $connection_id ?>"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div class="form-inline">
                                            <div class="form-group downgrade_within_div_<?= $connection_id ?>"  <?= isset($conn_row['downgrade_option']) && $conn_row['downgrade_option'] =="Available Within Specific Time Frame" ? '' : 'style="display: none;"' ?>>
                                                <select name="downgrade_within[<?= $connection_id ?>]" id="downgrade_within_<?= $connection_id ?>" class="form-control input-sm">
                                                  <?php
                                                  if(isset($conn_row['downgrade_within_type']) && $conn_row['downgrade_within_type']=="Weeks"){
                                                  $end_range = 52;
                                                  }else if(isset($conn_row['downgrade_within_type']) && $conn_row['downgrade_within_type']=="Months"){
                                                  $end_range = 24;
                                                  }else if(isset($conn_row['downgrade_within_type']) && $conn_row['downgrade_within_type']=="Years"){
                                                  $end_range = 10;
                                                  }else{
                                                  $end_range = 365;
                                                  }
                                                  ?>
                                                  <?php for($i=0;$i<=$end_range;$i++){ ?>
                                                  <option value="<?= $i ?>" <?= isset($conn_row['downgrade_within']) && $i==$conn_row['downgrade_within'] ? 'selected' :'' ?>><?= $i ?></option>
                                                  <?php } ?>
                                                </select>
                                                <select name="downgrade_within_type[<?= $connection_id ?>]" id="downgrade_within_type_<?= $connection_id ?>" class="form-control input-sm downgrade_within_type" data-id="<?= $connection_id ?>">
                                                  <option value="Days" <?= isset($conn_row['downgrade_within_type']) && 'Days'==$conn_row['downgrade_within_type'] ? 'selected' :'' ?>>Days</option>
                                                  <option value="Weeks" <?= isset($conn_row['downgrade_within_type']) && 'Weeks'==$conn_row['downgrade_within_type'] ? 'selected' :'' ?>>Weeks</option>
                                                  <option value="Months" <?= isset($conn_row['downgrade_within_type']) && 'Months'==$conn_row['downgrade_within_type'] ? 'selected' :'' ?>>Months</option>
                                                  <option value="Years" <?= isset($conn_row['downgrade_within_type']) && 'Years'==$conn_row['downgrade_within_type'] ? 'selected' :'' ?>>Years</option>
                                                </select>
                                                <p class="error" id="error_downgrade_within_<?= $connection_id ?>"></p>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <p class="m-b-5"><strong>Life Event Rules:</strong></p>
                                    <div><label class="label-input" for="is_allow_downgrade_life_event_<?= $connection_id ?>"><input type="checkbox" name="is_allow_downgrade_life_event[<?= $connection_id ?>]" id="is_allow_downgrade_life_event_<?= $connection_id ?>" class="is_allow_downgrade_life_event" data-id="<?= $connection_id ?>" value="Y" <?= isset($conn_row['is_allow_downgrade_life_event']) && 'Y'==$conn_row['is_allow_downgrade_life_event'] ? 'checked' :'' ?>>  Allow downgrade if primary Plan holder has specific life event?</label></div>
                                    <div class="row">
                                          <?php 
                                    $downgrade_life_event_options = array();
                                    if(isset($conn_row['is_allow_downgrade_life_event']) && 'Y' == $conn_row['is_allow_downgrade_life_event']) {
                                        $downgrade_life_event_options = json_decode($conn_row['downgrade_life_event_options'],true);
                                        if(empty($downgrade_life_event_options)) {
                                          $downgrade_life_event_options = array();
                                        }
                                    }
                                    ?>
                                    <div class="col-sm-6 downgrade_life_event_options_div_<?= $connection_id ?>" <?= isset($conn_row['is_allow_downgrade_life_event']) && 'Y'==$conn_row['is_allow_downgrade_life_event'] ? '' :'style="display: none;"' ?>>
                                        <p>Select all that apply: </p>
                                        <div class="form-group">
                                            <select name="downgrade_life_event_options[<?= $connection_id ?>][]" class="se_multiple_select downgrade_life_event_options" id="downgrade_life_event_options_<?= $connection_id ?>" multiple="multiple">
                                            <?php 
                                              if(!empty($LifeEvents)) {
                                                foreach ($LifeEvents as $LifeEventKey => $LifeEventLabel) {
                                                  ?>
                                                  <option value="<?=$LifeEventKey?>" <?=in_array($LifeEventKey,$downgrade_life_event_options)?'selected':''?>><?=$LifeEventLabel?></option>
                                                  <?php
                                                }
                                              }
                                            ?>
                                            </select>
                                            <label for="downgrade_life_event_options_<?= $connection_id ?>">Life Event</label>
                                            <p class="error" id="error_downgrade_life_event_options_<?= $connection_id ?>"></p>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix m-t-20"></div>
                                 <p><span class="fw500">Select products : </span> First product is the highest offered product and the last product is the lowest offered product in relation to display and upgrade/downgrade rules.</p>
                                <div class="connection_products_main_div_<?= $connection_id ?> connected_prd_up_down" id="connection_products_main_div_<?= $connection_id ?>" data-id="<?= $connection_id ?>">
                                  <div class="connected_grade_arrow">
                                    
                                  </div>
                                    <div class="col-sm-6" >
                                        <?php if(!empty($connValue)) { $i=1;?>
                                            <?php foreach ($connValue as $prdKey => $prdValue) { ?>
                                                <div class="connection_products_div_<?= $connection_id ?> m-t-10" id="connection_products_div_<?= $connection_id ?>_<?= $prdValue['id'] ?>" data-div-id="<?= $connection_id ?>" data-id="<?= $prdValue['id'] ?>">
                                                    <div class="form-group">
                                                        <div class="row m-l-30">
                                                            <div class="col-xs-2 text-right" >
                                                                <div class="search_btn font-bold fs18 text-blue display_number" id="display_number_<?= $connection_id ?>_<?= $prdValue['id'] ?>" data-display-number="<?= $i ?>"><?= $i ?></div>
                                                            </div>
                                                            <div class="col-xs-9 pr">
                                                                <select class="form-control has-value add_control_<?= $connection_id ?>_<?= $prdValue['id'] ?> category_products" id="connection_products_<?= $connection_id ?>_<?= $prdValue['id'] ?>" name="connection_products[<?= $connection_id ?>][<?= $prdValue['id'] ?>]" data-id="<?= $prdValue['id'] ?>" data-div-id="<?= $connection_id ?>" data-prev="">
                                                                    <option value=""></option>
                                                                    <?php if(!empty($resProduct)){
                                                                        foreach ($resProduct as $key => $value) {?>
                                                                            <?php if(!empty($value['product_id']) && !empty($value['category_id']) && $value['category_id']==$prdValue['category_id'] ){ ?>
                                                                                <option value="<?= $value['product_id']?>" class="option_cat_<?= $value['category_id'] ?> option_product_<?= $value['product_id'] ?>" <?= $value['product_id'] == $prdValue['product_id']  ? 'selected' : ''?> 
                                                                                    <?= $value['product_id'] != $prdValue['product_id'] && in_array($value['product_id'], $allConnectedProduct)?'disabled':'' ?>><?= $value['name'].' ('.$value['product_code'].')' ?></option>
                                                                            <?php }                                
                                                                        }
                                                                    } ?>
                                                                </select>
                                                                <label>Select Product</label>
                                                            </div>
                                                            <div class="col-xs-1" >
                                                                <a href="javascript:void(0);" class="fs16 font-bold text-light-gray remove_connection_products_div" data-div-id="<?= $connection_id ?>" data-id="<?= $prdValue['id'] ?>">X</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $i++ ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="col-md-6 ">
                                    <p class="error" id="error_connection_products_<?= $connection_id ?>"></p>
                                    <div class="form-group text-right">
                                        <button type="button" class="btn btn-primary  addConnection" data-id="<?= $connection_id ?>" id="addConnection_<?= $connection_id ?>">Connect Product</button>
                                        <button type="button" class="btn btn-primary saveConnection" data-id="<?= $connection_id ?>" id="saveConnection_<?= $connection_id ?>">Save</button>
                                        <hr />
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
    <?php } ?>


    
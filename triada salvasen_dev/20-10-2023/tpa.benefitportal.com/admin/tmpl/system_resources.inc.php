<div class="panel panel-default panel-block advance_info_div">
   <div class="panel-body">
      <div class="phone-control-wrap ">
         <div class="phone-addon w-90 v-align-top">
            <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
         </div>
         <div class="phone-addon text-left">
            <div class="info_box_max_width">
               <p class="fs14 mn">Below are resources available to improve, increase, or streamline your
                  operations. <?= $DEFAULT_SITE_NAME ?> continues to add new resources to better assist your business and its
                  growth.
               </p>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="panel panel-default panel-block">
   <div class="panel-body">
      <h4 class="m-t-7 m-b-15">Select Option</h4>
      <div class="m-b-15 resources_wrap">
         <div class="blue_arrow_tab">
            <ul class="nav nav-tabs nav-noscroll nav-justified">
               <li class="active">
                  <a data-toggle="tab" href="#resources">
                  <span class="set-icons"><img src="images/icons/document.svg" width="30px"></span> Resources
                  </a>
               </li>
               <li>
                  <a data-toggle="tab" href="#mass_updates">
                  <span class="set-icons"><img src="images/icons/dark-mass-update-icon.svg"
                     width="30px"></span> Mass Updates</a>
               </li>
               <li>
                  <a data-toggle="tab" href="#system_setup">
                  <span class="set-icons"><img src="images/icons/system-dark-icon.svg" width="30px"></span>
                  System Setup</a>
               </li>
            </ul>
         </div>
      </div>
   </div>
</div>
<div class="tab-content mn theme-form">
   <!-- resorces code start -->
      <div id="resources" class="tab-pane fade in active">
           <?php include_once 'system_resources_listing.inc.php'; ?>
      </div>
   <!-- resorces code ends -->
   <div id="report_detail" style="display: none;">
            <div class="panel panel-block panel-default">
               <div class="panel-body">
                  <h4 class="pull-left m-b-0 m-t-7">Quick Sales Summary</h4>
                  <div class="pull-right"><a href="javascript:void(0);" class="btn btn-default" id="back_reporting">Back</a></div>
               </div>
            </div>
            <div class="panel panel-block panel-default">
               <div class="panel-body">
                  <div class="row theme-form">
                     <div class="col-md-6 col-sm-12">
                        <div class="row">
                           <div id="date_range" class="col-md-12 col-sm-12">
                              <div class="form-group height_auto m-b-20">
                                 <select class="form-control" id="join_range" name="join_range">
                                    <option value=""></option>
                                    <option value="Range">Range</option>
                                    <option value="Exactly">Exactly</option>
                                    <option value="Before">Before</option>
                                    <option value="After">After</option>
                                 </select>
                                 <label>Date</label>
                              </div>
                           </div>
                           <div class="select_date_div col-md-9 col-sm-12" style="display:none">
                              <div class="form-group height_auto m-b-20">
                                 <div id="all_join" class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" name="added_date" id="added_date" value="" class="form-control" />
                                 </div>
                                 <div  id="range_join" style="display:none;">
                                    <div class="phone-control-wrap">
                                       <div class="phone-addon">
                                          <label class="mn">From</label>
                                       </div>
                                       <div class="phone-addon">
                                          <div class="input-group">
                                             <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                             <input type="text" name="fromdate" id="fromdate" value="" class="form-control" />
                                          </div>
                                       </div>
                                       <div class="phone-addon">
                                          <label class="mn">To</label>
                                       </div>
                                       <div class="phone-addon">
                                          <div class="input-group">
                                             <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                             <input type="text" name="todate" id="todate" value="" class="form-control" />
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-12 text-right">
                        <p class="m-t-7 fw500 fs16 text-action">Selected Date - <span class="fw300">09/04/2019</span></p>
                     </div>
                  </div>
                  <div class="report_detail_table">
                     <div class="table-responsive">
                        <table  class="<?=$table_class?>">
                           <tbody>
                              <tr>
                                 <td class="fw500">Total</td>
                                 <td class="fw500">$228,235.15</td>
                              </tr>
                              <tr>
                                 <td class="fw500">Approved</td>
                                 <td>
                                    <span class="fw500">447  /  $139,879.30</span>&nbsp; &nbsp;   (New Business : <span class="fw500">68  /  $27,203.55</span> )&nbsp;&nbsp;&nbsp;    (Renewal Business: <span class="fw500">379  /  $112,675.75</span> )
                                 </td>
                              </tr>
                              <tr>
                                 <td class="fw500">Cancelled</td>
                                 <td class="fw500 text-warning">2  /  $1,049.95</td>
                              </tr>
                              <tr>
                                 <td class="fw500">Declined</td>
                                 <td class="fw500 text-warning">205  /  $64,587.50</td>
                              </tr>
                              <tr>
                                 <td class="fw500">Refund/Void</td>
                                 <td class="fw500 text-action">44  /  ($15,665.10)</td>
                              </tr>
                              <tr>
                                 <td class="fw500">Payment<br> Returned (ACH)</td>
                                 <td class="fw500 text-action">2  /  ($578.90)</td>
                              </tr>
                              <tr>
                                 <td class="fw500">Chargeback</td>
                                 <td class="fw500 text-action">15  /  ($6,474.40)</td>
                              </tr>
                              <tr>
                                 <td class="fw500">ACH</td>
                                 <td class="fw500">
                                    0  /  $0.00<br>
                                    <span class="text-warning">0  /  $0.00</span>
                                 </td>
                              </tr>
                              <tr>
                                 <td class="fw500">CC</td>
                                 <td class="fw500">
                                    447  /  $139,879.30<br>
                                    <span class="text-warning">64  /  $64,587.50</span>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="text-right m-t-30">
                        <a href="javascript:void(0);" class="btn btn-action">Export</a>
                     </div>
                  </div>
               </div>
            </div>
   </div>
   <div id="mass_updates" class="tab-pane fade ">
      <div class="panel panel-default panel-block">
         <form id="frm_mass_updates" name="frm_mass_updates" action="ajax_upload_data_file.php" method="POST">
            <input type="hidden" name="save_as" id="save_as" value="">
            <div class="panel-body">
               <div class="pull-right">
                  <div class="m-b-15">
                     <a class="btn btn-default" href="manage_imports.php" target="_blank">Manage Imports</a>
                  </div>
               </div>
               <div class="m-b-20">
                  <p>Select an option below to make a mass update change.</p>
                  <div class="m-b-5">
                     <label><input type="radio" name="module_name" value="members">Members</label>
                  </div>
                  <div class="m-b-5">
                     <label><input type="radio" name="module_name" value="agents">Agents</label>
                  </div>
                  <div class="m-b-5">
                     <label><input type="radio" name="module_name" value="products">Products</label>
                  </div>
               </div>
               <div class="m-b-20">
                  <div id="member_options" style="display: none;">
                     <p>Choose the type of action to mass change for Members.</p>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_members">Member Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="member_add_products">Member Add Product Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="term_products">Member Term Product Import</label>
                     </div>
                  </div>
                  <div id="agent_options" style="display: none;">
                     <p>Choose the type of action to mass change for Agents.</p>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_agents">Agent Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_license">Agent License Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_appointment">Agent Appointment Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_direct_deposit">Agent Direct Deposit Import</label>
                     </div>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_e_o_coverage">Agent E&O Coverage Import</label>
                     </div>
                  </div>
                  <div id="product_options" style="display: none;">
                     <p>Choose the type of action to mass change for Products.</p>
                     <div class="m-b-5">
                        <label><input type="radio" name="import_action" value="add_products">Product Import</label>
                     </div>
                  </div>
               </div>
               <div class="row m-t-30" id="upload_csv" style="display: none;">
                  <div class="col-sm-8">
                     <div class="form-group height_auto">
                        <div class="clearfix m-b-15">
                           <div class="pull-left">
                              <p class="mn">Upload CSV</p>
                           </div>
                           <div class="pull-right templates" id="add_members_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=MEMBER_IMPORT_TEMPLATE.csv" class="pn red-link fw500">Download + Member
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="member_add_products_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=MEMBER_ADDPRODUCT_TEMPLATE.xlsx" class="pn red-link fw500">Download Member Add Products
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="term_products_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=MEMBER_TERMPRODUCT_TEMPLATE.xlsx" class="pn red-link fw500">Download Member Term Product
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_agents_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=AGENT_IMPORT_TEMPLATE.xlsx" class="pn red-link fw500">Download + Agent
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_license_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=AGENT_LICENSE_IMPORT_TEMPLATE.xlsx" class="pn red-link fw500">Download Agent License
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_appointment_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=AGENT_APPOINTMENTS_TEMPLATE.xlsx" class="pn red-link fw500">Download Agent Appointment
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_direct_deposit_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=AGENT_DIRECT_DEPOSIT_TEMPLATE.xlsx" class="pn red-link fw500">Download Agent Direct Deposit
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_e_o_coverage_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=AGENT_E_O_TEMPLATE.csv" class="pn red-link fw500">Download Agent E&O Coverage
                              Template</a>
                           </div>
                           <div class="pull-right templates" id="add_products_template" style="display: none;">
                              <a href="<?=$ADMIN_HOST?>/download_import_template.php?file_name=PRODUCTS_IMPORT_TEMPLATE.csv" class="pn red-link fw500">Download + Product
                              Template</a>
                           </div>
                        </div>
                        <div class="custom_drag_control">
                           <span class="btn btn-action">Upload CSV</span>
                           <input type="file" class="gui-file" id="csv_file" name="csv_file" accept=".csv">
                           <input type="text" id="choose_file" class="gui-input form-control" placeholder="Choose File">
                        </div>
                        <div id="instructions" style="display: none;">
                           <hr>
                           <p class="mn">Match the data fields from your file with the database fields displayed on the
                           left. (i.e. Primary First Name = choose the column in your file that displays Primary
                           First Name)
                        </p>   
                        </div>
                     </div>
                  </div>
               </div>
               <div id="fields_wrapper" style="display: none;">
                  
               </div>   
               <div class="text-center m-b-10">
                  <button class="btn btn-action" id="btn_import">Import</button>
                  <a href="javascript:void(0);" class="btn red-link">Cancel</a>
               </div>
            </div>
         </form>
      </div>
   </div>
   <div id="system_setup" class="tab-pane fade ">
      <form action="ajax_save_system_setup.php" id="form_system_setup" method="POST">
         <div class="panel panel-default panel-block" id="panel_system_setup">
            <div class="panel-body">
               <div class="clearfix m-b-15 tbl_filter">
                  <div class="pull-left">
                     <h4 class="m-t-7">Communications</h4>
                  </div>
                  <div class="pull-right">
                     <a href="javascript:void(0);" id="edit_system_setup_detail"></a>
                  </div>
               </div>
               <div id="pwd_system_setup"></div>
               <div class="resources_setup_wrap">
                  <div class="row">
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Default Email From
                              </div>
                              <div class="pr">
                                 <input type="text" name="default_email_from" id="default_email_from" class="form-control no_space" value="<?=isset($app_setting_res['default_email_from'])?$app_setting_res['default_email_from']:''?>" readonly="">
                                 <label>Email Addresses</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_default_email_from"></span></p>
                        </div>
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Default Email From Name
                              </div>
                              <div class="pr">
                                 <input type="text" name="default_from_name" id="default_from_name" class="form-control" value="<?=isset($app_setting_res['default_from_name'])?$app_setting_res['default_from_name']:''?>" readonly="">
                                 <label>From Name</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_default_from_name"></span></p>
                        </div>
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 SMS Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="sms_twilio_number" id="sms_twilio_number" class="form-control input_cell_phone" value="<?=isset($app_setting_res['sms_twilio_number'])?$app_setting_res['sms_twilio_number']:''?>" readonly="">
                                 <label>Phone Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_sms_twilio_number"></span></p>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="twliioNumberDiv"></div>
            </div>
         </div>
         <div class="panel panel-default panel-block" id="panel_system_support">
            <div class="panel-body">
               <div class="clearfix m-b-15 tbl_filter">
                  <div class="pull-left">
                     <h4 class="m-t-7">Support Services Information</h4>
                  </div>
                  <div class="pull-right">
                     <a href="javascript:void(0);" id="edit_system_support_detail"></a>
                  </div>
               </div>
               <div id="pwd_system_support"></div>
               <div class="resources_setup_wrap">
                  <div class="row">
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Agent Services Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="agent_services_cell_phone" id="agent_services_cell_phone" class="form-control input_cell_phone" value="<?=isset($app_setting_res['agent_services_cell_phone'])?$app_setting_res['agent_services_cell_phone']:''?>" readonly="">
                                 <label>Phone Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_agent_services_cell_phone"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Agent Services Email
                              </div>
                              <div class="pr">
                                 <input type="text" name="agent_services_email" id="agent_services_email" class="form-control no_space" value="<?=isset($app_setting_res['agent_services_email'])?$app_setting_res['agent_services_email']:''?>" readonly="">
                                 <label>Email Addresses</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_agent_services_email"></span></p>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Member Services Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="member_services_cell_phone" id="member_services_cell_phone" class="form-control input_cell_phone" value="<?=isset($app_setting_res['member_services_cell_phone'])?$app_setting_res['member_services_cell_phone']:''?>" readonly="">
                                 <label>Phone Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_member_services_cell_phone"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Member Services Email
                              </div>
                              <div class="pr">
                                 <input type="text" name="member_services_email" id="member_services_email" class="form-control no_space" value="<?=isset($app_setting_res['member_services_email'])?$app_setting_res['member_services_email']:''?>" readonly="">
                                 <label>Email Addresses</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_member_services_email"></span></p>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Group Services Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="group_services_cell_phone" id="group_services_cell_phone" class="form-control input_cell_phone" value="<?=isset($app_setting_res['group_services_cell_phone'])?$app_setting_res['group_services_cell_phone']:''?>" readonly="">
                                 <label>Phone Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_group_services_cell_phone"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Group Services Email
                              </div>
                              <div class="pr">
                                 <input type="text" name="group_services_email" id="group_services_email" class="form-control no_space" value="<?=isset($app_setting_res['group_services_email'])?$app_setting_res['group_services_email']:''?>" readonly="">
                                 <label>Email Addresses</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_group_services_email"></span></p>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Enrollment Display Name
                              </div>
                              <div class="pr">
                                 <input type="text" name="enrollment_display_name" id="enrollment_display_name" class="form-control " value="<?=isset($app_setting_res['enrollment_display_name'])?$app_setting_res['enrollment_display_name']:''?>" readonly="">
                                 <label>Enrollment Display Name</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_enrollment_display_name"></span></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="panel panel-default panel-block" id="panel_nahca_file">
            <div class="panel-body">
               <div class="clearfix m-b-15 tbl_filter">
                  <div class="pull-left">
                     <h4 class="m-t-7">NACHA File Information</h4>
                  </div>
                  <div class="pull-right">
                     <a href="javascript:void(0);" id="edit_nacha_file_detail"></a>
                  </div>
               </div>
               <div id="pwd_nacha_file"></div>
               <div class="resources_setup_wrap">
                  <div class="row">
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Routing Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="immediate_destination" id="immediate_destination" class="form-control" value="<?=isset($app_setting_res['immediate_destination'])?$app_setting_res['immediate_destination']:''?>" readonly="">
                                 <label>Routing Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_immediate_destination"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Name of Bank
                              </div>
                              <div class="pr">
                                 <input type="text" name="immediate_destination_name" id="immediate_destination_name" class="form-control" value="<?=isset($app_setting_res['immediate_destination_name'])?$app_setting_res['immediate_destination_name']:''?>" readonly="">
                                 <label>Name of Bank</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_immediate_destination_name"></span></p>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <?php 
                        $resAcctno = $pdo->selectOne("SELECT *,AES_DECRYPT(setting_value,'" . $CREDIT_CARD_ENC_KEY . "') as acctNo FROM app_settings WHERE setting_key = 'immediate_origin'");
                        $encAcctNo = !empty($resAcctno["acctNo"]) ? "*".substr($resAcctno["acctNo"],-4) : "";
                        $acctNo = !empty($resAcctno["acctNo"]) ? $resAcctno["acctNo"] : "";
                        
                        ?>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Account ID/EIN 
                              </div>
                              <div class="pr">
                                 <input type="text" name="immediate_origin" id="immediate_origin" class="form-control" data-einAcctNo="<?=$encAcctNo?>" data-acctNo="<?=$acctNo?>" value="<?=isset($encAcctNo) ? $encAcctNo:''?>" readonly="">
                                 <label>Account ID/EIN </label>
                              </div>
                           </div>
                           <p class="error"><span id="error_immediate_origin"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Company Name 
                              </div>
                              <div class="pr">
                                 <input type="text" name="immediate_origin_name" id="immediate_origin_name" class="form-control" value="<?=isset($app_setting_res['immediate_origin_name'])?$app_setting_res['immediate_origin_name']:''?>" readonly="">
                                 <label>Company Name</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_immediate_origin_name"></span></p>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 Description
                              </div>
                              <div class="pr">
                                 <input type="text" name="company_entry_description" id="company_entry_description" class="form-control" value="<?=isset($app_setting_res['company_entry_description'])?$app_setting_res['company_entry_description']:''?>" readonly="">
                                 <label>CommPmt</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_company_entry_description"></span></p>
                        </div>
                     </div>
                     <div class="col-sm-12 col-md-6">
                        <div class="form-group height_auto">
                           <div class="input-group">
                              <div class="input-group-addon">
                                 DFI Number
                              </div>
                              <div class="pr">
                                 <input type="text" name="originating_dfi_id" id="originating_dfi_id" class="form-control" value="<?=isset($app_setting_res['originating_dfi_id'])?$app_setting_res['originating_dfi_id']:''?>" readonly="">
                                 <label>DFI Number</label>
                              </div>
                           </div>
                           <p class="error"><span id="error_originating_dfi_id"></span></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
<div style="display:none" id="clonePwdPopup">
   <div id="password_popup_~name~" class="system_setup_access m-b-20">
       <div class="input-group">
          <input type="password" class="form-control radius-zero" name="det" id="password_~name~">
          <span class="input-group-addon" data-name="~name~" id="unlockBtn">Unlock</span>
        </div>
   </div>
   <div class="clearfix"></div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
    dropdown_pagination('twliioNumberDiv')

         checkEmail();
         loadTwilioNumber();
        $(document).on('change',"input[name=module_name]",function(){
         $('.templates').hide();
         value = $(this).val();   
         if(value == 'members'){
            $('#member_options').slideDown();
            $('#product_options').slideUp();
            $('#agent_options').slideUp();
         }else if(value == 'agents'){
            $('#member_options').slideUp();
            $('#product_options').slideUp();
            $('#agent_options').slideDown();
         }else if(value == 'products'){
            $('#member_options').slideUp();
            $('#product_options').slideDown();
            $('#agent_options').slideUp();
         }else{
            $('#member_options').slideUp();
            $('#product_options').slideUp();
            $('#agent_options').slideUp();
         }
        }); 

        $(document).on('change',"input[name=import_action]",function(){
            value = $(this).val();
            if(value != ""){
               $('#csv_file').val('');
               $('#choose_file').val('');
               $('#upload_csv').slideDown();
               $('.templates').hide();
               $('#' + value + '_template').show();
               $('#fields_wrapper').hide();
               $('#instructions').hide();
               $('#primary_fields').html('');

            }else{
               $('#csv_file').val('');
               $('#choose_file').val('');
               $('#upload_csv').slideUp();
               $('.templates').hide();
               $('#fields_wrapper').hide();
               $('#instructions').hide();
               $('#primary_fields').html('');
            }
        });


        $(document).on('change', "#csv_file", function () {
            if ($(this).val() != '') {
                $("#save_as").val("upload_csv");
                $("#ajax_loader").show();
                $("#frm_mass_updates").submit();
            }
        });

        $(document).on('click', "#btn_import", function (e) {
            e.stopImmediatePropagation();
            $("#save_as").val("add_request");
            $("#ajax_loader").show();
            $("#frm_mass_updates").submit();
            return false;
        });

        $('#frm_mass_updates').ajaxForm({
            type: "POST",
            dataType: 'JSON',
            success: function (res) {
                $(".error").html('');

                if (res.html) {
                    $('#fields_wrapper').html(res.html);
                    $('#fields_wrapper').show();
                    $('#instructions').show();
                    $('[data-toggle="tooltip"]').tooltip(); 
                    common_select();
                    fRefresh();
                } else if (res.status == 'fail') {

                    var is_error = true;
                    $.each(res.errors, function (index, error) {

                        if(index == 'csv_file'){
                           $("#ajax_loader").hide();
                           swal({
                                text: res.errors['csv_file'],
                                showCancelButton: false,
                                confirmButtonText: "Close",
                                showCloseButton: false
                            }).then(function () {
                                $('#csv_file').val('');
                                $('#choose_file').val('');
                                $('#fields_wrapper').html('');
                                $('#fields_wrapper').hide();
                            });
                            return false;
                        }  

                        $('#err_' + index).html(error).show();
                        if (is_error) {
                            var offset = $('#err_' + index).offset();
                            var offsetTop = offset.top;
                            var totalScroll = offsetTop - 50;
                            $('body,html').animate({scrollTop: totalScroll}, 1200);
                            is_error = false;
                        }
                    });
                } else if (res.status == 'success') {
                    window.location = 'manage_imports.php';
                }
                $("#ajax_loader").hide();
                // $('.select').selectpicker('refresh');
            }
        });

        $(".input_cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $("#reporing_detail").click(function () {
            $("#reporting_table").hide();
            $("#report_detail").show();
        });
        $("#back_reporting").click(function () {
            $("#report_detail").hide();
            $("#reporting_table").show();
        });
        
        $("#edit_system_setup_detail").addClass('pull-right fa fa-edit fs18  is_edit_enabled');
        $("#edit_system_support_detail").addClass('pull-right fa fa-edit fs18  is_edit_enabled');
        $("#edit_nacha_file_detail").addClass('pull-right fa fa-edit fs18  is_edit_enabled');

        $(document).off('click', '#edit_system_setup_detail');
        $(document).on('click', '#edit_system_setup_detail', function (e) {
         if($(this).hasClass('hasPassword')){
            if ($(this).hasClass('is_edit_enabled')) {
                $("#edit_system_setup_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18 ');
                $("#edit_system_setup_detail").addClass('pull-right btn btn-info').text('Save');
                $("#panel_system_setup input").each(function(){
                      $(this).removeAttr('readonly','readonly');
                });
            } else {
                save_system_setup("system_setup");
            }
         }else{
            $popupHtml = $("#clonePwdPopup").html();
            $("#pwd_system_setup").html($popupHtml.replace(/~name~/g,'system_setup')).show();
         }
        });

        $(document).off('click', '#edit_system_support_detail');
        $(document).on('click', '#edit_system_support_detail', function (e) {
         if($(this).hasClass('hasPassword')){
            if ($(this).hasClass('is_edit_enabled')) {
                $("#edit_system_support_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18 m-t-15');
                $("#edit_system_support_detail").addClass('pull-right btn btn-info m-t-7').text('Save');
                $("#panel_system_support input").each(function(){
                      $(this).removeAttr('readonly','readonly');
                });
            } else {
                save_system_setup("system_support");
            }
         }else{
            $popupHtml = $("#clonePwdPopup").html();
            $("#pwd_system_support").html($popupHtml.replace(/~name~/g,'system_support')).show();
         }
        });

        $(document).off('click', '#edit_nacha_file_detail');
        $(document).on('click', '#edit_nacha_file_detail', function (e) {
            $acctNo = $("#immediate_origin").attr("data-acctNo");
          if($(this).hasClass('hasPassword')){
            if ($(this).hasClass('is_edit_enabled')) {
                $("#immediate_origin").val($acctNo);
                $("#edit_nacha_file_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18 m-t-15');
                $("#edit_nacha_file_detail").addClass('pull-right btn btn-info m-t-7').text('Save');
                $("#panel_nahca_file input").each(function(){
                      $(this).removeAttr('readonly','readonly');
                });
            } else {
                save_system_setup("nacha_file");
            }
         }else{
            $popupHtml = $("#clonePwdPopup").html();
            $("#pwd_nacha_file").html($popupHtml.replace(/~name~/g,'nacha_file')).show();
         }
        });

         $(document).on('click','#unlockBtn',function(e){
            e.preventDefault();
            $divName = $(this).attr("data-name");
            if($("#password_"+$divName).val() === '5401'){
               if($divName == "system_setup"){
                  $("#edit_system_setup_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18');
                   $("#edit_system_setup_detail").addClass('pull-right btn btn-info hasPassword').text('Save');
                   $("#panel_system_setup input").each(function(){
                         $(this).removeAttr('readonly','readonly');
                   });
               }else if($divName == "system_support"){
                  $("#edit_system_support_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18');
                   $("#edit_system_support_detail").addClass('pull-right btn btn-info m-t-7 hasPassword').text('Save');
                   $("#panel_system_support input").each(function(){
                         $(this).removeAttr('readonly','readonly');
                   });
               }else if($divName == "nacha_file"){
                  $acctNo = $("#immediate_origin").attr("data-acctNo");
                  $("#immediate_origin").val($acctNo);
                  $("#edit_nacha_file_detail").removeClass('is_edit_enabled pull-right fa fa-edit fs18 m-t-15');
                  $("#edit_nacha_file_detail").addClass('pull-right btn btn-info m-t-7 hasPassword').text('Save');
                  $("#panel_nahca_file input").each(function(){
                         $(this).removeAttr('readonly','readonly');
                  });
               }
            }
            $("#pwd_"+$divName).html('');
         });

        $("#resources_product").multipleSelect({
            selectAll: false
        });

        
        $(".resource_description").colorbox({iframe: true, width: '800px', height: '330px'});
    });
    $(document).off('change', '#join_range');
         $(document).on('change', '#join_range', function(e) {
           e.preventDefault();
               if ($(this).val() == '') {
                   $('.select_date_div').hide();
                   $('#date_range').removeClass('col-md-3').addClass('col-md-12');
               } else {
                   $('#date_range').removeClass('col-md-12').addClass('col-md-3');
                   $('.select_date_div').show();
                   if ($(this).val() == 'Range') {
                       $('#range_join').show();
                       $('#all_join').hide();
                   } else {
                       $('#range_join').hide();
                       $('#all_join').show();
                   }
           }
       });
     
    function save_system_setup(section)
    {
        var form_action = "ajax_save_system_setup.php?section="+section;
        $("#form_system_setup").attr('action',form_action);

        formHandler($("#form_system_setup"),
            function () {
                $("#ajax_loader").show();
            },
            function (data) {
                $("#ajax_loader").hide();
                $("p.error").hide();
                if (data.status == 'success') {
                    setNotifySuccess(data.msg);
                    if(section == "system_support") {
                        $("#edit_system_support_detail").removeClass('pull-right btn btn-info  hasPassword').text('');
                        $("#edit_system_support_detail").addClass('pull-right fa fa-edit fs18 is_edit_enabled');  
                        $("#panel_system_support input").each(function(){
                            $(this).attr('readonly','readonly');
                        });
                    } else if(section == "system_setup"){
                        $("#edit_system_setup_detail").removeClass('pull-right btn btn-info  hasPassword').text('');
                        $("#edit_system_setup_detail").addClass('pull-right fa fa-edit fs18  is_edit_enabled');  
                        $("#panel_system_setup input").each(function(){
                            $(this).attr('readonly','readonly');
                        });
                    }else if(section == "nacha_file"){
                        $("#edit_nacha_file_detail").removeClass('pull-right btn btn-info  hasPassword').text('');
                        $("#edit_nacha_file_detail").addClass('pull-right fa fa-edit fs18  is_edit_enabled');  
                        $("#panel_nahca_file input").each(function(){
                            $(this).attr('readonly','readonly');
                        });
                        window.location.reload();
                    }
                } else {
                    $(".error").hide();
                    var tmp_flag = true;
                    $.each(data.errors, function (key, value) {
                        $('#error_' + key).parent("p.error").show();
                        $('#error_' + key).html(value).show();
                        $('.error_' + key).parent("p.error").show();
                        $('.error_' + key).html(value).show();

                        if (tmp_flag == true && $("[name='" + key + "']").length > 0) {
                            tmp_flag = false;
                            $('html, body').animate({
                                scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                            }, 1000);
                        }
                    });
                }
            }
        );
    }

   loadTwilioNumber = function(search_val) {
      $('#ajax_loader').show();
      $('#twliioNumberDiv').hide();
      $.ajax({
         url: 'loadTwilioNumber.php',
         type:'GET',
         data:{is_ajaxed:1},
         success: function(res) {
            $('#ajax_loader').hide();
            $('#twliioNumberDiv').html(res).show();
            $(".is_active_number").not('.js-switch').uniform();
            common_select();
         }
      });
   }
</script>
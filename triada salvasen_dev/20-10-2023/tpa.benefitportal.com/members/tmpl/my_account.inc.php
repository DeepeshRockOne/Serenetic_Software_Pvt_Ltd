<div class="container">
    <h2 class="text-action m-t-30 m-b-30">My Account</h2>
</div>
<?php if($sponsor_billing_method == "individual") { ?>
<div class="member_product_area ">
    <div class="container">
        <div class="acount_profile_top">
            <div class="row">
                <div class="col-sm-4">
                    <h4 class="m-t-0 text-blue m-b-25">Upcoming Payment</h4>
                    <div class="clearfix">
                            <div class="pull-left">
                                <p class="fs26 text-success mn"><?=displayAmount($next_purchase_info['grandTotal'])?></p>
                                On <?= getCustomDate($next_purchase_info['next_billing_date']) ?>
                            </div>
                            <div class="pull-right m-t-25">
                                <a href="upcoming_payment.php" class="upcoming_payment btn btn-success">Details</a>
                            </div>
                    </div>
                </div>
                
                <div class="col-sm-5 col-sm-offset-3">
                    <h4 class="m-t-0 text-blue m-b-25">Billing Profile</h4>
                    <div class="clearfix">
                        <div class="pull-left">
                            <p class="mn">
                                <?php if( !empty($cb_row) ) { ?>
                                <?=$cb_row['fname']?><br>
                                <?php if($cb_row['payment_mode'] == 'ACH') { ?>
                                ACH *<?=substr($cb_row['cb_ach_account_number'], -4)?><br>
                                <?php } else { ?>
                                <?=$cb_row['card_type']?> *<?=$cb_row['card_no']?><br>
                                <?php } ?>
                                <strong>Last Modified - <?=date('m/d/Y',strtotime($cb_row['updated_at']))?></strong>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="pull-right m-t-25">
                            <a href="edit_billing_profile.php" class="edit_billing_profile btn btn-info">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="my_account_wrap">
    <div class="container">
        <div class="bg_white">
            <ul class="nav nav-tabs tabs  customtab  fixed_tab_top" role="tablist">
                <li class="active" role="presentation">
                    <a href="#policy_tab" data-toggle="tab" onclick="scrollToDiv($('#policy_tab'), 0);"
                       aria-expanded="true">Primary Plan Holder</a>
                </li>
                <li role="presentation">
                    <a href="#dependents_tab" data-toggle="tab" onclick="scrollToDiv($('#dependents_tab'), 0);"
                       aria-expanded="true">Dependents</a>
                </li>
                <?php if($sponsor_billing_method == "individual") { ?>
                <li role="presentation">
                    <a href="#orders_tab" data-toggle="tab" onclick="scrollToDiv($('#orders_tab'), 0);"
                       aria-expanded="true">Orders</a>
                </li>
                <?php } ?>
                <?php if($isGapPrd){ ?>
                <li role="presentation">
                    <a href="#hrm_tab" data-toggle="tab" onclick="scrollToDiv($('#hrm_tab'), 0);"
                       aria-expanded="true">HRM Payment</a>
                </li>
                <?php } ?>
                <li role="presentation">
                    <a href="#account_tab" data-toggle="tab" onclick="scrollToDiv($('#account_tab'), 0);"
                       aria-expanded="true">Account</a>
                </li>
            </ul>
            <div class="m-t-20">
                <div role="tabpanel" class="tab-pane active" id="policy_tab">
                    <form action="ajax_save_primary_policy.php" name="primary_policy_form" id="primary_policy_form">
                        <input type="hidden" name="is_valid_address" id="is_valid_address" value="Y">
                        <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?= $cust_row['is_address_verified'] ?>">
                        <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
                        <input type="hidden" name="sponsor_type" value="<?=$sponsor_type?>">
                        <div class="alert alert-danger">
                            <strong>Primary Plan Holder Information</strong>
                        </div>

                        <div class="theme-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="fname" name="fname" value="<?= trim($cust_row['fname']) ?>"
                                               class="form-control">
                                        <label>First Name</label>
                                        <p class="error error_fname"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="lname" name="lname" value="<?= trim($cust_row['lname']) ?>"
                                               class="form-control">
                                        <label>Last Name</label>
                                        <p class="error error_lname"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="email" value="<?= trim($cust_row['email']) ?>"
                                               class="form-control no_space">
                                        <label>Email</label>
                                        <p class="error error_email"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="cell_phone" id="cell_phone"
                                               value="<?= format_telephone($cust_row['cell_phone']) ?>"
                                               class="form-control">
                                        <label>Phone</label>
                                        <p class="error error_cell_phone"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="address" id="address" value="<?= $cust_row['address'] ?>"
                                               class="form-control">
                                        <label>Address 1</label>
                                        <p class="error error_address" id="error_address"></p>
                                        <input type="hidden" name="old_address" value="<?=$cust_row['address']?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="address_2" id="address_2" value="<?= $cust_row['address_2'] ?>" class="form-control" onkeypress="return block_special_char(event)">
                                        <label>Address 2 (suite, apt)</label>
                                        <p class="error error_address_2" id="error_address_2"></p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="city" value="<?= $cust_row['city'] ?>" id="city"
                                               class="form-control" readonly='readonly'>
                                        <label>City</label>
                                        <p class="error error_city"></p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="state" class="form-control" id="state"
                                               value="<?= $cust_row['state'] ?>" readonly='readonly'>
                                        <label>State</label>
                                        <p class="error error_state"></p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="zip" value="<?= $cust_row['zip'] ?>" readonly='readonly'
                                               maxlength="5" onkeypress="return isNumberKey(event)" id="primary_zip"
                                               class="form-control">
                                        <label>Zip Code</label>
                                        <p class="error error_primary_zip"></p>
                                        <input type="hidden" name="old_zip" value="<?=$cust_row['zip']?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="birth_date" id="birth_date"
                                               value="<?= getCustomDate($cust_row['birth_date']) ?>" class="form-control"  readonly='readonly'>
                                        <label>DOB</label>
                                        <p class="error error_birth_date"></p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="phone-control-wrap">
                                        <div class="phone-addon">
                                            <div class="form-group">
                                                <input type="text" name="ssn" id="display_ssn" readonly='readonly'
                                                       class="form-control"
                                                       value="<?= secure_string_display_format($cust_row['dssn'], 4); ?>">
                                                <label>SSN</label>
                                                <input type="text" class="form-control" id="ssn" name="ssn" value=""
                                                       style="display:none"/>
                                                <input type="hidden" name="is_ssn_edit" id='is_ssn_edit' value='N'/>
                                                <p class="error error_ssn"></p>
                                            </div>
                                        </div>
                                        <div class="phone-addon w-30">
                                            <div class="m-b-25">
                                                <a href="javascript:void(0)" id="edit_ssn" class="text-action icons"
                                                   style="display:block">
                                                    <i class="fa fa-edit fa-lg"></i></a>
                                                <a href="javascript:void(0)" id="cancel_ssn" class="text-action icons"
                                                   style="display:none">
                                                    <i class="fa fa-remove fa-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="gender" class="form-control" id="gender"
                                               value="<?= $cust_row['gender']; ?>" readonly='readonly'>
                                        <label>Gender</label>
                                    </div>
                                </div>
                                <?php if($sponsor_type == 'Group') { ?>
                                    <div class="col-sm-4">
                                    <div class="form-group">
                                        <?php /*<select class="form-control" name="group_class" id="group_class">
                                            <option value=""></option>
                                            <?php if(!empty($resGroupClass)) { 
                                                foreach($resGroupClass as $class){ ?>
                                                <option value="<?= $class['id'] ?>" <?= !empty($group_classes_id) && $group_classes_id==$class['id'] ? 'selected' : '' ?>><?= $class['class_name'] ?></option>
                                            <?php } } ?>
                                        </select> */ ?>
                                        <input type="text" class="form-control" name="group_class" value="<?=$className?>" id="group_class" disabled="disabled">
                                        <label>Enrollee Class<em>*</em></label>
                                        <p class="error error_group_class"></p>
                                    </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4">
                                    <div class="form-group">
                                        <?php /*<select class="form-control" id="group_company_id" name="group_company_id"> 
                                            <option value=""></option>
                                            <option value="0" <?= $group_company_id == 0 ? 'selected' : '' ?>><?= $group_name ?></option>
                                                <?php if(!empty($group_cmp_res)) { ?>
                                                <?php foreach ($group_cmp_res as $key => $value) { ?>
                                                    <option value="<?= $value['id'] ?>" data-location="<?= $value['location'] ?>" <?= !empty($group_company_id) && $value['id'] == $group_company_id ? 'selected' : '' ?>><?= $value['name'] ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                        </select> */ ?>
                                        <input type="text" class="form-control" name="group_company_id" value="<?=$group_name?>" id="group_company_id" disabled="disabled">
                                        <label>Company<em>*</em></label>
                                        <p class="error error_group_company_id"></p>
                                    </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="text-right m-b-30">
                                <button type="button" id="btn_save_primary_policy" class="btn btn-action">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="dependents_tab">
                    <div class="alert alert-danger">
                        <strong>Dependent Information</strong>
                    </div>
                    <div class="table-responsive m-b-30">
                        <table class="<?= $table_class ?>">
                            <thead>
                            <th>ID/Added Date</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Relationship</th>
                            <th>Date of Birth</th>
                            <th>Age</th>
                            <th class="text-center">Active Products</th>
                            <th width="70px">Actions</th>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($dep_res)) {
                                foreach ($dep_res as $dep_row) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" class="fw500 text-action">
                                                <?= $dep_row['display_id'] ?></a><br>
                                            <?= getCustomDate($dep_row['created_at']) ?>
                                        </td>
                                        <td><?= $dep_row['fname'] ?></td>
                                        <td><?= $dep_row['lname'] ?></td>
                                        <td><?= $dep_row['crelation']; ?></td>
                                        <td><?= getCustomDate($dep_row['birth_date']) ?></td>
                                        <td><?= calculateAge($dep_row['birth_date']) ?></td>
                                        <td class="icons text-center">
                                            <a href="depedents_active_product.php?id=<?= $dep_row['id'] ?>"
                                               class="depedents_active_product"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                        <td class="icons">
                                            <a href="add_depedents.php?id=<?= md5($customer_id) ?>&dep_id=<?= $dep_row['id'] ?>&action=Edit"
                                               data-toggle="tooltip" data-placement="top" title="Edit"
                                               class="edit_depedents"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No dependents found!</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($sponsor_billing_method == "individual") { ?>
                <div role="tabpanel" class="tab-pane" id="orders_tab">
                    <div class="alert alert-danger">
                        <strong>Order Information</strong>
                    </div>
                    <form method="GET" action="member_orders.php" id="member_orders" class="row">
                        <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1"/>
                        <input type="hidden" name="pages" id="per_pages" value="<?= $per_page; ?>"/>
                        <input type="hidden" name="sort_by" id="sort_by_column" value=""/>
                        <input type="hidden" name="sort_direction" id="sort_by_direction" value=""/>
                    </form>
                    <div id="member_orders_ajax_data" class=""></div>
                </div>
                <?php } ?>
                <?php if($isGapPrd) { ?>
                <div role="tabpanel" class="tab-pane" id="hrm_tab">
                    <div class="alert alert-danger">
                        <strong>Completed HRM Payment</strong>
                    </div>
                    <form method="GET" action="member_completed_hrm_payments.php" id="member_hrm" class="row">
                        <input type="hidden" name="is_ajaxed_hrm" id="is_ajaxed_hrm" value="1"/>
                        <input type="hidden" name="hrm_pages" id="hrm_per_pages" value="<?= $per_page; ?>"/>
                        <input type="hidden" name="hrm_sort_by" id="hrm_sort_by_column" value=""/>
                        <input type="hidden" name="hrm_sort_direction" id="hrm_sort_by_direction" value=""/>
                    </form>
                    <div id="member_hrm_ajax_data" class=""></div>
                </div>
                <?php } ?>
                <div role="tabpanel" class="tab-pane" id="document_tab">
                <h4 class="m-t-0 m-b-20" style="color:gray;"> Documentation</h4>
                    <form method="GET" action="member_document.php" id="member_document" class="row">
                        <input type="hidden" name="is_ajaxed_doc" id="is_ajaxed_doc" value="1"/>
                        <input type="hidden" name="doc_pages" id="doc_per_pages" value="<?= $per_page; ?>"/>
                        <input type="hidden" name="page" id="nav_page" value="" />
                        <input type="hidden" name="doc_sort_by" id="doc_sort_by_column" value=""/>
                        <input type="hidden" name="doc_sort_direction" id="doc_sort_by_direction" value=""/>
                    </form>
                <div id="member_document_tab_ajax_data" class=""></div>
                <div role="tabpanel" class="tab-pane" id="account_tab">
                    <form action="ajax_save_account_data.php" name="account_form" id="account_form" autocomplete="off">
                        <div class="alert alert-danger">
                            <strong>Account Information</strong>
                        </div>
                        <div class="theme-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                               maxlength="20" value="" autocomplete="new-password"
                                               onblur="check_password(this, 'password_err','error_password', event, 'input_validation');"
                                               onkeyup="check_password_Keyup(this,'password_err','error_password', event, 'input_validation');">
                                        <label>Create New Password</label>
                                        <p class="error error_password"></p>
                                        <div id="password_err" class="mid"><span></span></div>
                                        <div id="pswd_info" class="pswd_popup" style="display: none">
                                            <div class="pswd_popup_inner">
                                                <h4>Password Requirements</h4>
                                                <ul>
                                                    <li id="pwdLength" class="invalid"><em></em>Minimum 8 Characters</li>
                                                    <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase
                                                        letter
                                                    </li>
                                                    <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowercase
                                                        letter
                                                    </li>
                                                    <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                                                </ul>
                                                <div class="btarrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="password" name="confirm_password" id="confirm_password"
                                               class="form-control">
                                        <label>Confirm New Password</label>
                                        <p class="error error_confirm_password"></p>
                                    </div>
                                </div>
                            </div>
                            <p>
                                <div class="radio-question">
                                    <label class="radio-inline">May we contact you by email?</label>
                                    <label class="radio-inline"><input type="radio"
                                                                       name="allow_contact_email" <?= $cust_row['allow_contact_email'] == "Y" ? "checked" : ""; ?>
                                                                       value="Y">Yes</label>
                                    <label class="radio-inline"><input type="radio"
                                                                       name="allow_contact_email" <?= $cust_row['allow_contact_email'] == "N" ? "checked" : ""; ?>
                                                                       value="N">No</label>
                                </div>
                            </p>
                            <p>
                                <div class="radio-question">
                                    <label class="radio-inline">May we contact you by text?</label>
                                    <label class="radio-inline"><input type="radio"
                                                                       name="allow_contact_text" <?= $cust_row['allow_contact_text'] == "Y" ? "checked" : ""; ?>
                                                                       value="Y" class="allow_contact_text">Yes</label>
                                    <label class="radio-inline"><input type="radio"
                                                                       name="allow_contact_text" <?= $cust_row['allow_contact_text'] == "N" ? "checked" : ""; ?>
                                                                       value="N" class="allow_contact_text">No</label>
                                </div>                                   
                            </p>
                            <div class="text-right m-b-30">
                                <button type="button" id="btn_save_account_data" class="btn btn-action">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display:none">
    <div class="panel panel-default mn panel-shadowless" id="address_change">
        <div class="panel-heading">
            <h4 class="mn">Address Change - <span class="fw300"><?= $cust_row['fname'] . ' ' . $cust_row['lname'] ?></span></h4>
        </div>
        <hr class="mn">
        <div class="panel-body">
            <p class="m-b-30">There is a conflict with one or more of this members products.</p>
            <p class="m-b-30"><span class="fs18 text-action fw500" id="conflict_product">HSA DB6550 -</span> <strong>This product is not available in the state of <span id="state_span"></span></strong></p>
            <p class="m-b-30">In order to make this address change, the above product(s) must be terminated and
                following the termination date the address may be updated. </p>
            <div class="text-center">
                <a href="javascript:void(0)" class="btn red-link pn" onclick="parent.$.colorbox.close(); return false;">Close</a>
            </div>
        </div>
    </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        document_tab();
        checkEmail();
        $(".edit_billing_profile").colorbox({iframe: true, width: '768px', height: '675px'});
        $(".upcoming_payment").colorbox({iframe: true, width: '1024px', height: '630px'});

        /*---- Profile Tab ----*/
        $("#primary_policy_form :input").each(function (e) {
            if ($(this).val() !== '') {
                $(this).addClass('has-value');
            }
        })
        $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
        $("#ssn").inputmask({"mask": "999-99-9999",'showMaskOnHover': false});
        $("#birth_date").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});

        $('#edit_ssn').click(function () {
            $(this).hide();
            $('#display_ssn').hide();
            $('#ssn').show();
            $('#is_ssn_edit').val('Y');
            $('#cancel_ssn').show();

        });

        $('#cancel_ssn').click(function () {
            $(this).hide();
            $('#display_ssn').show();
            $('#ssn').hide();
            $('#is_ssn_edit').val('N');
            $('#edit_ssn').show();
            $('#error_ssn').html('');
        });

        $(document).off('click', '#btn_save_primary_policy');
        $(document).on('click', '#btn_save_primary_policy', function (e) {
            $('#btn_save_primary_policy').prop('disabled', true);
            $is_address_ajaxed = $("#is_address_ajaxed").val();
            if($is_address_ajaxed == 1){
                updateAddress();
            }else{
                ajaxSaveAccountDetails();
            }
        });

        /*--- Dependents Tab ---*/
        $(".depedents_active_product").colorbox({iframe: true, width: '360px', height: '330px'});
        $(".edit_depedents").colorbox({
            iframe: true,
            width: '768px',
            height: '450px',
            overlayClose: false,
            escKey: false
        });

        // Hrm Tab
        $isGapPrd = '<?=$isGapPrd?>';
        if($isGapPrd == '1'){
            get_member_hrm();
        }
        dropdown_pagination('member_hrm_ajax_data');

        /*--- Orders Tab ---*/
        <?php if($sponsor_billing_method == "individual") { ?>
        get_member_orders();
        dropdown_pagination('member_orders_ajax_data');
        $(document).off('click', '#member_orders_ajax_data ul.pagination li a');
        $(document).on('click', '#member_orders_ajax_data ul.pagination li a', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $('#member_orders_ajax_data').hide();
            $.ajax({
                url: $(this).attr('href'),
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#member_orders_ajax_data').html(res).show();
                    common_select();
                }
            });
        });
        $(document).on('click', ".transaction_receipt", function () {
            $href = $(this).attr('data-href');
            window.open($href, "myWindow", "width=1024,height=630");
        });

        <?php } ?>
        /*--- Account Tab ---*/
        $(document).off('change', '.allow_contact_text');
        $(document).on('change', '.allow_contact_text', function (e) {
            if($(this).val() == "Y") {
                var sysNumber = "<?php echo preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $TwilioNumber);?>";
                swal({
                    text: '<br/>Remove Phone: User must text "START" to <span class="text-nowrap">' + sysNumber + ' </span> to be removed.',
                    showCancelButton: false,
                    confirmButtonText: 'Close'
                });
            }
        });

        $(document).off('click', '#btn_save_account_data');
        $(document).on('click', '#btn_save_account_data', function (e) {
            $('#btn_save_account_data').prop('disabled', true);
            $.ajax({
                url: $("#account_form").attr('action'),
                type: 'POST',
                data: $("#account_form").serialize(),
                dataType: 'json',
                beforeSend: function (e) {
                    $("#ajax_loader").show();
                },
                success: function (res) {
                    $('#btn_save_account_data').prop('disabled', false);
                    $("#ajax_loader").hide();
                    $(".error").html("");
                    if (res.status == 'success') {
                        $("#password").val('').removeClass('has-value');
                        $("#confirm_password").val('').removeClass('has-value');
                        setNotifySuccess("Account information updated successfully.");
                    } else {
                        $.each(res.errors, function (index, error) {
                            $(".error_" + index).html(error).show();
                        });
                    }
                }
            });
        });
    });
    
    function document_tab() {
        $('#ajax_loader').show();
        $('#member_document_tab_ajax_data').hide();
        $('#is_ajaxed').val('1');
        var params = $('#member_document').serialize();
        $.ajax({
            url: $('#member_document').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#member_document_tab_ajax_data').html(res).show();
                $('.popup').colorbox({iframe: true, width: '1000px', height: '600px'});
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        return false;   
    }
    
    function updateAddress(){
   
        $.ajax({
          url : $("#primary_policy_form").attr('action'),
          type : 'POST',
          data:$("#primary_policy_form").serialize(),
          dataType:'json',
          beforeSend :function(e){
             $("#ajax_loader").show();
             $(".error").html('');
          },success(res){
             $('#btn_save_primary_policy').prop('disabled', false);
             $("#is_address_ajaxed").val("");
             $("#ajax_loader").hide();
             $(".suggested_address_box").uniform();
             if(res.zip_response_status =="success"){
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                $("#is_address_verified").val('N');
                ajaxSaveAccountDetails();
             }else if(res.address_response_status =="success"){
                $(".suggestedAddressEnteredName").html($("#fname").val()+" "+$("#lname").val());
                $("#state").val(res.state).addClass('has-value');
                $("#city").val(res.city).addClass('has-value');
                $(".suggestedAddressEntered").html(res.enteredAddress);
                $(".suggestedAddressAPI").html(res.suggestedAddress);
                $("#is_valid_address").val('Y');
                $.colorbox({
                      inline:true,
                      href:'#suggestedAddressPopup',
                      height:'500px',
                      width:'650px',
                      escKey:false, 
                      overlayClose:false,
                      closeButton:false,
                      onClosed:function(){
                         $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();
                         
                         if($suggestedAddressRadio=="Suggested"){
                            $("#address").val(res.address).addClass('has-value');
                            $("#address_2").val(res.address2).addClass('has-value');
                            $("#is_address_verified").val('Y');
                         }else{
                            $("#is_address_verified").val('N');
                         }
                         ajaxSaveAccountDetails();
                      },
                });
             }else if(res.status == 'success'){
                $("#is_address_verified").val('N');
                ajaxSaveAccountDetails();
             }else{
                $.each(res.errors,function(index,error){
                   $(".error_"+index).html(error).show();
               });
             }
          }
        });
    }

    function ajaxSaveAccountDetails(){
        $.ajax({
            url: $("#primary_policy_form").attr('action'),
            type: 'POST',
            data: $("#primary_policy_form").serialize(),
            dataType: 'json',
            beforeSend: function (e) {
                $("#ajax_loader").show();
            },
            success: function (res) {
                $('#btn_save_primary_policy').prop('disabled', false);
                $("#ajax_loader").hide();
                $(".error").html("");
                if (res.status == 'success') {
                    setNotifySuccess("Profile updated successfully.");
                } else {
                    if (res.product_popup !== undefined && res.products !== undefined && res.product_popup == 'product_popup') {
                        $("#conflict_product").text('');
                        $("#conflict_product").text(res.products);
                        $("#state_span").text($("#state").val());
                        $.colorbox({
                            href: '#address_change',
                            inline: true,
                            width: '585px',
                            height: '330px'
                        });
                    }
                    $.each(res.errors, function (index, error) {
                        $(".error_" + index).html(error).show();
                    });
                }
            }
        });   
    }

    function scrollToDiv(element, navheight) {
        if ($(element).length) {
            var offset = element.offset();
            var offsetTop = offset.top;
            var totalScroll = offsetTop - navheight;
            if ($(window).width() >= 1099) {
                var totalScroll = offsetTop - $("nav.navbar-default").outerHeight() - 15
            } else {
                var totalScroll = offsetTop - $("nav.navbar-default ").outerHeight() - 15
            }
            $('body,html').animate({
                scrollTop: totalScroll
            }, 1200);
        }
    }

    function get_member_orders() {
        $('#ajax_loader').show();
        $('#member_orders_ajax_data').hide();
        $('#is_ajaxed').val('1');
        var params = $('#member_orders').serialize();
        $.ajax({
            url: $('#member_orders').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#member_orders_ajax_data').html(res).show();
                $('.popup').colorbox({iframe: true, width: '1000px', height: '600px'});
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        return false;
    }

    function get_member_hrm() {
        $('#ajax_loader').show();
        $('#member_hrm_ajax_data').hide();
        $('#is_ajaxed_hrm').val('1');
        var params = $('#member_hrm').serialize();
        $.ajax({
            url: $('#member_hrm').attr('action'),
            type: 'GET',
            data: params,
            success: function (res) {
                $('#ajax_loader').hide();
                $('#member_hrm_ajax_data').html(res).show();
                $('.popup').colorbox({iframe: true, width: '1000px', height: '600px'});
                common_select();
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        return false;
    }

    $(document).on('focus','#address,#primary_zip',function(){
       $("#is_address_ajaxed").val(1);
    });

    $(document).on('blur','#address',function(){
    });

    $(window).on('resize load', function(){
       if ($(window).width() <= 991) {
          $('.nav-tabs:not(.nav-noscroll)').scrollingTabs('destroy');
          autoResizeNav();
       }
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
</script>
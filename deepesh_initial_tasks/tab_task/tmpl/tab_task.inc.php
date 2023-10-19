<div class="container mt-5">
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <h3 class="text-center">Tab Task</h3>        
            <div class="container mt-5">
                <ul class="nav nav-tabs" id="myTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Tab 1</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Tab 2</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Tab 3</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab4-tab" data-bs-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false">Tab 4</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        <form action="" id="tab1_form" method="post">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter first name">
                                <div class="first_name_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter last name">
                                <div class="last_name_error"></div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary mt-3 tab_next tabbing2">Next</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <form action="" id="tab2_form" method="post">
                            <label for="gender">Gender:</label>
                            <div class="form-control">
                                <input type="radio" name="gender" id="gender_male" value="Male">Male
                                <input type="radio" name="gender" id="gender_female" value="Female">Female
                            </div>
                            <div class="gender_error"></div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                                <div class="email_error"></div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary mt-3 tab_next tabbing3">Next</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                        <form action="" id="tab3_form" method="post">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
                                <div class="password_error"></div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm password:</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter confirm password">
                                <div class="confirm_password_error"></div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary mt-3 tab_next tabbing4">Next</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                        <form action="" id="tab4_form" method="post">
                            <div class="form-group">
                                <label for="phone">Phone number:</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone number">
                                <div class="phone_error"></div>
                            </div>
                            <div class="form-group">
                                <div class="my-2">
                                    <input type="checkbox" name="terms_condition" value="1" id="terms_condition">
                                    <label for="terms_condition">Terms & condition</label>
                                </div>
                                <div class="terms_condition_error"></div>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary mt-3 tab_submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var url = 'ajax_tab_task.php';

        $('.tab_next').click(function(e){
            e.preventDefault();

            var tab_next_class = $(this).attr('class');
            var matches = tab_next_class.match(/(tabbing\S)/g);

            var next_tab_number = matches[0].split('tabbing');
            var current_tab_number = next_tab_number[1]-1;

            var form_data = {};

            form_data['form_' + current_tab_number] = $('#tab' + current_tab_number + '_form').serialize();
            form_data['tab_next_clicked'] = "tab_"+current_tab_number+"_next=true";

            $.ajax({
                url: url,
                data: form_data,
                dataType: 'json',
                type: 'POST',
                cache: false,
                success:function(res){
                    var error_field = [];
                    var field_array = [];

                    if (!$.isEmptyObject(res)) {
                        $.each(res, function(field, message){
                            if (field != '') {
                                switch (current_tab_number) {
                                    case 1:
                                        if (field == 'first_name' || field == 'last_name') {
                                            error_field.push(field);
                                        }
                                        break;
                                    case 2:
                                        if (field == 'gender' || field == 'email') {
                                            error_field.push(field);
                                        }
                                    case 3:
                                        if (field == 'password' || field == 'confirm_password') {
                                            error_field.push(field);
                                        }
                                        break;
                                    default:
                                        error_field = [];
                                }

                                if (!$.isEmptyObject(error_field)) {
                                    $('.'+field+'_error').addClass('my-4');
                                    $('.'+field+'_error').html('<span class="alert alert-danger my-2 '+field+'_error_message">'+message+'</span>');

                                    e.preventDefault();
                                }
                            }
                        });
                    } else {
                        $('#myTabs a[href="#tab'+next_tab_number[1]+'"]').tab('show');
                    }

                    switch (current_tab_number) { 
                        case 1: 
                            field_array.push('first_name', 'last_name');
                            break;
                        case 2:
                            field_array.push('gender', 'email');
                            break;
                        case 3: 
                            field_array.push('password', 'confirm_password');
                            break;
                        default:
                            field_array = [];
                    }
                  
                    if (!$.isEmptyObject(field_array)) {
                        $.each(field_array, function(i, field){
                            if($.inArray(field, error_field) == -1) {
                                if ($('.'+field+'_error_message').length > 0) {
                                    $('.'+field+'_error').removeClass('my-4');
                                    $('.'+field+'_error_message').remove();
                                }
                            }
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $('.tab_submit').click(function(e){
            e.preventDefault();
            var form_submit_data = {};
            
            for (var i=1; i<=4; i++) {
                form_submit_data['form_' + i] = $('#tab' + i + '_form').serialize();
            }

            form_submit_data['tab_submit'] = "tab_submit=true";

            $.ajax({
                url: url,
                data: form_submit_data,
                dataType: 'json',
                type: 'POST',
                cache: false,
                success:function(res){
                    var error_field = [];
                    var tab_number_to_focus = [];

                    if (!$.isEmptyObject(res)) {
                        $.each(res, function(field, message){
                            var error_tab_id = matches = tab_number = '';
                            if (field != '') {
                                error_field.push(field);

                                $('.'+field+'_error').addClass('my-4');
                                $('.'+field+'_error').html('<span class="alert alert-danger my-2 '+field+'_error_message">'+message+'</span>');

                                if ($.isEmptyObject(tab_number_to_focus) && (field == 'first_name' || field == 'last_name')) {
                                    error_tab_id = $('#'+field).closest('div[id^="tab"]').attr('id');
                                    matches = error_tab_id.match(/(tab\S)/g);

                                    tab_number = matches[0].split('tab');
                                    tab_number_to_focus.push(tab_number[1]);
                                    //console.log(tab_number_to_focus);
                                } else if ($.isEmptyObject(tab_number_to_focus) && (field == 'gender' || field == 'email')) {
                                    if (field == 'gender') {
                                        error_tab_id = $('#gender_male').closest('div[id^="tab"]').attr('id');
                                    } else {
                                        error_tab_id = $('#'+field).closest('div[id^="tab"]').attr('id');
                                    }
                                    matches = error_tab_id.match(/(tab\S)/g);

                                    tab_number = matches[0].split('tab');
                                    tab_number_to_focus.push(tab_number[1]);
                                } else if ($.isEmptyObject(tab_number_to_focus) && (field == 'password' || field == 'confirm_password')) {
                                    error_tab_id = $('#'+field).closest('div[id^="tab"]').attr('id');
                                    matches = error_tab_id.match(/(tab\S)/g);

                                    tab_number = matches[0].split('tab');
                                    tab_number_to_focus.push(tab_number[1]);
                                    //console.log(tab_number_to_focus);
                                } else if ($.isEmptyObject(tab_number_to_focus) && (field == 'phone' || field == 'terms_condition')) {
                                    error_tab_id = $('#'+field).closest('div[id^="tab"]').attr('id');
                                    matches = error_tab_id.match(/(tab\S)/g);

                                    tab_number = matches[0].split('tab');
                                    tab_number_to_focus.push(tab_number[1]);
                                    //console.log(tab_number_to_focus);
                                }
                            } else {
                                tab_number_to_focus = [];
                            }
                        });
                    }

                    if (!$.isEmptyObject(tab_number_to_focus)) {
                        tab_number_to_focus = tab_number_to_focus[0];
                    }

                    $('#myTabs a[href="#tab'+tab_number_to_focus+'"]').tab('show');

                    var field_array = ['first_name', 'last_name', 'gender', 'email', 'phone', 'password', 'confirm_password', 'terms_condition'];
                    $.each(field_array, function(i, field){
                        if($.inArray(field, error_field) == -1) {
                            if ($('.'+field+'_error_message').length > 0) {
                                $('.'+field+'_error').removeClass('my-4');
                                $('.'+field+'_error_message').remove();
                            }
                        }
                    });

                    if (res.inseted_success == true) {
                        window.location = 'view_reg_records.php?registration_success=true';
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });

        });
    });
</script>
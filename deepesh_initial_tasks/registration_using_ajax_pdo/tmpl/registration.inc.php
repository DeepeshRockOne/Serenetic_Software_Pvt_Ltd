<div class="container mt-5">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <h3 class="text-center">Registration</h3>
            <form action="" id="reg_form" method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name">
                    <div class="first_name_error"></div>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name">
                    <div class="last_name_error"></div>
                </div>
                <div class="form-group">
                    <label class="mb-2">Gender:</label>
                    <div class="form-control">
                        <input type="radio" name="gender" value="Male">Male
                        <input type="radio" name="gender" value="Female">Female
                    </div>
                    <div class="gender_error"></div>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
                    <div class="email_error"></div>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone Number">
                    <div class="phone_error"></div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                    <div class="password_error"></div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Confirm Password">
                    <div class="confirm_password_error"></div>
                </div>
                <div class="form-group">
                    <div class="my-2">
                        <input type="checkbox" name="terms_condition" value="1" id="terms_condition">
                        <label for="terms_condition">Terms & condition</label>
                    </div>
                    <div class="terms_condition_error"></div>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary reg_submit" name="reg_submit" value="Submit">
                </div>
            </form>
        </div>
        <div class="col-3"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var url = 'ajax_registration.php';
        $(".reg_submit").click(function(e){
            e.preventDefault();
            var form_data = $("#reg_form").serialize();
            form_data = form_data + '&reg_submit=true';

            $.ajax({
                url: url,
                data: form_data,
                dataType: 'json',
                type: 'POST',
                cache: false,
                success:function(res){
                    var error_field = [];
                    $.each(res, function(field, message){
                        if (field != '') {
                            error_field.push(field);

                            $('.'+field+'_error').addClass('my-4');
                            $('.'+field+'_error').html('<span class="alert alert-danger my-2 '+field+'_error_message">'+message+'</span>');
                        }
                    });

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
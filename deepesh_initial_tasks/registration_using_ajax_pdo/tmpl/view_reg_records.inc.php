<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h3 class="text-center">View Registered Records</h3>
            <?php
                if (isset($_GET['registration_success']) && $_GET['registration_success'] == true) {
            ?>
                <div class="my-4 text-center registration_success">
                    <span class="alert alert-success">Registration Successfully</span>
                </div>
            <?php
                }
            ?>
            
            <div class="record_deleted_success"></div>

            <div class="record_update_success"></div>

            <?php
                if (isset($_GET['record_not_found']) && $_GET['record_not_found'] == true) {
            ?>
                <div class="my-4 text-center record_not_found">
                    <span class="alert alert-danger">Record not found</span>
                </div>
            <?php
                }
            ?>
            <table class="table table-striped" id="reg_records_table" border="1">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) {
                        foreach ($data as $d) {
                    ?>
                            <tr>
                                <td><?php echo $d['id']; ?></td>
                                <td><?php echo $d['first_name']; ?></td>
                                <td><?php echo $d['last_name']; ?></td>
                                <td><?php echo $d['gender']; ?></td>
                                <td><?php echo $d['email']; ?></td>
                                <td><?php echo $d['phone']; ?></td>
                                <td><?php echo $d['created_at']; ?></td>
                                <td><?php echo $d['updated_at']; ?></td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-primary edit_reg_record" data-editId="<?php echo $d['id']; ?>">Edit</a>
                                    <a href="javascript:void(0)" class="btn btn-danger delete_reg_record" data-deleteId="<?php echo $d['id']; ?>">Delete</a>
                                </td>
                            </tr>
                    <?php
                            }
                        } else {
                    ?>
                        <tr>
                            <td class="text-center" colspan="9">There is no records.</td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>

            <!-- Modal -->
                <div class="modal fade" id="modal_edit_reg" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title modal_edit_reg_title">Modal Header</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <form action="" id="update_reg_modal"  method="post">
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
                                                <input type="radio" name="gender" id="gender_male" value="Male">Male
                                                <input type="radio" name="gender" id="gender_female" value="Female">Female
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
                                            <div class="my-2">
                                                <input type="checkbox" name="terms_condition" value="1" id="terms_condition">
                                                <label for="terms_condition">Terms & condition</label>
                                            </div>
                                            <div class="terms_condition_error"></div>
                                        </div>
                                        <div class="form-group text-center">
                                            <input type="submit" class="btn btn-primary reg_update" name="reg_update" value="Update">
                                        </div>
                                        <input type="hidden" name="update_id" id="hidden_update_id">
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- Modal -->
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var url = 'ajax_view_reg_records.php';
        //delete record
        $("#reg_records_table").on('click', '.delete_reg_record', function(e){
            if (confirm('Are you sure you want to delete this?')) {
                var delete_id = $(this).attr('data-deleteId');
                var clicked_td = $(this);

                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'GET',
                    data: {'delete_id':delete_id},
                    cache: false,
                    success:function(res){
                        if (res.reg_delete_success) {
                            clicked_td.closest('tr').remove();
                            var tableRowCount = $('#reg_records_table').find('tr').length - 1;

                            if ($('.record_not_found').length > 0) {
                                $('.record_not_found').remove();
                            }

                            if ($('.registration_success').length > 0) {
                                $('.registration_success').remove();
                            }

                            $('.record_update_success').html('');
                            if ($('.record_update_success').hasClass("my-4 text-center")) {
                                $('.record_update_success').removeClass("my-4 text-center");
                            }

                            $('.record_deleted_success').addClass("my-4 text-center");
                            $('.record_deleted_success').html('<span class="alert alert-success">Record deleted Successfully</span>');

                            if (tableRowCount == 0) {
                                $('.view_reg_records')[0].click();
                            }
                        }
                        
                        if (res.record_not_found) {
                            $('.record_deleted_success').html('');
                            if ($('.record_deleted_success').hasClass("my-4 text-center")) {
                                $('.record_deleted_success').removeClass("my-4 text-center");
                            }
                            window.location = 'view_reg_records.php?record_not_found=true';
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });

        $("#reg_records_table").on('click', '.edit_reg_record', function(e){
            var edit_id = $(this).attr('data-editId');

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'GET',
                data: {'edit_id':edit_id},
                cache: false,
                success:function(res){
                    if (Object.keys(res).length > 0) {
                        $('#modal_edit_reg').modal('show');
                        $('.modal_edit_reg_title').html('Edit Registered Record');

                        $('#first_name').val(res[0].first_name);
                        $('#last_name').val(res[0].last_name);

                        if (res[0].gender == "Male") {
                            $("#gender_male").prop("checked", true);
                        }
                        if (res[0].gender == "Female") {
                            $("#gender_female").prop("checked", true);
                        }
                        
                        $('#email').val(res[0].email);
                        $('#phone').val(res[0].phone);

                        if (res[0].terms_condition) {
                            $("#terms_condition").prop("checked", true);
                        }

                        $('#hidden_update_id').val(res[0].id);
                    }
                    
                    if (res.record_not_found) {
                        $('.record_update_success').html('');
                        if ($('.record_update_success').hasClass("my-4 text-center")) {
                            $('.record_update_success').removeClass("my-4 text-center");
                        }
                        window.location = 'view_reg_records.php?record_not_found=true';
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $(".reg_update").click(function(e){
            e.preventDefault();
            var update_id = $('#hidden_update_id').val();
            var form_data = $("#update_reg_modal").serialize();
            form_data = form_data + '&reg_update=true&update_id='+update_id;

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'POST',
                data: form_data,
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

                    var field_array = ['first_name', 'last_name', 'gender', 'email', 'phone', 'terms_condition'];
                    $.each(field_array, function(i, field){
                        if($.inArray(field, error_field) == -1) {
                            if ($('.'+field+'_error_message').length > 0) {
                                $('.'+field+'_error').removeClass('my-4');
                                $('.'+field+'_error_message').remove();
                            }
                        }
                    });

                    if (res.update_success == true) {
                        if ($('.registration_success').length > 0) {
                            $('.registration_success').remove();
                        }

                        $('.record_deleted_success').html('');
                        if ($('.record_deleted_success').hasClass("my-4 text-center")) {
                            $('.record_deleted_success').removeClass("my-4 text-center");
                        }

                        $('.record_update_success').addClass("my-4 text-center");
                        $('.record_update_success').html('<span class="alert alert-success">Record udpted Successfully</span>');

                        $.ajax({
                            url: url,
                            dataType: 'json',
                            type: 'GET',
                            data: {'view_reg_records_after_update':true},
                            cache: false,
                            success:function(res){
                                $('#modal_edit_reg').modal('hide');
                                $('#reg_records_table tbody').empty();

                                var table_body_content = '';
                                $.each(res, function(field, value){
                                    table_body_content += '<tr>';
                                    table_body_content += '<td>' + value.id + '</td>';
                                    table_body_content += '<td>' + value.first_name + '</td>';
                                    table_body_content += '<td>' + value.last_name + '</td>';
                                    table_body_content += '<td>' + value.gender + '</td>';
                                    table_body_content += '<td>' + value.email + '</td>';
                                    table_body_content += '<td>' + value.phone + '</td>';
                                    table_body_content += '<td>' + value.created_at + '</td>';
                                    table_body_content += '<td>' + value.updated_at + '</td>';

                                    table_body_content += '<td><a href="javascript:void(0)" class="btn btn-primary edit_reg_record" data-editId='+value.id+'>Edit</a> <a href="javascript:void(0)" class="btn btn-danger delete_reg_record" data-deleteId='+value.id+'>Delete</a></td>';
                                    table_body_content += '</tr>';
                                });

                                $('#reg_records_table tbody').html(table_body_content);
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                        //window.location = 'view_reg_records.php?record_update_success=true';
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });
</script>

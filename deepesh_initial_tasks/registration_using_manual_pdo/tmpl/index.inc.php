<div class="container mt-5">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <h3 class="text-center">Registration</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" value="<?php echo $first_name; ?>">
                    <?php if (isset($validation_errors['first_name'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2">
                                <?php echo $validation_errors['first_name']; ?>
                            </span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name" value="<?php echo $last_name; ?>">
                    <?php if (isset($validation_errors['last_name'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['last_name']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label class="mb-2">Gender:</label>
                    <div class="form-control">
                        <input type="radio" name="gender" value="Male" <?php echo (isset($gender) && $gender == "Male") ? 'checked=checked' : ''; ?>>Male
                        <input type="radio" name="gender" value="Female" <?php echo (isset($gender) && $gender == "Female") ? 'checked=checked' : ''; ?>>Female
                    </div>
                    <?php if (isset($validation_errors['gender'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['gender']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo (isset($email)) ? $email : ''; ?>">
                    <?php if (isset($validation_errors['email'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['email']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone Number" value="<?php echo (isset($phone)) ? $phone : ''; ?>">
                    <?php if (isset($validation_errors['phone'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['phone']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" value="<?php echo (isset($password)) ? $password : ''; ?>">
                    <?php if (isset($validation_errors['password'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['password']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Confirm Password">
                    <?php if (isset($validation_errors['confirm_password'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2">
                                <?php echo $validation_errors['confirm_password']; ?>
                            </span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <div class="my-2">
                        <input type="checkbox" name="terms_condition" value="1" id="terms_condition" <?php echo (isset($terms_condition) && $terms_condition == 1) ? 'checked=checked' : ''; ?>>
                        <label for="terms_condition">Terms & condition</label>
                    </div>
                    <?php if (isset($validation_errors['terms_condition'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['terms_condition']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" name="reg_submit" value="Submit">
                </div>
            </form>
        </div>
        <div class="col-3"></div>
    </div>
</div>
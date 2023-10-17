<?php
    if (isset($data[0]['first_name'])) {
        $fname = $data[0]['first_name'];
    } elseif (isset($first_name)) {
        $fname = $first_name;
    } else {
        $fname = '';
    }

    if (isset($data[0]['last_name'])) {
        $lname = $data[0]['last_name'];
    } elseif (isset($last_name)) {
        $lname = $last_name;
    } else {
        $lname = '';
    }

    if (isset($data[0]['gender'])) {
        $gen = $data[0]['gender'];
    } else if (isset($gender)) {
        $gen = $gender;
    }

    if (isset($data[0]['email'])) {
        $eml = $data[0]['email'];
    } elseif (isset($email)) {
        $eml = $email;
    } else {
        $eml = '';
    }

    if (isset($data[0]['phone'])) {
        $phn = $data[0]['phone'];
    } elseif (isset($phone)) {
        $phn = $phone;
    } else {
        $phn = '';
    }

    if (isset($data[0]['terms_condition'])) {
        $trm_condition = $data[0]['terms_condition'];
    } elseif (isset($terms_condition)) {
        $trm_condition = $terms_condition;
    } else {
        $trm_condition = '';
    }

    if (isset($data[0]['id'])) {
        $up_id = $data[0]['id'];
    } elseif (isset($first_name)) {
        $up_id = $update_id;
    }
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <h3 class="text-center">Edit Registred Record</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" value="<?php echo $fname; ?>">
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
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name" value="<?php echo $lname; ?>">
                    <?php if (isset($validation_errors['last_name'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['last_name']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label class="mb-2">Gender:</label>
                    <div class="form-control">
                        <input type="radio" name="gender" value="Male" <?php echo ($gen == "Male") ? 'checked=checked' : ''; ?>>Male
                        <input type="radio" name="gender" value="Female" <?php echo ($gen == "Female") ? 'checked=checked' : ''; ?>>Female
                    </div>
                    <?php if (isset($validation_errors['gender'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['gender']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo $eml; ?>">
                    <?php if (isset($validation_errors['email'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['email']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone Number" value="<?php echo $phn; ?>">
                    <?php if (isset($validation_errors['phone'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['phone']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <div class="my-2">
                        <input type="checkbox" name="terms_condition" value="1" id="terms_condition" <?php echo ($trm_condition == 1) ? 'checked=checked' : ''; ?>>
                        <label for="terms_condition">Terms & condition</label>
                    </div>
                    <?php if (isset($validation_errors['terms_condition'])) { ?>
                        <div class="my-4">
                            <span class="alert alert-danger my-2"><?php echo $validation_errors['terms_condition']; ?></span>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" name="reg_update" value="Update">
                </div>
                <input type="hidden" name="update_id" value="<?php echo $up_id; ?>">
            </form>
        </div>
        <div class="col-3"></div>
    </div>
</div>
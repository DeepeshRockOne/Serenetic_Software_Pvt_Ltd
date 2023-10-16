<?php
	require_once("header.php");
	require_once("sotre_register.php");
?>
	<div class="container mt-5">
		<div class="row">
			<div class="col-3"></div>
			<div class="col-6">
				<h3 class="text-center">Registration</h3>
				<form action="" method="post">
					<div class="form-group">
						<label for="first_name">First Name:</label>
						<input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" value="<?php echo $first_name; ?>">
						<?php if (isset($first_name_error) && $first_name_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $first_name_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label for="last_name">Last Name:</label>
						<input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name" value="<?php echo $last_name; ?>">
						<?php if (isset($last_name_error) && $last_name_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $last_name_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label class="mb-2">Gender:</label>
						<div class="form-control">
							<input type="radio" name="gender" value="Male" <?php echo ($gender == "Male") ? 'checked=checked' : ''; ?>>Male
							<input type="radio" name="gender" value="Female" <?php echo ($gender == "Female") ? 'checked=checked' : ''; ?>>Female
						</div>
						<?php if (isset($gender_error) && $gender_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $gender_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo $email; ?>">
						<?php if (isset($email_error) && $email_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $email_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label for="phone">Phone Number:</label>
						<input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone Number" value="<?php echo $phone; ?>">
						<?php if (isset($phone_error) && $phone_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $phone_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" value="<?php echo $password; ?>">
						<?php if (isset($password_error) && $password_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $password_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<label for="confirm_password">Confirm Password:</label>
						<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Confirm Password">
						<?php if (isset($confirm_password_error) && $confirm_password_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $confirm_password_error; ?></span>
							</div>
						<?php } ?>
					</div>
					<div class="form-group">
						<div class="my-2">
							<input type="checkbox" name="terms_condition" value="1" id="terms_condition" <?php echo ($terms_condition == 1) ? 'checked=checked' : ''; ?>>
							<label for="terms_condition">Terms & condition</label>
						</div>
						<?php if (isset($terms_condition_error) && $terms_condition_error != '') { ?>
							<div class="my-4">
								<span class="alert alert-danger my-2"><?php echo $terms_condition_error; ?></span>
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
<?php
	require_once("footer.php");
?>
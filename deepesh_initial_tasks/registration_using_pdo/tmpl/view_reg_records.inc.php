<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h3 class="text-center">View Registered Records</h3>
            <?php
                if (isset($_GET['registration_success']) && $_GET['registration_success'] == true) {
            ?>
                <div class="my-4 text-center">
                    <span class="alert alert-success">Registration Successfully</span>
                </div>
            <?php
                }
            ?>
            <?php
                if (isset($_GET['reg_delete_success']) && $_GET['reg_delete_success'] == true) {
            ?>
                <div class="my-4 text-center">
                    <span class="alert alert-success">Record deleted Successfully</span>
                </div>
            <?php
                }
            ?>
            <?php
                if (isset($_GET['record_update_success']) && $_GET['record_update_success'] == true) {
            ?>
                <div class="my-4 text-center">
                    <span class="alert alert-success">Record udpted Successfully</span>
                </div>
            <?php
                }
            ?>
            <?php
                if (isset($_GET['record_not_found']) && $_GET['record_not_found'] == true) {
            ?>
                <div class="my-4 text-center">
                    <span class="alert alert-danger">Record not found</span>
                </div>
            <?php
                }
            ?>
            <table class="table table-striped" border="1">
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
                                    <a href="<?php echo "view_reg_records.php?edit_id=".$d['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="<?php echo "view_reg_records.php?delete_id=".$d['id']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure to delete?')">Delete</a>
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
        </div>
    </div>
</div>
<?php include("notify.inc.php"); ?>
<div class="tabbing-tab">
    <ul class="nav nav-tabs customtab nav-noscroll">
        <?php
        $setting = "active";
        include("br_broadcaster_tabs.inc.php");
        ?>
    </ul>
</div>
<div class="panel panel-default panel-block">
    <div class="panel-heading">
        <div>
            <i class="fa fa-envelope"></i>
            <h1><span>&nbsp; Manage Test Groups</span></h1>
        </div>
    </div>
</div>
<div class="panel panel-default panel-block panel-title-block">
    <div class="list-group">
        <div class="list-group-item">					
            <div class="panel panel-default panel-block panel-title-block">
                <div class="panel-heading">
                    <div><h1><span>Add Test Group</span></h1></div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-md-12">
                        <form id="frm_category" class="form-inline" name="frm_category" action="" method="POST">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" id="title" class="form-control" name="title" value='<?php echo $title ?>' />
                                        <?php if (isset($errors['title'])): ?>
                                            <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                                        <?php endif; ?>
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Company</label>
                                    <select id="company_id" name="company_id" class="form-control <?php echo isset($errors['company_id']) ? 'parsley-error' : '' ?>">
                                        <option value="">-- Select company --</option>
                                        <?php foreach ($company_res AS $key => $row) { ?>
                                            <option value="<?= $row['id'] ?>" <?= $company_id == $row['id'] ? 'selected' : '' ?>><?= $row['company_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($errors['company_id'])): ?>
                                        <ul class="parsley-error-list"><li class="required"><?php echo $errors['company_id'] ?></li></ul>
                                    <?php endif; ?>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" name="cat_save" id="cat_save" class="btn btn-info">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>                
                </div> 
            </div>
        </div>       
        <div class="list-group-item">
            <table class="table table-striped table-small color-table info-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="index">ID</th>
                        <th style="width: 45%;">Title</th>              
                        <th style="width: 30%;">Date/Time</th>             
                        <th style="width: 20%;" class="icons">Manage</th>             
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) > 0) { ?>
                        <?php foreach ($rows as $key => $row) { ?>        
                            <tr>
                                <td class="index"><?php echo $row['id']; ?></td>
                                <td class=""><?php echo $row['title']; ?></td>
                                <td class=""><?php echo retrieveDate($row['created_at']); ?></td>             
                                <td class="icons">
                                    <a href="test_email_group_addresses.php?id=<?php echo $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="Manage Email Addresses" class="popup" ><i class="fa fa-envelope"></i></a> 
                                    <a href="edit_test_email_groups.php?id=<?php echo $row['id']; ?>" title="" data-toggle="tooltip" data-original-title="Edit Test Group" class="edit_popup"><i class="fa fa-edit"></i></a> 
                                    <a href="<?php echo url_for('test_email_groups.php?del_id=' . $row['id']); ?>" title="" data-toggle="tooltip" data-original-title="Delete Test Group" onClick="return confirmAction();"><i class="fa fa-trash"></i></a>
                                </td>             
                            </tr>              
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4">No record(s) found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">
    function confirmAction() {
        var confirmed = confirm("Are you sure you want to delete this test group? \nNote: If you delete this test group, related email addresses will also automatically deleted.");
        return confirmed;
    }
    $(document).ready(function () {
        jQuery(".popup").colorbox({iframe: true, width: '80%', height: '600px', overflow: 'hidden'});
        jQuery(".edit_popup").colorbox({iframe: true, width: '40%', height: '300px', overflow: 'hidden'});
    });
</script>
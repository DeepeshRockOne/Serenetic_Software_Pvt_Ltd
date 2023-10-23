<?php include("notify.inc.php"); ?>
<div class="panel panel-default panel-popup">
    <div class="panel-heading">
        <div class="panel-title"><i class="fa-pencil-square-o fa"></i><span>Edit Title</span>
    </div>
</div>
<div class="panel-body">
    <form id="frm_category" class="form_wrap" name="frm_category" action="edit_test_email_groups.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="fields" class="col-xs-2 m-t-10">Title</label>
            <div class="col-xs-10">
                <input type="text" id="title" class="form-control" name="title" value='<?php echo $title ?>' />
                <?php if (isset($errors['title'])): ?>
                <ul class="parsley-error-list"><li class="required"><?php echo $errors['title'] ?></li></ul>
                <?php endif; ?>
                <?php if ($id != "") { ?>
                <input type="hidden" name="id" value="<?php echo $id ?>"/>
                <?php } ?>
                &nbsp;
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="fields" class="col-xs-2 m-t-10">Company</label>
            <div class="col-xs-10">
                <select id="company_id" name="company_id" class="form-control <?php echo isset($errors['company_id']) ? 'parsley-error' : '' ?>">
                    <option value="">-- Select company --</option>
                    <?php if(!empty($company_res)){ ?>
                        <?php foreach ($company_res AS $key => $row) { ?>
                            <option value="<?= $row['id'] ?>" <?= $company_id == $row['id'] ? 'selected' : '' ?>><?= $row['company_name'] ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?php if (isset($errors['company_id'])): ?>
                <ul class="parsley-error-list"><li class="required"><?php echo $errors['company_id'] ?></li></ul>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-group text-center">
                <button type="submit" name="cat_save" id="cat_save" class="btn btn-info" >Save</button>
            </div>
        </div>
    </form>
</div>
</div>

<!-- </section> -->
<script type="text/javascript">
    $(document).ready(function () {
        $('cat_save').click(function () {
            $.self.close();
        })
    });
</script>
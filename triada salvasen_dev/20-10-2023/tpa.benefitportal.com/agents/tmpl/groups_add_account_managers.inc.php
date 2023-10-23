<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">+ Account Managers</h4>
    </div>
  </div>
  <div class="panel-body">
    <form id="create_new_admin_level_frm"   method="POST">
      <input type="hidden" name="group_id" value="<?=$_GET['group_id']?>">
      <div class="row">
        <?php if(!empty($sub_group)){ ?>
          <input type="hidden" name="sub_id" value="<?=$_GET['sa_id']?>">
          <input type="hidden" name="operation" value="edit_sub_group">
          <div class="col-sm-5">
          <div class="theme-form">
            <div class="form-group">
              <input type="text" class="form-control" name="fname" id="fname" value="<?=checkIsset($sub_group['fname'])?>" />
              <label>First Name<em>*</em></label>
              <span class="error"><?=checkIsset($errors['fname'])?></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="lname" id="lname" value="<?=checkIsset($sub_group['lname'])?>" />
              <label>Last Name<em>*</em></label>
              <span class="error"><?=checkIsset($errors['lname'])?></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control no_space" name="email" id="email" value="<?=checkIsset($sub_group['email'])?>" />
              <label>Email<em>*</em></label>
              <span class="error"><?=checkIsset($errors['email'])?></span>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="password" id="password" value="<?=checkIsset($password)?>"  />
              <label>Password<em>*</em></label>
              <span class="error"><?=checkIsset($errors['password'])?></span>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="cpassword" id="cpassword"  value="<?=checkIsset($cpassword)?>" />
              <label>Confirm Password<em>*</em></label>
              <span class="error"><?=checkIsset($errors['cpassword'])?></span>
            </div>
            <div class="clearfix">
              <div class="phone-control-wrap">
                <div class="phone-addon text-left w-42 v-align-top">
                  <input type="checkbox" name="passcode" value="Y" id="passcode" <?=checkIsset($sub_group['passcode']) == 'Y' ? 'checked="checked"' : '' ?>  /> 
                </div>
                <div class="phone-addon text-left">
              <label for="passcode" class="mn">Check this box if account manager may manage members without requesting a passcode.</label>
            </div>
            </div>
            </div>
          </div>
        </div>
        <?php }else{?>
        <div class="col-sm-5">
          <div class="theme-form">
            <div class="form-group">
              <input type="text" class="form-control" name="fname" id="fname" value="<?=checkIsset($fname)?>" />
              <label>First Name<em>*</em></label>
              <span class="error"><?=checkIsset($errors['fname'])?></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="lname" id="lname" value="<?=checkIsset($lname)?>" />
              <label>Last Name<em>*</em></label>
              <span class="error"><?=checkIsset($errors['lname'])?></span>
            </div>
            <div class="form-group">
              <input type="text" class="form-control no_space" name="email" id="email" value="<?=checkIsset($email)?>" />
              <label>Email<em>*</em></label>
              <span class="error"><?=checkIsset($errors['email'])?></span>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="password" id="password" value="<?=checkIsset($password)?>"  />
              <label>Password<em>*</em></label>
              <span class="error"><?=checkIsset($errors['password'])?></span>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" name="cpassword" id="cpassword"  value="<?=checkIsset($cpassword)?>" />
              <label>Confirm Password<em>*</em></label>
              <span class="error"><?=checkIsset($errors['cpassword'])?></span>
            </div>
            <div class="clearfix">
              <div class="phone-control-wrap">
                <div class="phone-addon text-left w-42 v-align-top">
                  <input type="checkbox" name="passcode" value="Y" id="passcode" />
                </div>
                <div class="phone-addon text-left ">
                   <label for="passcode" class="mn"> Check this box if account manager may manage members without requesting a passcode.</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="col-sm-7">
            <div class="table-responsive">
              <?php 
                $account_manager = 'account_manager';
                include 'groups_access_edit.inc.php'; 
              ?>
              <span class="error"><?=checkIsset($errors['features'])?></span>
            </div>
        </div>
      </div>
      <div class="text-center m-t-30"> 
        <button class="btn btn-action" type="submit" name="save">Save</button>
        <a href="groups_account_managers.php?group_id=<?=$_GET['group_id']?>" class="btn red-link">Back</a>
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    checkEmail();
  })
</script>
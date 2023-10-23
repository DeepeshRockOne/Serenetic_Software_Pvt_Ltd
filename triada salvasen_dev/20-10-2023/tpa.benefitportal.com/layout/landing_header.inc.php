<div class="header">
  <div class="container">
    <div class="row">
    <div class="dropdown">
      <button class="dropdown-toggle" type="button" data-toggle="dropdown">Login
      <span class="caret"></span></button>
      <ul class="dropdown-menu">
        <li><a href="<?php echo $HOST; ?>/admin/">Admins</a></li>
        <li><a href="<?php echo $HOST; ?>/agents/">Brokers</a></li>
        <li><a href="<?php echo $HOST; ?>/groups/">Groups</a></li>
        <li><a href="<?php echo $CUSTOMER_HOST; ?>/">Members</a></li>
      </ul>
    </div>
    <div class="mobile-dropdown">
       <a href="javascript:void(0);" class="menu-icon hide" > 
        <span class="bar-1"></span> 
        <span class="bar-2"></span> 
        <span class="bar-3"></span> 
      </a>
      <ul id="menu-ul" style="display: none;">
        <li><a href="<?php echo $HOST; ?>/admin/">Admins</a></li>
        <li><a href="<?php echo $HOST; ?>/agents/">Brokers</a></li>
        <li><a href="<?php echo $HOST; ?>/groups/">Groups</a></li>
        <li><a href="<?php echo $HOST; ?>/member/">Members</a></li>
      </ul>
    </div>
  </div>
  </div>
</div>

<script type="text/javascript">
$(document).off("click", ".menu-icon");
$(document).on("click", ".menu-icon", function() {
    $("#menu-ul").show();
    $(".menu-icon").toggleClass('active');
    $('body').toggleClass('menu-overlay');
});
$(document).off("click", ".menu-icon.active");
$(document).on("click", ".menu-icon.active", function() {
    $("#menu-ul").hide();
});
</script>
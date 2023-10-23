<?php
  $popup_open_res = false;

if ($_SESSION["groups"]['id'] > 0 && in_array($_SESSION['groups']['status'],array('Pending Approval','Pending Contract','Pending Documentation'))) {
	$popup_open_res = true;

}
$Header_agent_id = $_SESSION['groups']['id'];


$businessNameHeader = !empty($_SESSION['groups']['public_name']) ? $_SESSION['groups']['public_name'] : '';

$headerResource = "SELECT label,url FROM group_resource_link WHERE is_deleted='N' AND group_id=:group_id";
$headerResource = $pdo->select($headerResource,array(":group_id"=>$_SESSION['groups']['id']));

?>
<div class="header">
	<div class="container">
       <div class="dropdown agent_dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                
                <div class="agent_circle"><?=  $_SESSION['groups']['fname'][0] . $_SESSION['groups']['lname'][0] ?></div>
                <?=  $_SESSION['groups']['fname'] . " " . $_SESSION['groups']['lname'] ?>
                
            </a>
            <?php if (!$popup_open_res) {?>
              <span class="caret"></span>
            <ul class="dropdown-menu dropdown-menu-left">
                    <li><a href="profile.php"><i class="ti-user"></i> My Profile</a></li>
                    <li><a href="personal_branding.php" class="btn_personal_branding"><i class="ti-palette"></i> Personal Branding</a></li>
                    <input type="hidden" name="previous_page" id="previous_page" value="<?=urlencode($_SERVER['REQUEST_URI'])?>">
                    <li><a href="javascript:void(0);" class="btn_logout_portal"><i class="fa fa-power-off"></i> Logout</a></li>
            </ul>
            <?php }?>
       </div>
       <div class="menu">
       
       	<ul id="menu-ul">
        	<li><a href="dashboard.php"><i class="material-icons">home</i></a></li>
          <?php if (!$popup_open_res) { ?>
            <?php if (group_has_menu_access(1)) {?>
              <li class="dropdown ">
                	<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Users  <span class="caret"></span></a>
                     <ul class="dropdown-menu">
                        <?php if(group_has_menu_access(2)) { ?><li><a href="member_listing.php">Members</a></li><?php } ?>
                        <?php if(group_has_menu_access(3)) { ?><li><a href="group_classes.php">Classes</a></li><?php } ?>
                        <?php if(group_has_menu_access(4)) { ?><li><a href="group_enrollees.php">Enrollees</a></li><?php } ?>
                      </ul>
              </li>
            <?php } ?>
            <?php if (group_has_menu_access(5)) {?>
              <li>
                  <a href="group_billing.php">Billing </a>
              </li>
            <?php } ?>
            <?php if (group_has_menu_access(6)) {?>
              <li class="dropdown ">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Resources  <span class="caret"></span></a>
                   <ul class="dropdown-menu">
                      <?php if(group_has_menu_access(7)) { ?><li><a href="coverage_periods.php">Coverage Periods</a></li><?php } ?>
                      <?php if(group_has_menu_access(8)) { ?><li><a href="add_email_broadcast.php">Email Broadcaster</a></li><?php } ?>
                      <?php if(group_has_menu_access(9)) { ?><li><a href="manage_website.php">Enrollment Websites</a></li><?php } ?>
                      <?php if(group_has_menu_access(10)) { ?><li><a href="product_informations.php">Products</a></li><?php } ?>
                      <?php if(group_has_menu_access(11)) { ?><li><a href="reporting.php">Reporting</a></li><?php } ?>
                      <?php if(group_has_menu_access(12)) { ?><li><a href="add_sms_broadcast.php">Text Broadcaster</a></li><?php } ?>
                      <?php if(group_has_menu_access(13)) { ?><li><a href="communications_queue.php">Communications Queue</a></li><?php } ?>
                      <?php if(!empty($headerResource)){ ?>
                        <?php foreach ($headerResource as $headerRKey => $headerRValue) { ?>
                          <?php 
                            $tmpHeaderUrl = $headerRValue['url'];
                            if (!preg_match("~^(?:f|ht)tps?://~i", $tmpHeaderUrl)) {
                                $tmpHeaderUrl = "http://" . $tmpHeaderUrl;
                            }
                          ?>
                          <li><a href="<?= $tmpHeaderUrl ?>" target="_BLANK"><?= $headerRValue['label'] ?></a></li>
                        <?php } ?>
                      <?php } ?>
                    </ul>
              </li>
            <?php } ?>
          <?php }else{ ?>
            <li class=""><a href="javascript:void(0);" class="btn_logout_portal">Logout</a></li>
          <?php } ?>
        </ul>
       </div>
       <div class="right-icons">
        <ul class="nav navbar-top-links">
        <li>
         <a href="javascript:void(0);" class="menu-icon hide"> <span class="bar-1"></span> <span class="bar-2"></span> <span class="bar-3"></span> </a>
        </li>
         <?php if (group_has_menu_access(1)) {?>
        <li>
         <a href="javascript:void(0);" class="ricon-link" id="search-icon">  <i class="material-icons">search</i></a>
        </li>
         <?php } ?>
         <li>
         <a href="javascript:void(0);" class="ricon-link">  <i class="material-icons">notifications</i></a>
        </li>
         </ul>
       </div>
       <?php if (group_has_menu_access(1)) { ?>
          <div class="searching_panel top-right">
              <div id="searchbar_wrap">
                  <form method="GET" action="global_search.php" id="global_search" name="global_search" role="search" class="app-search ">
                      <div class="search-group">
                          <input type="text" name="gsearch" id="gsearch" placeholder="Search..." size="" class="form-control gsearch">
                          <input type="hidden" name="is_ajaxed" id="g_is_ajaxed" value="false" />
                          <input type="hidden" name="pages" id="g_per_pages" value="<?=$per_page;?>" />
                          <input type="hidden" name="sort" id="g_sort_column" value="<?=isset($SortBy) ? $SortBy : '';?>" />
                          <input type="hidden" name="direction" id="g_sort_direction" value="<?=isset($SortDirection) ? $SortDirection : '';?>" />
                          <input type="hidden" name="type" id="g_type" value="" />
                          <input type="hidden" name="rep_id" id="g_rep_id" value="" />
                          <input type="hidden" name="fname" id="g_fname" value="" />
                          <input type="hidden" name="email" id="g_email" value="" />
                          <input type="hidden" name="custom_date" id="g_custom_date" value="" />
                          <input type="hidden" name="fromdate" id="g_fromdate" value="" />
                          <input type="hidden" name="todate" id="g_todate" value="" /> 
                          <button type="submit" id="all_search"><i class="fa fa-search"></i></button>
                      </div>
                  </form>
              </div>      
          </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
/* responsive toggle menu script start */
$(document).off("click", ".menu-icon");
  $(document).on("click", ".menu-icon", function() {
    $("#menu-ul").slideToggle('300').toggleClass('show');
    $(".menu-icon").toggleClass('active');
    $('body').toggleClass('menu-overlay');
});
/* responsive toggle menu script end */
//global search end
$(document).ready(function () {
   /*Header menu active js */
    $(".btn_personal_branding").colorbox({iframe: true, width: '620px', height: '580px'});
    var e = window.location,
    i = $("#menu-ul li a").filter(function() {
        return this.href == e || 0 == e.href.indexOf(this.href)
    }).addClass("active").parent().parent().parent();
    i.is("li") && i.addClass("active")

  <?php if (group_has_menu_access(1)) { ?>
    $("#search-icon").popover({
        html : true,
        trigger : 'click',
        template : '<div class="popover searchpopover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
        content: function() {
            str = '<div id="searchbar_wrap_small">';
                      str += '<form method="GET" action="global_search.php" id="global_search_small" name="global_search_small" role="search" class="app-search ">';
                        str += '<div class="search-group">';
                      str += '<input type="text" name="gsearch" id="gsearch_small" placeholder="Search..." size="" class="form-control gsearch">';

            str +=  '<input type="hidden" name="is_ajaxed" id="g_is_ajaxed_small" value="false" />';
            str +=  '<input type="hidden" name="pages" id="g_per_pages_small" value="<?= isset($per_page)?$per_page:0;?>" />';
            str +=  '<input type="hidden" name="sort" id="g_sort_column_small" value="<?=isset($SortBy) ? $SortBy:"";?>" />';
            str +=  '<input type="hidden" name="direction" id="g_sort_direction_small" value="<?=isset($SortDirection)?$SortDirection:"";?>" />';
            str +=  '<input type="hidden" name="type" id="g_type_small" value="" />';
            str +=  '<input type="hidden" name="rep_id" id="g_rep_id_small" value="" />';
            str +=  '<input type="hidden" name="fname" id="g_fname_small" value="" />';
            str +=  '<input type="hidden" name="email" id="g_email_small" value="" />';
            str +=  '<input type="hidden" name="custom_date" id="g_custom_date_small" value="" />';
            str +=  '<input type="hidden" name="fromdate" id="g_fromdate_small" value="" />';
            str +=  '<input type="hidden" name="todate" id="g_todate_small" value="" />' ;
            str +=  '<button type="submit" id="all_search_small"><i class="fa fa-search"></i></button>';
            str +=  '</div>';
            str +=  '</form>';
            str +=  '</div>';
            return str;
        },
        placement: 'bottom',
        container: 'body'
    }); 
  <?php } ?>

  $('html').on('click', function (e) {
        $('#search-icon').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
              $(this).popover('hide');
            }
        });
  });

  $("#g_search").click(function () {
    $('#g_search').attr('placeholder', 'Type Name, Id, Email Or Phone to search');
  });

  $(document).on('click', '#all_search', function () {
    //var g_search = $("#gsearch").val();
    
    var g_search = $("#gsearch").val();
    if (g_search == "" || g_search == null) {
        alert("Enter Name,Id,Email Or Phone to search user");
        return false;
    } else {
        $("#g_is_ajaxed").val("0");
        $("#gsearch").val(g_search);
        $("#global_search").submit();
    }

  });
  $(document).on('click', '#all_search_small', function () {
    //var g_search = $("#gsearch").val();
    
    var g_search = $("#gsearch_small").val();
    if (g_search == "" || g_search == null) {
        alert("Enter Name,Id,Email Or Phone to search user");
        return false;
    } else {
        $("#g_is_ajaxed").val("0");
        $("#gsearch").val(g_search);
        $("#global_search").submit();
    }

  });
//global search end
});
</script>
<div class="sub-header">
  <div class="container">	
    
      <?php if (isset($breadcrumbes)): ?>
          <div id="page">
           <ol class="breadcrumb breadcrumb-nav">
          <?php $link = 'dashboard.php'; ?>
            <li><a href="<?=$link?>"><i class="material-icons">home</i></a></li>
             <?php //$breadcrumbes['title'] = '<i class="fa fa-home"></i>';
          // $breadcrumbes['link'] = 'dashboard.php';
             foreach ($breadcrumbes as $key => $breadcrumbe) { 
              if($key == 0)
                continue; ?>
               <li <?php echo isset($breadcrumbe['class']) ? 'class="' . $breadcrumbe['class'] . '"' : '' ?>>
                 <a href="<?= !empty($breadcrumbe['link']) ? $breadcrumbe['link'] : '' ?>"><?php echo isset($breadcrumbe['title']) ? $breadcrumbe['title'] : '' ?></a>
               </li>
             <?php } ?>
           </ol>
       </div>
        <?php endif;?>
  </div>
</div>
<div class="clearfix"></div>
<?php 
if ($popup_open_res) {
    if (basename($_SERVER['PHP_SELF']) != "group_enrollment.php") {
        redirect('group_enrollment.php');
    }
}
?>
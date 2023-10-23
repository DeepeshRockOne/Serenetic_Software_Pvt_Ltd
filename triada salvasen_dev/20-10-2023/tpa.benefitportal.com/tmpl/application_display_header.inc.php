<?php if (!empty($customer_res['sponsor_id'])) {
          $agentSponsorId = $customer_res['sponsor_id'];
        } else {
          $agentSponsorId = $_SESSION['SPONSOR_INFO']['id'];
        }
        $sponsorDetailSql = "SELECT * FROM customer WHERE id=:id and is_deleted='N'";
        $sponsorDetailRes = $pdo->selectOne($sponsorDetailSql, array(":id" => $agentSponsorId)); ?>


<div class="full-header">
  <div class="container">       
  	  <ul class="user-info">
      	<li class="title"><i class="material-icons text-action">info</i> Your Agent </li>
        <li><?= ($sponsorDetailRes['public_name'] != '') ? $sponsorDetailRes['public_name'] : (($sponsorDetailRes['business_name'] != '') ? $sponsorDetailRes['business_name'] : $sponsorDetailRes['fname'] . ' ' . $sponsorDetailRes['lname']) ?></li>
        <li><a href="tel:+<?= ($sponsorDetailRes['public_phone'] != '') ? format_telephone($sponsorDetailRes['public_phone']) : "-" ?>"><?= ($sponsorDetailRes['public_phone'] != '') ? format_telephone($sponsorDetailRes['public_phone']) : "-" ?></a></li>
        <li><a href="mailto:+<?= ($sponsorDetailRes['public_email'] != '') ? $sponsorDetailRes['public_email'] : "-" ?>"><?= ($sponsorDetailRes['public_email'] != '') ? $sponsorDetailRes['public_email'] : "-" ?></a></li>
      </ul>
      
  </div> 
</div>
<div class="agent_enroll_head patten_bg">
	<div class="container text-center">
    	<h1 class="fw300">Powering Change In Healthcare</h1>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    if ($(window).width() <= 767){
      $(document).off("click", ".mob-icon a");
      $(document).on("click", ".mob-icon a", function() {
        $(".agent_info").slideToggle("100");
      });
    }
});
</script>
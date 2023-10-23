<?php
$header_agent_name =  $agent_row['fname'].' '.$agent_row['lname'];
if(!empty($agent_row['public_name'])) {
    $header_agent_name = $agent_row['public_name'];
}
?>
<?php if($enrollmentLocation != "self_enrollment_site" && !isset($display_header)) { ?>
<div class="quote_header">
    <div class="container">
    	<h4 class="m-t-20 m-b-20">Welcome <span class="fw300 text-action"><?= $header_agent_name;?></span></h4>
    </div>
</div>
<?php } ?>


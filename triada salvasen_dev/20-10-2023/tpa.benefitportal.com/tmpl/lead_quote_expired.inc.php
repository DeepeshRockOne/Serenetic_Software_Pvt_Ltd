<?php 
if (hasNotify('success')) {
  $notify_class = "alert-success";
  $notify_message = getNotify('success');
} elseif (hasNotify('error')) {
  $notify_class = "alert-danger";
  $notify_message = getNotify('error');
} elseif (hasNotify('alert')) {
  $notify_class = "alert-warning";
  $notify_message = getNotify('alert');
}
?>

<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                   <h4 class="media-heading">It seems you have accessed a completed, expired, or restricted link.  <span>Please contact your services provider or the sender of this page for assistance.</span></h4>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>
<?php /*
	if($notify_message == "quote_expired"){
?>
<!-- <div class="container text-center">
	<h4 class="m-t-20 ">Sorry, your quote is outdated or has expired. </h4>
  <h4 class="p-b-20">If you would like to receive a new quote, please contact us today!</h4>
	<div class=" m-t-20 p-b-20"></div>
	<img src="images/session.png" alt="" style="max-width:85%" />
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Outdated/Expired Quote.</h4>
                  <p class="m-t-10 text-white m-b-0">If you would like to receive a new quote, please contact us today!</p>
                  <p class="text-white mn">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>

<?php
	}elseif($notify_message == "quote_not_found"){
?>
<!-- <div class="container text-center">
	<h4 class="m-t-20 ">Sorry, your quote is not found.</h4>
  <h4 class="p-b-20">If you would like to receive a new quote, please contact us today!</h4>
	<div class=" m-t-20 p-b-20"></div>
	<img src="<?= $HOST ?>/images/not_found.png" alt="" style="max-width:85%" />
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Sorry, your quote is not found.</h4>
                  <p class="m-t-10 text-white m-b-0">If you would like to receive a new quote, please contact us today!</p>
                  <p class="text-white mn">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>

<?php
	}elseif($notify_message == "already_enrolled"){
?>
<!-- <div class="container text-center">
	<h4 class="m-t-20 ">Sorry, your quote is already enrolled. </h4>
  <h4 class="p-b-20">If you would like to receive a new quote, please contact us today!</h4>
	<div class=" m-t-20 p-b-20"></div>
	<img src="images/already_enroll.svg" alt="" style="max-width:300px" />
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Sorry, your quote is already enrolled. </h4>
                  <p class="m-t-10 text-white m-b-0">If you would like to receive a new quote, please contact us today!</p>
                  <p class="text-white mn">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>

<?php
	}elseif($notify_message == "enroll_success"){
?>
<!-- <div class="container text-center">
  <h4 class="m-t-20 ">Success, Your quote is enrolled successfully. </h4>
  <div class=" m-t-20 p-b-20"></div>
  <img src="images/enroll_success.svg" alt="" style="max-width:200px" />
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Success, Your quote is enrolled successfully.</h4>
                  <p class="text-white fw300 m-t-10 m-b-0">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>
<?php
}elseif($notify_message == "user_not_found"){
?>
<!-- <div class="container text-center">
	<h4 class="m-t-20 ">Sorry, We have not found any sponsor. </h4>
	<div class=" m-t-20 p-b-20"></div>
	<img src="<?= $HOST ?>/images/not_found.png" alt="" style="max-width:200px" />
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Sorry, We have not found any sponsor.</h4>
                  <p class="text-white fw300 m-t-10 m-b-0">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>
<?php
}else{
?>	
<!-- <div class="container text-center">
	<h4 class="m-t-20 ">Quote lead enrollment</h4>
  <div class=" m-t-20 p-b-20"></div>
  <div><img src="<?= $ADMIN_HOST ?>/images/smart-e-logo-powered-by.svg" width="125" alt="" style="max-width:100%" /></div>
</div> -->
<section class="not_found_page">
   <div class="container">
      <div class="not_found_content_wrap">
         <div class="not_found_content">
            <div class="media">
               <div class="media-left">
                  <span class="material-icons">info</span>
               </div>
               <div class="media-body">
                  <h4 class="media-heading">Quote lead enrollment.</h4>
                  <p class="text-white fw300 m-t-10 m-b-0">[[AgentPublicDisplayName]] | [[AgentPublicDisplayPhone]] | [[AgentPublicDisplayEmail]]</p>
               </div>
            </div>
         </div>
         <div class="text-center">
            <h1 class="fw700">Thank You!</h1>
         </div>
      </div>
   </div>
</section>
<?php	
	} */
?>
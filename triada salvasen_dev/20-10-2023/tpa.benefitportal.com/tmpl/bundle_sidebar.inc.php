 <style type="text/css">
    .group_member_enroll .affix{width: 100%; max-width: 260px;}
 </style>
 <div>
 <div class="bg_light_gray p-5 text-center b-all">
   <h4 class="m-t-20 text-uppercase">Cart</h4>
   <p class="m-b-30">Enrollee cost per day period <span class="d-block">(<?= !empty($enrolleeClassData['pay_period']) ? $enrolleeClassData['pay_period'] : '' ?>)</span></p>
   <div class="self_guiding_benefits_page_product_display"></div>
   <div class="self_guiding_benefits_page_GrandTotalDiv">
      <div class="self_guiding_benefits_page_electNone">
         Elect a <?= isset($page_location) && $page_location != 'self_guiding_benefits_page_' ? 'Bundle' : 'Plan' ?>
      </div>
      <div class="self_guiding_benefits_page_elected" style="display: none;">
         <h4 class="fs16 m-t-30">
            <i class="fa fa-info-circle fa-lg self_guiding_benefits_page_totalInfo" aria-hidden="true" data-container="body" data-html="true" data-toggle="popover" title="Full Rate Breakdown" data-placement="auto" data-trigger="hover" data-template='<div class="popover" role="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content plan-break-popover"></div></div>' data-content=''></i> Total
         </h4>
         <h2 class="font-bold" ><span bablic-exclude>$</span><span class="self_guiding_benefits_page_total_amount" bablic-exclude>00.00</span><span class="fs14"> /pay period</span></h2>
      </div>
   </div>
</div>
<div class="bg_light_gray p-5 text-center b-all m-t-30 takeHomePayBtnDiv" style="
    display: none;">
<h4 class="m-t-20 m-b-20 text-uppercase">Take Home Pay</h4>
   <div class="home_pay_amt">
      <div class="div_table">
         <div class="table_row">
            <div class="table_cell">
               <div class="bg-success p-15 text-left">
                  <h2 class="mn text-white gapTakeHomeAmt">$0.00</h2>
               </div>
            </div>
            <div class="table_cell w-90">
               <div class="bg_light_success p-20">
                  <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Review" data-placement="bottom" class="text-action viewTakeHomePay"><i class="fa fa-eye fs24" aria-hidden="true"></i></a>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
</div>

<script type="text/javascript">
$(document).off('click', '.viewTakeHomePay');
  $(document).on('click', '.viewTakeHomePay', function (e) {
    e.preventDefault();
    $.colorbox({
      href: '#takeHomePayDiv',
      inline: true, 
      width: '1024px', 
      height: '600px',
      closeButton: false,
      overlayClose: false,
      escKey: false,
    });
  });
</script>

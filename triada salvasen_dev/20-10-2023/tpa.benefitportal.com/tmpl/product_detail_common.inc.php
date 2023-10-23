<div class="panel panel-default product_detail_panel panel-cyan">
  <div class="panel-heading ">
    <h4 class="mn text-white">Brightidea Dental 1500 Annual Max</h4>
  </div>
  <div class="space p-5">
    &nbsp;
  </div>
  <div class="table-responsive">
    <table class="<?=$table_class?> table-cyan">
      <thead>
        <tr>
          <th>Added Date</th>
          <th>Coverage Type</th>
          <th>Effective Date</th>
          <th>Eligibility Status</th>
          <th class="text-center">Dependents</th>
          <th>Price</th>
          <th width="100px" class="text-center">ID Card</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>03/28/2019</td>
          <td>Member + Spouse</td>
          <td>08/01/2019</td>
          <td>Active</td>
          <td class="text-center icons"><a href="<?=$HOST?>/view_depedents.php" class="view_depedents"><i class="fa fa-eye fa-lg"></i></a></td>
          <td>$49.99</td>
          <td class="text-center icons"><a href="<?=$HOST?>/id_card_popup.php" class="id_card_popup"><i class="fa fa-address-card-o fa-lg" aria-hidden="true"></i></a></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="panel-body">
      <ul class="nav nav-tabs tabs customtab nav-noscroll">
        <li class="active"><a href="#association" data-toggle="tab">Association</a></li>
        <li><a href="#benefits_overview" data-toggle="tab">Benefits Overview</a></li>
        <li><a href="#claims" data-toggle="tab">Claims</a></li>
        <li><a href="#support" data-toggle="tab">Support</a></li>
        <li><a href="#resources" data-toggle="tab">Resources</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade in active" id="association">
          <h4>Association Name</h4>
          <p>At times, it may seem difficult to choose a sleep apnea treatment that works. With so many choices, it can be tough to know which apnea treatment method works best and which does not. An individual who has been diagnosed with sleep apnea requires a prompt apnea treatment in order to avoid the serious complications that are often associated with the disorder. Among them, heart disease, a greater likelihood of a stroke or other serious medical condition.</p>
          <p>First and foremost on your mind when selecting an apnea treatment should be safety. Is the product or procedure safe? What type of risks or warnings are associated with its use? If you are considering the use of an anti-snoring device, this may help you to have better quality sleep but these products are not intended to be a cure for sleep apnea on their own. For instance, the Sleep Genie is a doctor recommended anti-snoring device that may help sufferers enjoy a better quality sleep. While supporting the jaw, it helps the mouth to remain closed using a comfortable nylon lycra blend. It is important to understand that the Sleep Genie is not intended to be a cure for sleep apnea, but rather a product that can help the sufferer to rest better throughout the night.</p>
          <p>Other natural sleep apnea treatment methods include the removal of household allergens with the help of air filtration devices, sleeping on your side instead of your back, giving up cigarettes and/or alcohol and paying close attention to your diet. Obesity is one of the leading causes of snoring, which is a direct sign of sleep apnea. Therefore, if you are overweight or have been diagnosed as being obese, it may be time to consider a medically supervised diet and exercise program as the next step in your apnea treatment search. In addition to being a good sleep apnea treatment, losing weight will help to improve your overall health.</p>
          <p>This article is intended for informational purposes only. It should not be used as, or in place of, professional medical advice. Before beginning any treatment for snoring, please consult a doctor for a proper diagnosis and remedy.</p>
          <div class="table-responsive m-t-30 m-b-30">
            <table class="<?=$table_class?>">
              <thead>
                <tr>
                  <th>Label</th>
                  <th>Label</th>
                  <th>Label</th>
                  <th width="130px">Label</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Super Duper Record</td>
                  <td>Another Record</td>
                  <td>Short Record</td>
                  <td>$49.99</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p>At times, it may seem difficult to choose a sleep apnea treatment that works. With so many choices, it can be tough to know which apnea treatment method works best and which does not. An individual who has been diagnosed with sleep apnea requires a prompt apnea treatment in order to avoid the serious complications that are often associated with the disorder. Among them, heart disease, a greater likelihood of a stroke or other serious medical condition.
          </p>
          <div class="text-right m-t-30">
            <a href="javascript:void(0);" class="text-action"><i class="fa fa-download"></i></a>
            <a href="javascript:void(0);" class="btn red-link">Export</a>
          </div>
        </div>
        <div class="tab-pane fade" id="benefits_overview">
          Benefits Overview
        </div>
        <div class="tab-pane fade" id="claims">
          Claims
        </div>
        <div class="tab-pane fade" id="support">
          Support
        </div>
        <div class="tab-pane fade" id="resources">
          Resources
        </div>
      </div>
  </div>
  <div class="panel-footer text-center">
    <a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
  </div>
</div>

<script type="text/javascript">
$(document).off('click', '.id_card_popup');
  $(document).on('click', '.id_card_popup', function (e) {
    e.preventDefault();
    window.parent.$.colorbox({
      href: $(this).attr('data-href'),
      iframe: true, 
      width: '1024px', 
      height: '500px'
    });
  });
  $(document).off('click', '.view_depedents');
  $(document).on('click', '.view_depedents', function (e) {
    e.preventDefault();
    window.parent.$.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '1024px', 
      height: '350px'
    });
  });
</script>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4 class="mn">Required Product Add On</h4>
  </div>
  <div class="panel-body">
    <div class="text-center">
      <p class="text-center mn">You have selected a product that requires a different product. Telehealth Product 01 has been added to the cart.</p>
      <label class="m-t-30"><input type="checkbox" name="">Please don’t show this again</label>
    </div>
    <div class="text-center m-t-30">
      <a href="auto_assinged.php" class="btn btn-action auto_assinged_popup">Confirm</a>
      <a href="javascript:void(0)" class="btn btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).off('click', '.auto_assinged_popup');
  $(document).on('click', '.auto_assinged_popup', function (e) {
    e.preventDefault();
    window.parent.$.colorbox({
      href: $(this).attr('href'),
      iframe: true, 
      width: '580px', 
      height: '420px'
    });
  });
</script>
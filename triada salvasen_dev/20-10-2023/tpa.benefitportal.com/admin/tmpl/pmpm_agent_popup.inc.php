<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
      <p class="fs18"><strong class="fw500">PMPM Agents</strong> <span class="fw300">Agents(2)</span></p>
    </div>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
      <table class="<?= $table_class ?>">
        <thead>
          <tr>
            <th>ID/Agent Name</th>
            <th>LOA Only</th>
            <th width="100px">Downline</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          	<td ><a href="javascript:void(0);" class="fw500 text-red">A1111111</a><br />Agent Name1</td>
            <td>No</td>
            <td>Yes</td>
          </tr>
          <tr>
          	<td ><a href="javascript:void(0);" class="fw500 text-red">A11234567</a><br />Agent Name2</td>
            <td>No</td>
            <td>Yes</td>
          </tr>
        </tbody>
        <tfoot>
        <tr>
          <td colspan="3">
                  <?php echo $paginate->links_html; ?>
          </td>
        </tr>
      </tfoot>
      </table>
      <div class="text-center">
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
      </div>
    </div>
  </div>
</div>

<script>

  $(document).ready(function(){
    common_select()
  });
      $(document).off('change', '.pagination_select');
    $(document).on('change', '.pagination_select', function(e) {
        e.preventDefault();
        $('panel-body').html('');
        var page_url = $(this).find('option:selected').attr('data-page_url');
        window.location.href=page_url
        common_select();
    });
  </script>

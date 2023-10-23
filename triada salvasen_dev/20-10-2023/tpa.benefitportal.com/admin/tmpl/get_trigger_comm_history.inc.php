<?php if ($total_rows > 0) { ?>
  <?php foreach ($fetch_rows as $row) { ?>
    <tr>
      <td width="70%" style="text-align:left;"><?php echo stripslashes($row['title']); ?><br/><i class="icon-envelope"></i>&nbsp;<?php echo stripslashes($row['email']); ?></td>      
      <td width="11%"><?php echo ($row['status']=="Success") ? 'Y' : 'N'; ?></td>
      <td width="19%"><?php echo retrieveDate($row['created_at']); ?></td>
      
    </tr>
  <?php } ?>
<?php } else { ?>
  <tr>
    <td colspan="3">No Record(s).</td>  
  </tr>      
<?php } ?>
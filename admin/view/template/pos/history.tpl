<?php echo $header; ?>
<div id="content">
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/home.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
      
      <div class="latest">
        <div class="dashboard-heading">Statistics</div>
        <div class="dashboard-content">
          <table class="list">
            <thead>
              <tr>
                <td class="right"><?php echo $column_username; ?></td>
                <td class="left"><?php echo $column_name; ?></td>
                <td class="left"><?php echo $column_withdraw; ?></td>
                <td class="right"><?php echo $column_time; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $row) { ?>
              <tr>
                <td class="right"><?php echo $row['username']; ?></td>
                <td class="left"><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                <td class="left"><?php echo $this->currency->format($row['amount']); ?></td>
                <td class="right"><?php echo date('d/m/Y h:i:s A', strtotime($row['date'])) ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
            <div class="pagination">
                <?= $pagination ?>
            </div>  
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<!-- END .hide -->
<?php echo $footer; ?>

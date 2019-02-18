<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="operation_xml" value="<?php echo $xml; ?>">
  <input type="hidden" name="signature" value="<?php echo $signature; ?>">
  <div class="buttons">
    <div class="right">
      <span class="button_pink"><input type="submit" value="<?php echo $button_confirm; ?>" class="button" /></span>
    </div>
  </div>
</form>

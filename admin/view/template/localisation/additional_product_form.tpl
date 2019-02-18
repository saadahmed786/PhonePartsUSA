<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/stock-status.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td><?php foreach ($languages as $language) { ?>
              <input type="text" name="additional_product[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($additional_product[$language['language_id']]) ? $additional_product[$language['language_id']]['name'] : ''; ?>" />
              <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> 
			  Sort Order: <input type="text" size ="3" name="additional_product[<?php echo $language['language_id']; ?>][sort]" value="<?php echo isset($additional_product[$language['language_id']]) ? $additional_product[$language['language_id']]['sort'] : ''; ?>" /><br />
              <?php if (isset($error_name[$language['language_id']])) { ?>
              <span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
              <?php } ?>
              <?php } ?></td>
			<td class="left">
				<?php if ((isset($dropdown)) && ($dropdown)) { ?>
                <input type="checkbox" name="dropdown" value="1" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="dropdown" value="1" />
                <?php } ?>
				Drop Down</td> 
			<td class="left">
				<?php if ((isset($display)) && ($display)) { ?>
                <input type="checkbox" name="display" value="1" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="display" value="1" />
                <?php } ?>
				Display in front-end</td> 
			
				
          </tr>
        </table>
		
		<table id="values" class="list">
        <thead>
          <tr>
            <td class="left">Values</td>            
            <td></td>
          </tr>
        </thead>
        <?php $value_row = 0; ?>
        <?php foreach ($values as $row => $value) { ?> 
        <tbody id="value-row<?php echo $value_row; ?>">
          <tr>
            <td>
			<?php foreach ($languages as $language) { ?>
			<input type="text" name="values[<?php echo $language['language_id']; ?>][<?php echo $value_row; ?>][value]" size="80" value="<?php if (isset($values[$row][$language['language_id']]['value'])) { echo $values[$row][$language['language_id']]['value'];} ?>" />
			<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
            <?php } ?>
			</td>
            <td class="left"><a onclick="$('#value-row<?php echo $value_row; ?>').remove();" class="button">Remove</a></td>
          </tr>
        </tbody>
        <?php $value_row++; ?>
        
        <?php } ?>
        <tfoot>
          <tr>
            <td colspan="1"></td>
            <td class="left"><a onclick="addvalue();" class="button">Add value</a></td>
          </tr>
        </tfoot>
      </table>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
var value_row = <?php echo $value_row; ?>;

function addvalue() {	
        
	html  = '<tbody id="value-row' + value_row + '">';
	html += '  <tr>';
	html += '    <td>';
	<?php foreach ($languages as $language) { ?>
	html += '    <input type="text" name="values[<?php echo $language['language_id']; ?>][' + value_row + '][value]" size="80" value="" />';	
	html += '	 <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />';
	<?php } ?>
	html += '    </td>';
	html += '    <td class="left"><a onclick="$(\'#value-row' + value_row + '\').remove();" class="button">Remove</a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#values tfoot').before(html);
	
	value_row++;
}
</script>
<?php echo $footer; ?>
<?php echo (isset($header)) ? $header : '' ?>
<!--
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
//-----------------------------------------
-->
<?php if (isset($breadcrumbs)) { ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if (!empty($success)) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

  <div class="box">
	<div class="heading">
      <h1><img src="view/image/<?php echo !empty($extension_class) ? $extension_class : 'module' ?>.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
    <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a><a href="#tab-support"><?php echo $tab_support; ?></a></div>
    <div id="tab-general">
      <form action="<?php echo $csv_import; ?>" method="post" enctype="multipart/form-data" id="csv_import">
        <table class="form" style="min-width: 1200px;">
		  <tr>
	        <td>Usage:</td>
	        <td>This tool lets you export data from ANY table in CSV format. From there you can edit it using any spreadsheet tool, and import it back in. Unlike other similar tools, this tool is completely dynamic so it works with any fields or tables added by custom mods, adding quick update features to any mod. The system automatically detects if records are being updated or inserted as new records, so it takes the guess work out of it and lets you safely mass update large amounts of data with ease.</td>
	      </tr>
          <tr>
            <td><?php echo $entry_import; ?></td>
            <td style="width:40%">
              <input type="file" name="csv_import" /><br/>
              <input type="checkbox" id="truncate" name="truncate" value="1" /><label for="truncate"><?php echo $entry_truncate; ?></label>
            </td>
            <td><a onclick="if (confirm('<?php echo $text_challenge; ?>')) { $('#csv_import').submit(); }" class="button"><span><?php echo $button_import; ?></span></a></td>
          </tr>
        </table>
      </form>
      <form action="<?php echo $csv_export; ?>" method="post" enctype="multipart/form-data" id="csv_export">
        <table class="form" style="min-width: 1200px;">
          <tr>
            <td><?php echo $entry_export; ?></td>
            <td style="width:40%">
			  <?php echo $entry_table; ?><br/>
              <select name="csv_export_table" style="min-width:200px; width:300px;">
                <?php foreach ($tables as $table) { ?>
                <option value="<?php echo $table; ?>" ><?php echo $table; ?></option>
                <?php } ?>
              </select>
			  <br />
			  <table><tr><td>
			  <?php echo $entry_columns; ?>
			  <br />
              <select name="csv_export_columns[]" multiple="multiple" size="10" style="min-width:200px; width:300px;">
              </select>
              </td>
              <td width="150px"><?php echo $help_columns; ?></td>
              </tr></table>
              <br />
			  <table><tr>
			  <td>
			    <?php echo $entry_start; ?><br/>
                <input type="text" name="start_row" value="" size="5" />
              </td>
              <td>
			    <?php echo $entry_end; ?><br/>
                <input type="text" name="end_row" value="" size="5" />
              </td>
              </tr>
			  <tr><td colspan="2"><?php echo $help_start_end; ?></td></tr>
              </table>
            </td>
            <td><a onclick="$('#csv_export').submit();" class="button"><span><?php echo $button_export; ?></span></a></td>
          </tr>
        </table>
      </form>
    </div>
    <div id="tab-support">
	  <table class="form">
	  	<tr>
	      <td>Information:</td>
	      <td>Please DO NOT use the forums or extension store commments to request support.<br/>Fastest Support is had by contacting me directly</td>
	    </tr>
	    <tr>
	      <td>Support Email:</td>
	      <td>Qphoria@gmail.com</td>
	    </tr>
	    <tr>
	      <td>Support Skype:</td>
	      <td>taqmobile</td>
	    </tr>
	    <tr>
	      <td>Things to include when requesting support:</td>
	      <td>
	        <ul>
	          <li>A link to your site</li>
	          <li>Admin Access if admin related</li>
	          <li>FTP Access if you are seeing errors on the site</li>
	        </ul>
	      </td>
	    </tr>
	  </table>
    </div>
    </div>
  </div>
</div>


<script type="text/javascript"><!--
<?php if (isset($breadcrumbs)) { //v15x ?>
$('#tabs a').tabs();
<?php } else { ?>
$.tabs('#tabs a');
<?php } ?>

$('select[name=\'csv_export_table\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=module/<?php echo $classname; ?>/getColumns&token=<?php echo $token; ?>&table=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {

			html = '';
			if (json['columns']) {
				for (i = 0; i < json['columns'].length; i++) {
        			html += '<option value="' + json['columns'][i] + '" selected="selected">' + json['columns'][i] + '</option>';
				}
			}

			$('select[name=\'csv_export_columns[]\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'csv_export_table\']').trigger('change');
</script>
<?php echo (isset($footer)) ? $footer : '' ?>
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
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div class="vtabs">        
        	<a href="#tab-general"><?php echo $tab_general; ?></a>
			<a href="#tab_insurance"><?php echo $tab_insurance; ?></a>
			<a href="#tab_paymentfee"><?php echo $tab_paymentfee; ?></a>
			<a href="#tab_survey"><?php echo $tab_survey; ?></a>
			<a href="#tab_report"><?php echo $tab_report; ?></a>
	  	</div>			
      	<div id="tab-general" class="vtabs-content">
		<table class="form">
		  <tr>
            <td><?php echo $entry_shipping; ?></td>
            <td><select name="onecheckout_shipping">
                <?php foreach ($shippings as $shipping){ ?>
				<?php if($shipping['code']==$this->config->get('onecheckout_shipping')) {?>
				<option value="<?php echo $shipping['code']; ?>" selected="selected"><?php echo $shipping['title']; ?></option>
				<?php }else{ ?>
				<option value="<?php echo $shipping['code']; ?>"><?php echo $shipping['title']; ?></option>
				<?php } ?>
				<?php } ?>
              </select></td>
          </tr>
		  <tr>
            <td><?php echo $entry_payment; ?></td>
            <td><select name="onecheckout_payment">
                <?php foreach ($payments as $payment){ ?>
				<?php if($payment['code']==$this->config->get('onecheckout_payment')) {?>
				<option value="<?php echo $payment['code']; ?>" selected="selected"><?php echo $payment['title']; ?></option>
				<?php }else{ ?>
				<option value="<?php echo $payment['code']; ?>"><?php echo $payment['title']; ?></option>
				<?php } ?>
				<?php } ?>
              </select></td>
          </tr>
		  <tr>
            <td><?php echo $entry_fields; ?></td>
			<td><div class="scrollbox" style="height:250px;" id="displayfields">
				<?php if ($onecheckout_fields) { ?>
					<input type="hidden" name="onecheckout_fields[]" value="email" />
					<div class="odd">				
				    <?php if (in_array('firstname',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="firstname" id="firstname" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="firstname" id="firstname" />
					<?php } ?>
					<label for="firstname"><?php echo $field_firstname; ?></label></div>
					<div class="even">
					<?php if (in_array('lastname',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="lastname" id="lastname" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="lastname" id="lastname" />
					<?php } ?>
					<label for="lastname"><?php echo $field_lastname; ?></label></div>
					<div class="odd">				
				    <?php if (in_array('company',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="company" id="company" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="company" id="company" />
					<?php } ?>
					<label for="company"><?php echo $field_company; ?></label></div>
					<div class="even">
					<?php if (in_array('address1',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="address1" id="address1" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="address1" id="address1" />
					<?php } ?>
					<label for="address1"><?php echo $field_address1; ?></label></div>
					<div class="odd">				
				    <?php if (in_array('address2',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="address2" id="address2" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="address2" id="address2" />
					<?php } ?>
					<label for="address2"><?php echo $field_address2; ?></label></div>
					<div class="even">
					<?php if (in_array('telephone',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="telephone" id="telephone" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="telephone" id="telephone" />
					<?php } ?>
					<label for="telephone"><?php echo $field_telephone; ?></label></div>
					<div class="odd">				
				    <?php if (in_array('fax',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="fax" id="fax" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="fax" id="fax" />
					<?php } ?>
					<label for="fax"><?php echo $field_fax; ?></label></div>
					<div class="even">
					<?php if (in_array('city',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="city" id="city" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="city" id="city" />
					<?php } ?>
					<label for="city"><?php echo $field_city; ?></label></div>
					<div class="odd">				
				    <?php if (in_array('postcode',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="postcode" id="postcode" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="postcode" id="postcode" />
					<?php } ?>
					<label for="postcode"><?php echo $field_postcode; ?></label></div>
					<div class="even">
					<?php if (in_array('country',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="country" id="country" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="country" id="country" />
					<?php } ?>
					<label for="country"><?php echo $field_country; ?></label></div>
					<div class="odd">				
				    <?php if (in_array('zone',$onecheckout_fields)) { ?>
						<input type="checkbox" name="onecheckout_fields[]" value="zone" id="zone" checked="checked" />
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_fields[]" value="zone" id="zone" />
					<?php } ?>
					<label for="zone"><?php echo $field_zone; ?></label></div>
				<?php } else { ?>
				  <input type="hidden" name="onecheckout_fields[]" value="email" />
				  <div class="odd">
					<input type="checkbox" name="onecheckout_fields[]" value="firstname" id="firstname" checked="checked" /><label for="firstname"><?php echo $field_firstname; ?></label>
				  </div>
				  <div class="even">
				  	<input type="checkbox" name="onecheckout_fields[]" value="lastname" id="lastname" checked="checked" /><label for="lastname"><?php echo $field_lastname; ?></label>
				  </div>
				  <div class="odd">
				  	<input type="checkbox" name="onecheckout_fields[]" value="company" id="company" checked="checked" /><label for="company"><?php echo $field_company; ?></label>
				  </div>
				  <div class="even">
				  	<input type="checkbox" name="onecheckout_fields[]" value="address1" id="address1" checked="checked" /><label for="address1"><?php echo $field_address1; ?></label>
				  </div>
				  <div class="odd">
				  	<input type="checkbox" name="onecheckout_fields[]" value="address2" id="address2" checked="checked" /><label for="address2"><?php echo $field_address2; ?></label>
				  </div>
				  <div class="even">
				  	<input type="checkbox" name="onecheckout_fields[]" value="telephone" id="telephone" checked="checked" /><label for="telephone"><?php echo $field_telephone; ?></label>
				  </div>
				  <div class="odd">
				  	<input type="checkbox" name="onecheckout_fields[]" value="fax" id="fax" checked="checked" /><label for="fax"><?php echo $field_fax; ?></label>
				  </div>
				  <div class="even">
				  	<input type="checkbox" name="onecheckout_fields[]" value="city" id="city" checked="city" /><label for="city"><?php echo $field_city; ?></label>
				  </div>
				  <div class="odd">
				  	<input type="checkbox" name="onecheckout_fields[]" value="postcode" id="postcode" checked="checked" /><label for="postcode"><?php echo $field_postcode; ?></label>
				  </div>
				  <div class="even">
				  	<input type="checkbox" name="onecheckout_fields[]" value="country" id="country" checked="checked" /><label for="country"><?php echo $field_country; ?></label>
				  </div>
				  <div class="odd">
				  	<input type="checkbox" name="onecheckout_fields[]" value="zone" id="zone" checked="checked" /><label for="zone"><?php echo $field_zone; ?></label>
				  </div>
				<?php } ?>
                </div><span><a onclick="$('#displayfields :checkbox').attr('checked','checked');"><u>Select All</u></a> / <a onclick="$('#displayfields :checkbox').removeAttr('checked');"><u>Unselect All</u></a></span></td>
		  </tr>
		  <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="onecheckout_status">
                <?php if ($onecheckout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        </table>
		</div>
		<div id="tab_insurance" class="vtabs-content">
		<table class="form">
        <tr>
          <td><?php echo $entry_insurance_fee_percentage; ?></td>
          <td><input type="text" name="insurance_fee_percentage" value="<?php echo $insurance_fee_percentage; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_fixed_fee; ?></td>
          <td><input type="text" name="insurance_fee_fixed_fee" value="<?php echo $insurance_fee_fixed_fee; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_selection; ?></td>
          <td>
		  	<?php if ($insurance_fee_selection==0 || !$insurance_fee_selection) { ?>
		  	<label><input type="radio" name="insurance_fee_selection" value="0" checked="checked" />&nbsp;<?php echo $text_insurance_fee_offer; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="0" />&nbsp;<?php echo $text_insurance_fee_offer; ?></label><br />
			<?php } ?>
			<?php if ($insurance_fee_selection==1) { ?>
  			<label><input type="radio" name="insurance_fee_selection" value="1" checked="checked" />&nbsp;<?php echo $text_insurance_fee_opt; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="1" />&nbsp;<?php echo $text_insurance_fee_opt; ?></label><br />
			<?php } ?>
			<?php if ($insurance_fee_selection==2) { ?>
  			<label><input type="radio" name="insurance_fee_selection" value="2" checked="checked" />&nbsp;<?php echo $text_insurance_fee_required; ?></label><br />
			<?php } else { ?>
			<label><input type="radio" name="insurance_fee_selection" value="2" />&nbsp;<?php echo $text_insurance_fee_required; ?></label><br />
			<?php } ?>
		  </td>
        </tr>
		<tr>
          <td><?php echo $entry_insurance_fee_geo_zone; ?></td>
          <td><select name="insurance_fee_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $insurance_fee_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_insurance_fee_status; ?></td>
          <td><select name="insurance_fee_status">
              <?php if ($insurance_fee_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_insurance_fee_sort_order; ?></td>
          <td><input type="text" name="insurance_fee_sort_order" value="<?php echo $insurance_fee_sort_order; ?>" size="1" /></td>
        </tr>
        </table>
		</div>		
		<div id="tab_paymentfee" class="vtabs-content">
		<table class="form">
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_total; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_total" value="<?php echo $onecheckout_payment_fee_total; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_percentage; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_percentage" value="<?php echo $onecheckout_payment_fee_percentage; ?>" /></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_payment_fee_fixed_fee; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_fixed_fee" value="<?php echo $onecheckout_payment_fee_fixed_fee; ?>" /></td>
        </tr>
		<tr>
	      <td><?php echo $entry_onecheckout_payment_fee_payment_type; ?></td>
          <td><div class="scrollbox">
          <?php $class = 'odd'; ?>
	          <?php foreach ($payment_types as $payment_type) { ?>
	          <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
				<div class="<?php echo $class; ?>">
				<?php if ($onecheckout_payment_fee_types) { ?>
				    <?php if (in_array($payment_type['paymentkey'],$onecheckout_payment_fee_types)) { ?>
						<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" checked="checked" />
						<?php echo $payment_type['name']; ?>
					<?php } else { ?> 
						<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" />
						<?php echo $payment_type['name']; ?>
					<?php } ?>
				<?php } else { ?>
					<input type="checkbox" name="onecheckout_payment_fee_types[]" value="<?php echo $payment_type['paymentkey']; ?>" />
					<?php echo $payment_type['name']; ?>
				<?php } ?>
                </div>			
              <?php } ?>
           </div></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_payment_fee_geo_zone; ?></td>
          <td><select name="onecheckout_payment_fee_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $onecheckout_payment_fee_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_tax; ?></td>
          <td><select name="onecheckout_payment_fee_tax_class_id">
              <option value="0"><?php echo $text_none; ?></option>
              <?php foreach ($tax_classes as $tax_class) { ?>
              <?php if ($tax_class['tax_class_id'] == $onecheckout_payment_fee_tax_class_id) { ?>
              <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_status; ?></td>
          <td><select name="onecheckout_payment_fee_status">
              <?php if ($onecheckout_payment_fee_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_onecheckout_payment_fee_sort_order; ?></td>
          <td><input type="text" name="onecheckout_payment_fee_sort_order" value="<?php echo $onecheckout_payment_fee_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
		</div>
		<div id="tab_survey" class="vtabs-content">
		<table class="form">
        <tr>
          <td><?php echo $entry_survey; ?></td>
          <td><input type="text" id="onecheckout_addoption" value="" />&nbsp;<a id="addoption" class="button"><span><?php echo $button_add; ?></span></a></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><div class="scrollbox" id="option">
              <?php $class = 'odd'; ?>
              <?php foreach ($survey_options as $option) { ?>
              <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
              <div class="<?php echo $class; ?>"><?php echo $option; ?> <img src="view/image/delete.png" />
                <input type="hidden" name="onecheckout_survey_option[]" value="<?php echo $option; ?>" />
              </div>
              <?php } ?>
            </div></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_survey_position; ?></td>
          <td><select name="onecheckout_survey_position">
              <?php if ($onecheckout_survey_position) { ?>
              <option value="1" selected="selected"><?php echo $text_position_after; ?></option>
              <option value="0"><?php echo $text_position_before; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_position_after; ?></option>
              <option value="0" selected="selected"><?php echo $text_position_before; ?></option>
              <?php } ?>
            </select></td>
        </tr>
		<tr>
          <td><?php echo $entry_onecheckout_survey_status; ?></td>
          <td><select name="onecheckout_survey_status">
              <?php if ($onecheckout_survey_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
      	</table>
		</div>
		<div id="tab_report" class="vtabs-content">
		<table class="filter">
        <tr>
          <td style="padding-right:20px;"><?php echo $entry_date_start; ?>
            <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" id="date-start" size="12" /></td>
          <td style="padding-right:20px;"><?php echo $entry_date_end; ?>
            <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" id="date-end" size="12" /></td>
          <td><a onclick="getSurveyChart($('#date-start').val(), $('#date-end').val());" class="button"><span><?php echo $button_filter; ?></span></a></td>
        </tr>
      	</table><br />
		<div id="placeholder" style="width:600px;height:300px;"></div>
		</div>	
      </form>
    </div>
  </div>
</div>
<!--[if IE]>
<script type="text/javascript" src="view/javascript/jquery/flot/excanvas.js"></script>
<![endif]--> 
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script> 
<script type="text/javascript"><!--
function getSurveyChart(startdate, enddate) {
	$.ajax({
		type: 'GET',
		url: 'index.php?route=module/onecheckout/chart&token=<?php echo $token; ?>&startdate=' + startdate + '&enddate=' +enddate,
		dataType: 'json',
		async: false,
		success: function(json) {					
			var option = {	
				shadowSize: 0,
				bars: {
					show: true,
					align: "center",
					barWidth:0.5
				},
				grid: {
					backgroundColor: '#FFFFFF'
				},
				yaxis: {
					tickDecimals: 0,
					min: 0
				},
				xaxis: {
            		ticks: json.xaxis
				}
			}
			$('#placeholder').css('width',json.count);
			$.plot($('#placeholder'), [json.order], option);
		}
	});
}
getSurveyChart('<?php echo $filter_date_start; ?>','<?php echo $filter_date_end; ?>');
//--></script> 
<script type="text/javascript"><!--
$('#addoption').live('click',function() {
	if($('#onecheckout_addoption').val()!=''){
		$('#option').append('<div>' + $('#onecheckout_addoption').val() + '<img src="view/image/delete.png" /><input type="hidden" name="onecheckout_survey_option[]" value="' + $('#onecheckout_addoption').val() + '" /></div>');
		$('#onecheckout_addoption').val('');
		$('#option div:odd').attr('class', 'odd');
		$('#option div:even').attr('class', 'even');		
	}
	$('#onecheckout_addoption').focus();
});

$('#option div img').live('click', function() {
	$(this).parent().remove();
	
	$('#option div:odd').attr('class', 'odd');
	$('#option div:even').attr('class', 'even');
});
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});	
	$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 

<?php echo $footer; ?>
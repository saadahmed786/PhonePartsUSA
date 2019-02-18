<table class="form">
  <?php if(in_array('firstname',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
    <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" class="large-field" /><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="firstname" value=" " class="large-field" /><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('lastname',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
    <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" class="large-field" /><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="lastname" value=" " class="large-field" /><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('company',$onecheckout_fields)) { ?>
  <tr>
    <td><?php echo $entry_company; ?></td>
    <td><input type="text" name="company" value="<?php echo $company; ?>" class="large-field" /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="company" value="" class="large-field" /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('address1',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
    <td><input type="text" name="address_1" value="<?php echo $address_1; ?>" class="large-field" /><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="address_1" value="   " class="large-field" /><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('address2',$onecheckout_fields)) { ?>
  <tr>
    <td><?php echo $entry_address_2; ?></td>
    <td><input type="text" name="address_2" value="<?php echo $address_2; ?>" class="large-field" /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="address_2" value="" class="large-field" /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('city',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_city; ?></td>
    <td><input type="text" name="city" value="<?php echo $city; ?>" class="large-field" /><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="city" value="   " class="large-field" /><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('postcode',$onecheckout_fields)) { ?>
  <tr>
    <td><span id="shipping-postcode-required" class="required">*</span> <?php echo $entry_postcode; ?></td>
    <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" /><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><input type="text" name="postcode" value="   " class="large-field" /><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('country',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_country; ?></td>
    <td><select name="country_id" class="large-field">
        <option value=""><?php echo $text_select; ?></option>
        <?php foreach ($countries as $country) { ?>
        <?php if ($country['country_id'] == $country_id) { ?>
        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
        <?php } ?>
        <?php } ?>
      </select><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><select name="country_id" class="large-field">
        <?php foreach ($countries as $country) { ?>
        <?php if ($country['country_id'] == $country_id) { ?>
        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
        <?php } ?>
        <?php } ?>
      </select><br /></td>
  </tr>
  <?php } ?>
  <?php if(in_array('zone',$onecheckout_fields)) { ?>
  <tr>
    <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
    <td><select name="zone_id" class="large-field">
      </select><br /></td>
  </tr>
  <?php } else { ?>
  <tr style="display:none;">
    <td>&nbsp;</td>
    <td><select name="zone_id" class="large-field">
      </select><br /></td>
  </tr>
  <?php } ?>
</table>
<br />
<script type="text/javascript"><!--
$('#shipping-address select[name=\'country_id\']').bind('change', function() {
	getzone('shipping','<?php echo $zone_id; ?>');
});
getzone('shipping','<?php echo $zone_id; ?>');
//--></script>
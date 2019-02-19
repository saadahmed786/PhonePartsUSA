<?php echo $header; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <p><?php echo $text_account_already; ?></p>
  <form class="register" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <h2><?php echo $text_your_details; ?></h2>
    <div class="content">
      <table class="form">
        
        <tr>
          <td>Account Code (For Businesses Only):</td>
        </tr>
        <tr>
          <td><input type="text" name="account_code" value="<?php echo $account_code; ?>" /></td>
        </tr>
        
        <tr>
          <td><?php echo $entry_firstname; ?><span class="required">(*)</span> </td>
        </tr>
        <tr>
          <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_lastname; ?><span class="required">(*)</span> </td>
          </tr>
          <tr>
            <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
              <?php if ($error_lastname) { ?>
              <span class="error"><?php echo $error_lastname; ?></span>
              <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_email; ?><span class="required">(*)</span> </td>
            </tr>
            <tr>
              <td><input type="text" name="email" value="<?php echo $email; ?>" />
                <?php if ($error_email) { ?>
                <span class="error"><?php echo $error_email; ?></span>
                <?php } ?></td>
              </tr>
              <tr>
                <td><?php echo $entry_telephone; ?><span class="required">(*)</span> </td>
              </tr>
              <tr>
                <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
                  <?php if ($error_telephone) { ?>
                  <span class="error"><?php echo $error_telephone; ?></span>
                  <?php } ?></td>
                </tr>
                <tr>
                  <td><?php echo $entry_fax; ?></td>
                </tr>
                <tr>
                  <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
                </tr>
              </table>
            </div>
            <h2><?php echo $text_your_address; ?></h2>
            <div class="content">
              <table class="form">
                <tr>
                  <td><?php echo $entry_company; ?></td>
                </tr>
                <tr>
                  <td><input type="text" name="company" value="<?php echo $company; ?>" /></td>
                </tr>
                
                
                
                
                <tr style="display: <?php echo (count($customer_groups) > 1 ? 'table-row' : 'none'); ?>;">
                  <td><?php echo $entry_account; ?></td>
                  <td><select name="customer_group_id">
                    <?php foreach ($customer_groups as $customer_group) { ?>
                    <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                    <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr  id="company-id-display">
                  <td><input type="text" name="company_id" value="<?php echo $company_id; ?>" />
                    <?php if ($error_company_id) { ?>
                    <span class="error"><?php echo $error_company_id; ?></span>
                    <?php } ?></td>
                  </tr>
                  <tr id="tax-id-display">
                    <td><?php echo $entry_tax_id; ?><span id="tax-id-required" class="required">(*)</span> </td>
                    <td><input type="text" name="tax_id" value="<?php echo $tax_id; ?>" />
                      <?php if ($error_tax_id) { ?>
                      <span class="error"><?php echo $error_tax_id; ?></span>
                      <?php } ?></td>
                    </tr>
                    <tr>
                      <td><?php echo $entry_address_1; ?><span class="required">(*)</span> </td>
                    </tr>
                    <tr>
                      <td><input type="text" name="address_1" value="<?php echo $address_1; ?>" />
                        <?php if ($error_address_1) { ?>
                        <span class="error"><?php echo $error_address_1; ?></span>
                        <?php } ?></td>
                      </tr>
                      <tr>
                        <td><?php echo $entry_address_2; ?></td>
                      </tr>
                      <tr>
                        <td><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></td>
                      </tr>
                      <tr>
                        <td><?php echo $entry_city; ?><span class="required">(*)</span> </td>
                      </tr>
                      <tr>
                        <td><input type="text" name="city" value="<?php echo $city; ?>" />
                          <?php if ($error_city) { ?>
                          <span class="error"><?php echo $error_city; ?></span>
                          <?php } ?></td>
                        </tr>
                        <tr>
                          <td><?php echo $entry_postcode; ?><span id="postcode-required" class="required">(*)</span> </td>
                        </tr>
                        <tr>
                          <td><input type="text" name="postcode" value="<?php echo $postcode; ?>" />
                            <?php if ($error_postcode) { ?>
                            <span class="error"><?php echo $error_postcode; ?></span>
                            <?php } ?></td>
                          </tr>
                          <tr>
                            <td><?php echo $entry_country; ?><span class="required">(*)</span> </td>
                          </tr>
                          <tr>
                            <td><select name="country_id">
                              <option value=""><?php echo $text_select; ?></option>
                              <?php foreach ($countries as $country) { ?>
                              <?php if ($country['country_id'] == $country_id) { ?>
                              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                              <?php } else { ?>
                              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                              <?php } ?>
                              <?php } ?>
                            </select>
                            <?php if ($error_country) { ?>
                            <span class="error"><?php echo $error_country; ?></span>
                            <?php } ?></td>
                          </tr>
                          <tr>
                            <td><?php echo $entry_zone; ?><span class="required">(*)</span> </td>
                          </tr>
                          <tr>
                            <td><select name="zone_id">
                            </select>
                            <?php if ($error_zone) { ?>
                            <span class="error"><?php echo $error_zone; ?></span>
                            <?php } ?></td>
                          </tr>
                        </table>
                      </div>
                      <h2><?php echo $text_your_password; ?></h2>
                      <div class="content">
                        <table class="form">
                          <tr>
                            <td><?php echo $entry_password; ?><span class="required">(*)</span> </td>
                          </tr>
                          <tr>
                            <td><input type="password" name="password" value="<?php echo $password; ?>" />
                              <?php if ($error_password) { ?>
                              <span class="error"><?php echo $error_password; ?></span>
                              <?php } ?></td>
                            </tr>
                            <tr>
                              <td><?php echo $entry_confirm; ?><span class="required">(*)</span> </td>
                            </tr>
                            <tr>
                              <td><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
                                <?php if ($error_confirm) { ?>
                                <span class="error"><?php echo $error_confirm; ?></span>
                                <?php } ?></td>
                              </tr>
                            </table>
                          </div>
                          <h2><?php echo $text_newsletter; ?></h2>
                          <div class="content">
                            <table class="form">
                              <tr>
                                <td><?php echo $entry_newsletter; ?></td>
                                <td><?php if ($newsletter) { ?>
                                  <input type="radio" name="newsletter" value="1" checked="checked" />
                                  <?php echo $text_yes; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                  <input type="radio" name="newsletter" value="0" />
                                  <?php echo $text_no; ?>
                                  <?php } else { ?>
                                  <input type="radio" name="newsletter" value="1" />
                                  <?php echo $text_yes; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                  <input type="radio" name="newsletter" value="0" checked="checked" />
                                  <?php echo $text_no; ?>
                                  <?php } ?></td>
                                </tr>
                              </table>
                            </div>
                            <?php if ($text_agree) { ?>
                            <div class="buttons">
                              <div class="left">
                                <?php if ($agree) { ?>
                                <input type="checkbox" name="agree" value="1" checked="checked" />
                                <?php } else { ?>
                                <input type="checkbox" name="agree" value="1" />
                                <?php } ?><?php echo $text_agree; ?> <br/>
                                <span class="button_pink"><input type="submit" value="<?php echo $button_continue; ?>" class="button" /></span>
                              </div>
                            </div>
                            <?php } else { ?>
                            <div class="buttons">
                              <div class="left">
                                <span class="button_pink"><input type="submit" value="<?php echo $button_continue; ?>" class="button" /></span>
                              </div>
                            </div>
                            <?php } ?>
                          </form>
                          <?php echo $content_bottom; ?></div>
                          <script type="text/javascript"><!--
                            $('select[name=\'customer_group_id\']').live('change', function() {
                             var customer_group = [];
                             
                             <?php foreach ($customer_groups as $customer_group) { ?>
                               customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
                               customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
                               customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
                               customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
                               customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
                               <?php } ?>	

                               if (customer_group[this.value]) {
                                if (customer_group[this.value]['company_id_display'] == '1') {
                                 $('#company-id-display').show();
                                 $('#company-id-text').show();
                               } else {
                                 $('#company-id-display').hide();
                                 $('#company-id-text').hide();
                               }
                               
                               if (customer_group[this.value]['company_id_required'] == '1') {
                                 $('#company-id-required').show();
                               } else {
                                 $('#company-id-required').hide();
                               }
                               
                               if (customer_group[this.value]['tax_id_display'] == '1') {
                                 $('#tax-id-display').show();
                               } else {
                                 $('#tax-id-display').hide();
                               }
                               
                               if (customer_group[this.value]['tax_id_required'] == '1') {
                                 $('#tax-id-required').show();
                               } else {
                                 $('#tax-id-required').hide();
                               }	
                             }
                           });

$('select[name=\'customer_group_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
  $('select[name=\'country_id\']').bind('change', function() {
   $.ajax({
    url: 'index.php?route=account/register/country&country_id=' + this.value,
    dataType: 'json',
    beforeSend: function() {
     $('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
   },
   complete: function() {
     $('.wait').remove();
   },			
   success: function(json) {
     if (json['postcode_required'] == '1') {
      $('#postcode-required').show();
    } else {
      $('#postcode-required').hide();
    }
    
    html = '<option value=""><?php echo $text_select; ?></option>';
    
    if (json['zone'] != '') {
      for (i = 0; i < json['zone'].length; i++) {
       html += '<option value="' + json['zone'][i]['zone_id'] + '"';
       
       if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
         html += ' selected="selected"';
       }
       
       html += '>' + json['zone'][i]['name'] + '</option>';
     }
   } else {
    html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
  }
  
  $('select[name=\'zone_id\']').html(html);
},
error: function(xhr, ajaxOptions, thrownError) {
 alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
});
});

$('select[name=\'country_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
  $(document).ready(function() {
   function get_Width_Height() {
    var array = new Array();
    if(getWidthBrowser() > 766){
     array[0] = 640;
     array[1] = 480;
   } else if(getWidthBrowser() < 767 && getWidthBrowser() > 480) {
     array[0] = 450;
     array[1] = 350;
   }else{
     array[0] = 300;
     array[1] = 300;
   }
   return array;
 }
 $('.colorbox').colorbox({
  width: get_Width_Height()[0],
  height: get_Width_Height()[1]
});
});
  //--></script>  
  <?php echo $footer; ?>
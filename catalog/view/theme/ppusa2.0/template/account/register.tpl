<script type="text/javascript">
  var recaptchaCallback = function () {
    console.log('recaptcha is ready'); // not showing
    grecaptcha.render("recaptcha", {
        sitekey: '6Lesqy8UAAAAAGTGe0AayW8a4WsZAcI7OhO5HdHV',
        callback: function () {
            console.log('recaptcha callback');
        }
    });
  }
</script>
<script src="catalog/view/javascript/jquery/jquery.mask.js"></script>
<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=en" async defer></script>

                  <div class="row row-centered">
                  <div class="col-lg-9 col-xs-11 col-centered">


                    <?php if ($error_warning) { ?>
                    <div class="alert alert-danger alert-dismissible"  role="alert"><?php echo $error_warning; ?></div>
                    <?php } ?>

                     <div class="row row-centered register-interest">
                  <div class="col-lg-9 col-xs-11 col-centered">
                    <div class="text-center">
                      <p>Are you purchasing $1000+ monthly? <br>
                      Or buying on behalf of a Repair Business or School?</p>
                      <a href="<?php echo $this->url->link('wholesale/wholesale');?>" class="btn btn-info light">COMPLETE A WHOLESALE APPLICATION</a>
                    </div>
                  </div>
                </div>
                <div class="border"></div>
                <br>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                       <div class="col-md-5 col-xs-12">
          <div class="form-group labelholder" data-label="First Name">
                <input type="input" class="form-control" id="inputFname" placeholder="First Name" name="firstname" value="<?php echo $firstname; ?>" required/>
          </div>
        </div>
        <div class="col-md-1"></div>
         <div class="col-md-6 col-xs-12">
          <div class="form-group labelholder" data-label="Last Name">
            <input type="input" class="form-control" id="inputLname" placeholder="Last Name" name="lastname" value="<?php echo $lastname; ?>" required/>
          </div>
        </div>

        <div class="col-md-12 col-xs-12">
          <div class="form-group labelholder" data-label="Company Name">
                <input type="input" class="form-control" id="inputCompany" placeholder="Company Name" name="company" value="<?php echo $company; ?>" required />
          </div>
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="form-group labelholder" data-label="Phone">
                <input type="input" class="form-control" id="inputTelephone" placeholder="Phone" name="telephone" value="<?php echo $telephone; ?>" required/>
          </div>
        </div>

                      <div class="col-md-5 col-xs-12">
          <div class="form-group labelholder" data-label="Email">
              <input type="email" onkeyup="confirmEmail();" class="form-control" id="inputEmailReg" placeholder="Email" name="email"  required/>
          </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 col-xs-12">
          <div class="form-group labelholder" data-label="Confirm Email">
             <input onkeyup="confirmEmail();" type="email" class="form-control" id="inputEmail2Reg" placeholder="Confirm Email">
          </div>
        </div>


        <div class="col-md-5 col-xs-12">
          <div class="form-group labelholder" data-label="Password">
                <input type="password" onkeyup="confirmPassword();" class="form-control" id="inputPasswordReg" placeholder="Password" name="password" value="<?php echo $password; ?>" required/>
          </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 col-xs-12">
          <div class="form-group labelholder" data-label="Confirm Password">
             <input onkeyup="confirmPassword();" type="password" class="form-control" id="inputPassword2Reg" placeholder="Confirm Password" name="confirm" value="<?php echo $confirm; ?>" required/>
          </div>
        </div>

        <?php

        $source_options = array('Google','Facebook','Bing','Other Online Ad','Magazine','Postcard','Referral','Blog','Discussion Board');
        sort($source_options);
        
        ?>
        <div class="col-md-12 col-xs-12 ">
            <div class="form-group">
              <select class="selectpicker" id="source" name="source"  data-size="6" required>
                <option value="">How Did You Hear About Us?</option>
                <?php
                foreach(($source_options) as $_source)
                {
                ?>
                  <option><?php echo $_source;?></option>
                <?php
                }

                ?>
              </select>
            </div>
          </div>
          <?php

          
          ?>


          <?php

        $busines_type_options = array('Not a Business','Computer Repair Shop','Convenience/General Store','Government (School/School District/Agency)','Mobile Repair','Phone Repair Franchise','Phone Repair Shop','Refurbishing Company','Supplier/Distributor','Other Business Type');
        //sort($busines_type_options);
      
        ?>
        <div class="col-md-12 col-xs-12 ">
            <div class="form-group">
              <select class="selectpicker" id="business_type" name="business_type"  data-size="6" required>
                <option value="">Business Type</option>
                <?php
                foreach(($busines_type_options) as $_source)
                {
                ?>
                  <option><?php echo $_source;?></option>
                <?php
                }

                ?>
              </select>
            </div>
          </div>
          <?php

          
          ?>


       
       
                      <!-- Hidden Fields -->
                      <div style="display:none;" class="content">
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
                      <input type="hidden" name="account_code" value="52151" />
                    <span class="required"></span>
                    <input type="hidden" name="fax" value="5151" />
                  
                    <div style="display:none;">
                      <?php echo $entry_account; ?>
                      <select name="customer_group_id">
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                        <option value="null" selected="selected"><?php echo $customer_group['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                    <input type="hidden" name="company_id" value="61561" />                    
                    <span class="required"></span><input type="hidden" name="tax_id" value="06565" />                    
                    <span class="required"></span><input type="hidden" name="address_1" value="SDSD" />                       
                    <input type="hidden" name="address_2" value="null" />
                    <span class="required"></span><input type="hidden" name="city" value="DFDF" />                          
                    <span class="required"></span><input type="hidden" name="postcode" value="54544" />            
                    <div>
                            <div style="display:none;" ><select name="country_id">
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
                            <?php } ?></div>
                          </div>  
                          <div>
                            <div style="display:none;"><?php echo $entry_zone; ?><span class="required">(*)</span> </div>
                          </div>
                          <div>
                            <div style="display:none;"><select name="zone_id">
                            </select>
                            <?php if ($error_zone) { ?>
                            <span class="error"><?php echo $error_zone; ?></span>
                            <?php } ?></div>
                          </div> 
                    <span class="required"></span><input type="hidden" name="zone_id" value="null"> 
                    <!-- Hidden Fields End -->
                    <div class="form-group">
                    <label for="inputLname" class="col-xs-4 control-label"></label>
                    <div class="col-xs-8">
                      <p class="hidden">
                        
                        <input type="checkbox" name="agree" class="css-checkbox" id="ck1" value="1" checked="checked">
                        <label for="ck1" class="css-label2"><?php echo $text_agree; ?></label>
                        
                      </p>
                      
                    </div>
                    </div>

                      <div class="form-group">
                        <!-- <label for="inputLname" class="col-xs-4 control-label"></label> -->
                        <div class="col-xs-12">
                        <p>
                            <?php if ($newsletter) { ?>
                          <input type="checkbox" name="newsletter" class="css-checkbox" id="ck2" value="1" checked="checked">
                          <label for="ck2" class="css-label2">Receive Special Offers from PhonePartsUSA</label>
                          <?php } else { ?>
                          <input type="checkbox" name="newletter" class="css-checkbox" id="ck2" value="1">
                          <label for="ck2" class="css-label2">Receive Special Offers from PhonePartsUSA</label>
                          <?php } ?> <br/>
                        </p>
                        </div>
                      </div>
                      <div  id="recaptcha"></div>
                      <div class="form-group">
                        <div class="text-center form-submit">
                          <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary">
                        </div>
                      </div>
                    </form> 
                  </div>  
                </div>
               
               
                <script type="text/javascript"><!--
                            $(document).on('change','select[name=\'customer_group_id\']', function() {
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
//   $(document).ready(function() {
//    function get_Width_Height() {
//     var array = new Array();
//     if(getWidthBrowser() > 766){
//      array[0] = 640;
//      array[1] = 480;
//    } else if(getWidthBrowser() < 767 && getWidthBrowser() > 480) {
//      array[0] = 450;
//      array[1] = 350;
//    }else{
//      array[0] = 300;
//      array[1] = 300;
//    }
//    return array;
//  }
//  $('.colorbox').colorbox({
//   width: get_Width_Height()[0],
//   height: get_Width_Height()[1]
// });
// });
</script>  
<script type="text/javascript">
            function confirmEmail() {
              // var email = document.getElementById("inputEmail").value;
              // var confemail = document.getElementById("inputEmail2").value;
              var email = $("#inputEmailReg").val();
              var confemail = $("#inputEmail2Reg").val();
              if(email !== confemail) {
                $('#inputEmailReg').parent().parent().addClass('has-error');
                $('#inputEmail2Reg').parent().parent().addClass('has-error');
                //jQuery('#emailcheck').show();
              } else {
                //jQuery('#emailcheck').hide();
               $('#inputEmailReg').parent().parent().removeClass('has-error').addClass('has-success');
                $('#inputEmail2Reg').parent().parent().removeClass('has-error').addClass('has-success');;
              }
              // else if(email == confemail){
              //   jQuery('#emailcheck').toggle('hide');
              // }
            }
          </script>
<script type="text/javascript">
            function confirmPassword() {
              var email = $("#inputPasswordReg").val();
              var confemail = $("#inputPassword2Reg").val();
              if(email !== confemail) {
                  $('#inputPasswordReg').parent().parent().addClass('has-error');
                $('#inputPassword2Reg').parent().parent().addClass('has-error');
                // jQuery('#pwcheck').show();
              } else {
                $('#inputPasswordReg').parent().parent().removeClass('has-error').addClass('has-success');;
                $('#inputPassword2Reg').parent().parent().removeClass('has-error').addClass('has-success');;
              }
              // else if(pw == confpw){
              //   jQuery('#pwcheck').toggle('hide');
              // }
            }
            jQuery(document).ready(function(){
    jQuery('.labelholder').labelholder()
  });
    jQuery('#inputTelephone').mask('(000) 000-0000');


          </script>

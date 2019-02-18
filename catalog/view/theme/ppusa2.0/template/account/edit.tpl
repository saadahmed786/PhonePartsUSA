<?php
if(isset($this->session->data['account_error']))
{
?>
<div class="alert alert-danger alert-dismissible"  role="alert"><?php echo $this->session->data['account_error'];?></div>
<?php
// unset($this->session->data['account_error']);
}

?>

<?php
if(isset($this->session->data['success']))
{
?>
<div class="alert alert-success alert-dismissible"  role="alert"><?php echo $this->session->data['success'];?></div>
<?php
// unset($this->session->data['success']);
}

?>
<div class="form-horizontal v-form">    
  <div class="row account-setting-row">
    <div class="col-md-6 account-setting-left">
      <h3 class="uppercase">Account information</h3>
      <form class="edit_account" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
          <div class="col-md-5 mb-sm-15 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
        <div class="form-group labelholder" data-label="First Name">
           
            <input type="text" name="firstname" class="form-control " id="Fname" placeholder="First Name" value="<?php echo $firstname; ?>">
          </div>
          

        </div>
        <div class="col-md-1 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>"></div>

        <div class="col-md-6 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
         <div class="form-group labelholder" data-label="Last Name">
            
            <input type="text" name="lastname" class="form-control" id="Lname" placeholder="Last Name" value="<?php echo $lastname; ?>">
            </div>
          </div>
          <div class="col-md-12 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
        <div class="form-group labelholder" data-label="Business Name (Optional)">
          
            <input type="text" name="business_name" value="<?php echo $business_name; ?>" placeholder="Business Name (Optional)" class="form-control" id="Bname">
          </div>
        </div>
          <div class="col-md-3 add-phone clearfix <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
        <div class="form-group">
          
            <div class="phone-option">
              <select name="phoneselector[]" value="<?php echo $phones[0]['type']; ?>" class="selectpicker">
                <option <?php echo ($phones[0]['type'] == '1')? 'selected=""':''; ?> value="1">Store</option>
                <option <?php echo ($phones[0]['type'] == '2')? 'selected=""':''; ?> value="2">Office</option>
                <option <?php echo ($phones[0]['type'] == '3')? 'selected=""':''; ?> value="3">Mobile</option>
              </select>
            </div>
            </div>
            </div>
            <div class="col-md-1 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>"></div>
            <div class="col-md-8 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
            <div class="form-group labelholder" data-label="Phone Number">
            <input type="tel" class="form-control" name="phonenumber[]" value="<?php echo $phones[0]['number']; ?>" id="inputPhone" placeholder="Phone Number">
            <!-- <div class="clearfix"></div>
            <br>
            <a href="#" class="blue underline more-phone">Add Phone</a> -->
            </div>
            
            
        </div>
       <!--  <div class="phone-copy clearfix">
              <a href="#" class="remove-phone"><i class="fa fa-times"></i></a>
              <div class="phone-option">
                <select name="phoneselector[]" value="<?php echo $phones[0]['type']; ?>"  class="selectpicker">
                  <option <?php echo ($phones[0]['type'] == '1')? 'selected=""':''; ?> value="1">Store</option>
                <option <?php echo ($phones[0]['type'] == '2')? 'selected=""':''; ?> value="2">Office</option>
                <option <?php echo ($phones[0]['type'] == '3')? 'selected=""':''; ?> value="3">Mobile</option>
                </select>
              </div>
              <input type="tel" class="form-control" id="inputPhone" value="<?php echo $phones[0][number]; ?>" name="phonenumber[]" placeholder="Phone...">
              <div class="clearfix"></div>
              <br>
            </div> -->
          
        <input type="hidden" name="phones" id="phones" value=""/>
          <div class="col-md-5 mb-sm-15 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
        <div class="form-group labelholder" data-label="Email Address">
            
            <input type="text" onkeyup="confirmEmail();" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php echo $email; ?>">
          </div>
          </div>
          <div class="col-md-1 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>"></div>
          <div class="col-md-6 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
            <div class="form-group labelholder" data-label="Confirm Email Address">
            <input type="text" onkeyup="confirmEmail();" class="form-control" id="confemail" placeholder="Confirm Email Address" value="<?php echo $email; ?>">
            <div id="emailcheck" style="display:none;">Email Addresses do not match.</div>
          </div>
          </div>

          <div class="col-md-12">
        <div class="form-group labelholder" data-label="RepairDesk API Token (Optional)">
          
            <input type="text" name="repairdesk_token" value="<?php echo $repairdesk_token; ?>" placeholder="RepairDesk API Token (Optional)" class="form-control" id="repairdesk_token" readOnly>
          </div>
        </div>
     
        <div class="form-group ">
          <div class="col-md-12 <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
            <button class="btn btn-primary update" type="submit">Update information</button>
          </div>
        </div>
        

        <!-- <div class="border"></div> -->
        <!-- <h3 class="uppercase">update password</h3> -->
          <div class="col-md-5 mb-sm-15">
        <div class="form-group labelholder" data-label="Password">
            
            <input type="password" name="password" onkeyup="confirmPassword();" class="form-control" id="password" placeholder="Password" >
          </div>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-6">
           <div class="form-group labelholder" data-label="Confirm Password">
            <input type="password" name="confirm_password" onkeyup="confirmPassword();" class="form-control" id="confpassword" placeholder="Confirm Password" >
            <div id="pwcheck" style="display:none;">Passwords do not match.</div>
          </div>
        </div>
        <!-- <div class="form-group">
          <div class="col-md-12">
            <button class="btn btn-primary" type="submit">Change password</button>
          </div>
        </div> -->
        <div class="form-group">
          <div class="col-md-12">
            <button class="btn btn-primary" onclick="checkPassword();" type="button">Update Password</button>
          </div>
        </div>
      </div>
    </form>
    <div class="col-md-6 account-setting-right <?php echo (isset($this->session->data['wholesale_account_user'])?'hidden':'');?>">
       <div class="border"></div>
      <h3 class="uppercase">Contact History</h3>
      <div class="form-group">
        <div class="col-md-12">
          <div class="address-scroll">
            <?php foreach($infos as $info){ ?>
              <li>
                <input id="rdo_info_<?php echo $info['address_id'];?>" type="radio" name="rdo_info" value="<?php echo $info['address_id'];?>" class="css-radio2 addressCheck2">
                <label for="rdo_info_<?php echo $address['address_id'];?>" class="css-radio2" style="font-size:13px"><?php echo $info['firstname'].' '.$info['lastname'].', '.$address['company'].' ( '.$address['telephone'].' ) ';?></label>
              </li>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="form-group">

            <div class="col-md-12">
              <div class="btn-row">
                <!-- <button class="btn btn-primary" type="button" onclick="makePrimary()">MAKE PRIMARY</button> -->
                <!-- <button class="btn btn-primary" type="button" onclick="serializeForm()">UPDATE</button> -->
               <button class="btn btn-primary" type="button" onclick="removeAddress2()">REMOVE</button>
              </div>
            </div>
          </div>

      <div class="border"></div>
      <h3 class="uppercase">Address History</h3>
      <div class="form-group">
        <div class="col-md-12">
          <div class="address-scroll">
            <?php foreach($addresses as $address){ ?>
              <li>
                <input id="rdo<?php echo $address['address_id'];?>" type="radio" name="rdo_address" value="<?php echo $address['address_id'];?>" onclick="populateAddressFields('<?php echo $address['address_1'];?>','<?php echo $address['city'];?>','<?php echo $address['zone_id'];?>','<?php echo $address['country_id'];?>','<?php echo $address['postcode'];?>','<?php echo $address['address_2'];?>','<?php echo $address['firstname'];?>','<?php echo $address['lastname'];?>','<?php echo $address['company'];?>');" class="css-radio2 addressCheck">
                <label for="rdo<?php echo $address['address_id'];?>" class="css-radio2" style="font-size:13px"><?php echo $address['firstname'].' '.$address['lastname'].($address['company']!=''?', '.$address['company']:'').', '.$address['address_1'].', '.$address['address_2'].', '.$address['city'].', '.$address['zone'].', '.$address['postcode'] ;?>.</label>
              </li>
              <?php } ?>
            </div>
          </div>
        </div>
        <form id="address">
          <div class="form-group">

            <div class="col-md-12">
              <div class="btn-row">
                <button class="btn btn-primary" type="button" onclick="makePrimary()">MAKE PRIMARY</button>
                <button class="btn btn-primary" type="button" onclick="serializeForm()">UPDATE</button>
                <p style="padding-top:5px"><button class="btn btn-primary" type="button" onclick="removeAddress()">REMOVE</button></p>
              </div>
            </div>
          </div>
           <div class="col-md-5 mb-sm-15">
          <div class="form-group labelholder" data-label="First Name">
              <!-- <label for="zipcode" class="control-label">ZIP Code</label> -->
              <input type="text" class="form-control" id="firstname" placeholder="First Name" name="firstname" value="">
            </div>
            </div>

            <div class="col-md-1"></div>

            <div class="col-md-6 mb-sm-15">
          <div class="form-group labelholder" data-label="Last Name">
              <!-- <label for="zipcode" class="control-label">ZIP Code</label> -->
              <input type="text" class="form-control" id="lastname" placeholder="Last Name" name="lastname" value="">
            </div>
            </div>

            <div class="col-md-12 mb-sm-15">
          <div class="form-group labelholder" data-label="Business Name">
              <!-- <label for="zipcode" class="control-label">ZIP Code</label> -->
              <input type="text" class="form-control" id="business_name" placeholder="Business Name" name="business_name" value="">
            </div>
            </div>




            <div class="col-md-12">
          <div class="form-group labelholder" data-label="Street Address">
              
              <input type="text" class="form-control" placeholder="Street Address" id="street" name="address_1" value="">
            </div>
          </div>
            <div class="col-md-12">
          <div class="form-group labelholder" data-label="Suite or Apartment">
              
              <input type="text" class="form-control" id="suite" name="suite" value="" placeholder="Suite or Apartment">
            </div>
          </div>
            <div class="col-md-3 mb-sm-15">
          <div class="form-group labelholder" data-label="Zip Code">
              <!-- <label for="zipcode" class="control-label">ZIP Code</label> -->
              <input type="text" class="form-control" id="zipcode" placeholder="Zip Code" name="postcode" value="">
            </div>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-3 col-xs-5">
            <div class="form-group labelholder" data-label="City">
              
              <input type="text" class="form-control" id="city" placeholder="City" name="city" value="">
            </div>
            </div>
            <div class="col-md-1 col-xs-1"></div>
            <div class="col-md-4 col-xs-6">

              
              <select class="selectpicker" name="zone_id">
                <option value="">State</option>
                <?php
                foreach($zones as $zone)
                {
                  ?>
                  <option value="<?php echo $zone['zone_id'];?>" <?php echo ($zone['zone_id']==$zone_id?'selected="selected"': '');?>><?php echo $zone['name'];?></option>
                  <?php
                }
                ?>
              </select>
            </div>
          
          <div class="form-group">
            <div class="col-md-12">
              
              <select data-size="10" data-live-search="true" class="selectpicker" name="country_id">
                <option value="">Country</option>
                <?php
                foreach($countries as $country)
                {
                  ?>
                  <option value="<?php echo $country['country_id'];?>" <?php echo ($country['country_id']==$country_id?'selected="selected"': '');?>><?php echo $country['name'];?></option>
                  <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function confirmEmail() {
              // var email = document.getElementById("inputEmail").value;
              // var confemail = document.getElementById("inputEmail2").value;
              var email = $("#email").val();
              var confemail = $("#confemail").val();
              if(email !== confemail) {
                jQuery('#emailcheck').show();
              } else {
                jQuery('#emailcheck').hide();
              }
              // else if(email == confemail){
              //   jQuery('#emailcheck').toggle('hide');
              // }
            } 
          </script>
          <script type="text/javascript">
            function confirmPassword() {
              var pw = $("#password").val();
              var confpw = $("#confpassword").val();
              if(pw !== confpw) {
                jQuery('#pwcheck').show();
              } else {
                jQuery('#pwcheck').hide();
              }
              // else if(pw == confpw){
              //   jQuery('#pwcheck').toggle('hide');
              // }
            }
            function checkPassword() {
              var pw = $("#password").val();
              var confpw = $("#confpassword").val();
              if((pw == '' && confpw == '') || (pw != '' && confpw == '') || (pw == '' && confpw != '')) {
                return false;
              } else {
                $('.update').click();
              }
              // else if(pw == confpw){
              //   jQuery('#pwcheck').toggle('hide');
              // }
            }
          </script>
          <script type="text/javascript">
            function populateAddressFields(address_1,city,zone,country,postcode,suite,firstname,lastname,business_name)
            {
               $('#address #firstname').val(firstname);
               $('#address #lastname').val(lastname);
               $('#address #business_name').val(business_name);

              $('#street').val(address_1);
              // $('#street').attr('value','address_1');
              $('#suite').val(suite);
              // $('#suite').attr('value','suite');
              $('#city').val(city);
              // $('#city').attr('value','city');
              $('#zipcode').val(postcode);
              // $('#zipcode').attr('value','postcode');
                  // $('select[name=zone_id]').val(zone_code);
                  $('select[name=zone_id]').selectpicker('val', zone);
                  $('select[name=country_id]').selectpicker('val', country);
                  // $('.selectpicker').selectpicker('val', 'Mustard');

                }

              </script>
              <script type="text/javascript"><!--
                $("select[name=country_id]").bind('change', function() {
                  if (this.value == '') return;
                  $.ajax({
                    url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
                    dataType: 'json',
                    beforeSend: function() {
      // $('#payment-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
    },
    complete: function() {
      // $('.wait').remove();
    },      
    success: function(json) {
      // if (json['postcode_required'] == '1') {
      //  $('#payment-postcode-required').show();
      // } else {
      //  $('#payment-postcode-required').hide();
      // }
      
      html = '<option value="">State</option>';
      
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
      
      $('select[name=zone_id]').html(html);
      $('.selectpicker').selectpicker('refresh');
    },
    error: function(xhr, ajaxOptions, thrownError) {
      alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
                });
              </script>
              <script type="text/javascript">
                function serializeForm()
                {
                  var address_id = $('.addressCheck:checked').val();
                  var formdata = $("#address").serialize() + '&address_id=' + address_id;
                  $.ajax({
                    type: "POST",
                    url: 'index.php?route=account/edit/updateAddress',
                    data: formdata,
                    dataType: "json",

                    }).always(function(json) {
                    if (json['success']) {
                      alert('Updated');
                       $('[for=rdo' + address_id + ']').html(json['firstname'] +' '+ json['lastname'] + (json['company']!=''?', '+json['company']:'') + ', ' + json['address_1'] + ', ' + json['address_2'] + ', ' + json['city'] + ', ' + json['zone']+', '+json['postcode']+'.');
                    }
                  });
      
                         
                }

                function removeAddress() {
                  var address_id = $('.addressCheck:checked').val();
                  $.ajax({
                    type: "POST",
                    url: 'index.php?route=account/edit/deleteAddress',
                    data: {address_id: address_id},
                    dataType: "json"
                  }).always(function(json) {
                    if (json['success']) {
                      $('#rdo' + address_id).parent().remove();
                       // $('#rdo_info_' + address_id).parent().remove();
                    }
                  });
                }

                 function removeAddress2() {
                  var address_id = $('.addressCheck2:checked').val();
                  $.ajax({
                    type: "POST",
                    url: 'index.php?route=account/edit/deleteAddress',
                    data: {address_id: address_id},
                    dataType: "json"
                  }).always(function(json) {
                    if (json['success']) {
                      // $('#rdo' + address_id).parent().remove();
                       $('#rdo_info_' + address_id).parent().remove();
                    }
                  });
                }

                function makePrimary() {
                  var address_id = $('.addressCheck:checked').val();
                  $.ajax({
                    type: "POST",
                    url: 'index.php?route=account/edit/setDefault',
                    data: {address_id: address_id},
                    dataType: "json"
                  }).always(function(json) {
                    if (json['success']) {
                      alert('Updated');
                    }
                  });
                }
                $(document).ready(function(){
    $('.labelholder').labelholder()
  });
              </script>

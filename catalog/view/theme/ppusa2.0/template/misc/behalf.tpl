
<div class="tab-inner pd60" id="behalf-payment-element">
						    	
					    	</div>


					    	<script>
                $(document).ready(function(){
                  $('#confirm-payment-method').addClass('disabled');
                  $('#confirm-payment-method').attr('disabled','disabled');
                })
  window.behalfPaymentReady = function() {
    var config = {
      "clientToken" : "<?php echo $behalf_client_token;?>",
      "showPromo" : true,
      "callToAction" : {
        "workflow" : "noredirect",
        "text" : "In order to enjoy these terms, pay with Behalf on your upcoming order."
      }
    };

    BehalfPayment.init(config);

    var checkoutContext = {
      "buyerDetails" : {
      "email" : "<?php echo $email;?>",
      "sellerBuyerId" : "<?php echo $email;?>",
      "behalfBuyerId" : "<?php echo $behalf_buyer_id['behalf_buyer_id'];?>",
      "businessName" : "<?php echo $payment_address['company'];?>",
      "tin" : "",
      "ownerFirstName" : "<?php echo $payment_address['firstname'];?>",
      "ownerLastName" : "<?php echo $payment_address['lastname'];?>",
      "physicalAddress" : {
        "line1" : "<?php echo $payment_address['address_1'];?>",
        "line2" : "<?php echo $payment_address['address2'];?>",
        "city" : "<?php echo $payment_address['city'];?>",
        "state" : "<?php echo $payment_address['zone_code'];?>",
        "zipCode" : "<?php echo $payment_address['postcode'];?>",
        "phone" : "<?php echo $telephone;?>"
      },
      "additionalData": {
       
        "highValueBuyer": true
      }  
    },
      "paymentDetails" : {
        "sellerOrderId" : "<?php echo $this->session->data['order_id'];?>",
        "shippingAmount": <?php echo (float)$shipping_cost;?>,
        "taxAmount": <?php echo (float)$tax;?>,
        "totalAmount" : <?php echo (float)$total;?>,
        "orderDescription": "<?php echo $this->session->data['order_id'];?> by <?php echo $email;?>",
        "shippingAddress" : {
        "firstName" : "<?php echo $payment_address['firstname'];?>",
        "lastName" : "<?php echo $payment_address['lastname'];?>",
          "line1" : "<?php echo $payment_address['address_1'];?>",
          "line2" : "<?php echo $payment_address['address_2'];?>",
          "city" : "<?php echo $payment_address['city'];?>",
          "state" : "<?php echo $payment_address['zone_code'];?>",
          "zipCode" : "<?php echo $payment_address['postcode'];?>",
          "phone" : "<?php echo $telephone;?>"
        }
      }
    };


   BehalfPayment.load("#behalf-payment-element", checkoutContext);

    BehalfPayment.on("error", function(eventData) {
  //callback code
  alert('error');
  // alert(JSON.stringify(eventData));
});

    BehalfPayment.on("app_failed_to_load", function(eventData) {
  //callback code
  alert(eventData['errorDetails'][0]['message']);
});

    BehalfPayment.on("buyer_status_changed", function(eventData) {
  //callback code
  // console.log('buyer_status_changed:'+JSON.stringify(eventData));

   $.ajax({
        url: 'index.php?route=payment/behalf/addBuyer',
        type: 'post',
        data: 'email='+encodeURIComponent('<?php echo $email;?>')+'&behalfBuyerId='+encodeURIComponent(eventData.behalfBuyerId)+'&behalfBuyerStatus='+encodeURIComponent(eventData.buyerStatus),
        dataType: 'json',
        success: function(json) {
        
          console.log('Buyer ID saved successfully');
            
        
        }
      });

});


  BehalfPayment.on("payment_status_changed", function(eventData) {
  //callback code
  // console.log('payment_status_changed:'+JSON.stringify(eventData));
  if(eventData.paymentStatus === "approved" || eventData.paymentStatus === "in_review" ) {
    // var token = paymentData.paymentToken;
    //send token to server
      $('#confirm-payment-method').removeClass('disabled');
      $('#confirm-payment-method').removeAttr('disabled');
        // $('#confirm-payment-method').addClass('disabled');
                  // $('#confirm-payment-method').attr('disabled','disabled');
  }

   $.ajax({
        url: 'index.php?route=payment/behalf/addPayment',
        type: 'post',
        data: 'paymentStatus='+encodeURIComponent(eventData.paymentStatus)+'&paymentToken='+encodeURIComponent(eventData.paymentToken)+'&behalfBuyerId='+encodeURIComponent(eventData.behalfBuyerId),
        dataType: 'json',
        success: function(json) {
        
          console.log('Payment saved successfully');
            $('#confirm-payment-method').trigger('click');
        
        }
      });


});



  };
  
</script>
<script src="<?php echo $behalf_sdk_uri;?>" async></script>
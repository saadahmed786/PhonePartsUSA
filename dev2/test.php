<div class="tab-inner pd60" id="behalf-payment-element">
                  
                </div>
<script>
  window.behalfPaymentReady = function() {
    var config = {
      "clientToken" : "test1234",
      "showPromo" : true,
      "callToAction" : {
        "workflow" : "noredirect",
        "text" : "In order to enjoy these terms, pay with Behalf on your upcoming order."
      }
    };

    BehalfPayment.init(config);

    var checkoutContext = {
      "buyerDetails" : {
      "email" : "john@everycompany.com",
      "sellerBuyerId" : "BUYER-1234",
      "behalfBuyerId" : "<behalfBuyerId>",
      "businessName" : "Every Company",
      "tin" : "123456789",
      "ownerFirstName" : "John",
      "ownerLastName" : "Doe",
      "physicalAddress" : {
        "line1" : "126 5th Av.",
        "line2" : "10th Floor Unit 10c",
        "city" : "New York",
        "state" : "NY",
        "zipCode" : "10011",
        "phone" : "8779439962"
      },
      "additionalData": {
        "firstPurchaseDate": "2016-08-18",
        "lifetimeSalesTotal": 500000,
        "avgOrderAmount": 2500,
        "highValueBuyer": true
      }  
    },
      "paymentDetails" : {
        "sellerOrderId" : "ABC-123",
        "shippingAmount": 45.50,
        "taxAmount": 55,
        "totalAmount" : 2500,
        "orderDescription": "SKU / product name",
        "shippingAddress" : {
        "firstName" : "John",
        "lastName" : "Doe",
          "line1" : "126 5th Av.",
          "line2" : "10th Floor Unit 10c",
          "city" : "New York",
          "state" : "NY",
          "zipCode" : "10011",
          "phone" : "8779439962"
        }
      }
    };

    BehalfPayment.load("#behalf-payment-element", checkoutContext);
  };
</script>
<script src="https://sdk.demo.behalf.com/sdk/v4/mock/behalf_payment_sdk.js" async></script>
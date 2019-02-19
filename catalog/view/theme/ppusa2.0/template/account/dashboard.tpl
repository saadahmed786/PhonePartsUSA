
<div class="tab-inner">
                <div class="row hidden">
                    <div class="col-md-8 col-sm-7 ">
                      
                    </div>
                    <div class="col-md-4  col-sm-5 text-right all-voucher text-xs-center">
                      <a href="javascript:void(0)" class="btn btn-primary mb-xs-20">request a product</a>
                    </div>
                  </div>
                  <div class="row service-row hidden-xs hidden">
                    <div class="tab-inner pd60 pb60 pdr0">
                    <div id="chartContainer" style="height: 350px; "></div>
                    
                  </div>
                  </div>
                </div>
                <!-- <div class="border"></div> -->
                <div class="tab-inner pd60">
        <?php echo $orders;?>
          </div>
                <!-- <div class="border"></div> -->
                <div class="tab-inner pd60" style="margin-top:38px">
                  <div class="row">
                    <div class="col-md-8 col-sm-7 col-xs-12 ">
                      <h3 class="uppercase blue-title">available credit  vouchers</h3>
                    </div>
                    <div class="col-md-4  col-sm-5 text-right all-voucher text-sm-center col-xs-12">
                      <a href="<?php echo $viewvouchers; ?>" class="btn btn-primary mb-xs-20">view all vouchers</a>
                    </div>
                  </div>
                  <div class="parent"></div>
                  <?php echo $vouchers;?>
                </div>
                <?php echo $template_returns;?>


                <?php echo $buyback;?>

                <script type="text/javascript">
                  $(document).ready(function(){
  // $.ajax({
  //   url: 'index.php?route=account/order/getOrder',
  //   dataType: 'json',     
  //   beforeSend: function(){
  //     $('#chartContainer').html('<div style="text-align:center;"><img src="catalog/view/theme/ppusa2.0/images/spinner2.gif" style="width:30%"></div>');
  //   },
  //   success: function(json) {

  //     points = [];
  //      if (json != '') {
  //       for (i = 0; i < json.length; i++) {
  //         value = parseFloat(json[i]['total'].split('$')[1]);
  //         points.push({label: json[i]['date_added'], y: value });
  //       }
       
  //      var chart = new CanvasJS.Chart("chartContainer", { 
  //         title: {
  //           text: ""
  //         },
  //         data: [
  //           {
  //             type: "area",
  //             dataPoints: points
  //           }
  //         ],
  //         axisY:{
  //           prefix: "$"
  //         },
  //         axisX:{
  //           labelFontSize: 10
  //         }
  //       });
  //      chart.render();
  //    } else {
  //     $('#chartContainer').attr('style','height: 50px')
  //     $('#chartContainer').html('<br><br><div style="text-align:center;"><h3 style="font-size:20px;font-weight:500">No Order(s) Found. Order Chart is generated based on the Orders made.</h3></div>');
  //    }
  //   },
  //   error: function(xhr, ajaxOptions, thrownError) {
  //   }
  // });



});

                </script>
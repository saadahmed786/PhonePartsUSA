<?php
include_once 'auth.php';
include_once 'inc/functions.php';
page_permission('sales_dashboard');
function str_decorate($str)
{
  $str = str_replace("_", " ", $str);
$str = ucwords($str);
$str = str_replace('Ups', "UPS", $str);
$str = str_replace('Usps', "USPS", $str);
$str = str_replace('Fedex', "FedEx", $str);

  return $str;
}
if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}

if(!isset($_GET['filter_month']) || !isset($_GET['filter_year']))
{
    $filter_month = date('m');
    $filter_year = date('Y');
}
else
{
    $filter_month = $_GET['filter_month'];
    $filter_year = $_GET['filter_year'];
  
}


if(isset($_POST['type']) && $_POST['type']=='load_data')
{
  $rows = $cache->get('warehouse_report.monthly_chart.'.$filter_month.$filter_year);
  $amount = 0;
  $json = array();
  // print_r($rows);exit;
  foreach($rows as $row)
  {
    // print_r($row);exit;
      $amount+=(float)$row['amount'];
    $json[] = array(
      'total'=>$row['total'],
      'reason'=>utf8_encode(str_replace('Warehouse - ', '', $row['reason'])),

      );
  }
  echo json_encode(array('amount'=>date('M Y',strtotime($filter_year.'-'.$filter_month.'-01')).": $".number_format($amount,2),'data'=>$json));
  exit;
}

if(isset($_POST['type']) && $_POST['type']=='load_data2')
{
  $rows = $cache->get('warehouse_report.monthly_chart2');
  $amount = 0;
  $json = array();
  // print_r($rows);exit;
  foreach($rows as $row)
  {
    // print_r($row);exit;
      $amount+=(float)$row['amount'];
    $json[] = array(
      'total'=>$row['amount'],
      'reason'=>date('m/Y',strtotime($row['date_added'])),

      );
  }
  echo json_encode(array('amount'=>"$".number_format($amount,2),'data'=>$json));
  exit;
}



$rows_main  =  $cache->get('warehouse_report.monthly_chart3');
if(!$rows_main)
{

  
$rows_main = $db->func_query("select a.service_code, sum(c.shipping_cost) as shipping_cost,sum(a.shipping_cost) as shipping_paid,month(c.dateofmodification) as date_month,year(c.dateofmodification) as date_year from inv_orders_details c,inv_shipstation_transactions a where a.order_id=c.order_id and a.voided=0 and date(c.dateofmodification) between '".date('Y-m-01',strtotime('-6 Month'))."' and '".date('Y-m-d')."' group by a.service_code,month(c.dateofmodification),year(c.dateofmodification) order by a.ship_date,a.service_code");

$cache->set('warehouse_report.monthly_chart3',$rows_main);
}

  $amount = 0;
  $json = array();

  $data = array();
  foreach($rows_main as $row)
  {
    $data[$row['date_month'].'-'.$row['date_year']][$row['service_code']] = array('shipping_cost'=>round($row['shipping_cost']-$row['shipping_paid'],2));
  }

  // testObject($data);exit;

  // print_r($rows);exit;
  $labels = array();
  foreach($data as $label => $_temps)
  {
    $labels[] = $label;
    // foreach($_temps as $key =>$_temp)
    // {
    // $labels[] = $key;
      
    // }
  }
  $labels = (array_unique($labels));
  


  $labels2 = array();
  foreach($data as $label => $_temps)
  {
    
    foreach($_temps as $key =>$_temp)
    {
    $labels2[] = $key;
      
    }
  }
  $labels2 = (array_unique($labels2));
  
// print_r($labels2);exit;
  



$inv_query="select a.voucher_id,a.code,sum(a.amount) as amount,count(*) as total,b.reason,b.id as reason_id from oc_voucher a,inv_voucher_reasons b where a.reason_id=b.id and left(b.reason,9)='Warehouse' AND MONTH(a.date_added)='".$filter_month."' and year(a.date_added)='".$filter_year."' group by b.reason ";
$rows = $cache->get('warehouse_report.monthly_chart.'.$filter_month.$filter_year);
if(!$rows)
{
$rows = $db->func_query($inv_query);
$cache->set('warehouse_report.monthly_chart.'.$filter_month.$filter_year,$rows);
}

$rows2 = $cache->get('warehouse_report.monthly_chart2');
if(!$rows2)
{
     $rows2= $db->func_query("select a.date_added,sum(a.amount) as amount,count(*) as total,b.reason from oc_voucher a,inv_voucher_reasons b where a.reason_id=b.id and left(b.reason,9)='Warehouse' group by month(a.date_added),year(a.date_added) order by date_added desc limit 0,13");
 $cache->set('warehouse_report.monthly_chart2',$rows2);    
}
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script src="js/jquery.min.js"></script>
  <script src="js/chart.bundle.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  <title>Warehouse / Shipping Related Report</title>

</head>
<body>
  <?php if (!$_SESSION['hide_header']) { ?>
  <div align="center"> 
    <?php } else { ?>
    <div style="display: none;" align="center">
      <?php } ?>
      <?php include_once 'inc/header.php';?>
    </div>
    <?php if(@$_SESSION['message']):?>
      <div align="center"><br />
        <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
      </div>
    <?php endif;?>
    
    <h2 align="center">Warehouse / Shipping Related Report</h2>
    <table width="98%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          <td colspan="4" align="center" width="50%">
            <div id="container" style="width: 80%;;">
              <canvas id="canvas"></canvas>
            </div>

          </td>
          <td colspan="4" valign="center" width="40%" >
              <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Reason</th>
                          <th>Amount</th>
                          <th># Issued</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                    foreach($rows as $row)
                    {
                    ?>
                      <tr>
                          <td><a href="popupfiles/warehouse_voucher_popup.php?reason_id=<?php echo $row['reason_id'] ?>&reason=<?php echo $row['reason'] ?>&filter_month=<?php echo $filter_month;?>&filter_year=<?php echo $filter_year;?>" class="fancybox3 fancybox.iframe" ><?php echo $row['reason'];?></a>
                          </td>
                          <td><?php echo '$'.number_format($row['amount'],2);?></td>
                          <td><?php echo number_format($row['total']);?></td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
              </table>
          </td>
        </tr>
        <tr>
            <td colspan="8"><hr></td>
        </tr>

        <tr>
          <td colspan="4" align="center"  >
            <div id="container" style="width: 80%;;">
              <canvas id="canvas2"></canvas>
            </div>

          </td>
          <td colspan="4" valign="center"  >
              <table width="100%" class="xtable" cellspacing="0"   align="center" style="margin-top:3px;line-height: 12px">
                  <thead>
                      <tr>
                          <th>Months</th>
                          <th>Total Amount</th>
                          <th>Total Issued</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                    foreach($rows2 as $row)
                    {
                    ?>
                      <tr>
                          <td><a href="warehouse_vouchers_report.php?filter_month=<?php echo date('m',strtotime($row['date_added']));?>&filter_year=<?php echo date('Y',strtotime($row['date_added']));?>"><?php echo date('M-Y',strtotime($row['date_added']));?></a></td>
                          <td><?php echo '$'.number_format($row['amount'],2);?></td>
                          <td><?php echo number_format($row['total']);?></td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
              </table>
          </td>
        </tr>


        <tr>
            <td colspan="8"><hr></td>
        </tr>

        <tr>
          <td colspan="8" align="center"  >
            <div id="container" style="width: 95%;;">
              <canvas id="canvas3"></canvas>
            </div>

          </td>
          
        </tr>

        

      </tbody>
    </table>

  </body>
  </html>
  <script>

  </script>
  <script>

  <?php
  $shipping_color = array(
    'fedex_2day'=>'rgb(220, 57, 18)',
    'fedex_express_saver'=>'rgb(255, 153, 0)',
    'fedex_home_delivery'=>'rgb(255, 255, 0)',
    'fedex_priority_overnight'=>'rgb(16, 150, 24)',
    'fedex_standard_overnight'=>'rgb(51, 102, 204)',
    'usps_first_class_mail'=>'rgb(156, 39, 176)',
    'usps_first_class_package_international'=>'rgb(201, 203, 207)',
    'usps_priority_mail'=>'rgb(0, 150, 136)',
    'ups_ground'=>'rgb(121, 85, 72)',
    'fedex_ground'=>'rgb(0, 0, 0)',
    'usps_priority_mail_international'=>'rgb(118, 255, 3)',
    'fedex_ground_international'=>'rgb(3, 169, 254)',
    'usps_priority_mail_express'=>'rgb(233, 30, 99)',
    'fedex_international_economy'=>'rgb(205, 220, 57)',
    'ups_next_day_air_saver'=>'rgb(118, 255, 30)',
    'ups_3_day_select'=>'rgb(255, 0, 255)',
    'ups_2nd_day_air'=>'rgb(244, 81, 30)',
    'ups_next_day_air'=>'rgb(105,240,174)',

    )
  ?>
    window.chartColors = {
      red: 'rgb(220, 57, 18)',
      orange: 'rgb(255, 153, 0)',
      yellow: 'rgb(255, 255, 0)',
      green: 'rgb(16, 150, 24)',
      blue: 'rgb(51, 102, 204)',
      purple: 'rgb(156, 39, 176)',
      grey: 'rgb(201, 203, 207)'
    };
    
    var color = Chart.helpers.color;
    
    window.onload = function() {

    };
    $(document).ready(function(e){
      setTimeout(function(){
       $.ajax({
        url: 'warehouse_vouchers_report.php?filter_month=<?php echo $filter_month;?>&filter_year=<?php echo $filter_year;?>',
        type: 'post',
        data: {type:'load_data'},
        dataType: 'json',
        beforeSend: function() {
        },  
        complete: function() {
        },      
        success: function(json) {

          var barChartData = {
      labels: [],
      datasets: []
    };


          var newDataset = {
            label: json['amount'],
            backgroundColor: [
            window.chartColors.red,
            window.chartColors.blue,
            window.chartColors.orange,
            window.chartColors.purple,
            window.chartColors.green,
            window.chartColors.grey,
            window.chartColors.yellow,
          ],
            // borderColor: window.chartColors.blue,
            borderWidth: 1,
            data: []
          };
          var labels = json['data'].map(function(item) {
                              
   barChartData.labels.push(item.reason);

 });
          for (var index = 0; index < barChartData.labels.length; ++index) {
            newDataset.data.push(json['data'][index]['total']);
          }
          barChartData.datasets.push(newDataset);

          var ctx = document.getElementById('canvas').getContext('2d');
          window.myBar = new Chart(ctx, {
            type: 'doughnut',
            data: barChartData,
            options: {
              responsive: true,
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text:  json['amount']
              }
            }
          });
                        // window.myBar.update(); 
                      }
                    }); 
     }, 1000);


      setTimeout(function(){
       $.ajax({
        url: 'warehouse_vouchers_report.php?filter_month=<?php echo $filter_month;?>&filter_year=<?php echo $filter_year;?>',
        type: 'post',
        data: {type:'load_data2'},
        dataType: 'json',
        beforeSend: function() {
        },  
        complete: function() {
        },      
        success: function(json) {
              var barChartData = {
      labels: [],
      datasets: []
    };
          var newDataset = {
            label: 'Vouchers',
            backgroundColor: window.chartColors.blue,
            // borderColor: window.chartColors.blue,
            borderWidth: 1,
            data: []
          };
          var labels = json['data'].map(function(item) {
                              
   barChartData.labels.push(item.reason);

 });
          for (var index = 0; index < barChartData.labels.length; ++index) {
            newDataset.data.push(json['data'][index]['total']);
          }
          barChartData.datasets.push(newDataset);

          var ctx = document.getElementById('canvas2').getContext('2d');
          window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
              responsive: true,
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text:  '12 Months Data'
              }
            }
          });
                        // window.myBar.update(); 
                      }
                    }); 

    


     }, 
    1500);


window.randomScalingFactor = function() {
    return Math.round(Math.random() * (10000 - 0) + 0);
  };
      // chart 3

      var barChartData = {
      labels: [<?php echo "'" . implode("','", $labels) . "'";?>],
      datasets: [<?php
 $i=1;
 foreach($labels2 as $label2)
{ 

  ?>
      {
        label: '<?php echo str_decorate($label2);?>',
        backgroundColor:'<?php echo $shipping_color[$label2];?>',
        stack: 'Stack <?php echo $i-1;?>',
        data: [
          <?php
          $a='';
           foreach($labels as $label)
          {
            
            foreach($data[$label][$label2] as $row)
            {
              if(!$row)
              {
                $row = 0.00;
              }
              $a.=round($row,2).',';

            }

          }
          echo rtrim($a,',');
          ?>

        ]
      },
      <?php
      // if($i==5) break;;
      
      $i++;
    
    }
    ?>]

    };

    function random_rgba() {
    var o = Math.round, r = Math.random, s = 255;
    return 'rgb(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + r().toFixed(1) + ')';
}

    window.onload = function() {
      var ctx = document.getElementById('canvas3').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Shipping Profit / Loss - Multi Axis'
          },
          tooltips: {
            mode: 'index',
            intersect: false
          },
          scales: {
            xAxes: [{
              stacked: true,
            }],
            yAxes: [{
              stacked: true
            }]
          }
        }
      });
    };



    });
    


  </script>

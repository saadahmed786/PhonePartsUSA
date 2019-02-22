<?php
include_once '../auth.php';
include_once '../inc/functions.php';
page_permission('purchasing_metrics');

$sku = $_GET['sku'];

if(isset($_POST['type']) and $_POST['type']=='load_data')
{

  
  $chart_weeks = (int)$_POST['chart_weeks'];
  $chart_group = $_POST['chart_group'];

  $json = array();
 

   
    

      if($chart_group=='Weeks')
      {
    // $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by yearweek(b.date,3) order by b.date desc limit  ".$chart_weeks);

      	$rows = $db->func_query("select sum(b.product_price) as sold_price,sum(b.product_true_cost*b.product_qty) as sold_cost,a.order_date from inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status) in ('shipped','processed','completed') and lower(b.product_sku)='".$sku."'  group by yearweek(a.order_date,3) order by a.order_date desc limit  ".$chart_weeks);
      }
      elseif($chart_group=='Months')
      {
           // $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by month(b.date),year(b.date) order by b.date desc limit  ".$chart_weeks);

      	$rows = $db->func_query("select sum(b.product_price) as sold_price,sum(b.product_true_cost*b.product_qty) as sold_cost,a.order_date from inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status) in ('shipped','processed','completed') and lower(b.product_sku)='".$sku."'  group by month(a.order_date),year(a.order_date) order by a.order_date desc limit  ".$chart_weeks);
      }
      else
      {
          // $rows = $db->func_query("SELECT SUM(b.total) AS total,b.date FROM inv_customer_data_summary b,inv_customers a where lower(trim(a.email))=lower(trim(b.email))  and b.type='order' and a.user_id='".$user_id."' group by quarter(b.date),year(b.date) order by b.date desc limit  ".$chart_weeks); 

      	$rows = $db->func_query("select sum(b.product_price) as sold_price,sum(b.product_true_cost*b.product_qty) as sold_cost,a.order_date from inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status) in ('shipped','processed','completed') and lower(b.product_sku)='".$sku."'  group by quarter(a.order_date),year(a.order_date) order by a.order_date desc limit  ".$chart_weeks);
      }

    
  
    
  
  for($i=count($rows)-1;$i>=0;$i--)
  {
    if($chart_group=='Weeks')
    {
      $_date = date('W-Y',strtotime($rows[$i]['order_date']));
    }
    elseif($chart_group=='Months')
    {
     $_date =  date('m-Y',strtotime($rows[$i]['order_date']));
    }
    else
    {
    $_date = date('m-Y',strtotime($rows[$i]['order_date'])) ;
    }
    $json[] = array(
      'total'=>$rows[$i]['sold_price'],
      'date'=>$_date,

      );
  }

  echo json_encode($json);exit;
}

?>
<html>
<head>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script src="../js/chart.bundle.js"></script>
<script type="text/javascript" src="../include/bootstrap/js/bootstrap.min.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" /> -->
<!-- <link rel="stylesheet" type="text/css" href="../include/bootstrap/css/bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="../include/bootstrap/css/bootstrap-theme.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css"> -->
<style>
.read_class{
	background-color: #eee;
}


</style>
<!-- <link rel="stylesheet" type="text/css" href="<?php echo $host_path;?>/include/xtable.css" media="screen" /> -->
</head>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center"><?php echo strtoupper($sku);?> - Chart</h2>
		<?php
		if($_SESSION['message'])
		{
			?>
			<h5 align="center" style="color:red"><?php echo $_SESSION['message'];?></h5>
			<?php
			unset($_SESSION['message']);
		}
		?>
		
		
			<div id="" class="">
				<table width="100%">
				<tr>
				<td align="center"> <?php
             $group_bys = array('Weeks','Months','Quarters');
             ?>
             <label for="group_by">Period</label>
             <select name="group_by" onchange="changeChartGroupValues(this.value);makeChart();">
               <?php
               foreach($group_bys as $key => $group_by)
               {
                ?>
                <option value="<?=$group_by;?>" <?php if($_GET['group_by']==$group_by) echo 'selected';?>><?=$group_by;?></option>
                <?php
              }

              ?>
            </select>
            <?php

               // $chart_weeks = array('12'=>'12 Weeks','24'=>'24 Weeks','36'=>'36 Weeks','52'=>'52 Weeks','76'=>'76 Weeks');
            ?>

             <label for="chart_weeks">Group By</label>
             <select name="chart_weeks" class="chart_weeks" onchange="makeChart()">
              <?php
                foreach($chart_weeks as $_week => $chart_week)
                {
                  ?>
                    <option value="<?php echo $_week;?>" <?php echo ($_GET['chart_weeks']==$_week?'selected':'');?>><?php echo $chart_week;?></option>
                  <?php
                }
              ?>
            </select></td>
				</tr>

				<tr>

				<td align="center">
				<div id="container" style="width:80%">
              
          </div>
				</td>
				</tr>
				</table>

					</div>
			
			</div>
			
		
	</div>	
</body>


<script>
function changeChartGroupValues(objValue)
      {
        // var objValue = $('select[name=group_by] option:selected').val();
        var html='';
          if(objValue=='Weeks')
          {
              html='<option value="12">12 Weeks</option>';
              html+='<option value="24">24 Weeks</option>';
              html+='<option value="36">36 Weeks</option>';
              html+='<option value="52">52 Weeks</option>';
              html+='<option value="76">76 Weeks</option>';
          }
          else if(objValue=='Months')
          {
               html='<option value="3">3 Months</option>';
              html+='<option value="6">6 Months</option>';
              html+='<option value="12">12 Months</option>';
              html+='<option value="18">18 Months</option>';
              html+='<option value="24">24 Months</option>';
          }
          else
          {
             html='<option value="4">4 Quarters</option>';
              html+='<option value="8">8 Quarters</option>';
              
          }

          $('.chart_weeks').html(html);
      }

      $(document).ready(function(){
      	$('select[name=group_by]').trigger('change');
      	makeChart();
      });
     
      window.chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  blue: 'rgb(51, 102, 204)',
  purple: 'rgb(153, 102, 255)',
  grey: 'rgb(201, 203, 207)'
};

    
    var color = Chart.helpers.color;
    
      function makeChart()
      {
      	var barChartData = {
      labels: [],
      datasets: []

    };
    

      	$.ajax({
                          url: 'item_chart.php?sku=<?php echo $sku;?>',
                          type: 'post',
                          data: {type:'load_data',chart_group:$('select[name=group_by]').val(),chart_weeks:$('select[name=chart_weeks]').val()},
                          dataType: 'json',
                          beforeSend: function() {
                          	

                          },  
                          complete: function() {

                          },      
                          success: function(json) {
                          	$("canvas#canvas").remove();
$("div#container").append('<canvas id="canvas" ></canvas>');

                            var newDataset = {
        label: 'Sales',
        backgroundColor: window.chartColors.blue,
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: []
      };



                              var labels = json.map(function(item) {
   //  console.log(item.date);                           
   barChartData.labels.push(item.date);
    
  });

                         for (var index = 0; index < barChartData.labels.length; ++index) {
        newDataset.data.push(json[index]['total']);
      }

      barChartData.datasets.push(newDataset);

                            
var ctx = document.getElementById('canvas').getContext('2d');
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
            text: $('select[name=chart_weeks]:eq(0) option:selected').text()+' Data'
          }
        }
      });





      // bar2



                        // window.myBar.update(); 

                        // ctx.destroy();

                          }
                        }); 
      }

</script>
</html>
<?php
include_once '../auth.php';
include_once '../inc/functions.php';
page_permission('finance_report');


if(isset($_POST['action']) && $_POST['action']=='show_orders')
{
  $order_date = $_POST['order_date'];
// echo "SELECT order_id,tax,order_date,email,customer_name,(sub_total+shipping_amount) as order_total  from inv_orders where lower(order_status) in ('shipped','processed','completed','unshipped') and tax>0.00 and date(order_date)='".$order_date."' ";exit;
  $subs_sub = $db->func_query("SELECT order_id,tax,order_date,email,customer_name,(sub_total+shipping_amount) as order_total  from inv_orders where lower(order_status) in ('shipped','processed','completed','unshipped') and tax>0.00 and date(order_date)='".$order_date."' ");


$html='';
$html.=' <table width="100%" class="tablesorter xtable" border="1" cellspacing="10" cellpadding="20" align="center">
                        <thead>
                        <tr>
                        <th>Order Date</th>
                        <th>Order ID</th>
                        <th>Customer Account</th>
                        <th>Order Total</th>
                        <th>Tax</th>
                        </tr>
                        </thead>
                        <tbody>';
                        
                         foreach($subs_sub  as $k => $sub_sub)
                      {
                        
                        $html.='<tr>
                        <td>'.americanDate($sub_sub['order_date']).'</td>
                        <td>'.linkToOrder($sub_sub['order_id'],$host_path).'</td>
                        <td>'.linkToProfile($sub_sub['email'],$host_path).'</td>
                        <td>$'.number_format($sub_sub['order_total'],2).'</td>
                        <td>$'.number_format($sub_sub['tax'],2).'</td>
                        </tr>';
                        
                      }
                    
                    $html.='</tbody></table>';
                    echo $html;
exit;
}


$query = "SELECT sum(tax) as tax,month(order_date) as order_month,year(order_date) as order_year from inv_orders where lower(order_status) in ('shipped','processed','completed','unshipped') and order_date between   DATE_SUB(NOW(), INTERVAL 12 MONTH) and now() group by month(order_date),year(order_date) order by order_date desc ";

// echo $query;exit;


$rows = $db->func_query($query);
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script src="../js/jquery.min.js"></script>
  <script src="../js/chart.bundle.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
  <link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  <title>Sales Tax Detail</title>

</head>
<body>
  <div style="display: none;" align="center">
      <?php include_once '../inc/header.php';?>
    </div>
    <h3 align="center">Sales Tax Details (12 Month)</h3>

    <table width="100%" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          <td  valign="center" width="100%" >
              <table width="100%" class="tablesorter xtable" border="1" cellspacing="10" cellpadding="20" align="center">
                <thead>
                  <tr style="background: linear-gradient(#777, #444);color: #fff">
                   
                    <th>Date Month</th>
                    <th>Total Tax</th>
                    
                   
                  </tr>
                </thead>
                  <tbody>
                    <?php
                    
                    foreach ($rows as $i => $detail) { ?>
                   
                    <tr style="cursor:pointer" onclick="$('.row_<?php echo $i;?>').toggle();$('[id*=row_<?php echo $i;?>_]').hide();" >
                    
                      <td style="font-weight:bold;"">
                        
                     <?php echo date('F y',strtotime($detail['order_year'].'-'.$detail['order_month'].'-01'));?>
                      </td>
                      <td style="font-weight: bold"><?php echo '$'. number_format($detail['tax'],2);?></td>
                    
                      
                    </tr>
                    <?php
                    $subs = $db->func_query("SELECT sum(tax) as tax,date(order_date) as order_date  from inv_orders where lower(order_status) in ('shipped','processed','completed','unshipped') and month(order_date)='".$detail['order_month']."' and year(order_date)='".$detail['order_year']."' group by date(order_date)order by order_date  ");
                    foreach($subs as $j => $sub)
                    {
                      ?>
                      <tr class="row_<?php echo $i;?>" style="display:none;cursor:pointer" onClick="showData('<?php echo $sub['order_date'];?>','<?php echo $i;?>','<?php echo $j;?>');">
                     
                      <td><?php echo americanDate($sub['order_date'],false);?></td>
                      <td><?php echo '$'.number_format($sub['tax'],2);?></td>
                      </tr>

                      <?php
                      
                     
                        ?>


                        <tr id="row_<?php echo $i;?>_<?php echo $j;?>"  style="display:none" >
                     
                        <td colspan="2">
                       
                        </td>
                      </tr>

                        <?php
                     
                    }
                    ?>
                    <?php } ?>
                  </tbody>
              </table>
            
            </td>
          </tr>
      </tbody>
    </table>

  </body>
  <script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(e) {
     $(".tablesorter").tablesorter(); 
   });
    function showData(order_date,i,j)
    {
      $.ajax({

      url: 'finance_sales_tax.php',

      type: 'post',

      data: {action: 'show_orders',order_date:order_date},



      beforeSend: function () {

        $('#row_'+i+'_'+j+' td').html('<img src="<?php echo $host_path;?>images/loading.gif" height="18" width="18">');

       
        $('#row_'+i+'_'+j).toggle();

      },

      complete: function () {

        
        // $('#tr_'+i+'_'+j+' td').html('<img onClick="showOrders(\''+store_type+'\')" style="cursor:pointer" src="<?php echo $host_path;?>images/plus.png" height="18" width="18">');

      },

      success: function (data) {

        if (data=='') {

          alert('Some Error Occured! Please refresh your page');

        }



        if (data) {

         
        // $('#row_'+i+'_'+j).show();

          
        $('#row_'+i+'_'+j+' td').html(data)

        }

      }

    });
    }
  </script>
  </html>

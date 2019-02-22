<?php
include_once '../auth.php';
include_once '../inc/functions.php';
$date= $_GET['date'];
$user_id = $_GET['user_id'];


$query = "SELECT (sale_amount) as total_amount,(voucher_amount) as commission_voucher,(commission) as commission,(date_updated)  from inv_user_commission WHERE yearweek(date_updated,3)='".$date."' and user_id='".$user_id."' and commission<>0.00   order by date_updated ";

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
  <title>Reason Classified Vouchers</title>

</head>
<body>
  <div style="display: none;" align="center">
      <?php include_once '../inc/header.php';?>
    </div>
    <h3 align="center">Commission Detail (<?php echo $_GET['date'];?>)</h3>

    <table width="100%" style="border: 0px solid #585858;"  align="center">
      <tbody>

        <tr>
          <td  valign="center" width="100%" >
              <table width="100%" class="tablesorter xtable" border="1" cellspacing="10" cellpadding="20" align="center">
                <thead>
                  <tr style="background: linear-gradient(#777, #444);color: #fff">
                    <th>#</th>
                    <th>Date</th>
                    <th>Total Sale</th>
                    <th>Vouchers Used</th>
                    <th>Net</th>
                    <!-- <th>Used</th> -->
                    <th>Commission</th>
                   
                  </tr>
                </thead>
                  <tbody>
                    <?php
                    
                    foreach ($rows as $i => $detail) { ?>
                   
                    <tr>
                    <td><?= ($i) + 1 ?></td>
                      <td>
                        
                     <?php echo americanDate($detail['date_updated'],false);?>
                      </td>
                      <td><?php echo '$'. number_format($detail['total_amount'],2);?></td>
                      <td><?php echo '$'. number_format($detail['commission_voucher'],2);?></td>
                      <td><?php echo '$'. number_format($detail['total_amount']  + $detail['commission_voucher'],2);?></td>
                      <td><?php echo '$'. number_format($detail['commission'],2);?></td>
                      
                    </tr>
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
  </script>
  </html>

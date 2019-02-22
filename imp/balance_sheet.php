<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

$pageLink = 'balance_sheet.php';
$page_title = 'Balance Sheet';
//page_permission('trial_balance');

if(!isset($_GET['hide_header']))
{
  unset($_SESSION['hide_header']);
}


// Getting Page information
if (isset($_GET['page'])) {
  $page = intval($_GET['page']);
}
if ($page < 1) {
  $page = 1;
}

 $start = (int)($page-1)*50;
  $end = 50;

if(!isset($_GET['date']))
{
  $_GET['date'] = date('Y-m-d H:i:s');
}
else
{
  $_GET['date'] = date('Y-m-d H:i:s',strtotime($_GET['date']));
}
// echo $_GET['date'];
$parameters = '&page='.$page;

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
$parameters = str_replace('sort=' . $_GET['sort'], '', $parameters);
$parameters = str_replace('&order_by=' . $_GET['order_by'], '', $parameters);

$data = array();
$inv_query="SELECT a.account_code,b.name,sum(a.debit)-sum(a.credit) as amount from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and date(a.date_added)<='".$_GET['date']."' group by a.account_code order by a.account_code";
// echo $inv_query;exit;
$rows = $db->func_query($inv_query);

// print_r($data);exit;data
  



?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title><?php echo $page_title;?> | PhonePartsUSA</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <script type="text/javascript" src="../js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $host_path; ?>include/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>

   

         
  <link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" type="text/css" href="../include/xtable.css" media="screen" />
  
  <link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap.flaty.min.css">
  
  <link rel="stylesheet" type="text/css" href="http://phonepartsusa.com/catalog/view/theme/ppusa2.0/stylesheet/font-awesome.css">
  
    <style type="text/css" media="screen">

  .nav a{
    color:#000;
  }  
  a{
    color:#000;
  } 

    #xcontent{width: 100%;
      height: 100%;
      top: 0px;
      left: 0px;
      position: fixed;
      display: block;
      opacity: 0.8;
      background-color: #000;
      z-index: 99;}
    </style>
    <style>
      #interactive.viewport {position: relative; width: 100%; height: auto; overflow: hidden; text-align: center;}
      #interactive.viewport > canvas, #interactive.viewport > video {max-width: 100%;width: 100%;}
      canvas.drawing, canvas.drawingBuffer {position: absolute; left: 0; top: 0;}
    </style>

</head>
  <script>
   jQuery(document).ready(function () {
        // jQuery('.fancybox').fancybox({width: '400px', height: '200px', autoCenter: true, autoSize: false});
        jQuery('.fancybox2').fancybox({width: '500px', height: '500px', autoCenter: true, autoSize: false});
      });
  </script>
</head>

<body>
  <div id="xcontent" style="display:none"><div style="color:#fff;
      top:40%;
      position:fixed;
      left:40%;
      font-weight:bold;font-size:25px"><img src="https://phonepartsusa.com/catalog/view/theme/default/image/loader_white.gif" /><span style="margin-left: 11%;
      margin-top: 33%;
      position: absolute;

      width: 201px;">Please wait...</span></div></div>  
  <div class="col-md-12">
    <div align="center"> 
      <?php include_once 'inc/header.php';?>
    </div>
    <?php if ($_SESSION['message']) { ?>
    <div class="col-md-12 bg-danger text-white">
      
        <?php
        echo $_SESSION['message'];
        unset($_SESSION['message']);
        ?>
      
      
    </div>
    <?php } ?>
    <!-- <h2 style="font-size:15px"> <?= $pageName; ?>:: All Orders <a href="#" id="reload">(reload)</a></h2> -->
      <div class="row" style="margin-top:10px">
      <div class="col-md-1">

      </div>
      <div class="col-md-10">
      <div class="row" >
        <div class="col-md-2 text-right font-weight-bold"></div>
        <div class="col-md-8">

         <div id="datetimepicker4"></div>

      </div>
      <div class="col-md-2"></div>
      </div>

      <div class="col-md-12 text-center" style="margin-bottom:10px">
      <input type="button" class="btn btn-primary" onClick="searchIt();" value="Search">
      <input type="button" class="btn btn-secondary" onClick="resetIt();" value="Reset">

      </div>

      <div class="row ">
        <div class="col-md-12 bg-primary  text-white p-3">
          <h3>Balance Sheet</h3>
        </div>
        
        
      </div>

     
      

      <?php
      $assets = $db->func_query("SELECT * FROM inv_charts WHERE left(main_code,2) in ('03','06','01') and type='G' order by FIELD(LEFT(main_code,2),'06','03','01'),main_code asc");
      $balance = 0.00;
        foreach($assets as $asset)
        {


          
          

          if(substr($asset['main_code'],3,2)=='00')
          {
             if($balance)
          {
            ?>
              <div class="row" style="margin-top:3px;border-top:1px solid #000;border-bottom: 1px solid #000">
              <div class="col-md-6 font-weight-bold">TOTAL</div>
              <div class="col-md-6 font-weight-bold"><?php echo   ($balance>0?'$'.number_format($balance,2).'':'$'.number_format($balance*(-1),2));?></div>
              <!-- <span class="font-weight-bold"><?php echo '$'.number_format($balance,2);?></span> -->
              </div>
            <?php
            $balance = 0.00;
          }

            ?>
              <div class="row" style="margin-top:10px">
      <div class="col-md-12 bg-info text-white p-3">
      <h5 class="font-weight-bold"><?php echo $asset['name'];?></h5>
      </div>
      </div>
            <?php
          }
          else
          {

            $rows = $db->func_query("SELECT a.account_code,b.name, coalesce(SUM(a.debit)-sum(a.credit),0) as balance from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and left(a.account_code,5)='".substr($asset['main_code'],0,5)."' and a.date_added<='".$_GET['date']."' group by a.account_code");

            $balance_check = $db->func_query_first_cell("SELECT  coalesce(SUM(a.debit)-sum(a.credit),0) as balance from inv_accounts_vouchers a,inv_charts b where a.account_code=b.main_code and left(a.account_code,5)='".substr($asset['main_code'],0,5)."' and a.date_added<='".$_GET['date']."' ");
            // print_r($rows);exit;

            if($balance_check!=0)
            {
            ?>
               <div class="row" style="margin-top:5px" >
                <div class="col-md-12">
                 <span class="font-weight-bold"><?php echo $asset['name'];?></span>
                </div>
              </div>

        <?php
              foreach($rows as $row)
              {
                $balance = $balance + $row['balance'];

                ?>
                  <div class="row" >
                <div class="col-md-6"><a href="<?php echo $host_path;?>account_journal.php?code=<?php echo $row['account_code'];?>"><?php echo $row['name'];?></a></div>
                <div class="col-md-6"><?php echo   ($row['balance']>0?'$'.number_format($row['balance'],2).'':'$'.number_format($row['balance']*(-1),2));?></div>
              </div>
                <?php
              }
              ?>
             
              <?php

              
            }

          }
          ?>

          

          <?php
        }
      ?>
      
      </div>
      <div class="col-md-1">

      </div>

      </div>
  </body>

  </html>
  
  <script>

  $(function () {
    // var d = new Date('2019',2,1,2,25,0);
    var d = new Date('<?php echo date('Y',strtotime($_GET['date']));?>','<?php echo (int)date('n',strtotime($_GET['date']))-1;?>','<?php echo (int)date('d',strtotime($_GET['date']));?>','<?php echo (int)date('H',strtotime($_GET['date']));?>','<?php echo (int)date('i',strtotime($_GET['date']));?>','<?php echo (int)date('s',strtotime($_GET['date']));?>');
    // var d = new Date('<?php echo strtotime($_GET['date']);?>');
    console.log(d);

    console.log('<?php echo date('n',strtotime($_GET['date']));;?>');
var month = d.getMonth();
var day = d.getDate();
var year = d.getFullYear();

                $('#datetimepicker4').datetimepicker({

                   inline: true,
                sideBySide: true,
                 useCurrent: false,

                });
                $('#datetimepicker4').data("DateTimePicker").date(d);

            });

  function searchIt()
  {
    window.location='<?php echo $pageLink;?>?date='+encodeURIComponent($('#datetimepicker4').data('date'));
  }
  function resetIt()
  {
    window.location='<?php echo $pageLink;?>';
  }
  </script>
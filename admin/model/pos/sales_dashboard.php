<?php
include_once 'auth.php';
include_once 'inc/split_page_results.php';
include_once 'inc/functions.php';
if($_SESSION['group']!='Sales Agent' and $_SESSION['login_as']!='admin')
{
  echo 'Permission Denied!';
}
$sql_condition = " 1 > 1";

$order_status_query_array = array('processed','shipped','completed','unshipped');

function getWeeksMonthsYears($start_date,$end_date,$type='Weeks')
{
  
$return = array();
$startTime = strtotime($start_date);
$endTime = strtotime($end_date);


switch($type)
{
  case 'Days':
  $_x = 'd';
  $type =  'day';
  break;
  case 'Weeks':
  $_x = 'W';
  $type =  'week';
  break;
  case 'Months':
  $_x = 'm';
  $type =  'month';
  break;
  case 'Years':
  $_x = 'Y';
  $type =  'year';
  break;
  default:
  $_x = 'W';
  $type='week';
  break;

}

while ($startTime < $endTime) {
// echo date('Y-m-d',$startTime )."<br>";  
    $return[] = date('m/d/Y',$startTime ); 

    $startTime += strtotime('+1 '.$type, 0);
    
}
// exit;
// print_r($return);exit;
return $return;
}

if(!isset($_GET['group_by']))
{
  $group_by = 'Weeks';
  $_GET['group_by'] = $group_by;
}

if(!isset($_GET['filter_date_start']))
{
  
  $_GET['filter_date_start'] = date('Y-m-d',strtotime('-7 days'));
  $_GET['filter_date_end'] = date('Y-m-d');
}


    if(isset($_GET['user_id']))
    {
      $sql_condition = " user_id='".(int)$_GET['user_id']."'";
    }

 

  if($_SESSION['group']=='Sales Agent')
  {
    $sql_condition = " user_id='".(int)$_SESSION['user_id']."'";

  }



$inv_query= "select * from inv_customers where $sql_condition";
// echo $inv_query;
$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1)*$num_rows;

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_return_item_wise.php",$page);
$inv_orders = $db->func_query($splitPage->sql_query);



?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="include/jquery-ui.css">
		<script src="js/jquery.min.js"></script>
        <script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.1.5"></script>
	 <link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		<script src="js/jquery-ui.js"></script>
        <title>Sales Dashboard</title>
        
        
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <br />
        
        <br />
        
        <h2 align="center">Sales Dashboard</h2>
       
        
        <form name="order" action="" method="get">
                <table width="90%" cellpadding="10" style="border: 0px solid #585858;"  align="center">
                <tbody>
                    <tr>
                        

                        
                        <?php
                        if($_SESSION['login_as']=='admin')
                        {
                          $agents = $db->func_query("select id,name from inv_users WHERE group_id='10' and status=1 ");
                          ?>
                        <td>
                            <label for="start_date">Agent:</label>
                            <select name="user_id">
                            <option value="">Select Agent</option>
                            <?php
                            foreach($agents as $agent)
                            {
                              ?>
                              <option value="<?=$agent['id'];?>" <?php if($_GET['user_id']==$agent['id']) echo 'selected';?>><?=$agent['name'];?></option>
                              <?php
                            } 

                            ?>
                            </select>
                       </td>
                          <?php
                        }
                        ?>
                        
                        <td>
                           <label for="filter_date_start">Date Start</label>
                           <input type="text" name="filter_date_start" class="datepicker" value="<?php echo @$_GET['filter_date_start'];?>">
                           <label for="filter_date_end">Date End</label>
                           <input type="text" name="filter_date_end" class="datepicker" value="<?php echo @$_GET['filter_date_end'];?>">

                           <?php
                           $group_bys = array('Years','Months','Weeks','Days');
                           ?>
                            <label for="group_by">Group By</label>
                           <select name="group_by">
                           <?php
                           foreach($group_bys as $key => $group_by)
                           {
                            ?>
                            <option value="<?=$group_by;?>" <?php if($_GET['group_by']==$group_by) echo 'selected';?>><?=$group_by;?></option>
                            <?php
                           }

                           ?>
                           </select>
                       </td>
                       

                       

                       <td><input type="submit" value="Search" name="submit" style="margin: 10px 0 0 60px"></td>
                    </tr>

                    <tr>
                        <?php if($inv_orders):?>
                            <td colspan=8>
                                <table width="100%" cellspacing="0" cellpadding="5px" border="1" align="center">
                                    <thead>
                                        <tr style="background-color:#e5e5e5;">
                                            <th>SN</th>
                                            <th>Date</th>
                                            <th>Email</th>
                                            <th>Name</th>
                                            <th>Telephone</th>
                                            <th>Customer Group</th>
                                            <?php
                                            if($_GET['group_by']!='Years')
                                            {
                                            ?>
                                            <th>Amt Purchased</th>
                                            <th>Amt Returned</th>
                                            <?php
                                          }
                                          ?>
                                            <?php
                                            if($_GET['group_by']=='Months' || $_GET['group_by']=='Years' )
                                            {
                                            ?>
                                            <th># of Orders</th>
                                            <th># of Returns</th>
                                            <th>Amt Refunded</th>
                                            <th>Amt Store Credit</th>
                                            <th>Amt Replacement</th>
                                            <?php
                                          }
                                          ?>
                                            <th>Total Purchased</th>
                                            <th>Total Returned</th>
                                            <?php
                                            if($_SESSION['login_as']=='admin')
                                            {
                                            ?>
                                            <th>Total Profit</th>
                                            <?php
                                          }
                                          ?>
                                        </tr>
                                    </thead>
                                    <?php $i = $splitPage->display_i_count();
                                      ?>
                                        <?php
                                        $amount_purchased = '0.00';
                                        $amount_returned = '0.00';
                                        $amount_refunded = '0.00';
                                        $amount_store_credit = '0.00';
                                        $amount_replacement = '0.00';
                                        $total_purchased = '0';
                                        $total_returned = '0';
                                        $total_profit = '0.00';
										foreach($inv_orders as $row)
										{
                     // echo "SELECT SUM(order_price) FROM inv_orders WHERE trim(LOWER(email))='".trim(strtolower($row['email']))."' and order_date BETWEEN '".$_GET['filter_date_start']."' AND '".$_GET['filter_date_end']."'";exit;
                      $total_purchased = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders WHERE  LOWER(order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and trim(LOWER(email))='".trim(strtolower($row['email']))."' and order_date BETWEEN '".$_GET['filter_date_start']."' AND '".$_GET['filter_date_end']."'");
                      $total_returned = $db->func_query_first_cell("SELECT sum(b.price) FROM inv_return_decision b,inv_returns a WHERE a.id=b.return_id and b.action='Issue Replacement' and TRIM(lower(a.email))='".trim(strtolower($row['email']))."' and a.date_completed BETWEEN '".$_GET['filter_date_start']."' AND '".$_GET['filter_date_end']."' ");
										?>
                                            <tr id="<?php echo $row['id'];?>" style="background-color:#c0c0c0;font-weight:bold">
                                                <td align="center"><?php echo $i; ?></td>
                                                <td align="center"> - </td>
                                                <td align="center"><?php echo linkToProfile($row['email'], $host_path);?></td>
                                                <td align=""><?php echo $row['firstname'].' '.$row['lastname'];?></td>
                                                <td align=""><?php echo $row['telephone'] ;?></td>
                                                <td align=""><?php echo $row['customer_group'] ;?></td>
                                                <?php
                                            if($_GET['group_by']!='Years' )
                                            {
                                            ?>
                                                      <td align="center"> - </td>
                                                <td align="center"> - </td>
                                      <!--           <td align="right">$<?php echo number_format($amount_purchased,2) ;?></td>
                                                <td align="right">$<?php echo number_format($amount_returned,2) ;?></td> -->
                                             <?php
                                             }

                                             ?>
                                             <?php
                                            if($_GET['group_by']=='Months' || $_GET['group_by']=='Years' )
                                            {
                                              ?>
                                               <td align="center"> - </td>
                                              <td align="center"> - </td>
                                              <td align="center"> - </td>
                                              <td align="center"> - </td>
                                              <td align="center"> - </td>


                                             <!--  <td align="center"><?php echo $no_of_orders ;?></td>
                                              <td align="center"><?php echo $no_of_returns ;?></td>
                                              <td align="right">$<?php echo number_format($amount_refunded,2) ;?></td>
                                              <td align="right">$<?php echo number_format($amount_store_credit,2) ;?></td>
                                              <td align="right">$<?php echo number_format($amount_replacement,2) ;?></td> -->
                                              <?php
                                            }
                                            ?>  
                                            <td align="right">$<?=number_format($total_purchased,2);?></td>
                                            <td align="right">$<?=number_format($total_returned,2);?></td>
                                            <?php
                                            if($_SESSION['login_as']=='admin')
                                            {
                                              ?>
                                              <td align="center"> - </td>
                                               <!-- <td align="right">$<?php echo number_format($total_profit,2) ;?></td> -->
                                              <?php
                                            }
                                            ?>
                                            </tr>

                                          <?php 
                                          $rows = getWeeksMonthsYears($_GET['filter_date_start'],$_GET['filter_date_end'],$_GET['group_by']);

                                          foreach($rows as $key => $data)
                                          {
                                            switch($_GET['group_by'])
                                            {
                                              case 'Days':
                                              $query1 =" DATE(order_date)='".$data."'";
                                              $query2 = " DATE(a.date_completed)='".$data."'";
                                              $query3 = " DATE(date_added)='".$data."'";
                                              break;
                                              
                                              case 'Months':
                                              $query1 =" month(order_date)='".date('m',strtotime($data))."' and YEAR(order_date)='".date('Y',strtotime($data))."'";
                                                $query2 = " month(a.date_completed)='".date('m',strtotime($data))."' and YEAR(a.date_completed)='".date('Y',strtotime($data))."'";
                                                $query3 =" month(date_added)='".date('m',strtotime($data))."' and YEAR(date_added)='".date('Y',strtotime($data))."'";
                                              break;
                                              case 'Years':
                                               $query1 =" YEAR(order_date)='".date('Y',strtotime($data))."'";
                                                $query2 =" YEAR(a.date_completed)='".date('Y',strtotime($data))."'";
                                                $query3 =" YEAR(date_added)='".date('Y',strtotime($data))."'";
                                              break;
                                              default:
                                               $query1 =" WEEKOFYEAR(order_date)='".date('W',strtotime($data))."' and YEAR(order_date)='".date('Y',strtotime($data))."'";
                                               $query2 =" WEEKOFYEAR(a.date_completed)='".date('W',strtotime($data))."' and YEAR(a.date_completed)='".date('Y',strtotime($data))."'";
                                               $query3 =" WEEKOFYEAR(date_added)='".date('W',strtotime($data))."' and YEAR(date_added)='".date('Y',strtotime($data))."'";
                                              break;

                                            }

                                             $amount_purchased = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders WHERE TRIM(lower(email))='".trim(strtolower($row['email']))."' AND LOWER(order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and $query1");
                                             // $amount_returned = $db->func_query_first_cell("SELECT SUM(b.price) FROM inv_return_decision b,inv_returns a WHERE a.id=b.return_id and TRIM(lower(a.email))='".trim(strtolower($row['email']))."' and $query2 ");


                                            $no_of_orders = $db->func_query_first_cell("SELECT COUNT(*) FROM inv_orders WHERE TRIM(lower(email))='".trim(strtolower($row['email']))."' AND LOWER(order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and $query1");
                                              $no_of_returns = $db->func_query_first_cell("SELECT COUNT(*) FROM inv_returns a WHERE TRIM(lower(a.email))='".trim(strtolower($row['email']))."' and $query2 ");
                                              $amount_refunded = $db->func_query_first_cell("SELECT sum(b.price) FROM inv_return_decision b,inv_returns a WHERE a.id=b.return_id and b.action='Issue Refund' and TRIM(lower(a.email))='".trim(strtolower($row['email']))."' and $query2 ");
                                              $amount_store_credit = $db->func_query_first_cell("SELECT sum(`amount`) FROM oc_voucher WHERE status=1 and TRIM(lower(to_email))='".trim(strtolower($row['email']))."' and  $query3");
                                              $amount_replacement = $db->func_query_first_cell("SELECT SUM(order_price) FROM inv_orders WHERE LCASE(payment_source) = 'replacement' and TRIM(lower(email))='".trim(strtolower($row['email']))."' AND LOWER(order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and $query1");
                                              
                                              $amount_returned = $amount_refunded + $amount_store_credit + $amount_replacement;

                                              
                                              $profit = $db->func_query_first_cell("SELECT SUM(profit) FROM inv_orders WHERE TRIM(lower(email))='".trim(strtolower($row['email']))."' AND LOWER(order_status) IN (". "'" . implode("','", $order_status_query_array) . "'".") and $query1");
                                            $profit = $profit - $amount_refunded - $amount_store_credit - $amount_replacement;
                                              ?>
                                            <tr >
                                                <td align="center"> - </td>
                                                <td align="center"><?=$data;?></td>
                                                <td align="center"> - </td>
                                                <td align="center"> - </td>
                                                <td align="center">- </td>
                                                <td align="center"> - </td>
                                                <?php
                                            if($_GET['group_by']!='Years' )
                                            {



                                             
                                            ?>
                                                     
                                                <td align="right">$<?php echo number_format($amount_purchased,2) ;?></td>
                                                <td align="right">$<?php echo number_format($amount_returned,2) ;?></td>
                                             <?php
                                             }

                                             ?>
                                             <?php
                                            if($_GET['group_by']=='Months' || $_GET['group_by']=='Years' )
                                            {
                                              
                                              ?>
                                               


                                              <td align="center"><?php echo $no_of_orders ;?></td>
                                              <td align="center"><?php echo $no_of_returns ;?></td>
                                              <td align="right">$<?php echo number_format($amount_refunded,2) ;?></td>
                                              <td align="right">$<?php echo number_format($amount_store_credit,2) ;?></td>
                                              <td align="right">$<?php echo number_format($amount_replacement,2) ;?></td>
                                              <?php
                                            }
                                            ?>  
                                            <td align="center"> - </td>
                                            <td align="center"> - </td>
                                            <?php
                                            if($_SESSION['login_as']=='admin')
                                            {
                                             
                                              ?>
                                              <!-- <td align="center"> - </td> -->
                                               <td align="right" style="color:<?php echo  ($profit>0?'green':'red');?>">$<?php echo number_format($total_profit,2) ;?></td>
                                              <?php
                                            }
                                            ?>
                                            </tr>

                                            <?php



                                          }

                                          ?>

                                            <?php $i++;  ?>
                                            <?php
										}
										?>
                                    
                                </table>
                            </td>  
                            
                        <?php else : ?> 
                        
                            <td colspan=4><label style="color: red; margin-left: 600px;">No Record Found</label></td>
                             
                        <?php endif;?>
                    </tr>
                    
                    <tr>
                       <td colspan="5" align="left">
                           <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                       </td>
                       
                       <td colspan="6" align="right">
                       		<?php echo $splitPage->display_links(10,$parameters);?>
                       </td>
                    </tr>
             </tbody>
        </table>
    </form>
</body>
</html>
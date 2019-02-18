<?php

include_once 'auth.php';

include_once 'inc/split_page_results.php';

include_once 'inc/functions.php';

page_permission('email_return_report');

if(isset($_REQUEST['submit'])){

    $inv_query   = '';

    



    $parameters  = $_SERVER['QUERY_STRING'];

	

	 $date_sent = $db->func_escape_string($_REQUEST['date_sent']);

        $order_id   = $db->func_escape_string(trim($_REQUEST['order_id']));

        $rma_number   = $db->func_escape_string(trim($_REQUEST['rma_number']));

        $email = strtolower($db->func_escape_string(trim($_REQUEST['email'])));

        



        if(@$date_sent){

            $conditions[] =  " a.date_sent >= '".date('Y-m-d',strtotime($date_sent))."' ";

        }



        if(@$order_id){

            $conditions[] =  " LCASE(a.order_id) = LCASE('$order_id') ";

        }



        if(@$rma_number){

            $conditions[] =  " LCASE(b.rma_number) = LCASE('$rma_number') ";

        }

        

        if(@$email){

        	$conditions[] =  " LOWER(a.customer_email) = LCASE('$email') ";

        }



        

            $condition_sql = implode(" AND " , $conditions);

        

        

        if(!$condition_sql){

            $condition_sql = ' 1 = 1';

        }

        

        $inv_query = "SELECT 

  a.*,

  b.rma_number 

FROM

  `inv_email_report` a 

  LEFT OUTER JOIN `inv_returns` b 

    ON (a.`order_id` = b.`order_id`) WHERE $condition_sql GROUP BY a.customer_email,a.date_sent ORDER BY a.email_report_id DESC";



}

else{

    $inv_query = "SELECT 

  a.*,

  b.rma_number 

FROM

  `inv_email_report` a 

  LEFT OUTER JOIN `inv_returns` b 

    ON (a.`order_id` = b.`order_id`) GROUP BY a.customer_email,a.date_sent ORDER BY a.email_report_id DESC";

}



if(isset($_GET['page'])){

    $page = intval($_GET['page']);

}

if($page < 1){

    $page = 1;

}



$max_page_links = 10;

$num_rows = 50;

$start = ($page - 1)*$num_rows;



$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "report_return_emails.php",$page);

$inv_orders = $db->func_query($splitPage->sql_query);



?>

<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <link rel="stylesheet" href="include/jquery-ui.css">

		<script src="js/jquery.min.js"></script>

       

		<script src="js/jquery-ui.js"></script>

        <title>Report Return Emails</title>

        

        <script>

				$('.fancybox3').fancybox({ width: '980px' , autoCenter : true , autoSize : true });

        </script>

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

        

        <h2 align="center">Emails</h2>

        

        <form name="order" action="" method="get">

                <table width="90%" cellpadding="10" style="border: 0px solid #585858;"  align="center">

                <tbody>

                    <tr>

                        



                        

                        

                        <td>

                            <label for="start_date">Order ID:</label>

                            <input type="text" name="order_id" value="<?php echo @$_REQUEST['order_id'];?>" />

                       </td>

                        <td>

                            <label for="start_date">RMA #:</label>

                            <input type="text" name="rma_number" value="<?php echo @$_REQUEST['rma_number'];?>" />

                       </td>

                       <td>

                            <label for="start_date">Email:</label>

                            <input type="text" name="email" value="<?php echo @$_REQUEST['email'];?>" />

                       </td>

                        

                       <td>

                            <label for="start_date">Date Sent:</label>

                            <input type="text" class="datepicker" value="<?php echo @$_REQUEST['date_sent'];?>" name="date_sent" size="20" style="width: 110px;" readonly="readonly" value="<?php echo @$_REQUEST['date_sent'];?>" />

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

                                            <th>Order ID</th>

                                             <th>RMA #</th>

                                            <th>Customer Name</th>

                                            <th>Customer Email</th>

                                            <th>Items Returned</th>

                                            <th>Resolution</th>

                                            <th>Completed By</th>

                                            <th>Action</th>

                                            

                                        </tr>

                                    </thead>

                                    <?php $i = $splitPage->display_i_count();

                                      ?>

                                        <?php

										foreach($inv_orders as $email)

										{

											$profile_id = $db->func_query_first_cell('SELECT `customer_id` from `inv_customers` WHERE `email` = "'. $email['customer_email'] .'"');

										?>

                                            <tr id="<?php echo $email['email_report_id'];?>">

                                                <td align="center"><?php echo $i; ?></td>

                                                

                                                 <td align="center"><?php echo americanDate($email['date_sent']);?></td>

                                                <td align="center">

                                                	<a href="viewOrderDetail.php?order=<?php echo $email['order_id']?>"><?php echo @$email['order_id'];?></a>

                                                </td>

                                                <td align="center">

                                                	<a href="return_detail.php?rma_number=<?php echo $email['rma_number'];?>"><?php echo @$email['rma_number'];?></a>

                                                </td>

                                                

                                                 <td align="center"><?php echo @$email['customer_name'];?></td>

                                                

                                                <td align="center"><?= linkToProfile($email['customer_email']); ?></td>

                                                

                                                 <td align="center"><?php 

												 $rows = $db->func_query("SELECT * FROM inv_email_report WHERE order_id='".$email['order_id']."' AND sku IS NOT NULL");

												 echo count($rows).' item(s) returned<br />';

												 foreach($rows as $row)

												 {

													 /*echo $db->func_query_first_cell("SELECT

b.`name`

FROM

    `oc_product` a

    INNER JOIN `oc_product_description` b

        ON (a.`product_id` = b.`product_id`) WHERE a.sku='".$row['sku']."'");*/

		echo '<a href="'.$host_path.'product/'.$row['sku'].'">'.$row['sku']."</a><br>";

													 

													 

												 }

												 

												 

												 ?></td>

                                                

                                                

                                                <td align="center"><?php echo @$email['resolution'];?></td>

                                                

                                                <td align="center"><?php echo get_username($email['sent_by']);?></td>

                                                

                                                

                                                

                                                <td align="center">

                                                  		<a href="<?php echo $host_path;?>/popupfiles/view_email.php?email_id=<?php echo $email['email_report_id']?>" class="fancybox3 fancybox.iframe">View Email</a>

                                                </td>

                                            </tr>

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
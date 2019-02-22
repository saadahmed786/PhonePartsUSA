<?php

include_once 'auth.php';
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

$max_page_links = 10;
$num_rows = 10;
$start = ($page - 1)*$num_rows;

if($_GET['order_id']){
	$order_id = $db->func_escape_string(trim($_GET['order_id']));	
	$inv_query  = "select * from inv_fb_errors where order_id = '$order_id' order by dateofmodification DESC";
}
else{
	$inv_query  = "select * from inv_fb_errors order by dateofmodification DESC";
}

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "error_logs.php",$page);
$error_logs = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Error Logs</title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form method="get">
	        	Order ID:
	        	<input type="text" name="order_id" value="<?php echo $_GET['order_id'];?>" />
	        	<input type="submit" name="search" value="Search" />
	        </form>
	    </div>    
        
        <?php if($error_logs):?>
             <table width="90%" cellspacing="0" cellpadding="5px" border="1" align="center" style="border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Error Code</th>
                        <th>Error Message</th>
                        <th>Order ID</th>
                        <th>Other</th>
                        <th>Date</th>
                   </tr>
               </thead>
               <tbody>
                 <?php $i = $splitPage->display_i_count();
           		     foreach($error_logs as $error_log):?>
                                            
                       <tr id="<?php echo $error_log['id'];?>">
                          <td align="center"><?php echo $i; ?></td>
                                                
                          <td align="center"><?php echo $error_log['error_code'];?></td>
                          
                          <td align="center"><?php echo $error_log['error_message'];?></td>
                          
                          <td align="center">
                          		<a href="viewOrderDetail.php?order=<?php echo $error_log['order_id'];?>"><?php echo $error_log['order_id'];?></a>
                          </td>
                          
                          <td align="center" style="width:500px;float:left;word-wrap:break-word;border:0" ><?php print_r(json_decode($error_log['other_details'],true));?></td>
                                                
                          <td align="center"><?php echo date('d-M-Y H:i:s' , strtotime($error_log['dateofmodification']));?></td>
                        </tr>
                  <?php $i++; endforeach; ?>
                      
                  <tr>
                  	  <td colspan="6" align="right">
                       	  <br />
	                        <?php echo $display = $splitPage->display_count("Displaying %s to %s of (%s)");
	                              print "&nbsp;";
	                              $display_links_string = $splitPage->display_links(10,$parameters);
	                              echo $display_links_string;
	                         ?>
                       </td>
                  </tr>
               </tbody>   
            </table>   
        <?php else : ?> 
              <p>
                 <label style="color: red; margin-left: 600px;">No logs Exist</label>
              </p>     
        <?php endif;?>
   </body>
</html>        
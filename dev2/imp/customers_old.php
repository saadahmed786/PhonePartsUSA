<?php
include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';
page_permission('customers');
if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;

$order_id = (int)$_REQUEST['order_id'];
$keyword  = $db->func_escape_string($_REQUEST['keyword']);
$filter_city = $db->func_escape_string($_REQUEST['filter_city']);
$filter_state = $db->func_escape_string($_REQUEST['filter_state']);

$filter_order_select  = $db->func_escape_string($_REQUEST['filter_order_select']); 
$filter_order_range1  = $db->func_escape_string($_REQUEST['filter_order_range1']); 
$filter_order_range2  = $db->func_escape_string($_REQUEST['filter_order_range2']); 

$filter_total_select  = $db->func_escape_string($_REQUEST['filter_total_select']); 
$filter_total_range1  = $db->func_escape_string($_REQUEST['filter_total_range1']); 
$filter_total_range2  = $db->func_escape_string($_REQUEST['filter_total_range2']); 




$where = array();
$having = array();

if($order_id){
	$where[] = " customer_id in (select customer_id from oc_order where order_id  = '$order_id' ) ";
	$parameters[] = "order_id=$order_id";
	$parameters2[] = "order_id=$order_id";
}

if($filter_city){
	$where[] = " LOWER(shipping_city) LIKE '%".$filter_city."%' ";
	$parameters[] = "filter_city=$filter_city";
	$parameters2[] = "filter_city=$filter_city";
}

if($filter_state){
	$where[] = " LOWER(shipping_zone) LIKE '%".$filter_state."%' ";
	$parameters[] = "filter_state=$filter_state";
	$parameters2[] = "filter_state=$filter_state";
}

if($keyword){
	$where[] = " (email like '%$keyword%' OR firstname like '%$keyword%' OR lastname like '%$keyword%') ";
	$parameters[] = "keyword=$keyword";
	$parameters2[] = "keyword=$keyword";
}

if($filter_order_range1)
{
	$parameters[] = "filter_order_select=$filter_order_select";
$parameters[] = "filter_order_range1=$filter_order_range1";
$parameters[] = "filter_order_range2=$filter_order_range2";
	
$parameters2[] = "filter_order_select=$filter_order_select";
$parameters2[] = "filter_order_range1=$filter_order_range1";
$parameters2[] = "filter_order_range2=$filter_order_range2";
	if($filter_order_select=='BETWEEN')
	{
		if($filter_order_range2)
		{
		$having[] = ' sum(total)>='.(int)$filter_order_range1.' AND sum(total)<='.(int)$filter_order_range2.' ';
		}
		
	}
	else
		{
			$having[] = ' sum(total)'.$filter_order_select.'='.(int)$filter_order_range1.' ';
			
		}
}

if($filter_total_range1)
{
	$parameters[] = "filter_total_select=$filter_total_select";
$parameters[] = "filter_total_range1=$filter_total_range1";
$parameters[] = "filter_total_range2=$filter_total_range2";
	
$parameters2[] = "filter_total_select=$filter_total_select";
$parameters2[] = "filter_total_range1=$filter_total_range1";
$parameters2[] = "filter_total_range2=$filter_total_range2";
	if($filter_total_select=='BETWEEN')
	{
		if($filter_total_range2)
		{
		$having[] = ' sum(price)>='.(int)$filter_total_range1.' AND sum(price)<='.(int)$filter_total_range2.' ';
		}
		
	}
	else
		{
			$having[] = ' sum(price)'.$filter_total_select.'='.(int)$filter_total_range1.' ';
			
		}
}


if($where){
	$where = implode(" AND ", $where);
}
else{
	$where = "1 = 1";
}
if($having)
{
	$having = implode(" AND ",$having);
}
else
{
	$having = " 1 = 1";	
}

$sort = $_GET['sort'];
if(!in_array($sort, array("COUNT(o.order_id)","MAX(o.date_added)"))){
	$sort = "COUNT(o.order_id)";
}
if($sort=='COUNT(o.order_id)')
{
	$sort2 = 'total';	
}
else
{
	$sort2 = 'last_ordered';	
}


$dir = @$_GET['dir'];
if(!$dir || !in_array($dir, array("asc","desc"))){
	$dir = 'desc';
}

$inv_query = "SELECT customer_id,firstname,lastname,email,telephone,shipping_city,shipping_zone,SUM(total) as total,SUM(price) as price,name,last_ordered FROM view_all_customers WHERE $where GROUP BY email having $having  ORDER BY $sort2 $dir";

$splitPage  = new splitPageResults($db , $inv_query , $num_rows , "customers.php",$page);

$customers  = $db->func_query($splitPage->sql_query);
$parameters[] = "sort=".$sort;
$parameters[] = "dir=".$dir;
if($parameters){
	$parameters = implode("&",$parameters);
}
else{
	$parameters = '';
}
if($parameters2){
	$parameters2 = implode("&",$parameters2);
}
else{
	$parameters2 = '';
}

if($dir == 'desc'){
	$dir = 'asc';
}
else{
	$dir = 'desc';
}

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="include/calendar.css" rel="stylesheet" type="text/css" />
        <link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="include/calendar.js"></script>
        <script type="text/javascript" src="include/calendar-en.js"></script>
        <script type="text/javascript" src="include/calhelper.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <title>Customers</title>
    </head>
    <body>
        <?php include_once 'inc/header.php';?>

        <?php if(@$_SESSION['message']):?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
                </div>
        <?php endif;?>
        
        <div align="center">
	        <form name="order" action="" method="get">
	             <table width="90%" cellpadding="10" border="1"  align="center">
	             	  <tr>
	             	  	 <td>Keyword</td>
	             	  	 <td>
	             	  	 	 <input type="text" style="width:100px" name="keyword" value="<?php echo @$_REQUEST['keyword'];?>" />
	             	  	 </td>
	             	  	 
	             	  	 <td>Order ID</td>
	             	  	 <td>
	             	  	 	 <input type="text" style="width:70px" name="order_id" value="<?php echo @$_REQUEST['order_id'];?>" />
	             	  	 </td>
                         	 <td>City</td>
	             	  	 <td>
	             	  	 	 <input type="text" style="width:100px" name="filter_city" value="<?php echo @$_REQUEST['filter_city'];?>" />
	             	  	 </td>
                         
                         	 <td>State</td>
	             	  	 <td>
	             	  	 	 <input style="width:100px" type="text" name="filter_state" value="<?php echo @$_REQUEST['filter_state'];?>" />
	             	  	 </td>
                         
                         
                          <td># Of Orders</td>
	             	  	 <td align="center">
	             	  	 	 <select name="filter_order_select" style="margin-bottom:5px" onChange="if($(this).val()=='BETWEEN') { $('input[name=filter_order_range2]').show();}else{$('input[name=filter_order_range2]').hide();}">
                             
                             <option value=">" <?php if($_GET['filter_order_select']==">") echo 'selected';?>>Above</option>
                             <option value="<" <?php if($_GET['filter_order_select']=="<") echo 'selected';?>>Below</option>
                              <option value="BETWEEN" <?php if($_GET['filter_order_select']=="BETWEEN") echo 'selected';?>>Between</option>
                             </select><br />
                             <input type="text" style="width:80px" name="filter_order_range1"  value="<?php echo @$_GET['filter_order_range1'];?>">
                             <input type="text" style="width:80px;<?php if($_GET['filter_order_select']!='BETWEEN') {echo 'display:none';} ?>" name="filter_order_range2"  value="<?php echo @$_GET['filter_order_range2'];?>" >
                             
                             
	             	  	 </td>
                         
                         <td>Total Amount</td>
	             	  	 <td align="center">
	             	  	 	 <select name="filter_total_select" style="margin-bottom:5px" onChange="if($(this).val()=='BETWEEN') { $('input[name=filter_total_range2]').show();}else{$('input[name=filter_total_range2]').hide();}">
                             
                             <option value=">" <?php if($_GET['filter_total_select']==">") echo 'selected';?>>Above</option>
                             <option value="<" <?php if($_GET['filter_total_select']=="<") echo 'selected';?>>Below</option>
                              <option value="BETWEEN" <?php if($_GET['filter_total_select']=="BETWEEN") echo 'selected';?>>Between</option>
                             </select><br />
                             <input type="text" style="width:80px" name="filter_total_range1"  value="<?php echo @$_GET['filter_total_range1'];?>">
                             <input type="text" style="width:80px;<?php if($_GET['filter_total_select']!='BETWEEN') {echo 'display:none';} ?>" name="filter_total_range2"  value="<?php echo @$_GET['filter_total_range2'];?>" >
                             
                             
	             	  	 </td>
                         
                          
	             	  	 
                         
	             	  	 
	             	  	 <td><input type="submit" name="search" value="Search" class="button" /></td>
	             	  </tr>   
	             </table>
	        </form>
	        
	        <table border="1" cellpadding="5" cellspacing="0" width="80%">
        		<tr style="background:#e5e5e5;">
        			<th>S.N.</th>
        			<th>First Name</th>
        			<th>Last Name</th>
        			<th>Email</th>
                    <th>City</th>
                    <th>State</th>
        			
        			<th>Group</th>
        			<th><a href="customers.php?sort=COUNT(o.order_id)&dir=<?php echo $dir;?>&page=<?php echo $page;?>&<?php echo $parameters2;?>"># Of Orders</a> 
                    <?php
					if($sort=='COUNT(o.order_id)')
					{
						if($dir=='desc')
						{	
						echo '&uarr;';	
						}
						else
						{
							echo '&darr;';	
						}
						
					}
					?>
                    </a></th>
        			<th>Total Amount</th>
        			<th><a href="customers.php?sort=MAX(o.date_added)&dir=<?php echo $dir;?>&page=<?php echo $page;?>&<?php echo $parameters2;?>">Last Order</a> 
                    <?php
					if($sort=='MAX(o.date_added)')
					{
						if($dir=='desc')
						{	
						echo '&uarr;';	
						}
						else
						{
							echo '&darr;';	
						}
						
					}
					?>
                    </a></th>
        			<th>Action</th>
        	   </tr>
		       <?php if($customers):?>
		       		<?php foreach($customers as $i => $customer):?>
					       <tr>
					       		<td><?php echo $i+1;?></td>
			        			<td><?php echo $customer['firstname']?></td>
			        			<td><?php echo $customer['lastname']?></td>
			        			<td><?php echo $customer['email']?></td>
                                <td><?php echo $customer['shipping_city']?></td>
                                <td><?php echo $customer['shipping_zone']?></td>
			        			
			        			<td><?php echo $customer['name']?></td>
			        			<td><?php echo $customer['total']?></td>
			        			<td>$<?php echo number_format($customer['price'],2);?></td>
			        			<td><?php echo date("d M Y h:iA",strtotime($customer['last_ordered']))?></td>
			        			<td>
			        			<?php if($customer['customer_id']>0): ?>	<a href="<?php echo $host_path;?>customer_profile.php?id=<?php echo $customer['customer_id']?>">View Profile</a><?php endif;?>
			        			</td>
					       </tr>
					<?php endforeach;?>       
		       <?php endif;?>
		       
		       	<tr>
                  	  <td colspan="4" align="left">
	                      <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
                      </td>
                      
                      <td colspan="7" align="right">
	                      <?php  echo $splitPage->display_links(10,$parameters); ?>
                      </td>
                </tr>
	        </table>
        </div>
    </body>
</html>
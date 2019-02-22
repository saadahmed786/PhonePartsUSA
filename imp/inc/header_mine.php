<style>
  * { font-family: Verdana, Geneva, sans-serif; font-size:11px; }
</style>
<link href="<?php echo $host_path?>/include/style.css" rel="stylesheet" type="text/css" />
<center>
	<table style="margin:0%;" cellpadding="10" align="center">
	       <?php if(@$_SESSION['error']):?>
	       		<tr>
		            <td colspan="2" align="center">
		               <font color="red"><?php echo $_SESSION['error']; unset($_SESSION['error']);?> <br /></font>
		            </td>
		       </tr>
	       <?php endif;?>
	       
	       <tr>
	         <td width="50">
	         		Welcome <?php echo $_SESSION['login_as']; ?>
	         </td>
	         <td>
		         <div> 
		         	 <ul class="nav">
			         	 <?php if($_SESSION['login_as'] == 'employee'):?>
			         	 	  <li><a href="<?php echo $host_path?>kit_skus.php">Kit Skus</a></li>
			         	 	  
			         	 	  <li><a href="<?php echo $host_path?>order.php">Order History</a></li>
			         	      
			         	      <li><a href="<?php echo $host_path?>ignore.php">Ignore Orders</a></li>
			         	      
		                      <li><a href="<?php echo $host_path?>error_logs.php">Error Logs</a></li>
		                      
		                      <li><a href="<?php echo $host_path?>order_create.php">Create Order</a></li>
			         	 		
			         	 <?php elseif($_SESSION['login_as'] == 'admin'):?>
				         	 <li><a href="<?php echo $host_path?>home.php">Home</a></li>
				         	 
				             <li><a href="<?php echo $host_path?>configuration.php">Configurations</a></li>
				             
				             <li>
				             	<a href="<?php echo $host_path?>order.php">Order History</a>
				             	<ul class="drop">
				             		<li><a href="<?php echo $host_path?>order.php">Orders</a></li>
				             		
									<li><a href="<?php echo $host_path?>amazon/reports.php">Amazon Reports</a></li>		
									
									<li><a href="<?php echo $host_path?>error_logs.php">Error Logs</a></li>     
									
									<li><a href="<?php echo $host_path?>ignore.php">Ignore Orders</a></li>   
									
									<li><a href="<?php echo $host_path?>order_create.php">Create Order</a></li>     	
				             	</ul>	
				             </li>
		                     
		                     <li><a href="<?php echo $host_path?>kit_skus.php">Kit Skus</a></li>
		                     
		                     <li>
		                     	<a href="<?php echo $host_path?>products.php">Inventory</a>
		                     	<ul class="drop">
		                     		 <li><a href="<?php echo $host_path?>products.php">Products</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>product_skus.php">Product SKUs</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>sku_creation.php">SKU Creation</a></li>
		                     	</ul>
		                     </li>
		                     
		                     <li>
		                     	<a href="<?php echo $host_path?>manage_returns.php">Returns</a>
		                     	<ul class="drop">
		                     		 <li><a href="<?php echo $host_path?>manage_returns.php">Manage</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>returns_history.php">History</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>returns.php">Input</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>settings/reject_reasons.php">Reason types</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>manage_returns_po.php">Orders</a></li>
		                     		 
		                     		 <li><a href="<?php echo $host_path?>manage_returns_boxes.php">Shipment Boxes</a></li>
		                     	</ul>
		                     </li>

		                     <li>
		                     	<a href="<?php echo $host_path?>users.php">Users</a>
		                     	<ul class="drop">
		                     		<li><a href="<?php echo $host_path?>users.php">Users</a></li>
		                     		
		                     		<li><a href="<?php echo $host_path?>groups.php">Groups</a></li>
		                     	</ul>
		                     </li>
		                     
		                     <li><a href="<?php echo $host_path?>sales.php">Re-Ordering</a></li>
		                 	 
		                 	 <li>
		                 	 	<a href="<?php echo $host_path?>shipments.php">Manage Shipments</a>
		                 	 	<ul class="drop">
		                 	 		 <li><a href="<?php echo $host_path?>shipments.php">Shipments</a></li>
		                 	 		 
		                 	 		 <?php if($_SESSION['rejected_shipment']):?>
		                 	 			<li><a href="<?php echo $host_path?>rejected_shipments.php">Rejected</a></li>
		                 	 		 <?php endif;?>
		                 	 	</ul>	
		                 	 </li>
		                 	 
		                 	 <li>
		                 	 	<a href="<?php echo $host_path?>finance.php">Finance</a>
		                 	 	<ul class="drop">
		                 	 		<li><a href="<?php echo $host_path?>finance.php">Finance</a></li>
		                 	 		
		                 	 		<li><a href="<?php echo $host_path?>monthly_finance.php">Monthly</a></li>
		                 	 	</ul>
		                 	 </li>
                             
                              <li>
		                 	 	<a href="#">Category Link</a>
		                 	 	<ul class="drop">
		                 	 		<li><a href="<?php echo $host_path?>device_page.php">Device Page</a></li>
                                   
                                    
                                    <li><a href="<?php echo $host_path?>carrier_list.php">Carrier</a></li>
                                    <li><a href="<?php echo $host_path?>manufacturer_list.php">Manufacturer</a></li>
		                 	 		
		                 	 		<li><a href="<?php echo $host_path?>model_list.php">Model</a></li>
                                    <li><a href="<?php echo $host_path?>attribute_group_list.php">Attributes</a></li>
                                     
                                    <li><a href="<?php echo $host_path?>attribute_list.php">Assign Attributes to SKU</a></li>
		                 	 	</ul>
		                 	 </li>
                             
                             <li>
		                 	 	<a href="#">Scrapping</a>
		                 	 	<ul class="drop">
		                 	 		<li><a href="<?php echo $host_path?>scrap_mengtor.php">Scrape Mengtor</a></li>
                                    <li><a href="<?php echo $host_path?>scrap_mobiledefender.php">Scrape Mobile Defender</a></li>
                                   
                                    
                                   
		                 	 	</ul>
		                 	 </li>
                             
		                     
		                 <?php else:?>
		                 	 <?php if($_SESSION['reorder_page']):?>
		                 	 	<li><a href="<?php echo $host_path?>sales.php">Re-Ordering</a></li>
		                 	 <?php endif;?>	
		                 	 
		                 	 <li><a href="<?php echo $host_path?>shipments.php">Manage Shipments</a></li>
		                 	 
		                 	 <?php if($_SESSION['rejected_shipment']):?>
		                 	 		<li><a href="<?php echo $host_path?>rejected_shipments.php">Rejected Shipments</a></li>
		                 	 <?php endif;?>
		                 	 
		                 	 <?php if($_SESSION['sku_creation']):?>
		                 	 		<li><a href="<?php echo $host_path?>sku_creation.php">SKU Creation</a></li>
		                 	 <?php endif;?>
		                 	 
		                 	 <?php if($_SESSION['order_history']):?>
		                 	 		<li>
						             	<a href="<?php echo $host_path?>order.php">Order History</a>
						             	<ul class="drop">
						             		<li><a href="<?php echo $host_path?>order.php">Orders</a></li>
						             		
											<li><a href="<?php echo $host_path?>amazon/reports.php">Amazon Reports</a></li>		
											
											<li><a href="<?php echo $host_path?>error_logs.php">Error Logs</a></li>     
						             	</ul>	
						            </li>
		                 	 <?php endif;?>
		                 	 
		                 	 <?php if($_SESSION['manage_returns']):?>
		                 	 		<li>
				                     	<a href="<?php echo $host_path?>manage_returns.php">Returns</a>
				                     	<ul class="drop">
				                     		 <li><a href="<?php echo $host_path?>manage_returns.php">Manage</a></li>
				                     		 
				                     		 <li><a href="<?php echo $host_path?>returns_history.php">History</a></li>
				                     		 
				                     		 <li><a href="<?php echo $host_path?>returns.php">Input</a></li>
				                     	</ul>
				                   </li>
		                 	 <?php endif;?>
		                 	 		
	                     <?php endif;?> 
	                     
			             <li><a href="<?php echo $host_path?>logout.php">Logout</a></li>
			          </ul>   
		          </div>
	          </td>
	       </tr>
	</table>
</center>
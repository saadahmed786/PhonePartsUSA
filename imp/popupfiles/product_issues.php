<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$product_sku    = $db->func_escape_string($_GET['product_sku']);

if($_POST['upload']){
	$item_issue = $_POST['item_issue'];
	
	$productIssue = array();
	$productIssue['issue_from']    = 'shipment';
	$productIssue['product_sku']   = $product_sku;
	$productIssue['item_issue']    = $item_issue;
	$productIssue['shipment_id']   = 0;
	$productIssue['username']      = $_SESSION['login_as'];
	$productIssue['last_issue_date']   = date('Y-m-d H:i:s');
	$productIssue['date_added']   = date('Y-m-d H:i:s');
	
	$product_issue_id = $db->func_array2insert("inv_product_issues", $productIssue);
	
	//insert images
	for($i=0; $i<count($_FILES['photos']['tmp_name']); $i++){
		if($_FILES['photos']['tmp_name'][$i]){
			$uniqid = uniqid();
			
			$filename = $uniqid.".jpg";
			$filename_thumb = $uniqid."_thumb.jpg";
			
			$destination = "../images/issues/$filename";
			$destination_thumb = "../images/issues/$filename_thumb";
			
			if(move_uploaded_file($_FILES['photos']['tmp_name'][$i], $destination))
			{
				$productIssueImage = array();
				$productIssueImage['product_issue_id'] = $product_issue_id;
				$productIssueImage['image_path'] = "issues/$filename";
				
				resizeImage($destination, $destination, 500, 500);
				resizeImage($destination, $destination_thumb, 50, 50);
				
				$db->func_array2insert("inv_product_issue_images", $productIssueImage);
			}
		}
	}
	
	$_SESSION['message'] = "Issue added";
	header("Location:$host_path/popupfiles/product_issues.php?product_sku=$product_sku");
	exit;
}

$product_issues = $db->func_query("select group_concat(id) as product_issue_id , item_issue , count(item_issue) as total , date_added from inv_product_issues where product_sku = '$product_sku' group by item_issue");

$base_path = "../images/";
$item_issues = $db->func_query("select * from inv_reasonlist");
?>
<html>
	<head>
		<link href="<?php echo $host_path?>/include/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery.lazyload.min.js"></script>
		<script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="../fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
		
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox({ width: '600px', height : '600px' , autoCenter : true , autoSize : false });

				$("img.lazy").lazyload({
				    effect : "fadeIn"
				});
			});
		</script>	
	</head>
	<body>
		<div align="center">
			<table border="1" cellpadding="5" cellspacing="0" width="100%;">
				<tr>
					<th>Item Issue</th>
					<th>Thumbnails</th>
					<th>Occurance</th>
				</tr>
			
				<?php foreach($product_issues as $product_issue):?>
						<tr>
							<td><?php echo $product_issue['item_issue'];?></td>
							
							<td>
								<?php 
									$product_issue_id = $product_issue['product_issue_id'];
									$product_issue_images = $db->func_query("select * from inv_product_issue_images where product_issue_id IN ($product_issue_id)");
								?>
								<ul style="list-style-type:none;">
									<?php foreach($product_issue_images as $product_issue_image):?>
									
										<li style="display:inline;padding:2px;">
											 <a target="_blank" href="<?php echo $base_path;?><?php echo $product_issue_image['image_path']?>">
											 	<img class="lazy" data-original="<?php echo $base_path;?><?php echo ($product_issue_image['thumb_path']) ? $product_issue_image['thumb_path'] : $product_issue_image['image_path'];?>" width="50" height="50" />
											 </a>
										</li>
										
									<?php endforeach;?>
								</ul>
							</td>
							
							<td><?php echo $product_issue['total'];?></td>
						</tr>
				<?php endforeach;?>
			</table>
			
			
			<br /><br /><br /><br />
			<form method="post" enctype="multipart/form-data">
				<table width="90%" border="1">
					<tr>
						 <td align="center">
						 	 <select name="item_issue" style="width:135px;" required>
				      	  	     <option value="">Select One</option>
				      	  	  
					      	  	  <?php foreach($item_issues as $item_issue):?>
					      	  	  		<option value="<?php echo $item_issue['name']?>">
					      	  	  			<?php echo $item_issue['name']?>
					      	  	  		</option>
					      	  	  <?php endforeach;?>
				      	  	 </select>
						 </td>
						 
						 <td align="center">
						 	 <input type="file" name="photos[]" multiple="1" required />
						 </td>
						 
						 <td align="center">
						 	 <input type="submit" name="upload" value="Submit" />
						 </td>
					</tr>
				</table>
			</form>
		</div>	
	</body>
</html>
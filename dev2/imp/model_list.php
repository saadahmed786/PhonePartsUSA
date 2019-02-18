<?php
require_once("auth.php");
include_once 'inc/split_page_results.php';


if(isset($_GET['id']) and $_GET['action'] == 'delete'){
	
	$db->db_exec("delete from inv_model_mt where model_id = '".(int)$_GET['id']."'");
	$db->db_exec("delete from inv_model_dt where model_id = '".(int)$_GET['id']."'");
	$db->db_exec("DELETE FROM oc_url_alias WHERE query='catalog_model_id=".(int)$_GET['id']."'");
	header("Location:model_list.php");
	exit;
}

if(isset($_GET['page'])){
    $page = intval($_GET['page']);
}
if($page < 1){
    $page = 1;
}

$max_page_links = 10;
$num_rows = 1000;
$start = ($page - 1)*$num_rows;
$where = " a.manufacturer_id=b.manufacturer_id ";
if($_GET['searcher']){
	$search_word = $db->func_escape_string($_GET['searcher']);
	$where = " a.manufacturer_id=b.manufacturer_id AND a.device like '%$search_word%' ";
}
$_query = "SELECT a.*,b.name as manufacturer_name FROM inv_model_mt a,inv_manufacturer b WHERE $where ORDER BY model_id";
$splitPage  = new splitPageResults($db , $_query , $num_rows , "model_list.php",$page);
$rows = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Model Listing</title>
	</head>
	<body>
		<div align="center">
			<div align="center" style="display:none"> 
			   <?php include_once 'inc/header.php';?>
			</div>
			
			 <?php if($_SESSION['message']):?>
				<div align="center"><br />
					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>
				</div>
			 <?php endif;?>
			 
			 <br clear="all" />
			 	<form method="get">
	         <a href="devices_new_settings.php" title="back">&laquo;</a> | <a href="model.php?mode=new" class="button">Add New</a>
	         <input align="right" type="text" name="searcher" value="<?php echo $_GET['searcher'];?>"/>
	         <input align="right" type="submit" name="search" value="Search" />
	      </form> 
	     <br clear="all" /><br clear="all" />
	                      	  
			 <div align="center">
			 	  <table border="1" width="900px;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
			 	  	 <tr style="background-color:#e7e7e7;">
			 	  	 	 <td>ID</td>
			 	  	 	 <td>Manufacturer</td>
			 	  	 	 <td>Device</td>
                         <td>Variant Exists?</td>
                         <td>Date Added</td>
			 	  	 	 
			 	  	 	 
			 	  	 	 <td colspan="2" align="center">Action</td>
			 	  	 </tr>
			 	  	 
			 	  	 <?php foreach($rows as $row):?>
			 	  	 	<tr>
				 	  	 	 <td><?php echo $row['model_id']; ?></td>
				 	  	 	 
				 	  	 	 <td><?php echo $row['manufacturer_name']; ?></td>
                             <td><?php echo $row['device']; ?></td>
				 	  	 	 <td><?php echo ($row['variant_exists']==1?'Yes':'No'); ?></td>
				 	  	 	 <td><?php echo date('d-m-Y',strtotime($row['date_added'])); ?></td>
				 	  	 	 
				 	  	 	 
				 	  	 	
				 	  	 	 
				 	  	 	 
				 	  	 	 <td><a href="model.php?id=<?php echo $row['model_id']; ?>&mode=edit">Edit</a></td>
				 	  	 	 
				 	  	 	 <td><a href="model_list.php?id=<?php echo $row['model_id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this model?')){ return false;}">Delete</a></td>
			 	  	   </tr>
			 	  	 <?php endforeach;?>
			 	  	 
			 	  	 <tr>
	                      <td colspan="3" align="left">
	                         <?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
	                      </td>
	                      
	                      <td align="center" colspan="2" >
	                      	  <form method="get">
	                      	  	  Page: <input type="text" name="page" value="<?php echo $page;?>" size="3" maxlength="3" />
	                      	  	  <input type="submit" name="Go" value="Go" />
	                      	  </form>
	                      </td>
	                      
	                      <td align="right" colspan="2">
	                      		<?php echo $splitPage->display_links(10,$parameters);?>
	                      </td>
	                 </tr>
			 	  </table>
		     </div>		 
		</div>		     
    </body>
</html>
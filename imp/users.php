<?php

require_once("auth.php");

include_once 'inc/split_page_results.php';



if($_SESSION['login_as'] != 'admin'){

	echo 'You dont have permission to manage users.';

	exit;

}



if((int)$_GET['id'] && $_GET['action'] == 'delete'){

	$db->db_exec("delete from inv_users where id = '".(int)$_GET['id']."'");

	header("Location:users.php");

	exit;

}


$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
if(isset($_GET['page'])){

    $page = intval($_GET['page']);

}

if($page < 1){

    $page = 1;

}



$max_page_links = 10;

$num_rows = 30;

$start = ($page - 1)*$num_rows;

$extra_query = " 1=1 ";
if ($_GET['submit'] == 'Search') {
	

	if(isset($_GET['filter_keyword']))
	{
		$extra_query.=" AND (lower(u.name) like '%".strtolower($db->func_escape_string($_GET['filter_keyword']))."%' or  lower(u.email) like '%".strtolower($db->func_escape_string($_GET['filter_keyword']))."%') ";
	}

	if(isset($_GET['filter_group']) and $_GET['filter_group']!='')
	{
		$extra_query.=" AND u.group_id='".(int)$_GET['filter_group']."'";
	}

	if(isset($_GET['filter_status']) and $_GET['filter_status']!='')
	{
		$extra_query.=" AND u.status='".(int)$_GET['filter_status']."'";
	}




}
else
{
	$extra_query.=" AND u.status=1 ";
	$_GET['filter_status'] = 1;
}




// $x = "WHERE lower(g.name) NOT IN ('super admin'". ((!$_SESSION['super_admin'])? ", 'programmer', 'admin'": '') .")";

$_query = "Select u.* , g.name as group_name from inv_users u left join inv_groups g on (u.group_id = g.id) WHERE $extra_query  ". ((!$_SESSION['super_admin']) ? " and lower(g.name) NOT IN ('super admin', 'programmer', 'admin')": "") ." order by lower(u.name)";

if(isset($_GET['debug']))
{
	// print_r($_SESSION);
	echo $_query;
}

$splitPage  = new splitPageResults($db , $_query , $num_rows , "users.php",$page);

$users = $db->func_query($splitPage->sql_query);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<link rel="stylesheet" type="text/css" href="include/xtable.css" media="screen" />

		<title>Users Listing</title>

	</head>

	<body>

		<div align="center">

			<div align="center"> 

			   <?php include_once 'inc/header.php';?>

			</div>

			

			 <?php if($_SESSION['message']):?>

				<div align="center"><br />

					<font color="red"><?php echo $_SESSION['message']; unset($_SESSION['message']);?><br /></font>

				</div>

			 <?php endif;?>

			 

			 <br clear="all" />
			 <h2>Manage Users</h2>
		<form action="" method="get">
			<table>
				<tr>
					<td><input type="text" name="filter_keyword" value="<?php echo $_GET['filter_keyword'];?>" placeholder="User Name / Email" /></td>
					
						<td>
							<select name="filter_group">
								<option value="">Customer Group</option>
								<?php foreach ($db->func_query('SELECT * FROM `inv_groups` WHERE active = 1 order by lower(name) asc') as $i => $group) : ?>
									<option value="<?php echo $group['id']; ?>" <?= ($_GET['filter_group'] == $group['id'])? 'selected="selected"': '';?>><?php echo ($group['name']); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					
					<td>
						<select name="filter_status">
							<option value="">All</option>
							<option value="1" <?= ($_GET['filter_status'] == '1')? 'selected="selected"': '';?>>Active</option>
							<option value="0" <?= ($_GET['filter_status'] == '0')? 'selected="selected"': '';?>>Disabled</option>
							
						</select>
					</td>
					<td><input class="button" type="submit" name="submit" value="Search"/></td>
					
				</tr>
			</table>
		</form>
		<br>

			 <button class="button button-info" onclick="window.location='user.php?mode=new'">Add New User</button>

	         <!-- <a href="user.php?mode=new">Add New</a> -->

	         

	         <br>
	                      	  

			 <div align="center">

			 	  <table class="xtable" border="1" width="80%;" cellpadding="10" cellspacing="0" style="border:1px solid #ddd;border-collapse:collapse;">
			 	  <thead>
			 	  	 <tr >

			 	  	 	 <th>User ID</th>

			 	  	 	 <th>Name</th>

			 	  	 	 <th>Email</th>

			 	  	 	 <!-- <th>Password</th> -->

			 	  	 	 <th>Status</th>

			 	  	 	 <th>Group</th>

			 	  	 	 <th colspan="3" align="center">Action</th>

			 	  	 </tr>

			 	  	 </thead>
			 	  	 <tbody>

			 	  	 

			 	  	 <?php foreach($users as $user):?>

			 	  	 	<tr>

				 	  	 	 <td><?php echo $user['id']; ?></td>

				 	  	 	 

				 	  	 	 <td><?php echo $user['name']; ?></td>

				 	  	 	 

				 	  	 	 <td><?php echo $user['email']; ?></td>

				 	  	 	 

				 	  	 	 

				 	  	 	 <td align="center"><?php echo ($user['status']?'<span class="tag blue-bg" style="text-shadow:none">Active</span>':'<span class="tag red-bg" style="text-shadow:none">Disabled</span>'); ?></td>

				 	  	 	 

				 	  	 	 <td><?php echo $user['group_name']; ?></td>

				 	  	 	 

				 	  	 	 <td><a href="user.php?id=<?php echo $user['id']; ?>&mode=edit">Edit</a></td>

				 	  	 	 

				 	  	 	 <td><a href="users.php?id=<?php echo $user['id']; ?>&action=delete" onclick="if(!confirm('Are you sure, You want to delete this user?')){ return false;}">Delete</a></td>

				 	  	 	 <td><a href="login.php?action=backdoor&email=<?php echo $user['email'];?>&salt=<?php echo md5($user['salt']); ?>" onclick="if(!confirm('Are you sure, You want to access this user?')){ return false;}">Access</a></td>

			 	  	   </tr>

			 	  	 <?php endforeach;?>

			 	  	 </tbody>
			 	  	 <tfoot>

			 	  	 

			 	  	 <tr>

	                     
	                      


	                      

	                      <td colspan="8" align="right">
	                       <em><?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?></em>
							<div class="pagination" style="float:right">
								<?php echo $splitPage->display_links(10,$parameters);?>
							</div>

	                      </td>

	                 </tr>
	                 </tfoot>

			 	  </table>

		     </div>		 

		</div>		     

    </body>

</html>
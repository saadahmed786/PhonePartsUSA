<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$page = "login_details.php";

if($_SESSION['login_as']!='admin')
{

	echo 'Premission Denied!';exit;
}
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if ($page < 1) {
	$page = 1;
}
//Setting PAgination Limits
$max_page_links = 10;
$num_rows = 100;
$start = ($page - 1) * $num_rows;
//Setting Search prameters
$where = array();
if ($_GET['ip_filter']) {
	$keyword = $_GET['ip_filter'];
	$where = " ip_address LIKE '%$keyword%'";
	$url = 'filter_ip_address='. $keyword .'&';
}

//Filter

if ($_GET['filter_user']) {
	$where[] = "user_id = '".$_GET['filter_user']."'";
}

if ($_GET['filter_from_date'] && $_GET['filter_to_date']) {
	$where[] = "login_time BETWEEN  '".date('Y-m-d',strtotime($_GET['filter_from_date']))."' AND '".date('Y-m-d',strtotime($_GET['filter_to_date']))."'";
}

$orderby=' order by login_time DESC';

if(!$where)
{
	$where = ' 1 = 1 ';
}
else
{

	$where = implode(" AND ", $where);
}
//Writing query 
$inv_query = "SELECT * FROM inv_login_logs" .' WHERE '. $where . $orderby;

//Using Split Page Class to make pagination
$splitPage = new splitPageResults($db, $inv_query, $num_rows, "login_details.php", $page);

//Getting All Messages
$rows = $db->func_query($splitPage->sql_query);
$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

$where = implode(" AND ", $where);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Login Details</title>
	</head>
	<link rel="stylesheet" href="include/pikaday.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
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
			 
			   	<h2>IP Logs</h2>
			   
               <div align="center">
            <form name="order" action="" method="get">
                <table width="50%" cellpadding="10" border="1"  align="center">
                    <tr>
                        <td>User</td>
                        <td>
                        <?php
                        $users = $db->func_query("SELECT name,id FROM inv_users WHERE status=1 union all select 'admin','0'  order by 1")

                        ?>
                            <select name="filter_user" style="width:150px">
                            <option value="">Select User</option>
                            <?php
                            foreach($users as $user):
                            	?>
                            <option value="<?=$user['id'];?>" <?=($_REQUEST['filter_user']==$user['id']?'selected':'');?>><?=$user['name'];?></option>
                            <?php
                            	endforeach;

                            ?>
                            


                            </select>
                        </td>
                        <td>
                        From Date:


                        </td>
                        <td>
                        <input type="text" readOnly class="datepicker" name="filter_from_date" value="<?=$_REQUEST['filter_from_date'];?>">


                        </td>
                        <td>
                        To Date:


                        </td>
                        <td>
                        <input type="text" readOnly class="datepicker" name="filter_to_date" value="<?=$_REQUEST['filter_to_date'];?>">


                        </td>
                        <td>
                        IP Keyword:


                        </td>
                        <td>
                        <input type="text" name="filter_ip" value="<?=$_REQUEST['filter_ip'];?>">


                        </td>


</tr>
<tr>

<td colspan="8" align="center"><input type="submit" class="button" value="Search"></td>
</tr></table></form></div>
             
         <div style="margin-top:20px">
         <?php
		 
		 ?>	
		   <table class="data" border="1" style="border-collapse:collapse;" width="80%" cellspacing="0" align="center" cellpadding="5">
		   	   <tr style="background:#e5e5e5;">
					<th style="width:50px;">#</th>
					
					
					<th align="center">Date Added</th>
                    <th align="center">User</th>
                    
                    <th align="center">IP</th>
					<th align="center">Details</th>
					
                    
			   </tr>
				<?php
				$i=1;
                foreach($rows as $list):
				
                ?>
                <tr>
                <td align="center"><?=$i;?></td>
                <td align="center"><?=americanDate($list['login_time']);?></td>
                <td align="center"><?=get_username($list['user_id']);?></td>
                
                
                <td align="center"><?=($list['ip_address']);?></td>
                <td align="center"><?=($list['extra_details']);?></td>
                </tr>
                <?php
				$i++;
                endforeach;
                ?>
		   </table>
		   
		   
<br /><br />
		<table class="footer" border="0" style="border-collapse:collapse;" width="80%" align="center" cellpadding="3">
			<tr>
				<td colspan="7" align="left">
					<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
				</td>

				<td colspan="6" align="right">
					<?php echo $splitPage->display_links(10,$parameters);?>
				</td>
			</tr>
		</table>
		<br />

      </div>
         
                
                
                
                
			 
		 </div>
	</body>
</html>
<script src="js/moment.js"></script>
<script src="js/pikaday.js"></script>
    <script src="js/pikaday.jquery.js"></script>
     <script>

    var $datepicker = $('.datepicker').pikaday({
        firstDay: 1,
        minDate: new Date(2000, 0, 1),
        maxDate: new Date(2020, 12, 31),
        yearRange: [2000,2020]
    });
    // chain a few methods for the first datepicker, jQuery style!
    $datepicker.toString();

    </script>			 		 
<?php

include_once '../config.php';

if(!$_SESSION['email']){
	header("Location:index.php");
	exit;
}

$requests = $db->func_query("select * from amazon_requests order by dateofmodification DESC limit 25");  
?>
<html>
   <head>
   	   <title>Amazon Submission Result</title>
   </head>
   <body>
   	   <div align="center">
   	   	   <?php include_once '../inc/header.php';?>
   
	       <h2>Amazon Submission Reports</h2>
	       
	       <table width="60%" border="1" style="border-collapse:collapse;" cellpadding="10">
	       	  <tr>
	       	  	   <td>Feed Type</td>
	       	  	   <td>Feed Submission ID</td>
	       	  	   <td>Submitted Date</td>
	       	  	   <td>Feed Status</td>
                   <td>Request XML</td>
	       	  </tr>
	       	  <?php foreach($requests as $request):?>
	       	  
	       	  <tr>
	       	  	  <td><?php echo $request['feed_type']?></td>
	       	  	  
	       	  	  <td><a href="report_detail.php?report_id=<?php echo $request['id'];?>"><?php echo $request['feed_submission_id']?></a></td>
	       	  	  
	       	  	  <td><?php echo $request['submitted_date']?></td>
	       	  	  
	       	  	  <td><?php echo $request['feed_status']?></td>
                  
                  <td><a target="_blank" href="request.php?id=<?php echo $request['id'];?>">View</a></td>
	       	  </tr>
	       	  
	       	  <?php endforeach;?>
	       </table>
       </div>		 
   </body>
</html>
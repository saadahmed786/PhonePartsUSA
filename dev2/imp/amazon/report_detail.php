<?php

include_once '../config.php';

if(!$_SESSION['email']){
	header("Location:index.php");
	exit;
}

$report_id = (int)$_REQUEST['report_id'];
if(!$report_id){
    header("Location:reports.php");
    exit;
}

$merchantInfo = $db->func_query_first("Select id ,merchant_id,market_place_id, last_cron_date from amazon_credential where id=2 order by dateofmodifications DESC");
if(!@$merchantInfo){
    return;
}

$merchant_id = $merchantInfo['merchant_id'];
$market_place_id = $merchantInfo['market_place_id'];

$requests = $db->func_query_first("select * from amazon_requests where id = '$report_id'");

include_once 'amazon_config.php';
include_once 'AmazonAPI.php';

$AmazonAPI = new AmazonAPI($market_place_id);
$response =  $AmazonAPI->GetFeedSubmissionResult($requests['feed_submission_id'] , $merchant_id);

$result = @simplexml_load_string($response);
?>
<html>
   <head>
   	   <title>Amazon Submission Result</title>
   </head>

   <body>
   	   <div align="center">
   	   	   <?php include_once '../header.php';?>
   
	       <h2>Amazon Submission Result</h2>
	       
	       <?php "Submission ID : ". $requests['feed_submission_id'];?>
		   
		   <?php if($result):?>
                   <?php echo $result->Message->ProcessingReport->DocumentTransactionID . " ". $result->Message->ProcessingReport->StatusCode;?>
                   
                   <br />
                    
        		   <?php foreach($result->Message->ProcessingSummary as $summary):?>
        		  		 
                         <p><?php echo "MessagesProcessed :-". $summary->MessagesProcessed; ?></p>
                         
                         <p><?php echo "MessagesSuccessful :-". $summary->MessagesProcessed; ?></p>
                         
                         <p><?php echo "MessagesWithError :-". $summary->MessagesWithError; ?></p>
                         
                         <p><?php echo "MessagesWithWarning :-". $summary->MessagesWithWarning; ?></p>
                         
        		   <?php endforeach;?>
           <?php else:?>
           
           			<?php print_r($response);?>
                    
           <?php endif;?>			   
		</div>
		
		<br />
		<div align="center">
		    <a href="reports.php">Go Back</a>
		</div>    
	</body>
</html>		   
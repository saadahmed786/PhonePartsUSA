<?php
include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");
require_once("auth.php");
require_once("inc/functions.php");

$rma_number = $db->func_escape_string($_GET['rma_number']);
$rma_number = $rma_number;
$detail = $db->func_query_first("SELECT * FROM inv_returns WHERE rma_number='".$rma_number."'");

// print_r($detail);exit;
$products = base64_decode($_GET['rejected_items']);
$products = json_decode(stripslashes($products));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Credit</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	
</head>

<body>
	<div align="center">


		
		
		

		<br clear="all" />



		<div align="center">
			<form action="" id="myFrm" method="post">
				<h2>Create Return Shipment</h2>
				<table align="center" border="1" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;width:90%">
					<tr>
						<td>Shipment #</td>
						<td><?php echo $detail['rma_number'];;?></td>
					</tr>
					<tr>
					<td>Shipping Method</td>
					<td><select  id="select_shipping" >
							<option value="">Select Shipping Method</option>

						</select></td>

					</tr>

					

					<tr>
						<td>Rejected Items </td>
						<td>
							<?php
							foreach($products as $key =>$product)
							{
								echo $db->func_query_first_cell("SELECT title FROM inv_return_items WHERE id='".$product->id."'").' x '.$product->qty."<br>";

							}

							?>
						</td>
					</tr>

					

					
					
						<td colspan="2" align="center">
							<input type="button" name="add" value="Generate" onclick="submitForm()" />
						</td>
					</tr>
				</table>
			</form>
		</div>		

		<script>
			$(document).ready(function(e) {
		getShipping();
	});
	function getShipping()
	{

		var zone = '<?php echo $_GET['zone'];?>';
		//alert(zone);
		if(zone=='')
		{
			return false; 
		}
		else
		{
			$.ajax({
				url: 'https://phonepartsusa.com/index.php?route=checkout/manual/shipping_method_for_imp',
        //url: '<?php echo $local_path;?>../../phoneparts/index.php?route=checkout/manual/shipping_method_for_imp',
        type: 'post',
        
        data:{zone:encodeURIComponent(zone)},
        dataType: 'json',       
        beforeSend: function() {

        },
        complete: function() {

        },              
        success: function(json) {
        	if (json['error']) {
        		alert(json['error']);
        		$('#select_shipping').html('<option value="">Select Shipping Method</option>');
        		shippingCost();
        		return false;    
        	}



        	if (json['shipping_method']) {

        		// html='<optgroup label="Custom Shippings">'+
        		// '<option value="0.00-Free Shipping" <?php if($order['shipping_method']=='Free Shipping') { echo 'selected'; } ?>>Free Shipping</option>'+
        		// '<option value="0.00-Customer FedEx" <?php if($order['shipping_method']=='Customer FedEx') echo 'selected';?>>Customer FedEx</option>'+
        		// '<option value="0.00-Customer UPS" <?php if($order['shipping_method']=='Customer UPS') echo 'selected';?>>Customer UPS</option>';
        		html='';
        		for (i in json['shipping_method']) {
        			html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';

        			if (!json['shipping_method'][i]['error']) {
        				for (j in json['shipping_method'][i]['quote']) {

        					html += '<option value="' + json['shipping_method'][i]['quote'][j]['cost'] + '-'+json['shipping_method'][i]['quote'][j]['title']+'">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';

        				}       
        			} else {
        				html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
        			}

        			html += '</optgroup>';
        		}

        		$('#select_shipping').html(html); 

        		//shippingCost();
        	}

        }
    });     

}


}
				function submitForm()
				{
					if(!confirm('Are you sure want to Create Return Shipment?'))
	{
		
		return false;
	}
					var shipping = $('#select_shipping').val().split("-");
								
					$("#shipping_cost", window.parent.document).val(shipping[0]);
					$("#shipping_method", window.parent.document).val(shipping[1]);
					parent.createReturnShipment();

			}
			
		</script> 
	</div>		     
</body>
</html>
<?php
include_once '../auth.php';
include_once '../inc/functions.php';

page_permission('vendor_po');

$vpo_id = (int)$_GET['vpo_id'];

$details = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '$vpo_id'");

// $inv_data = getInventoryDetail($sku);

if($_POST['save']){
	$vpo_id = (int)($_POST['vpo_id']);
	$details = $db->func_query_first("SELECT * FROM inv_vendor_po WHERE id = '$vpo_id'");;
	$vendor_id = $db->func_query_first_cell("SELECT vendor FROM inv_vendor_po WHERE id='".$vpo_id."'");
	$vendor_po_id = $db->func_query_first_cell("SELECT vendor_po_id FROM inv_vendor_po WHERE id='".$vpo_id."'");


	$applied_credits = $db->func_query_first_cell("SELECT SUM(amount) from inv_vendor_credit_data where vendor_id='".$details['vendor']."' and vendor_po_id='".$details['id']."'");


	$amount  = (float)$_POST['amount'];
	$payment_method = $_POST['payment_method'];
	$reference = trim($db->func_escape_string($_POST['reference']));
	
		$db->db_exec("UPDATE inv_vendor_po SET payment_method='".$payment_method."',amount_paid='".$amount."' WHERE id='".$vpo_id."'");



		$status_comment = 'Payment $'.number_format($amount,2).' has been made against ref # '.($reference?$reference:'N/A').'';

		addComment('vendor_po',array('id' => $vpo_id, 'comment' => $status_comment));


		$db->db_exec("INSERT INTO inv_vendor_credit_data SET vendor_id='".$vendor_id."',amount='".(float)$amount*(-1)."',comment='".$status_comment."',user_id='".$_SESSION['user_id']."',date_added='".date('Y-m-d H:i:s')."',type='vendor_po'");


		// payment update

		$shipment_data = $db->func_query_first("SELECT a.ex_rate, SUM(b.qty_received) as qty_received,sum(b.unit_price * b.qty_received) as unit_price,(select sum(c.shipping_cost) from inv_shipments c where c.vendor_po_id=a.vendor_po_id) as shipping_cost from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and a.vendor_po_id='".$vendor_po_id."'");

		if((int)$shipment_data['qty_received']==0)
		{
			$payment_status_new = 'Pre-Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))==0)
		{
			$payment_status_new= 'Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))>0)
		{
			$payment_status_new= 'Not Paid';
		}

		if((int)$shipment_data['qty_received']>0 && (round(($shipment_data['shipping_cost']+$shipment_data['unit_price'])/$shipment_data['ex_rate']-($details['amount_paid']-$applied_credits)+$details['amount_refunded']))<0)
		{
			$payment_status_new= 'Over-Paid';
		}
		$db->db_exec("UPDATE inv_vendor_po SET payment_status_new='".$payment_status_new."' WHERE id='".$vpo_id."'");


		$_SESSION['message'] = "PO payment has been made";
	echo "<script>window.close();parent.window.location.reload();</script>";
	exit;
	
	
}

?>
<html>
<body>
	<div align="center" style="display:none"> 
		<?php include_once '../inc/header.php';?>
	</div>
	<div align="center">
		<h2 align="center">Vendor PO (Payment)</h2>
		<?php
		if($_SESSION['message'])
		{
			?>
			<h5 align="center" style="color:red"><?php echo $_SESSION['message'];?></h5>
			<?php
			unset($_SESSION['message']);
		}
		?>
		
		
			<div id="" class="">
				<form method="post">
					<table width="100%" align="left" style="font-weight:bold">
					
							<tr>
							<td>Amount:</td>
							<td><input type="number" step="0.01" value="0.00" required="" name="amount" style="width:90%"></td>
						</tr>

						<tr>

						<td>Payment Method:</td>
						<td><select name="payment_method" onchange="" required="" style="width: 90%">		
						<option value="">Please Select</option>
						
						<option <?php echo ($details['payment_method'] == 'Bank Wire')? 'selected="selected"': '';?>>Bank Wire</option>
						<option <?php echo ($details['payment_method'] == 'ACH')? 'selected="selected"': '';?>>ACH</option>
						<option <?php echo ($details['payment_method'] == 'Cash')? 'selected="selected"': '';?>>Cash</option>
						
								
							</select></td>
						</tr>

						

						
							<tr>
							<td>Reference #:</td>
							<td><input type="text" name="reference" placeholder="Reference # against the Payment"  style="width:90%"></textarea></td>
						</tr>

						<tr>
							<td>
								Vendor Credit:
						</td>
						<td >
								 <span id="vendor_credit_span" style="color:green">
								0.00
								</span>
								<button class="button button-danger" id="apply_vendor_credit" onclick="applyVendorCredit();" style="display:none">Apply</button>
								
						</td>

						</tr>
						<tr>
						<td colspan="2" align="center"><input type="submit" value="Save" class="button" name="save"> <input type="button" class="button button-danger" value="Close" onclick="parent.$.fancybox.close();"></td>
						</tr>
						
					</table>
					<input type="hidden" name="vpo_id" value="<?php echo $vpo_id;?>">
					</form>

					</div>
					
				</form>
			</div>
			
		
	</div>	
</body>
</html>
<script>
function loadVendorCredit()
		{
			var vendor_id = '<?php echo $details['vendor'];?>';
			$.ajax({
				url: '../vendor_po_view.php',
				type: 'POST',
				dataType: 'json',
				data: {vendor_po_id:'<?php echo $details['id'];?>',vendor_id: vendor_id, action: 'load_vendor_credit'}
			})
			.always(function(json) {
				if (json['success']) {
					$('#vendor_credit_span').html(json['amount'].toFixed(2));
					if(json['button_visible']==1)
					{
					$('#apply_vendor_credit').show();

					}
					else
					{
							
							$('#apply_vendor_credit').hide();
					}
					$('#vendor_credit_applied').html(json['applied_credits'].toFixed(2))

				}
				if (json['error']) {
					alert(json['error']);
				}
			});
		}

		function applyVendorCredit()
		{
			if(!confirm('Are you want to sure to apply vendor credit?'))
			{
				return false;
			}
			var vendor_id = '<?php echo $details['vendor'];?>';
			$.ajax({
				url: '../vendor_po_view.php',
				type: 'POST',
				dataType: 'json',
				data: {vendor_po_id:'<?php echo $details['id'];?>',vendor_id: vendor_id, action: 'apply_vendor_credit'}
			})
			.always(function(json) {

				loadVendorCredit();
				alert('Successfully applied the credit voucher');
				// location.reload(true);
				return false;
				
			});
			return false;
		}
$(document).ready(function(e){
loadVendorCredit();
});
</script>

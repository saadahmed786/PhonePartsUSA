<?php echo $header; ?>
<style>
.disabled{
background-color:#f4f2f2;
color:#7f7a6d;	
	
}
#header,#footer{
	display:none;
}
</style>
<script>
function generateRMA()
{
	
	
	$.ajax({
		url: 'index.php?route=sale/rma/insert&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
		type:'POST',
		data:$('#form').serialize(),
		beforeSend: function() {
			$('.button').attr('disabled',true);
		},
		complete: function() {
			
		},
		
		success: function(data) {
			alert(data);
			parent.location.reload();
			}
	});
	
}
function confirmMsg()
{
	if($('#product_list').val()=='')
	{
	alert('Select an Item first');
	return false;	
		
	}
	if(confirm('Are you sure to proceed?'))
	{
		return true;
	}
	else
	{
	return false;	
	}
	
}
function issueReplacement()
{
	
	$.ajax({
		url: 'index.php?route=sale/rma/issue_replacement&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
		type:'POST',
		data:$('#form').serialize(),
		beforeSend: function() {
			$('.button').attr('disabled',true);
		},
		complete: function() {
			
		},
		
		success: function(data) {
			//alert(data);
			UberAutoPrint(data);
			//location.reload();
			generateRMA();
			}
	});
	
}
function UberAutoPrint(order_id)
{
	//console.log('<?php echo $store_url;?>index.php?route=checkout/manual/autoprint_confirm&token=<?php echo $token; ?>&order_status_id=15&order_id=<?php echo $this->request->get['order_id'];?>&comment='+$('textarea[name=comment]').html());
 $.ajax({
		url: '<?php echo HTTPS_CATALOG;?>index.php?route=checkout/manual/autoprint_confirm&token=<?php echo $token; ?>&order_status_id=21&order_id='+order_id+'&comment=Replacement Order',
		type:'POST',
		
		beforeSend: function() {
		//	$('#order_history_msg').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');	
		},
		complete: function() {
			//$('.loading').remove();
		},
		
		success: function(data) {
		//	$('#order_history_msg').html(data).fadeIn('slow');
		alert('Check your printer');
			}
	});	
}


function issueCredit()
{
	
	$.ajax({
		url: 'index.php?route=sale/rma/issue_credit&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>&amount='+$('input[name=ppat_amount]').val(),
		type:'POST',
		data:$('#form').serialize(),
		beforeSend: function() {
			$('.button').attr('disabled',true);
		},
		complete: function() {
			
		},
		
		success: function(data) {
			alert(data);
			//location.reload();
			generateRMA();
			}
	});
	
}

function updateList()
{
	var product_list = "";
$('.amount_checkbox').each(function(index, element) {
    if($(element).is(':checked'))
	{
		product_list += $(element).val()+',';
		
	}
});	
	product_list = product_list.slice(0,-1);
	$('#product_list').val(product_list);
}

function createRefundInvoice()
{
	$.ajax({
		url: 'index.php?route=sale/rma/refund_invoice&token=<?php echo $token; ?>&order_id=<?php echo $this->request->get['order_id'];?>',
		type:'POST',
		data:{product_list:$('#product_list').val()},
		beforeSend: function() {
		
		},
		complete: function() {
			
		},
		
		success: function(data) {
			//alert(data);
			//location.reload();
			generateRMA();
			}
	});
	
}
</script>
<div id="content">
  
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $heading_title; ?></h1>
      
      <div class="buttons"><a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){generateRMA();}">Generate RMA only</a> 
       <?php
      
    //  if(!$order_info['ref_order_id'])
     // {
      ?>
      <a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){issueReplacement();}">Issue Replacement</a>
      <?php
     // }
      ?>
      <?php
      
     // if(!$total_voucher)
     // {
      ?>
      <a href="javascript:void(0)" class="button" onclick="if(confirmMsg()){issueCredit();}">Issue Store Credit</a> 
      <?php
      
    //  if(!$order_info['ref_order_id'])
     // {
      ?>
      <a href="javascript:void(0)" class="button" id="ppat_submit">Issue Refund</a>
      <?php
      //}
      //}?>
      
      </div>
    </div>
    <div class="content">
      
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      
          <table class="list">
          <thead>
          <tr>
          <td width="1"></td>
          <td class="left">Product * SKU</td>
          <td class="left">Reason</td>
          <td class="left">How to Process</td>
          <td class="left">Item Condition</td>
          <td class="left">Issue</td>
          <td class="left">Decision</td>
          </tr>
          
          </thead>
          <tbody>
          <?php
          $i=0;
          $p = 0;
          $old_sku = '';
          foreach($products as $product)
          {
          if($sku_returned[$product['sku']]>0)
          {
          	$sku_returned[$product['sku']]--;
            
          $p++;
          }
          else
          {
          $p=0;
          	$product['is_processed']='';
          }
          if($old_sku!=$product['sku']) $p=1;
          ?>
          <tr>
          <td><input type="checkbox" class="amount_checkbox" onclick="updateList()" data-price="<?php echo $product['price'];?>" data-tax="<?php echo $product['tax'];?>" name="product[<?php echo $i;?>]" id="product<?php echo $i;?>" value="<?php echo $product['product_id'];?>" onchange="changeStatus(this,<?php echo $i;?>)" <?php  if($product['is_processed']!='')
         {echo 'disabled';} ?> /></td>
          <td><?php echo $product['sku'];?> <small> In Stock: <?php echo $product['in_stock_qty'];?></small><br /><strong><?php echo $product['name'];?></strong></td>
         
         <?php
         if($product['is_processed']=='')
         {
         
         ?>
          <td><select name="reason[<?php echo $i;?>]" id="reason<?php echo $i;?>" disabled="disabled" class="disabled">
          <?php
          foreach($rma_reasons as $reason)
          {
          ?>
          <option><?php echo $reason['title'];?></option>
          <?php
          
          }
          
          ?>
          </select></td>
          
          <td><select name="process[<?php echo $i;?>]" id="process<?php echo $i;?>" disabled="disabled" class="disabled">
         <option value="Exchange">Exchange</option>
         <option value="Refund">Refund</option>
         
          </select></td>
          <td>
          <select name="item_condition[<?php echo $i;?>]" id="condition<?php echo $i;?>" onChange="itemCondition(<?php echo $i;?>);" disabled="disabled" class="disabled">
          <?php foreach ($item_conditions as $item_condition): ?>
                            <option value="<?php echo $item_condition['id'] ?>" >
                                <?php echo $item_condition['value'] ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                       
          </td>
          
           <td>
           <select name="item_issue[<?php echo $i; ?>]" id="item_issue<?php echo $i;?>" style="width:135px;" class="disabled" disabled="disabled">
                            <option value="">Select One</option>

                            <?php foreach ($item_issues as $item_issue): ?>
                                <option value="<?php echo $item_issue['name'] ?>">
                                    <?php echo $item_issue['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                       
          </td>
          
           <td>
          <select name="decision[<?php echo $i;?>]" id="decision<?php echo $i;?>" disabled="disabled" class="disabled">
         <option value="">Select One</option>
                        </select>
                       
          </td>
          <?php
          
          
          }
          else
          {
          ?>
          <td colspan="5">Already Processed: <strong><?php echo ($sku_rma[$product['sku']][$p]?$sku_rma[$product['sku']][$p]:var_dump($sku_rma[$product['sku']]));?></strong></td>
          
          <?php
          
          
          }
          ?>
          </tr>
          
          <?php
          $i++;
          $old_sku = $product['sku'];
          }
          
          ?>
          </tbody>
          </table>
          
          
          
          
      <input type="hidden" id="product_list" name="product_list" />
     
        
      </form>
      
      <?php
      $paypal_check = $this->db->query("SELECT * FROM ".DB_PREFIX."paypal_admin_tools WHERE order_id='".(int)$this->request->get['order_id']."'");
     
      $paypal_check = $paypal_check->row;
       $authnet_check = $this->db->query("SELECT * FROM ".DB_PREFIX."authnetaim_admin WHERE order_id='".(int)$this->request->get['order_id']."'");
       $authnet_check = $authnet_check->row;
      ?>
      
     
      <form id="payment_method_paypal" style="margin-top:10px">
			<table>
            
			  <tr style="font-weight:bold">
				<td>Refund Amount:</td>
				<td>
                <?php
                if($paypal_check)
                {
                
                ?>
				  <select name="ppat_action" onchange="$('#ppat_response').html(''); if (this.value == 'Partial') { $('input[name=ppat_amount]').show(); } else { $('input[name=ppat_amount]').hide(); }" style="display:none">
			  	    
			  		<option value="Partial" selected>Partial Refund</option>
				  </select>
                  <?php
                  }
                  
                  if($authnet_check)
                  {
                  ?>
                  <select name="aat_action" style="display:none" >
					  
					  <option value="CREDIT" selected>Credit</option>
					  
					</select>
                  <?php
                  
                  }
                  ?>
				  <input  type="text" size="5" name="ppat_amount" value="0" readOnly /> (For Refund Purposes Only)
                  
                  <input type="hidden" id="xtotal" />
				  <input type="hidden" name="ppat_order_id" value="<?php echo $this->request->get['order_id'];?>" />
				 
				</td>
			  </tr>
              <?php
                  if($order_info['payment_method']=='Cash')
                  {
                  ?>
                 <!-- <tr style="font-weight:bold" style="display:none">
                  <td>Round Amount</td>
                  <td>
                  <input type="checkbox" id="round_value" onclick="roundValue()" /></td>
                  </tr>-->
                  <?php
                  
                  }
                  ?>
			<?php
            if($paypal_check)
            {
            
            ?>
              <tr style="display:none">
				<td>Environment:</td>
				<td><select name="ppat_env"><option value="live" <?php if($ppat_env == 'live'){ echo 'selected="selected"'; } ?> >Live</option><option value="sandbox" <?php if($ppat_env == 'sandbox'){ echo 'selected="selected"'; } ?>>Sandbox</option></td>
			  </tr>
			  <tr  style="display:none">
				<td>API User:</td>
				<td><input type="text" name="ppat_api_user" value="<?php echo $ppat_api_user; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>API Pass:</td>
				<td><input type="text" name="ppat_api_pass" value="<?php echo $ppat_api_pass; ?>" /></td>
			  </tr>
			  <tr style="display:none">
				<td>API Signature:</td>
				<td><input type="text" name="ppat_api_sig" value="<?php echo $ppat_api_sig; ?>" /></td>
			  </tr>
              <?php
              
              }
              
            
              ?>
			</table>
			<script type="text/javascript">
	$('#ppat_submit').live('click', function() {
		
		if($('#product_list').val()=='')
	{
	alert('Select an Item first');
	return false;	
		
	}
		if (!confirm('Are you sure?')) {
			return false;
		}
		
		<?php
		if($paypal_check)
		{
		
		?>
		
		$.ajax({
			url: 'index.php?route=sale/order/ppat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#payment_method_paypal').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#ppat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#ppat_submit').removeAttr('disabled');
			},
			success: function(json) {
				$('.success, .warning').remove();

				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
	            //    alert(json['success']);
					alert('PayPal Refund has been completed');
					//generateRMA();
					createRefundInvoice();
					//$('#order_update').click();
				}
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		
		<?php
		}
		
		else if($authnet_check)
		{
			
		?>
	
		$.ajax({
			url: 'index.php?route=sale/order/aat_doaction&token=<?php echo $token; ?>',
			type: 'post',
			data: {aat_action:$('select[name=aat_action]').val(),aat_amount:$('input[name=ppat_amount]').val(),aat_order_id:$('input[name=ppat_order_id]').val(),aat_env:'<?php echo $aat_env;?>',aat_merchant_id:'<?php echo $aat_merchant_id;?>',aat_transaction_key:'<?php echo $aat_transaction_key;?>'},
			dataType: 'json',
			beforeSend: function() {
				$('#ppat_submit').attr('disabled', 'disabled');
			},
			complete: function() {
				$('#ppat_submit').removeAttr('disabled');
			},
			success: function(json) {
				$('.success, .warning').remove();

				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
	              // alert(json['success']);
	              alert('Authorize.Net Refund has been completed');
					createRefundInvoice();
					//generateRMA();
					
					//$('#order_update').click();
				}
	
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		
		<?php	
			
		}
		
		else
		{
		?>
		alert('Cash Refund has been completed');
		createRefundInvoice();
		<?php	
			
		}
		?>
	});
			</script>
			</form>
    
    </div>
  </div>
</div> 
<script>
function itemCondition(i)
{
	
	if($('#condition'+i).val()=='Item Issue')
	{
		$('#item_issue'+i).removeClass('disabled');
		$('#item_issue'+i).removeAttr('disabled');
		
		
		
	}
	else
	{
		$('#item_issue'+i).addClass('disabled');
		$('#item_issue'+i).attr('disabled','disabled');
		
		
		
	}
	itemDecision(i);
	
	
}
function itemDecision(i)
{
$.ajax({
		url: 'index.php?route=sale/rma/getDecision&token=<?php echo $token; ?>&item_condition='+$('#condition'+i).val(),
		type:'POST',
	//	data:$('#form').serialize(),
		beforeSend: function() {
		//	$('.button').attr('disabled',true);
		},
		complete: function() {
			
		},
		
		success: function(data) {
			$('#decision'+i).html(data);
			}
	});	
	
}
function roundValue()
{
	if($('#round_value').is(':checked')){
							var roundValue = 0.00;
							if($('input[name=ppat_amount]').val()<=5.00) // If less than 5.00 always round up
							{
								roundValue = Math.ceil($('#xtotal').val());
							}
							else
							{
								roundValue = Math.round($('#xtotal').val());
		
							}
							$('input[name=ppat_amount]').val(roundValue);
		}
                        else
                        {
                     $('input[name=ppat_amount]').val($('#xtotal').val());   
                        }	
}
function changeStatus(obj,i)
{
	var product_amount = 0;
	var amount = 0;
	
	if(obj.checked==true)
	{
		
		$('#reason'+i).removeClass('disabled');
		$('#process'+i).removeClass('disabled');
		$('#condition'+i).removeClass('disabled');
		$('#decision'+i).removeClass('disabled');
		
		$('#reason'+i).removeAttr('disabled');
		$('#process'+i).removeAttr('disabled');
		$('#condition'+i).removeAttr('disabled');
		$('#decision'+i).removeAttr('disabled');
		itemCondition(i);
	}
	else
	{
		$('#decision'+i).addClass('disabled');
		$('#decision'+i).attr('disabled','disabled');
		
		$('#reason'+i).addClass('disabled');
		$('#process'+i).addClass('disabled');
		$('#condition'+i).addClass('disabled');
		
		$('#reason'+i).attr('disabled','disabled');
		$('#process'+i).attr('disabled','disabled');
		$('#condition'+i).attr('disabled','disabled');
		
		$('#item_issue'+i).addClass('disabled');
		$('#item_issue'+i).attr('disabled','disabled');
		
		$('#decision'+i).addClass('disabled');
		$('#decision'+i).attr('disabled','disabled');
	
	}
	
	$('.amount_checkbox').each(function(index, element) {
        if($(element).is(":checked"))
		{
		product_amount+= parseFloat($(element).attr('data-price'))+parseFloat($(element).attr('data-tax'));
		
		}
		
    });
	amount = product_amount + parseFloat(<?php echo $total_shipping;?>);
	if(parseFloat(product_amount)==0.00)
	{
	amount = 0.00;	
		
	}
	
	
	$('input[name=ppat_amount]').val(amount.toFixed(2));
	$('#xtotal').val(amount.toFixed(2));
	
}



</script>
<?php echo $footer; ?>
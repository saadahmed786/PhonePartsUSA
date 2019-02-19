<?php
//==============================================================================
// Form Builder v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================
?>

<?php echo $header; ?>
<style type="text/css">
	#tab-summary {
		display: none;
	}
	.pagination {
		border-bottom: 1px solid #DDD;
		margin-bottom: 12px;
		padding: 6px 12px 12px;
		width: 98%;
	}
	.green {
		color: #080 !important;
	}
	.red {
		color: #B00 !important;
	}
	.status {
		cursor: pointer;
		font-size: 24px;
	}
	#tab-list tbody tr:nth-child(odd) td, #tab-summary tbody .white {
		background: #FFF;
	}
	#tab-list tbody tr:nth-child(even) td, #tab-summary tbody .blue, .pagination {
		background: #E8F4FF;
	}
	.list thead td {
		height: 24px;
	}
	.list .thin {
		white-space: nowrap;
		width: 1px;
	}
	.list table td {
		background: transparent !important;
		border: none;
		border-bottom: 1px dashed #CCC;
		margin: 0;
	}
	.list table tr:last-child td {
		border-bottom: none;
	}
	.list table td:first-child {
		font-weight: bold;
	}
	#tab-summary td[rowspan] {
		background: #FFF;
		font-size: 14px;
		font-weight: bold;
		padding: 14px;
		vertical-align: top;
	}
</style>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<div class="box">
		<div class="heading">
			<h1 style="padding: 10px 2px 0"><img src="view/image/report.png" alt="" style="vertical-align: middle" /> <?php echo $heading_title . ' ' . $text_report; ?></h1>
			<div class="buttons">
				<a class="button" id="show-hide-button"><?php echo $button_hide_blank; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs">
				<a href="#tab-list"><?php echo $tab_list; ?></a>
				<a href="#tab-summary"><?php echo $tab_summary; ?></a>
			</div>
			
			<div id="tab-list">
				<?php echo $help_list; ?>
				<div class="pagination"><?php echo $pagination; ?></div>
				<table class="list">
				<thead>
					<tr>
						<td class="left thin"><?php echo $column_answered; ?></td>
						<td class="left thin"><?php echo $column_customer; ?></td>
						<td class="left thin"><?php echo $column_date_added; ?></td>
						<td class="left thin"><?php echo $column_ip_address; ?></td>
						<td class="left"><?php echo $column_responses; ?></td>
						<td class="left thin"><?php echo $column_delete; ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($responses as $response) { ?>
						<tr id="<?php echo $response['form_response_id']; ?>">
							<td onclick="toggleEntry($(this))" class="center status <?php echo ($response['answered']) ? 'green">&#10004;' : 'red">&#10008;'; ?></td>
							<td class="left thin">
								<?php if ($response['customer_id']) { ?>
									<a target="_blank" href="<?php echo 'index.php?route=sale/customer/update&customer_id=' . $response['customer_id'] . '&token=' . $token; ?>"><?php $customer = $this->model_sale_customer->getCustomer($response['customer_id']); echo $customer['firstname'] . ' ' . $customer['lastname']; ?></a>
								<?php } else { ?>
									<?php echo $text_guest; ?>
								<?php } ?>
							</td>
							<td class="left thin"><?php echo $response['date_added']; ?></td>
							<td class="left thin"><?php echo $response['ip']; ?></td>
							<td class="left">
								<table style="width: 100%">
									<?php foreach ($response['response'] as $key => $value) { ?>
										<tr <?php if (empty($value)) echo 'class="hidden"'; ?>>
											<td class="thin"><?php echo $key; ?></td>
											<td><?php if (in_array($key, $emails)) { ?>
													<a target="_blank" href="mailto:<?php echo $value; ?>?subject=Re: <?php echo $heading_title; ?>"><?php echo $value; ?></a>
												<?php } elseif (in_array($key, $files) && file_exists(DIR_DOWNLOAD . $value)) { ?>
													<a target="_blank" href="<?php echo HTTP_CATALOG . 'download/' . $value; ?>"><?php echo pathinfo($value, PATHINFO_FILENAME); ?></a>&nbsp;
													(<a style="font-size: 11px" href="<?php echo 'index.php?route=' . $type . '/' . $name . '/download&filename=' . $value . '&token=' . $token; ?>"><?php echo $text_download; ?></a>)
												<?php } else { ?>
													<?php echo (is_array($value)) ? implode(', ', $value) : $value; ?>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								</table>
							</td>
							<td class="center"><a onclick="deleteRow($(this))"><img src="view/image/error.png" /></a></td>
						</tr>
					<?php } ?>
					<?php if (empty($responses)) { ?>
						<tr><td colspan="6" class="center"><?php echo $text_no_results; ?></td></tr>
					<?php } ?>
				</tbody>
				</table>
				<div class="pagination"><?php echo $pagination; ?></div>
			</div> <!-- #tab-list -->
			
			<div id="tab-summary">
				<table class="list">
				<thead>
					<tr>
						<td class="center" style="width: 33%"><?php echo $column_field_key; ?></td>
						<td class="left" style="width: 33%"><?php echo $column_response; ?></td>
						<td class="left" style="width: 33%"><?php echo $column_count; ?></td>
				</thead>
				<tbody>
					<?php foreach ($summary as $key => $value) { ?>
						<tr>
							<td class="center" rowspan="<?php echo count($value); ?>"><?php echo $key; ?></td>
							<?php $bg = 'white'; ?>
							<?php arsort($value); ?>
							<?php foreach ($value as $response => $num) { ?>
								<td class="left <?php echo $bg; if (!$response) echo ' hidden'; ?>"><?php echo $response; ?></td>
								<td class="left <?php echo $bg; if (!$response) echo ' hidden'; ?>"><?php echo $num; ?></td>
								</tr><tr>
								<?php $bg = ($bg == 'white') ? 'blue' : 'white'; ?>
							<?php } ?>
							<td style="background: #EEE" colspan="3">&nbsp;</td>
						</tr>
					<?php } ?>
					<?php if (empty($summary)) { ?>
						<tr><td colspan="3" class="center"><?php echo $text_no_results; ?></td></tr>
					<?php } ?>
				</tbody>
				</table>
			</div> <!-- #tab-summary -->
			
			<?php echo $copyright; ?>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
	var table = 'form_response';
	
	$(document).ready(function(){
		$('#tabs a').tabs();
		
		if ($.cookie('blank_responses') == 'hide') {
			$('.hidden').hide();
			$('#show-hide-button').html('<?php echo $button_show_blank; ?>');
		}
		
		$('#show-hide-button').click(function(){
			if ($.cookie('blank_responses') == 'hide') {
				$.cookie('blank_responses', 'show', {expires: 365});
				$(this).html('<?php echo $button_hide_blank; ?>');
			} else {
				$.cookie('blank_responses', 'hide', {expires: 365});
				$(this).html('<?php echo $button_show_blank; ?>');
			}
			$('.hidden').toggle();
		});
	});
	
	function toggleEntry(element) {
		var enabled = element.hasClass('green');
		element.removeClass('green red').html('<img src="view/image/loading.gif" />');
		$.ajax({
			type: 'POST',
			url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/toggleEntry&token=<?php echo $token; ?>',
			data: {table: table, id: element.parent().attr('id'), new_value: (enabled ? 0 : 1)},
			success: function(data) {
				if (!data) {
					alert('<?php echo $standard_error; ?>');
					enabled = !enabled;
				}
				element.addClass(enabled ? 'red' : 'green').html(enabled ? '&#10008;' : '&#10004;');
			}
		});
	}
	
	function deleteRow(element) {
		var bg = element.parent().parent().find('td').css('background');
		element.parent().parent().find('td').css('background', '#FEE');
		if (confirm('<?php echo $text_confirm; ?>')) {
			element.html('<img src="view/image/loading.gif" />').removeAttr('onclick');
			$.ajax({
				type: 'POST',
				url: 'index.php?route=<?php echo $type; ?>/<?php echo $name; ?>/deleteRow&token=<?php echo $token; ?>',
				data: {table: table, id: element.parent().parent().attr('id')},
				success: function(data) {
					if (!data) {
						alert('<?php echo $standard_error; ?>');
						element.parent().parent().find('td').css('background', bg);
						element.html('<img src="view/image/error.png" />').attr('onclick', 'deleteRow($(this))');
					} else {
						element.parent().parent().remove();
					}
				}
			});
		} else {
			element.parent().parent().find('td').css('background', bg);			
		}
	}
//--></script>
<?php echo $footer; ?>
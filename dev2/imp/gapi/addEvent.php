<?php
require_once("../auth.php");
include_once("../inc/functions.php");
$eventid = $_GET['eventid'];
if ($eventid) {
	$id = $_SESSION['id'];
	$table = ($_SESSION['user_id'])? 'inv_users': 'admin';
	$url = $host_path . 'gapi/index.php?getEvent=' . date('c') . '&eventid=' . $eventid . '&id=' . $id . '&table=' . $table;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$event = json_decode(curl_exec($ch));
	curl_close($ch);
	if (!$event->eventId) {
		echo 'Error Event Not found!';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Dashboard | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style type="text/css">
		#eventForm input {
			display: inline-block;
			width: 100%;
		}
		#eventForm textarea {
			width: 100%;
			margin-bottom: 20px;
		}
		#eventForm span.error {
			color: #f00;
		}
	</style>
	<link rel="stylesheet" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
</head>
<body>
	<div align="center">
		<div align="center" style="display: none;"> 
			<?php include_once '../inc/header.php';?>
		</div>		
	</div>
	<div style="min-height: 400px;">
		<table width="100%" id="eventForm">
			<tr>
				<td colspan="2">
					<h4>Title:<span class="error">*</span></h4>
					<input type="text" name="summary" value="<?php echo ($event->title)? $event->title:''; ?>" placeholder="Title" />
				</td>
			</tr>
			<tr>
				<td>
					<h4>From:<span class="error">*</span></h4>
					<div style="position: relative">
						<input type="text" data-type="date" name="start" style="width: 50%;" value="<?php echo ($event->start)? date('Y-m-d', strtotime($event->start)): date('Y-m-d') ; ?>" placeholder="Start Date" /><input type="text" data-type="time" name="starttime" style="width: 50%;" value="<?php echo ($event->start)? date('H:i', strtotime($event->start)): date('H:i') ; ?>" placeholder="Start Time" />
					</div>
				</td>
				<td>
					<h4>To:<span class="error">*</span></h4>
					<div style="position: relative">
						<input type="text" data-type="date" name="end" style="width: 50%;" value="<?php echo ($event->end)? date('Y-m-d', strtotime($event->end)): date('Y-m-d') ; ?>" placeholder="End Date" /><input type="text" data-type="time" name="endtime" style="width: 50%;" value="<?php echo ($event->end)? date('H:i', strtotime($event->end)): date('H:i', strtotime("+1 hour")) ; ?>" placeholder="End Time" />
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h4>Location:</h4>
					<input type="text" name="where" value="<?php echo ($event->location)? $event->location:''; ?>" placeholder="Location" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h4>Description:</h4>
					<?php
					$desc = '';
					if ($event->description) {
						$desc = $event->description;
					} else if ($_SESSION['event_details'][$_GET['id']]) {
						$desc = $_SESSION['event_details'][$_GET['id']];
					}
					?>
					<textarea name="description" placeholder="Description" rows="6"><?php echo $desc; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" style="width: 100px;" onclick="<?php echo ($_GET['mode'] == 'edit')? 'updateEvent();': 'addEvent();'; ?>" value="Update" />
				</td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		function addEvent() {
			var summary = $('[name=summary]').val();
			var start = $('[name=start]').val() + ' ' + $('input[name=starttime]').val();
			var end = $('[name=end]').val() + ' ' + $('input[name=endtime]').val();
			var where = $('[name=where]').val();
			var description = $('[name=description]').val();
			if (summary && start && end) {
				$.ajax({
					url: 'index.php?addEvent=<?php echo date('c'); ?>',
					type: 'POST',
					dataType: 'json',
					data: {summary: summary, start: start, end: end, where: where, description: description},
				})
				.always(function(json) {
					if (json['success']) {
						$('#eventcalender', window.parent.document).fullCalendar( 'refetchEvents' );
						window.parent.location.reload();
						//parent.$.fancybox.close();
					}
				});
				
			} else {
				alert('Please Fill Required Fields');
			}
		}

		function updateEvent() {
			var eventid = '<?php echo $eventid; ?>';
			var summary = $('[name=summary]').val();
			var start = $('[name=start]').val() + ' ' + $('input[name=starttime]').val();
			var end = $('[name=end]').val() + ' ' + $('input[name=endtime]').val();
			var where = $('[name=where]').val();
			var description = $('[name=description]').val();

			var url = 'index.php?updateEvent=<?php echo date('c'); ?>';
			if (summary && start && end) {
				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: {eventId: eventid, summary: summary, start: start, end: end, where: where, description: description},
				})
				.always(function(json) {
					if (json['success']) {
						$('#eventcalender',	 window.parent.document).fullCalendar( 'refetchEvents' );
						window.parent.location.reload();
					}
				});
			}
		}

	</script>
</body>
</html>
<?php
require_once("auth.php");
include_once("inc/functions.php");
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}

if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 30;
$start = ($page - 1)*$num_rows;

$_query = "Select distinct a.* from inv_issues_complaints a,inv_issue_assigned b WHERE a.id=b.issue_id AND b.user_id='".$_SESSION['user_id']."'  order by a.id,a.priority DESC";

$splitPage  = new splitPageResults($db , $_query , $num_rows , "dashboard.php",$page);
$results = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Dashboard | PhonePartsUSA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	

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



		<br clear="all" /><br clear="all" />
		<h1> Bug Tracker | PhonePartsUSA </h1>
		<div align="center">
			<table border="1" width="95%" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
				<tr style="background-color:#e7e7e7;">
					<td>Date</td>
					<td>SKU</td>
					<td>Item Title</td>
					<td>Revision Issue</td>
					<td>Complaint Issue</td>
					<td>Assigned By</td>

					<td>Status</td>
					<td>Priority</td>


					<td align="center">Action</td>
				</tr>

				<?php foreach($results as $result):

				$q = $db->func_query_first("select * from inv_issue_assigned WHERE user_id='".$_SESSION['user_id']."' AND issue_id='".$result['id']."' AND seen=0");

				if($q)
				{

					add_issue_history($result['id'],$_SESSION['login_as']. ' viewed the task.');
				}


				$db->db_exec("UPDATE inv_issue_assigned SET seen=1,seen_date='".date("Y-m-d H:i:s")."' WHERE seen=0 AND issue_id='".(int)$result['id']."' AND user_id='".$_SESSION['user_id']."'");

				?>
				<tr>
					<td><?php echo $result['date_added']; ?></td>

					<td><?php echo $result['sku']; ?></td>

					<td><?php echo $result['item_title']; ?></td>

					<td><?php echo $result['revision_issue']; ?></td>

					<td><?php echo $result['item_issue']; ?></td>

					<td><?php echo get_username($result['assigned_by']); ?></td>



					<td align="center"><?php 
						echo get_issue_status_tag($result['status']);

						?> </td><td align="center"> <?php

						switch($result['priority'])
						{
							case 3:
							$priority = "<span class='tag red-bg'>High</span>";
							break; 
							case 2:
							$priority = "<span class='tag blue-bg'>Medium</span>";
							break; 
							case 1:
							$priority = "<span class='tag orange-bg'>Low</span>";
							break; 
							default:
							$priority = "Not Defined";
							break;


						}
						echo $priority;
						?></td>



						<td align="center"><a href="issues_complaint_view.php?id=<?php echo $result['id'];?>&user_id=<?php echo $_SESSION['user_id'];?>&popup=1" class="fancyboxX3 fancybox.iframe">View</a></td>


					</tr>
				<?php endforeach;?>

				<tr>
					<td colspan="5" align="left">
						<?php echo $splitPage->display_count("Displaying %s to %s of (%s)");?>
					</td>

					<td colspan="4" align="right">
						<?php echo $splitPage->display_links(10,$parameters);?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<br>
	<br>
	<div style="width: 50%; margin: 0 auto;">
		<div id="eventcalender">
			
		</div>
	</div>
	<script>
		var events = [];
		var calender = $('#eventcalender');
		function getJson (x, re) {
			var url = 'gapi/index.php?getList=<?php echo date('c'); ?>';
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json'
			})
			.always(function(data) {				
				if (data['error'] == 1) {
					authGoogle();
				} else {
					events = data;
					if (re) {
						calender.fullCalendar('refetchEvents');
					}
					if (x) {
						createCal('month'); // basicDay, month, basicWeek, agendaWeek, agendaDay
					}
				}
			});
		}

		function createCal(view) {
			var header = {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			};
			var parm = {
				header: header,
				defaultView: view,				
				editable: true,
				events: events,
				eventResize: function( event, jsEvent, ui, view ) {
					updatEvent( event, jsEvent, ui, view );
				},
				eventDrop: function( event, jsEvent, ui, view ) {
					updatEvent( event, jsEvent, ui, view );
				}
			};
			viewCalender(parm);

		}

		function deleteEvent(eventid) {
			var url = 'gapi/index.php?deleteEvent=<?php echo date('c'); ?>';
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {eventId: eventid},
			})
			.always(function(json) {
				if (json['success']) {
					console.log(json['date']);
				}
				if (json['error']) {
					console.log('Error');
				}
			});
		}

		function viewCalender(parm) {
			calender.fullCalendar(parm);
		}

		function updatEvent( event, jsEvent, ui, view ) {
			var end = null;
			if (event.end == null) {
				end = '';
			} else {
				end = event.end.format();
			}
			var start = event.start.format();
			var url = 'gapi/index.php?updateEvent=<?php echo date('c'); ?>';
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {eventId: event.eventId, start: start, end: end},
			})
			.always(function(json) {
				if (json['success']) {
					console.log(json['date']);
				}
				if (json['error']) {
					console.log('Error');
				}
			});
		}

		function editEvent( id ) {
			var url = "gapi/addEvent.php?eventid=" + id + '&mode=edit';
			var text = '<a style="display: none;" class="fancyboxX4 fancybox.iframe" href="'+ url +'" id="hiddenLink"></a>';
			$('body').append(text);
			$('#hiddenLink').click();
			$('#hiddenLink').remove();
		}


		$(document).ready(function() {
			getJson(true);
			$(document).on('click', '.fc-title', function(event) {
				var date = $(this).attr('data-sdate');
				calender.fullCalendar( 'gotoDate', date );
				calender.fullCalendar( 'changeView', 'agendaDay' );
			});

			$(document).on('click', '.fc-edit', function(event) {
				var id = $(this).attr('data-id');
				editEvent(id);
			});

			$(document).on('click', '.fc-delete', function(event) {
				var id = $(this).attr('data-id');
				var did = $(this).attr('data-did');
				deleteEvent(id);
				calender.fullCalendar( 'removeEvents', did );
			});
		});

	</script>
</body>
</html>


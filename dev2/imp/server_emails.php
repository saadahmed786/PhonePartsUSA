	<?php
	
	require_once("auth.php");
	
	require_once("inc/functions.php");
	include_once 'inc/split_page_results.php';
	//$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
	//Deleteing Record
    
	if ($_GET['email_id']) {
		$id = $_GET['email_id'];
		$email = $db->func_query_first("SELECT * FROM inv_server_emails WHERE id='".$id."'");
		// print_r($email);exit;
		$json = array();
		if ($email) {
			$json['success'] = 1;
			$json['name'] = $email['from_name'];
			$json['email'] = $email['from_email'];
			$json['subject'] = $db->func_escape_string($email['subject']);
			$json['message'] = (nl2br($email['message']));
			$json['user'] = get_username($email['assigned_user']);
		} else {
			$json['error'] = 1;
		}
		// print_r($json);exit;
		$json = array_map('utf8_encode', $json);
		echo json_encode($json);
		exit;
	}
	if($_POST['action']=='updateAgent')
	{
		$email_ids =  $_POST['email_ids'];
		foreach ($email_ids as $email_id) {			
			$db->db_exec("UPDATE inv_server_emails SET assigned_user='".$_POST['id']."' WHERE id='".$email_id."' ");	
		}
		$json['user'] = get_username($_POST['id']);
		echo json_encode($json);
		exit;
	}
	// Getting Page information
	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}

	if ($page < 1) {
		$page = 1;
	}
	//Setting PAgination Limits
	$max_page_links = 5;
	$num_rows = 10;
	$start = ($page - 1) * $num_rows;
	//Setting Search prameters
	$where = '';
	$filter = array();
	if ($_GET['user_id']) {
		$filtertype = $_GET['user_id'];
		$filter[] = "`assigned_user` = '$filtertype'";
	}

	if ($filter) {
		$where = 'WHERE ' . implode( ' AND ', $filter);
	}
	//Writing query 
	//echo "SELECT * FROM `inv_server_emails` $where order by date_added desc";exit;
	$inv_query = "SELECT * FROM `inv_server_emails` $where order by date_added desc";

	//Using Split Page Class to make pagination
	$splitPage = new splitPageResults($db, $inv_query, $num_rows, "canned_messages_manage.php", $page);

	//Getting All Messages
	$emails = $db->func_query($splitPage->sql_query);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Server Emails | PhonePartsUSA</title>
		<link href="https://fonts.googleapis.com/css?family=Oleo+Script:400,700" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Teko:400,700" rel="stylesheet">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
		<script type="text/javascript" src="ckeditor/ckeditor.js">

			
		</script>
		<script>
			$(document).ready(function(e) {
				$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
			});

		</script>
		<style type="text/css">
			body{ margin-top:50px;}
			.nav-tabs .glyphicon:not(.no-margin) { margin-right:10px; }
			.tab-pane .list-group-item:first-child {border-top-right-radius: 0px;border-top-left-radius: 0px;}
			.tab-pane .list-group-item:last-child {border-bottom-right-radius: 0px;border-bottom-left-radius: 0px;}
			.tab-pane .list-group .checkbox { display: inline-block;margin: 0px; }
			.tab-pane .list-group input[type="checkbox"]{ margin-top: 2px; }
			.tab-pane .list-group .glyphicon { margin-right:5px; }
			.tab-pane .list-group .glyphicon:hover { color:#FFBC00; }
			a.list-group-item.read { color: #222;background-color: #F3F3F3; }
			hr { margin-top: 5px;margin-bottom: 10px; }
			.nav-pills>li>a {padding: 5px 10px;}

			.ad { padding: 5px;background: #F5F5F5;color: #222;font-size: 80%;border: 1px solid #E5E5E5; }
			.ad a.title {color: #15C;text-decoration: none;font-weight: bold;font-size: 110%;}
			.ad a.url {color: #093;text-decoration: none;}
			/*Contact sectiom*/
			.content-header{
				font-family: 'Oleo Script', cursive;
				color:#ffffff;
				font-size: 45px;
			}

			.section-content{
				text-align: center; 

			}
			#contact{

				font-family: 'Teko', sans-serif;
				padding-top: 10px;
				width: 80%;
				/*width: 50vw;*/
				height: 430px;
				background: #A9A9A9; 
				color : #fff;    
			}
			.contact-section{
				padding-top: 0px;
			}
			.contact-section .col-md-6{
				width: 50%;
			}

			.form-line{
				border-right: 1px solid #ffffff;
			}

			.form-group{
				margin-top: 10px;
			}
			label{
				font-size: 1.3em;
				line-height: 1em;
				font-weight: normal;
				color: #ffffff;
			}
			.form-control{
				font-size: 1.3em;
				color: #080808;
			}
			textarea.form-control {
				height: 135px;
				/* margin-top: px;*/
			}

			.submit{
				font-size: 1.1em;
				float: right;
				width: 150px;
				background-color: transparent;
				color: #fff;

			}

		</style>
	</head>
	<body>
		<div align="center">
			<div align="center"> 
				<?php include_once 'inc/header.php';?>
			</div>
			<?php if ($_SESSION['message']) { ?>
			<div align="center"><br />
				<font color="red">
					<?php
					echo $_SESSION['message'];
					unset($_SESSION['message']);
					?>
					<br />
				</font>
			</div>
			<?php } ?>
			<br><br><br><br><br>
			<div class="container">
				<div class="row">
					<div class="col-sm-3 col-md-2">
						<div class="btn-group">
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
								Assign Agents <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
							<?php

										$agents =$db->func_query("SELECT id,name FROM inv_users WHERE is_sales_agent=1");
										foreach($agents as $agent)
										{
											?>
											<li><a href="javascript:void(0)" onclick="saveAgent(<?php echo $agent['id'];?>)"><?php echo $agent['name'];?></a></li>
											<?php
										}
										?>
								
								
							</ul>
						</div>
					</div>
					<div class="col-sm-7 col-md-8">
					<form method="get" action="">
					<table border="0" cellspacing="10" cellpadding="10">
						<tr>
							<td>Agent:</td>
							<td><select name="user_id" class="selectDiv" style="width:150px" >
										<option value="0">None</option>
										<?php

										$agents =$db->func_query("SELECT id,name FROM inv_users WHERE is_sales_agent=1");
										foreach($agents as $agent)
										{
											?>
											<option value="<?=$agent['id'];?>"<?=($_GET['user_id']==$agent['id']?'selected':'');?>><?=$agent['name'];?></option>
											<?php
										}
										?>
									</select></td>
									<td><input type="submit" class="button" name="search" value="Search"></td>
						</tr>
					</table>
						 
					</form>
						<!-- <button type="button" class="btn btn-default" data-toggle="tooltip" title="Refresh"><span class="glyphicon glyphicon-refresh"></span>Refresh</button> -->
							
						</div>
					</div>
					<hr /><br><br>

					<div class="row">
						<div class="col-sm-12 col-md-12" style="height:250px;overflow:auto;">
							<!-- Nav tabs -->
							
							<!-- Tab panes -->
								<table style="width: 1120px;"  border="0" cellpadding="10" cellspacing="10" >
								
									<tr style="background:#e5e5e5;">
										<td style="padding-left: 20px;font-style: large;" colspan="2">Name</td>
										<td align="center" style="font-style: large;" colspan="2">Subject</td>
										<td align="right" style="font-style: large;padding-right: 70px;" colspan="2">Date</td>
									</tr>
								
								<?php foreach($emails as $email){ ?>
									<tr onclick="renderThis(<?php echo $email['id'] ?>);" style="padding-top: 5px;padding-bottom: 5px;" >
									<td  style="padding-left: 20px;padding-top: 5px;padding-bottom: 5px;font-size: medium;" colspan="2">
										<input type="checkbox" class="selection" name="email_id" id="email_id" value="<?php echo $email['id'] ?>" onChange="selectThis(this)"> <?php echo $email['from_name']; ?>
									</td>	
									<td colspan="2" style="font-size: small;padding-top: 5px;padding-bottom: 5px;" align="center">
										<?php echo $email['subject']; ?>
									</td>								

									<td colspan="2" class="badge" align="center" style="float: right;padding-top: 5px;padding-bottom: 5px;"><?php echo americanDate($email['date_email']); ?></td>
								</tr>
								
							<!-- <div class="tab-content tab-pane fade in active list-group" style="align-items: left;">
								
								<a href="javascript:void(0)" onclick="renderThis(<?php echo $email['id'] ?>);"  class="list-group-item">

									<span class="name"  style="min-width: 120px;
									display: inline-block;margin-left: -750px;"><input type="checkbox" class="selection" name="email_id" id="email_id" value="<?php echo $email['id'] ?>" onChange="selectThis(this)"> <?php echo $email['from_name']; ?></span><span style="min-width: 120px;
									display: inline-block;margin-left: -100px;"></span><?php echo $email['subject']; ?></span>
									 <span
									class="badge"><?php echo americanDate($email['date_email']); ?></span> <span class="pull-right"></span>
								</a>
							</div> -->
								<?php } ?>
								</table>
						</div>
					</div><br>
					<br><br>
					<div class="row" id="email_table" style="display: none;">
						<section id="contact">
							<div class="section-content" >
							<h3 class="section-header">Email Description</h3>
							</div>
							<div class="contact-section">
								<div class="container">
									<form>
										<div class="col-md-4 form-line ">
										<div class="form-group">
												<label class="pull-left" for="exampleInputEmail">From</label>
												<input type="email" class="form-control" id="from_email" value="">
											</div>
											<div class="form-group">
												<label class="pull-left" for="exampleInputUsername">Subject</label>
												<input type="text" class="form-control" id="subject" value="">
											</div>	
										</div>
										<div class="col-md-4">
										<div class="form-group">
												<label class="pull-left" for="exampleInputEmail">Name</label>
												<input type="email" class="form-control" id="from_name" value="">
											</div>
											<div class="form-group">
												<label class="pull-left" for="exampleInputUsername">Assigned User</label>
												<input type="text" class="form-control" id="assigned_user" value="">
											</div>	
										</div>
										<div class="col-md-8">
											<div class="form-group">
												<!-- <label class="pull-left" for ="description"> Message</label> -->
												<div id="message" style="color:#000;text-align:left;background-color:#FFF;height:90%" ></div>
											</div>
										</div>
									</form>
								</div>
							</section>
						</div>
					</div>


					</body>
					<script type="text/javascript">
					emails = [];
						function renderThis(id){
							jQuery.ajax({
								url: 'server_emails.php?email_id='+id,
								type:"GET",
								dataType:'json',
								success: function(json){
									if (json['success']) {
										/*alert('here');*/
										$('#from_name').val(json['name']);
										$('#from_email').val(json['email']);
										$('#subject').val(json['subject']);
										$('#message').html(json['message']);
										$('#hidden_id').html(id);
										$('#assigned_user').val(json['user']);
										$('#email_table').show();
									} else {
										alert('werror');
									}
									
								}
							}); 
						}
						function saveAgent(obj) {
							//var agent = $(obj).val();
							var id = obj;
							if (!emails[0]) {
								alert('Please select emails to be assigned');
								return false;
							}
							if(!confirm('Are you sure want to update the agent for selected ?'))
							{
								return false;
							}
							
							$.ajax({
								url: 'server_emails.php',
								type: 'post',
								dataType:'json',
								data: {action: 'updateAgent',id:id,email_ids:emails},

								beforeSend: function () {

								},
								complete: function () {

								},
								success: function (json) {
									alert('Agent Modified');
									$('#assigned_user').val(json['user']);
								}
							});


						}
						function selectThis(obj){
								if ($(obj).is(':checked')) {
									if (!emails.includes($(obj).val())) {
										emails.push($(obj).val());
									}
								} else {
									emails.splice($.inArray($(obj).val(), emails), 1);
								}
								console.log(emails);
						}
					</script>
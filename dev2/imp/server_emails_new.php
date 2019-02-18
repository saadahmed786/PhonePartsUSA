	<?php
	require_once("auth.php");
	require_once("inc/functions.php");
	include_once 'inc/split_page_results.php';
	$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);
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
		
		
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $host_path; ?>include/bootstrap/css/bootstrap-theme.min.css">
		

			
		</script>
		<script>
			$(document).ready(function(e) {
				$('.fancybox3').fancybox({ width: '90%' , autoCenter : true , autoSize : true });
			});

		</script>
		<style type="text/css">


.btn-compose-email {
    padding: 10px 0px;
    margin-bottom: 20px;
}

.btn-danger {
    background-color: #E9573F;
    border-color: #E9573F;
    color: white;
}

.panel-teal .panel-heading {
    background-color: #37BC9B;
    border: 1px solid #36b898;
    color: white;
}

.panel .panel-heading {
    padding: 5px;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
    border-bottom: 1px solid #DDD;
    -moz-border-radius: 0px;
    -webkit-border-radius: 0px;
    border-radius: 0px;
}

.panel .panel-heading .panel-title {
    padding: 10px;
    font-size: 17px;
}

form .form-group {
    position: relative;
    margin-left: 0px !important;
    margin-right: 0px !important;
}

.inner-all {
    padding: 10px;
}

/* ========================================================================
 * MAIL
 * ======================================================================== */
.nav-email > li:first-child + li:active {
  margin-top: 0px;
}
.nav-email > li + li {
  margin-top: 1px;
}
.nav-email li {
  background-color: white;
}
.nav-email li.active {
  background-color: transparent;
}
.nav-email li.active .label {
  background-color: white;
  color: black;
}
.nav-email li a {
  color: black;
  -moz-border-radius: 0px;
  -webkit-border-radius: 0px;
  border-radius: 0px;
}
.nav-email li a:hover {
  background-color: #EEEEEE;
}
.nav-email li a i {
  margin-right: 5px;
}
.nav-email li a .label {
  margin-top: -1px;
}

.table-email tr:first-child td {
  border-top: none;
}
.table-email tr td {
  vertical-align: top !important;
}
.table-email tr td:first-child, .table-email tr td:nth-child(2) {
  text-align: center;
  width: 35px;
}
.table-email tr.unread, .table-email tr.selected {
  background-color: #EEEEEE;
}
.table-email .media {
  margin: 0px;
  padding: 0px;
  position: relative;
}
.table-email .media h4 {
  margin: 0px;
  font-size: 14px;
  line-height: normal;
}
.table-email .media-object {
  width: 35px;
  -moz-border-radius: 2px;
  -webkit-border-radius: 2px;
  border-radius: 2px;
}
.table-email .media-meta, .table-email .media-attach {
  font-size: 11px;
  color: #999;
  position: absolute;
  right: 10px;
}
.table-email .media-meta {
  top: 0px;
}
.table-email .media-attach {
  bottom: 0px;
}
.table-email .media-attach i {
  margin-right: 10px;
}
.table-email .media-attach i:last-child {
  margin-right: 0px;
}
.table-email .email-summary {
  margin: 0px 110px 0px 0px;
}
.table-email .email-summary strong {
  color: #333;
}
.table-email .email-summary span {
  line-height: 1;
}
.table-email .email-summary span.label {
  padding: 1px 5px 2px;
}
.table-email .ckbox {
  line-height: 0px;
  margin-left: 8px;
}
.table-email .star {
  margin-left: 6px;
}
.table-email .star.star-checked i {
  color: goldenrod;
}

.nav-email-subtitle {
  font-size: 15px;
  text-transform: uppercase;
  color: #333;
  margin-bottom: 15px;
  margin-top: 30px;
}

.compose-mail {
  position: relative;
  padding: 15px;
}
.compose-mail textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #DDD;
}

.view-mail {
  padding: 10px;
  font-weight: 300;
}

.attachment-mail {
  padding: 10px;
  width: 100%;
  display: inline-block;
  margin: 20px 0px;
  border-top: 1px solid #EFF2F7;
}
.attachment-mail p {
  margin-bottom: 0px;
}
.attachment-mail a {
  color: #32323A;
}
.attachment-mail ul {
  padding: 0px;
}
.attachment-mail ul li {
  float: left;
  width: 200px;
  margin-right: 15px;
  margin-top: 15px;
  list-style: none;
}
.attachment-mail ul li a.atch-thumb img {
  width: 200px;
  margin-bottom: 10px;
}
.attachment-mail ul li a.name span {
  float: right;
  color: #767676;
}

@media (max-width: 640px) {
  .compose-mail-wrapper .compose-mail {
    padding: 0px;
  }
}
@media (max-width: 360px) {
  .mail-wrapper .panel-sub-heading {
    text-align: center;
  }
  .mail-wrapper .panel-sub-heading .pull-left, .mail-wrapper .panel-sub-heading .pull-right {
    float: none !important;
    display: block;
  }
  .mail-wrapper .panel-sub-heading .pull-right {
    margin-top: 10px;
  }
  .mail-wrapper .panel-sub-heading img {
    display: block;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 10px;
  }
  .mail-wrapper .panel-footer {
    text-align: center;
  }
  .mail-wrapper .panel-footer .pull-right {
    float: none !important;
    margin-left: auto;
    margin-right: auto;
  }
  .mail-wrapper .attachment-mail ul {
    padding: 0px;
  }
  .mail-wrapper .attachment-mail ul li {
    width: 100%;
  }
  .mail-wrapper .attachment-mail ul li a.atch-thumb img {
    width: 100% !important;
  }
  .mail-wrapper .attachment-mail ul li .links {
    margin-bottom: 20px;
  }

  .compose-mail-wrapper .search-mail input {
    width: 130px;
  }
  .compose-mail-wrapper .panel-sub-heading {
    padding: 10px 7px;
  }
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
			
			<div class="container">
<div class="row">
    <div class="col-sm-3">
        <a href="mail-compose.html" class="btn btn-danger btn-block btn-compose-email">Compose Email</a>
        <ul class="nav nav-pills nav-stacked nav-email shadow mb-20">
            <li class="active">
                <a href="#mail-inbox.html">
                    <i class="fa fa-inbox"></i> Inbox  <span class="label pull-right">7</span>
                </a>
            </li>
            
            <li>
                <a href="#"><i class="fa fa-certificate"></i> Important</a>
            </li>
            
            <li><a href="#"> <i class="fa fa-trash-o"></i> Trash</a></li>
        </ul><!-- /.nav -->

    </div>
    <div class="col-sm-9">
        <div class="panel rounded shadow panel-teal">
            <div class="panel-heading">
                <div class="pull-left">
                    <h3 class="panel-title">Inbox (7)</h3>
                </div>
                <div class="pull-right">
                    <form action="#" class="form-horizontal mr-5 mt-3">
                        <div class="form-group no-margin no-padding has-feedback">
                            <input type="text" class="form-control no-border" placeholder="Search mail">
                            <button type="submit" class="btn btn-theme fa fa-search form-control-feedback"></button>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div><!-- /.panel-heading -->
            <div class="panel-sub-heading inner-all">
                <div class="pull-left">
                    <ul class="list-inline no-margin">
                        <li>
                            <div class="ckbox ckbox-theme">
                                <input id="checkbox-group" type="checkbox" class="mail-group-checkbox">
                                <label for="checkbox-group"></label>
                            </div>
                        </li>
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-md dropdown-toggle" data-toggle="dropdown">
                                    All <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">None</a></li>
                                    <li><a href="#">Read</a></li>
                                    <li><a href="#">Unread</a></li>
                                </ul>
                            </div>
                        </li>
                        
                      
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="list-inline no-margin">
                        <li class="hidden-xs"><span class="text-muted">Showing 1-50 of 2,051 messages</span></li>
                        <li>
                            <div class="btn-group">
                                <a href="#" class="btn btn-md btn-default"><i class="fa fa-angle-left"></i></a>
                                <a href="#" class="btn btn-md btn-default"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </li>
                      
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div><!-- /.panel-sub-heading -->
            <div class="panel-body no-padding">

                <div class="table-responsive">
                    <table class="table table-hover table-email">
                        <tbody>
                        <tr class="unread selected">
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox1" type="checkbox"  class="mail-checkbox">
                                    <label for="checkbox1"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star star-checked"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar1.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">John Kribo</h4>
                                        <p class="email-summary"><strong>Commits pushed</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit... <span class="label label-success">New</span> </p>
                                        <span class="media-meta">Today at 6:16am</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox2" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox2"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar2.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Jennifer Poiyem</h4>
                                        <p class="email-summary"><strong>Send you gift</strong> Sed do eiusmod tempor incididunt...<span class="label label-success">New</span> </p>
                                        <span class="media-meta">Today at 1:13am</span>
                                        <span class="media-attach"><i class="fa fa-paperclip"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="unread">
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox3" type="checkbox" checked="checked" class="mail-checkbox">
                                    <label for="checkbox3"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star star-checked"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar3.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Clara Wati</h4>
                                        <p class="email-summary"><strong>Follow you</strong> Ut enim ad minim veniam, quis nostrud exercitation... </p>
                                        <span class="media-meta">Jul 02</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox4" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox4"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar4.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Toni Mriang</h4>
                                        <p class="email-summary"><strong>Check out new template</strong> Laboris nisi ut aliquip ex ea commodo consequat... <span class="label label-warning">Urgent</span></p>
                                        <span class="media-meta">Jul 02</span>
                                        <span class="media-attach"><i class="fa fa-paperclip"></i><i class="fa fa-share"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="selected">
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox5" type="checkbox" checked="checked" class="mail-checkbox">
                                    <label for="checkbox5"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star star-checked"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar5.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Bella negoro</h4>
                                        <p class="email-summary"><strong>Monthly sales report</strong> Excepteur sint occaecat cupidatat non proident... </p>
                                        <span class="media-meta">Jul 02</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="unread">
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox6" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox6"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar6.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Kim Mbako</h4>
                                        <p class="email-summary"><strong>1 New job</strong> Sed ut perspiciatis unde omnis iste natus error sit voluptatem... <span class="label label-danger">Promotion</span></p>
                                        <span class="media-meta">Jul 01</span>
                                        <span class="media-attach"><i class="fa fa-paperclip"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox7" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox7"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar6.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Pack Suparman</h4>
                                        <p class="email-summary"><strong>You sold a item!</strong> Ut enim ad minim veniam, quis nostrud exercitation... </p>
                                        <span class="media-meta">Jul 01</span>
                                        <span class="media-attach"><i class="fa fa-users"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox8" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox8"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar6.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Jeddy Mentri</h4>
                                        <p class="email-summary"><strong>IOS Developer</strong> Ut enim ad minim veniam, quis nostrud exercitation... </p>
                                        <span class="media-meta">Jun 25</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox9" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox9"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar1.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Daddy Botak</h4>
                                        <p class="email-summary"><strong>User interface Status</strong> Ut enim ad minim veniam, quis nostrud exercitation... </p>
                                        <span class="media-meta">Jun 23</span>
                                        <span class="media-attach"><i class="fa fa-paperclip"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="unread">
                            <td>
                                <div class="ckbox ckbox-theme">
                                    <input id="checkbox10" type="checkbox" class="mail-checkbox">
                                    <label for="checkbox10"></label>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="star"><i class="fa fa-star"></i></a>
                            </td>
                            <td>
                                <div class="media">
                                    <a href="#" class="pull-left">
                                        <img alt="..." src="https://bootdey.com/img/Content/avatar/avatar6.png" class="media-object">
                                    </a>
                                    <div class="media-body">
                                        <h4 class="text-primary">Sarah Tingting</h4>
                                        <p class="email-summary"><strong>Java Developer + 2 new jobs</strong> Nemo enim ipsam voluptatem quia voluptas sit aspernatur... </p>
                                        <span class="media-meta">Jun 05</span>
                                        <span class="media-attach"><i class="fa fa-warning"></i></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->

            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
    </div>
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
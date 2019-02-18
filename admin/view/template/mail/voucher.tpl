<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
<style type="text/css">
body{ background:#ebebeb;font-family: Arial, Helvetica, sans-serif;}
#wrapper { margin: 0 auto; }
a { color: #54ced5; text-decoration: none; transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; outline: none; }
a:hover { color: #29aae3; text-decoration: underline; }
/* @Header + Banner
********************************************************************************************
********************************************************************************************/
.container{ width:544px; margin:0 auto;}
.hading-holder{ margin:0; background:#24241e; padding:23px 0;}
.hading-holder h1{ margin:0 0 0 22px;}

.center-body{ background:#f6f6f7; padding:28px 67px 31px 77px; text-align:center;}
.center-body h2{ font-size:25px; color:#5d5d51; margin:0 0 27px;}
.msg-img{ display:block; margin:0 0 40px;}
.code-x{ font-size:18px; color:#5d5d51;}

.footer{ padding:37px 97px 57px 97px; background:#fff; text-align:center;}
.footer p{ font-size:12px; color:#5d5d51; margin:0 0 74px;}
.card-btn{ padding:9px 0; width:236px; text-align:center; font-size:24px; background:#7fbe56; display:inline-block; color:#fff;}
.card-btn:hover{ color:#fff; background:#4d8529; text-decoration:none;}

.footer-p{ padding:33px 115px; text-align:center;}
.footer-p p{ margin:0; font-size:12px; color:#b7b7b7;}
</style>
</head>
<body style="background:#ebebeb;font-family: Arial, Helvetica, sans-serif;">
<div class="container" style="width:544px; margin:0 auto;">

	<!-- Start header -->
	<div class="header">
    	<div class="hading-holder" style="margin:0; background:#24241e; padding:23px 0;">
        	<h1 style="margin:0 0 0 22px;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $store_name; ?>" /></a></h1>
        </div>
    </div>
    <!-- End header -->
    
    <!-- Start Centerbody -->
	<div class="center-body" style="background:#f6f6f7; padding:28px 67px 31px 77px; text-align:center;">
    	<h2 style="ont-size:25px; color:#5d5d51; margin:0 0 27px;"><?php echo $text_main;?></h2>
        <span class="msg-img" style="display:block; margin:0 0 40px;"><img src="<?php echo $store_credit_image;?>" alt="Store Credit"></span>
        <strong class="code-x" style="font-size:18px; color:#5d5d51; font-weight:500"><?php echo $text_secret_code;?></strong>
    </div>
    <!-- End Centerbody -->
    
    <!-- Start Footer -->
	<div class="footer" style="padding:37px 97px 57px 97px; background:#fff; text-align:center;">
    	<p style="font-size:12px; color:#5d5d51; margin:0 0 74px;}"><?php echo $text_footer;?></p>
        <a class="card-btn" style=" margin: 0 auto;padding:9px 0; width:236px; text-align:center; font-size:24px; background:#7fbe56; display:inline-block; color:#fff;" href="<?php echo $store_url; ?>">Use Store Credit</a>
    </div>
    <!-- End Footer -->
    <div class="footer-p" style=" padding:33px 115px; text-align:center;"><p style="margin:0; font-size:12px; color:#b7b7b7;"><?php echo $text_footer2;?></p></div>
</div>
</body>
</html>

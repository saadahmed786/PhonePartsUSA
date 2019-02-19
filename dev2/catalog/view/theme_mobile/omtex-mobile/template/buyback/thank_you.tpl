<?php echo $header; ?>
<style>
	.print-inner{ width:690px; overflow:hidden;}
	.product-img{ margin:0 30px 0 0; height:141px; background:#e9e9e9; float:left; border:1px solid #cfcfcf; width:114px;}
	.product-detail{ width:544px; float:left;text-align:left}
	.print-inner .return-btn{ width:237px; font-size:18px; position:relative; margin:0 0 9px;}
	.print-inner .return-btn img{ position:absolute; left:16px; top:12px;}
	.print-inner strong{ display:block; margin:0 0 9px;}

	.follwing-note{ padding:7px; background:#e9e9e9; margin:0 0 13px;}
	.follwing-note ul{ margin:0; padding:0; list-style:none;}
	.follwing-note ul li{ margin:0 0 5px; font-size:14px;text-align:left;}
	.follwing-note ul li img{ margin:0 5px 0 0;}

	.sipmle-box{ /*width:579px;*/ margin:0 auto; /*height:115px;*/ background:#e9e9e9; text-align:center;}
	.sipmle-box img{height:100% !important;width:100% !important;}
	.select-active{ background-color:#fff !important;color:#000;}
	.return-btn{ width:341px; display:inline-block; color:#fff !important; text-align:center; padding:15px 0; background:#dd5555; border-radius:4px;font-size:16px;}
	.return-btn:hover{ text-decoration:none; color:#fff; background:#E7676A;}

	.return-btn-small{ width:130px; display:inline-block; color:#fff !important; text-align:center; padding:6px 0; background:#dd5555; border-radius:4px;font-size:12px;}
	.return-btn-small:hover{ text-decoration:none; color:#fff; background:#E7676A;}
	#content {
		padding: 25px;
	}
	.selet-item-header .pull-left {
		font-size: 25px;
	}
	.selet-item-holder .print-inner {
		width: 100%;
		text-align: center;
	}
	.print-inner .product-img {
		float: none;
		display: inline-block;
		height: 200px;
    	width: 170px;
    	margin: 0;
	}
	.print-inner .product-detail {
		float: none;
    	width: 100%;
    	text-align: center;
	}
</style>
<!-- Wholesale Form -->
<ul id="breadcrumbs-one">
	<?php 
	$total = count($breadcrumbs); 
	$i=0;
	foreach ($breadcrumbs as $breadcrumb) { 
		$i++;
		if($i==$total)
			{ ?>
		<li><a class="current"><?php echo $breadcrumb['text']; ?></a></li>
		<?php  } else { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>" rel="external"><?php echo $breadcrumb['text']; ?></a></li>
		<?php }
	} ?>
</ul>
<div id="content">
	<?php echo $content_top; ?>
	<h1><?php echo $heading_title; ?></h1>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<p>
		<?php echo $html;?>
	</p>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>
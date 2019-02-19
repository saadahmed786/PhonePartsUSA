<?php echo $header; ?>
<div id="main" role="main">
	<?php echo $content_top; ?>	
	<ul id="breadcrumbs">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	<?php } ?>
	</ul>
	<h1><?php echo $heading_title; ?></h1>
	<ul>
		<?php foreach ($informations as $information) { ?>
		<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
		<?php } ?>
	</ul>
	<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>
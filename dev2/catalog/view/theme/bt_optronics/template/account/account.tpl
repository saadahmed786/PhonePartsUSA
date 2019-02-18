<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
		<div class="content myaccount">
		<h2><?php echo $text_my_account; ?></h2>
			<ul>
			<?php if (!$this->session->data['warehouse']) { ?>
			  <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
			  <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
			  <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
			  <?php } ?>
			  <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
              <li><a href="<?php echo $viewvouchers; ?>"><?php echo $text_viewvouchers; ?></a></li>
			</ul>
		</div>
	  <div class="content myaccount">
	  <h2><?php echo $text_my_orders; ?></h2>
		<ul>
		  <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
		  <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
		  <?php if ($reward) { ?>
		  <li><a href="<?php echo $reward; ?>"><?php echo $text_reward; ?></a></li>
		  <?php } ?>
		  <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
		  <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
		</ul>
	  </div>
	  <div class="content myaccount">
	  <h2><?php echo $text_my_lbb; ?></h2>
		<ul>
		  <li><a href="<?php echo $lbb; ?>"><?php echo $text_lbb; ?></a></li>
		</ul>
	  </div>
	  <div class="content myaccount">
	  <h2><?php echo $text_my_returns; ?></h2>
		<ul>
		  <li><a href="<?php echo $returns; ?>"><?php echo $text_returns; ?></a></li>
		</ul>
	  </div>
	  <div class="content myaccount">
	  <h2><?php echo $text_my_newsletter; ?></h2>
		<ul>
		  <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
		</ul>
	  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 
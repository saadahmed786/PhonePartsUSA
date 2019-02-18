</div>
<div id="footer">
	<?php echo $module; ?>
	<ul class="tools">
	    <li><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#header"><?php echo $text_top; ?></a></li>
	    <li>
	      <?php echo $text_view; ?> <?php echo $text_mobile; ?> / <a href="<?php if (strpos($_SERVER['QUERY_STRING'], 'view=mobile') === false) {
	                  echo $_SERVER['REQUEST_URI'] . (empty($_SERVER['QUERY_STRING']) ? '?view=desktop' : '&view=desktop');
	                } else {
	                  echo str_replace('view=mobile', 'view=desktop', $_SERVER['REQUEST_URI']);
	                } ?>"><?php echo $text_standard; ?></a>
	    </li>
	</ul>
 	<p style="text-align:center; font-size: smaller; padding-top: 2em">Powered by <a href="http://www.omframework.com">OMFramework 2.3.0 Basic</a></p>
</div>
<?php if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/js/script.js')) { ?>
<script type="text/javascript"  src="<?php echo 'catalog/view/theme/' . $this->config->get('config_template') ?>/js/script.js"></script>
<?php } else { ?>
<script type="text/javascript" src="catalog/view/theme/omf2/js/script.js"></script>
<?php } ?>
</body></html>
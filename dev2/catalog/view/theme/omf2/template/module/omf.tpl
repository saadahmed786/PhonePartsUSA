<?php if ((strrpos($route, "checkout") === false) && (strrpos($route, "account") === false)) { ?>
  <form action="index.php?route=product/search_mobile" method="post" id="search" class="inline-form module">
    <fieldset >
      <?php
      $field_name = (defined('VERSION') && (version_compare(VERSION, '1.5.5', '>=') == true)) ? 'search' : 'filter_name';
      if ($filter_name) { ?>
      <input type="search" name="<?php echo $field_name; ?>" placeholder="<?php echo $filter_name; ?>" />
      <?php } else { ?>
      <input type="search" name="<?php echo $field_name; ?>"  placeholder="<?php echo $text_search; ?>" />
      <?php } ?>
      <input type="submit" value="<?php echo $text_search_link; ?>" />
    </fieldset>
  </form>
<?php } ?>
<?php if (isset($categories)) { ?>
  <nav id="secondary">
    <ul class="nav">
      <?php $i = 0; ?>
      <?php foreach ($categories as $category) { ?>
      <li>
        <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
      </li>
      <?php  if (++$i == $config_mobile_front_page_cat_list) break; ?>
      <?php } ?>
    </ul>
    <a href="<?php echo $all_categories; ?>" class="stack"><?php echo $text_all_categories; ?></a>
    <?php if ($blog) { ?>
    <a href="<?php echo $this->url->link('blog/category/home'); ?>"> <?php echo $text_blog; ?> </a>
    <?php } ?>
  </nav>
<?php } ?>
<?php if (strrpos($route, "checkout/checkout") === false) { ?>
  <nav id="primary">
    <ul class="nav">
      <li><a href="<?php echo $home; ?>" class="n-home"><?php echo $text_home; ?></a></li>
      <li><a href="<?php echo $info; ?>" class="n-info"><?php echo $text_information; ?></a>
        <?php if (!empty($informations)) { ?>
          <ul>
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
        </ul>
        <?php } ?>
      </li>
      <li><a href="<?php echo $account; ?>" class="n-account"><?php echo $text_account; ?></a>
        <ul>
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
        </ul>
      </li>
      <li><a href="<?php echo $contact; ?>" class="n-contact"><?php echo $text_contact; ?></a>
        <ul>
          <li><a href="tel:<?php echo $telephone; ?>"><?php echo $text_call; ?></a></li>
          <li><a href="<?php echo $contact; ?>"><?php echo $text_enquiry; ?></a></li>
          <li><a href="http://maps.google.com/maps?q=<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
        </ul>
      </li>
    </ul>
  </nav>
  <aside id="settings">
    <?php if(isset($language)) echo $language; ?>
    <?php if(isset($currency)) echo $currency; ?>
  </aside>
  <span id="welcome">
    <?php if (!$logged) { ?>
    <?php echo $text_welcome; ?>
    <?php } else { ?>
    <?php echo $text_logged; ?>
    <?php } ?>
  </span>
<?php } ?>
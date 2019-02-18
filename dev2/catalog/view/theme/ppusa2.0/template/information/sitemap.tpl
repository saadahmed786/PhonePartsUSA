<?php echo $header; ?>
<main class="main">
  <div class="container lcd-buy-signout-page">
    <div class="row row-centered">
      <div class="col-md-10 intro-head col-centered">
        <div class="text-center">
          <h1 class="blue blue-title uppercase mb20"><?php echo $heading_title;?></h1>
          <!-- <h4 class="uppercase">Optional info sub-title</h4> -->
        </div>
      </div>
    </div>
    <div class="white-box overflow-hide">
      <div class="row">
        <div class="col-md-12">
          <div class="row inline-block">
            <div class="col-md-6 white-box-left pr0 inline-block" style="padding-left:3%">
              <!-- <div class="white-box-inner panel-trigger-parent">
              <h4 class="blue-title">ABOUT SITEMAP <a href="#" class="panel-trigger"><i class="fa fa-chevron-down"></i></a></h4>
              </div> -->
              <div class="white-box-inner">
                  <h4 class="blue-title"></h4>
                </div>   

              <div class="white-box-inner panel-triggered">
              <ul style="list-style-type:disc;">
                    <?php foreach ($categories as $category_1) { ?>
                    <li><a href="<?php echo $category_1['href']; ?>"><?php echo $category_1['name']; ?></a>
                      <?php if ($category_1['children']) { ?>
                      <ul style="list-style-type:circle;margin-left:7%">
                        <?php foreach ($category_1['children'] as $category_2) { ?>
                        <li><a href="<?php echo $category_2['href']; ?>"><?php echo $category_2['name']; ?></a>
                          <?php if ($category_2['children']) { ?>
                          <ul style="list-style-type:disc;margin-left:14%">
                            <?php foreach ($category_2['children'] as $category_3) { ?>
                            <li><a href="<?php echo $category_3['href']; ?>"><?php echo $category_3['name']; ?></a></li>
                            <?php } ?>
                          </ul>
                          <?php } ?>
                        </li>
                        <?php } ?>
                      </ul>
                      <?php } ?>
                    </li>
                    <?php } ?>
                    </ul>
                </div>
              </div>
              <div class="col-md-6 white-box-right inline-block overflow-hide">
                <div class="white-box-inner">
                  <h4 class="blue-title"></h4>
                </div>    
                <div class="white-box-inner">
                  
                   <ul style="list-style-type:disc">  
        <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
         
        </li>
        <li><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a></li>
        <li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
        <li><?php echo $text_information; ?>
          <ul style="list-style-type:circle;margin-left:7%">
            <?php foreach ($informations as $information) { ?>
            <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
            <?php } ?>
            <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main><!-- @End of main -->
  <?php echo $footer; ?>
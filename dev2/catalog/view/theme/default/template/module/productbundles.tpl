<style>
/* @Reset
********************************************************************************************
********************************************************************************************/
/*body{ background:#282828;}*/
#wrapper { margin: 0 auto;}
/*a { color: #00bba6; text-decoration: none; transition: all 0.5s; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; outline: none; }
a:hover { color: #8b59da; text-decoration: underline; }*/
/* @Header
********************************************************************************************
********************************************************************************************/
.main-holder{ max-width:660px; margin:0 auto; padding:10px 12px 10px 20px; background:#fff;}
.ad-inner{overflow:hidden; border:2px solid #363636;}
.top-bar{ background:#363636; overflow:hidden; margin:0 0 37px; height:41px;}
.top-bar h2{ margin:0; font-size:25px; color:#fff;}
.top-right{ padding:9px 0 14px 14px; float:left;}
.top-left{ padding:9px 9px 14px 0; float:right; background:#b00000; position:relative;}
.top-left strong{ color:#fff; font-size:22px; font-weight:bold; font-family:Arial, Helvetica, sans-serif;}
.strong-bg{ background:url(image/image04.png) no-repeat; height:41px; width:30px; position:absolute; right:100%; top:0;}

.figure-list-holder{ padding:0 31px 46px 5px; overflow:hidden;}
.figure-list{ margin:0; padding:0; list-style:none;}
.figure-list li{ float:left; margin:0 22px 0 0;}
.figure1{ width:146px; float:left;}
.figure-inn{ text-align:center; width:113px; float:left;}
.figure1 ad-img{ margin:0 0 17px 0; display:block;}
.figure1 p{ margin:0; font-size:10px;}
.add-icon{ display:block; margin:45px 0 0; float:right; background:url(image/image05.png) no-repeat; height:33px; width:33px;}
.result-icon{ display:block; margin:45px 0 0; background:url(image/image06.png) no-repeat; height:15px; width:25px; float:right;}

.figure-list li:last-child{ text-align:center; width:110px; margin:0;}
.old-prise{ color:#000; font-size:20px; display:block;}
.new-price{ color:#cc0909; font-size:30px; margin:0 0 26px; display:block;}
.buy-botton{ display:block;margin-left:-22px;}
.buy-botton img{ }
</style>
<?php if ( ($data['ProductBundles']['Enabled'] != 'no') && ($ShowTheModule == true) ) { ?>

        <?php if(!empty($data['ProductBundles']['CustomCSS'])): ?>
        <style>
        <?php echo htmlspecialchars_decode($data['ProductBundles']['CustomCSS']); ?>
        </style>
        <?php endif; ?>
         
	
    	<div class="wrapper">
        <div class="main-holder">
		<div class="ad-inner">
    	<div class="top-bar">
      	<div class="top-right"><h2>>> <?php echo $data['ProductBundles']['WidgetTitle']; ?></h2></div>
        <div class="top-left"><strong>Save <?php echo $VoucherPrice;?></strong><span class="strong-bg"></span></div>
    </div>
    	<div class="figure-list-holder">
    	<ul class="figure-list">
    	
          <?php $i=0;
         $total_products =  count($products);
         
           ?>
          <?php foreach ($products as $product) { ?>
        <li><div class="figure1">
      		<span class="figure-inn">
        		<?php if ($product['thumb']) { ?>
                <span class="ad-img"><img src="<?php echo $product['thumb'];?>" alt="<?php echo $product['name'];?>" onClick="window.location='<?php echo $product['href'];?>'" style="cursor:pointer"></span>
                <?php
                }
                ?>
        		<p><?php echo $product['name'];?></p>
          </span>
          <?php if ($i+1 != $total_products) { ?> 
          <span class="add-icon"></span>
          <?php
          }
          else
          {
          ?>
            <span class="result-icon"></span>
          <?php
          }
          ?>
      </div></li>
      
      <?php
      $i++;
      }
      ?>
      
      
      
      <li>
      	<span class="old-prise"><?php echo $ProductBundles_BundlePrice;?></span>
        <span class="new-price"><?php echo $FinalPrice;?></span>
        <span class="buy-botton"><a id="ProductBundlesSubmitButton"><img src="image/image07.jpg" alt="image07"></a></span>
      </li>
      </ul>
      <form method="post" id="ProductBundlesForm">
                        <input id="ProductBundlesProducts" type="hidden" name="products" value="<?php echo $BundleProducts; ?>" />
                        <input id="ProductBundlesDiscount" type="hidden" name="discount" value="<?php echo $VoucherData; ?>" />
                        <input id="ProductBundlesBundleID" type="hidden" name="bundle" value="<?php echo $BundleNumber; ?>" />
                        </form>
      </div>
  </div>
</div>
</div>
             <?php } ?>
             
             
             <script>
			 jQuery(window).load(function () {
				$('#ProductBundlesSubmitButton').on('click', function(e){
					<?php if ($productOptions==true) { ?>
						$.fancybox.open({
							href : 'index.php?route=module/productbundles/bundleproductoptions&bundle=<?php echo $BundleNumber; ?>',
							type : 'ajax',
							padding : 20,
							openEffect : 'elastic',
							openSpeed  : 150,
							closeBtn  : <?php echo $CloseButton; ?>
						});
					<?php } else { ?>
						 $.ajax({
							url: 'index.php?route=module/productbundles/bundletocart',
							type: 'post',
							data: $('#ProductBundlesForm').serialize(),
							dataType: 'json',
							success: function(json) {
								if (json['error']) {
									alert("There is a problem with the form. Please try again later.");
								}
								if (json['duplicate'] && !json['error']) {
									window.location = "<?php echo html_entity_decode($this->url->link('checkout/cart', 'duplicated=true')); ?>";	
								}
								if (json['success']) {
									window.location = "<?php echo html_entity_decode($this->url->link('checkout/cart')); ?>";	
								}
							}
						});
			<?php } ?>
        });
		
		});
        </script>
        

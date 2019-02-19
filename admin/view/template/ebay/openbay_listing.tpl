<?php echo $header; ?>
<style>
    .ebaySpecificTitle{
        font-weight:bold;
    }
    .ebaySpecificSelect{
        margin-right:10px;
        width:200px;
    }
    .ebaySpecificInput{
        margin-left:0px;
        width:195px;
    }
    .ebaySpecificOther{
        width:190px;
    }
    .ebaySpecificSpan{
        display:none;
    }

    .attention, .warning, .success{
        margin-bottom:0px;
    }
</style>

<div id="content">
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
      
    <div class="heading">
      <h1><img src="view/image/information.png" alt="" /> <?php echo $lang_page_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $lang_cancel; ?></span></a></div>
    </div>
      
    <div class="content" id="mainForm">
        
        <div id="tabs" class="htabs"> 
            <a href="#tab-listing-general"><?php echo $lang_tab_general; ?></a>
            <a href="#tab-listing-description"><?php echo $lang_tab_description; ?></a>
            <a href="#tab-listing-images"><?php echo $lang_tab_images; ?></a>
            <a href="#tab-listing-price"><?php echo $lang_tab_price; ?></a>
            <a href="#tab-listing-payment"><?php echo $lang_tab_payment; ?></a>
            <a href="#tab-listing-shipping"><?php echo $lang_tab_shipping; ?></a>
            <a href="#tab-listing-returns"><?php echo $lang_tab_returns; ?></a>
        </div>
        
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
        <input type="hidden" name="auction_type" value="FixedPriceItem" />
        <div id="tab-listing-general">
        
        <table class="form">
          <?php if($product['store_cats'] != false) { ?> 
          <tr>
              <td>Shop Category</td>
              <td>
                  <?php
                    echo'<select name="eBayStoreCatId" id="eBayStoreCatId">';
                        foreach($product['store_cats'] as $key => $cat){
                            echo'<option value="'.$key.'">'.$cat.'</option>';
                        }
                    echo'</select>';
                  ?>
              </td>
          </tr>
          <?php } ?>
          <tr>
              <td><?php echo $lang_category_suggested; ?><span class="help"><?php echo $lang_category_suggested_help; ?></span></td>
              <td>
                <p id="suggestedLoading" style="display:none;"><img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageLoadingSuggestedLoading"/> <?php echo $lang_category_suggested_check; ?></p>
                <div id="suggested_cats"></div>
              </td>
          </tr>
          <tr>
              <td><?php echo $lang_category_popular; ?><span class="help"><?php echo $lang_category_popular_help; ?></span></td>
              <td>
                  <?php
                    echo'<p><input type="radio" name="popular" value="" id="popular_default" checked /> <strong>'.$lang_none.'</strong></p>';
                    foreach($product['popular_cats'] as $cat){
                        echo'<p><input type="radio" name="popular" value="'.$cat['CategoryID'].'" class="popular_category" /> '.$cat['breadcrumb'].'</p>';
                    }
                  ?>
              </td>
          </tr>
          <tr>
            <td><?php echo $lang_category; ?></td>
            <td id="catSelectBox">
                <div id="cSelections">
                    <select id="catsSelect1" onchange="loadCategories(2);"></select>
                    <select id="catsSelect2" style="display:none; margin-left:10px;" onchange="loadCategories(3);"></select>
                    <select id="catsSelect3" style="display:none; margin-left:10px;" onchange="loadCategories(4);"></select>
                    <select id="catsSelect4" style="display:none; margin-left:10px;" onchange="loadCategories(5);"></select>
                    <select id="catsSelect5" style="display:none; margin-left:10px;" onchange="loadCategories(6);"></select>
                    <select id="catsSelect6" style="display:none; margin-left:10px;" onchange="loadCategories(7);"></select>
                    <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageLoading" style="display:none;" />
                </div>
                <input type="hidden" name="finalCat" id="finalCat" />
            </td>

            </tr>
            <tr id="conditionContainer" style="display:none;">
                <td><?php echo $lang_listing_condition; ?></td>
                <td>
                    <select name="condition" id="conditionRow" style="display:none; width:200px;"></select>
                    <img id="conditionLoading" src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" />
                </td>
            </tr>

            <tr id="durationContainer" style="display:none;">
                <td><?php echo $lang_listing_duration; ?></td>
                <td>
                    <select name="auction_duration" id="durationRow" style="display:none; width:200px;"></select>
                    <img id="durationLoading" src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" />
                </td>
            </tr>
            <tr id="featureContainer" style="display:none;">
                <td style="vertical-align:top; padding-top:15px;"><?php echo $lang_category_features; ?></td>
                <td>
                    <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="featLoading" style="display:none;" />
                    <table class="form" id="featureRow"></table>
                </td>
            </tr>
        </table>
        </div>
        
        <div id="tab-listing-description">
            <table class="form">
                <tr>
                    <td><?php echo $lang_title; ?></td>
                    <td><div id="name_highlight"><input type="text" name="name" value="<?php echo $product['name']; ?>" size="85" id="name" /> <span id="name_highlight_msg" style="display:none;"> <?php echo $lang_title_error; ?></span></div></td>
                </tr>
                <tr>
                    <td><?php echo $lang_subtitle; ?></td>
                    <td><div id="sub_name_highlight"><input type="text" id="sub_name" name="sub_name" value="" size="85" /> <span id="sub_name_highlight_msg" style="display:none;"> <?php echo $lang_subtitle_help; ?></span></div></td>
                </tr>
                <tr>
                    <td><?php echo $lang_template; ?><span class="help"><a href="http://shop.openbaypro.com/opencart_design_services/opencart_theme_design/ebay_html_template_openbay_pro" target="_BLANK"><?php echo $lang_template_link; ?></a></span></td>
                    <td>
                        <?php 
                        $i = 0;
                        foreach($product['templates'] as $template){
                            echo'<p><input type="radio" name="template" value="'.$template.'"'; 

                            if($template == $product['defaults']['ebay_template']){ echo 'checked="checked"';}

                            echo' /> '.$template;

                            if($template != 'None'){ echo ' [ <a href="'.HTTP_CATALOG.'catalog/view/theme/default/ebay/'.$template.'/index.html" target="_BLANK">'.$lang_preview.'</a> ]';}

                            echo'</p>';
                            $i++;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_description; ?></td>
                    <td><textarea name="description" id="descriptionField"><?php echo $product['description']; ?></textarea></td>
                </tr>
            </table>
       </div>
        
        <div id="tab-listing-images">
            <table class="form">
                <tr>
                    <td>
                        <?php if(!empty($product['product_images'])){ ?>
                        <p>* <?php echo $lang_images_text_1; ?></p>
                        <p>* <?php echo $lang_images_text_2; ?></p>
            
                        <table class="form">
                            <tr>
                                <td style="border:none;"><?php echo $lang_image_gallery; ?></td>
                                <td style="border:none;">
                                    <input type="text" name="gallery_height" value="<?php echo $product['defaults']['gallery_height']; ?>" id="gallery_height" />h&nbsp;
                                    <input type="text" name="gallery_width" value="<?php echo $product['defaults']['gallery_width']; ?>" id="gallery_width" />w
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;"><?php echo $lang_image_thumb; ?></td>
                                <td style="border:none;">
                                    <input type="text" name="thumb_height" value="<?php echo $product['defaults']['thumb_height']; ?>" id="thumb_height" />h&nbsp;
                                    <input type="text" name="thumb_width" value="<?php echo $product['defaults']['thumb_width']; ?>" id="thumb_width" />w
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;"><?php echo $lang_images_supersize; ?></td>
                                <td style="border:none;">
                                    <input type="hidden" name="gallery_super" value="0" />
                                    <input type="checkbox" name="gallery_super" value="1" id="gallery_super" />
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;"><?php echo $lang_images_gallery_plus; ?></td>
                                <td style="border:none;">
                                    <input type="hidden" name="gallery_plus" value="0" />
                                    <input type="checkbox" name="gallery_plus" value="1" id="gallery_plus" />
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;"><?php echo $lang_gallery_select_all; ?></td>
                                <td style="border:none;">
                                    <p><input type="checkbox" name="allTemplateImages" value="1" id="allTemplateImages" style="margin-top:2px;" /> <?php echo $lang_template_images; ?></p>
                                    <p><input type="checkbox" name="allEbayImages" value="1" id="allEbayImages" style="margin-top:2px;" /> <?php echo $lang_ebay_images; ?></p>
                                </td>
                            </tr>
                        </table>
                            <?php
                            $i = 0;
                            foreach($product['product_images'] as $img){
                                echo'<div style="border:1px solid #898989; padding:10px; margin-bottom:10px; width:120px; float:left; margin-right:10px;">
                                <img src="'.$img['preview'].'" />
                                <p><input type="checkbox" id="imgUrl'.$i.'" name="img_tpl['.$i.']" value="'.$img['image'].'" class="checkboxTemplateImage" onchange="toggleTplImage('.$i.')" /> '.$lang_template_image.'</p>
                                <p class="imgTag" id="imgTag'.$i.'" style="display:none;">Tag: {imageurl_'.$i.'}</p>
                                <p>
                                    <input type="hidden" name="img['.$i.']" value="null" />
                                    <input type="checkbox" class="checkboxEbayImage" onchange="toggleRad('.$i.')" id="imgChk'.$i.'" name="img['.$i.']" value="'.$img['image'].'" '.( ($i == 0) ? 'checked="checked" ' : '').'/> '.$lang_image_ebay.'
                                </p>
                                <p id="imgRad'.$i.'"'.( ($i == 0) ? '' : 'style="display:none;"').'><input type="radio" name="main_image" '; if($i == 0){ echo 'checked="checked"';} echo ' value="'.$i.'"/> '.$lang_main_image_ebay.'</p>
                                </div>';
                                $i++;
                            }
                        }else{
                            echo'<p>'.$lang_images_none.'</p>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tab-listing-price">
            <table class="form">
                
<?php 
                if(!empty($addon['openstock']) && $addon['openstock'] == true && !empty($product['options']))
                {
?>
                    <tr>
                        <td><?php echo $lang_stock_matrix; ?></td>
                        <td>
                            <table class="list" style="margin-bottom:0px;">
                                <thead>
                                    <tr>
                                        <td class="center"><?php echo $lang_stock_col_qty; ?></td>
                                        <td class="left"><?php echo $lang_stock_col_comb; ?></td>
                                        <td class="left">Price ex tax</td>
                                        <td class="left">Price inc tax</td>
                                        <td class="center"><?php echo $lang_stock_col_enabled; ?></td>
                                    </tr>
                                </thead>
                                <tbody>
<?php
                                // create the option group array
                                $t = array();
                                $t_rel = array();
                                
                                foreach($product['option_grp'] as $key => $grp)
                                {
                                    $t_tmp = array();
                                    foreach($grp['product_option_value'] as $grp_node)
                                    {
                                        $t_tmp[$grp_node['option_value_id']] = $grp_node['name'];
                                        
                                        $t_rel[$grp_node['product_option_value_id']] = $grp['name'];
                                    }
                                    
                                    $t[] = array('name' => $grp['name'], 'child' => $t_tmp);
                                }
                                
                                echo'<input type="hidden" name="optGroupArray" value="'.  base64_encode(serialize($t)).'" />';
                                echo'<input type="hidden" name="optGroupRelArray" value="'.  base64_encode(serialize($t_rel)).'" />';

                                    $v = 0;
                                    foreach($product['options'] as $option)
                                    {
                                        if($v == 0)
                                        {
                                            //create a php version of the opt array to use on server side
                                            echo'<input type="hidden" name="optArray" value="'.  base64_encode(serialize($option['opts'])).'" />';
                                        }
                                        
                                        echo'<input type="hidden" name="opt['.$v.'][sku]" value="'.$option['var'].'" />';
                                        echo'<input type="hidden" name="opt['.$v.'][qty]" value="'.$option['stock'].'" />';

                                        echo'<input type="hidden" name="opt['.$v.'][active]" value="';
                                        if($option['active'] == 1) {  echo '1'; }else{ echo '0'; }
                                        echo '" />';

                                        if($option['price'] == 0){
                                            $option['price'] = $product['price'];
                                        }

                                        echo'<tr>';
                                            echo'<input type="hidden" name="varPriceExCount" class="varPriceExCount" value="'.$v.'" />';
                                            echo'<td class="center" style="width:100px;'; if($option['stock'] < 1){ echo' background-color: #CC9933;';} echo'">'.$option['stock'].'</td>';
                                            echo'<td class="left">'.$option['combi'].'</td>';
                                            echo'<td class="left" style="width:100px;"><input id="varPriceEx_'.$v.'" onkeyup="updateVarPriceFromEx('.$v.');" type="text" name="opt['.$v.'][priceex]" value="'.number_format($option['price'], 2).'" style="width:80px;" /></td>';
                                            echo'<td class="left" style="width:100px;"><input class="varPriceInc" id="varPriceInc_'.$v.'" onkeyup="updateVarPriceFromInc('.$v.');"  type="text" name="opt['.$v.'][price]" value="0" style="width:80px;" /></td>';
                                            echo'<td class="center" style="width:100px;'; if($option['active'] != 1){ echo' background-color: #CC9933;';} echo'">'; if($option['active'] == 1){ echo $lang_yes; }else{ echo $lang_no; } echo '</td>';
                                        echo'</tr>';
                                        
                                        echo'<tr><td colspan="4" class="optSpecifics" id="optSpecifics'.$v.'">';
                                        
                                        echo'</td></tr>';
                                        $v++;
                                    }
?>
                                </tbody>
                            </table>
                        </td>
                    </tr>    
                    <tr>
                        <td><?php echo $lang_tax_inc; ?></td>
                        <td><input type="text" name="tax" id="taxRate" onkeyup="updateVarPrice();" value="<?php echo $product['defaults']['tax']; ?>" style="width:75px; text-align:right;" /> %</td>
                    </tr>        
                    <?php }else{ ?>
                    <tr>
                        <td><?php echo $lang_qty; ?></td>
                        <td><input type="hidden" name="qty[0]" value="<?php echo $product['quantity']; ?>" id="qty" /><?php echo $product['quantity']; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $lang_price_ex_tax; ?> <span class="help"><?php echo $lang_price_ex_tax_help; ?></span></td>
                        <td><p><input type="text" name="price_no_tax[0]" id="taxEx" value="<?php echo number_format($product['price'], 2); ?>" style="width:75px; text-align:right;" onkeyup="updatePriceFromEx();" /></p></td>
                    </tr>
                    </tr>
                    <tr>
                        <td><?php echo $lang_price_inc_tax; ?> <span class="help"><?php echo $lang_price_inc_tax_help; ?></span></td>
                        <td><p><input type="text" name="price[0]" value="0" id="taxInc" style="width:75px; text-align:right;" onkeyup="updatePriceFromInc();" /></p></td>
                    </tr>
                    <tr>
                        <td><?php echo $lang_tax_inc; ?></td>
                        <td><input type="text" name="tax" id="taxRate" onkeyup="updatePriceFromEx();" value="<?php echo $product['defaults']['tax']; ?>" style="width:75px; text-align:right;" /> %</td>
                    </tr>
                <?php }
                if(empty($product['options'])) { ?>
                <tr>
                    <td><?php echo $lang_offers; ?></td>
                    <td>
                        <p><input type="radio" name="bestoffer" value="0" checked/> <?php echo $lang_no; ?></p>
                        <p><input type="radio" name="bestoffer" value="1" /> <?php echo $lang_yes; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td><?php echo $lang_private; ?></td>
                    <td>
                        <p><input type="radio" name="private_listing" value="0" checked/> <?php echo $lang_no; ?></p>
                        <p><input type="radio" name="private_listing" value="1" /> <?php echo $lang_yes; ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tab-listing-payment">
            <table class="form">
                <tr>
                    <td><?php echo $lang_imediate_payment; ?></td>
                    <td>
                        <p><input type="radio" name="payment_immediate" value="0" <?php if($product['defaults']['payment_immediate'] != 1){ echo'checked '; } ?>/> <?php echo $lang_no; ?></p>
                        <p><input type="radio" name="payment_immediate" value="1" <?php if($product['defaults']['payment_immediate'] == 1){ echo'checked '; } ?>/> <?php echo $lang_yes; ?></p>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_payment; ?></td>
                    <td>
<?php
                    foreach($product['payments'] as $payment){
                        echo'<p><input type="checkbox" name="payments['.$payment['ebay_name'].']" value="1"';

                        if($product['defaults']['ebay_payment_types'][$payment['ebay_name']] == 1) { echo ' checked="checked"'; }

                        echo' /> '.$payment['local_name'].'</p>';
                        
                        if($payment['ebay_name'] == 'PayPal')
                        {
                            echo'<p><strong>'.$lang_payment_pp_email.'</strong>&nbsp;<input type="text" name="paypal_email" size="46" value="'.$product['defaults']['paypal_address'].'" /></p>';
                        }
                    }
?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_payment_instruction; ?></td>
                    <td><textarea name="payment_instruction" style="width:400px; height:100px;"><?php echo $product['defaults']['payment_instruction']; ?></textarea></td>
                </tr>
            </table>
        </div>
        
        <div id="tab-listing-shipping">
            <table class="form">
                <tr>
                    <td><?php echo $lang_profile_load; ?></td>
                    <td>
                        <select name="profile_shipping" id="profile_shipping">
                            <option value="def">Select</option>
                            <?php foreach($product['profiles_shipping'] as $profile) { ?>
                                <?php echo '<option value="'.$profile['ebay_profile_id'].'">'.$profile['name'].'</option>'; ?>
                            <?php } ?>
                        </select>
                        <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="profileShippingLoading" style="display:none;" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_item_postcode; ?></td>
                    <td>
                        <input type="text" name="location" id="location" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_despatch_time; ?></td>
                    <td>
                        <select name="dispatch_time" id="dispatch_time">
                            <?php foreach($product['dispatchTimes'] as $dis){ ?>
                                <option value="<?php echo $dis['DispatchTimeMax'];?>"><?php echo $dis['Description'];?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_shipping_in_description; ?></td>
                    <td>
                        <input type="hidden" name="shipping_in_desc" value="0" />
                        <input type="checkbox" name="shipping_in_desc" value="1" id="shipping_in_desc" />
                    </td>
                </tr>
                <tr id="shipping_table_rows">
                    <td colspan="2">
                        <h2 style="border:none;"><?php echo $lang_shipping_national; ?></h2>
                        <div class="attention" style="display:none;" id="maxShippingAlert"><?php echo $lang_shipping_max_national; ?></div>
                        <input type="hidden" name="count_national" value="0" id="count_national" />
                        <div id="nationalBtn">
                        
                        </div>
                        <a class="button" onclick="addShipping('national');"><span><?php echo $lang_add; ?></span></a>

                        <h2 style="border:none;"><?php echo $lang_shipping_international; ?></h2>
                        <input type="hidden" name="count_international" value="0" id="count_international" />
                        <div id="internationalBtn">
                        
                        </div>
                        <a class="button" onclick="addShipping('international');"><span><?php echo $lang_add; ?></span></a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="tab-listing-returns">
            <table class="form">
                <tr>
                    <td><?php echo $lang_profile_load; ?></td>
                    <td>
                        <select name="profile_return" id="profile_return" class="returns_input">
                            <option value="def">Select</option>
                            <?php foreach($product['profiles_returns'] as $profile) { ?>
                                <?php echo '<option value="'.$profile['ebay_profile_id'].'">'.$profile['name'].'</option>'; ?>
                            <?php } ?>
                        </select>
                        <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="profileReturnsLoading" style="display:none;" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_return_accepted; ?></td>
                    <td>
                        <select name="returns_accepted" id="returns_accepted" class="returns_input">
                            <option value="ReturnsNotAccepted"><?php echo $lang_no; ?></option>
                            <option value="ReturnsAccepted"><?php echo $lang_yes; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_return_type; ?></td>
                    <td>
                        <select name="returns_option" id="returns_option" class="returns_input">
                            <option value="MoneyBack"><?php echo $lang_return_type_1; ?></option>
                            <option value="MoneyBackOrExchange"><?php echo $lang_return_type_2; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_return_policy; ?></td>
                    <td><textarea name="return_policy" id="returns_policy" class="returns_input" style="width:400px; height:100px;"></textarea></td>
                </tr>
                <tr>
                    <td><?php echo $lang_return_days; ?></td>
                    <td>
                        <select name="returns_within" id="returns_within" class="returns_input">
                            <option value="Days_14"><?php echo $lang_return_14day; ?></option>
                            <option value="Days_30"><?php echo $lang_return_30day; ?></option>
                            <option value="Days_60"><?php echo $lang_return_60day; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $lang_return_scosts; ?></td>
                    <td>
                        <select name="returns_shipping" id="returns_shipping" class="returns_input">
                            <option value="Buyer"><?php echo $lang_return_buy_pays; ?></option>
                            <option value="Seller"><?php echo $lang_return_seller_pays; ?></option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <table class="form">
            <tr>
                <td align="right" colspan="2">
                    <a onclick="ebayVerify();" class="button" id="reviewButton"><span><?php echo $lang_preview; ?></span></a>
                    <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="reviewButtonLoading" style="display:none;" />
                </td>
            </tr>
        </table>
        
      </form>
    </div>
    <div class="content" id="reviewForm" style="display:none;">
        <table class="form" id="reviewFormTable">
          <tr class="listingFees">
            <td><?php echo $lang_review_costs; ?></td>
            <td id="reviewFormTableCosts"></td>
          </tr>
          <tr class="listingFees">
            <td><?php echo $lang_review_costs_total; ?></td>
            <td id="reviewFormTableCostsTotal"></td>
          </tr>
          <tr>
            <td></td>
            <td align="right">
                <a onclick="goToEdit();" class="button" id="editButton"><span><?php echo $lang_review_edit; ?></span></a>&nbsp;&nbsp;&nbsp;<a onclick="eBaySubmit();" class="button" id="submitListing"><span><?php echo $lang_save; ?></span></a><img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="submitListingLoading" style="display:none;" />
            </td>
          </tr>
          <tr id="previewFrameRow" style="display:none;">
            <td valign="top"><?php echo $lang_review_preview; ?><span class="help"><?php echo $lang_review_preview_help; ?></span></td>
            <td id="previewFrame"></td>
          </tr>
        </table>
    </div>
      <div class="content" id="doneForm" style="display:none;">
          <h2><?php echo $lang_created_title; ?></h2>
          <p><?php echo $lang_created_msg; ?>: <span id="itemNumber"></span></p>
      </div>
      <div class="content" id="failedForm" style="display:none;">
          <h2><?php echo $lang_failed_title; ?></h2>
          <p><?php echo $lang_failed_msg1; ?></p>
          <ul>
              <li><?php echo $lang_failed_li1; ?></li>
              <li><?php echo $lang_failed_li2; ?></li>
              <li><?php echo $lang_failed_li3; ?></li>
          </ul>
          <p><?php echo $lang_failed_contact; ?></p>
      </div>
  </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>

<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>

<script type="text/javascript"><!--
    CKEDITOR.replace('description', {
        filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
        filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
        filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
        filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
        filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
        filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
    });

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }
//--></script>

<script type="text/javascript"><!--
    function image_upload(field, preview) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

        $('#dialog').dialog({
            title: 'Image',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>',
                        type: 'POST',
                        data: 'image=' + encodeURIComponent($('#' + field).attr('value')),
                        dataType: 'text',
                        success: function(data) {
                            $('#' + preview).replaceWith('<img src="' + data + '" alt="" id="' + preview + '" class="image" onclick="image_upload(\'' + field + '\', \'' + preview + '\');" />');
                        }
                    });
                }
            },
            bgiframe: false,
            width: 800,
            height: 400,
            resizable: false,
            modal: false
        });
    };

    function getSuggestedCategories(){
        var qry = $('#name').val();
        $.ajax({
                url: 'index.php?route=ebay/openbay/getSuggestedCategories&token=<?php echo $token; ?>&qry='+qry,
                type: 'GET',
                dataType: 'json',
                beforeSend: function(){$('#suggestedLoading').show();},
                success: function(data) {
                    if(data.error == false)
                    {
                        $('#suggested_cats').empty();

                        var htmlInj = '';

                        if(data.data)
                        {
                            htmlInj += '<p><input type="radio" name="suggested" value="" id="suggested_default" checked="checked"/> <strong><?php echo $lang_none; ?></strong></p>';
                            $.each(data.data, function(key,val){
                                if(val.percent != 0) {
                                    htmlInj += '<p><input type="radio" class="suggested_category" name="suggested" value="'+val.id+'" /> ('+val.percent+'% match) '+val.name+'</p>';
                                }
                            });
                        }

                        $('#suggested_cats').html(htmlInj);

                        $('input[name=suggested]').bind('change', function(){

                            if($(this).val() != '')
                            {
                                categorySuggestedChange($(this).val());
                            }
                        });

                        $('.suggested_category').bind('click', function(){
                            $('#cSelections').hide();
                            $('input[name=popular]').removeAttr('checked');
                            $('#popular_default').prop('checked', true);
                        });

                        $('#suggested_default').bind('click', function(){
                            $('#cSelections').show();
                            $('#featureContainer').hide();
                            $('#featureRow').empty();
                            $('#specifics').empty();
                            $('input[name=popular]').removeAttr('checked');
                            $('#popular_default').prop('checked', 'checked');
                        });
                    }else{
                        alert(data.msg);
                    }

                    $('#suggestedLoading').hide();
                },
                failure: function(){
                    $('#suggestedLoading').hide();
                    alert('<?php echo $lang_ajax_noload; ?>');
                },
                error: function(){
                    $('#suggestedLoading').hide();
                    alert('<?php echo $lang_ajax_noload; ?>');
                }
        });
    }

    function categoryFavChange(id){
        loadCategories(1, true);

        $('input[name=finalCat]').attr('value', id);

        getCategoryFeatures(id);
    }

    function categorySuggestedChange(id){
        loadCategories(1, true);

        $('input[name=finalCat]').attr('value', id);

        getCategoryFeatures(id);
    }

    function loadCategories(level, skip){
        $('#featureContainer').hide();
        $('#featureRow').empty();
        $('#specifics').empty();

        if(level == 1)
        {
            var parent = '';
        }else{
            var prevLevel = level - 1;
            var parent = $('#catsSelect'+prevLevel).val();
            $('#popular_default').attr('checked', true);
        }

        var countI = level;

        while(countI <= 6)
            {
                $('#catsSelect'+countI).hide().empty();
                countI++;
            }

        $.ajax({
                url: 'index.php?route=ebay/openbay/getCategories&token=<?php echo $token; ?>&parent='+parent,
                type: 'GET',
                dataType: 'json',
                beforeSend: function()
                {
                    $('#cSelections').removeClass('success').addClass('attention');
                    $('#imageLoading').show();
                },
                success: function(data) {
                    if(data.items != null)
                    {
                        $('#catsSelect'+level).empty();
                        $('#catsSelect'+level).append('<option value="">-- SELECT --</option>');
                        $.each(data.cats, function(key, val) {
                            if(val.CategoryID != parent)
                            {
                                $('#catsSelect'+level).append('<option value="'+val.CategoryID+'">'+val.CategoryName+'</option>');
                            }
                        });

                        if(skip != true)
                        {
                            $('#finalCat').val('');
                        }

                        $('#catsSelect'+level).show();
                    }else{
                        if(data.error)
                        {
                            alert(data.error);
                            // disable review button
                            $('#reviewButton').hide();

                            $('#content').prepend('<div class="warning"><?php echo $lang_ajax_catproblem; ?></div>');

                            $('#mainForm, .heading').hide();
                        }else{
                            $('#finalCat').val($('#catsSelect'+prevLevel).val());
                            $('#cSelections').removeClass('attention').addClass('success');
                            getCategoryFeatures($('#catsSelect'+prevLevel).val());
                        }
                    }
                    $('#imageLoading').hide();
                }
        });
    }

    function getCategoryFeatures(cat){
        itemFeatures(cat);

        $('#durationRow').hide();
        $('#durationLoading').show();
        $('#durationContainer').show();

        $('#conditionRow').hide();
        $('#conditionLoading').show();
        $('#conditionContainer').show();

        $.ajax({
                url: 'index.php?route=ebay/openbay/getCategoryFeatures&token=<?php echo $token; ?>&category='+cat,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.error == false)
                    {
                        var htmlInj = '';

                        listingDuration(data.data.durations);

                        if(data.data.maxshipping != false){
                            $('#maxShippingAlert').append(data.data.maxshipping).show();
                        }

                        if(data.data.conditions)
                        {
                            $.each(data.data.conditions, function(key, val){
                                htmlInj += '<option value='+val.id+'>'+val.name+'</option>';
                            });

                            $('#conditionRow').empty().html(htmlInj);
                            $('#conditionRow').show();
                            $('#conditionLoading').hide();
                        }
                    }else{
                        alert(data.msg);
                    }
                }
        });
    }

    function listingDuration(data){
        var lang            = new Array();
        var listingDefault  = '<?php echo $product['defaults']['listing_duration']; ?>';

        lang["Days_1"]      = '<?php echo $lang_listing_1day; ?>';
        lang["Days_3"]      = '<?php echo $lang_listing_3day; ?>';
        lang["Days_5"]      = '<?php echo $lang_listing_5day; ?>';
        lang["Days_7"]      = '<?php echo $lang_listing_7day; ?>';
        lang["Days_10"]     = '<?php echo $lang_listing_10day; ?>';
        lang["Days_30"]     = '<?php echo $lang_listing_30day; ?>';
        lang["GTC"]         = '<?php echo $lang_listing_gtc; ?>';

        htmlInj        = '';
        $.each(data, function(key, val){
            htmlInj += '<option value="'+val+'"';
                if(val == listingDefault){ htmlInj += ' selected="selected"';}
            htmlInj += '>'+lang[val]+'</option>';
        });

        $('#durationRow').empty().html(htmlInj);
        $('#durationRow').show();
        $('#durationLoading').hide();
    }

    function itemFeatures(cat){
        $('#featureRow').show();
        $('#featureContainer').show();
        $('#featLoading').show();

        $.ajax({
                url: 'index.php?route=ebay/openbay/getEbayCategorySpecifics&token=<?php echo $token; ?>&category='+cat,
                type: 'GET',
                dataType: 'json',
                beforeSend: function(){},
                success: function(data) {
                    if(data.error == false){
                        $('#featureRow').empty();
                        $('.optSpecifics').empty().hide();

                        var htmlInj = '';
                        var htmlInj2 = '';
                        var specificCount = 0;

                        if(data.data.Recommendations.NameRecommendation){
                            $.each(data.data.Recommendations.NameRecommendation, function(key, val){
                                htmlInj2 = '';

                                if("ValueRecommendation" in val){
                                    htmlInj2 += '<option value="">-- SELECT --</option>';
                                    if($.isArray(val.ValueRecommendation)){
                                        $.each(val.ValueRecommendation, function(key2, option){
                                            htmlInj2 += '<option value="'+option.Value+'">'+option.Value+'</option>';
                                        });
                                    }else{
                                        htmlInj2 += '<option value="'+val.ValueRecommendation.Value+'">'+val.ValueRecommendation.Value+'</option>';
                                    }
                                    htmlInj2 += '<option value="Other"><?php echo $lang_other; ?></option>';

                                    htmlInj += '<tr><td class="ebaySpecificTitle">'+val.Name+'</td><td><select name="feat['+val.Name+']" class="ebaySpecificSelect" id="spec_sel_'+specificCount+'" onchange="toggleSpecOther('+specificCount+');">'+htmlInj2+'</select><span id="spec_'+specificCount+'_other" class="ebaySpecificSpan"><?php echo $lang_other; ?>:&nbsp;<input type="text" name="featother['+val.Name+']" class="ebaySpecificOther" /></span></td></tr>';
                                }else{
                                    htmlInj += '<tr><td class="ebaySpecificTitle">'+val.Name+'</td><td><input type="text" name="feat['+val.Name+']" class="ebaySpecificInput" /></td></tr>';
                                }

                                specificCount++;
                            });

                            $('#featureRow').append(htmlInj);
                        }else{
                            $('#featureRow').text('None');
                        }
                    }else{
                        alert(data.msg);
                    }

                    $('#featLoading').hide();
                }
        });
    }

    function toggleSpecOther(id){
        var selectVal = $('#spec_sel_'+id).val();

        if(selectVal == 'Other'){
            $('#spec_'+id+'_other').show();
        }else{
            $('#spec_'+id+'_other').hide();
        }
    }

    function addShipping(id){
        if(id == 'national'){
            var loc = '0';
        }else{
            var loc = '1';
        }

        var count = $('#count_'+id).val();

        count = parseInt(count);

        $.ajax({
                url: 'index.php?route=ebay/openbay/getShippingService&token=<?php echo $token; ?>&loc='+loc,
                type: 'GET',
                dataType: 'json',
                beforeSend: function(){},
                success: function(data) {
                    html = '';
                html += '<p class="shipping_' + id + '_' + count + '" style="border-top:1px dotted; margin:0; padding:8px 0;"><label><strong><?php echo $lang_shipping_service; ?></strong> <label><select name="service_' + id + '[' + count + ']">';

                $.each(data.svc, function(key, val) {
                    html += '<option value="' + val.ShippingService + '">' + val.description + '</option>';
                });

                html += '</select></p>';

                if(id == 'international'){
                    html += '<h5 style="margin:5px 0;" class="shipping_' + id + '_' + count + '">Ship to zones</h5>';
                    html += '<div style="border:1px solid #000; background-color:#F5F5F5; width:100%; min-height:40px; margin-bottom:10px; display:inline-block;" class="shipping_' + id + '_' + count + '">';
                    html += '<div style="display:inline; float:left; padding:10px 6px;line-height:20px; height:20px;">';
                    html += '<input type="checkbox" name="shipto_international[' + count + '][]" value="Worldwide" /> Worldwide</div>';
                    
                    <?php foreach($data['shipping_international_zones'] as $zone){ ?>
                        html += '<div style="display:inline; float:left; padding:10px 6px;line-height:20px; height:20px;">';
                        html += '<input type="checkbox" name="shipto_international[' + count + '][]" value="<?php echo $zone['shipping_location']; ?>" /> <?php echo $zone['description']; ?></div>';
                    <?php } ?>
                    
                    html += '</div>';
                    html += '<div style="clear:both;" class="shipping_' + id + '_' + count + '"></div>';
                }
                
                
                html += '<p class="shipping_' + id + '_' + count + '"><label><?php echo $lang_shipping_first; ?></label><input type="text" name="price_' + id + '[' + count + ']" style="width:50px;" value="0.00" />';
                html += '&nbsp;&nbsp;<label><?php echo $lang_shipping_add; ?></label><input type="text" name="priceadditional_' + id + '[' + count + ']" style="width:50px;" value="0.00" />&nbsp;&nbsp;<a onclick="removeShipping(\'' + id + '\',\'' + count + '\');" class="button"><span><?php echo $lang_btn_remove; ?></span></a></p>';
                html += '<div style="clear:both;" class="shipping_' + id + '_' + count + '"></div>';    
                    $('#'+id+'Btn').append(html);
                }
        });

        $('#count_'+id).val(count + 1);
    }

    function checkLocExtra(id){
        if($('#'+id).val() == 2){
            $('#'+id+'_extra').show();
        }else{
            $('#'+id+'_extra').hide();
        }
    }

    function removeShipping(id, count){
        $('.shipping_'+id+'_'+count).remove();
    }

    function ebayVerify(){
        CKupdate();

        var err = 0;

        if($('#finalCat').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_cat; ?>');
            return;
        }

        if($('#auction_duration').val() == ''){
            err = 1;
            alert('Select a listing duration');
            return;
        }

        if($('#sku').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_sku; ?>');
            return;
        }

        if($('#name').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_name; ?>');
            return;
        }

        if($('#name').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_name_len; ?>');
            return;
        }

        if($('#location').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_loc; ?>');
            return;
        }

        if($('#dispatch_time').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_time; ?>');
            return;
        }

        if($('#count_national').val() == 0){
            err = 1;
            alert('<?php echo $lang_ajax_error_nat_svc; ?>');
            return;
        }

        if($('#durationRow').val() == ''){
            err = 1;
            alert('<?php echo $lang_ajax_error_duration; ?>');
            return;
        }

<?php
        if(!empty($addon['openstock']) && $addon['openstock'] == true && !empty($product['options'])){
            echo 'var hasOptions = "yes";';
        }else{
            echo 'var hasOptions = "no";';
?>
            if($('#qty').val() < 1){
                err = 1;
                alert('<?php echo $lang_ajax_error_stock; ?>');
                return;
            }
<?php
        }
?>

        if(err == 0){
            $.ajax({
                type:'POST',
                dataType: 'json',
                url: 'index.php?route=ebay/openbay/verify&token=<?php echo $token; ?>&options='+hasOptions,
                data: $("#form").serialize(),
                success: function(data){

                     $('#previewFrame').empty();
                     $('#previewFrameRow').hide();

                    if(data.error != true)
                    {
                        $('#reviewButton').show();
                        $('#reviewButtonLoading').hide();
                        $('#mainForm').hide();

                        if(data.data.Errors)
                        {
                            $.each(data.data.Errors, function(key, val){
                                $('#reviewForm').prepend('<div class="warning" style="margin-bottom:5px;">'+val+'</div>');
                            });
                        }

                        if(data.data.Ack != 'Failure')
                        {
                            var feeTot = parseFloat(0.00);
                            var Cur = '';

                            $.each(data.data.Fees, function(key, val){

                                if(val.Fee != 0.0 && val.Name != 'ListingFee')
                                {
                                    feeTot = feeTot + parseFloat(val.Fee);
                                    $('#reviewFormTableCosts').append(val.Name+' - '+val.Cur+' '+parseFloat(val.Fee).toFixed(2)+'<br />');
                                }

                                Cur = val.Cur;
                            });

                            $('#previewFrame').html('<iframe src="'+data.data.link+'" frameborder="0" height="600" width="100%" style="margin-left:auto; margin-right:auto;" scrolling="auto"></iframe>');
                            $('#previewFrameRow').show();

                            $('#reviewFormTableCostsTotal').html(Cur+' '+feeTot.toFixed(2));
                        }else{
                            $('.listingFees').hide();
                            $('#submitListing').hide();
                        }

                        $('#reviewForm').show();
                    }else{
                        $('#submitListing').hide();
                        $('#reviewButton').show();
                        $('#reviewButtonLoading').hide();
                        $('.listingFees').hide();
                        alert(data.msg);
                    }
                },
                beforeSend: function(){
                    $('#submitListing').show();
                    $('#reviewButton').hide();
                    $('#reviewButtonLoading').show();
                    $('.listingFees').show();
                }
            });
        }
    }

    function eBaySubmit(){
        CKupdate();
    <?php
                    if(!empty($addon['openstock']) && $addon['openstock'] == true && !empty($product['options'])){
                        echo 'var hasOptions = "yes";';
                    }else{
                        echo 'var hasOptions = "no";';
                    }
    ?>
            $.ajax({
                type:'POST',
                dataType: 'json',
                url: 'index.php?route=ebay/openbay/listItem&token=<?php echo $token; ?>&options='+hasOptions,
                data: $("#form").serialize(),
                success: function(data){

                    if(data.error == true){
                        alert(data.msg);

                    }else{
                        if(data.data.Failed == true){
                            $('#submitListing').show();
                            $('#submitListingLoading').hide();
                            $('#reviewForm').hide();
                            $('#failedForm').show();
                        }else{
                            $('#submitListing').show();
                            $('#submitListingLoading').hide();
                            $('#reviewForm').hide();

                            if(data.data.Errors){
                                $.each(data.data.Errors, function(key, val){
                                    $('#failedForm').prepend('<div class="warning">'+val+'</div>');
                                    $('#doneForm').prepend('<div class="warning">'+val+'</div>');
                                });
                            }

                            $('#itemNumber').text(data.data.ItemID);

                            $('#doneForm').show();
                        }
                    }
                },
                beforeSend: function(){
                    $('#submitListing').hide();
                    $('#submitListingLoading').show();
                    $('#editButton').hide();
                }
            });
    }

    function titleLength(){
        if($('#name').val().length > 80){
            $('#name_highlight').addClass('warning');
            $('#name_highlight_msg').show();
        }else{
            $('#name_highlight').removeClass('warning');
            $('#name_highlight_msg').hide();
        }
    }

    function subtitleRefocus(){
        $('#sub_name').focus();
    }

    function goToEdit(){
        $('#reviewFormTableCosts').empty();
        $('#reviewFormTableCostsTotal').empty();
        $('.warning').remove();
        $('#reviewForm').hide();
        $('#mainForm').show();
    }

    function toggleRad(id){
        if ($("#imgChk"+id).is(':checked')) { $("#imgRad"+id).show(); } else { $("#imgRad"+id).hide(); }
    }

    function toggleTplImage(id){
        if ($("#imgUrl"+id).is(':checked')) { $("#imgTag"+id).show(); } else { $("#imgTag"+id).hide(); }
    }

    function updatePrice(){
        var taxEx = $('#taxEx').val();
        var rate = $('#taxRate').val();
        var taxInc = taxEx * ((rate /100)+1);

        $('#taxInc').val(parseFloat(taxInc).toFixed(2));
    }

    function updateVarPrice(){
        var rate = $('#taxRate').val();
        var taxEx = '';
        var id = '';
        var taxInc = '';

        $.each($('.varPriceExCount'), function() {
            id = $(this).val();
            taxEx = $('#varPriceEx_'+id).val();
            taxInc = taxEx * ((rate /100)+1);

            $('#varPriceInc_'+id).val(parseFloat(taxInc).toFixed(2));
        });
    }

    function updateVarPriceFromEx(id){
        var taxEx = $('#varPriceEx_'+id).val();
        var rate = $('#taxRate').val();
        var taxInc = taxEx * ((rate /100)+1);

        $('#varPriceInc_'+id).val(parseFloat(taxInc).toFixed(2));
    }

    function updatePriceFromEx(){
        var taxEx = $('#taxEx').val();
        var rate = $('#taxRate').val();
        var taxInc = taxEx * ((rate /100)+1);

        $('#taxInc').val(parseFloat(taxInc).toFixed(2));
    }

    function updateVarPriceFromInc(id){
        var taxInc = $('#varPriceInc_'+id).val();
        var rate = $('#taxRate').val();
        var taxEx = taxInc / ((rate /100)+1);

        $('#varPriceEx_'+id).val(parseFloat(taxEx).toFixed(2));
    }

    function updatePriceFromInc(){
        var taxInc = $('#taxInc').val();
        var rate = $('#taxRate').val();
        var taxEx = taxInc / ((rate /100)+1);

        $('#taxEx').val(parseFloat(taxEx).toFixed(2));
    }

    $('#popular_default').click(function(){
        $('#cSelections').show();
        $('#featureContainer').hide();
        $('#featureRow').empty();
        $('#specifics').empty();
        $('input[name=suggested]').removeAttr('checked');
        $('#suggested_default').prop('checked', 'checked');
    });

    $('input[name=popular]').bind('change', function(){
        if($(this).val() != '')
        {
            categoryFavChange($(this).val());
        }
    });

    $('#allTemplateImages').bind('change', function(){
        if($('#allTemplateImages').is(':checked')){
            $('.checkboxTemplateImage').prop('checked', 'checked');
            $('.imgTag').show();
        }else{
            $('.checkboxTemplateImage').removeAttr('checked');
            $('.imgTag').hide();
        }
    });

    $('#allEbayImages').bind('change', function(){
        if($('#allEbayImages').is(':checked')){
            $('.checkboxEbayImage').prop('checked', 'checked');
        }else{
            $('.checkboxEbayImage').removeAttr('checked');
        }
    });

    $('#shipping_in_desc').bind('change', function(){
        if($('#shipping_in_desc').is(':checked')){
            $('#shipping_table_rows').hide();
        }else{
            $('#shipping_table_rows').show();
        }
    });

    $('#profile_shipping').change(function(){
        $('#profileShippingLoading').show();

        if($('#profile_shipping').val() != 'def'){
            $.ajax({
                type:'GET',
                dataType: 'json',
                url: 'index.php?route=ebay/openbay/profileGet&token=<?php echo $token; ?>&ebay_profile_id='+$('#profile_shipping').val(),
                success: function(data){ 
                    setTimeout(function(){
                        $('#location').val(data.data.postcode);
                        $('#dispatch_time').val(data.data.dispatch_time);
                        $('#shipping_in_desc').prop('checked', false);
                        if(data.data.shipping_in_desc == 1){
                            $('#shipping_in_desc').prop('checked', true);
                            $('#shipping_table_rows').hide();
                        }else{
                            $('#shipping_in_desc').prop('checked', false);
                            $('#shipping_table_rows').show();
                        }
                        $('#nationalBtn').html(data.html.national);
                        $('#internationalBtn').html(data.html.international);
                        $('#count_international').html(data.html.international_count);
                        $('#count_national').html(data.html.national_count);
                        $('#profileShippingLoading').hide();
                    }, 1000);
                }
            });
        }
    });

    $('#profile_return').change(function(){

        $('#profileReturnsLoading').show();

        if($('#profile_return').val() != 'def'){
            $.ajax({
                type:'GET',
                dataType: 'json',
                url: 'index.php?route=ebay/openbay/profileGet&token=<?php echo $token; ?>&ebay_profile_id='+$('#profile_return').val(),
                success: function(data){ 
                    setTimeout(function(){
                        $('#returns_accepted').val(data.data.returns_accepted);
                        $('#returns_option').val(data.data.returns_option);
                        $('#returns_within').val(data.data.returns_within);
                        $('#returns_policy').val(data.data.returns_policy);
                        $('#returns_shipping').val(data.data.returns_shipping);
                        $('#profileReturnsLoading').hide();
                    }, 1000);
                }
            });
        }
    });

    $('#sub_name').focus(function(){
        $('#sub_name_highlight').addClass('attention');
        $('#sub_name_highlight_msg').show();
    });

    $('#sub_name').focusout(function(){
        setTimeout(function(){
            $('#sub_name_highlight').removeClass('attention');
            $('#sub_name_highlight_msg').hide();
        }, 100);
    });

    $('#name').keyup(function(){
        titleLength();
    });

    $('#name').change(function(){
        titleLength();
    });

    $(document).ready(function() {
        loadCategories(1);
        titleLength();
        getSuggestedCategories();
        updatePrice();
        updateVarPrice();
    });
//--></script>
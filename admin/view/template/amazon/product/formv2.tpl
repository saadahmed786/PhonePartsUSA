<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
 <?php if(!empty($errors)) { ?>
    <div class="warning"><ul>
    <?php foreach($errors as $error) : ?>
            <li><?php echo $error['message']; ?></li>
        
    <?php endforeach; ?>
    </ul></div>
 <?php } ?>
    
  <div class="box">
    
    <div class="heading">
      <h1><?php echo $heading_title; ?></h1>
      <div class="buttons">
          <a id="cancel_button" onclick="location = '<?php echo $cancel_url; ?>';" class="button"><?php echo $cancel_button_text; ?></a>
      </div>
    </div>
    <div class="content"> 
        
        <div id="tabs" class="htabs">
            <a href="#page-quick" id="tab-quick"><?php echo $quick_listing_tab_text ;?></a>
        </div>

        <form method="POST" id="product_form_quick">
            <div id="page-quick">
                <table class="form" align="left">
                    <tr>
                        <td colspan="2"><h2><?php echo $quick_listing_header_text; ?></h2>
                        <p><?php echo $quick_listing_description; ?></p>
                        </td>
                    </tr>
                </table>
                
                <table id="quick_table" class="form" align="left"> 
                    <tbody>
                        <tr>
                            <td style="width: 400px;"><?php echo $listing_row_text; ?></td>
                            <td><a href="a">Product link</a></td>
                        </tr>
                    </tbody>
                    
                   
                    <tbody id="fields_quick"></tbody>
                    <tbody>
                        
                        
                        <!-- Marketplaces -->
                        <tr>
                            <td>
                                <span class="required">* </span>Marketplaces<span class="help">Help text</span>
                            </td>
                            <td>
                                <?php foreach ($marketplaces as $mp) { ?>
                                    <div style="text-align: center; float: left; margin-right: 20px;">
                                        <label for="marketplace_<?php echo $mp['code'] ?>"><?php echo $mp['name'] ?></label>
                                    <input id="marketplace_<?php echo $mp['code'] ?>" <?php if ($mp['default']) { ?> checked="checked" <?php }?> type="checkbox" name="fields_marketplaces[]" value="<?php echo $mp['value'] ?>">
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                        <!-- SKU -->
                        <tr>
                            <td>
                                <span class="required">* </span>SKU<span class="help">Help text</span>
                            </td>
                            <td>
                                <input name="fields[sku]" value="">
                            </td>
                        </tr>
                        <!-- Standard produc id -->
                        <tr>
                            <td>
                                <span class="required">* </span>Standard product ID<span class="help">Help text</span>
                            </td>
                            <td>
                                <input name="fields[product_id]" value="">
                            </td>
                        </tr>
                        <!-- Standard produc id type -->
                        <tr>
                            <td>
                                <span class="required">* </span>Standard product ID type<span class="help">Help text</span>
                            </td>
                            <td>
                                <select name="fields[product_id_type]">
                                    <option></option>
                                    <option>ASIN</option>
                                    <option>ISBN</option>
                                    <option>UPC</option>
                                    <option>EAN</option>
                                </select>
                            </td>
                        </tr>
                        <!-- Condition type -->
                        <tr>
                            <td>
                                <span class="required">* </span>Condition<span class="help">Help text</span>
                            </td>
                            <td>
                                <select name="fields[condition]">
                                   <option></option>
                                   <option value="New">New</option>
                                   <option value="UsedLikeNew">Used, Like New</option>
                                   <option value="UsedVeryGood">Used, Very Good</option>
                                   <option value="UsedGood">Used, Good</option>
                                   <option value="UsedAcceptable">Used Acceptable</option>
                                   <option value="CollectibleLikeNew">Collectible, Like New</option>
                                   <option value="CollectibleVeryGood">Collectible, Very Good</option>
                                   <option value="CollectibleGood">Collectible, Good</option>
                                   <option value="CollectibleAcceptable">Collectible, Acceptable</option>
                                   <option value="Refurbished">Refurbished</option>
                                   <option value="Club">Club</option>
                                </select>
                            </td>
                        </tr>
                        <!-- Condition note -->
                        <tr>
                            <td>
                                Condition note<span class="help">Help text</span>
                            </td>
                            <td>
                                <textarea rows="5" cols="60" name="fields[ConditionNote]"></textarea>
                            </td>
                        </tr>
                        <!-- Price -->
                        <tr>
                            <td>
                                <span class="required">* </span>Price<span class="help">Help text</span>
                            </td>
                            <td>
                                <input name="fields[price]" value="">
                            </td>
                        </tr>
                        <!-- Currency -->
                        <tr>
                            <td>
                                <span class="required">* </span>Currecny<span class="help">Help text</span>
                            </td>
                            <td>
                                <select name="fields[currency]">
                                    <option></option>
                                    <option>GBP</option>
                                </select>
                            </td>
                        </tr>
                         <!-- Quantity -->
                        <tr>
                            <td>
                                <span class="required">* </span>Quantity<span class="help">Help text</span>
                            </td>
                            <td>
                                <input name="fields[quantity]" value="">
                            </td>
                        </tr>
                        
                        <?php print_r($product_images) ?>
                        
                        
                        <tr>
                            <td>
                                <div class="buttons">
                                    <a id="save_button" onclick="validate_and_save('quick')" class="button">Upload</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                  
                </table>
            </div>
        </form>

        
        
    </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#tabs a').tabs();
    });
</script>

<?php echo $footer; ?>
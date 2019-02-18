<?php echo $header; ?>

<div id="content">

  <div class="box">
      
    <div class="heading">
      <h1><img src="view/image/information.png" alt="" /> Create quick item</h1>
    </div>
      
    <div class="content" id="mainForm">

        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
        <input type="hidden" name="auction_type" value="FixedPriceItem" />
        
        <table class="form">
            <tr>
                <td>ID Type</td>
                <td>
                    <select name="type" id="pType">
                        <option value="ISBN">ISBN (10 or 13)</option>
                        <option value="EAN">EAN</option>
                        <option value="UPC">UPC</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>ID</td>
                <td>
                    <input type="text" name="code" id="pCode" />
                </td>
            </tr>
            <tr>
                <td colspan="2"><a onclick="" class="button" id="lookUp"><span>Find</span></a></td>
            </tr>
        </table>

        <table class="form" style="display:none;">
            <tr>
                <td>Name</td>
                <td id="pName"></td>
            </tr>
            <tr>
                <td>Name</td>
                <td id="pName"></td>
            </tr>
        </table>
    </div>
  </div>
</div>
<?php echo $footer; ?>
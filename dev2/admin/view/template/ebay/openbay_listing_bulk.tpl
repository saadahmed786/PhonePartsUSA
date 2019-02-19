<?php echo $header; ?>

<div id="content">

  <div class="box">

    <div class="heading">
      <h1><?php echo $lang_page_title; ?></h1>
    </div>

      <table class="list">
          <tbody>
          <tr>
              <td>
            <?php $i = 0; if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                  <input type="hidden" name="product[<?php echo $i; ?>]" value="<?php echo $i; ?>" />

          <table  style="margin:10px 0; width:100%; border: 1px solid #DDDDDD; border-bottom: 0px;" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="center" style="width:100px;"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" /></td>
                    <td class="left" style="width:500px;">
                        <p><input type="text" name="name_<?php echo $i; ?>" id="name_<?php echo $i; ?>" value="<?php echo $product['name']; ?>" style="width:480px;"/></p>
                        <p><label style="display:inline-block; width:75px; font-weight:bold; margin-right:10px;">Price:</label><input type="text" name="price_<?php echo $i; ?>" value="<?php echo number_format($product['price']*$default['defaults']['tax'], 2); ?>" style="width:50px;"/></p>
                        <p><label style="display:inline-block; width:75px; font-weight:bold; margin-right:10px;">Stock:</label><?php echo $product['quantity']; ?></p>
                    </td>
                    <td>
                        <p id="conditionContainer_<?php echo $i; ?>" style="display:none;">
                            <label style="display:inline-block; width:75px; font-weight:bold; margin-right:10px;">Condition: </label>
                            <select name="condition_<?php echo $i; ?>" id="conditionRow_<?php echo $i; ?>" style="display:none; width:200px;"></select>
                            <img id="conditionLoading_<?php echo $i; ?>" src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" />
                        </p>
                        <p id="durationContainer_<?php echo $i; ?>" style="display:none;">
                            <label style="display:inline-block; width:75px; font-weight:bold; margin-right:10px;">Duration: </label>
                            <select name="auction_duration_<?php echo $i; ?>" id="durationRow_<?php echo $i; ?>" style="display:none; width:200px;"></select>
                            <img id="durationLoading_<?php echo $i; ?>" src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" />
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding:5px;">
                        <p style="font-weight:bold; margin:0px;">Category: <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="loadingSuggestedCat_<?php echo $i; ?>" /></p>

                        <div style="float:left; padding-left:10px;" id="suggestedCat_<?php echo $i; ?>"></div>

                        <div style="clear:both;"></div>

                        <div id="cSelections_<?php echo $i; ?>" style="float:left; padding-left:30px; display:none; margin-top:10px;">
                            <select id="catsSelect1_<?php echo $i; ?>" onchange="loadCategories(2, false, <?php echo $i; ?>);"></select>
                            <select id="catsSelect2_<?php echo $i; ?>" style="display:none; margin-left:10px;" onchange="loadCategories(3, false, <?php echo $i; ?>);"></select>
                            <select id="catsSelect3_<?php echo $i; ?>" style="display:none; margin-left:10px;" onchange="loadCategories(4, false, <?php echo $i; ?>);"></select>
                            <select id="catsSelect4_<?php echo $i; ?>" style="display:none; margin-left:10px;" onchange="loadCategories(5, false, <?php echo $i; ?>);"></select>
                            <select id="catsSelect5_<?php echo $i; ?>" style="display:none; margin-left:10px;" onchange="loadCategories(6, false, <?php echo $i; ?>);"></select>
                            <select id="catsSelect6_<?php echo $i; ?>" style="display:none; margin-left:10px;" onchange="loadCategories(7, false, <?php echo $i; ?>);"></select>
                            <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageLoading_<?php echo $i; ?>" style="display:none;" />
                        </div>

                        <input type="hidden" name="finalCat_<?php echo $i; ?>" id="finalCat_<?php echo $i; ?>" />
                    </td>
                </tr>
          </table>
                <?php $i++; } ?>
            <?php } else { ?>
            <tr>
                <td class="center" colspan="3"><?php echo $text_no_results; ?></td>
            </tr>
            </table>
            <?php } ?>
          </td></tr>
      </tbody>
      </table>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        <?php $j = 0; while($j <= $i){ ?>
            getSuggestedCategories('<?php echo (int)$j; ?>');
        <?php $j++; } ?>
    });

    function useManualCategory(id){
        loadCategories(1, true, id);

        $('#cSelections_'+id).show();
    }

    function getSuggestedCategories(id){
        var qry = $('#name_'+id).val();

        $.ajax({
            url: 'index.php?route=ebay/openbay/getSuggestedCategories&token=<?php echo $token; ?>&qry='+qry,
            type: 'GET',
            dataType: 'json',
            beforeSend: function(){ $('#loadingSuggestedCat_'+id).show(); },
            success: function(data) {
                if(data.error == false)
                {
                    $('#suggestedCat_'+id).empty();

                    var htmlInj = '';

                    if(data.data)
                    {
                        $.each(data.data, function(key,val){
                            if(val.percent != 0) {
                                htmlInj += '<p style="margin:0px; padding:0 0 0 10px;"><input type="radio" id="suggested_category_'+id+'" name="suggested_'+id+'" value="'+val.id+'" onchange="categorySuggestedChange('+val.id+','+id+')" /> ('+val.percent+'% match) '+val.name+'</p>';
                            }
                        });

                        htmlInj += '<p style="margin:0px; padding:0 0 0 10px;"><input type="radio" id="manual_use_category_'+id+'" name="suggested_'+id+'" value="" onchange="useManualCategory('+id+')" /> Choose category</p>';

                        $('#suggestedCat_'+id).html(htmlInj);
                    }
                }

                $('#loadingSuggestedCat_'+id).hide();
            },
            failure: function(){
                $('#loadingSuggestedCat_'+id).hide();
            },
            error: function(){
                $('#loadingSuggestedCat_'+id).hide();
            }
        });
    }

    function loadCategories(level, skip, id){
        if(level == 1)
        {
            var parent = '';
        }else{
            var prevLevel = level - 1;
            var parent = $('#catsSelect'+prevLevel+'_'+id).val();
        }

        var countI = level;

        while(countI <= 6)
        {
            $('#catsSelect'+countI+'_'+id).hide().empty();
            countI++;
        }

        $.ajax({
            url: 'index.php?route=ebay/openbay/getCategories&token=<?php echo $token; ?>&parent='+parent,
            type: 'GET',
            dataType: 'json',
            beforeSend: function()
            {
                $('#cSelections_'+id).removeClass('success').addClass('attention');
                $('#imageLoading_'+id).show();
            },
            success: function(data) {
                if(data.items != null)
                {
                    $('#catsSelect'+level+'_'+id).empty();
                    $('#catsSelect'+level+'_'+id).append('<option value="">-- SELECT --</option>');
                    $.each(data.cats, function(key, val) {
                        if(val.CategoryID != parent)
                        {
                            $('#catsSelect'+level+'_'+id).append('<option value="'+val.CategoryID+'">'+val.CategoryName+'</option>');
                        }
                    });

                    if(skip != true)
                    {
                        $('#finalCat_'+id).val('');
                    }

                    $('#catsSelect'+level+'_'+id).show();
                }else{
                    if(data.error)
                    {

                    }else{
                        $('#finalCat_'+id).val($('#catsSelect'+prevLevel+'_'+id).val());
                        $('#cSelections_'+id).removeClass('attention').addClass('success');
                        getCategoryFeatures($('#catsSelect'+prevLevel+'_'+id).val(), id);
                    }
                }
                $('#imageLoading_'+id).hide();
            }
        });
    }

    function getCategoryFeatures(cat, id){
        $('#durationRow_'+id).hide();
        $('#durationLoading_'+id).show();
        $('#durationContainer_'+id).show();

        $('#conditionRow_'+id).hide();
        $('#conditionLoading_'+id).show();
        $('#conditionContainer_'+id).show();

        $.ajax({
            url: 'index.php?route=ebay/openbay/getCategoryFeatures&token=<?php echo $token; ?>&category='+cat,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if(data.error == false)
                {
                    var htmlInj = '';

                    listingDuration(data.data.durations, id);

                    if(data.data.maxshipping != false){

                    }

                    if(data.data.conditions)
                    {
                        $.each(data.data.conditions, function(key, val){
                            htmlInj += '<option value='+val.id+'>'+val.name+'</option>';
                        });

                        if(htmlInj == ''){
                            $('#conditionRow_'+id).empty();
                            $('#conditionContainer_'+id).hide();
                            $('#conditionRow_'+id).hide();
                            $('#conditionLoading_'+id).hide();
                        }else{
                            $('#conditionRow_'+id).empty().html(htmlInj);
                            $('#conditionRow_'+id).show();
                            $('#conditionLoading_'+id).hide();
                        }
                    }
                }else{
                    alert(data.msg);
                }
            }
        });
    }

    function listingDuration(data, id){
        var lang            = new Array();
        var listingDefault  = '<?php echo $product['defaults']['listing_duration']; ?>';

        lang["Days_1"]      = '1 Day';
        lang["Days_3"]      = '3 Days';
        lang["Days_5"]      = '5 Days';
        lang["Days_7"]      = '7 Days';
        lang["Days_10"]     = '10 Days';
        lang["Days_30"]     = '30 Days';
        lang["GTC"]         = 'GTC';

        htmlInj        = '';
        $.each(data, function(key, val){
            htmlInj += '<option value="'+val+'"';
            if(val == listingDefault){ htmlInj += ' selected="selected"';}
            htmlInj += '>'+lang[val]+'</option>';
        });

        $('#durationRow_'+id).empty().html(htmlInj);
        $('#durationRow_'+id).show();
        $('#durationLoading_'+id).hide();
    }

    function categorySuggestedChange(val, id){
        $('#cSelections_'+id).hide();

        loadCategories(1, true, id);

        $('input[name=finalCat]').attr('value', val);

        getCategoryFeatures(val, id);
    }
</script>
<?php echo $footer; ?>
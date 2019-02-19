<table class="form">
    <tbody>
        <tr>
            <td colspan="2"><h2><?php echo $links_header_text; ?></h2>
            
                <p><?php echo $links_desc1_text; ?></p>
                <p><?php echo $links_desc2_text; ?></p>
                <a class="button" onclick="loadUnlinked(this)" ><?php echo $links_load_btn_text; ?></a>
                
            </td>
        </tr>
    </tbody>
</table>

<table align="left" class="list" id="linkListTable">
    <thead id="tableThread1">
        <tr>
            <td class="center" colspan="3"><?php echo $links_new_link_text; ?></td>
        </tr>
    </thead>
    <thead id="tableThread2">
        <tr>
            <td class="right" width="45%"><?php echo $links_autocomplete_product_text; ?></td>
            <td class="left" width="45%"><?php echo $links_amazon_sku_text; ?></td>
            <td class="center" width="10%"><?php echo $links_action_text; ?></td>
        </tr>
    </thead>
    <tbody id="tableBody">
        <tr>
            <td class="right">
                <input id="newProduct" type="text">
                <input type="hidden" id="newProductId">
            </td>
            <td>
                <input id="newAmazonSku" type="text">
            </td>
            <td class="center">
                <a class="button" id="addNewButton" onclick="addNewLinkAutocomplete()" ><?php echo $links_add_text; ?></a>
            </td>
        </tr>
    </tbody>
</table>


<table align="left" class="list" id="linkListTable">
    <thead>
        <tr>
            <td class="center" colspan="5"><?php echo $links_linked_items_text; ?></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td width="22.5%"><?php echo $links_name_text; ?></td>
            <td width="22.5%"><?php echo $links_model_text; ?></td>
            <td width="22.5%"><?php echo $links_sku_text; ?></td>
            <td width="22.5%"><?php echo $links_amazon_sku_text; ?></td>
            <td class="center" width="10%"><?php echo $links_action_text; ?></td>
        </tr>
    </thead>
    <tbody id="linkedItems">
    </tbody>
</table>


<script type="text/javascript"><!--

$(document).ready(function() {
   loadLinks();
});

function loadLinks() {
    $.ajax({
            url: '<?php echo html_entity_decode($loadLinks); ?>',
            type: 'get',
            dataType: 'json',
            data: 'product_id=' + encodeURIComponent($('#newProductId').val()) + '&amazon_sku=' + encodeURIComponent($('#newAmazonSku').val()),
            success: function(json) {
                
                var rows = '';
                for(i in json) {
                    rows += '<tr>';
                    rows += '<td class="left">' + json[i]['product_name'] + '</td>';
                    rows += '<td class="left">' + json[i]['model'] + '</td>';
                    rows += '<td class="left">' + json[i]['sku'] + '</td>';
                    rows += '<td class="left">' + json[i]['amazon_sku'] + '</td>';
                    rows += '<td class="center"><a class="button" onclick="removeLink(this, \'' + json[i]['amazon_sku'] + '\')" ><?php echo $links_remove_text; ?></a></td>';
                    rows += '</tr>';
                }
                
                 $('#linkedItems').html(rows);  
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
    });	
}

function loadUnlinked(button) {
    $.ajax({
            url: '<?php echo html_entity_decode($loadUnlinked); ?>',
            type: 'get',
            dataType: 'json',
            beforeSend: function() {
               $(button).after('<span class="wait"><img src="view/image/loading.gif" alt="" /></span>');  
               $(button).hide();
            },
            complete: function() {
                $(".wait").remove();
                $(button).show();
            },
            success: function(json) {
                
                var thread1 = '';
                thread1 += '<tr>';
                thread1 += '<td class="center" colspan="5"><?php echo $links_unlinked_items_text; ?></td>';
                thread1 += '</tr>';
                $('#tableThread1').html(thread1);
                
                var thread2 = '';
                thread2 += '<tr>';
                thread2 += '<td width="22.5%"><?php echo $links_name_text; ?></td>';
                thread2 += '<td width="22.5%"><?php echo $links_model_text; ?></td>';
                thread2 += '<td width="22.5%"><?php echo $links_sku_text; ?></td>';
                thread2 += '<td width="22.5%"><?php echo $links_amazon_sku_text; ?></td>';
                thread2 += '<td class="center" width="10%"><?php echo $links_action_text; ?></td>';
                $('#tableThread2').html(thread2);
                
                var rows = '';
                for(i in json) {
                    rows += '<tr id="productRow_' + json[i]['product_id'] + '">';
                    rows += '<td class="left">' + json[i]['product_name'] + '</td>';
                    rows += '<td class="left">' + json[i]['model'] + '</td>';
                    rows += '<td class="left">' + json[i]['sku'] + '</td>';
                    
                    rows += '<td class="left">';
                    rows += '<div class="amazonSkuDiv_' + json[i]['product_id'] + '">';
                    rows += '<input class="amazonSku_' + json[i]['product_id'] + '"  type="text">';
                    rows += '<a onclick="addNewSkuField(' + json[i]['product_id'] + ')"><img src="view/image/add.png" alt="<?php echo $links_add_sku_tooltip; ?>" title="<?php echo $links_add_sku_tooltip; ?>"></a>';
                    rows += '</div>';
                    rows += '</td>';
                    
                    rows += '<td class="center"><a class="button" onclick="addNewLink(this, \'' +  json[i]['product_id'] + '\')"><?php echo $links_add_text; ?></a></td>';
                    rows += '</tr>';
                }
                
                 $('#tableBody').html(rows);  
                 
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
    });	
}

function addLink(button, product_id, amazon_sku) {
    $.ajax({
            url: '<?php echo html_entity_decode($addLink); ?>',
            type: 'get',
            dataType: 'json',
            async: 'false',
            data: 'product_id=' + encodeURIComponent(product_id) + '&amazon_sku=' + encodeURIComponent(amazon_sku),
            beforeSend: function() {
               $(button).after('<span class="wait"><img src="view/image/loading.gif" alt="" /></span>');  
               $(button).hide();
            },
            complete: function() {
                $('.wait').remove();
                $(button).show();
            },
            success: function(json) {
                //alert(json);
                loadLinks();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
    });	
}

function removeLink(button, amazon_sku) {
    $.ajax({
            url: '<?php echo html_entity_decode($removeLink); ?>',
            type: 'get',
            dataType: 'json',
            data: 'amazon_sku=' + encodeURIComponent(amazon_sku),
            beforeSend: function() {
               $(button).after('<span class="wait"><img src="view/image/loading.gif" alt="" /></span>');  
               $(button).hide();
            },
            success: function(json) {
                //alert(json);
                loadLinks();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
    });	
}

function addNewSkuField(product_id) {
    var newField = '';
    newField += '<div class="amazonSkuDiv_' + product_id + '">';
    newField += '<input class="amazonSku_' + product_id + '"  type="text">';
    newField += '<a class="removeSkuIcon_' + product_id + '" onclick="removeSkuField(this, \'' + product_id + '\')"><img src="view/image/delete.png" alt=""></a>';
    newField += '</div>';
    
    $(".amazonSkuDiv_" + product_id).last().after(newField);
}

function removeSkuField(icon, product_id) {
    var removeIndex = $('.removeSkuIcon_' + product_id).index($(icon)) + 1;
    $(".amazonSkuDiv_" + product_id + ":eq(" + removeIndex + ")").remove();
}

function addNewLink(button, product_id) {
    var errors = 0;
    $(".amazonSku_" + product_id).each(function(index) {
        if($(this).val() == '') {
            errors ++;
        }
    });
    if(errors > 0) {
        alert('<?php echo $links_sku_empty_warning; ?>');
        return;
    }
    
    $(".amazonSku_" + product_id).each(function(index) {
            addLink(button, product_id, $(this).val());
    });
    
    $("#productRow_" + product_id).remove();
}

function addNewLinkAutocomplete() {
    if($('#newProduct').val() == "") {
        alert('<?php echo $links_name_empty_warning; ?>');
        return;
    }
    
    if($('#newProductId').attr('label') != $('#newProduct').val()) {
        alert('<?php echo $links_product_warning; ?>');
        return;
    }
    
    if($('#newAmazonSku').val() == "") {
        alert('<?php echo $links_sku_empty_warning; ?>');
        return;
    }

    var product_id = $('#newProductId').val();
    var amazon_sku = $('#newAmazonSku').val();
    
    $('#newProduct').val('');
    $('#newAmazonSku').val('');
    $('#newProductId').val('');
    $('#newProductId').attr('label', '');
    
    addLink('#addNewButton', product_id, amazon_sku);
}

$('#newProduct').autocomplete({
    delay: 0,
    source: function(request, response) {
        $.ajax({
            url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {		
                    response($.map(json, function(item) {
                            return {
                                    id: item.product_id,
                                    label: item.name
                            }
                    }));
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }, 
    select: function(event, ui) {
        $('#newProductId').val(ui.item.id);
        $('#newProductId').attr('label', ui.item.label);
    }
});   
//--></script> 
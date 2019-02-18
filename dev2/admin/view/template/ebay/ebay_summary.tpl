<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>

    <div class="box" style="margin-bottom:130px;"> 
        <div class="heading">
            <h1><?php echo $lang_heading; ?></h1>
            <div class="buttons">
                <?php if($validation == true) { ?>
                    <a onclick="loadSummary();" class="button" id="loadUsage"><span><?php echo $lang_load; ?></span></a>
                    <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageLoadUsage" style="display:none;"/>
                <?php } ?>
                <a onclick="location = '<?php echo $return; ?>';" class="button"><span><?php echo $lang_btn_return; ?></span></a>
            </div>
        </div>
        <div class="content">
        <?php if($validation == true) { ?>
            <p><?php echo $lang_use_desc; ?></p>

            <h2 style="margin-top:30px; display:none;"><?php echo $lang_limits_heading; ?></h2>
            <div id="sellingLimits" style="display:none;" class="attention"></div>


        <?php }else{ ?>
            <div class="warning"><?php echo $lang_error_validation; ?></div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript"><!--
    function loadSummary(){
	$.ajax({ 
            url: 'index.php?route=ebay/openbay/getSellerSummary&token=<?php echo $token; ?>',
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
                $('#loadUsage').hide();
                $('#imageLoadUsage').show();
                $('#sellingLimits').empty().hide();
            },
            success: function(json) {
                $('#loadUsage').show();
                $('#imageLoadUsage').hide();


                var limitHtml = '';

                if(json.data.summary.QuantityLimitRemaining != ''){
                    limitHtml += '<p><span style="font-weight:bold;"><?php echo $lang_ebay_limit_head; ?></span></p>';
                    limitHtml += '<p><?php echo $lang_ebay_limit_t1; ?> <span style="text-decoration: underline;font-weight:bold;">'+json.data.summary.QuantityLimitRemaining+'</span> <?php echo $lang_ebay_limit_t2; ?> <span style="text-decoration: underline;font-weight:bold;">'+json.data.summary.AmountLimitRemaining+'</span></p>';
                    limitHtml += '<p><?php echo $lang_ebay_limit_t3; ?></p>';
                }

                if(limitHtml != ''){
                    $('#sellingLimits').html(limitHtml).show();
                }
                
                if(json.lasterror == true)
                {
                    alert(json.lastmsg);
                }
            },
            failure: function(){
                $('#imageLoadUsage').hide();
                $('#loadUsage').show();

                alert('<?php echo $lang_ajax_load_error; ?>');
            },
            error: function(){
                $('#imageLoadUsage').hide();
                $('#loadUsage').show();

                alert('<?php echo $lang_ajax_load_error; ?>');
            }
	});
    }
//--></script> 

<?php if($validation == true) {
    echo '<script type="text/javascript"><!--
            $(document).ready(function() { loadSummary(); });
          //--></script>'; } 
?>
<?php echo $footer; ?>

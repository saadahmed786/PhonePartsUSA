<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>

    <?php if ($success) { ?>
        <div class="success"><?php echo $success; ?></div>
    <?php } ?>

    <div class="box" style="margin-bottom:130px;">
        <div class="heading">
            <h1><?php echo $lang_heading; ?></h1>
        </div>
        <div class="content">
            <div class="openbayLinks">
                <div class="openbayPod" onclick="location='<?php echo $links_settings; ?>'">
                    <img src="<?php echo $image['icon1']; ?>" title="" alt="" border="0" />
                    <h3><?php echo $lang_text_success; ?></h3>
                </div>

                <?php if($validation == true){ ?>
                    <div class="openbayPod" onclick="location='<?php echo $links_pricing; ?>'">
                        <img src="<?php echo $image['icon13']; ?>" title="" alt="" border="0" />
                        <h3><?php echo $lang_text_price_report; ?></h3>
                    </div>
                <?php }else{ ?>
                    <a class="openbayPod" href="https://playuk.openbaypro.com/account/register" target="_BLANK">
                        <img src="<?php echo HTTPS_SERVER; ?>view/image/openbay/openbay_icon2.png" title="" alt="" border="0" />
                        <h3><?php echo $lang_text_register; ?></h3>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

	$(document).ready(function() {

	});

//--></script>

<?php echo $footer; ?>

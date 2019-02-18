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
    <?php if ($error) { ?>
    <div class="warning"><?php echo $error; ?></div>
    <?php } ?>

    <?php // error messages
    if($this->data['mcrypt'] != 1){ echo'<div class="warning">'.$lang_mcrypt_text_false.'</div>';}
    if($this->data['mbstring'] != 1){ echo'<div class="warning">'.$lang_mb_text_false.'</div>';}
    if($this->data['ftpenabled'] != 1){ echo'<div class="warning">'.$lang_ftp_text_false.'</div>';}
    if(VERSION < '1.5.1.1'){ echo'<div class="warning">' . $lang_error_oc_version . '</div>';}           
    ?>

    <div id="openbay_version" class="attention" style="background-image:none; margin:5px 0px;">
        <div id="openbay_version_loading"><img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" /> <?php echo $lang_checking_version; ?></div>
    </div>
    <div class="box">
        <div class="heading">
            <a href="http://www.openbaypro.com" target="_BLANK"><img src="https://uk.openbaypro.com/asset/OpenBayPro_30px_h.png" alt="OpenBay Pro" style="margin-top:5px; margin-left:5px; border: 0px;;" border="0" /></a>
            <div class="buttons"><a class="button" href="<?php echo $manage_link; ?>"><span><?php echo $lang_btn_manage; ?></span></a></div>
        </div>
        <div class="content">
            <table class="list">
                <thead>
                    <tr>
                        <td class="left"><?php echo $lang_column_name; ?></td>
                        <td class="left"><?php echo $lang_column_status; ?></td>
                        <td class="right"><?php echo $lang_column_action; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($extensions) { ?>
                    <?php foreach ($extensions as $extension) { ?>
                    <tr>
                        <td class="left"><?php echo $extension['name']; ?></td>
                        <td class="left"><?php echo $extension['status'] ?></td>
                        <td class="right"><?php foreach ($extension['action'] as $action) { ?>
                            [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                            <?php } ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="center" colspan="8"><?php echo $lang_text_no_results; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript"><!-- 

function getOpenbayVersion() {

    var version = '<?php echo $openbay_version; ?>';

    $('#openbay_version').empty().html('<div id="openbay_version_loading"><img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" /> <?php echo $lang_checking_version; ?></div>');

    setTimeout(function(){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=extension/ebay/getVersion&token=<?php echo $_GET['token']; ?>',
            dataType: 'json',
            success: function(json) {

                $('#openbay_version_loading').hide(); 

                if(version < json.version){
                    $('#openbay_version').removeClass('attention').addClass('warning').append('<?php echo $lang_version_old_1; ?> v.'+version+', <?php echo $lang_version_old_2; ?> v.'+json.version);
                }else{
                    $('#openbay_version').removeClass('attention').addClass('success').append('<?php echo $lang_latest; ?> (v.'+version+')');
                }
            },
            failure: function(){
                $('#openbay_version').html('<?php echo $lang_error_retry; ?><strong><span onclick="getOpenbayVersion();"><?php echo $lang_btn_retry; ?></span></strong>');
            },
            error: function() {
                $('#openbay_version').html('<?php echo $lang_error_retry; ?><strong><span onclick="getOpenbayVersion();"><?php echo $lang_btn_retry; ?></span></strong>');
            }
        });
    },500);
}
//--></script> 
<?php echo $footer; ?>

<script type="text/javascript"><!-- 

getOpenbayVersion();

//--></script> 
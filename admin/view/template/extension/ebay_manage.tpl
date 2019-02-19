<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>

    <div class="box" style="margin-bottom:130px;">
        <div class="left"></div>
        <div class="right"></div>
        <div class="heading">
            <h1><?php echo $lang_text_manager; ?></h1>
            <div class="buttons"><a onclick="validateForm(); return false;" class="button"><span><?php echo $lang_btn_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $lang_btn_cancel; ?></span></a></div>
        </div>
        <div class="content">

            <div id="tabs" class="htabs">
                <a href="#tab-updates"><?php echo $lang_btn_update; ?></a>
                <a href="#tab-settings"><?php echo $lang_btn_settings; ?></a>
                <a href="#tab-patch"><?php echo $lang_btn_patch; ?></a>
                <a href="#tab-help"><?php echo $lang_tab_help; ?></a>
            </div>

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

                <div id="tab-updates">
                    
                    <p><?php echo $lang_patch_notes1; ?> <a href="http://shop.openbaypro.com/index.php?route=information/information/changelog" title="OpenBay Pro change log" target="_BLANK"><?php echo $lang_patch_notes2; ?></a></p>
                    
                    <table  width="100%" cellspacing="0" cellpadding="2" border="0" class="adminlist">

                        <tr class="row0">
                            <td width="230"><?php echo $lang_installed_version; ?>:</td>
                            <td id="openBayVersionText">
                                <?php echo $txt_obp_version; ?>
                            </td>
                        <input type="hidden" name="openbay_version" value="<?php echo $openbay_version;?>" id="openbay_version" />
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_username"><?php echo $field_ftp_user; ?></label></td>
                            <td><input class="ftpsetting" type="text" name="openbay_ftp_username" id="openbay_ftp_username" style="width:250px;" maxlength="" value="<?php echo $openbay_ftp_username;?>" /></td>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_pw"><?php echo $field_ftp_pw; ?></label></td>
                            <td><input class="ftpsetting" type="text" name="openbay_ftp_pw" id="openbay_ftp_pw" style="width:250px;" maxlength="" value="<?php echo $openbay_ftp_pw;?>" /></td>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_server"><?php echo $field_ftp_server_address; ?></label></td>
                            <td><input class="ftpsetting" type="text" name="openbay_ftp_server" id="openbay_ftp_server" style="width:250px;" maxlength="" value="<?php echo $openbay_ftp_server;?>" /></td>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_rootpath"><?php echo $field_ftp_root_path; ?><span class="help"><?php echo $field_ftp_root_path_info; ?></span></label></td>
                            <td><input class="ftpsetting" type="text" name="openbay_ftp_rootpath" id="openbay_ftp_rootpath" style="width:250px;" maxlength="" value="<?php echo $openbay_ftp_rootpath;?>" /></td>
                        </tr>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_admin_directory"><?php echo $lang_admin_dir; ?><span class="help"><?php echo $lang_admin_dir_desc; ?></span></label></td>
                            <td><input class="ftpsetting" type="text" name="openbay_admin_directory" id="openbay_admin_directory" style="width:250px;" maxlength="" value="<?php echo $openbay_admin_directory;?>" /></td>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_pasv"><?php echo $lang_use_pasv; ?></label></td>
                            <td><input class="ftpsetting" type="checkbox" name="openbay_ftp_pasv" id="openbay_ftp_pasv" value="true" /></td>
                        </tr>

                        <tr class="row0">
                            <td width="230"><label for="openbay_ftp_beta"><?php echo $lang_use_beta; ?><span class="help"><?php echo $lang_use_beta_2; ?></span></label></td>
                            <td><input class="ftpsetting" type="checkbox" name="openbay_ftp_beta" id="openbay_ftp_beta" value="true" /></td>
                        </tr>

                        <tr class="row0" id="ftpTestRow">
                            <td width="230" height="50" valign="middle"><?php echo $lang_test_conn; ?></td>
                            <td>
                                <a onclick="ftpTest();" class="button" id="ftpTest"><span><?php echo $lang_btn_test; ?></span></a>
                                <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageFtpTest" style="display:none;"/>
                            </td>
                        </tr>

                        <tr class="row0" id="ftpUpdateRow" style="display:none;">
                            <td width="230" height="50" valign="middle"><?php echo $lang_text_run_1; ?></td>
                            <td><span id="preFtpTestText"><?php echo $lang_text_run_2; ?></span>
                                <a onclick="updateModule();" class="button" id="moduleUpdate" style="display:none;"><span><?php echo $lang_btn_update; ?></span></a>
                                <img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageModuleUpdate" style="display:none;"/>
                            </td>
                        </tr>

                        <tr class="row0">
                            <td colspan="2" id="updateBox"></td>
                        </tr>

                    </table>
                </div>

                <div id="tab-settings">
                    <table  width="100%" cellspacing="0" cellpadding="2" border="0" class="adminlist">
                        <tr>
                            <td  width="230"><?php echo $field_openbaypro_disable_homemsg; ?></td>
                            <td>
                                <select name="openbay_disable_homemessage">
                                    <?php if ($openbay_disable_homemessage) { ?>
                                    <option value="1" selected="selected"><?php echo $lang_yes; ?></option>
                                    <option value="0"><?php echo $lang_no; ?></option>
                                    <?php } else { ?>
                                    <option value="1"><?php echo $lang_yes; ?></option>
                                    <option value="0" selected="selected"><?php echo $lang_no; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td  width="230"><?php echo $lang_language; ?></td>
                            <td>
                                <select name="openbay_language">
                                    <?php foreach($languages as $key => $language){ ?>
                                    <option value="<?php echo $key; ?>" <?php if($key == $openbay_language){ echo'selected="selected"'; } ?>><?php echo $language; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tab-patch">
                    <table  width="100%" cellspacing="0" cellpadding="2" border="0" class="adminlist">
                        <tr>
                            <td  width="230"><?php echo $lang_run_patch_desc; ?></td>
                            <td><a onclick="runPatch();" class="button" id="runPatch"><span><?php echo $lang_run_patch; ?></span></a><img src="<?php echo HTTPS_SERVER; ?>view/image/loading.gif" id="imageRunPatch" style="display:none;"/></td>
                        </tr>
                    </table>
                </div>

                <div id="tab-help">
                    <h2><?php echo $lang_help_title; ?></h2>
                    <table  width="100%" cellspacing="0" cellpadding="2" border="0" class="adminlist">
                        <tr class="row0">
                            <td width="230" style="padding:10px;"><?php echo $lang_help_support_title; ?></td>
                            <td style="padding:10px;"><?php echo $lang_help_support_description; ?></td>
                        </tr>
                        <tr class="row0">
                            <td width="230" style="padding:10px;"><?php echo $lang_help_template_title; ?></td>
                            <td style="padding:10px;"><?php echo $lang_help_template_description; ?></td>
                        </tr>
                        <tr class="row0">
                            <td width="230" style="padding:10px;"><?php echo $lang_help_guide; ?></td>
                            <td style="padding:10px;"><?php echo $lang_help_guide_description; ?></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    $('.ftpsetting').keypress(function(){
        $('#preFtpTestText').show();
        $('#moduleUpdate').hide();
        $('#ftpTestRow').show();
        $('#ftpUpdateRow').hide();
    });

    function ftpTest(){
        var user            = encodeURIComponent($('#openbay_ftp_username').val());
        var pw              = encodeURIComponent($('#openbay_ftp_pw').val());
        var server          = encodeURIComponent($('#openbay_ftp_server').val());
        var rootpath        = encodeURIComponent($('#openbay_ftp_rootpath').val());
        var adminDir        = encodeURIComponent($('#openbay_admin_directory').val());

        $.ajax({
            url: 'index.php?route=extension/ebay/ftpTestConnection&token=<?php echo $_GET['token']; ?>&user='+user+'&pw='+pw+'&server='+server+'&rootpath='+rootpath+'&adminDir='+adminDir,
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
                $('#ftpTest').hide();
                $('#imageFtpTest').show();
            },
            success: function(json) {

                alert(json.msg);

                if(json.connection == true)
                {
                    $('#preFtpTestText').hide();
                    $('#moduleUpdate').show();
                    $('#ftpTestRow').hide();
                    $('#ftpUpdateRow').show();
                }

                $('#ftpTest').show();
                $('#imageFtpTest').hide();
            }
        });
    }

    function runPatch(){

        $.ajax({
            url: 'index.php?route=extension/ebay/runPatch&token=<?php echo $_GET['token']; ?>',
            type: 'post',
            dataType: 'json',
            beforeSend: function(){
                $('#runPatch').hide();
                $('#imageRunPatch').show();
            },
            success: function() {
                alert('Patch applied');
                $('#runPatch').show();
                $('#imageRunPatch').hide();
            }
        });
    }

    function updateModule(){
        var user            = encodeURIComponent($('#openbay_ftp_username').val());
        var pw              = encodeURIComponent($('#openbay_ftp_pw').val());
        var server          = encodeURIComponent($('#openbay_ftp_server').val());
        var rootpath        = encodeURIComponent($('#openbay_ftp_rootpath').val());
        var adminDir        = encodeURIComponent($('#openbay_admin_directory').val());
        var beta            = false;
        var pasv            = false;
        
        if($('#openbay_ftp_beta').prop('checked')) { beta = true; }
        if($('#openbay_ftp_pasv').prop('checked')) { pasv = true; }
        
        $.ajax({
            url: 'index.php?route=extension/ebay/ftpUpdateModule&token=<?php echo $_GET['token']; ?>&user='+user+'&pw='+pw+'&server='+server+'&rootpath='+rootpath+'&beta='+beta+'&adminDir='+adminDir+'&pasv='+pasv,
            type: 'post',
            dataType: 'json',
            beforeSend: function(){ 
                $('#moduleUpdate').hide();
                $('#imageModuleUpdate').show();
            },
            success: function(json) {
                alert(json.msg);

                $('#openBayVersionText').text(json.version);
                $('#openbay_version').val(json.version);

                $('#moduleUpdate').show();
                $('#imageModuleUpdate').hide();
            }
        });
        
        //readUpdateLog();
    }
    
    function validateForm(){
        $('#form').submit();
    }

    function readUpdateLog(){
        $.ajax({
            url: '<?php echo HTTP_CATALOG; ?>index.php?route=ebay/openbay/readUpdateLog',
            type: 'get',
            dataType: 'json',
            success: function(json) {
                $('#updateBox').append('<p><strong>'+json.pos+'%</strong> '+json.text+'</p>');

                if(json.pos == 100){
                    alert('Updated');
                    $('#moduleUpdate').show();
                    $('#imageModuleUpdate').hide();
                }else{
                    setTimeout(function(){
                        readUpdateLog();
                    }, 1000);
                }
            }
        });
    }

</script>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 

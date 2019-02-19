<?php echo $header; ?>
<div id="content">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
  <?php echo (empty($data['ProductBundles']['LicensedOn'])) ? base64_decode('ICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciBmYWRlIGluIj4NCiAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgYXJpYS1oaWRkZW49InRydWUiPsOXPC9idXR0b24+DQogICAgICAgIDxoND5XYXJuaW5nISBVbmxpY2Vuc2VkIHZlcnNpb24gb2YgdGhlIG1vZHVsZSE8L2g0Pg0KICAgICAgICA8cD5Zb3UgYXJlIHJ1bm5pbmcgYW4gdW5saWNlbnNlZCB2ZXJzaW9uIG9mIHRoaXMgbW9kdWxlISBZb3UgbmVlZCB0byBlbnRlciB5b3VyIGxpY2Vuc2UgY29kZSB0byBlbnN1cmUgcHJvcGVyIGZ1bmN0aW9uaW5nLCBhY2Nlc3MgdG8gc3VwcG9ydCBhbmQgdXBkYXRlcy48L3A+PGRpdiBzdHlsZT0iaGVpZ2h0OjVweDsiPjwvZGl2Pg0KICAgICAgICA8YSBjbGFzcz0iYnRuIGJ0bi1kYW5nZXIiIGhyZWY9ImphdmFzY3JpcHQ6dm9pZCgwKSIgb25jbGljaz0iJCgnYVtocmVmPSNzdXBwb3J0XScpLnRyaWdnZXIoJ2NsaWNrJykiPkVudGVyIHlvdXIgbGljZW5zZSBjb2RlPC9hPg0KICAgIDwvZGl2Pg==') : '' ?>
    <?php if ($error_warning) { ?><div class="alert alert-danger" > <i class="icon-exclamation-sign"></i>&nbsp;<?php echo $error_warning; ?></div><?php } ?>
    <?php if (!empty($this->session->data['success'])) { ?>
        <div class="alert alert-success autoSlideUp"> <i class="fa fa-info"></i>&nbsp;<?php echo $this->session->data['success']; ?> </div>
        <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
    <?php $this->session->data['success'] = null; } ?>
      <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-gift"></i>&nbsp;<span style="vertical-align:middle;font-weight:bold;"><?php echo $heading_title; ?></span></h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                <div class="tabbable">
                    <div class="tab-navigation form-inline">
                        <ul class="nav nav-tabs mainMenuTabs" id="mainTabs" role="tablist">
                            <li class="active"><a href="#controlpanel" data-toggle="tab"><i class="fa fa-power-off"></i>&nbsp;Control Panel</a></li>
                            <li id="bundlesTab"><a href="#bundles" data-toggle="tab"><i class="fa fa-cubes"></i>&nbsp;Bundles</a></li>
                            <li id="settingsTab" class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-columns"></i>&nbsp;Settings<span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#widget" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i>&nbsp;Bundle Widget</a></li>
                                    <li><a href="#listing" role="tab" data-toggle="tab"><i class="fa fa-indent"></i>&nbsp;Bundle Listing</a></li>
                                </ul>
							</li>
                            <li><a href="#support" data-toggle="tab"><i class="fa fa-external-link"></i>&nbsp;Support</a></li>    
                        </ul>
                        <div class="tab-buttons">
                            <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;Save Changes</button>
                            <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel?></a>
                        </div> 
                    </div><!-- /.tab-navigation --> 
                    <div class="tab-content">
                        <div id="controlpanel" class="tab-pane active">
                          <?php require_once(DIR_APPLICATION.'view/template/module/productbundles/tab_controlpanel.php'); ?>                        
                        </div> 
                        <div id="bundles" class="tab-pane">
                          <?php require_once(DIR_APPLICATION.'view/template/module/productbundles/tab_bundles.php'); ?>                        
                        </div>    
                        <div id="listing" class="tab-pane">
                          <?php require_once(DIR_APPLICATION.'view/template/module/productbundles/tab_listing.php'); ?>                        
                        </div>
                        <div id="widget" class="tab-pane">
                          <?php require_once(DIR_APPLICATION.'view/template/module/productbundles/tab_widget.php'); ?>                        
                        </div>         
                        <div id="support" class="tab-pane">
                          <?php require_once(DIR_APPLICATION.'view/template/module/productbundles/tab_support.php'); ?>                        
                        </div>
                    </div> <!-- /.tab-content --> 
                </div><!-- /.tabbable -->
            </form>
        </div> 
    </div>
</div>
<?php echo $footer; ?>
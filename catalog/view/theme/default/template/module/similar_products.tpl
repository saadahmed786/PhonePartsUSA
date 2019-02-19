<?php if ($show_similar) { ?>
<style type="text/css">.sp-c{position:relative;width:100%;overflow:hidden}.sp-o{width:100%;height:100%;position:absolute;top:0;left:0;z-index:1000;display:none;background-color:#fff}.sp-l{background-color:#fff}.sp-ul{z-index:999}.sp-ll{z-index:998}.sp-t{display:table;width:100%;height:100%}.sp-t-c{display:table-cell;vertical-align:middle;text-align:center}.sp-t-c img{display:inline}.sp-nbm{margin-bottom:0px !important}.sp-p{margin-right:9px !important;margin-left:10px !important; width:17%; float:left;} .pagination .links b{float:left;}.pagination .results{float:none;} .box {
background: url(catalog/view/theme/bt_optronics/image/boss-category-bg.jpg) repeat-x bottom;
border: 1px solid #ccc;
padding: 0 0 9px;
margin-top: 20px;
}
.box .box-heading {
background: url("catalog/view/theme/bt_optronics/image/bg_box.jpg") repeat-x scroll 0 0 transparent;
border: 1px solid #DEDCDC;
color: #1D1D1D;
font: 400 14px/36px HelveticaBold;
height: 36px;
padding: 0 14px;
text-transform: uppercase;
float: left;
width: 96%;
border: none;
}</style>

<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">

    <div id="sp-c<?php echo $mid; ?>" class="sp-c"<?php echo $lazy_load ? ' style="height:200px"' : ''; ?>>
      <div id="sp-o<?php echo $mid; ?>" class="sp-o">
        <div class="sp-t">
          <div class="sp-t-c"><img src="catalog/view/theme/default/image/loading_similar.gif" /></div>
        </div>
      </div>
      <div class="sp-ll sp-l" id="sp-p<?php echo $mid; ?>" data-mid="<?php echo $mid; ?>"><?php echo $products; ?></div>
    </div>

  </div>
</div>

<script type="text/javascript"><!--
(function(bull5i,$,undefined){bull5i.texts=$.extend({},bull5i.texts,{error_ajax_request:'<?php echo addslashes($error_ajax_request); ?>'});<?php if ($lazy_load) { ?>$(function(){$('#sp-p<?php echo $mid; ?>').waypoint(function(){bull5i.sp_get_products('index.php?route=module/similar_products/get&pid=<?php echo $product_id; ?>&mid=<?php echo $mid; ?><?php echo $path; ?>',"<?php echo $mid; ?>");},{triggerOnce:true,offset:'bottom-in-view'})})<?php } ?>}(window.bull5i=window.bull5i||{},jQuery));
//--></script>
<?php } ?>

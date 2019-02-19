<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <a <?php echo(($breadcrumb == end($breadcrumbs)) ? 'class="last"' : ''); ?> href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php //echo $content_top; ?>
 <div style="position:absolute;margin-left:88%;margin-top:2%;"><img src="image/printer_icon.png" style="curor:pointer" /> <span style="display:block;margin-left:20px;margin-top:-18px"><a href="javascript:void(0);" onclick="printThis();">Print RMA Label</a></span></div>
 <div id="printarea" ><img  src="<?php echo HTTP_IMAGE;?>/returns/<?php echo $return_image;?>" style="height:100%;width:100%" /></div>
 
 <div style="position:absolute;margin-left:88%;margin-top:-2%;"><img src="image/printer_icon.png" style="curor:pointer" /> <span style="display:block;margin-left:20px;margin-top:-18px"><a href="javascript:void(0);" onclick="printThis();">Print RMA Label</a></span></div>
  <div class="buttons">
    <div class="left"><a href="<?php echo $continue; ?>" class="button_pink"><span><?php echo $button_continue; ?></span></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
  <!-- Google Code for Conversion Conversion Page -->



   




<script>
function printThis()
{
var mywindow = window.open('', 'RMA Print', 'height=400,width=600');
        mywindow.document.write('<html><head><title>RMA Print</title>');
        
        mywindow.document.write('</head><body >');
        mywindow.document.write($('#printarea').html());
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;	
}

</script>



<?php echo $footer; ?>
<script>
var slideimages=new Array()
var slidelinks=new Array()
function slideshowimages(){
for (i=0;i<slideshowimages.arguments.length;i++){
slideimages[i]=new Image()
slideimages[i].src=slideshowimages.arguments[i]
}
}

function slideshowlinks(){
for (i=0;i<slideshowlinks.arguments.length;i++)
slidelinks[i]=slideshowlinks.arguments[i]
}

function gotoshow(){
if (!window.winslide||winslide.closed)
winslide=window.open(slidelinks[whichlink])
else
winslide.location=slidelinks[whichlink]
winslide.focus()
}
</script>


<?php 
$slideshowimages;	
$slideshowinks;	

foreach ($banners as $banner) { 
    
	$slideshowimages[] = '"' . $banner['image'] . '"';
	$slideshowlinks[] = '"' . html_entity_decode(str_replace('/index', 'index', $banner['link'])) . '"';	
}

?>
<div class="module">
	<a href=<?php echo $slideshowlinks[0] ?> id="slideLink" style="border:0;"><img src=<?php echo $slideshowimages[0] ?> name="slide" style="width:100%;"/></a>
</div>

<script>
slideshowimages(<?php echo implode(',',$slideshowimages); ?>)
slideshowlinks(<?php echo implode(',',$slideshowlinks);?>)

var slideshowspeed=8888

var whichlink=0
var whichimage=0

function slideit(){
if (!document.images)
return
document.images.slide.src=slideimages[whichimage].src
whichlink=whichimage
document.getElementById("slideLink").href=slidelinks[whichlink]
if (whichimage<slideimages.length-1)
whichimage++
else
whichimage=0

setTimeout("slideit()",slideshowspeed)
}

slideit()
</script>
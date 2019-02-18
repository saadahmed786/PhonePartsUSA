
<!-- JavaScript -->

   
   
    <script type="text/javascript">  
      $(document).ready(function(){  
        $("#CategoryFrm").submit(function(event) {  
  
            /* stop form from submitting normally */  
           event.preventDefault();   
              
            $.post( 'controller/icache/files/printerform.php', $("#CategoryFrm").serialize(),  
              function( data ) {  
                  $("#status").html(data);  
              }  
            );  
          });  
      });  
    </script>  
  </head>  
<?php

$id79 = '<div id="status"></div>';
require_once 'GoogleCloudPrint.php';


// Create object
$gcp = new GoogleCloudPrint();


$printers = $gcp->getPrinters();


$printerid = "";
if(count($printers)==0) {
	
	echo "Could not get printers";
	exit;
}
       
require 'controller/icache/files/printerhash.php';


$oko = (isset($p_active2));

if (empty($oko)) 
{
  echo ('Please Select A Printer, then press Apply, Then Check the "Invoice Tab" '); echo $id79;
  }
  else {


echo $p_active2; echo $id79;

}
	foreach($printers as $printer)
{
$ifg2 = $printer['displayName'];
$ifg = $printer['id'];
$item[]=$ifg;
$item2[]=$ifg2;

}

echo '<br><br>';

	foreach(array_combine($item, $item2) as $f => $n) {
// $idend = '<input type="submit" value="Apply" class="button">' ; //name="submit"
$id3 = '<form action="" method="post" id="CategoryFrm">'.'<input type="radio"'.'name="Printer_hash"'. 'value="' . $f . ',' . $n . '">' . $n . '<br>' ;
echo $id3;
}
echo '<br>'.'<input type="image" src="controller/icache/files/apply.png" class="button"></form>';

?>


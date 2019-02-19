<?php
include_once '../config.php';
include_once '../inc/split_page_results.php';

if(!$_SESSION['email']){
    header("Location:$host_path/index.php");
    exit;
}

if($_POST['Upload']){
    $csv_mimetypes = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'text/anytext',
        'application/octet-stream',
        'application/txt',
    );

    $type = $_FILES['mapping']['type'];
    if(in_array($type,$csv_mimetypes)){
        $filename = $_FILES['mapping']['tmp_name'];
        $handle   = fopen("$filename", "r");

        $insert = array();
        $db->db_exec("truncate table ebay_mapping");

        $row = fgetcsv($handle);
        while(!feof($handle)){
            $row = fgetcsv($handle);

            $itemId = trim($row[0]);
            $row[1] = trim($row[1]);
            $sku = $db->func_escape_string($row[1]);

            if(!$insert[$sku]){
                $insert[$sku] = $itemId;
            }
            else{
                //echo $sku . "<br />";
            }
        }

        if(count($insert) > 0){
            $insertQuery = "Insert into ebay_mapping(ebay_item_id , product_sku , dateofmodification) values";
            foreach($insert as $sku => $itemId){
                $insertQuery .= " ('$itemId' , '$sku' , '".date('Y-m-d H:i:s')."') ,";
            }

            $insertQuery = substr($insertQuery,0,-1);
            $db->db_exec($insertQuery);
        }

        $_SESSION['message'] = "eBay Mapping updated successfully";
    }
    else{
        $_SESSION['message'] = "Uploaded File is not csv file";
    }

    header("Location:$host_path/ebay/upload_mapping.php");
    exit;
}

?>
<!DOCTYPE body PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="include/calendar.css" rel="stylesheet" type="text/css" />
	<link href="include/calendar-blue.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="include/calendar.js"></script>
	<script type="text/javascript" src="include/calendar-en.js"></script>
	<script type="text/javascript" src="include/calhelper.js"></script>
	<title>Upload eBay Mapping</title>
</head>
<body>
<?php include_once '../inc/header.php';?>

<?php if(@$_SESSION['message']):?>
	<div align="center"><br />
		<font color="red">
			<?php echo $_SESSION['message']; unset($_SESSION['message']);?><br />
		</font>
	</div>
<?php endif;?>

<h2 align="center">Upload eBay Mapping</h2>

<form name="ebay_mapping" action="" method="post"  enctype="multipart/form-data">
	<table width="40%" cellpadding="10" style="border: 1px solid #585858;"  align="center">
	    <tr>
	        <td align="right">Upload File:</td>
	
	        <td><input type="file" name="mapping" /></td>
	    </tr>

	    <tr>
	        <td align="center" colspan="2">
	        	<input type="submit" name="Upload" value="Upload Mapping" />
	        </td>
	    </tr>
	</table>
</form>
</body>
</html>
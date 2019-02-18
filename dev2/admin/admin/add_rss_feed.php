<?php
require_once ("../includes/config.php");
require_once ("../includes/functions.php");
$script_name = 'add_rss_feed';

function safe($str) {
    if (get_magic_quotes_gpc) {
        return trim($str);
    } else {
        return mysql_real_escape_string(trim($str));
    }
}

$content = '';
$cats = "<option value=\"\"></option>";

$get_cats = "SELECT category_id, category_desc FROM pqdb_categories ORDER BY category_name ASC";
$get_cats_result = mysql_query($get_cats);
while ($cat_row = mysql_fetch_array($get_cats_result)) {

    // format category names (get rid of "Information ...")
    $take_out = array("& Information", "Information on", "Information");
    $category_desc = str_replace($take_out, "", $cat_row['category_desc']);

    if ($_POST['cat'] == $cat_row['category_id']) {
        $cats .= "<option value=\"" . $cat_row['category_id'] . "\" selected>" . $category_desc . "</option>";
    } else {
        $cats .= "<option value=\"" . $cat_row['category_id'] . "\">" . $category_desc . "</option>";
    }
}

// check if article has been submitted
if ($_POST['do'] == 'add') {
    // article has been submitted so let's check it

    $error = '';

    // check that all data is present
    if (trim($_POST['rss']) == '') {
        $error .= "<li>Please type the RSS feed.</li>";
    } 
    if (trim($_POST['cat']) == '') {
        $error .= "<li>Please choose a category.</li>";
    } 
    
    if ($error == '') {
        // there are no errors so insert data
        
        $update = "UPDATE pqdb_categories 
                   SET category_rss = '"  . safe($_POST['rss']) . "' 
                   WHERE category_id = "  . safe($_POST['cat']);
        $update_result = mysql_query($update);
        
        if ($update_result) {
            $content .= "<h3 style=\"color: green;\">RSS Feed inserted successfully!</h3>";
        } else {
            $content .= "<h3 style=\"color: red;\">Something failed. Please try again.</h3>";
        }
        
    } else {
        // there are errors so show errors and don't insert data
        
        // show errors here
        $content .= "<h3 style=\"color: red;\">ERROR!</h3><ol>" . $error . "</ol>";
        
    }

} 
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<title>Admin >> Add RSS Feed to Category</title>
</head>
<body>


<h1><a href="index.html">Admin</a> >> Add RSS Feed to Category</h1>

<center><?php echo $content; ?></center>

<form method="post">
<input type="hidden" name="do" value="add">
<table align="center" border="0" cellspacing="10">
    <tr>
        <td align="left">
            <strong>Category:</strong><br />
            <select name="cat">
                <?php echo $cats; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="left">
            <strong>RSS Feed:</strong><br />
            <textarea name="rss" rows="20" cols="60"><?php echo stripslashes(trim($_POST['rss'])); ?></textarea>
        </td>
    </tr>
    <tr>
        <td align="center">
            <input type="submit" value="Submit">
        </td>
    </tr>
</table>
</form>

</body>
</html>























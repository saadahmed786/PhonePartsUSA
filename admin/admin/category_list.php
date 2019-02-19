<?php
require_once ("../includes/config.php");
$sql = "SELECT category_id, category_desc FROM pqdb_categories ORDER BY category_name ASC";
$result = mysql_query($sql);
$list = "";
while ($row = mysql_fetch_array($result)) {

    // format category names (get rid of "Information ...")
    $take_out = array("& Information", "Information on", "Information");
    $category_desc = str_replace($take_out, "", $row['category_desc']);

    $list .= "<li>" . $category_desc . " (Category ID: " . $row['category_id'] . ")</li>"; 
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<title>Admin >> Category ID's</title>
</head>
<body>


<h1><a href="index.html">Admin</a> >> Category ID's</h1>
<a href="#instructions">Instructions Below</a>
<ul>
    <?php echo $list; ?>
</ul>

<a name="instructions"></a>
If you want to show specific categories go to your includes/config.php file and find $selected_categories.
Between the parenthesis (), put the ID of the categories you want to display.  DO NOT put quotes.  Separate each 
ID with a comma.  See the following example:<br />


<pre>
$selected_categories = array(130,132,100);
</pre>


<br /><br />


If you want to show all the categories don't do anything!<br />
<pre>
$selected_categories = array();
</pre>


</body>
</html>

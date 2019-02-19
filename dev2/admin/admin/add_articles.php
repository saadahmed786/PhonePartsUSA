<?php
require_once ("../includes/config.php");
require_once ("../includes/functions.php");
$script_name = 'add_articles';

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

    if ($_POST['article_cat'] == $cat_row['category_id']) {
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
    if (trim($_POST['article_title']) == '') {
        $error .= "<li>Please type the article title.</li>";
    } 
    if (trim($_POST['article_text']) == '') {
        $error .= "<li>Please type the article text.</li>";
    } 
    if (trim($_POST['article_cat']) == '') {
        $error .= "<li>Please choose a category.</li>";
    } 
    if (trim($_POST['article_meta_key']) == '') {
        $error .= "<li>Please type the article meta keywords.</li>";
    } 
    if (trim($_POST['article_meta_desc']) == '') {
        $error .= "<li>Please type the article meta description.</li>";
    } 
    
    if ($error == '') {
        // there are no errors so insert data
        
        $insert = "INSERT INTO pqdb_articles (
                        article_title, 
                        article_text, 
                        article_cat, 
                        article_meta_key, 
                        article_meta_desc
                  ) VALUES (
                        '"  . safe($_POST['article_title']) . "', 
                        '"  . safe($_POST['article_text']) . "', 
                        "  . safe($_POST['article_cat']) . ", 
                        '"  . safe($_POST['article_meta_key']) . "', 
                        '"  . safe($_POST['article_meta_desc']) . "'
                  )";
        $insert_result = mysql_query($insert);
        
        if ($insert_result) {
            $content .= "<h3 style=\"color: green;\">Article inserted successfully!</h3>";
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
	<title>Admin >> Add Articles</title>
</head>
<body>


<h1><a href="index.html">Admin</a> >> Add Articles</h1>

<center><?php echo $content; ?></center>

<form method="post">
<input type="hidden" name="do" value="add">
<table align="center" border="0" cellspacing="10">
    <tr>
        <td align="left">
            <strong>Title:</strong><br />
            <input type="text" name="article_title" value="<?php echo stripslashes(trim($_POST['article_title'])); ?>" size="60">
        </td>
    </tr>
    <tr>
        <td align="left">
            <strong>Meta Keywords:</strong><br />
            <input type="text" name="article_meta_key" value="<?php echo stripslashes(trim($_POST['article_meta_key'])); ?>" size="60">
        </td>
    </tr>
    <tr>
        <td align="left">
            <strong>Meta Description:</strong><br />
            <input type="text" name="article_meta_desc" value="<?php echo stripslashes(trim($_POST['article_meta_desc'])); ?>" size="60">
        </td>
    </tr>
    <tr>
        <td align="left">
            <strong>Category:</strong><br />
            <select name="article_cat">
                <?php echo $cats; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="left">
            <strong>Text:</strong><br />
            <textarea name="article_text" rows="20" cols="60"><?php echo stripslashes(trim($_POST['article_text'])); ?></textarea>
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























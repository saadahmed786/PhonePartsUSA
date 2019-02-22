<?php
require_once("auth.php");
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

if ((int) $_GET['id'] && $_GET['action'] == 'delete') {
    $db->db_exec("delete from inv_po_customers where id = '" . (int) $_GET['id'] . "'");
    header("Location:po_businesses.php");
    exit;
}

if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}

if ($page < 1) {
    $page = 1;
}

$parameters = str_replace('&page=' . $_GET['page'], '', $_SERVER['QUERY_STRING']);

$max_page_links = 10;
$num_rows = 20;
$start = ($page - 1) * $num_rows;

$_query = "Select * from inv_po_customers order by id DESC";

$splitPage = new splitPageResults($db, $_query, $num_rows, "po_businesses.php", $page);
$users = $db->func_query($splitPage->sql_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>PO Clients Listing</title>
    </head>
    <body>
        <div align="center">
            <div align="center"> 
                <?php include_once 'inc/header.php'; ?>
            </div>

            <?php if ($_SESSION['message']): ?>
                <div align="center"><br />
                    <font color="red"><?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?><br /></font>
                </div>
            <?php endif; ?>

            <br clear="all" />

            <a href="po_business_create.php?mode=new">Add New</a>

            <br clear="all" /><br clear="all" />

            <div align="center">
                <table border="1" width="80%;" cellpadding="10" cellspacing="0" style="border:1px solid #585858;border-collapse:collapse;">
                    <tr style="background-color:#e7e7e7;">
                        <td>Company Name</td>
                        <td>Contact Name</td>
                        <td>Telephone</td>
                        <td>Email</td>
                        <td>Tax ID</td>
                        <td>City</td>
                        <td>State</td>
                        <td>Zip Code</td>
                        <td colspan="2" align="center">Action</td>
                    </tr>

                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['company_name']; ?></td>

                            <td><?php echo $user['contact_name']; ?></td>

                            <td><?php echo $user['telephone']; ?></td>

                            <td><?php echo linkToProfile($user['email']); ?></td>

                            <td><?php echo $user['tax_id']; ?></td>

                            <td><?php echo $user['city']; ?></td>

                            <td><?php echo $user['state']; ?></td>

                            <td><?php echo $user['zip']; ?></td>

                            <td><a href="po_business_create.php?id=<?php echo $user['id']; ?>&mode=edit">Edit</a></td>

                            <td><a href="po_businesses.php?id=<?php echo $user['id']; ?>&action=delete" onclick="if (!confirm('Are you sure, You want to delete this customer?')) {
                                        return false;
                                    }">Delete</a></td>
                        </tr>
<?php endforeach; ?>

                    <tr>
                        <td colspan="2" align="left">
<?php echo $splitPage->display_count("Displaying %s to %s of (%s)"); ?>
                        </td>

                        <td align="center" colspan="3">
                            <form method="get">
                                Page: <input type="text" name="page" value="<?php echo $page; ?>" size="3" maxlength="3" />
                                <input type="submit" name="Go" value="Go" />
                            </form>
                        </td>

                        <td colspan="5" align="right">
<?php echo $splitPage->display_links(10, $parameters); ?>
                        </td>
                    </tr>
                </table>
            </div>		 
        </div>		     
    </body>
</html>
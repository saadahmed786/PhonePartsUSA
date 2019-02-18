<?php
require_once("auth.php");
require_once("inc/functions.php");
page_permission('inv_customer_contact_view');
$customer_email = $_GET['customer_id'];
$contacts = $db->func_query("SELECT * FROM `inv_customer_contacts` WHERE customer_id = '". $customer_email ."'");
foreach ($contacts as $key => $contact) {
	$contacts[$key]['contacts'] = $db->func_query("SELECT * FROM `inv_contacts_ph` WHERE customer_contact_id = '". $contact['id'] ."'");
}
?>
<?php if ($contacts) { ?>
<h3>Contacts</h3>
<table style="border-collapse:collapse;" border="1" width="100%" cellpadding="10" cellspacing="0">
	<thead>
		<tr>
			<th>Name</th>
			<th>Position</th>
			<th>Email</th>
			<th>Contacts</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($contacts as $contact) : ?>
			<tr>
				<td><?php echo $contact['first_name']; ?> <?php echo $contact['last_name']; ?></td>
				<td><?php echo $contact['position']; ?></td>
				<td><?php echo $contact['email']; ?></td>
				<td>
					<?php foreach ($contact['contacts'] as $contactx) : ?>
						<span><?php echo $contactx['type']; ?>: </span> <strong><?php echo $contactx['contact']; ?></strong><br>
					<?php endforeach;?>
				</td>
				<td><a class="fancybox2 fancybox.iframe" href="addContact.php?customer_id=<?php echo $customer_email;?>&contact_id=<?php echo $contact['id'];?>">Edit</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php } ?>
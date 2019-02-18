<?php
include_once("../config.php");

include_once("../inc/functions.php");

require_once ("../imap/autoload.php");



require_once ("../imap/ImapClient/ImapClientException.php");
require_once ("../imap/ImapClient/ImapConnect.php");
require_once ("../imap/ImapClient/ImapClient.php");
require_once ("../imap/ImapClient/IncomingMessage.php");
require_once ("../imap/ImapClient/TypeAttachments.php");
require_once ("../imap/ImapClient/TypeBody.php");

use SSilence\ImapClient\ImapClientException;

use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;


$mailbox = 'phonepartsusa.com';
$username = 'gohar@phonepartsusa.com';
$password = 'gohar!@#';
$encryption = Imap::ENCRYPT_SSL;

try{
    $imap = new Imap($mailbox, $username, $password, $encryption);
    // You can also check out example-connect.php for more connection options

}catch (ImapClientException $error){
    echo $error->getMessage().PHP_EOL; // You know the rule, no errors in production ...
    die(); // Oh no :( we failed
}

$imap->selectFolder('INBOX');
$emails = $imap->getMessages();
foreach ($emails as $key => $email) {
   $msgno = $email->header->msgno;
   
	$check = $db->func_query_first_cell("select msg_no from inv_server_emails where msg_no = '".$msgno."'");
	if (!$check) {
	    //testObject($email);
	    //echo utf8_decode($email->message->html->body);exit;
		$insert = array();
		$insert['msg_no'] = $msgno;
		$insert['from_name'] = $email->header->details->from[0]->personal;
		$insert['from_email'] = $email->header->details->from[0]->mailbox.'@'.$email->header->details->from[0]->host;
		$insert['subject'] = utf8_decode(trim($db->func_escape_string($email->header->subject)));
		/*$insert['subject'] = str_replace("<div>"," ",$insert['subject']);
		$insert['subject'] = str_replace("</div>"," ",$insert['subject']);
		$insert['subject'] = str_replace("<div"," ",$insert['subject']);
		$insert['subject'] = str_replace('dir="ltr"',' ',$insert['subject']);*/
		
		$insert['message'] = utf8_decode(trim($db->func_escape_string($email->message->html->body)));
		/*$insert['message'] = str_replace("<div>"," ",$insert['message']);
		$insert['message'] = str_replace("</div>"," ",$insert['message']);
		$insert['message'] = str_replace("<div"," ",$insert['message']);
		$insert['message'] = str_replace('dir="ltr"',' ',$insert['message']);*/
		
		$date = DateTime::createFromFormat( 'D, d M Y H:i:s O', $email->header->date);
		$insert['date_email'] =	$date->format( 'Y-m-d H:i:s');
		$insert['date_added'] =date('Y-m-d H:i:s');
		$db->func_array2insert("inv_server_emails", $insert); 		
	}
	}
	echo "success";
?>
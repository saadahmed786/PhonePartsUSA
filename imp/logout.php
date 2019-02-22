<?php
require_once("config.php");
unset($_SESSION);
unset($_SESSION['email']);
unset($_SESSION['list']);
unset($_SESSION['newlist']);

session_destroy();

header("Location:index.php");
exit;
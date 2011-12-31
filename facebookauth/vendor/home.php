<?php
# this script don't require authentication
session_start();
?>
<h2>Welcome, <?=(!empty($_SESSION) ? $_SESSION['username'] : 'Guest | <a href="login_facebook.php">Login?</a>'); ?></h2>
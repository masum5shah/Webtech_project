<?php
session_start();
session_unset();
session_destroy();
header("Location: tourist_login.php");
exit;
?>
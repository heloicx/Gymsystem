<?php
include 'auth.php';
logout_admin();
header('Location: login.php');
exit();
?>
<?php
session_start();
session_destroy();
header("Location: ../index.php"); // Or login.php
exit();
?>
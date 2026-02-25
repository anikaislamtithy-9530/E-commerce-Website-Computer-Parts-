<link rel="stylesheet" href="../assets/style.css">


<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>

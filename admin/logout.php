<?php
// =====================================
// Destroy Session & Redirect
// =====================================
session_start();
session_destroy();
header('Location: login.php');
exit;
?>
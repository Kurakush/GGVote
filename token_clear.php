<?php
if (session_status() === PHP_SESSION_NONE) session_start();

unset($_SESSION['last_token']);
$_SESSION['flash_message'] = "Token masqué.";
header("Location: profil_electeur.php");
exit;
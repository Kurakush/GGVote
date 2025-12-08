<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    // Si le formulaire admin est dans le header du site public :
    header("Location: ../index.php?error=admin_required");
    exit;
}
?>

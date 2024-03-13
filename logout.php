<?php
session_start(); // Porneste sesiunea
session_unset(); // Elimina toate variabilele de sesiune
session_destroy(); // Distruge sesiunea

header('Location: /Autentificare/autentificare.php'); // Redirecționează către pagina de autentificare
exit;
?>
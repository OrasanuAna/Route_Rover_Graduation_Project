<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

// Verifică dacă ID-ul a fost trimis
if (isset($_GET['id'])) {
    $soferID = mysqli_real_escape_string($conn, $_GET['id']);

    // Pregătește și execută interogarea SQL pentru a șterge șoferul
    $sql = "DELETE FROM Soferi WHERE SoferID = '$soferID'";

    if ($conn->query($sql) === TRUE) {
        echo "Șoferul a fost șters.";
    } else {
        echo "Eroare la ștergerea șoferului: " . $conn->error;
    }
}

$conn->close(); // Închide conexiunea la baza de date
header('Location: soferi.php'); // Redirecționează înapoi la pagina cu șoferi
exit;

?>

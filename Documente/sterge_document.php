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
    $documentID = mysqli_real_escape_string($conn, $_GET['id']);

    // Pregătește și execută interogarea SQL pentru a șterge documentul
    $sql = "DELETE FROM Documente WHERE DocumentID = '$documentID'";

    if ($conn->query($sql) === TRUE) {
        echo "Documentul a fost șters.";
    } else {
        echo "Eroare la ștergerea documentului: " . $conn->error;
    }
}

$conn->close(); // Închide conexiunea la baza de date
header('Location: documente.php'); // Redirecționează înapoi la pagina cu documente
exit;

?>

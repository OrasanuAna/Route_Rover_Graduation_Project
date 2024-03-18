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
    $contractID = mysqli_real_escape_string($conn, $_GET['id']);

    // Pregătește și execută interogarea SQL pentru a șterge contractul
    $sql = "DELETE FROM Contracte WHERE ContractID = '$contractID'";

    if ($conn->query($sql) === TRUE) {
        echo "Contractul a fost șters.";
    } else {
        echo "Eroare la ștergerea contractului: " . $conn->error;
    }
}

$conn->close(); // Închide conexiunea la baza de date
header('Location: contracte.php'); // Redirecționează înapoi la pagina cu contracte
exit;

?>

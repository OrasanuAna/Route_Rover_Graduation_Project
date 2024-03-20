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
    $taskID = mysqli_real_escape_string($conn, $_GET['id']);

    // Pregătește și execută interogarea SQL pentru a șterge task-ul
    $sql = "DELETE FROM Sarcini WHERE SarcinaID = '$taskID'";

    if ($conn->query($sql) === TRUE) {
        echo "Task-ul a fost șters.";
    } else {
        echo "Eroare la ștergerea task-ului: " . $conn->error;
    }
}

$conn->close(); // Închide conexiunea la baza de date
header('Location: /MeniuPrincipal/meniu_principal.php'); // Redirecționează înapoi la meniul principal
exit;

?>

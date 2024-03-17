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
    $vehiculID = mysqli_real_escape_string($conn, $_GET['id']);

    // Pregătește și execută interogarea SQL pentru a șterge vehiculul
    $sql = "DELETE FROM Vehicule WHERE VehiculID = '$vehiculID'";

    if ($conn->query($sql) === TRUE) {
        echo "Vehiculul a fost șters.";
    } else {
        echo "Eroare la ștergerea vehiculului: " . $conn->error;
    }
}

$conn->close(); // Închide conexiunea la baza de date
header('Location: vehicule.php'); // Redirecționează înapoi la pagina cu vehicule
exit;

?>

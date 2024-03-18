<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Dacă utilizatorul nu este autentificat, redirecționează
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

// Verifică dacă ID-ul documentului a fost furnizat
if (isset($_GET['id'])) {
    $documentId = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT NumeDocument, TipDocument, ContinutDocument, NumeFisier FROM Documente WHERE DocumentID = '$documentId' AND UtilizatorID = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Setează headerele corespunzătoare pentru tipul documentului
        header("Content-Type: " . $row['TipDocument']);
        header("Content-Disposition: inline; filename=\"" . $row['NumeDocument'] . "\"");

        // Trimite conținutul documentului
        echo $row['ContinutDocument'];
    } else {
        echo 'Documentul nu a fost găsit sau nu aveți acces la acesta.';
    }
    $conn->close();
} else {
    echo 'ID document necunoscut.';
}
?>

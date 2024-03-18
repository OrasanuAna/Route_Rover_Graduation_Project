<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Dacă utilizatorul nu este autentificat, redirecționează
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

// Verifică dacă ID-ul contractului a fost furnizat
if (isset($_GET['id'])) {
    $contractId = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT NumeContract, TipContract, ContinutContract, NumeFisier FROM Contracte WHERE ContractID = '$contractId' AND UtilizatorID = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Setează headerele corespunzătoare pentru tipul fișierului
        header("Content-Type: " . $row['TipContract']);
        header("Content-Disposition: inline; filename=\"" . $row['NumeFisier'] . "\""); // Folosește NumeFisier pentru a forța download-ul sau vizualizarea

        // Trimite conținutul contractului
        echo $row['ContinutContract'];
    } else {
        echo 'Contractul nu a fost găsit sau nu aveți acces la acesta.';
    }
    $conn->close();
} else {
    echo 'ID contract necunoscut.';
}
?>

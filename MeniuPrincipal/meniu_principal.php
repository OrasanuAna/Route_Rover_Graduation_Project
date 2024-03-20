<?php

session_start(); // Inițializează sesiunea

include '../db_connect.php'; // Include fișierul de conectare la baza de date

// Verifică dacă utilizatorul este autentificat (adică dacă există un user_id setat în sesiune)
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Verifică dacă avem un nume de utilizator setat în sesiune
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

$userID = $_SESSION['user_id']; // Extrage ID-ul utilizatorului conectat

// Include logica pentru a prelua sarcinile de la baza de date
$taskuriRestante = [];
$taskurileZilei = [];
$taskuriViitoare = [];

$today = date('Y-m-d');

// Pregătește și execută interogarea SQL pentru a prelua sarcinile asignate utilizatorului
$sql = "SELECT SarcinaID, NumeSarcina, TermenLimitaSarcina FROM Sarcini WHERE UtilizatorID = {$userID} ORDER BY TermenLimitaSarcina ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taskInfo = ['id' => $row['SarcinaID'], 'nume' => $row['NumeSarcina']];
        $taskDate = $row['TermenLimitaSarcina'];

        if ($taskDate < $today) {
            $taskuriRestante[] = $taskInfo;
        } elseif ($taskDate == $today) {
            $taskurileZilei[] = $taskInfo;
        } else {
            $taskuriViitoare[] = $taskInfo;
        }
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meniu Principal</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link href="/MeniuPrincipal/meniu_principal.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/MeniuPrincipal/meniu_principal.php">
                <img src="/Imagini/Logo.png" class="navbar-logo" alt="Logo" style="max-height: 50px;">
                Route Rover
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto custom-navbar">
                    <li class="nav-item active">
                        <a class="nav-link" href="/MeniuPrincipal/meniu_principal.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Profil/profil.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Soferi/soferi.php"><i class="fas fa-users"></i> Șoferi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Vehicule//vehicule.php"><i class="fas fa-truck"></i> Vehicule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Documente/documente.php"><i class="fas fa-file-alt"></i> Documente</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Contracte/contracte.php"><i class="fas fa-file-contract"></i> Contracte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Sarcini/adauga_task.php"><i class="fas fa-tasks"></i> Task nou</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto"> 
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Conținutul principal al paginii -->
        <div class="container">

            <h1 class="text-center">Welcome to Route Rover, <?php echo htmlspecialchars($username); ?>!</h1>

            <div class="container table-container">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <thead class="text-black" style="background-color: #ADD8E6;">
                                <tr>
                                    <th scope="col">Task-uri Restante</th>
                                    <th scope="col">Task-urile Zilei</th>
                                    <th scope="col">Task-uri Viitoare</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php foreach ($taskuriRestante as $task): ?>
                                            <div>
                                                <?php echo htmlspecialchars($task['nume']); ?>
                                                <a href="informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($taskurileZilei as $task): ?>
                                            <div>
                                                <?php echo htmlspecialchars($task['nume']); ?>
                                                <a href="informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($taskuriViitoare as $task): ?>
                                            <div>
                                                <?php echo htmlspecialchars($task['nume']); ?>
                                                <a href="informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    </body>
</html>

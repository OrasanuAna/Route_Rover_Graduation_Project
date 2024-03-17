<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$error = '';
$success = '';
$userID = $_SESSION['user_id']; // Preia ID-ul utilizatorului conectat

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numeDocument = mysqli_real_escape_string($conn, trim($_POST['numeDocument']));
    $tipDocument = mysqli_real_escape_string($conn, trim($_POST['tipDocument']));
    $dataIncarcare = mysqli_real_escape_string($conn, trim($_POST['dataIncarcare']));
    $caleFisier = mysqli_real_escape_string($conn, trim($_FILES['caleFisier']['name'])); // Presupunând că ai un câmp de încărcare a fișierului

    if (empty($numeDocument) || empty($tipDocument) || empty($dataIncarcare) || empty($caleFisier)) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        // Aici ar trebui să încarci fișierul într-un director specific și să obții calea finală pentru a o stoca în baza de date
        // De exemplu, încărcarea fișierului și obținerea căii finale
        $targetDirectory = "documente/"; // Schimbă cu directorul tău de încărcare
        $targetFile = $targetDirectory . basename($_FILES["caleFisier"]["name"]);
        move_uploaded_file($_FILES["caleFisier"]["tmp_name"], $targetFile);

        $sql = "INSERT INTO Documente (NumeDocument, TipDocument, DataIncarcareDocument, CaleFisierDocument, UtilizatorID)
                VALUES ('$numeDocument', '$tipDocument', '$dataIncarcare', '$targetFile', '$userID')";

        if (mysqli_query($conn, $sql)) {
            $success = 'Documentul a fost adăugat cu succes.';
        } else {
            $error = 'Eroare: ' . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link href="/Vehicule/adauga_vehicul.css" rel="stylesheet">
        <title>Adaugă un document</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/MeniuPrincipal/meniu_principal.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Profil/profil.php"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Soferi/soferi.php"><i class="fas fa-users"></i> Șoferi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Vehicule/vehicule.php"><i class="fas fa-truck"></i> Vehicule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Documente/documente.php"><i class="fas fa-file-alt"></i> Documente</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-file-contract"></i> Contracte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-tasks"></i> Task nou</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <h1 class="text-center my-4 mt-5 mb-5">Adaugă un document</h1>

            <!-- Zona pentru mesajul de eroare sau succes -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert-container">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center" role="alert"><?php echo $error; ?></div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success text-center" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Formularul de adăugare a unui document -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numeDocument">Nume document:</label>
                            <input type="text" class="form-control" id="numeDocument" name="numeDocument" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="tipDocument">Tip document:</label>
                            <input type="text" class="form-control" id="tipDocument" name="tipDocument" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataIncarcare">Data încărcării:</label>
                            <input type="date" class="form-control" id="dataIncarcare" name="dataIncarcare">
                        </div>
                        <div class="form-group">
                            <label for="caleFisier">Calea fișierului:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="caleFisier" name="caleFisier">
                                <label class="custom-file-label" for="caleFisier">Alege calea fișierului...</label>
                            </div>
                        </div>

                        <div class="float-right">
                            <button type="submit" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă documentul</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <!-- Script pentru a ascunde alertele după 3 secunde -->
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 3000);
            });
        </script>

    </body>
</html>
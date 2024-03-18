<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$error = '';
$success = '';
$userID = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificăm dacă toate câmpurile au fost completate
    if (empty($_POST['numeContract']) || empty($_POST['tipContract']) ||
        empty($_POST['dataInceputContract']) || empty($_POST['dataSfarsitContract']) ||
        !isset($_FILES['caleFisier']) || $_FILES['caleFisier']['error'] != 0) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        $numeContract = mysqli_real_escape_string($conn, $_POST['numeContract']);
        $tipContract = mysqli_real_escape_string($conn, $_POST['tipContract']);
        $dataInceputContract = mysqli_real_escape_string($conn, $_POST['dataInceputContract']);
        $dataSfarsitContract = mysqli_real_escape_string($conn, $_POST['dataSfarsitContract']);

        $filePath = $_FILES['caleFisier']['tmp_name'];
        $fileContent = file_get_contents($filePath);
        $fileName = mysqli_real_escape_string($conn, $_FILES['caleFisier']['name']);
        $fileType = mysqli_real_escape_string($conn, $_FILES['caleFisier']['type']); // Tipul fișierului încărcat

        $fileContentEscaped = mysqli_real_escape_string($conn, $fileContent);

        $sql = "INSERT INTO Contracte (NumeContract, TipContract, DataInceputContract, DataSfarsitContract, NumeFisier, ContinutContract, UtilizatorID)
                VALUES ('$numeContract', '$tipContract', '$dataInceputContract', '$dataSfarsitContract', '$fileName', '$fileContentEscaped', '$userID')";

        if (mysqli_query($conn, $sql)) {
            $success = 'Contractul a fost adăugat cu succes.';
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
        <link href="/Contracte/adauga_contract.css" rel="stylesheet">
        <title>Adaugă un contract</title>
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
                        <a class="nav-link" href="/Contracte/contracte.php"><i class="fas fa-file-contract"></i> Contracte</a>
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
            <h1 class="text-center my-4 mt-5 mb-5">Adaugă un contract</h1>

            <!-- Zona pentru mesajul de eroare sau succes -->
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="alert-container">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center" role="alert"><?php echo $error; ?></div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success text-center" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Formularul de adăugare a unui contract -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numeContract">Nume contract:</label>
                            <input type="text" class="form-control" id="numeContract" name="numeContract" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="tipContract">Tip contract:</label>
                            <input type="text" class="form-control" id="tipContract" name="tipContract" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="caleFisier">Selectați fișierul:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="caleFisier" name="caleFisier">
                                <label class="custom-file-label" for="caleFisier">Alege calea fișierului...</label>
                                <div id="fileNameDisplay" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataInceputContract">Data început contract:</label>
                            <input type="date" class="form-control" id="dataInceputContract" name="dataInceputContract">
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitContract">Data sfârșit contract:</label>
                            <input type="date" class="form-control" id="dataSfarsitContract" name="dataSfarsitContract">
                        </div>

                        <div class="float-right mt-5">
                            <button type="submit" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă contractul</button>
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

        <!-- Script pentru a afișa numele fișierului încărcat -->
        <script>
            $(document).ready(function() {
                $('.custom-file-input').on('change', function() {
                    // Obțineți numele fișierului din calea fișierului
                    var fileName = $(this).val().split('\\').pop();
                    // Actualizați elementul adăugat cu numele fișierului selectat
                    $('#fileNameDisplay').text(fileName);
                });
            });
        </script>

    </body>
</html>


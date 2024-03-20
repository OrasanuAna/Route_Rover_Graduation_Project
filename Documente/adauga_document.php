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
    if (empty($_POST['numeDocument']) || empty($_POST['tipDocument']) || empty($_POST['dataIncarcare']) ||
        !isset($_FILES['caleFisier']) || $_FILES['caleFisier']['error'] != 0) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        $numeDocument = mysqli_real_escape_string($conn, $_POST['numeDocument']);

        // Verifică dacă există deja un document cu același nume pentru utilizatorul curent
        $checkSql = "SELECT * FROM Documente WHERE NumeDocument = '$numeDocument' AND UtilizatorID = '$userID'";
        $checkResult = $conn->query($checkSql);
        if ($checkResult->num_rows > 0) {
            // Documentul există deja
            $error = 'Numele documentului există deja. Alegeți un alt nume.';
        } else {
            $tipDocument = mysqli_real_escape_string($conn, $_POST['tipDocument']);
            $dataIncarcare = mysqli_real_escape_string($conn, $_POST['dataIncarcare']);
            $filePath = $_FILES['caleFisier']['tmp_name']; // Calea temporară a fișierului încărcat
            $fileContent = file_get_contents($filePath);
            $fileName = mysqli_real_escape_string($conn, $_FILES['caleFisier']['name']);
            $fileContentEscaped = mysqli_real_escape_string($conn, $fileContent);

            $sql = "INSERT INTO Documente (NumeDocument, TipDocument, DataIncarcareDocument, ContinutDocument, NumeFisier, UtilizatorID)
                    VALUES ('$numeDocument', '$tipDocument', '$dataIncarcare', '$fileContentEscaped', '$fileName', '$userID')";

            if (mysqli_query($conn, $sql)) {
                $success = 'Documentul a fost adăugat cu succes.';
            } else {
                $error = 'Eroare: ' . mysqli_error($conn);
            }
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
        <link href="/Documente/adauga_document.css" rel="stylesheet">
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

        <div class="container">
            <h1 class="text-center my-4 mt-5 mb-5">Adaugă un document</h1>

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
                            <label for="caleFisier">Selectați fișierul:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="caleFisier" name="caleFisier">
                                <label class="custom-file-label" for="caleFisier">Alege calea fișierului...</label>
                                <div id="fileNameDisplay" class="mt-2"></div>
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
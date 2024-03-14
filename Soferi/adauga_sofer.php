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
    $nume = mysqli_real_escape_string($conn, trim($_POST['nume']));
    $prenume = mysqli_real_escape_string($conn, trim($_POST['prenume']));
    $telefon = mysqli_real_escape_string($conn, trim($_POST['telefon']));
    $dataNasterii = mysqli_real_escape_string($conn, trim($_POST['dataNasterii']));
    $dataAngajarii = mysqli_real_escape_string($conn, trim($_POST['dataAngajarii']));
    $dataSalariu = mysqli_real_escape_string($conn, trim($_POST['dataSalariu']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $dataEmiterePermis = mysqli_real_escape_string($conn, trim($_POST['dataEmiterePermis']));
    $dataExpirarePermis = mysqli_real_escape_string($conn, trim($_POST['dataExpirarePermis']));

    if (empty($nume) || empty($prenume) || empty($telefon) || empty($email) || empty($dataNasterii) || empty($dataAngajarii) || empty($dataSalariu) || empty($dataEmiterePermis) || empty($dataExpirarePermis)) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        $sql = "INSERT INTO Soferi (Nume, Prenume, Telefon, DataNasterii, DataAngajarii, DataSalariu, Email, DataEmiterePermis, DataExpirarePermis, UtilizatorID) 
                VALUES ('$nume', '$prenume', '$telefon', '$dataNasterii', '$dataAngajarii', '$dataSalariu', '$email', '$dataEmiterePermis', '$dataExpirarePermis', '$userID')";

        if (mysqli_query($conn, $sql)) {
            $success = 'Șoferul a fost adăugat cu succes.';
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
        <link href="/Soferi/adauga_sofer.css" rel="stylesheet">
        <title>Adaugă un șofer</title>
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
                        <a class="nav-link" href="#"><i class="fas fa-truck"></i> Vehicule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-file-alt"></i> Documente</a>
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
            <h1 class="text-center my-4 mt-5 mb-5">Adaugă un șofer</h1>

            <!-- Zona pentru mesajul de eroare sau succes -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert-container">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center" role="alert"><?php echo $error; ?></div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success text-center" role="alert"><?php echo $success; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Formularul de adăugare a unui șofer -->
            <form method="POST">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nume">Nume:</label>
                            <input type="text" class="form-control" id="nume" autocomplete="off" name="nume">
                        </div>
                        <div class="form-group">
                            <label for="prenume">Prenume:</label>
                            <input type="text" class="form-control" id="prenume" autocomplete="off" name="prenume">
                        </div>
                        <div class="form-group">
                            <label for="telefon">Telefon:</label>
                            <input type="text" class="form-control" id="telefon" autocomplete="off" name="telefon">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataNasterii">Data nașterii:</label>
                            <input type="date" class="form-control" id="dataNasterii" name="dataNasterii">
                        </div>
                        <div class="form-group">
                            <label for="dataAngajarii">Data angajării:</label>
                            <input type="date" class="form-control" id="dataAngajarii" name="dataAngajarii">
                        </div>
                        <div class="form-group">
                            <label for="dataSalariu">Data salariului:</label>
                            <input type="date" class="form-control" id="dataSalariu" name="dataSalariu">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" autocomplete="off" name="email">
                        </div>
                        <div class="form-group">
                            <label for="dataEmiterePermis">Data emiterii permisului:</label>
                            <input type="date" class="form-control" id="dataEmiterePermis" name="dataEmiterePermis">
                        </div>
                        <div class="form-group">
                            <label for="dataExpirarePermis">Data expirării permisului:</label>
                            <input type="date" class="form-control" id="dataExpirarePermis" name="dataExpirarePermis">
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă șoferul</button>
                </div>
            </form>
        </div>

    </body>

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

</html>
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
    $numarInmatriculare = mysqli_real_escape_string($conn, trim($_POST['numarInmatriculare']));
    $marcaModel = mysqli_real_escape_string($conn, trim($_POST['marcaModel']));
    $anFabricatie = mysqli_real_escape_string($conn, trim($_POST['anFabricatie']));
    $culoare = mysqli_real_escape_string($conn, trim($_POST['culoare']));
    $tipCombustibil = mysqli_real_escape_string($conn, trim($_POST['tipCombustibil']));
    $dataInceputITP = mysqli_real_escape_string($conn, trim($_POST['dataInceputITP']));
    $dataSfarsitITP = mysqli_real_escape_string($conn, trim($_POST['dataSfarsitITP']));
    $dataInceputAsigurare = mysqli_real_escape_string($conn, trim($_POST['dataInceputAsigurare']));
    $dataSfarsitAsigurare = mysqli_real_escape_string($conn, trim($_POST['dataSfarsitAsigurare']));
    $soferID = mysqli_real_escape_string($conn, trim($_POST['soferID']));

    if (empty($numarInmatriculare) || empty($marcaModel) || empty($anFabricatie) || empty($culoare) || empty($tipCombustibil) || empty($dataInceputITP) || empty($dataSfarsitITP) || empty($dataInceputAsigurare) || empty($dataSfarsitAsigurare) || empty($soferID)) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        $sql = "INSERT INTO Vehicule (NumarInmatriculare, MarcaModel, AnFabricatie, Culoare, TipCombustibil, DataInceputITP, DataSfarsitITP, DataInceputAsigurare, DataSfarsitAsigurare, SoferID, UtilizatorID)
                VALUES ('$numarInmatriculare', '$marcaModel', '$anFabricatie', '$culoare', '$tipCombustibil', '$dataInceputITP', '$dataSfarsitITP', '$dataInceputAsigurare', '$dataSfarsitAsigurare', '$soferID', '$userID')";

        if (mysqli_query($conn, $sql)) {
            $success = 'Vehiculul a fost adăugat cu succes.';
        } else {
            $error = 'Eroare: ' . mysqli_error($conn);
        }
    }
}

$query = "SELECT SoferID, Nume, Prenume FROM Soferi WHERE UtilizatorID = $userID";
$result = mysqli_query($conn, $query);
$soferiOptions = '<option value="">Selectează un șofer</option>';

while ($row = mysqli_fetch_assoc($result)) {
    $soferiOptions .= '<option value="' . $row['SoferID'] . '">' . htmlspecialchars($row['Nume']) . ' ' . htmlspecialchars($row['Prenume']) . '</option>';
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
        <title>Adaugă un vehicul</title>
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
            <h1 class="text-center my-4 mt-5 mb-5">Adaugă un vehicul</h1>

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

            <!-- Formularul de adăugare a unui vehicul -->
            <form method="POST">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numarInmatriculare">Număr de înmatriculare:</label>
                            <input type="text" class="form-control" id="numarInmatriculare" autocomplete="off" name="numarInmatriculare">
                        </div>
                        <div class="form-group">
                            <label for="marcaModel">Marca și model:</label>
                            <input type="text" class="form-control" id="marcaModel" autocomplete="off" name="marcaModel">
                        </div>
                        <div class="form-group">
                            <label for="anFabricatie">Anul fabricației:</label>
                            <input type="number" class="form-control" id="anFabricatie" min="2000" max="2024" autocomplete="off" name="anFabricatie">
                        </div>
                        <div class="form-group">
                            <label for="culoare">Culoare:</label>
                            <input type="text" class="form-control" id="culoare" autocomplete="off" name="culoare">
                        </div>
                        <div class="form-group">
                            <label for="tipCombustibil">Tipul de combustibil:</label>
                            <input type="text" class="form-control" id="tipCombustibil" autocomplete="off" name="tipCombustibil">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataInceputITP">Dată emitere ITP:</label>
                            <input type="date" class="form-control" id="dataInceputITP" name="dataInceputITP">
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitITP">Dată expirare ITP:</label>
                            <input type="date" class="form-control" id="dataSfarsitITP" name="dataSfarsitITP">
                        </div>
                        <div class="form-group">
                            <label for="dataInceputAsigurare">Dată emitere asigurare:</label>
                            <input type="date" class="form-control" id="dataInceputAsigurare" name="dataInceputAsigurare">
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitAsigurare">Dată expirare asigurare:</label>
                            <input type="date" class="form-control" id="dataSfarsitAsigurare" name="dataSfarsitAsigurare">
                        </div>
                        <div class="form-group">
                            <label for="soferID">Șoferul asignat:</label>
                            <select class="form-control" id="soferID" name="soferID">
                                <?php echo $soferiOptions; ?>
                            </select>
                        </div>
                        <div class="float-right">
                            <button type="submit" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă vehiculul</button>
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


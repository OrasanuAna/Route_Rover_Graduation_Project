<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$soferInfo = [];
$error = '';
$success = '';
$userID = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $soferID = mysqli_real_escape_string($conn, $_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nume = mysqli_real_escape_string($conn, trim($_POST['nume']));
        $prenume = mysqli_real_escape_string($conn, trim($_POST['prenume']));
        $telefon = mysqli_real_escape_string($conn, trim($_POST['telefon']));
        $dataNasterii = mysqli_real_escape_string($conn, $_POST['dataNasterii']);
        $dataAngajarii = mysqli_real_escape_string($conn, $_POST['dataAngajarii']);
        $dataSalariu = mysqli_real_escape_string($conn, $_POST['dataSalariu']);
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $dataEmiterePermis = mysqli_real_escape_string($conn, $_POST['dataEmiterePermis']);
        $dataExpirarePermis = mysqli_real_escape_string($conn, $_POST['dataExpirarePermis']);
        $dataInceputConcediu = mysqli_real_escape_string($conn, $_POST['dataInceputConcediu']);
        $dataSfarsitConcediu = mysqli_real_escape_string($conn, $_POST['dataSfarsitConcediu']);

        if (empty($nume) || empty($prenume) || empty($telefon) || empty($dataNasterii) || empty($dataAngajarii) || empty($dataSalariu) || empty($email) || empty($dataEmiterePermis) || empty($dataExpirarePermis) || empty($dataInceputConcediu) || empty($dataSfarsitConcediu)) {
            $error = 'Toate câmpurile sunt obligatorii.';
        } else {
            $sql = "SELECT * FROM Soferi WHERE SoferID = '$soferID' AND UtilizatorID = '$userID'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $existingInfo = $result->fetch_assoc();
                if ($nume != $existingInfo['Nume'] || $prenume != $existingInfo['Prenume'] || $telefon != $existingInfo['Telefon'] || $dataNasterii != $existingInfo['DataNasterii'] || $dataAngajarii != $existingInfo['DataAngajarii'] || $dataSalariu != $existingInfo['DataSalariu'] || $email != $existingInfo['Email'] || $dataEmiterePermis != $existingInfo['DataEmiterePermis'] || $dataExpirarePermis != $existingInfo['DataExpirarePermis'] || $dataInceputConcediu != $existingInfo['DataInceputConcediu'] || $dataSfarsitConcediu != $existingInfo['DataSfarsitConcediu']) {
                    $updateSql = "UPDATE Soferi SET Nume='$nume', Prenume='$prenume', Telefon='$telefon', DataNasterii='$dataNasterii', DataAngajarii='$dataAngajarii', DataSalariu='$dataSalariu', Email='$email', DataEmiterePermis='$dataEmiterePermis', DataExpirarePermis='$dataExpirarePermis', DataInceputConcediu='$dataInceputConcediu', DataSfarsitConcediu='$dataSfarsitConcediu' WHERE SoferID='$soferID' AND UtilizatorID = '$userID'";
                    if ($conn->query($updateSql) === TRUE) {
                        $success = 'Informațiile au fost actualizate cu succes.';
                    } else {
                        $error = 'Eroare la actualizarea datelor: ' . $conn->error;
                    }
                } else {
                    $error = 'Vă rugăm să modificați informațiile înainte de actualizare.';
                }
            } else {
                $error = 'Șoferul specificat nu există sau nu aveți permisiunea de a-l edita.';
            }
        }
    }

    $sql = "SELECT * FROM Soferi WHERE SoferID = '$soferID' AND UtilizatorID = '$userID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $soferInfo = $result->fetch_assoc();
    } else {
        $error = "Nu au fost găsite informații pentru șoferul specificat sau nu aveți permisiunea de a vizualiza aceste informații.";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link href="/Soferi/informatii_sofer.css" rel="stylesheet">
        <title>Informații despre șofer</title>
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
                        <a class="nav-link" href="/Rapoarte/genereaza_raport.php"><i class="fas fa-chart-bar"></i> Rapoarte</a>
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
            <h1 class="text-center my-4 mt-5">Informații despre șoferul <u><?php echo htmlspecialchars($soferInfo['Nume']) . ' ' . htmlspecialchars($soferInfo['Prenume']); ?></u></h1>
            <!-- Zona pentru mesajul de eroare sau succes -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert-container">
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success text-center" role="alert">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Formularul de editare a șoferului -->
            <form method="POST">
                <div class="row">
                    <!-- Prima coloană -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nume">Nume:</label>
                            <div class="input-group position-relative">
                                <input type="text" class="form-control" id="nume" name="nume" autocomplete="off" value="<?php echo htmlspecialchars($soferInfo['Nume']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenume">Prenume:</label>
                            <div class="input-group position-relative">
                                <input type="text" class="form-control" id="prenume" name="prenume" autocomplete="off" value="<?php echo htmlspecialchars($soferInfo['Prenume']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="telefon">Telefon:</label>
                            <div class="input-group position-relative">
                                <input type="text" class="form-control" id="telefon" name="telefon" autocomplete="off"  value="<?php echo htmlspecialchars($soferInfo['Telefon']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataInceputConcediu">Data început concediu:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataInceputConcediu" name="dataInceputConcediu" value="<?php echo htmlspecialchars($soferInfo['DataInceputConcediu']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- A doua coloană -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataNasterii">Data nașterii:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataNasterii" name="dataNasterii" value="<?php echo htmlspecialchars($soferInfo['DataNasterii']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataAngajarii">Data angajării:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataAngajarii" name="dataAngajarii" value="<?php echo htmlspecialchars($soferInfo['DataAngajarii']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataSalariu">Data salariului:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataSalariu" name="dataSalariu" value="<?php echo htmlspecialchars($soferInfo['DataSalariu']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitConcediu">Data sfârșit concediu:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataSfarsitConcediu" name="dataSfarsitConcediu" value="<?php echo htmlspecialchars($soferInfo['DataSfarsitConcediu']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- A treia coloană -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <div class="input-group position-relative">
                                <input type="email" class="form-control" id="email" name="email" autocomplete="off" value="<?php echo htmlspecialchars($soferInfo['Email']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataEmiterePermis">Data emiterii permisului:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataEmiterePermis" name="dataEmiterePermis" value="<?php echo htmlspecialchars($soferInfo['DataEmiterePermis']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataExpirarePermis">Data expirării permisului:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataExpirarePermis" name="dataExpirarePermis" value="<?php echo htmlspecialchars($soferInfo['DataExpirarePermis']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col text-right">
                        <button type="submit" class="btn custom-update-btn"><i class="fas fa-redo"></i> Actualizează informațiile</button>
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

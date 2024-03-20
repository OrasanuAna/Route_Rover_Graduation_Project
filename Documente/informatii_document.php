<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$documentInfo = [];
$error = '';
$success = '';
$userID = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $documentID = mysqli_real_escape_string($conn, $_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $numeDocument = mysqli_real_escape_string($conn, $_POST['numeDocument'] ?? '');
        $tipDocument = mysqli_real_escape_string($conn, $_POST['tipDocument'] ?? '');
        $dataIncarcare = mysqli_real_escape_string($conn, $_POST['dataIncarcareDocument'] ?? '');
        $fileChanged = isset($_FILES['caleFisier']) && $_FILES['caleFisier']['error'] == 0;

        $sql = "SELECT * FROM Documente WHERE DocumentID = '$documentID' AND UtilizatorID = '$userID'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $existingInfo = $result->fetch_assoc();

            // Verifica daca au fost efectuate modificari
            $changesMade = $numeDocument != $existingInfo['NumeDocument'] || 
                           $tipDocument != $existingInfo['TipDocument'] || 
                           $dataIncarcare != $existingInfo['DataIncarcareDocument'] || 
                           $fileChanged;

            // Verifica daca campurile obligatorii au fost completate si daca au fost efectuate modificari
            if (empty($numeDocument) || empty($tipDocument) || empty($dataIncarcare) || !$fileChanged || !$changesMade) {
                $error = 'Toate câmpurile sunt obligatorii și trebuie să faci o modificare pentru a actualiza.';
            } else {
                if ($fileChanged) {
                    $filePath = $_FILES['caleFisier']['tmp_name'];
                    $fileContent = file_get_contents($filePath);
                    $fileName = mysqli_real_escape_string($conn, $_FILES['caleFisier']['name']);
                    $fileContentEscaped = mysqli_real_escape_string($conn, $fileContent);

                    $updateSql = "UPDATE Documente SET
                                  NumeDocument = '$numeDocument',
                                  TipDocument = '$tipDocument',
                                  DataIncarcareDocument = '$dataIncarcare',
                                  ContinutDocument = '$fileContentEscaped',
                                  NumeFisier = '$fileName'
                                  WHERE DocumentID = '$documentID' AND UtilizatorID = '$userID'";
                } else {
                    $updateSql = "UPDATE Documente SET
                                  NumeDocument = '$numeDocument',
                                  TipDocument = '$tipDocument',
                                  DataIncarcareDocument = '$dataIncarcare'
                                  WHERE DocumentID = '$documentID' AND UtilizatorID = '$userID'";
                }

                if ($conn->query($updateSql) === TRUE) {
                    $success = 'Informațiile au fost actualizate cu succes.';
                } else {
                    $error = 'Eroare la actualizarea datelor: ' . $conn->error;
                }
            }
        } else {
            $error = 'Documentul specificat nu există sau nu aveți permisiunea de a-l edita.';
        }
    }

    // Re-fetch the updated info
    $sql = "SELECT * FROM Documente WHERE DocumentID = '$documentID' AND UtilizatorID='$userID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $documentInfo = $result->fetch_assoc();
        $documentInfo['DataIncarcare'] = !empty($documentInfo['DataIncarcareDocument']) ? date('Y-m-d', strtotime($documentInfo['DataIncarcareDocument'])) : '';
    } else {
        $error = "Nu au fost găsite informații pentru documentul specificat sau nu aveți permisiunea de a vizualiza aceste informații.";
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
        <link href="/Documente/informatii_document.css" rel="stylesheet">
        <title>Informații despre document</title>
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
            <h1 class="text-center my-4 mt-5">Informații despre documentul <u><?php echo htmlspecialchars($documentInfo['NumeDocument']); ?></u></h1>
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

            <form method="POST" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numeDocument">Nume document:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="numeDocument" name="numeDocument" autocomplete="off" value="<?php echo htmlspecialchars($documentInfo['NumeDocument']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipDocument">Tip document:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tipDocument" name="tipDocument" autocomplete="off" value="<?php echo htmlspecialchars($documentInfo['TipDocument']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataIncarcare">Data încărcării:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataIncarcare" name="dataIncarcareDocument" value="<?php echo htmlspecialchars($documentInfo['DataIncarcare']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="caleFisier">Selectați fișierul:</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="caleFisier" name="caleFisier">
                                    <label class="custom-file-label" for="caleFisier">Alege calea fișierului...</label>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                            <div id="fileNameDisplay" class="mt-2">
                            <?php if (!empty($documentInfo['NumeFisier'])): ?>
                                <?php echo htmlspecialchars($documentInfo['NumeFisier']); ?>
                            <?php endif; ?>
                            </div>
                        </div>
                            <div class="float-right">
                                <button type="submit" class="btn custom-update-btn"><i class="fas fa-redo"></i> Actualizează informațiile</button>
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
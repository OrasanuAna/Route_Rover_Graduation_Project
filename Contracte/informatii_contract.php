<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$contractInfo = [];
$error = '';
$success = '';
$userID = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $contractID = mysqli_real_escape_string($conn, $_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $numeContract = mysqli_real_escape_string($conn, $_POST['numeContract'] ?? '');
        $tipContract = mysqli_real_escape_string($conn, $_POST['tipContract'] ?? '');
        $dataInceputContract = mysqli_real_escape_string($conn, $_POST['dataInceputContract'] ?? '');
        $dataSfarsitContract = mysqli_real_escape_string($conn, $_POST['dataSfarsitContract'] ?? '');
        $fileChanged = isset($_FILES['caleFisier']) && $_FILES['caleFisier']['error'] == 0;

        $sql = "SELECT * FROM Contracte WHERE ContractID = '$contractID' AND UtilizatorID = '$userID'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $existingInfo = $result->fetch_assoc();

            // Verifica daca au fost efectuate modificari
            $changesMade = $numeContract != $existingInfo['NumeContract'] || 
                           $tipContract != $existingInfo['TipContract'] || 
                           $dataInceputContract != $existingInfo['DataInceputContract'] || 
                           $dataSfarsitContract != $existingInfo['DataSfarsitContract'] || 
                           $fileChanged;

            // Verifica daca campurile obligatorii au fost completate si daca au fost efectuate modificari
            if (empty($numeContract) || empty($tipContract) || empty($dataInceputContract) || empty($dataSfarsitContract) || !$fileChanged || !$changesMade) {
                $error = 'Toate câmpurile sunt obligatorii și trebuie să faci o modificare pentru a actualiza.';
            } else {
                if ($fileChanged) {
                    $filePath = $_FILES['caleFisier']['tmp_name'];
                    $fileContent = file_get_contents($filePath);
                    $fileName = mysqli_real_escape_string($conn, $_FILES['caleFisier']['name']);
                    $fileContentEscaped = mysqli_real_escape_string($conn, $fileContent);

                    $updateSql = "UPDATE Contracte SET
                                  NumeContract = '$numeContract',
                                  TipContract = '$tipContract',
                                  DataInceputContract = '$dataInceputContract',
                                  DataSfarsitContract = '$dataSfarsitContract',
                                  ContinutContract = '$fileContentEscaped',
                                  NumeFisier = '$fileName'
                                  WHERE ContractID = '$contractID' AND UtilizatorID = '$userID'";
                } else {
                    $updateSql = "UPDATE Contracte SET
                                  NumeContract = '$numeContract',
                                  TipContract = '$tipContract',
                                  DataInceputContract = '$dataInceputContract',
                                  DataSfarsitContract = '$dataSfarsitContract'
                                  WHERE ContractID = '$contractID' AND UtilizatorID = '$userID'";
                }

                if ($conn->query($updateSql) === TRUE) {
                    $success = 'Informațiile contractului au fost actualizate cu succes.';
                } else {
                    $error = 'Eroare la actualizarea datelor: ' . $conn->error;
                }
            }
        } else {
            $error = 'Contractul specificat nu există sau nu aveți permisiunea de a-l edita.';
        }
    }

    // Re-fetch the updated info
    $sql = "SELECT * FROM Contracte WHERE ContractID = '$contractID' AND UtilizatorID='$userID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $contractInfo = $result->fetch_assoc();
    } else {
        $error = "Nu au fost găsite informații pentru contractul specificat sau nu aveți permisiunea de a vizualiza aceste informații.";
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
        <link href="/Contracte/informatii_contract.css" rel="stylesheet">
        <title>Informații despre contract</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="/Grafice/grafice.php"><i class="fas fa-chart-pie"></i> Grafice</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" style="padding-top: 12px;" href="#" id="themeToggle"><i class="fas fa-sun"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <h1 class="text-center my-4 mt-5">Informații despre contractul <u><?php echo htmlspecialchars($contractInfo['NumeContract']); ?></u></h1>
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
                            <label for="numeContract">Nume contract:</label>
                            <div class="input-group position-relative">
                                <input type="text" class="form-control" id="numeContract" name="numeContract" autocomplete="off" value="<?php echo htmlspecialchars($contractInfo['NumeContract']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipContract">Tip contract:</label>
                            <div class="input-group position-relative">
                                <input type="text" class="form-control" id="tipContract" name="tipContract" autocomplete="off" value="<?php echo htmlspecialchars($contractInfo['TipContract']); ?>">
                                <i class="fas fa-pencil-alt field-icon" style="color: #495057;"></i>
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
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataInceputContract">Data început contract:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataInceputContract" name="dataInceputContract" value="<?php echo htmlspecialchars($contractInfo['DataInceputContract']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitContract">Data sfârșit contract:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataSfarsitContract" name="dataSfarsitContract" value="<?php echo htmlspecialchars($contractInfo['DataSfarsitContract']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="float-right mt-5">
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

        <script>
            // Schimbă tema la clic pe iconiță
            document.getElementById('themeToggle').addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const themeIcon = this.querySelector('i');
                if (document.body.classList.contains('dark-mode')) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'light');
                }
            });

            // Setează tema inițială în funcție de preferința stocată
            window.addEventListener('DOMContentLoaded', () => {
                const storedTheme = localStorage.getItem('theme') || 'light';
                if (storedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                    document.getElementById('themeToggle').querySelector('i').classList.add('fa-moon');
                    document.getElementById('themeToggle').querySelector('i').classList.remove('fa-sun');
                }
            });
        </script>

        <!-- CSS pentru tema dark mode -->
        <style>
            .dark-mode {
                background-color: #1A2733;
                color: white;
            }

            .dark-mode .navbar {
                background-color: #0A0F19 !important;
            }

            .dark-mode .navbar-light .navbar-brand,
            .dark-mode .navbar-light .navbar-nav .nav-link {
                color: white !important;
            }

            .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect):hover::after,
            .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect):focus::after {
                background-color: #fff;
            }

            .dark-mode .navbar-light .navbar-nav .nav-item .nav-link:not(.no-hover-effect)::after {
                background-color: #fff; 
            }

            .dark-mode .navbar-light .navbar-nav .nav-link:hover,
            .dark-mode .navbar-light .navbar-nav .nav-link:focus {
                color: #ddd;
            }

            .dark-mode .table {
                background-color: #1A2733;
                color: white;
            }

            .dark-mode .table th {
                background-color: #0A0F19;
                border: none;
            }

            .dark-mode .table td {
                background-color: #1A2733;
            }

            .dark-mode tbody tr:hover {
                background-color: #0A0F19; /* Schimbă culoarea de fundal la hover */
            }

            .dark-mode .btn.custom-btn {
                background-color: #0A0F19;
                border-color: #fff;
                color: #fff;
            }
            
            .dark-mode .btn.custom-btn:hover {
                background-color: #05060A;
                border-color: #fff;
            }

            .dark-mode .custom-update-btn {
                background-color: #0A0F19;
                border-color: #fff;
                color: #fff;
            }

            .dark-mode .custom-update-btn:hover {
                background-color: #05060A;
                border-color: #fff;
            }

            .dark-mode .btn-add {
                background-color: #0A0F19;
                border-color: #fff;
                color: #fff;
            }

            .dark-mode .btn-add:hover {
                background-color: #05060A;
                border-color: #fff;
            }

            .dark-mode .confirm-icon {
                color: #3BD16F;
            }

            .dark-mode .confirm-icon:hover {
                color: #006400;
            }
        </style>

    </body>
</html>
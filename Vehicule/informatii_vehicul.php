<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php';

$vehiculInfo = [];
$error = '';
$success = '';
$userID = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $vehiculID = mysqli_real_escape_string($conn, $_GET['id']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $numarInmatriculare = mysqli_real_escape_string($conn, $_POST['numarInmatriculare']);
        $marcaModel = mysqli_real_escape_string($conn, $_POST['marcaModel']);
        $anFabricatie = mysqli_real_escape_string($conn, $_POST['anFabricatie']);
        $culoare = mysqli_real_escape_string($conn, $_POST['culoare']);
        $tipCombustibil = mysqli_real_escape_string($conn, $_POST['tipCombustibil']);
        $dataInceputITP = mysqli_real_escape_string($conn, $_POST['dataInceputITP']);
        $dataSfarsitITP = mysqli_real_escape_string($conn, $_POST['dataSfarsitITP']);
        $dataInceputAsigurare = mysqli_real_escape_string($conn, $_POST['dataInceputAsigurare']);
        $dataSfarsitAsigurare = mysqli_real_escape_string($conn, $_POST['dataSfarsitAsigurare']);
        $soferID = mysqli_real_escape_string($conn, $_POST['soferID']);

        // Verificăm dacă toate câmpurile formularului sunt completate
        if (empty($numarInmatriculare) || empty($marcaModel) || empty($anFabricatie) || empty($culoare) || empty($tipCombustibil) || empty($dataInceputITP) || empty($dataSfarsitITP) || empty($dataInceputAsigurare) || empty($dataSfarsitAsigurare) || empty($soferID)) {
            $error = 'Toate câmpurile sunt obligatorii.';
        } else {
            // Extrage informațiile actuale ale vehiculului din baza de date
            $sql = "SELECT * FROM Vehicule WHERE VehiculID = '$vehiculID' AND UtilizatorID = '$userID'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $existingInfo = $result->fetch_assoc();
                // Verifică dacă s-au făcut modificări în formular față de baza de date
                if ($numarInmatriculare != $existingInfo['NumarInmatriculare'] || $marcaModel != $existingInfo['MarcaModel'] || $anFabricatie != $existingInfo['AnFabricatie'] || $culoare != $existingInfo['Culoare'] || $tipCombustibil != $existingInfo['TipCombustibil'] || $dataInceputITP != $existingInfo['DataInceputITP'] || $dataSfarsitITP != $existingInfo['DataSfarsitITP'] || $dataInceputAsigurare != $existingInfo['DataInceputAsigurare'] || $dataSfarsitAsigurare != $existingInfo['DataSfarsitAsigurare'] || $soferID != $existingInfo['SoferID']) {
                    // Actualizează informațiile vehiculului în baza de date
                    $updateSql = "UPDATE Vehicule SET NumarInmatriculare='$numarInmatriculare', MarcaModel='$marcaModel', AnFabricatie='$anFabricatie', Culoare='$culoare', TipCombustibil='$tipCombustibil', DataInceputITP='$dataInceputITP', DataSfarsitITP='$dataSfarsitITP', DataInceputAsigurare='$dataInceputAsigurare', DataSfarsitAsigurare='$dataSfarsitAsigurare', SoferID='$soferID' WHERE VehiculID='$vehiculID' AND UtilizatorID='$userID'";
                    if ($conn->query($updateSql) === TRUE) {
                        $success = 'Informațiile au fost actualizate cu succes.';
                    } else {
                        $error = 'Eroare la actualizarea datelor: ' . $conn->error;
                    }
                } else {
                    $error = 'Vă rugăm să modificați informațiile înainte de actualizare.';
                }
            } else {
                $error = 'Vehiculul specificat nu există sau nu aveți permisiunea de a-l edita.';
            }
        }
    }

    $sql = "SELECT * FROM Vehicule WHERE VehiculID = '$vehiculID' AND UtilizatorID='$userID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $vehiculInfo = $result->fetch_assoc();
    } else {
        $error = "Nu au fost găsite informații pentru vehiculul specificat sau nu aveți permisiunea de a vizualiza aceste informații.";
    }
}

$query = "SELECT SoferID, Nume, Prenume FROM Soferi WHERE UtilizatorID = $userID";
$result = mysqli_query($conn, $query);
$soferiOptions = '<option value="">Selectează un șofer</option>';

while ($row = mysqli_fetch_assoc($result)) {
    $selected = ($row['SoferID'] == $vehiculInfo['SoferID']) ? 'selected' : ''; // Verifică dacă acesta este șoferul vehiculului
    $soferiOptions .= '<option value="' . $row['SoferID'] . '"' . $selected . '>' . htmlspecialchars($row['Nume']) . ' ' . htmlspecialchars($row['Prenume']) . '</option>';
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
        <link href="/Vehicule/informatii_vehicul.css" rel="stylesheet">
        <title>Informații despre vehicul</title>
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
            <h1 class="text-center my-4 mt-5">Informații despre vehiculul <u><?php echo htmlspecialchars($vehiculInfo['MarcaModel']) . ' (' . htmlspecialchars($vehiculInfo['NumarInmatriculare']) . ')'; ?></u></h1>
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

            <form method="POST">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numarInmatriculare">Număr de înmatriculare:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="numarInmatriculare" name="numarInmatriculare" value="<?php echo htmlspecialchars($vehiculInfo['NumarInmatriculare']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="marcaModel">Marca și model:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="marcaModel" name="marcaModel" value="<?php echo htmlspecialchars($vehiculInfo['MarcaModel']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="anFabricatie">Anul fabricației:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="anFabricatie" name="anFabricatie" value="<?php echo htmlspecialchars($vehiculInfo['AnFabricatie']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="culoare">Culoare:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="culoare" name="culoare" value="<?php echo htmlspecialchars($vehiculInfo['Culoare']); ?>" autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipCombustibil">Tipul de combustibil:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tipCombustibil" name="tipCombustibil" value="<?php echo htmlspecialchars($vehiculInfo['TipCombustibil']); ?>" autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataInceputITP">Dată emitere ITP:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataInceputITP" name="dataInceputITP" value="<?php echo htmlspecialchars($vehiculInfo['DataInceputITP']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitITP">Dată expirare ITP:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataSfarsitITP" name="dataSfarsitITP" value="<?php echo htmlspecialchars($vehiculInfo['DataSfarsitITP']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataInceputAsigurare">Dată emitere asigurare:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataInceputAsigurare" name="dataInceputAsigurare" value="<?php echo htmlspecialchars($vehiculInfo['DataInceputAsigurare']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dataSfarsitAsigurare">Dată expirare asigurare:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="dataSfarsitAsigurare" name="dataSfarsitAsigurare" value="<?php echo htmlspecialchars($vehiculInfo['DataSfarsitAsigurare']); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="soferID">Șoferul asignat:</label>
                            <div class="input-group">
                                <select class="form-control" id="soferID" name="soferID">
                                    <?php echo $soferiOptions; ?>
                                </select>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
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

    </body>
</html>
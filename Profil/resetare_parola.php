<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Inițializează mesajul de eroare și succes
$error = '';
$success = '';

// Conectare la baza de date
include '../db_connect.php';

// Verifică dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = mysqli_real_escape_string($conn, $_POST['oldPassword']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['newPassword']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

    // Verifică dacă toate câmpurile au fost completate
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Toate câmpurile sunt obligatorii.';
    } else {
        $user_id = $_SESSION['user_id'];
        // Verifică dacă parola veche este corectă
        $sql = "SELECT Parola FROM Utilizatori WHERE UtilizatorID = '$user_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($oldPassword, $user['Parola'])) {
                // Verifică dacă noua parolă și confirmarea acesteia se potrivesc
                if ($newPassword === $confirmPassword) {
                    // Actualizează parola
                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateSql = "UPDATE Utilizatori SET Parola = '$newPasswordHash' WHERE UtilizatorID = '$user_id'";
                    if ($conn->query($updateSql) === TRUE) {
                        $success = 'Parola a fost actualizată cu succes. Redirecționare...';
                        header("refresh:2;url=/Profil/profil.php"); // Redirect după 2 secunde
                    } else {
                        $error = 'Eroare la actualizarea parolei: ' . $conn->error;
                    }
                } else {
                    $error = 'Noua parolă și confirmarea parolei nu se potrivesc.';
                }
            } else {
                $error = 'Parola actuală introdusă nu este corectă.';
            }
        } else {
            $error = 'A apărut o eroare. Vă rugăm să încercați din nou.';
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="/Profil/resetare_parola.css" rel="stylesheet">
    <title>Resetare parolă</title>
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

    <div class="container mt-5">
        <h1 class="text-center">Resetare parolă</h1>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="alert-container">
                    <?php if ($error != ''): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($success != ''): ?>
                        <div class="alert alert-success text-center" role="alert">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label for="oldPassword">Parola actuală:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="oldPassword" name="oldPassword">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password"><i class="fas fa-eye-slash" style="cursor: pointer;"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Parola nouă:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" name="newPassword">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password"><i class="fas fa-eye-slash" style="cursor: pointer;"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirmă parola:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password"><i class="fas fa-eye-slash" style="cursor: pointer;"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-primary custom-reset-btn">Resetează parola</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script src="/Inregistrare/inregistrare.js"></script>

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
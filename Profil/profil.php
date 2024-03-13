<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Conectare la baza de date

include '../db_connect.php'; // Include your database connection file

$userInfo = []; // Inițializează array-ul pentru informațiile utilizatorului

// Verifică dacă avem un ID de utilizator setat în sesiune
if (isset($_SESSION['user_id'])) {
    $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

    // Pregătește și execută interogarea SQL
    $sql = "SELECT Nume, Prenume, Email, Telefon, NumeUtilizator FROM Utilizatori WHERE UtilizatorID = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc(); // Păstrează informațiile utilizatorului
    } else {
        // Handle error or redirect
        $error = "Nu s-au găsit informații pentru utilizatorul specificat.";
    }
}

$conn->close(); // Închide conexiunea la baza de date


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="/Profil/profil.css" rel="stylesheet">
    <title>Profil</title>
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
                    <a class="nav-link" href="#"><i class="fas fa-users"></i> Șoferi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-file-alt"></i> Documente</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-file-contract"></i> Contracte</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-truck"></i> Vehicule</a>
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
        <h1 class="text-center">Informații profil</h1>
        <form>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <!-- Câmpurile pentru nume, prenume și nume de utilizator -->
                    <div class="form-group">
                        <label for="nume">Nume:</label>
                        <div class="input-group">
                            <input type="text" class="form-control custom-input" id="nume" placeholder="<?php echo htmlspecialchars($userInfo['Nume']); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prenume">Prenume:</label>
                        <div class="input-group">
                            <input type="text" class="form-control custom-input" id="prenume" placeholder="<?php echo htmlspecialchars($userInfo['Prenume']); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username">Nume de utilizator:</label>
                        <input type="text" class="form-control custom-input" id="username" placeholder="<?php echo htmlspecialchars($userInfo['NumeUtilizator']); ?>" readonly>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Numele de utilizator nu poate fi schimbat.
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Câmpurile pentru email și număr de telefon -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="input-group">
                            <input type="email" class="form-control custom-input" id="email" placeholder="<?php echo htmlspecialchars($userInfo['Email']); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefon">Nr. Telefon:</label>
                        <div class="input-group">
                            <input type="text" class="form-control custom-input" id="telefon" placeholder="<?php echo htmlspecialchars($userInfo['Telefon']); ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                        </div>
                    </div>
                    <!-- Gruparea butoanelor -->
                    <div class="form-group d-flex align-items-end justify-content-end">
                        <!-- Buton pentru resetarea parolei -->
                        <button type="button" class="btn custom-reset-btn mr-3">
                            <i class="fas fa-key"></i> Resetare parolă
                        </button>
                        <!-- Buton pentru trimiterea formularului -->
                        <button type="submit" class="btn btn-outline-primary custom-update-btn">
                            <i class="fas fa-redo"></i> Actualizează informațiile
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
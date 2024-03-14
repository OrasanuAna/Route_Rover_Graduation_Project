<?php
session_start(); // Asigură-te că această linie este la începutul fișierului

// Verifică dacă utilizatorul este autentificat (adică dacă există un user_id setat în sesiune)
if (!isset($_SESSION['user_id'])) {
    // Dacă nu este autentificat, redirecționează utilizatorul către pagina de autentificare
    header('Location: /Autentificare/autentificare.php');
    exit; // Oprirea execuției scriptului ulterior
}

// Verifică dacă avem un nume de utilizator setat în sesiune
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Presupunem că numele de utilizator este stocat în sesiune sub cheia 'username'
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meniu Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="/MeniuPrincipal/meniu_principal.css" rel="stylesheet">
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
                <li class="nav-item active">
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

    <!-- Conținutul principal al paginii -->
    <div class="container">

        <h1 class="text-center">Welcome to Route Rover, <?php echo htmlspecialchars($username); ?>!</h1>

        <div class="container table-container">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead class="text-black" style="background-color: #ADD8E6;">
                            <tr>
                                <th scope="col">Task-uri Restante</th>
                                <th scope="col">Task-urile Zilei</th>
                                <th scope="col">Task-uri Viitoare</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Exemple de linii de tabel; poti adauga sau modifica conform necesitatilor tale -->
                            <tr>
                                <td>Task 1 restant</td>
                                <td>Task 1 de azi</td>
                                <td>Task 1 viitor</td>
                            </tr>
                            <tr>
                                <td>Task 2 restant</td>
                                <td>Task 2 de azi</td>
                                <td>Task 2 viitor</td>
                            </tr>
                            <!-- Adauga mai multe randuri dupa necesitate -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

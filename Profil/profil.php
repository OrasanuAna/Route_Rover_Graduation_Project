<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Conectare la baza de date
include '../db_connect.php';

// Inițializează variabilele pentru mesaje
$error = '';
$success = '';

$userInfo = [];

// Pregătește și execută interogarea SQL pentru a obține informațiile actuale ale utilizatorului
if (isset($_SESSION['user_id'])) {
    $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
    $sql = "SELECT Nume, Prenume, Email, Telefon, NumeUtilizator FROM Utilizatori WHERE UtilizatorID = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();
    } else {
        $error = "Nu s-au găsit informații pentru utilizatorul specificat.";
    }
}

// Verifică dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_nume = mysqli_real_escape_string($conn, trim($_POST['nume']));
    $new_prenume = mysqli_real_escape_string($conn, trim($_POST['prenume']));
    $new_email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $new_telefon = mysqli_real_escape_string($conn, trim($_POST['telefon']));

    // Verifică dacă toate câmpurile sunt completate
    if (empty($new_nume) || empty($new_prenume) || empty($new_email) || empty($new_telefon)) {
        $error = "Toate câmpurile sunt obligatorii.";
    } elseif ($new_nume == $userInfo['Nume'] && $new_prenume == $userInfo['Prenume'] && $new_email == $userInfo['Email'] && $new_telefon == $userInfo['Telefon']) {
        $error = "Vă rugăm să modificați informațiile înainte de actualizare.";
    } else {
        // Actualizează informațiile utilizatorului
        $sql_update = "UPDATE Utilizatori SET Nume='$new_nume', Prenume='$new_prenume', Email='$new_email', Telefon='$new_telefon' WHERE UtilizatorID = '$user_id'";
        if ($conn->query($sql_update) === TRUE) {
            $success = "Informațiile au fost actualizate cu succes.";
            // Reîncarcă informațiile utilizatorului după actualizare
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $userInfo = $result->fetch_assoc();
            }
        } else {
            $error = "Eroare la actualizarea informațiilor: " . $conn->error;
        }
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
                <li class="nav-item active">
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
                    <a class="nav-link no-hover-effect" style="padding-top: 12px;" href="#" id="themeToggle"><i class="fas fa-sun"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">

        <h1 class="text-center">Informații profil</h1>

        <div class="row justify-content-center">
            <div class="col-md-5">
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
            </div>
        </div>

        <form method="POST">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <!-- Câmpurile pentru nume, prenume și nume de utilizator -->
                    <div class="form-group">
                        <label for="nume">Nume:</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control custom-input" id="nume" name="nume" autocomplete="off" value="<?php echo htmlspecialchars($userInfo['Nume']); ?>">
                            <i class="fas fa-edit field-icon" style="color: #495057;"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prenume">Prenume:</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control custom-input" id="prenume" name="prenume" autocomplete="off" value="<?php echo htmlspecialchars($userInfo['Prenume']); ?>">
                            <i class="fas fa-edit field-icon" style="color: #495057;"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username">Nume de utilizator:</label>
                        <input type="text" class="form-control custom-input" id="username" value="<?php echo htmlspecialchars($userInfo['NumeUtilizator']); ?>" readonly>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Numele de utilizator nu poate fi schimbat.
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Câmpurile pentru email și număr de telefon -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="input-group position-relative">
                            <input type="email" class="form-control custom-input" id="email" name="email" autocomplete="off" value="<?php echo htmlspecialchars($userInfo['Email']); ?>">
                            <i class="fas fa-edit field-icon" style="color: #495057;"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefon">Nr. Telefon:</label>
                        <div class="input-group position-relative">
                            <input type="text" class="form-control custom-input" id="telefon" name="telefon" autocomplete="off" value="<?php echo htmlspecialchars($userInfo['Telefon']); ?>">
                            <i class="fas fa-edit field-icon" style="color: #495057;"></i>
                        </div>
                    </div>
                    <!-- Gruparea butoanelor -->
                    <div class="form-group d-flex align-items-end justify-content-end">
                        <!-- Buton pentru trimiterea formularului -->
                        <button type="submit" class="btn btn-outline-primary custom-update-btn">
                            <i class="fas fa-redo"></i> Actualizează informațiile
                        </button>
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

        .dark-mode .confirm-icon {
            color: #3BD16F;
        }

        .dark-mode .confirm-icon:hover {
            color: #006400;
        }
    </style>

</body>
</html>
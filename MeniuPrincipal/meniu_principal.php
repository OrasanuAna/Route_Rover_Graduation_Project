<?php

session_start(); // Inițializează sesiunea

include '../db_connect.php'; // Include fișierul de conectare la baza de date

// Verifică dacă utilizatorul este autentificat (adică dacă există un user_id setat în sesiune)
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Verifică dacă avem un nume de utilizator setat în sesiune
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

$userID = $_SESSION['user_id']; // Extrage ID-ul utilizatorului conectat

// Include logica pentru a prelua sarcinile de la baza de date
$taskuriRestante = [];
$taskurileZilei = [];
$taskuriViitoare = [];

$today = date('Y-m-d');

// Pregătește și execută interogarea SQL pentru a prelua sarcinile asignate utilizatorului
$sql = "SELECT SarcinaID, NumeSarcina, TermenLimitaSarcina FROM Sarcini WHERE UtilizatorID = {$userID} ORDER BY TermenLimitaSarcina ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taskInfo = ['id' => $row['SarcinaID'], 'nume' => $row['NumeSarcina']];
        $taskDate = $row['TermenLimitaSarcina'];

        if ($taskDate < $today) {
            $taskuriRestante[] = $taskInfo;
        } elseif ($taskDate == $today) {
            $taskurileZilei[] = $taskInfo;
        } else {
            $taskuriViitoare[] = $taskInfo;
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
    <title>Meniu Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <!-- Conținutul principal al paginii -->
    <div class="container">
        <h1 class="text-center">Welcome to Route Rover, <?php echo htmlspecialchars($username); ?>!</h1>

        <div class="container table-container">
            <div class="row">
                <div class="col text-center">
                    <a href="/Sarcini/adauga_task.php" class="btn custom-btn">
                        <i class="fas fa-plus-circle"></i> Adaugă o sarcină
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead class="text-black" style="background-color: #ADD8E6;">
                            <tr>
                                <th scope="col">Sarcini Restante</th>
                                <th scope="col">Sarcinile Zilei</th>
                                <th scope="col" class="text-right">Sarcini Viitoare</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?php foreach ($taskuriRestante as $task): ?>
                                        <div class="mb-3">
                                            <?php echo htmlspecialchars($task['nume']); ?>
                                            <a href="/Sarcini/informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php foreach ($taskurileZilei as $task): ?>
                                        <div class="mb-3">
                                            <?php echo htmlspecialchars($task['nume']); ?>
                                            <a href="/Sarcini/informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php foreach ($taskuriViitoare as $task): ?>
                                        <div class="mb-3 text-right">
                                            <?php echo htmlspecialchars($task['nume']); ?>
                                            <a href="/Sarcini/informatii_task.php?id=<?php echo $task['id']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="#" class="confirm-icon" data-taskid="<?php echo $task['id']; ?>"><i class="fas fa-check"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="floating-container">
        <div class="floating-button">
            <i class="fas fa-headset"></i>
        </div>
        <div class="element-container">
            <a href="../contact.html" class="float-element tooltip-left" data-tooltip="Contact">
                <i class="fas fa-phone"></i>
            </a>
            <a href="../privacy.html" class="float-element tooltip-left" data-tooltip="Politica de Confidențialitate">
                <i class="fas fa-shield-alt"></i>
            </a>
            <a href="../terms.html" class="float-element tooltip-left" data-tooltip="Termeni și Condiții">
                <i class="fas fa-file-signature"></i>
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        document.querySelectorAll('.confirm-icon').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-taskid');
                Swal.fire({
                    title: 'Ați finalizat task-ul?',
                    text: "Confirmați că ați finalizat acest task.",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Da',
                    cancelButtonText: 'Anulare',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/Sarcini/sterge_task.php?id=" + taskId;
                    }
                });
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

        .dark-mode .btn.custom-btn {
            background-color: #0A0F19;
            border-color: #fff;
            color: white;
        }
        
        .dark-mode .btn.custom-btn:hover {
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

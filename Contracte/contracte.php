<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Conectare la baza de date
include '../db_connect.php';

// Inițializează array-ul pentru a stoca informațiile contractelor
$contracte = [];

// Obține ID-ul utilizatorului curent din sesiune
$currentUserId = $_SESSION['user_id'];

// Pregătește interogarea SQL pentru a selecta doar contractele adăugate de utilizatorul curent
$sql = "SELECT ContractID, NumeContract, TipContract, DataInceputContract, DataSfarsitContract, NumeFisier FROM Contracte WHERE UtilizatorID = $currentUserId";

// Execută interogarea
$result = $conn->query($sql);

// Verifică dacă interogarea a returnat rezultate
if ($result && $result->num_rows > 0) {
    // Parcurge rezultatele și le adaugă în array-ul $contracte
    while($row = $result->fetch_assoc()) {
        $contracte[] = $row;
    }
}

// Închide conexiunea la baza de date
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="/Contracte/contracte.css" rel="stylesheet">
        <title>Contracte</title>
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
                    <li class="nav-item active">
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
            <h1 class="text-center my-4 mt-5">Informații despre contracte</h1>
            <div class="text-center my-4">
                <a href="/Contracte/adauga_contract.php" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă un contract</a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead class="text-black" style="background-color: #ADD8E6;">
                        <tr>
                            <th scope="col" class="text-center align-middle">Nr. crt.</th>
                            <th scope="col" class="align-middle">Nume contract</th>
                            <th scope="col" class="align-middle">Tip contract</th>
                            <th scope="col" class="align-middle">Dată început contract</th>
                            <th scope="col" class="align-middle">Dată sfârșit contract</th>
                            <th scope="col" class="align-middle">Vizualizează contractul</th>
                            <th scope="col" class="align-middle"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach ($contracte as $contract): ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $counter++; ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($contract['NumeContract']); ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($contract['TipContract']); ?></td>
                            <td class="align-middle"><?php echo date('d-m-Y', strtotime($contract['DataInceputContract'])); ?></td>
                            <td class="align-middle"><?php echo date('d-m-Y', strtotime($contract['DataSfarsitContract'])); ?></td>
                            <td class="align-middle"><a href="serve_contract.php?id=<?php echo $contract['ContractID']; ?>" target="_blank"><?php echo htmlspecialchars($contract['NumeFisier']); ?></a></td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center justify-content-center">
                                    <a href="informatii_contract.php?id=<?php echo $contract['ContractID']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="#" class="delete-icon mr-4" data-contractid="<?php echo $contract['ContractID']; ?>"><i class="fas fa-times"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script>
            document.querySelectorAll('.delete-icon').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const contractId = this.getAttribute('data-contractid');
                    Swal.fire({
                        title: 'Sunteți sigur?',
                        text: "Nu veți putea reveni asupra acestei acțiuni!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Da',
                        cancelButtonText: 'Anulare',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "sterge_contract.php?id=" + contractId;
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
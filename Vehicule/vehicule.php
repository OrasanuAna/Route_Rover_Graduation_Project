<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Conectare la baza de date
include '../db_connect.php';

// Inițializează array-ul pentru a stoca informațiile vehiculelor
$vehicule = [];

// Obține ID-ul utilizatorului curent din sesiune
$currentUserId = $_SESSION['user_id'];

// Pregătește interogarea SQL pentru a selecta doar vehiculele adăugate de utilizatorul curent
$sql = "SELECT Vehicule.VehiculID, Vehicule.NumarInmatriculare, Vehicule.MarcaModel, Soferi.Nume AS SoferNume, Soferi.Prenume AS SoferPrenume FROM Vehicule LEFT JOIN Soferi ON Vehicule.SoferID = Soferi.SoferID WHERE Vehicule.UtilizatorID = $currentUserId";

// Execută interogarea
$result = $conn->query($sql);

// Verifică dacă interogarea a returnat rezultate
if ($result && $result->num_rows > 0) {
    // Parcurge rezultatele și le adaugă în array-ul $vehicule
    while($row = $result->fetch_assoc()) {
        $vehicule[] = $row;
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
        <link href="/Vehicule/vehicule.css" rel="stylesheet">
        <title>Vehicule</title>
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
                    <li class="nav-item active">
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
            <h1 class="text-center my-4 mt-5">Informații despre vehicule</h1>
            <div class="text-center my-4">
                <a href="/Vehicule/adauga_vehicul.php" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă un vehicul</a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead class="text-black" style="background-color: #ADD8E6;">
                        <tr>
                            <th scope="col" class="text-center">Nr. crt.</th>
                            <th scope="col">Număr de înmatriculare</th>
                            <th scope="col">Marcă și model</th>
                            <th scope="col">Numele șoferului asignat</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach ($vehicule as $vehicul): ?>
                        <tr>
                            <td class="text-center"><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($vehicul['NumarInmatriculare']); ?></td>
                            <td><?php echo htmlspecialchars($vehicul['MarcaModel']); ?></td>
                            <td><?php echo htmlspecialchars($vehicul['SoferNume']) . ' ' . htmlspecialchars($vehicul['SoferPrenume']); ?></td>
                            <td>
                                <a href="informatii_vehicul.php?id=<?php echo $vehicul['VehiculID']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" class="delete-icon" data-vehiculid="<?php echo $vehicul['VehiculID']; ?>"><i class="fas fa-times"></i></a>
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
                    const vehiculId = this.getAttribute('data-vehiculid');
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
                            window.location.href = "sterge_vehicul.php?id=" + vehiculId;
                        }
                    });
                });
            });
        </script>

    </body>
</html>

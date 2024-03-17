<?php

session_start();

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

// Conectare la baza de date
include '../db_connect.php';

// Inițializează array-ul pentru a stoca informațiile documentelor
$documente = [];

// Obține ID-ul utilizatorului curent din sesiune
$currentUserId = $_SESSION['user_id'];

// Pregătește interogarea SQL pentru a selecta doar documentele adăugate de utilizatorul curent
$sql = "SELECT DocumentID, NumeDocument, TipDocument, DataIncarcareDocument, CaleFisierDocument FROM Documente WHERE UtilizatorID = $currentUserId";

// Execută interogarea
$result = $conn->query($sql);

// Verifică dacă interogarea a returnat rezultate
if ($result && $result->num_rows > 0) {
    // Parcurge rezultatele și le adaugă în array-ul $documente
    while($row = $result->fetch_assoc()) {
        $documente[] = $row;
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
        <link href="/Documente/documente.css" rel="stylesheet">
        <title>Documente</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/Documente/documente.php"><i class="fas fa-file-alt"></i> Documente</a>
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

        <div class="container">
            <h1 class="text-center my-4 mt-5">Informații despre documente</h1>
            <div class="text-center my-4">
                <a href="/Documente/adauga_document.php" class="btn btn-add"><i class="fas fa-plus-circle"></i> Adaugă un document</a>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead class="text-black" style="background-color: #ADD8E6;">
                        <tr>
                            <th scope="col" class="text-center">Nr. crt.</th>
                            <th scope="col">Nume document</th>
                            <th scope="col">Tip document</th>
                            <th scope="col">Data încărcării</th>
                            <th scope="col">Calea fișierului</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach ($documente as $document): ?>
                        <tr>
                            <td class="text-center"><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($document['NumeDocument']); ?></td>
                            <td><?php echo htmlspecialchars($document['TipDocument']); ?></td>
                            <td><?php echo htmlspecialchars($document['DataIncarcareDocument']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($document['CaleFisierDocument']); ?>" target="_blank">Vizualizează</a></td>
                            <td>
                                <a href="informatii_document.php?id=<?php echo $document['DocumentID']; ?>" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" class="delete-icon" data-documentid="<?php echo $document['DocumentID']; ?>"><i class="fas fa-times"></i></a>
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
                    const documentId = this.getAttribute('data-documentid');
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
                            window.location.href = "sterge_document.php?id=" + documentId;
                        }
                    });
                });
            });
        </script>

    </body>
</html>
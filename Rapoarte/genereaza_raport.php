<?php

require '../vendor/autoload.php'; // Include autoloader-ul Composer pentru a putea folosi TCPDF

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: /Autentificare/autentificare.php');
    exit;
}

include '../db_connect.php'; // Include conexiunea la baza de date

$userID = $_SESSION['user_id']; // Preia ID-ul utilizatorului conectat

// Inițializare variabile pentru stocarea erorilor și a mesajelor
$error = '';
$message = '';

// Verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tabel']) && !empty($_POST['fields'])) {
    // Preia informațiile selectate de utilizator
    $tabel = mysqli_real_escape_string($conn, $_POST['tabel']);
    $fields = $_POST['fields']; // Acesta este un array

    // Crează o instanță nouă de TCPDF
    $pdf = new TCPDF();

    // Setări document
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Route Rover');
    $pdf->SetTitle('Raport');
    $pdf->SetSubject('Raport generat automat');
    $pdf->SetKeywords('TCPDF, PDF, raport');

    // Setări header și footer în document
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Adaugă o pagină nouă
    $pdf->AddPage();

    // Construiește interogarea SQL bazată pe selecțiile utilizatorului
    $selectedFields = implode(', ', array_map(function($field) use ($conn) {
        return mysqli_real_escape_string($conn, $field);
    }, $fields));

    $sql = "SELECT $selectedFields FROM $tabel WHERE UtilizatorID = $userID";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Adaugă tabelul în PDF
        $tbl = '<table cellspacing="0" cellpadding="6" border="1">';
        $tbl .= '<tr>';

        // Antetele tabelului
        foreach ($fields as $field) {
            $tbl .= '<th>' . htmlspecialchars($field) . '</th>';
        }
        $tbl .= '</tr>';

        // Datele tabelului
        while ($row = $result->fetch_assoc()) {
            $tbl .= '<tr>';
            foreach ($fields as $field) {
                $tbl .= '<td>' . htmlspecialchars($row[$field]) . '</td>';
            }
            $tbl .= '</tr>';
        }
        $tbl .= '</table>';

        // Scrie tabelul în obiectul PDF
        $pdf->writeHTML($tbl, true, false, false, false, '');

        // Închide și trimite documentul PDF
        $pdf->Output('raport.pdf', 'I');
    } else {
        $error = 'Nu există date pentru acest raport.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
        <link href="/Rapoarte/genereaza_raport.css" rel="stylesheet">
        <title>Generează un raport</title>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/Rapoarte/genereaza_raport.php"><i class="fas fa-chart-bar"></i> Rapoarte</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container my-5">
            <h1 class="text-center">Generează un raport</h1>
            <form id="reportForm" method="POST">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-4 mb-4">
                        <h2>Selectează tabelul:</h2>
                        <div class="form-group">
                            <div>
                                <input type="radio" id="soferi" name="tabel" value="Soferi">
                                <label for="soferi">Șoferi</label>
                            </div>
                            <div>
                                <input type="radio" id="vehicule" name="tabel" value="Vehicule">
                                <label for="vehicule">Vehicule</label>
                            </div>
                            <div>
                                <input type="radio" id="documente" name="tabel" value="Documente">
                                <label for="documente">Documente</label>
                            </div>
                            <div>
                                <input type="radio" id="contracte" name="tabel" value="Contracte">
                                <label for="contracte">Contracte</label>
                            </div>
                            <div>
                                <input type="radio" id="sarcini" name="tabel" value="Sarcini">
                                <label for="sarcini">Sarcini</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <h2>Filtrează</h2>
                        <div id="fieldSelection" class="form-group">
                            <!-- Checkbox-urile pentru câmpuri vor fi generate dinamic -->
                        </div>
                    </div>
                </div>
                <!-- Butonul plasat în afara .row pentru a evita deplasarea acestuia -->
                <div class="row justify-content-center">
                    <div class="col text-center">
                        <button type="submit" class="btn custom-btn">Generează raport</button>
                    </div>
                </div>
            </form>
        </div>



        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script>
            // Obținerea elementelor necesare din DOM
            const reportForm = document.getElementById('reportForm');
            const fieldSelection = document.getElementById('fieldSelection');
            
            // Obiect pentru a mapa tabelele la câmpurile lor
            const tableFields = {
                'Soferi': ['Nume', 'Prenume', 'DataNasterii', 'DataAngajarii', 'Telefon', 'Email'],
                'Vehicule': ['NumarInmatriculare', 'MarcaModel', 'AnFabricatie', 'Culoare', 'TipCombustibil'],
                'Documente': ['NumeDocument', 'TipDocument', 'DataIncarcareDocument'],
                'Contracte': ['NumeContract', 'TipContract', 'DataInceputContract', 'DataSfarsitContract'],
                'Sarcini': ['NumeSarcina', 'DescriereSarcina', 'TermenLimitaSarcina']
            };

            // Funcție pentru a actualiza opțiunile de filtrare
            function updateFieldSelection(table) {
                // Șterge câmpurile curente
                fieldSelection.innerHTML = '';
                
                // Adaugă noi câmpuri bazate pe tabelul selectat
                tableFields[table].forEach(function(field) {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.className = 'form-check-input';
                    input.id = field;
                    input.name = 'fields[]';
                    input.value = field;
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = field;
                    label.textContent = field;
                    div.appendChild(input);
                    div.appendChild(label);
                    fieldSelection.appendChild(div);
                });
            }

            // Eveniment pentru schimbarea selecției tabelului
            reportForm.addEventListener('change', function(event) {
                if (event.target.name === 'tabel') {
                    updateFieldSelection(event.target.value);
                }
            });
        </script>

    </body>
</html>
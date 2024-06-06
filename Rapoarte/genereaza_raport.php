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

// Verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['tabel']) || empty($_POST['tabel'])) {
        $error = 'Trebuie selectată o tabelă.';
    }
    // Verifică dacă au fost selectate filtre (câmpuri)
    elseif (!isset($_POST['fields']) || empty($_POST['fields'])) {
        $error = 'Trebuie selectat cel puțin un filtru.';
    } else {
        // Preia informațiile selectate de utilizator
        $tabel = mysqli_real_escape_string($conn, $_POST['tabel']);
        $fields = $_POST['fields']; // Acesta este un array

        // Preia datele de început și sfârșit pentru câmpurile de tip dată
        $dateFilters = [];
        foreach ($fields as $field) {
            if (isset($_POST["{$field}StartDate"]) && isset($_POST["{$field}EndDate"])) {
                $startDate = mysqli_real_escape_string($conn, $_POST["{$field}StartDate"]);
                $endDate = mysqli_real_escape_string($conn, $_POST["{$field}EndDate"]);
                if (!empty($startDate) && !empty($endDate)) {
                    $dateFilters[$field] = [
                        'start' => $startDate,
                        'end' => $endDate,
                    ];
                }
            }
        }

        class PDFWithWatermark extends TCPDF {
            public function Footer() {
                // Setează transparența pentru watermark
                $this->SetAlpha(0.2);
                
                // Alege fontul, mărimea și culoarea
                $this->SetFont('Helvetica', 'B', 50);
                
                // Salvează starea curentă a matricii de transformare
                $this->StartTransform();
                
                // Rotire text și poziționare
                $this->Rotate(45, 110, 230);
                
                // Setează culoarea textului (gri)
                $this->SetTextColor(150, 150, 150);
                
                // Adaugă textul
                $this->Text(150, 120, 'Route Rover');
                
                // Restabilește starea matricii de transformare
                $this->StopTransform();
                
                // Resetează transparența la valoarea normală
                $this->SetAlpha(1);
                
                $this->SetFont('freeserif', '', 10);
                
                // Linie deasupra textului din footer
                $this->SetY(-16);
                $this->SetDrawColor(0, 0, 0);
                $this->Line(10, $this->GetY(), 200, $this->GetY());
                
                // Adaugă textul în footer în partea stângă
                $this->SetY(-15);
                $this->Cell(0, 10, 'Acest raport a fost generat automat de către aplicația Route Rover.', 0, false, 'L', 0, '', 0, false, 'T', 'M');
                
                // Adaugă textul în footer în partea dreaptă
                $this->Cell(0, 10, 'Aveți o întrebare? support@route-rover.ro', 0, false, 'R', 0, '', 0, false, 'T', 'M');
            }
        }
        
        // Crează o instanță nouă de TCPDF
        $pdf = new PDFWithWatermark();
        
        // Setări document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Route Rover');
        $pdf->SetTitle('Raport');
        $pdf->SetSubject('Raport generat automat');
        $pdf->SetKeywords('TCPDF, PDF, raport');
        
        // Setări header și footer în document
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        
        // Adaugă o pagină nouă
        $pdf->AddPage();
        
        // Adaugă logo-ul în colțul din stânga sus
        $logo = '../Imagini/Logo.png';
        $pdf->Image($logo, 2, 2, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        // Ajustează poziția Y pentru a nu fi afectat de logo
        $pdf->SetY(30);
        
        // Adaugă titlul deasupra tabelului
        $pdf->SetFont('freeserif', 'B', 16, '', true);
        $pdf->Cell(0, 10, 'Raport ' . ucfirst($tabel) . ' pentru firma de transport mărfuri Leahu Transit', 0, 1, 'C');
        $pdf->Ln(10); // Adaugă un spațiu între titlu și tabel
        
        $pdf->SetFont('freeserif', '', 12, '', true); // 'freeserif' este un font care suportă caracterele UTF-8.
        
        // Construiește interogarea SQL bazată pe selecțiile utilizatorului
        $selectedFields = implode(', ', array_map(function($field) use ($conn) {
            return mysqli_real_escape_string($conn, $field);
        }, $fields));
        
        $conditions = ["UtilizatorID = $userID"];
        foreach ($dateFilters as $field => $dates) {
            $conditions[] = "$field BETWEEN '{$dates['start']}' AND '{$dates['end']}'";
        }
        $whereClause = implode(' AND ', $conditions);
        
        $sql = "SELECT $selectedFields FROM $tabel WHERE $whereClause";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Adaugă tabelul în PDF
            $tbl = '<style>
                th {
                    background-color: #ADD8E6;
                    color: #000;
                    font-weight: bold;
                }
            </style>
            <table cellspacing="0" cellpadding="6" border="1">
            <tr>';
        
            // Antetele tabelului
            foreach ($fields as $field) {
                $tbl .= '<th>' . htmlspecialchars($field) . '</th>';
            }
            $tbl .= '</tr>';
        
            // Datele tabelului
            while ($row = $result->fetch_assoc()) {
                $tbl .= '<tr>';
                foreach ($fields as $field) {
                    // Verifică dacă valoarea este o dată în format Y-M-D
                    if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $row[$field])) {
                        // Converteste data din Y-M-D în D-M-Y
                        $formattedDate = date('d-m-Y', strtotime($row[$field]));
                        $tbl .= '<td>' . htmlspecialchars($formattedDate) . '</td>';
                    } else {
                        // Dacă valoarea nu este o dată, o afișează nemodificată
                        $tbl .= '<td>' . htmlspecialchars($row[$field]) . '</td>';
                    }
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
                    <a class="nav-link no-hover-effect" style="padding-top: 12px;" href="#" id="themeToggle"><i class="fas fa-sun"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link no-hover-effect" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center">Generează un raport</h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert-container">
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center" role="alert"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

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
                    <h2>Filtrează:</h2>
                    <div id="fieldSelection" class="form-group">
                        <!-- Checkbox-urile pentru câmpuri vor fi generate dinamic -->
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-4" id="dateFilters" style="display: none;">
                    <h2>Filtrează după dată*:</h2>
                    <div id="dateInputs" class="form-group">
                        <!-- Input-urile pentru intervalul de date vor fi generate dinamic -->
                    </div>
                </div>
            </div>
            <!-- Butonul plasat în afara .row pentru a evita deplasarea acestuia -->
            <div class="row justify-content-center">
                <div class="col text-center">
                    <button type="submit" class="btn custom-btn"><i class="fas fa-chart-bar"></i> Generează raport</button>
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
        // Obținerea elementelor necesare din DOM
        const reportForm = document.getElementById('reportForm');
        const fieldSelection = document.getElementById('fieldSelection');
        const dateFilters = document.getElementById('dateFilters');
        const dateInputs = document.getElementById('dateInputs');

        // Obiect pentru a mapa tabelele la câmpurile lor
        const tableFields = {
            'Soferi': ['Nume', 'Prenume', 'DataNasterii', 'DataAngajarii', 'Telefon', 'Email'],
            'Vehicule': ['NumarInmatriculare', 'MarcaModel', 'AnFabricatie', 'Culoare', 'TipCombustibil'],
            'Documente': ['NumeDocument', 'TipDocument', 'DataIncarcareDocument'],
            'Contracte': ['NumeContract', 'TipContract', 'DataInceputContract', 'DataSfarsitContract'],
            'Sarcini': ['NumeSarcina', 'DescriereSarcina', 'TermenLimitaSarcina']
        };

        const dateFields = ['DataAngajarii', 'AnFabricatie', 'DataIncarcareDocument', 'DataSfarsitContract', 'TermenLimitaSarcina'];

        // Funcție pentru a actualiza opțiunile de filtrare
        function updateFieldSelection(table) {
            // Șterge câmpurile curente și input-urile de dată
            fieldSelection.innerHTML = '';
            dateInputs.innerHTML = '';
            dateFilters.style.display = 'none';

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

                // Adaugă input-uri pentru intervalul de date dacă câmpul este unul de tip dată
                if (dateFields.includes(field)) {
                    input.addEventListener('change', function() {
                        const dateInputsDiv = document.getElementById(`${field}-date-inputs`);
                        if (input.checked && !dateInputsDiv) {
                            const dateDiv = document.createElement('div');
                            dateDiv.id = `${field}-date-inputs`;
                            dateDiv.className = 'form-group';
                            dateDiv.innerHTML = `
                                <label for="${field}StartDate" class="mr-2">${field} - Data început:</label>
                                <input type="date" class="form-control mr-3" id="${field}StartDate" name="${field}StartDate">
                                <label for="${field}EndDate" class="mr-2 mt-2">${field} - Data sfârșit:</label>
                                <input type="date" class="form-control" id="${field}EndDate" name="${field}EndDate">
                            `;
                            dateInputs.appendChild(dateDiv);
                            dateFilters.style.display = 'block';
                        } else if (!input.checked && dateInputsDiv) {
                            dateInputsDiv.remove();
                            if (dateInputs.innerHTML.trim() === '') {
                                dateFilters.style.display = 'none';
                            }
                        }
                    });
                }
            });
        }

        // Eveniment pentru schimbarea selecției tabelului
        reportForm.addEventListener('change', function(event) {
            if (event.target.name === 'tabel') {
                updateFieldSelection(event.target.value);
            }
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